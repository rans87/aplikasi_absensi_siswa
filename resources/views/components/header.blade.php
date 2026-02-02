<nav class="app-header navbar navbar-expand border-0 shadow-sm"
    style="background: linear-gradient(90deg, #2c1cbaea, #6a3cc0);">

    <div class="container-fluid px-4">

        {{-- LEFT SIDE --}}
        <ul class="navbar-nav align-items-center gap-2">

            {{-- Sidebar Toggle --}}
            <li class="nav-item">
                <a class="nav-link text-white fs-5" data-lte-toggle="sidebar" href="#">
                    <i class="bi bi-list"></i>
                </a>
            </li>

            {{-- Dashboard Icon --}}
            <li class="nav-item d-none d-md-block">
                <a href="#" class="nav-link text-white d-flex align-items-center gap-2">
                    <i class="bi bi-speedometer2"></i>
                    <span class="fw-semibold">Dashboard</span>
                </a>
            </li>

        </ul>

        {{-- RIGHT SIDE --}}
        <ul class="navbar-nav ms-auto align-items-center gap-3">

            {{-- Notification Icon --}}
            <li class="nav-item">
                <a href="#" class="nav-link text-white position-relative">
                    <i class="bi bi-bell fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                        style="font-size:10px;">3</span>
                </a>
            </li>

            {{-- Fullscreen --}}
            <li class="nav-item">
                <a class="nav-link text-white fs-5" data-lte-toggle="fullscreen" href="#">
                    <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                </a>
            </li>

            {{-- PROFILE DROPDOWN --}}
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle d-flex align-items-center gap-2 text-white"
                    data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle fs-4"></i>
                    <span class="d-none d-md-inline fw-semibold">Admin</span>
                </a>

                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-2 p-0"
                    style="border-radius:14px; width:240px; overflow:hidden;">

                    {{-- Header Profile --}}
                    <li class="text-center p-3" style="background: linear-gradient(90deg, #0d6efd, #6f42c1);">
                        <i class="bi bi-person-circle text-white" style="font-size:60px;"></i>
                        <div class="text-white fw-semibold mt-2">Admin</div>
                        <small class="text-white-50">Administrator</small>
                    </li>

                    {{-- Menu Items --}}
                    <li>
                        <a href="#" class="dropdown-item py-2 px-3 d-flex align-items-center gap-2">
                            <i class="bi bi-person-lines-fill text-primary"></i>
                            My Profile
                        </a>
                    </li>

                    <li>
                        <a href="#" class="dropdown-item py-2 px-3 d-flex align-items-center gap-2">
                            <i class="bi bi-gear text-secondary"></i>
                            Settings
                        </a>
                    </li>

                    <li>
                        <hr class="dropdown-divider m-0">
                    </li>

                    <li>
                        {{-- <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="dropdown-item py-2 px-3 text-danger d-flex align-items-center gap-2">
                                <i class="bi bi-box-arrow-right"></i>
                                Logout
                            </button>
                        </form> --}}
                    </li>

                </ul>
            </li>

        </ul>
    </div>
</nav>