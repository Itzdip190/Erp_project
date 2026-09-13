@extends('layouts.app')

@section('page-title', 'Staff Salary Assignment')

@section('styles')
<style>
    :root {
        --sal-blue-primary: #2563eb;
        --sal-blue-dark: #1e40af;
        --sal-blue-navy: #1e3a8a;
        --sal-blue-light: #eff6ff;
        --sal-blue-border: #bfdbfe;
        --sal-card-bg: #ffffff;
        --sal-text-main: #0f172a;
        --sal-text-muted: #64748b;
        --sal-cell-border: #e2e8f0;
        --sal-cell-hover: #f1f5f9;
        --sal-modified-bg: #fefce8;
        --sal-modified-border: #fef08a;
    }

    .sal-assign-container {
        padding: 0 4px 40px 4px;
    }

    /* Top Stats / KPI Cards */
    .sal-kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 22px;
    }
    .sal-kpi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .sal-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.08);
    }
    .sal-kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }
    .sal-kpi-label {
        font-size: 11.5px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }
    .sal-kpi-value {
        font-size: 22px;
        font-weight: 800;
        line-height: 1.2;
    }

    /* Main Container Card */
    .excel-sheet-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    /* Header Banner (Blue & White Theme) */
    .excel-sheet-hdr {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%);
        color: #ffffff;
        padding: 18px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }
    .excel-sheet-title {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .excel-sheet-title-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    .excel-sheet-title h1 {
        font-size: 18px;
        font-weight: 800;
        margin: 0;
        color: #ffffff;
        letter-spacing: -0.2px;
    }
    .excel-sheet-title p {
        font-size: 12px;
        margin: 2px 0 0 0;
        color: #dbeafe;
    }

    /* Filter Toolbar */
    .excel-filter-bar {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 14px 20px;
    }
    .excel-filter-row {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .filter-ctrl-group {
        display: inline-flex;
        align-items: center;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        padding: 0 12px;
        height: 40px;
        transition: all 0.2s ease;
    }
    .filter-ctrl-group:focus-within, .filter-ctrl-group:hover {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }
    .filter-ctrl-group i {
        color: #2563eb;
        font-size: 13px;
        margin-right: 8px;
    }
    .filter-ctrl-group select, .filter-ctrl-group input {
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
    .filter-ctrl-group.search-box {
        flex: 1;
        min-width: 220px;
    }
    .filter-ctrl-group.search-box input {
        width: 100%;
        cursor: text;
        font-weight: 500;
    }
    .btn-filter-action {
        height: 40px;
        padding: 0 16px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        border: none;
    }
    .btn-filter-primary {
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
    }
    .btn-filter-primary:hover {
        background: #1d4ed8;
        color: #ffffff;
    }
    .btn-filter-reset {
        background: #ffffff;
        color: #475569;
        border: 1px solid #cbd5e1;
    }
    .btn-filter-reset:hover {
        background: #fee2e2;
        color: #dc2626;
        border-color: #fca5a5;
    }

    /* Bulk Actions Toolbar */
    .excel-ribbon-bar {
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        padding: 12px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }
    .excel-ribbon-left {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .excel-ribbon-right {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .ribbon-btn {
        height: 38px;
        padding: 0 16px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid transparent;
        text-decoration: none;
    }
    .ribbon-btn-save {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
    }
    .ribbon-btn-save:hover {
        background: linear-gradient(135deg, #1d4ed8, #1e3a8a);
        color: #ffffff;
    }
    .ribbon-btn-apply {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }
    .ribbon-btn-apply:hover {
        background: #dbeafe;
    }
    .ribbon-btn-export {
        background: #f8fafc;
        color: #334155;
        border: 1px solid #cbd5e1;
    }
    .ribbon-btn-export:hover {
        background: #e2e8f0;
    }

    /* Spreadsheet Table Grid */
    .excel-grid-wrap {
        max-height: 68vh;
        overflow: auto;
        position: relative;
        background: #f8fafc;
    }
    .excel-grid-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 12.5px;
        background: #ffffff;
    }
    .excel-grid-table th {
        position: sticky;
        top: 0;
        background: #1e40af;
        color: #ffffff;
        font-weight: 700;
        text-align: left;
        padding: 10px 12px;
        white-space: nowrap;
        border-right: 1px solid rgba(255, 255, 255, 0.18);
        border-bottom: 2px solid #172554;
        z-index: 15;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        user-select: none;
    }
    .excel-grid-table th.col-freeze-1 {
        position: sticky;
        left: 0;
        z-index: 25;
        background: #1e3a8a;
    }
    .excel-grid-table th.col-freeze-2 {
        position: sticky;
        left: 50px;
        z-index: 25;
        background: #1e3a8a;
    }
    .excel-grid-table th.col-num {
        width: 50px;
        text-align: center;
    }
    .excel-grid-table th.col-align-right {
        text-align: right;
    }
    .excel-grid-table th.col-align-center {
        text-align: center;
    }

    /* Header Group Styles */
    .th-grp-earning {
        background: #0284c7 !important;
        border-left: 2px solid #0369a1;
    }
    .th-grp-deduction {
        background: #e11d48 !important;
        border-left: 2px solid #be123c;
    }
    .th-grp-net {
        background: #1e3a8a !important;
        border-left: 2px solid #0f172a;
    }

    /* Table Cells */
    .excel-grid-table td {
        padding: 6px 10px;
        border-right: 1px solid var(--sal-cell-border);
        border-bottom: 1px solid var(--sal-cell-border);
        vertical-align: middle;
        background: #ffffff;
        white-space: nowrap;
        transition: background 0.15s ease;
    }
    .excel-grid-table tr:nth-child(even) td {
        background: #fbfcfe;
    }
    .excel-grid-table tr:hover td {
        background: #f0f7ff !important;
    }
    .excel-grid-table tr.row-modified td {
        background: #fefce8 !important;
    }
    .excel-grid-table tr.row-selected td {
        background: #eff6ff !important;
    }

    /* Frozen Columns in Table Body */
    .excel-grid-table td.col-freeze-1 {
        position: sticky;
        left: 0;
        z-index: 5;
        background: #ffffff;
        text-align: center;
    }
    .excel-grid-table tr:nth-child(even) td.col-freeze-1 {
        background: #fbfcfe;
    }
    .excel-grid-table tr:hover td.col-freeze-1 {
        background: #f0f7ff !important;
    }
    .excel-grid-table tr.row-modified td.col-freeze-1 {
        background: #fefce8 !important;
    }
    .excel-grid-table tr.row-selected td.col-freeze-1 {
        background: #eff6ff !important;
    }

    .excel-grid-table td.col-freeze-2 {
        position: sticky;
        left: 50px;
        z-index: 5;
        background: #ffffff;
        box-shadow: 2px 0 5px rgba(0,0,0,0.03);
    }
    .excel-grid-table tr:nth-child(even) td.col-freeze-2 {
        background: #fbfcfe;
    }
    .excel-grid-table tr:hover td.col-freeze-2 {
        background: #f0f7ff !important;
    }
    .excel-grid-table tr.row-modified td.col-freeze-2 {
        background: #fefce8 !important;
    }
    .excel-grid-table tr.row-selected td.col-freeze-2 {
        background: #eff6ff !important;
    }

    /* Editable Cell Inputs */
    .cell-input, .cell-select {
        width: 100%;
        min-width: 90px;
        height: 32px;
        padding: 4px 8px;
        border: 1px solid transparent;
        border-radius: 6px;
        background: transparent;
        font-size: 12.5px;
        font-weight: 600;
        color: #1e293b;
        transition: all 0.15s ease;
        text-align: right;
        outline: none;
    }
    .cell-select {
        text-align: left;
        min-width: 140px;
        cursor: pointer;
        padding-right: 18px;
    }
    .cell-input.text-left {
        text-align: left;
    }
    .cell-input:hover, .cell-select:hover {
        border-color: #cbd5e1;
        background: #ffffff;
    }
    .cell-input:focus, .cell-select:focus {
        border-color: #2563eb !important;
        background: #ffffff !important;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2) !important;
    }
    .cell-modified {
        background: #fef08a !important;
        border-color: #facc15 !important;
        font-weight: 700 !important;
    }

    /* Badges & Computed Values */
    .badge-dept {
        background: #eff6ff;
        color: #1e40af;
        border: 1px solid #bfdbfe;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
    }
    .badge-desig {
        background: #f5f3ff;
        color: #6d28d9;
        border: 1px solid #ddd6fe;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
    }
    .badge-status-assigned {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .badge-status-unassigned {
        background: #fff1f2;
        color: #9f1239;
        border: 1px solid #fecdd3;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .val-badge-earn {
        font-weight: 800;
        color: #0284c7;
        font-size: 13px;
        text-align: right;
        display: block;
    }
    .val-badge-ded {
        font-weight: 800;
        color: #dc2626;
        font-size: 13px;
        text-align: right;
        display: block;
    }
    .val-badge-net {
        font-weight: 900;
        color: #1e3a8a;
        font-size: 13.5px;
        text-align: right;
        display: block;
        background: #eff6ff;
        padding: 4px 8px;
        border-radius: 6px;
        border: 1px solid #bfdbfe;
    }

    /* Row Action Buttons */
    .btn-row-action {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid transparent;
        cursor: pointer;
        font-size: 11.5px;
        transition: all 0.15s ease;
        background: #f1f5f9;
        color: #475569;
    }
    .btn-row-save {
        background: #eff6ff;
        color: #2563eb;
        border-color: #bfdbfe;
    }
    .btn-row-save:hover {
        background: #2563eb;
        color: #ffffff;
    }

    /* Footer Summary Bar */
    .excel-sheet-footer {
        background: #0f172a;
        color: #ffffff;
        padding: 14px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        border-top: 2px solid #1e293b;
    }
    .footer-summary-items {
        display: flex;
        align-items: center;
        gap: 24px;
        flex-wrap: wrap;
    }
    .footer-sum-item {
        display: flex;
        flex-direction: column;
    }
    .footer-sum-lbl {
        font-size: 11px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .footer-sum-val {
        font-size: 16px;
        font-weight: 800;
        color: #ffffff;
    }

    /* Floating Save Bar */
    .floating-save-bar {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #0f172a;
        color: #ffffff;
        padding: 12px 20px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
        display: none;
        align-items: center;
        gap: 16px;
        z-index: 1000;
        border: 1px solid #334155;
        animation: slideUp 0.3s ease;
    }
    @keyframes slideUp {
        from { transform: translateY(100px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    /* Toast Notification */
    .sal-toast {
        position: fixed;
        top: 24px;
        right: 24px;
        padding: 14px 20px;
        border-radius: 10px;
        color: #ffffff;
        font-size: 13.5px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        z-index: 9999;
        animation: fadeIn 0.25s ease;
    }
    .sal-toast-success {
        background: #2563eb;
    }
    .sal-toast-error {
        background: #dc2626;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Dark Mode Overrides */
    body.dark-mode .excel-sheet-card {
        background: #1e293b;
        border-color: #334155;
    }
    body.dark-mode .excel-filter-bar,
    body.dark-mode .excel-ribbon-bar {
        background: #0f172a;
        border-color: #334155;
    }
    body.dark-mode .filter-ctrl-group {
        background: #1e293b;
        border-color: #475569;
        color: #f8fafc;
    }
    body.dark-mode .filter-ctrl-group select,
    body.dark-mode .filter-ctrl-group input {
        color: #f8fafc;
    }
    body.dark-mode .excel-grid-table {
        background: #1e293b;
    }
    body.dark-mode .excel-grid-table td {
        background: #1e293b;
        border-color: #334155;
        color: #f8fafc;
    }
    body.dark-mode .excel-grid-table tr:nth-child(even) td {
        background: #0f172a;
    }
    body.dark-mode .excel-grid-table td.col-freeze-1,
    body.dark-mode .excel-grid-table td.col-freeze-2 {
        background: #1e293b;
    }
    body.dark-mode .excel-grid-table tr:nth-child(even) td.col-freeze-1,
    body.dark-mode .excel-grid-table tr:nth-child(even) td.col-freeze-2 {
        background: #0f172a;
    }
    body.dark-mode .cell-input,
    body.dark-mode .cell-select {
        color: #f8fafc;
    }
    body.dark-mode .cell-input:hover,
    body.dark-mode .cell-select:hover {
        background: #334155;
    }
    body.dark-mode .sal-kpi-card {
        background: #1e293b;
        border-color: #334155;
        color: #f8fafc;
    }
</style>
@endsection

@section('content')
<div class="sal-assign-container">

    @if(session('success'))
        <div style="padding: 14px 18px; background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; border-radius: 12px; font-size: 13.5px; font-weight: 600; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 8px rgba(37,99,235,0.1);">
            <div><i class="fas fa-check-circle" style="margin-right: 8px; font-size: 16px;"></i> {{ session('success') }}</div>
            <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; color: #1e40af; font-size: 16px; cursor: pointer;">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div style="padding: 14px 18px; background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; border-radius: 12px; font-size: 13.5px; font-weight: 600; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 8px rgba(239,68,68,0.1);">
            <div><i class="fas fa-exclamation-circle" style="margin-right: 8px; font-size: 16px;"></i> {{ session('error') }}</div>
            <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; color: #991b1b; font-size: 16px; cursor: pointer;">&times;</button>
        </div>
    @endif

    <!-- Top KPI Row -->
    <div class="sal-kpi-grid">
        <div class="sal-kpi-card">
            <div class="sal-kpi-icon" style="background: #eff6ff; color: #2563eb;">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <div class="sal-kpi-label">Active Staff</div>
                <div class="sal-kpi-value" style="color: #1e293b;">{{ $totalStaffCount }}</div>
            </div>
        </div>

        <div class="sal-kpi-card">
            <div class="sal-kpi-icon" style="background: #eff6ff; color: #1d4ed8;">
                <i class="fas fa-user-check"></i>
            </div>
            <div>
                <div class="sal-kpi-label">Assigned Structures</div>
                <div class="sal-kpi-value" style="color: #1d4ed8;">{{ $totalAssignedCount }}</div>
            </div>
        </div>

        <div class="sal-kpi-card">
            <div class="sal-kpi-icon" style="background: #fff1f2; color: #e11d48;">
                <i class="fas fa-user-tag"></i>
            </div>
            <div>
                <div class="sal-kpi-label">Unassigned / Custom</div>
                <div class="sal-kpi-value" style="color: #e11d48;">{{ $totalUnassignedCount }}</div>
            </div>
        </div>

        <div class="sal-kpi-card">
            <div class="sal-kpi-icon" style="background: #faf5ff; color: #7c3aed;">
                <i class="fas fa-wallet"></i>
            </div>
            <div>
                <div class="sal-kpi-label">Est. Monthly Payroll</div>
                <div class="sal-kpi-value" style="color: #7c3aed;" id="kpi-monthly-payroll">₹{{ number_format($totalEstimatedMonthlyPayroll, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Excel Spreadsheet Card -->
    <div class="excel-sheet-card">

        <!-- Header (Blue & White Theme) -->
        <div class="excel-sheet-hdr">
            <div class="excel-sheet-title">
                <div class="excel-sheet-title-icon">
                    <i class="fas fa-table-cells"></i>
                </div>
                <div>
                    <h1>Staff Salary Assignment (Spreadsheet Matrix)</h1>
                    <p>Department & Designation wise matrix. Assign salary structures, modify amounts inline, and save changes in bulk.</p>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <a href="{{ route('school.payroll.salary-structure') }}" class="btn-filter-action btn-filter-reset" style="background: rgba(255,255,255,0.15); color: #ffffff; border-color: rgba(255,255,255,0.3); backdrop-filter: blur(4px);">
                    <i class="fas fa-arrow-left"></i> Salary Structures Master
                </a>
                <button type="button" class="btn-filter-action" style="background: #ffffff; color: #1e40af; box-shadow: 0 2px 8px rgba(0,0,0,0.15);" onclick="saveAllStaffAssignments()">
                    <i class="fas fa-save"></i> Save All Changes <span id="hdr-modified-count" style="display:none; background: #dc2626; color: #fff; padding: 1px 6px; border-radius: 10px; font-size: 11px; margin-left: 4px;">0</span>
                </button>
            </div>
        </div>

        <!-- Filter Bar -->
        <form method="GET" action="{{ route('school.payroll.staff-assign') }}" class="excel-filter-bar" id="filterForm">
            <div class="excel-filter-row">
                <!-- Search -->
                <div class="filter-ctrl-group search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" id="gridLiveSearch" value="{{ $search }}" placeholder="Quick search staff name, employee ID, phone...">
                </div>

                <!-- Department Filter -->
                <div class="filter-ctrl-group">
                    <i class="fas fa-building"></i>
                    <select name="department_id" onchange="document.getElementById('filterForm').submit()">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ (string)$selectedDept === (string)$dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Designation Filter -->
                <div class="filter-ctrl-group">
                    <i class="fas fa-id-badge"></i>
                    <select name="designation_id" onchange="document.getElementById('filterForm').submit()">
                        <option value="">All Designations</option>
                        @foreach($designations as $desig)
                            <option value="{{ $desig->id }}" {{ (string)$selectedDesig === (string)$desig->id ? 'selected' : '' }}>
                                {{ $desig->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Salary Structure Filter -->
                <div class="filter-ctrl-group">
                    <i class="fas fa-receipt"></i>
                    <select name="salary_structure_id" onchange="document.getElementById('filterForm').submit()">
                        <option value="">All Structures</option>
                        @foreach($salaryStructures as $st)
                            <option value="{{ $st->id }}" {{ (string)$selectedStructure === (string)$st->id ? 'selected' : '' }}>
                                {{ $st->name }} (₹{{ number_format($st->basic_salary, 0) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="filter-ctrl-group">
                    <i class="fas fa-filter"></i>
                    <select name="status" onchange="document.getElementById('filterForm').submit()">
                        <option value="">All Status</option>
                        <option value="assigned" {{ $statusFilter === 'assigned' ? 'selected' : '' }}>Assigned</option>
                        <option value="unassigned" {{ $statusFilter === 'unassigned' ? 'selected' : '' }}>Unassigned / Custom</option>
                    </select>
                </div>

                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn-filter-action btn-filter-primary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    @if($selectedDept || $selectedDesig || $selectedStructure || $statusFilter || $search)
                        <a href="{{ route('school.payroll.staff-assign') }}" class="btn-filter-action btn-filter-reset">
                            <i class="fas fa-sync-alt"></i> Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <!-- Ribbon / Bulk Action Bar -->
        <div class="excel-ribbon-bar">
            <div class="excel-ribbon-left">
                <label style="display: inline-flex; align-items: center; gap: 6px; font-weight: 700; font-size: 12.5px; color: #475569; cursor: pointer; margin: 0;">
                    <input type="checkbox" id="selectAllCheckbox" style="width: 16px; height: 16px; cursor: pointer; accent-color: #2563eb;">
                    <span>Select All (<span id="selectedRowsCount">0</span> selected)</span>
                </label>

                <div style="display: inline-flex; align-items: center; gap: 8px; margin-left: 12px; padding-left: 12px; border-left: 2px solid #e2e8f0;">
                    <span style="font-size: 12px; font-weight: 700; color: #64748b;">Bulk Apply:</span>
                    <select id="bulkStructureSelect" class="cell-select" style="border: 1.5px solid #cbd5e1; height: 36px; min-width: 180px; background: #ffffff;">
                        <option value="">-- Choose Salary Structure --</option>
                        @foreach($salaryStructures as $st)
                            <option value="{{ $st->id }}">{{ $st->name }} (Basic: ₹{{ number_format($st->basic_salary, 0) }})</option>
                        @endforeach
                    </select>
                    <button type="button" class="ribbon-btn ribbon-btn-apply" onclick="applyStructureToSelected()">
                        <i class="fas fa-wand-magic-sparkles"></i> Apply to Selected
                    </button>
                </div>
            </div>

            <div class="excel-ribbon-right">
                <button type="button" class="ribbon-btn ribbon-btn-export" onclick="exportGridToCSV()">
                    <i class="fas fa-file-excel" style="color: #2563eb;"></i> Export CSV / Excel
                </button>
                <button type="button" class="ribbon-btn ribbon-btn-save" onclick="saveAllStaffAssignments()">
                    <i class="fas fa-check-double"></i> Save All (<span id="ribbon-modified-count">0</span>)
                </button>
            </div>
        </div>

        <!-- Excel Table Grid -->
        <div class="excel-grid-wrap" id="gridTableContainer">
            <table class="excel-grid-table" id="staffSalaryTable">
                <thead>
                    <tr>
                        <th class="col-freeze-1 col-num">
                            <input type="checkbox" id="headerMasterCheck" style="cursor: pointer; accent-color: #ffffff;">
                        </th>
                        <th class="col-freeze-2" style="width: 95px;">EMP ID</th>
                        <th style="min-width: 190px;">STAFF NAME</th>
                        <th style="min-width: 130px;">DEPARTMENT</th>
                        <th style="min-width: 130px;">DESIGNATION</th>
                        <th style="min-width: 170px;">SALARY STRUCTURE</th>
                        <th style="min-width: 110px;">SALARY TYPE</th>
                        <th class="col-align-right th-grp-earning" style="min-width: 120px;">BASIC SALARY (₹)</th>
                        <th class="col-align-right th-grp-earning" style="min-width: 100px;">HRA (₹)</th>
                        <th class="col-align-right th-grp-earning" style="min-width: 100px;">DA (₹)</th>
                        <th class="col-align-right th-grp-earning" style="min-width: 100px;">TA (₹)</th>
                        <th class="col-align-right th-grp-earning" style="min-width: 110px;">OTHER ALLOW. (₹)</th>
                        <th class="col-align-right th-grp-earning" style="min-width: 120px;">TOTAL EARNINGS</th>
                        <th class="col-align-right th-grp-deduction" style="min-width: 95px;">PF (₹)</th>
                        <th class="col-align-right th-grp-deduction" style="min-width: 95px;">ESI (₹)</th>
                        <th class="col-align-right th-grp-deduction" style="min-width: 95px;">TDS (₹)</th>
                        <th class="col-align-right th-grp-deduction" style="min-width: 95px;">PROF TAX (₹)</th>
                        <th class="col-align-right th-grp-deduction" style="min-width: 120px;">TOTAL DED.</th>
                        <th class="col-align-right th-grp-net" style="min-width: 135px;">NET SALARY (₹)</th>
                        <th class="col-align-center" style="min-width: 130px;">EFFECTIVE FROM</th>
                        <th class="col-align-center" style="min-width: 110px;">STATUS</th>
                        <th class="col-align-center" style="min-width: 90px;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staffList as $index => $staff)
                        <tr id="row-staff-{{ $staff['id'] }}" data-staff-id="{{ $staff['id'] }}" class="staff-grid-row">
                            <!-- Col 1: Checkbox & Num -->
                            <td class="col-freeze-1 col-num">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 4px;">
                                    <input type="checkbox" class="row-selector-check" value="{{ $staff['id'] }}" onchange="handleRowCheck(this)">
                                    <span style="font-size: 11px; font-weight: 600; color: #64748b;">{{ $index + 1 }}</span>
                                </div>
                            </td>

                            <!-- Col 2: Employee ID -->
                            <td class="col-freeze-2">
                                <span style="font-weight: 800; font-family: monospace; font-size: 11.5px; color: #1e3a8a; background: #eff6ff; padding: 2px 6px; border-radius: 4px; border: 1px solid #bfdbfe;">
                                    {{ $staff['employee_id'] }}
                                </span>
                            </td>

                            <!-- Col 3: Name -->
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 28px; height: 28px; border-radius: 50%; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 11.5px; font-weight: 800; flex-shrink: 0;">
                                        {{ strtoupper(substr($staff['name'], 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight: 800; color: #0f172a; font-size: 13px; line-height: 1.2;">
                                            {{ $staff['name'] }}
                                        </div>
                                        @if($staff['phone'])
                                            <div style="font-size: 11px; color: #64748b;">{{ $staff['phone'] }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Col 4: Department -->
                            <td>
                                <span class="badge-dept">{{ $staff['department_name'] }}</span>
                            </td>

                            <!-- Col 5: Designation -->
                            <td>
                                <span class="badge-desig">{{ $staff['designation_name'] }}</span>
                            </td>

                            <!-- Col 6: Salary Structure Dropdown -->
                            <td>
                                <select class="cell-select val-structure-id" onchange="onStructureDropdownChange({{ $staff['id'] }})">
                                    <option value="">-- Custom / None --</option>
                                    @foreach($salaryStructures as $st)
                                        <option value="{{ $st->id }}" {{ (string)$staff['salary_structure_id'] === (string)$st->id ? 'selected' : '' }}>
                                            {{ $st->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <!-- Col 7: Salary Type -->
                            <td>
                                <select class="cell-select val-salary-type" onchange="markRowModified({{ $staff['id'] }})">
                                    <option value="Monthly" {{ $staff['salary_type'] === 'Monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="Daily" {{ $staff['salary_type'] === 'Daily' ? 'selected' : '' }}>Daily</option>
                                    <option value="Hourly" {{ $staff['salary_type'] === 'Hourly' ? 'selected' : '' }}>Hourly</option>
                                    <option value="Contract" {{ $staff['salary_type'] === 'Contract' ? 'selected' : '' }}>Contract</option>
                                </select>
                            </td>

                            <!-- Col 8: Basic Salary -->
                            <td>
                                <input type="number" step="0.01" min="0" class="cell-input val-basic" value="{{ (float)$staff['basic_salary'] }}" oninput="recalcRow({{ $staff['id'] }})">
                            </td>

                            <!-- Col 9: HRA -->
                            <td>
                                <input type="number" step="0.01" min="0" class="cell-input val-hra" value="{{ (float)$staff['hra'] }}" oninput="recalcRow({{ $staff['id'] }})">
                            </td>

                            <!-- Col 10: DA -->
                            <td>
                                <input type="number" step="0.01" min="0" class="cell-input val-da" value="{{ (float)$staff['da'] }}" oninput="recalcRow({{ $staff['id'] }})">
                            </td>

                            <!-- Col 11: TA -->
                            <td>
                                <input type="number" step="0.01" min="0" class="cell-input val-ta" value="{{ (float)$staff['ta'] }}" oninput="recalcRow({{ $staff['id'] }})">
                            </td>

                            <!-- Col 12: Other Allowance -->
                            <td>
                                <input type="number" step="0.01" min="0" class="cell-input val-allowance" value="{{ (float)$staff['allowance'] }}" oninput="recalcRow({{ $staff['id'] }})">
                            </td>

                            <!-- Col 13: Total Allowances (Computed) -->
                            <td style="background: #f0f9ff;">
                                <span class="val-badge-earn" id="disp-earn-{{ $staff['id'] }}">
                                    ₹{{ number_format($staff['total_allowances'] + $staff['basic_salary'], 2) }}
                                </span>
                            </td>

                            <!-- Col 14: PF -->
                            <td>
                                <input type="number" step="0.01" min="0" class="cell-input val-pf" value="{{ (float)$staff['pf'] }}" oninput="recalcRow({{ $staff['id'] }})">
                            </td>

                            <!-- Col 15: ESI -->
                            <td>
                                <input type="number" step="0.01" min="0" class="cell-input val-esi" value="{{ (float)$staff['esi'] }}" oninput="recalcRow({{ $staff['id'] }})">
                            </td>

                            <!-- Col 16: TDS -->
                            <td>
                                <input type="number" step="0.01" min="0" class="cell-input val-tds" value="{{ (float)$staff['tds'] }}" oninput="recalcRow({{ $staff['id'] }})">
                            </td>

                            <!-- Col 17: Prof Tax -->
                            <td>
                                <input type="number" step="0.01" min="0" class="cell-input val-prof-tax" value="{{ (float)$staff['prof_tax'] }}" oninput="recalcRow({{ $staff['id'] }})">
                            </td>

                            <!-- Col 18: Total Deductions (Computed) -->
                            <td style="background: #fef2f2;">
                                <span class="val-badge-ded" id="disp-ded-{{ $staff['id'] }}">
                                    -₹{{ number_format($staff['total_deductions'], 2) }}
                                </span>
                            </td>

                            <!-- Col 19: Net Salary (Computed) -->
                            <td style="background: #eff6ff;">
                                <span class="val-badge-net" id="disp-net-{{ $staff['id'] }}">
                                    ₹{{ number_format($staff['net_salary'], 2) }}
                                </span>
                            </td>

                            <!-- Col 20: Effective From Date -->
                            <td>
                                <input type="date" class="cell-input text-left val-effective-from" value="{{ $staff['effective_from'] }}" onchange="markRowModified({{ $staff['id'] }})" style="min-width: 125px;">
                            </td>

                            <!-- Col 21: Status Badge -->
                            <td style="text-align: center;">
                                <span id="disp-status-{{ $staff['id'] }}" class="{{ $staff['is_assigned'] ? 'badge-status-assigned' : 'badge-status-unassigned' }}">
                                    <i class="fas {{ $staff['is_assigned'] ? 'fa-check-circle' : 'fa-circle-dot' }}"></i>
                                    {{ $staff['is_assigned'] ? 'Assigned' : 'Custom' }}
                                </span>
                            </td>

                            <!-- Col 22: Row Action -->
                            <td style="text-align: center;">
                                <div style="display: inline-flex; align-items: center; gap: 4px;">
                                    <button type="button" class="btn-row-action btn-row-save" onclick="saveSingleStaffAssignment({{ $staff['id'] }})" title="Save this row">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="22" style="padding: 60px 20px; text-align: center; color: #64748b;">
                                <div style="max-width: 400px; margin: 0 auto;">
                                    <div style="width: 56px; height: 56px; border-radius: 50%; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 12px auto;">
                                        <i class="fas fa-user-slash"></i>
                                    </div>
                                    <div style="font-weight: 800; font-size: 15px; color: #0f172a; margin-bottom: 4px;">No Staff Found Matching Criteria</div>
                                    <div style="font-size: 12.5px; color: #64748b; margin-bottom: 14px;">
                                        Try adjusting your department, designation, or search filters above.
                                    </div>
                                    <a href="{{ route('school.payroll.staff-assign') }}" class="btn-filter-action btn-filter-reset">
                                        <i class="fas fa-sync-alt"></i> Reset All Filters
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer Summary Bar -->
        <div class="excel-sheet-footer">
            <div class="footer-summary-items">
                <div class="footer-sum-item">
                    <span class="footer-sum-lbl">Visible Staff</span>
                    <span class="footer-sum-val" id="footer-staff-count">{{ count($staffList) }}</span>
                </div>
                <div class="footer-sum-item">
                    <span class="footer-sum-lbl">Total Basic (₹)</span>
                    <span class="footer-sum-val" id="footer-basic-sum" style="color: #60a5fa;">₹{{ number_format($totalBasicSum, 2) }}</span>
                </div>
                <div class="footer-sum-item">
                    <span class="footer-sum-lbl">Total Allowances (₹)</span>
                    <span class="footer-sum-val" id="footer-allow-sum" style="color: #38bdf8;">+₹{{ number_format($totalAllowancesSum, 2) }}</span>
                </div>
                <div class="footer-sum-item">
                    <span class="footer-sum-lbl">Total Deductions (₹)</span>
                    <span class="footer-sum-val" id="footer-ded-sum" style="color: #f87171;">-₹{{ number_format($totalDeductionsSum, 2) }}</span>
                </div>
                <div class="footer-sum-item">
                    <span class="footer-sum-lbl">Net Payroll Sum (₹)</span>
                    <span class="footer-sum-val" id="footer-net-sum" style="color: #93c5fd;">₹{{ number_format($totalEstimatedMonthlyPayroll, 2) }}</span>
                </div>
            </div>

            <div>
                <button type="button" class="ribbon-btn ribbon-btn-save" style="padding: 8px 20px; font-size: 13.5px;" onclick="saveAllStaffAssignments()">
                    <i class="fas fa-save"></i> Save All Modifications
                </button>
            </div>
        </div>
    </div>

    <!-- Floating Save Bar (Visible when changes exist) -->
    <div class="floating-save-bar" id="floatingSaveBar">
        <div style="display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-exclamation-circle" style="color: #facc15; font-size: 18px;"></i>
            <div>
                <div style="font-weight: 800; font-size: 13.5px;">Unsaved Modifications</div>
                <div style="font-size: 11.5px; color: #94a3b8;"><span id="floating-modified-count">0</span> staff member(s) salary modified</div>
            </div>
        </div>
        <div style="display: flex; gap: 8px;">
            <button type="button" class="ribbon-btn ribbon-btn-save" onclick="saveAllStaffAssignments()">
                <i class="fas fa-save"></i> Save Changes Now
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Embedded Salary Structure Templates Dictionary
    const salaryStructureTemplates = @json($salaryStructures->keyBy('id'));
    
    // Track modified rows set
    const modifiedStaffIds = new Set();

    // 1. Recalculate row amounts live on typing
    function recalcRow(staffId) {
        const row = document.getElementById(`row-staff-${staffId}`);
        if (!row) return;

        const basic = parseFloat(row.querySelector('.val-basic').value) || 0;
        const hra = parseFloat(row.querySelector('.val-hra').value) || 0;
        const da = parseFloat(row.querySelector('.val-da').value) || 0;
        const ta = parseFloat(row.querySelector('.val-ta').value) || 0;
        const allowance = parseFloat(row.querySelector('.val-allowance').value) || 0;

        const pf = parseFloat(row.querySelector('.val-pf').value) || 0;
        const esi = parseFloat(row.querySelector('.val-esi').value) || 0;
        const tds = parseFloat(row.querySelector('.val-tds').value) || 0;
        const profTax = parseFloat(row.querySelector('.val-prof-tax').value) || 0;

        const totalAllowances = hra + da + ta + allowance;
        const totalEarnings = basic + totalAllowances;
        const totalDeductions = pf + esi + tds + profTax;
        const netSalary = Math.max(0, totalEarnings - totalDeductions);

        // Update displays
        document.getElementById(`disp-earn-${staffId}`).textContent = '₹' + formatMoney(totalEarnings);
        document.getElementById(`disp-ded-${staffId}`).textContent = '-₹' + formatMoney(totalDeductions);
        document.getElementById(`disp-net-${staffId}`).textContent = '₹' + formatMoney(netSalary);

        markRowModified(staffId);
        recalcFooterTotals();
    }

    // 2. Handle Structure dropdown change in a specific row
    function onStructureDropdownChange(staffId) {
        const row = document.getElementById(`row-staff-${staffId}`);
        if (!row) return;

        const select = row.querySelector('.val-structure-id');
        const structId = select.value;
        const statusBadge = document.getElementById(`disp-status-${staffId}`);

        if (structId && salaryStructureTemplates[structId]) {
            const template = salaryStructureTemplates[structId];
            
            // Auto-populate fields from template
            row.querySelector('.val-salary-type').value = template.salary_type || 'Monthly';
            row.querySelector('.val-basic').value = parseFloat(template.basic_salary) || 0;
            row.querySelector('.val-hra').value = parseFloat(template.hra) || 0;
            row.querySelector('.val-da').value = parseFloat(template.da) || 0;
            row.querySelector('.val-ta').value = parseFloat(template.ta) || 0;
            row.querySelector('.val-allowance').value = parseFloat(template.allowance) || 0;

            row.querySelector('.val-pf').value = parseFloat(template.pf) || 0;
            row.querySelector('.val-esi').value = parseFloat(template.esi) || 0;
            row.querySelector('.val-tds').value = parseFloat(template.tds) || 0;
            row.querySelector('.val-prof-tax').value = parseFloat(template.prof_tax) || 0;

            if (template.effective_from) {
                row.querySelector('.val-effective-from').value = template.effective_from.split('T')[0];
            }

            statusBadge.className = 'badge-status-assigned';
            statusBadge.innerHTML = '<i class="fas fa-check-circle"></i> Assigned';
        } else {
            statusBadge.className = 'badge-status-unassigned';
            statusBadge.innerHTML = '<i class="fas fa-circle-dot"></i> Custom';
        }

        recalcRow(staffId);
    }

    // 3. Mark row modified
    function markRowModified(staffId) {
        const row = document.getElementById(`row-staff-${staffId}`);
        if (!row) return;

        modifiedStaffIds.add(staffId);
        row.classList.add('row-modified');

        updateModifiedCountUI();
    }

    function updateModifiedCountUI() {
        const count = modifiedStaffIds.size;
        
        const hdrCount = document.getElementById('hdr-modified-count');
        const ribbonCount = document.getElementById('ribbon-modified-count');
        const floatingCount = document.getElementById('floating-modified-count');
        const floatingBar = document.getElementById('floatingSaveBar');

        if (hdrCount) {
            hdrCount.textContent = count;
            hdrCount.style.display = count > 0 ? 'inline-block' : 'none';
        }
        if (ribbonCount) {
            ribbonCount.textContent = count;
        }
        if (floatingCount) {
            floatingCount.textContent = count;
        }
        if (floatingBar) {
            floatingBar.style.display = count > 0 ? 'flex' : 'none';
        }
    }

    // 4. Checkbox selection handling
    const headerMasterCheck = document.getElementById('headerMasterCheck');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');

    function toggleSelectAll(checked) {
        const rowChecks = document.querySelectorAll('.row-selector-check');
        rowChecks.forEach(chk => {
            const row = chk.closest('tr');
            if (row && row.style.display !== 'none') {
                chk.checked = checked;
                if (checked) {
                    row.classList.add('row-selected');
                } else {
                    row.classList.remove('row-selected');
                }
            }
        });
        updateSelectedCount();
    }

    if (headerMasterCheck) {
        headerMasterCheck.addEventListener('change', function() {
            toggleSelectAll(this.checked);
            if (selectAllCheckbox) selectAllCheckbox.checked = this.checked;
        });
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            toggleSelectAll(this.checked);
            if (headerMasterCheck) headerMasterCheck.checked = this.checked;
        });
    }

    function handleRowCheck(chk) {
        const row = chk.closest('tr');
        if (chk.checked) {
            row.classList.add('row-selected');
        } else {
            row.classList.remove('row-selected');
        }
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const selected = document.querySelectorAll('.row-selector-check:checked').length;
        const countEl = document.getElementById('selectedRowsCount');
        if (countEl) countEl.textContent = selected;
    }

    // 5. Bulk Apply Salary Structure to Selected Rows
    function applyStructureToSelected() {
        const structSelect = document.getElementById('bulkStructureSelect');
        const structId = structSelect.value;

        if (!structId) {
            showToast('Please choose a salary structure from the dropdown to apply.', 'error');
            return;
        }

        const selectedChecks = Array.from(document.querySelectorAll('.row-selector-check:checked'));
        if (selectedChecks.length === 0) {
            showToast('Please select at least one staff member checkbox.', 'error');
            return;
        }

        selectedChecks.forEach(chk => {
            const staffId = parseInt(chk.value);
            const row = document.getElementById(`row-staff-${staffId}`);
            if (row) {
                const structDropdown = row.querySelector('.val-structure-id');
                structDropdown.value = structId;
                onStructureDropdownChange(staffId);
            }
        });

        showToast(`Applied structure to ${selectedChecks.length} staff member(s). Click 'Save All' to persist.`, 'success');
    }

    // 6. Recalculate Footer Totals
    function recalcFooterTotals() {
        let visibleCount = 0;
        let basicSum = 0;
        let allowSum = 0;
        let dedSum = 0;
        let netSum = 0;

        document.querySelectorAll('.staff-grid-row').forEach(row => {
            if (row.style.display !== 'none') {
                visibleCount++;
                const basic = parseFloat(row.querySelector('.val-basic').value) || 0;
                const hra = parseFloat(row.querySelector('.val-hra').value) || 0;
                const da = parseFloat(row.querySelector('.val-da').value) || 0;
                const ta = parseFloat(row.querySelector('.val-ta').value) || 0;
                const allow = parseFloat(row.querySelector('.val-allowance').value) || 0;
                const totalAllow = hra + da + ta + allow;

                const pf = parseFloat(row.querySelector('.val-pf').value) || 0;
                const esi = parseFloat(row.querySelector('.val-esi').value) || 0;
                const tds = parseFloat(row.querySelector('.val-tds').value) || 0;
                const pt = parseFloat(row.querySelector('.val-prof-tax').value) || 0;
                const totalDed = pf + esi + tds + pt;

                const net = Math.max(0, basic + totalAllow - totalDed);

                basicSum += basic;
                allowSum += totalAllow;
                dedSum += totalDed;
                netSum += net;
            }
        });

        document.getElementById('footer-staff-count').textContent = visibleCount;
        document.getElementById('footer-basic-sum').textContent = '₹' + formatMoney(basicSum);
        document.getElementById('footer-allow-sum').textContent = '+₹' + formatMoney(allowSum);
        document.getElementById('footer-ded-sum').textContent = '-₹' + formatMoney(dedSum);
        document.getElementById('footer-net-sum').textContent = '₹' + formatMoney(netSum);

        const kpiPayroll = document.getElementById('kpi-monthly-payroll');
        if (kpiPayroll) {
            kpiPayroll.textContent = '₹' + formatMoney(netSum);
        }
    }

    // 7. Save Single Row
    function saveSingleStaffAssignment(staffId) {
        const row = document.getElementById(`row-staff-${staffId}`);
        if (!row) return;

        const payload = extractRowData(row);
        submitSaveRequest([payload], [staffId]);
    }

    // 8. Save All Modified Rows (or All Visible if none marked)
    function saveAllStaffAssignments() {
        const staffIdsToSave = modifiedStaffIds.size > 0 
            ? Array.from(modifiedStaffIds) 
            : Array.from(document.querySelectorAll('.staff-grid-row')).map(r => parseInt(r.dataset.staffId));

        if (staffIdsToSave.length === 0) {
            showToast('No staff rows available to save.', 'error');
            return;
        }

        const assignments = [];
        staffIdsToSave.forEach(id => {
            const row = document.getElementById(`row-staff-${id}`);
            if (row) {
                assignments.push(extractRowData(row));
            }
        });

        submitSaveRequest(assignments, staffIdsToSave);
    }

    function extractRowData(row) {
        const staffId = parseInt(row.dataset.staffId);
        return {
            staff_id: staffId,
            salary_structure_id: row.querySelector('.val-structure-id').value || null,
            salary_type: row.querySelector('.val-salary-type').value || 'Monthly',
            basic_salary: parseFloat(row.querySelector('.val-basic').value) || 0,
            hra: parseFloat(row.querySelector('.val-hra').value) || 0,
            da: parseFloat(row.querySelector('.val-da').value) || 0,
            ta: parseFloat(row.querySelector('.val-ta').value) || 0,
            allowance: parseFloat(row.querySelector('.val-allowance').value) || 0,
            pf: parseFloat(row.querySelector('.val-pf').value) || 0,
            esi: parseFloat(row.querySelector('.val-esi').value) || 0,
            tds: parseFloat(row.querySelector('.val-tds').value) || 0,
            prof_tax: parseFloat(row.querySelector('.val-prof-tax').value) || 0,
            effective_from: row.querySelector('.val-effective-from').value || '{{ now()->format("Y-m-d") }}',
            is_active: 1
        };
    }

    function submitSaveRequest(assignments, staffIds) {
        showToast('Saving staff salary configuration...', 'info');

        fetch(`{{ route('school.payroll.staff-assign.save') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ assignments: assignments })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                staffIds.forEach(id => {
                    modifiedStaffIds.delete(id);
                    const row = document.getElementById(`row-staff-${id}`);
                    if (row) {
                        row.classList.remove('row-modified');
                    }
                });
                updateModifiedCountUI();
                showToast(data.message || 'Staff salaries saved successfully!', 'success');
            } else {
                showToast(data.message || 'Error occurred while saving.', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            showToast('Network error while saving salary data.', 'error');
        });
    }

    // 9. Live Client-Side Search Filter (Instant without reload)
    const liveSearchInput = document.getElementById('gridLiveSearch');
    if (liveSearchInput) {
        liveSearchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            document.querySelectorAll('.staff-grid-row').forEach(row => {
                const text = row.innerText.toLowerCase();
                if (query === '' || text.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
            recalcFooterTotals();
        });
    }

    // 10. Export visible Grid Table to CSV
    function exportGridToCSV() {
        const rows = document.querySelectorAll('#staffSalaryTable tr');
        let csvContent = "data:text/csv;charset=utf-8,";

        rows.forEach(row => {
            if (row.style.display === 'none') return;
            const cols = row.querySelectorAll('th, td');
            let rowData = [];
            cols.forEach((col, idx) => {
                // Skip checkbox and actions col
                if (idx === 0 || idx === cols.length - 1) return;

                const input = col.querySelector('input, select');
                let val = '';
                if (input) {
                    val = input.value;
                    if (input.tagName === 'SELECT' && input.selectedIndex >= 0) {
                        val = input.options[input.selectedIndex].text;
                    }
                } else {
                    val = col.innerText.trim();
                }
                // Clean money format
                val = val.replace(/[\n\r]/g, ' ').replace(/"/g, '""');
                rowData.push(`"${val}"`);
            });
            csvContent += rowData.join(",") + "\r\n";
        });

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", `Staff_Salary_Assignment_${new Date().toISOString().slice(0,10)}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Helpers
    function formatMoney(num) {
        return parseFloat(num || 0).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function showToast(msg, type = 'success') {
        const existing = document.querySelector('.sal-toast');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.className = `sal-toast ${type === 'error' ? 'sal-toast-error' : 'sal-toast-success'}`;
        toast.innerHTML = `<i class="fas ${type === 'error' ? 'fa-triangle-exclamation' : 'fa-check-circle'}"></i> <span>${msg}</span>`;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }
</script>
@endsection
