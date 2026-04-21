@extends('layouts.app')

@section('title', 'Edit Rombongan Belajar')

@section('content')
    <div class="container py-4">
        <div class="card shadow border-0" style="border-radius:15px;">
            <div class="card-header bg-warning text-dark" style="border-radius:15px 15px 0 0;">
                <h4 class="mb-0"><i class="bi bi-pencil-square"></i> Edit Rombongan Belajar</h4>
            </div>

            <div class="card-body">
                <form action="{{ route('rombongan-belajar.update', $rombonganBelajar->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Kelas</label>
                        <input type="text" name="nama_kelas" class="form-control"
                            value="{{ old('nama_kelas', $rombonganBelajar->nama_kelas) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Jurusan</label>
                        <input type="text" name="jurusan" class="form-control"
                            value="{{ old('jurusan', $rombonganBelajar->jurusan) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Tingkat</label>
                        <input type="number" name="tingkat" class="form-control"
                            value="{{ old('tingkat', $rombonganBelajar->tingkat) }}" min="1" max="4" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Wali Kelas</label>
                        <select name="wali_kelas_id" class="form-select select2">
                            <option value="">-- Tanpa Wali Kelas --</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id }}" {{ old('wali_kelas_id', $rombonganBelajar->wali_kelas_id) == $guru->id ? 'selected' : '' }}>
                                    {{ $guru->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('rombongan-belajar.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-save"></i> Update
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection