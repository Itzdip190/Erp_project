@extends('layouts.app')

@section('page-title', 'Student Attendance')

@section('content')
<style>
    /* Styling for the Filter Section and Cards */
    .filter-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 12px;
        box-shadow: var(--shadow);
        padding: 20px;
        margin-bottom: 24px;
    }
    
    .filter-grid-2 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        align-items: flex-end;
    }
    
    /* Stats Cards Styles */
    .stats-container {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 12px;
        margin-bottom: 24px;
    }
    
    @media (max-width: 1200px) {
        .stats-container {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    
    @media (max-width: 768px) {
        .stats-container {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    .stat-card {
        display: flex;
        border-radius: 8px;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    .stat-card-left {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 30%;
        min-width: 44px;
        font-size: 20px;
    }
    
    .stat-card-right {
        display: flex;
        flex-direction: column;
        justify-content: center;
        width: 70%;
        padding: 12px 16px;
    }
    
    .stat-card-count {
        font-size: 24px;
        font-weight: 800;
        line-height: 1;
    }
    
    .stat-card-label {
        font-size: 11px;
        font-weight: 700;
        opacity: 0.9;
        text-transform: uppercase;
        margin-top: 4px;
        letter-spacing: 0.5px;
    }
    
    /* Stats Colors matching the design */
    .stat-present { background-color: #10b981; }
    .stat-present .stat-card-left { background-color: #059669; }
    
    .stat-absent { background-color: #ef4444; }
    .stat-absent .stat-card-left { background-color: #b91c1c; }
    
    .stat-halfday { background-color: #eab308; }
    .stat-halfday .stat-card-left { background-color: #a16207; }
    
    .stat-leave { background-color: #d97706; }
    .stat-leave .stat-card-left { background-color: #9a3412; }
    
    .stat-duty-leave { background-color: #ec4899; }
    .stat-duty-leave .stat-card-left { background-color: #be185d; }
    
    .stat-not-marked { background-color: #9ca3af; }
    .stat-not-marked .stat-card-left { background-color: #4b5563; }
    
    /* Edit mode toggle rules */
    .in-edit-mode .view-only-block { display: none !important; }
    .in-edit-mode .view-only-inline { display: none !important; }
    .in-edit-mode .edit-only-block { display: block !important; }
    .in-edit-mode .edit-only-inline { display: inline !important; }
    .in-edit-mode .edit-only-flex { display: flex !important; }
    
    /* Styling elements */
    .btn-outline-gold {
        border: 1px solid #b45309;
        color: #b45309;
        background: transparent;
        font-weight: 700;
        transition: all 0.2s;
    }
    .btn-outline-gold:hover {
        background: rgba(180, 83, 9, 0.05);
    }

    /* ── Student Daily Attendance Dark Mode Overrides ── */
    body.dark-mode .page-hdr h1 {
        color: #f8fafc !important;
    }
    body.dark-mode .page-hdr p {
        color: #cbd5e1 !important;
    }
    body.dark-mode .filter-card {
        background-color: #111827 !important;
        border-color: #1e293b !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3) !important;
    }
    body.dark-mode .form-label {
        color: #cbd5e1 !important;
    }
    body.dark-mode .form-control {
        background-color: #1f2937 !important;
        border-color: #374151 !important;
        color: #f8fafc !important;
    }
    body.dark-mode .form-control:focus {
        border-color: #4b5563 !important;
        background-color: #1f2937 !important;
    }
    body.dark-mode select.form-control option {
        background-color: #1f2937 !important;
        color: #f8fafc !important;
    }
    body.dark-mode .btn-outline-gold {
        background-color: #1f2937 !important;
        border-color: #f59e0b !important;
        color: #f59e0b !important;
    }
    body.dark-mode .btn-outline-gold:hover {
        background-color: rgba(245, 158, 11, 0.2) !important;
    }
    body.dark-mode .btn-icon {
        background-color: #1f2937 !important;
        border-color: #374151 !important;
        color: #cbd5e1 !important;
    }
    body.dark-mode .btn-icon:hover {
        background-color: #374151 !important;
        color: #ffffff !important;
    }
    body.dark-mode #show_logs_btn {
        background-color: #1f2937 !important;
        border-color: #f59e0b !important;
        color: #f59e0b !important;
    }
    body.dark-mode #show_logs_btn:hover {
        background-color: rgba(245, 158, 11, 0.2) !important;
    }
    body.dark-mode #attendanceSaveForm .card {
        background-color: #111827 !important;
        border-color: #1e293b !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3) !important;
    }
    body.dark-mode #form-buttons-container {
        background-color: #111827 !important;
        border-top-color: #1e293b !important;
    }
    body.dark-mode #btn-cancel-edit {
        background-color: #1f2937 !important;
        border-color: #374151 !important;
        color: #cbd5e1 !important;
    }
    body.dark-mode #btn-cancel-edit:hover {
        background-color: #374151 !important;
        color: #ffffff !important;
    }
    body.dark-mode #attendanceTableContainer {
        background-color: #111827 !important;
        color: #cbd5e1 !important;
    }

    /* Excel Export Slider Drawer Styles */
    .excel-slider-overlay {
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
    .excel-slider-overlay.active {
        display: block;
        opacity: 1;
    }
    .excel-slider-drawer {
        position: fixed;
        top: 0;
        right: -80%;
        width: 80%;
        height: 100vh;
        background: #ffffff;
        box-shadow: -10px 0 30px rgba(0, 0, 0, 0.15);
        z-index: 10000;
        transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        font-family: 'Inter', sans-serif;
    }
    .excel-slider-drawer.active {
        right: 0;
    }
    body.dark-mode .excel-slider-drawer {
        background: #111827;
        color: #f8fafc;
        box-shadow: -10px 0 30px rgba(0, 0, 0, 0.5);
    }
    .excel-slider-drawer .slider-header {
        padding: 20px 24px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #023c4d;
        color: #ffffff;
    }
    body.dark-mode .excel-slider-drawer .slider-header {
        background: #1f2937;
        border-bottom-color: #374151;
    }
    .excel-slider-drawer .slider-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 800;
        color: #ffffff;
    }
    .excel-slider-drawer .slider-header .close-btn {
        background: none;
        border: none;
        color: #ffffff;
        font-size: 24px;
        cursor: pointer;
        line-height: 1;
    }
    .excel-slider-drawer .slider-body {
        flex: 1;
        padding: 24px;
        overflow-y: auto;
    }
    .excel-slider-drawer .slider-footer {
        padding: 16px 24px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8fafc;
    }
    body.dark-mode .excel-slider-drawer .slider-footer {
        background: #1f2937;
        border-top-color: #374151;
    }
    .excel-slider-drawer .slider-field {
        margin-bottom: 20px;
        display: flex;
        flex-direction: column;
        text-align: left;
    }
    .excel-slider-drawer .slider-field label {
        font-size: 11px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        margin-bottom: 6px;
        letter-spacing: 0.5px;
    }
    body.dark-mode .excel-slider-drawer .slider-field label {
        color: #cbd5e1;
    }
    .excel-slider-drawer .slider-input, .excel-slider-drawer .slider-select {
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
        width: 100%;
    }
    body.dark-mode .excel-slider-drawer .slider-input, body.dark-mode .excel-slider-drawer .slider-select {
        background: #1f2937;
        border-color: #374151;
        color: #f8fafc;
    }

    /* Excel Grid Spreadsheet styling */
    .excel-preview-table {
        border-collapse: collapse;
        font-family: 'Consolas', 'Courier New', monospace;
        font-size: 11px;
        background: #fff;
        width: 100%;
    }
    .excel-preview-table th, .excel-preview-table td {
        border: 1px solid #cbd5e1;
        padding: 6px 8px;
        min-width: 50px;
        white-space: nowrap;
        text-align: left;
    }
    .excel-preview-table thead tr:first-child th {
        background: #f1f5f9;
        color: #475569;
        font-weight: 800;
        text-align: center;
        border-bottom: 2px solid #cbd5e1;
        font-size: 9px;
    }
    .excel-preview-table thead tr:nth-child(2) th {
        background: #e2e8f0;
        color: #1e293b;
        font-weight: 700;
        text-align: center;
    }
    .excel-preview-table tbody td.row-num {
        background: #f1f5f9;
        color: #475569;
        font-weight: 800;
        text-align: center;
        border-right: 2px solid #cbd5e1;
        font-size: 9px;
    }
    body.dark-mode .excel-preview-table {
        background: #111827;
    }
    body.dark-mode .excel-preview-table th, body.dark-mode .excel-preview-table td {
        border-color: #374151;
        color: #cbd5e1;
    }
    body.dark-mode .excel-preview-table thead tr:first-child th {
        background: #1f2937;
        color: #9ca3af;
        border-bottom-color: #374151;
    }
    body.dark-mode .excel-preview-table thead tr:nth-child(2) th {
        background: #374151;
        color: #f8fafc;
    }
    body.dark-mode .excel-preview-table tbody td.row-num {
        background: #1f2937;
        color: #9ca3af;
        border-right-color: #374151;
    }
</style>

<div class="page-hdr" style="margin-bottom: 20px;">
    <div class="page-hdr-left">
        <h1 style="font-size: 24px; font-weight: 800; color: var(--navy); display: flex; align-items: center; gap: 8px;">
            Student Attendance 
            <span style="display: inline-flex; align-items: center; justify-content: center; width: 20px; height: 20px; border-radius: 50%; background: #f97316; color: #fff; font-size: 10px; cursor: pointer;">
                <i class="fas fa-chevron-down"></i>
            </span>
        </h1>
        <p style="font-size: 13px; color: var(--t3); margin: 4px 0 0 0;">Student Management</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success" style="margin-bottom:20px; padding:12px 16px; border-radius:8px; background:#f0fdf4; border:1px solid #a7f3d0; color:#15803d; font-size:13px; font-weight:600;">
        <i class="fas fa-check-circle" style="margin-right:8px;"></i>{{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger" style="margin-bottom:20px; padding:12px 16px; border-radius:8px; background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; font-size:13px; font-weight:600;">
        <i class="fas fa-exclamation-circle" style="margin-right:8px;"></i>
        <ul style="margin:0; padding-left:16px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Filter Section -->
<div class="filter-card">
    <!-- Row 1: Academic Session, Date, Export Actions -->
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 18px; flex-wrap: wrap; gap: 16px;">
        <div style="display: flex; gap: 16px; flex-wrap: wrap;">
            <div class="form-group" style="margin-bottom: 0; min-width: 220px;">
                <label class="form-label" style="font-weight: 700; font-size: 12px; color: var(--t2); margin-bottom: 6px;">Academic Year *</label>
                <div style="position: relative;">
                    <i class="far fa-calendar-alt" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--t3);"></i>
                    <select id="academic_session_id" class="form-control" style="padding-left: 36px; height: 42px; border-radius: 8px; font-size: 13.5px; color: var(--t1); border: 1px solid #cbd5e1;" required>
                        @foreach($academicSessions as $ses)
                            <option value="{{ $ses->id }}" {{ $ses->is_current ? 'selected' : '' }}>{{ $ses->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 0; min-width: 220px;">
                <label class="form-label" style="font-weight: 700; font-size: 12px; color: var(--t2); margin-bottom: 6px;">Pick Date</label>
                <div style="position: relative;">
                    <i class="far fa-calendar" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--t3); pointer-events: none;"></i>
                    <input type="date" id="attendance_date" class="form-control" value="{{ date('Y-m-d') }}" style="padding-left: 36px; height: 42px; border-radius: 8px; font-size: 13.5px; color: var(--t1); border: 1px solid #cbd5e1;" required>
                </div>
            </div>
        </div>
        
        <div style="display: flex; gap: 8px; align-items: center;">
            <button type="button" id="btn-view-excel-slider" class="btn btn-outline-gold" style="height: 42px; border-radius: 8px; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; font-size: 12px; padding: 0 16px;">
                <i class="far fa-file-excel"></i> VIEW EXCEL
            </button>
            <button type="button" id="btn-download-register" class="btn btn-outline-gold" style="height: 42px; border-radius: 8px; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; font-size: 12px; padding: 0 16px;">
                <i class="fas fa-download"></i> DOWNLOAD
            </button>
            <button type="button" class="btn-icon" style="height: 42px; width: 42px; border-radius: 8px; border: 1px solid #cbd5e1; display: inline-flex; align-items: center; justify-content: center; background: #fff; color: #64748b; font-size: 14px;">
                <i class="far fa-comment-alt"></i>
            </button>
            <button type="button" class="btn-icon" style="height: 42px; width: 42px; border-radius: 8px; border: 1px solid #cbd5e1; display: inline-flex; align-items: center; justify-content: center; background: #fff; color: #64748b; font-size: 14px;">
                <i class="fas fa-cog"></i>
            </button>
        </div>
    </div>
    
    <!-- Row 2: Select Class, Section, Search, Status filter -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; align-items: end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" style="font-weight: 700; font-size: 12px; color: var(--t2); margin-bottom: 6px;">Select Class</label>
            <div style="position: relative;">
                <i class="fas fa-book" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--t3);"></i>
                <select id="class_id" class="form-control" style="padding-left: 34px; height: 42px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13.5px;" required>
                    <option value="">Select Class</option>
                    @foreach($classes as $cls)
                        <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" style="font-weight: 700; font-size: 12px; color: var(--t2); margin-bottom: 6px;">Select Section</label>
            <div style="position: relative;">
                <i class="fas fa-book" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--t3);"></i>
                <select id="section_id" class="form-control" style="padding-left: 34px; height: 42px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13.5px;" required>
                    <option value="">Select Section</option>
                </select>
            </div>
        </div>
        
        <div class="form-group" style="margin-bottom: 0; grid-column: span 2;">
            <label class="form-label" style="font-weight: 700; font-size: 12px; color: var(--t2); margin-bottom: 6px;">Search</label>
            <div style="position: relative;">
                <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--t3);"></i>
                <input type="text" id="search_student" class="form-control" placeholder="Student Name" style="padding-left: 34px; height: 42px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13.5px;">
            </div>
        </div>
        
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" style="font-weight: 700; font-size: 12px; color: var(--t2); margin-bottom: 6px;">Select Status</label>
            <div style="position: relative;">
                <i class="fas fa-folder" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--t3);"></i>
                <select id="status_filter" class="form-control" style="padding-left: 34px; height: 42px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13.5px;">
                    <option value="">All Statuses</option>
                    <option value="present">Present</option>
                    <option value="half_day">Half Day</option>
                    <option value="absent">Absent</option>
                    <option value="leave">Leave</option>
                    <option value="duty_leave">Duty Leave</option>
                    <option value="not_marked">Not Marked</option>
                </select>
            </div>
        </div>
        
        <div>
            <button type="button" id="show_logs_btn" class="btn" style="height: 42px; border-radius: 8px; border: 1px solid #b45309; color: #b45309; background: transparent; font-weight: 700; font-size: 12px; width: 100%;">
                SHOW LOGS
            </button>
        </div>
    </div>
</div>

<!-- Stats Counter Badges -->
<div class="stats-container">
    <!-- Present -->
    <div class="stat-card stat-present">
        <div class="stat-card-left"><i class="fas fa-check" style="font-size: 16px;"></i></div>
        <div class="stat-card-right">
            <div class="stat-card-count" id="count-present">0</div>
            <div class="stat-card-label">Present</div>
        </div>
    </div>
    
    <!-- Absent -->
    <div class="stat-card stat-absent">
        <div class="stat-card-left"><i class="fas fa-times" style="font-size: 16px;"></i></div>
        <div class="stat-card-right">
            <div class="stat-card-count" id="count-absent">0</div>
            <div class="stat-card-label">Absent</div>
        </div>
    </div>
    
    <!-- Half Day -->
    <div class="stat-card stat-halfday">
        <div class="stat-card-left"><i class="fas fa-times" style="font-size: 16px; transform: rotate(45deg);"></i></div>
        <div class="stat-card-right">
            <div class="stat-card-count" id="count-halfday">0</div>
            <div class="stat-card-label">HalfDay</div>
        </div>
    </div>
    
    <!-- Leave -->
    <div class="stat-card stat-leave">
        <div class="stat-card-left"><i class="fas fa-check" style="font-size: 16px;"></i></div>
        <div class="stat-card-right">
            <div class="stat-card-count" id="count-leave">0</div>
            <div class="stat-card-label">Leave</div>
        </div>
    </div>
    
    <!-- Duty Leave -->
    <div class="stat-card stat-duty-leave">
        <div class="stat-card-left"><i class="fas fa-check" style="font-size: 16px;"></i></div>
        <div class="stat-card-right">
            <div class="stat-card-count" id="count-duty-leave">0</div>
            <div class="stat-card-label">Duty Leave</div>
        </div>
    </div>
    
    <!-- Not Marked -->
    <div class="stat-card stat-not-marked">
        <div class="stat-card-left"><i class="fas fa-ban" style="font-size: 16px;"></i></div>
        <div class="stat-card-right">
            <div class="stat-card-count" id="count-not-marked">0</div>
            <div class="stat-card-label">NOT MARKED</div>
        </div>
    </div>
</div>

<!-- Marking Form -->
<form action="{{ route('school.attendance.students.store') }}" method="POST" id="attendanceSaveForm" style="display: none;">
    @csrf
    <input type="hidden" name="section_id" id="form_section_id">
    <input type="hidden" name="date" id="form_date">
    <input type="hidden" name="academic_session_id" id="form_academic_session_id">

    <div class="card" style="border-radius: 12px; overflow: hidden; border: 1px solid var(--border); box-shadow: var(--shadow);">
        <div class="card-body" style="padding: 0;">
            <!-- Top Buttons Container -->
            <div id="form-buttons-container" style="padding: 16px 20px; border-bottom: 1px solid var(--border); background: #f8fafc; display: flex; justify-content: flex-end; align-items: center;">
                <!-- View Mode Button -->
                <button type="button" class="btn" id="btn-mark-attendance" onclick="enterEditMode()" style="background-color: #b45309; border-color: #b45309; color: #fff; padding: 10px 24px; font-weight: 700; border-radius: 6px;">
                    MARK ATTENDANCE
                </button>
                
                <!-- Edit Mode Buttons -->
                <button type="button" class="btn btn-outline" id="btn-cancel-edit" onclick="exitEditMode()" style="display: none; padding: 10px 24px; font-weight: 700; border-radius: 6px; margin-right: 12px; border: 1px solid #cbd5e1; background: #fff; color: #475569;">
                    CANCEL
                </button>
                <button type="submit" class="btn" id="btn-save-attendance" style="display: none; background-color: #b45309; border-color: #b45309; color: #fff; padding: 10px 24px; font-weight: 700; border-radius: 6px;">
                    SAVE
                </button>
            </div>

            <div id="attendanceTableContainer" style="padding: 20px; color: var(--t3); text-align: center; background: #fff;">
                <div style="padding: 24px; color: var(--t2); font-weight: 500;">
                    Select Class and Section above to view student registers.
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Excel Export Slider Overlay and Drawer -->
<div class="excel-slider-overlay" id="excelSliderOverlay" onclick="closeExcelSlider()"></div>
<div class="excel-slider-drawer" id="excelSliderDrawer">
    <div class="slider-header">
        <h3>Excel Export Options</h3>
        <button type="button" class="close-btn" onclick="closeExcelSlider()">&times;</button>
    </div>
    
    <div class="slider-body">
        <div style="display: flex; gap: 16px;">
            <div class="slider-field" style="flex: 1;">
                <label>Export Type</label>
                <select id="slider_export_type" class="slider-select" onchange="toggleSliderFields()">
                    <option value="daily">Daily Attendance Register</option>
                    <option value="monthly">Monthly Attendance Summary</option>
                </select>
            </div>
            
            <div class="slider-field" id="slider_month_group" style="display: none; flex: 1;">
                <label>Select Month</label>
                <select id="slider_month" class="slider-select">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ sprintf('%02d', $m) }}" {{ date('m') == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                    @endfor
                </select>
            </div>

            <div class="slider-field" id="slider_year_group" style="display: none; flex: 1;">
                <label>Select Year</label>
                <select id="slider_year" class="slider-select">
                    @for ($y = date('Y') - 2; $y <= date('Y') + 2; $y++)
                        <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>

        <!-- Live Spreadsheet Preview Grid -->
        <div class="slider-field" style="margin-top: 20px;">
            <label>Live Spreadsheet Preview</label>
            <div id="excel_preview_container" style="overflow: auto; max-height: 480px; border: 1px solid #cbd5e1; border-radius: 6px; background: #f8fafc; padding: 2px;">
                <div style="padding: 24px; text-align: center; color: #64748b; font-weight: 600;">
                    Select parameters to load preview.
                </div>
            </div>
        </div>
    </div>

    <div class="slider-footer">
        <button type="button" class="btn btn-outline" onclick="closeExcelSlider()" style="border: 1px solid #cbd5e1; background: #fff; color: #475569; padding: 10px 20px; border-radius: 6px; font-weight: 700;">CANCEL</button>
        <button type="button" class="btn" id="sliderDownloadBtn" onclick="downloadExcelFromSlider()" style="background: #9a3412; border-color: #9a3412; color: #fff; padding: 10px 24px; font-weight: 700; border-radius: 6px;">DOWNLOAD EXCEL</button>
    </div>
</div>

@endsection

@section('scripts')
<script>
const allSections = @json($sections);
const sessions = @json($academicSessions);

// Handle class change to filter sections
$('#class_id').on('change', function() {
    let classId = $(this).val();
    let sectionSelect = $('#section_id');
    sectionSelect.empty().append('<option value="">Select Section</option>');
    if (classId) {
        let filtered = allSections.filter(s => s.class_id == classId);
        filtered.forEach(function(sec) {
            sectionSelect.append('<option value="' + sec.id + '">' + sec.name + '</option>');
        });
    }
});

// Auto-retrieve when parameters are selected
$('#class_id, #section_id, #attendance_date, #academic_session_id').on('change', function() {
    if (this.id === 'academic_session_id') {
        updateDateInputBoundaries();
    }
    triggerRetrieveStudents();
});

// Update min/max attributes on pick date field based on selected academic session dates
function updateDateInputBoundaries() {
    let sessionId = $('#academic_session_id').val();
    let dateInput = $('#attendance_date');
    if (!sessionId) return;
    
    let session = sessions.find(s => s.id == sessionId);
    if (session) {
        // Set calendar boundaries dynamically
        dateInput.attr('min', session.start_date);
        dateInput.attr('max', session.end_date);
        
        // Correct date if currently out of boundaries
        let val = dateInput.val();
        if (val) {
            let currentDate = new Date(val);
            currentDate.setHours(0,0,0,0);
            let startDate = new Date(session.start_date);
            startDate.setHours(0,0,0,0);
            let endDate = new Date(session.end_date);
            endDate.setHours(0,0,0,0);
            
            if (currentDate < startDate) {
                dateInput.val(session.start_date);
            } else if (currentDate > endDate) {
                dateInput.val(session.end_date);
            }
        }
    }
}

// Initial boundaries setup
updateDateInputBoundaries();

// Initialize filters from URL query parameters (useful after save redirects)
$(document).ready(function() {
    let urlParams = new URLSearchParams(window.location.search);
    let classId = urlParams.get('class_id');
    let sectionId = urlParams.get('section_id');
    let date = urlParams.get('date');
    let academicSessionId = urlParams.get('academic_session_id');

    if (academicSessionId) {
        $('#academic_session_id').val(academicSessionId);
        updateDateInputBoundaries();
    }
    if (date) {
        $('#attendance_date').val(date);
    }
    if (classId) {
        $('#class_id').val(classId).trigger('change');
        if (sectionId) {
            // Wait for section dropdown to populate on class change
            setTimeout(function() {
                $('#section_id').val(sectionId).trigger('change');
            }, 150);
        }
    }
});

function triggerRetrieveStudents() {
    let sectionId = $('#section_id').val();
    let date      = $('#attendance_date').val();
    let sessionId = $('#academic_session_id').val();
    
    if (!sectionId || !date || !sessionId) {
        $('#attendanceSaveForm').hide();
        return;
    }

    $('#form_section_id').val(sectionId);
    $('#form_date').val(date);
    $('#form_academic_session_id').val(sessionId);

    // Switch back to view mode on reload
    exitEditModeStyles();

    let container = $('#attendanceTableContainer');
    container.html('<div style="padding:24px;text-align:center;color:var(--t2);"><i class="fas fa-spinner fa-spin" style="font-size:20px;color:#d97706;display:block;margin-bottom:8px;"></i>Loading student registers...</div>');
    $('#attendanceSaveForm').show();

    $.ajax({
        url: "{{ route('school.attendance.students.load') }}",
        type: "POST",
        data: { 
            _token: "{{ csrf_token() }}",
            section_id: sectionId, 
            date: date, 
            academic_session_id: sessionId 
        },
        success: function(response) {
            if (response.success) {
                container.html(response.html);
            } else {
                container.html('<div style="padding:20px;color:var(--red);"><i class="fas fa-exclamation-circle"></i> Failed to load students.</div>');
            }
        },
        error: function() {
            container.html('<div style="padding:20px;color:var(--red);"><i class="fas fa-exclamation-circle"></i> Network error. Please try again.</div>');
        }
    });
}

// Enter Edit/Marking Mode
function enterEditMode() {
    $('#attendanceSaveForm').addClass('in-edit-mode');
    $('#btn-mark-attendance').hide();
    $('#btn-cancel-edit').show();
    $('#btn-save-attendance').show();
    updateCounts();
}

// Reset Styles back to View Mode
function exitEditModeStyles() {
    $('#attendanceSaveForm').removeClass('in-edit-mode');
    $('#btn-mark-attendance').show();
    $('#btn-cancel-edit').hide();
    $('#btn-save-attendance').hide();
}

// Exit Edit Mode and reload original values
function exitEditMode() {
    exitEditModeStyles();
    triggerRetrieveStudents();
}

// Set All status in header (toggle style)
function setAllStatus(status) {
    let className = `.status-btn.btn-${status === 'half_day' ? 'hd' : (status === 'duty_leave' ? 'dl' : status.charAt(0))} input`;
    let radios = $(className);
    let allChecked = true;
    radios.each(function() {
        if (!$(this).prop('checked')) {
            allChecked = false;
        }
    });

    if (allChecked) {
        radios.prop('checked', false).trigger('change');
    } else {
        radios.prop('checked', true).trigger('change');
    }
}


// Clear all selected radios
function clearAllAttendance() {
    $('.status-radio').prop('checked', false).trigger('change');
}

// Dynamic counter stats updater
function updateCounts() {
    let total = $('#attendanceTableBody tr[data-student-id]').length;
    let present = 0;
    let absent = 0;
    let halfday = 0;
    let leave = 0;
    let duty_leave = 0;
    let not_marked = 0;

    let isEditMode = $('#attendanceSaveForm').hasClass('in-edit-mode');

    $('#attendanceTableBody tr[data-student-id]').each(function() {
        let row = $(this);
        let status = 'not_marked';
        
        if (isEditMode) {
            let checkedRadio = row.find('input[type="radio"]:checked');
            if (checkedRadio.length > 0) {
                status = checkedRadio.val();
            }
        } else {
            status = row.attr('data-status') || 'not_marked';
        }

        if (status === 'present') present++;
        else if (status === 'absent') absent++;
        else if (status === 'half_day') halfday++;
        else if (status === 'leave') leave++;
        else if (status === 'duty_leave') duty_leave++;
        else not_marked++;
    });

    // Update stats counters
    $('#count-present').text(present);
    $('#count-absent').text(absent);
    $('#count-halfday').text(halfday);
    $('#count-leave').text(leave);
    $('#count-duty-leave').text(duty_leave);
    $('#count-not-marked').text(not_marked);
}

// Event listeners for radio button updates
$(document).on('change', '.status-radio', function() {
    updateCounts();
    $(this).closest('tr').attr('data-status', $(this).val());
    filterTable();
});

// Event listener for search and status filters
$(document).on('input', '#search_student', function() {
    filterTable();
});

$(document).on('change', '#status_filter', function() {
    filterTable();
});

function filterTable() {
    let searchText = $('#search_student').val().toLowerCase();
    let statusFilter = $('#status_filter').val();
    
    $('#attendanceTableBody tr[data-student-id]').each(function() {
        let row = $(this);
        let name = row.find('.student-name').text().toLowerCase();
        let roll = row.find('.student-roll').text().toLowerCase();
        let status = row.attr('data-status') || 'not_marked';
        
        let matchesSearch = name.includes(searchText) || roll.includes(searchText);
        let matchesStatus = !statusFilter || (statusFilter === 'not_marked' && status === 'not_marked') || (status === statusFilter);
        
        if (matchesSearch && matchesStatus) {
            row.show();
        } else {
            row.hide();
        }
    });
}

// File Attachment icon update
function updateAttachmentIcon(input) {
    let label = $(input).closest('.attachment-btn');
    if (input.files && input.files.length > 0) {
        label.css('color', '#10b981');
        label.attr('title', 'File Selected: ' + input.files[0].name);
    } else {
        label.css('color', '#d97706');
        label.attr('title', 'Upload Attachment');
    }
}

// Excel Export Slider functions
function openExcelSlider() {
    let classId = $('#class_id').val();
    let sectionId = $('#section_id').val();
    if (!classId || !sectionId) {
        alert('Please select Class and Section first.');
        return;
    }
    $('#excelSliderOverlay').addClass('active');
    $('#excelSliderDrawer').addClass('active');
    loadSpreadsheetPreview();
}

function closeExcelSlider() {
    $('#excelSliderOverlay').removeClass('active');
    $('#excelSliderDrawer').removeClass('active');
}

function toggleSliderFields() {
    let type = $('#slider_export_type').val();
    if (type === 'monthly') {
        $('#slider_month_group').show();
        $('#slider_year_group').show();
    } else {
        $('#slider_month_group').hide();
        $('#slider_year_group').hide();
    }
}

function loadSpreadsheetPreview() {
    let type = $('#slider_export_type').val();
    let classId = $('#class_id').val();
    let sectionId = $('#section_id').val();
    let academicSessionId = $('#academic_session_id').val();
    let date = $('#attendance_date').val();
    let month = $('#slider_month').val();
    let year = $('#slider_year').val();
    
    let container = $('#excel_preview_container');
    container.html('<div style="padding:24px;text-align:center;font-weight:600;color:#64748b;"><i class="fas fa-spinner fa-spin" style="font-size:18px;color:#9a3412;margin-right:8px;"></i> Loading Excel preview...</div>');
    
    $.ajax({
        url: "{{ route('school.attendance.students.preview') }}",
        type: "GET",
        data: {
            type: type,
            class_id: classId,
            section_id: sectionId,
            academic_session_id: academicSessionId,
            date: date,
            month: month,
            year: year
        },
        success: function(response) {
            if (response.success) {
                let html = '<table class="excel-preview-table"><thead>';
                
                // Row 1: Excel letter headers (A, B, C...)
                html += '<tr><th></th>';
                response.headers.forEach(h => {
                    html += `<th>${h}</th>`;
                });
                html += '</tr>';
                
                // Row 2: Header names
                html += '<tr><th></th>';
                response.columns.forEach(c => {
                    html += `<th>${c}</th>`;
                });
                html += '</tr></thead><tbody>';
                
                // Data rows
                response.rows.forEach((r, idx) => {
                    html += `<tr><td class="row-num">${idx + 1}</td>`;
                    r.forEach(cell => {
                        html += `<td>${cell ?? ''}</td>`;
                    });
                    html += '</tr>';
                });
                
                html += '</tbody></table>';
                container.html(html);
            } else {
                container.html('<div style="padding:24px;text-align:center;color:#ef4444;font-weight:600;">Failed to load preview.</div>');
            }
        },
        error: function() {
            container.html('<div style="padding:24px;text-align:center;color:#ef4444;font-weight:600;">Error fetching preview.</div>');
        }
    });
}

function downloadExcelFromSlider() {
    let type = $('#slider_export_type').val();
    let classId = $('#class_id').val();
    let sectionId = $('#section_id').val();
    let academicSessionId = $('#academic_session_id').val();
    let date = $('#attendance_date').val();
    let month = $('#slider_month').val();
    let year = $('#slider_year').val();
    
    let url = "{{ route('school.attendance.students.export') }}?type=" + type + 
              "&class_id=" + classId + 
              "&section_id=" + sectionId + 
              "&academic_session_id=" + academicSessionId;
              
    if (type === 'daily') {
        url += "&date=" + date;
    } else {
        url += "&month=" + month + "&year=" + year;
    }
    
    window.location.href = url;
    closeExcelSlider();
}

// Bind slider field change events
$(document).ready(function() {
    $('#slider_export_type, #slider_month, #slider_year').on('change', function() {
        loadSpreadsheetPreview();
    });
});

// Bind click events on document ready
$(document).ready(function() {
    $('#btn-view-excel-slider').on('click', function() {
        openExcelSlider();
    });
    
    $('#btn-download-register').on('click', function() {
        let classId = $('#class_id').val();
        let sectionId = $('#section_id').val();
        let academicSessionId = $('#academic_session_id').val();
        let date = $('#attendance_date').val();
        
        if (!classId || !sectionId) {
            alert('Please select Class and Section first.');
            return;
        }
        
        let url = "{{ route('school.attendance.students.export') }}?type=daily" + 
                  "&class_id=" + classId + 
                  "&section_id=" + sectionId + 
                  "&academic_session_id=" + academicSessionId + 
                  "&date=" + date;
                  
        window.location.href = url;
    });
});
</script>
@endsection
