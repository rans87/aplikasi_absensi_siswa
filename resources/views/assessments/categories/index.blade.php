@extends('layouts.app')

@section('title', 'Kelola Kategori Penilaian')

@section('content')
<div class="category-manager py-5 px-3">
    <div class="container">
        <!-- Header -->
        <div class="row align-items-center mb-5 g-4">
            <div class="col-md-7">
                <a href="{{ route('assessments.index') }}" class="text-decoration-none small fw-bold text-primary mb-2 d-inline-block">
                    <i class="bi bi-arrow-left"></i> KEMBALI KE PENILAIAN
                </a>
                <h2 class="fw-black text-dark mb-0">Manajemen Kategori</h2>
                <p class="text-muted">Kelola indikator penilaian karakter secara dinamis.</p>
            </div>
            <div class="col-md-5 text-md-end">
                <button class="btn btn-primary rounded-pill px-4 py-2 fw-black shadow-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="bi bi-plus-lg me-2"></i> TAMBAH INDIKATOR
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 border-0">NAMA INDIKATOR</th>
                            <th class="px-4 py-3 border-0">DESKRIPSI</th>
                            <th class="px-4 py-3 border-0">TIPE</th>
                            <th class="px-4 py-3 border-0">STATUS</th>
                            <th class="px-4 py-3 border-0 text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                        <tr>
                            <td class="px-4 py-3">
                                <span class="fw-bold text-dark">{{ $category->name }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-muted small">{{ Str::limit($category->description, 50) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge bg-secondary rounded-pill px-3">{{ strtoupper($category->type) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($category->is_active)
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 fw-bold">AKTIF</span>
                                @else
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 fw-bold">NON-AKTIF</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-light rounded-pill me-2" data-bs-toggle="modal" data-bs-target="#editCategory{{ $category->id }}">
                                        <i class="bi bi-pencil-fill text-primary"></i>
                                    </button>
                                    <form action="{{ route('assessments.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-light rounded-pill">
                                            <i class="bi bi-trash-fill text-danger"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editCategory{{ $category->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <form action="{{ route('assessments.categories.update', $category->id) }}" method="POST" class="modal-content border-0 rounded-4 shadow">
                                    @csrf @method('PUT')
                                    <div class="modal-header border-0 p-4">
                                        <h5 class="fw-black mb-0">Edit Indikator</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4 pt-0">
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Nama Indikator</label>
                                            <input type="text" name="name" class="form-control rounded-3" value="{{ $category->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Tipe</label>
                                            <select name="type" class="form-select rounded-3">
                                                <option value="student" {{ $category->type == 'student' ? 'selected' : '' }}>Student/Siswa</option>
                                                <option value="employee" {{ $category->type == 'employee' ? 'selected' : '' }}>Employee/Staf</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Deskripsi</label>
                                            <textarea name="description" class="form-control rounded-3" rows="3">{{ $category->description }}</textarea>
                                        </div>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $category->is_active ? 'checked' : '' }}>
                                            <label class="form-check-label small fw-bold">Status Aktif</label>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 p-4">
                                        <button type="button" class="btn btn-light px-4 rounded-3 fw-bold" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary px-4 rounded-3 fw-bold">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('assessments.categories.store') }}" method="POST" class="modal-content border-0 rounded-4 shadow">
            @csrf
            <div class="modal-header border-0 p-4">
                <h5 class="fw-black mb-0">Indikator Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Nama Indikator</label>
                    <input type="text" name="name" class="form-control rounded-3" placeholder="Contoh: Kedisiplinan" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Tipe</label>
                    <select name="type" class="form-select rounded-3">
                        <option value="student">Student/Siswa</option>
                        <option value="employee">Employee/Staf</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Deskripsi</label>
                    <textarea name="description" class="form-control rounded-3" rows="3" placeholder="Jelaskan apa yang dinilai dari indikator ini..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-light px-4 rounded-3 fw-bold" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary px-4 rounded-3 fw-bold">Simpan</button>
            </div>
        </form>
    </div>
</div>

<style>
    .category-manager { background: #f8fafc; min-height: 100vh; }
    .fw-black { font-weight: 800; }
</style>
@endsection
