<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fix user_tokens: ganti user_id -> siswa_id (karena auth siswa pakai tabel siswa)
 * Tambah kolom used_at timestamp
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_tokens', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('user_tokens', function (Blueprint $table) {
            $table->foreignId('siswa_id')->after('id')->constrained('siswa')->onDelete('cascade');
            $table->timestamp('used_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('user_tokens', function (Blueprint $table) {
            $table->dropForeign(['siswa_id']);
            $table->dropColumn(['siswa_id', 'used_at']);
        });

        Schema::table('user_tokens', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
        });
    }
};
