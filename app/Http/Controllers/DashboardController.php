<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Absensi;
use App\Models\RombonganBelajar;
use App\Models\AnggotaKelas;
use App\Models\MataPelajaran;
use App\Models\JadwalPelajaran;
use App\Models\TahunAjar;
use App\Models\AbsensiMapel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Admin Dashboard - Heavily optimized with caching and single queries
     */
    public function admin()
    {
        $today = Carbon::today();
        $cacheKey = 'admin_dashboard_' . $today->toDateString();

        // Cache dashboard stats for 2 minutes (refreshes automatically)
        $stats = Cache::remember($cacheKey, 120, function () use ($today) {
            $thisMonth = Carbon::now()->startOfMonth();

            // Single query for all counts using raw DB
            $counts = DB::table('siswa')->selectRaw('
                (SELECT COUNT(*) FROM siswa) as total_siswa,
                (SELECT COUNT(*) FROM guru) as total_guru,
                (SELECT COUNT(*) FROM absensi WHERE DATE(tanggal) = ?) as absensi_hari_ini,
                (SELECT COUNT(*) FROM rombongan_belajar) as total_kelas,
                (SELECT COUNT(*) FROM mata_pelajaran) as total_mapel,
                (SELECT COUNT(*) FROM absensi WHERE tanggal >= ? AND status = "hadir") as monthly_hadir,
                (SELECT COUNT(*) FROM absensi WHERE tanggal >= ? AND status = "terlambat") as monthly_terlambat
            ', [$today->toDateString(), $thisMonth->toDateString(), $thisMonth->toDateString()])
            ->first();

            return $counts;
        });

        // Attendance trend (7 days) - single query instead of 7 separate queries
        $attendance_trend = Cache::remember('admin_attendance_trend_' . $today->toDateString(), 300, function () use ($today) {
            $startDate = $today->copy()->subDays(6);
            
            $raw = DB::table('absensi')
                ->selectRaw('DATE(tanggal) as date, COUNT(*) as count')
                ->whereBetween('tanggal', [$startDate->toDateString(), $today->toDateString()])
                ->groupBy(DB::raw('DATE(tanggal)'))
                ->pluck('count', 'date')
                ->toArray();

            $trend = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $dateStr = $date->toDateString();
                $trend[] = [
                    'date' => $date->format('d M'),
                    'day' => $date->translatedFormat('D'),
                    'count' => $raw[$dateStr] ?? 0,
                ];
            }
            return $trend;
        });

        $qr_session = Cache::get('qr_session', 'pagi');

        $data = [
            'total_siswa' => $stats->total_siswa ?? 0,
            'total_guru' => $stats->total_guru ?? 0,
            'absensi_hari_ini' => $stats->absensi_hari_ini ?? 0,
            'total_kelas' => $stats->total_kelas ?? 0,
            'total_mapel' => $stats->total_mapel ?? 0,
            'monthly_hadir' => $stats->monthly_hadir ?? 0,
            'monthly_terlambat' => $stats->monthly_terlambat ?? 0,
            'qr_session' => $qr_session,
            'attendance_trend' => $attendance_trend,
            'radar_data' => $this->ambilDataRadarAdmin(),
        ];

        return view('dashboard.admin', $data);
    }

    public function switchQr($session)
    {
        if (!in_array($session, ['pagi', 'sore'])) {
            return back()->with('error', 'Sesi tidak valid.');
        }

        Cache::put('qr_session', $session);
        Cache::put('qr_salt', Str::random(8));

        // Clear admin dashboard cache
        Cache::forget('admin_dashboard_' . Carbon::today()->toDateString());

        return back()->with('success', "Berhasil beralih ke sesi QR " . ucfirst($session));
    }

    /**
     * Guru Dashboard - Optimized with fewer queries and smarter caching
     */
    public function guru()
    {
        $user = Auth::guard('guru')->user() ?? Auth::guard('web')->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login.');
        }

        $guru = Auth::guard('guru')->check() 
            ? Guru::with('kelasWali')->find($user->id) 
            : $user;

        $today = Carbon::today();
        $guruCachePrefix = 'guru_' . $guru->id . '_';

        // Single query for today's attendance stats (all statuses at once)
        $attendance_stats = Cache::remember('attendance_stats_' . $today->toDateString(), 120, function () use ($today) {
            return Absensi::today()
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();
        });

        // Today's schedule - uses model scope
        $jadwal_hari_ini = JadwalPelajaran::byGuru($guru->id)
            ->hariIni()
            ->with(['mataPelajaran:id,nama_mapel', 'rombonganBelajar:id,nama_kelas,jurusan'])
            ->ordered()
            ->get();

        // Class reports with attendance count (cached 5 min)
        $classReports = Cache::remember('guru_class_reports_' . $today->toDateString(), 300, function () use ($today) {
            return RombonganBelajar::select('nama_kelas', 'jurusan', 'tingkat', 'id')
                ->withCount(['absensi as hadir_count' => function ($query) use ($today) {
                    $query->whereDate('tanggal', $today)->where('status', 'hadir');
                }])
                ->get();
        });

        // Weekly trend - single query instead of 7 (cached 10 min)
        $weekly_attendance = Cache::remember('guru_weekly_attendance_' . $today->toDateString(), 600, function () use ($today) {
            $startDate = $today->copy()->subDays(6);
            $raw = DB::table('absensi')
                ->selectRaw('DATE(tanggal) as date, COUNT(*) as count')
                ->whereBetween('tanggal', [$startDate->toDateString(), $today->toDateString()])
                ->groupBy(DB::raw('DATE(tanggal)'))
                ->pluck('count', 'date')
                ->toArray();

            $data = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $dateStr = $date->toDateString();
                $data[] = [
                    'day' => $date->translatedFormat('D'),
                    'date' => $date->format('d'),
                    'count' => $raw[$dateStr] ?? 0,
                    'is_today' => $date->isToday(),
                ];
            }
            return $data;
        });

        // Guru-specific counts (cached per guru per day)
        $guruStats = [];

        $recent_absensi = Absensi::with('siswa:id,nama,nis')
            ->today()->latest()->take(5)->get();

        $data = [
            'guru' => $guru,
            'recent_absensi' => $recent_absensi,
            'total_absensi' => array_sum($attendance_stats),
            'classReports' => $classReports,
            'total_hadir_today' => $attendance_stats['hadir'] ?? 0,
            'total_terlambat_today' => $attendance_stats['terlambat'] ?? 0,
            'total_izin_today' => $attendance_stats['izin'] ?? 0,
            'total_sakit_today' => $attendance_stats['sakit'] ?? 0,
            'total_alfa_today' => $attendance_stats['alfa'] ?? 0,
            'weekly_attendance' => $weekly_attendance,
            'jadwal_hari_ini' => $jadwal_hari_ini,
            'radar_data' => $guru->kelasWali ? $this->ambilDataRadarGuru($guru->kelasWali->id) : null,
        ];
        return view('dashboard.guru', $data);
    }

    /**
     * Menampilkan Dashboard untuk Siswa.
     * Mengambil data kehadiran, poin (pelanggaran/prestasi), jadwal hari ini,
     * serta ringkasan evaluasi sikap terbaru untuk ditampilkan di UI.
     */
    public function siswa()
    {
        $siswa = Auth::guard('siswa')->user();
        $today = Carbon::today();

        // Single query for attendance stats
        $attendanceStats = Absensi::bySiswa($siswa->id)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $recent_absensi = Absensi::bySiswa($siswa->id)
            ->orderByDesc('tanggal')
            ->take(7)
            ->get();

        // Student class info - single eager loaded query
        $kelas_info = AnggotaKelas::where('siswa_id', $siswa->id)
            ->with('rombonganBelajar:id,nama_kelas,jurusan,tingkat,wali_kelas_id')
            ->first();

        // Today's schedule for the student's class
        $jadwal_hari_ini = collect();
        if ($kelas_info && $kelas_info->rombonganBelajar) {
            $jadwal_hari_ini = JadwalPelajaran::byRombel($kelas_info->rombongan_belajar_id)
                ->hariIni()
                ->with(['mataPelajaran:id,nama_mapel,kode_mapel', 'guru:id,nama'])
                ->ordered()
                ->get();
        }

        // Absensi mapel hari ini (untuk siswa lihat status per mapel)
        $absensiMapelHariIni = AbsensiMapel::bySiswa($siswa->id)
            ->today()
            ->with('jadwalPelajaran:id,mata_pelajaran_id')
            ->get()
            ->keyBy('jadwal_pelajaran_id');

        // Weekly attendance chart for siswa
        $weeklyChart = Cache::remember('siswa_weekly_' . $siswa->id . '_' . $today->toDateString(), 600, function () use ($siswa, $today) {
            $startDate = $today->copy()->subDays(6);
            $raw = DB::table('absensi')
                ->selectRaw('DATE(tanggal) as date, status')
                ->where('siswa_id', $siswa->id)
                ->whereBetween('tanggal', [$startDate->toDateString(), $today->toDateString()])
                ->get()
                ->groupBy('date')
                ->map(fn($group) => $group->first()->status)
                ->toArray();

            $chart = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $dateStr = $date->toDateString();
                $chart[] = [
                    'day' => $date->translatedFormat('D'),
                    'date' => $date->format('d'),
                    'status' => $raw[$dateStr] ?? null,
                    'is_today' => $date->isToday(),
                ];
            }
            return $chart;
        });

        // Evaluation/Sikap data for Siswa
        $latestAssessment = \App\Models\Assessment::with('details.category')
            ->where('evaluatee_id', $siswa->id)
            ->latest()
            ->first();

        $assessmentHistory = \App\Models\Assessment::where('evaluatee_id', $siswa->id)
            ->latest()
            ->take(5)
            ->get();

        $data = [
            'siswa' => $siswa,
            'total_hadir' => $attendanceStats['hadir'] ?? 0,
            'total_terlambat' => $attendanceStats['terlambat'] ?? 0,
            'total_izin' => $attendanceStats['izin'] ?? 0,
            'total_sakit' => $attendanceStats['sakit'] ?? 0,
            'total_alfa' => $attendanceStats['alfa'] ?? 0,
            'recent_absensi' => $recent_absensi,
            'kelas_info' => $kelas_info,
            'jadwal_hari_ini' => $jadwal_hari_ini,
            'absensiMapelHariIni' => $absensiMapelHariIni,
            'weeklyChart' => $weeklyChart,
            'latestAssessment' => $latestAssessment,
            'assessmentHistory' => $assessmentHistory,
            'radar_data' => $this->ambilDataRadarSiswa($siswa->id),
        ];
        return view('dashboard.siswa', $data);
    }

    /**
     * Wali Kelas Dashboard - Optimized with fewer queries
     */
    public function waliKelas()
    {
        $guru = Auth::guard('guru')->user();
        $rombel = RombonganBelajar::where('wali_kelas_id', $guru->id)->first();

        if (!$rombel) {
            return redirect()->route('guru.dashboard')->with('error', 'Anda belum ditugaskan sebagai Wali Kelas.');
        }

        $today = Carbon::today();
        
        // Load Students with today's attendance in a single query
        $studentIds = AnggotaKelas::where('rombongan_belajar_id', $rombel->id)
            ->pluck('siswa_id');

        $students = Siswa::whereIn('id', $studentIds)
            ->with(['absensi' => function($q) use ($today) {
                $q->whereDate('tanggal', $today);
            }])
            ->orderBy('nama')
            ->get();

        // Sessions for today
        $sessions = JadwalPelajaran::byRombel($rombel->id)
            ->hariIni()
            ->with('mataPelajaran:id,nama_mapel')
            ->ordered()
            ->get();

        // Mapel attendance - single query
        $mapel_attendance = AbsensiMapel::today()
            ->whereIn('siswa_id', $studentIds)
            ->get();

        // Attendance summary (hadir/izin/sakit/alfa counts)
        $attendanceSummary = $students->map(function($student) {
            $todayAbsensi = $student->absensi->first();
            return $todayAbsensi ? $todayAbsensi->status : 'belum';
        })->countBy()->toArray();

        return view('dashboard.wali_kelas', compact(
            'rombel', 'students', 'sessions', 'mapel_attendance', 
            'today', 'attendanceSummary'
        ));
    }

    /**
     * Mengambil data rata-rata skor penilaian per kategori untuk Admin (Global).
     */
    private function ambilDataRadarAdmin()
    {
        return DB::table('assessment_details')
            ->join('assessment_categories', 'assessment_details.category_id', '=', 'assessment_categories.id')
            ->select('assessment_categories.name as label', DB::raw('AVG(score) as value'))
            ->groupBy('assessment_categories.id', 'assessment_categories.name')
            ->get();
    }

    /**
     * Mengambil data rata-rata skor penilaian per kategori untuk Guru (Berdasarkan Kelas Wali).
     */
    private function ambilDataRadarGuru($rombelId)
    {
        return DB::table('assessment_details')
            ->join('assessments', 'assessment_details.assessment_id', '=', 'assessments.id')
            ->join('assessment_categories', 'assessment_details.category_id', '=', 'assessment_categories.id')
            ->join('anggota_kelas', 'assessments.evaluatee_id', '=', 'anggota_kelas.siswa_id')
            ->where('anggota_kelas.rombongan_belajar_id', $rombelId)
            ->select('assessment_categories.name as label', DB::raw('AVG(score) as value'))
            ->groupBy('assessment_categories.id', 'assessment_categories.name')
            ->get();
    }

    /**
     * Mengambil data skor penilaian terbaru untuk Siswa tertentu.
     */
    private function ambilDataRadarSiswa($siswaId)
    {
        $latest = \App\Models\Assessment::where('evaluatee_id', $siswaId)
            ->latest()
            ->first();

        if (!$latest) return collect();

        return DB::table('assessment_details')
            ->join('assessment_categories', 'assessment_details.category_id', '=', 'assessment_categories.id')
            ->where('assessment_id', $latest->id)
            ->select('assessment_categories.name as label', 'score as value')
            ->get();
    }
}
