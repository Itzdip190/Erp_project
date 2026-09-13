@extends('layouts.app')

@section('page-title', 'Student Bulk Edit')

@section('styles')
<style>
    .excel-container {
        background: var(--card, #ffffff);
        border: 1px solid var(--border, #e2e8f0);
        border-radius: 14px;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        margin-bottom: 30px;
    }
    .excel-toolbar {
        background: #f8fafc;
        border-bottom: 1px solid var(--border, #e2e8f0);
        padding: 16px 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }
    .excel-title {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .excel-title-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: linear-gradient(135deg, #107c41, #0d6535);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        box-shadow: 0 4px 12px rgba(16, 124, 65, 0.28);
    }
    .excel-title h2 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: var(--t1, #1e293b);
        letter-spacing: -0.2px;
    }
    .excel-title p {
        margin: 2px 0 0 0;
        font-size: 12px;
        color: var(--t3, #64748b);
    }

    /* Modern Horizontal Filter Bar */
    .excel-filters {
        padding: 12px 20px;
        background: #ffffff;
        border-bottom: 1px solid var(--border, #e2e8f0);
    }
    .filters-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: nowrap;
        width: 100%;
        overflow-x: auto;
        padding: 2px 0;
    }
    .filters-row::-webkit-scrollbar {
        height: 4px;
    }
    .filters-row::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .filter-group {
        display: flex;
        align-items: center;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 9px;
        padding: 0 10px;
        height: 40px;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }
    .filter-group:hover, .filter-group:focus-within {
        border-color: #107c41;
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(16, 124, 65, 0.1);
    }
    .filter-group .filter-icon {
        color: #64748b;
        font-size: 13px;
        margin-right: 8px;
        flex-shrink: 0;
    }
    .filter-group select, .filter-group input {
        border: none !important;
        background: transparent !important;
        box-shadow: none !important;
        outline: none !important;
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
        padding: 0;
        height: 100%;
        cursor: pointer;
    }
    .filter-group select option {
        background: #ffffff;
        color: #1e293b;
        font-weight: 500;
    }
    .filter-group.search-group {
        flex: 1;
        min-width: 220px;
        background: #ffffff;
    }
    .filter-group.search-group input {
        width: 100%;
        cursor: text;
        font-weight: 500;
    }
    .btn-filter-reset {
        height: 40px;
        padding: 0 14px;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 600;
        background: #f1f5f9;
        color: #475569;
        border: 1.5px solid #cbd5e1;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
        text-decoration: none;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }
    .btn-filter-reset:hover {
        background: #fee2e2;
        border-color: #fca5a5;
        color: #dc2626;
    }

    /* Spreadsheet Grid Styles */
    .excel-table-wrap {
        max-height: 70vh;
        overflow: auto;
        position: relative;
    }
    .excel-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 12.5px;
        background: #ffffff;
    }
    .excel-table th {
        position: sticky;
        top: 0;
        background: #107c41;
        color: #ffffff;
        font-weight: 600;
        text-align: left;
        padding: 8px 10px;
        white-space: nowrap;
        border-right: 1px solid rgba(255, 255, 255, 0.15);
        border-bottom: 2px solid #0b592e;
        z-index: 10;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        user-select: none;
    }
    .excel-table th.col-num {
        background: #0d6535;
        text-align: center;
        width: 50px;
        min-width: 50px;
        position: sticky;
        left: 0;
        z-index: 20;
    }
    .th-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    /* Mini Column Lock/Edit Switch in Headers */
    .col-toggle-switch {
        position: relative;
        display: inline-block;
        width: 26px;
        height: 14px;
        margin: 0;
        cursor: pointer;
        flex-shrink: 0;
    }
    .col-toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
        position: absolute;
    }
    .col-toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(255, 255, 255, 0.35);
        transition: all 0.2s ease;
        border-radius: 14px;
    }
    .col-toggle-slider:before {
        position: absolute;
        content: "";
        height: 10px;
        width: 10px;
        left: 2px;
        bottom: 2px;
        background-color: #ffffff;
        transition: all 0.2s ease;
        border-radius: 50%;
        box-shadow: 0 1px 2px rgba(0,0,0,0.3);
    }
    .col-toggle-switch input:checked + .col-toggle-slider {
        background-color: #22c55e;
    }
    .col-toggle-switch input:checked + .col-toggle-slider:before {
        transform: translateX(12px);
    }

    .excel-table td {
        padding: 0;
        border-right: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
        background: #ffffff;
        vertical-align: middle;
        position: relative;
        transition: background 0.15s ease;
    }
    .excel-table td.col-num {
        position: sticky;
        left: 0;
        background: #f1f5f9;
        color: #64748b;
        font-weight: 700;
        text-align: center;
        font-size: 11px;
        z-index: 5;
        border-right: 2px solid #cbd5e1;
    }
    .excel-table tr:hover td {
        background: #f8fafc;
    }
    .excel-table tr:hover td.col-num {
        background: #e2e8f0;
        color: #107c41;
    }

    /* Locked Column Cells */
    .excel-table td.col-locked {
        background: #f8fafc;
        cursor: not-allowed;
    }
    .excel-table td.col-locked .cell-input,
    .excel-table td.col-locked .cell-select {
        pointer-events: none;
        opacity: 0.65;
        user-select: none;
        background: transparent !important;
        cursor: not-allowed;
    }

    /* Editable Input Cells */
    .cell-input {
        width: 100%;
        height: 38px;
        border: none;
        outline: none;
        padding: 6px 10px;
        font-size: 12.5px;
        background: transparent;
        color: #1e293b;
        font-family: inherit;
        transition: all 0.15s ease;
    }
    .cell-input:focus {
        background: #eff6ff !important;
        box-shadow: inset 0 0 0 2px #2563eb;
    }
    .cell-select {
        width: 100%;
        height: 38px;
        border: none;
        outline: none;
        padding: 6px 8px;
        font-size: 12.5px;
        background: transparent;
        color: #1e293b;
        font-family: inherit;
        cursor: pointer;
    }
    .cell-select:focus {
        background: #eff6ff !important;
        box-shadow: inset 0 0 0 2px #2563eb;
    }

    /* Modified Cell Indicator */
    .is-modified {
        background-color: #fef9c3 !important;
    }
    .is-modified .cell-input, .is-modified .cell-select {
        font-weight: 600;
    }
    .excel-table td.is-modified::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 0 7px 7px 0;
        border-color: transparent #d97706 transparent transparent;
    }

    /* Action Footer */
    .excel-footer {
        background: #f8fafc;
        border-top: 1px solid var(--border, #e2e8f0);
        padding: 14px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }
    .status-badge-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        font-weight: 600;
        color: #475569;
    }

    .btn-excel-save {
        background: #107c41 !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: 8px !important;
        padding: 10px 22px !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        cursor: pointer !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        box-shadow: 0 4px 12px rgba(16, 124, 65, 0.25) !important;
        transition: all 0.2s ease !important;
    }
    .btn-excel-save:hover {
        background: #0d6535 !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 6px 16px rgba(16, 124, 65, 0.35) !important;
    }

    /* Toast alert banner */
    #ajaxToastAlert {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 9999;
        min-width: 300px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.18);
        border-radius: 10px;
        display: none;
    }

    body.dark-mode .excel-container {
        background: #1e293b;
        border-color: #334155;
    }
    body.dark-mode .excel-toolbar, body.dark-mode .excel-filters, body.dark-mode .excel-footer {
        background: #0f172a;
        border-color: #334155;
    }
    body.dark-mode .filter-group {
        background: #1e293b;
        border-color: #334155;
    }
    body.dark-mode .filter-group select, body.dark-mode .filter-group input {
        color: #f8fafc;
    }
    body.dark-mode .btn-filter-reset {
        background: #1e293b;
        border-color: #334155;
        color: #cbd5e1;
    }
    body.dark-mode .excel-table {
        background: #1e293b;
    }
    body.dark-mode .excel-table td {
        border-color: #334155;
        background: #1e293b;
    }
    body.dark-mode .excel-table td.col-locked {
        background: #0f172a;
    }
    body.dark-mode .excel-table td.col-num {
        background: #0f172a;
        color: #94a3b8;
        border-right-color: #334155;
    }
    body.dark-mode .cell-input, body.dark-mode .cell-select {
        color: #f8fafc;
    }

    /* Photo Column Styles */
    .photo-col-cell {
        text-align: center;
        vertical-align: middle !important;
        padding: 4px 6px !important;
        width: 65px;
        min-width: 65px;
    }
    .student-photo-cell {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    .student-photo-cell input[type="file"] {
        display: none !important;
    }
    .photo-preview-wrap {
        position: relative;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        overflow: hidden;
        background: #f1f5f9;
        border: 1.5px solid #cbd5e1;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .photo-preview-wrap:hover {
        border-color: #107c41;
        box-shadow: 0 2px 8px rgba(16, 124, 65, 0.28);
    }
    .student-thumb-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .photo-actions-overlay {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.75);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        opacity: 0;
        transition: opacity 0.2s ease;
        border-radius: 50%;
    }
    .photo-preview-wrap:hover .photo-actions-overlay,
    .student-photo-cell.is-active .photo-actions-overlay {
        opacity: 1;
    }
    .photo-btn-upload, .photo-btn-remove {
        width: 22px;
        height: 22px;
        border-radius: 4px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        color: #ffffff;
        cursor: pointer;
        transition: transform 0.15s ease, background-color 0.15s ease;
        margin: 0;
        padding: 0;
    }
    .photo-btn-upload {
        background: #107c41;
    }
    .photo-btn-upload:hover {
        background: #0d6535;
        transform: scale(1.15);
    }
    .photo-btn-remove {
        background: #ef4444;
    }
    .photo-btn-remove:hover {
        background: #dc2626;
        transform: scale(1.15);
    }
    body.dark-mode .photo-preview-wrap {
        background: #0f172a;
        border-color: #334155;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-0">

    <!-- Toast Notification Banner -->
    <div id="ajaxToastAlert" class="alert alert-success d-flex align-items-center mb-0 fade show" role="alert">
        <i class="fas fa-check-circle me-2 fs-5" id="toastIcon"></i>
        <span id="toastMsg" class="fw-bold">Changes saved successfully!</span>
    </div>

    <div class="excel-container">
        <!-- Toolbar Header -->
        <div class="excel-toolbar">
            <div class="excel-title">
                <div class="excel-title-icon">
                    <i class="fas fa-file-excel"></i>
                </div>
                <div>
                    <h2>Student Bulk Edit Spreadsheet</h2>
                    <p>Fast Excel-style inline editing with zero-delay AJAX save</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <label class="d-inline-flex align-items-center gap-2 px-3 py-2 bg-white rounded-3 border shadow-sm" style="font-size:12.5px; font-weight:600; cursor:pointer; user-select:none;">
                    <input type="checkbox" id="masterEditToggle" onchange="toggleAllColumns(this)">
                    <span><i class="fas fa-toggle-on me-1 text-success"></i> Toggle All Editable</span>
                </label>
                <a href="{{ route('school.students.index') }}" class="btn btn-outline-secondary" style="border-radius:8px; font-weight:600; font-size:13px;">
                    <i class="fas fa-arrow-left me-1"></i> Back to Directory
                </a>
                <button type="button" class="btn-excel-save" onclick="submitBulkEditForm()">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </div>

        <!-- Horizontal Filter Bar (Clean, auto-submit on change, no filter button, academic year managed via global context) -->
        <div class="excel-filters">
            <form action="{{ route('school.students.bulk-edit') }}" method="GET" id="filterForm">
                <input type="hidden" name="academic_session_id" value="{{ $selectedSessionId }}">
                <div class="filters-row">
                    <!-- Class -->
                    <div class="filter-group class-group" style="min-width: 150px;">
                        <i class="fas fa-graduation-cap filter-icon"></i>
                        <select name="class_id" class="form-select" onchange="onFilterClassChange(this)">
                            <option value="">All Classes</option>
                            @foreach($classes as $cls)
                                <option value="{{ $cls->id }}" {{ $classId == $cls->id ? 'selected' : '' }}>{{ $cls->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Section (Class-scoped) -->
                    <div class="filter-group section-group" style="min-width: 150px;">
                        <i class="fas fa-layer-group filter-icon"></i>
                        <select name="section_id" id="filterSectionSelect" class="form-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="">All Sections</option>
                            @if($classId)
                                @foreach($allSections->where('class_id', $classId) as $sec)
                                    <option value="{{ $sec->id }}" {{ $sectionId == $sec->id ? 'selected' : '' }}>Section {{ $sec->name }}</option>
                                @endforeach
                            @else
                                @foreach($allSections->pluck('name')->unique()->sort() as $name)
                                    <option value="{{ $name }}" {{ $sectionId == $name ? 'selected' : '' }}>Section {{ $name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Status -->
                    <div class="filter-group status-group" style="min-width: 135px;">
                        <i class="fas fa-user-check filter-icon"></i>
                        <select name="status" class="form-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active Only</option>
                            <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Status</option>
                        </select>
                    </div>

                    <!-- Search Input -->
                    <div class="filter-group search-group">
                        <i class="fas fa-search filter-icon"></i>
                        <input type="text" name="search" class="form-control" placeholder="Search name, roll no, adm no..." value="{{ $search }}" onkeydown="if(event.key === 'Enter'){ event.preventDefault(); document.getElementById('filterForm').submit(); }">
                    </div>

                    @if($classId || $sectionId || $search || $status !== 'active')
                        <a href="{{ route('school.students.bulk-edit') }}" class="btn-filter-reset" title="Reset Filters">
                            <i class="fas fa-undo"></i> Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Form for Bulk Save -->
        <form id="bulkEditForm" action="{{ route('school.students.bulk-update') }}" method="POST">
            @csrf
            <input type="hidden" name="academic_session_id" value="{{ $selectedSessionId }}">
            <div class="excel-table-wrap">
                <table class="excel-table">
                    <thead>
                        <tr>
                            <th class="col-num">#</th>
                            <th style="min-width: 80px; text-align: center;">Photo</th>
                            
                            <th style="min-width: 130px;">
                                <div class="th-content">
                                    <span>Adm No *</span>
                                    <label class="col-toggle-switch" title="Toggle column edit mode">
                                        <input type="checkbox" class="col-lock-toggle" data-col="admission_number" onchange="toggleColumnEditable(this)">
                                        <span class="col-toggle-slider"></span>
                                    </label>
                                </div>
                            </th>

                            <th style="min-width: 110px;">
                                <div class="th-content">
                                    <span>Roll No</span>
                                    <label class="col-toggle-switch" title="Toggle column edit mode">
                                        <input type="checkbox" class="col-lock-toggle" data-col="roll_number" onchange="toggleColumnEditable(this)">
                                        <span class="col-toggle-slider"></span>
                                    </label>
                                </div>
                            </th>

                            <th style="min-width: 140px;">
                                <div class="th-content">
                                    <span>First Name *</span>
                                    <label class="col-toggle-switch" title="Toggle column edit mode">
                                        <input type="checkbox" class="col-lock-toggle" data-col="first_name" onchange="toggleColumnEditable(this)">
                                        <span class="col-toggle-slider"></span>
                                    </label>
                                </div>
                            </th>

                            <th style="min-width: 130px;">
                                <div class="th-content">
                                    <span>Last Name</span>
                                    <label class="col-toggle-switch" title="Toggle column edit mode">
                                        <input type="checkbox" class="col-lock-toggle" data-col="last_name" onchange="toggleColumnEditable(this)">
                                        <span class="col-toggle-slider"></span>
                                    </label>
                                </div>
                            </th>

                            <th style="min-width: 130px;">
                                <div class="th-content">
                                    <span>Class</span>
                                    <label class="col-toggle-switch" title="Toggle column edit mode">
                                        <input type="checkbox" class="col-lock-toggle" data-col="class_id" onchange="toggleColumnEditable(this)">
                                        <span class="col-toggle-slider"></span>
                                    </label>
                                </div>
                            </th>

                            <th style="min-width: 120px;">
                                <div class="th-content">
                                    <span>Section</span>
                                    <label class="col-toggle-switch" title="Toggle column edit mode">
                                        <input type="checkbox" class="col-lock-toggle" data-col="section_id" onchange="toggleColumnEditable(this)">
                                        <span class="col-toggle-slider"></span>
                                    </label>
                                </div>
                            </th>

                            <th style="min-width: 120px;">
                                <div class="th-content">
                                    <span>Gender</span>
                                    <label class="col-toggle-switch" title="Toggle column edit mode">
                                        <input type="checkbox" class="col-lock-toggle" data-col="gender" onchange="toggleColumnEditable(this)">
                                        <span class="col-toggle-slider"></span>
                                    </label>
                                </div>
                            </th>

                            <th style="min-width: 135px;">
                                <div class="th-content">
                                    <span>DOB</span>
                                    <label class="col-toggle-switch" title="Toggle column edit mode">
                                        <input type="checkbox" class="col-lock-toggle" data-col="date_of_birth" onchange="toggleColumnEditable(this)">
                                        <span class="col-toggle-slider"></span>
                                    </label>
                                </div>
                            </th>

                            <th style="min-width: 135px;">
                                <div class="th-content">
                                    <span>Phone</span>
                                    <label class="col-toggle-switch" title="Toggle column edit mode">
                                        <input type="checkbox" class="col-lock-toggle" data-col="phone" onchange="toggleColumnEditable(this)">
                                        <span class="col-toggle-slider"></span>
                                    </label>
                                </div>
                            </th>

                            <th style="min-width: 160px;">
                                <div class="th-content">
                                    <span>Father Name</span>
                                    <label class="col-toggle-switch" title="Toggle column edit mode">
                                        <input type="checkbox" class="col-lock-toggle" data-col="father_name" onchange="toggleColumnEditable(this)">
                                        <span class="col-toggle-slider"></span>
                                    </label>
                                </div>
                            </th>

                            <th style="min-width: 140px;">
                                <div class="th-content">
                                    <span>Father Phone</span>
                                    <label class="col-toggle-switch" title="Toggle column edit mode">
                                        <input type="checkbox" class="col-lock-toggle" data-col="father_phone" onchange="toggleColumnEditable(this)">
                                        <span class="col-toggle-slider"></span>
                                    </label>
                                </div>
                            </th>

                            <th style="min-width: 160px;">
                                <div class="th-content">
                                    <span>Mother Name</span>
                                    <label class="col-toggle-switch" title="Toggle column edit mode">
                                        <input type="checkbox" class="col-lock-toggle" data-col="mother_name" onchange="toggleColumnEditable(this)">
                                        <span class="col-toggle-slider"></span>
                                    </label>
                                </div>
                            </th>

                            <th style="min-width: 135px;">
                                <div class="th-content">
                                    <span>Category</span>
                                    <label class="col-toggle-switch" title="Toggle column edit mode">
                                        <input type="checkbox" class="col-lock-toggle" data-col="category_id" onchange="toggleColumnEditable(this)">
                                        <span class="col-toggle-slider"></span>
                                    </label>
                                </div>
                            </th>

                            <th style="min-width: 120px;">
                                <div class="th-content">
                                    <span>Blood Grp</span>
                                    <label class="col-toggle-switch" title="Toggle column edit mode">
                                        <input type="checkbox" class="col-lock-toggle" data-col="blood_group" onchange="toggleColumnEditable(this)">
                                        <span class="col-toggle-slider"></span>
                                    </label>
                                </div>
                            </th>

                            <th style="min-width: 140px;">
                                <div class="th-content">
                                    <span>Aadhaar / ID</span>
                                    <label class="col-toggle-switch" title="Toggle column edit mode">
                                        <input type="checkbox" class="col-lock-toggle" data-col="national_id" onchange="toggleColumnEditable(this)">
                                        <span class="col-toggle-slider"></span>
                                    </label>
                                </div>
                            </th>

                            <th style="min-width: 180px;">
                                <div class="th-content">
                                    <span>Address</span>
                                    <label class="col-toggle-switch" title="Toggle column edit mode">
                                        <input type="checkbox" class="col-lock-toggle" data-col="address" onchange="toggleColumnEditable(this)">
                                        <span class="col-toggle-slider"></span>
                                    </label>
                                </div>
                            </th>

                            <th style="min-width: 120px;">
                                <div class="th-content">
                                    <span>Status</span>
                                    <label class="col-toggle-switch" title="Toggle column edit mode">
                                        <input type="checkbox" class="col-lock-toggle" data-col="is_active" onchange="toggleColumnEditable(this)">
                                        <span class="col-toggle-slider"></span>
                                    </label>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $index => $student)
                            @php
                                $sessionRec = $selectedSessionId
                                    ? $student->studentSessions->firstWhere('academic_session_id', $selectedSessionId)
                                    : $student->studentSessions->sortByDesc('academic_session_id')->first();
                                $sData = is_array($sessionRec?->session_data) ? $sessionRec->session_data : [];

                                $bRoll = $sessionRec?->roll_number ?? $student->roll_number;
                                $bClassId = $sessionRec?->class_id ?? $student->class_id;
                                $bSectionId = $sessionRec?->section_id ?? $student->section_id;
                                $bFirstName = (array_key_exists('first_name', $sData) && $sData['first_name'] !== null) ? $sData['first_name'] : $student->first_name;
                                $bLastName = (array_key_exists('last_name', $sData) && $sData['last_name'] !== null) ? $sData['last_name'] : $student->last_name;
                                $bGender = (array_key_exists('gender', $sData) && $sData['gender'] !== null) ? $sData['gender'] : $student->gender;
                                $bDob = (array_key_exists('date_of_birth', $sData) && $sData['date_of_birth'] !== null) ? $sData['date_of_birth'] : $student->date_of_birth;
                                $bPhone = (array_key_exists('phone', $sData) && $sData['phone'] !== null) ? $sData['phone'] : $student->phone;
                                $bFatherName = (array_key_exists('father_name', $sData) && $sData['father_name'] !== null) ? $sData['father_name'] : $student->father_name;
                                $bFatherPhone = (array_key_exists('father_phone', $sData) && $sData['father_phone'] !== null) ? $sData['father_phone'] : $student->father_phone;
                                $bMotherName = (array_key_exists('mother_name', $sData) && $sData['mother_name'] !== null) ? $sData['mother_name'] : $student->mother_name;
                                $bCategoryId = (array_key_exists('category_id', $sData) && $sData['category_id'] !== null) ? $sData['category_id'] : $student->category_id;
                                $bBloodGroup = (array_key_exists('blood_group', $sData) && $sData['blood_group'] !== null) ? $sData['blood_group'] : $student->blood_group;
                                $bNationalId = (array_key_exists('national_id', $sData) && $sData['national_id'] !== null) ? $sData['national_id'] : $student->national_id;
                                $bAddress = (array_key_exists('address', $sData) && $sData['address'] !== null) ? $sData['address'] : $student->address;
                                $bIsActive = ($sessionRec && $sessionRec->is_active !== null) ? $sessionRec->is_active : $student->is_active;

                                $photoPath = (array_key_exists('photo', $sData) && $sData['photo']) ? $sData['photo'] : $student->photo;
                                $defaultAvatarSvg = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2394a3b8'%3E%3Cpath d='M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z'/%3E%3C/svg%3E";
                                if ($photoPath) {
                                    $photoUrl = (str_starts_with($photoPath, 'http') || str_starts_with($photoPath, 'data:image'))
                                        ? $photoPath
                                        : \Illuminate\Support\Facades\Storage::disk('public')->url($photoPath);
                                } else {
                                    $photoUrl = $defaultAvatarSvg;
                                }
                            @endphp
                            <tr data-student-id="{{ $student->id }}">
                                <td class="col-num">{{ $index + 1 }}</td>

                                <td class="photo-col-cell">
                                    <div class="student-photo-cell" id="photo_cell_{{ $student->id }}">
                                        <div class="photo-preview-wrap" title="Click to upload / change photo" onclick="triggerPhotoUpload({{ $student->id }})">
                                            <img src="{{ $photoUrl }}" class="student-thumb-img" id="photo_preview_{{ $student->id }}" alt="Photo" onerror="this.onerror=null; this.src='{{ $defaultAvatarSvg }}';">
                                            <div class="photo-actions-overlay" onclick="event.stopPropagation()">
                                                <button type="button" class="photo-btn-upload" title="Upload / Change Photo" onclick="triggerPhotoUpload({{ $student->id }})">
                                                    <i class="fas fa-camera"></i>
                                                </button>
                                                <button type="button" class="photo-btn-remove {{ empty($photoPath) ? 'd-none' : '' }}" id="photo_remove_btn_{{ $student->id }}" title="Remove Photo" onclick="onPhotoRemove({{ $student->id }})">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <input type="file" accept="image/jpeg,image/png,image/webp,image/jpg" style="display: none !important;" id="photo_file_{{ $student->id }}" onchange="onPhotoSelected(this, {{ $student->id }})">
                                        <input type="hidden" data-field="photo" id="photo_input_{{ $student->id }}" value="">
                                        <input type="hidden" data-field="photo_action" id="photo_action_{{ $student->id }}" value="">
                                    </div>
                                </td>

                                <td class="col-locked">
                                    <input type="text" data-field="admission_number" class="cell-input" value="{{ $student->admission_number }}" required readonly oninput="markCellModified(this)">
                                </td>
                                <td class="col-locked">
                                    <input type="text" data-field="roll_number" class="cell-input" value="{{ $bRoll }}" readonly oninput="markCellModified(this)">
                                </td>
                                <td class="col-locked">
                                    <input type="text" data-field="first_name" class="cell-input" value="{{ $bFirstName }}" required readonly oninput="markCellModified(this)">
                                </td>
                                <td class="col-locked">
                                    <input type="text" data-field="last_name" class="cell-input" value="{{ $bLastName }}" readonly oninput="markCellModified(this)">
                                </td>
                                <td class="col-locked">
                                    <select data-field="class_id" class="cell-select row-class-select" disabled onchange="onRowClassChange(this)">
                                        <option value="">-- None --</option>
                                        @foreach($classes as $cls)
                                            <option value="{{ $cls->id }}" {{ $bClassId == $cls->id ? 'selected' : '' }}>{{ $cls->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="col-locked">
                                    <select data-field="section_id" class="cell-select row-section-select" disabled onchange="markCellModified(this)">
                                        <option value="">-- None --</option>
                                        @if($bClassId)
                                            @foreach($allSections->where('class_id', $bClassId) as $sec)
                                                <option value="{{ $sec->id }}" data-class-id="{{ $sec->class_id }}" {{ $bSectionId == $sec->id ? 'selected' : '' }}>{{ $sec->name }}</option>
                                            @endforeach
                                        @else
                                            @foreach($allSections as $sec)
                                                <option value="{{ $sec->id }}" data-class-id="{{ $sec->class_id }}" {{ $bSectionId == $sec->id ? 'selected' : '' }}>{{ $sec->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </td>
                                <td class="col-locked">
                                    <select data-field="gender" class="cell-select" disabled onchange="markCellModified(this)">
                                        <option value="">-- Select --</option>
                                        <option value="Male" {{ strtolower($bGender ?? '') === 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ strtolower($bGender ?? '') === 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="Other" {{ strtolower($bGender ?? '') === 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </td>
                                <td class="col-locked">
                                    <input type="date" data-field="date_of_birth" class="cell-input" value="{{ $bDob ? \Carbon\Carbon::parse($bDob)->format('Y-m-d') : '' }}" readonly onchange="markCellModified(this)">
                                </td>
                                <td class="col-locked">
                                    <input type="text" data-field="phone" class="cell-input" value="{{ $bPhone }}" readonly oninput="markCellModified(this)">
                                </td>
                                <td class="col-locked">
                                    <input type="text" data-field="father_name" class="cell-input" value="{{ $bFatherName }}" readonly oninput="markCellModified(this)">
                                </td>
                                <td class="col-locked">
                                    <input type="text" data-field="father_phone" class="cell-input" value="{{ $bFatherPhone }}" readonly oninput="markCellModified(this)">
                                </td>
                                <td class="col-locked">
                                    <input type="text" data-field="mother_name" class="cell-input" value="{{ $bMotherName }}" readonly oninput="markCellModified(this)">
                                </td>
                                <td class="col-locked">
                                    <select data-field="category_id" class="cell-select" disabled onchange="markCellModified(this)">
                                        <option value="">-- None --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ $bCategoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="col-locked">
                                    <select data-field="blood_group" class="cell-select" disabled onchange="markCellModified(this)">
                                        <option value="">-- None --</option>
                                        @foreach(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                                            <option value="{{ $bg }}" {{ $bBloodGroup === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="col-locked">
                                    <input type="text" data-field="national_id" class="cell-input" value="{{ $bNationalId }}" readonly oninput="markCellModified(this)">
                                </td>
                                <td class="col-locked">
                                    <input type="text" data-field="address" class="cell-input" value="{{ $bAddress }}" readonly oninput="markCellModified(this)">
                                </td>
                                <td class="col-locked">
                                    <select data-field="is_active" class="cell-select" disabled onchange="markCellModified(this)">
                                        <option value="1" {{ $bIsActive ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ !$bIsActive ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="19" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fs-2 mb-2 d-block text-secondary"></i>
                                    No student records found matching your filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Table Footer Controls -->
            <div class="excel-footer">
                <div class="d-flex align-items-center gap-3">
                    <span>Total Loaded: <strong>{{ count($students) }}</strong> students</span>
                    <span id="modifiedCountBadge" class="badge bg-warning text-dark d-none" style="font-size:12px; padding: 6px 12px; border-radius: 6px;">
                        0 fields modified
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn-excel-save" onclick="submitBulkEditForm()">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const allSectionsData = @json($allSections->map(function($s) {
        return ['id' => $s->id, 'class_id' => $s->class_id, 'name' => $s->name];
    }));
    const modifiedRows = new Set();
    let modifiedFieldsCount = 0;

    function toggleColumnEditable(checkbox) {
        const colField = checkbox.getAttribute('data-col');
        const isChecked = checkbox.checked;
        
        document.querySelectorAll(`[data-field="${colField}"]`).forEach(el => {
            const td = el.closest('td');
            if (isChecked) {
                el.removeAttribute('disabled');
                el.removeAttribute('readonly');
                if (td) {
                    td.classList.remove('col-locked');
                    td.classList.add('col-editable');
                }
            } else {
                if (el.tagName === 'SELECT') {
                    el.setAttribute('disabled', 'disabled');
                } else {
                    el.setAttribute('readonly', 'readonly');
                }
                if (td) {
                    td.classList.add('col-locked');
                    td.classList.remove('col-editable');
                }
            }
        });

        updateMasterToggleState();
    }

    function toggleAllColumns(masterToggle) {
        const isChecked = masterToggle.checked;
        document.querySelectorAll('.col-lock-toggle').forEach(chk => {
            chk.checked = isChecked;
            toggleColumnEditable(chk);
        });
    }

    function updateMasterToggleState() {
        const master = document.getElementById('masterEditToggle');
        if (!master) return;
        const all = document.querySelectorAll('.col-lock-toggle');
        const checked = document.querySelectorAll('.col-lock-toggle:checked');
        master.checked = (all.length > 0 && all.length === checked.length);
    }

    function onFilterClassChange(classSelect) {
        // Clear section filter when class changes so it reloads clean class-wise section data
        const secSelect = document.getElementById('filterSectionSelect');
        if (secSelect) {
            secSelect.value = '';
        }
        document.getElementById('filterForm').submit();
    }

    function onRowClassChange(classSelect) {
        markCellModified(classSelect);
        const row = classSelect.closest('tr');
        if (!row) return;

        const sectionSelect = row.querySelector('.row-section-select');
        const selectedClassId = classSelect.value;

        if (sectionSelect) {
            const prevVal = sectionSelect.value;
            sectionSelect.innerHTML = '<option value="">-- None --</option>';

            const matchedSections = selectedClassId
                ? allSectionsData.filter(s => String(s.class_id) === String(selectedClassId))
                : allSectionsData;

            let valueMatched = false;
            matchedSections.forEach(sec => {
                const opt = document.createElement('option');
                opt.value = sec.id;
                opt.setAttribute('data-class-id', sec.class_id);
                opt.textContent = sec.name;
                if (String(sec.id) === String(prevVal)) {
                    opt.selected = true;
                    valueMatched = true;
                }
                sectionSelect.appendChild(opt);
            });

            if (!valueMatched) {
                sectionSelect.value = '';
            }
            markCellModified(sectionSelect);
        }
    }

    function markCellModified(element) {
        const td = element.closest('td');
        const tr = element.closest('tr');
        if (td && !td.classList.contains('is-modified')) {
            td.classList.add('is-modified');
            modifiedFieldsCount++;
        }
        if (tr && tr.dataset.studentId) {
            modifiedRows.add(tr);
        }
        updateModifiedBadge();
    }

    function updateModifiedBadge() {
        const badge = document.getElementById('modifiedCountBadge');
        if (modifiedFieldsCount > 0) {
            badge.textContent = `${modifiedFieldsCount} field(s) edited`;
            badge.classList.remove('d-none');
        } else {
            badge.classList.add('d-none');
        }
    }

    function showToast(message, isError = false) {
        const alertEl = document.getElementById('ajaxToastAlert');
        const msgEl = document.getElementById('toastMsg');
        const iconEl = document.getElementById('toastIcon');

        msgEl.textContent = message;
        if (isError) {
            alertEl.className = "alert alert-danger d-flex align-items-center mb-0 fade show";
            iconEl.className = "fas fa-exclamation-circle me-2 fs-5";
        } else {
            alertEl.className = "alert alert-success d-flex align-items-center mb-0 fade show";
            iconEl.className = "fas fa-check-circle me-2 fs-5";
        }

        alertEl.style.display = 'flex';
        setTimeout(() => {
            alertEl.style.display = 'none';
        }, 4000);
    }

    async function submitBulkEditForm() {
        if (modifiedRows.size === 0) {
            showToast('No fields were changed to save.', false);
            return;
        }

        const btnBtns = document.querySelectorAll('.btn-excel-save');
        btnBtns.forEach(b => {
            b.disabled = true;
            b.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';
        });

        // Collect ONLY modified rows & fields into lightweight payload
        const studentsData = {};
        modifiedRows.forEach(tr => {
            const studentId = tr.dataset.studentId;
            studentsData[studentId] = {};
            tr.querySelectorAll('[data-field]').forEach(input => {
                studentsData[studentId][input.dataset.field] = input.value;
            });
        });

        try {
            const response = await fetch('{{ route("school.students.bulk-update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    academic_session_id: '{{ $selectedSessionId }}',
                    students: studentsData
                })
            });

            const result = await response.json();

            if (response.ok && result.success) {
                showToast(result.message || 'Saved successfully!');
                
                // Clear modified indicators & photo action states
                document.querySelectorAll('[data-field="photo_action"]').forEach(inp => inp.value = '');
                document.querySelectorAll('[data-field="photo"]').forEach(inp => inp.value = '');
                document.querySelectorAll('.is-modified').forEach(td => td.classList.remove('is-modified'));
                modifiedRows.clear();
                modifiedFieldsCount = 0;
                updateModifiedBadge();
            } else {
                showToast(result.message || 'Failed to save changes.', true);
            }
        } catch (err) {
            showToast('Network error while saving changes.', true);
        } finally {
            btnBtns.forEach(b => {
                b.disabled = false;
                b.innerHTML = '<i class="fas fa-save me-1"></i> Save Changes';
            });
        }
    }

    function triggerPhotoUpload(studentId) {
        const fileInput = document.getElementById(`photo_file_${studentId}`);
        if (fileInput) {
            fileInput.click();
        }
    }

    function onPhotoSelected(fileInput, studentId) {
        if (!fileInput.files || !fileInput.files[0]) return;
        const file = fileInput.files[0];

        // Max 5MB check
        if (file.size > 5 * 1024 * 1024) {
            showToast('Image file size cannot exceed 5MB.', true);
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            const base64Data = e.target.result;
            const previewImg = document.getElementById(`photo_preview_${studentId}`);
            const photoInput = document.getElementById(`photo_input_${studentId}`);
            const actionInput = document.getElementById(`photo_action_${studentId}`);
            const removeBtn = document.getElementById(`photo_remove_btn_${studentId}`);

            if (previewImg) {
                previewImg.src = base64Data;
            }
            if (photoInput) {
                photoInput.value = base64Data;
            }
            if (actionInput) {
                actionInput.value = 'update';
            }
            if (removeBtn) {
                removeBtn.classList.remove('d-none');
            }

            if (photoInput) {
                markCellModified(photoInput);
            }
        };
        reader.readAsDataURL(file);
    }

    const DEFAULT_AVATAR_SVG = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2394a3b8'%3E%3Cpath d='M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z'/%3E%3C/svg%3E";

    function onPhotoRemove(studentId) {
        const previewImg = document.getElementById(`photo_preview_${studentId}`);
        const photoInput = document.getElementById(`photo_input_${studentId}`);
        const actionInput = document.getElementById(`photo_action_${studentId}`);
        const removeBtn = document.getElementById(`photo_remove_btn_${studentId}`);
        const fileInput = document.getElementById(`photo_file_${studentId}`);

        if (previewImg) {
            previewImg.src = DEFAULT_AVATAR_SVG;
        }
        if (fileInput) {
            fileInput.value = '';
        }
        if (photoInput) {
            photoInput.value = '';
        }
        if (actionInput) {
            actionInput.value = 'remove';
        }
        if (removeBtn) {
            removeBtn.classList.add('d-none');
        }

        if (photoInput) {
            markCellModified(photoInput);
        }
    }
</script>
@endsection

