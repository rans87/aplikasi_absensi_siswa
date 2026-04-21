<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointRule extends Model
{
    use HasFactory;

    protected $table = 'point_rules';

    protected $fillable = [
        'rule_name',
        'target_role',
        'condition_type',
        'condition_operator',
        'condition_value',
        'point_modifier',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'point_modifier' => 'integer',
    ];

    // ===== SCOPES =====

    /**
     * Hanya aturan yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Filter berdasarkan role target
     */
    public function scopeForRole($query, string $role)
    {
        return $query->where('target_role', $role);
    }

    /**
     * Filter berdasarkan tipe kondisi (check_in_time atau late_minutes)
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('condition_type', $type);
    }

    // ===== HELPERS =====

    /**
     * Evaluasi apakah aturan ini cocok dengan waktu check-in
     * 
     * @param string $checkInTime Format H:i:s (contoh: "06:25:00")
     * @param int|null $lateMinutes Jumlah menit keterlambatan
     * @return bool
     */
    public function evaluate($checkInTime, $lateMinutes = null): bool
    {
        $value = $this->condition_value;

        if ($this->condition_type === 'check_in_time') {
            // Bandingkan waktu check-in dengan condition_value (format H:i:s)
            return match ($this->condition_operator) {
                '<'  => $checkInTime < $value,
                '<=' => $checkInTime <= $value,
                '>'  => $checkInTime > $value,
                '>=' => $checkInTime >= $value,
                '='  => $checkInTime == $value,
                default => false,
            };
        }

        if ($this->condition_type === 'late_minutes' && $lateMinutes !== null) {
            $condVal = (int) $value;
            return match ($this->condition_operator) {
                '<'  => $lateMinutes < $condVal,
                '<=' => $lateMinutes <= $condVal,
                '>'  => $lateMinutes > $condVal,
                '>=' => $lateMinutes >= $condVal,
                '='  => $lateMinutes == $condVal,
                default => false,
            };
        }

        return false;
    }

    /**
     * Tampilkan deskripsi rule yang readable
     */
    public function getReadableDescriptionAttribute(): string
    {
        $type = $this->condition_type === 'check_in_time' ? 'Jam Kedatangan' : 'Menit Terlambat';
        $modifier = $this->point_modifier > 0 ? "+{$this->point_modifier}" : (string) $this->point_modifier;
        return "JIKA {$type} {$this->condition_operator} {$this->condition_value} MAKA {$modifier} Poin";
    }
}
