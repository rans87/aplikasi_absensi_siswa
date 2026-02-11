<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Siswa;
use App\Models\Guru;

class Pelanggaran extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'pelanggaran';

    protected $fillable = ['siswa_id', 'guru_id', 'nama_pelanggaran', 'poin', 'keterangan'];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }
}