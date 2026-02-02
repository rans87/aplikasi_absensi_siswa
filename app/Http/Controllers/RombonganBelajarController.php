<?php

namespace App\Http\Controllers;

use App\Models\RombonganBelajar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RombonganBelajarController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $rombel = RombonganBelajar::when($search, function ($q) use ($search) {
            $q->where('nama_kelas', 'like', "%$search%")
                ->orWhere('jurusan', 'like', "%$search%");
        })
            ->latest()
            ->paginate(10) // 👈 INI YANG MENGATUR JUMLAH DATA
            ->withQueryString(); // biar search tidak hilang saat pindah halaman

        return view('rombongan_belajar.index', compact('rombel'));
    }

    public function create()
    {
        return view('rombongan_belajar.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required',
            'jurusan' => 'required',
            'tingkat' => 'required|integer|min:1|max:4',
        ]);

        RombonganBelajar::create($request->all());

        return redirect()->route('rombongan-belajar.index')
            ->with('success', 'Rombongan belajar berhasil ditambahkan');
    }

    public function edit(RombonganBelajar $rombonganBelajar)
    {
        return view('rombongan_belajar.edit', compact('rombonganBelajar'));
    }

    public function update(Request $request, RombonganBelajar $rombonganBelajar)
    {
        $request->validate([
            'nama_kelas' => 'required',
            'jurusan' => 'required',
            'tingkat' => 'required|integer|min:1|max:4',
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

    public function syncApi()
    {
        try {
            $url = env('API_ROMBEL_URL');

            if (!$url) {
                return back()->with('error', 'API_ROMBEL_URL belum diset di file .env');
            }

            $response = Http::timeout(20)->get($url);

            if ($response->failed()) {
                return back()->with('error', 'API error dengan status: ' . $response->status());
            }

            $json = $response->json();

            if (!isset($json['data']) || !is_array($json['data'])) {
                return back()->with('error', 'Format data API tidak sesuai');
            }

            $dataSiswa = $json['data'];

            $jumlah = 0;

            foreach ($dataSiswa as $item) {

                // Pastikan ada rombel
                if (empty($item['rombel_id']) || empty($item['nama_rombel'])) {
                    continue;
                }

                // Ambil tingkat dari nama rombel (XI, X, XII)
                preg_match('/XII|XI|X/', $item['nama_rombel'], $match);
                $tingkat = $match[0] ?? '0';

                RombonganBelajar::updateOrCreate(
                    [
                        'api_rombel_id' => $item['rombel_id']
                    ],
                    [
                        'nama_kelas' => $item['nama_rombel'],
                        'jurusan' => $item['diterima_kelas_smk'] ?? '-',
                        'tingkat' => str_replace(['XII', 'XI', 'X'], ['12', '11', '10'], $tingkat),
                    ]
                );

                $jumlah++;
            }

            return back()->with('success', "Sinkronisasi rombel berhasil ($jumlah data diproses)");

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
