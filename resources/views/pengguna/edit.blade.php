@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Edit Pengguna</h5>
            </div>
            <div class="card-body bg-white">
                <form action="{{ route('pengguna.update', $pengguna->id) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" value="{{ $pengguna->username }}" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password (kosongkan jika tidak diubah)</label>
                        <input type="password" name="password" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="admin" {{ $pengguna->role == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="guru" {{ $pengguna->role == 'guru' ? 'selected' : '' }}>Guru</option>
                            <option value="siswa" {{ $pengguna->role == 'siswa' ? 'selected' : '' }}>Siswa</option>
                        </select>
                    </div>

                    <button class="btn btn-primary">Update</button>
                    <a href="{{ route('pengguna.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
@endsection