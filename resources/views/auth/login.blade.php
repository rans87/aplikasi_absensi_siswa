<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PresenceX | Akses Eksklusif</title>
    
    <!-- Google Fonts: Inter & Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        :root {
            --primary: #0052cc;
            --primary-soft: #e6f0ff;
            --secondary: #00d2ff;
            --white: #ffffff;
            --dark: #091e42;
            --gray: #6b778c;
            --light: #f4f5f7;
            --success: #36b37e;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        h1, h2, .brand-name, .tab-btn {
            font-family: 'Outfit', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--light);
            background-image: linear-gradient(135deg, #f4f5f7 0%, #e6f0ff 100%);
            overflow: hidden;
            position: relative;
        }

        /* Ambient Orbs */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            opacity: 0.4;
            animation: orbMove 20s infinite alternate ease-in-out;
        }
        .orb-1 { width: 400px; height: 400px; background: var(--primary); top: -100px; left: -100px; }
        .orb-2 { width: 300px; height: 300px; background: var(--secondary); bottom: -50px; right: -50px; animation-delay: -5s; }

        @keyframes orbMove {
            from { transform: translate(0, 0) scale(1); }
            to { transform: translate(100px, 100px) scale(1.2); }
        }

        .login-wrapper {
            width: 100%;
            max-width: 440px;
            padding: 20px;
            z-index: 10;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 32px;
            border: 1px solid var(--white);
            box-shadow: 0 40px 80px -20px rgba(9, 30, 66, 0.15);
            padding: 50px 40px;
            text-align: center;
            animation: slideIn 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .brand-logo {
            width: 72px;
            height: 72px;
            background: var(--primary);
            color: white;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin-bottom: 24px;
            box-shadow: 0 15px 30px rgba(0, 82, 204, 0.3);
        }

        .brand-name {
            font-size: 32px;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 8px;
            letter-spacing: -1px;
        }

        .brand-name span { color: var(--primary); }

        .brand-slogan {
            color: var(--gray);
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 40px;
        }

        /* Modern Tabs */
        .tab-nav {
            display: flex;
            background: var(--light);
            padding: 5px;
            border-radius: 18px;
            margin-bottom: 35px;
            position: relative;
        }

        .tab-slider {
            position: absolute;
            left: 5px;
            top: 5px;
            bottom: 5px;
            width: calc(50% - 5px);
            background: var(--white);
            border-radius: 14px;
            box-shadow: 0 4px 12px rgba(9, 30, 66, 0.08);
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 1;
        }
        .tab-slider.right { transform: translateX(100%); }

        .tab-btn {
            flex: 1;
            padding: 14px;
            border: none;
            background: transparent;
            font-size: 14px;
            font-weight: 700;
            color: var(--gray);
            cursor: pointer;
            z-index: 2;
            transition: color 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .tab-btn.active { color: var(--primary); }

        /* Form Area */
        .form-content {
            display: none;
            text-align: left;
            animation: fadeIn 0.5s ease;
        }
        .form-content.active { display: block; }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 10px;
            padding-left: 5px;
        }

        .input-box {
            position: relative;
            margin-bottom: 25px;
        }

        .input-box i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
            font-size: 18px;
            transition: color 0.3s;
        }

        .input-box input {
            width: 100%;
            padding: 16px 20px 16px 50px;
            background: var(--white);
            border: 2px solid var(--light);
            border-radius: 18px;
            font-size: 15px;
            font-weight: 600;
            color: var(--dark);
            transition: all 0.3s;
        }

        .input-box input:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 8px 20px rgba(0, 82, 204, 0.08);
        }

        .input-box input:focus + i { color: var(--primary); }

        .tip-box {
            background: var(--primary-soft);
            padding: 12px 18px;
            border-radius: 14px;
            font-size: 12px;
            color: var(--primary);
            line-height: 1.5;
            margin-bottom: 30px;
            display: flex;
            gap: 12px;
            border: 1px solid rgba(0, 82, 204, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 18px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 18px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s;
            box-shadow: 0 10px 25px rgba(0, 82, 204, 0.2);
        }

        .btn-login:hover {
            background: #0047b3;
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(0, 82, 204, 0.3);
        }

        .btn-login:active { transform: translateY(0); }

        .card-footer {
            margin-top: 40px;
            color: var(--gray);
            font-size: 12px;
            font-weight: 500;
        }

        .card-footer strong { color: var(--primary); }

        /* Loader */
        .loading-ring {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-top: 3px solid white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .loading .loading-ring { display: block; }
        .loading .btn-text, .loading .bi-arrow-right { display: none; }

        @media (max-width: 480px) {
            .login-card {
                padding: 35px 25px;
                border-radius: 24px;
            }
            .brand-logo {
                width: 60px;
                height: 60px;
                font-size: 24px;
            }
            .brand-name {
                font-size: 26px;
            }
            .tab-btn {
                font-size: 13px;
                padding: 12px 10px;
            }
            .input-box input {
                padding: 14px 18px 14px 45px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <div class="login-wrapper">
        <div class="login-card">
            <div class="brand-logo">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <h1 class="brand-name">Presence<span>X</span></h1>
            <p class="brand-slogan">Secure & Intelligent System</p>

            <div class="tab-nav">
                <div class="tab-slider" id="slider"></div>
                <button class="tab-btn active" id="tabG" onclick="switchLogin('guru')">
                    <i class="bi bi-person-badge"></i> GURU
                </button>
                <button class="tab-btn" id="tabS" onclick="switchLogin('siswa')">
                    <i class="bi bi-mortarboard"></i> SISWA
                </button>
            </div>

            <!-- FORM GURU -->
            <form id="formG" class="form-content active" action="{{ route('login.post') }}" method="POST">
                @csrf
                <input type="hidden" name="role" value="guru">
                <label class="form-label">Email Terdaftar</label>
                <div class="input-box">
                    <input type="text" name="email" placeholder="nama@sekolah.id" required autocomplete="off">
                    <i class="bi bi-envelope-at"></i>
                </div>
                <div class="tip-box">
                    <i class="bi bi-lightbulb-fill mt-1"></i>
                    <span><strong>Tips:</strong> Gunakan email resmi Anda tanpa spasi atau karakter khusus. Contoh: <code>mhoerudinspdi@gmail.com</code></span>
                </div>
                <button type="submit" class="btn-login">
                    <span class="btn-text">MASUK DASHBOARD GURU</span>
                    <i class="bi bi-arrow-right"></i>
                    <div class="loading-ring"></div>
                </button>
            </form>

            <!-- FORM SISWA -->
            <form id="formS" class="form-content" action="{{ route('login.post') }}" method="POST">
                @csrf
                <input type="hidden" name="role" value="siswa">
                <label class="form-label">Nomor Induk Siswa (NIS)</label>
                <div class="input-box">
                    <input type="text" name="nis" placeholder="Masukkan Nomor NIS" required autocomplete="off">
                    <i class="bi bi-hash"></i>
                </div>
                <div class="tip-box">
                    <i class="bi bi-info-circle-fill mt-1"></i>
                    <span>Tanyakan pada Administrator jika Anda belum memiliki atau lupa Nomor NIS Anda.</span>
                </div>
                <button type="submit" class="btn-login">
                    <span class="btn-text">MASUK DASHBOARD SISWA</span>
                    <i class="bi bi-arrow-right"></i>
                    <div class="loading-ring"></div>
                </button>
            </form>

            <div class="card-footer">
                &copy; 2026 <strong>PresenceX</strong> &bull; Developed by Zielabs
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function switchLogin(mode) {
            const slider = document.getElementById('slider');
            const fG = document.getElementById('formG');
            const fS = document.getElementById('formS');
            const tG = document.getElementById('tabG');
            const tS = document.getElementById('tabS');

            if (mode === 'guru') {
                slider.classList.remove('right');
                fG.classList.add('active');
                fS.classList.remove('active');
                tG.classList.add('active');
                tS.classList.remove('active');
            } else {
                slider.classList.add('right');
                fS.classList.add('active');
                fG.classList.remove('active');
                tS.classList.add('active');
                tG.classList.remove('active');
            }
        }

        document.querySelectorAll('form').forEach(f => {
            f.addEventListener('submit', function() {
                this.querySelector('.btn-login').classList.add('loading');
            });
        });

        // Feedback System
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Akses Berhasil',
                text: '{{ session('success') }}',
                confirmButtonColor: '#0052cc',
                timer: 2000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Akses Ditolak',
                text: '{{ session('error') }}',
                confirmButtonColor: '#0052cc'
            });
        @endif
    </script>
</body>

</html>