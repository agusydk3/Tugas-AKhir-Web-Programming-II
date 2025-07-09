<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Customer')</title>
    <link rel="stylesheet" href="{{ asset('css/user-dashboard.css') }}">
    <style>
        /* Animasi page transition */
        .page-transition-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.3);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        
        .page-transition-overlay.active {
            opacity: 1;
            pointer-events: all;
        }
        
        .page-loader {
            width: 40px;
            height: 40px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #667eea;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Animasi konten */
        .content {
            opacity: 0;
            transform: translateY(15px);
            transition: opacity 0.4s ease-out, transform 0.4s ease-out;
        }
        
        .content.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Page Transition Overlay -->
    <div class="page-transition-overlay" id="pageTransition">
        <div class="page-loader"></div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo">T</div>
            <div class="logo-text">Toko Online</div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
                <a href="{{ route('user.dashboard') }}" class="nav-link">
                    <span class="nav-icon">📊</span>
                    Dasbor
                </a>
            </div>
            <div class="nav-item {{ request()->is('product') ? 'active' : '' }}">
                <a href="{{ route('user.product') }}" class="nav-link">
                    <span class="nav-icon">📦</span>
                    Produk
                </a>
            </div>
            <div class="nav-item {{ request()->is('deposit') ? 'active' : '' }}">
                <a href="{{ route('user.deposit') }}" class="nav-link">
                    <span class="nav-icon">💰</span>
                    Deposit
                </a>
            </div>
            <div class="nav-item {{ request()->is('history') ? 'active' : '' }}">
                <a href="{{ route('user.history') }}" class="nav-link">
                    <span class="nav-icon">📋</span>
                    Riwayat
                </a>
            </div>
            <div class="nav-item {{ request()->is('contact') ? 'active' : '' }}">
                <a href="{{ route('user.contact') }}" class="nav-link">
                    <span class="nav-icon">📞</span>
                    Kontak
                </a>
            </div>
            <div class="nav-item">
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <span class="nav-icon">🚪</span>
                    Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </nav>
    </div>

    <!-- Overlay untuk mobile -->
    <div class="overlay" id="overlay"></div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="header">
            <!-- Sidebar Toggle Button (mobile only) -->
            <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle menu">☰</button>
            
            <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
            
            <div class="user-profile">
                <div class="user-avatar">{{ Auth::user()->name[0] ?? 'U' }}</div>
                <div class="user-info">
                    <h4>{{ Auth::user()->name ?? 'User' }}</h4>
                    <p>{{ Auth::user()->getRoleName() }}</p>
                </div>
            </div>
        </div>
        
        <div class="content" id="pageContent">
            @yield('content')
        </div>
    </div>

    <script>
        // Mobile sidebar toggle
        const sidebar = document.getElementById("sidebar");
        const overlay = document.getElementById("overlay");
        const sidebarToggle = document.getElementById("sidebarToggle");
        const pageTransition = document.getElementById("pageTransition");
        const pageContent = document.getElementById("pageContent");
        
        // Menampilkan konten dengan animasi saat halaman dimuat
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                pageContent.classList.add("visible");
            }, 100);
        });
        
        function toggleSidebar() {
            sidebar.classList.toggle("open");
            overlay.classList.toggle("show");
            
            if (sidebar.classList.contains("open")) {
                sidebarToggle.innerHTML = "✕";
            } else {
                sidebarToggle.innerHTML = "☰";
            }
        }
        
        sidebarToggle.addEventListener("click", toggleSidebar);
        overlay.addEventListener("click", toggleSidebar);
        
        // Menambahkan animasi saat berpindah halaman
        document.querySelectorAll(".nav-link").forEach(link => {
            link.addEventListener("click", function(e) {
                // Hanya berlaku untuk tautan internal (bukan logout)
                if (this.getAttribute("href") && !this.getAttribute("href").startsWith("#")) {
                    e.preventDefault();
                    const targetUrl = this.getAttribute("href");
                    
                    // Tampilkan overlay transisi
                    pageTransition.classList.add("active");
                    
                    // Sembunyikan konten dengan animasi
                    pageContent.classList.remove("visible");
                    
                    // Navigasi ke halaman baru setelah animasi selesai
                    setTimeout(function() {
                        window.location.href = targetUrl;
                    }, 500);
                }
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html> 