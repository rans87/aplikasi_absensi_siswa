<?php

namespace App\Http\Controllers;

use App\Models\AnggotaKelas;
use App\Models\Siswa;
use App\Models\RombonganBelajar;
use App\Models\TahunAjar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AnggotaKelasController extends Controller
{
    // Pastikan fungsi index ini ada dan ditulis dengan benar
    public function index()
    {
        // Mengambil data dari database lokal dengan relasinya
        $anggota = AnggotaKelas::with(['siswa', 'rombel', 'tahunAjar'])->get();

        // Mengambil URL API dari .env
        $apiUrl = env('API_ROMBEL_URL');
        $apiData = [];

        try {
            $response = Http::get($apiUrl);
            if ($response->successful()) {
                $apiData = $response->json();
            }
        } catch (\Exception $e) {
            $apiData = ['error' => 'Gagal mengambil data API'];
        }

        return view('anggota_kelas.index', compact('anggota', 'apiData'));
    }

    public function create()
    {
        $siswa = Siswa::all();
        $rombel = RombonganBelajar::all();
        $tahunAjar = TahunAjar::all();

        return view('anggota_kelas.create', compact('siswa', 'rombel', 'tahunAjar'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required',
            'rombongan_belajar_id' => 'required',
            'tahun_ajar_id' => 'required',
        ]);

        AnggotaKelas::create($validated);

        return redirect()->route('anggota-kelas.index')->with('success', 'Data berhasil disimpan!');
    }
}