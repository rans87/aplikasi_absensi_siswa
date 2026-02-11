@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row g-4 mb-4">
    {{-- Main Stats Hero --}}
    <div class="col-12">
        <div class="card border-0 shadow-premium overflow-hidden fade-in hero-dashboard">
            <div class="card-body p-0 position-relative">
                <div class="hero-bg-accent"></div>
                <div class="p-5 p-md-5 position-relative z-1">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="d-flex align-items-center gap-2 mb-4">
                                <span class="badge bg-white bg-opacity-10 text-white px-3 py-2 border border-white border-opacity-25 rounded-pill ls-1 fw-bold" style="font-size: 10px;">
                                    <i class="bi bi-shield-lock-fill me-2"></i>ADMINISTRATOR PRIVILEGE
                                </span>
                                <span class="badge bg-emerald bg-opacity-20 text-emerald px-3 py-2 border border-emerald border-opacity-25 rounded-pill ls-1 fw-bold" style="font-size: 10px;">
                                    <i class="bi bi-broadcast me-2 pulse"></i>SISTEM AKTIF
                                </span>
                            </div>
                            <h1 class="fw-extrabold text-white mb-3 display-4 h-ls-tight">Selamat Datang, <span class="text-secondary-blue">Administrator!</span></h1>
                            <p class="text-white text-opacity-75 mb-0 fs-5 fw-medium pe-lg-5">Monitor performa presensi, kendali sistem QR, dan pantau kedisiplinan siswa dalam satu panel kontrol modern.</p>
                        </div>
                        <div class="col-lg-4 text-center text-lg-end mt-4 mt-lg-0">
                            <div class="glass-card-premium p-4 rounded-5 d-inline-block text-white shadow-lg">
                                <div class="text-uppercase ls-2 small opacity-50 mb-2 fw-bold">Waktu Server</div>
                                <h2 class="mb-1 fw-extrabold ls-tight">{{ now()->format('H:i') }}</h2>
                                <div class="opacity-75 fw-bold small"><i class="bi bi-calendar3 me-2"></i>{{ now()->translatedFormat('d F Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards Dynamic --}}
    @php
        $stats = [
            ['Total Siswa', $total_siswa, 'bi-people-fill', 'primary', 'Siswa Terdaftar'],
            ['Guru Aktif', $total_guru, 'bi-person-badge-fill', 'indigo', 'Tenaga Pendidik'],
            ['Absensi/Harian', $absensi_hari_ini, 'bi-calendar-check-fill', 'emerald', 'Total Scan'],
            ['Pelanggaran', $total_pelanggaran, 'bi-exclamation-triangle-fill', 'rose', 'Poin Kedisiplinan'],
        ];
    @endphp

    @foreach($stats as $s)
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 h-100 overflow-hidden shadow-sm hover-up-premium">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="icon-box-premium bg-{{ $s[3] }}-soft text-{{ $s[3] }}">
                        <i class="bi {{ $s[2] }}"></i>
                    </div>
                    <div class="badge-growth bg-{{ $s[3] }}-soft text-{{ $s[3] }}">+{{ rand(1, 5) }}%</div>
                </div>
                <div>
                    <h3 class="fw-extrabold mb-1 ls-tight">{{ number_format($s[1]) }}</h3>
                    <div class="text-dark fw-bold small text-uppercase ls-1 opacity-75">{{ $s[0] }}</div>
                    <p class="text-muted small mb-0 mt-1">{{ $s[4] }}</p>
                </div>
            </div>
            <div class="card-footer-mini bg-{{ $s[3] }}"></div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4">
    {{-- Live Activity Center (Violations & Achievements) --}}
    <div class="col-12">
        <div class="row g-4">
            {{-- Recent Violations --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100 bg-white">
                    <div class="card-header border-0 bg-transparent p-4 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="bg-rose bg-opacity-10 p-2 rounded-4 me-3">
                                <i class="bi bi-patch-exclamation-fill text-rose fs-4"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-extrabold text-dark">Pelanggaran Baru</h5>
                                <small class="text-muted fw-medium">Input poin negatif terbaru</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 border-0">
                                <tbody>
                                    @forelse($recent_points as $p)
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle-sm bg-light text-primary fw-extrabold me-3">{{ substr($p->siswa->nama ?? 'S', 0, 1) }}</div>
                                                <div>
                                                    <div class="fw-bold text-dark small ls-tight">{{ Str::limit($p->siswa->nama ?? 'Siswa', 25) }}</div>
                                                    <div class="text-muted mt-1" style="font-size: 10px;"><i class="bi bi-clock me-1"></i>{{ $p->created_at->diffForHumans() }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-rose-soft text-rose border-0 fw-bold small">{{ Str::limit($p->nama_pelanggaran, 18) }}</span></td>
                                        <td class="pe-4 text-end">
                                            <span class="text-rose fw-extrabold small fs-6">-{{ $p->poin }}</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="3" class="text-center py-5 text-muted small fw-bold">Belum ada pelanggaran</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Achievements --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100 bg-white">
                    <div class="card-header border-0 bg-transparent p-4 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="bg-emerald bg-opacity-10 p-2 rounded-4 me-3">
                                <i class="bi bi-award-fill text-emerald fs-4"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-extrabold text-dark">Prestasi Baru</h5>
                                <small class="text-muted fw-medium">Input poin prestasi terbaru</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 border-0">
                                <tbody>
                                    @php $recent_prestasi = \App\Models\Prestasi::with('siswa')->latest()->take(5)->get(); @endphp
                                    @forelse($recent_prestasi as $pr)
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle-sm bg-light text-emerald fw-extrabold me-3">{{ substr($pr->siswa->nama ?? 'S', 0, 1) }}</div>
                                                <div>
                                                    <div class="fw-bold text-dark small ls-tight">{{ Str::limit($pr->siswa->nama ?? 'Siswa', 25) }}</div>
                                                    <div class="text-muted mt-1" style="font-size: 10px;"><i class="bi bi-clock me-1"></i>{{ $pr->created_at->diffForHumans() }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-emerald-soft text-emerald border-0 fw-bold small">{{ Str::limit($pr->nama_prestasi, 18) }}</span></td>
                                        <td class="pe-4 text-end">
                                            <span class="text-emerald fw-extrabold small fs-6">+{{ $pr->poin }}</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="3" class="text-center py-5 text-muted small fw-bold">Belum ada prestasi</td></tr>
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

<style>
    .ls-tight { letter-spacing: -1px; }
    .ls-extratight { letter-spacing: -2px; }
    .ls-1 { letter-spacing: 0.5px; }
    .ls-2 { letter-spacing: 1.5px; }
    .h-ls-tight { letter-spacing: -2.5px; }
    .fs-xs { font-size: 8px; }

    .hero-dashboard { background: linear-gradient(135deg, var(--deep-blue), var(--primary-blue)); border-radius: 40px !important; }
    .hero-bg-accent { position: absolute; top: -50%; right: -10%; width: 500px; height: 500px; background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%); border-radius: 50%; }
    .glass-card-premium { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.1); }
    
    .hover-up-premium:hover { transform: translateY(-10px); box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.1) !important; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
    .icon-box-premium { width: 48px; height: 48px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    .badge-growth { padding: 4px 10px; border-radius: 10px; font-size: 10px; font-weight: 800; }
    .card-footer-mini { height: 4px; width: 100%; border-radius: 0 0 4px 4px; opacity: 0.3; }

    .bg-indigo { background-color: #6366f1; }
    .bg-indigo-soft { background-color: #eef2ff; }
    .text-indigo { color: #6366f1; }
    .bg-rose { background-color: #e11d48; }
    .bg-rose-soft { background-color: #fff1f2; }
    .text-rose { color: #e11d48; }
    .bg-emerald { background-color: #10b981; }
    .bg-emerald-soft { background-color: #ecfdf5; }
    .text-emerald { color: #10b981; }
    .bg-amber { background-color: #f59e0b; }
    .bg-amber-soft { background-color: #fffbeb; }
    .text-amber { color: #f59e0b; }

    .qr-control-box { transition: all 0.4s ease; border: 1px solid var(--border-color); }
    .qr-session-morning { background: linear-gradient(135deg, #fff9f0, #ffffff); }
    .qr-session-afternoon { background: linear-gradient(135deg, #f0f4ff, #ffffff); }
    .session-icon-box { width: 80px; height: 80px; border-radius: 24px; background: white; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; }
    
    .btn-session { padding: 18px; border-radius: 20px; border: 2px solid #f1f5f9; background: #f8fafc; color: #64748b; font-weight: 800; text-decoration: none; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .btn-session:hover { background: #f1f5f9; color: var(--deep-blue); transform: scale(1.02); }
    .btn-session.active.morning { border-color: var(--primary-blue); background: var(--primary-blue); color: white; box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2); }
    .btn-session.active.afternoon { border-color: var(--amber); background: var(--amber); color: white; box-shadow: 0 10px 20px rgba(245, 158, 11, 0.2); }

    .btn-view-all { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: var(--primary-blue); text-decoration: none; padding: 8px 16px; border-radius: 12px; background: var(--light-blue); }
    .btn-view-all:hover { filter: brightness(0.95); }
    .avatar-circle-sm { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }

    .pulse { animation: pulseSmall 2s infinite; }
    @keyframes pulseSmall { 0% { opacity: 1; } 50% { opacity: 0.3; } 100% { opacity: 1; } }
</style>
@endsection
