@extends('layouts.app')

@section('title', 'Student Mark Bulk Attendance')
@section('page-title', 'Student Mark Bulk Attendance')

@section('content')
<style>
    /* Premium CSS for Student Bulk Attendance Page */
    .bulk-container {
        font-family: 'Inter', sans-serif;
        background: #f8f9fa;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        max-width: 100%;
        width: 100%;
        overflow: hidden;
    }

    .bulk-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .bulk-title h1 {
        font-size: 24px;
        font-weight: 800;
        color: #1a202c;
        margin: 0 0 4px 0;
    }
    
    .bulk-title p {
        font-size: 13px;
        color: #718096;
        margin: 0;
    }

    /* Filters Card Grid */
    .filters-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 20px;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 16px;
    }

    .filter-col {
        display: flex;
        flex-direction: column;
    }
    .filter-col label {
        font-size: 10px;
        font-weight: 700;
        color: #718096;
        text-transform: uppercase;
        margin-bottom: 6px;
        letter-spacing: 0.3px;
    }
    .filter-col input, .filter-col select {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 13px;
        color: #1a202c;
        outline: none;
        transition: all 0.2s;
        width: 100%;
        font-weight: 600;
    }
    .filter-col input:focus, .filter-col select:focus {
        border-color: #94a3b8;
        background: #fff;
    }

    /* Notice Banner styling */
    .notice-banner {
        background: #eff6ff; /* Light blue */
        border: 1px solid #bfdbfe;
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 24px;
        font-size: 13px;
        color: #2563eb;
        line-height: 1.6;
    }
    .notice-banner p {
        margin: 2px 0;
        font-weight: 600;
    }

    /* Main Grid Panel */
    .data-panel {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        max-width: 100%;
        width: 100%;
    }

    .panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        border-bottom: 1px solid #edf2f7;
    }
    .panel-header-left h3 {
        font-size: 16px;
        font-weight: 700;
        color: #2d3748;
        margin: 0;
    }
    .panel-header-left p {
        font-size: 12px;
        color: #718096;
        margin: 2px 0 0 0;
    }

    .panel-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .btn-settings-gear {
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-settings-gear:hover {
        background: #e2e8f0;
        color: #475569;
    }

    /* Mustard/Brown Save Button matching design */
    .btn-save-attendance {
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
        text-decoration: none;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .btn-save-attendance:hover {
        background: #7c2d12;
    }

    /* Fix layout expansion to keep elements inside screen boundaries */
    .main {
        min-width: 0 !important;
    }
    .pg {
        min-width: 0 !important;
        max-width: 100% !important;
        overflow-x: hidden !important;
    }

    /* Responsive grid scroll wrapper */
    .grid-scroll-wrapper {
        overflow-x: auto !important;
        width: 100% !important;
        max-width: 100% !important;
        display: block !important;
        scrollbar-width: auto;
    }
    .grid-scroll-wrapper::-webkit-scrollbar {
        height: 10px;
        display: block;
    }
    .grid-scroll-wrapper::-webkit-scrollbar-track {
        background: #f8fafc;
        border-radius: 6px;
    }
    .grid-scroll-wrapper::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 6px;
        border: 2px solid #f8fafc;
    }
    .grid-scroll-wrapper::-webkit-scrollbar-thumb:hover {
        background-color: #94a3b8;
    }

    .bulk-grid-table {
        width: 100%;
        border-collapse: collapse;
    }

    .bulk-grid-table th {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        font-size: 12px;
        font-weight: 700;
        color: #4a5568;
        padding: 12px 14px;
        text-align: left;
    }

    .bulk-grid-table th.date-column-header {
        text-align: center;
        min-width: 120px;
        max-width: 140px;
        border-left: 1px solid #e2e8f0;
        padding: 10px 8px;
    }

    /* Highlight ONLY Sunday column headers as holiday */
    .bulk-grid-table th.sunday-header {
        background: #fef3c7;
    }
    /* Saturday stays white/normal (working day) */
    .bulk-grid-table th.saturday-header {
        background: #f8fafc;
    }

    .bulk-grid-table td {
        padding: 12px 10px;
        border-bottom: 1px solid #edf2f7;
        vertical-align: middle;
    }

    .student-cell {
        min-width: 220px;
        position: sticky;
        left: 0;
        background: #fff;
        z-index: 10;
        box-shadow: 4px 0 8px rgba(0,0,0,0.02);
    }

    .bulk-grid-table tr:hover .student-cell {
        background: #f8fafc;
    }

    .student-profile {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Letter Avatar */
    .student-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #ea580c;
        color: #fff;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        overflow: hidden;
        border: 1px solid #cbd5e1;
        flex-shrink: 0;
    }

    .student-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .student-info {
        display: flex;
        flex-direction: column;
    }
    .student-name {
        font-weight: 700;
        color: #1a202c;
        font-size: 12.5px;
    }
    .student-sub {
        font-size: 10px;
        color: #b45309;
        font-weight: 600;
        margin-top: 1px;
    }

    /* Date Attendance marking box */
    .date-cell-container {
        border-left: 1px solid #edf2f7;
        padding: 0 4px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    /* Status Select Dropdown */
    .status-select {
        width: 100%;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 6px 8px;
        font-size: 12px;
        font-weight: 700;
        outline: none;
        height: 32px;
        transition: all 0.15s;
        text-align: center;
        cursor: pointer;
    }
    .status-select:focus {
        border-color: #94a3b8;
    }

    /* Dynamic select styling colors */
    .select-present {
        color: #047857 !important;
        border-color: #a7f3d0 !important;
        background: #f0fdf4 !important;
    }
    .select-absent {
        color: #b91c1c !important;
        border-color: #fecaca !important;
        background: #fef2f2 !important;
    }
    .select-late {
        color: #d97706 !important;
        border-color: #fde68a !important;
        background: #fffbeb !important;
    }
    .select-half_day {
        color: #d97706 !important;
        border-color: #fde68a !important;
        background: #fffbeb !important;
    }
    .select-holiday {
        color: #4b5563 !important;
        border-color: #d1d5db !important;
        background: #f3f4f6 !important;
    }
    .select-leave, .select-duty_leave {
        color: #1d4ed8 !important;
        border-color: #bfdbfe !important;
        background: #eff6ff !important;
    }
    .select-not_marked {
        color: #718096 !important;
        border-color: #cbd5e1 !important;
        background: #f8fafc !important;
    }

    /* Empty state */
    .empty-grid {
        text-align: center;
        padding: 48px;
        color: #718096;
    }
    .empty-grid i {
        font-size: 40px;
        margin-bottom: 12px;
        color: #cbd5e1;
    }

    /* ── Student Bulk Attendance Dark Mode Overrides ── */
    body.dark-mode .bulk-container {
        background: #0b0f19 !important;
        box-shadow: none !important;
    }
    body.dark-mode .bulk-title h1 {
        color: #f8fafc !important;
    }
    body.dark-mode .bulk-title p {
        color: #cbd5e1 !important;
    }
    body.dark-mode .filters-card {
        background: #111827 !important;
        border-color: #1e293b !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3) !important;
    }
    body.dark-mode .filter-col label {
        color: #cbd5e1 !important;
    }
    body.dark-mode .filter-col input, 
    body.dark-mode .filter-col select {
        background: #1f2937 !important;
        border-color: #374151 !important;
        color: #f8fafc !important;
    }
    body.dark-mode .filter-col input:focus, 
    body.dark-mode .filter-col select:focus {
        border-color: #4b5563 !important;
        background: #1f2937 !important;
    }
    body.dark-mode .notice-banner {
        background: rgba(37, 99, 235, 0.1) !important;
        border-color: rgba(37, 99, 235, 0.3) !important;
        color: #60a5fa !important;
    }
    body.dark-mode .notice-banner p {
        color: #60a5fa !important;
    }
    body.dark-mode .data-panel {
        background: #111827 !important;
        border-color: #1e293b !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3) !important;
    }
    body.dark-mode .panel-header {
        border-bottom-color: #1e293b !important;
    }
    body.dark-mode .panel-header h3, 
    body.dark-mode .panel-header-left h3 {
        color: #f8fafc !important;
    }
    body.dark-mode .panel-header p, 
    body.dark-mode .panel-header-left p {
        color: #94a3b8 !important;
    }
    body.dark-mode .btn-settings-gear {
        background: #1f2937 !important;
        color: #cbd5e1 !important;
        border-color: #374151 !important;
    }
    body.dark-mode .btn-settings-gear:hover {
        background: #374151 !important;
        color: #ffffff !important;
    }
    body.dark-mode .bulk-grid-table th {
        background: #1f2937 !important;
        color: #cbd5e1 !important;
        border-bottom-color: #374151 !important;
    }
    body.dark-mode .bulk-grid-table th.date-column-header {
        border-left-color: #374151 !important;
    }
    body.dark-mode .bulk-grid-table th.sunday-header {
        background: rgba(180, 83, 9, 0.2) !important;
        color: #f59e0b !important;
    }
    body.dark-mode .bulk-grid-table th.saturday-header {
        background: #1f2937 !important;
    }
    body.dark-mode .bulk-grid-table td {
        border-bottom-color: #1e293b !important;
    }
    body.dark-mode .student-cell {
        background: #111827 !important;
    }
    body.dark-mode .student-name {
        color: #f8fafc !important;
    }
    body.dark-mode .student-sub {
        color: #f97316 !important;
    }
    body.dark-mode .status-select {
        background: #1f2937 !important;
        border-color: #374151 !important;
        color: #cbd5e1 !important;
    }
    body.dark-mode .status-select option {
        background: #1f2937 !important;
        color: #f8fafc !important;
    }
    body.dark-mode .select-present {
        color: #34d399 !important;
        border-color: #065f46 !important;
        background: #064e3b !important;
    }
    body.dark-mode .select-absent {
        color: #f87171 !important;
        border-color: #7f1d1d !important;
        background: #991b1b !important;
    }
    body.dark-mode .select-late {
        color: #fbbf24 !important;
        border-color: #78350f !important;
        background: #7c2d12 !important;
    }
    body.dark-mode .select-half_day {
        color: #fbbf24 !important;
        border-color: #78350f !important;
        background: #7c2d12 !important;
    }
    body.dark-mode .select-holiday {
        color: #9ca3b8 !important;
        border-color: #374151 !important;
        background: #1f2937 !important;
    }
    body.dark-mode .select-leave, 
    body.dark-mode .select-duty_leave {
        color: #60a5fa !important;
        border-color: #1e3a8a !important;
        background: #1e3a8a !important;
    }
    body.dark-mode .select-not_marked {
        color: #9ca3af !important;
        border-color: #374151 !important;
        background: #1f2937 !important;
    }
    body.dark-mode .empty-grid {
        color: #cbd5e1 !important;
    }

    /* Bulk Attendance Slider Drawer Styles */
    .bulk-slider-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.4);
        backdrop-filter: blur(4px);
        z-index: 9999;
        display: none;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .bulk-slider-overlay.active {
        display: block;
        opacity: 1;
    }
    .bulk-slider-drawer {
        position: fixed;
        top: 0;
        right: -480px;
        width: 480px;
        height: 100vh;
        background: #ffffff;
        box-shadow: -10px 0 30px rgba(0, 0, 0, 0.15);
        z-index: 10000;
        transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        font-family: 'Inter', sans-serif;
    }
    .bulk-slider-drawer.active {
        right: 0;
    }
    body.dark-mode .bulk-slider-drawer {
        background: #111827;
        color: #f8fafc;
        box-shadow: -10px 0 30px rgba(0, 0, 0, 0.5);
    }
    .slider-header {
        padding: 20px 24px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #023c4d;
        color: #ffffff;
    }
    body.dark-mode .slider-header {
        background: #1f2937;
        border-bottom-color: #374151;
    }
    .slider-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 800;
    }
    .slider-header .close-btn {
        background: none;
        border: none;
        color: #ffffff;
        font-size: 24px;
        cursor: pointer;
        line-height: 1;
    }
    .slider-body {
        flex: 1;
        padding: 24px;
        overflow-y: auto;
    }
    .slider-footer {
        padding: 16px 24px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8fafc;
    }
    body.dark-mode .slider-footer {
        background: #1f2937;
        border-top-color: #374151;
    }
    .slider-step {
        display: none;
    }
    .slider-step.active {
        display: block;
    }
    .slider-field {
        margin-bottom: 20px;
        display: flex;
        flex-direction: column;
    }
    .slider-field label {
        font-size: 11px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        margin-bottom: 6px;
        letter-spacing: 0.5px;
    }
    body.dark-mode .slider-field label {
        color: #cbd5e1;
    }
    .slider-input, .slider-select {
        height: 42px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 0 14px;
        font-size: 13.5px;
        font-weight: 600;
        outline: none;
        background: #ffffff;
        color: #1e293b;
        transition: all 0.2s;
    }
    body.dark-mode .slider-input, body.dark-mode .slider-select {
        background: #1f2937;
        border-color: #374151;
        color: #f8fafc;
    }
    .slider-input:focus, .slider-select:focus {
        border-color: #023c4d;
    }
