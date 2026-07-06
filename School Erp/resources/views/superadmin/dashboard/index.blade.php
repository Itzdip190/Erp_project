@extends('superadmin.layouts.master')

@section('styles')
<style>
    /* Stats Cards Styles (Original Theme) */
    .stat-card {
        border-radius: 16px !important;
        border: 1px solid rgba(229, 231, 235, 0.5) !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.01) !important;
        background-color: #ffffff;
        transition: transform 0.2s, box-shadow 0.2s;
        overflow: hidden;
        position: relative;
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.02) !important;
    }

    .stat-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    /* Accent colors from design */
    .bg-light-blue { background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .bg-light-green { background-color: rgba(16, 185, 129, 0.1); color: #10b981; }
    .bg-light-teal { background-color: rgba(6, 182, 212, 0.1); color: #06b6d4; }
    .bg-light-orange { background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .bg-light-yellow { background-color: rgba(234, 179, 8, 0.1); color: #eab308; }
    .bg-light-red { background-color: rgba(239, 68, 68, 0.1); color: #ef4444; }
    .bg-light-purple { background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6; }

    .stat-label {
        font-size: 0.85rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 800;
        color: #1e1b4b;
        letter-spacing: -0.5px;
    }

    .stat-trend {
        font-size: 0.78rem;
        font-weight: 600;
    }

    .trend-up { color: #10b981; }
    .trend-down { color: #ef4444; }

    /* SVG Sparkline Sparkle */
    .sparkline-container {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 35px;
    }

    /* Tables styling */
    .table-custom th {
        font-size: 0.72rem;
        font-weight: 800;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.8px;
        border-top: none !important;
        border-bottom: 2px solid #f3f4f6 !important;
        padding: 12px 16px !important;
    }

    .table-custom td {
        font-size: 0.88rem;
        color: #1e1b4b;
        vertical-align: middle !important;
        padding: 14px 16px !important;
        border-bottom: 1px solid #f3f4f6 !important;
        border-top: none !important;
    }

    .school-name-td {
        font-weight: 700;
        color: #1e1b4b;
    }

    .badge-premium-trial {
        background-color: rgba(139, 92, 246, 0.12);
        color: #8b5cf6;
        font-weight: 700;
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 8px;
    }

    .badge-status-active {
        background-color: #ecfdf5;
        color: #10b981;
        font-weight: 700;
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 8px;
    }

    .badge-status-suspended {
        background-color: #fef2f2;
        color: #ef4444;
        font-weight: 700;
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 8px;
    }

    .badge-status-trial {
        background-color: #fef9c3;
        color: #ca8a04;
        font-weight: 700;
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 8px;
    }

    .btn-view-action {
        background-color: #f3f4f6;
        border: none;
        color: #4b5563;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 8px;
        transition: all 0.2s;
    }

    .btn-view-action:hover {
        background-color: #e5e7eb;
        color: #1e1b4b;
    }

    /* Quick Actions */
    .quick-action-card {
        background: #ffffff;
        border: 1px solid rgba(229, 231, 235, 0.5);
        border-radius: 16px;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        text-decoration: none !important;
        transition: all 0.2s;
        box-shadow: 0 4px 15px rgba(0,0,0,0.005);
    }

    .quick-action-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.025);
        border-color: rgba(229, 186, 115, 0.3);
    }

    .quick-action-icon-circle {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        margin-bottom: 0.75rem;
    }

    .quick-action-title {
        font-size: 0.85rem;
        font-weight: 700;
        color: #1e1b4b;
    }

    /* Bottom Promotion Banner */
    .bottom-promo-banner {
        background: linear-gradient(135deg, #161329 0%, #0d0c18 100%);
        border-radius: 20px;
        padding: 1.8rem 2.2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 1.5rem;
        border: 1px solid rgba(255, 255, 255, 0.04);
        position: relative;
        overflow: hidden;
    }

    .bottom-promo-banner::before {
        content: "";
        position: absolute;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(229, 186, 115, 0.05) 0%, transparent 70%);
        top: -125px;
        right: -100px;
    }

    .promo-content {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .promo-icon-box {
        width: 54px;
        height: 54px;
        background: rgba(229, 186, 115, 0.1);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #e5ba73;
        font-size: 1.4rem;
    }

    .promo-title-main {
        font-family: 'Syne', sans-serif;
        font-size: 1.15rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 0.2rem;
    }

    .promo-text-sub {
        font-size: 0.88rem;
        color: #94a3b8;
        margin-bottom: 0;
    }

    .btn-gold-banner {
        background: linear-gradient(135deg, #e5ba73, #c59b27);
        color: #0c1024 !important;
        border: none;
        font-weight: 700;
        border-radius: 12px;
        font-size: 0.88rem;
        padding: 10px 22px;
        box-shadow: 0 4px 15px rgba(229, 186, 115, 0.25);
        transition: all 0.2s;
        white-space: nowrap;
    }

    .btn-gold-banner:hover {
        transform: scale(1.02);
        box-shadow: 0 6px 20px rgba(229, 186, 115, 0.35);
    }

    /* Live Feed Ticker & Feed Button */
    .header-feed-btn {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 30px;
        padding: 5px 14px;
        font-size: 0.78rem;
        font-weight: 800;
        color: #10b981;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        transition: all 0.2s;
    }

    .header-feed-btn:hover {
        background: #f0fdf4;
        border-color: #bbf7d0;
    }

    .live-dot {
        width: 8px;
        height: 8px;
        background-color: #10b981;
        border-radius: 50%;
        animation: pulse-green 1.5s infinite;
        display: inline-block;
    }

    @keyframes pulse-green {
        0% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(16, 185, 201, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 201, 0); }
        100% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(16, 185, 201, 0); }
    }

    /* Demographics & Timeline List */
    .demographics-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .demographics-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .demographics-item:last-child {
        border-bottom: none;
    }

    .demographics-label {
        font-size: 0.92rem;
        font-weight: 700;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .demographics-value {
        font-size: 0.85rem;
        font-weight: 800;
        color: #0f172a;
        background: #f1f5f9;
        padding: 3px 10px;
        border-radius: 12px;
    }

    .protocol-stream {
        height: 230px;
        overflow-y: auto;
        padding-right: 5px;
    }

    .protocol-log-item {
        border-left: 2px solid #cbd5e1;
        position: relative;
        padding-left: 20px;
        padding-bottom: 14px;
    }

    .protocol-log-item::before {
        content: "";
        width: 10px;
        height: 10px;
        background: #f59e0b;
        border-radius: 50%;
        position: absolute;
        left: -6px;
        top: 5px;
    }

    .protocol-log-item:last-child {
        padding-bottom: 0;
        border-left: 2px solid transparent;
    }

    .protocol-time {
        font-size: 0.75rem;
        font-weight: 700;
        color: #94a3b8;
        margin-bottom: 4px;
    }

    .protocol-desc {
        font-size: 0.85rem;
        font-weight: 700;
        color: #334155;
    }

    /* System Health Progress Bars */
    .health-bar-label {
        font-size: 0.75rem;
        font-weight: 800;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
    }

    .health-progress-wrapper {
        height: 8px;
        border-radius: 10px;
        background: #f1f5f9;
        overflow: hidden;
        margin-bottom: 12px;
    }

    .health-progress-bar {
        height: 100%;
        border-radius: 10px;
    }

    /* Clickable error log lines */
    .error-item-clickable {
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .error-item-clickable:hover {
        background-color: #fff5f5;
    }

    /* Responsive adjustments */
    @media (max-width: 767.98px) {
        .bottom-promo-banner {
            flex-direction: column;
            text-align: center;
            gap: 1.5rem;
            padding: 1.5rem;
        }
        .promo-content {
            flex-direction: column;
        }
        .stat-value {
            font-size: 1.5rem;
        }
    }

    /* Dark Mode overrides for Dashboard */
    body.dark-mode .stat-card {
        background-color: #111827 !important;
        border-color: #1e293b !important;
    }
    body.dark-mode .stat-value {
        color: #f8fafc !important;
    }
    body.dark-mode .stat-label {
        color: #94a3b8 !important;
    }
    body.dark-mode .table-custom th {
        background-color: #0b0f19 !important;
        color: #475569 !important;
        border-bottom-color: #1e293b !important;
    }
    body.dark-mode .table-custom td,
    body.dark-mode .school-name-td {
        color: #cbd5e1 !important;
        border-bottom-color: #1e293b !important;
    }
    body.dark-mode .table-custom tr:hover td {
        background-color: #1a2235 !important;
    }
    body.dark-mode .quick-action-card {
        background-color: #111827 !important;
        border-color: #1e293b !important;
    }
    body.dark-mode .quick-action-title {
        color: #f8fafc !important;
    }
    body.dark-mode .demographics-value {
        background-color: #1f2937 !important;
        color: #cbd5e1 !important;
    }
    body.dark-mode .demographics-label {
        color: #cbd5e1 !important;
    }
    body.dark-mode .demographics-item {
        border-bottom-color: #1e293b !important;
    }
    body.dark-mode .btn-view-action {
        background-color: #1f2937 !important;
        color: #cbd5e1 !important;
    }
    body.dark-mode .btn-view-action:hover {
        background-color: #374151 !important;
        color: #f8fafc !important;
    }
    body.dark-mode .header-feed-btn {
        background-color: #111827 !important;
        border-color: #1e293b !important;
    }
    body.dark-mode .header-feed-btn:hover {
        background-color: #1a2235 !important;
    }
    body.dark-mode .health-progress-wrapper {
        background-color: #1f2937 !important;
    }
    body.dark-mode .health-bar-label {
        color: #cbd5e1 !important;
    }
    body.dark-mode .protocol-log-item {
        border-left-color: #374151 !important;
    }
    body.dark-mode .protocol-desc {
        color: #cbd5e1 !important;
    }
    body.dark-mode .error-item-clickable:hover {
        background-color: rgba(239, 68, 68, 0.08) !important;
    }
</style>
@endsection

@section('content')

<!-- Time ticker and dynamic header widgets -->
<div class="row mt-4 mb-2 align-items-center">
    <div class="col-md-6 col-12 mb-2 mb-md-0 text-left">
        <span class="text-muted font-weight-bold" style="font-size: 0.85rem;" id="live-time-ticker">Thursday, Jul 2, 2026 - --:--:-- --</span>
    </div>
    <div class="col-md-6 col-12 text-md-right text-left">
        <button class="header-feed-btn mr-2" data-toggle="modal" data-target="#liveFeedModal">
            <span class="live-dot"></span>
            <span>LIVE SYSTEM FEED</span>
        </button>
        <span class="badge badge-light border font-weight-bold py-2 px-3" style="border-radius: 30px; font-size: 0.78rem;">
            <i class="fas fa-users text-primary mr-1"></i> <span id="val-online">{{ $onlineCount }}</span> Online
        </span>
    </div>
</div>

<!-- Row 1: High-level Metrics Stats -->
<div class="row">
    <!-- Card 1: Total Schools -->
    <div class="col-xl-3 col-md-6 col-12 mb-4">
        <div class="card stat-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon-wrapper bg-light-blue">
                        <i class="fas fa-school"></i>
                    </div>
                    <span class="stat-trend trend-up">
                        <i class="fas fa-arrow-up me-1"></i>
                        <span>{{ number_format($schoolChange, 1) }}%</span>
                    </span>
                </div>
                <div class="stat-label">Total Schools</div>
                <div class="stat-value" id="val-schools">{{ $totalSchools }}</div>
            </div>
            <!-- Sparkline SVG -->
            <div class="sparkline-container">
                <svg viewBox="0 0 100 30" width="100%" height="30" preserveAspectRatio="none">
                    <path d="M 0 25 Q 15 15, 30 22 T 60 10 T 80 18 T 100 5" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round"></path>
                    <path d="M 0 25 Q 15 15, 30 22 T 60 10 T 80 18 T 100 5 L 100 30 L 0 30 Z" fill="rgba(59, 130, 246, 0.05)"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Card 2: Active Subscriptions -->
    <div class="col-xl-3 col-md-6 col-12 mb-4">
        <div class="card stat-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon-wrapper bg-light-green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <span class="stat-trend trend-up">
                        <i class="fas fa-arrow-up me-1"></i>
                        <span>8.4%</span>
                    </span>
                </div>
                <div class="stat-label">Active Subs</div>
                <div class="stat-value" id="val-subs">{{ $activeSubscriptions }}</div>
            </div>
            <!-- Sparkline SVG -->
            <div class="sparkline-container">
                <svg viewBox="0 0 100 30" width="100%" height="30" preserveAspectRatio="none">
                    <path d="M 0 20 Q 20 8, 40 15 T 70 5 T 100 10" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round"></path>
                    <path d="M 0 20 Q 20 8, 40 15 T 70 5 T 100 10 L 100 30 L 0 30 Z" fill="rgba(16, 185, 129, 0.05)"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Card 3: Total Students -->
    <div class="col-xl-3 col-md-6 col-12 mb-4">
        <div class="card stat-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon-wrapper bg-light-teal">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <span class="stat-trend trend-up">
                        <i class="fas fa-arrow-up me-1"></i>
                        <span>3.2%</span>
                    </span>
                </div>
                <div class="stat-label">Total Students</div>
                <div class="stat-value" id="val-students">{{ number_format($totalStudents) }}</div>
            </div>
            <!-- Sparkline SVG -->
            <div class="sparkline-container">
                <svg viewBox="0 0 100 30" width="100%" height="30" preserveAspectRatio="none">
                    <path d="M 0 28 Q 15 22, 35 25 T 65 12 T 85 8 T 100 15" fill="none" stroke="#06b6d4" stroke-width="2" stroke-linecap="round"></path>
                    <path d="M 0 28 Q 15 22, 35 25 T 65 12 T 85 8 T 100 15 L 100 30 L 0 30 Z" fill="rgba(6, 182, 212, 0.05)"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Card 4: MRR Revenue -->
    <div class="col-xl-3 col-md-6 col-12 mb-4">
        <div class="card stat-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon-wrapper bg-light-orange">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <span class="stat-trend trend-up">
                        <i class="fas fa-arrow-up me-1"></i>
                        <span>15.3%</span>
                    </span>
                </div>
                <div class="stat-label">MRR Revenue</div>
                <div class="stat-value" id="val-revenue">{{ $formattedMrr }}</div>
            </div>
            <!-- Sparkline SVG -->
            <div class="sparkline-container">
                <svg viewBox="0 0 100 30" width="100%" height="30" preserveAspectRatio="none">
                    <path d="M 0 22 C 20 28, 40 10, 60 15 C 80 20, 90 2, 100 5" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round"></path>
                    <path d="M 0 22 C 20 28, 40 10, 60 15 C 80 20, 90 2, 100 5 L 100 30 L 0 30 Z" fill="rgba(245, 158, 11, 0.05)"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Row 2: Secondary Status Cards -->
<div class="row">
    <!-- Card 5: Expiring Soon -->
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div class="stat-icon-wrapper bg-light-yellow">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <div class="stat-label text-truncate">Expiring Soon (&le; 7 days)</div>
                    <div class="stat-value">{{ $expiringSoonCount }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 6: Suspended Schools -->
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div class="stat-icon-wrapper bg-light-red">
                    <i class="fas fa-ban"></i>
                </div>
                <div>
                    <div class="stat-label text-truncate">Suspended Schools</div>
                    <div class="stat-value">{{ $suspendedSchools }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 7: New Schools This Month -->
    <div class="col-lg-4 col-md-12 mb-4">
        <div class="card stat-card">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div class="stat-icon-wrapper bg-light-purple">
                    <i class="fas fa-plus"></i>
                </div>
                <div>
                    <div class="stat-label text-truncate">New Schools This Month</div>
                    <div class="stat-value">{{ $newSchoolsThisMonth }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 3: Charts Section -->
<div class="row">
    <!-- Line Chart: Monthly School Registrations -->
    <div class="col-lg-8 mb-4">
        <div class="card card-custom h-100">
            <div class="card-header-custom">
                <h5 class="card-title-custom">Monthly School Registrations</h5>
                <select class="chart-filter-select">
                    <option>Last 12 Months</option>
                    <option>Last 6 Months</option>
                </select>
            </div>
            <div class="card-body-custom">
                <div style="height: 300px; width: 100%; position: relative;">
                    <canvas id="schoolRegistrationsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Doughnut Chart: Plan Distribution -->
    <div class="col-lg-4 mb-4">
        <div class="card card-custom h-100">
            <div class="card-header-custom">
                <h5 class="card-title-custom">Subscription Plans</h5>
                <span class="badge" style="background-color: #ecfdf5; color: #10b981; font-weight: 700; font-size: 0.72rem; padding: 4px 8px; border-radius: 6px;">Active Share</span>
            </div>
            <div class="card-body-custom d-flex flex-column justify-content-center">
                <div style="height: 220px; width: 100%; position: relative;" class="mb-3">
                    <canvas id="plansDistributionChart"></canvas>
                </div>
                <!-- Custom Legends styled nicely -->
                <div class="d-flex justify-content-center gap-3 mt-2" style="font-size: 0.8rem; font-weight: 600;">
                    @foreach($planLabels as $index => $label)
                        <div class="d-flex align-items-center gap-1">
                            <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background-color: {{ ['#3b82f6', '#10b981', '#f59e0b'][$index % 3] }}"></span>
                            <span class="text-muted">{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 4: Tables Section -->
<div class="row">
    <!-- Left Table: Recent Schools -->
    <div class="col-xl-7 col-12 mb-4">
        <div class="card card-custom h-100">
            <div class="card-header-custom">
                <h5 class="card-title-custom">Recent Schools Registered</h5>
                <a href="{{ route('superadmin.schools.index') }}" style="font-size: 0.85rem; font-weight: 700; color: #e5ba73; text-decoration: none;">View All</a>
            </div>
            <div class="card-body-custom p-0">
                <div class="table-responsive">
                    <table class="table table-custom m-0 table-panel">
                        <thead>
                            <tr>
                                <th>School Name</th>
                                <th>Plan</th>
                                <th>Status</th>
                                <th>Expiry Date</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSchools as $school)
                            @php
                                $sub = $school->subscriptions->first();
                            @endphp
                            <tr id="school-row-{{ $school->id }}">
                                <td class="school-name-td">
                                    {{ $school->name }}
                                    <div class="small text-muted font-weight-normal mt-1">
                                        <i class="far fa-envelope mr-1"></i>{{ $school->admin_email }}
                                    </div>
                                </td>
                                <td>
                                    @if($sub && $sub->plan)
                                        <span class="badge-premium-trial">{{ $sub->plan->name }}</span>
                                    @else
                                        <span class="text-muted">&ndash;</span>
                                    @endif
                                </td>
                                <td>
                                    @if($school->status == 'active')
                                        <span class="badge-status-active">Active</span>
                                    @elseif($school->status == 'suspended')
                                        <span class="badge-status-suspended">Suspended</span>
                                    @else
                                        <span class="badge-status-trial">Trial</span>
                                    @endif
                                </td>
                                <td>
                                    <span id="expiry-label-{{ $school->id }}">
                                        @if($sub && $sub->subscription_ends_at)
                                            {{ $sub->subscription_ends_at->format('M d, Y') }}
                                        @else
                                            <span class="text-muted">&ndash;</span>
                                        @endif
                                    </span>
                                </td>
                                <td class="text-right" style="white-space: nowrap;">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <!-- Quick extend button inline -->
                                        <button class="btn btn-xs btn-outline-success font-weight-bold px-2 py-1 rounded-pill" style="font-size: 0.68rem;" onclick="extendSubscriptionInline({{ $school->id }})" id="extend-btn-{{ $school->id }}">
                                            +30d
                                        </button>
                                        <!-- Impersonate school admin in 1 click -->
                                        <form action="{{ route('superadmin.schools.impersonate', $school->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-xs btn-primary font-weight-bold px-2 py-1 rounded-pill" style="font-size: 0.68rem;" title="Impersonate School Admin">
                                                Impersonate
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No schools registered yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Table: Recent Orders -->
    <div class="col-xl-5 col-12 mb-4">
        <div class="card card-custom h-100">
            <div class="card-header-custom">
                <h5 class="card-title-custom">Recent Subscription Orders</h5>
                <a href="{{ Route::has('superadmin.orders.index') ? route('superadmin.orders.index') : '#' }}" style="font-size: 0.85rem; font-weight: 700; color: #e5ba73; text-decoration: none;">View All</a>
            </div>
            <div class="card-body-custom p-0">
                <div class="table-responsive">
                    <table class="table table-custom m-0">
                        <thead>
                            <tr>
                                <th>School</th>
                                <th>Amount</th>
                                <th>Gateway</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr>
                                <td class="font-weight-bold">{{ $order->school->name ?? 'Deleted School' }}</td>
                                <td>&nbsp;₹{{ number_format($order->amount, 0) }}</td>
                                <td><span class="text-capitalize text-muted" style="font-size: 0.8rem; font-weight: 600;">{{ $order->gateway }}</span></td>
                                <td>
                                    @if($order->status == 'completed')
                                        <span class="badge-status-active">Completed</span>
                                    @elseif($order->status == 'failed')
                                        <span class="badge-status-suspended">Failed</span>
                                    @else
                                        <span class="badge-status-trial">Pending</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No orders processed yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 5: Telemetry and Event Panels (New logic styled in original white-card theme) -->
<div class="row">
    <!-- Demographics Details Panel -->
    <div class="col-xl-4 col-md-6 col-12 mb-4">
        <div class="card card-custom h-100">
            <div class="card-header-custom">
                <h5 class="card-title-custom"><i class="fas fa-chart-pie text-purple mr-1"></i> Platform Demographics</h5>
            </div>
            <div class="card-body-custom">
                <ul class="demographics-list">
                    <li class="demographics-item">
                        <span class="demographics-label"><i class="fas fa-school text-primary"></i> Tenant Schools</span>
                        <span class="demographics-value bg-light-blue">{{ $demographics['schools'] }}</span>
                    </li>
                    <li class="demographics-item">
                        <span class="demographics-label"><i class="fas fa-user-graduate text-success"></i> Registered Students</span>
                        <span class="demographics-value bg-light-green">{{ number_format($demographics['students']) }}</span>
                    </li>
                    <li class="demographics-item">
                        <span class="demographics-label"><i class="fas fa-users-cog text-info"></i> Employee Staff</span>
                        <span class="demographics-value bg-light-teal">{{ number_format($demographics['staff']) }}</span>
                    </li>
                    <li class="demographics-item">
                        <span class="demographics-label"><i class="fas fa-user text-purple"></i> Logins & Users</span>
                        <span class="demographics-value bg-light-purple">{{ number_format($demographics['users']) }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Live Protocol Timeline Panel -->
    <div class="col-xl-4 col-md-6 col-12 mb-4">
        <div class="card card-custom h-100">
            <div class="card-header-custom">
                <h5 class="card-title-custom"><i class="fas fa-history text-warning mr-1"></i> Live Protocol</h5>
                <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 0.65rem; border-radius: 4px;">● LIVE</span>
            </div>
            <div class="card-body-custom">
                <div class="protocol-stream">
                    @forelse($liveLogs as $log)
                    <div class="protocol-log-item">
                        <div class="protocol-time">{{ $log->created_at->format('M d, Y - h:i A') }}</div>
                        <div class="protocol-desc font-weight-bold">
                            <i class="fas fa-key text-primary mr-1"></i> User Logged In
                        </div>
                        <div class="small text-muted mt-1">{{ $log->user->name ?? $log->email_attempted }} ({{ $log->user->role ?? 'N/A' }})</div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-muted">No activity stream recorded.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- System Health & Maintenance Panel -->
    <div class="col-xl-4 col-12 mb-4">
        <div class="card card-custom h-100">
            <div class="card-header-custom">
                <h5 class="card-title-custom"><i class="fas fa-server text-primary mr-1"></i> System Health</h5>
                <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 0.65rem; border-radius: 4px;">{{ $dbStatus }}</span>
            </div>
            <div class="card-body-custom">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="font-weight-bold text-muted" style="font-size: 0.85rem;">Database Size</span>
                    <span class="font-weight-extrabold text-dark" style="font-size: 0.88rem;">{{ $dbSizeFormatted }}</span>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="font-weight-bold text-muted" style="font-size: 0.85rem;">Est. Concurrent Limits</span>
                    <span class="badge badge-info px-2 py-1 font-weight-bold" style="font-size: 0.72rem; border-radius: 6px;">~2500 max</span>
                </div>

                <div class="health-bar-label">
                    <span>Disk Capacity</span>
                    <span>{{ $diskUsedPercent }}%</span>
                </div>
                <div class="health-progress-wrapper">
                    <div class="health-progress-bar bg-primary" style="width: {{ $diskUsedPercent }}%;"></div>
                </div>

                <div class="health-bar-label">
                    <span>RAM Capacity</span>
                    <span>{{ $ramUsedPercent }}%</span>
                </div>
                <div class="health-progress-wrapper">
                    <div class="health-progress-bar bg-success" style="width: {{ $ramUsedPercent }}%;"></div>
                </div>

                <button class="btn btn-block btn-outline-primary btn-sm font-weight-bold mt-2" onclick="triggerSystemOptimization()" id="optimize-sys-btn">
                    <i class="fas fa-broom mr-1"></i> OPTIMIZE PLATFORM
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Row 6: Expiring soon and exceptions panels (white cards) -->
<div class="row">
    <!-- Subscription Expiries Panel -->
    <div class="col-xl-6 col-12 mb-4">
        <div class="card card-custom h-100">
            <div class="card-header-custom">
                <h5 class="card-title-custom"><i class="fas fa-calendar-times text-danger mr-1"></i> Subscription Expiries (30 Days)</h5>
                <a href="{{ Route::has('superadmin.subscriptions.index') ? route('superadmin.subscriptions.index') : '#' }}" style="font-size: 0.85rem; font-weight: 700; color: #e5ba73; text-decoration: none;">View All</a>
            </div>
            <div class="card-body-custom p-0">
                <div class="table-responsive">
                    <table class="table table-custom table-panel m-0">
                        <tbody>
                            @forelse($expiringSoonSubscriptions as $index => $sub)
                            @if($sub->school)
                            <tr id="sub-row-{{ $sub->school->id }}">
                                <td style="width: 35px; font-weight: 800; color: #94a3b8; text-align: center;">{{ $index + 1 }}.</td>
                                <td>
                                    <div class="font-weight-extrabold text-dark">{{ $sub->school->name }}</div>
                                    <div class="small text-muted"><i class="far fa-envelope mr-1"></i>{{ $sub->school->admin_email }}</div>
                                </td>
                                <td>
                                    <span class="badge badge-light border font-weight-bold py-1 px-2">{{ $sub->plan->name ?? 'N/A' }}</span>
                                </td>
                                <td class="text-right" style="white-space: nowrap;">
                                    <div class="d-flex align-items-center justify-content-end gap-2">
                                        <button class="btn btn-xs btn-outline-success font-weight-bold px-2 py-1 rounded-pill" style="font-size: 0.65rem;" onclick="extendSubscriptionInline({{ $sub->school->id }})" id="sub-extend-btn-{{ $sub->school->id }}">
                                            +30 Days
                                        </button>
                                        <span class="badge badge-danger px-2 py-1 font-weight-bold" style="font-size: 0.72rem; border-radius: 8px;" id="sub-expiry-badge-{{ $sub->school->id }}">
                                            {{ $sub->subscription_ends_at->format('M d, Y') }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                            @endif
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">No schools expiring in the next 30 days.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Application Errors logger panel -->
    <div class="col-xl-6 col-12 mb-4">
        <div class="card card-custom h-100">
            <div class="card-header-custom">
                <h5 class="card-title-custom"><i class="fas fa-bug text-danger mr-1"></i> Live Application Errors</h5>
            </div>
            <div class="card-body-custom p-0">
                <div class="list-group list-group-flush">
                    @forelse($applicationErrors as $err)
                    <a href="javascript:void(0)" class="list-group-item list-group-item-action border-0 error-item-clickable px-4 py-3" onclick="openErrorDetailModal({{ json_encode($err) }})">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge badge-danger font-weight-bold" style="font-size: 0.65rem; border-radius: 4px;">{{ $err['type'] }}</span>
                            <small class="text-muted font-weight-bold">{{ $err['time_ago'] }}</small>
                        </div>
                        <div class="text-dark font-weight-extrabold text-truncate" style="font-size: 0.8rem; font-family: monospace;">
                            {{ $err['message'] }}
                        </div>
                    </a>
                    @empty
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-check-circle text-success mb-2" style="font-size: 1.5rem;"></i>
                        <p class="mb-0" style="font-size: 0.85rem;">No errors logged recently.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 7: Quick Actions Bar (Original) -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card card-custom">
            <div class="card-header-custom py-3">
                <h5 class="card-title-custom">Quick Operations Bar</h5>
            </div>
            <div class="card-body-custom">
                <div class="row">
                    <!-- Action 1: Add New School -->
                    <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                        <a href="{{ route('superadmin.schools.create') }}" class="quick-action-card">
                            <div class="quick-action-icon-circle bg-light-blue">
                                <i class="fas fa-plus"></i>
                            </div>
                            <span class="quick-action-title">Add New School</span>
                        </a>
                    </div>
                    <!-- Action 2: Create Plan -->
                    <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                        <a href="{{ route('superadmin.plans.create') }}" class="quick-action-card">
                            <div class="quick-action-icon-circle bg-light-purple">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <span class="quick-action-title">Create Plan</span>
                        </a>
                    </div>
                    <!-- Action 3: Send Broadcast -->
                    <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                        <a href="{{ route('superadmin.broadcast') }}" class="quick-action-card">
                            <div class="quick-action-icon-circle bg-light-orange">
                                <i class="fas fa-paper-plane"></i>
                            </div>
                            <span class="quick-action-title">Send Broadcast</span>
                        </a>
                    </div>
                    <!-- Action 4: Menu Manager -->
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ Route::has('superadmin.menu-manager.index') ? route('superadmin.menu-manager.index') : '#' }}" class="quick-action-card">
                            <div class="quick-action-icon-circle bg-light-teal">
                                <i class="fas fa-list"></i>
                            </div>
                            <span class="quick-action-title">Menu Manager</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Banner -->
<div class="row">
    <div class="col-12">
        <div class="bottom-promo-banner">
            <div class="promo-content">
                <div class="promo-icon-box">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div>
                    <h4 class="promo-title-main">Streamline Your School Operations</h4>
                    <p class="promo-text-sub">Configure notification preferences, edit templates, and review analytics logs to manage global institutions.</p>
                </div>
            </div>
            <div>
                <button class="btn btn-gold-banner mt-3 mt-lg-0">Explore Features <i class="fas fa-arrow-right ms-2"></i></button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Live System Feed -->
<div class="modal fade" id="liveFeedModal" tabindex="-1" role="dialog" aria-labelledby="liveFeedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 16px; border: none;">
            <div class="modal-header border-bottom-0 p-4">
                <h5 class="modal-title font-weight-bold" id="liveFeedModalLabel">
                    <span class="live-dot mr-2"></span> Real-Time System Activity Feed
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 bg-light" style="max-height: 450px; overflow-y: auto; border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                <div class="list-group">
                    @forelse($liveLogs as $log)
                    <div class="list-group-item border-0 mb-2 p-3" style="border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.015);">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge badge-primary font-weight-bold" style="font-size: 0.7rem;">LOGIN SUCCESS</span>
                            <span class="text-muted font-weight-bold" style="font-size: 0.72rem;">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="font-weight-extrabold text-dark" style="font-size: 0.85rem;">
                            User {{ $log->user->name ?? $log->email_attempted }} successfully logged in
                        </div>
                        <div class="text-muted mt-2" style="font-size: 0.78rem; font-family: monospace;">
                            IP: {{ $log->ip_address }} | Agent: {{ Str::limit($log->user_agent, 80) }}
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-muted">No activity feed.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Error Details Viewer -->
<div class="modal fade" id="errorDetailModal" tabindex="-1" role="dialog" aria-labelledby="errorDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 16px; border: none;">
            <div class="modal-header border-bottom-0 p-4">
                <h5 class="modal-title font-weight-bold text-danger" id="errorDetailModalLabel">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Application Error Details
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 bg-dark text-light" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                <div class="mb-3">
                    <strong class="text-danger" style="font-size: 0.85rem;">ERROR TYPE:</strong>
                    <div class="badge badge-danger font-weight-bold ml-2" id="modal-err-type" style="font-size: 0.75rem;">production.ERROR</div>
                </div>
                <div class="mb-3">
                    <strong class="text-warning" style="font-size: 0.85rem;">LOGGED AT:</strong>
                    <div id="modal-err-time" class="d-inline ml-2 font-weight-bold" style="font-size: 0.82rem;">45 minutes ago</div>
                </div>
                <div>
                    <strong class="text-info" style="font-size: 0.85rem; display: block; margin-bottom: 8px;">STACK TRACE / EXCEPTION MESSAGE:</strong>
                    <pre id="modal-err-trace" class="bg-black p-3 rounded text-white" style="white-space: pre-wrap; font-size: 0.78rem; font-family: monospace; max-height: 300px; overflow-y: auto; border: 1px solid #334155;"></pre>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // 1. Dynamic Greeting and Local Clock
    function updateGreetingAndClock() {
        const now = new Date();
        
        let hours = now.getHours();
        let minutes = now.getMinutes();
        let seconds = now.getSeconds();
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; 
        minutes = minutes < 10 ? '0' + minutes : minutes;
        seconds = seconds < 10 ? '0' + seconds : seconds;
        const timeStr = `${hours}:${minutes}:${seconds} ${ampm}`;

        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const dayName = days[now.getDay()];
        const monthName = months[now.getMonth()];
        const dateStr = `${dayName}, ${monthName} ${now.getDate()}, ${now.getFullYear()} - ${timeStr}`;
        
        const ticker = document.getElementById('live-time-ticker');
        if (ticker) ticker.innerHTML = dateStr;
    }
    
    setInterval(updateGreetingAndClock, 1000);
    updateGreetingAndClock();

    // 2. Line Chart: Monthly School Registrations
    const regCtx = document.getElementById('schoolRegistrationsChart').getContext('2d');
    const regGradient = regCtx.createLinearGradient(0, 0, 0, 300);
    regGradient.addColorStop(0, 'rgba(229, 186, 115, 0.4)');
    regGradient.addColorStop(1, 'rgba(229, 186, 115, 0.0)');

    new Chart(regCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartMonths) !!},
            datasets: [{
                label: 'Schools Registered',
                data: {!! json_encode($chartSchoolCounts) !!},
                borderColor: '#e5ba73',
                borderWidth: 3,
                backgroundColor: regGradient,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#0c1024',
                pointBorderColor: '#e5ba73',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0c1024',
                    titleColor: '#ffffff',
                    bodyColor: '#e5ba73',
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        color: '#64748b',
                        font: { family: 'Lato', weight: 600, size: 11 }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(229, 231, 235, 0.4)' },
                    ticks: {
                        color: '#64748b',
                        stepSize: 1,
                        font: { family: 'Lato', weight: 600, size: 11 }
                    }
                }
            }
        }
    });

    // 3. Doughnut Chart: Plan Distribution
    const plansCtx = document.getElementById('plansDistributionChart').getContext('2d');
    new Chart(plansCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($planLabels) !!},
            datasets: [{
                data: {!! json_encode($planCounts) !!},
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b'],
                borderWidth: 3,
                borderColor: '#ffffff',
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0c1024',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: true,
                    boxWidth: 8,
                    boxHeight: 8
                }
            }
        }
    });

    // 4. Count Up Animation for Stats
    function animateValue(id, start, end, duration) {
        const obj = document.getElementById(id);
        if (!obj || start === end) return;
        const range = end - start;
        const steps = 50;
        const stepVal = range / steps;
        let step = 0;

        const timer = setInterval(function() {
            step++;
            let current = Math.floor(start + (stepVal * step));
            if (step >= steps) {
                clearInterval(timer);
                obj.innerHTML = end.toLocaleString();
            } else {
                obj.innerHTML = current.toLocaleString();
            }
        }, 20);
    }

    window.addEventListener('DOMContentLoaded', () => {
        animateValue('val-schools', 0, {{ $totalSchools }}, 1000);
        animateValue('val-subs', 0, {{ $activeSubscriptions }}, 1000);
        animateValue('val-students', 0, {{ $totalStudents }}, 1000);
    });

    // 5. Inline AJAX quick-extend
    function extendSubscriptionInline(schoolId) {
        const btn = document.getElementById(`extend-btn-${schoolId}`) || document.getElementById(`sub-extend-btn-${schoolId}`);
        const badge = document.getElementById(`expiry-label-${schoolId}`) || document.getElementById(`sub-expiry-badge-${schoolId}`);
        
        if (!btn || btn.disabled) return;
        btn.disabled = true;
        btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i>`;
        
        fetch(`/superadmin/dashboard/quick-extend`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ school_id: schoolId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                if (badge) {
                    badge.innerHTML = data.new_expiry;
                    badge.className = "badge badge-success px-2 py-1 font-weight-bold";
                }
                btn.innerHTML = `<i class="fas fa-check"></i>`;
                btn.className = "btn btn-xs btn-success font-weight-bold px-2 py-1 rounded-pill";
                
                // Update KPI card count
                const countSubs = document.getElementById('val-subs');
                if (countSubs) {
                    const currentVal = parseInt(countSubs.innerText) || 0;
                    countSubs.innerText = (currentVal + 1).toLocaleString();
                }
            } else {
                alert(data.message || 'Failed to extend subscription.');
                btn.disabled = false;
                btn.innerHTML = '+30d';
            }
        })
        .catch(err => {
            console.error(err);
            alert('An error occurred during quick extend.');
            btn.disabled = false;
            btn.innerHTML = '+30d';
        });
    }

    // 6. Platform Caches System Optimizer
    function triggerSystemOptimization() {
        const btn = document.getElementById('optimize-sys-btn');
        if (!btn || btn.disabled) return;
        
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<i class="fas fa-spinner fa-spin mr-1"></i> OPTIMIZING...`;
        
        fetch(`/superadmin/dashboard/optimize-db`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message || 'System caches optimized successfully!');
            btn.innerHTML = `<i class="fas fa-check mr-1"></i> OPTIMIZED`;
            btn.className = "btn btn-block btn-success btn-sm font-weight-bold mt-2";
            setTimeout(() => {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                btn.className = "btn btn-block btn-outline-primary btn-sm font-weight-bold mt-2";
            }, 3000);
        })
        .catch(err => {
            console.error(err);
            alert('An error occurred during optimization.');
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        });
    }

    // 7. Error traces modal displayer
    function openErrorDetailModal(err) {
        document.getElementById('modal-err-type').innerText = err.type;
        document.getElementById('modal-err-time').innerText = err.time_ago;
        document.getElementById('modal-err-trace').innerText = err.full_message;
        $('#errorDetailModal').modal('show');
    }
</script>
@endsection
