<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Siswa extends Authenticatable
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
            if (empty($siswa->qr_code)) {
                $siswa->qr_code = (string) Str::uuid();
            }
        });
    }

    // Default Laravel behavior uses 'id' and 'id' as identifier name and identifier.
    // We removed custom overrides to ensure standard session behavior.
}
