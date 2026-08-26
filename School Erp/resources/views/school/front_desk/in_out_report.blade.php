@extends('layouts.app')

@section('title', 'Visitor In & Out Report - Front Desk')
@section('page-title', 'Visitor In & Out Report')

@section('content')
<style>
    :root {
        --theme-royal-blue: #0038b8;
        --theme-royal-blue-hover: #002d99;
        --theme-blue-dark: #002366;
        --theme-blue-header: #002c77;
        --theme-blue-subtle: #eff6ff;
        --theme-blue-ice: #f4f8ff;
        --theme-blue-border: #bfdbfe;
        --theme-border-dash: #cbd5e1;
        --theme-text-main: #1e293b;
        --theme-text-muted: #64748b;
    }

    .report-page-container {
        width: 100%;
        padding: 0 4px 30px 4px;
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    /* Top Breadcrumb & Page Title */
    .report-top-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 20px;
    }

    .report-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--theme-text-muted);
        font-weight: 500;
        margin-bottom: 4px;
    }

    .report-breadcrumb a {
        color: var(--theme-text-muted);
        text-decoration: none;
        transition: color 0.15s ease;
    }

    .report-breadcrumb a:hover {
        color: var(--theme-royal-blue);
    }

    .report-breadcrumb .sep {
        font-size: 10px;
        opacity: 0.5;
    }

    .report-page-title {
        font-size: 24px;
        font-weight: 800;
        color: var(--theme-text-main);
        letter-spacing: -0.4px;
        margin: 0;
    }

    /* Top Quick Stats Badge Pills */
    .report-stats-strip {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .stat-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 12.5px;
        font-weight: 700;
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }

    .stat-pill.primary {
        border-color: #bfdbfe;
        background: #eff6ff;
        color: var(--theme-royal-blue);
    }

    .stat-pill.success {
        border-color: #bbf7d0;
        background: #f0fdf4;
        color: #15803d;
    }

    .stat-pill.warning {
        border-color: #fed7aa;
        background: #fff7ed;
        color: #c2410c;
    }

    .stat-pill .count {
        font-size: 14px;
        font-weight: 800;
    }

    /* =========================================================
       FILTER SECTION (EXACT MATCH TO IMAGE 1)
       ========================================================= */
    .filter-card-container {
        background: #ffffff;
        border: 1.5px dashed var(--theme-border-dash);
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
    }

    .filter-form-grid {
        display: grid;
        grid-template-columns: 1.2fr 1.2fr 1.5fr 2fr auto;
        gap: 16px;
        align-items: flex-end;
    }

    @media (max-width: 1200px) {
        .filter-form-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 640px) {
        .filter-form-grid {
            grid-template-columns: 1fr;
        }
    }

    .filter-field-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .filter-label {
        font-size: 11px;
        font-weight: 800;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin: 0;
    }

    .filter-input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }

    .filter-input-wrap input,
    .filter-input-wrap select {
        width: 100%;
        height: 42px;
        padding: 8px 14px;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 600;
        color: var(--theme-text-main);
        background-color: #ffffff;
        transition: all 0.2s ease;
        outline: none;
    }

    .filter-input-wrap input::placeholder {
        color: #94a3b8;
        font-weight: 500;
        text-transform: uppercase;
        font-size: 12px;
    }

    .filter-input-wrap input:focus,
    .filter-input-wrap select:focus {
        border-color: var(--theme-royal-blue);
        box-shadow: 0 0 0 3px rgba(0, 56, 184, 0.12);
    }

    /* Date Input specific */
    .filter-input-wrap input[type="date"] {
        font-family: inherit;
        text-transform: uppercase;
    }

    /* Select Dropdown */
    .filter-input-wrap select {
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2.5' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 14px 14px;
        padding-right: 36px;
        cursor: pointer;
    }

    /* Button Filter Logs (Blue & Bold) */
    .btn-filter-logs {
        height: 42px;
        padding: 0 26px;
        background: var(--theme-royal-blue);
        color: #ffffff;
        border: none;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 800;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 4px 12px rgba(0, 56, 184, 0.22);
        white-space: nowrap;
    }

    .btn-filter-logs:hover {
        background: var(--theme-royal-blue-hover);
        box-shadow: 0 6px 16px rgba(0, 56, 184, 0.35);
        transform: translateY(-1px);
        color: #ffffff;
    }

    .btn-filter-logs:active {
        transform: translateY(0);
    }

    .btn-reset-filters {
        height: 42px;
        padding: 0 16px;
        background: #ffffff;
        color: #64748b;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-reset-filters:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #1e293b;
    }

    .filter-actions-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* =========================================================
       REPORT TABLE CARD (EXACT MATCH TO IMAGE 1 & 2)
       ========================================================= */
    .report-table-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 56, 184, 0.05), 0 1px 3px rgba(0,0,0,0.03);
        border: 1.5px solid #e2e8f0;
        overflow: hidden;
    }

    .table-toolbar-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 20px;
        background: #ffffff;
        border-bottom: 1px solid #edf2f7;
        flex-wrap: wrap;
        gap: 12px;
    }

    .table-results-counter {
        font-size: 13px;
        font-weight: 700;
        color: #475569;
    }

    .table-results-counter strong {
        color: var(--theme-royal-blue);
    }

    .table-quick-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-table-tool {
        padding: 6px 14px;
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .btn-table-tool:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: var(--theme-royal-blue);
    }

    /* Report Responsive Table */
    .report-table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    .visitor-inout-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        text-align: left;
    }

    /* Royal Blue Table Header (Image 1 + Image 2) */
    .visitor-inout-table thead tr {
        background: var(--theme-blue-header);
        background: linear-gradient(180deg, #002d72 0%, #002366 100%);
    }

    .visitor-inout-table thead th {
        color: #ffffff;
        font-size: 11.5px;
        font-weight: 800;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        padding: 13px 16px;
        border: none;
        white-space: nowrap;
    }

    .visitor-inout-table thead th.text-center {
        text-align: center;
    }

    /* Table Body Rows */
    .visitor-inout-table tbody tr {
        background: #ffffff;
        transition: background 0.15s ease;
        border-bottom: 1px solid #f1f5f9;
    }

    .visitor-inout-table tbody tr:nth-child(even) {
        background: #fafcff;
    }

    .visitor-inout-table tbody tr:hover {
        background: var(--theme-blue-ice);
    }

    .visitor-inout-table tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        font-size: 13px;
        color: var(--theme-text-main);
        border-bottom: 1px solid #edf2f7;
    }

    /* Columns Specific Styling */
    .col-sno {
        width: 55px;
        font-weight: 700;
        color: #64748b;
        text-align: center;
    }

    .col-passno {
        width: 190px;
        font-family: 'Plus Jakarta Sans', monospace;
        font-weight: 800;
        font-size: 12.5px;
        letter-spacing: 0.2px;
        color: #0f172a;
        white-space: nowrap;
    }

    /* Visitor Details Column */
    .col-visitor-details {
        min-width: 200px;
    }

    .visitor-name-title {
        font-weight: 800;
        color: #0f172a;
        font-size: 13.5px;
        margin-bottom: 2px;
        line-height: 1.25;
    }

    .visitor-phone-sub {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 4px;
        letter-spacing: 0.2px;
    }

    /* Category Pill Badge (Dark/Navy pill like Image 1) */
    .visitor-category-badge {
        display: inline-block;
        background: #334155;
        color: #ffffff;
        font-size: 9.5px;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        padding: 2px 8px;
        border-radius: 4px;
        line-height: 1.3;
    }

    .visitor-category-badge.parent { background: #3b5998; }
    .visitor-category-badge.vendor { background: #475569; }
    .visitor-category-badge.interview { background: #2563eb; }
    .visitor-category-badge.guest { background: #0891b2; }

    /* Whom To Meet Column */
    .col-whom-to-meet {
        min-width: 190px;
    }

    .host-target-name {
        font-weight: 700;
        color: #1e293b;
        font-size: 13px;
        margin-bottom: 2px;
    }

    .host-meta-details {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    /* Check-In & Check-Out Columns */
    .col-time-stamp {
        min-width: 160px;
        font-size: 12.5px;
        font-weight: 600;
        color: #334155;
        white-space: nowrap;
    }

    .time-dash-placeholder {
        color: #94a3b8;
        font-weight: 700;
        letter-spacing: 1px;
    }

    /* Status Badges (IN vs OUT) */
    .col-status-cell {
        width: 90px;
        text-align: center;
    }

    .badge-status-in {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 50px;
        padding: 3px 10px;
        background: #e0f2fe;
        color: #0284c7;
        border: 1px solid #bae6fd;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .badge-status-out {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 50px;
        padding: 3px 10px;
        background: #dcfce7;
        color: #16a34a;
        border: 1px solid #bbf7d0;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    /* Action Button (PRINT outline pill) */
    .col-action-cell {
        width: 100px;
        text-align: center;
    }

    .btn-action-print {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 3px 14px;
        background: #ffffff;
        color: #0f172a;
        border: 1.5px solid #1e293b;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        text-decoration: none;
        transition: all 0.15s ease;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }

    .btn-action-print:hover {
        background: var(--theme-royal-blue);
        border-color: var(--theme-royal-blue);
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(0, 56, 184, 0.25);
        transform: translateY(-1px);
    }

    /* Empty State */
    .empty-report-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-icon-wrap {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: var(--theme-blue-subtle);
        color: var(--theme-royal-blue);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin-bottom: 16px;
        border: 1.5px solid var(--theme-blue-border);
    }

    .empty-title {
        font-size: 17px;
        font-weight: 800;
        color: var(--theme-text-main);
        margin-bottom: 6px;
    }

    .empty-subtitle {
        font-size: 13.5px;
        color: var(--theme-text-muted);
        max-width: 420px;
        margin: 0 auto 18px auto;
    }

    /* Table Pagination */
    .report-pagination-wrap {
        padding: 16px 20px;
        background: #ffffff;
        border-top: 1px solid #edf2f7;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    /* Print media styles */
    @media print {
        .report-top-header,
        .filter-card-container,
        .table-toolbar-bar,
        .report-pagination-wrap,
        .col-action-cell {
            display: none !important;
        }

        .report-table-card {
            border: none !important;
            box-shadow: none !important;
        }

        .visitor-inout-table thead tr {
            background: #002d72 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .visitor-inout-table thead th {
            color: #ffffff !important;
        }
    }
</style>

<div class="container-fluid report-page-container px-0">
    
    <!-- Top Header & Breadcrumbs -->
    <div class="report-top-header">
        <div>
            <h4 class="report-page-title">Visitor In & Out Logs Report</h4>
        </div>

        <!-- Quick Summary Strip -->
        <div class="report-stats-strip">
            <div class="stat-pill primary" title="Total visitor records logged">
                <i class="fas fa-id-card-clip"></i>
                <span>Total:</span>
                <span class="count">{{ number_format($totalCount ?? 0) }}</span>
            </div>
            <div class="stat-pill warning" title="Visitors currently inside campus">
                <i class="fas fa-door-open"></i>
                <span>Inside Campus:</span>
                <span class="count">{{ number_format($insideCount ?? 0) }}</span>
            </div>
            <div class="stat-pill success" title="Visitors checked out">
                <i class="fas fa-person-walking-dashed-line-arrow-right"></i>
                <span>Checked Out:</span>
                <span class="count">{{ number_format($checkedOutCount ?? 0) }}</span>
            </div>
        </div>
    </div>

    <!-- =========================================================
         TOP FILTER SECTION (EXACT DESIGN MATCH TO IMAGE 1)
         ========================================================= -->
    <div class="filter-card-container">
        <form method="GET" action="{{ route('school.front-desk.in-out-report') }}" id="filterLogsForm">
            <div class="filter-form-grid">
                
                <!-- 1. FROM DATE -->
                <div class="filter-field-group">
                    <label class="filter-label" for="from_date">FROM DATE</label>
                    <div class="filter-input-wrap">
                        <input type="date" 
                               id="from_date" 
                               name="from_date" 
                               value="{{ request('from_date') }}" 
                               placeholder="dd-mm-yyyy">
                    </div>
                </div>

                <!-- 2. TO DATE -->
                <div class="filter-field-group">
                    <label class="filter-label" for="to_date">TO DATE</label>
                    <div class="filter-input-wrap">
                        <input type="date" 
                               id="to_date" 
                               name="to_date" 
                               value="{{ request('to_date') }}" 
                               placeholder="dd-mm-yyyy">
                    </div>
                </div>

                <!-- 3. VISITOR TYPE -->
                <div class="filter-field-group">
                    <label class="filter-label" for="visitor_type">VISITOR TYPE</label>
                    <div class="filter-input-wrap">
                        <select id="visitor_type" name="visitor_type">
                            <option value="">ALL CATEGORIES</option>
                            @foreach($visitorTypes as $type)
                                <option value="{{ $type }}" {{ request('visitor_type') == $type ? 'selected' : '' }}>
                                    {{ strtoupper($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- 4. SEARCH (NAME/PASS/MOBILE) -->
                <div class="filter-field-group">
                    <label class="filter-label" for="search">SEARCH (NAME/PASS/MOBILE)</label>
                    <div class="filter-input-wrap">
                        <input type="text" 
                               id="search" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="KEYWORD ENTRY..">
                    </div>
                </div>

                <!-- 5. FILTER LOGS BUTTON -->
                <div class="filter-actions-wrap">
                    <button type="submit" class="btn-filter-logs" id="btnFilterLogs">
                        <i class="fas fa-filter"></i>
                        <span>FILTER LOGS</span>
                    </button>

                    @if(request()->hasAny(['from_date', 'to_date', 'visitor_type', 'search', 'status']))
                        <a href="{{ route('school.front-desk.in-out-report') }}" class="btn-reset-filters" title="Reset Filters">
                            <i class="fas fa-rotate-left"></i>
                        </a>
                    @endif
                </div>

            </div>
        </form>
    </div>

    <!-- =========================================================
         DATA TABLE CARD (EXACT DESIGN MATCH TO IMAGE 1 & 2)
         ========================================================= -->
    <div class="report-table-card">
        
        <!-- Table Toolbar -->
        <div class="table-toolbar-bar">
            <div class="table-results-counter">
                Showing <strong>{{ $visitors->firstItem() ?? 0 }}</strong> - <strong>{{ $visitors->lastItem() ?? 0 }}</strong> of <strong>{{ $visitors->total() ?? 0 }}</strong> Visitor Logs
            </div>

            <div class="table-quick-actions">
                <button type="button" class="btn-table-tool" onclick="window.print();" title="Print Current View">
                    <i class="fas fa-print"></i>
                    <span>Print Report</span>
                </button>
                <button type="button" class="btn-table-tool" onclick="exportTableToCSV('visitor_in_out_report.csv');" title="Export as CSV">
                    <i class="fas fa-file-csv text-success"></i>
                    <span>Export CSV</span>
                </button>
            </div>
        </div>

        <!-- Table View -->
        <div class="report-table-responsive">
            <table class="visitor-inout-table" id="visitorReportTable">
                <thead>
                    <tr>
                        <th class="col-sno text-center">S.NO</th>
                        <th class="col-passno">PASS NO</th>
                        <th class="col-visitor-details">VISITOR DETAILS</th>
                        <th class="col-whom-to-meet">WHOM TO MEET</th>
                        <th class="col-time-stamp">CHECK-IN</th>
                        <th class="col-time-stamp">CHECK-OUT</th>
                        <th class="col-status-cell text-center">STATUS</th>
                        <th class="col-action-cell text-center">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($visitors as $index => $visitor)
                        @php
                            // Calculate continuous serial number
                            $sNo = ($visitors->currentPage() - 1) * $visitors->perPage() + ($index + 1);
                            
                            // Format Pass Number with clean spacing
                            $cleanPassNo = $visitor->pass_number;
                            if (strpos($cleanPassNo, '-') !== false) {
                                $passParts = explode('-', $cleanPassNo);
                                $formattedPassNo = implode(' - ', array_map('trim', $passParts));
                            } else {
                                $formattedPassNo = $cleanPassNo;
                            }

                            // Extract short category badge
                            $typeUpper = strtoupper($visitor->visitor_type);
                            $badgeClass = 'visitor';
                            $badgeText = 'VISITOR';

                            if (str_contains($typeUpper, 'PARENT')) {
                                $badgeClass = 'parent';
                                $badgeText = 'PARENT';
                            } elseif (str_contains($typeUpper, 'VENDOR')) {
                                $badgeClass = 'vendor';
                                $badgeText = 'VENDOR';
                            } elseif (str_contains($typeUpper, 'APPLICANT') || str_contains($typeUpper, 'INTERVIEW')) {
                                $badgeClass = 'interview';
                                $badgeText = 'INTERVIEW';
                            } elseif (str_contains($typeUpper, 'GUEST')) {
                                $badgeClass = 'guest';
                                $badgeText = 'GUEST';
                            } else {
                                $badgeText = explode('/', $typeUpper)[0];
                                $badgeText = trim(explode(' ', $badgeText)[0]);
                            }

                            // Format Host / Whom to meet subtitle
                            $whomType = $visitor->whom_to_meet_type ?: 'STAFF';
                            $whomShort = strtoupper(explode('/', $whomType)[0]);
                            $whomShort = trim($whomShort);

                            // Extract Gate Number
                            $gateString = $visitor->security_gate ?: '1';
                            preg_match('/\d+/', $gateString, $gateMatches);
                            $gateNo = !empty($gateMatches) ? $gateMatches[0] : '1';

                            // Format check-in and check-out dates
                            $checkInFormatted = $visitor->check_in_at 
                                ? $visitor->check_in_at->format('d-M-y h:i A') 
                                : ($visitor->created_at ? $visitor->created_at->format('d-M-y h:i A') : '---');

                            $checkOutFormatted = $visitor->check_out_at 
                                ? $visitor->check_out_at->format('d-M-y h:i A') 
                                : null;

                            $isInside = ($visitor->status === 'checked_in' || empty($visitor->check_out_at));
                        @endphp
                        <tr>
                            <!-- S.NO -->
                            <td class="col-sno text-center">{{ $sNo }}</td>

                            <!-- PASS NO -->
                            <td class="col-passno">{{ $formattedPassNo }}</td>

                            <!-- VISITOR DETAILS -->
                            <td class="col-visitor-details">
                                <div class="visitor-name-title">{{ $visitor->full_name }}</div>
                                <div class="visitor-phone-sub">{{ $visitor->mobile_number }}</div>
                                <div>
                                    <span class="visitor-category-badge {{ $badgeClass }}">{{ $badgeText }}</span>
                                </div>
                            </td>

                            <!-- WHOM TO MEET -->
                            <td class="col-whom-to-meet">
                                <div class="host-target-name">
                                    {{ $visitor->host_name ?: ($visitor->whom_to_meet_type ?: 'Staff Member') }}
                                </div>
                                <div class="host-meta-details">
                                    {{ $whomShort }} | GATE: {{ $gateNo }}
                                </div>
                            </td>

                            <!-- CHECK-IN -->
                            <td class="col-time-stamp">
                                {{ $checkInFormatted }}
                            </td>

                            <!-- CHECK-OUT -->
                            <td class="col-time-stamp">
                                @if($checkOutFormatted)
                                    {{ $checkOutFormatted }}
                                @else
                                    <span class="time-dash-placeholder">---</span>
                                @endif
                            </td>

                            <!-- STATUS -->
                            <td class="col-status-cell text-center">
                                @if($isInside)
                                    <span class="badge-status-in">IN</span>
                                @else
                                    <span class="badge-status-out">OUT</span>
                                @endif
                            </td>

                            <!-- ACTION -->
                            <td class="col-action-cell text-center">
                                <a href="{{ route('school.front-desk.visitor.print', $visitor->id) }}" 
                                   target="_blank" 
                                   class="btn-action-print" 
                                   title="Print Visitor Pass">
                                    PRINT
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-report-state">
                                    <div class="empty-icon-wrap">
                                        <i class="fas fa-clipboard-list"></i>
                                    </div>
                                    <h5 class="empty-title">No Visitor In/Out Records Found</h5>
                                    <p class="empty-subtitle">
                                        No visitor check-in or check-out logs match your current filter parameters. Try adjusting the date range or keywords.
                                    </p>
                                    @if(request()->hasAny(['from_date', 'to_date', 'visitor_type', 'search', 'status']))
                                        <a href="{{ route('school.front-desk.in-out-report') }}" class="btn-filter-logs" style="text-decoration: none;">
                                            <i class="fas fa-rotate-left"></i>
                                            <span>Clear All Filters</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Pagination Footer -->
        @if($visitors->hasPages())
            <div class="report-pagination-wrap">
                <div class="text-muted small" style="font-weight: 600;">
                    Showing {{ $visitors->firstItem() }} to {{ $visitors->lastItem() }} of {{ $visitors->total() }} results
                </div>
                <div>
                    {{ $visitors->links() }}
                </div>
            </div>
        @endif

    </div>

</div>

<!-- Simple CSV Export Script -->
<script>
function exportTableToCSV(filename) {
    var csv = [];
    var rows = document.querySelectorAll("#visitorReportTable tr");
    
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll("td, th");
        
        // Skip empty placeholder row if no records
        if (cols.length === 1 && cols[0].getAttribute("colspan")) continue;

        // Extract up to 7 columns (exclude the action column)
        var colLimit = cols.length === 8 ? 7 : cols.length;
        for (var j = 0; j < colLimit; j++) {
            var data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, " ").trim();
            data = data.replace(/"/g, '""');
            row.push('"' + data + '"');
        }
        
        csv.push(row.join(","));
    }

    var csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
    var downloadLink = document.createElement("a");
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
</script>
@endsection
