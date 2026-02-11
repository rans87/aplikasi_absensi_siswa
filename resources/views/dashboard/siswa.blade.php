@extends('layouts.app')

@section('title', 'Siswa Dashboard')

@section('content')
<div class="row g-4">
    {{-- Personal QR & Student Card --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-premium overflow-hidden fade-in student-card-main mx-auto" style="max-width: 450px;">
            @php
                $qr_session = Cache::get('qr_session', 'pagi');
                $qr_salt = Cache::get('qr_salt', 'init');
                $qr_string = $siswa->qr_code . "|" . $qr_session . "|" . $qr_salt;
            @endphp
            <div class="card-body p-4 p-md-5 position-relative">
                <div class="card-glass-overlay"></div>
                <div class="position-relative z-1 text-center">
                    <div class="mb-4">
                        <span class="badge bg-white bg-opacity-20 text-white px-4 py-2 rounded-pill ls-2 fw-bold" style="font-size: 9px; border: 1px solid rgba(255,255,255,0.2);">
                            KARTU PELAJAR DIGITAL
                        </span>
                    </div>
                    
                    <div class="qr-wrapper cursor-pointer" data-bs-toggle="modal" data-bs-target="#qrModal">
                        <div class="qr-container-premium mb-4 d-inline-block p-3 p-md-4 bg-white rounded-5 shadow-lg position-relative group">
                            {!! QrCode::size(200)->eye('circle')->color(15, 23, 42)->generate($qr_string) !!}
                            <div class="qr-scan-line"></div>
                            <div class="qr-hover-overlay rounded-5">
                                <i class="bi bi-zoom-in text-white fs-1"></i>
                            </div>
                        </div>
                    </div>

                    <h3 class="fw-extrabold text-white mb-1 ls-tight fs-4 fs-md-3">{{ $siswa->nama }}</h3>
                    <p class="text-white text-opacity-75 mb-4 fw-bold ls-1 small">NIS: {{ $siswa->nis }}</p>

                    <div class="d-flex justify-content-center flex-wrap gap-2">
                        <div class="glass-badge px-3 py-2 rounded-4 small">
                             <i class="bi bi-shield-check me-1 text-emerald"></i>AKTIF
                        </div>
                        <div class="glass-badge px-3 py-2 rounded-4 small">
                             <i class="bi bi-broadcast me-1 {{ $qr_session == 'pagi' ? 'text-info' : 'text-amber' }}"></i>SESI {{ strtoupper($qr_session) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-deep-blue border-0 p-4 text-center">
                <p class="text-white-50 xs-small mb-0 fw-medium">Klik QR untuk memperbesar tampilan</p>
            </div>
        </div>
    </div>

    {{-- Stats & Points --}}
    <div class="col-lg-7">
        <div class="row g-4">
            {{-- Point Tracker --}}
            <div class="col-6 col-md-6">
                <div class="card border-0 shadow-sm h-100 bg-white overflow-hidden hover-up-premium-mini">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="icon-circle bg-rose-soft text-rose">
                                <i class="bi bi-patch-exclamation-fill"></i>
                            </div>
                            <h6 class="mb-0 fw-extrabold text-dark ls-1 d-none d-md-block">PELANGGARAN</h6>
                        </div>
                        <div class="d-flex align-items-end gap-2">
                            <h1 class="display-4 fw-extrabold text-rose mb-0 ls-extratight">{{ $poin_pelanggaran }}</h1>
                            <div class="pb-2 text-muted small fw-bold uppercase">Points</div>
                        </div>
                    </div>
                    <div class="progress-mini bg-rose"></div>
                </div>
            </div>

            <div class="col-6 col-md-6">
                <div class="card border-0 shadow-sm h-100 bg-white overflow-hidden hover-up-premium-mini">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="icon-circle bg-emerald-soft text-emerald">
                                <i class="bi bi-award-fill"></i>
                            </div>
                            <h6 class="mb-0 fw-extrabold text-dark ls-1 d-none d-md-block">PRESTASI</h6>
                        </div>
                        <div class="d-flex align-items-end gap-2">
                            <h1 class="display-4 fw-extrabold text-emerald mb-0 ls-extratight">+{{ $poin_prestasi }}</h1>
                            <div class="pb-2 text-muted small fw-bold uppercase">Points</div>
                        </div>
                    </div>
                    <div class="progress-mini bg-emerald"></div>
                </div>
            </div>

            {{-- Records Table --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm bg-white overflow-hidden">
                    <div class="card-header border-0 bg-transparent p-4 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-circle bg-primary-soft text-primary">
                                <i class="bi bi-journal-text"></i>
                            </div>
                            <h6 class="mb-0 fw-extrabold text-dark ls-tight">Catatan Kedisiplinan Terbaru</h6>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light-subtle">
                                    <tr>
                                        <th class="ps-4 small fw-bold text-muted border-0">CATATAN</th>
                                        <th class="text-end pe-4 small fw-bold text-muted border-0">TANGGAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $activities = collect();
                                        foreach($recent_pelanggaran as $p) {
                                            $activities->push(['type' => 'Pelanggaran', 'note' => $p->nama_pelanggaran, 'date' => $p->created_at, 'color' => 'rose']);
                                        }
                                        foreach($recent_prestasi as $pr) {
                                            $activities->push(['type' => 'Prestasi', 'note' => $pr->nama_prestasi, 'date' => $pr->created_at, 'color' => 'emerald']);
                                        }
                                        $sortedActivities = $activities->sortByDesc('date');
                                    @endphp

                                    @forelse($sortedActivities as $act)
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="badge bg-{{ $act['color'] }} bg-opacity-10 text-{{ $act['color'] }} rounded-3 px-2 py-1 small fw-bold" style="font-size: 9px;">
                                                    {{ strtoupper($act['type']) }}
                                                </div>
                                                <div class="fw-bold text-dark small ls-tight">{{ $act['note'] }}</div>
                                            </div>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <div class="text-dark fw-bold small">{{ $act['date']->format('d M') }}</div>
                                            <div class="text-muted" style="font-size: 9px;">{{ $act['date']->diffForHumans() }}</div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="2" class="text-center py-5">
                                            <div class="opacity-10 mb-3"><i class="bi bi-inbox fs-1"></i></div>
                                            <p class="text-muted small fw-bold mb-0">Belum ada riwayat catatan.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Motivation --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm bg-deep-blue text-white p-4 overflow-hidden position-relative" style="border-radius: 28px;">
                    <div class="position-absolute top-0 end-0 p-4 opacity-10">
                        <i class="bi bi-quote display-1"></i>
                    </div>
                    <div class="position-relative z-1">
                        <h6 class="fw-bold ls-2 mb-2 opacity-50 text-uppercase" style="font-size: 10px;">INSIGHT</h6>
                        <p class="fs-6 fw-bold mb-0 opacity-75">"Kedisiplinan adalah awal dari setiap kesuksesan besar."</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- QR Magnifier Modal --}}
<div class="modal fade" id="qrModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-5 overflow-hidden">
            <div class="modal-body p-5 text-center bg-deep-blue">
                <div class="mb-4">
                    <h5 class="text-white fw-extrabold ls-1">KODE QR ABSENSI</h5>
                    <p class="text-white-50 small mb-0">{{ $siswa->nama }}</p>
                </div>
                <div class="bg-white p-4 rounded-5 d-inline-block shadow-glow-primary">
                    {!! QrCode::size(300)->eye('circle')->color(15, 23, 42)->generate($qr_string) !!}
                </div>
                <div class="mt-4">
                    <button type="button" class="btn btn-light rounded-pill px-5 fw-bold" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .ls-tight { letter-spacing: -1.2px; }
    .ls-extratight { letter-spacing: -3px; }
    .ls-1 { letter-spacing: 0.5px; }
    .ls-2 { letter-spacing: 1.5px; }
    .xs-small { font-size: 10px; }
    .cursor-pointer { cursor: pointer; }

    .student-card-main { background: linear-gradient(135deg, #0f172a, #2563eb); border-radius: 40px !important; }
    .card-glass-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle at 10% 10%, rgba(255,255,255,0.1) 0%, transparent 50%); }
    
    .qr-container-premium { position: relative; border: 4px solid rgba(255,255,255,0.1); transition: all 0.3s ease; }
    .qr-hover-overlay { position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(37, 99, 235, 0.4); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; opacity: 0; transition: all 0.3s ease; }
    .qr-wrapper:hover .qr-hover-overlay { opacity: 1; }
    .qr-wrapper:hover .qr-container-premium { transform: scale(1.02); }

    .qr-scan-line { position: absolute; top: 0; left: 0; width: 100%; height: 2px; background: var(--primary-blue); box-shadow: 0 0 15px var(--primary-blue); animation: scanLine 3s infinite linear; }
    @keyframes scanLine { 
        0% { top: 10%; } 
        50% { top: 90%; } 
        100% { top: 10%; } 
    }

    .glass-badge { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); font-size: 10px; font-weight: 700; color: white; letter-spacing: 0.5px; }
    
    .shadow-glow-primary { box-shadow: 0 0 30px rgba(37, 99, 235, 0.4); }
    .icon-circle { width: 44px; height: 44px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
    .bg-rose-soft { background-color: #fff1f2; }
    .bg-emerald-soft { background-color: #ecfdf5; }
    .bg-primary-soft { background-color: #eff6ff; }
    .progress-mini { height: 4px; border-radius: 0 0 10px 10px; opacity: 0.4; margin-top: 15px; }
    .bg-light-subtle { background-color: #f8fafc; }
    
    .hover-up-premium-mini:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.05) !important; transition: all 0.3s ease; }

    @media (max-width: 576px) {
        .display-4 { font-size: 2.5rem; }
        .p-5 { padding: 2rem !important; }
        .card-body { padding: 1.5rem !important; }
    }
</style>
@endsection