</style>

<div class="bulk-container">
    <div class="bulk-header">
        <div class="bulk-title">
            <h1>Student Bulk Attendance</h1>
            <p>Student Management</p>
        </div>
    </div>

    @if(session('warning'))
        <div class="alert alert-warning" style="margin-bottom:20px; padding:12px 16px; border-radius:8px; background:#fffbeb; border:1px solid #fde68a; color:#b45309; font-size:13px; font-weight:600;">
            <i class="fas fa-exclamation-triangle" style="margin-right:8px;"></i>{{ session('warning') }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:20px; padding:12px 16px; border-radius:8px; background:#f0fdf4; border:1px solid #a7f3d0; color:#15803d; font-size:13px; font-weight:600;">
            <i class="fas fa-check-circle" style="margin-right:8px;"></i>{{ session('success') }}
        </div>
    @endif

    <!-- Filters Card Panel -->
    <div class="filters-card">
        <form id="bulkFilterForm" method="GET" action="{{ route('school.student-mgmt.bulk-attendance') }}">
            <div class="filters-grid">
                <!-- Select Class -->
                <div class="filter-col">
                    <label>Select Class *</label>
                    <select name="class_id" id="classSelect" required onchange="filterSections(); document.getElementById('bulkFilterForm').submit();">
                        <option value="">Select Class</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Select Section -->
                <div class="filter-col">
                    <label>Select Section *</label>
                    <select name="section_id" id="sectionSelect" required onchange="document.getElementById('bulkFilterForm').submit();">
                        <option value="">Select Section</option>
                        @foreach($sections as $s)
                            <option value="{{ $s->id }}" data-class-id="{{ $s->class_id }}" {{ $sectionId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- From Date Picker -->
                <div class="filter-col">
                    <label>From Date</label>
                    <input type="date" name="from_date" value="{{ $fromDate }}" onchange="document.getElementById('bulkFilterForm').submit();">
                </div>

                <!-- To Date Picker -->
                <div class="filter-col">
                    <label>To Date</label>
                    <input type="date" name="to_date" value="{{ $toDate }}" onchange="document.getElementById('bulkFilterForm').submit();">
                </div>

                <!-- Search Input -->
                <div class="filter-col">
                    <label>Search Student</label>
                    <input type="text" name="search" placeholder="Search students" value="{{ $search }}" onchange="document.getElementById('bulkFilterForm').submit();">
                </div>

                <!-- Academic Year Dropdown (next to Search) -->
                <div class="filter-col">
                    <label>Academic Year *</label>
                    <select name="academic_session_id" onchange="document.getElementById('bulkFilterForm').submit();">
                        @foreach($academicSessions as $ses)
                            <option value="{{ $ses->id }}" {{ $sessionId == $ses->id ? 'selected' : '' }}>{{ $ses->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>

    <!-- Notice Banner Info -->
    @if($classId && $sectionId)
        @php
            $selectedClass = $classes->firstWhere('id', $classId);
            $selectedSection = $sections->firstWhere('id', $sectionId);
            $className = $selectedClass ? $selectedClass->name : 'N/A';
            $sectionName = $selectedSection ? $selectedSection->name : 'N/A';
        @endphp
        <div class="notice-banner">
            <p style="font-size:14px; font-weight:700; color:#1e40af; margin-bottom:6px;">Date Range: {{ date('d-m-Y', strtotime($fromDate)) }} to {{ date('d-m-Y', strtotime($toDate)) }} ({{ $totalDays }} days total — {{ $weekdays }} working days, {{ $weekends }} Sundays)</p>
            <p style="font-weight:600; color:#1e40af;">Class: {{ $className }} - {{ $sectionName }}</p>
            <p style="font-weight:600; color:#1e40af;">{{ $academicYearText }}</p>
            <p style="color:#d97706; font-size:12px; font-weight:700;"><i class="fas fa-info-circle"></i> Saturday = Working Day &nbsp;|&nbsp; Sunday = Holiday (auto-marked)</p>
        </div>

        <!-- Main Attendance Grid Form -->
        <form method="POST" action="{{ route('school.student-mgmt.bulk-attendance.post') }}">
            @csrf
            <input type="hidden" name="academic_session_id" value="{{ $sessionId }}">
            <input type="hidden" name="class_id" value="{{ $classId }}">
            <input type="hidden" name="section_id" value="{{ $sectionId }}">
            
            <div class="data-panel">
                <div class="panel-header">
                    <div class="panel-header-left">
                        <h3>Attendance Data ({{ $students->count() }} students)</h3>
                        <p style="font-size:12px; color:#64748b;">{{ date('d-m-Y', strtotime($fromDate)) }} to {{ date('d-m-Y', strtotime($toDate)) }}</p>
                    </div>
                    <div class="panel-actions">
                        <button type="button" class="btn-save-attendance" onclick="openBulkAttendanceSlider()" style="background: #023c4d; font-weight: 700; margin-right: 6px;">Bulk Attendance Slider</button>
                        <button type="button" class="btn-settings-gear"><i class="fas fa-cog"></i></button>
                        <button type="submit" class="btn-save-attendance">Save Attendance</button>
                    </div>
                </div>

                <div class="grid-scroll-wrapper">
                    @if($students->isNotEmpty())
                        <table class="bulk-grid-table">
                            <thead>
                                <tr>
                                    <th class="student-cell">Student Details</th>
                                    @foreach($datesInRange as $dObj)
                                        @php
                                            $isSunday   = ($dObj->dayOfWeek === 0);
                                            $isSaturday = ($dObj->dayOfWeek === 6);
                                            $dateStr = $dObj->format('Y-m-d');
                                            $headerClass = $isSunday ? 'sunday-header' : ($isSaturday ? 'saturday-header' : '');
                                        @endphp
                                        <th class="date-column-header {{ $headerClass }}">
                                            <div style="font-weight:700; color:#2d3748;">{{ $dObj->format('d M') }}</div>
                                            <div style="font-size:10px; font-weight:700; margin-top:2px; text-transform:uppercase;
                                                {{ $isSunday ? 'color:#d97706;' : ($isSaturday ? 'color:#047857;' : 'color:#b45309;') }}">
                                                {{ $dObj->format('D') }}
                                                @if($isSunday)
                                                    <br><span style="font-size:8px; font-weight:800; color:#d97706;">Holiday</span>
                                                @elseif($isSaturday)
                                                    <br><span style="font-size:8px; font-weight:800; color:#047857;">Working</span>
                                                @endif
                                            </div>
                                            <!-- Set All Dropdown -->
                                            <select class="header-status-select" onchange="setAllColumnStatus('{{ $dateStr }}', this.value)" style="width: 100%; font-size: 10px; height: 24px; padding: 2px; margin-top: 6px; border-radius: 4px; border: 1px solid #cbd5e1; font-weight: 700; color: #475569; background: #fff; cursor: pointer;">
                                                <option value="">Set All</option>
                                                <option value="present">Present</option>
                                                <option value="absent">Absent</option>
                                                <option value="late">Late</option>
                                                <option value="half_day">Half Day</option>
                                                <option value="holiday">Holiday</option>
                                                <option value="leave">Leave</option>
                                                <option value="duty_leave">Duty Leave</option>
                                            </select>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $st)
                                    @php
                                        $bgColors = ['#9a3412', '#b45309', '#047857', '#1d4ed8', '#7c3aed', '#db2777'];
                                        $bgColor = $bgColors[$st->id % count($bgColors)];
                                    @endphp
                                    <tr>
                                        <td class="student-cell">
                                            <div class="student-profile">
                                                <div class="student-avatar" style="background-color: {{ $bgColor }};">
                                                    @if($st->photo)
                                                        <img src="{{ $st->photo_url }}" alt="Photo">
                                                    @else
                                                        {{ substr($st->first_name, 0, 1) }}
                                                    @endif
                                                </div>
                                                <div class="student-info">
                                                    <span class="student-name">{{ $st->full_name }}</span>
                                                    <span class="student-sub">Roll: {{ $st->roll_number ?? 'N/A' }} • ID: {{ $st->admission_number }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        @foreach($datesInRange as $dObj)
                                            @php
                                                $isSunday = ($dObj->dayOfWeek === 0);
                                                $dateStr  = $dObj->format('Y-m-d');
                                                $record   = isset($attendanceMatrix[$st->id][$dateStr]) ? $attendanceMatrix[$st->id][$dateStr] : null;
                                                // Sunday defaults to 'holiday' if not already marked
                                                $status   = $record ? $record->status : ($isSunday ? 'holiday' : 'not_marked');
                                            @endphp
                                            <td {{ $isSunday ? 'style=background:#fffbeb;' : '' }}>
                                                <div class="date-cell-container">
                                                    <!-- Status Select dropdown -->
                                                    <select name="attendance[{{ $st->id }}][{{ $dateStr }}][status]" class="status-select" data-date="{{ $dateStr }}" onchange="updateSelectColor(this)">
                                                        <option value="not_marked" {{ $status === 'not_marked' ? 'selected' : '' }}>Not Marked</option>
                                                        <option value="present" {{ $status === 'present' ? 'selected' : '' }}>Present</option>
                                                        <option value="absent" {{ $status === 'absent' ? 'selected' : '' }}>Absent</option>
                                                        <option value="late" {{ $status === 'late' ? 'selected' : '' }}>Late</option>
                                                        <option value="half_day" {{ $status === 'half_day' ? 'selected' : '' }}>Half Day</option>
                                                        <option value="holiday" {{ $status === 'holiday' ? 'selected' : '' }}>Holiday</option>
                                                        <option value="leave" {{ $status === 'leave' ? 'selected' : '' }}>Leave</option>
                                                        <option value="duty_leave" {{ $status === 'duty_leave' ? 'selected' : '' }}>Duty Leave</option>
                                                    </select>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="empty-grid">
                            <i class="fas fa-users-slash"></i>
                            <p>No active students found matching the selected filters.</p>
                        </div>
                    @endif
                </div>
            </div>
        </form>
    @else
        <div class="card" style="padding:48px; text-align:center;">
            <i class="fas fa-calendar-check" style="font-size:48px; color:#cbd5e1; margin-bottom:16px;"></i>
            <h3 style="color:#4a5568; margin-bottom:8px;">Please select Class and Section</h3>
            <p style="color:#718096; font-size:14px;">Select the class, section, and date range above to load the student bulk roll call sheet.</p>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    // Set all status selects in a date column
    function setAllColumnStatus(dateStr, status) {
        if (!status) return;
        document.querySelectorAll(`.status-select[data-date="${dateStr}"]`).forEach(function(select) {
            select.value = status;
            updateSelectColor(select);
        });
    }

    // Client-side section filter based on selected class
    function filterSections() {
        const classSelect = document.getElementById('classSelect');
        const sectionSelect = document.getElementById('sectionSelect');
        const selectedClassId = classSelect.value;

        // Reset section select if class changes
        let selectedSectionStillValid = false;
        
        for (let i = 0; i < sectionSelect.options.length; i++) {
            const option = sectionSelect.options[i];
            const optionClassId = option.getAttribute('data-class-id');

            if (!selectedClassId) {
                // If no class is selected, show all section options
                option.style.display = 'block';
            } else if (!optionClassId || optionClassId === selectedClassId) {
                // Show option if it belongs to selected class
                option.style.display = 'block';
                if (option.value === sectionSelect.value) {
                    selectedSectionStillValid = true;
                }
            } else {
                // Hide option if it doesn't belong to selected class
                option.style.display = 'none';
            }
        }

        if (!selectedSectionStillValid && selectedClassId) {
            sectionSelect.value = '';
        }
    }

    // Update color class of the select elements based on selected value (optimized)
    function updateSelectColor(selectEl) {
        const val = selectEl.value;
        selectEl.className = 'status-select select-' + val;
    }

    // Initialize select colors on document load
    document.addEventListener('DOMContentLoaded', function() {
        filterSections();
        document.querySelectorAll('.status-select').forEach(function(select) {
            updateSelectColor(select);
        });
    });

    // Slider scripts
    let currentSliderStep = 1;

    function openBulkAttendanceSlider() {
        // Populate class select
        let classSelect = $('#slider_class_id');
        classSelect.empty().append('<option value="">Select Class</option>');
        $('#classSelect option').each(function() {
            if ($(this).val()) {
                classSelect.append($(this).clone());
            }
        });

        // Copy selected class value from main page
        let currentClassVal = $('#classSelect').val();
        if (currentClassVal) {
            classSelect.val(currentClassVal);
        }

        // Populate sections
        filterSliderSections();

        // Copy selected section value from main page
        let currentSectionVal = $('#sectionSelect').val();
        if (currentSectionVal) {
            $('#slider_section_id').val(currentSectionVal);
        }

        toggleSliderTargetFields();
        calculateDefaultWorkingDays();

        $('#bulkSliderOverlay').addClass('active');
        $('#bulkSliderDrawer').addClass('active');
    }

    function closeBulkAttendanceSlider() {
        $('#bulkSliderOverlay').removeClass('active');
        $('#bulkSliderDrawer').removeClass('active');
        currentSliderStep = 1;
        showSliderStep(currentSliderStep);
    }

    function showSliderStep(step) {
        $('.slider-step').removeClass('active');
        $(`#sliderStep${step}`).addClass('active');
        
        if (step === 1) {
            $('#sliderPrevBtn').hide();
            $('#sliderNextBtn').show();
            $('#sliderSaveBtn').hide();
        } else {
            $('#sliderPrevBtn').show();
            $('#sliderNextBtn').hide();
            $('#sliderSaveBtn').show();
        }
    }

    function nextSliderStep() {
        let working = parseFloat($('#slider_working_days').val()) || 0;
        let present = parseFloat($('#slider_present_days').val()) || 0;
        if (working <= 0) {
            alert('Working days must be greater than 0.');
            return;
        }
        if (present < 0 || present > working) {
            alert('Present days must be between 0 and total working days.');
            return;
        }
        currentSliderStep = 2;
        showSliderStep(currentSliderStep);
    }

    function prevSliderStep() {
        currentSliderStep = 1;
        showSliderStep(currentSliderStep);
    }

    function toggleSliderTargetFields() {
        let type = $('#slider_apply_type').val();
        if (type === 'all') {
            $('#slider_class_group').hide();
            $('#slider_section_group').hide();
        } else if (type === 'class') {
            $('#slider_class_group').show();
            $('#slider_section_group').hide();
        } else {
            $('#slider_class_group').show();
            $('#slider_section_group').show();
        }
    }

    function filterSliderSections() {
        let classId = $('#slider_class_id').val();
        let sectionSelect = $('#slider_section_id');
        sectionSelect.empty().append('<option value="">Select Section</option>');
        
        if (classId) {
            $('#sectionSelect option').each(function() {
                let optionClassId = $(this).attr('data-class-id');
                if (optionClassId == classId) {
                    sectionSelect.append($(this).clone());
                }
            });
        }
    }

    function calculateDefaultWorkingDays() {
        let fromStr = $('#slider_from_date').val();
        let toStr = $('#slider_to_date').val();
        if (!fromStr || !toStr) return;

        let fromDate = new Date(fromStr);
        let toDate = new Date(toStr);
        let count = 0;
        let temp = new Date(fromDate);
        while (temp <= toDate) {
            let day = temp.getDay();
            if (day !== 0 && day !== 6) { // Skip Sat and Sun
                count++;
            }
            temp.setDate(temp.getDate() + 1);
        }
        $('#slider_working_days').val(count);
        $('#slider_present_days').val(count);
        updateSliderPercentage();
    }

    function updateSliderPercentage() {
        let working = parseFloat($('#slider_working_days').val()) || 0;
        let present = parseFloat($('#slider_present_days').val()) || 0;
        let pct = 0;
        if (working > 0) {
            pct = Math.round((present / working) * 100);
        }
        $('#slider_percentage_badge').text(pct + '%');
        if (pct >= 75) {
            $('#slider_percentage_badge').css({'background': '#d1fae5', 'color': '#065f46'});
        } else {
            $('#slider_percentage_badge').css({'background': '#fee2e2', 'color': '#991b1b'});
        }
    }

    function saveSliderBulkAttendance() {
        let fromDate = $('#slider_from_date').val();
        let toDate = $('#slider_to_date').val();
        let workingDays = $('#slider_working_days').val();
        let presentDays = $('#slider_present_days').val();
        let applyType = $('#slider_apply_type').val();
        let classId = $('#slider_class_id').val();
        let sectionId = $('#slider_section_id').val();
        let academicSessionId = $('input[name="academic_session_id"]').val() || $('#bulkFilterForm select[name="academic_session_id"]').val() || $('#academic_session_id').val();

        if (applyType === 'class' && !classId) {
            alert('Please select a class.');
            return;
        }
        if (applyType === 'section' && (!classId || !sectionId)) {
            alert('Please select class and section.');
            return;
        }

        // Show loading state
        let btn = $('#sliderSaveBtn');
        btn.prop('disabled', true).text('SAVING...');

        $.ajax({
            url: "{{ route('school.student-mgmt.bulk-attendance.slider') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                from_date: fromDate,
                to_date: toDate,
                working_days: workingDays,
                present_days: presentDays,
                apply_type: applyType,
                class_id: classId,
                section_id: sectionId,
                academic_session_id: academicSessionId
            },
            success: function(response) {
                if (response.success) {
                    alert('Attendance applied and updated successfully for targets.');
                    closeBulkAttendanceSlider();
                    window.location.reload();
                } else {
                    alert('Error: ' + response.message);
                    btn.prop('disabled', false).text('APPLY & SAVE');
                }
            },
            error: function() {
                alert('Network or server error. Please try again.');
                btn.prop('disabled', false).text('APPLY & SAVE');
            }
        });
    }
</script>

<!-- Slider Overlay and Drawer -->
<div class="bulk-slider-overlay" id="bulkSliderOverlay" onclick="closeBulkAttendanceSlider()"></div>
<div class="bulk-slider-drawer" id="bulkSliderDrawer">
    <div class="slider-header">
        <h3>Bulk Attendance Parameters</h3>
        <button type="button" class="close-btn" onclick="closeBulkAttendanceSlider()">&times;</button>
    </div>
    
    <div class="slider-body">
        <!-- Step 1: Input Working & Present Days -->
        <div class="slider-step active" id="sliderStep1">
            <h4 style="font-weight: 700; font-size: 14px; margin-bottom: 16px; color: #023c4d;">Step 1: Set Attendance Duration & Days</h4>
            
            <div class="slider-field">
                <label>From Date</label>
                <input type="date" id="slider_from_date" class="slider-input" value="{{ $fromDate }}" onchange="calculateDefaultWorkingDays()">
            </div>
            
            <div class="slider-field">
                <label>To Date</label>
                <input type="date" id="slider_to_date" class="slider-input" value="{{ $toDate }}" onchange="calculateDefaultWorkingDays()">
            </div>

            <div class="slider-field">
                <label>Total Working Days</label>
                <input type="number" id="slider_working_days" class="slider-input" value="{{ $totalDays }}" min="1" oninput="updateSliderPercentage()">
            </div>

            <div class="slider-field">
                <label>Total Present Days</label>
                <input type="number" id="slider_present_days" class="slider-input" value="{{ $totalDays }}" min="0" oninput="updateSliderPercentage()">
            </div>

            <div style="background: #f8fafc; border-radius: 8px; padding: 16px; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
                <span style="font-size: 13px; font-weight: 700; color: #475569;">Attendance Rate:</span>
                <span id="slider_percentage_badge" style="font-size: 14px; font-weight: 800; padding: 6px 12px; border-radius: 20px; background: #d1fae5; color: #065f46;">100%</span>
            </div>
        </div>

        <!-- Step 2: Apply to Classes & Sections -->
        <div class="slider-step" id="sliderStep2">
            <h4 style="font-weight: 700; font-size: 14px; margin-bottom: 16px; color: #023c4d;">Step 2: Choose Targets</h4>

            <div class="slider-field">
                <label>Apply To</label>
                <select id="slider_apply_type" class="slider-select" onchange="toggleSliderTargetFields()">
                    <option value="section">Specific Section</option>
                    <option value="class">Specific Class</option>
                    <option value="all">All Classes & Sections</option>
                </select>
            </div>

            <div class="slider-field" id="slider_class_group">
                <label>Select Class</label>
                <select id="slider_class_id" class="slider-select" onchange="filterSliderSections()">
                    <!-- Will be populated dynamically from page -->
                </select>
            </div>

            <div class="slider-field" id="slider_section_group">
                <label>Select Section</label>
                <select id="slider_section_id" class="slider-select">
                    <!-- Will be populated dynamically from page -->
                </select>
            </div>
        </div>
    </div>

    <div class="slider-footer">
        <button type="button" class="btn" id="sliderPrevBtn" onclick="prevSliderStep()" style="border: 1px solid #cbd5e1; background: #fff; color: #475569; padding: 10px 20px; border-radius: 6px; font-weight: 700; display: none;">BACK</button>
        <div style="flex: 1;"></div>
        <button type="button" class="btn" id="sliderNextBtn" onclick="nextSliderStep()" style="background: #9a3412; border-color: #9a3412; color: #fff; padding: 10px 24px; font-weight: 700; border-radius: 6px;">NEXT</button>
        <button type="button" class="btn" id="sliderSaveBtn" onclick="saveSliderBulkAttendance()" style="background: #9a3412; border-color: #9a3412; color: #fff; padding: 10px 24px; font-weight: 700; border-radius: 6px; display: none;">APPLY & SAVE</button>
    </div>
</div>
@endsection
