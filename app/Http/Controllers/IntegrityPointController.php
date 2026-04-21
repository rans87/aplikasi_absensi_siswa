<?php

namespace App\Http\Controllers;

use App\Models\PointRule;
use App\Models\PointLedger;
use App\Models\FlexibilityItem;
use App\Models\UserToken;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Controller Admin: Mengelola Rules, Marketplace Items, dan Leaderboard Poin Integritas
 */
class IntegrityPointController extends Controller
{
    // ========================
    // RULE BUILDER (CRUD)
    // ========================

    /**
     * Tampilkan daftar rules + form builder
     */
    public function indexRules()
    {
        $rules = PointRule::orderByDesc('is_active')->orderBy('rule_name')->get();
        return view('integrity.admin.rules', compact('rules'));
    }

    /**
     * Simpan rule baru
     */
    public function storeRule(Request $request)
    {
        $request->validate([
            'rule_name' => 'required|string|max:255',
            'target_role' => 'required|in:siswa',
            'condition_type' => 'required|in:check_in_time,late_minutes',
            'condition_operator' => 'required|in:<,>,<=,>=,=',
            'condition_value' => 'required|string|max:50',
            'point_modifier' => 'required|integer',
        ]);

        PointRule::create($request->all());
        return back()->with('success', 'Aturan poin berhasil ditambahkan!');
    }

    /**
     * Update rule
     */
    public function updateRule(Request $request, $id)
    {
        $request->validate([
            'rule_name' => 'required|string|max:255',
            'condition_type' => 'required|in:check_in_time,late_minutes',
            'condition_operator' => 'required|in:<,>,<=,>=,=',
            'condition_value' => 'required|string|max:50',
            'point_modifier' => 'required|integer',
            'is_active' => 'boolean',
        ]);

        $rule = PointRule::findOrFail($id);
        $rule->update($request->all());
        return back()->with('success', 'Aturan berhasil diperbarui!');
    }

    /**
     * Hapus rule
     */
    public function destroyRule($id)
    {
        PointRule::findOrFail($id)->delete();
        return back()->with('success', 'Aturan berhasil dihapus.');
    }

    /**
     * Toggle aktif/nonaktif rule
     */
    public function toggleRule($id)
    {
        $rule = PointRule::findOrFail($id);
        $rule->update(['is_active' => !$rule->is_active]);
        return back()->with('success', 'Status aturan berhasil diubah.');
    }

    // ========================
    // MARKETPLACE ITEMS (CRUD)
    // ========================

    /**
     * Tampilkan daftar item marketplace
     */
    public function indexItems()
    {
        $items = FlexibilityItem::orderByDesc('is_active')->orderBy('item_name')->get();
        return view('integrity.admin.items', compact('items'));
    }

