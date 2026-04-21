<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlexibilityItem extends Model
{
    use HasFactory;

    protected $table = 'flexibility_items';

    protected $fillable = [
        'item_name',
        'category',
        'icon',
        'description',
        'point_cost',
        'tolerance_minutes',
        'stock_limit',
        'is_active',
    ];

    protected $casts = [
        'point_cost' => 'integer',
        'tolerance_minutes' => 'integer',
        'stock_limit' => 'integer',
        'is_active' => 'boolean',
    ];

    // ===== RELATIONSHIPS =====

    public function userTokens()
    {
        return $this->hasMany(UserToken::class);
    }

    // ===== SCOPES =====

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ===== HELPERS =====

    /**
     * Cek apakah siswa sudah melebihi batas pembelian bulanan
     */
    public function isStockAvailableFor(int $siswaId): bool
    {
        if ($this->stock_limit === null) {
            return true; // Tidak ada batas
        }

        $purchasedThisMonth = UserToken::where('siswa_id', $siswaId)
            ->where('flexibility_item_id', $this->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return $purchasedThisMonth < $this->stock_limit;
    }
}
