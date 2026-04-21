<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotifikasiGuru extends Model
{
    use HasFactory;

    protected $table = 'notifikasi_guru';

    protected $fillable = [
        'guru_id',
        'jadwal_pelajaran_id',
        'from_guru_id',
        'judul',
        'pesan',
        'tipe',
        'dibaca',
    ];

    protected $casts = [
        'dibaca' => 'boolean',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function fromGuru()
    {
        return $this->belongsTo(Guru::class, 'from_guru_id');
    }

    public function jadwalPelajaran()
    {
        return $this->belongsTo(JadwalPelajaran::class);
    }
}
