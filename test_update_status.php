<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = \Illuminate\Http\Request::create('/absen', 'POST', [
    'siswa_id' => 1,
    'jadwal_pelajaran_id' => 1,
    'status' => 'hadir'
]);

try {
    $guru = \App\Models\Guru::first();
    Auth::guard('guru')->login($guru);

    $controller = new \App\Http\Controllers\AbsensiMapelController();
    $response = $controller->updateStatus($request);
    echo "SUCCESS:\n";
    print_r($response->getContent());
} catch (\Throwable $e) {
    echo "ERROR CAUGHT:\n";
    echo $e->getMessage() . "\n";
    echo $e->getFile() . ":" . $e->getLine() . "\n";
}
