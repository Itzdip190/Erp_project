@extends('layouts.app')

@section('title', 'Library Dashboard & Intelligence - Library Management')
@section('page-title', 'Library Dashboard')

@section('content')
<!-- Load Chart.js for smooth interactive visual graphs -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    :root {
        --dash-blue-primary: #0038b8;
        --dash-blue-deep: #002266;
        --dash-blue-light: #eff6ff;
        --dash-blue-border: #bfdbfe;
        --dash-blue-gradient: linear-gradient(135deg, #002266 0%, #0038b8 50%, #1d4ed8 100%);
        --dash-card-bg: #ffffff;
        --dash-slate-50: #f8fafc;
        --dash-slate-100: #f1f5f9;
        --dash-slate-200: #e2e8f0;
        --dash-slate-600: #475569;
        --dash-slate-800: #1e293b;
        --dash-slate-900: #0f172a;
    }

    .lib-dash-wrapper {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        color: var(--dash-slate-900);
        padding-bottom: 30px;
    }

    /* Top Command Header Bar */
    .dash-command-header {
        background: var(--dash-blue-gradient);
        border-radius: 16px;
        padding: 22px 26px;
        color: #ffffff;
        margin-bottom: 24px;
        box-shadow: 0 10px 25px -5px rgba(0, 34, 102, 0.25);
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        position: relative;
        overflow: hidden;
    }

    .dash-command-header::after {
        content: '';
        position: absolute;
        right: -40px;
        top: -40px;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .dash-header-title {
        font-size: 22px;
        font-weight: 800;
        letter-spacing: -0.5px;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .dash-header-sub {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.85);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .dash-header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .dash-action-btn {
        background: #ffffff;
        color: var(--dash-blue-primary);
        font-weight: 700;
        font-size: 12.5px;
        padding: 8px 16px;
        border-radius: 8px;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        transition: all 0.2s ease;
    }

    .dash-action-btn:hover {
        background: #f0f7ff;
        color: var(--dash-blue-deep);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    .dash-action-btn-outline {
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.35);
    }

    .dash-action-btn-outline:hover {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }

    /* Custom Academic Session Dropdown */
    .academic-session-btn {
        background: rgba(255, 255, 255, 0.18) !important;
        color: #ffffff !important;
        border: 1.5px solid rgba(255, 255, 255, 0.38) !important;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-radius: 24px !important;
        padding: 7px 16px !important;
        font-size: 12.5px !important;
        font-weight: 700 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08) !important;
        text-decoration: none !important;
    }

    .academic-session-btn:hover, 
    .academic-session-btn:focus, 
    .academic-session-btn[aria-expanded="true"] {
        background: rgba(255, 255, 255, 0.28) !important;
        color: #ffffff !important;
        border-color: rgba(255, 255, 255, 0.65) !important;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15) !important;
        transform: translateY(-1px);
    }

    .academic-session-menu {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        min-width: 230px !important;
        box-shadow: 0 10px 30px rgba(0, 34, 102, 0.2) !important;
        padding: 8px !important;
        animation: dropdownFadeIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes dropdownFadeIn {
        from {
            opacity: 0;
            transform: translateY(-8px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .academic-session-menu .dropdown-item {
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
        border-radius: 8px;
        padding: 8px 12px;
        transition: all 0.15s ease;
    }

    .academic-session-menu .dropdown-item:hover {
        background: #eff6ff;
        color: #0038b8 !important;
    }

    .active-session-item {
        background: #f0f7ff !important;
        color: #0038b8 !important;
        font-weight: 700 !important;
        border-left: 3.5px solid #0038b8 !important;
    }

    /* KPI Summary Cards */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin-bottom: 24px;
    }

    @media (max-width: 1200px) {
        .kpi-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 640px) {
        .kpi-grid {
            grid-template-columns: 1fr;
        }
    }

    .kpi-card {
        background: #ffffff;
        border: 1.5px solid var(--dash-slate-200);
        border-radius: 14px;
        padding: 18px 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        position: relative;
        overflow: hidden;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 56, 184, 0.1);
        border-color: var(--dash-blue-border);
    }

    .kpi-top-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }

    .kpi-label {
        font-size: 12px;
        font-weight: 700;
        color: var(--dash-slate-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .kpi-icon-box {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        background: var(--dash-blue-light);
        color: var(--dash-blue-primary);
        border: 1px solid var(--dash-blue-border);
    }

    .kpi-main-val {
        font-size: 28px;
        font-weight: 900;
        color: var(--dash-slate-900);
        line-height: 1.1;
        margin-bottom: 6px;
        letter-spacing: -0.8px;
    }

    .kpi-sub-stats {
        font-size: 12px;
        color: var(--dash-slate-600);
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .kpi-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .kpi-badge-blue {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #dbeafe;
    }

    .kpi-badge-green {
        background: #f0fdf4;
        color: #16a34a;
        border: 1px solid #dcfce7;
    }

    .kpi-badge-danger {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fee2e2;
    }

    /* Progress bar in KPI */
    .kpi-progress-bar {
        height: 6px;
        background: #e2e8f0;
        border-radius: 10px;
        margin-top: 10px;
        overflow: hidden;
    }

    .kpi-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #0038b8 0%, #38bdf8 100%);
        border-radius: 10px;
        transition: width 0.8s ease;
    }

    /* Main Chart Grid */
    .chart-analytics-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 18px;
        margin-bottom: 24px;
    }

    @media (max-width: 992px) {
        .chart-analytics-grid {
            grid-template-columns: 1fr;
        }
    }

    .dash-widget-card {
        background: #ffffff;
        border: 1.5px solid var(--dash-slate-200);
        border-radius: 14px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        padding: 20px 22px;
        display: flex;
        flex-direction: column;
    }

    .widget-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 12px;
    }

    .widget-title {
        font-size: 15px;
        font-weight: 800;
        color: var(--dash-slate-900);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .widget-title i {
        color: var(--dash-blue-primary);
    }

    .widget-badge {
        font-size: 11.5px;
        font-weight: 700;
        color: var(--dash-slate-600);
        background: var(--dash-slate-100);
        padding: 4px 10px;
        border-radius: 20px;
        border: 1px solid var(--dash-slate-200);
    }

    /* 3-Column Operations Hub */
    .operations-hub-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
        margin-bottom: 24px;
    }

    @media (max-width: 1100px) {
        .operations-hub-grid {
            grid-template-columns: 1fr;
        }
    }

    /* List items styling */
    .defaulter-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 12px;
        border-radius: 10px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        margin-bottom: 8px;
        transition: all 0.2s ease;
    }

    .defaulter-item:hover {
        background: #ffffff;
        border-color: #fca5a5;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.08);
    }

    .popular-book-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        border-radius: 10px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        margin-bottom: 8px;
        transition: all 0.2s ease;
    }

    .popular-book-item:hover {
        background: #ffffff;
        border-color: var(--dash-blue-border);
        box-shadow: 0 4px 12px rgba(0, 56, 184, 0.08);
    }

    .rank-badge {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: var(--dash-blue-primary);
        color: #ffffff;
        font-weight: 800;
        font-size: 11.5px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .rank-badge-gold {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .recent-activity-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 12px;
        border-radius: 10px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        margin-bottom: 8px;
        font-size: 12.5px;
    }

    .recent-activity-item:hover {
        background: #ffffff;
        border-color: var(--dash-blue-border);
    }

    /* Section Capacity mini-deck */
    .sections-deck-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 14px;
    }

    .section-mini-card {
        background: #ffffff;
        border: 1.5px solid var(--dash-slate-200);
        border-radius: 10px;
        padding: 12px 14px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.03);
    }
