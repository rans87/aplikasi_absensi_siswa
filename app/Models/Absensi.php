<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';

    protected $fillable = [
        'siswa_id',
        'guru_id',
        'rombongan_belajar_id',
        'tanggal',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // ===== SCOPES (Reusable Query Builders) =====

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('tanggal', today());
    }

    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('tanggal', now()->month)
                     ->whereYear('tanggal', now()->year);
    }

    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('tanggal', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeByDate(Builder $query, $date): Builder
    {
        return $query->whereDate('tanggal', $date);
    }

    public function scopeBySiswa(Builder $query, int $siswaId): Builder
    {
        return $query->where('siswa_id', $siswaId);
    }

    public function scopeByGuru(Builder $query, int $guruId): Builder
    {
        return $query->where('guru_id', $guruId);
    }

    public function scopeByRombel(Builder $query, int $rombelId): Builder
    {
        return $query->where('rombongan_belajar_id', $rombelId);
    }

    // ===== RELATIONSHIPS =====

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function rombonganBelajar()
    {
        return $this->belongsTo(RombonganBelajar::class);
    }
}
