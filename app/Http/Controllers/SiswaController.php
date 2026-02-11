<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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
            ->latest()
            ->paginate(10);

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

        $siswa->update($request->all());

        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil diupdate');
    }

    public function destroy(Siswa $siswa)
    {
        $siswa->delete();
        return back()->with('success', 'Data siswa dihapus');
    }

    // 🔵 AMBIL DATA DARI API
    public function syncApi()
    {
        $url = env('API_ROMBEL_URL');

        try {
            // Timeout diperpanjang hingga 60 detik karena data cukup besar (2.8MB+)
            $response = Http::timeout(60)->get($url);

            if (!$response->successful()) {
                return back()->with('error', 'Gagal mengambil data dari API (Server Pusat Sibuk)');
            }

            $result = $response->json();
            $dataSiswa = $result['data'] ?? $result;

            if (!is_array($dataSiswa)) {
                return back()->with('error', 'Format data API tidak sesuai');
            }

            foreach ($dataSiswa as $item) {
                if (!isset($item['no_induk'], $item['nama'])) {
                    continue;
                }

                $siswa = Siswa::where('nis', $item['no_induk'])->first();
                
                if (!$siswa) {
                    $siswa = new Siswa();
                    $siswa->nis = $item['no_induk'];
                    $siswa->qr_code = (string) Str::uuid();
                }

                $siswa->nama = $item['nama'];
                $siswa->no_hp = $item['no_telp'] ?? null;
                $siswa->jenis_kelamin = in_array($item['jenis_kelamin'], ['L', 'P']) ? $item['jenis_kelamin'] : 'L';
                $siswa->save();
            }

            // Hapus cache login agar siswa bisa langsung login dengan data terbaru
            Cache::forget('api_siswa_data');

            return back()->with('success', 'Data siswa berhasil disinkron dari API');

        } catch (\Exception $e) {
            return back()->with('error', 'Koneksi API Gagal: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        abort(404); // atau isi sesuai kebutuhan
    }

}
