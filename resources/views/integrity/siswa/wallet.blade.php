@extends('layouts.app')

@section('title', 'Dompet Integritas')

@section('content')
<div class="row g-4">
    {{-- HERO SECTION --}}
    <div class="col-12">
        <div class="card border-0 shadow-premium overflow-hidden wallet-hero rounded-5">
            <div class="card-body position-relative p-4 p-md-5">
                <div class="wallet-glass-overlay"></div>
                <div class="position-relative z-1">
                    <div class="row align-items-center">
                        <div class="col-lg-7 mb-4 mb-lg-0">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="badge bg-white bg-opacity-20 text-white px-4 py-2 rounded-pill fw-bold" style="font-size:10px; border: 1px solid rgba(255,255,255,0.2); letter-spacing: 2px;">
                                    DOMPET INTEGRITAS SISWA
                                </span>
                            </div>
                            <div class="d-flex align-items-end gap-3 mb-1">
                                <h1 class="display-2 fw-black text-white mb-0 ls-extratight wallet-saldo">
                                    {{ number_format($saldo) }}
                                </h1>
                                <span class="text-white text-opacity-75 fw-bold mb-3" style="font-size:18px;">POIN</span>
                            </div>
                            <p class="text-white text-opacity-75 fw-medium mb-4" style="font-size:14px;">Kumpulkan poin untuk membeli tiket pengurang denda keterlambatan.</p>
                            
                            <div class="d-flex align-items-center gap-3 mt-4">
                                <div class="bg-white bg-opacity-15 px-4 py-2 rounded-pill d-flex align-items-center gap-2" style="backdrop-filter:blur(10px); border: 1px solid rgba(255,255,255,0.15);">
                                    <i class="bi {{ $level['icon'] }} fs-5" style="color: {{ $level['color'] }}"></i>
                                    <span class="fw-extrabold text-white" style="font-size:13px;">Status: {{ $level['name'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="glass-stat-card rounded-5 p-4 text-center">
                                        <div class="text-white text-opacity-60 fw-bold mb-2" style="font-size:10px; letter-spacing: 1px;">TOTAL TIKET</div>
                                        <h3 class="fw-black text-info mb-0">{{ $tokensAvailable }}</h3>
                                        <div class="text-white text-opacity-50" style="font-size:9px;">AVAILABLE</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="glass-stat-card rounded-5 p-4 text-center">
                                        <div class="text-white text-opacity-60 fw-bold mb-2" style="font-size:10px; letter-spacing: 1px;">SUDAH PAKAI</div>
                                        <h3 class="fw-black text-amber mb-0">{{ $tokensUsed }}</h3>
                                        <div class="text-white text-opacity-50" style="font-size:9px;">USED</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABS SECTION --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm overflow-hidden rounded-5">
            <div class="card-header border-0 bg-white p-0">
                <ul class="nav nav-tabs nav-fill border-0" id="walletTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active wallet-tab-btn fw-bold py-3" id="mutasi-tab" data-bs-toggle="tab" data-bs-target="#mutasi" type="button" role="tab">
                            <i class="bi bi-clock-history me-2"></i>Mutasi & Klaim
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link wallet-tab-btn fw-bold py-3" id="marketplace-tab" data-bs-toggle="tab" data-bs-target="#marketplace" type="button" role="tab">
                            <i class="bi bi-shop me-2"></i>Beli Tiket
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link wallet-tab-btn fw-bold py-3" id="inventory-tab" data-bs-toggle="tab" data-bs-target="#inventory" type="button" role="tab">
                            <i class="bi bi-backpack2-fill me-2"></i>Inventory
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-0">
                <div class="tab-content" id="walletTabsContent">
                    
                    {{-- TAB 1: Mutasi & Klaim --}}
                    <div class="tab-pane fade show active" id="mutasi" role="tabpanel">
                        <div class="p-4 bg-light bg-opacity-50 border-bottom">
                            <h6 class="fw-extrabold text-dark mb-1"><i class="bi bi-receipt-cutoff me-2 text-primary"></i>Riwayat & Penggunaan Tiket</h6>
                            <p class="text-muted small mb-0">Jika Anda terkena denda, gunakan tiket di sini untuk mengurangi pengurangannya.</p>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <tbody>
                                    @forelse($mutasi as $m)
                                    <tr>
                                        <td class="ps-4 py-4" style="width:60px;">
                                            @if($m->amount > 0)
                                                <div class="rounded-4 bg-emerald-soft d-flex align-items-center justify-content-center shadow-sm" style="width:48px;height:48px; border: 1px solid rgba(16,185,129,0.1);">
                                                    <i class="bi bi-graph-up-arrow text-emerald fs-5"></i>
                                                </div>
                                            @else
                                                <div class="rounded-4 bg-rose-soft d-flex align-items-center justify-content-center shadow-sm" style="width:48px;height:48px; border: 1px solid rgba(225,29,72,0.1);">
                                                    <i class="bi bi-shield-exclamation text-rose fs-5"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-extrabold text-dark fs-6">{{ $m->description }}</div>
                                            <div class="text-muted x-small mt-1"><i class="bi bi-calendar-event me-1"></i>{{ $m->created_at->translatedFormat('d M Y, H:i') }} WIB</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="fw-black {{ $m->amount > 0 ? 'text-emerald' : 'text-rose' }} fs-4">
                                                {{ $m->amount > 0 ? '+' : '' }}{{ $m->amount }}
                                            </div>
                                        </td>
                                        <td class="pe-4 text-end">
                                            {{-- LOGIKA TOMBOL KLAIM MANUAL --}}
                                            @if($m->transaction_type === 'PENALTY' && $m->absensi_id && $tokensAvailable > 0)
                                                <button class="btn btn-primary rounded-pill btn-sm px-4 fw-bold shadow-sm" 
                                                        onclick="openClaimModal({{ $m->absensi_id }}, {{ $m->amount }})">
                                                    <i class="bi bi-ticket-perforated me-1"></i>Gunakan Tiket
                                                </button>
                                            @elseif(str_contains($m->description, 'Pemulihan'))
                                                <span class="badge bg-emerald-soft text-emerald rounded-pill px-3 py-2 fw-bold">
                                                    <i class="bi bi-check-circle-fill me-1"></i>Denda Dikurangi
                                                </span>
                                            @else
                                                <span class="text-muted small fw-bold">Selesai</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="4" class="text-center py-5 text-muted">Belum ada aktivitas poin.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB 2: Marketplace --}}
                    <div class="tab-pane fade" id="marketplace" role="tabpanel">
                        <div class="p-4 row g-4">
                            @forelse($items as $item)
                            <div class="col-md-6 col-xl-4">
                                <div class="card border-0 shadow-sm rounded-5 market-card overflow-hidden">
                                    <div class="card-body p-4 text-center">
                                        <div class="mx-auto mb-3 bg-primary-soft rounded-4 d-flex align-items-center justify-content-center shadow-sm" style="width:65px;height:65px;">
                                            <i class="bi bi-ticket-perforated-fill text-primary fs-2"></i>
                                        </div>
                                        <h6 class="fw-extrabold text-dark mb-1">{{ $item->item_name }}</h6>
                                        <div class="badge bg-emerald-soft text-emerald rounded-pill px-3 py-1 mb-3 fw-bold" style="font-size:10px;">
                                            Diskon Denda: +{{ $item->tolerance_minutes }} Poin
                                        </div>
                                        <p class="text-muted x-small mb-4">{{ $item->description ?? 'Tiket otomatis mengurangi denda poin Anda.' }}</p>
                                        
                                        <form method="POST" action="{{ route('wallet.purchase') }}">
                                            @csrf
                                            <input type="hidden" name="item_id" value="{{ $item->id }}">
                                            <button type="submit" class="btn {{ $saldo >= $item->point_cost ? 'btn-primary' : 'btn-light text-muted' }} w-100 rounded-pill fw-bold" {{ $saldo < $item->point_cost ? 'disabled' : '' }}>
                                                Tukar {{ $item->point_cost }} Poin
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-12 text-center py-5 text-muted">Marketplace kosong.</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- TAB 3: Inventory --}}
                    <div class="tab-pane fade" id="inventory" role="tabpanel">
                        <div class="p-4 table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr class="x-small fw-bold text-muted text-uppercase ls-1">
                                        <th class="ps-0">Nama Tiket</th>
                                        <th class="text-center">Nilai Diskon</th>
                                        <th class="text-center">Mode</th>
                                        <th class="text-end pe-0">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tokens as $token)
                                    <tr>
                                        <td class="ps-0 py-3">
                                            <div class="fw-bold text-dark">{{ $token->flexibilityItem->item_name ?? '-' }}</div>
                                            <div class="x-small text-muted">Id: #TKN-{{ $token->id }}</div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info-soft text-info rounded-pill px-3">+{{ $token->flexibilityItem->tolerance_minutes ?? 0 }} Poin</span>
                                        </td>
                                        <td class="text-center">
                                            @if($token->status === 'AVAILABLE')
                                            <form action="{{ route('wallet.tokens.toggle-auto', $token->id) }}" method="POST">
                                                @csrf
                                                <div class="form-check form-switch d-inline-block">
                                                    <input class="form-check-input" type="checkbox" onchange="this.form.submit()" {{ $token->is_auto_use ? 'checked' : '' }}>
                                                    <label class="x-small fw-bold text-uppercase">{{ $token->is_auto_use ? 'Otomatis' : 'Manual' }}</label>
                                                </div>
                                            </form>
                                            @else - @endif
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="badge {{ $token->status === 'AVAILABLE' ? 'bg-emerald text-white' : 'bg-light text-muted' }} rounded-pill px-3 py-1 fw-bold" style="font-size:9px;">
                                                {{ $token->status }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="4" class="text-center py-5 text-muted">Belum punya tiket.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL KLAIM TIKET MANUAL --}}
