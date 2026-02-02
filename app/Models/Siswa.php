<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
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
            $siswa->qr_code = Str::uuid(); // isi QR unik
        });
    }

}
