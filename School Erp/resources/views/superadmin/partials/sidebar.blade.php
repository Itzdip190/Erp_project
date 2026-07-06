@php
    $pendingRequestsCount = \App\Models\SchoolRequest::where('status', 'pending')->count();
@endphp
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-0">
    <!-- Brand Logo -->
    <a href="{{ route('superadmin.dashboard') }}" class="brand-link">
        <div class="brand-logo-icon-gold">
            <i class="fas fa-cloud"></i>
        </div>
        <div class="brand-text-wrapper">
            <span class="brand-text-main">SchoolCloud ERP</span>
            <span class="brand-text-sub">SaaS Platform Hub</span>
        </div>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Session / Tenant Panel (Custom Styled to Match 2nd Image) -->
        <div class="tenant-panel my-3 mx-2">
            <div class="tenant-logo-circle">
                <i class="fas fa-server"></i>
            </div>
            <div class="tenant-info">
                <span class="tenant-name text-truncate">SchoolCloud SaaS</span>
                <span class="tenant-session">Version 12.0 &bull; 2026-27</span>
                <div class="tenant-badge mt-1">
                    <i class="fas fa-crown mr-1 text-gold"></i>
                    <span>SuperAdmin Access</span>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
                
                <!-- Group: PLATFORM OVERVIEW -->
                <li class="nav-header">PLATFORM OVERVIEW</li>
                
                <li class="nav-item">
                    <a href="{{ route('superadmin.dashboard') }}" class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="{{ route('superadmin.schools.index') }}" class="nav-link {{ request()->is('superadmin/schools') || (request()->is('superadmin/schools/*') && !request()->is('superadmin/schools/create')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-school"></i>
                        <p>All Schools</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('superadmin.schools.create') }}" class="nav-link {{ request()->routeIs('superadmin.schools.create') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-plus-circle"></i>
                        <p>Add New School</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('superadmin.school-requests.index') }}" class="nav-link {{ request()->routeIs('superadmin.school-requests.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-signature"></i>
                        <p>
                            School Requests
                            @if($pendingRequestsCount > 0)
                                <span class="badge badge-warning right">{{ $pendingRequestsCount }}</span>
                            @endif
                        </p>
                    </a>
                </li>

                <!-- Group: SUBSCRIPTIONS & BILLING -->
                <li class="nav-header">SUBSCRIPTIONS & BILLING</li>
                
                <li class="nav-item">
                    <a href="{{ Route::has('superadmin.plans.index') ? route('superadmin.plans.index') : '#' }}" class="nav-link {{ request()->routeIs('superadmin.plans.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-layer-group"></i>
                        <p>Subscription Plans</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ Route::has('superadmin.subscriptions.index') ? route('superadmin.subscriptions.index') : '#' }}" class="nav-link {{ request()->routeIs('superadmin.subscriptions.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-check-circle"></i>
                        <p>Active Subscriptions</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ Route::has('superadmin.orders.index') ? route('superadmin.orders.index') : '#' }}" class="nav-link {{ request()->routeIs('superadmin.orders.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-receipt"></i>
                        <p>Orders / Payments</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ Route::has('superadmin.gateways.index') ? route('superadmin.gateways.index') : '#' }}" class="nav-link {{ request()->routeIs('superadmin.gateways.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-credit-card"></i>
                        <p>Payment Gateways</p>
                    </a>
                </li>

                <!-- Group: PLATFORM CONFIG -->
                <li class="nav-header">PLATFORM CONFIG</li>

                <li class="nav-item">
                    <a href="{{ Route::has('superadmin.sms-gateways.index') ? route('superadmin.sms-gateways.index') : '#' }}" class="nav-link {{ request()->routeIs('superadmin.sms-gateways.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-sms"></i>
                        <p>SMS Gateways</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ Route::has('superadmin.notification-types.index') ? route('superadmin.notification-types.index') : '#' }}" class="nav-link {{ request()->routeIs('superadmin.notification-types.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-bell"></i>
                        <p>Notification Types</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ Route::has('superadmin.menu-manager.index') ? route('superadmin.menu-manager.index') : '#' }}" class="nav-link {{ request()->routeIs('superadmin.menu-manager.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-list"></i>
                        <p>Menu Manager</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ Route::has('superadmin.blog-cms.index') ? route('superadmin.blog-cms.index') : '#' }}" class="nav-link {{ request()->routeIs('superadmin.blog-cms.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-blog"></i>
                        <p>Blog / CMS</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ Route::has('superadmin.white-label.index') ? route('superadmin.white-label.index') : '#' }}" class="nav-link {{ request()->routeIs('superadmin.white-label.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-paint-brush"></i>
                        <p>White-Label Settings</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ Route::has('superadmin.platform-settings.index') ? route('superadmin.platform-settings.index') : '#' }}" class="nav-link {{ request()->routeIs('superadmin.platform-settings.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>Platform Settings</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('superadmin.profile.index') }}" class="nav-link {{ request()->routeIs('superadmin.profile.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-circle"></i>
                        <p>My Profile</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('superadmin.settings') }}" class="nav-link {{ request()->routeIs('superadmin.settings') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-sliders-h"></i>
                        <p>My Preferences</p>
                    </a>
                </li>

                <!-- Group: MONITORING -->
                <li class="nav-header">MONITORING</li>

                <li class="nav-item">
                    <a href="{{ route('superadmin.ai.index') }}" class="nav-link {{ request()->is('superadmin/ai') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-brain"></i>
                        <p>AI Analytics</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ Route::has('superadmin.audit-logs') ? route('superadmin.audit-logs') : '#' }}" class="nav-link {{ request()->routeIs('superadmin.audit-logs') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-history"></i>
                        <p>Audit Logs</p>
                    </a>
                </li>

                <!-- Group: AI INTELLIGENCE -->
                <li class="nav-header" style="background:linear-gradient(90deg,rgba(99,102,241,0.15),transparent);color:#818cf8;font-weight:800;letter-spacing:1px;">AI INTELLIGENCE</li>

                <li class="nav-item">
                    <a href="{{ route('superadmin.ai.index') }}" class="nav-link {{ request()->is('superadmin/ai') && !request()->is('superadmin/ai/chat') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-microchip" style="color:#818cf8;"></i>
                        <p>AI Overview</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('superadmin.ai.chat') }}" class="nav-link {{ request()->is('superadmin/ai/chat*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-comments" style="color:#10b981;"></i>
                        <p>AI Chat</p>
                    </a>
                </li>

            </ul>
        </nav>

        
        <!-- Sidebar Bottom Help Support Section -->
        <div class="sidebar-help-card my-4 mx-2">
            <div class="d-flex align-items-center gap-2 mb-2">
                <div class="help-icon-circle">
                    <i class="fas fa-headset text-white"></i>
                </div>
                <span class="help-title">Need Help?</span>
            </div>
            <p class="help-text">We're here to support you at any stage.</p>
            <a href="#" class="btn btn-gold-sidebar w-100 btn-sm">Contact Support</a>
        </div>

        <!-- Logout Section -->
        <div class="sidebar-logout-wrapper px-2 py-3">
            <a href="{{ route('logout') }}" class="btn-sidebar-logout w-100">
                <i class="fas fa-sign-out-alt mr-2"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
</aside>
