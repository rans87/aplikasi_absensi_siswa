@extends('layouts.app')

@section('content')
    <div class="container py-4">

        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="card-header text-white d-flex justify-content-between align-items-center"
                style="background: linear-gradient(135deg, #2196F3, #64B5F6);">
                <h5 class="mb-0 fw-semibold">📋 Data Absensi Siswa</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('absensi.scan') }}" class="btn btn-warning fw-semibold rounded-pill px-3 shadow-sm">
                        📷 Scan QR
                    </a>

                    <a href="{{ route('absensi.create') }}"
                        class="btn btn-light text-primary fw-semibold rounded-pill px-3 shadow-sm">
                        + Input Absensi
                    </a>
                </div>

            </div>

            <div class="card-body bg-light">

                <form method="GET" class="row mb-3">
                    <div class="col-md-4">
                        <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control shadow-sm">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100 shadow-sm">Filter</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table align-middle table-hover">
                        <thead class="table-primary text-center">
                            <tr>
                                <th>No</th>
                                <th>Siswa</th>
                                <th>Guru</th>
                                <th>Rombel</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($absensi as $item)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-semibold text-start">{{ $item->siswa->nama }}</td>
                                    <td>{{ $item->guru->nama }}</td>
                                    <td>{{ $item->rombonganBelajar->nama }}</td>
                                    <td>{{ $item->tanggal->format('d M Y') }}</td>
                                    <td>
                                        @if($item->status == 'hadir')
                                            <span class="badge bg-success">Hadir</span>
                                        @elseif($item->status == 'izin')
                                            <span class="badge bg-warning text-dark">Izin</span>
                                        @elseif($item->status == 'sakit')
                                            <span class="badge bg-info text-dark">Sakit</span>
                                        @else
                                            <span class="badge bg-danger">Alfa</span>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('absensi.destroy', $item->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger rounded-pill">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Belum ada data absensi</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
@endsection