<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Access - PresenceX</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --deep-blue: #0f172a;
            --primary-blue: #2563eb;
            --glass: rgba(255, 255, 255, 0.1);
        }
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: var(--deep-blue);
            font-family: 'Outfit', sans-serif;
            overflow: hidden;
            position: relative;
        }
        .bg-glow {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, var(--primary-blue) 0%, transparent 70%);
            top: -200px;
            right: -200px;
            opacity: 0.3;
            filter: blur(60px);
        }
        .card {
            width: 100%;
            max-width: 400px;
            padding: 40px;
            border-radius: 30px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            z-index: 10;
        }
        @media (max-width: 480px) {
            .card { margin: 20px; padding: 30px; }
        }
        .logo-box {
            width: 60px;
            height: 60px;
            background: var(--primary-blue);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 28px;
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3);
        }
        h2 { text-align: center; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 13px; font-weight: 600; opacity: 0.7; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px; }
        input {
            width: 100%;
            padding: 14px 18px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            color: white;
            font-size: 15px;
            box-sizing: border-box;
            transition: 0.3s;
        }
        input:focus { outline: none; border-color: var(--primary-blue); background: rgba(255, 255, 255, 0.1); }
        button {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 14px;
            background: var(--primary-blue);
            color: white;
            font-weight: 800;
            font-size: 15px;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }
        button:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3); background: #1d4ed8; }
        .error { background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.3); padding: 12px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; color: #fca5a5; }
    </style>
</head>
<body>
    <div class="bg-glow"></div>
    <div class="card">
        <div class="logo-box"><i class="bi bi-shield-lock-fill"></i></div>
        <h2>Admin Console</h2>
        @if($errors->any())
            <div class="error"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="/admin/login">
            @csrf
            <div class="form-group">
                <label>Admin Username</label>
                <input type="text" name="username" placeholder="Masukkan ID Admin" required>
            </div>
            <div class="form-group">
                <label>Security Key</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit">AUTHENTICATE ACCESS</button>
        </form>
    </div>
</body>
</html>