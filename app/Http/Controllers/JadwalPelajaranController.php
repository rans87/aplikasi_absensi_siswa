<?php

namespace App\Http\Controllers;

use App\Models\JadwalPelajaran;
use App\Models\RombonganBelajar;
use App\Models\MataPelajaran;
use App\Models\Guru;
use Illuminate\Http\Request;

class JadwalPelajaranController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $kelas_id = $request->kelas_id;
        $hari = $request->hari;

        $jadwal = JadwalPelajaran::with(['rombonganBelajar', 'mataPelajaran', 'guru'])
            ->when($kelas_id, function ($query) use ($kelas_id) {
                $query->where('rombongan_belajar_id', $kelas_id);
            })
            ->when($hari, function ($query) use ($hari) {
                $query->where('hari', $hari);
            })
            ->when($search, function ($query) use ($search) {
                $query->whereHas('mataPelajaran', function ($q) use ($search) {
                    $q->where('nama_mapel', 'like', "%{$search}%");
                })->orWhereHas('guru', function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%");
                })->orWhereHas('rombonganBelajar', function ($q) use ($search) {
                    $q->where('nama_kelas', 'like', "%{$search}%");
                });
            })
            ->orderBy('hari')
            ->orderBy('urutan')
            ->paginate(20)
            ->withQueryString();

        $kelas = RombonganBelajar::orderBy('nama_kelas')->get();

        return view('jadwal_pelajaran.index', compact('jadwal', 'kelas', 'kelas_id', 'hari'));
    }

    public function create()
    {
        $kelas = RombonganBelajar::orderBy('nama_kelas')->get();
        $mataPelajaran = MataPelajaran::orderBy('nama_mapel')->get();
        $guru = Guru::orderBy('nama')->get();

        return view('jadwal_pelajaran.create', compact('kelas', 'mataPelajaran', 'guru'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'rombongan_belajar_id' => 'required|exists:rombongan_belajar,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'guru_id' => 'required|exists:guru,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'urutan' => 'required|integer|min:1',
        ]);

        JadwalPelajaran::create($request->all());

        return redirect()->route('jadwal-pelajaran.index')->with('success', 'Jadwal pelajaran berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $jadwal = JadwalPelajaran::findOrFail($id);
        $kelas = RombonganBelajar::orderBy('nama_kelas')->get();
        $mataPelajaran = MataPelajaran::orderBy('nama_mapel')->get();
        $guru = Guru::orderBy('nama')->get();

        return view('jadwal_pelajaran.edit', compact('jadwal', 'kelas', 'mataPelajaran', 'guru'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'rombongan_belajar_id' => 'required|exists:rombongan_belajar,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'guru_id' => 'required|exists:guru,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'urutan' => 'required|integer|min:1',
        ]);

        $jadwal = JadwalPelajaran::findOrFail($id);
        $jadwal->update($request->all());

        return redirect()->route('jadwal-pelajaran.index')->with('success', 'Jadwal pelajaran berhasil diperbarui!');
    }

    public function destroy($id)
    {
        JadwalPelajaran::destroy($id);
        return redirect()->route('jadwal-pelajaran.index')->with('success', 'Jadwal pelajaran berhasil dihapus!');
    }

    // API endpoint to get schedule by class (for AJAX)
    public function getByKelas($kelasId)
    {
        $hari = now()->translatedFormat('l');
        $hariMap = [
            'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
        ];
        $hariIndo = $hariMap[now()->format('l')] ?? 'Senin';

        $jadwal = JadwalPelajaran::with(['mataPelajaran', 'guru'])
            ->where('rombongan_belajar_id', $kelasId)
            ->where('hari', $hariIndo)
            ->orderBy('urutan')
            ->get();

        return response()->json($jadwal);
    }
}
