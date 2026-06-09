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
                    <a href="{{ route('superadmin.dashboard') }}" class="nav-link active">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-school"></i>
                        <p>All Schools</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('superadmin.schools.create') }}" class="nav-link">
                        <i class="nav-icon fas fa-plus-circle"></i>
                        <p>Add New School</p>
                    </a>
                </li>

                <!-- Group: SUBSCRIPTIONS & BILLING -->
                <li class="nav-header">SUBSCRIPTIONS & BILLING</li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-layer-group"></i>
                        <p>Subscription Plans</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-check-circle"></i>
                        <p>Active Subscriptions</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-receipt"></i>
                        <p>Orders / Payments</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-credit-card"></i>
                        <p>Payment Gateways</p>
                    </a>
                </li>

                <!-- Group: PLATFORM CONFIG -->
                <li class="nav-header">PLATFORM CONFIG</li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-sms"></i>
                        <p>SMS Gateways</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-bell"></i>
                        <p>Notification Types</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-list"></i>
                        <p>Menu Manager</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-blog"></i>
                        <p>Blog / CMS</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-paint-brush"></i>
                        <p>White-Label Settings</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>Platform Settings</p>
                    </a>
                </li>

                <!-- Group: MONITORING -->
                <li class="nav-header">MONITORING</li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-server"></i>
                        <p>Server Status</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-brain"></i>
                        <p>AI Analytics</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-clock"></i>
                        <p>Cron Monitoring</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-history"></i>
                        <p>Audit Logs</p>
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
