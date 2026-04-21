@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            {{-- /* header informasi kelas */ --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-body p-4 bg-primary text-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="fw-bold mb-1">Input Absensi Manual</h4>
                            <p class="mb-0 opacity-75">Kelas: <span class="fw-extrabold text-info">{{ $rombongan->nama_kelas }}</span> | Jurusan: {{ $rombongan->jurusan }}</p>
                        </div>
                        <div class="bg-white bg-opacity-20 p-3 rounded-4">
                            <i class="bi bi-person-check fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('absensi.manual-store') }}" method="POST">
                @csrf
                <input type="hidden" name="rombongan_belajar_id" value="{{ $rombongan->id }}">
                
                {{-- /* tabel input absensi massal */ --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="row align-items-center mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted small uppercase ls-1">Tanggal Absensi</label>
                                <input type="date" name="tanggal" class="form-control rounded-3 border-light bg-light" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-8 text-md-end mt-3 mt-md-0 d-flex flex-column align-items-md-end gap-3">
                                <div class="d-flex gap-2">
                                    <button type="button" id="open-scanner" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold shadow-sm">
                                        <i class="bi bi-qr-code-scan me-1"></i>Buka Scanner
                                    </button>
                                    <button type="button" id="set-all-hadir" class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-sm">
                                        <i class="bi bi-check-all me-1"></i>Set Semua Hadir
                                    </button>
                                </div>
                                <div class="d-inline-flex gap-2 flex-wrap justify-content-end">
                                    <span class="badge bg-success-soft text-success px-3 py-2 rounded-pill">H = Hadir</span>
                                    <span class="badge bg-info-soft text-info px-3 py-2 rounded-pill">I = Izin</span>
                                    <span class="badge bg-warning-soft text-warning px-3 py-2 rounded-pill">S = Sakit</span>
                                    <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill">D = Dispen</span>
                                    <span class="badge bg-danger-soft text-danger px-3 py-2 rounded-pill">A = Alfa</span>
                                    <span class="badge bg-amber-soft text-amber px-3 py-2 rounded-pill">T = Terlambat</span>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 border-0 rounded-start" style="width: 50px;">No</th>
                                        <th class="border-0">Nama Siswa</th>
                                        <th class="border-0 text-center" style="width: 80px;">H</th>
                                        <th class="border-0 text-center" style="width: 80px;">I</th>
                                        <th class="border-0 text-center" style="width: 80px;">S</th>
                                        <th class="border-0 text-center" style="width: 80px;">D</th>
                                        <th class="border-0 text-center" style="width: 80px;">A</th>
                                        <th class="border-0 text-center" style="width: 80px;">T</th>
                                        <th class="border-0 pe-4 rounded-end">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($siswa as $index => $s)
                                    <tr id="row_{{ $s->id }}" data-qr="{{ $s->qr_code }}">
                                        <td class="ps-4 fw-bold text-muted">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary-soft text-primary rounded-circle me-3 d-flex align-items-center justify-content-center fw-bold">
                                                    {{ substr($s->nama, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $s->nama }}</div>
                                                    <div class="small text-muted">NIS: {{ $s->nis }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" class="btn-check" name="attendance[{{ $s->id }}]" id="h_{{ $s->id }}" value="hadir">
                                            <label class="btn btn-outline-success border-2 rounded-circle btn-sm p-1" style="width: 32px; height: 32px;" for="h_{{ $s->id }}"></label>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" class="btn-check" name="attendance[{{ $s->id }}]" id="i_{{ $s->id }}" value="izin">
                                            <label class="btn btn-outline-info border-2 rounded-circle btn-sm p-1" style="width: 32px; height: 32px;" for="i_{{ $s->id }}"></label>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" class="btn-check" name="attendance[{{ $s->id }}]" id="s_{{ $s->id }}" value="sakit">
                                            <label class="btn btn-outline-warning border-2 rounded-circle btn-sm p-1" style="width: 32px; height: 32px;" for="s_{{ $s->id }}"></label>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" class="btn-check" name="attendance[{{ $s->id }}]" id="d_{{ $s->id }}" value="dispen">
                                            <label class="btn btn-outline-primary border-2 rounded-circle btn-sm p-1" style="width: 32px; height: 32px;" for="d_{{ $s->id }}"></label>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" class="btn-check" name="attendance[{{ $s->id }}]" id="a_{{ $s->id }}" value="alfa" checked>
                                            <label class="btn btn-outline-danger border-2 rounded-circle btn-sm p-1" style="width: 32px; height: 32px;" for="a_{{ $s->id }}"></label>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" class="btn-check" name="attendance[{{ $s->id }}]" id="t_{{ $s->id }}" value="terlambat">
                                            <label class="btn btn-outline-amber border-2 rounded-circle btn-sm p-1" style="width: 32px; height: 32px;" for="t_{{ $s->id }}"></label>
                                        </td>
                                        <td class="pe-4">
                                            <input type="text" name="notes[{{ $s->id }}]" class="form-control form-control-sm rounded-3 border-light bg-light" placeholder="Opsi...">
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <img src="https://illustrations.popsy.co/blue/abstract-art-4.svg" style="height: 150px;" class="mb-3">
                                            <p class="text-muted">Tidak ada siswa terdaftar di kelas ini.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 bg-light">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <a href="{{ route('guru.dashboard') }}" class="btn btn-light rounded-pill px-4 fw-bold">
                            <i class="bi bi-arrow-left me-2"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                            <i class="bi bi-cloud-arrow-up me-2"></i>Simpan Laporan Absensi
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- /* modal scanner qr manual */ --}}
<div class="modal fade" id="scannerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 30px; overflow: hidden;">
            <div class="modal-header bg-primary text-white border-0 p-4 text-center d-block position-relative">
                <h5 class="modal-title fw-extrabold mb-0">
                    <i class="bi bi-qr-code-scan me-2"></i>Scanner Absensi
                </h5>
                <p class="small mb-0 opacity-75">Scan QR Siswa untuk otomatis Hadir</p>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 top-50 translate-middle-y me-4" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div id="reader-manual" style="width: 100%; height: 350px; background: #000;"></div>
                <div id="scan-feedback" class="p-3 text-center d-none bg-emerald text-white fw-bold">
                    Siswa Berhasil Ditemukan!
                </div>
            </div>
            <div class="modal-footer border-0 p-4 bg-light">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup Scanner</button>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-success-soft { background-color: rgba(25, 135, 84, 0.1); }
    .bg-info-soft { background-color: rgba(13, 202, 240, 0.1); }
    .bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); }
    .bg-danger-soft { background-color: rgba(220, 53, 69, 0.1); }
    .bg-primary-soft { background-color: rgba(0, 97, 255, 0.1); }
    .bg-amber-soft { background-color: rgba(245, 158, 11, 0.1); }
    .text-amber { color: #f59e0b; }
    
    .ls-1 { letter-spacing: 1px; }
    .fw-extrabold { font-weight: 800; }
    
    .btn-check:checked + .btn-outline-success { background-color: #198754; color: white !important; }
    .btn-check:checked + .btn-outline-info { background-color: #0dcaf0; color: white !important; }
    .btn-check:checked + .btn-outline-warning { background-color: #ffc107; color: white !important; }
    .btn-check:checked + .btn-outline-primary { background-color: #0061ff; color: white !important; }
    .btn-check:checked + .btn-outline-danger { background-color: #dc3545; color: white !important; }
    .btn-check:checked + .btn-outline-amber { background-color: #f59e0b; color: white !important; }
    
    .table > :not(caption) > * > * {
        background-color: transparent;
        border-bottom-color: rgba(0,0,0,0.03);
    }

    #reader-manual video {
        object-fit: cover !important;
        height: 350px !important;
        width: 100% !important;
    }
    
    #reader-manual__scan_region {
        background: #000;
    }

    .bg-emerald { background-color: #10b981; }
</style>
@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let html5QrCodeManual = null;

    document.getElementById('open-scanner').addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('scannerModal'));
        modal.show();
        
        document.getElementById('scannerModal').addEventListener('shown.bs.modal', function() {
            startManualScanner();
        }, { once: true });
    });

    function startManualScanner() {
        if (html5QrCodeManual) {
            html5QrCodeManual.stop().catch(() => {});
        }

        html5QrCodeManual = new Html5Qrcode("reader-manual");
        html5QrCodeManual.start(
            { facingMode: "environment" },
            { fps: 15, qrbox: { width: 250, height: 250 } },
            onScanSuccessManual
        ).catch(err => {
            console.error(err);
            Swal.fire('Error', 'Kamera tidak dapat diakses', 'error');
        });
    }

    function onScanSuccessManual(decodedText) {
        // Find row with this QR
        const rows = document.querySelectorAll('tr[data-qr]');
        let found = false;
        
        rows.forEach(row => {
            if (row.dataset.qr === decodedText) {
                const siswaId = row.id.replace('row_', '');
                const radioHadir = document.getElementById('h_' + siswaId);
                
                if (radioHadir) {
                    radioHadir.checked = true;
                    found = true;
                    
                    // Visual feedback in modal
                    const feedback = document.getElementById('scan-feedback');
                    feedback.classList.remove('d-none');
                    setTimeout(() => feedback.classList.add('d-none'), 1500);
                    
                    // Vibration
                    if (window.navigator && window.navigator.vibrate) {
                        window.navigator.vibrate(100);
                    }
                    
                    // Highlight row in background (optional)
                    row.style.backgroundColor = 'rgba(25, 135, 84, 0.1)';
                    setTimeout(() => row.style.backgroundColor = '', 2000);
                }
            }
        });

        if (!found) {
            // Toast for not found
            Swal.fire({
                icon: 'warning',
                title: 'Tidak Ditemukan',
                text: 'Siswa tidak terdaftar di kelas ini.',
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
    }

    document.getElementById('scannerModal').addEventListener('hidden.bs.modal', function() {
        if (html5QrCodeManual) {
            html5QrCodeManual.stop().catch(() => {});
        }
    });

    document.getElementById('set-all-hadir').addEventListener('click', function() {
        const radioHadir = document.querySelectorAll('input[type="radio"][value="hadir"]');
        radioHadir.forEach(radio => {
            radio.checked = true;
        });
        
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: 'Seluruh siswa telah diset Hadir.',
            timer: 1500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    });
</script>
@endpush
@endsection
