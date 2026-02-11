<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnggotaKelas extends Model
{
    protected $table = 'anggota_kelas';
    protected $fillable = ['siswa_id', 'rombongan_belajar_id', 'tahun_ajar_id'];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function rombonganBelajar(): BelongsTo
    {
        return $this->belongsTo(RombonganBelajar::class);
    }

    public function tahunAjar(): BelongsTo
    {
        return $this->belongsTo(TahunAjar::class);
    }
}