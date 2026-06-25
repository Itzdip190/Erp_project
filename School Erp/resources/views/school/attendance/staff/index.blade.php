@extends('layouts.app')

@section('title', 'Staff Attendance')
@section('page-title', 'Staff Attendance')

@section('content')
<style>
    /* Premium CSS for Staff Attendance Register */
    .register-container {
        font-family: 'Inter', sans-serif;
        background: #f8f9fa;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
    }
    
    .register-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    
    .register-title h1 {
        font-size: 24px;
        font-weight: 800;
        color: #1a202c;
        margin: 0 0 4px 0;
    }
    
    .register-title p {
        font-size: 13px;
        color: #718096;
        margin: 0;
    }

    .top-controls {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    /* Outlined Buttons like screenshots */
    .btn-outline-gold {
        border: 1px solid #d97706;
        color: #d97706;
        background: transparent;
        border-radius: 8px;
        padding: 8px 16px;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-transform: uppercase;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }
    .btn-outline-gold:hover {
        background: #fef3c7;
    }

    .btn-icon-only {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #edf2f7;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4a5568;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-icon-only:hover {
        background: #e2e8f0;
    }

    /* Datepicker styling to look like input label */
    .datepicker-box {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 6px 12px;
        background: #fff;
        display: flex;
        flex-direction: column;
        width: 160px;
    }
    .datepicker-box label {
        font-size: 9px;
        color: #718096;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 2px;
    }
    .datepicker-box input {
        border: none;
        outline: none;
        font-size: 13px;
        font-weight: 700;
        color: #1a202c;
        width: 100%;
        background: transparent;
    }

    /* Filters Row Grid */
    .filters-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
        background: #fff;
        padding: 16px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }

    .filter-col {
        display: flex;
        flex-direction: column;
    }
    .filter-col label {
        font-size: 11px;
        font-weight: 600;
        color: #718096;
        margin-bottom: 6px;
    }
    .filter-col input, .filter-col select {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 13px;
        color: #1a202c;
        outline: none;
        transition: all 0.2s;
        width: 100%;
    }
    .filter-col input:focus, .filter-col select:focus {
        border-color: #cbd5e1;
        background: #fff;
    }

    /* Metrics Cards Grid */
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 12px;
        margin-bottom: 24px;
    }

    .metric-card {
        border-radius: 10px;
        padding: 14px;
        display: flex;
        align-items: center;
        color: #fff;
        position: relative;
        min-height: 70px;
    }

    .metric-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        margin-right: 12px;
    }

    .metric-info {
        display: flex;
        flex-direction: column;
    }
    .metric-info .count {
        font-size: 18px;
        font-weight: 800;
        line-height: 1.2;
    }
    .metric-info .label {
        font-size: 11px;
        font-weight: 700;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    /* Color classes for cards */
    .card-present { background: #10b981; }
    .card-absent { background: #b91c1c; }
    .card-halfday { background: #b45309; }
    .card-leave { background: #f97316; }
    .card-custom { background: #db2777; }
    .card-notmarked { 
        background: #9ca3af; 
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Attendance percentage circle — SVG ring style */
    .att-pct-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }
    .att-pct-svg {
        position: relative;
        width: 52px;
        height: 52px;
        flex-shrink: 0;
    }
    .att-pct-svg svg {
        transform: rotate(-90deg);
        width: 52px;
        height: 52px;
    }
    .att-pct-svg .ring-bg {
        fill: none;
        stroke: #e2e8f0;
        stroke-width: 4;
    }
    .att-pct-svg .ring-fill {
        fill: none;
        stroke-width: 4;
        stroke-linecap: round;
        transition: stroke-dashoffset 0.5s ease;
    }
    .att-pct-center {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 800;
        line-height: 1;
    }

    /* Table Container Styling */
    .table-container {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        margin-bottom: 80px; /* Leave space for sticky bottom button */
    }

    .premium-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .premium-table th {
        background: #023c4d; /* Dark Teal header */
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        padding: 14px 18px;
        text-transform: capitalize;
        border: none;
    }

    .premium-table td {
        padding: 12px 18px;
        font-size: 13px;
        color: #4a5568;
        border-bottom: 1px solid #edf2f7;
        vertical-align: middle;
    }

    .premium-table tr:hover td {
        background: #f8fafc;
    }

    .index-num {
        color: #a0aec0;
        font-weight: 700;
        font-size: 12px;
    }

    .staff-profile {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .avatar-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #edf2f7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #4a5568;
        overflow: hidden;
        border: 1px solid #cbd5e1;
    }

    .avatar-circle img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .staff-details {
        display: flex;
        flex-direction: column;
    }
    .staff-name {
        font-weight: 700;
        color: #1a202c;
    }
    .staff-sub {
        font-size: 11px;
        color: #718096;
        margin-top: 2px;
    }



    /* Status Badge styling */
    .status-text {
        font-weight: 600;
    }
    .status-badge-present { color: #10b981; }
    .status-badge-absent { color: #ef4444; }
    .status-badge-halfday { color: #b45309; }
    .status-badge-leave { color: #ea580c; }
    .status-badge-late { color: #db2777; }
    .status-badge-notmarked { color: #718096; }

    /* Floating/Sticky Bottom Button like in image */
    .bottom-bar {
        position: fixed;
        bottom: 0;
        left: 185px; /* Sidebar width */
        right: 0;
        background: #fff;
        padding: 16px 32px;
        box-shadow: 0 -4px 10px rgba(0,0,0,0.05);
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        z-index: 100;
        transition: left 0.3s ease;
    }

    body.sidebar-collapsed .bottom-bar {
        left: 60px;
    }

    .btn-mark-attendance {
        background: #9a3412; /* Dark brownish orange */
        color: #fff;
        border: none;
        border-radius: 4px;
        padding: 10px 24px;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        cursor: pointer;
        letter-spacing: 0.5px;
        transition: background 0.2s;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
    }
    .btn-mark-attendance:hover {
        background: #7c2d12;
    }

    /* Cancel and Save buttons for edit mode */
    .btn-cancel-edit {
        background: #fff;
        color: #9a3412;
        border: 1px solid #9a3412;
        border-radius: 4px;
        padding: 10px 24px;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        cursor: pointer;
        letter-spacing: 0.5px;
        transition: background 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }
    .btn-cancel-edit:hover {
        background: #fff5f5;
    }

    .btn-save-edit {
        background: #9a3412;
        color: #fff;
        border: none;
        border-radius: 4px;
        padding: 10px 24px;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        cursor: pointer;
        letter-spacing: 0.5px;
        transition: background 0.2s;
    }
    .btn-save-edit:hover {
        background: #7c2d12;
    }

    /* Pills Container for Edit Mode */
    .status-pills-container {
        display: flex;
        gap: 6px;
        justify-content: flex-start;
        align-items: center;
    }
    
    .status-pill-btn {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 1px solid #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 700;
        color: #718096;
        cursor: pointer;
        transition: all 0.15s;
        position: relative;
        background: #fff;
        user-select: none;
    }
    
    .status-pill-btn input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    /* Style based on checked state of nested radio */
    .status-pill-btn:has(input:checked) {
        color: #fff !important;
        border-color: transparent !important;
        font-weight: 800;
    }

    .pill-p:has(input:checked) { background: #10b981; }
    .pill-hd:has(input:checked) { background: #007791; } /* Dark blue/teal matching image */
    .pill-a:has(input:checked) { background: #ef4444; }
    .pill-l:has(input:checked) { background: #ea580c; }
    .pill-cl:has(input:checked) { background: #db2777; }

    /* Outline Time Input Box styled on border */
    .outline-input-container {
        position: relative;
        margin: 6px 0;
        width: 100%;
        max-width: 220px;
    }
    
    .outline-input-container label {
        position: absolute;
        left: 10px;
        top: -7px;
        background: #fff;
        padding: 0 4px;
        font-size: 9px;
        font-weight: 700;
        color: #718096;
        pointer-events: none;
        text-transform: uppercase;
        letter-spacing: 0.2px;
    }
    
    .outline-input-container input {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 10px 32px 10px 12px;
        font-size: 13px;
        outline: none;
        color: #2d3748;
        font-weight: 600;
        background: transparent;
    }
    
    .outline-input-container input:focus {
        border-color: #4a5568;
    }
    
    .outline-input-container .input-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #a0aec0;
        font-size: 14px;
        pointer-events: none;
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 48px;
        color: #718096;
    }
    .empty-state i {
        font-size: 40px;
        margin-bottom: 12px;
        color: #cbd5e1;
    }
</style>

<div class="register-container">
    <form id="filterForm" method="GET" action="{{ route('school.attendance.staff.index') }}">
        <input type="hidden" name="mode" value="{{ $mode }}">
        
        <div class="register-header">
            <div class="register-title">
                <h1>Staff Attendance</h1>
                <p>Staff Management</p>
            </div>
            
            <div class="top-controls">
                <!-- Date Picker Box -->
                <div class="datepicker-box">
                    <label>Pick Date</label>
                    <input type="date" name="date" value="{{ $date }}" max="{{ date('Y-m-d') }}" onchange="document.getElementById('filterForm').submit();">
                </div>

                <!-- Academic Year dropdown -->
                <div class="datepicker-box" style="width: 180px;">
                    <label>Academic Year</label>
                    <select name="academic_year" style="border:none; outline:none; font-size:13px; font-weight:700; color:#1a202c; padding:0; background:transparent;" onchange="document.getElementById('filterForm').submit();">
                        <option value="2025-2026">Apr 2025 - Mar 2026</option>
                    </select>
                </div>

                <!-- Gold outlined buttons -->
                <a href="{{ route('school.attendance.staff.report', ['export' => 'excel', 'department_id' => $departmentId ?: 1]) }}" class="btn-outline-gold">
                    <i class="fas fa-file-excel"></i> View Excel
                </a>
                
                <a href="{{ route('school.attendance.staff.report', ['export' => 'excel', 'department_id' => $departmentId ?: 1]) }}" class="btn-outline-gold">
                    <i class="fas fa-download"></i> Download
                </a>

                <button type="button" class="btn-outline-gold">
                    <i class="fas fa-fingerprint"></i> Biometric Report
                </button>

                <button type="button" class="btn-icon-only">
                    <i class="fas fa-cog"></i>
                </button>
            </div>
        </div>

        <!-- Filters Row Grid -->
        <div class="filters-grid">
            <div class="filter-col">
                <label>Search</label>
                <input type="text" name="search" placeholder="Staff Name" value="{{ $search }}" onchange="document.getElementById('filterForm').submit();">
            </div>

            <div class="filter-col">
                <label>Select Staff Type</label>
                <select name="staff_type" onchange="document.getElementById('filterForm').submit();">
                    @foreach(['All staffs', 'Teaching', 'Non Teaching', 'Driver/Supporting staff', 'Others', 'Admin'] as $t)
                        <option value="{{ $t }}" {{ $staffType === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-col">
                <label>Select Status</label>
                <select name="status" onchange="document.getElementById('filterForm').submit();">
                    <option value="">Select Status</option>
                    @foreach(['Present', 'Absent', 'HalfDay', 'Leave', 'Custom Leaves', 'Not Marked'] as $stOpt)
                        <option value="{{ $stOpt }}" {{ $status === $stOpt ? 'selected' : '' }}>{{ $stOpt }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-col">
                <label>Filter by department</label>
                <select name="department_id" onchange="document.getElementById('filterForm').submit();">
                    <option value="">Filter by department</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ $departmentId == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    <!-- Metrics Cards Row -->
    <div class="metrics-grid">
        <div class="metric-card card-present">
            <div class="metric-icon"><i class="fas fa-check"></i></div>
            <div class="metric-info">
                <span class="count">{{ $presentCount }}</span>
                <span class="label">Present</span>
            </div>
        </div>
        
        <div class="metric-card card-absent">
            <div class="metric-icon"><i class="fas fa-times"></i></div>
            <div class="metric-info">
                <span class="count">{{ $absentCount }}</span>
                <span class="label">Absent</span>
            </div>
        </div>

        <div class="metric-card card-halfday">
            <div class="metric-icon"><i class="fas fa-slash"></i></div>
            <div class="metric-info">
                <span class="count">{{ $halfDayCount }}</span>
                <span class="label">HalfDay</span>
            </div>
        </div>

        <div class="metric-card card-leave">
            <div class="metric-icon"><i class="fas fa-plane-departure"></i></div>
            <div class="metric-info">
                <span class="count">{{ $leaveCount }}</span>
                <span class="label">Leave</span>
            </div>
        </div>

        <div class="metric-card card-custom">
            <div class="metric-icon"><i class="fas fa-star"></i></div>
            <div class="metric-info">
                <span class="count">{{ $customLeavesCount }}</span>
                <span class="label">Custom Leaves</span>
            </div>
        </div>

        <div class="metric-card card-notmarked">
            <div class="metric-icon"><i class="fas fa-minus-circle"></i></div>
            <div class="metric-info">
                <span class="count">{{ $notMarkedCount }}</span>
                <span class="label">Not Marked</span>
            </div>
        </div>
    </div>

    <!-- Table Container & Form -->
    @if($mode === 'edit')
    <form method="POST" action="{{ route('school.attendance.staff.store') }}">
        @csrf
        <input type="hidden" name="date" value="{{ $date }}">
        <input type="hidden" name="department_id" value="{{ $departmentId }}">
        <input type="hidden" name="staff_type" value="{{ $staffType }}">
        <input type="hidden" name="status_filter" value="{{ $status }}">
        <input type="hidden" name="search_filter" value="{{ $search }}">
    @endif

    <div class="table-container">
        @if($staffList->isNotEmpty())
            <table class="premium-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Staff</th>
                        <th>Department</th>
                        
                        @if($mode === 'edit')
                            <th style="width: 220px; text-align: left;">
                                <div style="margin-bottom:4px;">Attendance status</div>
                                <div style="display:flex; gap:16px; font-size:10px; font-weight:700; color:rgba(255,255,255,0.7); padding-left:4px;">
                                    <span>P</span>
                                    <span>HD</span>
                                    <span>A</span>
                                    <span>L</span>
                                    <span>CL</span>
                                </div>
                            </th>
                            <th style="width: 220px;">Check In</th>
                            <th style="width: 220px;">Check Out</th>
                        @else
                            <th>Attendance status</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th style="text-align: center; width: 140px;">Attendance %</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($staffList as $idx => $staff)
                        @php
                            $att = $dateAttendances->get($staff->id);
                            
                            // Map database status to display status
                            $displayStatus = 'Not Marked';
                            $badgeClass = 'notmarked';
                            $checkInTime = '—';
                            $checkOutTime = '—';

                            if ($att) {
                                $dbStatus = strtolower($att->status);
                                if ($dbStatus === 'present') {
                                    $displayStatus = 'Present';
                                    $badgeClass = 'present';
                                } elseif ($dbStatus === 'absent') {
                                    $displayStatus = 'Absent';
                                    $badgeClass = 'absent';
                                } elseif ($dbStatus === 'half_day') {
                                    $displayStatus = 'HalfDay';
                                    $badgeClass = 'halfday';
                                } elseif ($dbStatus === 'leave') {
                                    $displayStatus = 'Leave';
                                    $badgeClass = 'leave';
                                } elseif ($dbStatus === 'late' || $dbStatus === 'holiday') {
                                    $displayStatus = 'Custom Leaves';
                                    $badgeClass = 'late';
                                }

                                if ($att->clock_in_at) {
                                    $checkInTime = date('h:i A', strtotime($att->clock_in_at));
                                }
                                if ($att->clock_out_at) {
                                    $checkOutTime = date('h:i A', strtotime($att->clock_out_at));
                                }
                            }

                            // Attendance percentage rendering color
                            $pct = $staff->attendance_percentage;
                            $progressColor = '#ef4444'; // Red default
                            if ($pct >= 75) {
                                $progressColor = '#10b981'; // Green for high rate
                            }
                        @endphp
                        <tr>
                            <td class="index-num">{{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}.</td>
                            <td>
                                <div class="staff-profile">
                                    <div class="avatar-circle">
                                        @if($staff->photo)
                                            <img src="{{ $staff->photo_url }}" alt="Photo">
                                        @else
                                            {{ substr($staff->first_name, 0, 1) }}{{ substr($staff->last_name, 0, 1) }}
                                        @endif
                                    </div>
                                    <div class="staff-details">
                                        <span class="staff-name">{{ $staff->full_name }}</span>
                                        <span class="staff-sub">{{ $staff->staff_type }} • {{ $staff->employee_id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>{{ optional($staff->department)->name ?? '—' }}</td>
                            
                            @if($mode === 'edit')
                                <td>
                                    <input type="hidden" name="attendance[{{ $idx }}][staff_id]" value="{{ $staff->id }}">
                                    <!-- Pills Row -->
                                    <div class="status-pills-container">
                                        <!-- Present -->
                                        <label class="status-pill-btn pill-p" title="Present">
                                            <input type="radio" name="attendance[{{ $idx }}][status]" value="Present" {{ $displayStatus === 'Present' ? 'checked' : '' }}>
                                            <span>P</span>
                                        </label>
                                        <!-- HalfDay -->
                                        <label class="status-pill-btn pill-hd" title="HalfDay">
                                            <input type="radio" name="attendance[{{ $idx }}][status]" value="HalfDay" {{ $displayStatus === 'HalfDay' ? 'checked' : '' }}>
                                            <span>HD</span>
                                        </label>
                                        <!-- Absent -->
                                        <label class="status-pill-btn pill-a" title="Absent">
                                            <input type="radio" name="attendance[{{ $idx }}][status]" value="Absent" {{ $displayStatus === 'Absent' ? 'checked' : '' }}>
                                            <span>A</span>
                                        </label>
                                        <!-- Leave -->
                                        <label class="status-pill-btn pill-l" title="Leave">
                                            <input type="radio" name="attendance[{{ $idx }}][status]" value="Leave" {{ $displayStatus === 'Leave' ? 'checked' : '' }}>
                                            <span>L</span>
                                        </label>
                                        <!-- Custom Leaves -->
                                        <label class="status-pill-btn pill-cl" title="Custom Leaves">
                                            <input type="radio" name="attendance[{{ $idx }}][status]" value="Custom Leaves" {{ $displayStatus === 'Custom Leaves' ? 'checked' : '' }}>
                                            <span>CL</span>
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="outline-input-container">
                                        <label>Check In Time</label>
                                        <input type="text" name="attendance[{{ $idx }}][clock_in_at]" placeholder="hh:mm aa" value="{{ $checkInTime !== '—' ? $checkInTime : '' }}">
                                        <i class="far fa-clock input-icon"></i>
                                    </div>
                                </td>
                                <td>
                                    <div class="outline-input-container">
                                        <label>Check Out Time</label>
                                        <input type="text" name="attendance[{{ $idx }}][clock_out_at]" placeholder="hh:mm aa" value="{{ $checkOutTime !== '—' ? $checkOutTime : '' }}">
                                        <i class="far fa-clock input-icon"></i>
                                    </div>
                                </td>
                            @else
                                <td>
                                    <span class="status-text status-badge-{{ $badgeClass }}">{{ $displayStatus }}</span>
                                </td>
                                <td>{{ $checkInTime }}</td>
                                <td>{{ $checkOutTime }}</td>
                                <td align="center">
                                    @if($pct !== null)
                                        @php
                                            $radius = 20;
                                            $circ   = round(2 * M_PI * $radius, 2);
                                            $offset = round($circ * (1 - $pct / 100), 2);
                                            $ringColor = $pct >= 75 ? '#10b981' : ($pct >= 50 ? '#f59e0b' : '#ef4444');
                                            $textColor = $pct >= 75 ? '#059669' : ($pct >= 50 ? '#d97706' : '#dc2626');
                                        @endphp
                                        <div class="att-pct-wrap">
                                            <div class="att-pct-svg">
                                                <svg viewBox="0 0 52 52">
                                                    <circle class="ring-bg" cx="26" cy="26" r="{{ $radius }}"/>
                                                    <circle class="ring-fill"
                                                        cx="26" cy="26" r="{{ $radius }}"
                                                        stroke="{{ $ringColor }}"
                                                        stroke-dasharray="{{ $circ }}"
                                                        stroke-dashoffset="{{ $offset }}"/>
                                                </svg>
                                                <div class="att-pct-center" style="color:{{ $textColor }};">
                                                    {{ number_format($pct, 0) }}%
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span style="color:#a0aec0;font-size:18px;">—</span>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <p>No active staff members found matching your search filters.</p>
            </div>
        @endif
    </div>

    <!-- Floating bottom action bar -->
    <div class="bottom-bar">
        @if($mode === 'edit')
            <a href="{{ route('school.attendance.staff.index', [
                'date' => $date,
                'department_id' => $departmentId,
                'staff_type' => $staffType,
                'status' => $status,
                'search' => $search,
                'mode' => 'view'
            ]) }}" class="btn-cancel-edit">Cancel</a>
            <button type="submit" class="btn-save-edit">Save</button>
        @else
            <!-- View Mode: Clicking this goes to Edit Mode on this page -->
            <a href="{{ route('school.attendance.staff.index', [
                'date' => $date,
                'department_id' => $departmentId,
                'staff_type' => $staffType,
                'status' => $status,
                'search' => $search,
                'mode' => 'edit'
            ]) }}" class="btn-mark-attendance">Mark Attendance</a>

            <!-- Prominent button to enter Multi-Day Bulk Attendance view -->
            <a href="{{ route('school.staff.bulk-attendance', [
                'from_date' => $date,
                'to_date' => $date,
                'staff_type' => ($staffType === 'All staffs' ? 'Teaching' : $staffType),
                'department_id' => $departmentId
            ]) }}" class="btn-outline-gold" style="border-color:#9a3412; color:#9a3412; font-weight:800; padding:10px 20px; border-radius:4px; height: 38px; display:inline-flex; align-items:center;">
                Multi-Day Bulk Attendance
            </a>
        @endif
    </div>

    @if($mode === 'edit')
    </form>
    @endif
</div>
@endsection
