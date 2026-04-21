@extends('layouts.app')

@section('title', 'Riwayat Mutasi Poin')

@section('content')
<div class="row g-4">
    {{-- Header --}}
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <a href="{{ route('wallet.index') }}" class="text-muted fw-bold text-decoration-none small mb-1 d-block">
                    <i class="bi bi-arrow-left me-1"></i>Kembali ke Dompet
                </a>
                <h1 class="display-6 fw-extrabold text-dark mb-0" style="letter-spacing:-1.2px;">📒 Riwayat Mutasi</h1>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-dark text-white rounded-pill px-4 py-2 fs-6 fw-bold">
                    Saldo: {{ number_format($saldo) }} Poin
                </span>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="col-12">
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('wallet.riwayat') }}" class="btn btn-sm {{ !request('type') ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-3 fw-bold">
                Semua
            </a>
            <a href="{{ route('wallet.riwayat', ['type' => 'EARN']) }}" class="btn btn-sm {{ request('type') === 'EARN' ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-3 fw-bold">
                <i class="bi bi-arrow-down-circle me-1"></i>Earned
            </a>
            <a href="{{ route('wallet.riwayat', ['type' => 'SPEND']) }}" class="btn btn-sm {{ request('type') === 'SPEND' ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-3 fw-bold">
                <i class="bi bi-cart me-1"></i>Spent
            </a>
            <a href="{{ route('wallet.riwayat', ['type' => 'PENALTY']) }}" class="btn btn-sm {{ request('type') === 'PENALTY' ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-3 fw-bold">
                <i class="bi bi-exclamation-triangle me-1"></i>Penalty
            </a>
        </div>
    </div>

    {{-- Mutation List --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <tbody>
                            @forelse($mutasi as $m)
                            <tr>
                                <td class="ps-4 py-3" style="width:50px;">
                                    @if($m->amount >= 0)
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;background:#ecfdf5;">
                                            <i class="bi bi-arrow-down-circle-fill text-emerald fs-4"></i>
                                        </div>
                                    @else
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;background:#fff1f2;">
                                            <i class="bi bi-arrow-up-circle-fill text-rose fs-4"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $m->description }}</div>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        @php
                                            $typeBadge = match($m->transaction_type) {
                                                'EARN' => 'bg-emerald-soft text-emerald',
                                                'SPEND' => 'bg-amber-soft text-amber',
                                                'PENALTY' => 'bg-rose-soft text-rose',
                                                default => 'bg-light text-muted',
                                            };
                                        @endphp
                                        <span class="badge {{ $typeBadge }} rounded-pill px-2 fw-bold" style="font-size:9px;">{{ $m->transaction_type }}</span>
                                        <span class="text-muted" style="font-size:10px;"><i class="bi bi-clock me-1"></i>{{ $m->created_at->translatedFormat('d M Y, H:i') }}</span>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="fw-extrabold {{ $m->amount >= 0 ? 'text-emerald' : 'text-rose' }}" style="font-size:18px;">
                                        {{ $m->amount >= 0 ? '+' : '' }}{{ $m->amount }}
                                    </div>
                                    <div class="text-muted fw-bold" style="font-size:10px;">Saldo: {{ number_format($m->current_balance) }}</div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-5 text-muted">
                                    <i class="bi bi-journal-x display-4 d-block mb-2 opacity-25"></i>
                                    <div class="fw-bold">Tidak ada data mutasi.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($mutasi->hasPages())
            <div class="card-footer bg-white border-0 p-3 d-flex justify-content-center">
                {{ $mutasi->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .text-emerald { color: #10b981 !important; }
    .text-rose { color: #e11d48 !important; }
    .text-amber { color: #d97706 !important; }
    .bg-emerald-soft { background-color: #ecfdf5 !important; }
    .bg-rose-soft { background-color: #fff1f2 !important; }
    .bg-amber-soft { background-color: #fffbeb !important; }
</style>
@endsection
