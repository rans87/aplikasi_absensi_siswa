@extends('layouts.app')

@section('title', 'Kalender Akademik & Jam Masuk')

@section('content')
<div class="container-fluid px-4 fade-in">
    <!-- Header Section -->
    <div class="row mb-4 align-items-center">
        <div class="col-xl-6">
            <h1 class="m-0 text-dark fw-extrabold display-6 ls-1"><i class="bi bi-calendar3 text-primary me-3"></i>Kalender Sekolah</h1>
            <p class="text-muted mt-2 fw-medium">Kelola jadwal masuk & hari libur untuk sistem poin integritas.</p>
        </div>
        <div class="col-xl-6 text-xl-end mt-3 mt-xl-0">
            <div class="d-flex flex-wrap justify-content-xl-end gap-2">
                <form action="{{ route('school-calendar.index') }}" method="GET" class="d-inline-flex gap-2">
                    <select name="month" class="form-select rounded-pill border-0 shadow-sm px-4 fw-bold">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                        @endforeach
                    </select>
                    <select name="year" class="form-select rounded-pill border-0 shadow-sm px-4 fw-bold">
                        @foreach(range(now()->year - 1, now()->year + 1) as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary rounded-circle shadow-sm p-3 leading-none d-flex align-items-center justify-content-center" style="width:45px; height:45px"><i class="bi bi-search"></i></button>
                </form>
                <button type="button" class="btn btn-success rounded-pill shadow-sm px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#bulkModal">
                    <i class="bi bi-lightning-fill me-2"></i> Buat Kalender Bulanan
                </button>
            </div>
        </div>
    </div>

    <!-- Status Legend -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex gap-4 p-3 bg-white rounded-4 shadow-sm align-items-center overflow-auto no-scrollbar">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-circle p-1 bg-primary" style="width:12px; height:12px"></span>
                    <span class="small fw-bold text-dark">Hari Sekolah (Masuk)</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-circle p-1 bg-danger" style="width:12px; height:12px"></span>
                    <span class="small fw-bold text-dark">Hari Libur Sekolah</span>
                </div>
                <div class="ms-auto small text-muted italic">
                    <i class="bi bi-info-circle me-1"></i> Klik pada kotak tanggal untuk mengubah pengaturan.
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Grid -->
    <div class="card border-0 shadow-sm rounded-5 overflow-hidden mb-5">
        <div class="bg-white py-4 px-4 border-bottom text-center">
             <h4 class="fw-extrabold mb-0 text-dark text-uppercase ls-2">{{ Carbon\Carbon::create($year, $month)->translatedFormat('F Y') }}</h4>
        </div>
        
        <div class="calendar-grid bg-light p-3 p-md-4">
            <!-- Day Names -->
            <div class="calendar-row mb-2">
                @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $day)
                    <div class="calendar-day-name fw-extrabold text-muted text-uppercase small ls-1 text-center py-2">{{ $day }}</div>
                @endforeach
            </div>

            <div class="calendar-row row-cols-7 g-2 g-md-3">
                @php
                    $firstDay = $startDate->dayOfWeek;
                    $daysInMonth = $startDate->daysInMonth;
                    $todayStr = now()->toDateString();
                @endphp

                <!-- Empty cells for start of month -->
                @for($i = 0; $i < $firstDay; $i++)
                    <div class="calendar-cell-empty"></div>
                @endfor

                <!-- Date cells -->
                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $currentDate = Carbon\Carbon::create($year, $month, $day);
                        $dateStr = $currentDate->format('Y-m-d');
                        $cal = $calendars[$dateStr] ?? null;
                        $isLibur = $cal ? $cal->is_libur : ($currentDate->isWeekend());
                        $jamMasuk = $cal ? $cal->jam_masuk : '07:00:00';
                        $isToday = ($dateStr === $todayStr);
                    @endphp
                    
                    <div class="calendar-cell cursor-pointer" onclick="editCalendar('{{ $dateStr }}', '{{ $jamMasuk }}', {{ $isLibur ? 1 : 0 }}, '{{ $cal->keterangan ?? '' }}')">
                        <div class="card h-100 border-0 rounded-4 shadow-sm calendar-date-card {{ $isLibur ? 'is-holiday' : 'is-school' }} {{ $isToday ? 'is-today' : '' }}">
                            <div class="card-body p-2 p-md-3 d-flex flex-column h-100">
                                <div class="d-flex justify-content-between align-items-start mb-auto">
                                    <span class="date-number fw-extrabold fs-5">{{ $day }}</span>
                                    @if($isToday)
                                        <span class="badge bg-white text-primary border rounded-pill px-2 py-1 x-small fw-bold mt-1">HARI INI</span>
                                    @endif
                                </div>
                                <div class="mt-2 text-center">
                                    @if(!$isLibur)
                                        <div class="entry-time fw-extrabold small text-white opacity-90"><i class="bi bi-clock me-1"></i> {{ Carbon\Carbon::parse($jamMasuk)->format('H:i') }}</div>
                                    @else
                                        <div class="entry-time fw-extrabold small text-white opacity-75"><i class="bi bi-x-circle-fill"></i> LIBUR</div>
                                    @endif
                                </div>
                                @if($cal && $cal->keterangan)
                                    <div class="mt-1 small text-white opacity-75 italic text-truncate text-center" style="font-size: 10px;">{{ $cal->keterangan }}</div>
                                @endif
                                
                                <div class="hover-overlay">
                                    <i class="bi bi-pencil-fill"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="calendarModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-5 shadow-lg overflow-hidden">
            <div class="modal-header border-0 p-4 bg-primary text-white">
                <i class="bi bi-calendar-check-fill fs-3 me-3"></i>
                <h5 class="fw-extrabold mb-0" id="modalTitle">Atur Tanggal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('school-calendar.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 p-md-5">
                    <input type="hidden" name="tanggal" id="modalDateInput">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Jam Masuk Sekolah</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-clock"></i></span>
                            <input type="time" name="jam_masuk" id="modalJamInput" class="form-control form-control-lg bg-light border-0 shadow-none" required>
                        </div>
                        <small class="text-muted mt-2 d-block">Siswa masuk melebihi jam ini akan terkena poin terlambat.</small>
                    </div>
                    <div class="mb-4 p-3 rounded-4 bg-light">
                        <div class="form-check form-switch custom-switch d-flex align-items-center gap-3">
                            <input class="form-check-input" type="checkbox" name="is_libur" value="1" id="modalLiburInput">
                            <label class="form-check-label fw-extrabold text-dark" for="modalLiburInput">LIBURKAN TANGGAL INI</label>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold small">Catatan / Alasan Libur</label>
                        <textarea name="keterangan" id="modalKetInput" class="form-control bg-light border-0" placeholder="Misal: Libur Nasional" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 bg-light d-flex justify-content-between">
                    <button type="button" class="btn btn-white rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-lg fw-bold">SIMPAN PENGATURAN</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Bulk -->
<div class="modal fade" id="bulkModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-5 shadow-lg overflow-hidden">
            <div class="modal-header border-0 p-4 bg-success text-white">
                <i class="bi bi-lightning-charge-fill fs-3 me-3"></i>
                <h5 class="fw-extrabold mb-0">Generate Bulanan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('school-calendar.bulk') }}" method="POST">
                @csrf
                <div class="modal-body p-4 p-md-5">
                    <div class="row mb-4">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Bulan</label>
                            <select name="month" class="form-select bg-light border-0 shadow-none">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Tahun</label>
                            <select name="year" class="form-select bg-light border-0 shadow-none">
                                @foreach(range(now()->year - 1, now()->year + 1) as $y)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Jam Masuk Default</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-clock"></i></span>
                            <input type="time" name="jam_masuk" class="form-control form-control-lg bg-light border-0 shadow-none" value="07:00" required>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold d-block mb-3">Pilih Hari Libur Rutin</label>
                        <div class="d-flex flex-wrap gap-2">
                            @php
                                $days = [
                                    0 => 'Minggu',
                                    1 => 'Senin',
                                    2 => 'Selasa',
                                    3 => 'Rabu',
                                    4 => 'Kamis',
                                    5 => 'Jumat',
                                    6 => 'Sabtu'
                                ];
                            @endphp
                            @foreach($days as $index => $name)
                                <div class="form-check form-check-inline mx-0">
                                    <input class="form-check-input d-none" type="checkbox" name="libur_days[]" value="{{ $index }}" id="day_{{ $index }}" {{ in_array($index, [0, 6]) ? 'checked' : '' }}>
                                    <label class="btn btn-outline-danger btn-sm rounded-pill px-3 py-2 fw-bold day-label" for="day_{{ $index }}">
                                        {{ $name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 bg-light d-flex justify-content-between">
                    <button type="button" class="btn btn-white rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-5 shadow-lg fw-bold">GENERATE SEKARANG</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 15px;
    }
    .calendar-row {
        display: contents;
    }
    .calendar-cell {
        aspect-ratio: 1/1;
        min-height: 100px;
    }
    .calendar-cell-empty {
        aspect-ratio: 1/1;
    }
    .calendar-date-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .calendar-date-card.is-school {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: white;
    }
    .calendar-date-card.is-holiday {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }
    .calendar-date-card.is-today {
        ring: 3px solid #000;
        transform: scale(1.02);
        box-shadow: 0 10px 25px rgba(0,0,0,0.2) !important;
        z-index: 10;
        border: 2px solid white !important;
    }
    .calendar-date-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.15) !important;
    }
    .calendar-date-card .date-number { opacity: 0.9; }
    .hover-overlay {
        position: absolute;
        inset: 0;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s;
        font-size: 24px;
    }
    .calendar-date-card:hover .hover-overlay { opacity: 1; }
    
    .custom-switch .form-check-input { width: 3.5em; height: 1.75em; cursor: pointer; }
    .custom-switch .form-check-input:checked { background-color: #ef4444; border-color: #ef4444; }
    .btn-white { background: white; border: 1px solid #f1f5f9; }
    .day-label { cursor: pointer; transition: all 0.2s; border-width: 2px; font-size: 11px; }
    .form-check-input:checked + .day-label { background-color: #ef4444; color: white; border-color: #ef4444; }
    
    @media (max-width: 768px) {
        .calendar-grid { gap: 8px; }
        .calendar-cell { min-height: 80px; }
        .date-number { font-size: 1.1rem !important; }
        .entry-time { font-size: 8px !important; }
        .x-small { font-size: 7px !important; }
    }
    
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .ls-2 { letter-spacing: 2px; }
</style>

<script>
    function editCalendar(date, jam, isLibur, ket) {
        document.getElementById('modalDateInput').value = date;
        document.getElementById('modalJamInput').value = jam;
        document.getElementById('modalLiburInput').checked = isLibur == 1;
        document.getElementById('modalKetInput').value = ket;
        
        const d = new Date(date);
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('modalTitle').innerText = d.toLocaleDateString('id-ID', options);
        
        new bootstrap.Modal(document.getElementById('calendarModal')).show();
    }
</script>
@endsection
