<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserToken extends Model
{
    use HasFactory;

    protected $table = 'user_tokens';

    protected $fillable = [
        'siswa_id',
        'flexibility_item_id',
        'status',
        'is_auto_use',
        'used_at',
        'used_at_attendance_id',
        'used_at_attendance_type',
    ];

    protected $casts = [
        'used_at' => 'datetime',
        'is_auto_use' => 'boolean',
    ];

    // ===== RELATIONSHIPS =====

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function flexibilityItem()
    {
        return $this->belongsTo(FlexibilityItem::class);
    }

    public function absensi()
    {
        return $this->belongsTo(Absensi::class, 'used_at_attendance_id');
    }

    // ===== SCOPES =====

    public function scopeAvailable($query)
    {
        return $query->where('status', 'AVAILABLE');
    }

    public function scopeUsed($query)
    {
        return $query->where('status', 'USED');
    }

    public function scopeBySiswa($query, int $siswaId)
    {
        return $query->where('siswa_id', $siswaId);
    }

    // ===== HELPERS =====

    /**
     * Tandai token sebagai USED saat dipakai oleh interceptor
     */
    public function markAsUsed(int $absensiId, ?string $attendanceType = null): void
    {
        $this->update([
            'status' => 'USED',
            'used_at' => now(),
            'used_at_attendance_id' => $absensiId,
            'used_at_attendance_type' => $attendanceType,
        ]);
    }
}