    /**
     * Simpan item baru
     */
    public function storeItem(Request $request)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'category' => 'required|in:attendance_token,physical_reward',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'point_cost' => 'required|integer|min:1',
            'tolerance_minutes' => 'required|integer|min:0',
            'stock_limit' => 'nullable|integer|min:1',
        ]);

        FlexibilityItem::create($request->all());
        return back()->with('success', 'Item marketplace berhasil ditambahkan!');
    }

    /**
     * Update item
     */
    public function updateItem(Request $request, $id)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'category' => 'required|in:attendance_token,physical_reward',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'point_cost' => 'required|integer|min:1',
            'tolerance_minutes' => 'required|integer|min:0',
            'stock_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $item = FlexibilityItem::findOrFail($id);
        $item->update($request->all());
        return back()->with('success', 'Item berhasil diperbarui!');
    }

    /**
     * Hapus item
     */
    public function destroyItem($id)
    {
        FlexibilityItem::findOrFail($id)->delete();
        return back()->with('success', 'Item berhasil dihapus.');
    }

    /**
     * Toggle aktif/nonaktif item
     */
    public function toggleItem($id)
    {
        $item = FlexibilityItem::findOrFail($id);
        $item->update(['is_active' => !$item->is_active]);
        return back()->with('success', 'Status item berhasil diubah.');
    }

    // ========================
    // LEADERBOARD & ANALYTICS
    // ========================

    /**
     * Tampilkan leaderboard poin integritas
     */
    public function leaderboard(Request $request)
    {
        $period = $request->period ?? 'month'; // month, all
        $search = $request->search;
        $dateFilter = $request->date;
        $classFilter = $request->rombongan_belajar_id;

        // Query Utama Ranking
        if ($period === 'month') {
            $month = $dateFilter ? Carbon::parse($dateFilter)->month : now()->month;
            $year = $dateFilter ? Carbon::parse($dateFilter)->year : now()->year;

            $leaderboardQuery = DB::table('point_ledgers')
                ->join('siswa', 'point_ledgers.siswa_id', '=', 'siswa.id')
                ->leftJoin('anggota_kelas', 'siswa.id', '=', 'anggota_kelas.siswa_id')
                ->leftJoin('rombongan_belajar', 'anggota_kelas.rombongan_belajar_id', '=', 'rombongan_belajar.id')
                ->whereMonth('point_ledgers.created_at', $month)
                ->whereYear('point_ledgers.created_at', $year)
                ->select(
                    'siswa.id',
                    'siswa.nama',
                    'siswa.nis',
                    'rombongan_belajar.nama_kelas',
                    DB::raw('SUM(point_ledgers.amount) as total_poin_bulan'),
                    DB::raw('MAX(point_ledgers.current_balance) as current_balance')
                )
                ->groupBy('siswa.id', 'siswa.nama', 'siswa.nis', 'rombongan_belajar.nama_kelas')
                ->orderByDesc('total_poin_bulan');
        } else {
            // Eagerly join the latest ledger entry
            $leaderboardQuery = DB::table('siswa')
                ->join('point_ledgers', 'siswa.id', '=', 'point_ledgers.siswa_id')
                ->leftJoin('anggota_kelas', 'siswa.id', '=', 'anggota_kelas.siswa_id')
                ->leftJoin('rombongan_belajar', 'anggota_kelas.rombongan_belajar_id', '=', 'rombongan_belajar.id')
                ->whereRaw('point_ledgers.id IN (SELECT MAX(id) FROM point_ledgers GROUP BY siswa_id)')
                ->select(
                    'siswa.id',
                    'siswa.nama',
                    'siswa.nis',
                    'rombongan_belajar.nama_kelas',
                    'point_ledgers.current_balance',
                    'point_ledgers.current_balance as total_poin_bulan'
                )
                ->orderByDesc('point_ledgers.current_balance');
        }

        // Apply Global Filters to Ranking
        if ($search) {
            $leaderboardQuery->where(function($q) use ($search) {
                $q->where('siswa.nama', 'like', "%{$search}%")
                  ->orWhere('siswa.nis', 'like', "%{$search}%");
            });
        }
        if ($classFilter) {
            $leaderboardQuery->where('rombongan_belajar.id', $classFilter);
        }

        $leaderboard = $leaderboardQuery->limit(50)->get();

        // Statistik ringkasan (Optimized)
        $stats = DB::table('point_ledgers')
            ->select(
                DB::raw('COUNT(DISTINCT siswa_id) as total_siswa'),
                DB::raw('COUNT(*) as total_transaksi')
            )
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->first();

        $totalSiswaAktif = $stats->total_siswa;
        $totalTransaksi = $stats->total_transaksi;
        
        $avgBalance = DB::table('point_ledgers')
            ->whereIn('id', function($q) {
                $q->selectRaw('MAX(id)')->from('point_ledgers')->groupBy('siswa_id');
            })
            ->avg('current_balance');

        // --- AKTIVITAS TERBARU (Dengan Filter Date) ---
        $recentActivitiesQuery = PointLedger::with(['siswa:id,nama,nis', 'absensi'])
            ->when($dateFilter, function($q) use ($dateFilter) {
                $q->whereDate('created_at', $dateFilter);
            })
            ->when($search, function($q) use ($search) {
                $q->whereHas('siswa', function($sq) use ($search) {
                    $sq->where('nama', 'like', "%{$search}%");
                });
            })
            ->latest('id');

        $recentActivities = $recentActivitiesQuery->limit(50)->get();
        $classes = DB::table('rombongan_belajar')->get();

        return view('integrity.admin.leaderboard', compact(
            'leaderboard', 'period', 'totalSiswaAktif', 'totalTransaksi', 'avgBalance', 
            'recentActivities', 'search', 'dateFilter', 'classFilter', 'classes'
        ));
    }

    // ========================
    // MANUAL TRANSACTIONS
    // ========================

    /**
     * Tampilkan form pemberian poin manual
     */
    public function indexManualAward(Request $request)
    {
        $search = $request->search;
        $siswa = Siswa::when($search, function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

        return view('integrity.admin.manual_award', compact('siswa'));
    }

    /**
     * Simpan transaksi poin manual
     */
    public function storeManualAward(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'amount' => 'required|integer|not_in:0',
            'description' => 'required|string|max:255',
        ]);

        $service = new \App\Services\IntegrityPointService();
        $service->awardManualPoint(
            $request->siswa_id,
            $request->amount,
            $request->description
        );

        return back()->with('success', 'Poin berhasil dicatat untuk siswa tersebut!');
    }
}
