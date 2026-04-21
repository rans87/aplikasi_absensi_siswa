@extends('layouts.app')

@section('title', 'Guru Dashboard')

@section('content')
<div class="row g-4 mb-5">
    {{-- Hero Section for Guru --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-premium overflow-hidden fade-in hero-guru h-100">
            <div class="card-body p-0 position-relative h-100">
                <div class="hero-pattern"></div>
                <div class="p-4 p-md-5 position-relative z-1">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="bg-white bg-opacity-20 p-2 rounded-4 backdrop-blur shadow-sm border border-white border-opacity-25">
                            <i class="bi bi-person-workspace text-white fs-4"></i>
                        </div>
                        <span class="badge bg-white bg-opacity-10 text-white px-3 py-2 rounded-pill ls-1 fw-bold" style="font-size: 10px; border: 1px solid rgba(255,255,255,0.2);">
                            PORTAL GURU TERVERIFIKASI
                        </span>
                    </div>
                    <h1 class="fw-extrabold text-white mb-3 display-5 ls-tight">Halo, Pak/Bu <span class="text-info">{{ $guru->nama }}</span>!</h1>
                    
                    @if($guru->kelasWali)
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <span class="badge bg-warning bg-opacity-25 text-warning px-3 py-2 border border-warning border-opacity-25 rounded-pill fw-bold" style="font-size: 11px;">
                            <i class="bi bi-person-check-fill me-2"></i>WALI KELAS: {{ $guru->kelasWali->nama_kelas }}
                        </span>
                    </div>
                    @endif

                    <p class="text-white text-opacity-75 mb-4 fs-6 fw-medium pe-lg-5">Optimalkan manajemen kelas Anda. Gunakan fitur pindaian QR untuk presensi cepat dan pantau jadwal mengajar hari ini.</p>
                    
                    <div class="d-flex flex-wrap gap-3 mt-auto">
                        <a href="{{ route('guru.show', $guru->id) }}" class="btn btn-info rounded-pill px-4 fw-bold shadow-sm border border-white border-opacity-20 text-white">
                            <i class="bi bi-person-bounding-box me-2"></i>Lihat Profil Lengkap
                        </a>
                        <div class="glass-info px-4 py-3 rounded-pill border border-white border-opacity-10 d-flex align-items-center">
                            <i class="bi bi-calendar-event text-white opacity-50 me-2"></i>
                            <span class="text-white small fw-bold ls-1 uppercase">{{ now()->translatedFormat('d F Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Live Scanner Section --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-premium bg-dark overflow-hidden h-100" style="border-radius: 40px;">
            <div class="card-header bg-transparent border-0 p-4 text-center">
                <h6 class="text-white fw-bold mb-0 lh-1"><i class="bi bi-camera-video me-2 text-rose"></i>QUICK SCANNER</h6>
            </div>
            <div class="card-body p-0 position-relative">
                <div id="reader" style="width: 100%; background: #000;"></div>
                <div id="scanner-status" class="position-absolute top-50 start-50 translate-middle text-center text-white-50">
                    <i class="bi bi-qr-code-scan display-4 d-block mb-3 opacity-25"></i>
                    <p class="small fw-bold">MENUNGGU KAMERA...</p>
                </div>
            </div>
            <div class="card-footer bg-dark border-0 p-3 text-center">
                <a href="{{ route('absensi.scan') }}" class="btn btn-primary btn-sm rounded-pill w-100 fw-bold">Buka Scanner Penuh</a>
            </div>
            <form id="scan-form" action="{{ route('absensi.prosesScan') }}" method="POST" class="d-none">
                @csrf
                <input type="hidden" name="qr_code" id="qr_input">
            </form>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    {{-- Stats Row --}}
    @php
        $stats = [
            ['Hadir Hari Ini', $total_hadir_today, 'bi-person-check-fill', 'primary', 'Siswa'],
            ['Terlambat', $total_terlambat_today, 'bi-clock-fill', 'amber', 'Siswa'],
            ['Izin & Sakit', ($total_izin_today + $total_sakit_today), 'bi-bandaid-fill', 'info', 'Total'],
            ['Alfa', $total_alfa_today, 'bi-person-x-fill', 'rose', 'Tanpa Ket.'],
        ];
    @endphp

    @foreach($stats as $s)
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 h-100 shadow-sm hover-up-premium-mini overflow-hidden bg-white">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-circle bg-{{ $s[3] }}-soft text-{{ $s[3] }} me-3">
                        <i class="bi {{ $s[2] }}"></i>
                    </div>
                    <span class="text-muted small fw-bold text-uppercase ls-1">{{ $s[0] }}</span>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    <h2 class="fw-extrabold mb-0 ls-tight">{{ $s[1] }}</h2>
                    <div class="text-{{ $s[3] }} small fw-bold">{{ $s[4] }}</div>
                </div>
            </div>
            <div class="progress-mini bg-{{ $s[3] }}"></div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4">
    {{-- Schedule & Classes --}}
    <div class="col-lg-8">
        <div class="row g-4">
            {{-- Today's Schedule for Guru --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm bg-white overflow-hidden">
                    <div class="card-header border-0 bg-transparent p-4 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-calendar3 text-primary fs-4"></i>
                            <h5 class="mb-0 fw-extrabold text-dark">Jadwal Mengajar Hari Ini</h5>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light-subtle">
                                    <tr>
                                        <th class="ps-4 small fw-bold text-muted border-0">JAM</th>
                                        <th class="small fw-bold text-muted border-0">MATA PELAJARAN</th>
                                        <th class="small fw-bold text-muted border-0">KELAS</th>
                                        <th class="pe-4 text-end small fw-bold text-muted border-0">SESI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($jadwal_hari_ini as $jadwal)
                                    @php
                                        $isActive = now()->format('H:i') >= $jadwal->jam_mulai && now()->format('H:i') <= $jadwal->jam_selesai;
                                        $isDone = now()->format('H:i') > $jadwal->jam_selesai;
                                    @endphp
                                    <tr class="schedule-row {{ $isActive ? 'bg-primary-soft' : '' }}" onclick="window.location='{{ route('absensi-mapel.session', $jadwal->id) }}'" style="cursor: pointer;">
                                        <td class="ps-4 py-3">
                                            <div class="fw-bold text-primary small">{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</div>
                                            @if($isActive) <span class="badge bg-primary text-white p-1 rounded-circle pulse-blue" style="width: 8px; height: 8px;"></span> @endif
                                        </td>
                                        <td>
                                            <div class="fw-extrabold text-dark small">{{ $jadwal->mataPelajaran->nama_mapel }}</div>
                                            <div class="text-muted" style="font-size: 10px;">{{ $isActive ? 'Sedang Berlangsung' : ($isDone ? 'Selesai' : 'Mendatang') }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary-soft text-primary border-0 rounded-pill px-3 fw-bold">{{ $jadwal->rombonganBelajar->nama_kelas }}</span>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <a href="{{ route('absensi-mapel.session', $jadwal->id) }}" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold" style="font-size: 10px;">
                                                <i class="bi bi-person-check me-1 text-primary"></i>Absen
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted small fw-bold">Tidak ada jadwal mengajar hari ini</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Attendance Weekly Trend --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm bg-white overflow-hidden rounded-4">
                    <div class="card-header border-0 bg-transparent p-4 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-indigo-soft p-2 rounded-4 text-indigo">
                                <i class="bi bi-bar-chart-line-fill fs-4"></i>
                            </div>
                            <h5 class="mb-0 fw-extrabold text-dark">Tren Kehadiran Mingguan</h5>
                        </div>
                        <span class="badge bg-light text-muted fw-bold rounded-pill px-3">7 Hari Terakhir</span>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="d-flex justify-content-between align-items-end gap-2" style="height: 150px;">
                            @php 
                                $counts = array_column($weekly_attendance, 'count');
                                $maxW = count($counts) > 0 ? max(max($counts), 1) : 1;
                            @endphp
                            @foreach($weekly_attendance as $w)
                            <div class="text-center flex-fill group-hover">
                                <div class="chart-bar-container position-relative" style="height: 100px;">
                                    <div class="chart-count small fw-bold text-muted mb-1 opacity-0 transition-all">{{ $w['count'] }}</div>
                                    <div class="chart-bar mx-auto {{ $w['is_today'] ? 'bg-primary shadow-glow-blue' : 'bg-light-blue' }}" 
                                         style="height: {{ max(($w['count'] / $maxW) * 80, 8) }}px; width: 35px; border-radius: 12px; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);"
                                         data-bs-toggle="tooltip" title="{{ $w['count'] }} Siswa"></div>
                                </div>
                                <div class="small fw-bold mt-3 {{ $w['is_today'] ? 'text-primary' : 'text-muted' }}">{{ $w['day'] }}</div>
                                <div class="text-muted" style="font-size: 10px; font-weight: 600;">{{ $w['date'] }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Activity & Class Reports --}}
    <div class="col-lg-4">
        <div class="row g-4">
            {{-- Class Reports --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm bg-white h-100 rounded-4">
                    <div class="card-header border-0 bg-transparent p-4 d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 fw-extrabold text-dark ls-tight">KONTROL KELAS & ABSENSI</h5>
                        <i class="bi bi-grid-fill text-muted"></i>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr class="bg-light">
                                        <th class="ps-4 small fw-bold text-muted border-0">KELAS</th>
                                        <th class="pe-4 text-end small fw-bold text-muted border-0">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($classReports as $report)
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="fw-bold text-dark small">{{ $report->nama_kelas }}</div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-emerald-soft text-emerald px-2 py-1" style="font-size: 9px;">{{ $report->hadir_count }} Hadir</span>
                                                <div class="text-muted" style="font-size: 10px;">{{ $report->jurusan }}</div>
                                            </div>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <a href="{{ route('absensi.manual-input', $report->id) }}" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold" style="font-size: 11px;">
                                                <i class="bi bi-pencil-square me-1"></i> Absen Manual
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Radar Chart: Class Performance --}}
            @if($radar_data && $radar_data->count() > 0)
            <div class="col-12">
                <div class="card border-0 shadow-sm bg-white rounded-4 overflow-hidden">
                    <div class="card-header border-0 bg-transparent p-4 d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 fw-extrabold text-dark ls-tight">KUALITAS KARAKTER KELAS</h5>
                        <i class="bi bi-bullseye text-primary"></i>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <div class="bg-primary-soft p-3 rounded-4 mb-3">
                            <p class="small text-dark mb-0 fw-bold">Rata-rata Penilaian Kelas: {{ $guru->kelasWali->nama_kelas }}</p>
                        </div>
                        <div style="max-height: 250px;">
                            <canvas id="radarChartGuru"></canvas>
                        </div>
                        <div class="mt-3">
                            <p class="xs-small text-muted italic mb-0">* Grafik ini menunjukkan rata-rata skor evaluasi sikap seluruh siswa di kelas perwalian Anda.</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif


        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    /**
     * Inisialisasi Grafik Radar untuk Performa Kelas (Guru)
     */
    function inisialisasiGrafikRadarGuru() {
        const ctxGuru = document.getElementById('radarChartGuru');
        if (!ctxGuru) return;

        const dataRadarGuru = {
            labels: [
                @if($radar_data)
                    @foreach($radar_data as $rd)
                    '{{ $rd->label }}',
                    @endforeach
                @endif
            ],
            datasets: [{
                label: 'Rata-rata Kelas',
                data: [
                    @if($radar_data)
                        @foreach($radar_data as $rd)
                        {{ number_format($rd->value, 2) }},
                        @endforeach
                    @endif
                ],
                fill: true,
                backgroundColor: 'rgba(79, 70, 229, 0.2)',
                borderColor: 'rgb(79, 70, 229)',
                pointBackgroundColor: 'rgb(79, 70, 229)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgb(79, 70, 229)'
            }]
        };

        new Chart(ctxGuru, {
            type: 'radar',
            data: dataRadarGuru,
            options: {
                elements: {
                    line: { borderWidth: 3 }
                },
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
        // Scanner
        const html5QrCode = new Html5Qrcode("reader");
        const statusEl = document.getElementById('scanner-status');
        const qrInput = document.getElementById('qr_input');
        const scanForm = document.getElementById('scan-form');

        const qrConfig = { fps: 10, qrbox: { width: 150, height: 150 } };

        function onScanSuccess(decodedText, decodedResult) {
            html5QrCode.stop().then(() => {
                statusEl.innerHTML = `
                    <div class="spinner-border text-primary mb-3" role="status"></div>
                    <p class="small fw-bold text-white">MEMPROSES DATA...</p>
                `;
                qrInput.value = decodedText;
                scanForm.submit();
            });
        }

        Html5Qrcode.getCameras().then(devices => {
            if (devices && devices.length) {
                html5QrCode.start(
                    { facingMode: "environment" }, 
                    qrConfig, 
                    onScanSuccess
                ).then(() => {
                    statusEl.classList.add('d-none');
                });
            } else {
                statusEl.innerHTML = '<p class="text-danger small fw-bold">KAMERA TIDAK DITEMUKAN</p>';
            }
        }).catch(err => {
            statusEl.innerHTML = `<p class="text-danger small fw-bold">ERROR: ${err}</p>`;
        });

        // Grafik Radar
        inisialisasiGrafikRadarGuru();
    });
</script>
@endpush

<style>
    .ls-tight { letter-spacing: -1.2px; }
    .ls-1 { letter-spacing: 0.5px; }
    
    .hero-guru { background: linear-gradient(135deg, #4f46e5, #6366f1); border-radius: 40px !important; }
    .hero-pattern { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 40%); }
    .backdrop-blur { backdrop-filter: blur(8px); }
    
    #reader { border-radius: 0; overflow: hidden; height: auto !important; min-height: 200px; }
    #reader__scan_region { background: #000; }
    #reader video { object-fit: cover !important; height: auto !important; min-height: 200px; width: 100% !important; }

    @media (max-width: 576px) {
        .chart-bar { width: 25px !important; }
        .hero-guru { border-radius: 24px !important; }
        .hero-guru h1 { font-size: 1.75rem !important; }
    }

    .hover-up-premium-mini:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.05) !important; transition: all 0.3s ease; }
    
    .icon-circle { width: 44px; height: 44px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
    .bg-primary-soft { background-color: #eff6ff; }
    .bg-amber-soft { background-color: #fffbeb; }
    .text-amber { color: #f59e0b; }
    .bg-emerald-soft { background-color: #ecfdf5; }
    .text-emerald { color: #10b981; }
    .bg-rose-soft { background-color: #fff1f2; }
    .text-rose { color: #e11d48; }
    .bg-info-soft { background-color: #e0f2fe; }
    .text-info { color: #0ea5e9; }

    .progress-mini { height: 4px; border-radius: 0 0 10px 10px; opacity: 0.4; margin-top: 15px; }
    .bg-light-subtle { background-color: #f8fafc; }
    .bg-light-blue { background-color: #f0f9ff; }
    .bg-indigo-soft { background-color: #eef2ff; }
    .text-indigo { color: #4f46e5; }
    .shadow-glow-blue { box-shadow: 0 0 15px rgba(37, 99, 235, 0.4); }
    
    .group-hover:hover .chart-bar { transform: scaleY(1.1) !important; filter: brightness(1.1); }
    .group-hover:hover .chart-count { opacity: 1 !important; transform: translateY(-5px); }
    .transition-all { transition: all 0.3s ease; }
    .schedule-row:hover { background-color: #f1f5f9 !important; transition: all 0.2s ease; }
    .pulse-blue { animation: pulseBlue 1.5s infinite; }
    @keyframes pulseBlue { 0% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.4); } 70% { box-shadow: 0 0 0 6px rgba(37, 99, 235, 0); } 100% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0); } }
</style>
@endsection
