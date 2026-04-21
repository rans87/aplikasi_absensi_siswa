@extends('layouts.app')

@section('title', 'Data Absensi')

@section('content')
{{-- /* header data presensi */ --}}
<div class="content-header fade-in">
    <div class="container-fluid px-4">
        <div class="row mb-4 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark fw-extrabold display-6 ls-1"><i class="bi bi-clipboard-check-fill text-primary me-3"></i>Data Presensi</h1>
                <p class="text-muted mt-2 fw-medium">Monitor kehadiran harian siswa secara real-time.</p>
            </div>
            <div class="col-sm-6 text-md-end mt-3 mt-md-0">
                <div class="d-flex flex-wrap justify-content-md-end gap-2">

                    <a href="{{ route('absensi.create') }}" class="btn btn-primary btn-lg rounded-4 shadow-lg px-4 hover-up">
                        <i class="bi bi-plus-circle me-2"></i> Input Manual
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4 fade-in-delayed">
    {{-- /* statistik kehadiran harian */ --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden hover-up">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-primary-soft p-3 rounded-4 shadow-sm text-primary">
                            <i class="bi bi-person-lines-fill fs-2"></i>
                        </div>
                        <div class="text-end">
                            <span class="text-muted small fw-bold text-uppercase ls-1">Total Records</span>
                            <h3 class="fw-extrabold mb-0 text-dark">{{ $absensi->total() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden hover-up">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-emerald-soft p-3 rounded-4 shadow-sm text-emerald">
                            <i class="bi bi-check-circle-fill fs-2"></i>
                        </div>
                        <div class="text-end">
                            <span class="text-muted small fw-bold text-uppercase ls-1">Hadir ({{ $summary['hadir'] ?? 0 }})</span>
                            <h3 class="fw-extrabold mb-0 text-emerald">{{ $attendanceRate > 0 ? $attendanceRate . '%' : ($summary['hadir'] ?? 0) }}</h3>
                        </div>
                    </div>
                    @if($totalSiswa > 0)
                        <div class="progress rounded-pill mt-3" style="height: 6px;">
                            <div class="progress-bar bg-emerald" style="width:{{ $attendanceRate }}%"></div>
                        </div>
                        <div class="small text-muted mt-2 fw-medium">Based on <b>{{ $totalSiswa }}</b> students</div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden hover-up">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-amber-soft p-3 rounded-4 shadow-sm text-amber">
                            <i class="bi bi-envelope-paper-fill fs-2"></i>
                        </div>
                        <div class="text-end">
                            <span class="text-muted small fw-bold text-uppercase ls-1">Izin / Sakit</span>
                            <h3 class="fw-extrabold mb-0 text-amber">{{ ($summary['izin'] ?? 0) + ($summary['sakit'] ?? 0) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden hover-up">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-rose-soft p-3 rounded-4 shadow-sm text-rose">
                            <i class="bi bi-x-circle-fill fs-2"></i>
                        </div>
                        <div class="text-end">
                            <span class="text-muted small fw-bold text-uppercase ls-1">Alfa / Tanpa Ket</span>
                            <h3 class="fw-extrabold mb-0 text-rose">{{ $summary['alfa'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- /* filter laporan absensi */ --}}
    <div class="card border-0 shadow-sm rounded-4 mb-5 overflow-hidden">
        <div class="card-body p-4 p-md-5" style="background: linear-gradient(to right, #ffffff, var(--soft-blue));">
            <form method="GET" id="filterForm" class="row g-4 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label text-uppercase ls-1 px-1">Pilih Tanggal</label>
                    <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden border">
                        <span class="input-group-text bg-white border-0 ps-3 text-primary"><i class="bi bi-calendar3"></i></span>
                        <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control border-0 py-3 fs-6">
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label text-uppercase ls-1 px-1">Tipe Laporan</label>
                    <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden border">
                        <span class="input-group-text bg-white border-0 ps-3 text-primary"><i class="bi bi-layers"></i></span>
                        <select name="type" class="form-select border-0 py-3 fs-6" id="typeSelector">
                            <option value="harian" {{ $type == 'harian' ? 'selected' : '' }}>Kehadiran Harian</option>
                            <option value="mapel" {{ $type == 'mapel' ? 'selected' : '' }}>Mata Pelajaran</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label text-uppercase ls-1 px-1">Pilih Kelas</label>
                    <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden border">
                        <span class="input-group-text bg-white border-0 ps-3 text-primary"><i class="bi bi-door-open"></i></span>
                        <select name="rombongan_belajar_id" class="form-select border-0 py-3 fs-6">
                            <option value="">Semua Kelas</option>
                            @foreach($rombonganList as $r)
                                <option value="{{ $r->id }}" {{ request('rombongan_belajar_id') == $r->id ? 'selected' : '' }}>{{ $r->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" id="mapelContainer" style="{{ $type == 'harian' ? 'display:none' : '' }}">
                    <label class="form-label text-uppercase ls-1 px-1">Pilih Mata Pelajaran</label>
                    <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden border">
                        <span class="input-group-text bg-white border-0 ps-3 text-primary"><i class="bi bi-book"></i></span>
                        <select name="mata_pelajaran_id" class="form-select border-0 py-3 fs-6">
                            <option value="">Semua Mapel</option>
                            @foreach($mapelList as $m)
                                <option value="{{ $m->id }}" {{ request('mata_pelajaran_id') == $m->id ? 'selected' : '' }}>{{ $m->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label text-uppercase ls-1 px-1">Filter Status</label>
                    <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden border">
                        <span class="input-group-text bg-white border-0 ps-3 text-primary"><i class="bi bi-funnel"></i></span>
                        <select name="status" class="form-select border-0 py-3 fs-6">
                            <option value="">Semua Status</option>
                            <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                            <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                            <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="alfa" {{ request('status') == 'alfa' ? 'selected' : '' }}>Alfa</option>
                            <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-5 col-md-6">
                    <label class="form-label text-uppercase ls-1 px-1">Cari Siswa</label>
                    <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden border">
                        <span class="input-group-text bg-white border-0 ps-3 text-primary"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-0 py-3 fs-6" 
                               placeholder="Nama Siswa atau NIS..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="row g-2">
                        <div class="col-8">
                            <button class="btn btn-primary btn-lg w-100 rounded-4 py-3 fw-bold shadow-sm" type="submit">
                                <i class="bi bi-display me-2"></i> Tampilkan Laporan
                            </button>
                        </div>
                        <div class="col-4">
                            <a href="{{ route('absensi.index') }}" class="btn btn-light btn-lg w-100 rounded-4 py-3 fw-bold border">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- /* tabel log absensi */ --}}
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
                            @if(isset($item->jadwalPelajaran))
                                <span class="badge bg-blue-soft text-primary px-3 py-2 rounded-3 border border-primary border-opacity-10">
                                    {{ $item->jadwalPelajaran->rombonganBelajar->nama_kelas }}
                                </span>
                                <div class="mt-1 small fw-bold text-indigo">{{ $item->jadwalPelajaran->mataPelajaran->nama_mapel }}</div>
                            @elseif($item->rombonganBelajar)
                                <span class="badge bg-blue-soft text-primary px-3 py-2 rounded-3 border border-primary border-opacity-10">
                                    {{ $item->rombonganBelajar->nama_kelas }}
                                </span>
                                <div class="mt-1 small text-muted italic">Absensi Umum</div>
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
                            @if(!isset($item->jadwalPelajaran))
                            <form action="{{ route('absensi.destroy', $item->id) }}" method="POST" class="d-inline delete-form">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-light text-rose rounded-3 shadow-sm border p-2 confirm-delete" title="Hapus Log">
                                    <i class="bi bi-trash3-fill fs-5"></i>
                                </button>
                            </form>
                            @endif
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
    .bg-emerald { background-color: #059669 !important; }
    .bg-rose { background-color: #e11d48 !important; }
    .bg-amber { background-color: #f59e0b !important; }
    .italic { font-style: italic; }
</style>

<script>
    document.getElementById('typeSelector').addEventListener('change', function() {
        const mapelContainer = document.getElementById('mapelContainer');
        if (this.value === 'mapel') {
            mapelContainer.style.display = 'block';
        } else {
            mapelContainer.style.display = 'none';
        }
    });

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
