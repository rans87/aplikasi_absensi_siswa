<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Performance indexes for PresenceX
     * These indexes dramatically speed up dashboard queries, attendance lookups, and filtering.
     */
    public function up(): void
    {
        // Absensi - Most queried table
        Schema::table('absensi', function (Blueprint $table) {
            $table->index('tanggal', 'idx_absensi_tanggal');
            $table->index('status', 'idx_absensi_status');
            $table->index(['siswa_id', 'tanggal'], 'idx_absensi_siswa_tanggal');
            $table->index(['tanggal', 'status'], 'idx_absensi_tanggal_status');
            $table->index('guru_id', 'idx_absensi_guru_id');
            $table->index('rombongan_belajar_id', 'idx_absensi_rombel_id');
        });

        // Absensi Mapel
        Schema::table('absensi_mapel', function (Blueprint $table) {
            $table->index('tanggal', 'idx_absensi_mapel_tanggal');
            $table->index(['siswa_id', 'tanggal'], 'idx_absensi_mapel_siswa_tanggal');
            $table->index(['jadwal_pelajaran_id', 'tanggal'], 'idx_absensi_mapel_jadwal_tanggal');
            $table->index('guru_id', 'idx_absensi_mapel_guru_id');
            $table->index(['siswa_id', 'jadwal_pelajaran_id', 'tanggal'], 'idx_absensi_mapel_unique_check');
        });

        // Jadwal Pelajaran
        Schema::table('jadwal_pelajaran', function (Blueprint $table) {
            $table->index(['guru_id', 'hari'], 'idx_jadwal_guru_hari');
            $table->index(['rombongan_belajar_id', 'hari'], 'idx_jadwal_rombel_hari');
            $table->index('mata_pelajaran_id', 'idx_jadwal_mapel_id');
        });

        // Anggota Kelas
        Schema::table('anggota_kelas', function (Blueprint $table) {
            $table->index('siswa_id', 'idx_anggota_siswa_id');
            $table->index(['rombongan_belajar_id', 'tahun_ajar_id'], 'idx_anggota_rombel_tahun');
            $table->index(['siswa_id', 'tahun_ajar_id'], 'idx_anggota_siswa_tahun');
        });

        // Pelanggaran
        Schema::table('pelanggaran', function (Blueprint $table) {
            $table->index('siswa_id', 'idx_pelanggaran_siswa_id');
            $table->index('guru_id', 'idx_pelanggaran_guru_id');
        });

        // Prestasi
        Schema::table('prestasi', function (Blueprint $table) {
            $table->index('siswa_id', 'idx_prestasi_siswa_id');
            $table->index('guru_id', 'idx_prestasi_guru_id');
        });

        // Rombongan Belajar
        Schema::table('rombongan_belajar', function (Blueprint $table) {
            $table->index('wali_kelas_id', 'idx_rombel_wali_kelas');
            $table->index('api_rombel_id', 'idx_rombel_api_id');
        });

        // Siswa
        Schema::table('siswa', function (Blueprint $table) {
            $table->index('qr_code', 'idx_siswa_qr_code');
            $table->index('nis', 'idx_siswa_nis');
        });

        // Guru
        Schema::table('guru', function (Blueprint $table) {
            $table->index('email', 'idx_guru_email');
            $table->index('nip', 'idx_guru_nip');
        });

        // Notifikasi Guru
        Schema::table('notifikasi_guru', function (Blueprint $table) {
            $table->index(['guru_id', 'dibaca'], 'idx_notifikasi_guru_dibaca');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->dropIndex('idx_absensi_tanggal');
            $table->dropIndex('idx_absensi_status');
            $table->dropIndex('idx_absensi_siswa_tanggal');
            $table->dropIndex('idx_absensi_tanggal_status');
            $table->dropIndex('idx_absensi_guru_id');
            $table->dropIndex('idx_absensi_rombel_id');
        });

        Schema::table('absensi_mapel', function (Blueprint $table) {
            $table->dropIndex('idx_absensi_mapel_tanggal');
            $table->dropIndex('idx_absensi_mapel_siswa_tanggal');
            $table->dropIndex('idx_absensi_mapel_jadwal_tanggal');
            $table->dropIndex('idx_absensi_mapel_guru_id');
            $table->dropIndex('idx_absensi_mapel_unique_check');
        });

        Schema::table('jadwal_pelajaran', function (Blueprint $table) {
            $table->dropIndex('idx_jadwal_guru_hari');
            $table->dropIndex('idx_jadwal_rombel_hari');
            $table->dropIndex('idx_jadwal_mapel_id');
        });

        Schema::table('anggota_kelas', function (Blueprint $table) {
            $table->dropIndex('idx_anggota_siswa_id');
            $table->dropIndex('idx_anggota_rombel_tahun');
            $table->dropIndex('idx_anggota_siswa_tahun');
        });

        Schema::table('pelanggaran', function (Blueprint $table) {
            $table->dropIndex('idx_pelanggaran_siswa_id');
            $table->dropIndex('idx_pelanggaran_guru_id');
        });

        Schema::table('prestasi', function (Blueprint $table) {
            $table->dropIndex('idx_prestasi_siswa_id');
            $table->dropIndex('idx_prestasi_guru_id');
        });

        Schema::table('rombongan_belajar', function (Blueprint $table) {
            $table->dropIndex('idx_rombel_wali_kelas');
            $table->dropIndex('idx_rombel_api_id');
        });

        Schema::table('siswa', function (Blueprint $table) {
            $table->dropIndex('idx_siswa_qr_code');
            $table->dropIndex('idx_siswa_nis');
        });

        Schema::table('guru', function (Blueprint $table) {
            $table->dropIndex('idx_guru_email');
            $table->dropIndex('idx_guru_nip');
        });

        Schema::table('notifikasi_guru', function (Blueprint $table) {
            $table->dropIndex('idx_notifikasi_guru_dibaca');
        });
    }
};
