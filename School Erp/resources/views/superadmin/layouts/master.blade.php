<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SchoolCloud ERP') }} - SuperAdmin Dashboard</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">

    <!-- FontAwesome Free -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <!-- OverlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- AdminLTE 3.x CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">

    <!-- Premium Theme Overrides (EduManage Pro Colors & Design) -->
    <style>
        /* Typography & Core Styles */
        body, h1, h2, h3, h4, h5, h6, .brand-text-main, .card-title-custom, .font-heading, .nav-sidebar .nav-link, .brand-text-sub, .user-greeting-text, .user-profile-name, .user-profile-role {
            font-family: 'Lato', sans-serif !important;
        }

        body {
            background-color: #faf8f5;
            color: #1e1b4b;
        }

        h1, h2, h3, h4, h5, h6, .font-heading {
            font-weight: 700;
            color: #1e1b4b;
        }

        .content-wrapper {
            background-color: #faf8f5 !important;
            padding: 1.5rem;
        }

        /* Sidebar Styling Overrides */
        .main-sidebar {
            background-color: #0c1024 !important;
            width: 270px !important;
            display: flex;
            flex-direction: column;
            border-right: none !important;
        }

        .layout-fixed .main-sidebar {
            height: 100vh;
        }

        .sidebar {
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .brand-link {
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            background-color: #0c1024 !important;
            padding: 1.25rem 1.25rem !important;
            text-decoration: none !important;
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
        }

        .brand-logo-icon-gold {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #e5ba73, #c59b27);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0c1024 !important;
            font-size: 1.2rem;
            box-shadow: 0 4px 10px rgba(229, 186, 115, 0.3);
            flex-shrink: 0 !important;
        }

        .brand-logo-icon-gold i {
            color: #0c1024 !important;
        }

        .brand-text-wrapper {
            display: flex;
            flex-direction: column;
            flex-grow: 1 !important;
        }

        .brand-text-main {
            font-family: 'Lato', sans-serif !important;
            font-weight: 800;
            color: #ffffff;
            font-size: 1.15rem;
            line-height: 1.2;
        }

        .brand-text-sub {
            font-size: 0.72rem;
            color: #e5ba73;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-top: 1px;
        }

        /* Sidebar Tenant Panel */
        .tenant-panel {
            background: #171b30;
            border-radius: 14px;
            padding: 12px;
            display: flex;
            align-items: center;
            border: 1px solid rgba(255, 255, 255, 0.04);
            gap: 12px;
        }

        .tenant-logo-circle {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e5ba73;
            font-size: 1.15rem;
            flex-shrink: 0;
        }

        .tenant-info {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            min-width: 0;
        }

        .tenant-name {
            font-size: 0.85rem;
            font-weight: 700;
            color: #ffffff;
        }

        .tenant-session {
            font-size: 0.72rem;
            color: #94a3b8;
        }

        .tenant-badge {
            background: rgba(229, 186, 115, 0.12);
            color: #e5ba73;
            border-radius: 6px;
            padding: 2px 6px;
            font-size: 0.68rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            align-self: flex-start;
        }

        .text-gold {
            color: #e5ba73 !important;
        }

        /* Nav menu items in Sidebar */
        .nav-sidebar .nav-header {
            color: #5e6a82 !important;
            font-size: 0.72rem !important;
            font-weight: 800 !important;
            letter-spacing: 1.2px !important;
            padding: 1.25rem 0.5rem 0.4rem 0.5rem !important;
        }

        .nav-sidebar .nav-link {
            color: #94a3b8 !important;
            font-size: 0.88rem !important;
            font-weight: 500 !important;
            padding: 8px 12px !important;
            margin-bottom: 2px !important;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
        }

        .nav-sidebar .nav-link i {
            font-size: 1rem !important;
            margin-right: 10px !important;
            width: 20px;
            text-align: center;
        }

        .nav-sidebar .nav-link.active {
            background: linear-gradient(135deg, #e5ba73, #c59b27) !important;
            color: #0c1024 !important;
            font-weight: 600 !important;
            border-radius: 12px !important;
            box-shadow: 0 4px 15px rgba(229, 186, 115, 0.2) !important;
        }

        .nav-sidebar .nav-link.active i {
            color: #0c1024 !important;
        }

        .nav-sidebar .nav-link:hover:not(.active) {
            background-color: rgba(255, 255, 255, 0.04) !important;
            color: #ffffff !important;
            border-radius: 12px !important;
        }

        /* Sidebar bottom support card */
        .sidebar-help-card {
            background: #171b30;
            border-radius: 16px;
            padding: 16px;
            border: 1px solid rgba(255, 255, 255, 0.04);
            margin-top: auto !important; /* Pushes support card to bottom */
        }

        .help-icon-circle {
            width: 28px;
            height: 28px;
            background: rgba(229, 186, 115, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e5ba73;
            font-size: 0.85rem;
        }

        .help-title {
            color: #ffffff;
            font-weight: 700;
            font-size: 0.85rem;
        }

        .help-text {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-bottom: 0.8rem;
            line-height: 1.4;
        }

        .btn-gold-sidebar {
            background: linear-gradient(135deg, #e5ba73, #c59b27);
            color: #0c1024 !important;
            border: none;
            font-weight: 700;
            border-radius: 10px;
            font-size: 0.78rem;
            padding: 6px 12px;
            transition: all 0.2s;
        }

        .btn-gold-sidebar:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(229, 186, 115, 0.35);
        }

        /* Logout button at bottom */
        .sidebar-logout-wrapper {
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }

        .btn-sidebar-logout {
            display: flex;
            align-items: center;
            color: #94a3b8;
            text-decoration: none !important;
            font-size: 0.88rem;
            font-weight: 600;
            padding: 8px 12px;
            transition: color 0.2s;
        }

        .btn-sidebar-logout:hover {
            color: #fca5a5;
        }

        /* Navbar Custom Styling */
        .main-header {
            background-color: #faf8f5 !important;
            height: 75px;
            display: flex;
            align-items: center;
            padding: 0 1.5rem !important;
        }

        .nav-collapse-btn {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #1d193d, #2f2960) !important;
            border: none !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center;
            justify-content: center;
            color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(29, 25, 61, 0.2) !important;
            transition: all 0.2s;
            padding: 0 !important;
        }

        .user-greeting-text {
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #0c1024;
        }

        .user-greeting-sub {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 500;
        }

        .navbar-date-selector {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 10px 18px;
            font-size: 0.85rem;
            font-weight: 700;
            color: #1e1b4b;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.015);
        }

        .btn-indigo-navbar {
            background: linear-gradient(135deg, #1d193d, #2f2960) !important;
            border: none !important;
            color: #ffffff !important;
            border-radius: 14px !important;
            font-weight: 700 !important;
            font-size: 0.85rem !important;
            padding: 10px 20px !important;
            display: flex !important;
            align-items: center;
            gap: 8px !important;
            box-shadow: 0 4px 12px rgba(29, 25, 61, 0.15);
            transition: all 0.2s;
        }

        .btn-indigo-navbar:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(29, 25, 61, 0.25);
        }

        .navbar-notification-bell {
            width: 44px;
            height: 44px;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 14px !important;
            display: flex !important;
            align-items: center;
            justify-content: center;
            color: #1e1b4b !important;
            position: relative;
            box-shadow: 0 2px 5px rgba(0,0,0,0.015);
            padding: 0 !important;
        }

        .navbar-badge-red {
            position: absolute;
            top: -5px !important;
            right: -5px !important;
            background-color: #ef4444 !important;
            color: #fff !important;
            border: 2px solid #ffffff !important;
            border-radius: 50% !important;
            width: 20px !important;
            height: 20px !important;
            font-size: 0.65rem !important;
            display: flex !important;
            align-items: center;
            justify-content: center;
            font-weight: 700 !important;
            padding: 0 !important;
        }

        .user-avatar-navbar {
            width: 44px;
            height: 44px;
            border-radius: 50% !important;
            object-fit: cover;
            border: 2px solid #ffffff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        .user-profile-meta {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
            margin-left: 8px;
        }

        .user-profile-name {
            font-size: 0.9rem;
            font-weight: 700;
            color: #1e1b4b;
        }

        .user-profile-role {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 600;
        }

        .dropdown-menu-custom {
            border: 1px solid #e5e7eb !important;
            border-radius: 16px !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08) !important;
            padding: 8px 0 !important;
            margin-top: 10px !important;
            overflow: hidden;
        }

        .dropdown-menu-custom .dropdown-item {
            font-size: 0.85rem !important;
            padding: 10px 16px !important;
            color: #4b5563 !important;
        }

        .dropdown-menu-custom .dropdown-item:hover {
            background-color: #faf8f5 !important;
            color: #1e1b4b !important;
        }

        /* Custom Sidebar Width (270px) and Collapse Animations */
        .main-sidebar {
            width: 270px !important;
            transition: margin-left .3s ease-in-out, transform .3s ease-in-out, width .3s ease-in-out !important;
        }

        /* Desktop layout overrides */
        @media (min-width: 992px) {
            body:not(.sidebar-collapse) .main-sidebar {
                width: 270px !important;
                margin-left: 0 !important;
            }
            body:not(.sidebar-collapse) .main-header, 
            body:not(.sidebar-collapse) .content-wrapper, 
            body:not(.sidebar-collapse) .main-footer {
                margin-left: 270px !important;
                transition: margin-left .3s ease-in-out !important;
            }
            body.sidebar-collapse .main-sidebar {
                margin-left: -270px !important;
            }
            body.sidebar-collapse .main-header, 
            body.sidebar-collapse .content-wrapper, 
            body.sidebar-collapse .main-footer {
                margin-left: 0 !important;
                transition: margin-left .3s ease-in-out !important;
            }
        }

        /* Mobile/tablet layout overrides (prevents main page from hiding/translating) */
        @media (max-width: 991.98px) {
            .sidebar-open .content-wrapper,
            .sidebar-open .main-footer,
            .sidebar-open .main-header {
                transform: none !important;
                margin-left: 0 !important;
            }
            .main-sidebar {
                margin-left: -270px !important;
            }
            body.sidebar-open .main-sidebar {
                margin-left: 0 !important;
            }
            .content-wrapper, .main-header, .main-footer {
                margin-left: 0 !important;
            }
        }

        /* Cards and premium elements styling */
        .card-custom {
            border-radius: 20px !important;
            border: 1px solid rgba(229, 231, 235, 0.5) !important;
            box-shadow: 0 6px 20px rgba(0,0,0,0.012) !important;
            background-color: #ffffff !important;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .card-header-custom {
            background-color: transparent !important;
            border-bottom: 1px solid rgba(229, 231, 235, 0.5) !important;
            padding: 1.25rem 1.5rem !important;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title-custom {
            font-size: 1.05rem !important;
            font-weight: 700 !important;
            color: #1e1b4b !important;
            margin: 0 !important;
            font-family: 'Syne', sans-serif;
        }

        .card-body-custom {
            padding: 1.5rem !important;
        }
    </style>
    @yield('styles')
</head>
<body class="hold-transition layout-fixed layout-navbar-fixed">
<div class="wrapper">

    <!-- Navbar -->
    @include('superadmin.partials.navbar')

    <!-- Sidebar -->
    @include('superadmin.partials.sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Flash messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px; background-color: #ecfdf5; color: #065f46;">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="close text-success" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @yield('content')
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- OverlayScrollbars -->
<script src="{{ asset('vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
@yield('scripts')
</body>
</html>
