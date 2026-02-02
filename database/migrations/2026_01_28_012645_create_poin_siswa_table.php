<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('poin_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('guru_id')->constrained('guru')->cascadeOnDelete();
            $table->enum('jenis', ['pelanggaran', 'prestasi']);
            $table->foreignId('pelanggaran_id')->nullable()->constrained('pelanggaran')->nullOnDelete();
            $table->foreignId('prestasi_id')->nullable()->constrained('prestasi')->nullOnDelete();
            $table->integer('poin');
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poin_siswa');
    }
};
