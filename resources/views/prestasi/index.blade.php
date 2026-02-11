@extends('layouts.app')

@section('title', 'Data Prestasi')

@section('content')
<div class="content-header fade-in">
    <div class="container-fluid px-4">
        <div class="row mb-4 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark fw-extrabold display-6 ls-1"><i class="bi bi-trophy-fill text-amber me-3"></i>Data Prestasi</h1>
                <p class="text-muted mt-2 fw-medium">Kelola dan apresiasi setiap pencapaian membanggakan siswa di sekolah.</p>
            </div>
            <div class="col-sm-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('prestasi.create') }}" class="btn btn-primary btn-lg rounded-4 shadow-lg px-4 hover-up">
                    <i class="bi bi-star-fill me-2"></i> Input Prestasi
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
                    <label class="form-label fw-bold text-dark small text-uppercase ls-1">Cari Prestasi / Siswa</label>
                    <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden border">
                        <span class="input-group-text bg-white border-0 ps-4 text-primary"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-0 py-3 fs-6" 
                               placeholder="Cari berdasarkan nama, NIS atau kategori prestasi..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <button class="btn btn-primary btn-lg w-100 rounded-4 py-3 fw-bold shadow-sm" type="submit">
                        <i class="bi bi-funnel-fill me-2"></i> Cari
                    </button>
                </div>
                <div class="col-lg-2 col-6">
                    <a href="{{ route('prestasi.index') }}" class="btn btn-light btn-lg w-100 rounded-4 py-3 fw-bold border">
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
                <h5 class="fw-bold mb-0 text-dark">Daftar Prestasi Siswa</h5>
                <span class="badge bg-emerald-soft text-emerald px-3 py-2 rounded-3 border border-success border-opacity-10">Total Pencapaian: {{ $prestasis->total() }}</span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted">
                    <tr>
                        <th class="ps-4 py-3 small fw-bold text-uppercase ls-1" style="width:70px">NO</th>
                        <th class="py-3 small fw-bold text-uppercase ls-1">IDENTITAS SISWA</th>
                        <th class="py-3 small fw-bold text-uppercase ls-1">KATEGORI PRESTASI</th>
                        <th class="py-3 small fw-bold text-uppercase ls-1 text-center">POIN REWARD</th>
                        <th class="py-3 small fw-bold text-uppercase ls-1 d-none d-lg-table-cell">GURU VALIDATOR</th>
                        <th class="py-3 small fw-bold text-uppercase ls-1 text-center">TANGGAL</th>
                        <th class="pe-4 py-3 text-end small fw-bold text-uppercase ls-1" style="width:120px">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prestasis as $p)
                    <tr>
                        <td class="ps-4 text-muted small fw-bold">{{ $loop->iteration + ($prestasis->currentPage() - 1) * $prestasis->perPage() }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-blue-soft text-primary rounded-4 d-flex align-items-center justify-content-center me-3 shadow-sm" style="width:48px;height:48px">
                                    <i class="bi bi-person-check-fill fs-4"></i>
                                </div>
                                <div>
                                    <div class="fw-extrabold text-dark fs-6">{{ $p->siswa->nama ?? 'Siswa Terhapus' }}</div>
                                    <div class="text-muted small">NIS: {{ $p->siswa->nis ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark fs-6">{{ $p->nama_prestasi }}</div>
                            <div class="text-muted small">Catatan Prestasi</div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-emerald-soft text-emerald px-3 py-2 rounded-pill fw-extrabold" style="min-width: 50px;">
                                +{{ $p->poin }}
                            </span>
                        </td>
                        <td class="d-none d-lg-table-cell">
                            <div class="text-dark small fw-bold"><i class="bi bi-person-check-fill text-primary me-2"></i>{{ $p->guru->nama ?? 'System' }}</div>
                        </td>
                        <td class="text-center">
                            <div class="text-muted small fw-bold">{{ $p->created_at->format('d M Y') }}</div>
                        </td>
                        <td class="pe-4 text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('prestasi.edit', $p->id) }}" class="btn btn-sm btn-light text-primary rounded-3 border p-2" title="Edit Data">
                                    <i class="bi bi-pencil-square fs-5"></i>
                                </a>
                                <form action="{{ route('prestasi.destroy', $p->id) }}" method="POST" class="d-inline delete-form">
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
                        <td colspan="7" class="text-center py-5">
                            <div class="py-5">
                                <i class="bi bi-trophy fs-1 text-muted opacity-25 d-block mb-3 display-1"></i>
                                <h4 class="text-muted fw-bold">Belum Ada Prestasi</h4>
                                <p class="text-muted">Ayo, tetap semangat dalam mengapresiasi pencapaian siswa.</p>
                                <a href="{{ route('prestasi.create') }}" class="btn btn-primary rounded-pill px-4 mt-2">Input Prestasi</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($prestasis->hasPages())
        <div class="card-footer bg-white border-0 py-4 px-4 border-top">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div class="text-muted small fw-medium">
                    Menampilkan <span class="text-dark fw-bold">{{ $prestasis->firstItem() }}</span> - <span class="text-dark fw-bold">{{ $prestasis->lastItem() }}</span> dari <span class="text-dark fw-bold">{{ $prestasis->total() }}</span> laporan
                </div>
                {{ $prestasis->links() }}
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
    .text-amber { color: #d97706; }
    .bg-blue-soft { background-color: #eff6ff; }
    .hover-up:hover { transform: translateY(-3px); box-shadow: 0 10px 20px -5px rgba(0,0,0,0.1) !important; transition: all 0.3s ease; }
    
    .pagination .page-link { border: none; padding: 0.6rem 1rem; margin: 0 3px; border-radius: 12px !important; color: #64748b; font-weight: 600; }
    .pagination .page-item.active .page-link { background-color: var(--primary-blue); color: white; shadow: 0 4px 10px rgba(37, 99, 235, 0.2); }
</style>

<script>
    document.querySelectorAll('.confirm-delete').forEach(button => {
        button.addEventListener('click', function() {
            Swal.fire({
                title: 'Hapus Prestasi?',
                text: "Keuntungan poin untuk siswa akan ditarik kembali.",
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
