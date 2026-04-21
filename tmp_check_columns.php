<?php
$columns = Schema::getColumnListing('jadwal_pelajaran');
foreach ($columns as $column) {
    echo $column . PHP_EOL;
}
