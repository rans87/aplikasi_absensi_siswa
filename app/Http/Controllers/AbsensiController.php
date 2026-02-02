<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\RombonganBelajar;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->tanggal ?? now()->toDateString();

        $absensi = Absensi::with(['siswa', 'guru', 'rombonganBelajar'])
            ->whereDate('tanggal', $tanggal)
            ->latest()
            ->get();

        return view('absensi.index', compact('absensi', 'tanggal'));
    }

    public function create()
    {
        return view('absensi.create', [
            'siswa' => Siswa::all(),
            'guru' => Guru::all(),
            'rombongan' => RombonganBelajar::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required',
            'guru_id' => 'required',
            'rombongan_belajar_id' => 'required',
            'tanggal' => 'required|date',
            'status' => 'required',
        ]);

        Absensi::create($request->all());

        return redirect()->route('absensi.index')
            ->with('success', 'Data absensi berhasil disimpan');
    }

    public function destroy(Absensi $absensi)
    {
        $absensi->delete();

        return back()->with('success', 'Data absensi dihapus');
    }

    public function scan()
    {
        return view('absensi.scan');
    }

    public function prosesScan(Request $request)
    {
        $request->validate([
            'qr_code' => 'required'
        ]);

        $siswa = Siswa::where('qr_code', $request->qr_code)->first();

        if (!$siswa) {
            return back()->with('error', 'QR tidak dikenali');
        }

        // Cegah dobel absensi di hari yang sama
        $sudahAbsen = Absensi::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', Carbon::today())
            ->exists();

        if ($sudahAbsen) {
            return back()->with('error', 'Siswa sudah absen hari ini');
        }

        Absensi::create([
            'siswa_id' => $siswa->id,
            'guru_id' => Auth::user()->guru->id, // pastikan relasi ada
            'rombongan_belajar_id' => 1, // sementara default, nanti bisa dinamis
            'tanggal' => Carbon::today(),
            'status' => 'hadir',
        ]);

        return back()->with('success', 'Absensi hadir berhasil dicatat');
    }
}
