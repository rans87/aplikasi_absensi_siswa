@extends('layouts.app')

@section('title', 'Data Absensi')

@section('content')
<div class="content-header fade-in">
    <div class="container-fluid px-4">
        <div class="row mb-4 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark fw-extrabold display-6 ls-1"><i class="bi bi-clipboard-check-fill text-primary me-3"></i>Data Presensi</h1>
                <p class="text-muted mt-2 fw-medium">Monitor kehadiran harian siswa secara real-time.</p>
            </div>
            <div class="col-sm-6 text-md-end mt-3 mt-md-0">
                <div class="d-flex flex-wrap justify-content-md-end gap-2">
                    <a href="{{ route('absensi.scan') }}" class="btn btn-warning btn-lg rounded-4 shadow-sm px-4 fw-bold hover-up text-dark">
                        <i class="bi bi-qr-code-scan me-2"></i> Scan QR Antrian
                    </a>
                    <a href="{{ route('absensi.create') }}" class="btn btn-primary btn-lg rounded-4 shadow-lg px-4 hover-up">
                        <i class="bi bi-plus-circle me-2"></i> Input Manual
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4 fade-in-delayed">
    {{-- Filter Card --}}
    <div class="card border-0 shadow-sm rounded-4 mb-5 overflow-hidden">
        <div class="card-body p-4 p-md-5" style="background: linear-gradient(to right, #ffffff, var(--soft-blue));">
            <form method="GET" class="row g-4 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <label class="form-label fw-bold text-dark small text-uppercase ls-1 px-1">Pilih Tanggal</label>
                    <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden border">
                        <span class="input-group-text bg-white border-0 ps-4 text-primary"><i class="bi bi-calendar3"></i></span>
                        <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control border-0 py-3 fs-6">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <label class="form-label fw-bold text-dark small text-uppercase ls-1 px-1">Cari Nama Siswa</label>
                    <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden border">
                        <span class="input-group-text bg-white border-0 ps-4 text-primary"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-0 py-3 fs-6" 
                               placeholder="Nama Siswa..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <button class="btn btn-primary btn-lg w-100 rounded-4 py-3 fw-bold shadow-sm" type="submit">
                        <i class="bi bi-funnel-fill me-2"></i> Terapkan
                    </button>
                </div>
                <div class="col-lg-2 col-6">
                    <a href="{{ route('absensi.index') }}" class="btn btn-light btn-lg w-100 rounded-4 py-3 fw-bold border">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Data Table Card --}}
    <div class="card border-0 shadow-sm overflow-hidden rounded-4 mb-5">
        <div class="card-header bg-white py-4 px-4 border-bottom border-light d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">Data Log Absensi</h5>
            <div class="text-muted small">
                Tanggal: <span class="text-primary fw-bold">{{ date('D, d M Y', strtotime($tanggal)) }}</span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted">
                    <tr>
                        <th class="ps-4 py-3 small fw-bold text-uppercase ls-1" style="width:70px">NO</th>
                        <th class="py-3 small fw-bold text-uppercase ls-1">IDENTITAS SISWA</th>
                        <th class="py-3 small fw-bold text-uppercase ls-1 d-none d-lg-table-cell">GURU PENERIMA</th>
                        <th class="py-3 small fw-bold text-uppercase ls-1 d-none d-md-table-cell">ROMBEL / KELAS</th>
                        <th class="py-3 small fw-bold text-uppercase ls-1 text-center">STATUS</th>
                        <th class="pe-4 py-3 text-end small fw-bold text-uppercase ls-1" style="width:100px">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($absensi as $item)
                    <tr>
                        <td class="ps-4 text-muted small fw-bold">{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-blue-soft text-primary rounded-4 d-flex align-items-center justify-content-center me-3 shadow-sm" style="width:48px;height:48px">
                                    <i class="bi bi-person-check fs-4"></i>
                                </div>
                                <div>
                                    <div class="fw-extrabold text-dark fs-6">{{ $item->siswa->nama ?? 'Siswa Terhapus' }}</div>
                                    <div class="text-muted small">NIS: {{ $item->siswa->nis ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="d-none d-lg-table-cell">
                            <span class="text-dark small fw-medium"><i class="bi bi-check2-circle text-success me-2"></i>{{ $item->guru->nama ?? '-' }}</span>
                        </td>
                        <td class="d-none d-md-table-cell">
                            @if($item->rombonganBelajar)
                                <span class="badge bg-blue-soft text-primary px-3 py-2 rounded-3 border border-primary border-opacity-10">
                                    {{ $item->rombonganBelajar->nama_kelas }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @php
                                $statusClasses = [
                                    'hadir' => ['bg' => 'emerald-soft', 'text' => 'emerald', 'icon' => 'bi-check-circle-fill'],
                                    'izin' => ['bg' => 'amber-soft', 'text' => 'amber', 'icon' => 'bi-envelope-paper-fill'],
                                    'sakit' => ['bg' => 'blue-soft', 'text' => 'blue', 'icon' => 'bi-heart-pulse-fill'],
                                    'alfa' => ['bg' => 'rose-soft', 'text' => 'rose', 'icon' => 'bi-x-circle-fill'],
                                ];
                                $st = $statusClasses[$item->status] ?? $statusClasses['alfa'];
                            @endphp
                            <span class="badge bg-{{ $st['bg'] }} text-{{ $st['text'] }} px-3 py-2 rounded-pill border border-{{ $st['text'] }} border-opacity-10">
                                <i class="bi {{ $st['icon'] }} me-1"></i> {{ strtoupper($item->status) }}
                            </span>
                        </td>
                        <td class="pe-4 text-end">
                            <form action="{{ route('absensi.destroy', $item->id) }}" method="POST" class="d-inline delete-form">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-light text-rose rounded-3 shadow-sm border p-2 confirm-delete" title="Hapus Log">
                                    <i class="bi bi-trash3-fill fs-5"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="py-5">
                                <i class="bi bi-calendar-x fs-1 text-muted opacity-25 d-block mb-3 display-1"></i>
                                <h4 class="text-muted fw-bold">Tidak Ada Data Absensi</h4>
                                <p class="text-muted">Gunakan filter atau pencarian lain untuk melihat data presensi.</p>
                                <a href="{{ route('absensi.index') }}" class="btn btn-primary rounded-pill px-4 mt-2">Muat Ulang Halaman</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($absensi->hasPages())
        <div class="card-footer bg-white border-0 py-4 px-4 border-top">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div class="text-muted small fw-medium">
                    Menampilkan <span class="text-dark fw-bold">{{ $absensi->firstItem() }}</span> - <span class="text-dark fw-bold">{{ $absensi->lastItem() }}</span> dari <span class="text-dark fw-bold">{{ $absensi->total() }}</span> log
                </div>
                {{ $absensi->links() }}
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
    .text-amber { color: #d97706; }
    .bg-amber-soft { background-color: #fffbeb; }
    .text-rose { color: #e11d48; }
    .bg-rose-soft { background-color: #fff1f2; }
    .text-blue { color: #2563eb; }
    .bg-blue-soft { background-color: #eff6ff; }
    .hover-up:hover { transform: translateY(-3px); box-shadow: 0 10px 20px -5px rgba(0,0,0,0.1) !important; transition: all 0.3s ease; }
    
    .pagination .page-link { border: none; padding: 0.6rem 1rem; margin: 0 3px; border-radius: 12px !important; color: #64748b; font-weight: 600; }
    .pagination .page-item.active .page-link { background-color: var(--primary-blue); color: white; shadow: 0 4px 10px rgba(37, 99, 235, 0.2); }
</style>

<script>
    document.querySelectorAll('.confirm-delete').forEach(button => {
        button.addEventListener('click', function() {
            Swal.fire({
                title: 'Hapus Log Kehadiran?',
                text: "Data kehadiran ini akan dihapus secara permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus Log',
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
