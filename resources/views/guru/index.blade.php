@extends('layouts.app')

@section('title', 'Data Guru')

@section('content')
    <div class="container py-4">

        <div class="card shadow-lg border-0" style="border-radius:15px;">
            <div class="card-header text-white d-flex justify-content-between align-items-center"
                style="background: linear-gradient(135deg, #2196F3, #64B5F6); border-radius:15px 15px 0 0;">
                <h4 class="mb-0"><i class="bi bi-person-badge-fill"></i> Data Guru</h4>
                <a href="{{ route('guru.create') }}" class="btn btn-light fw-bold">
                    <i class="bi bi-plus-circle"></i> Tambah Guru
                </a>
            </div>

            <div class="card-body bg-white">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead style="background-color:#E3F2FD;">
                            <tr class="text-center">
                                <th>No</th>
                                <th>Nama</th>
                                <th>NIP</th>
                                <th>No HP</th>
                                <th width="180">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($guru as $g)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $g->nama }}</td>
                                    <td>{{ $g->nip ?? '-' }}</td>
                                    <td>{{ $g->no_hp ?? '-' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('guru.edit', $g->id) }}" class="btn btn-sm btn-warning text-white">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form action="{{ route('guru.destroy', $g->id) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger"
                                                onclick="return confirm('Yakin hapus data guru ini?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Belum ada data guru</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
@endsection