@extends('layouts.app')

@section('title', 'Rule Engine - Aturan Poin Integritas')

@section('content')
<div class="container-fluid px-4 fade-in">
    {{-- Header Content --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h1 class="display-6 fw-extrabold text-dark ls-tight mb-2"><i class="bi bi-cpu-fill text-primary me-2"></i>Rule Engine</h1>
            <p class="text-muted fw-medium mb-0">Otomatisasi poin berdasarkan kedatangan siswa. Waktu masuk diambil otomatis dari <strong>Kalender Sekolah</strong>.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <button class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addRuleModal">
                <i class="bi bi-plus-lg me-2"></i>Buat Aturan Baru
            </button>
        </div>
    </div>

    {{-- Info Banner --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-5 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start gap-3">
                        <div class="bg-primary-soft p-3 rounded-4">
                            <i class="bi bi-info-circle-fill fs-3 text-primary"></i>
                        </div>
                        <div>
                            <h6 class="fw-extrabold text-dark mb-2">Bagaimana Rule Engine Bekerja?</h6>
                            <p class="text-muted small mb-2">
                                Setiap kali siswa scan absensi, sistem menghitung <strong>selisih menit</strong> antara waktu scan dan jam masuk di <strong>Kalender Sekolah</strong>.
                                Aturan yang aktif akan dievaluasi berdasarkan menit terlambat tersebut.
                            </p>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-emerald-soft text-emerald rounded-pill px-3 py-2 fw-bold border border-success border-opacity-10">
                                    <i class="bi bi-check-circle-fill me-1"></i>Menit = 0 → Tepat Waktu (+Poin)
                                </span>
                                <span class="badge bg-amber-soft text-warning rounded-pill px-3 py-2 fw-bold border border-warning border-opacity-10">
                                    <i class="bi bi-exclamation-circle-fill me-1"></i>Menit > 0 → Terlambat (-Poin)
                                </span>
                                <span class="badge bg-primary-soft text-primary rounded-pill px-3 py-2 fw-bold border border-primary border-opacity-10">
                                    <i class="bi bi-calendar-check me-1"></i>Jam masuk dari Kalender
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Rules Grid --}}
    <div class="row g-4 mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-5 overflow-hidden">
                <div class="card-header bg-white p-4 border-0 d-flex align-items-center justify-content-between">
                    <h5 class="fw-extrabold text-dark mb-0"><i class="bi bi-list-stars me-2 text-primary"></i>Daftar Aturan Aktif</h5>
                    <span class="badge bg-primary-soft text-primary rounded-pill px-3 py-2 fw-bold">{{ $rules->count() }} Aturan Terpasang</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-muted small fw-bold text-uppercase ls-1">Aturan</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase ls-1">Logika & Kondisi</th>
                                    <th class="py-3 text-center text-muted small fw-bold text-uppercase ls-1">Nilai Poin</th>
                                    <th class="py-3 text-center text-muted small fw-bold text-uppercase ls-1">Status</th>
                                    <th class="pe-4 py-3 text-end text-muted small fw-bold text-uppercase ls-1">Kelola</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rules as $rule)
                                <tr class="{{ !$rule->is_active ? 'bg-light bg-opacity-50' : '' }}">
                                    <td class="ps-4 py-4">
                                        <div class="fw-extrabold text-dark fs-6">{{ $rule->rule_name }}</div>
                                        <div class="small fw-bold text-muted text-uppercase mt-1 ls-1" style="font-size: 10px;">Untuk: {{ $rule->target_role }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center flex-wrap gap-2">
                                            <span class="small fw-bold text-muted">JIKA</span>
                                            <span class="badge bg-primary-soft text-primary rounded-pill px-3 py-2 border border-primary border-opacity-10">
                                                ⏳ Menit Terlambat
                                            </span>
                                            <span class="fw-bold text-dark fs-5">{{ $rule->condition_operator }}</span>
                                            <span class="badge bg-dark rounded-pill px-3 py-2 fw-extrabold">
                                                {{ $rule->condition_value }} Menit
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($rule->point_modifier > 0)
                                            <div class="badge bg-emerald text-white rounded-pill px-4 py-2 fs-6 fw-extrabold shadow-sm">+{{ $rule->point_modifier }}</div>
                                        @else
                                            <div class="badge bg-rose text-white rounded-pill px-4 py-2 fs-6 fw-extrabold shadow-sm">{{ $rule->point_modifier }}</div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <form method="POST" action="{{ route('integrity.rules.toggle', $rule->id) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-sm border-0 p-0 hover-up">
                                                @if($rule->is_active)
                                                    <span class="badge bg-emerald-soft text-success border border-success border-opacity-10 rounded-pill px-3 py-2 fw-bold">AKTIF</span>
                                                @else
                                                    <span class="badge bg-light text-muted border border-secondary border-opacity-10 rounded-pill px-3 py-2 fw-bold">NONAKTIF</span>
                                                @endif
                                            </button>
                                        </form>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button class="btn btn-white btn-sm rounded-4 shadow-sm px-3 fw-bold text-primary border" 
                                                onclick="openEditModal({{ json_encode($rule) }})">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </button>
                                            <form method="POST" action="{{ route('integrity.rules.destroy', $rule->id) }}" onsubmit="return confirm('Hapus aturan ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-white btn-sm rounded-4 shadow-sm px-3 text-danger border">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="py-4">
                                            <div class="bg-light rounded-circle d-inline-flex p-4 mb-3">
                                                <i class="bi bi-cpu display-5 text-muted opacity-50"></i>
                                            </div>
                                            <h5 class="fw-bold text-dark">Belum ada aturan terpasang</h5>
                                            <p class="text-muted">Mulai otomatisasi poin dengan membuat aturan pertama Anda.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SHARED FORM MODAL (Add & Edit combined logic) --}}
