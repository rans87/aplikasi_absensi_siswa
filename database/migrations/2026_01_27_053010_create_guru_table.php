<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('guru', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel users (akun login)
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');

            $table->string('nip')->nullable()->unique();
            $table->string('nama');
            $table->string('no_hp')->nullable();
            $table->string('foto')->nullable(); // opsional kalau mau foto guru

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru');
    }
};
