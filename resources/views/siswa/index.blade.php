@extends('layouts.app')

@section('title', 'Data Siswa')

@section('content')
<div class="content-header fade-in">
    <div class="container-fluid px-4">
        <div class="row mb-4 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark fw-extrabold display-6 ls-1"><i class="bi bi-people-fill text-primary me-3"></i>Manajemen Siswa</h1>
                <p class="text-muted mt-2 fw-medium">Database siswa lengkap dengan sistem identifikasi QR Code.</p>
            </div>
            <div class="col-sm-6 text-md-end mt-3 mt-md-0">
                <div class="d-flex flex-wrap justify-content-md-end gap-2">
                    <a href="{{ route('siswa.sync') }}" class="btn btn-outline-primary btn-lg rounded-4 px-4 hover-up">
                        <i class="bi bi-arrow-repeat me-2"></i> Sinkron API
                    </a>
                    <a href="{{ route('siswa.create') }}" class="btn btn-primary btn-lg rounded-4 shadow-lg px-4 hover-up">
                        <i class="bi bi-person-plus-fill me-2"></i> Tambah Siswa
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4 fade-in-delayed">
    {{-- Search & Filter --}}
    <div class="card border-0 shadow-sm rounded-4 mb-5 overflow-hidden">
        <div class="card-body p-4 p-md-5" style="background: linear-gradient(to right, #ffffff, var(--light-blue));">
            <form method="GET" class="row g-4 align-items-end">
                <div class="col-lg-8">
                    <label class="form-label fw-bold text-dark small text-uppercase ls-1">Cari Data Siswa</label>
                    <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden border">
                        <span class="input-group-text bg-white border-0 ps-4"><i class="bi bi-search text-primary"></i></span>
                        <input type="text" name="search" class="form-control border-0 py-3 fs-6" 
                               placeholder="Ketik Nama atau NIS Siswa..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <button class="btn btn-primary btn-lg w-100 rounded-4 py-3 fw-bold shadow-sm" type="submit">
                        <i class="bi bi-filter me-2"></i> Cari
                    </button>
                </div>
                <div class="col-lg-2 col-6">
                    <a href="{{ route('siswa.index') }}" class="btn btn-light btn-lg w-100 rounded-4 py-3 fw-bold border">
                        <i class="bi bi-x-lg me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="card border-0 shadow-sm overflow-hidden rounded-4 mb-5">
        <div class="card-header bg-white py-4 px-4 border-bottom border-light">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">Daftar Aktif Siswa</h5>
                <span class="badge bg-blue-soft text-primary px-3 py-2 rounded-3 border border-primary border-opacity-10">Total: {{ $siswa->total() }} Siswa</span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted">
                    <tr>
                        <th class="ps-4 py-3 small fw-bold text-uppercase ls-1" style="width:120px">NIS</th>
                        <th class="py-3 small fw-bold text-uppercase ls-1">IDENTITAS SISWA</th>
                        <th class="py-3 small fw-bold text-uppercase ls-1 text-center">GENDER</th>
                        <th class="py-3 small fw-bold text-uppercase ls-1 text-center">KONTAK</th>
                        <th class="py-3 small fw-bold text-uppercase ls-1 text-center">QR CODE</th>
                        <th class="pe-4 py-3 text-end small fw-bold text-uppercase ls-1" style="width:150px">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($siswa as $item)
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-light text-dark border px-3 py-2 rounded-3 fw-bold fs-6">{{ $item->nis }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-blue-soft text-primary rounded-4 d-flex align-items-center justify-content-center me-3 shadow-sm" style="width:48px;height:48px">
                                    <i class="bi bi-person-fill fs-4"></i>
                                </div>
                                <div>
                                    <div class="fw-extrabold text-dark fs-6">{{ $item->nama }}</div>
                                    <div class="text-muted small">Anggota Kelas Aktif</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            @if($item->jenis_kelamin == 'L')
                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill small fw-bold">
                                    <i class="bi bi-gender-male me-1"></i> Laki-laki
                                </span>
                            @else
                                <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill small fw-bold">
                                    <i class="bi bi-gender-female me-1"></i> Perempuan
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="text-muted small fw-medium">{{ $item->no_hp ?? '-' }}</span>
                        </td>
                        <td class="text-center">
                            <div class="qr-preview-box shadow-sm mx-auto" 
                                 onclick="showQR(`{!! base64_encode(QrCode::size(300)->generate($item->qr_code ?? 'fallback')) !!}`, '{{ $item->nama }}')">
                                {!! QrCode::size(40)->generate($item->qr_code ?? 'fallback') !!}
                                <div class="qr-overlay"><i class="bi bi-zoom-in"></i></div>
                            </div>
                        </td>
                        <td class="pe-4 text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('siswa.edit', $item->id) }}" class="btn btn-sm btn-light text-primary rounded-3 border p-2" title="Edit Siswa">
                                    <i class="bi bi-pencil-fill fs-5"></i>
                                </a>
                                <form action="{{ route('siswa.destroy', $item->id) }}" method="POST" class="d-inline confirm-delete-form">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-light text-danger rounded-3 border p-2 confirm-delete" title="Hapus Siswa">
                                        <i class="bi bi-trash3-fill fs-5"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="py-5">
                                <i class="bi bi-people fs-1 text-muted opacity-25 d-block mb-3 display-1"></i>
                                <h4 class="text-muted fw-bold">Data Siswa Kosong</h4>
                                <p class="text-muted">Database siswa belum terisi atau tidak ditemukan.</p>
                                <a href="{{ route('siswa.sync') }}" class="btn btn-outline-primary rounded-pill px-4 mt-2">Sinkron Sekarang</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($siswa->hasPages())
        <div class="card-footer bg-white border-0 py-4 px-4 border-top">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div class="text-muted small fw-medium">
                    Menampilkan <span class="text-dark fw-bold">{{ $siswa->firstItem() }}</span> - <span class="text-dark fw-bold">{{ $siswa->lastItem() }}</span> dari <span class="text-dark fw-bold">{{ $siswa->total() }}</span> siswa
                </div>
                {{ $siswa->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

{{-- QR Code Modal --}}
<div id="qrModal" class="qr-overlay-container" onclick="closeQR()">
    <div class="qr-modal-card" onclick="event.stopPropagation()">
        <div class="qr-header">
            <div>
                <h5 class="fw-bold text-dark mb-0">Identitas Digital</h5>
                <small id="qrStudentName" class="text-primary fw-bold"></small>
            </div>
            <button class="btn btn-light rounded-circle p-2" onclick="closeQR()"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="qr-modal-body">
            <img id="qrPreview" src="" class="img-fluid rounded-4 shadow-sm border p-3 bg-white">
            <p class="text-muted small mt-4 mb-0 text-center lh-sm">
                Arahkan QR Code ini ke kamera alat scan untuk melakukan absensi otomatis.
            </p>
        </div>
        <div class="qr-footer mt-4">
            <button class="btn btn-primary w-100 py-3 rounded-4 fw-bold" onclick="window.print()">
                <i class="bi bi-printer-fill me-2"></i> Cetak Kartu QR
            </button>
        </div>
    </div>
</div>

<style>
    .fw-extrabold { font-weight: 800; }
    .ls-1 { letter-spacing: 0.5px; }
    .bg-blue-soft { background-color: #eff6ff; }
    .hover-up:hover { transform: translateY(-3px); box-shadow: 0 10px 20px -5px rgba(0,0,0,0.1) !important; transition: all 0.3s ease; }
    
    /* QR Preview Layout */
    .qr-preview-box {
        width: 54px;
        height: 54px;
        padding: 5px;
        background: white;
        border-radius: 12px;
        border: 1px dashed var(--soft-blue);
        cursor: pointer;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .qr-preview-box:hover { transform: scale(1.1); border-color: var(--primary-blue); }
    .qr-overlay {
        position: absolute;
        inset: 0;
        background: rgba(37, 99, 235, 0.8);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: 0.3s;
    }
    .qr-preview-box:hover .qr-overlay { opacity: 1; }

    /* Modal Styling */
    .qr-overlay-container {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(15, 23, 42, 0.8);
        backdrop-filter: blur(12px);
        justify-content: center;
        align-items: center;
        padding: 20px;
    }
    .qr-modal-card {
        background: white;
        padding: 40px;
        border-radius: 32px;
        max-width: 400px;
        width: 100%;
        box-shadow: 0 50px 100px -20px rgba(0,0,0,0.5);
        animation: modalFadeUp 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .qr-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px; }
    .qr-modal-body { text-align: center; }
    
    @keyframes modalFadeUp {
        from { transform: translateY(30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    @media (max-width: 576px) {
        .qr-modal-card { padding: 25px; border-radius: 20px; margin: 15px; }
        .qr-header { margin-bottom: 20px; }
        .qr-modal-body img { max-width: 200px; }
    }

    .pagination .page-link { border: none; padding: 0.6rem 1rem; margin: 0 3px; border-radius: 12px !important; color: #64748b; font-weight: 600; }
    .pagination .page-item.active .page-link { background-color: var(--primary-blue); color: white; shadow: 0 4px 10px rgba(37, 99, 235, 0.2); }
</style>

<script>
function showQR(base64, name) {
    document.getElementById('qrPreview').src = "data:image/svg+xml;base64," + base64;
    document.getElementById('qrStudentName').innerText = name;
    document.getElementById('qrModal').style.display = "flex";
}

function closeQR() {
    document.getElementById('qrModal').style.display = "none";
}

document.querySelectorAll('.confirm-delete').forEach(button => {
    button.addEventListener('click', function() {
        Swal.fire({
            title: 'Hapus Data Siswa?',
            text: "Data yang dihapus tidak dapat dipulihkan kembali.",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus Data',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                this.closest('form').submit();
            }
        })
    });
});
</script>
@endsection
