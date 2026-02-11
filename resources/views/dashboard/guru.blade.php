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
                    <p class="text-white text-opacity-75 mb-4 fs-6 fw-medium pe-lg-5">Sistem presensi digital PresenceX siap digunakan. Pastikan kamera perangkat aktif untuk mulai melakukan pemindaian QR Code siswa.</p>
                    
                    <div class="d-flex flex-wrap gap-3 mt-auto">
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
                <h6 class="text-white fw-bold mb-0 lh-1"><i class="bi bi-camera-video me-2 text-rose"></i>LIVE SCANNER</h6>
            </div>
            <div class="card-body p-0 position-relative">
                <div id="reader" style="width: 100%; background: #000;"></div>
                <div id="scanner-status" class="position-absolute top-50 start-50 translate-middle text-center text-white-50">
                    <i class="bi bi-qr-code-scan display-4 d-block mb-3 opacity-25"></i>
                    <p class="small fw-bold">MENUNGGU KAMERA...</p>
                </div>
            </div>
            <form id="scan-form" action="{{ route('absensi.prosesScan') }}" method="POST" class="d-none">
                @csrf
                <input type="hidden" name="qr_code" id="qr_input">
            </form>
        </div>
    </div>

    {{-- Stats Row --}}
    @php
        $stats = [
            ['Presensi Hari Ini', $total_absensi, 'bi-calendar-check-fill', 'primary', 'Total Scan'],
            ['Pelanggaran', $total_pelanggaran, 'bi-exclamation-triangle-fill', 'rose', 'Poin Disiplin'],
            ['Prestasi Siswa', $total_prestasi, 'bi-award-fill', 'amber', 'Poin Prestasi'],
            ['Update Terakhir', now()->format('H:i'), 'bi-clock-history', 'info', 'WIB'],
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
    {{-- Attendance Report by Class --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100 bg-white">
            <div class="card-header border-0 bg-transparent p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0 fw-extrabold text-dark ls-tight">Laporan Kehadiran Per Kelas</h5>
                    <p class="text-muted small mb-0 mt-1">Rekapitulasi scan siswa per rombongan belajar</p>
                </div>
                <span class="badge bg-light text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-bold">{{ now()->format('d M Y') }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th class="ps-4 small fw-bold text-muted border-0">NAMA KELAS</th>
                                <th class="text-center small fw-bold text-muted border-0">HADIR</th>
                                <th class="pe-4 text-end small fw-bold text-muted border-0">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classReports as $report)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-dark fs-6">{{ $report->nama_kelas }}</div>
                                    <div class="text-muted" style="font-size: 11px;">{{ $report->jurusan }} {{ $report->tingkat ? '(T'.$report->tingkat.')' : '' }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-emerald-soft text-emerald rounded-pill px-3 py-2 fw-bold border border-emerald border-opacity-10">
                                        {{ $report->hadir_count }} Siswa
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    <a href="{{ route('absensi.index', ['tanggal' => date('Y-m-d'), 'search' => $report->nama_kelas]) }}" 
                                       class="btn btn-sm btn-light text-primary fw-bold rounded-pill px-3 border">
                                        Detail Class
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-5">
                                    <div class="opacity-25 mb-3"><i class="bi bi-folder-x fs-1"></i></div>
                                    <p class="text-muted fw-bold">Belum Ada Data Laporan</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Scan Activity --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100 bg-white">
            <div class="card-header border-0 bg-transparent p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0 fw-extrabold text-dark ls-tight">Aktivitas Live Scan</h5>
                    <p class="text-muted small mb-0 mt-1">Linimasa kehadiran terbaru hari ini</p>
                </div>
                <div class="bg-info bg-opacity-10 p-2 rounded-4">
                    <i class="bi bi-activity text-info fs-5"></i>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($recent_absensi as $absen)
                    <div class="list-group-item p-4 border-0 border-bottom hover-light transition-all">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-primary-soft text-primary rounded-4 me-3 d-flex align-items-center justify-content-center fw-extrabold">
                                {{ substr($absen->siswa->nama ?? 'S', 0, 1) }}
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-0 text-dark small">{{ Str::words($absen->siswa->nama ?? 'Siswa', 3) }}</h6>
                                <p class="mb-0 text-muted" style="font-size: 10px;">
                                    <i class="bi bi-clock me-1"></i> {{ $absen->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <span class="badge bg-emerald text-white rounded-pill px-3 py-1 fw-bold ls-1" style="font-size: 9px;">HADIR</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <div class="opacity-10 mb-3"><i class="bi bi-qr-code fs-1"></i></div>
                        <p class="text-muted small fw-bold">Menunggu Scan Pertama...</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @if(count($recent_absensi) > 0)
            <div class="card-footer bg-transparent p-4 border-0 text-center">
                <a href="{{ route('absensi.index') }}" class="btn btn-outline-primary btn-sm rounded-pill w-100 fw-bold py-2">
                    Buka Semua Data Presensi
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const html5QrCode = new Html5Qrcode("reader");
        const statusEl = document.getElementById('scanner-status');
        const qrInput = document.getElementById('qr_input');
        const scanForm = document.getElementById('scan-form');

        const qrConfig = { fps: 10, qrbox: { width: 250, height: 250 } };

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
                const cameraId = devices[0].id; // Gunakan kamera pertama
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
    });
</script>
@endpush

<style>
    .ls-tight { letter-spacing: -1.2px; }
    .ls-1 { letter-spacing: 0.5px; }
    
    .hero-guru { background: linear-gradient(135deg, #1e40af, #3b82f6); border-radius: 40px !important; }
    .hero-pattern { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 40%); }
    .backdrop-blur { backdrop-filter: blur(8px); }
    
    #reader { border-radius: 0; overflow: hidden; }
    #reader__scan_region { background: #000; }
    #reader video { object-fit: cover !important; }

    .hover-up-premium:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.2) !important; transition: all 0.3s ease; }
    .hover-up-premium-mini:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.05) !important; transition: all 0.3s ease; }
    
    .icon-circle { width: 44px; height: 44px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
    .bg-info-soft { background-color: #f0f9ff; }
    .text-info { color: #0ea5e9; }
    .bg-amber-soft { background-color: #fffbeb; }
    .text-amber { color: #f59e0b; }
    .progress-mini { height: 4px; border-radius: 0 0 10px 10px; opacity: 0.4; margin-top: 15px; }

    .avatar-sm { width: 38px; height: 38px; }
    .bg-primary-soft { background-color: #eff6ff; }
    .bg-emerald-soft { background-color: #ecfdf5; }
    .text-emerald { color: #10b981; }
    .bg-emerald { background-color: #10b981 !important; }

    .bg-light-subtle { background-color: #f8fafc; }
    .transition-all { transition: all 0.2s ease; }
</style>
@endsection
