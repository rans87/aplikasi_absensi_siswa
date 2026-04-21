<?php

namespace App\Http\Controllers;

use App\Models\PointLedger;
use App\Models\FlexibilityItem;
use App\Models\UserToken;
use App\Services\IntegrityPointService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller Siswa: Dompet Integritas - Saldo, Mutasi, Marketplace, Inventory
 */
class WalletController extends Controller
{
    /**
     * Dashboard Dompet Integritas (Hero Section + Tabs)
     */
    public function index()
    {
        $siswa = Auth::guard('siswa')->user();
        $siswaId = $siswa->id;

        // Saldo & Level
        $saldo = PointLedger::getCurrentBalance($siswaId);
        $level = $siswa->integrity_level;

        // Tab 1: Riwayat Mutasi (10 terbaru)
        $mutasi = PointLedger::where('siswa_id', $siswaId)
            ->orderByDesc('id')
            ->take(20)
            ->get();

        // Tab 2: Marketplace
        $items = FlexibilityItem::active()->orderBy('point_cost')->get();

        // Tab 3: Inventory Token
        $tokens = UserToken::where('siswa_id', $siswaId)
            ->with('flexibilityItem')
            ->orderByDesc('created_at')
            ->get();

        $tokensAvailable = $tokens->where('status', 'AVAILABLE')->count();
        $tokensUsed = $tokens->where('status', 'USED')->count();

        // Statistik ringkasan bulan ini
        $earnedThisMonth = PointLedger::where('siswa_id', $siswaId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('amount', '>', 0)
            ->sum('amount');

        $spentThisMonth = PointLedger::where('siswa_id', $siswaId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('amount', '<', 0)
            ->sum('amount');
        
        // Buat jadi angka positif untuk tampilan "Sudah Digunakan/Dikurangi"
        $spentThisMonth = abs($spentThisMonth);

        return view('integrity.siswa.wallet', compact(
            'siswa', 'saldo', 'level', 'mutasi', 'items', 'tokens',
            'tokensAvailable', 'tokensUsed', 'earnedThisMonth', 'spentThisMonth'
        ));
    }

    /**
     * Beli token dari marketplace
     */
    public function purchaseToken(Request $request)
    {
        $request->validate(['item_id' => 'required|exists:flexibility_items,id']);

        $siswa = Auth::guard('siswa')->user();
        $service = new IntegrityPointService();
        $result = $service->beliToken($siswa->id, $request->item_id);

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Riwayat mutasi lengkap (semua, dengan pagination)
     */
    public function riwayatMutasi(Request $request)
    {
        $siswa = Auth::guard('siswa')->user();
        
        $query = PointLedger::where('siswa_id', $siswa->id)->orderByDesc('id');

        if ($request->type) {
            $query->where('transaction_type', $request->type);
        }

        $mutasi = $query->paginate(20)->withQueryString();
        $saldo = PointLedger::getCurrentBalance($siswa->id);

        return view('integrity.siswa.riwayat', compact('mutasi', 'saldo', 'siswa'));
    }

    /**
     * Toggle status is_auto_use untuk token milik siswa
     */
    public function toggleAutoUse(int $id)
    {
        $siswa = Auth::guard('siswa')->user();
        $token = UserToken::where('siswa_id', $siswa->id)
            ->where('status', 'AVAILABLE')
            ->findOrFail($id);

        $token->update([
            'is_auto_use' => !$token->is_auto_use
        ]);

        $statusText = $token->is_auto_use ? 'Mode Otomatis (Aktif)' : 'Mode Manual (Tersimpan)';
        return back()->with('success', "Status token '{$token->flexibilityItem->item_name}' diubah ke {$statusText}.");
    }

    /**
     * Menggunakan tiket secara manual untuk absensi tertentu (Claim Relief)
     */
    public function useManual(Request $request)
    {
        $request->validate([
            'token_id' => 'required|exists:user_tokens,id',
            'absensi_id' => 'required',
        ]);

        $siswa = Auth::guard('siswa')->user();
        $service = new IntegrityPointService();
        
        $result = $service->gunakanTiketManual($siswa->id, $request->token_id, $request->absensi_id);

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }
}
