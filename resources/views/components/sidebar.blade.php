@php
    // Cache wali kelas check to avoid DB query on every page load
    $currentGuruId = null;
    $isWali = false;
    $guruUser = null;
    
    if (Auth::guard('guru')->check()) {
        $currentGuruId = Auth::guard('guru')->id();
        $guruUser = Auth::guard('guru')->user();
        $isWali = Cache::remember('is_wali_' . $currentGuruId, 600, function () use ($currentGuruId) {
            return \App\Models\RombonganBelajar::where('wali_kelas_id', $currentGuruId)->exists();
        });
    }
@endphp

<aside class="app-sidebar shadow-lg border-0">
    <div class="sidebar-brand d-flex align-items-center gap-3 px-4 py-5">
        <div class="brand-identity position-relative">
            <div class="bg-white rounded-4 d-flex align-items-center justify-content-center shadow-glow-white overflow-hidden" style="width:50px; height:50px;">
                <i class="bi bi-qr-code text-primary fs-3"></i>
            </div>
            <div class="active-dot"></div>
        </div>
        <div class="ms-1">
            <span class="brand-text fw-extrabold text-white fs-3 d-block lh-1 ls-tight">Presence<span class="text-secondary-blue">X</span></span>
            <small class="text-white-50 fw-bold mt-2 d-block ls-2" style="font-size: 8px;">DIGITAL ECOSYSTEM</small>
        </div>
    </div>

    <div class="sidebar-wrapper px-3 scrollbar-custom">
        <nav class="mt-4">
            <ul class="nav flex-column gap-1" role="menu">

                {{-- Dashboard --}}
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                       class="nav-link sidebar-link {{ request()->routeIs('*dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span>Dashboard Utama</span>
                    </a>
                </li>

                {{-- ADMIN ONLY --}}
                @if(Auth::guard('web')->check())

                {{-- CORE ENGINE Dropdown --}}
                <li class="nav-item sidebar-dropdown {{ request()->routeIs('school-calendar.*', 'guru.*', 'siswa.*', 'rombongan-belajar.*', 'anggota-kelas.*', 'tahun_ajar.*') ? 'open' : '' }}">
                    <a href="javascript:void(0)" class="nav-link sidebar-link sidebar-dropdown-toggle">
                        <i class="bi bi-cpu-fill"></i>
                        <span>Core Engine</span>
                        <i class="bi bi-chevron-down sidebar-arrow"></i>
                    </a>
                    <ul class="sidebar-submenu">
                        <li>
                            <a href="{{ route('school-calendar.index') }}" class="sidebar-submenu-link {{ request()->routeIs('school-calendar.*') ? 'active' : '' }}">
                                <i class="bi bi-calendar-check-fill text-sky"></i> <span>Kalender Sekolah</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('guru.index') }}" class="sidebar-submenu-link {{ request()->routeIs('guru.*') ? 'active' : '' }}">
                                <i class="bi bi-person-badge-fill text-sky"></i> <span>Data Guru</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('siswa.index') }}" class="sidebar-submenu-link {{ request()->routeIs('siswa.*') ? 'active' : '' }}">
                                <i class="bi bi-mortarboard-fill text-sky"></i> <span>Data Siswa</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('rombongan-belajar.index') }}" class="sidebar-submenu-link {{ request()->routeIs('rombongan-belajar.*') ? 'active' : '' }}">
                                <i class="bi bi-layers-fill text-sky"></i> <span>Data Kelas</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('anggota-kelas.index') }}" class="sidebar-submenu-link {{ request()->routeIs('anggota-kelas.*') ? 'active' : '' }}">
                                <i class="bi bi-person-check-fill text-sky"></i> <span>Penempatan Siswa</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('tahun_ajar.index') }}" class="sidebar-submenu-link {{ request()->routeIs('tahun_ajar.*') ? 'active' : '' }}">
                                <i class="bi bi-calendar3-range-fill text-sky"></i> <span>Tahun Ajaran</span>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- JADWAL & MAPEL Dropdown --}}
                <li class="nav-item sidebar-dropdown {{ request()->routeIs('mata-pelajaran.*', 'jadwal-pelajaran.*') ? 'open' : '' }}">
                    <a href="javascript:void(0)" class="nav-link sidebar-link sidebar-dropdown-toggle">
                        <i class="bi bi-book-half"></i>
                        <span>Jadwal & Mapel</span>
                        <i class="bi bi-chevron-down sidebar-arrow"></i>
                    </a>
                    <ul class="sidebar-submenu">
                        <li>
                            <a href="{{ route('mata-pelajaran.index') }}" class="sidebar-submenu-link {{ request()->routeIs('mata-pelajaran.*') ? 'active' : '' }}">
                                <i class="bi bi-book-fill text-cyan"></i> <span>Mata Pelajaran</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('jadwal-pelajaran.index') }}" class="sidebar-submenu-link {{ request()->routeIs('jadwal-pelajaran.*') ? 'active' : '' }}">
                                <i class="bi bi-calendar-week-fill text-cyan"></i> <span>Jadwal Pelajaran</span>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- MONITORING Dropdown --}}
                <li class="nav-item sidebar-dropdown {{ request()->routeIs('pengguna.*', 'assessments.*', 'absensi.index') ? 'open' : '' }}">
                    <a href="javascript:void(0)" class="nav-link sidebar-link sidebar-dropdown-toggle">
                        <i class="bi bi-shield-check"></i>
                        <span>Monitoring</span>
                        <i class="bi bi-chevron-down sidebar-arrow"></i>
                    </a>
                    <ul class="sidebar-submenu">
                        <li>
                            <a href="{{ route('pengguna.index') }}" class="sidebar-submenu-link {{ request()->routeIs('pengguna.*') ? 'active' : '' }}">
                                <i class="bi bi-shield-lock-fill text-violet"></i> <span>Akses Admin</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('assessments.index') }}" class="sidebar-submenu-link {{ request()->routeIs('assessments.index') ? 'active' : '' }}">
                                <i class="bi bi-graph-up-arrow text-violet"></i> <span>Evaluasi Sikap</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('assessments.all-reports') }}" class="sidebar-submenu-link {{ request()->routeIs('assessments.all-reports') ? 'active' : '' }}">
                                <i class="bi bi-file-earmark-bar-graph-fill text-violet"></i> <span>Rekap Laporan</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('absensi.index') }}" class="sidebar-submenu-link {{ request()->routeIs('absensi.index') ? 'active' : '' }}">
                                <i class="bi bi-clock-history text-violet"></i> <span>Riwayat Absensi</span>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- INTEGRITY POINT SYSTEM Dropdown --}}
                <li class="nav-item sidebar-dropdown {{ request()->routeIs('integrity.*') ? 'open' : '' }}">
                    <a href="javascript:void(0)" class="nav-link sidebar-link sidebar-dropdown-toggle">
                        <i class="bi bi-stars"></i>
                        <span>Integrity Point</span>
                        <i class="bi bi-chevron-down sidebar-arrow"></i>
                    </a>
                    <ul class="sidebar-submenu">
                        <li>
                            <a href="{{ route('integrity.rules.index') }}" class="sidebar-submenu-link {{ request()->routeIs('integrity.rules.*') ? 'active' : '' }}">
                                <i class="bi bi-lightning-charge-fill text-amber"></i> <span>Rule Engine</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('integrity.items.index') }}" class="sidebar-submenu-link {{ request()->routeIs('integrity.items.*') ? 'active' : '' }}">
                                <i class="bi bi-shop-window text-amber"></i> <span>Marketplace Items</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('integrity.leaderboard') }}" class="sidebar-submenu-link {{ request()->routeIs('integrity.leaderboard') ? 'active' : '' }}">
                                <i class="bi bi-trophy-fill text-amber"></i> <span>Leaderboard Poin</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('integrity.manual.index') }}" class="sidebar-submenu-link {{ request()->routeIs('integrity.manual.*') ? 'active' : '' }}">
                                <i class="bi bi-patch-plus-fill text-amber"></i> <span>Poin Manual</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                {{-- GURU ONLY --}}
                @if(Auth::guard('guru')->check())

                {{-- Guru Dashboard --}}
                <li class="nav-item">
                    <a href="{{ route('guru.dashboard') }}"
                       class="nav-link sidebar-link {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-person-workspace"></i>
                        <span>Dashboard Saya</span>
                    </a>
                </li>

                @if($isWali)
                <li class="nav-item">
                    <a href="{{ route('wali-kelas.index') }}" class="nav-link sidebar-link {{ request()->routeIs('wali-kelas.*') ? 'active' : '' }}">
                        <i class="bi bi-shield-check text-amber pulse"></i> <span class="fw-bold">Panel Wali Kelas</span>
                    </a>
                </li>
                @endif

                {{-- PRESENSI & KELAS Dropdown --}}
                <li class="nav-item sidebar-dropdown {{ request()->routeIs('absensi.*', 'absensi-mapel.*') ? 'open' : '' }}">
                    <a href="javascript:void(0)" class="nav-link sidebar-link sidebar-dropdown-toggle">
                        <i class="bi bi-qr-code-scan"></i>
                        <span>Presensi & Kelas</span>
                        <i class="bi bi-chevron-down sidebar-arrow"></i>
                    </a>
                    <ul class="sidebar-submenu">
                        <li>
                            <a href="{{ route('absensi.scan') }}" class="sidebar-submenu-link {{ request()->routeIs('absensi.scan') ? 'active' : '' }}">
                                <i class="bi bi-qr-code-scan text-sky"></i> <span>Pindai Absensi</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('absensi-mapel.index') }}" class="sidebar-submenu-link {{ request()->routeIs('absensi-mapel.*') ? 'active' : '' }}">
                                <i class="bi bi-journal-check text-sky"></i> <span>PBM & Absen Mapel</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('absensi.index') }}" class="sidebar-submenu-link {{ request()->routeIs('absensi.index') ? 'active' : '' }}">
                                <i class="bi bi-clock-history text-sky"></i> <span>Riwayat Absensi</span>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- EVALUASI Dropdown --}}
                <li class="nav-item sidebar-dropdown {{ request()->routeIs('assessments.*') ? 'open' : '' }}">
                    <a href="javascript:void(0)" class="nav-link sidebar-link sidebar-dropdown-toggle">
                        <i class="bi bi-clipboard-data"></i>
                        <span>Evaluasi & Laporan</span>
                        <i class="bi bi-chevron-down sidebar-arrow"></i>
                    </a>
                    <ul class="sidebar-submenu">
                        <li>
                            <a href="{{ route('assessments.index') }}" class="sidebar-submenu-link {{ request()->routeIs('assessments.index') ? 'active' : '' }}">
                                <i class="bi bi-graph-up-arrow text-cyan"></i> <span>Evaluasi Karakter</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('assessments.all-reports') }}" class="sidebar-submenu-link {{ request()->routeIs('assessments.all-reports') ? 'active' : '' }}">
                                <i class="bi bi-file-earmark-bar-graph-fill text-cyan"></i> <span>Rekap Laporan</span>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- INTEGRITY Dropdown (Guru) --}}
                <li class="nav-item sidebar-dropdown {{ request()->routeIs('integrity.*') ? 'open' : '' }}">
                    <a href="javascript:void(0)" class="nav-link sidebar-link sidebar-dropdown-toggle">
                        <i class="bi bi-stars"></i>
                        <span>Integrity System</span>
                        <i class="bi bi-chevron-down sidebar-arrow"></i>
                    </a>
                    <ul class="sidebar-submenu">
                        <li>
                            <a href="{{ route('integrity.manual.index') }}" class="sidebar-submenu-link {{ request()->routeIs('integrity.manual.*') ? 'active' : '' }}">
                                <i class="bi bi-patch-plus-fill text-amber"></i> <span>Poin Manual</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('integrity.leaderboard') }}" class="sidebar-submenu-link {{ request()->routeIs('integrity.leaderboard') ? 'active' : '' }}">
                                <i class="bi bi-trophy-fill text-amber"></i> <span>Leaderboard Poin</span>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Profil Guru --}}
                <li class="nav-item mt-2">
                    <a href="{{ route('guru.show', $currentGuruId) }}" class="nav-link sidebar-link {{ request()->routeIs('guru.show') ? 'active' : '' }}">
                        <i class="bi bi-person-lines-fill"></i>
                        <span>Profil Saya</span>
                    </a>
                </li>
                @endif

                {{-- SISWA ONLY --}}
                @if(Auth::guard('siswa')->check())
                <li class="nav-item">
                    <a href="{{ route('siswa.dashboard') }}" class="nav-link sidebar-link {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-person-bounding-box"></i> <span>E-Kartu & QR</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('wallet.index') }}" class="nav-link sidebar-link {{ request()->routeIs('wallet.*') ? 'active' : '' }}">
                        <i class="bi bi-wallet2 text-emerald"></i> <span>Dompet Integritas</span>
                    </a>
                </li>
                @endif

                <div class="sidebar-footer-spacer py-5"></div>
            </ul>
        </nav>
    </div>
