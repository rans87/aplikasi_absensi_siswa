@extends('layouts.app')

@section('title', 'Scan QR Presensi')

@push('styles')
<style>
    :root {
        --primary-blue: #2563eb;
        --secondary-blue: #3b82f6;
        --soft-blue: #f1f5ff;
        --deep-blue: #1e3a8a;
    }

    .scanner-page {
        background-color: #f8fafc;
        min-height: calc(100vh - 100px);
        padding: 40px 0;
    }

    .scan-card {
        max-width: 500px;
        margin: 0 auto;
        border-radius: 35px;
        background: #ffffff;
        box-shadow: 0 40px 100px -20px rgba(37, 99, 235, 0.15);
        border: 1px solid rgba(37, 99, 235, 0.05);
        overflow: hidden;
        position: relative;
    }

    .scan-header {
        padding: 45px 30px 25px;
        text-align: center;
        background: radial-gradient(circle at top right, rgba(37, 99, 235, 0.03), transparent);
    }

    .pulse-avatar {
        width: 80px;
        height: 80px;
        background: var(--soft-blue);
        color: var(--primary-blue);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        margin: 0 auto 20px;
        position: relative;
        z-index: 1;
    }

    .pulse-avatar::after {
        content: '';
        position: absolute;
        inset: -5px;
        border-radius: 50%;
        border: 2px solid var(--primary-blue);
        opacity: 0.3;
        animation: pulse-ring 2s infinite;
    }

    @keyframes pulse-ring {
        0% { transform: scale(1); opacity: 0.3; }
        100% { transform: scale(1.5); opacity: 0; }
    }

    /* Scanner Viewport */
    .viewport-container {
        padding: 0 30px 40px;
    }

    #reader {
        width: 100%;
        border-radius: 28px;
        overflow: hidden;
        box-shadow: 0 20px 40px -10px rgba(0,0,0,0.1);
        border: 8px solid #ffffff;
        background: #0f172a;
        position: relative;
    }

    #reader video {
        object-fit: cover !important;
        transform: scale(1.1); /* Slight zoom for better focus area */
    }

    /* Modern Overlay */
    .scan-guide {
        position: absolute;
        inset: 40px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 20px;
        z-index: 10;
        pointer-events: none;
    }

    .scan-corner {
        position: absolute;
        width: 30px;
        height: 30px;
        border: 4px solid var(--primary-blue);
        z-index: 11;
    }
    .c-tl { top: -2px; left: -2px; border-right: 0; border-bottom: 0; border-top-left-radius: 12px; }
    .c-tr { top: -2px; right: -2px; border-left: 0; border-bottom: 0; border-top-right-radius: 12px; }
    .c-bl { bottom: -2px; left: -2px; border-right: 0; border-top: 0; border-bottom-left-radius: 12px; }
    .c-br { bottom: -2px; right: -2px; border-left: 0; border-top: 0; border-bottom-right-radius: 12px; }

    .laser-line {
        position: absolute;
        left: 10px;
        right: 10px;
        height: 2px;
        background: var(--primary-blue);
        box-shadow: 0 0 15px var(--primary-blue), 0 0 30px var(--primary-blue);
        top: 20%;
        z-index: 12;
        animation: scan-loop 3s infinite ease-in-out;
    }

    @keyframes scan-loop {
        0%, 100% { top: 20%; opacity: 0.1; }
        50% { top: 80%; opacity: 1; }
    }

    .status-badge {
        margin-top: 30px;
        padding: 14px 28px;
        border-radius: 50px;
        font-weight: 700;
        letter-spacing: 0.5px;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 10px 20px -5px rgba(0,0,0,0.05);
    }
    
    .st-idle { background: #f1f5f9; color: #64748b; }
    .st-active { background: #eff6ff; color: var(--primary-blue); border: 1px solid rgba(37, 99, 235, 0.1); }
    .st-ok { background: #ecfdf5; color: #059669; transform: scale(1.05); }

    .control-actions {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: -20px;
        position: relative;
        z-index: 20;
    }

    .btn-circle-pill {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        background: white;
        color: var(--deep-blue);
        border: none;
        box-shadow: 0 15px 30px -10px rgba(0,0,0,0.2);
        transition: 0.3s;
    }
    .btn-circle-pill:hover { transform: translateY(-5px) rotate(15deg); color: var(--primary-blue); }
    .btn-circle-pill:active { transform: scale(0.9); }
</style>
@endpush

@section('content')
<div class="scanner-page">
    <div class="container">
        {{-- Back Link --}}
        <div class="mb-5 text-center">
            <a href="{{ route('guru.dashboard') }}" class="text-decoration-none fw-bold text-muted hover-primary">
                <i class="bi bi-arrow-left-circle-fill me-2 fs-5"></i> Kembali ke Dashboard
            </a>
        </div>

        <div class="scan-card fade-in">
            <div class="scan-header">
                <div class="pulse-avatar">
                    <i class="bi bi-upc-scan"></i>
                </div>
                <h3 class="fw-extrabold text-dark mb-2">Smart Scanner</h3>
                <p class="text-muted fw-medium px-4">Posisikan Kode QR siswa tepat di dalam bingkai pindaian untuk absensi otomatis.</p>
            </div>

            <div class="viewport-container text-center">
                <div id="reader">
                    {{-- UI Decorator on top of video --}}
                    <div class="scan-guide">
                        <div class="scan-corner c-tl"></div>
                        <div class="scan-corner c-tr"></div>
                        <div class="scan-corner c-bl"></div>
                        <div class="scan-corner c-br"></div>
                        <div class="laser-line"></div>
                    </div>
                </div>

                <div class="control-actions">
                    <button id="switchCamera" class="btn-circle-pill" title="Putar Kamera">
                        <i class="bi bi-camera-rotate"></i>
                    </button>
                    <button id="btnRetry" class="btn-circle-pill" onclick="location.reload()" title="Refresh">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>

                <div id="scan-status" class="status-badge st-idle">
                    <div class="spinner-grow spinner-grow-sm me-2" role="status"></div>
                    Menginisialisasi Kamera...
                </div>
            </div>
            
            <div class="bg-light p-4 text-center border-top">
                <span class="text-muted small fw-bold text-uppercase ls-1">PresenceX v2.0 - Secure Attendance System</span>
            </div>
        </div>
    </div>
</div>

<form id="scanForm" action="{{ route('absensi.prosesScan') }}" method="POST" class="d-none">
    @csrf
    <input type="hidden" name="qr_code" id="qr_input">
</form>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    const html5QrCode = new Html5Qrcode("reader");
    let isProcessing = false;

    const qrConfig = { 
        fps: 20, 
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.0
    };

    function handleSuccess(decodedText) {
        if (isProcessing) return;
        isProcessing = true;

        const statusLabel = document.getElementById('scan-status');
        statusLabel.className = 'status-badge st-ok';
        statusLabel.innerHTML = '<i class="bi bi-check-all fs-5 me-2"></i> BERHASIL DIPINDAI';

        // Feedback Vibration if supported
        if (window.navigator && window.navigator.vibrate) {
            window.navigator.vibrate(100);
        }

        document.getElementById('qr_input').value = decodedText;
        
        Swal.fire({
            title: 'Berhasil!',
            text: 'Mencatat kehadiran siswa...',
            icon: 'success',
            showConfirmButton: false,
            timer: 1000,
            timerProgressBar: true,
            backdrop: `rgba(37, 99, 235, 0.1)`,
            willClose: () => {
                document.getElementById('scanForm').submit();
            }
        });
    }

    Html5Qrcode.getCameras().then(devices => {
        if (devices && devices.length > 0) {
            const cameraId = devices[devices.length - 1].id; // Back camera
            
            html5QrCode.start(cameraId, qrConfig, handleSuccess)
                .then(() => {
                    const statusLabel = document.getElementById('scan-status');
                    statusLabel.className = 'status-badge st-active';
                    statusLabel.innerHTML = '<i class="bi bi-broadcast me-2"></i> SCANNER AKTIF';
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('scan-status').innerHTML = "Gagal mengakses kamera.";
                });
        }
    });

    // Hover effect for back link
    document.querySelector('.hover-primary').addEventListener('mouseenter', function() {
        this.style.color = 'var(--primary-blue)';
    });
    document.querySelector('.hover-primary').addEventListener('mouseleave', function() {
        this.style.color = '#64748b';
    });
</script>
@endpush

