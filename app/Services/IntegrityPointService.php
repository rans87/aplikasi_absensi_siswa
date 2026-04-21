<?php

namespace App\Services;

use App\Models\PointRule;
use App\Models\PointLedger;
use App\Models\UserToken;
use App\Models\Absensi;
use App\Models\AbsensiMapel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Service Sistem Poin Integritas (New Logic: Penalty Relief)
 * 
 * Bertanggung jawab untuk:
 * 1. Rule Engine: Evaluasi aturan poin saat absensi
 * 2. Token Interceptor: Mengurangi denda poin jika punya tiket (Otomatis/Manual)
 * 3. Ledger Recording: Catat transaksi poin ke buku besar
 */
class IntegrityPointService
{
    /**
     * === ENTRY POINT UTAMA ===
     * Dipanggil setiap kali absensi berhasil disimpan.
     */
    public function prosesAbsensi($absensi, ?string $waktuCheckIn = null): array
    {
        $waktuCheckIn = $waktuCheckIn ?? now()->format('H:i:s');
        $siswaId = $absensi->siswa_id;
        $result = [
            'penalty_original' => 0,
            'relief_applied' => 0,
            'rules_triggered' => [],
            'token_used' => null,
            'final_status' => $absensi->status,
            'poin_total' => 0, // NEW: Net points
        ];

        // Hanya proses untuk status 'hadir' atau 'terlambat'
        if (!in_array($absensi->status, ['hadir', 'terlambat'])) {
            return $result;
        }

        // Tentukan batas jam masuk (daily absensi vs mapel absensi)
        $jamMasukHarapan = null;
        if ($absensi instanceof AbsensiMapel) {
            $absensi->loadMissing('jadwalPelajaran');
            if ($absensi->jadwalPelajaran) {
                $jamMasukHarapan = $absensi->jadwalPelajaran->jam_mulai;
            }
        }

        // Hitung menit keterlambatan
        $tanggal = Carbon::parse($absensi->tanggal)->toDateString();
        $lateMinutes = $this->hitungMenitTerlambat($waktuCheckIn, $tanggal, $jamMasukHarapan);

        // --- LANGKAH 1: Evaluasi Rule Engine (Dapatkan Denda Penuh) ---
        $triggeredRules = $this->evaluasiRules($waktuCheckIn, $lateMinutes);

        // Filter Reward/Penalti agar tidak campur aduk
        $filteredRules = $triggeredRules->filter(function($rule) use ($lateMinutes) {
            return $lateMinutes > 0 ? $rule->point_modifier < 0 : $rule->point_modifier > 0;
        });

        foreach ($filteredRules as $rule) {
            $amount = $rule->point_modifier;
            $result['penalty_original'] += $amount;
            $result['rules_triggered'][] = [
                'rule_name' => $rule->rule_name,
                'point_modifier' => $amount,
            ];

            // Catat denda awal ke ledger
            PointLedger::recordTransaction(
                $siswaId,
                $amount >= 0 ? 'EARN' : 'PENALTY',
                $amount,
                $rule->rule_name . " (" . Carbon::parse($absensi->tanggal)->format('d M Y') . ")",
                $absensi->id,
                get_class($absensi) // Polymorphic Type
            );
        }

        // --- LANGKAH 2: Token Relief (Otomatis) ---
        // Jika ada denda (poin < 0) dan siswa telat, cek tiket otomatis
        if ($result['penalty_original'] < 0 && $lateMinutes > 0) {
            $tokenResult = $this->interceptToken($siswaId, $absensi);
            
            if ($tokenResult) {
                $reliefAmount = $tokenResult['relief_points'];
                
                // Pastikan pengurangan tidak melebihi denda (tidak jadi untung)
                $actualRelief = min(abs($result['penalty_original']), $reliefAmount);
                
                // Catat transaksi pemulihan poin (Pengembalian denda)
                if ($actualRelief > 0) {
                    PointLedger::recordTransaction(
                        $siswaId,
                        'EARN', // Kembalikan sebagai poin positif
                        $actualRelief,
                        "Pemulihan Poin: Tiket '{$tokenResult['item_name']}' otomatis digunakan",
                        $absensi->id,
                        get_class($absensi) // Polymorphic Type
                    );
                    
                    $result['relief_applied'] = $actualRelief;
                    $result['token_used'] = $tokenResult;
                }
            }
        }
        
        // Hitung total poin akhir (Netto)
        $result['poin_total'] = $result['penalty_original'] + $result['relief_applied'];

        return $result;
    }

