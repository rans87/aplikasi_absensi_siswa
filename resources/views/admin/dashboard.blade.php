@extends('layouts.app')

@section('title', 'Admin Dashboard - PresenceX')

@section('content')
<div class="container-fluid px-4 md:px-5 fade-in">
    {{-- Top Welcome Section --}}
    <div class="row mb-5">
        <div class="col-12">
            <div class="welcome-banner position-relative overflow-hidden rounded-5 shadow-premium text-white">
                <div class="banner-pattern"></div>
                <div class="card-body p-4 p-md-5 position-relative z-1">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="badge bg-white bg-opacity-20 text-white px-3 py-2 rounded-pill fw-bold ls-1" style="font-size: 10px; backdrop-filter: blur(5px);">
                                    <i class="bi bi-shield-lock-fill me-1"></i> ADMINISTRATOR PRIVILEGE
                                </span>
                                <span class="badge bg-emerald text-white px-3 py-2 rounded-pill fw-bold ls-1" style="font-size: 10px;">
                                    <div class="pulse-dot me-1"></div> SISTEM AKTIF
                                </span>
                            </div>
                            <h1 class="display-4 fw-black mb-3 ls-tight">Selamat Datang, <span class="text-sky">Administrator!</span></h1>
                            <p class="text-white text-opacity-80 fw-medium fs-5 mb-0 max-w-600">
                                Monitor performa presensi, kendali sistem QR, dan pantau kedisiplinan siswa dalam satu panel kontrol modern.
                            </p>
                        </div>
                        <div class="col-lg-4 d-none d-lg-block">
                            <div class="glass-time-card p-4 rounded-5 text-center float-end">
                                <div class="text-white text-opacity-60 small fw-bold mb-1 ls-2">WAKTU SERVER</div>
                                <h2 class="fw-black mb-0 display-5 timer-text" id="liveTime">00:00:00</h2>
                                <div class="text-sky fw-extrabold small mt-2">{{ now()->translatedFormat('d F Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Stats Row --}}
    <div class="row g-4 mb-5">
        @php
            $stats = [
                ['label' => 'Total Siswa', 'val' => '1,927', 'icon' => 'bi-people-fill', 'color' => '#2563eb', 'bg' => '#eff6ff'],
                ['label' => 'Guru Aktif', 'val' => '154', 'icon' => 'bi-person-badge-fill', 'color' => '#8b5cf6', 'bg' => '#f5f3ff'],
                ['label' => 'Absensi Hari Ini', 'val' => '0', 'icon' => 'bi-calendar-check-fill', 'color' => '#10b981', 'bg' => '#ecfdf5'],
                ['label' => 'Total Kelas', 'val' => '52', 'icon' => 'bi-layers-fill', 'color' => '#f59e0b', 'bg' => '#fffbeb'],
            ];
        @endphp
        @foreach($stats as $s)
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-5 stat-card h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-4 p-3 d-flex align-items-center justify-content-center shadow-sm" style="background: {{ $s['bg'] }}; width: 55px; height: 55px;">
                            <i class="bi {{ $s['icon'] }} fs-3" style="color: {{ $s['color'] }}"></i>
                        </div>
                        <div class="text-end">
                            <span class="text-muted fw-bold small text-uppercase ls-1">{{ $s['label'] }}</span>
                        </div>
                    </div>
                    <h2 class="fw-black text-dark mb-0 ls-tight">{{ $s['val'] }}</h2>
                    <div class="mt-2">
                        <span class="text-emerald fw-bold small"><i class="bi bi-graph-up me-1"></i>+2.5%</span>
                        <span class="text-muted x-small ms-1">dari bulan lalu</span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Analytics Section --}}
    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-5 h-100 overflow-hidden">
                <div class="card-header bg-white p-4 border-0 d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="fw-extrabold text-dark mb-1">Tren Kehadiran 7 Hari</h5>
                        <p class="text-muted x-small mb-0">Statistik scan harian seluruh siswa</p>
                    </div>
                    <button class="btn btn-light rounded-pill btn-sm px-3 fw-bold">Detail <i class="bi bi-chevron-right ms-1"></i></button>
                </div>
                <div class="card-body p-4">
                    <div style="height: 300px;" class="d-flex align-items-end gap-3 justify-content-between px-3">
                        @foreach([12, 45, 67, 23, 89, 43, 13] as $val)
                            <div class="bg-primary rounded-pill w-100 position-relative chart-bar-hover" style="height: {{ $val }}%; transition: height 1s ease-out;">
                                <div class="bar-value small fw-bold text-primary position-absolute w-100 text-center" style="top:-25px">{{ $val }}</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="d-flex justify-content-between mt-3 px-1">
                        @foreach(['Sn', 'Sl', 'Rb', 'Km', 'Jm', 'Sb', 'Mg'] as $day)
                            <span class="text-muted x-small fw-bold text-uppercase">{{ $day }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-5 mb-4 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="bg-violet-soft p-3 rounded-4">
                            <i class="bi bi-stars text-violet fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-extrabold text-dark mb-0">Indeks Karakter</h6>
                            <span class="badge bg-violet-soft text-violet rounded-pill x-small fw-bold">RATA-RATA GLOBAL</span>
                        </div>
                    </div>
                    <div class="text-center py-3">
                        <h1 class="display-3 fw-black text-dark mb-0">84<span class="fs-4 text-muted">/100</span></h1>
                        <p class="text-emerald fw-bold small mt-1"><i class="bi bi-arrow-up-circle-fill me-1"></i>Sangat Baik</p>
                    </div>
                </div>
            </div>
            <div class="card border-0 shadow-sm rounded-5 overflow-hidden">
                <div class="card-body p-4">
                    <h6 class="fw-extrabold text-dark mb-3">Rekap Bulanan <span class="text-muted fw-medium fs-6 ms-2">April 2026</span></h6>
                    <div class="d-flex flex-column gap-3">
                        <div class="p-3 bg-emerald-soft rounded-4 d-flex align-items-center justify-content-between">
                            <span class="fw-bold text-emerald"><i class="bi bi-check-circle-fill me-2"></i>Hadir</span>
                            <span class="fw-black text-emerald fs-5">1,245</span>
                        </div>
                        <div class="p-3 bg-amber-soft rounded-4 d-flex align-items-center justify-content-between">
                            <span class="fw-bold text-warning"><i class="bi bi-exclamation-circle-fill me-2"></i>Terlambat</span>
                            <span class="fw-black text-warning fs-5">234</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .welcome-banner {
        background: linear-gradient(135deg, #1e3a8a, #2563eb, #3b82f6);
        min-height: 280px;
    }
    .banner-pattern {
        position: absolute; inset: 0;
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .ls-tight { letter-spacing: -2px; }
    .ls-1 { letter-spacing: 1px; }
    .ls-2 { letter-spacing: 2px; }
    .fw-black { font-weight: 900; }
    .text-sky { color: #7dd3fc !important; }
    .bg-emerald { background-color: #10b981 !important; }
    .bg-emerald-soft { background-color: #ecfdf5 !important; }
    .bg-amber-soft { background-color: #fffbeb !important; }
    .bg-violet-soft { background-color: #f5f3ff !important; }
    .text-violet { color: #8b5cf6 !important; }
    .text-emerald { color: #10b981 !important; }
    .x-small { font-size: 11px; }
    .max-w-600 { max-width: 600px; }
    
    .glass-time-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.15);
    }
    
    .stat-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
    }
    
    .pulse-dot {
        width: 8px; height: 8px; background: #fff; border-radius: 50%;
        animation: pulse-white 2s infinite;
    }
    @keyframes pulse-white {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(255, 255, 255, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
    }
    
    .chart-bar-hover:hover {
        background-color: #1e3a8a !important;
        cursor: pointer;
    }
    
    @media (max-width: 768px) {
        .welcome-banner { min-height: auto; }
        .ls-tight { letter-spacing: -1px; }
        .display-4 { font-size: 2rem !important; }
    }
</style>

<script>
    function updateClock() {
        const now = new Date();
        const timeStr = now.getHours().toString().padStart(2, '0') + ':' + 
                        now.getMinutes().toString().padStart(2, '0') + ':' + 
                        now.getSeconds().toString().padStart(2, '0');
        document.getElementById('liveTime').textContent = timeStr;
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>
@endsection
