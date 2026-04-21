@extends('layouts.app')

@section('title', 'Jadwal Pelajaran')

@section('content')
<div class="content-header mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
            <h2 class="fw-extrabold text-dark mb-1 ls-tight"><i class="bi bi-calendar-week-fill text-primary me-2"></i>Jadwal Pelajaran</h2>
            <p class="text-muted mb-0 fw-medium">Kelola jadwal mata pelajaran setiap kelas</p>
        </div>
        <a href="{{ route('jadwal-pelajaran.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-circle-fill me-2"></i>Tambah Jadwal
        </a>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('jadwal-pelajaran.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-bold small text-muted"><i class="bi bi-building me-1"></i>Filter Kelas</label>
                <select name="kelas_id" class="form-select">
                    <option value="">Semua Kelas</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ $kelas_id == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }} - {{ $k->jurusan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small text-muted"><i class="bi bi-calendar3 me-1"></i>Filter Hari</label>
                <select name="hari" class="form-select">
                    <option value="">Semua Hari</option>
                    @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
                        <option value="{{ $h }}" {{ $hari == $h ? 'selected' : '' }}>{{ $h }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small text-muted"><i class="bi bi-search me-1"></i>Pencarian</label>
                <input type="text" name="search" class="form-control" placeholder="Cari guru, mapel..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel-fill me-1"></i>Filter</button>
                <a href="{{ route('jadwal-pelajaran.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </form>
    </div>
</div>

{{-- Schedule Table --}}
<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 50px;">NO</th>
                        <th>HARI</th>
                        <th>KELAS</th>
                        <th>MATA PELAJARAN</th>
                        <th>GURU PENGAJAR</th>
                        <th class="text-center">JAM</th>
                        <th class="text-center">URUTAN</th>
                        <th class="pe-4 text-end">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwal as $i => $j)
                    <tr>
                        <td class="ps-4 fw-bold text-muted">{{ $jadwal->firstItem() + $i }}</td>
                        <td>
                            @php
                                $hariColors = ['Senin'=>'primary','Selasa'=>'info','Rabu'=>'success','Kamis'=>'warning','Jumat'=>'danger','Sabtu'=>'secondary'];
                                $hariColor = $hariColors[$j->hari] ?? 'primary';
                            @endphp
                            <span class="badge bg-{{ $hariColor }} bg-opacity-10 text-{{ $hariColor }} border-0 px-3 py-2 fw-bold rounded-pill">
                                <i class="bi bi-calendar3 me-1"></i>{{ $j->hari }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $j->rombonganBelajar->nama_kelas }}</div>
                            <small class="text-muted">{{ $j->rombonganBelajar->jurusan }}</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="icon-circle-sm bg-primary bg-opacity-10 text-primary me-2">
                                    <i class="bi bi-book-half"></i>
                                </div>
                                <div class="fw-bold text-dark">{{ $j->mataPelajaran->nama_mapel }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-xs bg-primary text-white rounded-3 me-2 d-flex align-items-center justify-content-center fw-bold" style="width:32px;height:32px;font-size:12px;">
                                    {{ strtoupper(substr($j->guru->nama, 0, 1)) }}
                                </div>
                                <span class="fw-medium">{{ Str::limit($j->guru->nama, 20) }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark fw-bold px-3 py-2 rounded-pill border">
                                <i class="bi bi-clock me-1 text-primary"></i>{{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary bg-opacity-10 text-primary fw-extrabold px-3 py-2 rounded-pill">Jam ke-{{ $j->urutan }}</span>
                        </td>
                        <td class="pe-4 text-end">
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('jadwal-pelajaran.edit', $j->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('jadwal-pelajaran.destroy', $j->id) }}" method="POST" class="d-inline delete-form">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 btn-delete">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="opacity-25 mb-3"><i class="bi bi-calendar-x fs-1"></i></div>
                            <p class="text-muted fw-bold">Belum ada jadwal pelajaran</p>
                            <a href="{{ route('jadwal-pelajaran.create') }}" class="btn btn-primary btn-sm rounded-pill px-4 mt-2">
                                <i class="bi bi-plus-circle me-1"></i>Tambah Jadwal Pertama
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($jadwal->hasPages())
    <div class="card-footer bg-white border-0 p-4">
        {{ $jadwal->links() }}
    </div>
    @endif
</div>

<style>
    .ls-tight { letter-spacing: -1px; }
    .icon-circle-sm { width: 32px; height: 32px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; }
</style>

@push('scripts')
<script>
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus Jadwal?',
                text: 'Data jadwal yang dihapus tidak dapat dikembalikan!',
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
