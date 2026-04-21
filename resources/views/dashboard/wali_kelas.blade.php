@extends('layouts.app')

@section('title', 'Panel Wali Kelas')

@section('content')
<div class="row g-4 mb-5">
    {{-- Hero Section --}}
    <div class="col-12">
        <div class="card border-0 shadow-premium overflow-hidden fade-in hero-wali">
            <div class="card-body p-0 position-relative">
                <div class="hero-pattern"></div>
                <div class="p-4 p-md-5 position-relative z-1">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="bg-white bg-opacity-20 p-2 rounded-4 backdrop-blur shadow-sm border border-white border-opacity-25">
                            <i class="bi bi-shield-lock text-white fs-4"></i>
                        </div>
                        <span class="badge bg-white bg-opacity-10 text-white px-3 py-2 rounded-pill ls-1 fw-bold" style="font-size: 10px; border: 1px solid rgba(255,255,255,0.2);">
                            PANEL WALI KELAS RESMI
                        </span>
                    </div>
                    <h1 class="fw-extrabold text-white mb-2 display-5 ls-tight">Monitor Kelas <span class="text-warning">{{ $rombel->nama_kelas }}</span></h1>
                    <p class="text-white text-opacity-75 mb-0 fs-6 fw-medium">Pantau kehadiran siswa Anda secara detail dari jam pertama hingga jam terakhir.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    {{-- Class Stats --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 bg-white hover-up-premium-mini">
            <div class="card-body p-4 text-center">
                <div class="icon-circle bg-primary-soft text-primary mx-auto mb-3">
                    <i class="bi bi-people-fill"></i>
                </div>
                <h2 class="fw-extrabold mb-1">{{ $students->count() }}</h2>
                <span class="text-muted small fw-bold text-uppercase ls-1">Total Siswa</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        @php
            $hadir_pagi = $students->filter(fn($s) => $s->absensi->where('status', 'hadir')->isNotEmpty())->count();
        @endphp
        <div class="card border-0 shadow-sm h-100 bg-white hover-up-premium-mini">
            <div class="card-body p-4 text-center">
                <div class="icon-circle bg-emerald-soft text-emerald mx-auto mb-3">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <h2 class="fw-extrabold mb-1">{{ $hadir_pagi }}</h2>
                <span class="text-muted small fw-bold text-uppercase ls-1">Hadir Pagi</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        @php
            $tidak_hadir = $students->count() - $hadir_pagi;
        @endphp
        <div class="card border-0 shadow-sm h-100 bg-white hover-up-premium-mini">
            <div class="card-body p-4 text-center">
                <div class="icon-circle bg-rose-soft text-rose mx-auto mb-3">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
                <h2 class="fw-extrabold mb-1">{{ $tidak_hadir }}</h2>
                <span class="text-muted small fw-bold text-uppercase ls-1">Alfa/Izin/Sakit</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 bg-white hover-up-premium-mini">
            <div class="card-body p-4 text-center">
                <div class="icon-circle bg-info-soft text-info mx-auto mb-3">
                    <i class="bi bi-calendar-event"></i>
                </div>
                <h2 class="fw-extrabold mb-1">{{ $sessions->count() }}</h2>
                <span class="text-muted small fw-bold text-uppercase ls-1">Sesi Mapel Hari Ini</span>
            </div>
        </div>
    </div>
</div>

{{-- Attendance Monitoring Table --}}
<div class="card border-0 shadow-premium rounded-4 overflow-hidden fade-in-delayed">
    <div class="card-header bg-white py-4 px-4 border-bottom border-light d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-1 text-dark">Monitoring Kehadiran Real-time</h5>
            <p class="text-muted small mb-0">Detail kehadiran siswa per mata pelajaran hari ini.</p>
        </div>
        <div class="bg-light-blue px-3 py-2 rounded-pill">
            <span class="text-primary small fw-bold"><i class="bi bi-clock me-2"></i>{{ $today->translatedFormat('l, d F Y') }}</span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 py-3 small fw-bold text-uppercase ls-1" style="min-width: 250px;">Siswa</th>
                    <th class="py-3 small fw-bold text-uppercase ls-1 text-center" style="min-width: 100px;">Masuk Pagi</th>
                    @foreach($sessions as $session)
                        <th class="py-3 small fw-bold text-uppercase ls-1 text-center" style="min-width: 120px;">
                            <div class="text-primary">{{ $session->jam_mulai }}</div>
                            <div class="text-muted" style="font-size: 10px;">{{ Str::limit($session->mataPelajaran->nama_mapel, 15) }}</div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle bg-primary-soft text-primary me-3">
                                {{ substr($student->nama, 0, 1) }}
                            </div>
                            <div>
                                <div class="fw-bold text-dark cursor-pointer text-hover-primary" 
                                     data-bs-toggle="modal" 
                                     data-bs-target="#studentModal{{ $student->id }}">
                                    {{ $student->nama }}
                                </div>
                                <div class="text-muted xs-small">NIS: {{ $student->nis }}</div>
                            </div>
                        </div>

                        {{-- Student Bio Modal --}}
                        <div class="modal fade" id="studentModal{{ $student->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 rounded-5 overflow-hidden shadow-premium">
                                    <div class="modal-header border-0 bg-primary text-white p-4">
                                        <h5 class="modal-title fw-extrabold"><i class="bi bi-person-badge me-2"></i>Biodata Siswa</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4 p-md-5">
                                        <div class="text-center mb-4">
                                            <div class="avatar-large bg-primary-soft text-primary mx-auto mb-3">
                                                {{ substr($student->nama, 0, 1) }}
                                            </div>
                                            <h4 class="fw-extrabold text-dark mb-1">{{ $student->nama }}</h4>
                                            <span class="badge bg-light-blue text-primary rounded-pill px-3 py-2">NIS: {{ $student->nis }}</span>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <label class="xs-small fw-bold text-muted uppercase ls-1">Jenis Kelamin</label>
                                                <div class="fw-bold text-dark">{{ $student->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                                            </div>
                                            <div class="col-6">
                                                <label class="xs-small fw-bold text-muted uppercase ls-1">No. WhatsApp</label>
                                                <div class="fw-bold text-dark">{{ $student->no_hp ?? '-' }}</div>
                                            </div>
                                            <div class="col-12">
                                                <hr class="opacity-10">
                                            </div>
                                            <div class="col-12 text-center">
                                                <p class="text-muted small italic">Informasi ini tersinkronisasi otomatis dengan database pusat.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 p-4">
                                        <button type="button" class="btn btn-light w-100 rounded-pill fw-bold" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        @php
                            $pagi = $student->absensi->first();
                        @endphp
                        @if($pagi)
                            <span class="badge bg-{{ $pagi->status == 'hadir' ? 'emerald' : ($pagi->status == 'terlambat' ? 'amber' : 'rose') }}-soft text-{{ $pagi->status == 'hadir' ? 'emerald' : ($pagi->status == 'terlambat' ? 'amber' : 'rose') }} rounded-pill px-3 py-2 border border-{{ $pagi->status == 'hadir' ? 'emerald' : ($pagi->status == 'terlambat' ? 'amber' : 'rose') }} border-opacity-10">
                                {{ strtoupper($pagi->status) }}
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-muted rounded-pill px-3 py-2">BELUM SCAN</span>
                        @endif
                    </td>
                    @foreach($sessions as $session)
                        <td class="text-center">
                            @php
                                $att = $mapel_attendance->where('siswa_id', $student->id)->where('jadwal_pelajaran_id', $session->id)->first();
                            @endphp
                            @if($att)
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-check-circle-fill text-{{ $att->status == 'hadir' ? 'emerald' : 'rose' }} fs-5"></i>
                                    <span class="xs-small fw-bold text-{{ $att->status == 'hadir' ? 'emerald' : 'rose' }}">{{ strtoupper($att->status) }}</span>
                                </div>
                            @else
                                <div class="d-flex flex-column align-items-center opacity-25">
                                    <i class="bi bi-dash-circle fs-5"></i>
                                    <span class="xs-small fw-bold text-muted">A/B</span>
                                </div>
                            @endif
                        </td>
                    @endforeach
                </tr>
                @empty
                <tr>
                    <td colspan="{{ 2 + $sessions->count() }}" class="text-center py-5">
                        <i class="bi bi-people fs-1 text-muted opacity-25 d-block mb-3"></i>
                        <h5 class="text-muted fw-bold">Belum ada siswa di kelas ini</h5>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    .hero-wali { background: linear-gradient(135deg, #4f46e5, #9333ea); border-radius: 40px !important; }
    .hero-pattern { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 40%); }
    .backdrop-blur { backdrop-filter: blur(8px); }
    .ls-tight { letter-spacing: -1.2px; }
    .ls-1 { letter-spacing: 0.5px; }
    
    .hover-up-premium-mini:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.05) !important; transition: all 0.3s ease; }
    .icon-circle { width: 44px; height: 44px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
    .avatar-circle { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.1rem; }
    .avatar-large { width: 80px; height: 80px; border-radius: 24px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 2.5rem; }
    .text-hover-primary:hover { color: var(--primary-blue) !important; transition: color 0.3s ease; }
    
    .bg-primary-soft { background-color: #eff6ff; }
    .bg-emerald-soft { background-color: #ecfdf5; }
    .text-emerald { color: #10b981; }
    .bg-rose-soft { background-color: #fff1f2; }
    .text-rose { color: #e11d48; }
    .bg-amber-soft { background-color: #fffbeb; }
    .text-amber { color: #f59e0b; }
    .bg-info-soft { background-color: #e0f2fe; }
    .text-info { color: #0ea5e9; }
    .bg-light-blue { background-color: #f0f9ff; }
    .xs-small { font-size: 10px; }

    @media (max-width: 768px) {
        .hero-wali { border-radius: 24px !important; }
        .hero-wali h1 { font-size: 1.75rem !important; }
    }
</style>
@endsection
