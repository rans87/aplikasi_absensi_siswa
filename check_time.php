<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$time = \App\Models\SchoolCalendar::getEntryTimeForDate(now()->toDateString());
echo "ENTRY TIME: " . var_export($time, true) . "\n";
