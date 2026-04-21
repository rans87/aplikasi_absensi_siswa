<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Builder;

class Siswa extends Authenticatable
{
    use HasFactory;

    protected $table = 'siswa';

    protected $fillable = [
        'nis',
        'nama',
        'no_hp',
        'jenis_kelamin',
        'qr_code',
    ];

    protected static function booted()
    {
        static::creating(function ($siswa) {
            if (empty($siswa->qr_code)) {
                $siswa->qr_code = (string) Str::uuid();
            }
        });
    }

    // ===== SCOPES =====

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (!$search) return $query;
        return $query->where('nama', 'like', "%{$search}%")
                     ->orWhere('nis', 'like', "%{$search}%");
    }

    // ===== RELATIONSHIPS =====

    public function anggotaKelas()
    {
        return $this->hasMany(AnggotaKelas::class);
    }

    public function currentKelas()
    {
        return $this->hasOne(AnggotaKelas::class)->latestOfMany();
    }

    public function rombonganBelajar()
    {
        return $this->hasOneThrough(
            RombonganBelajar::class, 
            AnggotaKelas::class,
            'siswa_id',
            'id',
            'id',
            'rombongan_belajar_id'
        );
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

    public function absensiMapel()
    {
        return $this->hasMany(AbsensiMapel::class);
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'evaluatee_id');
    }

    // ===== COMPUTED ATTRIBUTES =====



    // ===== INTEGRITY POINT SYSTEM =====

    public function pointLedgers()
    {
        return $this->hasMany(PointLedger::class);
    }

    public function userTokens()
    {
        return $this->hasMany(UserToken::class);
    }

    public function availableTokens()
    {
        return $this->hasMany(UserToken::class)->where('status', 'AVAILABLE');
    }

    /**
     * Saldo poin integritas terkini
     */
    public function getIntegrityBalanceAttribute(): int
    {
        return PointLedger::getCurrentBalance($this->id);
    }

    /**
     * Level integritas berdasarkan saldo poin
     */
    public function getIntegrityLevelAttribute(): array
    {
        $balance = $this->integrity_balance;

        if ($balance >= 100) return ['name' => 'Disiplin Elite', 'color' => '#10b981', 'icon' => 'bi-trophy-fill', 'progress' => 100];
        if ($balance >= 75) return ['name' => 'Sangat Baik', 'color' => '#3b82f6', 'icon' => 'bi-star-fill', 'progress' => 85];
        if ($balance >= 50) return ['name' => 'Baik', 'color' => '#06b6d4', 'icon' => 'bi-shield-check', 'progress' => 70];
        if ($balance >= 25) return ['name' => 'Cukup', 'color' => '#f59e0b', 'icon' => 'bi-exclamation-circle', 'progress' => 50];
        if ($balance >= 0) return ['name' => 'Perlu Perhatian', 'color' => '#ef4444', 'icon' => 'bi-exclamation-triangle', 'progress' => 30];
        return ['name' => 'Peringatan', 'color' => '#dc2626', 'icon' => 'bi-x-octagon-fill', 'progress' => 10];
    }
}

