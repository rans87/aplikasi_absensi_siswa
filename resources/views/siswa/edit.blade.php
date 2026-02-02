@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-header text-white"
             style="background: linear-gradient(135deg, #0d6efd, #4dabf7);">
            <h5 class="mb-0 fw-semibold">✏️ Edit Siswa</h5>
        </div>

        <div class="card-body bg-white px-4 py-4">

            <form action="{{ route('siswa.update', $siswa->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-semibold">NIS</label>
                    <input type="text" name="nis" value="{{ $siswa->nis }}"
                           class="form-control shadow-sm" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ $siswa->nama }}"
                           class="form-control shadow-sm" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">No HP</label>
                    <input type="text" name="no_hp" value="{{ $siswa->no_hp }}"
                           class="form-control shadow-sm">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-select shadow-sm" required>
                        <option value="L" {{ $siswa->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ $siswa->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('siswa.index') }}" class="btn btn-secondary rounded-3">Batal</a>
                    <button class="btn btn-primary rounded-3 px-4 shadow-sm">
                        Update
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
