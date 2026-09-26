<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - POS</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            display: flex;
            height: 100vh;
            align-items: center;
            justify-content: center;
            color: #0f172a;
        }

        .login-container {
            display: flex;
            width: 900px;
            max-width: 90%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .login-banner {
            flex: 1;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: white;
            position: relative;
        }

        .login-banner::after {
            content: '';
            position: absolute;
            top: 0; right: 0; bottom: 0; left: 0;
            background-image: url('https://images.unsplash.com/photo-1497935586351-b67a49e012bf?q=80&w=1000&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            opacity: 0.15;
        }

        .banner-content {
            position: relative;
            z-index: 1;
        }

        .banner-content h1 {
            font-size: 32px;
            margin: 0 0 10px 0;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .banner-content p {
            color: #94a3b8;
            line-height: 1.6;
            margin: 0;
        }

        .login-form-wrapper {
            flex: 1;
            padding: 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-header h2 {
            margin: 0 0 8px 0;
            font-size: 24px;
        }

        .login-header p {
            color: #64748b;
            margin: 0 0 32px 0;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
            color: #334155;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            box-sizing: border-box;
            transition: all 0.2s;
            background: #f8fafc;
        }

        .form-control:focus {
            outline: none;
            border-color: #0f172a;
            background: white;
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.1);
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: #0f172a;
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: #1e293b;
        }
        
        .error-message {
            color: #ef4444;
            font-size: 13px;
            margin-top: 6px;
            display: block;
        }

        .remember-flex {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            font-size: 14px;
        }
        
        .remember-flex label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #475569;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-banner">
            <div class="banner-content">
                <img src="{{ asset('logo.png') }}" alt="Logo" style="max-width: 200px; margin-bottom: 20px;">
                <p>Sistem Kasir Pintar (POS) Terintegrasi.<br>Kelola kedai kopi dengan lebih mudah dan cepat.</p>
                <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
                    <div style="font-size: 12px; color: #64748b; margin-bottom: 5px;">AKUN DEMO:</div>
                    <div style="font-size: 13px; margin-bottom: 5px;">• superadmin@example.com / password</div>
                    <div style="font-size: 13px; margin-bottom: 5px;">• admin@example.com / password</div>
                    <div style="font-size: 13px;">• kasir@example.com / password</div>
                </div>
            </div>
        </div>
        
        <div class="login-form-wrapper">
            <div class="login-header">
                <h2>Selamat Datang</h2>
                <p>Silakan masuk ke akun Anda untuk melanjutkan</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div style="color: #10b981; font-size: 14px; margin-bottom: 16px;">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="Masukkan email...">
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">Kata Sandi</label>
                    <input id="password" class="form-control" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="remember-flex">
                    <label for="remember_me">
                        <input id="remember_me" type="checkbox" name="remember" style="accent-color: #0f172a;">
                        Ingat Saya
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" style="color: #64748b; text-decoration: none;">Lupa Sandi?</a>
                    @endif
                </div>

                <button type="submit" class="btn-submit">
                    Masuk ke POS
                </button>
            </form>
        </div>
    </div>

</body>
</html>
