@extends('layouts.app')

@section('title', 'Edit Admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <h1 class="m-0 text-primary fw-bold">Edit Administrator</h1>
        <p class="text-muted">Perbarui informasi akun administrator</p>
    </div>
</div>

<div class="container-fluid pb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <form action="{{ route('pengguna.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control rounded-3" value="{{ $user->name }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Email / Username Login</label>
                        <input type="email" name="email" class="form-control rounded-3" value="{{ $user->email }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Ganti Password (Opsional)</label>
                        <input type="password" name="password" class="form-control rounded-3" placeholder="Kosongkan jika tidak diganti">
                        <small class="text-muted small">Minimal 6 karakter jika ingin mengganti.</small>
                    </div>

                    <div class="d-flex gap-2 justify-content-end pt-3">
                        <a href="{{ route('pengguna.index') }}" class="btn btn-outline-secondary px-4 rounded-pill">Batal</a>
                        <button type="submit" class="btn btn-primary px-5 rounded-pill shadow-sm fw-bold">Update Admin</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection