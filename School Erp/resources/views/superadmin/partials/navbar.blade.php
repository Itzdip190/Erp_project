<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom-0 px-4">
    <!-- Left navbar links -->
    <ul class="navbar-nav align-items-center" style="display: flex; flex-direction: row; align-items: center; list-style: none; padding-left: 0;">
        <li class="nav-item">
            <a class="nav-link nav-collapse-btn" data-widget="pushmenu" href="#" role="button" style="padding: 0;">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block ml-3">
            <div class="user-greeting-container">
                <h4 class="mb-0 user-greeting-text">Good Morning, Admin! 👋</h4>
                <p class="mb-0 user-greeting-sub">Here's what's happening across all schools today.</p>
            </div>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto align-items-center" style="display: flex; flex-direction: row; align-items: center; list-style: none; gap: 15px; margin-bottom: 0;">
        <!-- Date Selector Filter -->
        <li class="nav-item d-none d-md-block">
            <div class="navbar-date-selector">
                <i class="far fa-calendar mr-2 text-muted"></i>
                <span>{{ now()->startOfMonth()->format('M d') }} &ndash; {{ now()->format('M d, Y') }}</span>
                <i class="fas fa-chevron-down ml-2 text-muted" style="font-size: 0.8rem;"></i>
            </div>
        </li>

        <!-- Export Report Button -->
        <li class="nav-item d-none d-md-block">
            <button class="btn btn-indigo-navbar">
                <i class="fas fa-download mr-2"></i>
                <span>Export Report</span>
            </button>
        </li>

        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link navbar-notification-bell" data-toggle="dropdown" href="#" style="padding: 0;">
                <i class="far fa-bell"></i>
                <span class="badge badge-danger navbar-badge-red">3</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right dropdown-menu-custom">
                <span class="dropdown-item dropdown-header">3 Notifications</span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-exclamation-triangle mr-2 text-warning"></i> 1 School Expiring Soon
                    <span class="float-right text-muted text-sm">3 mins</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-school mr-2 text-success"></i> Greenwood registered
                    <span class="float-right text-muted text-sm">2 hrs</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item text-center dropdown-footer">See All Notifications</a>
            </div>
        </li>

        <!-- User Profile Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link p-0 d-flex align-items-center" data-toggle="dropdown" href="#" style="display: flex; align-items: center; text-decoration: none;">
                <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" class="user-avatar-navbar" alt="User Image">
                <div class="user-profile-meta d-none d-lg-block ml-2 text-left" style="text-align: left;">
                    <span class="user-profile-name" style="display: block;">Admin</span>
                    <span class="user-profile-role" style="display: block;">Super Admin</span>
                </div>
                <i class="fas fa-chevron-down text-muted d-none d-lg-block ml-2" style="font-size: 0.75rem;"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-md dropdown-menu-right dropdown-menu-custom">
                <a href="#" class="dropdown-item">
                    <i class="fas fa-user mr-2 text-muted"></i> Profile
                </a>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-sliders-h mr-2 text-muted"></i> Settings
                </a>
                <div class="dropdown-divider"></div>
                <a href="{{ route('logout') }}" class="dropdown-item text-danger">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
            </div>
        </li>
    </ul>
</nav>
<!-- /.navbar -->
