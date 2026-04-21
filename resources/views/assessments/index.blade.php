@extends('layouts.app')

@section('title', 'Evaluasi Performa Siswa')

@section('content')
<div class="assessment-container py-4 px-3 px-md-5">
    <!-- Header Section -->
    <div class="header-banner mb-5 p-5 animate__animated animate__fadeIn">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-white bg-opacity-20 text-white border border-white border-opacity-30 px-3 py-2 rounded-pill mb-3 ls-1 fw-bold">
                    MANAGEMENT PERFORMANCE
                </span>
                <h1 class="display-5 fw-black text-white mb-2 ls-tight">Evaluasi Karakter Siswa</h1>
                <p class="text-white text-opacity-80 fs-5 mb-0 fw-medium">Lakukan penilaian sikap dan perkembangan kompetensi siswa secara berkala.</p>
            </div>
            @if(Auth::guard('web')->check())
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                <a href="{{ route('assessments.categories.index') }}" class="btn btn-premium-white">
                    <i class="bi bi-gear-fill me-2"></i> KATEGORI PENILAIAN
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="stat-card-modern bg-white shadow-sm border-0 rounded-4 p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="stat-icon bg-blue-soft text-primary">
                        <i class="bi bi-person-check-fill fs-3"></i>
                    </div>
                </div>
                <h6 class="text-muted fw-bold small text-uppercase mb-1">PROGRES PENILAIAN BULAN INI</h6>
                <div class="d-flex align-items-end gap-2 mb-3">
                    <h2 class="fw-black mb-0">{{ $progress }}%</h2>
                    <span class="text-muted small mb-1 fw-bold">({{ $assessedThisMonth }}/{{ $totalSiswa }})</span>
                </div>
                <div class="progress rounded-pill" style="height: 8px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $progress }}%"></div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="search-card-modern bg-white shadow-sm border-0 rounded-4 p-4 h-100 d-flex align-items-center">
                <form action="{{ route('assessments.index') }}" method="GET" class="w-100">
                    <div class="input-group input-group-lg search-box-modern">
                        <span class="input-group-text bg-light border-0 ps-4"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-0 py-4 fs-6" 
                               placeholder="Cari nama siswa untuk dinilai..." value="{{ request('search') }}">
                        <button class="btn btn-primary px-5 fw-bold" type="submit">CARI</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Student Grid -->
    <div class="row g-4 mb-5">
        @foreach($users as $user)
        <div class="col-xl-3 col-md-4 col-sm-6 animate__animated animate__fadeInUp" style="animation-delay: {{ $loop->index * 0.05 }}s;">
            <div class="user-card-modern bg-white shadow-sm border-0 rounded-4 overflow-hidden position-relative">
                <div class="card-gradient-top bg-primary"></div>
                <div class="p-4 text-center">
                    <div class="avatar-container mb-3 mt-n5">
                        <div class="avatar-lg bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center mx-auto text-primary fs-2 fw-black border border-5 border-white">
                            {{ substr($user->nama, 0, 1) }}
                        </div>
                    </div>
                    <h5 class="fw-black text-dark mb-1 text-truncate">{{ $user->nama }}</h5>
                    <p class="text-muted small fw-bold mb-4">NIS: {{ $user->nis }}</p>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('assessments.create', $user->id) }}" class="btn btn-primary rounded-pill fw-bold">
                            <i class="bi bi-star-fill me-1"></i> BERI NILAI
                        </a>
                        <a href="{{ route('assessments.report', $user->id) }}" class="btn btn-outline-primary rounded-pill fw-bold">
                            <i class="bi bi-bar-chart-fill me-1"></i> LIHAT RAPOR
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $users->links() }}
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800;900&display=swap');
    
    .assessment-container { font-family: 'Outfit', sans-serif; background-color: #f8fafc; min-height: 100vh; }
    .fw-black { font-weight: 800; }
    .ls-1 { letter-spacing: 1px; }
    .ls-tight { letter-spacing: -1.5px; }

    .header-banner {
        background: linear-gradient(135deg, #0d6efd 0%, #00d2ff 100%);
        border-radius: 40px;
        box-shadow: 0 20px 40px -10px rgba(13, 110, 253, 0.4);
    }

    .btn-premium-white {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.3);
        backdrop-filter: blur(10px);
        padding: 12px 25px;
        border-radius: 15px;
        font-weight: 800;
        transition: all 0.3s;
    }
    .btn-premium-white:hover {
        background: white;
        color: #0d6efd;
        transform: translateY(-3px);
    }

    .stat-card-modern { transition: 0.3s; }
    .stat-card-modern:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.05) !important; }
    .stat-icon { width: 50px; height: 50px; border-radius: 15px; display: flex; align-items: center; justify-content: center; }
    .bg-blue-soft { background-color: #eef2ff; }

    .search-box-modern input { border-top-left-radius: 20px !important; border-bottom-left-radius: 20px !important; }
    .search-box-modern .btn { border-top-right-radius: 20px !important; border-bottom-right-radius: 20px !important; }

    .user-card-modern { transition: 0.3s; }
    .user-card-modern:hover { transform: translateY(-10px); box-shadow: 0 30px 60px -15px rgba(0,0,0,0.1) !important; }
    .card-gradient-top { height: 80px; }
    .mt-n5 { margin-top: -3rem !important; }
    .avatar-lg { width: 80px; height: 80px; }
</style>
@endsection
