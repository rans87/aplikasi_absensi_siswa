@extends('layouts.app')

@section('title', 'Catat Pelanggaran')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-primary">Input Pelanggaran</h1>
                <p class="text-muted">Gunakan form ini untuk mencatat tindakan tidak disiplin siswa</p>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid pb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4">
                <form action="{{ route('pelanggaran.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Pilih Siswa <span class="text-danger">*</span></label>
                        <select name="siswa_id" class="form-select form-control custom-select-modern" required>
                            <option value="">-- Cari Nama Siswa --</option>
                            @foreach($siswa as $s)
                                <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->nis }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Nama Pelanggaran <span class="text-danger">*</span></label>
                        <input type="text" name="nama_pelanggaran" class="form-control" placeholder="Contoh: Terlambat masuk sekolah, atribut tidak lengkap" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Poin Pengurangan <span class="text-danger">*</span></label>
                        <input type="number" name="poin" class="form-control" placeholder="Contoh: 10" required>
                        <small class="text-muted">Poin ini akan langsung mengurangi skor kedisiplinan siswa</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Keterangan Opsional</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Tambahkan detail jika diperlukan..."></textarea>
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('pelanggaran.index') }}" class="btn btn-outline-secondary px-4 rounded-pill">Batal</a>
                        <button type="submit" class="btn btn-primary px-5 rounded-pill shadow-sm">Simpan Catatan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
