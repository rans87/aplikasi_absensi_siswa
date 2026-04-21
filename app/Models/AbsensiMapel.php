<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class AbsensiMapel extends Model
{
    use HasFactory;

    protected $table = 'absensi_mapel';

    protected $fillable = [
        'siswa_id',
        'jadwal_pelajaran_id',
        'guru_id',
        'tanggal',
        'status',
        'waktu_scan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_scan' => 'datetime',
    ];

    // ===== SCOPES =====

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('tanggal', today());
    }

    public function scopeByJadwal(Builder $query, int $jadwalId): Builder
    {
        return $query->where('jadwal_pelajaran_id', $jadwalId);
    }

    public function scopeBySiswa(Builder $query, int $siswaId): Builder
    {
        return $query->where('siswa_id', $siswaId);
    }

    public function scopeByGuru(Builder $query, int $guruId): Builder
    {
        return $query->where('guru_id', $guruId);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    // ===== RELATIONSHIPS =====

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function jadwalPelajaran()
    {
        return $this->belongsTo(JadwalPelajaran::class);
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }
}
