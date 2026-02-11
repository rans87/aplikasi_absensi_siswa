<?php

namespace App\Http\Controllers;

use App\Models\AnggotaKelas;
use App\Models\Siswa;
use App\Models\RombonganBelajar;
use App\Models\TahunAjar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnggotaKelasController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $rombelId = $request->rombel_id;

        $query = AnggotaKelas::with(['siswa', 'rombonganBelajar', 'tahunAjar']);

        if ($rombelId) {
            $query->where('rombongan_belajar_id', $rombelId);
        }

        if ($search) {
            $query->whereHas('siswa', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $anggota = $query->latest()->paginate(20)->withQueryString();
        $rombels = RombonganBelajar::all();
        $tahunAjars = TahunAjar::all();

        return view('anggota_kelas.index', compact('anggota', 'rombels', 'tahunAjars'));
    }

    public function create()
    {
        $siswa = Siswa::orderBy('nama')->get();
        $rombels = RombonganBelajar::all();
        $tahunAjars = TahunAjar::all();
        return view('anggota_kelas.create', compact('siswa', 'rombels', 'tahunAjars'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'rombongan_belajar_id' => 'required|exists:rombongan_belajar,id',
            'tahun_ajar_id' => 'required|exists:tahun_ajar,id',
        ]);

        // Cek duplikat di tahun ajar yang sama
        $exists = AnggotaKelas::where('siswa_id', $request->siswa_id)
            ->where('tahun_ajar_id', $request->tahun_ajar_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Siswa ini sudah terdaftar di kelas lain pada tahun ajaran yang sama.');
        }

        AnggotaKelas::create($request->all());

        return redirect()->route('anggota-kelas.index')->with('success', 'Siswa berhasil dimasukkan ke kelas.');
    }

    public function destroy($id)
    {
        AnggotaKelas::destroy($id);
        return back()->with('success', 'Data anggota kelas berhasil dihapus.');
    }

    /**
     * Sinkronisasi masal dari API berdasarkan rombel
     */
    public function syncApi(Request $request)
    {
        $request->validate([
            'rombongan_belajar_id' => 'required|exists:rombongan_belajar,id',
            'tahun_ajar_id' => 'required|exists:tahun_ajar,id',
        ]);

        try {
            $response = Http::timeout(60)->get(env('API_ROMBEL_URL'));
            if (!$response->successful()) {
                return back()->with('error', 'Gagal mengambil data dari API.');
            }

            $data = $response->json();
            $apiSiswa = $data['data'] ?? [];

            $rombel = RombonganBelajar::find($request->rombongan_belajar_id);
            $count = 0;

            foreach ($apiSiswa as $item) {
                // Contoh: hanya masukkan siswa yang nama rombelnya di API cocok dengan nama kelas lokal
                if (isset($item['nama_rombel']) && $item['nama_rombel'] === $rombel->nama_kelas) {
                    
                    // Pastikan siswa ada di tabel lokal
                    $siswa = Siswa::where('nis', $item['no_induk'])->first();
                    if (!$siswa) {
                        $siswa = Siswa::create([
                            'nis' => $item['no_induk'],
                            'nama' => $item['nama'],
                            'jenis_kelamin' => in_array($item['jenis_kelamin'], ['L', 'P']) ? $item['jenis_kelamin'] : 'L',
                        ]);
                    }

                    AnggotaKelas::updateOrCreate(
                        [
                            'siswa_id' => $siswa->id,
                            'tahun_ajar_id' => $request->tahun_ajar_id,
                        ],
                        [
                            'rombongan_belajar_id' => $request->rombongan_belajar_id,
                        ]
                    );
                    $count++;
                }
            }

            return back()->with('success', "Berhasil mensinkronkan $count siswa ke kelas $rombel->nama_kelas.");

        } catch (\Exception $e) {
            Log::error("Sync Error: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}