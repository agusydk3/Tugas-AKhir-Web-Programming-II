<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - Digital Store</title>
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
        <h1 class="auth-header">Daftar</h1>
        <p class="auth-subtitle">Buat akun baru untuk mengakses layanan kami</p>
        
        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="form-group">
                <label for="name" class="form-label">Nama</label>
                <input id="name" type="text" class="form-input" name="name" value="{{ old('name') }}" required autofocus>
                @error('name')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" class="form-input" name="email" value="{{ old('email') }}" required>
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
            
            <div class="form-group">
                <label for="password-confirm" class="form-label">Konfirmasi Password</label>
                <input id="password-confirm" type="password" class="form-input" name="password_confirmation" required>
            </div>
            
            <button type="submit" class="auth-btn">Daftar</button>
        </form>
        
        <div class="form-footer">
            Sudah punya akun? <a href="{{ route('login') }}">Login</a>
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