<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migrasi perbaikan tabel-tabel Sistem Poin Integritas
 * - Ubah point_ledgers: user_id -> siswa_id (karena auth siswa pakai tabel siswa)
 * - Tambah kolom condition_type di point_rules (untuk membedakan tipe aturan: waktu/menit telat)
 * - Tambah kolom description, tolerance_minutes, is_active di flexibility_items
 * - Tambah kolom is_active di point_rules
 */
return new class extends Migration {
    public function up(): void
    {
        // 1. Fix point_ledgers: ganti user_id -> siswa_id
        Schema::table('point_ledgers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('point_ledgers', function (Blueprint $table) {
            $table->foreignId('siswa_id')->after('id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('reference_absensi_id')->nullable()->after('description')
                ->constrained('absensi')->onDelete('set null');
            $table->timestamp('updated_at')->nullable()->after('created_at');
        });

        // 2. Tambah kolom di point_rules
        Schema::table('point_rules', function (Blueprint $table) {
            $table->enum('condition_type', ['check_in_time', 'late_minutes'])->default('check_in_time')->after('target_role');
            $table->boolean('is_active')->default(true)->after('point_modifier');
        });

        // 3. Tambah kolom di flexibility_items
        Schema::table('flexibility_items', function (Blueprint $table) {
            $table->text('description')->nullable()->after('item_name');
            $table->integer('tolerance_minutes')->default(30)->after('point_cost');
            $table->boolean('is_active')->default(true)->after('stock_limit');
        });
    }

    public function down(): void
    {
        Schema::table('flexibility_items', function (Blueprint $table) {
            $table->dropColumn(['description', 'tolerance_minutes', 'is_active']);
        });

        Schema::table('point_rules', function (Blueprint $table) {
            $table->dropColumn(['condition_type', 'is_active']);
        });

        Schema::table('point_ledgers', function (Blueprint $table) {
            $table->dropForeign(['siswa_id']);
            $table->dropColumn(['siswa_id', 'reference_absensi_id', 'updated_at']);
        });

        Schema::table('point_ledgers', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
        });
    }
};
