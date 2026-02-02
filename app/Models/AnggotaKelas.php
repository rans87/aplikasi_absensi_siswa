<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnggotaKelas extends Model
{
    use HasFactory;

    protected $table = 'anggota_kelas';
    protected $fillable = ['siswa_id', 'rombongan_belajar_id', 'tahun_ajar_id'];

    // Relasi ke model Siswa (Asumsi nama model: Siswa)
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    // Relasi ke model RombonganBelajar
    public function rombel(): BelongsTo
    {
        return $this->belongsTo(RombonganBelajar::class, 'rombongan_belajar_id');
    }

    // Relasi ke model TahunAjar
    public function tahunAjar(): BelongsTo
    {
        return $this->belongsTo(TahunAjar::class, 'tahun_ajar_id');
    }
}