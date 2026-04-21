@extends('layouts.app')

@section('title', 'Marketplace - Toko Tiket Integritas')

@section('content')
<div class="container-fluid px-4 md:px-5 fade-in">
    {{-- Dynamic Header Section --}}
    <div class="row mb-5 align-items-center">
        <div class="col-md-7">
            <h1 class="display-5 fw-black text-dark ls-tight mb-2"><i class="bi bi-ticket-perforated-fill text-primary me-2"></i>Tiket Marketplace</h1>
            <p class="text-muted fw-medium fs-6">Kelola instrumen pengurang denda keterlambatan untuk menjaga moralitas siswa.</p>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0">
            <button class="btn btn-primary rounded-pill px-5 py-3 fw-black shadow-lg hover-up transition-all" data-bs-toggle="modal" data-bs-target="#addItemModal">
                <i class="bi bi-plus-lg me-2"></i>BUAT TIKET BARU
            </button>
        </div>
    </div>

    {{-- Logic Explanation Card (Responsive) --}}
    <div class="card border-0 shadow-sm rounded-5 overflow-hidden mb-5">
        <div class="card-body p-4 p-md-5">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-lg" style="width:50px; height:50px;">
                    <i class="bi bi-lightbulb-fill fs-4"></i>
                </div>
                <h5 class="fw-extrabold text-dark mb-0">Bagaimana Tiket Ini Bekerja?</h5>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="p-4 rounded-5 h-100 position-relative overflow-hidden border border-primary border-opacity-10 shadow-sm" style="background: linear-gradient(135deg, #f0f7ff 0%, #ffffff 100%);">
                        <span class="badge bg-primary text-white rounded-pill px-3 py-2 fw-bold mb-3 ls-1">MODE OTOMATIS</span>
                        <p class="text-dark fw-bold mb-2">Denda langsung dipotong saat absen.</p>
                        <p class="text-muted small mb-0 italic">"Sifatnya preventif. Siswa tidak perlu khawatir poinnya habis drastis jika sesekali terlambat."</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="p-4 rounded-5 h-100 position-relative overflow-hidden border border-slate-200 shadow-sm bg-white">
                        <span class="badge bg-secondary text-white rounded-pill px-3 py-2 fw-bold mb-3 ls-1">MODE MANUAL</span>
                        <p class="text-dark fw-bold mb-2">Siswa memilih kapan menggunakan tiket.</p>
                        <p class="text-muted small mb-0 italic">"Memberikan kendali penuh kepada siswa untuk memulihkan poin mereka di waktu yang krusial."</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Items Grid --}}
    <div class="row g-4 mb-5">
        @forelse($items as $item)
        <div class="col-xl-4 col-lg-6 col-md-6 col-12">
            <div class="card border-0 shadow-premium rounded-5 h-100 overflow-hidden hover-up transition-all {{ !$item->is_active ? 'opacity-70 grayscale' : '' }}">
                {{-- Status Flag --}}
                <div class="position-absolute top-0 end-0 p-4">
                    <form method="POST" action="{{ route('integrity.items.toggle', $item->id) }}">
                        @csrf
                        <button type="submit" class="badge {{ $item->is_active ? 'bg-emerald text-white shadow-emerald' : 'bg-light text-muted' }} border-0 rounded-pill px-3 py-2 fw-bold text-uppercase ls-1" style="font-size: 9px;">
                            {{ $item->is_active ? 'Status: Aktif' : 'Status: Nonaktif' }}
                        </button>
                    </form>
                </div>

                <div class="card-body p-4 p-md-5 text-center">
                    <div class="mx-auto mb-4 d-flex align-items-center justify-content-center bg-primary-soft rounded-5 shadow-inner" style="width: 90px; height: 90px;">
                        <i class="bi bi-ticket-perforated-fill text-primary display-4"></i>
                    </div>
                    
                    <h4 class="fw-black text-dark mb-2 ls-tight">{{ $item->item_name }}</h4>
                    <p class="text-muted small mb-4 px-3">{{ $item->description ?? 'Gunakan otomatis untuk memaafkan keterlambatan.' }}</p>

                    <div class="d-flex gap-3 justify-content-center mb-5">
                        <div class="text-center px-3 py-2 rounded-4 bg-amber-soft border border-warning border-opacity-10 h-100">
                            <small class="text-muted d-block fw-bold text-uppercase ls-1 mb-1" style="font-size: 8px;">Harga Beli</small>
                            <span class="text-warning fw-black fs-4"><i class="bi bi-stars me-1"></i>{{ $item->point_cost }}</span>
                        </div>
                        <div class="text-center px-3 py-2 rounded-4 bg-emerald-soft border border-emerald border-opacity-10 h-100">
                            <small class="text-muted d-block fw-bold text-uppercase ls-1 mb-1" style="font-size: 8px;">Nilai Diskon</small>
                            <span class="text-emerald fw-black fs-4">+{{ $item->tolerance_minutes }} Poin</span>
                        </div>
                    </div>

                    <div class="d-flex gap-2 px-md-3">
                        <button class="btn btn-light rounded-pill flex-fill fw-bold py-3 shadow-sm hover-bg-primary transition-all" onclick="editItem({{ json_encode($item) }})">
                            <i class="bi bi-pencil-square me-2"></i>Edit
                        </button>
                        <form method="POST" action="{{ route('integrity.items.destroy', $item->id) }}" class="flex-fill" onsubmit="return confirm('Hapus tiket ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-light rounded-pill w-100 fw-bold py-3 shadow-sm text-danger hover-bg-danger transition-all">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 py-5 text-center">
            <div class="bg-light rounded-circle d-inline-flex p-5 mb-3 opacity-50">
                <i class="bi bi-ticket display-1 text-muted"></i>
            </div>
            <h4 class="fw-bold text-muted">Belum ada tiket tersedia</h4>
        </div>
        @endforelse
    </div>
