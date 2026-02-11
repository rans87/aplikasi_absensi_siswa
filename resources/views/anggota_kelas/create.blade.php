@extends('layouts.app')

@section('content')
    <div class="container py-4">

        <div class="card shadow-lg border-0">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">➕ Tambah Anggota Kelas</h4>
            </div>

            <div class="card-body bg-white">

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('anggota-kelas.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">Siswa</label>
                        <select name="siswa_id" class="form-select">
                            <option value="">-- Pilih Siswa --</option>
                            @foreach($siswa as $s)
                                <option value="{{ $s->id }}">{{ $s->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">Rombongan Belajar</label>
                        <select name="rombongan_belajar_id" class="form-select">
                            <option value="">-- Pilih Rombel --</option>
                            @foreach($rombel as $r)
                                <option value="{{ $r->id }}">
                                    {{ $r->nama_kelas }} - {{ $r->jurusan }} (Tingkat {{ $r->tingkat }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">Tahun Ajar</label>
                        <select name="tahun_ajar_id" class="form-select">
                            @foreach($tahunAjar as $t)
                                <option value="{{ $t->id }}">{{ $t->tahun }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button class="btn btn-primary w-100 fw-bold">💾 Simpan</button>
                    <a href="{{ route('anggota-kelas.index') }}" class="btn btn-outline-primary w-100 mt-2">Kembali</a>
                </form>

            </div>
        </div>

    </div>
@endsection