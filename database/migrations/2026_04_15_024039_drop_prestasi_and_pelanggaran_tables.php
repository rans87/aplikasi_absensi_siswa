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
        // Drop the achievement and violation related tables
        Schema::dropIfExists('poin_siswa');
        Schema::dropIfExists('prestasi');
        Schema::dropIfExists('pelanggaran');
        
        // Remove index from performance if it exists (ref: 2026_03_09_002530_add_performance_indexes)
        // Since we are removing the tables, we don't need to manually remove indexes on non-existent tables.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No going back as per user request
    }
};