</aside>

<style>
    .ls-tight { letter-spacing: -1px; }
    .ls-2 { letter-spacing: 2px; }
    .text-secondary-blue { color: var(--secondary-blue); }
    .shadow-glow-white { box-shadow: 0 0 20px rgba(255, 255, 255, 0.1); }
    
    .brand-identity { position: relative; }
    .active-dot { position: absolute; bottom: 0; right: 0; width: 12px; height: 12px; background: var(--emerald); border-radius: 50%; border: 3px solid var(--deep-blue); }

    .scrollbar-custom::-webkit-scrollbar { width: 5px; }
    .scrollbar-custom::-webkit-scrollbar-track { background: transparent; }
    .scrollbar-custom::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.05); border-radius: 10px; }
    .scrollbar-custom::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.1); }
    
    .text-rose-soft { color: #fb7185; }

    /* ===== DROPDOWN SIDEBAR STYLES ===== */

    /* Color accent utilities */
    .text-sky { color: #38bdf8 !important; }
    .text-cyan { color: #22d3ee !important; }
    .text-violet { color: #a78bfa !important; }
    .text-amber { color: #fbbf24 !important; }
    .text-emerald { color: #34d399 !important; }

    /* Main sidebar link */
    .sidebar-link {
        display: flex !important;
        align-items: center !important;
        gap: 14px !important;
        border-radius: 16px !important;
        margin: 3px 8px !important;
        padding: 12px 18px !important;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1) !important;
        color: rgba(255,255,255,0.55) !important;
        font-weight: 600;
        font-size: 0.9rem;
        position: relative;
        cursor: pointer;
    }

    .sidebar-link i:first-child {
        font-size: 1.2rem;
        width: 22px;
        text-align: center;
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        opacity: 0.75;
        flex-shrink: 0;
    }

    .sidebar-link span {
        flex: 1;
        white-space: nowrap;
    }

    .sidebar-link.active {
        background: linear-gradient(135deg, #2563eb, #3b82f6) !important;
        color: #ffffff !important;
        box-shadow: 0 8px 20px -5px rgba(37, 99, 235, 0.45) !important;
    }

    .sidebar-link.active i:first-child {
        transform: scale(1.1);
        opacity: 1;
    }

    .sidebar-link:hover:not(.active) {
        background-color: rgba(255,255,255,0.07) !important;
        color: #ffffff !important;
        transform: translateX(4px);
    }

    /* Dropdown Arrow */
    .sidebar-arrow {
        font-size: 0.7rem !important;
        margin-left: auto;
        opacity: 0.5;
        transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s;
        width: auto !important;
    }

    .sidebar-dropdown.open > .sidebar-dropdown-toggle .sidebar-arrow {
        transform: rotate(180deg);
        opacity: 0.9;
    }

    .sidebar-dropdown-toggle:hover .sidebar-arrow {
        opacity: 0.8;
    }

    /* Submenu */
    .sidebar-submenu {
        list-style: none;
        padding: 0;
        margin: 0;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), 
                    opacity 0.3s ease,
                    padding 0.3s ease;
        opacity: 0;
    }

    .sidebar-dropdown.open > .sidebar-submenu {
        max-height: 500px;
        opacity: 1;
        padding: 4px 0 8px 0;
    }

    /* Submenu link */
    .sidebar-submenu-link {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        padding: 10px 18px 10px 54px !important;
        margin: 2px 8px !important;
        border-radius: 12px !important;
        color: rgba(255,255,255,0.45) !important;
        font-size: 0.83rem;
        font-weight: 500;
        text-decoration: none;
        position: relative;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .sidebar-submenu-link i {
        font-size: 0.95rem;
        width: 18px;
        text-align: center;
        flex-shrink: 0;
        opacity: 0.7;
        transition: opacity 0.3s, transform 0.3s;
    }

    /* Submenu connector line */
    .sidebar-submenu-link::before {
        content: '';
        position: absolute;
        left: 32px;
        top: 50%;
        transform: translateY(-50%);
        width: 8px;
        height: 2px;
        background: rgba(255,255,255,0.12);
        border-radius: 2px;
        transition: background 0.3s, width 0.3s;
    }

    .sidebar-submenu-link:hover {
        color: #ffffff !important;
        background: rgba(255,255,255,0.06) !important;
        transform: translateX(4px);
    }

    .sidebar-submenu-link:hover::before {
        background: rgba(255,255,255,0.3);
        width: 10px;
    }

    .sidebar-submenu-link:hover i {
        opacity: 1;
        transform: scale(1.1);
    }

    .sidebar-submenu-link.active {
        color: #ffffff !important;
        background: rgba(37, 99, 235, 0.2) !important;
        font-weight: 700;
    }

    .sidebar-submenu-link.active::before {
        background: #3b82f6;
        width: 10px;
    }

    .sidebar-submenu-link.active i {
        opacity: 1;
    }

    /* Vertical connector line for dropdown group */
    .sidebar-dropdown.open > .sidebar-submenu {
        position: relative;
    }

    .sidebar-dropdown.open > .sidebar-submenu::before {
        content: '';
        position: absolute;
        left: 40px;
        top: 8px;
        bottom: 16px;
        width: 2px;
        background: linear-gradient(180deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.03) 100%);
        border-radius: 4px;
    }

    /* Pulse animation */
    .pulse { animation: pulseSmall 2s infinite; }
    @keyframes pulseSmall { 
        0% { transform: scale(1); opacity: 1; } 
        50% { transform: scale(1.1); opacity: 0.8; } 
        100% { transform: scale(1); opacity: 1; } 
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dropdown toggle behavior
    document.querySelectorAll('.sidebar-dropdown-toggle').forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const parent = this.closest('.sidebar-dropdown');
            
            // Close other dropdowns
            document.querySelectorAll('.sidebar-dropdown.open').forEach(function(item) {
                if (item !== parent) {
                    item.classList.remove('open');
                }
            });
            
            // Toggle current
            parent.classList.toggle('open');
        });
    });
});
</script>
