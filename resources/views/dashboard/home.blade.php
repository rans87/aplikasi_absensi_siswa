@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

  {{-- Header --}}
  <div class="app-content-header mb-4">
    <div class="container-fluid px-4 py-3 rounded-3 shadow-sm"
      style="background: linear-gradient(90deg, #2c1cbaea, #6a3cc0);">
      <h4 class="fw-bold text-white mb-1">Dashboard Overview</h4>
      <small class="text-white-50">Ringkasan data sistem hari ini</small>
    </div>
  </div>

  {{-- Cards --}}
  <div class="app-content">
    <div class="container-fluid px-4">
      <div class="row g-3">

        @php
          $cards = [
            ['Total Siswa Hadir', '150', 'bi-people-fill', '#0d6efd', '#e7f1ff'],   // biru
            ['Total Guru Hadir', '53', 'bi-person-badge-fill', '#20c997', '#e6fcf5'], // mint
            ['Izin / Sakit', '12', 'bi-clipboard-check-fill', '#fd7e14', '#fff4e6'], // orange soft
            ['Tidak Hadir', '8', 'bi-x-circle-fill', '#e64980', '#fff0f6'], // pink coral
          ];
        @endphp

        @foreach ($cards as $card)
          <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="background: {{ $card[4] }};">
              <div class="card-body py-3 px-3">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <small class="text-muted">{{ $card[0] }}</small>
                    <h4 class="fw-bold mb-0" style="color: {{ $card[3] }};">{{ $card[1] }}</h4>
                  </div>
                  <div class="d-flex align-items-center justify-content-center rounded-circle"
                    style="width:45px;height:45px;background: white;">
                    <i class="bi {{ $card[2] }}" style="font-size:20px;color: {{ $card[3] }};"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @endforeach

      </div>
    </div>
  </div>

  {{-- Charts --}}
  <div class="container-fluid px-4 mt-4">
    <div class="row g-3">

      {{-- Line Chart --}}
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h6 class="fw-bold mb-3" style="color:#0d6efd;">Grafik Kehadiran Mingguan</h6>
            <div style="height:260px;">
              <canvas id="lineChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      {{-- Pie Chart --}}
      <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h6 class="fw-bold mb-3" style="color:#6f42c1;">Persentase Kehadiran Hari Ini</h6>
            <div style="height:220px;">
              <canvas id="pieChart"></canvas>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

@endsection