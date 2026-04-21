<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\RombonganBelajar;
use App\Models\AnggotaKelas;
use App\Services\IntegrityPointService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    /* Laporan & Filter Absensi (Harian/Mapel) */
    public function index(Request $request)
    {
        $tanggal = $request->tanggal ?? now()->toDateString();
        $search = $request->search;
        $rombel_id = $request->rombongan_belajar_id;
        $mapel_id = $request->mata_pelajaran_id;
        $status = $request->status;
        $type = $request->type ?? ($mapel_id ? 'mapel' : 'harian');

        // Logic for Dynamic Filtering
        if ($type === 'mapel') {
            $query = \App\Models\AbsensiMapel::with([
                'siswa:id,nama,nis', 
                'guru:id,nama', 
                'jadwalPelajaran.rombonganBelajar:id,nama_kelas',
                'jadwalPelajaran.mataPelajaran:id,nama_mapel'
            ])
            ->whereDate('tanggal', $tanggal);

            if ($mapel_id) {
                $query->whereHas('jadwalPelajaran', fn($q) => $q->where('mata_pelajaran_id', $mapel_id));
            }

            if ($rombel_id) {
                $query->whereHas('jadwalPelajaran', fn($q) => $q->where('rombongan_belajar_id', $rombel_id));
            }
        } else {
            $query = Absensi::with(['siswa:id,nama,nis', 'guru:id,nama', 'rombonganBelajar:id,nama_kelas'])
                ->byDate($tanggal);
            
            if ($rombel_id) {
                $query->where('rombongan_belajar_id', $rombel_id);
            }   
        }

        // Apply Status & Search Filters
        $query->when($status, fn($q) => $q->where('status', $status))
              ->when($search, function ($q) use ($search) {
                $q->whereHas('siswa', function ($sq) use ($search) {
                    $sq->where('nama', 'like', "%{$search}%")
                      ->orWhere('nis', 'like', "%{$search}%");
                });
            });

        $absensi = $query->latest()
            ->paginate(20)
            ->withQueryString();

        // 📊 ACCURATE STATISTICS CALCULATION
        $statQuery = ($type === 'mapel') 
            ? \App\Models\AbsensiMapel::whereDate('tanggal', $tanggal)
            : Absensi::byDate($tanggal);

        if ($rombel_id) {
            if ($type === 'mapel') {
                $statQuery->whereHas('jadwalPelajaran', fn($q) => $q->where('rombongan_belajar_id', $rombel_id));
                if ($mapel_id) $statQuery->whereHas('jadwalPelajaran', fn($q) => $q->where('mata_pelajaran_id', $mapel_id));
            } else {
                $statQuery->where('rombongan_belajar_id', $rombel_id);
            }
        }

        $summary = (clone $statQuery)->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Calculate Attendance Percentage if Rombel is selected
        $totalSiswa = 0;
        $attendanceRate = 0;
        if ($rombel_id) {
            $totalSiswa = AnggotaKelas::where('rombongan_belajar_id', $rombel_id)->count();
            if ($totalSiswa > 0) {
                $hadirCount = $summary['hadir'] ?? 0;
                $attendanceRate = round(($hadirCount / $totalSiswa) * 100, 1);
            }
        }

        // Contextual data for dropdowns
        $rombonganList = RombonganBelajar::orderBy('nama_kelas')->get(['id', 'nama_kelas']);
        $mapelList = \App\Models\MataPelajaran::orderBy('nama_mapel')->get(['id', 'nama_mapel']);

        return view('absensi.index', compact(
            'absensi', 'tanggal', 'summary', 'rombonganList', 
            'mapelList', 'totalSiswa', 'attendanceRate', 'type'
        ));
    }

    /* Form Tambah Absensi */
    public function create()
    {
        $siswa = Siswa::orderBy('nama')->get(['id', 'nama', 'nis']);
        $rombongan = RombonganBelajar::orderBy('nama_kelas')->get(['id', 'nama_kelas', 'jurusan', 'tingkat']);
        $guru = Guru::orderBy('nama')->get(['id', 'nama']);

        return view('absensi.create', compact('siswa', 'rombongan', 'guru'));
    }

    /* Simpan Data Absensi Baru */
    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required',
            'guru_id' => 'required',
            'rombongan_belajar_id' => 'required',
            'tanggal' => 'required|date',
            'status' => 'required',
        ]);

        $absensi = Absensi::create($request->all());

        // === TRIGGER RULE ENGINE ===
        if (in_array($absensi->status, ['hadir', 'terlambat'])) {
            $service = new IntegrityPointService();
            $service->prosesAbsensi($absensi);
        }

        // Clear relevant caches
        $this->clearAbsensiCache();

        return redirect()->route('absensi.index')
            ->with('success', 'Data absensi berhasil disimpan');
    }

    /* Hapus Data Absensi */
    public function destroy(Absensi $absensi)
    {
        $absensi->delete();
        $this->clearAbsensiCache();

        return back()->with('success', 'Data absensi dihapus');
    }

    /**
     * FUNGSI: Menampilkan Halaman Camera Scanner
     * Tujuan: Mengarahkan pengguna (Guru) ke halaman tampilan scanner (View) 
     *         dan mengirimkan data 10 absensi terakhir hari ini.
     */
    public function scan()
    {
        // Mengambil 10 data absen terbaru hari ini beserta nama dan NIS siswa
        $recent_absensi = Absensi::with('siswa:id,nama,nis')
            ->today()
            ->latest()
            ->take(10)
            ->get();

        // Mengembalikan tampilan halaman 'absensi.scan' sambil membawa data absensi terbaru
        return view('absensi.scan', compact('recent_absensi'));
    }

    /**
     * FUNGSI: Memproses Data Hasil Scan QR
     * Tujuan: Menerima teks QR dari tampilan scanner, memecahnya, 
     *         memvalidasi data siswa, dan menyimpannya ke tabel Absensi.
     */
    public function prosesScan(Request $request)
    {
        // 1. Memastikan bahwa kotak input QR Code ada isinya (tidak boleh kosong)
        $request->validate(['qr_code' => 'required']);

        // 2. Memecah teks QR Code menjadi beberapa bagian menggunakan pemisah '|'
        $data = explode('|', $request->qr_code);
        
        // 3. Validasi Keamanan: Jika potongan data kurang dari 3, berarti QR tidak sah
        if (count($data) < 3) {
            return back()->with('error', 'Format QR Code tidak valid.');
        }

        $qrCodeHash = $data[0];
        $qrSession = $data[1];
        $qrSalt = $data[2];

        // 4. Validasi Sesi: Mengecek apakah QR Code yang di-scan sesuai dengan pengaturan sesi saat ini
        $currentSession = Cache::get('qr_session', 'pagi');
        $currentSalt = Cache::get('qr_salt', 'init');

        if ($qrSession !== $currentSession || $qrSalt !== $currentSalt) {
            return back()->with('error', 'QR Code kadaluarsa atau sesi sudah berganti.');
        }

        // 5. Pencarian Data: Mencari identitas siswa di database berdasarkan hash QR Code
        $siswa = Siswa::where('qr_code', $qrCodeHash)->first(['id', 'nama', 'nis']);

        if (!$siswa) {
            // Jika siswa tidak ditemukan, proses berhenti
            return back()->with('error', 'Siswa tidak ditemukan dalam database.');
        }

        // 6. Validasi Kelas: Memastikan siswa tersebut sudah memiliki kelas
        $anggota = AnggotaKelas::where('siswa_id', $siswa->id)->latest()->first(['id', 'rombongan_belajar_id']);
        
        if (!$anggota) {
            return back()->with('error', 'Siswa belum terdaftar di kelas manapun.');
        }

        // 7. Cek Duplikasi: Memastikan siswa belum tercatat hadir/terlambat di hari yang sama
        $sudahAbsen = Absensi::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', Carbon::today())
            ->exists();

        if ($sudahAbsen) {
            return back()->with('error', 'Peringatan: Siswa ini sudah melakukan absensi hari ini.');
        }

        // 8. Cek Jam Masuk & Hari Libur
        $jamMasukHariIni = \App\Models\SchoolCalendar::getEntryTimeForDate(Carbon::today());
        if (!$jamMasukHariIni) {
            return back()->with('error', 'Hari ini adalah hari libur sekolah. Absensi kehadiran tidak aktif.');
        }

        // 9. Eksekusi Simpan Data
        $waktuSekarang = now()->format('H:i:s');
        $service = new IntegrityPointService();
        $lateMinutes = $service->hitungMenitTerlambat($waktuSekarang, Carbon::today()->toDateString());
        $status = $lateMinutes > 0 ? 'terlambat' : 'hadir';

        $absensi = Absensi::create([
            'siswa_id' => $siswa->id,
            'guru_id' => Auth::guard('guru')->id(), 
            'rombongan_belajar_id' => $anggota->rombongan_belajar_id,
            'tanggal' => Carbon::today(),
            'status' => $status,
        ]);

        // 10. === TRIGGER RULE ENGINE OTOMATIS ===
        $pointResult = $service->prosesAbsensi($absensi, $waktuSekarang);

        // Buat pesan sukses dengan info poin yang mendetail
        $message = "Scan Berhasil: " . $siswa->nama . " (" . strtoupper($status) . ")";
        
        if (count($pointResult['rules_triggered']) > 0) {
            $poinDetails = [];
            foreach($pointResult['rules_triggered'] as $rt) {
                $pref = $rt['point_modifier'] > 0 ? '+' : '';
                $poinDetails[] = "{$rt['rule_name']} ({$pref}{$rt['point_modifier']})";
            }
            $message .= " | " . implode(", ", $poinDetails);
        }

        if ($pointResult['token_used']) {
            $message .= " | 🎫 Token '{$pointResult['token_used']['item_name']}' aktif!";
        }

        // 10. Membersihkan cache agar angka statistik di dashboard langsung ter-update
        $this->clearAbsensiCache();

        // 11. Mengembalikan tampilan dengan pesan berhasil
        return back()->with('success', $message);
    }

    /* Halaman Input Manual Per Kelas */
    public function manualInput($rombongan_belajar_id)
    {
        $rombongan = RombonganBelajar::findOrFail($rombongan_belajar_id, ['id', 'nama_kelas', 'jurusan']);
        
        // Get students in this class with today's existing attendance
        $siswa = Siswa::whereHas('anggotaKelas', function ($q) use ($rombongan_belajar_id) {
            $q->where('rombongan_belajar_id', $rombongan_belajar_id);
        })
        ->with(['absensi' => function($q) {
            $q->today();
        }])
        ->orderBy('nama')
        ->get(['id', 'nama', 'nis', 'jenis_kelamin']);

        return view('absensi.manual', compact('rombongan', 'siswa'));
    }

    /* Simpan Absensi Massal Per Kelas */
    public function manualStore(Request $request)
    {
        $request->validate([
            'rombongan_belajar_id' => 'required',
            'tanggal' => 'required|date',
            'attendance' => 'required|array',
        ]);

        $guru_id = Auth::guard('guru')->id() ?? Auth::id();
        $tanggal = $request->tanggal;
        $rombongan_id = $request->rombongan_belajar_id;

        // Use DB transaction for batch inserts
        $service = new IntegrityPointService();
        \DB::transaction(function () use ($request, $guru_id, $tanggal, $rombongan_id, $service) {
            foreach ($request->attendance as $siswa_id => $status) {
                if ($status) {
                    $absensi = Absensi::updateOrCreate(
                        [
                            'siswa_id' => $siswa_id,
                            'tanggal' => $tanggal,
                        ],
                        [
                            'guru_id' => $guru_id,
                            'rombongan_belajar_id' => $rombongan_id,
                            'status' => $status,
                        ]
                    );

                    // === TRIGGER RULE ENGINE untuk setiap siswa ===
                    if (in_array($status, ['hadir', 'terlambat'])) {
                        $service->prosesAbsensi($absensi);
                    }
                }
            }
        });

        $this->clearAbsensiCache();

        return redirect()->route('guru.dashboard')->with('success', 'Absensi manual berhasil disimpan.');
    }

    /**
     * Clear all absensi-related caches
     */
    private function clearAbsensiCache(): void
    {
        $today = Carbon::today()->toDateString();
        Cache::forget('admin_dashboard_' . $today);
        Cache::forget('admin_attendance_trend_' . $today);
        Cache::forget('attendance_stats_' . $today);
        Cache::forget('guru_class_reports_' . $today);
        Cache::forget('guru_weekly_attendance_' . $today);
    }
}
