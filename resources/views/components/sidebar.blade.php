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
            <ul class="nav flex-column gap-2" data-lte-toggle="treeview" role="menu" data-accordion="false">

                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                       class="nav-link {{ request()->routeIs('*dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span>Dashboard Utama</span>
                    </a>
                </li>

                {{-- ADMIN ONLY --}}
                @if(Auth::guard('web')->check())
                <li class="nav-header text-white-50 small fw-bold mt-4 mb-2 px-4 ls-2" style="font-size: 10px;">CORE ENGINE</li>
                
                <li class="nav-item">
                    <a href="{{ route('guru.index') }}" class="nav-link {{ request()->routeIs('guru.*') ? 'active' : '' }}">
                        <i class="bi bi-person-badge-fill"></i> <span>Data Guru</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('siswa.index') }}" class="nav-link {{ request()->routeIs('siswa.*') ? 'active' : '' }}">
                        <i class="bi bi-mortarboard-fill"></i> <span>Data Siswa</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('rombongan-belajar.index') }}" class="nav-link {{ request()->routeIs('rombongan-belajar.*') ? 'active' : '' }}">
                        <i class="bi bi-layers-fill"></i> <span>Data Kelas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('anggota-kelas.index') }}" class="nav-link {{ request()->routeIs('anggota-kelas.*') ? 'active' : '' }}">
                        <i class="bi bi-person-check-fill"></i> <span>Penempatan Siswa</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('tahun_ajar.index') }}" class="nav-link {{ request()->routeIs('tahun_ajar.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar3-range-fill"></i> <span>Tahun Ajaran</span>
                    </a>
                </li>
                
                <li class="nav-header text-white-50 small fw-bold mt-4 mb-2 px-4 ls-2" style="font-size: 10px;">ANALYTICS & CONDUCT</li>
                <li class="nav-item">
                    <a href="{{ route('pelanggaran.index') }}" class="nav-link {{ request()->routeIs('pelanggaran.*') ? 'active' : '' }}">
                        <i class="bi bi-patch-exclamation-fill text-rose-soft"></i> <span>Pelanggaran</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('prestasi.index') }}" class="nav-link {{ request()->routeIs('prestasi.*') ? 'active' : '' }}">
                        <i class="bi bi-award-fill text-amber"></i> <span>Prestasi Siswa</span>
                    </a>
                </li>

                <li class="nav-header text-white-50 small fw-bold mt-4 mb-2 px-4 ls-2" style="font-size: 10px;">SECURITY CENTER</li>
                <li class="nav-item">
                    <a href="{{ route('pengguna.index') }}" class="nav-link {{ request()->routeIs('pengguna.*') ? 'active' : '' }}">
                        <i class="bi bi-shield-lock-fill"></i> <span>Akses Admin</span>
                    </a>
                </li>
                @endif

                {{-- GURU ONLY --}}
                @if(Auth::guard('guru')->check())
                <li class="nav-header text-white-50 small fw-bold mt-4 mb-2 px-4 ls-2" style="font-size: 10px;">GURU OPERATIONS</li>
                <li class="nav-item">
                    <a href="{{ route('absensi.scan') }}" class="nav-link {{ request()->routeIs('absensi.scan') ? 'active' : '' }}">
                        <i class="bi bi-qr-code-scan"></i> <span>Pindai Absensi</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('pelanggaran.index') }}" class="nav-link {{ request()->routeIs('pelanggaran.*') ? 'active' : '' }}">
                        <i class="bi bi-patch-exclamation-fill text-rose-soft"></i> <span>Pelanggaran Siswa</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('prestasi.index') }}" class="nav-link {{ request()->routeIs('prestasi.*') ? 'active' : '' }}">
                        <i class="bi bi-award-fill text-amber"></i> <span>Prestasi Siswa</span>
                    </a>
                </li>
                @endif

                {{-- SISWA ONLY --}}
                @if(Auth::guard('siswa')->check())
                <li class="nav-header text-white-50 small fw-bold mt-4 mb-2 px-4 ls-2" style="font-size: 10px;">STUDENT HUB</li>
                <li class="nav-item">
                    <a href="{{ route('siswa.dashboard') }}" class="nav-link {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-person-bounding-box"></i> <span>E-Kartu & QR</span>
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
    
    .nav-header { opacity: 0.4; }
    .text-rose-soft { color: #fb7185; }
    .text-amber { color: #fbbf24; }
</style>
