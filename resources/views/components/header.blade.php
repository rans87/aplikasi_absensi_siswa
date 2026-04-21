<nav class="app-header navbar navbar-expand border-0 shadow-sm sticky-top">
    <div class="container-fluid px-3 px-md-5">
        {{-- LEFT SIDE --}}
        <ul class="navbar-nav align-items-center gap-2">
            {{-- Sidebar Toggle --}}
            <li class="nav-item">
                <a class="nav-link text-primary fs-3 p-2 rounded-4 hover-bg-light transition-all" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="bi bi-list-nested"></i>
                </a>
            </li>

            {{-- Brand for Mobile --}}
            <li class="nav-item d-md-none ms-2">
                <span class="fw-extrabold text-primary fs-4 ls-tight">Presence<span class="text-dark">X</span></span>
            </li>
        </ul>

        {{-- RIGHT SIDE --}}
        <ul class="navbar-nav ms-auto align-items-center gap-3">
            {{-- NOTIFICATION BELL (GURU ONLY) --}}
            @if(Auth::guard('guru')->check())
            <li class="nav-item dropdown me-2">
                <a class="nav-link position-relative text-dark p-2 rounded-4 hover-bg-light transition-all" href="#" role="button" data-bs-toggle="dropdown" id="notification-bell">
                    <i class="bi bi-bell fs-4" id="bell-icon"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none shadow-sm" id="notification-badge" style="margin-left: -5px; margin-top: 5px; font-size: 10px;">
                        0
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-4 p-0 dropdown-custom overflow-hidden" style="border-radius:28px; width:350px;">
                    <div class="p-4 bg-primary text-white d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-bold">Notifikasi Mengajar</h6>
                        <button onclick="markAllAsRead()" class="btn btn-sm btn-link text-white text-decoration-none p-0 opacity-75 hover-opacity-100" style="font-size: 11px;">Baca Semua</button>
                    </div>
                    <div class="scrollbar-custom" style="max-height: 400px; overflow-y: auto;" id="notification-list">
                        {{-- Notifications will be injected here via JS --}}
                        <div class="p-5 text-center text-muted" id="no-notifications">
                            <i class="bi bi-bell-slash display-6 d-block mb-3 opacity-25"></i>
                            <p class="small fw-bold mb-0">Belum ada notifikasi baru</p>
                        </div>
                    </div>
                </div>
            </li>
            @endif

            {{-- Time Display (Subtle) --}}
            <li class="nav-item d-none d-lg-block me-3">
                <div class="glass-btn px-4 py-2 rounded-pill d-flex align-items-center shadow-sm border border-light">
                    <i class="bi bi-clock-fill text-primary me-2 pulse-slow"></i>
                    <span class="text-dark fw-bold small ls-1" id="real-time-display">{{ now()->format('H:i') }}</span>
                    <span class="text-muted small ms-2 fw-medium border-start ps-2 border-opacity-25 border-dark">WIB</span>
                </div>
            </li>

            {{-- PROFILE DROPDOWN --}}
            <li class="nav-item dropdown">
                @php
                    $user = Auth::guard('web')->user() ?? Auth::guard('guru')->user() ?? Auth::guard('siswa')->user();
                    $name = $user->name ?? $user->nama ?? 'User';
                    $roleLabel = Auth::guard('web')->check() ? 'Administrator' : (Auth::guard('guru')->check() ? 'Guru Pengajar' : 'Siswa Sekolah');
                    $initials = strtoupper(substr($name, 0, 1) . (strpos($name, ' ') ? substr($name, strpos($name, ' ')+1, 1) : ''));
                @endphp
                
                <a href="#" class="nav-link dropdown-toggle d-flex align-items-center gap-3 text-dark px-2 py-1 rounded-pill profile-toggle"
                    data-bs-toggle="dropdown">
                    
                    <div class="profile-avatar shadow-glow">
                        <div class="avatar-gradient d-flex align-items-center justify-content-center text-white fw-extrabold">
                            {{ $initials }}
                        </div>
                    </div>
                    
                    <div class="d-none d-md-block text-start lh-1 me-1">
                        <div class="fw-bold text-dark fs-6">{{ Str::words($name, 1, '') }}</div>
                        <div class="text-muted mt-1 ls-1" style="font-size: 9px; text-transform: uppercase; font-weight: 800;">{{ $roleLabel }}</div>
                    </div>
                    
                    <i class="bi bi-chevron-down small text-muted d-none d-md-block ms-1"></i>
                </a>

                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-4 p-3 fade-in dropdown-custom"
                    style="border-radius:30px; min-width:320px;">
                    
                    <li class="p-4 rounded-5 text-center mb-3 profile-dropdown-header">
                        <div class="position-relative d-inline-block mb-3">
                            <div class="profile-avatar-large shadow-lg border border-white border-4">
                                <div class="avatar-gradient d-flex align-items-center justify-content-center text-white fw-extrabold fs-2">
                                    {{ $initials }}
                                </div>
                            </div>
                            <span class="position-absolute bottom-0 end-0 bg-emerald p-2 rounded-circle border border-white border-3 shadow-sm"></span>
                        </div>
                        <div class="fw-extrabold text-dark fs-5 mb-1">{{ $name }}</div>
                        <div class="text-muted small mb-3 fw-medium">ID: {{ $user->nis ?? $user->nip ?? 'SYSTEM' }}</div>
                        <span class="badge-premium px-4 py-2">{{ $roleLabel }}</span>
                    </li>

                    <li><hr class="dropdown-divider opacity-25"></li>
                    
                    <li>
                        <a href="#" class="dropdown-item py-3 px-4 rounded-4 d-flex align-items-center gap-3 dropdown-item-custom">
                            <div class="bg-light-blue p-2 rounded-3 text-primary"><i class="bi bi-person-fill fs-5"></i></div>
                            <div class="fw-bold small">Profil Saya</div>
                        </a>
                    </li>

                    <li>
                        <a href="#" class="dropdown-item py-3 px-4 rounded-4 d-flex align-items-center gap-3 dropdown-item-custom">
                            <div class="bg-soft-blue p-2 rounded-3 text-primary"><i class="bi bi-gear-fill fs-5"></i></div>
                            <div class="fw-bold small">Pengaturan</div>
                        </a>
                    </li>

                    <li><hr class="dropdown-divider opacity-25"></li>

                    <li>
                        <form id="logoutForm" method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="button" onclick="confirmLogout()"
                                class="dropdown-item py-3 px-4 rounded-4 text-rose d-flex align-items-center gap-3 dropdown-item-custom">
                                <div class="bg-rose-soft p-2 rounded-3 text-rose"><i class="bi bi-box-arrow-right fs-5"></i></div>
                                <div class="fw-bold small">Keluar Sistem</div>
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

