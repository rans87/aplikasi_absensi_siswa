<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('evaluator_id'); // Bisa ID Guru atau Admin
            $table->foreignId('evaluatee_id')->constrained('siswa')->onDelete('cascade');
            $table->date('assessment_date');
            $table->string('period'); 
            $table->text('general_notes')->nullable();
            $table->timestamps();

            $table->index(['period', 'assessment_date', 'evaluator_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};