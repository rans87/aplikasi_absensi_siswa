@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header border-0 p-4"
             style="background: linear-gradient(135deg, #0d6efd, #4dabf7);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1 fw-bold text-white">📘 Manajemen Siswa</h4>
                    <p class="text-white-50 mb-0 small">Kelola data siswa, NIS, dan QR Code dalam satu panel.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('siswa.sync') }}" class="btn btn-white-glass btn-sm px-3 fw-semibold">
                        <i class="bi bi-arrow-repeat me-1"></i> Sync API
                    </a>
                    <a href="{{ route('siswa.create') }}" class="btn btn-light btn-sm px-3 fw-semibold text-primary shadow-sm">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Siswa
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body bg-white p-4">
            @if(session('success'))
                <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            <form method="GET" class="mb-4">
                <div class="input-group search-box">
                    <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-0 py-2" 
                           placeholder="Cari berdasarkan nama atau NIS siswa..." value="{{ request('search') }}">
                    <button class="btn btn-primary px-4 shadow-sm" type="submit">Cari</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle custom-table">
                    <thead>
                        <tr>
                            <th class="py-3 px-3">NIS</th>
                            <th class="py-3">Nama Lengkap</th>
                            <th class="py-3 text-center">No HP</th>
                            <th class="py-3 text-center">Gender</th>
                            <th class="py-3 text-center">QR Code</th>
                            <th class="py-3 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($siswa as $item)
                        <tr>
                            <td class="px-3">
                                <span class="fw-bold text-dark">{{ $item->nis }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $item->nama }}</div>
                            </td>
                            <td class="text-center text-muted small">
                                {{ $item->no_hp ?? 'Tidak ada' }}
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill {{ $item->jenis_kelamin == 'L' ? 'bg-primary-subtle text-primary' : 'bg-info-subtle text-info' }} px-3">
                                    {{ $item->jenis_kelamin }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="qr-wrapper shadow-sm mx-auto" 
                                     onclick="showQR(`{!! base64_encode(QrCode::size(250)->generate($item->qr_code)) !!}`)">
                                    {!! QrCode::size(45)->generate($item->qr_code) !!}
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('siswa.edit', $item->id) }}" 
                                       class="btn btn-outline-warning btn-sm border-0 me-1" title="Edit">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <form action="{{ route('siswa.destroy', $item->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm border-0" 
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <img src="https://illustrations.popsy.co/blue/searching.svg" alt="Empty" style="width: 150px;" class="mb-3">
                                <p class="text-muted mb-0">Oops! Data siswa tidak ditemukan.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <small class="text-muted">Menampilkan {{ $siswa->count() }} data</small>
                {{ $siswa->links() }}
            </div>
        </div>
    </div>
</div>

<div id="qrModal" class="qr-modal" onclick="closeQR()">
    <div class="qr-modal-content" onclick="event.stopPropagation()">
        <div class="text-center mb-3">
            <h6 class="fw-bold text-dark mb-0">Preview QR Code</h6>
            <small class="text-muted small">Scan untuk identifikasi siswa</small>
        </div>
        <div class="qr-image-container mb-3">
            <img id="qrPreview" src="">
        </div>
        <button class="btn btn-secondary w-100 rounded-3" onclick="closeQR()">Tutup</button>
    </div>
</div>

<style>

        /* Mengecilkan ukuran icon jika masih muncul raksasa */
    .pagination svg {
        width: 1rem;
        height: 1rem;
    }

    /* Mempercantik tampilan pagination Bootstrap agar lebih bulat */
    .pagination .page-item .page-link {
        border: none;
        color: #0d6efd;
        margin: 0 2px;
        border-radius: 8px !important;
        padding: 8px 16px;
        transition: all 0.3s ease;
    }

    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        color: white;
        box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
    }

    .pagination .page-item.disabled .page-link {
        background-color: transparent;
        color: #ccc;
    }
    /* Google Fonts */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    body {
        font-family: 'Inter', sans-serif;
        background-color: #f8f9fa;
    }

    /* Custom Table Styling */
    .custom-table thead th {
        background-color: #f8faff;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: #6c757d;
        border-bottom: 2px solid #edf2f9;
    }

    .custom-table tbody tr {
        transition: all 0.2s ease;
    }

    .custom-table tbody tr:hover {
        background-color: #f1f7ff !important;
        transform: scale(1.002);
    }

    /* Button White Glass */
    .btn-white-glass {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.3);
        backdrop-filter: blur(4px);
    }
    .btn-white-glass:hover {
        background: rgba(255, 255, 255, 0.3);
        color: white;
    }

    /* Search Box */
    .search-box {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e9ecef;
    }

    /* QR Wrapper */
    .qr-wrapper {
        width: fit-content;
        padding: 5px;
        background: white;
        border-radius: 8px;
        cursor: zoom-in;
        transition: all 0.3s ease;
    }
    .qr-wrapper:hover {
        transform: rotate(3deg) scale(1.1);
    }

    /* Badge Custom */
    .bg-primary-subtle { background-color: #e7f1ff; color: #0d6efd; }
    .bg-info-subtle { background-color: #e1f5fe; color: #03a9f4; }

    /* Modal Styling */
    .qr-modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 1050;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(8px);
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .qr-modal-content {
        background: white;
        padding: 24px;
        border-radius: 24px;
        max-width: 320px;
        width: 100%;
        animation: slideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .qr-image-container {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 16px;
        display: flex;
        justify-content: center;
    }

    .qr-image-container img {
        width: 100%;
        height: auto;
    }

    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>

<script>
function showQR(base64) {
    document.getElementById('qrPreview').src = "data:image/svg+xml;base64," + base64;
    document.getElementById('qrModal').style.display = "flex";
}

function closeQR() {
    document.getElementById('qrModal').style.display = "none";
}
</script>
@endsection