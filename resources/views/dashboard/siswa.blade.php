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
                             <i class="bi bi-layers me-1 text-info"></i>{{ $kelas_info->rombonganBelajar->nama_kelas ?? 'Tanpa Kelas' }}
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

    {{-- Stats & Schedule --}}
    <div class="col-lg-7">
        <div class="row g-4">
            {{-- Attendance Overview with ALL statuses --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm bg-white overflow-hidden p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="fw-extrabold text-dark mb-0 ls-1"><i class="bi bi-pie-chart-fill me-2 text-primary"></i>RINGKASAN KEHADIRAN</h6>
                        <span class="badge bg-primary-soft text-primary rounded-pill">Semester Aktif</span>
                    </div>
                    <div class="row text-center g-2 row-cols-2 row-cols-sm-3 row-cols-md-5">
                        <div class="col">
                            <div class="p-2 rounded-4 bg-emerald-soft h-100 d-flex flex-column justify-content-center">
                                <h3 class="fw-extrabold text-emerald mb-0">{{ $total_hadir }}</h3>
                                <div class="text-muted xs-small fw-bold">HADIR</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="p-2 rounded-4 bg-amber-soft h-100 d-flex flex-column justify-content-center">
                                <h3 class="fw-extrabold text-amber mb-0">{{ $total_terlambat }}</h3>
                                <div class="text-muted xs-small fw-bold">TELAT</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="p-2 rounded-4 bg-info-soft h-100 d-flex flex-column justify-content-center">
                                <h3 class="fw-extrabold text-info mb-0">{{ $total_izin ?? 0 }}</h3>
                                <div class="text-muted xs-small fw-bold">IZIN</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="p-2 rounded-4 h-100 d-flex flex-column justify-content-center" style="background: #fef3c7;">
                                <h3 class="fw-extrabold mb-0" style="color: #92400e;">{{ $total_sakit ?? 0 }}</h3>
                                <div class="text-muted xs-small fw-bold">SAKIT</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="p-2 rounded-4 bg-rose-soft h-100 d-flex flex-column justify-content-center">
                                <h3 class="fw-extrabold text-rose mb-0">{{ $total_alfa ?? 0 }}</h3>
                                <div class="text-muted xs-small fw-bold">ALFA</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Weekly Attendance Chart --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm bg-white overflow-hidden">
                    <div class="card-header border-0 bg-transparent p-4 d-flex align-items-center gap-3">
                        <div class="bg-indigo-soft p-2 rounded-4 text-indigo">
                            <i class="bi bi-bar-chart-line-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-extrabold text-dark">Kehadiran 7 Hari Terakhir</h6>
                            <small class="text-muted fw-medium" style="font-size: 10px;">Status kehadiran harian</small>
                        </div>
                    </div>
                    <div class="card-body px-4 pb-4 pt-0">
                        <div class="d-flex justify-content-between align-items-end gap-2">
                            @foreach($weeklyChart as $w)
                            <div class="text-center flex-fill">
                                @php
                                    $statusMap = [
                                        'hadir' => ['bg' => 'bg-emerald', 'icon' => 'bi-check-circle-fill', 'color' => 'text-emerald'],
                                        'terlambat' => ['bg' => 'bg-amber', 'icon' => 'bi-clock-fill', 'color' => 'text-amber'],
                                        'izin' => ['bg' => 'bg-info', 'icon' => 'bi-envelope-fill', 'color' => 'text-info'],
                                        'sakit' => ['bg' => 'bg-warning', 'icon' => 'bi-bandaid-fill', 'color' => 'text-warning'],
                                        'alfa' => ['bg' => 'bg-rose', 'icon' => 'bi-x-circle-fill', 'color' => 'text-rose'],
                                    ];
                                    $info = $statusMap[$w['status']] ?? null;
                                @endphp
                                <div class="weekly-dot mx-auto mb-2 rounded-circle d-flex align-items-center justify-content-center {{ $w['is_today'] ? 'border border-2 border-primary' : '' }}" 
                                     style="width: 40px; height: 40px; {{ $info ? '' : 'background: #f1f5f9;' }}" 
                                     class="{{ $info ? $info['bg'] . ' bg-opacity-15' : '' }}">
                                    @if($info)
                                        <i class="bi {{ $info['icon'] }} {{ $info['color'] }}" style="font-size: 16px;"></i>
                                    @else
                                        <i class="bi bi-dash text-muted" style="font-size: 14px;"></i>
                                    @endif
                                </div>
                                <div class="small fw-bold {{ $w['is_today'] ? 'text-primary' : 'text-muted' }}" style="font-size: 11px;">{{ $w['day'] }}</div>
                                <div class="text-muted" style="font-size: 9px;">{{ $w['date'] }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Integrity System Overview --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm bg-white overflow-hidden hover-up-premium-mini">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-circle bg-emerald-soft text-emerald">
                                    <i class="bi bi-shield-shaded"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-extrabold text-dark ls-1">DOMPET INTEGRITAS</h6>
                                    <small class="text-muted fw-bold" style="font-size: 10px;">{{ $siswa->integrity_level['name'] }}</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <h1 class="display-5 fw-extrabold text-primary mb-0 ls-extratight">{{ $siswa->integrity_balance }}</h1>
                                <div class="text-muted xs-small fw-bold uppercase">Points</div>
                            </div>
                        </div>
                        
                        <div class="progress rounded-pill mb-2" style="height: 8px; background: #f1f5f9;">
                            <div class="progress-bar progress-bar-animated" role="progressbar" 
                                 style="width: {{ $siswa->integrity_level['progress'] }}%; background: {{ $siswa->integrity_level['color'] }};" 
                                 aria-valuenow="{{ $siswa->integrity_level['progress'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                             <a href="{{ route('wallet.index') }}" class="btn btn-sm btn-emerald rounded-pill px-3 fw-bold" style="font-size: 10px;">
                                <i class="bi bi-wallet2 me-1"></i> Detail Saldo
                            </a>
                            <div class="xs-small text-muted fw-bold italic">
                                Skor minimal untuk level berikutnya: 100 Poin
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- New: Latest Assessment Summary with Radar Chart --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm bg-white overflow-hidden p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="fw-extrabold text-dark mb-0 ls-1"><i class="bi bi-shield-check-fill me-2 text-primary"></i>EVALUASI SIKAP (RADAR CHART)</h6>
                        <span class="badge bg-light text-dark rounded-pill border">{{ $latestAssessment->period ?? '-' }}</span>
                    </div>

                    @if($radar_data->count() > 0)
                    <div class="row align-items-center">
                        <div class="col-md-7 mb-3 mb-md-0">
                            <div style="max-height: 300px;">
                                <canvas id="radarChartSiswa"></canvas>
                            </div>
                        </div>
                        <div class="col-md-5 text-center border-start">
                            <div class="py-3">
                                <h1 class="display-4 fw-black text-dark mb-0 ls-extratight">
                                    {{ number_format($latestAssessment->details->avg('score'), 1) }}
                                </h1>
                                <p class="text-muted fw-bold small mb-3">SKOR RATA-RATA</p>
                                <a href="{{ route('assessments.report', $siswa->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold">
                                    <i class="bi bi-file-earmark-bar-graph me-1"></i> LIHAT RAPOR LENGKAP
                                </a>
                            </div>
                            <div class="mt-3 text-start bg-light p-3 rounded-4">
                                <h6 class="fw-bold small text-dark mb-2">Info Grafik:</h6>
                                <p class="xs-small text-muted mb-0">Grafik ini menunjukkan performa karakter Anda berdasarkan penilaian terbaru dari Bapak/Ibu Guru.</p>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="bi bi-journal-x display-4 text-light"></i>
                        <p class="text-muted mt-2 fw-bold">Belum ada data evaluasi sikap bulan ini.</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Today's Schedule with Attendance Status per Mapel --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm bg-white overflow-hidden">
                    <div class="card-header border-0 bg-transparent p-4 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary-soft p-2 rounded-4 text-primary">
                                <i class="bi bi-calendar3 fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-extrabold text-dark">Jadwal Belajar Hari Ini</h6>
                                <small class="text-muted fw-medium" style="font-size: 10px;">{{ now()->translatedFormat('l, d F Y') }}</small>
                            </div>
                        </div>
                        @if($jadwal_hari_ini->count() > 0)
                        <span class="badge bg-primary-soft text-primary rounded-pill px-3 fw-bold">{{ $jadwal_hari_ini->count() }} Mapel</span>
                        @endif
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light-subtle">
                                    <tr>
                                        <th class="ps-4 small fw-bold text-muted border-0">WAKTU</th>
                                        <th class="small fw-bold text-muted border-0">PELAJARAN</th>
                                        <th class="small fw-bold text-muted border-0">GURU</th>
                                        <th class="pe-4 text-end small fw-bold text-muted border-0">STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($jadwal_hari_ini as $jadwal)
                                    @php
                                        $isActive = now()->format('H:i') >= $jadwal->jam_mulai && now()->format('H:i') <= $jadwal->jam_selesai;
                                        $isDone = now()->format('H:i') > $jadwal->jam_selesai;
                                        $absenMapel = $absensiMapelHariIni[$jadwal->id] ?? null;
                                    @endphp
                                    <tr class="{{ $isActive ? 'bg-primary bg-opacity-5' : '' }}">
                                        <td class="ps-4 py-3">
                                            <div class="fw-bold text-primary small">{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</div>
                                            @if($isActive) 
                                                <span class="badge bg-primary text-white rounded-pill px-2" style="font-size: 8px;">BERLANGSUNG</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark small">{{ $jadwal->mataPelajaran->nama_mapel }}</div>
                                            @if($jadwal->mataPelajaran->kode_mapel)
                                            <div class="text-muted" style="font-size: 10px;">{{ $jadwal->mataPelajaran->kode_mapel }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-muted small fw-medium">{{ Str::limit($jadwal->guru->nama ?? '-', 20) }}</div>
                                        </td>
                                        <td class="pe-4 text-end">
                                            @if($absenMapel)
                                                @php
                                                    $statusBadge = match($absenMapel->status) {
                                                        'hadir' => 'bg-emerald text-white',
                                                        'sakit' => 'bg-warning text-dark',
                                                        'izin' => 'bg-info text-white',
                                                        'dispen' => 'bg-indigo text-white',
                                                        'alfa' => 'bg-rose text-white',
                                                        default => 'bg-secondary text-white',
                                                    };
                                                @endphp
                                                <span class="badge {{ $statusBadge }} rounded-pill px-3 fw-bold" style="font-size: 10px;">
                                                    {{ strtoupper($absenMapel->status) }}
                                                </span>
                                            @elseif($isDone)
                                                <span class="badge bg-light text-muted rounded-pill px-3 fw-bold" style="font-size: 10px;">SELESAI</span>
                                            @else
                                                <span class="badge bg-light text-muted rounded-pill px-3 fw-bold" style="font-size: 10px;">MENUNGGU</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted small fw-bold">
                                            <i class="bi bi-calendar-x d-block fs-1 mb-2 opacity-25"></i>
                                            Tidak ada jadwal belajar hari ini
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Point Mutation Table --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm bg-white overflow-hidden">
                    <div class="card-header border-0 bg-transparent p-3 d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-extrabold text-dark ls-tight">Mutasi Poin Terakhir</h6>
                        <a href="{{ route('wallet.index') }}" class="xs-small fw-bold text-primary text-decoration-none">LIHAT SEMUA</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <tbody>
                                    @forelse($siswa->pointLedgers()->latest()->take(5)->get() as $ledger)
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="badge bg-{{ $ledger->type == 'CREDIT' ? 'emerald' : 'rose' }} bg-opacity-10 text-{{ $ledger->type == 'CREDIT' ? 'emerald' : 'rose' }} rounded-3 px-2 py-1 small fw-bold" style="font-size: 8px;">
                                                    {{ $ledger->type == 'CREDIT' ? 'PENAMBAHAN' : 'PENGURANGAN' }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark small" style="font-size: 11px;">{{ $ledger->description }}</div>
                                                    <div class="text-muted" style="font-size: 9px;">{{ $ledger->created_at->diffForHumans() }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <span class="fw-extrabold text-{{ $ledger->type == 'CREDIT' ? 'emerald' : 'rose' }}">
                                                {{ $ledger->type == 'CREDIT' ? '+' : '-' }}{{ $ledger->amount }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="2" class="text-center py-4 text-muted small fw-bold">Belum ada mutasi poin.</td>
                                    </tr>
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

{{-- QR Magnifier Modal --}}
<div class="modal fade" id="qrModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-5 overflow-hidden">
            <div class="modal-body p-4 p-md-5 text-center bg-deep-blue">
                <div class="mb-4">
                    <h5 class="text-white fw-extrabold ls-1">KODE QR ABSENSI</h5>
                    <p class="text-white-50 small mb-0">{{ $siswa->nama }}</p>
                </div>
                <div class="bg-white p-3 p-md-4 rounded-5 d-inline-block shadow-glow-primary overflow-hidden" style="max-width: 100%;">
                    <div class="qr-magnify-wrapper">
                        {!! QrCode::size(300)->eye('circle')->color(15, 23, 42)->generate($qr_string) !!}
                    </div>
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

    .student-card-main { background: linear-gradient(135deg, #06b6d4, #3b82f6); border-radius: 40px !important; }
    .card-glass-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle at 10% 10%, rgba(255,255,255,0.1) 0%, transparent 50%); }
    
    .qr-container-premium { position: relative; border: 4px solid rgba(255,255,255,0.1); transition: all 0.3s ease; }
    .qr-hover-overlay { position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(6, 182, 212, 0.4); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; opacity: 0; transition: all 0.3s ease; }
    .qr-wrapper:hover .qr-hover-overlay { opacity: 1; }
    .qr-wrapper:hover .qr-container-premium { transform: scale(1.02); }

    .qr-scan-line { position: absolute; top: 0; left: 0; width: 100%; height: 2px; background: #22d3ee; box-shadow: 0 0 15px #22d3ee; animation: scanLine 3s infinite linear; }
    @keyframes scanLine { 
        0% { top: 10%; } 
        50% { top: 90%; } 
        100% { top: 10%; } 
    }

    .glass-badge { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); font-size: 10px; font-weight: 700; color: white; letter-spacing: 0.5px; }
    
    .shadow-glow-primary { box-shadow: 0 0 30px rgba(6, 182, 212, 0.4); }
    .icon-circle { width: 44px; height: 44px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
    .bg-rose-soft { background-color: #fff1f2; }
    .bg-emerald-soft { background-color: #ecfdf5; }
    .bg-amber-soft { background-color: #fffbeb; }
    .bg-info-soft { background-color: #e0f2fe; }
    .bg-primary-soft { background-color: #eff6ff; }
    .bg-indigo-soft { background-color: #eef2ff; }
    .text-indigo { color: #4f46e5; }
    .bg-indigo { background-color: #4f46e5; }
    .bg-emerald { background-color: #10b981; }
    .bg-rose { background-color: #e11d48; }
    .bg-amber { background-color: #f59e0b; }
    .text-emerald { color: #10b981; }
    .text-rose { color: #e11d48; }
    .text-amber { color: #f59e0b; }
    .progress-mini { height: 4px; border-radius: 0 0 10px 10px; opacity: 0.4; margin-top: 15px; }
    .bg-light-subtle { background-color: #f8fafc; }
    
    .hover-up-premium-mini:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.05) !important; transition: all 0.3s ease; }

    .qr-magnify-wrapper svg { width: 100% !important; height: auto !important; max-width: 300px; }
    
    .weekly-dot { transition: all 0.3s ease; }
    .weekly-dot:hover { transform: scale(1.15); }

    @media (max-width: 576px) {
        .student-card-main { border-radius: 24px !important; }
        .display-5 { font-size: 2.2rem; }
        .p-5 { padding: 2rem !important; }
        .card-body { padding: 1.25rem !important; }
        .qr-magnify-wrapper svg { max-width: 200px; }
        .weekly-dot { width: 32px !important; height: 32px !important; }
    }
</style>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    /**
     * Inisialisasi Grafik Radar untuk Performa Siswa
     */
    function inisialisasiGrafikRadarSiswa() {
        const ctxSiswa = document.getElementById('radarChartSiswa');
        if (!ctxSiswa) return;

        const dataRadar = {
            labels: [
                @foreach($radar_data as $rd)
                '{{ $rd->label }}',
                @endforeach
            ],
            datasets: [{
                label: 'Skor Karakter',
                data: [
                    @foreach($radar_data as $rd)
                    {{ $rd->value ?? 0 }},
                    @endforeach
                ],
                fill: true,
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                borderColor: 'rgb(59, 130, 246)',
                pointBackgroundColor: 'rgb(59, 130, 246)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgb(59, 130, 246)'
            }]
        };

        new Chart(ctxSiswa, {
            type: 'radar',
            data: dataRadar,
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

    // Jalankan inisialisasi saat dokumen siap
    document.addEventListener('DOMContentLoaded', function() {
        inisialisasiGrafikRadarSiswa();
    });
</script>
@endpush
@endsection
