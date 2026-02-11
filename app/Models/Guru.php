<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Guru extends Authenticatable
{
    use HasFactory;

    protected $table = 'guru';
    protected $fillable = ['nama', 'email', 'password', 'nip', 'jenis_kelamin', 'no_hp', 'alamat', 'foto'];
    protected $hidden = ['password', 'remember_token'];


    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
