<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Guru extends Authenticatable
{
    use HasFactory;

    protected $table = 'guru';
    protected $fillable = [
        'external_guru_id', 'nama', 'email', 'password', 'nip', 'nuptk', 'nik', 
        'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'no_hp', 'alamat', 'foto'
    ];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // ===== RELATIONSHIPS =====

    public function kelasWali()
    {
        return $this->hasOne(RombonganBelajar::class, 'wali_kelas_id');
    }

    public function jadwalPelajaran()
    {
        return $this->hasMany(JadwalPelajaran::class);
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

    public function notifikasi()
    {
        return $this->hasMany(NotifikasiGuru::class);
    }

    public function notifikasiBelumDibaca()
    {
        return $this->hasMany(NotifikasiGuru::class)->where('dibaca', false);
    }

    // ===== HELPERS =====

    public function isWaliKelas(): bool
    {
        return $this->kelasWali()->exists();
    }
}
