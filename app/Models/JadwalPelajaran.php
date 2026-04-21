<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class JadwalPelajaran extends Model
{
    use HasFactory;

    protected $table = 'jadwal_pelajaran';

    protected $fillable = [
        'rombongan_belajar_id',
        'mata_pelajaran_id',
        'guru_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'urutan',
    ];

    // ===== DAY MAP CONSTANT =====
    public const HARI_MAP = [
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu',
        'Sunday' => 'Minggu',
    ];

    // ===== SCOPES =====

    public function scopeHariIni(Builder $query): Builder
    {
        $hari = self::HARI_MAP[now()->format('l')] ?? 'Senin';
        return $query->where('hari', $hari);
    }

    public function scopeByGuru(Builder $query, int $guruId): Builder
    {
        return $query->where('guru_id', $guruId);
    }

    public function scopeByRombel(Builder $query, int $rombelId): Builder
    {
        return $query->where('rombongan_belajar_id', $rombelId);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('jam_mulai');
    }

    // ===== STATIC HELPER =====

    public static function getHariIndo(): string
    {
        return self::HARI_MAP[now()->format('l')] ?? 'Senin';
    }

    // ===== RELATIONSHIPS =====

    public function rombonganBelajar()
    {
        return $this->belongsTo(RombonganBelajar::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function absensiMapel()
    {
        return $this->hasMany(AbsensiMapel::class);
    }
}
