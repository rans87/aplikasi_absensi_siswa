<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - PresenceX</title>

    <!-- Google Fonts: Outfit -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <!-- OverlayScrollbars -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/styles/overlayscrollbars.min.css">
    <!-- AdminLTE v4 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/css/adminlte.min.css">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <style>
        :root {
            --primary-blue: #2563eb;
            --secondary-blue: #3b82f6;
            --light-blue: #f0f9ff;
            --soft-blue: #e0f2fe;
            --dark-blue: #1e3a8a;
            --deep-blue: #0f172a;
            --pure-white: #ffffff;
            --bg-gray: #f8fafc;
            --border-color: #f1f5f9;
            --accent-glow: rgba(37, 99, 235, 0.1);
            --rose: #e11d48;
            --emerald: #10b981;
            --amber: #f59e0b;
        }

        html {
            background-color: var(--deep-blue) !important;
        }

        body {
            font-family: 'Outfit', 'Inter', sans-serif;
            background-color: var(--bg-gray);
            color: #1e293b;
            overflow-x: hidden;
            letter-spacing: -0.01em;
        }

        /* Force 24-hour time inputs */
        input[type="time"]::-webkit-calendar-picker-indicator {
            filter: invert(0.5);
        }

        /* High Performance Layout */
        .app-wrapper {
            background-color: var(--bg-gray);
        }

        /* Premium Header */
        .app-header {
            background: rgba(255, 255, 255, 0.85) !important;
            backdrop-filter: blur(12px) !important;
            border-bottom: 1px solid var(--border-color) !important;
            box-shadow: 0 4px 15px -1px rgba(0, 0, 0, 0.02) !important;
            padding: 0.5rem 0 !important;
        }

        /* Modern Sidebar */
        .app-sidebar {
            background: var(--deep-blue) !important;
            border-right: none !important;
            box-shadow: 10px 0 30px rgba(0,0,0,0.1) !important;
        }

        .app-sidebar .sidebar-wrapper {
            height: calc(100vh - 130px) !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
        }

        .sidebar-brand {
            border-bottom: 1px solid rgba(255,255,255,0.05) !important;
            padding: 2.5rem 1.75rem !important;
        }

        .nav-link {
            border-radius: 18px !important;
            margin: 6px 18px !important;
            padding: 14px 20px !important;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
            color: rgba(255,255,255,0.6) !important;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .nav-link i {
            font-size: 1.35rem;
            margin-right: 14px;
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            opacity: 0.8;
        }

        .nav-link.active {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue)) !important;
            color: var(--pure-white) !important;
            box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.5) !important;
        }

        .nav-link.active i {
            transform: scale(1.1) rotate(5deg);
            opacity: 1;
        }

        .nav-link:hover:not(.active) {
            background-color: rgba(255,255,255,0.08) !important;
            color: var(--pure-white) !important;
            transform: translateX(8px);
        }

        /* Global UI Elements */
        .card {
            background: var(--pure-white);
            border-radius: 32px !important;
            border: 1px solid var(--border-color) !important;
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.04) !important;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.1) !important;
        }

        /* Action Buttons */
        .btn {
            border-radius: 20px !important;
            padding: 14px 30px !important;
            font-weight: 700 !important;
            letter-spacing: 0.3px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue)) !important;
            border: none !important;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.25) !important;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(37, 99, 235, 0.35) !important;
            filter: brightness(1.1);
        }

        /* Modern Form Styling */
        .form-control, .form-select {
            padding: 14px 20px !important;
            border-radius: 18px !important;
            border: 2px solid #f1f5f9 !important;
            background-color: #f8fafc !important;
            font-weight: 500 !important;
            transition: all 0.3s ease !important;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-blue) !important;
            background-color: #ffffff !important;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1) !important;
        }

        .form-label {
            margin-bottom: 10px !important;
            font-weight: 700 !important;
            color: var(--deep-blue) !important;
            font-size: 0.9rem !important;
            padding-left: 5px;
        }

        /* Ultra-Modern Tables */
        .table {
            --bs-table-hover-bg: rgba(37, 99, 235, 0.02);
            margin-bottom: 0;
        }

        .table thead th {
            background-color: #f8fafc !important;
            color: #64748b !important;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.12em;
            padding: 1.75rem 1.5rem !important;
            border: none !important;
        }

        .table tbody td {
            padding: 1.75rem 1.5rem !important;
            border-bottom: 1px solid var(--border-color) !important;
            vertical-align: middle;
        }

        /* App Main Context */
        .app-main {
            padding: 3rem !important;
            background-color: var(--bg-gray);
            min-height: calc(100vh - 120px);
        }

        @media (max-width: 991.98px) {
            .app-main {
                padding: 1.5rem 1rem !important;
            }
            .sidebar-expand-lg .app-header {
                padding: 0.25rem 0 !important;
            }
        }

        @media (max-width: 768px) {
            .app-main {
                padding: 1rem 0.5rem !important;
            }
            h1.display-4, .display-4 { font-size: 2.25rem !important; }
            h1.display-5, .display-5 { font-size: 1.75rem !important; }
            h1.display-6, .display-6 { font-size: 1.5rem !important; }
            .card { border-radius: 20px !important; }
            .card-body { padding: 1.25rem !important; }
            .btn { padding: 10px 18px !important; font-size: 0.85rem !important; }
            .container-fluid { padding-left: 0.5rem !important; padding-right: 0.5rem !important; }
            
            /* Table responsiveness fix */
            .table-responsive {
                border-radius: 16px;
                border: 1px solid var(--border-color);
                margin-bottom: 1rem;
            }
            .table thead th {
                padding: 1.25rem 1rem !important;
            }
            .table tbody td {
                padding: 1.25rem 1rem !important;
            }
        }

        @media (max-width: 576px) {
            h1.display-4, .display-4 { font-size: 1.85rem !important; }
            h1.display-5, .display-5 { font-size: 1.5rem !important; }
            .h4-mobile { font-size: 1.25rem !important; }
            .app-main { padding: 0.75rem 0.25rem !important; }
            .card { border-radius: 16px !important; }
            .nav-link { margin: 4px 8px !important; padding: 10px 12px !important; }
            .btn-lg { padding: 12px 20px !important; font-size: 0.9rem !important; }
            .sidebar-brand { padding: 1.5rem 1rem !important; }
            .brand-text { font-size: 1.5rem !important; }
        }

        /* Prevent horizontal overflow on mobile */
        .row { --bs-gutter-x: 1rem; }
        @media (max-width: 576px) {
            .row { --bs-gutter-x: 0.75rem; }
        }

        /* Ensure images and QR are responsive */
        img, svg { max-width: 100%; height: auto; }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: var(--bg-gray); }
        ::-webkit-scrollbar-thumb { 
            background: #cbd5e1; 
            border-radius: 20px;
            border: 2px solid var(--bg-gray);
        }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Smooth Animations */
        .fade-in { animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
        @keyframes fadeIn { 
            from { opacity: 0; transform: translateY(30px) scale(0.98); } 
            to { opacity: 1; transform: translateY(0) scale(1); } 
        }

        .hover-up:hover {
            transform: translateY(-8px) scale(1.01);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Color Utilities */
        .bg-primary-soft { background-color: rgba(37, 99, 235, 0.1) !important; color: #2563eb !important; }
        .bg-emerald-soft { background-color: rgba(16, 185, 129, 0.1) !important; color: #059669 !important; }
        .bg-rose-soft { background-color: rgba(225, 29, 72, 0.1) !important; color: #e11d48 !important; }
        .bg-amber-soft { background-color: rgba(245, 158, 11, 0.1) !important; color: #d97706 !important; }
        .bg-info-soft { background-color: rgba(14, 165, 233, 0.1) !important; color: #0284c7 !important; }
        .bg-warning-soft { background-color: rgba(245, 158, 11, 0.15) !important; color: #9a3412 !important; }
        .bg-indigo-soft { background-color: rgba(79, 70, 229, 0.1) !important; color: #4f46e5 !important; }

        .text-emerald { color: #059669 !important; }
        .text-rose { color: #e11d48 !important; }
        .text-amber { color: #d97706 !important; }
        .text-info { color: #0284c7 !important; }
        .text-warning { color: #9a3412 !important; }
        .text-emerald { color: #10b981 !important; }

        .pulse { animation: pulseSmall 2s infinite; }
        @keyframes pulseSmall { 
            0% { transform: scale(1); opacity: 1; } 
            50% { transform: scale(1.1); opacity: 0.8; } 
            100% { transform: scale(1); opacity: 1; } 
        }
    </style>

    @stack('styles')
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        @include('components.header')
        @include('components.sidebar')

        <main class="app-main fade-in">
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 fade show d-flex align-items-center" role="alert">
                        <i class="bi bi-check-circle-fill me-3 fs-4"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 fade show d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>

        <footer class="app-footer border-0 py-4 text-center" style="background: var(--deep-blue) !important; margin: 0 !important;">
            <div class="container">
                <span class="fw-medium" style="color: rgba(255,255,255,0.5);"><strong style="color: rgba(255,255,255,0.8);">PresenceX</strong> &copy; 2024. Crafted for Excellence.</span>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/browser/overlayscrollbars.browser.es6.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/js/adminlte.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        });
        function confirmLogout() {
            Swal.fire({
                title: 'Konfirmasi Keluar',
                text: "Apakah Anda yakin ingin mengakhiri sesi ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#f1f5f9',
                cancelButtonTextColor: '#1e293b',
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal',
                borderRadius: '24px',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logoutForm').submit();
                }
            })
        }

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
    @stack('scripts')
</body>
</html>