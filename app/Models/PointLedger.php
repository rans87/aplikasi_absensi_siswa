<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointLedger extends Model
{
    use HasFactory;

    protected $table = 'point_ledgers';

    protected $fillable = [
        'siswa_id',
        'transaction_type',
        'amount',
        'current_balance',
        'description',
        'reference_absensi_id',
        'reference_type',
    ];

    protected $casts = [
        'amount' => 'integer',
        'current_balance' => 'integer',
    ];

    // ===== RELATIONSHIPS =====

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function absensi()
    {
        return $this->belongsTo(Absensi::class, 'reference_absensi_id');
    }

    // ===== SCOPES =====

    public function scopeBySiswa($query, int $siswaId)
    {
        return $query->where('siswa_id', $siswaId);
    }

    public function scopeEarnings($query)
    {
        return $query->where('transaction_type', 'EARN');
    }

    public function scopeSpendings($query)
    {
        return $query->where('transaction_type', 'SPEND');
    }

    public function scopePenalties($query)
    {
        return $query->where('transaction_type', 'PENALTY');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }

    // ===== HELPERS =====

    /**
     * Ambil saldo terkini siswa dari record terakhir di ledger
     */
    public static function getCurrentBalance(int $siswaId): int
    {
        $lastEntry = static::where('siswa_id', $siswaId)
            ->latest('id')
            ->first();

        return $lastEntry ? $lastEntry->current_balance : 0;
    }

    /**
     * Catat transaksi baru ke ledger (Atomic / Anti race-condition)
     */
    public static function recordTransaction(int $siswaId, string $type, int $amount, string $description, ?int $absensiId = null, ?string $referenceType = null): static
    {
        return \DB::transaction(function () use ($siswaId, $type, $amount, $description, $absensiId, $referenceType) {
            // Kunci baris terakhir untuk mencegah race condition
            $lastEntry = static::where('siswa_id', $siswaId)
                ->lockForUpdate()
                ->latest('id')
                ->first();

            $currentBalance = $lastEntry ? $lastEntry->current_balance : 0;
            $newBalance = $currentBalance + $amount;

            return static::create([
                'siswa_id' => $siswaId,
                'transaction_type' => $type,
                'amount' => $amount,
                'current_balance' => $newBalance,
                'description' => $description,
                'reference_absensi_id' => $absensiId,
                'reference_type' => $referenceType,
            ]);
        });
    }
}
