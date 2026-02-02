<?php

use App\Http\Controllers\PenggunaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\RombonganBelajarController;
use App\Http\Controllers\TahunAjarController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AnggotaKelasController;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;

Route::get('/', function () {
    return redirect()->route('dashboard');
}); 

Route::get('/dashboard', function () {
    return view('dashboard.home');
})->name('dashboard');


Route::resource('guru', GuruController::class);
Route::resource('pengguna', PenggunaController::class);
Route::resource('siswa', SiswaController::class);
Route::resource('rombongan-belajar', RombonganBelajarController::class);
Route::get('rombongan-belajar-sync', [RombonganBelajarController::class, 'syncApi'])
    ->name('rombongan-belajar.sync');
Route::resource('rombongan-belajar', RombonganBelajarController::class);
Route::get('/absensi/scan', [AbsensiController::class, 'scan'])->name('absensi.scan');
Route::post('/absensi/proses-scan', [AbsensiController::class, 'prosesScan'])->name('absensi.prosesScan');

Route::resource('siswa', SiswaController::class);
Route::get('siswa-sync-api', [SiswaController::class, 'syncApi'])->name('siswa.sync');

Route::resource('absensi', AbsensiController::class)->except(['show']);

Route::middleware(['auth'])->group(function () {
    Route::resource('tahun_ajar', TahunAjarController::class);
});

Route::get('/anggota-kelas', [AnggotaKelasController::class, 'index'])->name('anggota-kelas.index');
Route::get('/anggota-kelas/tambah', [AnggotaKelasController::class, 'create'])->name('anggota-kelas.create');
Route::post('/anggota-kelas', [AnggotaKelasController::class, 'store'])->name('anggota-kelas.store');