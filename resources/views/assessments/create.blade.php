@extends('layouts.app')

@section('title', 'Beri Penilaian')

@push('styles')
<style>
    .assessment-form-container { background: #f0f7ff; min-height: 100vh; padding-bottom: 50px; }
    .hero-v2 { background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); border-radius: 0 0 50px 50px; padding: 60px 0 100px 0; color: white; margin-bottom: -60px; }
    .eval-card { border-radius: 30px; border: none; box-shadow: 0 20px 50px rgba(0,0,0,0.05); }
    
    /* Star Rating */
    .star-rating { display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: 10px; }
    .star-rating input { display: none; }
    .star-rating label { font-size: 32px; color: #cbd5e1; cursor: pointer; transition: 0.2s; }
    .star-rating input:checked ~ label { color: #f59e0b; transform: scale(1.1); }
    .star-rating label:hover, .star-rating label:hover ~ label { color: #facc15; }

    .category-item { background: white; border: 1px solid #e2e8f0; border-radius: 20px; transition: 0.3s; margin-bottom: 15px; }
    .category-item:hover { border-color: #3b82f6; background: #f8fafc; }

    .floating-header { position: sticky; top: 10px; z-index: 100; backdrop-filter: blur(10px); background: rgba(255,255,255,0.8); border: 1px solid rgba(0,0,0,0.05); }
</style>
@endpush

@section('content')
<div class="assessment-form-container">
    <div class="hero-v2 text-center px-3">
        <h1 class="fw-black mb-2 animate__animated animate__fadeInDown">Form Penilaian Karakter</h1>
        <p class="text-white-50 fs-5 animate__animated animate__fadeInUp">Objektivitas Anda sangat berharga untuk perkembangan siswa.</p>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <form action="{{ route('assessments.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="evaluatee_id" value="{{ $user->id }}">
                    <input type="hidden" name="period" value="{{ Carbon\Carbon::now()->format('F Y') }}">

                    <div class="card eval-card animate__animated animate__fadeInUp">
                        <div class="card-body p-4 p-md-5">
                            <!-- Student Info -->
                            <div class="d-flex align-items-center gap-4 mb-5 p-4 bg-light rounded-4">
                                <div class="avatar-sm bg-primary text-white rounded-circle fs-3 fw-bold d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    {{ substr($user->nama, 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="fw-black mb-0 text-dark">{{ $user->nama }}</h4>
                                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold">PERIODE: {{ Carbon\Carbon::now()->format('F Y') }}</span>
                                </div>
                            </div>

                            <!-- Rating Indicators -->
                            <h5 class="fw-black text-dark mb-4 border-start border-4 border-primary ps-3">Indikator Sikap</h5>
                            
                            @foreach($categories as $category)
                            <div class="category-item p-4 mb-4">
                                <div class="row align-items-center">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <h6 class="fw-black text-dark mb-1">{{ $category->name }}</h6>
                                        <p class="text-muted small mb-0">{{ $category->description }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="star-rating">
                                            @for($i = 5; $i >= 1; $i--)
                                            <input type="radio" id="star-{{ $category->id }}-{{ $i }}" name="scores[{{ $category->id }}]" value="{{ $i }}" required>
                                            <label for="star-{{ $category->id }}-{{ $i }}"><i class="bi bi-star-fill"></i></label>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach

                            <!-- Notes -->
                            <div class="mt-5">
                                <h5 class="fw-black text-dark mb-3">Catatan & Feedback</h5>
                                <textarea name="general_notes" class="form-control rounded-4 p-4 bg-light border-0" rows="4" placeholder="Berikan komentar membangun untuk siswa ini..."></textarea>
                            </div>

                            <div class="d-grid mt-5">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-black py-3 shadow-lg">
                                    SIMPAN PENILAIAN <i class="bi bi-check-circle-fill ms-2"></i>
                                </button>
                                <a href="{{ route('assessments.index') }}" class="btn btn-link text-muted mt-3 fw-bold text-decoration-none">BATAL</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
