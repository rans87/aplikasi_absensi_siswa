<aside class="app-sidebar shadow-sm sidebar-modern">
    
    <div class="sidebar-brand d-flex align-items-center gap-2 px-3 py-3 border-bottom border-light border-opacity-25">
        <img src="{{ asset('assets/img/logo.png') }}" class="brand-image shadow-sm rounded-3" style="width:38px">
        <span class="brand-text fw-bold text-white fs-5">PresenceX</span>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-3">
            <ul class="nav sidebar-menu flex-column px-2">

                <li class="nav-item mb-1">
                    <a href="{{ route('dashboard') }}"
                       class="nav-link sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>

                <li class="nav-item mb-1">
                    <a href="{{ route('guru.index') }}"
                       class="nav-link sidebar-link {{ request()->routeIs('guru.*') ? 'active' : '' }}">
                        <i class="bi bi-person-badge-fill me-2"></i> Guru
                    </a>
                </li>

                <li class="nav-item mb-1">
                    <a href="{{ route('pengguna.index') }}"
                       class="nav-link sidebar-link {{ request()->routeIs('pengguna.*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill me-2"></i> Pengguna
                    </a>
                </li>

                <li class="nav-item mb-1">
                    <a href="{{ route('siswa.index') }}"
                       class="nav-link sidebar-link {{ request()->routeIs('siswa.*') ? 'active' : '' }}">
                        <i class="bi bi-mortarboard-fill me-2"></i> Siswa
                    </a>
                </li>

                <li class="nav-item mb-1">
                    <a href="{{ route('rombongan-belajar.index') }}"
                       class="nav-link sidebar-link {{ request()->routeIs('rombongan-belajar.*') ? 'active' : '' }}">
                        <i class="bi bi-diagram-3-fill me-2"></i> Rombel
                    </a>
                </li>

                <li class="nav-item mb-1">
                    <a href="{{ route('absensi.index') }}"
                       class="nav-link sidebar-link {{ request()->routeIs('absensi.*') ? 'active' : '' }}">
                        <i class="bi bi-clipboard-check-fill me-2"></i> Absensi
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>

<style>
    .nav-link.hover-light:hover {
    background-color: rgba(255,255,255,0.15);
    transition: 0.2s ease-in-out;
    }

    .app-sidebar .nav-link {
        font-size: 15px;
        letter-spacing: 0.3px;
    }

    .app-sidebar .nav-link i {
        font-size: 18px;
    }

    .app-sidebar .nav-link.active,
    .app-sidebar .nav-link.bg-white {
        transition: 0.2s;
    }

    /* Background utama sidebar */
    .sidebar-modern {
        background: linear-gradient(180deg, #2c1cbaea, #6a3cc0);
    }

    /* Memaksa SEMUA link di sidebar (sebelum/sesudah klik) berwarna putih */
    .app-sidebar .nav-link.sidebar-link {
        color: #ffffff !important; /* Putih solid */
        opacity: 1 !important;    /* Menghilangkan efek pudar jika ada */
        font-size: 15px;
        letter-spacing: 0.3px;
        border-radius: 12px;
        padding: 10px 14px;
        font-weight: 500;
        transition: all 0.2s ease-in-out;
        position: relative;
    }

    /* Memaksa Ikon tetap putih */
    .app-sidebar .nav-link.sidebar-link i {
        color: #ffffff !important;
        font-size: 18px;
    }

    /* Efek Hover (Saat kursor di atasnya) */
    .sidebar-link:hover {
        background-color: rgba(255, 255, 255, 0.15); /* Background agak terang dikit saat didekati */
        color: #ffffff !important;
    }

    /* Kondisi ACTIVE (Setelah diklik/Halaman yang sedang dibuka) */
    .sidebar-link.active {
        background-color: rgba(255, 255, 255, 0.25); /* Tetap pakai transparan agar tulisan putih tidak tenggelam */
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    /* Garis aksen kiri saat aktif */
    .sidebar-link.active::before {
        content: "";
        position: absolute;
        left: 0;
        top: 8px;
        bottom: 8px;
        width: 4px;
        border-radius: 0 6px 6px 0;
        background-color: #ffffff;
    }

</style>