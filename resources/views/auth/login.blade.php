<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PresenceX</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --primary-blue: #2563eb;
            --secondary-blue: #3b82f6;
            --deep-blue: #172554;
            --light-blue: #f0f9ff;
            --soft-blue: #e0f2fe;
            --text-dark: #1e293b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at top right, var(--secondary-blue), var(--deep-blue));
            position: relative;
            overflow: hidden;
        }

        /* Animated Background Elements */
        .bg-glow {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.2) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 0;
        }

        .glow-1 { top: -200px; left: -100px; animation: float 10s infinite linear; }
        .glow-2 { bottom: -200px; right: -100px; animation: float 15s infinite linear reverse; }

        @keyframes float {
            0% { transform: translate(0, 0); }
            50% { transform: translate(50px, 30px); }
            100% { transform: translate(0, 0); }
        }

        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
            padding: 20px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 32px;
            padding: 60px 50px;
            box-shadow: 0 40px 100px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo-section {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            border-radius: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            box-shadow: 0 15px 35px rgba(37, 99, 235, 0.3);
            transform: rotate(-5deg);
        }

        .logo-icon i {
            font-size: 40px;
            color: white;
        }

        .logo-section h1 {
            font-size: 32px;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 10px;
            letter-spacing: -1px;
        }

        .logo-section p {
            color: #64748b;
            font-size: 15px;
            font-weight: 500;
        }

        /* Tab Buttons */
        .role-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 35px;
            background: #f1f5f9;
            padding: 6px;
            border-radius: 18px;
        }

        .role-tab {
            flex: 1;
            padding: 14px;
            border: none;
            background: transparent;
            color: #64748b;
            font-weight: 700;
            font-size: 14px;
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .role-tab:hover:not(.active) {
            color: var(--primary-blue);
            background: rgba(37, 99, 235, 0.05);
        }

        .role-tab.active {
            background: white;
            color: var(--primary-blue);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: var(--text-dark);
            font-weight: 600;
            font-size: 14px;
            padding-left: 4px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 18px;
        }

        .form-control {
            width: 100%;
            padding: 16px 20px 16px 55px;
            border: 2px solid #f1f5f9;
            border-radius: 16px;
            font-size: 15px;
            color: var(--text-dark);
            transition: all 0.3s ease;
            background: #f8fafc;
            font-weight: 500;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-blue);
            background: white;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            color: white;
            border: none;
            border-radius: 18px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            margin-top: 15px;
            box-shadow: 0 15px 30px rgba(37, 99, 235, 0.2);
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(37, 99, 235, 0.3);
            filter: brightness(1.05);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .footer-text {
            text-align: center;
            margin-top: 35px;
            color: #94a3b8;
            font-size: 13px;
            font-weight: 500;
        }

        /* Forms */
        .login-form {
            display: none;
        }

        .login-form.active {
            display: block;
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.98); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</head>

<body>
    <div class="bg-glow glow-1"></div>
    <div class="bg-glow glow-2"></div>
    <div class="login-container">
        <div class="login-card">
            <!-- Logo Section -->
            <div class="logo-section">
                <div class="logo-icon">
                    <i class="bi bi-qr-code-scan"></i>
                </div>
                <h1>PresenceX</h1>
                <p>Sistem Absensi Digital Modern</p>
            </div>

            <!-- Role Tabs -->
            <div class="role-tabs">
                <button class="role-tab active" data-role="siswa">Siswa</button>
                <button class="role-tab" data-role="guru">Guru</button>
                <button class="role-tab" data-role="admin">Admin</button>
            </div>

            <!-- Siswa Login Form -->
            <form id="siswaForm" class="login-form active" method="POST" action="{{ route('login.post') }}">
                @csrf
                <input type="hidden" name="role" value="siswa">
                
                <div class="form-group">
                    <label for="siswa_nis">Nomor Induk Siswa (NIS)</label>
                    <div class="input-wrapper">
                        <i class="bi bi-person-vcard input-icon"></i>
                        <input type="text" id="siswa_nis" name="nis" class="form-control" placeholder="Masukkan NIS Anda" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="siswa_nama">Nama Lengkap</label>
                    <div class="input-wrapper">
                        <i class="bi bi-person-fill input-icon"></i>
                        <input type="text" id="siswa_nama" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
                    </div>
                </div>

                <button type="submit" class="btn-login">Masuk ke Dashboard</button>
            </form>

            <!-- Guru Login Form -->
            <form id="guruForm" class="login-form" method="POST" action="{{ route('login.post') }}">
                @csrf
                <input type="hidden" name="role" value="guru">
                
                <div class="form-group">
                    <label for="guru_email">Alamat Email</label>
                    <div class="input-wrapper">
                        <i class="bi bi-envelope-fill input-icon"></i>
                        <input type="email" id="guru_email" name="email" class="form-control" placeholder="contoh@sekolah.id" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="guru_password">Password</label>
                    <div class="input-wrapper">
                        <i class="bi bi-lock-fill input-icon"></i>
                        <input type="password" id="guru_password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn-login">Masuk sebagai Guru</button>
            </form>

            <!-- Admin Login Form -->
            <form id="adminForm" class="login-form" method="POST" action="{{ route('login.post') }}">
                @csrf
                <input type="hidden" name="role" value="admin">
                
                <div class="form-group">
                    <label for="admin_username">Username / Email</label>
                    <div class="input-wrapper">
                        <i class="bi bi-shield-lock-fill input-icon"></i>
                        <input type="text" id="admin_username" name="username" class="form-control" placeholder="admin@presencex.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="admin_password">Password</label>
                    <div class="input-wrapper">
                        <i class="bi bi-key-fill input-icon"></i>
                        <input type="password" id="admin_password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn-login">Masuk sebagai Administrator</button>
            </form>

            <div class="footer-text">
                © 2026 PresenceX • Smart Digital Attendance
            </div>

        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Tab Switching
        const tabs = document.querySelectorAll('.role-tab');
        const forms = document.querySelectorAll('.login-form');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const role = tab.dataset.role;
                
                // Update active tab
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                
                // Update active form
                forms.forEach(form => {
                    form.classList.remove('active');
                    if (form.id === role + 'Form') {
                        form.classList.add('active');
                    }
                });
            });
        });

        // Show success message
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#667eea',
                timer: 3000
            });
        @endif

        // Show error message
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ session('error') }}',
                confirmButtonColor: '#667eea'
            });
        @endif

        // Form validation with SweetAlert
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const inputs = form.querySelectorAll('input[required]');
                let isValid = true;
                
                inputs.forEach(input => {
                    if (!input.value.trim()) {
                        isValid = false;
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian!',
                        text: 'Mohon lengkapi semua field yang diperlukan',
                        confirmButtonColor: '#667eea'
                    });
                }
            });
        });
    </script>
</body>

</html>