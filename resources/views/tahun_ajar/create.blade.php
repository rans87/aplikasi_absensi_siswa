@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Tambah Tahun Ajaran</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('tahun_ajar.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="tahun" class="form-label">Tahun Ajaran</label>
                        <input type="text" class="form-control" id="tahun" name="tahun" placeholder="Contoh: 2025/2026"
                            required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="aktif" name="aktif" value="1">
                        <label class="form-check-label" for="aktif">Aktif</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
@endsection