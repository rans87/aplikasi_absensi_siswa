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
use App\Http\Controllers\PelanggaranController;
use App\Http\Controllers\PrestasiController;
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
    Route::resource('guru', GuruController::class);
    Route::resource('pengguna', UserController::class);
    Route::resource('siswa', SiswaController::class);
    Route::get('siswa-sync-api', [SiswaController::class, 'syncApi'])->name('siswa.sync');
    Route::resource('rombongan-belajar', RombonganBelajarController::class);
    Route::get('rombongan-belajar-sync', [RombonganBelajarController::class, 'syncApi'])->name('rombongan-belajar.sync');
    Route::resource('tahun_ajar', TahunAjarController::class);
    Route::resource('anggota-kelas', AnggotaKelasController::class);
    Route::post('anggota-kelas-sync', [AnggotaKelasController::class, 'syncApi'])->name('anggota-kelas.syncApi');
    
    // QR Session Control
    Route::get('/admin/switch-qr/{session}', [DashboardController::class, 'switchQr'])->name('admin.switch-qr');
});

// Guru Only
Route::middleware(['auth:guru'])->group(function () {
    Route::get('/guru-panel', [DashboardController::class, 'guru'])->name('guru.dashboard');
    Route::get('/absensi/scan', [AbsensiController::class, 'scan'])->name('absensi.scan');
    Route::post('/absensi/proses-scan', [AbsensiController::class, 'prosesScan'])->name('absensi.prosesScan');
    Route::resource('absensi', AbsensiController::class)->except(['show']);
});

// Admin & Guru (Points Management)
Route::middleware(['auth:web,guru'])->group(function () {
    Route::resource('pelanggaran', PelanggaranController::class);
    Route::resource('prestasi', PrestasiController::class);
});

// Legacy redirects
Route::get('/siswa/dashboard', function() {
    return redirect()->route('siswa.dashboard');
});
