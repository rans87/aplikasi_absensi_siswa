@extends('layouts.app')

@section('title', 'Manajemen Absensi Mapel')

@section('content')
<div class="attendance-index-wrapper py-4 px-3 px-md-4 text-center">
    {{-- /* header dashboard guru */ --}}
    <div class="index-header mb-5 p-4 p-md-5 animate__animated animate__fadeIn text-start">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="d-flex align-items-center gap-4">
                    <div class="header-icon-circle bg-white text-primary text-center">
                        <i class="bi bi-journal-check fs-2"></i>
                    </div>
                    <div>
                        <h1 class="display-6 fw-black text-white mb-1 ls-tight">Absensi Mata Pelajaran</h1>
                        <form action="{{ route('absensi-mapel.index') }}" method="GET" class="d-flex align-items-center gap-2 mt-2">
                            <div class="input-group input-group-sm rounded-pill overflow-hidden bg-white bg-opacity-10 border-0" style="max-width: 250px;">
                                <span class="input-group-text bg-transparent border-0 text-white-50 ps-3">
                                    <i class="bi bi-calendar-event"></i>
                                </span>
                                <input type="date" name="tanggal" value="{{ $tanggal }}" 
                                       class="form-control bg-transparent border-0 text-white fw-bold py-2" 
                                       onchange="this.form.submit()">
                            </div>
                            @if($tanggal != now()->toDateString())
                                <a href="{{ route('absensi-mapel.index') }}" class="btn btn-sm btn-light rounded-pill px-3 fw-bold">Hari Ini</a>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 text-lg-end">
                <div class="quick-stats-glass d-inline-flex gap-4 p-3 rounded-4 backdrop-blur text-start">
                    <div class="stat-item text-center">
                        <div class="stat-val text-white fw-black">{{ $jadwalHariIni->count() }}</div>
                        <div class="stat-lab text-white text-opacity-75 small fw-bold">JADWAL</div>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item text-center">
                        <div class="stat-val text-white fw-black">{{ $absensi->count() }}</div>
                        <div class="stat-lab text-white text-opacity-75 small fw-bold">TOTAL HADIR</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- /* list jadwal mengajar hari ini */ --}}
    <h4 class="section-title mb-4 animate__animated animate__fadeInUp text-start" style="animation-delay: 0.1s;">
        <span class="title-decor"></span> JADWAL MENGAJAR HARI INI
    </h4>

    <div class="row g-4 mb-5 text-start">
        @forelse($jadwalHariIni as $j)
        @php
            $totalSiswa = \App\Models\AnggotaKelas::where('rombongan_belajar_id', $j->rombongan_belajar_id)->count();
            $sudahAbsen = \App\Models\AbsensiMapel::where('jadwal_pelajaran_id', $j->id)
                ->whereDate('tanggal', $tanggal)->count();
            $nowTime = now()->format('H:i');
            $isActive = $nowTime >= $j->jam_mulai && $nowTime <= $j->jam_selesai;
            $isDone = $nowTime > $j->jam_selesai;
        @endphp
        <div class="col-xl-4 col-md-6 animate__animated animate__fadeInUp" style="animation-delay: {{ 0.2 + ($loop->index * 0.1) }}s;">
            <div class="schedule-card-premium {{ $isActive ? 'active-glow' : '' }} {{ $isDone ? 'done-shaded' : '' }}">
                <div class="card-header-flex d-flex justify-content-between align-items-center mb-3">
                    <div class="time-badge">
                        <i class="bi bi-clock-fill me-1"></i> {{ $j->jam_mulai }} - {{ $j->jam_selesai }}
                    </div>
                    @if($isActive)
                        <span class="live-indicator"><span class="dot"></span> LIVE NOW</span>
                    @elseif($isDone)
                        <span class="badge bg-success-soft text-success rounded-pill px-3 py-1 fw-bold small">SELESAI</span>
                    @else
                        <span class="badge bg-light text-muted rounded-pill px-3 py-1 fw-bold small">UPCOMING</span>
                    @endif
                </div>

                <h3 class="subject-title fw-black text-dark mb-1 text-truncate">{{ $j->mataPelajaran->nama_mapel }}</h3>
                <div class="class-info text-muted fw-bold mb-4">
                    <i class="bi bi-door-open-fill me-1"></i>{{ $j->rombonganBelajar->nama_kelas }} 
                    <span class="mx-2">|</span> 
                    <i class="bi bi-mortarboard-fill me-1"></i>{{ $j->rombonganBelajar->jurusan }}
                </div>

                <div class="attendance-summary mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small text-muted fw-black">PROGRESS ABSENSI</span>
                        <span class="small fw-black text-primary">{{ $sudahAbsen }}/{{ $totalSiswa }} SISWA</span>
                    </div>
                    <div class="progress-premium-sm">
                        <div class="bar bg-primary" style="width: {{ $totalSiswa > 0 ? ($sudahAbsen/$totalSiswa*100) : 0 }}%"></div>
                    </div>
                </div>

                <div class="action-buttons-grid d-flex gap-2 pt-2">
                    <a href="{{ route('absensi-mapel.session', $j->id) }}" class="btn-grid-primary flex-grow-1">
                        <i class="bi bi-box-arrow-in-right"></i> BUKA SESI
                    </a>
                    @if(!$isDone)
                    <button class="btn-grid-outline btn-scan-mapel" 
                            data-jadwal-id="{{ $j->id }}"
                            data-mapel="{{ $j->mataPelajaran->nama_mapel }}"
                            data-kelas="{{ $j->rombonganBelajar->nama_kelas }}">
                        <i class="bi bi-qr-code-scan"></i>
                    </button>
                    @endif
                    <form action="{{ route('absensi-mapel.selesai') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="jadwal_pelajaran_id" value="{{ $j->id }}">
                        <button type="submit" class="btn-grid-success btn-selesai-mengajar" title="Selesai Mengajar">
                            <i class="bi bi-check2-circle"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center">
            <div class="empty-state-premium p-5">
                <img src="https://illustrations.popsy.co/white/calendar.svg" alt="Empty" style="width: 200px;" class="mb-4">
                <h4 class="fw-black text-dark">Tidak ada jadwal pada {{ $hari }}</h4>
                <p class="text-muted">Pastikan jadwal Anda sudah terdaftar atau pilih tanggal lain di atas.</p>
                <div class="mt-3">
                    <span class="badge bg-light text-muted p-3 rounded-4">
                        <i class="bi bi-info-circle me-1"></i> Jadwal Anda muncul otomatis sesuai hari di jadwal pelajaran.
                    </span>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    {{-- /* tabel riwayat absensi terbaru */ --}}
    <h4 class="section-title mb-4 animate__animated animate__fadeInUp text-start" style="animation-delay: 0.5s;">
        <span class="title-decor"></span> RIWAYAT ABSENSI TERBARU
    </h4>

    <div class="history-card-premium animate__animated animate__fadeInUp text-start" style="animation-delay: 0.6s;">
        <div class="table-responsive">
            <table class="table-index-premium table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">SISWA</th>
                        <th>MATA PELAJARAN</th>
                        <th>KELAS</th>
                        <th class="text-center">STATUS</th>
                        <th class="pe-4 text-end">WAKTU SCAN</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($absensi as $a)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-sm-circle bg-primary-soft text-primary text-center">
                                    {{ strtoupper(substr($a->siswa->nama ?? 'S', 0, 1)) }}
                                </div>
                                <div class="student-meta">
                                    <div class="fw-black text-dark">{{ $a->siswa->nama ?? 'Siswa' }}</div>
                                    <div class="small text-muted fw-bold">NIS: {{ $a->siswa->nis ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="subject-pill">{{ $a->jadwalPelajaran->mataPelajaran->nama_mapel ?? '-' }}</div>
                        </td>
                        <td><span class="fw-bold text-dark">{{ $a->jadwalPelajaran->rombonganBelajar->nama_kelas ?? '-' }}</span></td>
                        <td class="text-center">
                            <span class="badge bg-success-soft text-success rounded-pill px-3 py-2 fw-black">
                                <i class="bi bi-check-circle-fill me-1"></i>HADIR
                            </span>
                        </td>
                        <td class="pe-4 text-end">
                            <div class="fw-black text-muted"><i class="bi bi-clock me-1"></i>{{ $a->waktu_scan ? $a->waktu_scan->format('H:i:s') : '--:--' }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <p class="text-muted fw-bold mb-0">Belum ada aktivitas absensi hari ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- /* modal quick scanner */ --}}
<div class="modal fade" id="scannerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content index-modal-glass border-0">
            <div class="modal-header border-0 p-4 pb-0">
                <div class="text-white text-start">
                    <h5 class="fw-black mb-0"><i class="bi bi-qr-code-scan me-2"></i>QUICK SCAN</h5>
                    <p class="small text-white text-opacity-75 mb-0" id="scannerModalSubtitle"></p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="scanner-wrapper-index">
                    <div id="reader-mapel-index" style="width: 100%; height: 300px; background: #000;"></div>
                    <div class="scanner-overlay-index">
                        <div class="scan-frame-index"></div>
                    </div>
                </div>
                <div id="scan-loading" class="text-center p-4 d-none text-white">
                    <div class="spinner-border text-white mb-3" role="status"></div>
                    <p class="fw-black">MEMPROSES DATA...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* css styling premium index */
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800;900&display=swap');
    
    body { font-family: 'Outfit', sans-serif; background-color: #f8fafc; }
    .fw-black { font-weight: 800; }
    .ls-tight { letter-spacing: -1.5px; }

    /* Header Section */
    .index-header {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        border-radius: 40px;
        box-shadow: 0 30px 60px -12px rgba(79, 70, 229, 0.35);
        position: relative;
        overflow: hidden;
    }
    .header-icon-circle {
        width: 70px; height: 70px; border-radius: 22px;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .quick-stats-glass {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .stat-val { font-size: 1.5rem; line-height: 1; margin-bottom: 2px; }
    .stat-lab { font-size: 10px; letter-spacing: 1px; }
    .stat-divider { width: 1px; background: rgba(255, 255, 255, 0.2); align-self: stretch; }

    /* Section Titles */
    .section-title {
        display: flex; align-items: center; gap: 12px;
        font-weight: 900; letter-spacing: 1px; font-size: 1.1rem; color: #1e293b;
    }
    .title-decor { width: 40px; height: 6px; background: #4f46e5; border-radius: 100px; }

    /* Schedule Cards */
    .schedule-card-premium {
        background: white;
        border-radius: 35px;
        padding: 30px;
        border: 1px solid rgba(0,0,0,0.03);
        box-shadow: 0 10px 30px rgba(0,0,0,0.02);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    .schedule-card-premium:hover { transform: translateY(-10px); box-shadow: 0 30px 60px -15px rgba(0,0,0,0.1); }
    .active-glow { border: 2px solid #4f46e5; box-shadow: 0 20px 40px rgba(79, 70, 229, 0.1); }
    .done-shaded { opacity: 0.8; background: #f8fafc; }
    
    .time-badge {
        background: #f1f5f9; color: #475569; padding: 6px 16px; border-radius: 12px;
        font-weight: 800; font-size: 12px;
    }
    .live-indicator {
        font-size: 10px; font-weight: 900; color: #ef4444; display: flex; align-items: center; gap: 6px;
    }
    .live-indicator .dot { width: 8px; height: 8px; background: #ef4444; border-radius: 50%; animation: blink 1s infinite; }
    @keyframes blink { 0% { opacity: 1; } 50% { opacity: 0.3; } 100% { opacity: 1; } }

    .subject-title { font-size: 1.6rem; letter-spacing: -0.5px; }
    .progress-premium-sm { height: 6px; background: #f1f5f9; border-radius: 100px; overflow: hidden; }
    .progress-premium-sm .bar { height: 100%; border-radius: 100px; }

    /* Card Buttons */
    .btn-grid-primary {
        background: #4f46e5; color: white; border: none; padding: 12px; border-radius: 18px;
        font-weight: 800; display: flex; align-items: center; justify-content: center; gap: 8px;
        transition: all 0.2s; box-shadow: 0 8px 15px rgba(79, 70, 229, 0.2);
    }
    .btn-grid-primary:hover { background: #4338ca; transform: scale(1.02); color: white; }
    
    .btn-grid-outline {
        width: 48px; height: 48px; background: white; border: 2px solid #e2e8f0; border-radius: 18px;
        display: flex; align-items: center; justify-content: center; color: #475569; transition: all 0.2s;
    }
    .btn-grid-outline:hover { background: #f8fafc; border-color: #cbd5e1; transform: scale(1.05); }
    
    .btn-grid-success {
        width: 48px; height: 48px; background: #dcfce7; border: none; border-radius: 18px;
        display: flex; align-items: center; justify-content: center; color: #15803d; transition: all 0.2s;
    }
    .btn-grid-success:hover { background: #10b981; color: white; transform: scale(1.05); }

    /* History Table */
    .history-card-premium {
        background: white; border-radius: 35px; overflow: hidden;
        box-shadow: 0 40px 100px -20px rgba(0,0,0,0.04);
    }
    .table-index-premium thead th {
        background: #f8fafc; padding: 20px; font-weight: 800; font-size: 11px; color: #64748b;
        letter-spacing: 1px; border: none;
    }
    .table-index-premium tbody td { padding: 18px 20px; border-bottom: 1px solid #f1f5f9; }
    .avatar-sm-circle {
        width: 40px; height: 40px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; font-weight: 800;
    }
    .bg-primary-soft { background: #eef2ff; color: #4f46e5; }
    .subject-pill {
        display: inline-block; padding: 6px 14px; background: #f1f5f9; border-radius: 10px;
        font-weight: 800; font-size: 12px; color: #475569;
    }
    .bg-success-soft { background: #ecfdf5; color: #10b981; }

    /* Modal Glass */
    .index-modal-glass {
        background: rgba(15, 23, 42, 0.9); backdrop-filter: blur(20px); border-radius: 35px;
    }
    .scanner-wrapper-index { position: relative; border-radius: 20px; overflow: hidden; background: #000; }
    .scan-frame-index {
        width: 200px; height: 200px; border: 2px solid #4f46e5; border-radius: 30px;
        box-shadow: 0 0 0 2000px rgba(0,0,0,0.5); position: absolute;
        top: 50%; left: 50%; transform: translate(-50%, -50%);
    }

    .empty-state-premium { background: white; border-radius: 40px; border: 2px dashed #e2e8f0; }
</style>

@push('scripts')
{{-- /* script logika dashboard & quick scan */ --}}
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let html5QrCodeIndex = null;
    let currentJadwalId = null;
    
    document.querySelectorAll('.btn-scan-mapel').forEach(btn => {
        btn.addEventListener('click', function() {
            currentJadwalId = this.dataset.jadwalId;
            document.getElementById('scannerModalSubtitle').textContent = `${this.dataset.mapel} - ${this.dataset.kelas}`;
            const modal = new bootstrap.Modal(document.getElementById('scannerModal'));
            modal.show();
            document.getElementById('scannerModal').addEventListener('shown.bs.modal', startQuickScanner, { once: true });
        });
    });
    
    function startQuickScanner() {
        if (html5QrCodeIndex) html5QrCodeIndex.stop().catch(() => {});
        html5QrCodeIndex = new Html5Qrcode("reader-mapel-index");
        html5QrCodeIndex.start(
            { facingMode: "environment" },
            { fps: 15, qrbox: { width: 200, height: 200 } },
            processScanData
        ).catch(err => Swal.fire('Error', 'Gagal akses kamera', 'error'));
    }

    function processScanData(decodedText) {
        html5QrCodeIndex.stop().then(() => {
            document.getElementById('reader-mapel-index').classList.add('d-none');
            document.querySelector('.scanner-overlay-index').classList.add('d-none');
            document.getElementById('scan-loading').classList.remove('d-none');
            
            fetch('{{ route("absensi-mapel.proses-scan") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    qr_code: decodedText,
                    jadwal_pelajaran_id: currentJadwalId
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-4' }
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message,
                        customClass: { popup: 'rounded-4' }
                    }).then(() => {
                        document.getElementById('reader-mapel-index').classList.remove('d-none');
                        document.querySelector('.scanner-overlay-index').classList.remove('d-none');
                        document.getElementById('scan-loading').classList.add('d-none');
                        startQuickScanner();
                    });
                }
            })
            .catch(() => Swal.fire('Error', 'Kesalahan server', 'error').then(() => startQuickScanner()));
        });
    }

    document.getElementById('scannerModal').addEventListener('hidden.bs.modal', () => {
        if (html5QrCodeIndex) html5QrCodeIndex.stop().catch(() => {});
    });

    document.querySelectorAll('.btn-selesai-mengajar').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            Swal.fire({
                title: 'Selesai Mengajar?',
                text: 'Kirim notifikasi ke guru berikutnya?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                confirmButtonText: 'Ya, Selesai!',
                customClass: { popup: 'rounded-4', confirmButton: 'rounded-pill px-4', cancelButton: 'rounded-pill px-4' }
            }).then((result) => result.isConfirmed && form.submit());
        });
    });
</script>
@endpush
@endsection
