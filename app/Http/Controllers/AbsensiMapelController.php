<?php

namespace App\Http\Controllers;

use App\Models\AbsensiMapel;
use App\Models\JadwalPelajaran;
use App\Models\NotifikasiGuru;
use App\Models\Siswa;
use App\Models\AnggotaKelas;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AbsensiMapelController extends Controller
{
    /**
     * Guru: Show today's per-subject attendance for their classes
     */
    /* Halaman Utama Absensi Mapel */
    public function index(Request $request)
    {
        $guru = Auth::guard('guru')->user();
        $tanggal = $request->tanggal ?? now()->toDateString();

        $hari = JadwalPelajaran::HARI_MAP[Carbon::parse($tanggal)->format('l')] ?? 'Senin';

        // Get this teacher's schedule for today - selective columns
        $jadwalHariIni = JadwalPelajaran::with([
                'rombonganBelajar:id,nama_kelas,jurusan', 
                'mataPelajaran:id,nama_mapel,kode_mapel'
            ])
            ->byGuru($guru->id)
            ->where('hari', $hari)
            ->orderBy('urutan')
            ->get();

        // Get attendance records - selective loading
        $absensi = AbsensiMapel::with([
                'siswa:id,nama,nis', 
                'jadwalPelajaran:id,mata_pelajaran_id,rombongan_belajar_id',
                'jadwalPelajaran.mataPelajaran:id,nama_mapel', 
                'jadwalPelajaran.rombonganBelajar:id,nama_kelas'
            ])
            ->byGuru($guru->id)
            ->whereDate('tanggal', $tanggal)
            ->latest()
            ->get();

        return view('absensi_mapel.index', compact('jadwalHariIni', 'absensi', 'tanggal', 'hari', 'guru'));
    }

    /**
     * Guru: Show specific session attendance with student list
     */
    /* Sesi Absensi Berjalan */
    public function showSession($id)
    {
        $guru = Auth::guard('guru')->user();
        $jadwal = JadwalPelajaran::with([
                'rombonganBelajar:id,nama_kelas,jurusan', 
                'mataPelajaran:id,nama_mapel,kode_mapel'
            ])
            ->where('guru_id', $guru->id)
            ->findOrFail($id);
        
        // Get all students in this class - only needed columns
        $siswa = Siswa::whereHas('anggotaKelas', function($q) use ($jadwal) {
            $q->where('rombongan_belajar_id', $jadwal->rombongan_belajar_id);
        })->orderBy('nama')->get(['id', 'nama', 'nis', 'jenis_kelamin', 'qr_code']);

        // Get existing attendance for this session today
        $absensi = AbsensiMapel::byJadwal($id)
            ->today()
            ->get()
            ->keyBy('siswa_id');

        // Count stats
        $stats = [
            'total' => $siswa->count(),
            'hadir' => $absensi->where('status', 'hadir')->count(),
            'sakit' => $absensi->where('status', 'sakit')->count(),
            'izin' => $absensi->where('status', 'izin')->count(),
            'dispen' => $absensi->where('status', 'dispen')->count(),
            'alfa' => $absensi->where('status', 'alfa')->count(),
        ];

        return view('absensi_mapel.session', compact('jadwal', 'siswa', 'absensi', 'guru', 'stats'));
    }

    /**
     * Guru: Manually update status (Sakit, Dispen, Alfa, etc)
     */
    /* Update Status Absensi Manual */
    public function updateStatus(Request $request)
    {
        try {
            $request->validate([
                'siswa_id' => 'required|exists:siswa,id',
                'jadwal_pelajaran_id' => 'required|exists:jadwal_pelajaran,id',
                'status' => 'required|in:hadir,sakit,izin,dispen,alfa',
            ]);

            $guru = Auth::guard('guru')->user();
            if (!$guru) {
                return response()->json(['success' => false, 'message' => 'Sesi Anda telah berakhir. Silakan login kembali.'], 401);
            }

            $absensi = AbsensiMapel::updateOrCreate(
                [
                    'siswa_id' => $request->siswa_id,
                    'jadwal_pelajaran_id' => $request->jadwal_pelajaran_id,
                    'tanggal' => Carbon::today(),
                ],
                [
                    'guru_id' => $guru->id,
                    'status' => $request->status,
                    'waktu_scan' => $request->status == 'hadir' ? Carbon::now() : null,
                ]
            );

            // === INTEGRITY POINT TRIGGER ===
            if (in_array($request->status, ['hadir', 'terlambat'])) {
                $service = new \App\Services\IntegrityPointService();
                $service->prosesAbsensi($absensi, now()->format('H:i:s'));
            }

            // Get updated count for this session
            $sessionStats = AbsensiMapel::byJadwal($request->jadwal_pelajaran_id)
                ->today()
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            return response()->json([
                'success' => true,
                'message' => 'Status absensi berhasil diperbarui.',
                'status' => $request->status,
                'stats' => $sessionStats,
            ]);
        } catch (\Exception $e) {
            \Log::error('Update Status Error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Gagal memperbarui status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process QR scan for per-subject attendance
     */
    /* Proses Scan QR Absensi */
    public function prosesScanMapel(Request $request)
    {
        $request->validate([
            'qr_code' => 'required',
            'jadwal_pelajaran_id' => 'required|exists:jadwal_pelajaran,id',
        ]);

        $guru = Auth::guard('guru')->user();

        // Parse QR
        $data = explode('|', $request->qr_code);
        if (count($data) < 1) {
            return response()->json(['success' => false, 'message' => 'Format QR Code tidak valid.'], 400);
        }

        $qrCodeHash = $data[0];

        // Find student (indexed query)
        $siswa = Siswa::where('qr_code', $qrCodeHash)->first(['id', 'nama', 'nis']);
        if (!$siswa) {
            return response()->json(['success' => false, 'message' => 'Siswa tidak ditemukan.'], 404);
        }

        // Verify student belongs to the scheduled class (indexed query)
        $jadwal = JadwalPelajaran::findOrFail($request->jadwal_pelajaran_id, ['id', 'rombongan_belajar_id']);
        $isAnggota = AnggotaKelas::where('siswa_id', $siswa->id)
            ->where('rombongan_belajar_id', $jadwal->rombongan_belajar_id)
            ->exists();

        if (!$isAnggota) {
            return response()->json(['success' => false, 'message' => 'Siswa tidak terdaftar di kelas ini.'], 403);
        }

        // Check duplicate (uses composite index)
        $sudahAbsen = AbsensiMapel::bySiswa($siswa->id)
            ->byJadwal($request->jadwal_pelajaran_id)
            ->today()
            ->exists();

        if ($sudahAbsen) {
            return response()->json(['success' => false, 'message' => 'Siswa sudah absen untuk mata pelajaran ini hari ini.'], 409);
        }

        // Create attendance
        $absensiMapel = AbsensiMapel::create([
            'siswa_id' => $siswa->id,
            'jadwal_pelajaran_id' => $request->jadwal_pelajaran_id,
            'guru_id' => $guru->id,
            'tanggal' => Carbon::today(),
            'status' => 'hadir',
            'waktu_scan' => Carbon::now(),
        ]);

        // === INTEGRITY POINT TRIGGER ===
        $service = new \App\Services\IntegrityPointService();
        $pointResult = $service->prosesAbsensi($absensiMapel, now()->format('H:i:s'));
        
        $pointMessage = "";
        if ($pointResult['poin_total'] != 0) {
            $poinStr = $pointResult['poin_total'] > 0 ? "+{$pointResult['poin_total']}" : (string)$pointResult['poin_total'];
            $pointMessage = " | Poin: {$poinStr}";
        }

        // Get updated count
        $hadirCount = AbsensiMapel::byJadwal($request->jadwal_pelajaran_id)
            ->today()
            ->byStatus('hadir')
            ->count();

        return response()->json([
            'success' => true,
            'message' => "Absensi berhasil: {$siswa->nama}{$pointMessage}",
            'siswa' => $siswa->nama,
            'nis' => $siswa->nis,
            'hadir_count' => $hadirCount,
            'point_info' => $pointResult
        ]);
    }

    /**
     * Guru: Mark current class as finished
     */
    /* Selesai Mengajar & Kirim Notifikasi */
    public function selesaiMengajar(Request $request)
    {
        $request->validate([
            'jadwal_pelajaran_id' => 'required|exists:jadwal_pelajaran,id',
        ]);

        $guru = Auth::guard('guru')->user();
        $currentJadwal = JadwalPelajaran::with([
            'rombonganBelajar:id,nama_kelas', 
            'mataPelajaran:id,nama_mapel'
        ])->findOrFail($request->jadwal_pelajaran_id);

        // Find the next schedule
        $nextJadwal = JadwalPelajaran::with([
                'guru:id,nama', 
                'mataPelajaran:id,nama_mapel', 
                'rombonganBelajar:id,nama_kelas'
            ])
            ->where('rombongan_belajar_id', $currentJadwal->rombongan_belajar_id)
            ->where('hari', $currentJadwal->hari)
            ->where('urutan', '>', $currentJadwal->urutan)
            ->orderBy('urutan')
            ->first();

        if ($nextJadwal && $nextJadwal->guru) {
            NotifikasiGuru::create([
                'guru_id' => $nextJadwal->guru_id,
                'jadwal_pelajaran_id' => $nextJadwal->id,
                'from_guru_id' => $guru->id,
                'judul' => '🔔 Waktunya Mengajar!',
                'pesan' => "Pak/Bu {$guru->nama} telah selesai mengajar {$currentJadwal->mataPelajaran->nama_mapel} di kelas {$currentJadwal->rombonganBelajar->nama_kelas}. Sekarang giliran Anda mengajar {$nextJadwal->mataPelajaran->nama_mapel}.",
                'tipe' => 'mengajar',
            ]);

            return back()->with('success', "Pelajaran selesai! Notifikasi telah dikirim ke {$nextJadwal->guru->nama} untuk mengajar {$nextJadwal->mataPelajaran->nama_mapel}.");
        }

        return back()->with('success', 'Pelajaran selesai! Ini adalah jam pelajaran terakhir untuk kelas ini hari ini.');
    }

    /**
     * Get notifications (JSON for polling) - Optimized with limit
     */
    /* Ambil Notifikasi Guru (Polling) */
    public function getNotifikasi()
    {
        $guru = Auth::guard('guru')->user();

        $notifikasi = NotifikasiGuru::with([
                'fromGuru:id,nama', 
                'jadwalPelajaran:id,mata_pelajaran_id,rombongan_belajar_id',
                'jadwalPelajaran.mataPelajaran:id,nama_mapel', 
                'jadwalPelajaran.rombonganBelajar:id,nama_kelas'
            ])
            ->where('guru_id', $guru->id)
            ->where('dibaca', false)
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'count' => $notifikasi->count(),
            'data' => $notifikasi,
        ]);
    }

    /**
     * Mark notification as read
     */
    /* Baca Notifikasi Tunggal */
    public function bacaNotifikasi($id)
    {
        NotifikasiGuru::where('guru_id', Auth::guard('guru')->id())
            ->where('id', $id)
            ->update(['dibaca' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    /* Baca Semua Notifikasi */
    public function bacaSemuaNotifikasi()
    {
        NotifikasiGuru::where('guru_id', Auth::guard('guru')->id())
            ->where('dibaca', false)
            ->update(['dibaca' => true]);

        return response()->json(['success' => true]);
    }
}
