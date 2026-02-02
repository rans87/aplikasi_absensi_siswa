<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard')</title>

    {{-- Fonts --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css">

    {{-- Plugins --}}
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    {{-- AdminLTE --}}
    <link rel="stylesheet" href="{{ asset('assets/css/adminlte.css') }}">

    {{-- Custom Theme (Putih + Biru Modern) --}}
    <style>
        body {
            font-family: 'Source Sans 3', sans-serif;
            background-color: #f4f7fb;
        }

        .app-main {
            padding: 1.5rem;
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 6px 18px rgba(13, 110, 253, 0.08);
        }

        .card-header {
            background: transparent;
            border-bottom: none;
            font-weight: 600;
            color: #0d6efd;
        }

        .btn-primary {
            background: linear-gradient(45deg, #0d6efd, #3a8bfd);
            border: none;
        }

        .btn-primary:hover {
            opacity: 0.9;
        }

        .table thead {
            background-color: #e9f2ff;
        }

        .table tbody tr:hover {
            background-color: #f1f6ff;
        }

        .content-header h1 {
            font-weight: 700;
            color: #0d6efd;
        }
    </style>

    @stack('styles')
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">

        {{-- HEADER --}}
        @include('components.header')

        {{-- SIDEBAR --}}
        @include('components.sidebar')

        {{-- MAIN CONTENT --}}
        <main class="app-main">
            @yield('content')
        </main>

        {{-- FOOTER --}}
        @include('components.footer')

    </div>

    {{-- SCRIPTS --}}
    @include('components.scripts')
    @stack('scripts')

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {

            // =========================
            // LINE CHART (Mingguan)
            // =========================
            const lineCtx = document.getElementById('lineChart');
            if (lineCtx) {
                new Chart(lineCtx, {
                    type: 'line',
                    data: {
                        labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
                        datasets: [
                            {
                                label: 'Hadir',
                                data: [120, 132, 128, 140, 150],
                                borderColor: '#0d6efd',
                                backgroundColor: 'rgba(13,110,253,0.12)',
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: '#0b5ed7',
                                pointBorderColor: '#ffffff',
                                pointRadius: 5
                            },
                            {
                                label: 'Tidak Hadir',
                                data: [10, 8, 12, 6, 5],
                                borderColor: '#e64980',
                                backgroundColor: 'rgba(230,73,128,0.12)',
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: '#d6336c',
                                pointBorderColor: '#ffffff',
                                pointRadius: 5
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                labels: {
                                    color: '#34495e',
                                    font: { weight: '600' }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(13,110,253,0.08)' },
                                ticks: { color: '#6c757d' }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { color: '#6c757d' }
                            }
                        }
                    }
                });
            }

            // =========================
            // DOUGHNUT CHART (Hari Ini)
            // =========================
            const pieCtx = document.getElementById('pieChart');
            if (pieCtx) {
                new Chart(pieCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Hadir', 'Izin', 'Sakit', 'Alpa'],
                        datasets: [{
                            data: [150, 12, 8, 5],
                            backgroundColor: ['#0d6efd', '#20c997', '#fd7e14', '#6f42c1'],
                            hoverOffset: 8,
                            borderWidth: 0
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: '#34495e',
                                    padding: 18,
                                    font: { weight: '600' }
                                }
                            }
                        },
                        cutout: '65%',
                        responsive: true
                    }
                });
            }

        });
    </script>
</body>

</html>