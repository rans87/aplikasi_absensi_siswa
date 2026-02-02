@extends('layouts.app')

@section('content')
    <div class="container py-4">

        <div class="card shadow border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Manajemen Pengguna</h5>
                <a href="{{ route('pengguna.create') }}" class="btn btn-light btn-sm fw-semibold">
                    + Tambah Pengguna
                </a>
            </div>

            <div class="card-body bg-white">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Relasi</th>
                                <th width="180">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pengguna as $p)
                                <tr>
                                    <td class="fw-semibold">{{ $p->username }}</td>
                                    <td>
                                        <span class="badge bg-primary text-uppercase">{{ $p->role }}</span>
                                    </td>
                                    <td>
                                        @if($p->guru) Guru: {{ $p->guru->nama }} @endif
                                        @if($p->siswa) Siswa: {{ $p->siswa->nama }} @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('pengguna.edit', $p->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('pengguna.destroy', $p->id) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger"
                                                onclick="return confirm('Hapus data?')">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
@endsection