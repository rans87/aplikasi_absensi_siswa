@extends('layouts.app')

@section('title', 'Anggota Kelas')

@section('content')
<div class="content-header fade-in">
    <div class="container-fluid px-4">
        <div class="row mb-4 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark fw-extrabold display-6 ls-1"><i class="bi bi-person-workspace text-primary me-3"></i>Penempatan Kelas</h1>
                <p class="text-muted mt-2 fw-medium">Atur dan kelola daftar siswa di setiap rombongan belajar sekolah.</p>
            </div>
            <div class="col-sm-6 text-md-end mt-3 mt-md-0">
                <div class="d-flex flex-wrap justify-content-md-end gap-2">
                    <a href="{{ route('anggota-kelas.syncApi') }}" class="btn btn-outline-primary btn-lg rounded-4 px-4 hover-up">
                        <i class="bi bi-cloud-download-fill me-2"></i> Sinkron API Masal
                    </a>
                    <a href="{{ route('anggota-kelas.create') }}" class="btn btn-primary btn-lg rounded-4 shadow-lg px-4 hover-up">
                        <i class="bi bi-person-plus-fill me-2"></i> Tambah Anggota
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4 fade-in-delayed">
    {{-- Advanced Filter Card --}}
    <div class="card border-0 shadow-sm rounded-4 mb-5 overflow-hidden">
        <div class="card-body p-4 p-md-5" style="background: linear-gradient(to right, #ffffff, var(--light-blue));">
            <form action="{{ route('anggota-kelas.index') }}" method="GET" class="row g-4 align-items-end">
                <div class="col-lg-5 col-md-6">
                    <label class="form-label fw-bold text-dark small text-uppercase ls-1">Cari Nama / NIS</label>
                    <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden border">
                        <span class="input-group-text bg-white border-0 ps-4 text-primary"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-0 py-3 fs-6" 
                               placeholder="Contoh: Budi Santoso..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-bold text-dark small text-uppercase ls-1">Filter Rombel</label>
                    <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden border">
                        <span class="input-group-text bg-white border-0 ps-4 text-primary"><i class="bi bi-filter-circle"></i></span>
                        <select name="rombel_id" class="form-select border-0 fs-6">
                            <option value="">Semua Rombel</option>
                            @foreach($rombels as $r)
                                <option value="{{ $r->id }}" {{ request('rombel_id') == $r->id ? 'selected' : '' }}>{{ $r->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-4 py-3 fw-bold shadow-sm">
                        <i class="bi bi-check-lg me-2"></i> Filter
                    </button>
                </div>
                <div class="col-lg-2 col-6">
                    <a href="{{ route('anggota-kelas.index') }}" class="btn btn-light btn-lg w-100 rounded-4 py-3 fw-bold border">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Data Placement Card --}}
    <div class="card border-0 shadow-sm overflow-hidden rounded-4 mb-5">
        <div class="card-header bg-white py-4 px-4 border-bottom border-light">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">Daftar Penempatan Siswa</h5>
                <span class="badge bg-blue-soft text-primary px-3 py-2 rounded-3 border border-primary border-opacity-10">Total Terdaftar: {{ $anggota->total() }}</span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted">
                    <tr>
                        <th class="ps-4 py-3 small fw-bold text-uppercase ls-1">IDENTITAS SISWA</th>
                        <th class="py-3 small fw-bold text-uppercase ls-1">NIS</th>
                        <th class="py-3 small fw-bold text-uppercase ls-1 text-center">PENEMPATAN KELAS</th>
                        <th class="py-3 small fw-bold text-uppercase ls-1 text-center">TAHUN AJAR</th>
                        <th class="pe-4 py-3 text-end small fw-bold text-uppercase ls-1" style="width:100px">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($anggota as $item)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-blue-soft text-primary rounded-4 d-flex align-items-center justify-content-center me-3 shadow-sm" style="width:48px;height:48px">
                                    <i class="bi bi-person-fill fs-4"></i>
                                </div>
                                <div>
                                    <div class="fw-extrabold text-dark fs-6">{{ $item->siswa->nama ?? 'Siswa Terhapus' }}</div>
                                    <div class="text-muted small">Anggota Aktif</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <code class="bg-light px-2 py-1 rounded-3 text-primary fw-bold">{{ $item->siswa->nis ?? '-' }}</code>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-blue-soft text-primary px-3 py-2 rounded-pill border border-primary border-opacity-10 fw-bold">
                                {{ $item->rombonganBelajar->nama_kelas ?? '-' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="text-dark small fw-extrabold"><i class="bi bi-calendar2-check me-2 text-success"></i>{{ $item->tahunAjar->tahun ?? '-' }}</div>
                        </td>
                        <td class="pe-4 text-end">
                            <form action="{{ route('anggota-kelas.destroy', $item->id) }}" method="POST" class="d-inline delete-form">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-light text-rose rounded-3 shadow-sm border p-2 confirm-delete" title="Keluarkan Siswa">
                                    <i class="bi bi-x-circle-fill fs-5"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="py-5">
                                <i class="bi bi-people-fill fs-1 text-muted opacity-25 d-block mb-3 display-1"></i>
                                <h4 class="text-muted fw-bold">Belum Ada Penempatan</h4>
                                <p class="text-muted">Gunakan tombol Sinkron API atau Tambah Anggota untuk mengisi data.</p>
                                <a href="{{ route('anggota-kelas.index') }}" class="btn btn-primary rounded-pill px-4 mt-2">Segarkan</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($anggota->hasPages())
        <div class="card-footer bg-white border-0 py-4 px-4 border-top">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div class="text-muted small fw-medium">
                    Menampilkan <span class="text-dark fw-bold">{{ $anggota->firstItem() }}</span> - <span class="text-dark fw-bold">{{ $anggota->lastItem() }}</span> dari <span class="text-dark fw-bold">{{ $anggota->total() }}</span> penempatan
                </div>
                {{ $anggota->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<style>
    .fw-extrabold { font-weight: 800; }
    .ls-1 { letter-spacing: 0.5px; }
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
                title: 'Keluarkan Siswa?',
                text: "Siswa akan dihapus dari daftar anggota kelas ini.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Keluarkan!',
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
