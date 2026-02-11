<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RombonganBelajar extends Model
{
    protected $table = 'rombongan_belajar';

    protected $fillable = [
        'nama_kelas',
        'jurusan',
        'tingkat',
        'api_rombel_id'
    ];

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

    public function anggotaKelas()
    {
        return $this->hasMany(AnggotaKelas::class);
    }
}