<div class="modal fade" id="claimModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-5 shadow-lg">
            <div class="modal-header border-0 p-4">
                <h5 class="fw-black text-dark mb-0">Gunakan Tiket Anda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('wallet.tokens.use-manual') }}" method="POST">
                @csrf
                <input type="hidden" name="absensi_id" id="claimAbsensiId">
                <div class="modal-body p-4 text-center">
                    <div class="mb-4">
                        <i class="bi bi-ticket-perforated text-primary display-1"></i>
                    </div>
                    <p class="text-muted">Pilih satu tiket dari inventory Anda untuk mengurangi denda sebesar <strong id="penaltyDisplay" class="text-rose"></strong> poin pada absensi ini.</p>
                    
                    <select name="token_id" class="form-select border-0 bg-light rounded-4 px-4 py-3 fw-bold" required>
                        <option value="">-- Pilih Tiket --</option>
                        @foreach($tokens->where('status', 'AVAILABLE') as $t)
                            <option value="{{ $t->id }}">🎟️ {{ $t->flexibilityItem->item_name }} (+{{ $t->flexibilityItem->tolerance_minutes }} Poin)</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-3 shadow-lg">KONFIRMASI PENGGUNAAN</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .ls-extratight { letter-spacing: -3px; }
    .wallet-hero { background: linear-gradient(135deg, #0f172a, #1e3a8a, #2563eb) !important; position: relative; }
    .wallet-glass-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle at 90% 10%, rgba(59,130,246,0.3) 0%, transparent 50%); }
    .glass-stat-card { background: rgba(255,255,255,0.08); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.08); transition: all 0.3s ease; }
    .wallet-tab-btn { border: none !important; color: #64748b !important; border-bottom: 3px solid transparent !important; transition: all 0.3s ease !important; }
    .wallet-tab-btn.active { color: #2563eb !important; border-bottom-color: #2563eb !important; background: transparent !important; }
    .bg-emerald-soft { background-color: #ecfdf5 !important; }
    .bg-rose-soft { background-color: #fff1f2 !important; }
    .bg-primary-soft { background-color: #eff6ff !important; }
    .text-emerald { color: #10b981 !important; }
    .text-rose { color: #e11d48 !important; }
    .fw-black { font-weight: 900; }
    .x-small { font-size: 11px; }
    .ls-1 { letter-spacing: 1px; }
</style>

<script>
    function openClaimModal(absensiId, penaltyAmount) {
        document.getElementById('claimAbsensiId').value = absensiId;
        document.getElementById('penaltyDisplay').innerText = penaltyAmount;
        new bootstrap.Modal(document.getElementById('claimModal')).show();
    }
</script>
@endsection
