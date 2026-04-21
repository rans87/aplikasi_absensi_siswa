<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Remove FK constraint from point_ledgers
        Schema::table('point_ledgers', function (Blueprint $table) {
            // Check if foreign key exists (standard name based on migration)
            try {
                $table->dropForeign(['reference_absensi_id']);
            } catch (\Exception $e) {
                // If it fails, maybe it has a custom name or already dropped
            }
        });

        // 2. Remove FK constraint from user_tokens
        Schema::table('user_tokens', function (Blueprint $table) {
            try {
                $table->dropForeign(['used_at_attendance_id']);
            } catch (\Exception $e) {
                // If it fails
            }
        });

        // 3. Add polymorphic type column to point_ledgers (Optional but good)
        Schema::table('point_ledgers', function (Blueprint $table) {
            $table->string('reference_type')->nullable()->after('reference_absensi_id');
        });

        // 4. Add polymorphic type column to user_tokens
        Schema::table('user_tokens', function (Blueprint $table) {
            $table->string('used_at_attendance_type')->nullable()->after('used_at_attendance_id');
        });
    }

    public function down(): void
    {
        Schema::table('user_tokens', function (Blueprint $table) {
            $table->dropColumn('used_at_attendance_type');
            $table->foreign('used_at_attendance_id')->references('id')->on('absensi')->onDelete('set null');
        });

        Schema::table('point_ledgers', function (Blueprint $table) {
            $table->dropColumn('reference_type');
            $table->foreign('reference_absensi_id')->references('id')->on('absensi')->onDelete('set null');
        });
    }
};
