@extends('layouts.app')

@section('title', 'Pemberian Poin Manual')

@section('content')
<div class="row g-4">
    {{-- Header --}}
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="display-6 fw-extrabold text-dark ls-tight mb-1">🎯 Pemberian Poin Manual</h1>
                <p class="text-muted fw-medium mb-0">Berikan poin untuk prestasi (Juara Lomba, dsb) atau penalti manual.</p>
            </div>
            <a href="{{ route('integrity.leaderboard') }}" class="btn btn-light rounded-pill px-4 fw-bold">
                <i class="bi bi-trophy me-2 text-warning"></i>Lihat Leaderboard
            </a>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm overflow-hidden mb-4">
            <div class="card-header border-0 bg-white p-4 d-flex align-items-center justify-content-between">
                <h6 class="fw-extrabold text-dark mb-0"><i class="bi bi-search me-2 text-primary"></i>Pilih Siswa</h6>
                <form action="{{ route('integrity.manual.index') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control form-control-sm rounded-pill px-3" placeholder="Cari Nama/NIS..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary btn-sm rounded-circle"><i class="bi bi-search"></i></button>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="bg-light">
                                <th class="ps-4 py-3 small fw-bold text-muted border-0">SISWA</th>
                                <th class="small fw-bold text-muted border-0">KELAS</th>
                                <th class="small fw-bold text-muted border-0 text-center">SALDO SAAT INI</th>
                                <th class="pe-4 text-end small fw-bold text-muted border-0">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($siswa as $s)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-dark">{{ $s->nama }}</div>
                                    <div class="text-muted" style="font-size: 11px;">NIS: {{ $s->nis }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-primary-soft text-primary rounded-pill px-3 fw-bold">
                                        {{ $s->rombonganBelajar->nama_kelas ?? 'Tanpa Kelas' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-extrabold text-dark">{{ \App\Models\PointLedger::getCurrentBalance($s->id) }}</span>
                                    <small class="text-muted fw-bold ms-1" style="font-size: 9px;">POIN</small>
                                </td>
                                <td class="pe-4 text-end">
                                    <button class="btn btn-sm btn-primary rounded-pill px-4 fw-bold" 
                                            onclick="openAwardModal('{{ $s->id }}', '{{ $s->nama }}', '{{ \App\Models\PointLedger::getCurrentBalance($s->id) }}')">
                                        PILIH
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="text-muted">Siswa tidak ditemukan.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($siswa->hasPages())
            <div class="card-footer bg-white border-0 p-3">
                {{ $siswa->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- Side Info --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 mb-4 bg-primary text-white rounded-5">
            <h5 class="fw-extrabold mb-3">Panduan Cepat</h5>
            <ul class="list-unstyled mb-0">
                <li class="mb-3 d-flex gap-2">
                    <i class="bi bi-check-circle-fill"></i>
                    <small>Gunakan angka <b>positif</b> (misal: 50) untuk Prestasi/Penambahan.</small>
                </li>
                <li class="mb-3 d-flex gap-2">
                    <i class="bi bi-dash-circle-fill"></i>
                    <small>Gunakan angka <b>negatif</b> (misal: -20) untuk Pelanggaran/Pengurangan.</small>
                </li>
                <li class="d-flex gap-2">
                    <i class="bi bi-info-circle-fill"></i>
                    <small>Setiap transaksi akan langsung memengaruhi saldo siswa dan tercatat di riwayat ledger.</small>
                </li>
            </ul>
        </div>

        <div class="card border-0 shadow-sm p-4 rounded-5">
             <h6 class="fw-extrabold text-dark mb-3">Contoh Penggunaan:</h6>
             <div class="d-flex flex-column gap-2">
                <div class="p-3 bg-light rounded-4">
                    <div class="fw-bold text-emerald small">PRESTASI (+50)</div>
                    <div class="text-muted italic" style="font-size: 11px;">"Juara 1 Lomba Pidato Bahasa Inggris"</div>
                </div>
                <div class="p-3 bg-light rounded-4">
                    <div class="fw-bold text-rose small">PELANGGARAN (-30)</div>
                    <div class="text-muted italic" style="font-size: 11px;">"Merusak fasilitas sekolah (Sengaja)"</div>
                </div>
             </div>
        </div>
    </div>
</div>

{{-- Award Modal --}}
<div class="modal fade" id="awardModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-5 shadow-lg">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-extrabold text-dark">Beri Poin: <span id="modalStudentName" class="text-primary"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('integrity.manual.store') }}" method="POST">
                @csrf
                <input type="hidden" name="siswa_id" id="modalSiswaId">
                <div class="modal-body p-4">
                    <div class="mb-4 text-center p-3 bg-light rounded-4">
                         <div class="text-muted small fw-bold mb-1">SALDO SAAT INI</div>
                         <h3 class="fw-black text-dark mb-0" id="modalCurrentBalance">0</h3>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Jumlah Poin</label>
                        <div class="input-group">
                            <input type="number" name="amount" class="form-control rounded-4 px-3" placeholder="Contoh: 50 atau -20" required>
                        </div>
                        <small class="text-muted">Gunakan tanda minus (-) untuk pengurangan.</small>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold small">Keterangan / Alasan</label>
                        <textarea name="description" class="form-control rounded-4 px-3" rows="3" placeholder="Contoh: Juara 1 Lomba MTQ Nasional" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .ls-tight { letter-spacing: -1.2px; }
    .bg-primary-soft { background-color: #eff6ff !important; }
    .text-emerald { color: #10b981 !important; }
    .text-rose { color: #e11d48 !important; }
    .fw-black { font-weight: 900; }
</style>

@push('scripts')
<script>
    function openAwardModal(id, name, balance) {
        document.getElementById('modalSiswaId').value = id;
        document.getElementById('modalStudentName').textContent = name;
        document.getElementById('modalCurrentBalance').textContent = balance;
        new bootstrap.Modal(document.getElementById('awardModal')).show();
    }
</script>
@endpush
@endsection
