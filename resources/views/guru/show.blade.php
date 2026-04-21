@extends('layouts.app')

@section('title', 'Biodata Guru - ' . $guru->nama)

@section('content')
<div class="row g-4 fade-in">
    <div class="col-lg-4">
        <div class="card border-0 shadow-premium overflow-hidden h-100" style="border-radius: 30px;">
            <div class="card-body p-0">
                <div class="position-relative">
                    {{-- Profile Header Background --}}
                    <div class="bg-primary p-5" style="background: linear-gradient(135deg, var(--deep-blue), var(--primary-blue));"></div>
                    
                    {{-- Profile Picture Overlay --}}
                    <div class="text-center" style="margin-top: -60px;">
                        <div class="d-inline-block p-2 bg-white rounded-circle shadow-sm position-relative">
                            @if($guru->foto)
                                <img src="{{ $guru->foto }}" alt="{{ $guru->nama }}" class="rounded-circle object-fit-cover shadow-premium" style="width: 120px; height: 120px;">
                            @else
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary fw-extrabold shadow-premium" style="width: 120px; height: 120px; font-size: 3rem;">
                                    {{ substr($guru->nama, 0, 1) }}
                                </div>
                            @endif
                            <div class="position-absolute bottom-0 end-0 bg-emerald p-2 rounded-circle border border-white border-4 shadow-sm"></div>
                        </div>
                    </div>
                </div>

                <div class="p-4 text-center">
                    <h4 class="fw-extrabold text-dark mb-1">{{ $guru->nama }}</h4>
                    <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill fw-bold mb-4 ls-1">TENAGA PENDIDIK</span>
                    
                    <div class="d-flex justify-content-center gap-2 mb-4">
                        <div class="glass-pill px-3 py-2 rounded-4 small">
                            <i class="bi bi-shield-check me-1 text-emerald"></i>TERVERIFIKASI
                        </div>
                        <div class="glass-pill px-3 py-2 rounded-4 small">
                            <i class="bi bi-broadcast me-1 text-info"></i>AKTIF
                        </div>
                    </div>

                    @if($guru->kelasWali)
                    <div class="p-3 bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-4 mb-4 text-center">
                        <div class="text-warning small fw-bold mb-1 ls-1" style="font-size: 10px;">PENUGASAN KHUSUS</div>
                        <div class="fw-extrabold text-dark small"><i class="bi bi-person-check-fill me-2 text-warning"></i>WALI KELAS: {{ $guru->kelasWali->nama_kelas }}</div>
                    </div>
                    @endif

                    <div class="row g-3 text-start mt-2">
                        <div class="col-12">
                            <div class="p-3 bg-light rounded-4 border-0 hover-up-premium-mini transition-all">
                                <div class="text-muted small fw-bold mb-1 ls-1 uppercase" style="font-size: 10px;">NIK GURU</div>
                                <div class="fw-extrabold text-dark small ls-tight">{{ $guru->nik ?: '-' }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 bg-light rounded-4 border-0 hover-up-premium-mini transition-all">
                                <div class="text-muted small fw-bold mb-1 ls-1 uppercase" style="font-size: 10px;">ID SISTEM</div>
                                <div class="fw-extrabold text-dark small ls-tight opacity-50">{{ $guru->external_guru_id ?: $guru->id }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light border-0 p-4">
                <a href="{{ route('guru.index') }}" class="btn btn-outline-dark w-100 rounded-pill fw-bold py-2 shadow-sm transition-all hover-up-premium-mini">
                    <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-premium h-100" style="border-radius: 30px;">
            <div class="card-header bg-transparent border-0 p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary-soft p-2 rounded-4 text-primary">
                        <i class="bi bi-person-lines-fill fs-4"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-extrabold text-dark fs-4">Informasi Biodata Lengkap</h5>
                        <p class="text-muted mb-0 small fw-medium">Detail profil guru yang sinkron dengan API Zielabs</p>
                    </div>
                </div>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="row g-4">
                    {{-- Identity Section --}}
                    <div class="col-12">
                        <h6 class="fw-bold text-dark mb-3 ps-2 border-start border-primary border-4 ls-1">IDENTITAS RESMI</h6>
                        <div class="row g-3">
                            <div class="col-md-6 text-start">
                                <div class="p-4 rounded-5 bg-white border border-light-subtle shadow-sm hover-up-premium-mini">
                                    <div class="text-muted small fw-bold mb-1 ls-1" style="font-size: 10px;">NIP (NOMOR INDUK PEGAWAI)</div>
                                    <div class="fw-extrabold text-primary fs-5">{{ $guru->nip ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6 text-start">
                                <div class="p-4 rounded-5 bg-white border border-light-subtle shadow-sm hover-up-premium-mini">
                                    <div class="text-muted small fw-bold mb-1 ls-1" style="font-size: 10px;">NUPTK</div>
                                    <div class="fw-extrabold text-indigo fs-5">{{ $guru->nuptk ?: '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Personal Stats Section --}}
                    <div class="col-12">
                        <h6 class="fw-bold text-dark mb-3 ps-2 border-start border-emerald border-4 ls-1">DETAIL PERSONAL</h6>
                        <div class="row g-3">
                            <div class="col-md-4 text-start">
                                <div class="p-4 rounded-5 bg-white border border-light-subtle shadow-sm h-100">
                                    <div class="text-muted small fw-bold mb-1 ls-1" style="font-size: 10px;">JENIS KELAMIN</div>
                                    <div class="fw-bold text-dark fs-5">
                                        {{ $guru->jenis_kelamin == 'L' ? 'Laki-Laki' : 'Perempuan' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8 text-start">
                                <div class="p-4 rounded-5 bg-white border border-light-subtle shadow-sm h-100">
                                    <div class="text-muted small fw-bold mb-1 ls-1" style="font-size: 10px;">TEMPAT, TANGGAL LAHIR</div>
                                    <div class="fw-bold text-dark fs-5">
                                        {{ $guru->tempat_lahir ?: '-' }}, {{ $guru->tanggal_lahir ? \Carbon\Carbon::parse($guru->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Contact Section --}}
                    <div class="col-12">
                        <h6 class="fw-bold text-dark mb-3 ps-2 border-start border-rose border-4 ls-1">KONTAK & ALAMAT</h6>
                        <div class="row g-3">
                            <div class="col-md-6 text-start">
                                <div class="p-4 rounded-5 bg-white border border-light-subtle shadow-sm h-100">
                                    <div class="d-flex align-items-center gap-3 mb-2 text-primary">
                                        <i class="bi bi-envelope-at-fill"></i>
                                        <div class="text-muted small fw-bold ls-1" style="font-size: 10px;">E-MAIL RESMI</div>
                                    </div>
                                    <div class="fw-bold text-dark">{{ $guru->email ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6 text-start">
                                <div class="p-4 rounded-5 bg-white border border-light-subtle shadow-sm h-100">
                                    <div class="d-flex align-items-center gap-3 mb-2 text-emerald">
                                        <i class="bi bi-whatsapp"></i>
                                        <div class="text-muted small fw-bold ls-1" style="font-size: 10px;">NO. HANDPHONE</div>
                                    </div>
                                    <div class="fw-bold text-dark">{{ $guru->no_hp ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12 text-start">
                                <div class="p-4 rounded-5 bg-white border border-light-subtle shadow-sm h-100">
                                    <div class="d-flex align-items-center gap-3 mb-2 text-amber">
                                        <i class="bi bi-geo-alt-fill"></i>
                                        <div class="text-muted small fw-bold ls-1" style="font-size: 10px;">ALAMAT LENGKAP</div>
                                    </div>
                                    <div class="fw-bold text-dark fs-6 lh-base">{{ $guru->alamat ?: 'Alamat belum diatur dalam sistem.' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 p-4">
                <div class="p-4 rounded-5 bg-primary bg-opacity-10 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary p-3 rounded-circle text-white pulse">
                            <i class="bi bi-qr-code"></i>
                        </div>
                        <div>
                            <div class="fw-extrabold text-primary small">Sistem Autentikasi Modern</div>
                            <div class="text-muted small fw-medium">Login menggunakan NIP terverifikasi</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .ls-tight { letter-spacing: -1px; }
    .ls-extratight { letter-spacing: -2px; }
    .ls-1 { letter-spacing: 0.5px; }
    .ls-2 { letter-spacing: 1.5px; }
    .uppercase { text-transform: uppercase; }
    
    .hover-up-premium-mini:hover { transform: translateY(-3px); border-color: var(--primary-blue) !important; box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; }
    .bg-primary-soft { background-color: #eff6ff; }
    .bg-light-subtle { background-color: #f8fafc; }
    .text-indigo { color: #6366f1; }
    .glass-pill { background: rgba(0, 0, 0, 0.03); border: 1px solid rgba(0,0,0,0.05); font-weight: 700; color: #64748b; letter-spacing: 0.5px; }
    
    .pulse { animation: pulseSmall 2s infinite; }
    @keyframes pulseSmall { 0% { transform: scale(1); } 50% { transform: scale(1.05); } 100% { transform: scale(1); } }
</style>
@endsection
