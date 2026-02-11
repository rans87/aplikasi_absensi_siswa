@extends('layouts.app')

@section('title', 'Input Prestasi Siswa')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-primary">Input Prestasi</h1>
                <p class="text-muted">Apresiasi pencapaian dan perilaku teladan siswa</p>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid pb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4">
                <form action="{{ route('prestasi.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Penghargaan Untuk Siswa <span class="text-danger">*</span></label>
                        <select name="siswa_id" class="form-select form-control custom-select-modern" required>
                            <option value="">-- Cari Nama Siswa --</option>
                            @foreach($siswa as $s)
                                <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->nis }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Nama Prestasi / Pencapaian <span class="text-danger">*</span></label>
                        <input type="text" name="nama_prestasi" class="form-control" placeholder="Contoh: Juara 1 Lomba LKS, Membantu sesama siswa" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Poin Tambahan <span class="text-danger">*</span></label>
                        <input type="number" name="poin" class="form-control" placeholder="Contoh: 20" required>
                        <small class="text-muted">Poin ini akan menambah skor prestasi siswa</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Catatan Kecil</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Ceritakan singkat tentang prestasi ini..."></textarea>
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('prestasi.index') }}" class="btn btn-outline-secondary px-4 rounded-pill">Batal</a>
                        <button type="submit" class="btn btn-primary px-5 rounded-pill shadow-sm">Beri Penghargaan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection