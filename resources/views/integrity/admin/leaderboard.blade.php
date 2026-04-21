@extends('layouts.app')

@section('title', 'Integrity Leaderboard - PresenceX')

@section('content')
<div class="container-fluid px-4 md:px-5 fade-in no-print">
    {{-- Summary Stats Section --}}
    <div class="card border-0 shadow-premium rounded-5 overflow-hidden mb-5">
        <div class="card-body p-0">
            <div class="row g-0">
                <div class="col-lg-5 bg-main-gradient p-4 p-md-5 text-white position-relative">
                    <div class="banner-pattern opacity-10"></div>
                    <div class="position-relative z-1">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="bg-white bg-opacity-20 p-3 rounded-4 shadow-sm">
                                <i class="bi bi-trophy-fill fs-2"></i>
                            </div>
                            <div>
                                <h1 class="fw-black mb-0 ls-tight">Papan Peringkat</h1>
                                <p class="text-white text-opacity-75 small mb-0 fw-bold">Monitoring integritas & disiplin siswa.</p>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-4">
                                <div class="small fw-bold text-white text-opacity-50 text-uppercase ls-1">SISWA AKTIF</div>
                                <h3 class="fw-black mb-0">{{ $totalSiswaAktif }}</h3>
                            </div>
                            <div class="col-4 border-start border-white border-opacity-20 ps-3">
                                <div class="small fw-bold text-white text-opacity-50 text-uppercase ls-1">TOTAL SKOR</div>
                                <h3 class="fw-black mb-0">{{ number_format($leaderboard->sum('current_balance')) }}</h3>
                            </div>
                            <div class="col-4 border-start border-white border-opacity-20 ps-3">
                                <div class="small fw-bold text-white text-opacity-50 text-uppercase ls-1">RATA-RATA</div>
                                <h3 class="fw-black mb-0">{{ number_format($avgBalance, 1) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 bg-white p-4 p-md-5">
                    <form action="{{ route('integrity.leaderboard') }}" method="GET" id="filterForm">
                        <input type="hidden" name="period" value="{{ request('period', 'month') }}">
                        <div class="row g-3">
                            <div class="col-md-6 col-12">
                                <label class="form-label x-small fw-extrabold text-muted text-uppercase ls-1">Pariode & Pencarian</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 px-3"><i class="bi bi-search"></i></span>
                                    <input type="text" name="search" value="{{ request('search') }}" class="form-control bg-light border-0 py-2 fw-bold" placeholder="Cari Nama / NIS...">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <label class="form-label x-small fw-extrabold text-muted text-uppercase ls-1">Pilih Kelas</label>
                                <select name="rombongan_belajar_id" class="form-select bg-light border-0 py-2 fw-bold" onchange="this.form.submit()">
                                    <option value="">Semua Kelas</option>
                                    @foreach($classes as $c)
                                        <option value="{{ $c->id }}" {{ request('rombongan_belajar_id') == $c->id ? 'selected' : '' }}>{{ $c->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8 col-12">
                                <label class="form-label x-small fw-extrabold text-muted text-uppercase ls-1">Saring Tanggal</label>
                                <input type="date" name="date" value="{{ request('date') }}" class="form-control bg-light border-0 py-2 fw-bold" onchange="this.form.submit()">
                            </div>
                            <div class="col-md-4 col-12 d-flex align-items-end">
                                <a href="{{ route('integrity.leaderboard') }}" class="btn btn-light w-100 rounded-pill py-2 fw-bold text-muted border">
                                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Controls & Data Table --}}
    <div class="card border-0 shadow-premium rounded-5 overflow-hidden mb-5">
        <div class="card-header bg-white p-4 border-0 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="bg-light p-1 rounded-pill d-flex gap-1 shadow-inner">
                <a href="{{ route('integrity.leaderboard', ['period' => 'month'] + request()->except('period')) }}" 
                   class="btn {{ request('period', 'month') == 'month' ? 'btn-white shadow-sm' : 'btn-link text-muted' }} btn-sm rounded-pill px-4 fw-bold text-decoration-none">
                    <i class="bi bi-calendar-event me-2"></i>Bulan Ini
                </a>
                <a href="{{ route('integrity.leaderboard', ['period' => 'all'] + request()->except('period')) }}" 
                   class="btn {{ request('period') == 'all' ? 'btn-white shadow-sm' : 'btn-link text-muted' }} btn-sm rounded-pill px-4 fw-bold text-decoration-none">
                    <i class="bi bi-infinity me-2"></i>Semua Waktu
                </a>
            </div>
            
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm" onclick="printReport()">
                    <i class="bi bi-printer-fill me-2"></i>Cetak Laporan
                </button>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0" id="leaderboardTable">
                    <thead>
                        <tr class="bg-light">
                            <th class="ps-4 py-3 x-small fw-extrabold text-muted text-uppercase ls-2">Rank</th>
                            <th class="py-3 x-small fw-extrabold text-muted text-uppercase ls-2">Siswa</th>
                            <th class="py-3 x-small fw-extrabold text-muted text-uppercase ls-2">Kelas</th>
                            <th class="py-3 x-small fw-extrabold text-muted text-uppercase ls-2 text-center">Poin {{ request('period') == 'all' ? 'Loncatan' : 'Bulan Ini' }}</th>
                            <th class="py-3 text-center x-small fw-extrabold text-muted text-uppercase ls-2">Status</th>
                            <th class="py-3 pe-4 x-small fw-extrabold text-muted text-uppercase ls-2 text-end">Saldo Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaderboard as $index => $row)
                        <tr class="leaderboard-row transition-all clickable-row" onclick="viewDetails({{ $row->id }})">
                            <td class="ps-4 py-3">
                                @if($index == 0) <div class="rank-badge gold"><i class="bi bi-trophy-fill"></i></div> 
                                @elseif($index == 1) <div class="rank-badge silver">2</div>
                                @elseif($index == 2) <div class="rank-badge bronze">3</div>
                                @else <div class="rank-num">{{ $index + 1 }}</div> @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-circle rounded-circle d-flex align-items-center justify-content-center bg-primary-soft text-primary fw-bold" style="width:45px; height:45px;">
                                        {{ substr($row->nama, 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="fw-extrabold text-dark fs-6">{{ $row->nama }}</div>
                                        <div class="text-muted x-small fw-bold">NIS: {{ $row->nis }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark rounded-pill px-3 py-1 fw-bold border">{{ $row->nama_kelas ?? 'Tanpa Kelas' }}</span>
                            </td>
                            <td class="text-center">
                                <span class="fw-extrabold {{ $row->total_poin_bulan >= 0 ? 'text-emerald' : 'text-rose' }} fs-5">
                                    {{ $row->total_poin_bulan >= 0 ? '+' : '' }}{{ $row->total_poin_bulan }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($row->total_poin_bulan > 100)
                                    <span class="badge bg-emerald-soft text-emerald rounded-pill px-2 py-1 x-small fw-bold"><i class="bi bi-star-fill me-1"></i>ELITE</span>
                                @elseif($row->total_poin_bulan >= 0)
                                    <span class="badge bg-primary-soft text-primary rounded-pill px-2 py-1 x-small fw-bold">KONSISTEN</span>
                                @else
                                    <span class="badge bg-rose-soft text-rose rounded-pill px-2 py-1 x-small fw-bold">WARNING</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-inline-flex bg-dark text-white px-4 py-2 rounded-pill fw-black fs-5 shadow-sm">
                                    {{ number_format($row->current_balance) }}
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-5 text-muted">Data tidak ditemukan untuk kriteria ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- PRINT ONLY LAYOUT --}}
<div class="print-only" style="display:none;">
    <div class="text-center mb-5 border-bottom pb-4">
        <h2 class="fw-black mb-1">LAPORAN RANKING INTEGRITAS SISWA</h2>
        <h4 class="text-uppercase mb-0">PRESENCEX DIGITAL ECOSYSTEM</h4>
        <p class="text-muted small mt-2">Periode: {{ request('period') == 'all' ? 'Semua Waktu' : now()->translatedFormat('F Y') }} | Dicetak pada: {{ now()->translatedFormat('d/m/Y H:i') }}</p>
    </div>
    
    <table class="table table-bordered align-middle">
        <thead class="bg-light">
            <tr>
                <th width="60">RANK</th>
                <th>NAMA SISWA</th>
                <th>NIS</th>
                <th>KELAS</th>
                <th class="text-center">POIN PERIODE</th>
                <th class="text-end">SALDO AKHIR</th>
            </tr>
        </thead>
        <tbody>
            @foreach($leaderboard as $index => $row)
            <tr>
                <td class="text-center fw-bold">{{ $index + 1 }}</td>
                <td class="fw-bold">{{ $row->nama }}</td>
                <td>{{ $row->nis }}</td>
                <td>{{ $row->nama_kelas }}</td>
                <td class="text-center fw-bold">{{ $row->total_poin_bulan }}</td>
                <td class="text-end fw-bold">{{ number_format($row->current_balance) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="row mt-5">
        <div class="col-8"></div>
        <div class="col-4 text-center">
            <p class="mb-5">Dicetak Oleh,</p>
            <br><br>
            <p class="fw-bold border-top pt-2">System Administrator</p>
        </div>
    </div>
</div>

<style>
    :root { --main-blue: #2563eb; --emerald: #10b981; --rose: #e11d48; }
    .bg-main-gradient { background: linear-gradient(135deg, #0f172a, #142850, #2563eb); }
    .shadow-premium { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08); }
    .shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06); }
    .fw-black { font-weight: 950; }
    .ls-tight { letter-spacing: -2px; }
    .rank-badge { width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 900; color: white; }
    .rank-badge.gold { background: linear-gradient(135deg, #FFD700, #B8860B); box-shadow: 0 4px 10px rgba(184,134,11,0.4); }
    .rank-badge.silver { background: linear-gradient(135deg, #C0C0C0, #808080); }
    .rank-badge.bronze { background: linear-gradient(135deg, #CD7F32, #8B4513); }
    .rank-num { width: 35px; text-align: center; font-weight: 900; color: #94a3b8; }
    .bg-emerald-soft { background: #ecfdf5; }
    .bg-primary-soft { background: #eff6ff; }
    .bg-rose-soft { background: #fff1f2; }
    .text-emerald { color: #10b981; }
    .text-rose { color: #e11d48; }
    .clickable-row { cursor: pointer; border-bottom: 1px solid #f1f5f9; }
    .clickable-row:hover { background-color: #f8fafc; transform: scale(1.002); }
    .ls-1 { letter-spacing: 1px; }

    @media print {
        .no-print { display: none !important; }
        .print-only { display: block !important; }
        body { background: white !important; font-size: 12pt; }
        .table { width: 100% !important; border-collapse: collapse !important; }
        .table-bordered th, .table-bordered td { border: 1px solid #000 !important; padding: 10px !important; }
        @page { margin: 1cm; size: A4; }
    }
</style>

<script>
    function printReport() {
        window.print();
    }
    
    function viewDetails(id) {
        // Fitur tambahan: Bisa diarahkan ke profil siswa atau buka modal history
        // window.location.href = `/integrity/history/${id}`;
        console.log("Viewing details for student ID:", id);
    }
</script>
@endsection
