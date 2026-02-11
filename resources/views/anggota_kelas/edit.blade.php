@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header text-white rounded-top-4" style="background: linear-gradient(135deg,#396afc,#2948ff);">
                <h4><i class="bi bi-pencil-square me-2"></i>Edit Anggota Kelas</h4>
            </div>

            <div class="card-body">
                <form action="{{ route('anggota-kelas.update', $anggota->id) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label text-primary fw-semibold">Siswa</label>
                        <select name="siswa_id" class="form-select rounded-3 shadow-sm">
                            @foreach($siswas as $s)
                                <option value="{{ $s['id'] }}" {{ $anggota->siswa_id == $s['id'] ? 'selected' : '' }}>
                                    {{ $s['nama'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-primary fw-semibold">Rombel</label>
                        <select name="rombongan_belajar_id" class="form-select rounded-3 shadow-sm">
                            @foreach($rombels as $r)
                                <option value="{{ $r['id'] }}" {{ $anggota->rombongan_belajar_id == $r['id'] ? 'selected' : '' }}>
                                    {{ $r['nama'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-primary fw-semibold">Tahun Ajar</label>
                        <select name="tahun_ajar_id" class="form-select rounded-3 shadow-sm">
                            @foreach($tahunAjar as $t)
                                <option value="{{ $t->id }}" {{ $anggota->tahun_ajar_id == $t->id ? 'selected' : '' }}>
                                    {{ $t->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button class="btn btn-primary rounded-3 px-4 shadow-sm">
                        <i class="bi bi-save"></i> Update
                    </button>
                    <a href="{{ route('anggota-kelas.index') }}" class="btn btn-outline-secondary rounded-3">
                        Kembali
                    </a>
                </form>
            </div>
        </div>
    </div>
@endsection