<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class RombonganBelajar extends Model
{
    protected $table = 'rombongan_belajar';

    protected $fillable = [
        'nama_kelas',
        'jurusan',
        'tingkat',
        'api_rombel_id',
        'wali_kelas_id'
    ];

    // ===== SCOPES =====

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (!$search) return $query;
        return $query->where('nama_kelas', 'like', "%{$search}%")
                     ->orWhere('jurusan', 'like', "%{$search}%");
    }

    // ===== RELATIONSHIPS =====

    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'wali_kelas_id');
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

    public function anggotaKelas()
    {
        return $this->hasMany(AnggotaKelas::class);
    }

    public function jadwalPelajaran()
    {
        return $this->hasMany(JadwalPelajaran::class);
    }

    // ===== HELPERS =====

    public function getJumlahSiswaAttribute(): int
    {
        return $this->anggotaKelas()->count();
    }
}
