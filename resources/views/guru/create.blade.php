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

                    {{-- ================= DATA GURU ================= --}}
                    <h5 class="mb-3 text-primary"><i class="bi bi-person-badge"></i> Data Guru</h5>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Guru</label>
                        <input type="text" name="nama" value="{{ old('nama') }}"
                            class="form-control @error('nama') is-invalid @enderror" required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">NIP</label>
                        <input type="text" name="nip" value="{{ old('nip') }}"
                            class="form-control @error('nip') is-invalid @enderror">
                        @error('nip')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">No HP</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                            class="form-control @error('no_hp') is-invalid @enderror">
                        @error('no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    {{-- ================= AKUN LOGIN ================= --}}
                    <h5 class="mb-3 text-success"><i class="bi bi-shield-lock"></i> Akun Login Guru</h5>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Password</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                            required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- ================= BUTTON ================= --}}
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('guru.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection