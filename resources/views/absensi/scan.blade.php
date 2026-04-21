@extends('layouts.app')

@section('title', 'Smart Scanner Presensi')

@push('styles')
<style>
    :root {
        --primary: #4f46e5;
        --primary-glow: rgba(79, 70, 229, 0.4);
        --accent: #0ea5e9;
        --glass: rgba(255, 255, 255, 0.85);
        --dark-glass: rgba(15, 23, 42, 0.9);
    }

    .scanner-container {
        min-height: calc(100vh - 120px);
        display: flex;
        flex-direction: column;
        gap: 24px;
        padding-bottom: 40px;
    }

    /* Enhanced Hero Scanner Section */
    .scanner-hero {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
        border-radius: 40px;
        padding: 40px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.1);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }

    .scanner-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, var(--primary-glow) 0%, transparent 70%);
        z-index: 0;
    }

    .scanner-viewport-wrapper {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 450px;
        margin: 0 auto;
    }

    #reader {
        width: 100%;
        border-radius: 32px;
        overflow: hidden !important;
        border: 4px solid rgba(255,255,255,0.1);
        background: #000;
        box-shadow: 0 0 0 8px rgba(255,255,255,0.05);
        aspect-ratio: 1/1;
    }

    #reader video {
        object-fit: cover !important;
    }

    /* High-Tech Overlay */
    .scan-overlay {
        position: absolute;
        inset: 0;
        pointer-events: none;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .scan-frame {
        width: 70%;
        height: 70%;
        border: 2px solid rgba(255,255,255,0.2);
        border-radius: 30px;
        position: relative;
    }

    .frame-corner {
        position: absolute;
        width: 40px;
        height: 40px;
        border: 4px solid var(--accent);
        filter: drop-shadow(0 0 8px var(--accent));
    }
    .tl { top: -4px; left: -4px; border-right: 0; border-bottom: 0; border-top-left-radius: 20px; }
    .tr { top: -4px; right: -4px; border-left: 0; border-bottom: 0; border-top-right-radius: 20px; }
    .bl { bottom: -4px; left: -4px; border-right: 0; border-top: 0; border-bottom-left-radius: 20px; }
    .br { bottom: -4px; right: -4px; border-left: 0; border-top: 0; border-bottom-right-radius: 20px; }

    .scan-laser {
        position: absolute;
        left: 5%;
        right: 5%;
        height: 3px;
        background: linear-gradient(90deg, transparent, var(--accent), transparent);
        box-shadow: 0 0 15px var(--accent);
        top: 20%;
        animation: laser-move 3s infinite ease-in-out;
    }

    @keyframes laser-move {
        0%, 100% { top: 15%; opacity: 0; }
        50% { top: 85%; opacity: 1; }
    }

    /* Controls */
    .control-buttons {
        display: flex;
        justify-content: center;
        gap: 16px;
        margin-top: 24px;
    }

    .btn-scanner-action {
        width: 56px;
        height: 56px;
        border-radius: 18px;
        background: var(--dark-glass);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.1);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-scanner-action:hover {
        transform: translateY(-4px);
        background: var(--primary);
        border-color: var(--primary);
        box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
    }

    /* Recent Scans List */
    .recent-scans-card {
        background: var(--glass);
        backdrop-filter: blur(12px);
        border-radius: 32px;
        border: 1px solid rgba(255,255,255,0.5);
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
    }

    .scan-item {
        padding: 16px 24px;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.3s ease;
    }

    .scan-item:last-child { border-bottom: none; }

    .student-avatar {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: var(--primary-glow);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 18px;
    }

    .status-indicator {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 8px #10b981;
    }

    /* Summary Stats */
    .scan-stats {
        display: flex;
        gap: 12px;
        margin-top: 24px;
    }

    .stat-pill {
        flex: 1;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        padding: 12px;
        border-radius: 20px;
        text-align: center;
    }

    @media (max-width: 768px) {
        .scanner-hero { padding: 30px 20px; border-radius: 30px; }
        .scanner-container { padding: 10px; }
    }
</style>
@endpush

@section('content')
<div class="scanner-container">
    {{-- Header Content --}}
    <div class="d-flex align-items-center justify-content-between mb-2">
        <div>
            <h4 class="fw-extrabold text-dark mb-0">Portal Presensi Cerdas</h4>
            <p class="text-muted small fw-medium mb-0">Guru: <span class="text-primary">{{ Auth::guard('guru')->user()->nama }}</span></p>
        </div>
        <a href="{{ route('guru.dashboard') }}" class="btn btn-light rounded-pill px-4 btn-sm fw-bold">
            <i class="bi bi-x-lg me-2"></i> Tutup
        </a>
    </div>

    <div class="row g-4">
        {{-- /* viewport scanner qr harian */ --}}
        <div class="col-lg-6">
            <div class="scanner-hero">
                <div class="text-center mb-4 position-relative z-1">
                    <span class="badge bg-primary bg-opacity-20 text-white border border-white border-opacity-20 px-3 py-2 rounded-pill mb-3 ls-1 fw-bold" style="font-size: 10px;">
                        LIVE SCANNING ACTIVE
                    </span>
                    <h2 class="text-white fw-extrabold ls-tight">Arahkan Kamera</h2>
                    <p class="text-white text-opacity-60 small" id="scan-status-text">MENUNGGU KAMERA...</p>
                </div>

                <div class="scanner-viewport-wrapper">
                    <div id="reader"></div>
                    <div class="scan-overlay">
                        <div class="scan-frame">
                            <div class="frame-corner tl"></div>
                            <div class="frame-corner tr"></div>
                            <div class="frame-corner bl"></div>
                            <div class="frame-corner br"></div>
                            <div class="scan-laser"></div>
                        </div>
                    </div>
                </div>

                <div class="control-buttons position-relative z-1">
                    <button id="switchCamera" class="btn-scanner-action" title="Ganti Kamera">
                        <i class="bi bi-camera-rotate"></i>
                    </button>
                    <button class="btn-scanner-action" onclick="location.reload()" title="Refresh">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                    <button class="btn-scanner-action" id="torchToggle" title="Flashlight">
                        <i class="bi bi-flashlight"></i>
                    </button>
                </div>

                <div class="scan-stats position-relative z-1">
                    <div class="stat-pill">
                        <div class="text-white-50 small fw-bold">TOTAL SCAN</div>
                        <div class="text-white fw-extrabold fs-4" id="sess-total">0</div>
                    </div>
                    <div class="stat-pill">
                        <div class="text-white-50 small fw-bold">TERAKHIR</div>
                        <div class="text-white fw-bold small" id="sess-last">-</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- /* list aktivitas pindaian terbaru */ --}}
        <div class="col-lg-6">
            <div class="recent-scans-card h-100">
                <div class="p-4 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="fw-extrabold text-dark mb-0">Aktivitas Terbaru Hari Ini</h5>
                    <i class="bi bi-clock-history text-primary"></i>
                </div>
                <div id="recent-scans-list" style="max-height: 500px; overflow-y: auto;">
                    {{-- Virtual list placeholder or server-side rendered initial items --}}
                    @forelse($recent_absensi ?? [] as $abs)
                    <div class="scan-item">
                        <div class="student-avatar">{{ substr($abs->siswa->nama, 0, 1) }}</div>
                        <div class="flex-grow-1">
                            <div class="fw-extrabold text-dark" style="font-size: 14px;">{{ $abs->siswa->nama }}</div>
                            <div class="text-muted" style="font-size: 11px;">NIS: {{ $abs->siswa->nis }} • {{ $abs->created_at->format('H:i') }}</div>
                        </div>
                        <div class="status-indicator"></div>
                    </div>
                    @empty
                    <div class="p-5 text-center text-muted">
                        <i class="bi bi-cloud-slash display-4 d-block mb-3 opacity-20"></i>
                        <p class="fw-bold small">Belum ada data pindaian dalam sesi ini.</p>
                    </div>
                    @endforelse
                </div>
                <div class="p-4 bg-light bg-opacity-50 border-top text-center" style="border-radius: 0 0 32px 32px;">
                    <a href="{{ route('absensi.index') }}" class="text-decoration-none small fw-bold text-primary">LIHAT SEMUA LOG ABSENSI <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 
    FUNGSI: Formulir Tersembunyi (Hidden Form) 
    Tujuan: HTML Form ini tidak terlihat di layar, tapi digunakan oleh Javascript 
            untuk mengirimkan hasil bacaan kamera (teks QR) ke Controller Laravel
