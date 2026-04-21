<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PointRule;
use App\Models\FlexibilityItem;
use App\Models\Siswa;
use App\Models\PointLedger;
use Illuminate\Support\Facades\DB;

class IntegritySystemSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Rules
        $rules = [
            [
                'rule_name' => 'Hadir Tepat Waktu',
                'target_role' => 'SISWA',
                'condition_type' => 'check_in_time',
                'condition_operator' => '<',
                'condition_value' => '07:00:00',
                'point_modifier' => 5,
                'is_active' => true,
            ],
            [
                'rule_name' => 'Terlambat Ringan',
                'target_role' => 'SISWA',
                'condition_type' => 'late_minutes',
                'condition_operator' => '<=',
                'condition_value' => '15',
                'point_modifier' => -10,
                'is_active' => true,
            ],
            [
                'rule_name' => 'Terlambat Berat',
                'target_role' => 'SISWA',
                'condition_type' => 'late_minutes',
                'condition_operator' => '>',
                'condition_value' => '15',
                'point_modifier' => -25,
                'is_active' => true,
            ],
        ];

        foreach ($rules as $rule) {
            PointRule::updateOrCreate(['rule_name' => $rule['rule_name']], $rule);
        }

        // 2. Seed Marketplace Items
        $items = [
            [
                'item_name' => 'Token Kelonggaran (Normal)',
                'description' => 'Gunakan otomatis untuk memaafkan keterlambatan < 10 menit',
                'point_cost' => 50,
                'tolerance_minutes' => 10,
                'stock_limit' => 5,
                'is_active' => true,
            ],
            [
                'item_name' => 'Token Kelonggaran (Premium)',
                'description' => 'Gunakan otomatis untuk memaafkan keterlambatan berdurasi berapapun',
                'point_cost' => 150,
                'tolerance_minutes' => 999,
                'stock_limit' => 2,
                'is_active' => true,
            ],
            [
                'item_name' => 'Voucher Kantin (Small)',
                'description' => 'Voucher makan senilai Rp 5.000',
                'point_cost' => 200,
                'tolerance_minutes' => 0,
                'stock_limit' => 1,
                'is_active' => true,
            ],
        ];

        foreach ($items as $item) {
            FlexibilityItem::updateOrCreate(['item_name' => $item['item_name']], $item);
        }

        // 3. Seed initial points for first 5 students
        $students = Siswa::take(5)->get();
        foreach ($students as $siswa) {
            // Check if already has ledger to avoid duplicates
            if (PointLedger::where('siswa_id', $siswa->id)->exists()) continue;

            // Give 100 base points
            PointLedger::create([
                'siswa_id' => $siswa->id,
                'transaction_type' => 'EARN',
                'amount' => 100,
                'current_balance' => 100,
                'description' => 'Saldo awal integritas (Bonus Sistem Baru)',
            ]);
            
            // Add some activity
            PointLedger::create([
                'siswa_id' => $siswa->id,
                'transaction_type' => 'PENALTY',
                'amount' => -10,
                'current_balance' => 90,
                'description' => 'Terlambat 5 Menit',
            ]);
        }
    }
}
