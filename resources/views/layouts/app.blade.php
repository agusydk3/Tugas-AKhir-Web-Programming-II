<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Toko Online')</title>
    
    <!-- Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #f5f7fa;
            min-height: 100vh;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 15px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            font-weight: bold;
            text-decoration: none;
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            background: #4CAF50;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .navbar-menu {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .navbar-item {
            color: white;
            text-decoration: none;
            font-size: 14px;
            opacity: 0.9;
            transition: opacity 0.3s ease;
        }

        .navbar-item:hover {
            opacity: 1;
        }

        .user-dropdown {
            position: relative;
            display: inline-block;
        }

        .user-dropdown-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .user-dropdown-content {
            position: absolute;
            right: 0;
            background: white;
            min-width: 160px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            z-index: 1;
            margin-top: 10px;
            display: none;
        }

        .user-dropdown-content.show {
            display: block;
        }

        .dropdown-item {
            display: block;
            padding: 12px 16px;
            text-decoration: none;
            color: #333;
            transition: background 0.2s ease;
        }

        .dropdown-item:hover {
            background: #f5f7fa;
        }

        .logout-form {
            margin: 0;
            padding: 0;
        }

        .logout-btn {
            width: 100%;
            text-align: left;
            background: none;
            border: none;
            padding: 12px 16px;
            color: #dc3545;
            cursor: pointer;
            font-size: 14px;
            font-family: inherit;
        }

        .logout-btn:hover {
            background: #f5f7fa;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .role-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
        }

        .role-badge.customer {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .role-badge.reseller {
            background-color: #e8f5e9;
            color: #4caf50;
        }

        .role-badge.admin {
            background-color: #fff8e1;
            color: #ff9800;
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 15px;
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .navbar-menu {
                width: 100%;
                justify-content: center;
                flex-wrap: wrap;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    @include('layouts.partials.navbar')

    <div class="container">
        @yield('content')
    </div>

    <!-- Scripts -->
    <script>
        function toggleDropdown() {
            document.getElementById('userDropdown').classList.toggle('show');
        }

        // Close the dropdown if clicked outside
        window.onclick = function(event) {
            if (!event.target.matches('.user-dropdown-btn') && 
                !event.target.matches('.user-dropdown-btn *')) {
                const dropdown = document.getElementById('userDropdown');
                if (dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                }
            }
        }
    </script>
    
    @stack('scripts')
</body>
</html> 