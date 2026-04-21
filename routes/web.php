<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\RombonganBelajarController;
use App\Http\Controllers\TahunAjarController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AnggotaKelasController;
use App\Http\Controllers\MataPelajaranController;
use App\Http\Controllers\JadwalPelajaranController;
use App\Http\Controllers\AbsensiMapelController;
use Illuminate\Support\Facades\Auth;

// ============= PUBLIC ROUTES =============
Route::get('/', function() {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/dashboard', function() {
    if (Auth::guard('web')->check()) return redirect()->route('admin.dashboard');
    if (Auth::guard('guru')->check()) return redirect()->route('guru.dashboard');
    if (Auth::guard('siswa')->check()) return redirect()->route('siswa.dashboard');
    return redirect()->route('login');
})->name('dashboard');

// ============= PROTECTED ROUTES =============

// General Auth (Any role)
Route::middleware(['auth:web,guru,siswa'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Siswa Only
Route::middleware(['auth:siswa'])->group(function () {
    Route::get('/panel-siswa', [DashboardController::class, 'siswa'])->name('siswa.dashboard');
});

// Admin Only
Route::middleware(['auth:web'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
    Route::resource('guru', GuruController::class)->except(['show']);
    Route::get('guru-sync-api', [GuruController::class, 'syncApi'])->name('guru.sync');
    Route::resource('pengguna', UserController::class);
    Route::resource('siswa', SiswaController::class);
    Route::get('siswa-sync-api', [SiswaController::class, 'syncApi'])->name('siswa.sync');
    Route::resource('rombongan-belajar', RombonganBelajarController::class);
    Route::get('rombongan-belajar-sync', [RombonganBelajarController::class, 'syncApi'])->name('rombongan-belajar.sync');
    Route::resource('tahun_ajar', TahunAjarController::class);
    Route::resource('anggota-kelas', AnggotaKelasController::class);
    Route::get('anggota-kelas-sync', [AnggotaKelasController::class, 'syncApi'])->name('anggota-kelas.syncApi');
    
    // QR Session Control
    Route::get('/admin/switch-qr/{session}', [DashboardController::class, 'switchQr'])->name('admin.switch-qr');

    // Mata Pelajaran Management (Admin Only)
    Route::resource('mata-pelajaran', MataPelajaranController::class);
    
    // Jadwal Pelajaran Management (Admin Only)
    Route::resource('jadwal-pelajaran', JadwalPelajaranController::class);
    Route::get('jadwal-pelajaran-kelas/{kelasId}', [JadwalPelajaranController::class, 'getByKelas'])->name('jadwal-pelajaran.by-kelas');

    // School Calendar (Admin Only)
    Route::post('school-calendar/bulk', [\App\Http\Controllers\SchoolCalendarController::class, 'bulkStore'])->name('school-calendar.bulk');
    Route::resource('school-calendar', \App\Http\Controllers\SchoolCalendarController::class);
});

// Guru Only Specifics
Route::middleware(['auth:guru'])->group(function () {
    Route::get('/guru-panel', [DashboardController::class, 'guru'])->name('guru.dashboard');
    Route::get('/wali-kelas', [DashboardController::class, 'waliKelas'])->name('wali-kelas.index');
    Route::get('/absensi/scan', [AbsensiController::class, 'scan'])->name('absensi.scan');
    Route::post('/absensi/proses-scan', [AbsensiController::class, 'prosesScan'])->name('absensi.prosesScan');
    
    // Absensi Per Mapel
    Route::get('/absensi-mapel', [AbsensiMapelController::class, 'index'])->name('absensi-mapel.index');
    Route::get('/absensi-mapel/session/{id}', [AbsensiMapelController::class, 'showSession'])->name('absensi-mapel.session');
    Route::post('/absensi-mapel/update-status', [AbsensiMapelController::class, 'updateStatus'])->name('absensi-mapel.update-status');
    Route::post('/absensi-mapel/proses-scan', [AbsensiMapelController::class, 'prosesScanMapel'])->name('absensi-mapel.proses-scan');
    Route::post('/absensi-mapel/selesai', [AbsensiMapelController::class, 'selesaiMengajar'])->name('absensi-mapel.selesai');
    
    // Notifikasi Guru
    Route::get('/notifikasi-guru', [AbsensiMapelController::class, 'getNotifikasi'])->name('notifikasi-guru.get');
    Route::post('/notifikasi-guru/{id}/baca', [AbsensiMapelController::class, 'bacaNotifikasi'])->name('notifikasi-guru.baca');
    Route::post('/notifikasi-guru/baca-semua', [AbsensiMapelController::class, 'bacaSemuaNotifikasi'])->name('notifikasi-guru.baca-semua');
});

// Shared Admin & Guru Management
Route::middleware(['auth:web,guru'])->group(function () {
    Route::resource('absensi', AbsensiController::class)->except(['show']);
    Route::get('guru/{guru}', [GuruController::class, 'show'])->name('guru.show');
    
    // Manual Attendance
    Route::get('/absensi/manual/{rombongan_id}', [AbsensiController::class, 'manualInput'])->name('absensi.manual-input');
    Route::post('/absensi/manual-store', [AbsensiController::class, 'manualStore'])->name('absensi.manual-store');

    // API for search siswa (used in searchable dropdowns)
    Route::get('/api/search-siswa', function (\Illuminate\Http\Request $request) {
        $search = $request->q;
        $siswa = \App\Models\Siswa::where('nama', 'like', "%{$search}%")
            ->orWhere('nis', 'like', "%{$search}%")
            ->orderBy('nama')
            ->limit(20)
            ->get(['id', 'nama', 'nis']);
        return response()->json($siswa);
    })->name('api.search-siswa');
});

// Legacy redirects
Route::get('/siswa/dashboard', function() {
    return redirect()->route('siswa.dashboard');
});

// ============= ASSESSMENT / EVALUASI SIKAP (NEW) =============
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\IntegrityPointController;
use App\Http\Controllers\WalletController;

Route::middleware(['auth:web,guru,siswa'])->group(function () {
    // Admin & Guru can access evaluation system
    Route::middleware(['auth:web,guru'])->group(function () {
        Route::get('/assessments', [AssessmentController::class, 'index'])->name('assessments.index');
        Route::get('/assessments/create/{id}', [AssessmentController::class, 'create'])->name('assessments.create');
        Route::post('/assessments', [AssessmentController::class, 'store'])->name('assessments.store');
        
        // Category Management (Admin Only)
        Route::middleware(['auth:web'])->group(function () {
            Route::get('/assessments/categories', [AssessmentController::class, 'indexCategory'])->name('assessments.categories.index');
            Route::post('/assessments/categories', [AssessmentController::class, 'storeCategory'])->name('assessments.categories.store');
            Route::put('/assessments/categories/{id}', [AssessmentController::class, 'updateCategory'])->name('assessments.categories.update');
            Route::delete('/assessments/categories/{id}', [AssessmentController::class, 'destroyCategory'])->name('assessments.categories.destroy');
        });
    });

    // Report (Admin, Guru, and Siswa for self-view)
    Route::get('/assessments/report/{id}', [AssessmentController::class, 'report'])->name('assessments.report');
    Route::get('/assessments/all-reports', [AssessmentController::class, 'allReports'])->name('assessments.all-reports');
});

// ============= INTEGRITY POINT SYSTEM =============

// Admin & Guru: Kelola Rules, Items, Leaderboard, Manual Award
Route::middleware(['auth:web,guru'])->prefix('integrity')->name('integrity.')->group(function () {
    // Rule Engine Management (Admin Only)
    Route::middleware(['auth:web'])->group(function () {
        Route::get('/rules', [IntegrityPointController::class, 'indexRules'])->name('rules.index');
        Route::post('/rules', [IntegrityPointController::class, 'storeRule'])->name('rules.store');
        Route::put('/rules/{id}', [IntegrityPointController::class, 'updateRule'])->name('rules.update');
        Route::delete('/rules/{id}', [IntegrityPointController::class, 'destroyRule'])->name('rules.destroy');
        Route::post('/rules/{id}/toggle', [IntegrityPointController::class, 'toggleRule'])->name('rules.toggle');

        // Marketplace Items Management
        Route::get('/items', [IntegrityPointController::class, 'indexItems'])->name('items.index');
        Route::post('/items', [IntegrityPointController::class, 'storeItem'])->name('items.store');
        Route::put('/items/{id}', [IntegrityPointController::class, 'updateItem'])->name('items.update');
        Route::delete('/items/{id}', [IntegrityPointController::class, 'destroyItem'])->name('items.destroy');
        Route::post('/items/{id}/toggle', [IntegrityPointController::class, 'toggleItem'])->name('items.toggle');
    });

    // Leaderboard & Analytics (Shared)
    Route::get('/leaderboard', [IntegrityPointController::class, 'leaderboard'])->name('leaderboard');

    // Manual Award (Shared)
    Route::get('/manual', [IntegrityPointController::class, 'indexManualAward'])->name('manual.index');
    Route::post('/manual', [IntegrityPointController::class, 'storeManualAward'])->name('manual.store');
});

// Siswa: Dompet Integritas
Route::middleware(['auth:siswa'])->prefix('wallet')->name('wallet.')->group(function () {
    Route::get('/', [WalletController::class, 'index'])->name('index');
    Route::post('/purchase', [WalletController::class, 'purchaseToken'])->name('purchase');
    Route::get('/riwayat', [WalletController::class, 'riwayatMutasi'])->name('riwayat');
    Route::post('/tokens/use-manual', [WalletController::class, 'useManual'])->name('tokens.use-manual');
    Route::post('/tokens/{id}/toggle-auto', [WalletController::class, 'toggleAutoUse'])->name('tokens.toggle-auto');
});

