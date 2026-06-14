<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SchoolCloud ERP</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Syne:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icon CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Premium Design Palette Styling -->
    <style>
        :root {
            --bg-slate: #020617;
            --sidebar-bg: #0B0F19;
            --panel-bg: #111827;
            --text-main: #F9FAFB;
            --text-muted: #9CA3AF;
            --accent: #3B82F6;
            --accent-hover: #2563EB;
            --border: rgba(255, 255, 255, 0.08);
            --card-glass: rgba(17, 24, 39, 0.7);
            --success: #10B981;
            --danger: #EF4444;
            --warning: #F59E0B;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-slate);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        /* Layout Container */
        .wrapper {
            display: flex;
            width: 100vw;
            min-height: 100vh;
        }

        /* Sidebar navigation */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 2rem 1.5rem;
            position: fixed;
            height: 100vh;
            z-index: 100;
            transition: all 0.3s;
        }

        .sidebar-logo {
            font-family: 'Syne', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--accent);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            margin-bottom: 3rem;
        }

        .sidebar-logo span {
            color: var(--text-main);
        }

        .nav-links {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: auto;
        }

        .nav-item a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.85rem 1rem;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            border-radius: 12px;
            transition: all 0.2s;
        }

        .nav-item a:hover, .nav-item.active a {
            color: var(--text-main);
            background-color: rgba(59, 130, 246, 0.1);
        }

        .nav-item.active a {
            border-left: 3px solid var(--accent);
        }

        .user-block {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #FFF;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-size: 0.9rem;
            font-weight: 700;
        }

        .user-role {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        /* Main Workspace Content */
        .main-content {
            margin-left: 260px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            position: relative;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-260px);
            }
            .main-content {
                margin-left: 0;
            }
            .sidebar.active {
                transform: translateX(0);
            }
        }

        /* Glass header bar */
        .header-bar {
            height: 70px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 3rem;
            backdrop-filter: blur(10px);
            background: rgba(2, 6, 23, 0.8);
            position: sticky;
            top: 0;
            z-index: 90;
        }

        .page-title {
            font-family: 'Syne', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .action-row {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .logout-btn {
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 0.5rem 1rem;
            color: var(--text-main);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s;
            cursor: pointer;
        }

        .logout-btn:hover {
            background-color: var(--danger);
            border-color: var(--danger);
        }

        /* Inner Page container */
        .container {
            padding: 48px 3rem;
            flex: 1;
            animation: fadeIn 0.4s ease-out;
            overflow-x: hidden;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Custom widgets UI styling */
        .glass-card {
            background-color: var(--card-glass);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem;
            backdrop-filter: blur(12px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            margin-bottom: 2rem;
        }

        .btn-accent {
            background-color: var(--accent);
            color: #FFF;
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-accent:hover {
            background-color: var(--accent-hover);
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            margin-top: 1rem;
        }

        .custom-table th {
            padding: 1rem;
            border-bottom: 2px solid var(--border);
            color: var(--text-muted);
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        .custom-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
            font-size: 0.95rem;
            vertical-align: middle;
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.6rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-success { background: rgba(16, 185, 129, 0.1); color: var(--success); }
        .badge-danger { background: rgba(239, 68, 68, 0.1); color: var(--danger); }
        .badge-warning { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
        
        /* Form stylings */
        .form-input {
            width: 100%;
            background-color: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            color: var(--text-main);
            outline: none;
            transition: all 0.2s;
        }

        .form-input:focus {
            border-color: var(--accent);
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }

        @media (max-width: 991px) {
            .grid-3 {
                grid-template-columns: 1fr;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <div>
                <a href="#" class="sidebar-logo">
                    <span class="brand-logo-icon"></span>
                    SchoolCloud <span>ERP</span>
                </a>
                <ul class="nav-links">
                    @if(auth()->check() && auth()->user()->hasRole('superadmin'))
                        <li class="nav-item {{ request()->is('superadmin*') ? 'active' : '' }}">
                            <a href="/superadmin/dashboard"><i class="fa fa-gauge"></i> Dashboard</a>
                        </li>
                    @elseif(auth()->check() && (auth()->user()->hasRole('school_admin') || auth()->user()->hasRole('teacher')))
                        <li class="nav-item {{ request()->is('school/dashboard') ? 'active' : '' }}">
                            <a href="/school/dashboard"><i class="fa fa-gauge"></i> Dashboard</a>
                        </li>
                        <li class="nav-item {{ request()->is('school/students*') ? 'active' : '' }}">
                            <a href="/school/students"><i class="fa fa-graduation-cap"></i> Students</a>
                        </li>
                        <li class="nav-item {{ request()->is('school/attendance/students*') ? 'active' : '' }}">
                            <a href="/school/attendance/students"><i class="fa fa-calendar-check"></i> Student Attendance</a>
                        </li>
                        <li class="nav-item {{ request()->is('school/attendance/staff*') ? 'active' : '' }}">
                            <a href="/school/attendance/staff"><i class="fa fa-user-clock"></i> Staff Attendance</a>
                        </li>
                    @elseif(auth()->check() && (auth()->user()->hasRole('parent') || auth()->user()->hasRole('student')))
                        <li class="nav-item {{ request()->is('parent/dashboard') ? 'active' : '' }}">
                            <a href="/parent/dashboard"><i class="fa fa-gauge"></i> Dashboard</a>
                        </li>
                        <li class="nav-item {{ request()->is('parent/attendance*') ? 'active' : '' }}">
                            <a href="/parent/attendance"><i class="fa fa-calendar-check"></i> Attendance</a>
                        </li>
                    @endif
                </ul>
            </div>

            <!-- User Status Info Block -->
            @if(auth()->check())
                <div class="user-block">
                    <div class="user-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="user-info">
                        <span class="user-name">{{ auth()->user()->name }}</span>
                        <span class="user-role">{{ ucfirst(auth()->user()->roles->first()?->name ?? 'User') }}</span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Main Area -->
        <div class="main-content">
            <!-- Glass Header -->
            <div class="header-bar">
                <div class="page-title">@yield('title', 'Control Panel')</div>
                <div class="action-row">
                    @if(app()->bound('currentSchool'))
                        <span style="font-weight: 700; color: var(--accent);"><i class="fa fa-school"></i> {{ app('currentSchool')->name }}</span>
                    @endif
                    <a href="{{ route('logout') }}" class="logout-btn"><i class="fa fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>

            <!-- Page Inner container -->
            <div class="container">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- jQuery for AJAX and micro-interactions -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Set up jQuery AJAX headers globally with the CSRF token from the layout meta tag
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
