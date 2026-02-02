<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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

        $response = Http::get($url);

        if (!$response->successful()) {
            return back()->with('error', 'Gagal mengambil data dari API');
        }

        $result = $response->json();

        // Kalau API bungkus dalam key "data"
        $dataSiswa = $result['data'] ?? $result;

        if (!is_array($dataSiswa)) {
            return back()->with('error', 'Format data API tidak sesuai');
        }

        foreach ($dataSiswa as $item) {

            // Skip kalau struktur tidak lengkap
            if (!isset($item['no_induk'], $item['nama'], $item['jenis_kelamin'])) {
                continue;
            }

            Siswa::updateOrCreate(
                ['nis' => $item['no_induk']],
                [
                    'nama' => $item['nama'],
                    'no_hp' => $item['no_telp'] ?? null,
                    'jenis_kelamin' => $item['jenis_kelamin'],
                    'qr_code' => Str::uuid(),
                ]
            );
        }

        return back()->with('success', 'Data siswa berhasil disinkron dari API');
    }

}