</div>

{{-- MODAL STYLING --}}
<div class="modal fade" id="itemModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-5 shadow-lg overflow-hidden">
            <div class="modal-header border-0 p-4 bg-primary text-white">
                <h5 class="fw-black mb-0 ls-tight" id="itemModalTitle">Konfigurasi Tiket</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="itemForm" method="POST">
                @csrf
                <div id="methodPlaceholder"></div>
                <input type="hidden" name="category" value="attendance_token">
                <input type="hidden" name="icon" value="bi-ticket-perforated-fill">

                <div class="modal-body p-4 p-md-5 bg-light bg-opacity-50">
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label x-small fw-black text-muted text-uppercase ls-1">Nama Tiket</label>
                            <input type="text" name="item_name" id="f-item_name" class="form-control form-control-lg border-0 rounded-4 px-4 py-3 shadow-sm" placeholder="Cth: Izin Telat 15 Menit" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label x-small fw-black text-muted text-uppercase ls-1">Deskripsi Singkat</label>
                            <textarea name="description" id="f-description" class="form-control border-0 rounded-4 px-4 py-3 shadow-sm" rows="3"></textarea>
                        </div>
                        <div class="col-6">
                            <label class="form-label x-small fw-black text-muted text-uppercase ls-1">Harga (Poin)</label>
                            <input type="number" name="point_cost" id="f-point_cost" class="form-control form-control-lg border-0 rounded-4 px-4 py-3 shadow-sm" required min="1">
                        </div>
                        <div class="col-6">
                            <label class="form-label x-small fw-black text-muted text-uppercase ls-1">Nilai Diskon (Poin)</label>
                            <input type="number" name="tolerance_minutes" id="f-tolerance_minutes" class="form-control form-control-lg border-0 rounded-4 px-4 py-3 shadow-sm" required min="1">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 bg-white d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-3 fw-black shadow-lg">SIMPAN TIKET</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .shadow-premium { box-shadow: 0 30px 60px rgba(0,0,0,0.06) !important; }
    .shadow-inner { box-shadow: inset 0 2px 4px rgba(0,0,0,0.05) !important; }
    .shadow-emerald { box-shadow: 0 4px 15px rgba(16,185,129,0.3) !important; }
    .bg-primary-soft { background-color: #f1f7ff !important; }
    .bg-amber-soft { background-color: #fff9eb !important; }
    .bg-emerald-soft { background-color: #ecfdf5 !important; }
    .text-emerald { color: #10b981 !important; }
    .fw-black { font-weight: 950; }
    .ls-tight { letter-spacing: -1.5px; }
    .grayscale { filter: grayscale(1); }
    
    .hover-up { transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .hover-up:hover { transform: translateY(-12px); box-shadow: 0 40px 80px rgba(0,0,0,0.12) !important; }
    
    .hover-bg-primary:hover { background-color: #2563eb !important; color: white !important; }
    .hover-bg-danger:hover { background-color: #e11d48 !important; color: white !important; }
    
    @media (max-width: 768px) {
        .rounded-5 { border-radius: 2rem !important; }
        .display-5 { font-size: 1.8rem !important; }
        .p-md-5 { padding: 1.5rem !important; }
    }
</style>

@push('scripts')
<script>
    const modal = new bootstrap.Modal(document.getElementById('itemModal'));
    const form = document.getElementById('itemForm');
    const title = document.getElementById('itemModalTitle');

    document.querySelector('[data-bs-target="#addItemModal"]').onclick = function() {
        title.innerText = 'Buat Tiket Baru';
        form.action = "{{ route('integrity.items.store') }}";
        document.getElementById('methodPlaceholder').innerHTML = '';
        form.reset();
        modal.show();
    };

    function editItem(item) {
        title.innerText = 'Edit Konfigurasi Tiket';
        form.action = `/integrity/items/${item.id}`;
        document.getElementById('methodPlaceholder').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('f-item_name').value = item.item_name;
        document.getElementById('f-description').value = item.description;
        document.getElementById('f-point_cost').value = item.point_cost;
        document.getElementById('f-tolerance_minutes').value = item.tolerance_minutes;
        modal.show();
    }
</script>
@endpush
@endsection
