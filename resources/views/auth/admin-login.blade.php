<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Admin Login - PresenceX</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            font-family: 'Poppins', sans-serif;
        }

        .card {
            width: 100%;
            max-width: 380px;
            padding: 35px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            color: white;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0 15px;
            border: none;
            border-radius: 8px;
        }

        button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 8px;
            background: white;
            color: #1e3a8a;
            font-weight: 600;
            cursor: pointer;
        }

        .error {
            background: rgba(255, 0, 0, 0.2);
            padding: 8px;
            border-radius: 6px;
            margin-bottom: 10px;
            font-size: 13px;
        }
    </style>
</head>

<body>

    <div class="card">
        <h2>Admin Panel</h2>

        @if($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="/admin/login">
            @csrf
            <label>Username</label>
            <input type="text" name="username" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit">Login Admin</button>
        </form>
    </div>

</body>

</html>