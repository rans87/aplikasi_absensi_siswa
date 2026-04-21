@extends('layouts.app')

@section('title', 'Rapor Performa')

@section('content')
<div class="report-container py-5 px-3">
    <div class="container">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('assessments.index') }}" class="btn btn-light rounded-pill px-4 fw-bold">
                <i class="bi bi-arrow-left me-2"></i> KEMBALI
            </a>
        </div>

        <div class="row g-4">
            <!-- Profile Sidebar -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center mb-4 bg-white">
                    <div class="avatar-xl bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 fw-black fs-1" style="width: 120px; height: 120px;">
                        {{ substr($user->nama, 0, 1) }}
                    </div>
                    <h3 class="fw-black text-dark mb-1">{{ $user->nama }}</h3>
                    <p class="text-muted fw-bold mb-3">NIS: {{ $user->nis }}</p>
                    <div class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-bold">ROLE: SISWA</div>
                </div>

                <!-- History Timeline -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h5 class="fw-black text-dark mb-4 border-start border-4 border-primary ps-3">Riwayat Penilaian</h5>
                    <div class="timeline-simple">
                        @forelse($history as $h)
                        <div class="timeline-item pb-4 position-relative ps-4">
                            <div class="timeline-dot bg-primary position-absolute start-0"></div>
                            <h6 class="fw-bold text-dark mb-1">{{ $h->period }}</h6>
                            <p class="text-muted small mb-0">{{ $h->assessment_date->format('d M Y') }}</p>
                            <p class="small text-dark mt-2 italic">"{{ Str::limit($h->general_notes, 50) }}"</p>
                        </div>
                        @empty
                        <p class="text-muted">Belum ada riwayat penilaian.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Chart & Detail -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-5 bg-white mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-5">
                        <h4 class="fw-black text-dark mb-0">Analisis Grafik Radar</h4>
                        <span class="text-muted fw-bold small">Latest Update: {{ $latestAssessment ? $latestAssessment->assessment_date->format('d M Y') : '-' }}</span>
                    </div>

                    @if($latestAssessment)
                    <div class="row align-items-center">
                        <div class="col-md-7 mb-4 mb-md-0">
                            <canvas id="radarChart" height="300"></canvas>
                        </div>
                        <div class="col-md-5">
                            <div class="bg-light p-4 rounded-4">
                                <h6 class="fw-black text-dark mb-3">Feedback Umum</h6>
                                <p class="text-dark small lh-base">
                                    {{ $latestAssessment->general_notes ?? 'Tidak ada catatan tambahan.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-bar-chart-fill display-1 text-light"></i>
                        <p class="text-muted mt-3">Siswa ini belum memiliki data penilaian.</p>
                    </div>
                    @endif
                </div>

                <!-- Raw Data Table -->
                @if($latestAssessment)
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="p-4 border-bottom">
                        <h5 class="fw-black text-dark mb-0">Rincian Skor Per Kategori</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 border-0">KATEGORI</th>
                                    <th class="px-4 py-3 border-0 text-center">SKOR (1-5)</th>
                                    <th class="px-4 py-3 border-0">PROGRESS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($latestAssessment->details as $detail)
                                <tr>
                                    <td class="px-4 py-3 fw-bold text-dark">{{ $detail->category->name }}</td>
                                    <td class="px-4 py-3 text-center"><span class="badge bg-primary rounded-pill px-3">{{ $detail->score }}</span></td>
                                    <td class="px-4 py-3">
                                        <div class="progress rounded-pill" style="height: 6px; width: 100px;">
                                            <div class="progress-bar bg-primary" style="width: {{ ($detail->score / 5) * 100 }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@if($latestAssessment)
<script>
    const ctx = document.getElementById('radarChart').getContext('2d');
    const data = {
        labels: [
            @foreach($latestAssessment->details as $detail)
            '{{ $detail->category->name }}',
            @endforeach
        ],
        datasets: [{
            label: 'Skor Perilaku',
            data: [
                @foreach($latestAssessment->details as $detail)
                {{ $detail->score }},
                @endforeach
            ],
            fill: true,
            backgroundColor: 'rgba(59, 130, 246, 0.2)',
            borderColor: 'rgb(59, 130, 246)',
            pointBackgroundColor: 'rgb(59, 130, 246)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgb(59, 130, 246)'
        }]
    };

    new Chart(ctx, {
        type: 'radar',
        data: data,
        options: {
            elements: {
                line: { borderWidth: 3 }
            },
            scales: {
                r: {
                    angleLines: { display: true },
                    suggestedMin: 0,
                    suggestedMax: 5,
                    ticks: { stepSize: 1 }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
@endif
@endpush

<style>
    .report-container { background: #f8fafc; min-height: 100vh; }
    .fw-black { font-weight: 800; }
    .timeline-dot { width: 12px; height: 12px; border-radius: 50%; top: 5px; left: -6px; border: 2px solid white; box-shadow: 0 0 0 3px #0d6efd; }
    .timeline-item::before { content: ""; position: absolute; left: 0; top: 15px; bottom: 0; width: 2px; background: #e2e8f0; }
    .timeline-item:last-child::before { display: none; }
</style>
@endsection
