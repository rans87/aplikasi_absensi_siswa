<?php

namespace App\Http\Controllers;

use App\Models\TahunAjar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TahunAjarController extends Controller
{
    public function index()
    {
        // Ambil data dari API
        $response = Http::get('https://api-contoh.com/data'); // Ganti dengan URL API asli
        $apiData = $response->json();

        // Simpan data dari API ke DB (contoh)
        foreach ($apiData as $item) {
            TahunAjar::updateOrCreate(
                ['tahun' => $item['diterima_kelas_smk']], // contoh mapping
                ['aktif' => $item['active'] === '1']
            );
        }

        $tahunAjar = TahunAjar::latest()->get();

        return view('tahun_ajar.index', compact('tahunAjar'));
    }

    public function create()
    {
        return view('tahun_ajar.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required|string|max:255',
            'aktif' => 'nullable|boolean',
        ]);

        TahunAjar::create([
            'tahun' => $request->tahun,
            'aktif' => $request->aktif ?? false,
        ]);

        return redirect()->route('tahun_ajar.index')->with('success', 'Tahun ajaran berhasil ditambahkan!');
    }
}
