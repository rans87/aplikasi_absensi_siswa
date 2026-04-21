@extends('layouts.app')

@section('title', 'Sesi Absensi: ' . $jadwal->mataPelajaran->nama_mapel)

@section('content')
    <div class="attendance-wrapper py-4 px-3 px-md-4">
        {{-- /* header informasi sesi */ --}}
        <div class="header-glass mb-4 p-4 p-md-5 animate__animated animate__fadeInDown">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <div class="d-flex align-items-center gap-4 mb-3">
                        <div class="icon-box-premium">
                            <i class="bi bi-journal-bookmark-fill fs-2"></i>
                        </div>
                        <div>
                            <div class="status-indicator-pill mb-2">
                                <span class="pulse-dot"></span>
                                SESI BERJALAN: {{ strtoupper($jadwal->hari) }}
                            </div>
                            <h1 class="display-5 fw-black text-white mb-1 ls-tight">{{ $jadwal->mataPelajaran->nama_mapel }}</h1>
                            <p class="text-white text-opacity-75 fs-5 mb-0 fw-medium">
                                <i class="bi bi-people-fill me-2"></i>Kelas: {{ $jadwal->rombonganBelajar->nama_kelas }} 
                                <span class="mx-2 opacity-50">|</span> 
                                <i class="bi bi-clock-fill me-2"></i>{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 text-lg-end">
                    <div class="d-flex flex-wrap gap-3 justify-content-lg-end">
                        <button class="btn-premium-light" data-bs-toggle="modal" data-bs-target="#scannerModal">
                            <i class="bi bi-qr-code-scan me-2"></i>ACTIVATE SCANNER
                        </button>
                        <a href="{{ route('guru.dashboard') }}" class="btn-premium-outline">
                            <i class="bi bi-grid-fill me-2"></i>DASHBOARD
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- /* kartu statistik kehadiran */ --}}
        <div class="row g-4 mb-4">
            <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                <div class="stat-card-premium h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stat-label">KEHADIRAN (%)</div>
                        <div class="stat-icon bg-success-soft"><i class="bi bi-graph-up-arrow"></i></div>
                    </div>
                    <div class="d-flex align-items-end gap-2 mb-3">
                        <h2 class="stat-value mb-0" id="progress-text">0%</h2>
                        <span class="stat-sub text-muted mb-1">DARI TOTAL SISWA</span>
                    </div>
                    <div class="progress-premium">
                        <div class="progress-bar-premium bg-gradient-success" id="progress-bar" style="width: 0%"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                <div class="stat-card-premium h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stat-label">HADIR / TOTAL</div>
                        <div class="stat-icon bg-primary-soft text-primary"><i class="bi bi-person-check-fill"></i></div>
                    </div>
                    <div class="d-flex align-items-end gap-2 mb-1">
                        <h2 class="stat-value mb-0 text-primary" id="hadir-count-display">0</h2>
                        <span class="stat-sub text-muted mb-1">/ {{ $siswa->count() }} SISWA</span>
                    </div>
                    <p class="small text-muted mb-0 fw-bold"><i class="bi bi-info-circle me-1"></i>Siswa yang sudah melakukan scan</p>
                </div>
            </div>
            <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
                <div class="stat-card-premium h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stat-label">TIDAK HADIR</div>
                        <div class="stat-icon bg-danger-soft text-danger"><i class="bi bi-person-x-fill"></i></div>
                    </div>
                    <div class="d-flex align-items-end gap-2 mb-1">
                        <h2 class="stat-value mb-0 text-danger" id="unabsent-count-display">{{ $siswa->count() }}</h2>
                        <span class="stat-sub text-muted mb-1">SISWA</span>
                    </div>
                    <p class="small text-muted mb-0 fw-bold"><i class="bi bi-exclamation-triangle-fill me-1 text-warning"></i>Termasuk izin, sakit & alfa</p>
                </div>
            </div>
        </div>

        {{-- /* tabel daftar siswa & presensi */ --}}
        <div class="list-container animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
            <div class="list-card-premium">
                <div class="list-header p-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <h4 class="fw-black text-dark mb-0">PRESENSI SISWA</h4>
                    <div class="search-box-premium">
                        <i class="bi bi-search"></i>
                        <input type="text" id="studentSearch" placeholder="Cari nama atau NIS...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table-premium table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4 py-4">DETAILS SISWA</th>
                                <th class="text-center">STATUS SAAT INI</th>
                                <th class="text-center">AKSI CEPAT (MANUAL)</th>
                                <th class="pe-4 text-end">WAKTU SCAN</th>
                            </tr>
                        </thead>
                        <tbody id="student-list-body">
                            @foreach($siswa as $s)
                            @php 
                                $abs = $absensi->get($s->id);
        $status = $abs ? $abs->status : 'belum';
                            @endphp
                            <tr id="row-{{ $s->id }}" data-qr="{{ $s->qr_code }}" class="student-row">
                                <td class="ps-4 py-3">
                                    <div class="student-profile d-flex align-items-center gap-3">
                                        <div class="avatar-premium bg-gradient-{{ ['primary', 'indigo', 'purple', 'blue'][($loop->index % 4)] }}">
                                            {{ strtoupper(substr($s->nama, 0, 1)) }}
                                        </div>
                                        <div class="profile-info">
                                            <div class="student-name fw-black text-dark fs-6">{{ $s->nama }}</div>
                                            <div class="student-nis text-muted fw-bold">NIS: {{ $s->nis }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="status-pill status-{{ $status }}" id="badge-{{ $s->id }}">
                                        {{ $status == 'belum' ? 'BELUM ABSEN' : strtoupper($status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="action-grid d-inline-flex gap-2">
                                        <button class="btn-action btn-h update-status" data-siswa-id="{{ $s->id }}" data-status="hadir" title="Hadir">H</button>
                                        <button class="btn-action btn-i update-status" data-siswa-id="{{ $s->id }}" data-status="izin" title="Izin">I</button>
                                        <button class="btn-action btn-s update-status" data-siswa-id="{{ $s->id }}" data-status="sakit" title="Sakit">S</button>
                                        <button class="btn-action btn-d update-status" data-siswa-id="{{ $s->id }}" data-status="dispen" title="Dispen">D</button>
                                        <button class="btn-action btn-a update-status" data-siswa-id="{{ $s->id }}" data-status="alfa" title="Alfa">A</button>
                                    </div>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="scan-time-premium text-muted fw-black" id="time-{{ $s->id }}">
                                        {{ $abs && $abs->status == 'hadir' && $abs->waktu_scan ? $abs->waktu_scan->format('H:i') : '--:--' }}
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="list-footer p-4 d-flex justify-content-between align-items-center bg-light-subtle rounded-bottom-4">
                    <p class="mb-0 text-muted fw-medium">Pastikan semua siswa terabsen sebelum sesi berakhir.</p>
                    <form action="{{ route('absensi-mapel.selesai') }}" method="POST">
                        @csrf
                        <input type="hidden" name="jadwal_pelajaran_id" value="{{ $jadwal->id }}">
                        <button type="submit" class="btn-finish-premium">
                            <i class="bi bi-flag-fill me-2"></i>SELESAI MENGAJAR
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- /* modal scanner qr code */ --}}
    <div class="modal fade" id="scannerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content modal-premium-glass border-0">
                <div class="modal-header border-0 p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-circle bg-white text-primary fs-4"><i class="bi bi-qr-code-scan"></i></div>
                        <div>
                            <h5 class="modal-title fw-black text-white mb-0">SMART SCANNER ACTIVE</h5>
                            <p class="text-white text-opacity-75 small mb-0">Arahkan kamera ke QR Code milik siswa</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 position-relative">
                    <div class="scanner-container">
                        <div id="reader-premium" style="width: 100%; min-height: 250px; background: #000;"></div>
                        <div class="scanner-overlay">
                            <div class="scanner-frame"></div>
                            <div class="scanner-line"></div>
                        </div>
                        <div id="scan-feedback-premium" class="scan-success-popup d-none">
                            <div class="success-icon"><i class="bi bi-check-circle-fill"></i></div>
                            <div class="success-text">PRESENSI BERHASIL!</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 bg-white bg-opacity-10 backdrop-blur">
                    <div class="d-flex justify-content-between w-100 align-items-center">
                        <div class="text-white small fw-bold">
                            <i class="bi bi-camera-video-fill me-2"></i>Status Kamera: <span class="text-success">Ready</span>
                        </div>
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-black" data-bs-dismiss="modal">STOP SCANNING</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* css styling premium */
        /* Fonts & Core */
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800;900&display=swap');

        body { font-family: 'Outfit', sans-serif; background-color: #f0f2f5; }
        .fw-black { font-weight: 800; }
        .ls-tight { letter-spacing: -2px; }

        /* Header Glass Design */
        .header-glass {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border-radius: 40px;
            box-shadow: 0 25px 50px -12px rgba(79, 70, 229, 0.4);
            position: relative;
            overflow: hidden;
        }
        .header-glass::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            top: -150px;
            right: -50px;
        }
        .icon-box-premium {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .status-indicator-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(0, 0, 0, 0.2);
            padding: 6px 16px;
            border-radius: 100px;
            color: white;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1px;
        }
        .pulse-dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        /* Buttons */
        .btn-premium-light {
            background: white;
            color: #4f46e5;
            border: none;
            padding: 12px 28px;
            border-radius: 16px;
            font-weight: 800;
            letter-spacing: 0.5px;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .btn-premium-light:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
            color: #4338ca;
        }
        .btn-premium-outline {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 12px 28px;
            border-radius: 16px;
            font-weight: 800;
            backdrop-filter: blur(10px);
            transition: all 0.3s;
        }
        .btn-premium-outline:hover { background: rgba(255, 255, 255, 0.2); color: white; transform: translateY(-3px); }

        /* Stat Cards */
        .stat-card-premium {
            background: white;
            border-radius: 30px;
            padding: 30px;
            border: 1px solid rgba(0,0,0,0.03);
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
            transition: transform 0.3s;
        }
        .stat-card-premium:hover { transform: translateY(-5px); }
        .stat-label { font-weight: 800; color: #64748b; font-size: 13px; letter-spacing: 1px; }
        .stat-icon { width: 45px; height: 45px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .stat-value { font-weight: 900; font-size: 2.8rem; letter-spacing: -2px; }
        .stat-sub { font-weight: 700; font-size: 12px; }

        .bg-success-soft { background: #ecfdf5; color: #10b981; }
        .bg-primary-soft { background: #eef2ff; color: #4f46e5; }
        .bg-danger-soft { background: #fef2f2; color: #ef4444; }

        .progress-premium { background: #f1f5f9; height: 10px; border-radius: 100px; overflow: hidden; }
        .progress-bar-premium { height: 100%; border-radius: 100px; transition: width 1s ease-in-out; }
        .bg-gradient-success { background: linear-gradient(90deg, #10b981, #34d399); }

        /* List Design */
        .list-card-premium {
            background: white;
            border-radius: 35px;
            box-shadow: 0 40px 100px -20px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .search-box-premium {
            position: relative;
            width: 300px;
        }
        .search-box-premium i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
        .search-box-premium input {
            width: 100%;
            padding: 12px 20px 12px 48px;
            background: #f8fafc;
            border: 2px solid #f1f5f9;
            border-radius: 16px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .search-box-premium input:focus { outline: none; border-color: #4f46e5; background: white; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }

        .table-premium thead th { font-weight: 800; color: #64748b; font-size: 12px; letter-spacing: 1px; border-bottom: 2px solid #f1f5f9; }
        .student-row { transition: background 0.2s; cursor: default; }
        .student-row:hover { background: #f8fafc; }

        .avatar-premium {
            width: 48px;
            height: 48px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 1.2rem;
        }
        .bg-gradient-primary { background: linear-gradient(135deg, #4f46e5, #6366f1); }
        .bg-gradient-indigo { background: linear-gradient(135deg, #4338ca, #4f46e5); }
        .bg-gradient-purple { background: linear-gradient(135deg, #7c3aed, #8b5cf6); }
        .bg-gradient-blue { background: linear-gradient(135deg, #2563eb, #3b82f6); }

        .status-pill {
            display: inline-block;
            padding: 8px 18px;
            border-radius: 12px;
            font-weight: 800;
            font-size: 11px;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }
        .status-hadir { background: #dcfce7; color: #15803d; border: 1px solid #bef264; }
        .status-belum { background: #f1f5f9; color: #64748b; }
        .status-izin { background: #e0f2fe; color: #0369a1; }
        .status-sakit { background: #fef9c3; color: #a16207; }
        .status-dispen { background: #f5f3ff; color: #6d28d9; }
        .status-alfa { background: #fee2e2; color: #b91c1c; }

        .action-grid .btn-action {
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 12px;
            transition: all 0.2s;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        .btn-h { background: #10b981; color: white; }
        .btn-i { background: #3b82f6; color: white; }
        .btn-s { background: #f59e0b; color: white; }
        .btn-d { background: #8b5cf6; color: white; }
        .btn-a { background: #ef4444; color: white; }
        .btn-action:hover { transform: scale(1.15) translateY(-2px); filter: brightness(1.1); }
        .btn-action:active { transform: scale(0.9); }

        .scan-time-premium { font-size: 14px; color: #64748b; }

        .btn-finish-premium {
            background: #10b981;
            color: white;
            border: none;
            padding: 14px 40px;
            border-radius: 20px;
            font-weight: 900;
            letter-spacing: 1px;
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .btn-finish-premium:hover { transform: scale(1.05); box-shadow: 0 15px 35px rgba(16, 185, 129, 0.4); }

        /* Modern Scanner Modal Styles */
        .modal-premium-glass {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 40px;
        }
        .icon-circle { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .scanner-container { position: relative; background: #000; overflow: hidden; border-radius: 20px; margin: 0 20px; }
        #reader-premium video { width: 100% !important; height: 100% !important; object-fit: cover !important; border-radius: 20px; }
        .scanner-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            pointer-events: none;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .scanner-frame {
            width: 280px;
            height: 280px;
            border: 3px solid rgba(255, 255, 255, 0.5);
            border-radius: 40px;
            box-shadow: 0 0 0 2000px rgba(0, 0, 0, 0.25);
            position: relative;
        }
        .scanner-frame::before, .scanner-frame::after, .scanner-frame span::before, .scanner-frame span::after {
            content: ''; position: absolute; width: 40px; height: 40px; border-color: #4f46e5; border-style: solid;
        }
        /* Corners for scanner frame */
        .scanner-frame {
            border: 2px solid rgba(255,255,255,0.2);
        }
        .scanner-line {
            position: absolute;
            width: 260px;
            height: 3px;
            background: linear-gradient(90deg, transparent, #4f46e5, transparent);
            box-shadow: 0 0 15px #4f46e5;
            top: 50%;
            animation: scanLines 2s ease-in-out infinite;
        }
        @keyframes scanLines {
            0% { transform: translateY(-125px); opacity: 0; }
            50% { opacity: 1; }
            100% { transform: translateY(125px); opacity: 0; }
        }
        .scan-success-popup {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #10b981;
            color: white;
            padding: 20px 40px;
            border-radius: 25px;
            text-align: center;
            z-index: 100;
            box-shadow: 0 20px 50px rgba(16, 185, 129, 0.5);
        }
        .success-icon { font-size: 3rem; margin-bottom: 10px; }
        .success-text { font-weight: 900; font-size: 1.2rem; letter-spacing: 1px; }

        /* Responsive */
        @media (max-width: 768px) {
            .header-glass { border-radius: 30px; padding: 30px !important; }
            .display-5 { font-size: 2rem; }
            .stat-value { font-size: 2rem; }
            .search-box-premium { width: 100%; }
        }
        @media (max-width: 576px) {
            .btn-finish-premium { width: 100%; }
        }


        /* Animations */
        .status-updated-success { animation: pulseGreen 1s ease-out; }
        @keyframes pulseGreen {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); box-shadow: 0 0 0 20px rgba(16, 185, 129, 0); }
        }
    </style>

    @push('scripts')
    {{-- /* script logika absensi & real-time update */ --}}
    <audio id="beepSuccess" src="https://assets.mixkit.co/active_storage/sfx/2218/2218-preview.mp3" preload="auto"></audio>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"></script>
    <script>
        let html5QrCodePremium = null;
        let isProcessingScan = false;
        const totalSiswa = {{ $siswa->count() }};

        // Audio Feedback
        const beepSuccess = new Audio('https://assets.mixkit.co/active_storage/sfx/2218/2218-preview.mp3');
        const beepError = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');

        function playSound(type) {
            const sound = type === 'success' ? beepSuccess : beepError;
            sound.currentTime = 0;
            sound.play().catch(e => { /* silence is golden */ });
            if (type === 'success' && navigator.vibrate) {
                navigator.vibrate([100, 50, 100]);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateStatsUI();

            // Search Functionality
            const searchInput = document.getElementById('studentSearch');
            searchInput.addEventListener('input', function() {
                const term = this.value.toLowerCase();
                const rows = document.querySelectorAll('.student-row');

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if(text.includes(term)) {
                        row.style.display = '';
                        row.classList.add('animate__animated', 'animate__fadeIn');
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            // Manual Status Update
            document.querySelectorAll('.update-status').forEach(btn => {
                btn.addEventListener('click', function() {
                    const siswaId = this.dataset.siswaId;
                    const status = this.dataset.status;
                    const studentName = this.closest('.student-row').querySelector('.student-name').textContent;

                    // Add loading state to button
                    this.classList.add('opacity-50');
                    updateStatusOnServer(siswaId, status, studentName, this);
                });
            });
        });

        function updateStatusOnServer(siswaId, status, name, btnElement) {
            fetch('/absensi-mapel/update-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    siswa_id: siswaId,
                    jadwal_pelajaran_id: '{{ $jadwal->id }}',
                    status: status
                })
            })
            .then(async res => {
                if (!res.ok) {
                    if (res.status === 419 || res.status === 401) {
                        throw new Error("Sesi Anda telah berakhir. Silakan muat ulang halaman (Refresh) dan login kembali.");
                    }
                    const errData = await res.json().catch(() => ({}));
                    throw new Error(errData.message || 'Gagal memperbarui status');
                }
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    updateStudentRecordUI(siswaId, status);
                    if(btnElement) btnElement.classList.remove('opacity-50');

                    // If we used a loading alert, update it to success
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: `${name} telah diabsen.`,
                        showConfirmButton: false,
                        timer: 1000
                    });
                } else {
                    throw new Error(data.message || 'Gagal memperbarui status');
                }
                isProcessingScan = false; // Reset lock
            })
            .catch(err => {
                console.error(err);
                if(btnElement) btnElement.classList.remove('opacity-50');
                Swal.fire({
                    icon: 'error',
                    title: 'System Error',
                    text: err.message || 'Terjadi kesalahan koneksi ke server.'
                });
                isProcessingScan = false;
            });
        }

        function updateStudentRecordUI(siswaId, status) {
            const badge = document.getElementById('badge-' + siswaId);
            const timeCell = document.getElementById('time-' + siswaId);

            // Update Badge
            badge.className = `status-pill status-${status} status-updated-success`;
            badge.textContent = status == 'belum' ? 'BELUM ABSEN' : status.toUpperCase();

            // Update Time
            if(status == 'hadir') {
                const now = new Date();
                timeCell.textContent = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
                timeCell.classList.add('text-primary');
            } else {
                timeCell.textContent = '--:--';
                timeCell.classList.remove('text-primary');
            }

            updateStatsUI();
        }

        function updateStatsUI() {
            const totalHadir = Array.from(document.querySelectorAll('.status-pill')).filter(b => b.textContent.trim() == 'HADIR').length;
            const totalUnabsent = totalSiswa - totalHadir;
            const percent = totalSiswa > 0 ? Math.round((totalHadir / totalSiswa) * 100) : 0;

            // Animate numbers
            animateValue("hadir-count-display", parseInt(document.getElementById('hadir-count-display').textContent), totalHadir, 500);
            animateValue("unabsent-count-display", parseInt(document.getElementById('unabsent-count-display').textContent), totalUnabsent, 500);

            document.getElementById('progress-text').textContent = percent + '%';
            document.getElementById('progress-bar').style.width = percent + '%';
        }

        function animateValue(id, start, end, duration) {
            if (start === end) return;
            const obj = document.getElementById(id);
            const range = end - start;
            let current = start;
            const increment = end > start ? 1 : -1;
            const stepTime = Math.abs(Math.floor(duration / range));
            const timer = setInterval(() => {
                current += increment;
                obj.textContent = current;
                if (current == end) clearInterval(timer);
            }, stepTime);
        }

        // Scanner Implementation
        const scannerModal = document.getElementById('scannerModal');
        scannerModal.addEventListener('shown.bs.modal', function() {
            // Delay start to ensure modal animation is complete
            setTimeout(startPremiumScanner, 500);
        });

        scannerModal.addEventListener('hidden.bs.modal', function() {
            if (html5QrCodePremium) {
                html5QrCodePremium.stop().then(() => {
                    html5QrCodePremium = null;
                }).catch(err => console.error(err));
            }
        });

        function startPremiumScanner() {
            if (html5QrCodePremium) return;

            html5QrCodePremium = new Html5Qrcode("reader-premium");
            const config = { 
                fps: 20, 
                qrbox: { width: 250, height: 250 },
                showTorchButtonIfSupported: true
            };

            const statusText = document.getElementById('camera-status-text');

            html5QrCodePremium.start(
                { facingMode: "environment" },
                config,
                onScanMatch
            ).then(() => {
                if(statusText) {
                    statusText.textContent = 'Active';
                }
            }).catch(err => {
                console.error("Scanner error:", err);
                html5QrCodePremium.start(
                    { facingMode: "user" },
                    config,
                    onScanMatch
                ).then(() => {
                    if(statusText) statusText.textContent = 'Active (Front)';
                }).catch(err2 => {
                    console.error("Fallback error:", err2);
                    if(statusText) {
                        statusText.textContent = 'Error: ' + err2;
                    }
                });
            });
        }

        function processScanOnServer(qrCodeString) {
            fetch('/absensi-mapel/proses-scan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    qr_code: qrCodeString,
                    jadwal_pelajaran_id: '{{ $jadwal->id }}'
                })
            })
            .then(async res => {
                if (!res.ok) {
                    if (res.status === 419 || res.status === 401) {
                        throw new Error("Sesi Anda telah berakhir. Silakan muat ulang halaman.");
                    }
                    const errData = await res.json().catch(() => ({}));
                    throw new Error(errData.message || 'Gagal memproses QR Code');
                }
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    playSound('success');
                    
                    // Show success frame
                    const scannerFrame = document.querySelector('.scanner-frame');
                    if(scannerFrame) {
                        scannerFrame.style.borderColor = '#10b981';
                        scannerFrame.style.boxShadow = '0 0 50px rgba(16, 185, 129, 0.5)';
                        setTimeout(() => {
                            if(scannerFrame) {
                                scannerFrame.style.borderColor = '';
                                scannerFrame.style.boxShadow = '';
                            }
                        }, 2000);
                    }

                    // Update UI using data from server
                    const studentName = data.siswa || 'Siswa';
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message || `Berhasil Absen: ${studentName}`,
                        showConfirmButton: false,
                        timer: 2000
                    });

                    // We need to find the siswa ID to update the UI row in the table
                    // We can look it up by matching the NIS or the name if ID isn't returned, 
                    // but the server can't return ID right now. Let's find it by data-qr:
                    const uuidPart = qrCodeString.split('|')[0].trim();
                    const rows = document.querySelectorAll('tr[data-qr]');
                    for (let row of rows) {
                        if (row.dataset.qr.trim() === uuidPart) {
                            const siswaId = row.id.replace('row-', '');
                            updateStudentRecordUI(siswaId, 'hadir');
                            break;
                        }
                    }

                } else {
                    throw new Error(data.message || 'Gagal memproses QR Code');
                }
            })
            .catch(err => {
                console.error(err);
                playSound('error');
                
                const scannerFrame = document.querySelector('.scanner-frame');
                if(scannerFrame) {
                    scannerFrame.style.borderColor = '#ef4444'; // Red for error
                    scannerFrame.style.boxShadow = '0 0 50px rgba(239, 68, 68, 0.5)';
                    setTimeout(() => {
                        if(scannerFrame) {
                            scannerFrame.style.borderColor = '';
                            scannerFrame.style.boxShadow = '';
                        }
                    }, 2000);
                }

                Swal.fire({
                    icon: 'warning',
                    title: 'Gagal',
                    text: err.message || 'Tidak dapat memproses absensi.'
                });
            })
            .finally(() => {
                // Add small delay before unlocking scanner
                setTimeout(() => { isProcessingScan = false; }, 2000);
            });
        }

        function onScanMatch(decodedText) {
            if (isProcessingScan) return;
            isProcessingScan = true;

            const decodedResult = decodedText.trim();
            
            // Visual feedback INSTANTLY without blocking Swal
            const scannerFrame = document.querySelector('.scanner-frame');
            if(scannerFrame) {
                scannerFrame.style.borderColor = '#3b82f6'; // Blue for processing
                scannerFrame.style.boxShadow = '0 0 50px rgba(59, 130, 246, 0.5)';
            }

            processScanOnServer(decodedResult);
        }

        // Finish Session Confirmation
        document.querySelector('.btn-finish-premium').addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');

            Swal.fire({
                title: 'Selesaikan Sesi?',
                text: "Pastikan semua data absensi sudah benar sebelum mengakhiri pelajaran.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Selesai!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-4',
                    confirmButton: 'rounded-pill px-4',
                    cancelButton: 'rounded-pill px-4'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    </script>
    @endpush
@endsection
