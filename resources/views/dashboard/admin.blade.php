@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row g-4 mb-4">
    {{-- Main Stats Hero --}}
    <div class="col-12">
        <div class="card border-0 shadow-premium overflow-hidden fade-in hero-dashboard">
            <div class="card-body p-0 position-relative">
                <div class="hero-bg-accent"></div>
                <div class="p-4 p-md-5 position-relative z-1">
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
            ['Absensi Hari Ini', $absensi_hari_ini, 'bi-calendar-check-fill', 'emerald', 'Total Scan'],
            ['Kelas', $total_kelas, 'bi-layers-fill', 'info', 'Rombongan Belajar'],
        ];
    @endphp

    @foreach($stats as $s)
    <div class="col-xl-2 col-lg-3 col-md-4 col-6">
        <div class="card border-0 h-100 overflow-hidden shadow-sm hover-up-premium">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="icon-box-premium bg-{{ $s[3] }}-soft text-{{ $s[3] }} d-none d-sm-flex">
                        <i class="bi {{ $s[2] }}"></i>
                    </div>
                    <div class="icon-box-premium bg-{{ $s[3] }}-soft text-{{ $s[3] }} d-flex d-sm-none" style="width: 32px; height: 32px; font-size: 1rem;">
                        <i class="bi {{ $s[2] }}"></i>
                    </div>
                </div>
                <div>
                    <h3 class="fw-extrabold mb-1 ls-tight h4-mobile">{{ number_format($s[1]) }}</h3>
                    <div class="text-dark fw-bold small text-uppercase ls-1 opacity-75" style="font-size: 9px;">{{ $s[0] }}</div>
                </div>
            </div>
            <div class="card-footer-mini bg-{{ $s[3] }}"></div>
        </div>
    </div>
    @endforeach
</div>