<style>
    .ls-tight { letter-spacing: -1.5px; }
    .ls-1 { letter-spacing: 0.5px; }
    .transition-all { transition: all 0.3s ease; }
    .hover-bg-light:hover { background-color: #f8fafc !important; }
    
    .glass-btn { background: rgba(255, 255, 255, 0.5); backdrop-filter: blur(8px); }
    .pulse-slow { animation: pulse 2s infinite; }
    @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }

    .profile-avatar { width: 42px; height: 42px; border-radius: 12px; overflow: hidden; }
    .avatar-gradient { width: 100%; height: 100%; background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue)); }
    .shadow-glow { box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3); }

    .dropdown-custom { 
        transform-origin: top right; 
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    
    @media (max-width: 576px) {
        .dropdown-custom {
            width: 92vw !important;
            min-width: unset !important;
            margin-right: 4vw !important;
            border-radius: 24px !important;
        }
        .profile-avatar-large {
            width: 70px !important;
            height: 70px !important;
            border-radius: 20px !important;
        }
        .profile-dropdown-header {
            padding: 1.5rem !important;
        }
    }

    .profile-dropdown-header { background: linear-gradient(to bottom, #f8fafc, #ffffff); border: 1px solid var(--border-color); }
    .profile-avatar-large { width: 90px; height: 90px; border-radius: 28px; overflow: hidden; }
    .badge-premium { background: var(--soft-blue); color: var(--primary-blue); border-radius: 12px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; }

    .dropdown-item-custom { transition: all 0.2s ease; }
    .dropdown-item-custom:hover { background: var(--light-blue) !important; color: var(--primary-blue) !important; transform: scale(1.02) translateX(5px); }
    .bg-rose-soft { background-color: #fff1f2; }
    .bg-light-blue { background-color: #f0f9ff; }
    .bg-soft-blue { background-color: #e0f2fe; }
    .text-rose { color: #e11d48; }
</style>

@push('scripts')
<script>
    // Real-time clock
    setInterval(() => {
        const now = new Date();
        const timeStr = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
        const el = document.getElementById('real-time-display');
        if(el) el.innerText = timeStr;
    }, 30000);

    @if(Auth::guard('guru')->check())
    // Notification polling
    function fetchNotifications() {
        fetch('{{ route("notifikasi-guru.get") }}', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            const badge = document.getElementById('notification-badge');
            const icon = document.getElementById('bell-icon');
            const list = document.getElementById('notification-list');
            const noNotif = document.getElementById('no-notifications');

            if (data.length > 0) {
                badge.textContent = data.length;
                badge.classList.remove('d-none');
                icon.className = 'bi bi-bell-fill fs-4 text-primary';
                noNotif.classList.add('d-none');

                let html = '';
                data.forEach(n => {
                    const tipeIcon = n.tipe === 'mengajar' ? 'bi-book-fill text-primary' :
                                     n.tipe === 'selesai' ? 'bi-check-circle-fill text-success' :
                                     'bi-info-circle-fill text-info';
                    const tipeColor = n.tipe === 'mengajar' ? '#eff6ff' :
                                      n.tipe === 'selesai' ? '#ecfdf5' : '#f0f9ff';
                    html += `
                        <div class="p-3 border-bottom notif-item" style="cursor:pointer; transition: all 0.2s; background: ${!n.dibaca ? tipeColor : '#fff'}" onclick="markAsRead(${n.id})">
                            <div class="d-flex align-items-start gap-3">
                                <div class="p-2 rounded-3" style="background: ${tipeColor}">
                                    <i class="bi ${tipeIcon} fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-dark small">${n.judul}</div>
                                    <div class="text-muted small mt-1" style="line-height: 1.4;">${n.pesan}</div>
                                    <div class="text-muted mt-2" style="font-size: 10px; font-weight: 700;">
                                        <i class="bi bi-clock me-1"></i>${timeAgo(n.created_at)}
                                    </div>
                                </div>
                            </div>
                        </div>`;
                });
                list.innerHTML = html;
            } else {
                badge.classList.add('d-none');
                icon.className = 'bi bi-bell fs-4';
                noNotif.classList.remove('d-none');
                list.innerHTML = '';
                list.appendChild(noNotif);
            }
        }).catch(() => {});
    }

    function markAsRead(id) {
        fetch('/notifikasi-guru/' + id + '/baca', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        }).then(() => fetchNotifications());
    }

    function markAllAsRead() {
        fetch('{{ route("notifikasi-guru.baca-semua") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        }).then(() => fetchNotifications());
    }

    function timeAgo(dateStr) {
        const now = new Date();
        const d = new Date(dateStr);
        const diff = Math.floor((now - d) / 1000);
        if (diff < 60) return 'Baru saja';
        if (diff < 3600) return Math.floor(diff / 60) + ' menit lalu';
        if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';
        return Math.floor(diff / 86400) + ' hari lalu';
    }

    // Initial fetch + poll every 30s
    fetchNotifications();
    setInterval(fetchNotifications, 30000);
    @endif
</script>
@endpush