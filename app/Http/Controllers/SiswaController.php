<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\RombonganBelajar;
use App\Models\AnggotaKelas;
use App\Models\TahunAjar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $siswa = Siswa::when($search, function ($query) use ($search) {
            $query->where('nama', 'like', "%{$search}%")
                ->orWhere('nis', 'like', "%{$search}%");
        })
            ->select('id', 'nis', 'nama', 'jenis_kelamin', 'no_hp', 'qr_code')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('siswa.index', compact('siswa'));
    }

    public function create()
    {
        return view('siswa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|unique:siswa',
            'nama' => 'required',
            'jenis_kelamin' => 'required|in:L,P',
        ]);

        Siswa::create([
            'nis' => $request->nis,
            'nama' => $request->nama,
            'no_hp' => $request->no_hp,
            'jenis_kelamin' => $request->jenis_kelamin,
            'qr_code' => Str::uuid(),
        ]);

        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil ditambahkan');
    }

    public function edit(Siswa $siswa)
    {
        return view('siswa.edit', compact('siswa'));
    }

    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nis' => 'required|unique:siswa,nis,' . $siswa->id,
            'nama' => 'required',
            'jenis_kelamin' => 'required|in:L,P',
        ]);

        $siswa->update($request->only(['nis', 'nama', 'no_hp', 'jenis_kelamin']));

        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil diupdate');
    }

    public function destroy(Siswa $siswa)
    {
        $siswa->delete();
        return back()->with('success', 'Data siswa dihapus');
    }

    public function syncApi()
    {
        $url = config('app.api_kelas_url', env('API_KELAS_URL'));

        try {
            $response = Http::timeout(60)->get($url);

            if (!$response->successful()) {
                return back()->with('error', 'Gagal mengambil data dari API (Server Pusat Sibuk)');
            }

            $result = $response->json();
            $dataSiswa = $result['data'] ?? $result;

            if (!is_array($dataSiswa)) {
                return back()->with('error', 'Format data API tidak sesuai');
            }

            $tahunAjar = TahunAjar::where('aktif', 1)->first();
            if (!$tahunAjar) {
                $tahunAjar = TahunAjar::latest()->first() ?? TahunAjar::create(['tahun' => '2024/2025', 'aktif' => 1]);
            }

            $countSiswa = 0;
            $syncedSiswaIds = [];

            // Use DB transaction for better performance
            DB::transaction(function () use ($dataSiswa, $tahunAjar, &$countSiswa, &$syncedSiswaIds) {
                foreach ($dataSiswa as $item) {
                    try {
                        if (!isset($item['no_induk'], $item['nama'])) continue;
                        if (!empty($item['deleted_at'])) continue;

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

                        $syncedSiswaIds[] = $siswa->id;

                        if (!empty($item['rombel_id']) && !empty($item['nama_rombel'])) {
                            $namaRombel = $item['nama_rombel'];
                            
                            // Extract Level (X, XI, XII)
                            preg_match('/(XII|XI|X)/i', $namaRombel, $match);
                            $tingkatRomawi = strtoupper($match[0] ?? 'X');
                            $tingkat = str_replace(['XII', 'XI', 'X'], ['12', '11', '10'], $tingkatRomawi);
                            
                            // Extract Jurusan (Simplified)
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
                        \Illuminate\Support\Facades\Log::warning("Gagal memproses satu record siswa: " . $e->getMessage());
                        continue;
                    }
                }
            });

            if (!empty($syncedSiswaIds)) {
                AnggotaKelas::where('tahun_ajar_id', $tahunAjar->id)
                    ->whereNotIn('siswa_id', $syncedSiswaIds)
                    ->delete();
            }

            Cache::forget('api_siswa_data');

            return back()->with('success', "Sinkronisasi Berhasil! $countSiswa siswa aktif telah diproses dan ditempatkan. Data usang telah dibersihkan.");

        } catch (\Exception $e) {
            return back()->with('error', 'Koneksi API Gagal: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        abort(404);
    }
}
