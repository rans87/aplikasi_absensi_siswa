@extends('layouts.app')

@section('title', 'Tambah Mata Pelajaran')

@section('content')
<div class="content-header mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('mata-pelajaran.index') }}" class="btn btn-outline-primary rounded-pill px-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h2 class="fw-extrabold text-dark mb-1 ls-tight">Tambah Mata Pelajaran</h2>
            <p class="text-muted mb-0 fw-medium">Isi form untuk menambahkan mata pelajaran baru</p>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm p-4 p-md-5">
            <form action="{{ route('mata-pelajaran.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="form-label fw-bold"><i class="bi bi-hash text-primary me-2"></i>Kode Mapel <span class="text-danger">*</span></label>
                    <input type="text" name="kode_mapel" class="form-control @error('kode_mapel') is-invalid @enderror" placeholder="Contoh: MTK, BIG, FIS" value="{{ old('kode_mapel') }}" required>
                    @error('kode_mapel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold"><i class="bi bi-book-fill text-primary me-2"></i>Nama Mata Pelajaran <span class="text-danger">*</span></label>
                    <input type="text" name="nama_mapel" class="form-control @error('nama_mapel') is-invalid @enderror" placeholder="Contoh: Matematika, Bahasa Inggris" value="{{ old('nama_mapel') }}" required>
                    @error('nama_mapel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('mata-pelajaran.index') }}" class="btn btn-outline-secondary px-4 rounded-pill">Batal</a>
                    <button type="submit" class="btn btn-primary px-5 rounded-pill shadow-sm">
                        <i class="bi bi-check-circle-fill me-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>.ls-tight { letter-spacing: -1px; }</style>
@endsection
