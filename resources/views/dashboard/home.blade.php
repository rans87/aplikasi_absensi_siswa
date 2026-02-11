@extends('layouts.app')

@section('title', 'System Dashboard')

@section('content')
<div class="row g-4 mb-5">
    {{-- System Status Hero --}}
    <div class="col-12">
        <div class="card border-0 shadow-premium overflow-hidden fade-in hero-system">
            <div class="card-body p-0 position-relative">
                <div class="hero-pattern"></div>
                <div class="p-5 position-relative z-1 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-white bg-opacity-20 p-2 rounded-4 backdrop-blur shadow-sm border border-white border-opacity-25">
                                <i class="bi bi-cpu text-white fs-4"></i>
                            </div>
                            <span class="badge bg-emerald bg-opacity-10 text-emerald px-3 py-2 rounded-pill ls-1 fw-bold" style="font-size: 10px; border: 1px solid rgba(16,185,129,0.2);">
                                SYSTEM OPERATIONAL
                            </span>
                        </div>
                        <h1 class="fw-extrabold text-white mb-2 display-5 ls-tight">System Overview</h1>
                        <p class="text-white text-opacity-75 mb-0 fs-5 fw-medium">Monitor real-time infrastructure and user engagement across PresenceX.</p>
                    </div>
                    <div class="d-none d-lg-block text-end">
                        <div class="glass-info px-4 py-3 rounded-pill border border-white border-opacity-10 shadow-lg">
                            <h6 class="text-white mb-1 fw-extrabold ls-1">{{ now()->format('l, d M Y') }}</h6>
                            <div class="d-flex align-items-center justify-content-end gap-2 text-white-50 small fw-bold">
                                <span class="pulse-emerald"></span> SYNC ACTIVE
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Row --}}
    @php
      $cards = [
        ['Siswa Hadir Hari Ini', '150', 'bi-people-fill', 'primary', 'Siswa'],
        ['Guru Aktif', '53', 'bi-person-badge-fill', 'info', 'Pendidik'],
        ['Izin / Sakit', '12', 'bi-calendar-event-fill', 'amber', 'Izin'],
        ['Tanpa Keterangan', '8', 'bi-x-octagon-fill', 'rose', 'Alfa'],
      ];
    @endphp

    @foreach ($cards as $card)
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 h-100 shadow-sm hover-up-premium-mini overflow-hidden bg-white">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-circle bg-{{ $card[3] }}-soft text-{{ $card[3] }} me-3">
                        <i class="bi {{ $card[2] }}"></i>
                    </div>
                    <span class="text-muted small fw-bold text-uppercase ls-1">{{ $card[0] }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <h2 class="fw-extrabold mb-0 ls-tight">{{ $card[1] }}</h2>
                    <span class="badge bg-{{ $card[3] }}-soft text-{{ $card[3] }} rounded-pill px-2 py-1" style="font-size: 10px;">{{ $card[4] }}</span>
                </div>
            </div>
            <div class="progress-mini bg-{{ $card[3] }}"></div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4">
    {{-- Attendance Trend --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100 bg-white">
            <div class="card-header border-0 bg-transparent p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0 fw-extrabold text-dark ls-tight">Trend Kehadiran Mingguan</h5>
                    <p class="text-muted small mb-0 mt-1">Akumulasi seluruh data siswa per hari</p>
                </div>
                <div class="btn-group rounded-pill overflow-hidden border shadow-sm">
                    <button class="btn btn-light btn-sm px-3 fw-bold active">7D</button>
                    <button class="btn btn-light btn-sm px-3 fw-bold">30D</button>
                </div>
            </div>
            <div class="card-body p-4 pt-0">
                <div style="height:350px;" class="position-relative">
                    <canvas id="lineChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Distribution --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100 bg-white">
            <div class="card-header border-0 bg-transparent p-4">
                <h5 class="mb-0 fw-extrabold text-dark ls-tight">Distribusi Status</h5>
                <p class="text-muted small mb-0 mt-1">Persentase hari ini</p>
            </div>
            <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center">
                <div style="height:250px; width: 100%;" class="position-relative">
                    <canvas id="pieChart"></canvas>
                    <div class="chart-center-label">
                        <h3 class="mb-0 fw-extrabold text-primary">85%</h3>
                        <small class="text-muted fw-bold uppercase">Hadir</small>
                    </div>
                </div>
                <div class="mt-5 w-100">
                    <div class="dist-item d-flex align-items-center justify-content-between mb-3 p-3 rounded-4 bg-light-subtle">
                        <div class="d-flex align-items-center gap-3">
                            <div class="dot bg-primary shadow-sm"></div>
                            <span class="fw-bold text-dark small">Hadir</span>
                        </div>
                        <span class="fw-extrabold text-primary">85%</span>
                    </div>
                    <div class="dist-item d-flex align-items-center justify-content-between mb-3 p-3 rounded-4 bg-light-subtle">
                        <div class="d-flex align-items-center gap-3">
                            <div class="dot bg-amber shadow-sm"></div>
                            <span class="fw-bold text-dark small">Izin/Sakit</span>
                        </div>
                        <span class="fw-extrabold text-amber">10%</span>
                    </div>
                    <div class="dist-item d-flex align-items-center justify-content-between p-3 rounded-4 bg-light-subtle">
                        <div class="d-flex align-items-center gap-3">
                            <div class="dot bg-rose shadow-sm"></div>
                            <span class="fw-bold text-dark small">Alfa</span>
                        </div>
                        <span class="fw-extrabold text-rose">5%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .ls-tight { letter-spacing: -1.2px; }
    .ls-1 { letter-spacing: 0.5px; }

    .hero-system { background: linear-gradient(135deg, #0f172a, #1e293b); border-radius: 40px !important; }
    .hero-pattern { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: radial-gradient(circle at 80% 20%, rgba(59,130,246,0.1) 0%, transparent 40%); }
    .backdrop-blur { backdrop-filter: blur(8px); }

    .icon-circle { width: 48px; height: 48px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    .bg-info-soft { background-color: #f0f9ff; }
    .text-info { color: #0ea5e9; }
    .bg-amber-soft { background-color: #fffbeb; }
    .text-amber { color: #f59e0b; }
    .bg-rose-soft { background-color: #fff1f2; }
    .text-rose { color: #f43f5e; }
    .progress-mini { height: 4px; border-radius: 0 0 10px 10px; opacity: 0.4; margin-top: 15px; }

    .pulse-emerald { display: inline-block; width: 8px; height: 8px; background-color: #10b981; border-radius: 50%; box-shadow: 0 0 0 rgba(16,185,129, 0.4); animation: pulseEmerald 2s infinite; }
    @keyframes pulseEmerald { 0% { box-shadow: 0 0 0 0 rgba(16,185,129, 0.4); } 70% { box-shadow: 0 0 0 10px rgba(16,185,129, 0); } 100% { box-shadow: 0 0 0 0 rgba(16,185,129, 0); } }

    .chart-center-label { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; }
    .dot { width: 10px; height: 10px; border-radius: 50%; }
    .bg-light-subtle { background-color: #f8fafc; }
    .transition-all { transition: all 0.2s ease; }
    .hover-up-premium-mini:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.05) !important; transition: all 0.3s ease; }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    Chart.defaults.font.family = "'Outfit', sans-serif";
    Chart.defaults.color = "#64748b";

    document.addEventListener("DOMContentLoaded", function () {
        // Line Chart
        const lineCtx = document.getElementById('lineChart');
        if (lineCtx) {
            const gradient = lineCtx.getContext('2d').createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
            gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

            new Chart(lineCtx, {
                type: 'line',
                data: {
                    labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                    datasets: [{
                        label: 'Siswa Hadir',
                        data: [140, 155, 148, 160, 152, 145],
                        borderColor: '#3b82f6',
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.45,
                        borderWidth: 4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#3b82f6',
                        pointBorderWidth: 3,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        pointHoverBorderWidth: 4,
                        pointHoverBackgroundColor: '#fff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { backgroundColor: '#0f172a', titleFont: { size: 13, weight: 'bold' }, bodyFont: { size: 13 }, padding: 12, borderRadius: 12, displayColors: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(226, 232, 240, 0.5)', drawBorder: false }, ticks: { padding: 10, font: { weight: '600' } } },
                        x: { grid: { display: false }, ticks: { padding: 10, font: { weight: '600' } } }
                    },
                    interaction: { intersect: false, mode: 'index' }
                }
            });
        }

        // Pie Chart
        const pieCtx = document.getElementById('pieChart');
        if (pieCtx) {
            new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Hadir', 'Izin/Sakit', 'Alfa'],
                    datasets: [{
                        data: [85, 10, 5],
                        backgroundColor: ['#3b82f6', '#f59e0b', '#f43f5e'],
                        borderWidth: 8,
                        borderColor: '#ffffff',
                        hoverOffset: 12,
                        borderRadius: 20
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '80%',
                    plugins: { legend: { display: false }, tooltip: { enabled: false } },
                    animation: { animateScale: true, animateRotate: true }
                }
            });
        }
    });
</script>
@endpush
@endsection