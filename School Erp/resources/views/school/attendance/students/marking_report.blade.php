@extends('layouts.app')

@section('title', 'Student Attendance Marking Report')

@section('styles')
<style>
    /* Premium visual overrides */
    .report-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 12px;
        box-shadow: var(--shadow);
        overflow: hidden;
        margin-top: 18px;
    }
    
    .table-container-scroll {
        overflow-x: auto;
        width: 100%;
    }

    table.tbl-marking {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        color: var(--t1);
    }

    table.tbl-marking th {
        background: #0a4b5c;
        color: #ffffff;
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 14px;
        border-bottom: 2px solid var(--border);
        text-align: left;
        white-space: nowrap;
    }

    table.tbl-marking td {
        padding: 12px 14px;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }

    table.tbl-marking tr:hover td {
        background: rgba(10, 75, 92, 0.03);
    }

    /* iOS style switch toggle */
    .switch-container {
        display: inline-flex;
        align-items: center;
        cursor: pointer;
        user-select: none;
    }
    
    .switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 22px;
        margin-left: 8px;
    }
    
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e1;
        transition: .25s ease;
        border-radius: 22px;
    }
    
    .slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .25s ease;
        border-radius: 50%;
    }
    
    input:checked + .slider {
        background-color: #10b981;
    }
    
    input:checked + .slider:before {
        transform: translateX(22px);
    }

    /* Text highlight colors */
    .text-defaulter {
        color: var(--red);
        font-weight: 700;
    }

    .text-marked {
        color: var(--green);
        font-weight: 700;
    }

    .info-tooltip {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 16px;
        height: 16px;
        background: #e0f2fe;
        border-radius: 50%;
        font-size: 9px;
        color: #0369a1;
        cursor: pointer;
        border: 1.5px solid #7dd3fc;
        margin-left: 5px;
        flex-shrink: 0;
        transition: background 0.2s, border-color 0.2s;
        user-select: none;
    }

    .info-tooltip:hover,
    .info-tooltip.active {
        background: #0ea5e9;
        border-color: #0284c7;
        color: #fff;
    }

    /* Popover description box */
    .info-popover {
        display: none;
        position: fixed;
        z-index: 9999;
        background: #1e293b;
        color: #e2e8f0;
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 12.5px;
        line-height: 1.55;
        max-width: 280px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.28);
        pointer-events: none;
    }
    .info-popover.visible {
        display: block;
        animation: popFadeIn 0.18s ease;
    }
    .info-popover::before {
        content: '';
        position: absolute;
        top: -6px;
        left: 18px;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-bottom: 6px solid #1e293b;
    }
    .info-popover-title {
        font-weight: 700;
        font-size: 12px;
        color: #7dd3fc;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    @keyframes popFadeIn {
        from { opacity: 0; transform: translateY(-4px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Premium inputs styling */
    .input-icon-wrap {
        position: relative;
        width: 100%;
    }

    .input-icon-wrap input {
        padding-right: 32px !important;
    }

    .input-icon-wrap i, .input-icon-wrap span {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--t2);
        pointer-events: none;
    }

    /* Modal Styling */
    .stub-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.4);
        backdrop-filter: blur(4px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }

    .stub-modal-content {
        background: #fff;
        border-radius: 12px;
        width: 90%;
        max-width: 500px;
        box-shadow: var(--shadow-lg);
        overflow: hidden;
        animation: modalFadeIn 0.3s ease;
    }

    @keyframes modalFadeIn {
        from { transform: scale(0.95); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    .stub-modal-hdr {
        background: var(--navy);
        padding: 16px 20px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .stub-modal-hdr h3 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .stub-modal-close {
        background: none;
        border: none;
        color: rgba(255,255,255,0.7);
        font-size: 18px;
        cursor: pointer;
    }

    .stub-modal-close:hover {
        color: #fff;
    }

    /* ── Toolbar Panel System ─────────────────────────────────────── */
    .tb-wrap {
        position: relative;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .tb-btn {
        background: none;
        border: none;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        opacity: 0.8;
        outline: none;
        padding: 6px 10px;
        border-radius: 6px;
        transition: background 0.15s, opacity 0.15s;
        letter-spacing: 0.3px;
    }
    .tb-btn:hover, .tb-btn.open {
        background: rgba(255,255,255,0.12);
        opacity: 1;
    }
    .tb-btn.open {
        background: rgba(255,255,255,0.18);
    }

    .tb-panel {
        display: none;
        position: absolute;
        top: calc(100% + 10px);
        left: 0;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.15);
        z-index: 500;
        min-width: 220px;
        animation: panelDrop 0.18s ease;
        overflow: hidden;
    }
    .tb-panel.open { display: block; }
    @keyframes panelDrop {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .tb-panel-hdr {
        background: #0a4b5c;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.4px;
        padding: 10px 14px;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        gap: 7px;
    }

    /* ── COLUMNS panel ── */
    .col-toggle-list {
        padding: 8px 0;
        max-height: 240px;
        overflow-y: auto;
    }
    .col-toggle-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 7px 14px;
        cursor: pointer;
        transition: background 0.12s;
        font-size: 12.5px;
        color: #1e293b;
        user-select: none;
    }
    .col-toggle-item:hover { background: #f0f9ff; }
    .col-toggle-item input[type=checkbox] {
        width: 15px; height: 15px;
        accent-color: #0a4b5c;
        cursor: pointer;
        flex-shrink: 0;
    }

    /* ── FILTERS panel ── */
    .filters-panel-body {
        padding: 12px 14px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .filter-input-row label {
        font-size: 10px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        display: block;
        margin-bottom: 4px;
    }
    .filter-input-row input {
        width: 100%;
        border: 1.5px solid #e2e8f0;
        border-radius: 7px;
        padding: 7px 10px;
        font-size: 12.5px;
        outline: none;
        color: #1e293b;
        transition: border-color 0.15s;
        box-sizing: border-box;
    }
    .filter-input-row input:focus { border-color: #0a4b5c; }
    .filter-reset-btn {
        align-self: flex-end;
        background: none;
        border: 1.5px solid #e2e8f0;
        border-radius: 6px;
        padding: 5px 12px;
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        cursor: pointer;
        transition: all 0.15s;
    }
    .filter-reset-btn:hover { border-color: #0a4b5c; color: #0a4b5c; background: #f0f9ff; }

    /* ── DENSITY panel ── */
    .density-option {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        cursor: pointer;
        transition: background 0.12s;
        font-size: 12.5px;
        color: #1e293b;
        user-select: none;
        border-bottom: 1px solid #f1f5f9;
    }
    .density-option:last-child { border-bottom: none; }
    .density-option:hover { background: #f0f9ff; }
    .density-option.active { background: #e0f2fe; color: #0a4b5c; font-weight: 700; }
    .density-icon {
        width: 30px;
        display: flex;
        flex-direction: column;
        gap: 2px;
        flex-shrink: 0;
    }
    .density-icon span {
        display: block;
        background: currentColor;
        border-radius: 1px;
        width: 100%;
        opacity: 0.7;
    }
    /* active row count indicator on toolbar */
    .tb-filter-count {
        background: #ef4444;
        color: #fff;
        border-radius: 10px;
        font-size: 9px;
        font-weight: 800;
        padding: 1px 5px;
        min-width: 14px;
        text-align: center;
        display: none;
    }
    .tb-filter-count.visible { display: inline-block; }
</style>
@endsection

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1>
            <i class="fas fa-chart-line" style="color:var(--gold); margin-right:8px;"></i>
            Student Attendance Marking Report
        </h1>
        <p style="color:var(--t2); font-size:12px;">Staff Management</p>
    </div>
    <div class="page-hdr-right">
        <button onclick="openStubModal()" class="btn" style="background:#b27d14; color:#fff; border-radius:6px; font-weight:700; font-size:12px; padding:10px 18px; border:none; cursor:pointer; box-shadow:0 2px 4px rgba(178,125,20,0.2);">
            SUBJECT-WISE ATTENDANCE NOT MARKED REPORT
        </button>
    </div>
</div>

@if(session('warning'))
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        <span>{{ session('warning') }}</span>
    </div>
@endif

<!-- Filters -->
<div class="card" style="margin-bottom:18px;">
    <div class="card-body" style="padding:20px 24px;">
        <form action="{{ route('school.attendance.students.marking-report') }}" method="GET" id="reportFilterForm">
            <!-- Row 1: Academic Year & Date Selectors -->
            <div class="grid-3" style="margin-bottom:14px;">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" style="font-weight:700; font-size:11px;">Academic Year *</label>
                    <select name="academic_session_id" class="form-control" required onchange="this.form.submit()">
                        @foreach($academicSessions as $ses)
                            <option value="{{ $ses->id }}" {{ $sessionId == $ses->id ? 'selected' : '' }}>
                                {{ $ses->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" style="font-weight:700; font-size:11px;">Select From Date</label>
                    <div class="input-icon-wrap">
                        <input type="date" name="from_date" value="{{ $from->toDateString() }}" class="form-control" required onchange="this.form.submit()">
                        <i class="far fa-calendar-alt"></i>
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" style="font-weight:700; font-size:11px;">Select To Date</label>
                    <div class="input-icon-wrap">
                        <input type="date" name="to_date" value="{{ $to->toDateString() }}" class="form-control" required onchange="this.form.submit()">
                        <i class="far fa-calendar-alt"></i>
                    </div>
                </div>
            </div>

            <!-- Validation Info -->
            <p style="color:var(--red); font-size:11px; font-style:italic; font-weight:600; margin-bottom:18px; margin-top:-6px;">
                *At max 90 days can be selected
            </p>

            <!-- Row 2: Search Staff and Toggle -->
            <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px;">
                <div style="display:flex; align-items:center; gap:16px; flex:1; min-width:300px;">
                    <div class="form-group" style="margin-bottom:0; flex:1;">
                        <label class="form-label" style="font-weight:700; font-size:11px;">Search Staff</label>
                        <select name="staff_id" class="form-control" onchange="this.form.submit()">
                            <option value="">Search Staff</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ $staffId == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->full_name }} ({{ $teacher->employee_id }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="padding-top:16px;">
                    <label class="switch-container">
                        <span style="font-size:11px; font-weight:700; color:var(--t1); text-transform:uppercase; letter-spacing:0.3px;">
                            SHOW DAY WISE ATTENDANCE
                        </span>
                        <span class="info-tooltip" data-info-key="daywise">
                            <i class="fas fa-info" style="font-size:8px;"></i>
                        </span>
                        <div class="switch">
                            <input type="checkbox" name="show_day_wise" id="dayWiseToggle" value="true" {{ $showDayWise ? 'checked' : '' }} onchange="toggleDayWise(this.checked)">
                            <span class="slider round"></span>
                        </div>
                    </label>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Report Table Card -->
<div class="report-card">
    <!-- Inner Toolbar (density/filters/columns) -->
    <div style="background:#0e4a5a; padding:8px 16px; display:flex; align-items:center; gap:4px; border-bottom:1px solid rgba(255,255,255,0.08); position:relative; z-index:200;">

        <!-- COLUMNS -->
        <div class="tb-wrap" id="tbWrapColumns">
            <button type="button" class="tb-btn" id="btnColumns" onclick="togglePanel('columns')">
                <i class="fas fa-columns" style="font-size:10px;"></i> COLUMNS
            </button>
            <div class="tb-panel" id="panelColumns">
                <div class="tb-panel-hdr"><i class="fas fa-columns"></i> Toggle Columns</div>
                <div class="col-toggle-list" id="colToggleList">
                    <!-- Populated by JS -->
                </div>
            </div>
        </div>

        <!-- FILTERS -->
        <div class="tb-wrap" id="tbWrapFilters">
            <button type="button" class="tb-btn" id="btnFilters" onclick="togglePanel('filters')">
                <i class="fas fa-filter" style="font-size:10px;"></i> FILTERS
                <span class="tb-filter-count" id="filterCount"></span>
            </button>
            <div class="tb-panel" id="panelFilters" style="min-width:260px;">
                <div class="tb-panel-hdr"><i class="fas fa-filter"></i> Filter Table</div>
                <div class="filters-panel-body">
                    <div class="filter-input-row">
                        <label>Class &amp; Section</label>
                        <input type="text" id="filterClass" placeholder="e.g. 10-A" oninput="applyTableFilters()">
                    </div>
                    <div class="filter-input-row">
                        <label>Teacher Name</label>
                        <input type="text" id="filterTeacher" placeholder="e.g. John" oninput="applyTableFilters()">
                    </div>
                    <button class="filter-reset-btn" onclick="resetFilters()"><i class="fas fa-times" style="font-size:10px; margin-right:4px;"></i>Reset</button>
                </div>
            </div>
        </div>

        <!-- DENSITY -->
        <div class="tb-wrap" id="tbWrapDensity">
            <button type="button" class="tb-btn" id="btnDensity" onclick="togglePanel('density')">
                <i class="fas fa-list-ul" style="font-size:10px;"></i> DENSITY
            </button>
            <div class="tb-panel" id="panelDensity" style="min-width:190px;">
                <div class="tb-panel-hdr"><i class="fas fa-list-ul"></i> Row Density</div>
                <div class="density-option active" data-density="normal" onclick="setDensity('normal', this)">
                    <div class="density-icon">
                        <span style="height:3px;"></span>
                        <span style="height:3px;"></span>
                        <span style="height:3px;"></span>
                    </div>
                    Normal
                </div>
                <div class="density-option" data-density="compact" onclick="setDensity('compact', this)">
                    <div class="density-icon">
                        <span style="height:2px;"></span>
                        <span style="height:2px;"></span>
                        <span style="height:2px;"></span>
                        <span style="height:2px;"></span>
                    </div>
                    Compact
                </div>
                <div class="density-option" data-density="comfortable" onclick="setDensity('comfortable', this)">
                    <div class="density-icon">
                        <span style="height:5px;"></span>
                        <span style="height:5px;"></span>
                    </div>
                    Comfortable
                </div>
            </div>
        </div>

    </div>

    <!-- Table Grid -->
    <div class="table-container-scroll">
        <table class="tbl-marking">
            <thead>
                <tr>
                    <th style="width:40px; text-align:center;">#</th>
                    <th>Class & Section</th>
                    <th>Teacher Name</th>
                    
                    <!-- Date Columns -->
                    @foreach($dates as $date)
                        <th class="date-column" style="text-align:center; {{ $showDayWise ? '' : 'display:none;' }}">
                            {{ $date->format('d-M') }}
                        </th>
                    @endforeach

                    <th style="text-align:center; width:130px;">
                        Total Working Days
                        <span class="info-tooltip" data-info-key="workingdays">
                            <i class="fas fa-info" style="font-size:8px;"></i>
                        </span>
                    </th>
                    <th style="text-align:center; width:120px;">
                        Marked Days
                        <span class="info-tooltip" data-info-key="markeddays">
                            <i class="fas fa-info" style="font-size:8px; color:#0e4a5a;"></i>
                        </span>
                    </th>
                    <th style="text-align:center; width:130px;">
                        Overall Percentage
                        <span class="info-tooltip" data-info-key="overallpct">
                            <i class="fas fa-info" style="font-size:8px;"></i>
                        </span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportData as $row)
                    <tr>
                        <td style="text-align:center; color:var(--t2); font-weight:600;">
                            {{ sprintf('%02d', $loop->iteration) }}
                        </td>
                        <td style="font-weight:700; color:var(--navy);">
                            {{ $row['class_name'] }} - {{ $row['section_name'] }}
                        </td>
                        <td style="font-weight:500;">
                            {{ $row['teacher_name'] }}
                        </td>

                        <!-- Date columns logic -->
                        @foreach($dates as $date)
                            @php
                                $dayVal = $row['day_wise'][$date->toDateString()];
                            @endphp
                            <td class="date-column" style="text-align:center; {{ $showDayWise ? '' : 'display:none;' }}">
                                @if($dayVal['is_marked'])
                                    <span class="text-marked">100%</span>
                                @else
                                    <span class="text-defaulter">0%</span>
                                @endif
                            </td>
                        @endforeach

                        <td style="text-align:center; font-weight:600; color:var(--t2);">
                            {{ $row['total_working_days'] }}
                        </td>
                        <td style="text-align:center; font-weight:600;">
                            {{ $row['marked_days'] }}
                        </td>
                        <td style="text-align:center; font-weight:700;">
                            @if($row['is_defaulter'])
                                <span class="text-defaulter">{{ $row['overall_percentage'] }}%</span>
                            @else
                                <span class="text-marked">{{ $row['overall_percentage'] }}%</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($dates) + 6 }}" style="text-align:center; padding:32px; color:var(--t3);">
                            No records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Table Footer / Stats summary -->
    <div style="background:var(--page); padding:12px 20px; border-top:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
        <span style="font-size:12px; font-weight:700; color:var(--t2);">
            Total Rows: {{ count($reportData) }}
        </span>
        <div style="display:flex; align-items:center; gap:6px;">
            <button type="button" class="btn btn-outline" style="padding:4px 10px; font-size:11px; cursor:not-allowed;" disabled>
                <i class="fas fa-chevron-left"></i>
            </button>
            <span style="display:inline-flex; align-items:center; justify-content:center; width:22px; height:22px; background:var(--green); color:#fff; border-radius:50%; font-size:11px; font-weight:700;">
                1
            </span>
            <button type="button" class="btn btn-outline" style="padding:4px 10px; font-size:11px; cursor:not-allowed;" disabled>
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</div>

<!-- Global Info Popover -->
<div id="infoPopover" class="info-popover">
    <div class="info-popover-title">
        <i class="fas fa-info-circle"></i>
        <span id="infoPopoverTitle"></span>
    </div>
    <div id="infoPopoverBody"></div>
</div>

<!-- Custom Stub Modal -->
<div id="subjectReportModal" class="stub-modal">
    <div class="stub-modal-content">
        <div class="stub-modal-hdr">
            <h3>Subject-Wise Attendance Not Marked Report</h3>
            <button onclick="closeStubModal()" class="stub-modal-close">&times;</button>
        </div>
        <div class="stub-modal-body">
            <p style="margin-bottom:12px;">This sub-report provides insights into subject-specific student attendance markings. All active subjects are currently tracked within standard daily parameters.</p>
            <p style="color:var(--t2); font-size:12px; font-style:italic;">Note: Daily subject teacher attendance tracking is mapped on the Timetable scheduler module.</p>
            <div style="text-align:right; margin-top:20px;">
                <button onclick="closeStubModal()" class="btn btn-primary" style="padding:6px 16px; border-radius:6px; font-size:12px;">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // ── Info Popover Definitions ────────────────────────────────────────────
    const INFO_DEFS = {
        daywise: {
            title: 'Show Day Wise Attendance',
            body:  'When enabled, the report expands to show one column per day in the selected date range. Each cell shows whether attendance was marked (100%) or not marked (0%) for that class-section on that date.'
        },
        workingdays: {
            title: 'Total Working Days',
            body:  'The total number of school working days within the selected date range. Sundays, holidays, and non-school days are excluded from this count.'
        },
        markeddays: {
            title: 'Marked Days',
            body:  'The number of working days on which the class teacher submitted an attendance register for this class-section. A day is counted as marked only when at least one student record is saved.'
        },
        overallpct: {
            title: 'Overall Percentage',
            body:  'The ratio of Marked Days to Total Working Days expressed as a percentage. This reflects how consistently attendance has been recorded for the class-section during the selected period.'
        }
    };

    let activeTooltip = null;
    const popover = document.getElementById('infoPopover');

    document.querySelectorAll('.info-tooltip[data-info-key]').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const key = btn.getAttribute('data-info-key');
            const def = INFO_DEFS[key];
            if (!def) return;

            // If same tooltip clicked again, close
            if (activeTooltip === btn && popover.classList.contains('visible')) {
                closeInfoPopover();
                return;
            }

            // Remove active from any previously active tooltip
            if (activeTooltip) activeTooltip.classList.remove('active');
            activeTooltip = btn;
            btn.classList.add('active');

            // Populate
            document.getElementById('infoPopoverTitle').textContent = def.title;
            document.getElementById('infoPopoverBody').textContent  = def.body;

            // Position popover below the button
            const rect = btn.getBoundingClientRect();
            popover.style.top  = (rect.bottom + 10 + window.scrollY) + 'px';
            popover.style.left = Math.min(rect.left, window.innerWidth - 295) + 'px';

            popover.classList.remove('visible');
            // Force reflow for animation
            void popover.offsetWidth;
            popover.classList.add('visible');
        });
    });

    function closeInfoPopover() {
        popover.classList.remove('visible');
        if (activeTooltip) { activeTooltip.classList.remove('active'); activeTooltip = null; }
    }

    // Close on click outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.info-tooltip')) closeInfoPopover();
    });

    // ── Day Wise Toggle ────────────────────────────────────────────────────
    function toggleDayWise(isChecked) {
        if (isChecked) {
            $('.date-column').show();
        } else {
            $('.date-column').hide();
        }
        let form = document.getElementById('reportFilterForm');
        let hiddenInput = document.getElementById('showDayWiseHidden');
        if (!hiddenInput) {
            hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.id = 'showDayWiseHidden';
            hiddenInput.name = 'show_day_wise';
            form.appendChild(hiddenInput);
        }
        hiddenInput.value = isChecked ? 'true' : 'false';
    }

    // ── Modal controls ─────────────────────────────────────────────────────
    function openStubModal() {
        document.getElementById('subjectReportModal').style.display = 'flex';
    }

    function closeStubModal() {
        document.getElementById('subjectReportModal').style.display = 'none';
    }

    window.onclick = function(event) {
        let modal = document.getElementById('subjectReportModal');
        if (event.target == modal) { modal.style.display = 'none'; }
    }

    // ═══════════════════════════════════════════════════════════════════════
    // TOOLBAR — COLUMNS / FILTERS / DENSITY
    // ═══════════════════════════════════════════════════════════════════════

    // ── Column definitions (must match th order exactly) ──────────────────
    const COLS = [
        { key: 'col-num',      label: '#',                    idx: 0,  fixed: true  },
        { key: 'col-class',    label: 'Class & Section',      idx: 1,  fixed: false },
        { key: 'col-teacher',  label: 'Teacher Name',         idx: 2,  fixed: false },
        { key: 'col-workdays', label: 'Total Working Days',   idx: -3, fixed: false },
        { key: 'col-marked',   label: 'Marked Days',          idx: -2, fixed: false },
        { key: 'col-pct',      label: 'Overall Percentage',   idx: -1, fixed: false },
    ];

    // Track visibility per key (true = visible)
    const colVisible = {};
    COLS.forEach(c => colVisible[c.key] = true);

    // Build column toggle list
    function buildColPanel() {
        const list = document.getElementById('colToggleList');
        list.innerHTML = '';
        COLS.forEach(function(col) {
            const item = document.createElement('label');
            item.className = 'col-toggle-item';
            item.innerHTML = `
                <input type="checkbox" ${col.fixed ? 'disabled' : ''} ${colVisible[col.key] ? 'checked' : ''} data-colkey="${col.key}">
                <span>${col.label}</span>`;
            item.querySelector('input').addEventListener('change', function() {
                colVisible[col.key] = this.checked;
                applyColVisibility();
            });
            list.appendChild(item);
        });
    }

    function applyColVisibility() {
        const table = document.querySelector('table.tbl-marking');
        if (!table) return;
        const rows = table.querySelectorAll('tr');
        // For each static column (not date-columns), we hide by col index
        // We rebuild a map of cell index → key
        const headers = table.querySelectorAll('thead tr th');
        const headerArr = Array.from(headers);
        // Build index map (skip date-columns, they sit between idx 2 and last-3)
        const staticIndices = {};
        COLS.forEach(function(col) {
            let absIdx;
            if (col.idx >= 0) {
                absIdx = col.idx;
            } else {
                absIdx = headerArr.length + col.idx;
            }
            staticIndices[col.key] = absIdx;
        });

        rows.forEach(function(row) {
            const cells = row.querySelectorAll('th, td');
            COLS.forEach(function(col) {
                if (col.fixed) return;
                const idx = staticIndices[col.key];
                if (cells[idx]) {
                    cells[idx].style.display = colVisible[col.key] ? '' : 'none';
                }
            });
        });
    }

    // ── DENSITY ────────────────────────────────────────────────────────────
    const DENSITY_PAD = { compact: '6px 10px', normal: '12px 14px', comfortable: '20px 18px' };
    let currentDensity = 'normal';

    function setDensity(mode, el) {
        currentDensity = mode;
        // Update active state
        document.querySelectorAll('.density-option').forEach(d => d.classList.remove('active'));
        el.classList.add('active');

        // Apply padding
        const pad = DENSITY_PAD[mode];
        document.querySelectorAll('table.tbl-marking td, table.tbl-marking th').forEach(function(cell) {
            cell.style.padding = pad;
        });
        closeAllPanels();
    }

    // ── FILTERS ────────────────────────────────────────────────────────────
    function applyTableFilters() {
        const classQ   = (document.getElementById('filterClass').value   || '').toLowerCase().trim();
        const teacherQ = (document.getElementById('filterTeacher').value || '').toLowerCase().trim();

        let activeCount = 0;
        if (classQ)   activeCount++;
        if (teacherQ) activeCount++;

        // Show/hide filter badge
        const badge = document.getElementById('filterCount');
        if (activeCount > 0) {
            badge.textContent = activeCount;
            badge.classList.add('visible');
        } else {
            badge.classList.remove('visible');
        }

        const rows = document.querySelectorAll('table.tbl-marking tbody tr');
        rows.forEach(function(row) {
            const classCell   = row.cells[1] ? row.cells[1].textContent.toLowerCase() : '';
            const teacherCell = row.cells[2] ? row.cells[2].textContent.toLowerCase() : '';

            const classMatch   = !classQ   || classCell.includes(classQ);
            const teacherMatch = !teacherQ || teacherCell.includes(teacherQ);

            row.style.display = (classMatch && teacherMatch) ? '' : 'none';
        });
    }

    function resetFilters() {
        document.getElementById('filterClass').value   = '';
        document.getElementById('filterTeacher').value = '';
        applyTableFilters();
    }

    // ── Panel open/close ──────────────────────────────────────────────────
    const panels = {
        columns: { btn: 'btnColumns',  panel: 'panelColumns'  },
        filters: { btn: 'btnFilters',  panel: 'panelFilters'  },
        density: { btn: 'btnDensity',  panel: 'panelDensity'  },
    };

    function togglePanel(name) {
        const isOpen = document.getElementById(panels[name].panel).classList.contains('open');
        closeAllPanels();
        if (!isOpen) {
            document.getElementById(panels[name].panel).classList.add('open');
            document.getElementById(panels[name].btn).classList.add('open');
        }
    }

    function closeAllPanels() {
        Object.values(panels).forEach(function(p) {
            document.getElementById(p.panel).classList.remove('open');
            document.getElementById(p.btn).classList.remove('open');
        });
    }

    // Close panels on outside click
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.tb-wrap') && !e.target.closest('.info-tooltip')) {
            closeAllPanels();
        }
    });

    // Init on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        buildColPanel();
    });
</script>
@endsection