    /**
     * Hitung menit keterlambat
     */
    public function hitungMenitTerlambat(string $checkInTime, ?string $date = null, ?string $waktuHarapan = null): int
    {
        $date = $date ?? now()->toDateString();
        $jamMasuk = $waktuHarapan ?? \App\Models\SchoolCalendar::getEntryTimeForDate($date);

        if (!$jamMasuk || !$checkInTime) return 0;

        try {
            $masuk = Carbon::createFromTimeString($jamMasuk);
            $actual = Carbon::createFromTimeString($checkInTime);

            if ($actual->lte($masuk)) return 0;

            return abs($actual->diffInMinutes($masuk));
        } catch (\Exception $e) {
            return 0; // Return 0 if time format is invalid
        }
    }

    public function evaluasiRules(string $checkInTime, int $lateMinutes)
    {
        $rules = PointRule::active()->whereIn('target_role', ['siswa', 'SISWA', 'Siswa'])->get();
        return $rules->filter(function (PointRule $rule) use ($checkInTime, $lateMinutes) {
            return $rule->evaluate($checkInTime, $lateMinutes);
        });
    }

    /**
     * === TOKEN INTERCEPTOR (New Logic) ===
     * Mencari tiket otomatis yang punya "Nilai Diskon Poin".
     */
    public function interceptToken(int $siswaId, $absensi): ?array
    {
        // Cari tiket TERSEDIA milik siswa yang is_auto_use = true
        $token = UserToken::where('siswa_id', $siswaId)
            ->where('status', 'AVAILABLE')
            ->where('is_auto_use', true)
            ->with('flexibilityItem')
            ->orderBy('id', 'asc') // FIFO
            ->first();

        if (!$token) return null;

        // Tandai tiket sebagai USED
        $token->markAsUsed($absensi->id, get_class($absensi));

        return [
            'token_id' => $token->id,
            'item_name' => $token->flexibilityItem->item_name,
            'relief_points' => $token->flexibilityItem->tolerance_minutes, // Reused column as points
        ];
    }

    /**
     * Beli tiket dari marketplace
     */
    public function beliToken(int $siswaId, int $itemId): array
    {
        $item = \App\Models\FlexibilityItem::active()->find($itemId);
        if (!$item) return ['success' => false, 'message' => 'Tiket tidak ditemukan.'];

        if (!$item->isStockAvailableFor($siswaId)) {
            return ['success' => false, 'message' => 'Batas pembelian bulanan tercapai.'];
        }

        $currentBalance = PointLedger::getCurrentBalance($siswaId);
        if ($currentBalance < $item->point_cost) {
            return ['success' => false, 'message' => "Poin Anda tidak cukup."];
        }

        PointLedger::recordTransaction($siswaId, 'SPEND', -$item->point_cost, "Beli Tiket: {$item->item_name}");

        UserToken::create([
            'siswa_id' => $siswaId,
            'flexibility_item_id' => $item->id,
            'status' => 'AVAILABLE',
            'is_auto_use' => true, // Default aktif supaya user senang
        ]);

        return ['success' => true, 'message' => "Tiket '{$item->item_name}' berhasil dibeli!"];
    }

    /**
     * === PENGGUNAAN TIKET MANUAL ===
     * Digunakan ketika siswa menekan tombol "Gunakan Tiket" di history keterlambatan.
     */
    public function gunakanTiketManual(int $siswaId, int $tokenId, int $absensiId): array
    {
        $token = UserToken::where('siswa_id', $siswaId)
            ->where('id', $tokenId)
            ->where('status', 'AVAILABLE')
            ->with('flexibilityItem')
            ->first();

        if (!$token) return ['success' => false, 'message' => 'Tiket tidak tersedia.'];

        $absensi = Absensi::find($absensiId);
        if (!$absensi || $absensi->siswa_id != $siswaId) return ['success' => false, 'message' => 'Data absensi tidak valid.'];

        // Cek apakah denda sudah pernah dipulihkan
        $alreadyRelieved = PointLedger::where('reference_absensi_id', $absensiId)
            ->where('description', 'like', '%Pemulihan%')
            ->exists();
        
        if ($alreadyRelieved) return ['success' => false, 'message' => 'Denda absensi ini sudah pernah dipulihkan.'];

        // Hitung denda yang ada di ledger untuk absensi ini
        $totalPenalty = PointLedger::where('reference_absensi_id', $absensiId)
            ->where('amount', '<', 0)
            ->sum('amount');

        if ($totalPenalty >= 0) return ['success' => false, 'message' => 'Tidak ada denda poin yang bisa dipulihkan pada absensi ini.'];

        $reliefAmount = min(abs($totalPenalty), $token->flexibilityItem->tolerance_minutes);

        $token->markAsUsed($absensiId);

        PointLedger::recordTransaction(
            $siswaId,
            'EARN',
            $reliefAmount,
            "Pemulihan Poin (Manual): Tiket '{$token->flexibilityItem->item_name}' digunakan",
            $absensiId
        );

        return ['success' => true, 'message' => 'Tiket berhasil digunakan. Denda poin Anda telah dikurangi!'];
    }
}
