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
        $search = $request->search;

        $absensi = Absensi::with(['siswa', 'guru', 'rombonganBelajar'])
            ->whereDate('tanggal', $tanggal)
            ->when($search, function ($query) use ($search) {
                $query->whereHas('siswa', function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('nis', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();


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
        $request->validate(['qr_code' => 'required']);

        // QR Format: NIS_HASH|SESSION|SALT
        $data = explode('|', $request->qr_code);
        
        if (count($data) < 3) {
            return back()->with('error', 'Format QR Code tidak valid.');
        }

        $qrCodeHash = $data[0];
        $qrSession = $data[1];
        $qrSalt = $data[2];

        // 1. Validasi Sesi
        $currentSession = \Illuminate\Support\Facades\Cache::get('qr_session', 'pagi');
        $currentSalt = \Illuminate\Support\Facades\Cache::get('qr_salt', 'init');

        if ($qrSession !== $currentSession || $qrSalt !== $currentSalt) {
            return back()->with('error', 'QR Code kadaluarsa atau sesi sudah berganti.');
        }

        // 2. Cari Siswa
        $siswa = Siswa::where('qr_code', $qrCodeHash)->first();

        if (!$siswa) {
            return back()->with('error', 'Siswa tidak ditemukan dalam database.');
        }

        // 3. Validasi Rombel
        $anggota = \App\Models\AnggotaKelas::where('siswa_id', $siswa->id)->latest()->first();
        
        if (!$anggota) {
            return back()->with('error', 'Siswa belum terdaftar di kelas manapun.');
        }

        // 4. Cegah Duplikasi
        $sudahAbsen = Absensi::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', Carbon::today())
            ->where('status', 'hadir') // Jika ingin membedakan hadir pagi/sore bisa ditambahkan field session
            ->exists();

        // Optional: Jika ingin mengizinkan absen sore meskipun sudah absen pagi, 
        // hapus pengecekan duplikasi ini atau tambahkan kolom 'sesi' di tabel absensi.

        if ($sudahAbsen) {
            return back()->with('error', 'Peringatan: Siswa ini sudah tercatat hadir hari ini.');
        }

        Absensi::create([
            'siswa_id' => $siswa->id,
            'guru_id' => Auth::guard('guru')->id(), 
            'rombongan_belajar_id' => $anggota->rombongan_belajar_id,
            'tanggal' => Carbon::today(),
            'status' => 'hadir',
        ]);

        return back()->with('success', "Absensi SESI " . strtoupper($currentSession) . " Berhasil: " . $siswa->nama);
    }

}
