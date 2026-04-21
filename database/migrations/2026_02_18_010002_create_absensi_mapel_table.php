<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi_mapel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('jadwal_pelajaran_id')->constrained('jadwal_pelajaran')->onDelete('cascade');
            $table->foreignId('guru_id')->nullable()->constrained('guru')->onDelete('set null');
            $table->date('tanggal');
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpha'])->default('hadir');
            $table->timestamp('waktu_scan')->nullable();
            $table->timestamps();

            // Prevent duplicate attendance per student per schedule per day
            $table->unique(['siswa_id', 'jadwal_pelajaran_id', 'tanggal'], 'unique_absensi_mapel');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_mapel');
    }
};