<div class="modal fade" id="ruleModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-5 shadow-lg overflow-hidden">
            <div class="modal-header border-0 p-4 bg-primary text-white">
                <i class="bi bi-gear-fill fs-3 me-3"></i>
                <h5 class="fw-extrabold mb-0" id="ruleModalTitle">Detail Aturan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="ruleForm" method="POST">
                @csrf
                <div id="methodPlaceholder"></div>
                <div class="modal-body p-4 p-md-5">
                    {{-- 🔮 Live Preview Banner --}}
                    <div class="p-4 rounded-5 mb-4 shadow-sm border" style="background: linear-gradient(135deg, #f8fafc, #eff6ff);">
                        <div class="small fw-extrabold text-primary text-uppercase ls-2 mb-3"><i class="bi bi-eye-fill me-2"></i>PREVIEW DINAMIS</div>
                        <div class="d-flex flex-wrap align-items-center gap-2" id="livePreviewContainer" style="font-size: 14px;">
                            <span class="badge bg-dark rounded-pill px-3 py-2 fw-bold">JIKA</span>
                            <span class="badge bg-blue-soft text-primary rounded-pill px-3 py-2 border border-primary border-opacity-10 fw-bold">⏳ Menit Terlambat</span>
                            <span class="fw-extrabold fs-5 text-dark" id="pvOp">></span>
                            <span class="badge bg-dark rounded-pill px-3 py-2 fw-extrabold shadow-sm" id="pvVal">...</span>
                            <span class="badge bg-dark rounded-pill px-3 py-2 fw-bold">MAKA</span>
                            <span class="badge bg-emerald rounded-pill px-4 py-2 text-white shadow-sm fw-extrabold" id="pvPoin">+0 POIN</span>
                        </div>
                    </div>

                    {{-- Info Kalender --}}
                    <div class="alert alert-info border-0 rounded-4 d-flex align-items-center gap-3 mb-4" style="background: #e0f2fe;">
                        <i class="bi bi-calendar-check-fill text-primary fs-4"></i>
                        <div class="small fw-medium text-dark">
                            <strong>Jam masuk diambil otomatis dari Kalender Sekolah.</strong> 
                            Anda hanya perlu mengatur kondisi berdasarkan <strong>menit terlambat</strong> saja.
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted">NAMA ATURAN</label>
                            <input type="text" name="rule_name" id="f-rule_name" class="form-control form-control-lg bg-light border-0 shadow-none px-4 rounded-4" placeholder="Contoh: Datang Tepat Waktu" required>
                        </div>
                        
                        <div class="col-md-6 d-none"> {{-- Defaulted --}}
                            <input type="hidden" name="target_role" value="siswa">
                        </div>

                        {{-- Condition type is now always late_minutes --}}
                        <input type="hidden" name="condition_type" value="late_minutes">

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">OPERATOR KONDISI</label>
                            <select name="condition_operator" id="f-condition_operator" class="form-select form-select-lg bg-light border-0 shadow-none px-4 rounded-4" required>
                                <option value="=">Sama Dengan (=) → Tepat waktu jika 0</option>
                                <option value="<">Kurang Dari (&lt;)</option>
                                <option value="<=">Kurang/Sama Dengan (&lt;=)</option>
                                <option value=">">Lebih Dari (&gt;)</option>
                                <option value=">=">Lebih/Sama Dengan (&gt;=)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">JUMLAH MENIT TERLAMBAT</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 px-3"><i class="bi bi-clock-fill text-primary"></i></span>
                                <input type="number" name="condition_value" id="f-condition_value" class="form-control form-control-lg bg-light border-0 shadow-none px-3 rounded-right-4" placeholder="Contoh: 0, 15, 30" required min="0">
                            </div>
                            <small class="text-muted mt-2 d-block">0 = tepat waktu. Angka lebih besar = semakin terlambat.</small>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted">MODIFIER POIN (POIN +/-)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 px-3"><i class="bi bi-star-fill text-warning"></i></span>
                                <input type="number" name="point_modifier" id="f-point_modifier" class="form-control form-control-lg bg-light border-0 shadow-none px-3 rounded-right-4" placeholder="Contoh: 5 atau -10" required>
                            </div>
                            <small class="text-muted mt-2 d-block">Gunakan tanda <strong>plus (+)</strong> untuk reward dan <strong>minus (-)</strong> untuk penalti.</small>
                        </div>
                    </div>

                    {{-- Contoh Aturan --}}
                    <div class="mt-4 p-4 rounded-4 bg-light border">
                        <div class="small fw-extrabold text-muted text-uppercase ls-2 mb-3"><i class="bi bi-lightbulb-fill me-2 text-warning"></i>CONTOH ATURAN</div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-2 p-2 rounded-3 bg-white shadow-sm">
                                    <span class="badge bg-emerald rounded-pill px-2 py-1 text-white fw-bold">+5</span>
                                    <span class="small fw-medium text-dark">Tepat Waktu → menit <strong>= 0</strong></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-2 p-2 rounded-3 bg-white shadow-sm">
                                    <span class="badge bg-rose rounded-pill px-2 py-1 text-white fw-bold">-3</span>
                                    <span class="small fw-medium text-dark">Telat Ringan → menit <strong>&lt;= 15</strong></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-2 p-2 rounded-3 bg-white shadow-sm">
                                    <span class="badge bg-rose rounded-pill px-2 py-1 text-white fw-bold">-5</span>
                                    <span class="small fw-medium text-dark">Telat Sedang → menit <strong>&lt;= 30</strong></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-2 p-2 rounded-3 bg-white shadow-sm">
                                    <span class="badge bg-rose rounded-pill px-2 py-1 text-white fw-bold">-10</span>
                                    <span class="small fw-medium text-dark">Telat Berat → menit <strong>&gt; 30</strong></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 bg-light d-flex justify-content-between">
                    <button type="button" class="btn btn-white rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-lg fw-bold">SIMPAN ATURAN</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Create Alias Modals for simple data-bs targets --}}
