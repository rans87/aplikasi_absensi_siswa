<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Absensi;
use App\Models\Pelanggaran;
use App\Models\Prestasi;
use App\Models\PoinSiswa;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class DashboardController extends Controller
{
    public function admin()
    {
        $qr_session = Cache::get('qr_session', 'pagi');
        
        $data = [
            'total_siswa' => Siswa::count(),
            'total_guru' => Guru::count(),
            'absensi_hari_ini' => Absensi::whereDate('tanggal', now())->count(),
            'total_pelanggaran' => Pelanggaran::count(),
            'total_prestasi' => Prestasi::count(),
            'recent_points' => Pelanggaran::with('siswa')->latest()->take(5)->get(),
            'qr_session' => $qr_session,
        ];
        return view('dashboard.admin', $data);
    }

    public function switchQr($session)
    {
        if (!in_array($session, ['pagi', 'sore'])) {
            return back()->with('error', 'Sesi tidak valid.');
        }

        // Set session and unique salt to prevent fraud
        Cache::put('qr_session', $session);
        Cache::put('qr_salt', Str::random(8));

        return back()->with('success', "Berhasil beralih ke sesi QR " . ucfirst($session));
    }



    public function guru()
    {
        $guru = Auth::guard('guru')->user();
        
        Log::debug('Guru Dashboard Access Attempt', [
            'has_user' => (bool)$guru,
            'user_id' => $guru ? $guru->id : 'null',
        ]);

        if (!$guru) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai guru.');
        }

        // Statistik Kehadiran Per Kelas Hari Ini
        $today = \Carbon\Carbon::today();
        $classReports = \App\Models\RombonganBelajar::select('nama_kelas', 'jurusan', 'tingkat', 'id')
            ->withCount(['absensi as hadir_count' => function ($query) use ($today) {
                $query->whereDate('tanggal', $today)->where('status', 'hadir');
            }])
            ->get();

        $data = [
            'guru' => $guru,
            'recent_absensi' => Absensi::with('siswa')->latest()->take(5)->get(),
            'total_absensi' => Absensi::whereDate('tanggal', $today)->count(),
            'total_pelanggaran' => Pelanggaran::count(),
            'total_prestasi' => Prestasi::count(),
            'classReports' => $classReports,
        ];
        return view('dashboard.guru', $data);
    }

    public function siswa()
    {
        $siswa = Auth::guard('siswa')->user();
        
        // Get Points
        $poin_pelanggaran = Pelanggaran::where('siswa_id', $siswa->id)->sum('poin');
        $poin_prestasi = Prestasi::where('siswa_id', $siswa->id)->sum('poin');
        
        $data = [
            'siswa' => $siswa,
            'poin_pelanggaran' => $poin_pelanggaran,
            'poin_prestasi' => $poin_prestasi,
            'recent_pelanggaran' => Pelanggaran::where('siswa_id', $siswa->id)->latest()->take(3)->get(),
            'recent_prestasi' => Prestasi::where('siswa_id', $siswa->id)->latest()->take(3)->get(),
        ];
        return view('dashboard.siswa', $data);
    }
}