--}}
<form id="scanForm" action="{{ route('absensi.prosesScan') }}" method="POST" class="d-none">
    @csrf
    <!-- Input ini akan diisi secara otomatis oleh Javascript saat QR Code terdeteksi -->
    <input type="hidden" name="qr_code" id="qr_input">
</form>

{{-- Audio Feedback --}}
<audio id="beepReady" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>
<audio id="beepSuccess" src="https://assets.mixkit.co/active_storage/sfx/2218/2218-preview.mp3" preload="auto"></audio>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Inisialisasi Pustaka Scanner
        // Tujuan: Mengubah div dengan id="reader" menjadi layar tampilan kamera
        const html5QrCode = new Html5Qrcode("reader");
        let isScanning = false;
        let scanCount = 0;

        // Pengaturan Kamera dan Kotak Fokus (QR Box)
        const qrConfig = { 
            fps: 20, 
            qrbox: (viewWidth, viewHeight) => {
                const boxSize = Math.min(viewWidth, viewHeight) * 0.7;
                return { width: boxSize, height: boxSize };
            },
            aspectRatio: 1.0,
            showTorchButtonIfSupported: true
        };

        // Fungsi Memutar Suara / Bunyi Beep
        function playSound(id) {
            const sound = document.getElementById(id);
            if(sound) {
                sound.currentTime = 0;
                sound.play().catch(e => console.log("Audio play blocked"));
            }
        }

        /**
         * FUNGSI: Aksi Jika QR Code Berhasil Dibaca (onScanSuccess)
         * Tujuan: Mengambil teks hasil bacaan kamera, menaruhnya di form tersembunyi, 
         *         dan mengirimnya langsung ke Controller tanpa diklik manual.
         */
        function onScanSuccess(decodedText) {
            if (isScanning) return;
            isScanning = true; // Mencegah scan berulang (SPAM)
            
            // Memutar suara sukses dan memberikan getaran pada HP
            playSound('beepSuccess');
            if (navigator.vibrate) navigator.vibrate([100, 50, 100]);

            const frame = document.querySelector('.scan-frame');
            frame.style.borderColor = '#10b981';
            frame.style.boxShadow = '0 0 40px rgba(16, 185, 129, 0.6)';

            document.getElementById('scan-status-text').innerHTML = "VERIFIKASI...";

            // A. Memasukkan teks yang dibaca kamera ke input form tersembunyi
            document.getElementById('qr_input').value = decodedText;
            
            // B. Mengirim (Submit) formulir ke alamat route('absensi.prosesScan')
            document.getElementById('scanForm').submit();
        }

        /**
         * FUNGSI: Menghidupkan Kamera (startScanner)
         * Tujuan: Meminta izin ke perangkat untuk menyalakan kamera belakang 
         *         dan memulai proses pencarian QR Code.
         */
        function startScanner() {
            html5QrCode.start(
                { facingMode: "environment" }, // Prioritaskan Kamera Belakang
                qrConfig, 
                onScanSuccess
            ).then(() => {
                isScanning = false;
                playSound('beepReady');
                document.getElementById('scan-status-text').innerHTML = "SCANNER AKTIF";
            }).catch(err => {
                console.error("Camera Error:", err);
                // Fallback (Alternatif) jika kamera belakang gagal diakses, cari kamera lain yang tersedia
                Html5Qrcode.getCameras().then(devices => {
                    const cameraId = devices.length > 0 ? devices[devices.length - 1].id : null;
                    if (cameraId) html5QrCode.start(cameraId, qrConfig, onScanSuccess);
                }).catch(e => {
                    Swal.fire({
                        title: 'Akses Kamera Ditolak!',
                        text: 'Browser memblokir kamera. Jika Anda menggunakan HP melalui WiFi lokal (HTTP), browser (Google Chrome/Safari) menolaknya demi keamanan. Gunakan HTTPS, atau buka chrome://flags di HP dan cari "insecure origins treated as secure", lalu tambahkan IP ini.',
                        icon: 'error',
                        confirmButtonText: 'Mengerti'
                    });
                    document.getElementById('scan-status-text').innerHTML = "AKSES KAMERA DITOLAK BROWSER";
                });
            });
        }

        // Jalankan pelacakan kamera secara otomatis ketika halaman selesai dimuat.
        startScanner();

        // Control handlers
        document.getElementById('switchCamera').addEventListener('click', function() {
            html5QrCode.stop().then(() => startScanner());
        });
    });
</script>
@endpush

