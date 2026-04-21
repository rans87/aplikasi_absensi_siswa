@extends('layouts.app')

@section('title', 'Data Mata Pelajaran')

@section('content')
<div class="content-header mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
            <h2 class="fw-extrabold text-dark mb-1 ls-tight"><i class="bi bi-book-fill text-primary me-2"></i>Mata Pelajaran</h2>
            <p class="text-muted mb-0 fw-medium">Kelola seluruh mata pelajaran yang tersedia</p>
        </div>
        <a href="{{ route('mata-pelajaran.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-circle-fill me-2"></i>Tambah Mapel
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-header bg-white border-0 p-4">
        <form method="GET" action="{{ route('mata-pelajaran.index') }}" class="d-flex gap-2">
            <div class="input-group" style="max-width: 400px;">
                <span class="input-group-text bg-light border-0 rounded-start-4"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-0 bg-light" placeholder="Cari mata pelajaran..." value="{{ request('search') }}" style="border-radius: 0 18px 18px 0 !important;">
            </div>
            <button type="submit" class="btn btn-primary px-4">Cari</button>
            @if(request('search'))
                <a href="{{ route('mata-pelajaran.index') }}" class="btn btn-outline-secondary px-3"><i class="bi bi-x-lg"></i></a>
            @endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 60px;">NO</th>
                        <th>KODE</th>
                        <th>NAMA MATA PELAJARAN</th>
                        <th class="text-center">JUMLAH JADWAL</th>
                        <th class="pe-4 text-end">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mataPelajaran as $i => $mp)
                    <tr>
                        <td class="ps-4 fw-bold text-muted">{{ $mataPelajaran->firstItem() + $i }}</td>
                        <td>
                            <span class="badge bg-primary-soft text-primary border-0 px-3 py-2 fw-bold rounded-pill">{{ $mp->kode_mapel }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-light text-primary me-3">
                                    <i class="bi bi-journal-bookmark-fill"></i>
                                </div>
                                <div class="fw-bold text-dark">{{ $mp->nama_mapel }}</div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark fw-bold px-3 py-2 rounded-pill">{{ $mp->jadwalPelajaran()->count() }} Jadwal</span>
                        </td>
                        <td class="pe-4 text-end">
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('mata-pelajaran.edit', $mp->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm">
                                    <i class="bi bi-pencil-square me-1"></i>Edit
                                </a>
                                <form action="{{ route('mata-pelajaran.destroy', $mp->id) }}" method="POST" class="d-inline delete-form">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm btn-delete">
                                        <i class="bi bi-trash3-fill me-1"></i>Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="opacity-25 mb-3"><i class="bi bi-book fs-1"></i></div>
                            <p class="text-muted fw-bold">Belum ada mata pelajaran</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($mataPelajaran->hasPages())
    <div class="card-footer bg-white border-0 p-4">
        {{ $mataPelajaran->links() }}
    </div>
    @endif
</div>

<style>
    .bg-primary-soft { background-color: #eff6ff; }
    .icon-circle { width: 38px; height: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
    .ls-tight { letter-spacing: -1px; }
</style>

@push('scripts')
<script>
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus Mata Pelajaran?',
                text: 'Data yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#f1f5f9',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    this.closest('form').submit();
                }
            });
        });
    });
</script>
@endpush
@endsection
