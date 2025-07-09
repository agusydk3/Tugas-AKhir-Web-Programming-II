<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - @yield('title')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @yield('css')
</head>
<body>
    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar">
        <div class="sidebar-header">
            <button id="sidebarClose" class="sidebar-close" type="button" aria-label="Close Sidebar" style="display:none;">
                <i class="fas fa-times"></i>
            </button>
            <div class="card-icon">
                <i class="fas fa-tachometer-alt"></i>
            </div>
            <h2 class="sidebar-title">Admin Panel</h2>
        </div>
        <nav class="sidebar-menu">
            <a href="{{ route('admin.dashboard') }}" class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" data-page="dashboard">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.products.index') }}" class="menu-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" data-page="products">
                <i class="fas fa-box"></i>
                <span>Product Management</span>
            </a>
            <a href="{{ route('admin.stocks.index') }}" class="menu-item {{ request()->routeIs('admin.stocks.*') ? 'active' : '' }}" data-page="stock">
                <i class="fas fa-warehouse"></i>
                <span>Stock Management</span>
            </a>
            <a href="{{ route('admin.transactions') }}" class="menu-item {{ request()->routeIs('admin.transactions') ? 'active' : '' }}" data-page="transactions">
                <i class="fas fa-receipt"></i>
                <span>Transaction History</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" data-page="users">
                <i class="fas fa-users"></i>
                <span>User Management</span>
            </a>
        </nav>
    </aside>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Topbar -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle" id="sidebarToggle" type="button">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 style="font-size: 1.2rem; font-weight: 600; color: #1e293b;">Admin Dashboard</h1>
            </div>
            <div class="topbar-right">
                <div class="user-profile">
                    <div class="user-avatar">{{ Auth::user()->name[0] ?? 'A' }}</div>
                    <span style="font-weight: 500;">{{ Auth::user()->name }}</span>
                    <small style="color: #64748b; margin-left: 5px;">({{ Auth::user()->getRoleName() }})</small>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </header>

        <!-- Content Area -->
        <main class="content">
            @yield('content')
        </main>
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
    @yield('scripts')
</body>
</html> 