{{-- Attendance Trend & Monthly Stats & Radar Chart --}}
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100 bg-white">
            <div class="card-header border-0 bg-transparent p-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-4 me-3">
                        <i class="bi bi-graph-up-arrow text-primary fs-4"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-extrabold text-dark">Tren Kehadiran 7 Hari</h5>
                        <small class="text-muted fw-medium">Statistik scan harian</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="attendance-chart d-flex align-items-end justify-content-between gap-1 gap-md-2" style="height: 180px;">
                    @php 
                        $trendCounts = array_column($attendance_trend, 'count');
                        $maxCount = count($trendCounts) > 0 ? max(max($trendCounts), 1) : 1;
                    @endphp
                    @foreach($attendance_trend as $trend)
                    <div class="chart-bar-wrapper text-center flex-fill" style="min-width: 0;">
                        <div class="chart-count fw-bold text-muted mb-2" style="font-size: 10px;">{{ $trend['count'] }}</div>
                        <div class="chart-bar mx-auto" style="height: {{ max(($trend['count'] / $maxCount) * 140, 8) }}px; background: linear-gradient(180deg, var(--primary-blue), var(--secondary-blue)); border-radius: 8px 8px 4px 4px; width: 100%; max-width: 40px; transition: all 0.3s ease;"></div>
                        <div class="chart-label mt-2">
                            <div class="fw-bold text-muted" style="font-size: 9px;">{{ $trend['day'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm h-100 bg-white">
            <div class="card-header border-0 bg-transparent p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-indigo bg-opacity-10 p-2 rounded-4 me-3">
                        <i class="bi bi-bullseye text-indigo fs-4"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-extrabold text-dark">Indeks Karakter</h5>
                        <small class="text-muted fw-medium">Rata-rata Global</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-4 pt-0">
                <div style="height: 200px;">
                    <canvas id="radarChartAdmin"></canvas>
                </div>
                <div class="mt-3 text-center">
                    <span class="badge bg-indigo-soft text-indigo rounded-pill px-3 py-2 fw-bold small">Data Terpusat</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm h-100 bg-white">
            <div class="card-header border-0 bg-transparent p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-emerald bg-opacity-10 p-2 rounded-4 me-3">
                        <i class="bi bi-pie-chart-fill text-emerald fs-4"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-extrabold text-dark">Rekap Bulanan</h5>
                        <small class="text-muted fw-medium">{{ now()->translatedFormat('F Y') }}</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="d-flex flex-column gap-2">
                    <div class="p-2 rounded-4 bg-emerald bg-opacity-10 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-emerald small"></i>
                            <div class="fw-bold text-dark" style="font-size: 11px;">Hadir</div>
                        </div>
                        <h5 class="fw-extrabold text-emerald mb-0">{{ $monthly_hadir }}</h5>
                    </div>
                    <div class="p-2 rounded-4 bg-amber bg-opacity-10 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-clock-fill text-amber small"></i>
                            <div class="fw-bold text-dark" style="font-size: 11px;">Telat</div>
                        </div>
                        <h5 class="fw-extrabold text-amber mb-0">{{ $monthly_terlambat }}</h5>
                    </div>
                    <div class="p-2 rounded-4 bg-primary bg-opacity-10 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-book-fill text-primary small"></i>
                            <div class="fw-bold text-dark" style="font-size: 11px;">Mapel</div>
                        </div>
                        <h5 class="fw-extrabold text-primary mb-0">{{ $total_mapel }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>

{{-- Quick Actions --}}
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm bg-white">
            <div class="card-header border-0 bg-transparent p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-indigo bg-opacity-10 p-2 rounded-4 me-3">
                        <i class="bi bi-lightning-charge-fill text-indigo fs-4"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-extrabold text-dark">Aksi Cepat</h5>
                        <small class="text-muted fw-medium">Akses menu utama dengan cepat</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="row g-3">
                    @php
                        $actions = [
                            ['Data Guru', 'bi-person-badge-fill', 'primary', route('guru.index'), 'Kelola data guru'],
                            ['Data Siswa', 'bi-mortarboard-fill', 'indigo', route('siswa.index'), 'Kelola data siswa'],
                            ['Data Kelas', 'bi-layers-fill', 'emerald', route('rombongan-belajar.index'), 'Kelola rombongan belajar'],
                            ['Tahun Ajar', 'bi-calendar3-range-fill', 'amber', route('tahun_ajar.index'), 'Atur tahun ajaran'],
                            ['Mata Pelajaran', 'bi-book-fill', 'primary', route('mata-pelajaran.index'), 'Kelola mapel'],
                            ['Jadwal', 'bi-calendar-week-fill', 'indigo', route('jadwal-pelajaran.index'), 'Kelola jadwal'],
                        ];
                    @endphp
                    @foreach($actions as $act)
                    <div class="col-xl-3 col-md-4 col-6">
                        <a href="{{ $act[3] }}" class="quick-action-card d-flex align-items-center gap-3 p-3 rounded-4 text-decoration-none border hover-up-premium" style="transition: all 0.3s ease;">
                            <div class="icon-box-sm bg-{{ $act[2] }}-soft text-{{ $act[2] }}">
                                <i class="bi {{ $act[1] }}"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark small">{{ $act[0] }}</div>
                                <div class="text-muted" style="font-size: 10px;">{{ $act[4] }}</div>
                            </div>
                        </a>
                    </div>
                    @endforeach
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
    
    .hover-up-premium:hover { transform: translateY(-6px); box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.08) !important; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
    .icon-box-premium { width: 44px; height: 44px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; }
    .icon-box-sm { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
    .card-footer-mini { height: 4px; width: 100%; border-radius: 0 0 4px 4px; opacity: 0.3; }
    .rank-badge { width: 32px; height: 32px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px; flex-shrink: 0; }

    .bg-info { background-color: #06b6d4 !important; }
    .bg-info-soft { background-color: #ecfeff; }
    .text-info { color: #06b6d4 !important; }
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
    .bg-primary-soft { background-color: #eff6ff; }

    .avatar-circle-sm { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
    .chart-bar-wrapper:hover .chart-bar { filter: brightness(1.15); transform: scaleY(1.05); transform-origin: bottom; }

    .quick-action-card:hover { background: #f8fafc; border-color: transparent !important; }

    .pulse { animation: pulseSmall 2s infinite; }
    @keyframes pulseSmall { 0% { opacity: 1; } 50% { opacity: 0.3; } 100% { opacity: 1; } }
</style>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    /**
     * Inisialisasi Grafik Radar untuk Performa Global (Admin)
     * Menggunakan format angka yang aman untuk JavaScript (titik sebagai desimal)
     */
    function inisialisasiGrafikRadarAdmin() {
        const elemenCanvas = document.getElementById('radarChartAdmin');
        if (!elemenCanvas) return;

        // Pastikan Chart.js sudah dimuat
        if (typeof Chart === 'undefined') {
            console.error('Chart.js tidak ditemukan. Pastikan koneksi internet stabil.');
            return;
        }

        const labelsRadar = [
            @foreach($radar_data as $rd)
            '{{ addslashes($rd->label) }}',
            @endforeach
        ];

        const dataRadar = [
            @foreach($radar_data as $rd)
            {{ number_format((float)$rd->value, 2, '.', '') }},
            @endforeach
        ];

        new Chart(elemenCanvas, {
            type: 'radar',
            data: {
                labels: labelsRadar,
                datasets: [{
                    label: 'Rata-rata Global',
                    data: dataRadar,
                    fill: true,
                    backgroundColor: 'rgba(99, 102, 241, 0.2)',
                    borderColor: 'rgb(99, 102, 241)',
                    pointBackgroundColor: 'rgb(99, 102, 241)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgb(99, 102, 241)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                elements: { line: { borderWidth: 3 } },
                scales: {
                    r: {
                        angleLines: { display: true },
                        suggestedMin: 0,
                        suggestedMax: 5,
                        ticks: { stepSize: 1, display: false }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        inisialisasiGrafikRadarAdmin();
    });
</script>
@endpush
@endsection
