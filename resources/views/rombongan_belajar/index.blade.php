@extends('layouts.app')

@section('title', 'Rombongan Belajar')

@section('content')
<div class="content-header fade-in">
    <div class="container-fluid px-4">
        <div class="row mb-4 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark fw-extrabold display-6 ls-1"><i class="bi bi-grid-1x2-fill text-primary me-3"></i>Rombongan Belajar</h1>
                <p class="text-muted mt-2 fw-medium">Kelola struktur organisasi kelas dan pembagian jurusan siswa.</p>
            </div>
            <div class="col-sm-6 text-md-end mt-3 mt-md-0">
                <div class="d-flex flex-wrap justify-content-md-end gap-2">
                    <a href="{{ route('rombongan-belajar.sync') }}" class="btn btn-outline-primary btn-lg rounded-4 px-4 hover-up">
                        <i class="bi bi-cloud-download-fill me-2"></i> Sinkron API Masal
                    </a>
                    <a href="{{ route('rombongan-belajar.create') }}" class="btn btn-primary btn-lg rounded-4 shadow-lg px-4 hover-up">
                        <i class="bi bi-plus-square-fill me-2"></i> Tambah Kelas
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4 fade-in-delayed">
    {{-- Search & Filter Section --}}
    <div class="card border-0 shadow-sm rounded-4 mb-5 overflow-hidden">
        <div class="card-body p-4 p-md-5" style="background: linear-gradient(to right, #ffffff, var(--light-blue));">
            <form action="{{ route('rombongan-belajar.index') }}" method="GET" class="row g-4 align-items-end">
                <div class="col-lg-8">
                    <label class="form-label fw-bold text-dark small text-uppercase ls-1">Cari Kelas atau Jurusan</label>
                    <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden border">
                        <span class="input-group-text bg-white border-0 ps-4 text-primary"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-0 py-3 fs-6" 
                               placeholder="Nama Kelas, Jurusan, atau Tingkat..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <button class="btn btn-primary btn-lg w-100 rounded-4 py-3 fw-bold shadow-sm" type="submit">
                        <i class="bi bi-funnel-fill me-2"></i> Cari
                    </button>
                </div>
                <div class="col-lg-2 col-6">
                    <a href="{{ route('rombongan-belajar.index') }}" class="btn btn-light btn-lg w-100 rounded-4 py-3 fw-bold border">
                        <i class="bi bi-x-circle me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Class List Card --}}
    <div class="card border-0 shadow-sm overflow-hidden rounded-4 mb-5">
        <div class="card-header bg-white py-4 px-4 border-bottom border-light">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">Data Rombel Sekolah</h5>
                <span class="badge bg-blue-soft text-primary px-3 py-2 rounded-3 border border-primary border-opacity-10">Total: {{ $rombel->total() }} Rombel</span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted">
                    <tr>
                        <th class="ps-4 py-3 small fw-bold text-uppercase ls-1" style="width:70px">NO</th>
                        <th class="py-3 small fw-bold text-uppercase ls-1">INFORMASI KELAS</th>
                        <th class="py-3 small fw-bold text-uppercase ls-1 d-none d-md-table-cell">JURUSAN / KOMPETENSI</th>
                        <th class="py-3 small fw-bold text-uppercase ls-1 text-center">TINGKAT</th>
                        <th class="pe-4 py-3 text-end small fw-bold text-uppercase ls-1" style="width:120px">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rombel as $r)
                    <tr>
                        <td class="ps-4 text-muted small fw-bold">{{ $loop->iteration + ($rombel->currentPage() - 1) * $rombel->perPage() }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-blue-soft text-primary rounded-4 d-flex align-items-center justify-content-center me-3 shadow-sm" style="width:48px;height:48px">
                                    <i class="bi bi-door-closed-fill fs-4"></i>
                                </div>
                                <div>
                                    <div class="fw-extrabold text-dark fs-6">{{ $r->nama_kelas }}</div>
                                    <div class="text-muted small">
                                        <i class="bi bi-person-check-fill text-warning me-1"></i>
                                        Wali: {{ $r->waliKelas->nama ?? 'Belum Ditentukan' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="d-none d-md-table-cell">
                            <span class="text-dark small fw-medium"><i class="bi bi-layers-half text-primary me-2"></i>{{ $r->jurusan ?? 'Semua Jurusan' }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill small fw-bold">
                                Kelas {{ $r->tingkat }}
                            </span>
                        </td>
                        <td class="pe-4 text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-sm btn-light text-success rounded-3 border p-2 show-students" data-id="{{ $r->id }}" data-name="{{ $r->nama_kelas }}" title="Lihat Daftar Siswa">
                                    <i class="bi bi-people-fill fs-5"></i>
                                </button>
                                <a href="{{ route('rombongan-belajar.edit', $r->id) }}" class="btn btn-sm btn-light text-primary rounded-3 border p-2" title="Edit Rombel">
                                    <i class="bi bi-pencil-square fs-5"></i>
                                </a>
                                <form action="{{ route('rombongan-belajar.destroy', $r->id) }}" method="POST" class="d-inline confirm-delete-form">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-light text-rose rounded-3 border p-2 confirm-delete" title="Hapus Rombel">
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
                                <i class="bi bi-grid-1x2 fs-1 text-muted opacity-25 d-block mb-3 display-1"></i>
                                <h4 class="text-muted fw-bold">Belum Ada Data Rombel</h4>
                                <p class="text-muted">Gunakan fitur Sinkron untuk menarik data dari API pusat.</p>
                                <a href="{{ route('rombongan-belajar.sync') }}" class="btn btn-primary rounded-pill px-4 mt-2">Sinkron Data</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($rombel->hasPages())
        <div class="card-footer bg-white border-0 py-4 px-4 border-top">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div class="text-muted small fw-medium">
                    Menampilkan <span class="text-dark fw-bold">{{ $rombel->firstItem() }}</span> - <span class="text-dark fw-bold">{{ $rombel->lastItem() }}</span> dari <span class="text-dark fw-bold">{{ $rombel->total() }}</span> rombel
                </div>
                {{ $rombel->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal Daftar Siswa -->
<div class="modal fade" id="siswaModal" tabindex="-1" aria-labelledby="siswaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-extrabold text-dark px-2" id="siswaModalLabel">Daftar Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive rounded-3 border">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center" style="width: 50px">NO</th>
                                <th>NAMA SISWA</th>
                                <th>NIS</th>
                                <th>L/P</th>
                            </tr>
                        </thead>
                        <tbody id="siswaTableBody">
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
    .fw-extrabold { font-weight: 800; }
    .ls-1 { letter-spacing: 0.5px; }
    .text-rose { color: #e11d48; }
    .bg-blue-soft { background-color: #eff6ff; }
    .bg-pink-soft { background-color: #fff1f2; }
    .hover-up:hover { transform: translateY(-3px); box-shadow: 0 10px 20px -5px rgba(0,0,0,0.1) !important; transition: all 0.3s ease; }
    
    .pagination .page-link { border: none; padding: 0.6rem 1rem; margin: 0 3px; border-radius: 12px !important; color: #64748b; font-weight: 600; }
    .pagination .page-item.active .page-link { background-color: var(--primary-blue); color: white; shadow: 0 4px 10px rgba(37, 99, 235, 0.2); }
</style>

<script>
    // Modal student list
    document.querySelectorAll('.show-students').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const modal = new bootstrap.Modal(document.getElementById('siswaModal'));
            const tableBody = document.getElementById('siswaTableBody');
            
            document.getElementById('siswaModalLabel').innerText = 'Daftar Siswa: ' + name;
            tableBody.innerHTML = '<tr><td colspan="4" class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><div class="mt-2">Memuat data...</div></td></tr>';
            
            modal.show();

            fetch(`/rombongan-belajar/${id}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(res => {
                if (res.success && res.data.length > 0) {
                    tableBody.innerHTML = '';
                    res.data.forEach((siswa, index) => {
                        tableBody.innerHTML += `
                            <tr>
                                <td class="text-center">${index + 1}</td>
                                <td class="fw-bold">${siswa.nama}</td>
                                <td>${siswa.nis ?? '-'}</td>
                                <td>${siswa.jenis_kelamin === 'L' ? '<span class="badge bg-blue-soft text-primary">Laki-laki</span>' : '<span class="badge bg-pink-soft text-danger">Perempuan</span>'}</td>
                            </tr>
                        `;
                    });
                } else {
                    tableBody.innerHTML = '<tr><td colspan="4" class="text-center py-5"><i class="bi bi-info-circle fs-1 text-muted d-block mb-2"></i><br>Tidak ada siswa di kelas ini.</td></tr>';
                }
            })
            .catch(err => {
                tableBody.innerHTML = '<tr><td colspan="4" class="text-center text-danger py-4">Gagal memuat data siswa.</td></tr>';
            });
        });
    });

    document.querySelectorAll('.confirm-delete').forEach(button => {
        button.addEventListener('click', function() {
            Swal.fire({
                title: 'Hapus Rombongan Belajar?',
                text: "Data ini mungkin terhubung dengan jadwal absensi siswa.",
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
