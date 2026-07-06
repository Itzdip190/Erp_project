<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom-0 px-4">
    <!-- Left navbar links -->
    <ul class="navbar-nav align-items-center" style="display: flex; flex-direction: row; align-items: center; list-style: none; padding-left: 0;">
        <li class="nav-item">
            <a class="nav-link nav-collapse-btn" data-widget="pushmenu" href="#" role="button" style="padding: 0;">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item ml-3">
            <div class="user-greeting-container">
                @php
                    $hour = now()->hour;
                    $greeting = 'Good Morning';
                    if ($hour >= 12 && $hour < 17) {
                        $greeting = 'Good Afternoon';
                    } elseif ($hour >= 17) {
                        $greeting = 'Good Evening';
                    }
                @endphp
                <h4 class="mb-0 user-greeting-text d-none d-sm-block" style="font-size: 1.1rem; font-weight: 800;">
                    {{ $greeting }}, {{ auth()->user()->name }}! 👋
                </h4>
                <p class="mb-0 user-greeting-sub d-none d-md-block" style="font-size: 0.78rem;">
                    Here's what's happening across all schools today.
                </p>
            </div>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto align-items-center" style="display: flex; flex-direction: row; align-items: center; list-style: none; gap: 15px; margin-bottom: 0;">
        <!-- Date Selector Filter (Functional) -->
        <li class="nav-item d-none d-md-block dropdown">
            <div class="navbar-date-selector dropdown-toggle" data-toggle="dropdown" role="button" id="navbarDateRangeSelector">
                <i class="far fa-calendar mr-2 text-muted"></i>
                <span>
                    @if(request('date_filter') === 'last_month')
                        Last Month
                    @elseif(request('date_filter') === 'this_year')
                        This Year
                    @elseif(request('date_filter') === 'all_time')
                        All Time
                    @else
                        {{ now()->startOfMonth()->format('M d') }} &ndash; {{ now()->format('M d, Y') }}
                    @endif
                </span>
            </div>
            <div class="dropdown-menu dropdown-menu-right dropdown-menu-custom" aria-labelledby="navbarDateRangeSelector">
                <a href="{{ request()->fullUrlWithQuery(['date_filter' => 'this_month']) }}" class="dropdown-item">This Month</a>
                <a href="{{ request()->fullUrlWithQuery(['date_filter' => 'last_month']) }}" class="dropdown-item">Last Month</a>
                <a href="{{ request()->fullUrlWithQuery(['date_filter' => 'this_year']) }}" class="dropdown-item">This Year</a>
                <a href="{{ request()->fullUrlWithQuery(['date_filter' => 'all_time']) }}" class="dropdown-item">All Time</a>
            </div>
        </li>

        <!-- Export Report Button (Functional) -->
        <li class="nav-item d-none d-md-block">
            <a href="{{ route('superadmin.dashboard.export-report') }}" class="btn btn-indigo-navbar">
                <i class="fas fa-download mr-2"></i>
                <span>Export Report</span>
            </a>
        </li>

        <!-- Dark Mode Toggle Button -->
        <li class="nav-item">
            <a class="nav-link navbar-notification-bell" href="javascript:void(0)" onclick="toggleTheme()" id="superadminThemeToggleBtn" title="Toggle Dark/Light Mode" style="padding: 0; cursor: pointer; display: flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 50%; background: #f1f5f9; color: #475569;">
                <i class="fas fa-moon" id="superadminThemeToggleIcon"></i>
            </a>
        </li>

        <!-- Notifications Dropdown Menu (Functional & Dynamic) -->
        <li class="nav-item dropdown">
            <a class="nav-link navbar-notification-bell" data-toggle="dropdown" href="#" style="padding: 0; cursor: pointer;">
                <i class="far fa-bell"></i>
                @if(isset($superadminNotifications) && count($superadminNotifications) > 0)
                    <span class="badge badge-danger navbar-badge-red">{{ count($superadminNotifications) }}</span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right dropdown-menu-custom">
                <span class="dropdown-item dropdown-header">{{ isset($superadminNotifications) ? count($superadminNotifications) : 0 }} Notifications</span>
                <div class="dropdown-divider"></div>
                @if(isset($superadminNotifications))
                    @foreach($superadminNotifications as $notif)
                        <a href="{{ $notif['url'] }}" class="dropdown-item">
                            <i class="{{ $notif['icon'] }} mr-2"></i>
                            <span class="text-wrap">{{ $notif['text'] }}</span>
                            <span class="float-right text-muted text-sm">{{ $notif['time'] }}</span>
                        </a>
                        <div class="dropdown-divider"></div>
                    @endforeach
                @endif
                <a href="#" class="dropdown-item text-center dropdown-footer">See All Notifications</a>
            </div>
        </li>

        <!-- User Profile Dropdown (Functional with Profile & Settings pages) -->
        <li class="nav-item dropdown">
            <a class="nav-link p-0 d-flex align-items-center" data-toggle="dropdown" href="#" style="display: flex; align-items: center; text-decoration: none; cursor: pointer;">
                <img src="{{ auth()->user()->photo ? asset(auth()->user()->photo) : 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80' }}" class="user-avatar-navbar" alt="User Image">
                <div class="user-profile-meta d-none d-lg-block ml-2 text-left" style="text-align: left;">
                    <span class="user-profile-name" style="display: block;">{{ auth()->user()->name }}</span>
                    <span class="user-profile-role" style="display: block;">{{ auth()->user()->role ?? 'Super Admin' }}</span>
                </div>
                <i class="fas fa-chevron-down text-muted d-none d-lg-block ml-2" style="font-size: 0.75rem;"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-md dropdown-menu-right dropdown-menu-custom" style="min-width: 180px;">
                <a href="{{ route('superadmin.profile.index') }}" class="dropdown-item">
                    <i class="fas fa-user mr-2 text-muted"></i> Profile
                </a>
                <a href="{{ route('superadmin.settings') }}" class="dropdown-item">
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
