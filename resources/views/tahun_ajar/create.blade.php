@extends('layouts.app')

@section('title', 'Tambah Tahun Ajar')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <h1 class="m-0 text-primary fw-bold">Tambah Tahun Ajar</h1>
        <p class="text-muted">Masukkan periode tahun ajaran baru sekolah</p>
    </div>
</div>

<div class="container-fluid pb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <form action="{{ route('tahun_ajar.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark">Tahun Ajaran</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 rounded-start-3 text-primary">
                                <i class="bi bi-calendar-event"></i>
                            </span>
                            <input type="text" name="tahun" class="form-control bg-light border-start-0 rounded-end-3 py-2" 
                                   placeholder="Contoh: 2025/2026 atau 2025" required>
                        </div>
                        <small class="text-muted">Gunakan format yang konsisten, misal: 2025/2026</small>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch p-3 bg-light rounded-3">
                            <input class="form-check-input ms-0 me-3" type="checkbox" name="aktif" id="aktifSwitch" checked>
                            <label class="form-check-label fw-bold text-dark" for="aktifSwitch">Set Sebagai Tahun Aktif</label>
                            <div class="text-muted small ms-5">Tahun ajar lain akan otomatis dinonaktifkan jika ini diaktifkan.</div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end pt-3">
                        <a href="{{ route('tahun_ajar.index') }}" class="btn btn-outline-secondary px-4 rounded-pill">Batal</a>
                        <button type="submit" class="btn btn-primary px-5 rounded-pill shadow-sm fw-bold">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection