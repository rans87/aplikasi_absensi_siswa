@extends('layouts.app')

@section('title', 'Edit Pelanggaran')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <h1 class="m-0 text-primary">Edit Data Pelanggaran</h1>
    </div>
</div>

<div class="container-fluid pb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4">
                <form action="{{ route('pelanggaran.update', $pelanggaran->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Siswa</label>
                        <select name="siswa_id" class="form-select form-control" required>
                            @foreach($siswa as $s)
                                <option value="{{ $s->id }}" {{ $pelanggaran->siswa_id == $s->id ? 'selected' : '' }}>
                                    {{ $s->nama }} ({{ $s->nis }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Nama Pelanggaran</label>
                        <input type="text" name="nama_pelanggaran" class="form-control" value="{{ $pelanggaran->nama_pelanggaran }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Poin Pengurangan</label>
                        <input type="number" name="poin" class="form-control" value="{{ $pelanggaran->poin }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3">{{ $pelanggaran->keterangan }}</textarea>
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('pelanggaran.index') }}" class="btn btn-outline-secondary px-4 rounded-pill">Batal</a>
                        <button type="submit" class="btn btn-primary px-5 rounded-pill shadow-sm">Update Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
