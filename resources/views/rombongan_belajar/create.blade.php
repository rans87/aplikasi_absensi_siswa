@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow-lg border-0">
            <div class="card-header text-white" style="background: linear-gradient(135deg, #0d6efd, #20c997);">
                <h5 class="mb-0">Tambah Rombongan Belajar</h5>
            </div>
            <div class="card-body bg-white">

                <form action="{{ route('rombongan-belajar.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Nama Kelas</label>
                        <input type="text" name="nama_kelas" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jurusan</label>
                        <input type="text" name="jurusan" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tingkat</label>
                        <input type="number" name="tingkat" class="form-control" min="1" max="4" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">API Rombel ID (Opsional)</label>
                        <input type="text" name="api_rombel_id" class="form-control">
                    </div>

                    <button class="btn btn-primary">Simpan</button>
                    <a href="{{ route('rombongan-belajar.index') }}" class="btn btn-secondary">Batal</a>
                </form>

            </div>
        </div>
    </div>
@endsection