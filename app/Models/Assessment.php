<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluator_id',
        'evaluatee_id',
        'assessment_date',
        'period',
        'general_notes',
    ];

    protected $casts = [
        'assessment_date' => 'date',
    ];

    public function evaluatorUser()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function evaluatorGuru()
    {
        return $this->belongsTo(Guru::class, 'evaluator_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function evaluatee()
    {
        return $this->belongsTo(Siswa::class, 'evaluatee_id');
    }

    public function details()
    {
        return $this->hasMany(AssessmentDetail::class);
    }
}
