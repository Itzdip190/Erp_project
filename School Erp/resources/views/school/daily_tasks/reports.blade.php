@extends('layouts.app')

@section('page-title', 'Daily Task Student Reports & Analytics')

@section('content')
<style>
    /* =========================================================
       PURE BLUE & WHITE SYSTEM - ENTERPRISE GRADE
       Fully Responsive on Mobile, Tablet, Laptop & Desktop
       ========================================================= */
    :root {
        --theme-royal-blue: #0038b8;
        --theme-primary-blue: #2563eb;
        --theme-hover-blue: #1d4ed8;
        --theme-light-blue: #eff6ff;
        --theme-subtle-blue: #f0f7ff;
        --theme-blue-border: #bfdbfe;
        --theme-card-border: #e2e8f0;
        --theme-card-bg: #ffffff;
        --theme-text-dark: #0f172a;
        --theme-text-muted: #64748b;
        --theme-shadow-sm: 0 2px 8px rgba(37, 99, 235, 0.05);
        --theme-shadow-md: 0 8px 24px -4px rgba(37, 99, 235, 0.1), 0 4px 10px -2px rgba(0, 0, 0, 0.03);
        --theme-shadow-lg: 0 18px 38px -6px rgba(37, 99, 235, 0.16), 0 8px 16px -4px rgba(37, 99, 235, 0.08);
    }

    body.dark-mode {
        --theme-card-bg: #131c2e;
        --theme-subtle-blue: #19253d;
        --theme-card-border: rgba(255, 255, 255, 0.1);
        --theme-text-dark: #f8fafc;
        --theme-text-muted: #94a3b8;
        --theme-light-blue: rgba(37, 99, 235, 0.15);
        --theme-blue-border: rgba(59, 130, 246, 0.35);
    }

    @keyframes dtSlideUp {
        from {
            opacity: 0;
            transform: translateY(16px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes dtPulseGlow {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.35);
        }
        50% {
            box-shadow: 0 0 0 8px rgba(37, 99, 235, 0);
        }
    }

    .anim-slide-up {
        animation: dtSlideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    .dt-wrapper {
        width: 100%;
        padding: 12px 16px;
        box-sizing: border-box;
    }

    @media (max-width: 576px) {
        .dt-wrapper {
            padding: 8px 10px;
        }
    }

    /* Page Header Banner */
    .dt-header-banner {
        background: linear-gradient(135deg, #ffffff 0%, #f0f7ff 100%);
        border: 1.5px solid var(--theme-blue-border);
        border-radius: 18px;
        padding: 22px 28px;
        margin-bottom: 22px;
        box-shadow: var(--theme-shadow-md);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        position: relative;
        overflow: hidden;
    }

    body.dark-mode .dt-header-banner {
        background: linear-gradient(135deg, #131c2e 0%, #19253d 100%);
    }

    .dt-header-banner::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 5px;
        background: linear-gradient(180deg, #2563eb 0%, #1d4ed8 100%);
    }

    .dt-header-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .dt-header-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        color: var(--theme-primary-blue);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        border: 1.5px solid var(--theme-blue-border);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.12);
        animation: dtPulseGlow 3s infinite;
        flex-shrink: 0;
    }

    .dt-header-title {
        font-size: 20px;
        font-weight: 800;
        color: var(--theme-text-dark);
        margin: 0;
        letter-spacing: -0.4px;
    }

    .dt-header-subtitle {
        font-size: 13px;
        color: var(--theme-text-muted);
        margin: 2px 0 0 0;
        font-weight: 500;
    }

    .dt-nav-tab-group {
        display: flex;
        gap: 6px;
        background: var(--theme-subtle-blue);
        padding: 5px;
        border-radius: 14px;
        border: 1.5px solid var(--theme-blue-border);
    }

    .dt-nav-tab-item {
        padding: 8px 18px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 13px;
        color: var(--theme-text-muted);
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .dt-nav-tab-item.active {
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    .dt-nav-tab-item:not(.active):hover {
        background: #e0edff;
        color: var(--theme-primary-blue);
    }

    .dt-btn-pdf {
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        color: #ffffff !important;
        border: none;
        padding: 9px 18px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.28);
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .dt-btn-pdf:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #172554 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.38);
    }

    .dt-btn-excel {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        color: #ffffff !important;
        border: none;
        padding: 9px 18px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(14, 165, 233, 0.28);
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .dt-btn-excel:hover {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(14, 165, 233, 0.38);
    }

    @media (max-width: 768px) {
        .dt-header-banner {
            flex-direction: column;
            align-items: flex-start;
            padding: 18px 20px;
        }
        .dt-header-actions-wrap {
            width: 100%;
            flex-direction: column;
        }
        .dt-nav-tab-group {
            width: 100%;
        }
        .dt-nav-tab-item {
            flex: 1;
            justify-content: center;
        }
        .dt-export-btns {
            width: 100%;
            display: flex;
            gap: 8px;
        }
        .dt-export-btns a {
            flex: 1;
            justify-content: center;
        }
    }

    /* KPI Stats Grid */
    .dt-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 18px;
        margin-bottom: 22px;
    }

    @media (max-width: 576px) {
        .dt-stats-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }
    }

    .dt-stat-box {
        background: var(--theme-card-bg);
        border: 1.5px solid var(--theme-blue-border);
        border-radius: 16px;
        padding: 18px 22px;
        box-shadow: var(--theme-shadow-sm);
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s, border-color 0.25s;
    }

    .dt-stat-box:hover {
        transform: translateY(-4px);
        box-shadow: var(--theme-shadow-lg);
        border-color: var(--theme-primary-blue);
    }

    .dt-stat-num {
        font-size: 26px;
        font-weight: 800;
        color: var(--theme-royal-blue);
        line-height: 1.1;
    }

    .dt-stat-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--theme-text-muted);
        margin-top: 4px;
    }

    /* =========================================================
       CLEAN UNIFIED FILTER PANEL & GRID
       ========================================================= */
    .dt-filter-panel {
        background: var(--theme-card-bg);
        border: 1.5px solid var(--theme-blue-border);
        border-radius: 18px;
        padding: 22px 26px;
        box-shadow: var(--theme-shadow-md);
        margin-bottom: 24px;
        width: 100%;
        box-sizing: border-box;
    }

    .dt-filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        align-items: flex-end;
        width: 100%;
    }

    @media (min-width: 1200px) {
        .dt-filter-grid {
            grid-template-columns: 160px 160px 180px 160px 180px auto;
        }
    }

    @media (max-width: 576px) {
        .dt-filter-grid {
            grid-template-columns: 1fr;
            gap: 14px;
        }
    }

    .dt-field-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .dt-field-label {
        font-size: 11.5px;
        font-weight: 800;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }

    body.dark-mode .dt-field-label {
        color: #cbd5e1;
    }

    .dt-field-input-wrap {
        position: relative;
        width: 100%;
    }

    .dt-field-input {
        width: 100%;
        height: 44px;
        padding: 9px 14px;
        border-radius: 12px;
        border: 1.5px solid var(--theme-blue-border);
        background: #ffffff;
        color: var(--theme-text-dark);
        font-size: 13.5px;
        font-weight: 600;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-sizing: border-box;
    }

    body.dark-mode .dt-field-input {
        background: #131c2e;
        color: #f8fafc;
        border-color: rgba(255, 255, 255, 0.15);
    }

    .dt-field-input:focus {
        border-color: var(--theme-primary-blue);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.16);
    }

    .dt-btn-royal {
        height: 44px;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff !important;
        border: none;
        padding: 0 24px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 13.5px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.28);
        transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none;
        white-space: nowrap;
    }

    .dt-btn-royal:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 22px rgba(37, 99, 235, 0.38);
    }

    /* ─── CARD PANELS & TABLES ─── */
    .dt-card-panel {
        background: var(--theme-card-bg);
        border: 1.5px solid var(--theme-blue-border);
        border-radius: 18px;
        box-shadow: var(--theme-shadow-md);
        overflow: hidden;
        margin-bottom: 30px;
    }

    .dt-card-header {
        padding: 16px 22px;
        background: linear-gradient(180deg, #ffffff 0%, #f8faff 100%);
        border-bottom: 1.5px solid var(--theme-blue-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    body.dark-mode .dt-card-header {
        background: linear-gradient(180deg, #131c2e 0%, #19253d 100%);
        border-bottom-color: rgba(255, 255, 255, 0.1);
    }

    .dt-card-title {
        font-size: 16px;
        font-weight: 800;
        color: var(--theme-text-dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .dt-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .dt-table thead th {
        background: var(--theme-subtle-blue);
        color: #475569;
        font-size: 11.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        padding: 14px 18px;
        border-bottom: 1.5px solid var(--theme-blue-border);
        white-space: nowrap;
    }

    body.dark-mode .dt-table thead th {
        background: #19253d;
        color: #cbd5e1;
        border-bottom-color: rgba(255, 255, 255, 0.1);
    }

    .dt-table tbody tr {
        transition: background-color 0.15s ease;
    }

    .dt-table tbody tr:hover {
        background-color: var(--theme-subtle-blue) !important;
    }

    .dt-table tbody td {
        padding: 14px 18px;
        font-size: 13.5px;
        color: var(--theme-text-dark);
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    body.dark-mode .dt-table tbody td {
        border-bottom-color: rgba(255, 255, 255, 0.05);
        color: #f8fafc;
    }

    /* Rank Badges */
    .rank-badge {
        width: 34px;
        height: 34px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 13px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
    }

    .rank-1 {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #b45309;
        border: 1.5px solid #fcd34d;
        box-shadow: 0 4px 10px rgba(245, 158, 11, 0.25);
    }

    .rank-2 {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        color: #475569;
        border: 1.5px solid #cbd5e1;
    }

    .rank-3 {
        background: linear-gradient(135deg, #ffedd5 0%, #fed7aa 100%);
        color: #c2410c;
        border: 1.5px solid #fdba74;
    }

    .rank-other {
        background: #eff6ff;
        color: #2563eb;
        border: 1.5px solid #bfdbfe;
    }

    .dt-student-avatar-wrap {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        overflow: hidden;
        flex-shrink: 0;
        position: relative;
        border: 1.5px solid var(--theme-blue-border);
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.08);
        background: #ffffff;
    }

    .dt-student-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .dt-student-mini-avatar {
        width: 100%;
        height: 100%;
        border-radius: 10px;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        color: #2563eb;
        font-weight: 800;
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<div class="dt-wrapper">
    <!-- Header Banner -->
    <div class="dt-header-banner anim-slide-up">
        <div class="dt-header-left">
            <div class="dt-header-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <h4 class="dt-header-title">Daily Task Student Reports & Analytics</h4>
                <p class="dt-header-subtitle">View student-wise daily task evaluations, performance scores & export in PDF / Excel</p>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3 flex-wrap dt-header-actions-wrap">
            <div class="dt-nav-tab-group">
                <a href="{{ route('school.daily-tasks.review') }}" class="dt-nav-tab-item">
                    <i class="fas fa-pen-to-square"></i> Review Entry
                </a>
                <a href="{{ route('school.daily-tasks.reports') }}" class="dt-nav-tab-item active">
                    <i class="fas fa-chart-pie"></i> Student Reports
                </a>
            </div>

            <!-- Export Buttons -->
            <div class="dt-export-btns d-flex align-items-center gap-2">
                <a href="{{ route('school.daily-tasks.reports.pdf', request()->query()) }}" target="_blank" class="dt-btn-pdf">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
                <a href="{{ route('school.daily-tasks.reports.excel', request()->query()) }}" class="dt-btn-excel">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards Grid -->
    <div class="dt-stats-grid anim-slide-up">
        <div class="dt-stat-box">
            <div>
                <div class="dt-stat-num">{{ $stats['total_evaluations'] }}</div>
                <div class="dt-stat-label">Total Task Evaluations</div>
            </div>
            <div class="dt-header-icon" style="background: #eff6ff; color: #2563eb; width: 46px; height: 46px; font-size: 19px;">
                <i class="fas fa-list-check"></i>
            </div>
        </div>
        <div class="dt-stat-box">
            <div>
                <div class="dt-stat-num d-flex align-items-center gap-2" style="color: #2563eb;">
                    {{ $stats['avg_rating'] }} <i class="fas fa-star text-warning fs-5"></i>
                </div>
                <div class="dt-stat-label">Average Star Rating</div>
            </div>
            <div class="dt-header-icon" style="background: #f0f7ff; color: #2563eb; width: 46px; height: 46px; font-size: 19px;">
                <i class="fas fa-star-half-stroke"></i>
            </div>
        </div>
        <div class="dt-stat-box">
            <div>
                <div class="dt-stat-num" style="color: #1d4ed8;">{{ $stats['total_5_stars'] }}</div>
                <div class="dt-stat-label">5-Star Perfect Ratings</div>
            </div>
            <div class="dt-header-icon" style="background: #dbeafe; color: #1e40af; width: 46px; height: 46px; font-size: 19px;">
                <i class="fas fa-award"></i>
            </div>
        </div>
        <div class="dt-stat-box">
            <div>
                <div class="dt-stat-num" style="color: #0284c7;">{{ $stats['unique_students'] }}</div>
                <div class="dt-stat-label">Students Reviewed</div>
            </div>
            <div class="dt-header-icon" style="background: #e0f2fe; color: #0284c7; width: 46px; height: 46px; font-size: 19px;">
                <i class="fas fa-user-graduate"></i>
            </div>
        </div>
    </div>

    <!-- Clean Unified Filter Panel -->
    <div class="dt-filter-panel anim-slide-up">
        <form method="GET" action="{{ route('school.daily-tasks.reports') }}">
            <div class="dt-filter-grid">
                
                <div class="dt-field-group">
                    <label class="dt-field-label"><i class="fas fa-calendar-day text-primary me-1"></i> From Date</label>
                    <div class="dt-field-input-wrap">
                        <input type="date" name="from_date" class="dt-field-input" value="{{ $fromDate }}">
                    </div>
                </div>

                <div class="dt-field-group">
                    <label class="dt-field-label"><i class="fas fa-calendar-day text-primary me-1"></i> To Date</label>
                    <div class="dt-field-input-wrap">
                        <input type="date" name="to_date" class="dt-field-input" value="{{ $toDate }}">
                    </div>
                </div>

                <div class="dt-field-group">
                    <label class="dt-field-label"><i class="fas fa-school text-primary me-1"></i> Class</label>
                    <div class="dt-field-input-wrap">
                        <select name="class_id" id="reportClass" class="dt-field-input" onchange="onClassFilterChange(this.value)">
                            <option value="all">All Classes</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ (string)$classId === (string)$c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="dt-field-group">
                    <label class="dt-field-label"><i class="fas fa-layer-group text-primary me-1"></i> Section</label>
                    <div class="dt-field-input-wrap">
                        <select name="section_id" id="reportSection" class="dt-field-input">
                            <option value="all">All Sections</option>
                        </select>
                    </div>
                </div>

                <div class="dt-field-group">
                    <label class="dt-field-label"><i class="fas fa-users-gear text-primary me-1"></i> Review Mode</label>
                    <div class="dt-field-input-wrap">
                        <select name="review_type" class="dt-field-input">
                            <option value="all" {{ $reviewType === 'all' ? 'selected' : '' }}>All Modes</option>
                            <option value="class_teacher" {{ $reviewType === 'class_teacher' ? 'selected' : '' }}>Class Teacher</option>
                            <option value="subject_teacher" {{ $reviewType === 'subject_teacher' ? 'selected' : '' }}>Subject Teacher</option>
                        </select>
                    </div>
                </div>

                <div class="dt-field-group d-flex flex-row gap-2">
                    <button type="submit" class="dt-btn-royal flex-grow-1">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('school.daily-tasks.reports') }}" class="btn btn-outline-primary d-flex align-items-center justify-content-center" style="height: 44px; width: 44px; border-radius: 12px; border: 1.5px solid var(--theme-blue-border);" title="Reset Filters">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                </div>

            </div>
        </form>
    </div>

    <!-- Student Performance Leaderboard (Top Ranking) -->
    @if($studentPerformances->isNotEmpty())
        <div class="dt-card-panel anim-slide-up mb-4">
            <div class="dt-card-header">
                <h6 class="dt-card-title">
                    <i class="fas fa-ranking-star text-warning"></i> Student Performance Summary Leaderboard
                </h6>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 fw-bold">
                    Based on {{ count($records) }} task evaluation entries
                </span>
            </div>
            <div class="table-responsive">
                <table class="dt-table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4" style="width: 80px;">Rank</th>
                            <th>Student Name</th>
                            <th>Roll No</th>
                            <th>Class & Section</th>
                            <th class="text-center">Total Tasks Logged</th>
                            <th class="text-center">Average Score / Rating</th>
                            <th class="text-end pe-4">Performance Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studentPerformances->take(10) as $index => $sp)
                            @php
                                $sInitials = strtoupper(substr($sp['name'] ?? 'ST', 0, 2));
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <span class="rank-badge {{ $index == 0 ? 'rank-1' : ($index == 1 ? 'rank-2' : ($index == 2 ? 'rank-3' : 'rank-other')) }}">
                                        @if($index == 0)
                                            <i class="fas fa-crown me-0.5"></i> 1
                                        @else
                                            #{{ $index + 1 }}
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="dt-student-avatar-wrap">
                                            @if(!empty($sp['photo_url']))
                                                <img src="{{ $sp['photo_url'] }}" alt="{{ $sp['name'] }}" class="dt-student-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="dt-student-mini-avatar" style="display: none;">{{ $sInitials }}</div>
                                            @else
                                                <div class="dt-student-mini-avatar">{{ $sInitials }}</div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="fw-bold fs-6">{{ $sp['name'] }}</div>
                                            <div class="text-muted small">Adm: {{ $sp['admission_no'] ?: '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-primary border border-primary-subtle px-2.5 py-1 fw-bold">
                                        Roll: {{ $sp['roll_no'] ?: '-' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary border px-2.5 py-1 fw-semibold">
                                        {{ $sp['class_section'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 fw-bold fs-6">
                                        {{ $sp['total_tasks'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-1.5 bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1 rounded-pill fw-bold fs-6">
                                        {{ $sp['avg_rating'] }} <i class="fas fa-star text-warning"></i>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    @if($sp['avg_rating'] >= 4.5)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill fw-bold">
                                            <i class="fas fa-circle-check me-1"></i> Outstanding
                                        </span>
                                    @elseif($sp['avg_rating'] >= 3.5)
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1.5 rounded-pill fw-bold">
                                            <i class="fas fa-star me-1"></i> Good Performance
                                        </span>
                                    @elseif($sp['avg_rating'] >= 2.5)
                                        <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1.5 rounded-pill fw-bold">
                                            <i class="fas fa-minus me-1"></i> Average
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1.5 rounded-pill fw-bold">
                                            <i class="fas fa-triangle-exclamation me-1"></i> Needs Attention
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Detailed Student Evaluation Logs Table -->
    <div class="dt-card-panel anim-slide-up">
        <div class="dt-card-header">
            <h6 class="dt-card-title">
                <i class="fas fa-list-check text-primary"></i> Detailed Daily Task Evaluations
            </h6>
            <div class="badge bg-light text-primary border border-primary-subtle px-3 py-1.5 fw-bold">
                Showing {{ $records->firstItem() ?? 0 }} - {{ $records->lastItem() ?? 0 }} of {{ $records->total() }} evaluations
            </div>
        </div>

        <div class="table-responsive">
            <table class="dt-table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Date</th>
                        <th>Student Name & Roll</th>
                        <th>Class & Section</th>
                        <th>Review Mode</th>
                        <th>Task / Criteria</th>
                        <th class="text-center">Rating / Result</th>
                        <th>Teacher Remarks</th>
                        <th class="text-end pe-4">Evaluated By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $rec)
                        @php
                            $stInitials = strtoupper(substr($rec->student_first_name ?? 'ST', 0, 1) . substr($rec->student_last_name ?? '', 0, 1)) ?: 'ST';
                        @endphp
                        <tr>
                            <td class="ps-4 text-nowrap">
                                <span class="badge bg-light text-primary border border-primary-subtle px-2.5 py-1.5 fw-bold">
                                    <i class="fas fa-calendar-day me-1"></i> {{ date('d M Y', strtotime($rec->date)) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="dt-student-avatar-wrap">
                                        @if(!empty($rec->photo_url))
                                            <img src="{{ $rec->photo_url }}" alt="{{ $rec->student_first_name }}" class="dt-student-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="dt-student-mini-avatar" style="display: none;">{{ $stInitials }}</div>
                                        @else
                                            <div class="dt-student-mini-avatar">{{ $stInitials }}</div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold fs-6">{{ $rec->student_first_name }} {{ $rec->student_last_name }}</div>
                                        <div class="text-muted small">
                                            <span class="badge bg-light text-secondary border me-1">Roll: {{ $rec->student_roll_no ?: '-' }}</span>
                                            <span class="badge bg-light text-secondary border">Adm: {{ $rec->student_admission_no ?: '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 fw-bold">
                                    {{ $rec->class_name }} - {{ $rec->section_name }}
                                </span>
                            </td>
                            <td>
                                @if($rec->review_type === 'class_teacher')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 fw-bold">
                                        <i class="fas fa-chalkboard-user me-1"></i> Class Teacher
                                    </span>
                                @else
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2.5 py-1 fw-bold">
                                        <i class="fas fa-book-bookmark me-1"></i> Subject ({{ $rec->subject_name ?: 'General' }})
                                    </span>
                                @endif
                            </td>
                            <td style="max-width: 260px;">
                                <div class="fw-bold text-dark">{{ $rec->question_text }}</div>
                                @if($rec->head_name)
                                    <div class="mt-1">
                                        <span class="badge bg-light text-primary border px-2 py-0.5" style="font-size: 11px;">
                                            <i class="fas fa-tag text-primary me-1"></i> {{ $rec->head_name }}
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="text-center text-nowrap">
                                @if($rec->rating)
                                    <div class="d-inline-flex align-items-center gap-1 bg-light border border-primary-subtle px-2.5 py-1 rounded-pill text-warning fw-bold">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $rec->rating ? 'text-warning' : 'text-muted' }}" style="font-size: 12px; opacity: {{ $i <= $rec->rating ? 1 : 0.25 }};"></i>
                                        @endfor
                                        <span class="ms-1 text-primary fw-bold small">({{ $rec->rating }}/5)</span>
                                    </div>
                                @elseif($rec->score !== null)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-6 fw-bold px-3 py-1">
                                        {{ $rec->score }} / {{ $rec->max_score ?? 10 }}
                                    </span>
                                @elseif($rec->status_option)
                                    <span class="badge bg-light text-primary border border-primary-subtle px-3 py-1.5 fw-bold">
                                        {{ $rec->status_option }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td style="max-width: 240px;">
                                @if($rec->remarks)
                                    <div class="p-2 rounded-3 bg-light border text-dark small">
                                        <i class="fas fa-comment-dots text-primary me-1"></i> {{ $rec->remarks }}
                                    </div>
                                @elseif($rec->review_overall_remarks)
                                    <div class="p-2 rounded-3 bg-light border text-muted small">
                                        <i class="fas fa-quote-left text-muted me-1"></i> {{ $rec->review_overall_remarks }}
                                    </div>
                                @else
                                    <span class="text-muted small"><em>No remarks</em></span>
                                @endif
                            </td>
                            <td class="text-end pe-4 text-nowrap">
                                <span class="badge bg-light text-primary border border-primary-subtle px-2.5 py-1.5 fw-bold">
                                    <i class="fas fa-user-tie me-1"></i> {{ trim(($rec->teacher_first_name ?? '') . ' ' . ($rec->teacher_last_name ?? '')) ?: 'Staff' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="py-4">
                                    <div class="mb-3" style="font-size: 48px; color: var(--theme-primary-blue); opacity: 0.5;">
                                        <i class="fas fa-chart-pie"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1 fs-5">No Daily Task Evaluation Records Found</h6>
                                    <p class="text-muted mb-3" style="font-size: 13.5px;">No evaluation records match the selected date range and filter criteria.</p>
                                    <a href="{{ route('school.daily-tasks.review') }}" class="dt-btn-royal">
                                        <i class="fas fa-pen-to-square"></i> Record New Review
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($records->hasPages())
            <div class="p-3 border-top d-flex justify-content-end" style="border-color: var(--theme-blue-border) !important;">
                {{ $records->links() }}
            </div>
        @endif
    </div>
</div>

<script>
    const classesData = @json($classes);
    const selectedSectionId = '{{ $sectionId }}';

    function onClassFilterChange(classId) {
        const secSelect = document.getElementById('reportSection');
        secSelect.innerHTML = '<option value="all">All Sections</option>';

        if (!classId || classId === 'all') return;

        const cls = classesData.find(c => String(c.id) === String(classId));
        if (cls && cls.sections) {
            cls.sections.forEach(sec => {
                const opt = document.createElement('option');
                opt.value = sec.id;
                opt.textContent = sec.name;
                if (selectedSectionId && String(sec.id) === String(selectedSectionId)) {
                    opt.selected = true;
                }
                secSelect.appendChild(opt);
            });
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const currentClassId = document.getElementById('reportClass').value;
        if (currentClassId && currentClassId !== 'all') {
            onClassFilterChange(currentClassId);
        }
    });
</script>
@endsection
