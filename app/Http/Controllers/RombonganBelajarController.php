<?php

namespace App\Http\Controllers;

use App\Models\RombonganBelajar;
use App\Models\Siswa;
use App\Models\AnggotaKelas;
use App\Models\TahunAjar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class RombonganBelajarController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $rombel = RombonganBelajar::with('waliKelas')->when($search, function ($q) use ($search) {
            $q->where('nama_kelas', 'like', "%$search%")
                ->orWhere('jurusan', 'like', "%$search%");
        })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('rombongan_belajar.index', compact('rombel'));
    }

    public function create()
    {
        $gurus = \App\Models\Guru::orderBy('nama')->get();
        return view('rombongan_belajar.create', compact('gurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required',
            'jurusan' => 'required',
            'tingkat' => 'required|integer|min:1|max:4',
            'wali_kelas_id' => 'nullable|exists:guru,id',
        ]);

        RombonganBelajar::create($request->all());

        return redirect()->route('rombongan-belajar.index')
            ->with('success', 'Rombongan belajar berhasil ditambahkan');
    }

    public function edit(RombonganBelajar $rombonganBelajar)
    {
        $gurus = \App\Models\Guru::orderBy('nama')->get();
        return view('rombongan_belajar.edit', compact('rombonganBelajar', 'gurus'));
    }

    public function update(Request $request, RombonganBelajar $rombonganBelajar)
    {
        $request->validate([
            'nama_kelas' => 'required',
            'jurusan' => 'required',
            'tingkat' => 'required|integer|min:1|max:4',
            'wali_kelas_id' => 'nullable|exists:guru,id',
        ]);

        $rombonganBelajar->update($request->all());

        return redirect()->route('rombongan-belajar.index')
            ->with('success', 'Data rombel berhasil diperbarui');
    }

    public function destroy(RombonganBelajar $rombonganBelajar)
    {
        $rombonganBelajar->delete();
        return back()->with('success', 'Data rombel berhasil dihapus');
    }

    public function show($id)
    {
        // Ambil tahun ajar aktif atau terbaru
        $tahunAjar = TahunAjar::where('aktif', 1)->first() ?? TahunAjar::latest()->first();
        
        // Load rombel with its students through anggotaKelas relation filtered by year
        $rombel = RombonganBelajar::with(['anggotaKelas' => function($query) use ($tahunAjar) {
            if ($tahunAjar) {
                $query->where('tahun_ajar_id', $tahunAjar->id);
            }
            $query->with('siswa');
        }])->findOrFail($id);
        
        // Fallback: jika tidak ada siswa di tahun ajar aktif, tampilkan dari semua tahun ajar
        if ($rombel->anggotaKelas->isEmpty()) {
            $rombel->load(['anggotaKelas' => function($query) {
                $query->with('siswa');
            }]);
        }
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $rombel->anggotaKelas->map(function($item) {
                    if (!$item->siswa) return null;
                    return [
                        'id' => $item->siswa->id,
                        'nama' => $item->siswa->nama,
                        'nis' => $item->siswa->nis,
                        'jenis_kelamin' => $item->siswa->jenis_kelamin,
                        'no_hp' => $item->siswa->no_hp,
                    ];
                })->filter()->values()
            ]);
        }

        return view('rombongan_belajar.show', compact('rombel', 'tahunAjar'));
    }

    public function syncApi()
    {
        try {
            $url = env('API_KELAS_URL');

            if (!$url) {
                return back()->with('error', 'API_KELAS_URL belum diset di file .env');
            }

            $response = Http::timeout(60)->get($url);

            if ($response->failed()) {
                return back()->with('error', 'API error dengan status: ' . $response->status());
            }

            $json = $response->json();
            $dataSiswa = $json['data'] ?? [];

            if (empty($dataSiswa)) {
                return back()->with('error', 'Format data API tidak sesuai atau data kosong');
            }

            // Ambil Tahun Ajar Aktif
            $tahunAjar = TahunAjar::where('aktif', 1)->first() ?? TahunAjar::latest()->first();
            if (!$tahunAjar) {
                return back()->with('error', 'Tahun ajaran aktif belum tersedia. Silakan buat di menu Tahun Ajar.');
            }
            $countRombel = 0;
            $countSiswa = 0;
            $syncedSiswaIds = [];

            foreach ($dataSiswa as $item) {
                // Skip jika data dasar tidak ada
                if (empty($item['rombel_id']) || empty($item['nama_rombel'])) {
                    continue;
                }

                // Skip jika siswa sudah dihapus (soft delete) di API
                if (!empty($item['deleted_at'])) {
                    continue;
                }

                // 1. Sync Rombongan Belajar
                preg_match('/XII|XI|X/', $item['nama_rombel'], $match);
                $tingkatRomawi = $match[0] ?? 'X';
                $tingkat = str_replace(['XII', 'XI', 'X'], ['12', '11', '10'], $tingkatRomawi);

                $parts = explode(' ', $item['nama_rombel']);
                $jurusanRaw = $parts[1] ?? '-';
                $jurusan = explode('-', $jurusanRaw)[0];
                if (is_numeric($jurusan)) $jurusan = 'Umum';

                $rombel = RombonganBelajar::updateOrCreate(
                    ['api_rombel_id' => $item['rombel_id']],
                    [
                        'nama_kelas' => $item['nama_rombel'],
                        'jurusan' => $jurusan,
                        'tingkat' => $tingkat,
                    ]
                );
                $countRombel++;

                // 2. Sync Siswa
                if (!empty($item['no_induk']) && !empty($item['nama'])) {
                    $siswa = Siswa::where('nis', $item['no_induk'])->first();
                    if (!$siswa) {
                        $siswa = new Siswa();
                        $siswa->nis = $item['no_induk'];
                        $siswa->qr_code = (string) Str::uuid();
                    }
                    // Bersihkan nama dari whitespace berlebih atau karakter aneh
                    $siswa->nama = trim(preg_replace('/\s+/', ' ', $item['nama']));
                    $siswa->no_hp = $item['no_telp'] ?? null;
                    $siswa->jenis_kelamin = in_array($item['jenis_kelamin'] ?? 'L', ['L', 'P']) ? $item['jenis_kelamin'] : 'L';
                    $siswa->save();
                    
                    $syncedSiswaIds[] = $siswa->id;
                    $countSiswa++;

                    // 3. Sync Anggota Kelas
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
            }

            // 4. Bersihkan data yang sudah tidak ada di API untuk tahun ajaran ini
            if (!empty($syncedSiswaIds)) {
                AnggotaKelas::where('tahun_ajar_id', $tahunAjar->id)
                    ->whereNotIn('siswa_id', $syncedSiswaIds)
                    ->delete();
            }

            $totalClasses = RombonganBelajar::count();
            return back()->with('success', "Sinkronisasi Berhasil! $totalClasses Rombel terdaftar dan $countSiswa siswa aktif telah ditempatkan. Data usang telah dibersihkan.");

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
