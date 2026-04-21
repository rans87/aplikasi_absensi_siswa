<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$siswa = \App\Models\Siswa::first();
$anggota = \App\Models\AnggotaKelas::where('siswa_id', $siswa->id)->latest()->first();

echo "Siswa: " . ($siswa ? $siswa->nama : 'Not Found') . "\n";
echo "Anggota: " . ($anggota ? 'Found (Rombel ID: '.$anggota->rombongan_belajar_id.')' : 'Not Found') . "\n";

$sudahAbsen = \App\Models\Absensi::where('siswa_id', $siswa->id)
    ->whereDate('tanggal', \Carbon\Carbon::today())
    ->exists();
echo "Sudah Absen: " . ($sudahAbsen ? 'Ya' : 'Belum') . "\n";
