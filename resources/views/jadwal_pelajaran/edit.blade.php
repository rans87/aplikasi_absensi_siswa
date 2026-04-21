@extends('layouts.app')

@section('title', 'Edit Jadwal Pelajaran')

@section('content')
<div class="content-header mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('jadwal-pelajaran.index') }}" class="btn btn-outline-primary rounded-pill px-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h2 class="fw-extrabold text-dark mb-1 ls-tight">Edit Jadwal Pelajaran</h2>
            <p class="text-muted mb-0 fw-medium">Perbarui jadwal mata pelajaran</p>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="card border-0 shadow-sm p-4 p-md-5">
            <form action="{{ route('jadwal-pelajaran.update', $jadwal->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold"><i class="bi bi-building text-primary me-2"></i>Kelas <span class="text-danger">*</span></label>
                        <select name="rombongan_belajar_id" class="form-select select2" required>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}" {{ $jadwal->rombongan_belajar_id == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }} - {{ $k->jurusan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold"><i class="bi bi-book-fill text-primary me-2"></i>Mata Pelajaran <span class="text-danger">*</span></label>
                        <select name="mata_pelajaran_id" class="form-select select2" required>
                            @foreach($mataPelajaran as $mp)
                                <option value="{{ $mp->id }}" {{ $jadwal->mata_pelajaran_id == $mp->id ? 'selected' : '' }}>{{ $mp->kode_mapel }} - {{ $mp->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold"><i class="bi bi-person-badge-fill text-primary me-2"></i>Guru Pengajar <span class="text-danger">*</span></label>
                        <select name="guru_id" class="form-select select2" required>
                            @foreach($guru as $g)
                                <option value="{{ $g->id }}" {{ $jadwal->guru_id == $g->id ? 'selected' : '' }}>{{ $g->nama }} {{ $g->nip ? '('.$g->nip.')' : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold"><i class="bi bi-calendar3 text-primary me-2"></i>Hari <span class="text-danger">*</span></label>
                        <select name="hari" class="form-select select2" required>
                            @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
                                <option value="{{ $h }}" {{ $jadwal->hari == $h ? 'selected' : '' }}>{{ $h }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold"><i class="bi bi-clock text-primary me-2"></i>Jam Mulai <span class="text-danger">*</span></label>
                        <input type="time" name="jam_mulai" class="form-control" value="{{ $jadwal->jam_mulai }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold"><i class="bi bi-clock-fill text-primary me-2"></i>Jam Selesai <span class="text-danger">*</span></label>
                        <input type="time" name="jam_selesai" class="form-control" value="{{ $jadwal->jam_selesai }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold"><i class="bi bi-sort-numeric-up text-primary me-2"></i>Urutan Jam Ke <span class="text-danger">*</span></label>
                        <input type="number" name="urutan" class="form-control" value="{{ $jadwal->urutan }}" min="1" required>
                    </div>
                </div>

                <hr class="my-4 opacity-10">

                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('jadwal-pelajaran.index') }}" class="btn btn-outline-secondary px-4 rounded-pill">Batal</a>
                    <button type="submit" class="btn btn-primary px-5 rounded-pill shadow-sm">
                        <i class="bi bi-check-circle-fill me-2"></i>Update Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>.ls-tight { letter-spacing: -1px; }</style>
@endsection
