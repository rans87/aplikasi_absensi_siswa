@extends('layouts.app')

@section('title', 'Tambah Anggota Kelas')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Tambah Anggota</h1>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card shadow-lg border-0">
                    <div class="card-header py-3" style="background: linear-gradient(45deg, #2c1cbaea, #6a3cc0);">
                        <h5 class="mb-0 text-white"><i class="bi bi-person-plus me-2"></i> Form Registrasi Kelas</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('anggota-kelas.store') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label fw-bold text-secondary small uppercase">Pilih Siswa</label>
                                <select name="siswa_id" class="form-select form-select-lg"
                                    style="border-radius: 12px; background-color: #f8faff;">
                                    <option value="" disabled selected>-- Cari Siswa --</option>
                                    @foreach($siswa as $s)
                                        <option value="{{ $s->id }}">{{ $s->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold text-secondary small uppercase">Rombongan
                                        Belajar</label>
                                    <select name="rombongan_belajar_id" class="form-select" style="border-radius: 12px;">
                                        @foreach($rombel as $r)
                                            <option value="{{ $r->id }}">{{ $r->nama_rombel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold text-secondary small uppercase">Tahun Ajar</label>
                                    <select name="tahun_ajar_id" class="form-select" style="border-radius: 12px;">
                                        @foreach($tahunAjar as $t)
                                            <option value="{{ $t->id }}">{{ $t->tahun }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <a href="{{ route('anggota-kelas.index') }}"
                                    class="text-decoration-none text-muted fw-bold">Batal</a>
                                <button type="submit" class="btn btn-primary px-5 py-2 shadow-sm"
                                    style="border-radius: 12px; font-weight: 700;">
                                    Simpan Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection