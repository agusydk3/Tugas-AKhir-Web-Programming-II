<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Digital Store</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
    <div class="header">
    <div class="logo">
            <div class="logo-icon">T</div>
            <span>Toko Online</span>
        </div>
        
        <div class="nav-menu">
            <a href="{{ route('login') }}" class="nav-item">Home</a>
            <a href="{{ route('login') }}" class="nav-item active">Masuk</a>
            <a href="{{ route('register') }}" class="nav-item">Daftar</a>
        </div>

        <div class="social-icons">
            <a href="#" class="social-icon">f</a>
            <a href="#" class="social-icon">📞</a>
        </div>
    </div>

    <div class="auth-container">
        <h1 class="auth-header">Login</h1>
        <p class="auth-subtitle">Masuk untuk mengakses akun Anda</p>
        
        @if (session('error'))
        <div class="alert">
            {{ session('error') }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" class="form-input" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input id="password" type="password" class="form-input" name="password" required>
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-options">
                <div class="checkbox-container">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember">Ingat saya</label>
                </div>
                <a href="#">Lupa password?</a>
            </div>
            
            <button type="submit" class="auth-btn">Login</button>
        </form>
        
        <div class="form-footer">
            Belum punya akun? <a href="{{ route('register') }}">Daftar Sekarang</a>
        </div>
    </div>

    <div class="contact-info">
        <h3 class="contact-title">Informasi Kontak</h3>
        <div class="contact-item">
            <span class="contact-label">Email:</span>
            <span class="contact-value">support@digitalstore.com</span>
        </div>
        <div class="contact-item">
            <span class="contact-label">WhatsApp:</span>
            <span class="contact-value">+62 812 3456 7890</span>
        </div>
        <div class="contact-item">
            <span class="contact-label">Telegram:</span>
            <span class="contact-value">@digitalstore</span>
        </div>
        <div class="contact-item">
            <span class="contact-label">Jam Operasional:</span>
            <span class="contact-value">24 Jam</span>
        </div>
    </div>
</body>
</html> 