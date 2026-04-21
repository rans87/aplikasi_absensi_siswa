<?php

namespace App\Http\Controllers;

use App\Models\AnggotaKelas;
use App\Models\Siswa;
use App\Models\RombonganBelajar;
use App\Models\TahunAjar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AnggotaKelasController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $rombelId = $request->rombel_id;
        $tahunAjarId = $request->tahun_ajar_id;

        $query = AnggotaKelas::with(['siswa', 'rombonganBelajar', 'tahunAjar']);

        // Default ke tahun aktif jika tidak ada filter tahun
        if (!$tahunAjarId) {
            $activeYear = TahunAjar::where('aktif', 1)->first() ?? TahunAjar::latest()->first();
            if ($activeYear) {
                $query->where('tahun_ajar_id', $activeYear->id);
            }
        } else {
            $query->where('tahun_ajar_id', $tahunAjarId);
        }

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
     * Sinkronisasi masal dari API (Semua Data)
     */
    public function syncApi(Request $request)
    {
        try {
            $url = env('API_KELAS_URL');
            if (!$url) {
                return back()->with('error', 'API_KELAS_URL belum diset di file .env');
            }

            $response = Http::timeout(60)->get($url);
            if (!$response->successful()) {
                return back()->with('error', 'Gagal mengambil data dari API.');
            }

            $json = $response->json();
            $dataSiswa = $json['data'] ?? [];

            if (empty($dataSiswa)) {
                return back()->with('error', 'Format data API tidak sesuai atau data kosong');
            }

            // Ambil Tahun Ajar Aktif
            $tahunAjar = TahunAjar::where('aktif', 1)->first() ?? TahunAjar::latest()->first();
            if (!$tahunAjar) {
                return back()->with('error', 'Tahun ajaran aktif belum tersedia.');
            }

            $countSiswa = 0;
            DB::transaction(function () use ($dataSiswa, $tahunAjar, &$countSiswa) {
                foreach ($dataSiswa as $item) {
                    try {
                        if (empty($item['no_induk']) || empty($item['nama'])) {
                            continue;
                        }

                        // 1. Sync Siswa
                        $siswa = Siswa::where('nis', $item['no_induk'])->first();
                        if (!$siswa) {
                            $siswa = new Siswa();
                            $siswa->nis = $item['no_induk'];
                            $siswa->qr_code = (string) Str::uuid();
                        }
                        $siswa->nama = trim(preg_replace('/\s+/', ' ', $item['nama']));
                        $siswa->no_hp = $item['no_telp'] ?? null;
                        $siswa->jenis_kelamin = in_array($item['jenis_kelamin'] ?? 'L', ['L', 'P']) ? $item['jenis_kelamin'] : 'L';
                        $siswa->save();

                        // 2. Sync Rombel & Anggota Kelas
                        if (!empty($item['rombel_id']) && !empty($item['nama_rombel'])) {
                            $namaRombel = $item['nama_rombel'];
                            
                            // Extract Level (X, XI, XII)
                            preg_match('/(XII|XI|X)/i', $namaRombel, $match);
                            $tingkatRomawi = strtoupper($match[0] ?? 'X');
                            $tingkat = str_replace(['XII', 'XI', 'X'], ['12', '11', '10'], $tingkatRomawi);
                            
                            // Extract Jurusan
                            $jurusan = 'Umum';
                            $cleanName = preg_replace('/(XII|XI|X)\s*/i', '', $namaRombel);
                            $parts = preg_split('/[\s-]+/', trim($cleanName));
                            if (!empty($parts[0]) && !is_numeric($parts[0])) {
                                $jurusan = $parts[0];
                            }

                            $rombel = RombonganBelajar::updateOrCreate(
                                ['api_rombel_id' => $item['rombel_id']],
                                [
                                    'nama_kelas' => $namaRombel,
                                    'jurusan' => $jurusan,
                                    'tingkat' => $tingkat,
                                ]
                            );

                            AnggotaKelas::updateOrCreate(
                                [
                                    'siswa_id' => $siswa->id,
                                    'tahun_ajar_id' => $tahunAjar->id,
                                ],
                                [
                                    'rombongan_belajar_id' => $rombel->id,
                                ]
                            );
                        }
                        
                        $countSiswa++;
                    } catch (\Exception $e) {
                        Log::warning("Gagal memproses satu record di AnggotaKelas: " . $e->getMessage());
                        continue;
                    }
                }
            });

            return back()->with('success', "Berhasil mensinkronkan $countSiswa siswa ke kelas masing-masing.");

        } catch (\Exception $e) {
            Log::error("Sync Error: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}