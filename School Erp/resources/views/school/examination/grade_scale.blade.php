@extends('layouts.app')

@section('page-title', 'Grade Scale')

@section('content')
<style>
    /* Premium Blue & White Design Theme Variables */
    :root {
        --primary-blue: #2563eb;
        --primary-blue-hover: #1d4ed8;
        --primary-blue-light: #eff6ff;
        --border-blue: #bfdbfe;
        --dark-navy: #1e3a8a;
        --text-dark: #1e293b;
        --text-muted: #64748b;
        --bg-light: #f8fafc;
        --white: #ffffff;
        --danger-red: #ef4444;
        --danger-light: #fef2f2;
    }

    /* Page Header */
    .page-hdr {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        background: var(--white);
        padding: 20px 24px;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
    }
    .page-hdr-left h1 {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 22px;
        font-weight: 700;
        color: var(--dark-navy);
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0 0 4px 0;
    }
    .page-hdr-left p {
        font-size: 13px;
        color: var(--text-muted);
        margin: 0;
    }
    .page-hdr-right {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* Dropdowns and Buttons */
    .select-academic-year {
        background-color: var(--bg-light);
        border: 1px solid #cbd5e1;
        color: var(--text-dark);
        padding: 8px 16px;
        font-size: 14px;
        font-weight: 600;
        border-radius: 8px;
        cursor: pointer;
        outline: none;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .select-academic-year:hover {
        border-color: var(--primary-blue);
        background-color: #f1f5f9;
    }

    .btn-create-scale {
        background-color: var(--primary-blue);
        color: var(--white);
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
    }
    .btn-create-scale:hover {
        background-color: var(--primary-blue-hover);
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(37, 99, 235, 0.3);
    }

    /* Scale Basis Radio Boxes */
    .scale-basis-container {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .scale-basis-box {
        background: var(--white);
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px 20px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.2s ease;
        position: relative;
    }
    .scale-basis-box:hover {
        border-color: var(--border-blue);
        background-color: var(--bg-light);
    }
    .scale-basis-box input[type="radio"] {
        display: none;
    }
    .scale-basis-circle {
        width: 20px;
        height: 20px;
        border: 2px solid #cbd5e1;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }
    .scale-basis-circle::after {
        content: '';
        width: 10px;
        height: 10px;
        background-color: var(--primary-blue);
        border-radius: 50%;
        transform: scale(0);
        transition: transform 0.2s ease;
    }
    .scale-basis-text {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .scale-basis-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-dark);
    }
    .scale-basis-subtitle {
        font-size: 12px;
        color: var(--text-muted);
    }
    /* Checked State */
    .scale-basis-box.active {
        border-color: var(--primary-blue);
        background-color: var(--primary-blue-light);
    }
    .scale-basis-box.active .scale-basis-circle {
        border-color: var(--primary-blue);
    }
    .scale-basis-box.active .scale-basis-circle::after {
        transform: scale(1);
    }

    /* Tabs Bar */
    .tabs-bar {
        display: flex;
        border-bottom: 2px solid #e2e8f0;
        margin-bottom: 20px;
        gap: 8px;
    }
    .tab-btn {
        background: none;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 12px 24px;
        font-size: 14px;
        font-weight: 700;
        color: var(--text-muted);
        cursor: pointer;
        transition: all 0.2s ease;
        letter-spacing: 0.5px;
    }
    .tab-btn:hover {
        color: var(--primary-blue);
    }
    .tab-btn.active {
        color: var(--primary-blue);
        border-bottom-color: var(--primary-blue);
    }

    /* Table Styling */
    .table-card {
        background: var(--white);
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }
    .scale-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }
    .scale-table th {
        background-color: var(--dark-navy);
        color: var(--white);
        padding: 14px 20px;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .scale-table td {
        padding: 16px 20px;
        border-bottom: 1px solid #e2e8f0;
        font-size: 14px;
        color: var(--text-dark);
    }
    .scale-table tr:last-child td {
        border-bottom: none;
    }
    .scale-table tr:hover td {
        background-color: var(--bg-light);
    }

    .applicable-pill {
        display: inline-flex;
        background-color: #f1f5f9;
        color: var(--text-dark);
        border-radius: 6px;
        padding: 2px 8px;
        font-size: 12px;
        font-weight: 600;
        margin-right: 4px;
        margin-bottom: 4px;
        border: 1px solid #e2e8f0;
    }

    /* Action Buttons */
    .action-btn-group {
        display: flex;
        gap: 8px;
    }
    .btn-action {
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.15s ease;
    }
    .btn-edit {
        background-color: var(--primary-blue-light);
        color: var(--primary-blue);
    }
    .btn-edit:hover {
        background-color: var(--primary-blue);
        color: var(--white);
    }
    .btn-delete {
        background-color: var(--danger-light);
        color: var(--danger-red);
    }
    .btn-delete:hover {
        background-color: var(--danger-red);
        color: var(--white);
    }

    /* Empty State */
    .empty-state {
        padding: 48px;
        text-align: center;
        color: var(--text-muted);
    }
    .empty-state i {
        font-size: 48px;
        color: #cbd5e1;
        margin-bottom: 16px;
    }
    .empty-state h3 {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 8px;
    }

    /* Sliding Drawer / Modal */
    .drawer-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(15, 23, 42, 0.4);
        backdrop-filter: blur(4px);
        z-index: 999;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    .drawer-overlay.open {
        opacity: 1;
        visibility: visible;
    }
    
    .drawer-container {
        position: fixed;
        top: 0;
        right: -800px;
        width: 800px;
        max-width: 90vw;
        height: 100%;
        background-color: var(--white);
        box-shadow: -4px 0 24px rgba(0, 0, 0, 0.15);
        z-index: 1000;
        display: flex;
        flex-direction: column;
        transition: right 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .drawer-container.open {
        right: 0;
    }

    .drawer-hdr {
        padding: 20px 24px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: var(--dark-navy);
        color: var(--white);
    }
    .drawer-hdr h2 {
        font-size: 18px;
        font-weight: 700;
        margin: 0;
    }
    .drawer-close {
        background: none;
        border: none;
        font-size: 20px;
        color: var(--white);
        cursor: pointer;
        opacity: 0.8;
        transition: opacity 0.2s ease;
    }
    .drawer-close:hover {
        opacity: 1;
    }

    .drawer-body {
        padding: 24px;
        overflow-y: auto;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* Forms Inside Drawer */
    .form-section {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 16px;
        background-color: var(--bg-light);
    }
    .form-section-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--dark-navy);
        margin-bottom: 12px;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 6px;
    }

    .form-label {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 6px;
        display: block;
    }
    .form-control {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 14px;
        color: var(--text-dark);
        background-color: var(--white);
        outline: none;
        transition: all 0.15s ease;
    }
    .form-control:focus {
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }

    /* Class Checkbox Grid */
    .classes-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
        gap: 10px;
        margin-top: 10px;
    }
    .class-checkbox-label {
        background-color: var(--white);
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 6px 10px;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
        user-select: none;
        transition: all 0.15s ease;
    }
    .class-checkbox-label:hover {
        border-color: var(--primary-blue);
        background-color: var(--primary-blue-light);
    }
    .class-checkbox-label input[type="checkbox"] {
        accent-color: var(--primary-blue);
        width: 16px;
        height: 16px;
    }

    /* Ranges Form Table */
    .ranges-table {
        width: 100%;
        border-collapse: collapse;
    }
    .ranges-table th {
        padding: 8px;
        font-size: 12px;
        font-weight: 700;
        color: var(--text-muted);
        text-align: left;
        border-bottom: 2px solid #cbd5e1;
    }
    .ranges-table td {
        padding: 8px 4px;
        border-bottom: 1px solid #e2e8f0;
    }
    .input-group {
        display: flex;
        align-items: center;
    }
    .input-group .form-control {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
    .input-addon {
        background-color: #e2e8f0;
        border: 1px solid #cbd5e1;
        border-left: none;
        border-top-right-radius: 6px;
        border-bottom-right-radius: 6px;
        padding: 8px 10px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-muted);
    }

    .btn-add-range {
        background: none;
        border: 2px dashed var(--primary-blue);
        color: var(--primary-blue);
        padding: 10px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        width: 100%;
        transition: all 0.2s ease;
        margin-top: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .btn-add-range:hover {
        background-color: var(--primary-blue-light);
        border-style: solid;
    }

    .btn-delete-row {
        background: none;
        border: none;
        color: var(--danger-red);
        cursor: pointer;
        padding: 4px;
        font-size: 16px;
    }
    .btn-delete-row:hover {
        color: #b91c1c;
    }

    .drawer-ftr {
        padding: 16px 24px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        background-color: var(--bg-light);
    }
    .btn-cancel {
        background-color: var(--white);
        border: 1px solid #cbd5e1;
        color: var(--text-dark);
        padding: 10px 20px;
        font-size: 14px;
        font-weight: 600;
        border-radius: 8px;
        cursor: pointer;
    }
    .btn-cancel:hover {
        background-color: #f1f5f9;
    }
    .btn-save {
        background-color: var(--primary-blue);
        color: var(--white);
        border: none;
        padding: 10px 24px;
        font-size: 14px;
        font-weight: 600;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .btn-save:hover {
        background-color: var(--primary-blue-hover);
    }

    .form-note {
        font-size: 12px;
        color: var(--text-muted);
        background-color: #fef08a;
        padding: 8px 12px;
        border-radius: 6px;
        border-left: 4px solid #eab308;
    }

    /* ── GRADE SCALE DARK MODE OVERRIDES ── */
    body.dark-mode {
        --primary-blue: #818cf8;
        --primary-blue-hover: #6366f1;
        --primary-blue-light: rgba(99, 102, 241, 0.15);
        --border-blue: #1e293b;
        --dark-navy: #1f2937;
        --text-dark: #f8fafc;
        --text-muted: #94a3b8;
        --bg-light: #0f172a;
        --white: #111827;
    }
    body.dark-mode .page-hdr {
        border-color: #1e293b !important;
        background: #111827 !important;
    }
    body.dark-mode .select-academic-year {
        background-color: #1f2937 !important;
        border-color: #374151 !important;
        color: #f8fafc !important;
    }
    body.dark-mode .select-academic-year:hover {
        border-color: #818cf8 !important;
        background-color: #374151 !important;
    }
    body.dark-mode .scale-basis-box {
        border-color: #1e293b !important;
        background: #111827 !important;
    }
    body.dark-mode .scale-basis-box:hover {
        border-color: #38bdf8 !important;
        background-color: #1f2937 !important;
    }
    body.dark-mode .scale-basis-box.active {
        border-color: #818cf8 !important;
        background-color: rgba(99, 102, 241, 0.15) !important;
    }
    body.dark-mode .tabs-bar {
        border-bottom-color: #1e293b !important;
    }
    body.dark-mode .tab-btn:hover,
    body.dark-mode .tab-btn.active {
        color: #818cf8 !important;
        border-bottom-color: #818cf8 !important;
    }
    body.dark-mode .table-card {
        border-color: #1e293b !important;
        background: #111827 !important;
    }
    body.dark-mode .scale-table th {
        background-color: #1f2937 !important;
        color: #f8fafc !important;
        border-bottom: 2px solid #1e293b !important;
    }
    body.dark-mode .scale-table td {
        border-bottom-color: #1e293b !important;
        color: #cbd5e1 !important;
    }
    body.dark-mode .scale-table tr:hover td {
        background-color: rgba(255, 255, 255, 0.02) !important;
    }
    body.dark-mode .applicable-pill {
        background-color: #1f2937 !important;
        border-color: #374151 !important;
        color: #f8fafc !important;
    }
    body.dark-mode .btn-edit {
        background-color: rgba(37, 99, 235, 0.15) !important;
        color: #60a5fa !important;
    }
    body.dark-mode .btn-edit:hover {
        background-color: #2563eb !important;
        color: #ffffff !important;
    }
    body.dark-mode .btn-delete {
        background-color: rgba(239, 68, 68, 0.15) !important;
        color: #f87171 !important;
    }
    body.dark-mode .btn-delete:hover {
        background-color: #ef4444 !important;
        color: #ffffff !important;
    }
    body.dark-mode .drawer-container {
        background-color: #111827 !important;
        border-left: 1px solid #1e293b !important;
    }
    body.dark-mode .drawer-hdr {
        background-color: #1f2937 !important;
        border-bottom-color: #1e293b !important;
    }
    body.dark-mode .form-section {
        border-color: #1e293b !important;
        background-color: #0f172a !important;
    }
    body.dark-mode .form-section-title {
        color: #818cf8 !important;
        border-bottom-color: #1e293b !important;
    }
    body.dark-mode .form-control {
        border-color: #374151 !important;
        color: #f8fafc !important;
        background-color: #1f2937 !important;
    }
    body.dark-mode .class-checkbox-label {
        border-color: #374151 !important;
        background-color: #1f2937 !important;
        color: #cbd5e1 !important;
    }
    body.dark-mode .class-checkbox-label:hover {
        border-color: #818cf8 !important;
        background-color: rgba(99, 102, 241, 0.1) !important;
    }
    body.dark-mode .ranges-table th {
        border-bottom-color: #374151 !important;
    }
    body.dark-mode .ranges-table td {
        border-bottom-color: #1e293b !important;
    }
    body.dark-mode .input-addon {
        background-color: #374151 !important;
        border-color: #374151 !important;
        color: #cbd5e1 !important;
    }
    body.dark-mode .btn-add-range {
        border-color: #818cf8 !important;
        color: #818cf8 !important;
    }
    body.dark-mode .btn-add-range:hover {
        background-color: rgba(99, 102, 241, 0.1) !important;
    }
    body.dark-mode .drawer-ftr {
        border-top-color: #1e293b !important;
        background-color: #1f2937 !important;
    }
    body.dark-mode .btn-cancel {
        background-color: #1f2937 !important;
        border-color: #374151 !important;
        color: #cbd5e1 !important;
    }
    body.dark-mode .btn-cancel:hover {
        background-color: #374151 !important;
    }
    body.dark-mode .form-note {
        background-color: rgba(234, 179, 8, 0.1) !important;
        border-left-color: #eab308 !important;
        color: #fef08a !important;
    }
</style>

<div class="page-hdr">
    <div class="page-hdr-left">
        <h1>
            <span style="display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:50%; background-color:var(--primary-blue-light); color:var(--primary-blue);">
                <i class="fas fa-percent" style="font-size:16px;"></i>
            </span>
            Grade Scale
        </h1>
        <p>Examination / Configure grading policies and marks brackets</p>
    </div>
    <div class="page-hdr-right">
        <select class="select-academic-year">
            <option>Apr 2025 - Mar 2026</option>
        </select>
        <button type="button" class="btn-create-scale" onclick="openCreateModal()">
            <i class="fas fa-plus"></i> CREATE GRADE SCALE
        </button>
    </div>
</div>

<!-- Scale Basis Toggle -->
<div class="scale-basis-container">
    <label class="scale-basis-box active" id="basis-subject-label">
        <input type="radio" name="filter_scale_basis" value="subject" checked onchange="handleBasisChange(this)">
        <div class="scale-basis-circle"></div>
        <div class="scale-basis-text">
            <span class="scale-basis-title">Subject Wise Grade</span>
            <span class="scale-basis-subtitle">Grades derived from subject marks</span>
        </div>
    </label>
    <label class="scale-basis-box" id="basis-attendance-label">
        <input type="radio" name="filter_scale_basis" value="attendance" onchange="handleBasisChange(this)">
        <div class="scale-basis-circle"></div>
        <div class="scale-basis-text">
            <span class="scale-basis-title">Attendance Wise Grade</span>
            <span class="scale-basis-subtitle">Grades based on attendance percentage</span>
        </div>
    </label>
</div>

<!-- Tabs for Type -->
<div class="tabs-bar">
    <button type="button" class="tab-btn active" data-tab="scholastic" onclick="switchTab('scholastic')">SCHOLASTIC</button>
    <button type="button" class="tab-btn" data-tab="custom_subject" onclick="switchTab('custom_subject')">CUSTOM SUBJECT</button>
    <button type="button" class="tab-btn" data-tab="non_scholastic" onclick="switchTab('non_scholastic')">NON SCHOLASTIC</button>
</div>

<!-- List of Grade Scales -->
<div class="table-card">
    <table class="scale-table">
        <thead>
            <tr>
                <th style="width: 30%;">Grade Scale Name</th>
                <th style="width: 50%;">Applicable On</th>
                <th style="width: 20%; text-align: right;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($gradeScales as $scale)
                @php
                    // Map class IDs to Names
                    $applicableClassNames = [];
                    if (is_array($scale->applicable_classes)) {
                        foreach ($scale->applicable_classes as $cid) {
                            $cObj = $classes->firstWhere('id', $cid);
                            if ($cObj) {
                                $applicableClassNames[] = $cObj->name;
                            }
                        }
                    }
                @endphp
                <tr class="scale-row" data-id="{{ $scale->id }}" data-basis="{{ $scale->scale_basis }}" data-type="{{ $scale->type }}">
                    <td style="font-weight: 700; color: var(--dark-navy);">{{ $scale->name }}</td>
                    <td>
                        @if(empty($applicableClassNames))
                            <span style="color: var(--text-muted); font-style: italic;">None selected</span>
                        @else
                            @foreach($applicableClassNames as $cname)
                                <span class="applicable-pill">{{ $cname }}</span>
                            @endforeach
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <div class="action-btn-group" style="justify-content: flex-end;">
                            <button type="button" class="btn-action btn-edit" title="Edit" onclick="openEditModal({{ json_encode($scale) }})">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                            <form id="delete-form-{{ $scale->id }}" method="POST" action="{{ route('school.examination.grade-scale') }}" style="display:none;">
                                @csrf
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="{{ $scale->id }}">
                            </form>
                            <button type="button" class="btn-action btn-delete" title="Delete" onclick="if(confirm('Are you sure you want to delete this grade scale?')) document.getElementById('delete-form-{{ $scale->id }}').submit();">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <!-- Handled by empty state if all rows hidden -->
            @endforelse
            <tr id="empty-state" style="display: none;">
                <td colspan="3">
                    <div class="empty-state">
                        <i class="fas fa-percentage"></i>
                        <h3>No Grade Scales Configured</h3>
                        <p>Get started by creating a new grade scale for this category.</p>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Sliding Drawer Overlay -->
<div class="drawer-overlay" id="drawer-overlay" onclick="closeDrawer()"></div>

<!-- Sliding Drawer Container -->
<div class="drawer-container" id="drawer-container">
    <div class="drawer-hdr">
        <h2 id="modal-title">Create Grade Scale</h2>
        <button type="button" class="drawer-close" onclick="closeDrawer()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <form method="POST" action="{{ route('school.examination.grade-scale') }}" style="display:flex; flex-direction:column; height:calc(100% - 61px);">
        @csrf
        <input type="hidden" id="scale-id" name="id" value="">

        <div class="drawer-body">
            <!-- Name & Types -->
            <div class="form-section">
                <div class="form-section-title">General Info</div>
                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="form-label" for="scale-name">Enter Name *</label>
                    <input type="text" id="scale-name" name="name" class="form-control" placeholder="e.g. FOR SCHOLASTIC" required>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label class="form-label">Scale Basis</label>
                        <div style="display: flex; gap: 16px; margin-top: 6px;">
                            <label style="display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; cursor: pointer;">
                                <input type="radio" name="scale_basis" value="subject" checked style="accent-color: var(--primary-blue);"> Subject Wise
                            </label>
                            <label style="display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; cursor: pointer;">
                                <input type="radio" name="scale_basis" value="attendance" style="accent-color: var(--primary-blue);"> Attendance Wise
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Classification Type</label>
                        <div style="display: flex; gap: 12px; margin-top: 6px;">
                            <label style="display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; cursor: pointer;">
                                <input type="radio" name="type" value="scholastic" checked style="accent-color: var(--primary-blue);"> Scholastic
                            </label>
                            <label style="display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; cursor: pointer;">
                                <input type="radio" name="type" value="custom_subject" style="accent-color: var(--primary-blue);"> Custom Subject
                            </label>
                            <label style="display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; cursor: pointer;">
                                <input type="radio" name="type" value="non_scholastic" style="accent-color: var(--primary-blue);"> Non Scholastic
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Applicable Classes -->
            <div class="form-section">
                <div class="form-section-title" style="display: flex; justify-content: space-between; align-items: center;">
                    <span>Classes Applicable On</span>
                    <label style="display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; cursor: pointer; color: var(--primary-blue);">
                        <input type="checkbox" id="select-all-classes" onchange="toggleSelectAllClasses(this)" style="accent-color: var(--primary-blue);"> Select all
                    </label>
                </div>
                <div class="classes-grid">
                    @foreach($classes as $c)
                        <label class="class-checkbox-label">
                            <input type="checkbox" name="applicable_classes[]" value="{{ $c->id }}" class="class-checkbox" onchange="updateSelectAllCheckbox()">
                            <span>{{ $c->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Marks Range Table -->
            <div class="form-section">
                <div class="form-section-title">Marks Range</div>
                <div style="overflow-x: auto;">
                    <table class="ranges-table">
                        <thead>
                            <tr>
                                <th style="width: 20%;">From (Marks) %</th>
                                <th style="width: 20%;">To (Marks) %</th>
                                <th style="width: 15%;">Points</th>
                                <th style="width: 20%;">Grade Value</th>
                                <th style="width: 20%;">Key Value</th>
                                <th style="width: 10%; text-align: center;">Fail</th>
                                <th style="width: 5%;"></th>
                            </tr>
                        </thead>
                        <tbody id="ranges-tbody">
                            <!-- Rows added dynamically by Javascript -->
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn-add-range" onclick="addRangeRow()">
                    <i class="fas fa-plus"></i> ADD MARKS RANGE
                </button>
            </div>

            <!-- Note at bottom -->
            <div class="form-note">
                <strong>*Note:</strong> If grade scale like 0-33, 33-50 is added then, 33 will be considered in 0-33 grade scale.
            </div>
        </div>

        <div class="drawer-ftr">
            <button type="button" class="btn-cancel" onclick="closeDrawer()">Cancel</button>
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i> SAVE
            </button>
        </div>
    </form>
</div>

<script>
    // Handle tab switching in list view
    function switchTab(tabName) {
        document.querySelectorAll('.tab-btn').forEach(btn => {
            if (btn.dataset.tab === tabName) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
        filterList();
    }

    // Handle basis selection change
    function handleBasisChange(input) {
        document.getElementById('basis-subject-label').classList.remove('active');
        document.getElementById('basis-attendance-label').classList.remove('active');
        
        if (input.checked) {
            input.closest('.scale-basis-box').classList.add('active');
        }
        filterList();
    }

    // Filter rows in table list
    function filterList() {
        const selectedBasis = document.querySelector('input[name="filter_scale_basis"]:checked').value;
        const activeTab = document.querySelector('.tab-btn.active').dataset.tab;
        
        const rows = document.querySelectorAll('.scale-row');
        let visibleCount = 0;
        
        rows.forEach(row => {
            const rowBasis = row.dataset.basis;
            const rowType = row.dataset.type;
            
            if (rowBasis === selectedBasis && rowType === activeTab) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        const emptyState = document.getElementById('empty-state');
        if (visibleCount === 0) {
            emptyState.style.display = '';
        } else {
            emptyState.style.display = 'none';
        }
    }

    // Toggle Select All Classes
    function toggleSelectAllClasses(masterCheckbox) {
        const checkboxes = document.querySelectorAll('.class-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = masterCheckbox.checked;
        });
    }

    // Update Select All Checkbox based on children
    function updateSelectAllCheckbox() {
        const checkboxes = document.querySelectorAll('.class-checkbox');
        const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
        const master = document.getElementById('select-all-classes');
        master.checked = checkboxes.length > 0 && checkedCount === checkboxes.length;
    }

    // Add Range Row dynamically
    function addRangeRow(from = '', to = '', points = '', gradeValue = '', keyValue = '', fail = false) {
        const tbody = document.getElementById('ranges-tbody');
        const rowIndex = tbody.children.length;
        
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <div class="input-group">
                    <input type="number" step="0.01" min="0" max="100" class="form-control" name="ranges[${rowIndex}][from]" value="${from}" required>
                    <span class="input-addon">%</span>
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input type="number" step="0.01" min="0" max="100" class="form-control" name="ranges[${rowIndex}][to]" value="${to}" required>
                    <span class="input-addon">%</span>
                </div>
            </td>
            <td>
                <input type="number" class="form-control" name="ranges[${rowIndex}][points]" value="${points}">
            </td>
            <td>
                <input type="text" class="form-control" name="ranges[${rowIndex}][grade_value]" value="${gradeValue}" placeholder="e.g. A+" required>
            </td>
            <td>
                <input type="text" class="form-control" name="ranges[${rowIndex}][key_value]" value="${keyValue}" placeholder="e.g. Excellent">
            </td>
            <td style="text-align:center;">
                <input type="checkbox" name="ranges[${rowIndex}][fail]" ${fail ? 'checked' : ''} value="1">
            </td>
            <td style="text-align:center;">
                <button type="button" class="btn-delete-row" onclick="this.closest('tr').remove(); reindexRows();">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    }

    // Reindex range rows after delete to ensure clean array in PHP POST
    function reindexRows() {
        const tbody = document.getElementById('ranges-tbody');
        Array.from(tbody.children).forEach((tr, index) => {
            tr.querySelectorAll('input').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    const newName = name.replace(/ranges\[\d+\]/, `ranges[${index}]`);
                    input.setAttribute('name', newName);
                }
            });
        });
    }

    // Open Modal/Drawer in Create Mode
    function openCreateModal() {
        document.getElementById('modal-title').innerText = 'Create Grade Scale';
        document.getElementById('scale-id').value = '';
        document.getElementById('scale-name').value = '';
        
        // Pre-select basis based on active filter
        const activeBasis = document.querySelector('input[name="filter_scale_basis"]:checked').value;
        const basisRadio = document.querySelector(`input[name="scale_basis"][value="${activeBasis}"]`);
        if (basisRadio) basisRadio.checked = true;
        
        // Pre-select type based on active tab
        const activeTab = document.querySelector('.tab-btn.active').dataset.tab;
        const typeRadio = document.querySelector(`input[name="type"][value="${activeTab}"]`);
        if (typeRadio) typeRadio.checked = true;
        
        // Clear classes
        const checkboxes = document.querySelectorAll('.class-checkbox');
        checkboxes.forEach(cb => cb.checked = false);
        document.getElementById('select-all-classes').checked = false;
        
        // Clear and add 1 empty row
        const tbody = document.getElementById('ranges-tbody');
        tbody.innerHTML = '';
        
        // Prepopulate with a default 5 rows matching scholastic standard to save time
        if (activeTab === 'scholastic' || activeTab === 'non_scholastic') {
            addRangeRow(0, 33, 5, 'NOT GOOD', 'no good', false);
            addRangeRow(33, 45, 4, 'AVERAGE', 'average', false);
            addRangeRow(45, 65, 3, 'GOOD', 'good', false);
            addRangeRow(65, 85, 2, 'VERY GOOD', 'very good', false);
            addRangeRow(85, 100, 1, 'EXCELLENT', 'excellent', false);
        } else {
            addRangeRow();
        }
        
        // Open
        document.getElementById('drawer-overlay').classList.add('open');
        document.getElementById('drawer-container').classList.add('open');
    }

    // Open Modal/Drawer in Edit Mode
    function openEditModal(scaleData) {
        document.getElementById('modal-title').innerText = 'Edit Grade Scale';
        document.getElementById('scale-id').value = scaleData.id;
        document.getElementById('scale-name').value = scaleData.name;
        
        // Set scale basis
        const basisRadio = document.querySelector(`input[name="scale_basis"][value="${scaleData.scale_basis}"]`);
        if (basisRadio) basisRadio.checked = true;
        
        // Set type
        const typeRadio = document.querySelector(`input[name="type"][value="${scaleData.type}"]`);
        if (typeRadio) typeRadio.checked = true;
        
        // Set classes
        const checkboxes = document.querySelectorAll('.class-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = scaleData.applicable_classes && scaleData.applicable_classes.map(String).includes(String(cb.value));
        });
        updateSelectAllCheckbox();
        
        // Set ranges
        const tbody = document.getElementById('ranges-tbody');
        tbody.innerHTML = '';
        if (scaleData.ranges && scaleData.ranges.length > 0) {
            scaleData.ranges.forEach(r => {
                addRangeRow(r.from, r.to, r.points, r.grade_value, r.key_value, r.fail);
            });
        } else {
            addRangeRow();
        }
        
        // Open
        document.getElementById('drawer-overlay').classList.add('open');
        document.getElementById('drawer-container').classList.add('open');
    }

    // Close Drawer
    function closeDrawer() {
        document.getElementById('drawer-overlay').classList.remove('open');
        document.getElementById('drawer-container').classList.remove('open');
    }

    // On page load
    document.addEventListener('DOMContentLoaded', () => {
        filterList();
    });
</script>
@endsection
