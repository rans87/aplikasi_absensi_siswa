@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4" style="background-color: #f0f5fa; min-height: 100vh;">
        <div class="row justify-content-center">
            <div class="col-12">

                {{-- Header Card --}}
                <div class="card border-0 shadow-sm mb-4"
                    style="border-radius: 15px; background: linear-gradient(45deg, #007bff, #00d2ff);">
                    <div class="card-body p-4 text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="fw-bold mb-1">Manajemen Rombongan Belajar</h3>
                                <p class="mb-0 opacity-75">Kelola data kelas dan jurusan dengan mudah dan cepat.</p>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('rombongan-belajar.sync') }}"
                                    class="btn btn-light text-primary fw-bold shadow-sm rounded-pill px-4">
                                    <i class="bi bi-cloud-arrow-down-fill me-2"></i> Sinkron API
                                </a>
                                <a href="{{ route('rombongan-belajar.create') }}"
                                    class="btn btn-white bg-white text-dark fw-bold shadow-sm rounded-pill px-4 border-0">
                                    <i class="bi bi-plus-lg me-2"></i> Tambah Kelas
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Main Content Card --}}
                <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                    <div class="card-body p-0">

                        {{-- Alert Messages --}}
                        @if(session('success'))
                            <div class="alert alert-success border-0 rounded-0 mb-0 d-flex align-items-center">
                                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                            </div>
                        @endif

                        {{-- Search Section --}}
                        <div class="p-4 bg-white border-bottom">
                            <form action="{{ route('rombongan-belajar.index') }}" method="GET">
                                <div class="input-group" style="max-width: 400px;">
                                    <input type="text" name="search" class="form-control border-light bg-light"
                                        placeholder="Cari kelas atau jurusan..." value="{{ request('search') }}"
                                        style="border-radius: 10px 0 0 10px;">
                                    <button class="btn btn-primary px-4" type="submit"
                                        style="border-radius: 0 10px 10px 0;">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Table --}}
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-secondary">
                                    <tr>
                                        <th class="ps-4 py-3 text-uppercase fs-xs fw-bold">Nama Kelas</th>
                                        <th class="py-3 text-uppercase fs-xs fw-bold">Jurusan</th>
                                        <th class="py-3 text-uppercase fs-xs fw-bold text-center">Tingkat</th>
                                        <th class="pe-4 py-3 text-uppercase fs-xs fw-bold text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rombel as $r)
                                        <tr style="transition: all 0.3s ease;">
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 me-3">
                                                        <i class="bi bi-building-fill fs-5"></i>
                                                    </div>
                                                    <span class="fw-bold text-dark">{{ $r->nama_kelas }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $r->jurusan }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="badge rounded-pill bg-info bg-opacity-10 text-info px-3 py-2 fw-semibold">
                                                    Kelas {{ $r->tingkat }}
                                                </span>
                                            </td>
                                            <td class="pe-4 text-end">
                                                <div class="dropdown">
                                                    <button class="btn btn-light btn-sm rounded-circle" type="button"
                                                        data-bs-toggle="dropdown">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('rombongan-belajar.edit', $r->id) }}"><i
                                                                    class="bi bi-pencil me-2"></i> Edit</a></li>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('rombongan-belajar.destroy', $r->id) }}"
                                                                method="POST" onsubmit="return confirm('Hapus data ini?')">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger"><i
                                                                        class="bi bi-trash me-2"></i> Hapus</button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5">
                                                <img src="https://illustrations.popsy.co/blue/box.svg" alt="Empty"
                                                    style="width: 150px;" class="mb-3">
                                                <h5 class="text-muted">Ups! Tidak ada data ditemukan.</h5>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination Section --}}
                        <div class="p-4 d-flex justify-content-between align-items-center bg-light">
                            <p class="text-muted mb-0 small">
                                Menampilkan {{ $rombel->firstItem() }} sampai {{ $rombel->lastItem() }} dari
                                {{ $rombel->total() }} data
                            </p>
                            <div class="pagination-custom">
                                {{ $rombel->links('pagination::bootstrap-5') }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Custom Styling untuk mempercantik */
        .fs-xs {
            font-size: 0.75rem;
        }

        .btn-white:hover {
            background-color: #f8f9fa !important;
        }

        .table tbody tr:hover {
            background-color: #f8fbff;
            transform: scale(1.002);
        }

        .pagination {
            margin-bottom: 0;
        }

        .page-link {
            border: none;
            color: #007bff;
            border-radius: 8px !important;
            margin: 0 2px;
        }

        .page-item.active .page-link {
            background-color: #007bff;
        }
    </style>
@endsection