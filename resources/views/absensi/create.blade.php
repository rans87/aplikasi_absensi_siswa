@extends('layouts.app')

@section('content')
    <div class="container py-4">

        <div class="card border-0 shadow-lg rounded-4">
            <div class="card-header text-white" style="background: linear-gradient(135deg, #1E88E5, #42A5F5);">
                <h5 class="mb-0">📝 Input Absensi Siswa</h5>
            </div>

            <div class="card-body bg-light">
                <form action="{{ route('absensi.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Siswa</label>
                            <select name="siswa_id" class="form-select shadow-sm" required>
                                <option value="">-- Pilih Siswa --</option>
                                @foreach($siswa as $s)
                                    <option value="{{ $s->id }}">{{ $s->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Guru</label>
                            <select name="guru_id" class="form-select shadow-sm" required>
                                <option value="">-- Pilih Guru --</option>
                                @foreach($guru as $g)
                                    <option value="{{ $g->id }}">{{ $g->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Rombongan Belajar</label>
                            <select name="rombongan_belajar_id" class="form-select shadow-sm" required>
                                <option value="">-- Pilih Rombel --</option>
                                @foreach($rombongan as $r)
                                    <option value="{{ $r->id }}">
                                        {{ $r->nama_kelas }} - {{ $r->jurusan }} (Tingkat {{ $r->tingkat }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control shadow-sm" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Status Kehadiran</label>
                            <select name="status" class="form-select shadow-sm" required>
                                <option value="hadir">Hadir</option>
                                <option value="izin">Izin</option>
                                <option value="sakit">Sakit</option>
                                <option value="alfa">Alfa</option>
                            </select>
                        </div>

                    </div>

                    <div class="mt-4 text-end">
                        <button class="btn btn-primary px-4 rounded-pill shadow-sm">
                            💾 Simpan Absensi
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
@endsection