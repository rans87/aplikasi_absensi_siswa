@extends('layouts.app')

@section('title', 'Rekap Laporan Performa')

@section('content')
<div class="all-reports-container py-4 px-3 px-md-5">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
        <div>
            <h1 class="fw-black text-dark mb-1">Rekapitulasi Penilaian</h1>
            <p class="text-muted">Melihat daftar siswa yang telah mendapatkan evaluasi karakter.</p>
        </div>
        <div>
            <a href="{{ route('assessments.index') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold">
                <i class="bi bi-star-fill me-2"></i> BERI NILAI BARU
            </a>
        </div>
    </div>

    <!-- Filter & Search Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-5">
        <div class="card-body p-4">
            <form action="{{ route('assessments.all-reports') }}" method="GET">
                <div class="row g-4">
                    <!-- Row 1: Search, Kelas, Guru -->
                    <div class="col-lg-5">
                        <label class="form-label small fw-bold text-muted text-uppercase ls-1">Cari Siswa</label>
                        <div class="input-group search-box-modern shadow-sm rounded-4 overflow-hidden border">
                            <span class="input-group-text bg-white border-0 ps-3 text-primary"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control border-0 py-2" 
                                   placeholder="Nama atau NIS..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label small fw-bold text-muted text-uppercase ls-1">Kelas</label>
                        <div class="input-group shadow-sm rounded-4 overflow-hidden border">
                            <span class="input-group-text bg-white border-0 ps-3 text-primary"><i class="bi bi-door-open"></i></span>
                            <select name="kelas_id" class="form-select border-0 py-2">
                                <option value="">Semua Kelas</option>
                                @foreach($rombels as $rombel)
                                    <option value="{{ $rombel->id }}" {{ request('kelas_id') == $rombel->id ? 'selected' : '' }}>
                                        {{ $rombel->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label small fw-bold text-muted text-uppercase ls-1">Guru Penilai</label>
                        <div class="input-group shadow-sm rounded-4 overflow-hidden border">
                            <span class="input-group-text bg-white border-0 ps-3 text-primary"><i class="bi bi-person-badge"></i></span>
                            <select name="evaluator_id" class="form-select border-0 py-2">
                                <option value="">Semua Guru</option>
                                @foreach($gurus as $g)
                                    <option value="{{ $g->id }}" {{ request('evaluator_id') == $g->id ? 'selected' : '' }}>
                                        {{ $g->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Row 2: Predikat, Tanggal, Buttons -->
                    <div class="col-lg-3">
                        <label class="form-label small fw-bold text-muted text-uppercase ls-1">Predikat / Performa</label>
                        <div class="input-group shadow-sm rounded-4 overflow-hidden border">
                            <span class="input-group-text bg-white border-0 ps-3 text-primary"><i class="bi bi-graph-up-arrow"></i></span>
                            <select name="performance" class="form-select border-0 py-2">
                                <option value="">Semua Performa</option>
                                <option value="excellent" {{ request('performance') == 'excellent' ? 'selected' : '' }}>Sangat Baik (4.0 - 5.0)</option>
                                <option value="good" {{ request('performance') == 'good' ? 'selected' : '' }}>Baik (3.0 - 3.9)</option>
                                <option value="low" {{ request('performance') == 'low' ? 'selected' : '' }}>Butuh Perhatian (< 3.0)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label small fw-bold text-muted text-uppercase ls-1">Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control shadow-sm rounded-4 border py-2" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label small fw-bold text-muted text-uppercase ls-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control shadow-sm rounded-4 border py-2" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-lg-3 d-flex align-items-end gap-2">
                        <button class="btn btn-primary flex-fill fw-bold py-2 rounded-4 shadow-sm" type="submit">
                            <i class="bi bi-funnel-fill me-1"></i> FILTER
                        </button>
                        <a href="{{ route('assessments.all-reports') }}" class="btn btn-light fw-bold py-2 rounded-4 border shadow-sm px-3">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-4 border-0">SISWA & KELAS</th>
                        <th class="px-4 py-4 border-0">NIS</th>
                        <th class="px-4 py-4 border-0 text-center">SKOR RATA-RATA</th>
                        <th class="px-4 py-4 border-0 text-center">PENILAIAN TERAKHIR</th>
                        <th class="px-4 py-4 border-0 text-center">PENILAI</th>
                        <th class="px-4 py-4 border-0 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswa as $s)
                    @php 
                        $latest = $s->assessments->first(); 
                        $avgScore = $latest ? $latest->details->avg('score') : 0;
                    @endphp
                    <tr>
                        <td class="px-4 py-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-sm bg-primary-soft text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 45px; height: 45px;">
                                    {{ substr($s->nama, 0, 1) }}
                                </div>
                                <div>
                                    <span class="fw-bold text-dark d-block mb-1">{{ $s->nama }}</span>
                                    <span class="badge bg-light text-dark border fw-bold small">
                                        {{ $s->currentKelas->rombonganBelajar->nama_kelas ?? 'Tanpa Kelas' }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-muted fw-bold small">{{ $s->nis }}</td>
                        <td class="px-4 py-4 text-center">
                            @if($avgScore > 0)
                            <div class="d-inline-flex align-items-center gap-2">
                                <span class="fw-black fs-5 {{ $avgScore >= 4 ? 'text-success' : ($avgScore >= 3 ? 'text-primary' : 'text-danger') }}">
                                    {{ number_format($avgScore, 1) }}
                                </span>
                                <div class="rating-mini">
                                    <i class="bi bi-star-fill text-warning"></i>
                                </div>
                            </div>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="badge bg-info-soft text-info rounded-pill px-3 py-2 fw-bold">
                                {{ $latest ? $latest->assessment_date->format('d M Y') : '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <div class="small fw-bold text-dark">{{ $latest->evaluatorGuru->nama ?? ($latest->evaluatorUser->name ?? '-') }}</div>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <a href="{{ route('assessments.report', $s->id) }}" class="btn btn-sm btn-white rounded-pill px-3 fw-bold border shadow-sm">
                                <i class="bi bi-eye-fill me-1 text-primary"></i> DETAIL
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-5 text-center">
                            <div class="py-4">
                                <i class="bi bi-clipboard-x display-1 text-light"></i>
                                <p class="text-muted mt-3 fs-5">Belum ada data laporan untuk ditampilkan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $siswa->links() }}
    </div>
</div>

<style>
    .fw-black { font-weight: 800; }
    .bg-primary-soft { background: rgba(13, 110, 253, 0.1); }
    .bg-info-soft { background: rgba(13, 202, 240, 0.1); }
    .search-box-modern input { border-top-left-radius: 15px !important; border-bottom-left-radius: 15px !important; }
    .search-box-modern .btn { border-top-right-radius: 15px !important; border-bottom-right-radius: 15px !important; }
    .table thead th { font-size: 0.8rem; letter-spacing: 1px; color: #6c757d; }
</style>
@endsection
