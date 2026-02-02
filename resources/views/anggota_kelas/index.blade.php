@extends('layouts.app')

@section('title', 'Manajemen Anggota Kelas')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1 class="m-0">Manajemen Anggota Kelas</h1>
                    <p class="text-muted">Monitoring dan kelola penempatan siswa secara efisien.</p>
                </div>
                <div class="col-sm-6 text-sm-end">
                    <a href="{{ route('anggota-kelas.create') }}" class="btn btn-primary px-4 py-2"
                        style="border-radius: 12px; font-weight: 700;">
                        <i class="bi bi-plus-lg me-2"></i> Tambah Anggota
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            </div>
        @endif

        <div class="card overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-uppercase text-secondary small fw-bold">No</th>
                                <th class="px-4 py-3 text-uppercase text-secondary small fw-bold">Informasi Siswa</th>
                                <th class="px-4 py-3 text-uppercase text-secondary small fw-bold">Rombongan Belajar</th>
                                <th class="px-4 py-3 text-uppercase text-secondary small fw-bold">Tahun Ajar</th>
                                <th class="px-4 py-3 text-uppercase text-secondary small fw-bold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($anggota as $index => $item)
                                <tr>
                                    <td class="px-4 fw-bold text-muted">{{ $index + 1 }}</td>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-3 d-flex align-items-center justify-content-center text-white fw-bold me-3 shadow-sm"
                                                style="width: 42px; height: 42px; background: linear-gradient(135deg, #2c1cbaea, #6a3cc0);">
                                                {{ substr($item->siswa->nama ?? '?', 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $item->siswa->nama ?? 'Unknown' }}</div>
                                                <small class="text-muted">NISN: {{ $item->siswa->nisn ?? '-' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4">
                                        <span class="badge rounded-pill px-3 py-2"
                                            style="background-color: #e9f2ff; color: #0d6efd; font-weight: 800; font-size: 0.7rem; border: 1px solid #cfe2ff;">
                                            {{ $item->rombel->nama_rombel ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-4 text-muted font-italic">{{ $item->tahunAjar->tahun ?? 'N/A' }}</td>
                                    <td class="px-4 text-center">
                                        <div class="btn-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                            <a href="{{ route('anggota-kelas.edit', $item->id) }}"
                                                class="btn btn-light btn-sm text-primary border-end">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <button class="btn btn-light btn-sm text-danger"
                                                onclick="confirmDelete('{{ $item->id }}')">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-inbox display-4 mb-3 d-block"></i>
                                            <p class="fw-bold">Belum ada data anggota kelas</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-4">
            <div class="bg-white px-3 py-2 rounded-pill border shadow-sm d-flex align-items-center gap-2">
                <span class="spinner-grow spinner-grow-sm text-primary" role="status"></span>
                <small class="fw-bold text-muted">API Connection: <span class="text-primary">Syncing External
                        Data</span></small>
            </div>
        </div>
    </div>
@endsection