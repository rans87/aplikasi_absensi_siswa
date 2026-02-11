@extends('layouts.app')

@section('title', 'Edit Tahun Ajar')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <h1 class="m-0 text-primary fw-bold">Edit Tahun Ajar</h1>
        <p class="text-muted">Perbarui informasi periode tahun ajaran</p>
    </div>
</div>

<div class="container-fluid pb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <form action="{{ route('tahun_ajar.update', $tahun_ajar->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark">Tahun Ajaran</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 rounded-start-3 text-primary">
                                <i class="bi bi-calendar-event"></i>
                            </span>
                            <input type="text" name="tahun" class="form-control bg-light border-start-0 rounded-end-3 py-2" 
                                   value="{{ $tahun_ajar->tahun }}" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch p-3 bg-light rounded-3 d-flex align-items-center">
                            <input class="form-check-input ms-0 me-3" type="checkbox" name="aktif" id="aktifSwitch" 
                                   {{ $tahun_ajar->aktif ? 'checked' : '' }} style="transform: scale(1.2);">
                            <div>
                                <label class="form-check-label fw-bold text-dark mb-0 d-block" for="aktifSwitch">Set Sebagai Tahun Aktif</label>
                                <small class="text-muted">Akan menonaktifkan tahun ajar lainnya.</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end pt-3">
                        <a href="{{ route('tahun_ajar.index') }}" class="btn btn-outline-secondary px-4 rounded-pill">Batal</a>
                        <button type="submit" class="btn btn-primary px-5 rounded-pill shadow-sm fw-bold">Update Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
