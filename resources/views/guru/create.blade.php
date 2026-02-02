@extends('layouts.app')

@section('title', 'Tambah Guru')

@section('content')
    <div class="container py-4">
        <div class="card shadow-lg border-0" style="border-radius:15px;">
            <div class="card-header text-white"
                style="background: linear-gradient(135deg, #1E88E5, #42A5F5); border-radius:15px 15px 0 0;">
                <h4 class="mb-0"><i class="bi bi-person-plus-fill"></i> Tambah Guru</h4>
            </div>

            <div class="card-body bg-white">
                <form action="{{ route('guru.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Guru</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">NIP</label>
                        <input type="text" name="nip" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">No HP</label>
                        <input type="text" name="no_hp" class="form-control">
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('guru.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection