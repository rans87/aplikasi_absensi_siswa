@extends('layouts.app')

@section('title', 'Manajemen Tahun Ajar')

@section('content')
<div class="content-header fade-in">
    <div class="container-fluid px-4">
        <div class="row mb-4 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark fw-extrabold display-6 ls-1"><i class="bi bi-calendar-range-fill text-primary me-3"></i>Tahun Ajaran</h1>
                <p class="text-muted mt-2 fw-medium">Konfigurasi periode aktif tahun ajaran untuk sistem presensi sekolah.</p>
            </div>
            <div class="col-sm-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('tahun_ajar.create') }}" class="btn btn-primary btn-lg rounded-4 shadow-lg px-4 hover-up">
                    <i class="bi bi-calendar-plus-fill me-2"></i> Tambah Tahun Ajar
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4 fade-in-delayed">
    {{-- Search Card --}}
    <div class="card border-0 shadow-sm rounded-4 mb-5 overflow-hidden">
        <div class="card-body p-4 p-md-5" style="background: linear-gradient(to right, #ffffff, var(--light-blue));">
            <form method="GET" class="row g-4 align-items-end">
                <div class="col-lg-8">
                    <label class="form-label fw-bold text-dark small text-uppercase ls-1">Cari Tahun Ajaran</label>
                    <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden border">
                        <span class="input-group-text bg-white border-0 ps-4 text-primary"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-0 py-3 fs-6" 
                               placeholder="Contoh: 2024/2025..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <button class="btn btn-primary btn-lg w-100 rounded-4 py-3 fw-bold shadow-sm" type="submit">
                        <i class="bi bi-funnel-fill me-2"></i> Cari
                    </button>
                </div>
                <div class="col-lg-2 col-6">
                    <a href="{{ route('tahun_ajar.index') }}" class="btn btn-light btn-lg w-100 rounded-4 py-3 fw-bold border">
                        <i class="bi bi-x-circle me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Data Table Card --}}
    <div class="card border-0 shadow-sm overflow-hidden rounded-4 mb-5">
        <div class="card-header bg-white py-4 px-4 border-bottom border-light">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">Data Periode Sekolah</h5>
                <span class="badge bg-blue-soft text-primary px-3 py-2 rounded-3 border border-primary border-opacity-10">Tercatat: {{ $tahunAjar->total() }} Periode</span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted">
                    <tr>
                        <th class="ps-4 py-3 small fw-bold text-uppercase ls-1" style="width:70px">NO</th>
                        <th class="py-3 small fw-bold text-uppercase ls-1">PERIODE TAHUN</th>
                        <th class="py-3 small fw-bold text-uppercase ls-1 text-center">STATUS AKTIVITAS</th>
                        <th class="py-3 small fw-bold text-uppercase ls-1 d-none d-md-table-cell text-center">TANGGAL RILIS</th>
                        <th class="pe-4 py-3 text-end small fw-bold text-uppercase ls-1" style="width:120px">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tahunAjar as $item)
                    <tr>
                        <td class="ps-4 text-muted small fw-bold">{{ $loop->iteration + ($tahunAjar->currentPage() - 1) * $tahunAjar->perPage() }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-blue-soft text-primary rounded-4 d-flex align-items-center justify-content-center me-3 shadow-sm" style="width:48px;height:48px">
                                    <i class="bi bi-calendar3-range fs-4"></i>
                                </div>
                                <div>
                                    <div class="fw-extrabold text-dark fs-5">{{ $item->tahun }}</div>
                                    <div class="text-muted small">Tahun Pelajaran</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            @if($item->aktif)
                                <span class="badge bg-emerald-soft text-emerald px-4 py-2 rounded-pill fw-bold border border-success border-opacity-10">
                                    <i class="bi bi-check-circle-fill me-2"></i> PERIODE AKTIF
                                </span>
                            @else
                                <span class="badge bg-light text-muted px-4 py-2 rounded-pill fw-bold border">
                                    <i class="bi bi-dash-circle me-2"></i> NONAKTIF
                                </span>
                            @endif
                        </td>
                        <td class="d-none d-md-table-cell text-center">
                            <div class="text-muted small fw-bold">{{ $item->created_at->format('d M Y') }}</div>
                        </td>
                        <td class="pe-4 text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('tahun_ajar.edit', $item->id) }}" class="btn btn-sm btn-light text-primary rounded-3 border p-2" title="Edit Data">
                                    <i class="bi bi-pencil-square fs-5"></i>
                                </a>
                                <form action="{{ route('tahun_ajar.destroy', $item->id) }}" method="POST" class="d-inline delete-form">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-light text-rose rounded-3 border p-2 confirm-delete" title="Hapus Data">
                                        <i class="bi bi-trash-fill fs-5"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="py-5">
                                <i class="bi bi-calendar-x fs-1 text-muted opacity-25 d-block mb-3 display-1"></i>
                                <h4 class="text-muted fw-bold">Belum Ada Tahun Ajar</h4>
                                <p class="text-muted">Siapkan data tahun ajaran baru untuk memulai operasional.</p>
                                <a href="{{ route('tahun_ajar.create') }}" class="btn btn-primary rounded-pill px-4 mt-2">Buat Sekarang</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tahunAjar->hasPages())
        <div class="card-footer bg-white border-0 py-4 px-4 border-top">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div class="text-muted small fw-medium">
                    Menampilkan <span class="text-dark fw-bold">{{ $tahunAjar->firstItem() }}</span> - <span class="text-dark fw-bold">{{ $tahunAjar->lastItem() }}</span> dari <span class="text-dark fw-bold">{{ $tahunAjar->total() }}</span> periode
                </div>
                {{ $tahunAjar->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<style>
    .fw-extrabold { font-weight: 800; }
    .ls-1 { letter-spacing: 0.5px; }
    .text-emerald { color: #059669; }
    .bg-emerald-soft { background-color: #ecfdf5; }
    .text-rose { color: #e11d48; }
    .bg-blue-soft { background-color: #eff6ff; }
    .hover-up:hover { transform: translateY(-3px); box-shadow: 0 10px 20px -5px rgba(0,0,0,0.1) !important; transition: all 0.3s ease; }
    
    .pagination .page-link { border: none; padding: 0.6rem 1rem; margin: 0 3px; border-radius: 12px !important; color: #64748b; font-weight: 600; }
    .pagination .page-item.active .page-link { background-color: var(--primary-blue); color: white; shadow: 0 4px 10px rgba(37, 99, 235, 0.2); }
</style>

<script>
    document.querySelectorAll('.confirm-delete').forEach(button => {
        button.addEventListener('click', function() {
            Swal.fire({
                title: 'Hapus Tahun Ajaran?',
                text: "Data yang terikat dengan tahun ajaran ini mungkin akan terdampak.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
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