<div id="addRuleModal" class="d-none"></div>

<style>
    .bg-emerald { background-color: #10b981 !important; }
    .bg-rose { background-color: #e11d48 !important; }
    .bg-emerald-soft { background-color: #ecfdf5 !important; }
    .text-emerald { color: #10b981 !important; }
    .bg-primary-soft { background-color: #eff6ff !important; }
    .bg-blue-soft { background-color: #f1f5f9 !important; }
    .bg-amber-soft { background-color: rgba(245, 158, 11, 0.1) !important; }
    .ls-tight { letter-spacing: -1.2px; }
    .ls-2 { letter-spacing: 2px; }
    .btn-white { background: white; border: 1px solid #f1f5f9; }
    .rounded-right-4 { border-top-right-radius: 1rem !important; border-bottom-right-radius: 1rem !important; }
    .hover-up { transition: transform 0.2s; }
    .hover-up:hover { transform: translateY(-2px); }
</style>

@push('scripts')
<script>
    const modal = new bootstrap.Modal(document.getElementById('ruleModal'));
    const form = document.getElementById('ruleForm');
    const methodPlaceholder = document.getElementById('methodPlaceholder');
    const title = document.getElementById('ruleModalTitle');
    
    const inputs = {
        name: document.getElementById('f-rule_name'),
        op: document.getElementById('f-condition_operator'),
        val: document.getElementById('f-condition_value'),
        poin: document.getElementById('f-point_modifier')
    };
    
    const pv = {
        op: document.getElementById('pvOp'),
        val: document.getElementById('pvVal'),
        poin: document.getElementById('pvPoin')
    };

    // Open for Create
    document.querySelector('[data-bs-target="#addRuleModal"]').onclick = function() {
        title.innerHTML = '<i class="bi bi-plus-circle-fill me-2 text-primary"></i>Buat Aturan Baru';
        form.action = "{{ route('integrity.rules.store') }}";
        methodPlaceholder.innerHTML = '';
        form.reset();
        updateDisplay();
        modal.show();
    };

    // Open for Edit
    function openEditModal(rule) {
        title.innerHTML = '<i class="bi bi-pencil-square me-2 text-primary"></i>Edit Aturan';
        form.action = `/integrity/rules/${rule.id}`;
        methodPlaceholder.innerHTML = '<input type="hidden" name="_method" value="PUT">';
        
        inputs.name.value = rule.rule_name;
        inputs.op.value = rule.condition_operator;
        inputs.val.value = rule.condition_value;
        inputs.poin.value = rule.point_modifier;
        
        updateDisplay();
        modal.show();
    }

    function updateDisplay() {
        pv.op.innerText = inputs.op.value;
        
        let valText = inputs.val.value || '...';
        valText += ' Menit';
        pv.val.innerText = valText;

        const p = parseInt(inputs.poin.value) || 0;
        pv.poin.innerText = (p >= 0 ? '+' : '') + p + ' POIN';
        pv.poin.className = `badge ${p >= 0 ? 'bg-emerald' : 'bg-rose'} rounded-pill px-4 py-2 text-white shadow-sm fw-extrabold`;
    }

    // Event Listeners for Live Preview
    Object.values(inputs).forEach(input => {
        input.addEventListener('input', updateDisplay);
        input.addEventListener('change', updateDisplay);
    });
</script>
@endpush
@endsection