</style>

<div class="container-fluid px-0 lib-dash-wrapper">

    <!-- 1. TOP COMMAND & ANALYTICS HEADER BAR -->
    <div class="dash-command-header">
        <div>
            <div class="dash-header-title">
                <i class="fas fa-book-open-reader text-warning"></i> Library Command & Analytics Hub
            </div>
            <div class="dash-header-sub">
                <span><i class="fas fa-shield-halved me-1"></i> Real-time Circulation, Inventory Surveillance & Defaulter Control</span>
                <span>•</span>
                <span><i class="fas fa-calendar-day me-1"></i> {{ date('l, d F Y') }}</span>
            </div>
        </div>

        <div class="dash-header-actions">
            <!-- Quick Action Links -->
            <a href="{{ route('school.library.transactions') }}" class="dash-action-btn">
                <i class="fas fa-arrows-spin text-primary"></i> Issue / Return Desk
            </a>
            <a href="{{ route('school.library.catalogue') }}" class="dash-action-btn dash-action-btn-outline">
                <i class="fas fa-book"></i> Book Catalogue
            </a>
            <a href="{{ route('school.library.basics') }}" class="dash-action-btn dash-action-btn-outline" title="Library Rules & Fines Policy">
                <i class="fas fa-sliders"></i> Rules
            </a>
        </div>
    </div>

    <!-- 2. MASTER METRIC KPI CARDS (4-Column Ribbon) -->
    <div class="kpi-grid">
        <!-- KPI 1: Catalogue & Inventory -->
        <div class="kpi-card">
            <div>
                <div class="kpi-top-row">
                    <span class="kpi-label">Catalogue & Stock</span>
                    <div class="kpi-icon-box"><i class="fas fa-book-bookmark"></i></div>
                </div>
                <div class="kpi-main-val">{{ number_format($totalBooks) }} <span style="font-size: 14px; font-weight: 600; color: #64748b;">Titles</span></div>
                <div class="kpi-sub-stats">
                    <span class="kpi-badge-pill kpi-badge-blue"><i class="fas fa-cubes me-1"></i> {{ $totalCopies }} Total Copies</span>
                    <span class="kpi-badge-pill kpi-badge-green"><i class="fas fa-check-circle me-1"></i> {{ $availableCopies }} Available</span>
                </div>
            </div>
            <div>
                <div class="d-flex justify-content-between text-muted" style="font-size: 11px; margin-top: 10px; font-weight: 600;">
                    <span>Utilization Rate</span>
                    <span>{{ $utilizationRate }}% Lent Out</span>
                </div>
                <div class="kpi-progress-bar">
                    <div class="kpi-progress-fill" style="width: {{ min(100, max(5, $utilizationRate)) }}%;"></div>
                </div>
            </div>
        </div>

        <!-- KPI 2: Today's Circulation Activity -->
        <div class="kpi-card">
            <div>
                <div class="kpi-top-row">
                    <span class="kpi-label">Today's Activity</span>
                    <div class="kpi-icon-box" style="background: #eff6ff; color: #0038b8;"><i class="fas fa-arrows-rotate"></i></div>
                </div>
                <div class="kpi-main-val">{{ $todayIssuedCount + $todayReturnedCount }} <span style="font-size: 14px; font-weight: 600; color: #64748b;">Actions</span></div>
                <div class="kpi-sub-stats">
                    <span class="kpi-badge-pill kpi-badge-blue"><i class="fas fa-arrow-up-right-from-square me-1"></i> {{ $todayIssuedCount }} Issued</span>
                    <span class="kpi-badge-pill kpi-badge-green"><i class="fas fa-arrow-down-left-and-up-right-to-center me-1"></i> {{ $todayReturnedCount }} Returned</span>
                    @if($todayRenewedCount > 0)
                    <span class="kpi-badge-pill kpi-badge-blue"><i class="fas fa-arrows-spin me-1"></i> {{ $todayRenewedCount }} Renewed</span>
                    @endif
                </div>
            </div>
            <div class="text-muted small mt-2" style="font-size: 11px;">
                <i class="fas fa-bolt text-warning me-1"></i> Live circulation operations recorded today.
            </div>
        </div>

        <!-- KPI 3: Active Loans & Overdue Defaulters -->
        <div class="kpi-card">
            <div>
                <div class="kpi-top-row">
                    <span class="kpi-label">Active Loans & Defaulters</span>
                    <div class="kpi-icon-box" style="background: {{ $overdueCount > 0 ? '#fef2f2' : '#eff6ff' }}; color: {{ $overdueCount > 0 ? '#dc2626' : '#0038b8' }}; border-color: {{ $overdueCount > 0 ? '#fecaca' : '#bfdbfe' }};">
                        <i class="fas {{ $overdueCount > 0 ? 'fa-triangle-exclamation' : 'fa-user-graduate' }}"></i>
                    </div>
                </div>
                <div class="kpi-main-val">{{ $activeLoansCount }} <span style="font-size: 14px; font-weight: 600; color: #64748b;">Active Loans</span></div>
                <div class="kpi-sub-stats">
                    <span class="kpi-badge-pill kpi-badge-blue"><i class="fas fa-graduation-cap me-1"></i> {{ $activeStudentLoansCount }} Students</span>
                    <span class="kpi-badge-pill kpi-badge-blue"><i class="fas fa-chalkboard-user me-1"></i> {{ $activeStaffLoansCount }} Staff</span>
                    @if($overdueCount > 0)
                    <span class="kpi-badge-pill kpi-badge-danger"><i class="fas fa-clock me-1"></i> {{ $overdueCount }} OVERDUE</span>
                    @else
                    <span class="kpi-badge-pill kpi-badge-green"><i class="fas fa-check me-1"></i> 0 Overdue</span>
                    @endif
                </div>
            </div>
            <div class="text-muted small mt-2" style="font-size: 11px;">
                <i class="fas fa-info-circle text-primary me-1"></i> Total active book copies held across campus.
            </div>
        </div>

        <!-- KPI 4: Fine & Financial Ledger -->
        <div class="kpi-card">
            <div>
                <div class="kpi-top-row">
                    <span class="kpi-label">Fines & Penalties</span>
                    <div class="kpi-icon-box" style="background: #f0fdf4; color: #16a34a; border-color: #bbf7d0;"><i class="fas fa-indian-rupee-sign"></i></div>
                </div>
                <div class="kpi-main-val">₹{{ number_format($totalFineCollected, 2) }} <span style="font-size: 14px; font-weight: 600; color: #64748b;">Collected</span></div>
                <div class="kpi-sub-stats">
                    <span class="kpi-badge-pill kpi-badge-green"><i class="fas fa-receipt me-1"></i> ₹{{ number_format($todayFineCollected, 2) }} Today</span>
                    @if($totalFinePending > 0)
                    <span class="kpi-badge-pill kpi-badge-danger"><i class="fas fa-hourglass-half me-1"></i> ₹{{ number_format($totalFinePending, 2) }} Due</span>
                    @endif
                    @if($waivedFinesCount > 0)
                    <span class="kpi-badge-pill kpi-badge-blue">{{ $waivedFinesCount }} Waived</span>
                    @endif
                </div>
            </div>
            <div class="text-muted small mt-2" style="font-size: 11px;">
                <i class="fas fa-shield-check text-success me-1"></i> Auto-accrued late fine calculation active.
            </div>
        </div>
    </div>

    <!-- 3. INTERACTIVE ANALYTICS & CHARTS GRID (Circulation Flow + Category Breakdown) -->
    <div class="chart-analytics-grid">
        <!-- Left: Monthly Circulation Dynamics -->
        <div class="dash-widget-card">
            <div class="widget-header">
                <div class="widget-title">
                    <i class="fas fa-chart-line-up"></i> Monthly Circulation Velocity (Issued vs Returned)
                </div>
                <span class="widget-badge"><i class="fas fa-calendar me-1"></i> Last 6 Months</span>
            </div>
            <div style="position: relative; height: 270px; width: 100%;">
                <canvas id="circulationTrendsChart"></canvas>
            </div>
        </div>

        <!-- Right: Category / Section Distribution -->
        <div class="dash-widget-card">
            <div class="widget-header">
                <div class="widget-title">
                    <i class="fas fa-chart-pie"></i> Catalogue by Category
                </div>
                <span class="widget-badge">{{ $totalSections }} Sections</span>
            </div>
            <div style="position: relative; height: 220px; width: 100%; display: flex; align-items: center; justify-content: center;">
                <canvas id="categoryDoughnutChart"></canvas>
            </div>
            <div class="text-center text-muted small mt-2" style="font-size: 11.5px;">
                Breakdown of active book collections across library sections.
            </div>
        </div>
    </div>

    <!-- 4. THREE-COLUMN INTELLIGENCE & OPERATIONS HUB -->
    <div class="operations-hub-grid">
        
        <!-- Column 1: 🚨 Critical Overdue Defaulters Alert -->
        <div class="dash-widget-card">
            <div class="widget-header">
                <div class="widget-title text-danger">
                    <i class="fas fa-triangle-exclamation text-danger"></i> Overdue Defaulters Alert
                </div>
                <span class="badge {{ $overdueTransactions->count() > 0 ? 'bg-danger' : 'bg-success' }} px-2 py-1">
                    {{ $overdueTransactions->count() }} Alert{{ $overdueTransactions->count() === 1 ? '' : 's' }}
                </span>
            </div>

            <div style="flex: 1 1 auto;">
                @forelse($overdueTransactions as $otxn)
                    @php
                        $borrower = $otxn->member_type === 'staff' ? $otxn->staff : $otxn->student;
                        $bName = $borrower ? trim(($borrower->first_name ?? '') . ' ' . ($borrower->last_name ?? '')) : 'Borrower';
                        $bCode = $otxn->member_type === 'staff' ? ($borrower?->employee_id ?: 'EMP') : ($borrower?->admission_number ?: 'ADM');
                        $bPhoto = $borrower?->photo_url ?? null;
                    @endphp
                    <div class="defaulter-item">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div class="rounded-circle overflow-hidden d-flex align-items-center justify-content-center text-white fw-bold border border-danger-subtle" style="width: 36px; height: 36px; font-size: 12px; background: #dc2626; flex-shrink: 0;">
                                @if($bPhoto && !str_contains($bPhoto, 'avatar-student.png'))
                                    <img src="{{ $bPhoto }}" alt="{{ $bName }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <span style="display:none;">{{ strtoupper(substr($bName, 0, 1)) }}</span>
                                @else
                                    <span>{{ strtoupper(substr($bName, 0, 1)) }}</span>
                                @endif
                            </div>
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 13px;">{{ $bName }} <span class="badge bg-light text-dark border ms-1 font-monospace" style="font-size: 10px;">{{ $bCode }}</span></div>
                                <div class="text-primary small fw-semibold text-truncate" style="font-size: 11.5px; max-width: 170px;">
                                    <i class="fas fa-book me-1"></i> {{ $otxn->book?->title ?? 'Book' }}
                                </div>
                                <div class="text-danger small" style="font-size: 11px; font-weight: 700;">
                                    <i class="fas fa-clock me-1"></i> Due: {{ $otxn->due_date ? \Carbon\Carbon::parse($otxn->due_date)->format('d M Y') : '—' }} ({{ $otxn->late_days ?? '—' }}d Overdue)
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-bold" style="font-size: 11px;">₹{{ number_format($otxn->total_fine, 2) }}</span>
                            <div class="mt-1">
                                <a href="{{ route('school.library.transactions') }}" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 10.5px; border-radius: 4px;">
                                    Resolve
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        <div class="mb-2"><i class="fas fa-circle-check text-success fa-3x"></i></div>
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 14px;">No Overdue Defaulters!</h6>
                        <p class="small text-muted mb-0" style="font-size: 12px;">All lent library books are currently within their valid return periods.</p>
                    </div>
                @endforelse
            </div>
            <div class="mt-2 pt-2 border-top text-center">
                <a href="{{ route('school.library.transactions') }}?status=overdue" class="text-decoration-none small fw-bold text-primary" style="font-size: 12px;">
                    View All Overdue Loans <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>

        <!-- Column 2: 🔥 Most Frequently Borrowed Books -->
        <div class="dash-widget-card">
            <div class="widget-header">
                <div class="widget-title">
                    <i class="fas fa-fire-flame-curved text-warning"></i> Most Popular Titles
                </div>
                <span class="widget-badge"><i class="fas fa-star text-warning me-1"></i> Top Books</span>
            </div>

            <div style="flex: 1 1 auto;">
                @forelse($popularBooks as $index => $pbook)
                    <div class="popular-book-item">
                        <div class="rank-badge {{ $index === 0 ? 'rank-badge-gold' : '' }}">
                            {{ $index + 1 }}
                        </div>
                        <div style="flex: 1 1 auto; min-width: 0;">
                            <div class="fw-bold text-dark text-truncate" style="font-size: 13px;" title="{{ $pbook->title }}">
                                {{ $pbook->title }}
                            </div>
                            <div class="text-muted small text-truncate" style="font-size: 11px;">
                                <i class="fas fa-user-pen me-1"></i> {{ $pbook->author ?: 'Unknown Author' }}
                            </div>
                            <div class="d-flex align-items-center gap-2 mt-1" style="font-size: 10.5px;">
                                <span class="badge bg-primary-subtle text-primary border" style="font-size: 10px;">{{ $pbook->section?->name ?? 'General' }}</span>
                                <span class="text-success fw-bold">{{ $pbook->available_copies }} copies left</span>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-light text-dark border fw-bold" style="font-size: 11px;">{{ $pbook->issued_copies }} On Loan</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        <div class="mb-2"><i class="fas fa-book-open text-primary fa-3x" style="opacity: 0.5;"></i></div>
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 14px;">No Circulation Data</h6>
                        <p class="small text-muted mb-0" style="font-size: 12px;">Issue books from the circulation desk to see popular books ranked here.</p>
                    </div>
                @endforelse
            </div>
            <div class="mt-2 pt-2 border-top text-center">
                <a href="{{ route('school.library.catalogue') }}" class="text-decoration-none small fw-bold text-primary" style="font-size: 12px;">
                    Explore Book Catalogue <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>

        <!-- Column 3: ⚡ Recent Activity Stream (Live Ticker) -->
        <div class="dash-widget-card">
            <div class="widget-header">
                <div class="widget-title">
                    <i class="fas fa-wave-pulse text-primary"></i> Live Activity Feed
                </div>
                <span class="widget-badge"><i class="fas fa-clock me-1"></i> Latest Events</span>
            </div>

            <div style="flex: 1 1 auto;">
                @forelse($recentTransactions as $rtxn)
                    @php
                        $rBorrower = $rtxn->member_type === 'staff' ? $rtxn->staff : $rtxn->student;
                        $rName = $rBorrower ? trim(($rBorrower->first_name ?? '') . ' ' . ($rBorrower->last_name ?? '')) : 'Borrower';
                        $rPhoto = $rBorrower?->photo_url ?? null;
                        $isReturned = $rtxn->status === 'returned';
                    @endphp
                    <div class="recent-activity-item">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div class="rounded-circle overflow-hidden d-flex align-items-center justify-content-center text-white fw-bold border" style="width: 38px; height: 38px; font-size: 13px; background: {{ $isReturned ? '#16a34a' : '#0038b8' }}; flex-shrink: 0; position: relative;">
                                @if($rPhoto && !str_contains($rPhoto, 'avatar-student.png'))
                                    <img src="{{ $rPhoto }}" alt="{{ $rName }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <span style="display:none;">{{ strtoupper(substr($rName, 0, 1)) }}</span>
                                @else
                                    <span>{{ strtoupper(substr($rName, 0, 1)) }}</span>
                                @endif
                            </div>
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 12.5px;">
                                    {{ $rName }} 
                                    <span class="badge {{ $isReturned ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-primary' }} ms-1" style="font-size: 9.5px;">
                                        {{ $isReturned ? 'RETURNED' : 'ISSUED' }}
                                    </span>
                                </div>
                                <div class="text-muted text-truncate" style="font-size: 11px; max-width: 170px;" title="{{ $rtxn->book?->title ?? 'Book' }}">
                                    <i class="fas fa-book me-1 text-primary"></i> {{ $rtxn->book?->title ?? 'Book' }}
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <a href="{{ route('school.library.transactions.print', $rtxn->id) }}" target="_blank" class="btn btn-sm btn-light border p-1 px-2" title="Print Twin Issue Slip" style="font-size: 10.5px;">
                                <i class="fas fa-print text-secondary"></i>
                            </a>
                            <div class="text-muted" style="font-size: 9.5px; margin-top: 2px;">
                                {{ $rtxn->created_at ? $rtxn->created_at->diffForHumans(null, true) : 'recent' }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        <div class="mb-2"><i class="fas fa-clipboard-list text-muted fa-3x" style="opacity: 0.4;"></i></div>
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 14px;">No Recent Events</h6>
                        <p class="small text-muted mb-0" style="font-size: 12px;">New issue and return transactions will automatically appear here in real time.</p>
                    </div>
                @endforelse
            </div>
            <div class="mt-2 pt-2 border-top text-center">
                <a href="{{ route('school.library.transactions') }}" class="text-decoration-none small fw-bold text-primary" style="font-size: 12px;">
                    View Complete Transaction Log <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>

    </div>

    <!-- 5. SECTION SHELF & RACK CAPACITY MINI-DECK -->
    @if(count($sectionsWithCounts) > 0)
    <div class="dash-widget-card">
        <div class="widget-header">
            <div class="widget-title">
                <i class="fas fa-layer-group text-primary"></i> Library Sections & Shelf Capacity
            </div>
            <span class="widget-badge">{{ count($sectionsWithCounts) }} Active Collections</span>
        </div>

        <div class="sections-deck-grid">
            @foreach($sectionsWithCounts as $secItem)
                <div class="section-mini-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold text-dark" style="font-size: 13px;">{{ $secItem->name }}</span>
                        <span class="badge bg-primary-subtle text-primary border" style="font-size: 10.5px;">{{ $secItem->books_count }} Titles</span>
                    </div>
                    <div class="text-muted small" style="font-size: 11.5px; margin-bottom: 6px;">
                        <i class="fas fa-location-dot me-1 text-primary"></i> {{ $secItem->rack_location ?: 'Main Floor' }}
                    </div>
                    <div class="kpi-progress-bar" style="height: 5px; margin-top: 4px;">
                        <div class="kpi-progress-fill" style="width: {{ min(100, max(15, ($secItem->books_count / max(1, $totalBooks)) * 100)) }}%;"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

<!-- Initialize Charts -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Circulation Trends Area/Bar Chart
        const ctxTrends = document.getElementById('circulationTrendsChart').getContext('2d');
        
        // Gradient fill for Issued
        const gradientIssued = ctxTrends.createLinearGradient(0, 0, 0, 260);
        gradientIssued.addColorStop(0, 'rgba(0, 56, 184, 0.25)');
        gradientIssued.addColorStop(1, 'rgba(0, 56, 184, 0.0)');

        // Gradient fill for Returned
        const gradientReturned = ctxTrends.createLinearGradient(0, 0, 0, 260);
        gradientReturned.addColorStop(0, 'rgba(2, 132, 199, 0.25)');
        gradientReturned.addColorStop(1, 'rgba(2, 132, 199, 0.0)');

        new Chart(ctxTrends, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [
                    {
                        label: 'Books Issued',
                        data: {!! json_encode($chartIssuedData) !!},
                        borderColor: '#0038b8',
                        backgroundColor: gradientIssued,
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#0038b8',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4.5,
                        pointHoverRadius: 6.5,
                    },
                    {
                        label: 'Books Returned',
                        data: {!! json_encode($chartReturnedData) !!},
                        borderColor: '#0284c7',
                        backgroundColor: gradientReturned,
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#0284c7',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4.5,
                        pointHoverRadius: 6.5,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { family: 'inherit', size: 12, weight: '700' },
                            usePointStyle: true,
                            boxWidth: 8
                        }
                    },
                    tooltip: {
                        backgroundColor: '#002266',
                        titleFont: { weight: 'bold', size: 12 },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 8
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11, weight: '600' }, color: '#64748b' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: { stepSize: 1, font: { size: 11 }, color: '#64748b' }
                    }
                }
            }
        });

        // 2. Category Doughnut Chart
        const ctxCat = document.getElementById('categoryDoughnutChart').getContext('2d');
        new Chart(ctxCat, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($categoryLabels) !!},
                datasets: [{
                    data: {!! json_encode($categoryCounts) !!},
                    backgroundColor: [
                        '#0038b8',
                        '#0284c7',
                        '#3b82f6',
                        '#60a5fa',
                        '#93c5fd',
                        '#0f172a'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 10,
                            font: { size: 11, weight: '600' },
                            padding: 12
                        }
                    },
                    tooltip: {
                        backgroundColor: '#002266',
                        cornerRadius: 8,
                        padding: 10
                    }
                }
            }
        });
    });
</script>
@endsection
