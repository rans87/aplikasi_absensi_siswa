<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RombonganBelajar;

$stats = RombonganBelajar::whereNotNull('wali_kelas_id')->count();
$total = RombonganBelajar::count();
echo "Classes with Wali Kelas: $stats / Total: $total\n";

$sample = RombonganBelajar::whereNotNull('wali_kelas_id')->first();
if ($sample) {
    echo "Sample Wali Kelas ID: " . $sample->wali_kelas_id . " for Class: " . $sample->nama_kelas . "\n";
}
