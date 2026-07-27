@extends('layouts.app')

@section('title', 'Manage Designations')
@section('page-title', 'Manage Designations')

@section('styles')
<style>
/* ── Blue-white theme overrides matching Role Category ── */
:root {
    --dg-blue: #1d4ed8;
    --dg-blue-light: #3b82f6;
    --dg-blue-xlight: #eff6ff;
    --dg-blue-border: #bfdbfe;
    --dg-white: #fff;
    --dg-text-dark: #1e3a5f;
    --dg-text-muted: #64748b;
    --dg-row-alt: #f8faff;
    --dg-hover: #eff6ff;
    --dg-input-bg: #fff;
    --dg-selected: #dbeafe;
    --dg-red: #ef4444;
    --dg-red-light: #fef2f2;
    --dg-red-border: #fecaca;
    --dg-green: #10b981;
    --dg-green-light: #ecfdf5;
    --dg-green-border: #a7f3d0;
    --dg-purple: #8b5cf6;
    --dg-purple-light: #f5f3ff;
    --dg-purple-border: #ddd6fe;
}

body.dark-mode {
    --dg-blue: #3b82f6;
    --dg-blue-light: #60a5fa;
    --dg-blue-xlight: #1e293b;
    --dg-blue-border: #374151;
    --dg-white: #111827;
    --dg-text-dark: #f8fafc;
    --dg-text-muted: #94a3b8;
    --dg-row-alt: #1f2937;
    --dg-hover: rgba(255, 255, 255, 0.04);
    --dg-input-bg: #1f2937;
    --dg-selected: rgba(59, 130, 246, 0.25);
}

.dg-page-header {
    background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 60%, #60a5fa 100%);
    border-radius: 16px;
    padding: 28px 32px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 8px 32px rgba(29,78,216,.25);
}
.dg-page-header h1 {
    color: #fff;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 22px;
    font-weight: 800;
    margin-bottom: 4px;
}
.dg-page-header p { color: rgba(255,255,255,.75); font-size: 13px; }

.dg-add-btn {
    background: rgba(255,255,255,.15);
    color: #fff;
    border: 1.5px solid rgba(255,255,255,.4);
    border-radius: 10px;
    padding: 11px 22px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 7px;
    transition: .2s;
    text-decoration: none;
    white-space: nowrap;
}
.dg-add-btn:hover { background: rgba(255,255,255,.28); transform: translateY(-1px); color: #fff; }

/* Search bar styling */
.dg-search-card {
    background: var(--dg-white);
    border: 1px solid var(--dg-blue-border);
    border-radius: 14px;
    padding: 18px 24px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(29,78,216,.04);
}
.dg-search-form {
    display: flex;
    gap: 12px;
    align-items: center;
}
.dg-search-input-wrapper {
    position: relative;
    flex: 1;
}
.dg-search-input-wrapper i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--dg-text-muted);
    font-size: 14px;
}
.dg-search-input {
    width: 100%;
    padding: 10px 16px 10px 40px;
    border: 1.5px solid var(--dg-blue-border);
    border-radius: 10px;
    font-size: 13.5px;
    color: var(--dg-text-dark);
    background-color: var(--dg-input-bg);
    outline: none;
    transition: all .2s;
}
.dg-search-input:focus {
    border-color: var(--dg-blue-light);
    box-shadow: 0 0 0 3px rgba(59,130,246,.12);
}
.dg-search-btn {
    background: var(--dg-blue);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 10px 24px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: .2s;
}
.dg-search-btn:hover { background: #1e40af; }
.dg-clear-btn {
    background: var(--dg-input-bg);
    color: var(--dg-text-muted);
    border: 1.5px solid var(--dg-blue-border);
    border-radius: 10px;
    padding: 9px 18px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: .2s;
}
.dg-clear-btn:hover { background: var(--dg-hover); color: var(--dg-text-dark); }

/* Table card */
.dg-table-card {
    background: var(--dg-white);
    border: 1px solid var(--dg-blue-border);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(29,78,216,.06);
}
.dg-table { width: 100%; border-collapse: collapse; }
.dg-table th {
    padding: 14px 20px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--dg-text-muted);
    background: var(--dg-row-alt);
    border-bottom: 1.5px solid var(--dg-blue-border);
    text-align: left;
}
.dg-table td {
    padding: 16px 20px;
    font-size: 13px;
    color: var(--dg-text-dark);
    border-bottom: 1px solid var(--dg-blue-border);
    vertical-align: middle;
}
.dg-table tr:last-child td { border-bottom: none; }
.dg-table tr:nth-child(even) td { background: var(--dg-row-alt); }
.dg-table tr:hover td { background: var(--dg-hover); }

/* Badge tags */
.role-badge {
    font-size: 11px;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-transform: uppercase;
}
.role-badge.school_admin {
    background: var(--dg-red-light);
    color: #b91c1c;
    border: 1px solid var(--dg-red-border);
}
.role-badge.teacher {
    background: var(--dg-blue-xlight);
    color: var(--dg-blue);
    border: 1px solid var(--dg-blue-border);
}
.role-badge.accountant {
    background: var(--dg-green-light);
    color: #047857;
    border: 1px solid var(--dg-green-border);
}
.role-badge.driver {
    background: var(--dg-purple-light);
    color: #6d28d9;
    border: 1px solid var(--dg-purple-border);
}
.role-badge.none {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
}

.staff-count-badge {
    background: var(--dg-blue-xlight);
    color: var(--dg-blue);
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 12px;
}

.action-btn-group {
    display: flex;
    gap: 8px;
}
.action-btn {
    background: var(--dg-blue-xlight);
    color: var(--dg-blue);
    border: 1.5px solid var(--dg-blue-border);
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: all .2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.action-btn:hover {
    background: var(--dg-blue);
    color: #fff;
    border-color: var(--dg-blue);
    transform: translateY(-1px);
}
.action-btn.delete-btn {
    background: #fff5f5;
    color: var(--dg-red);
    border-color: #fca5a5;
}
.action-btn.delete-btn:hover {
    background: var(--dg-red);
    color: #fff;
    border-color: var(--dg-red);
}

/* ── SLIDE-IN PANEL ── */
.dg-panel-backdrop {
    position: fixed; inset: 0;
    background: rgba(15,23,42,.45);
    z-index: 990;
    display: none;
    backdrop-filter: blur(3px);
}
.dg-panel-backdrop.open { display: block; }

.dg-panel {
    position: fixed; top: 0; right: -460px;
    width: 440px; height: 100vh;
    background: var(--dg-white);
    z-index: 1000;
    box-shadow: -8px 0 40px rgba(29,78,216,.18);
    display: flex; flex-direction: column;
    transition: right .35s cubic-bezier(.4,0,.2,1);
    border-left: 1px solid var(--dg-blue-border);
}
.dg-panel.open { right: 0; }

.dg-panel-header {
    padding: 22px 24px 16px;
    background: linear-gradient(135deg, var(--dg-blue) 0%, var(--dg-blue-light) 100%);
    display: flex; align-items: flex-start; justify-content: space-between;
    flex-shrink: 0;
}
.dg-panel-header h3 {
    color: #fff;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 16px; font-weight: 800;
    margin-bottom: 3px;
}
.dg-panel-header p { color: rgba(255,255,255,.75); font-size: 11.5px; }
.dg-panel-close {
    background: rgba(255,255,255,.15); border: none;
    color: #fff; width: 30px; height: 30px;
    border-radius: 8px; cursor: pointer; font-size: 14px;
    display: flex; align-items: center; justify-content: center;
    transition: .2s; flex-shrink: 0;
}
.dg-panel-close:hover { background: rgba(255,255,255,.25); }

.dg-panel-body {
    flex: 1; overflow-y: auto; padding: 24px;
}
.dg-panel-body::-webkit-scrollbar { width: 4px; }
.dg-panel-body::-webkit-scrollbar-thumb { background: var(--dg-blue-border); border-radius: 4px; }

.form-group {
    margin-bottom: 20px;
}
.form-group label {
    display: block;
    font-size: 12.5px;
    font-weight: 700;
    color: var(--dg-text-dark);
    margin-bottom: 6px;
}
.form-group label span {
    color: var(--dg-red);
}
.form-control {
    width: 100%;
    padding: 10px 14px;
    border: 1.5px solid var(--dg-blue-border);
    border-radius: 9px;
    font-size: 13px;
    outline: none;
    background-color: var(--dg-input-bg);
    color: var(--dg-text-dark);
    transition: .2s;
    font-family: 'Inter', sans-serif;
}
.form-control:focus {
    border-color: var(--dg-blue-light);
    box-shadow: 0 0 0 3px rgba(59,130,246,.12);
}

.dg-panel-footer {
    padding: 16px 24px;
    border-top: 1px solid var(--dg-blue-border);
    display: flex; gap: 10px; align-items: center;
    flex-shrink: 0;
    background: var(--dg-row-alt);
}
.dg-panel-save {
    flex: 1; padding: 11px;
    background: var(--dg-blue); color: #fff;
    border: none; border-radius: 9px;
    font-size: 13px; font-weight: 700;
    cursor: pointer; transition: .2s;
    display: flex; align-items: center; justify-content: center; gap: 6px;
}
.dg-panel-save:hover { background: #1e40af; }
.dg-panel-cancel {
    padding: 11px 18px;
    background: none; color: var(--dg-text-muted);
    border: 1.5px solid var(--dg-blue-border);
    border-radius: 9px; font-size: 13px;
    font-weight: 600; cursor: pointer; transition: .2s;
}
.dg-panel-cancel:hover { background: var(--dg-blue-xlight); color: var(--dg-blue); }

.dg-empty {
    padding: 40px 20px; text-align: center; color: var(--dg-text-muted);
}
.dg-empty i { font-size: 36px; color: var(--dg-blue-border); margin-bottom: 10px; display: block; }

.dg-tab-btn {
    border-bottom: 3px solid transparent !important;
    border-radius: 0 !important;
    padding: 12px 20px !important;
    font-size: 14px !important;
    color: var(--dg-text-muted) !important;
    background: none !important;
    border: none !important;
    font-weight: 700 !important;
    cursor: pointer !important;
    transition: .2s !important;
    outline: none !important;
}
.dg-tab-btn:hover {
    color: var(--dg-blue-light) !important;
}
.dg-tab-btn.active {
    color: var(--dg-blue) !important;
    border-bottom: 3px solid var(--dg-blue) !important;
}
body.dark-mode .dg-tab-btn.active {
    color: var(--dg-blue-light) !important;
    border-bottom-color: var(--dg-blue-light) !important;
}

/* ── MOBILE RESPONSIVE STYLES ── */
@media (max-width: 768px) {
    .dg-page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
        padding: 20px;
    }
    .dg-page-header h1 {
        font-size: 18px;
    }
    .dg-add-btn {
        width: 100%;
        justify-content: center;
    }
    .dg-search-card {
        padding: 14px 16px;
    }
    .dg-search-form {
        flex-direction: column;
        gap: 10px;
        align-items: stretch;
    }
    .dg-search-btn, .dg-clear-btn {
        width: 100%;
        text-align: center;
        justify-content: center;
    }
    .dg-table-card {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border-radius: 12px;
    }
    .dg-table {
        min-width: 650px;
    }
    .dg-table th, .dg-table td {
        padding: 12px 14px;
    }
    /* Slide-in panel full width on mobile */
    .dg-panel {
        width: 100%;
        right: -100%;
    }
    .dg-panel.open {
        right: 0;
    }

    /* Mobile pagination: Hide all page numbers, show ONLY Prev (<) & Next (>) arrows */
    .dg-pagination-wrap nav[role="navigation"] span.shadow-sm > a,
    .dg-pagination-wrap nav[role="navigation"] span.shadow-sm > span,
    .dg-pagination-wrap .pagination li {
        display: none !important;
    }
    .dg-pagination-wrap nav[role="navigation"] span.shadow-sm > a:first-child,
    .dg-pagination-wrap nav[role="navigation"] span.shadow-sm > span:first-child,
    .dg-pagination-wrap nav[role="navigation"] span.shadow-sm > a:last-child,
    .dg-pagination-wrap nav[role="navigation"] span.shadow-sm > span:last-child,
    .dg-pagination-wrap .pagination li:first-child,
    .dg-pagination-wrap .pagination li:last-child {
        display: inline-flex !important;
    }
    .dg-pagination-wrap nav[role="navigation"] span.shadow-sm {
        display: flex !important;
        justify-content: center !important;
        gap: 20px !important;
        width: 100% !important;
    }
}

@media (max-width: 480px) {
    .dg-table {
        min-width: 580px;
    }
    .dg-table th, .dg-table td {
        padding: 10px 12px;
        font-size: 12px;
    }
}
</style>
@endsection

@section('content')

{{-- Page Header --}}
<div class="dg-page-header">
    <div>
        <h1><i class="fas fa-id-card-clip" style="margin-right:10px;opacity:.9;"></i>Role Category — Manage Designations & Departments</h1>
        <p>Create staff departments and designations, then connect designations to departments and map roles.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <button type="button" class="dg-add-btn" id="addDesgBtn" onclick="openCreatePanel()">
            <i class="fas fa-plus-circle"></i> Add Designation
        </button>
        <button type="button" class="dg-add-btn" id="addDeptBtn" onclick="openCreateDeptPanel()" style="display: none;">
            <i class="fas fa-plus-circle"></i> Add Department
        </button>
    </div>
</div>

{{-- Tabs Navigation --}}
<div class="dg-tabs" style="display: flex; gap: 15px; margin-bottom: 20px; border-bottom: 1px solid var(--dg-blue-border); padding-bottom: 0;">
    <button class="dg-tab-btn active" id="tab-designations" onclick="switchTab('designations')">
        <i class="fas fa-id-card-clip" style="margin-right: 6px;"></i> Designations
    </button>
    <button class="dg-tab-btn" id="tab-departments" onclick="switchTab('departments')">
        <i class="fas fa-sitemap" style="margin-right: 6px;"></i> Departments
    </button>
</div>

{{-- Section: Designations --}}
<div id="section-designations">
    {{-- Search Card --}}
    <div class="dg-search-card">
        <form method="GET" action="{{ route('school.designations.index') }}" class="dg-search-form">
            <div class="dg-search-input-wrapper">
                <i class="fas fa-magnifying-glass"></i>
                <input type="text"
                       name="search"
                       class="dg-search-input"
                       value="{{ $search }}"
                       placeholder="Search designations by name or description...">
            </div>
            <button type="submit" class="dg-search-btn"><i class="fas fa-filter" style="margin-right: 5px;"></i> Filter</button>
            @if($search)
                <a href="{{ route('school.designations.index') }}" class="dg-clear-btn">Clear</a>
            @endif
        </form>
    </div>

    @if(!$designations->count())
    <div class="dg-empty" style="background:#fff;border-radius:14px;border:1px solid var(--dg-blue-border);">
        <i class="fas fa-folder-open"></i>
        <strong style="display:block;color:#1e3a5f;font-size:15px;margin-bottom:6px;">No Designations Found</strong>
        <p>Get started by creating a new designation.</p>
    </div>
    @else
    {{-- Listing --}}
    <div class="dg-table-card">
        <table class="dg-table">
            <thead>
                <tr>
                    <th style="width: 20%;">Designation Name</th>
                    <th style="width: 20%;">Department</th>
                    <th style="width: 30%;">Description</th>
                    <th style="width: 15%;">System Role / Privilege</th>
                    <th style="width: 10%;">Assigned Staff</th>
                    <th style="width: 5%; text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($designations as $desg)
                <tr id="desg-row-{{ $desg->id }}">
                    <td>
                        <div style="font-weight: 700; color: var(--dg-text-dark);">{{ $desg->name }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 600; color: var(--dg-blue);">{{ $desg->department ? $desg->department->name : 'N/A' }}</div>
                    </td>
                    <td>
                        <div style="color: var(--dg-text-muted);">{{ $desg->description ?: 'No description provided' }}</div>
                    </td>
                    <td>
                        <span class="role-badge {{ $desg->system_role ?: 'none' }}">
                            @if($desg->system_role == 'school_admin')
                                School Admin
                            @elseif($desg->system_role == 'teacher')
                                Teacher
                            @elseif($desg->system_role == 'accountant')
                                Accountant
                            @elseif($desg->system_role == 'driver')
                                Driver
                            @else
                                No Role / Basic Staff
                            @endif
                        </span>
                    </td>
                    <td>
                        <span class="staff-count-badge">
                            {{ $desg->staffs_count }} Staff
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <div class="action-btn-group">
                            <button type="button"
                                    class="action-btn"
                                    onclick="openEditPanel(this)"
                                    data-id="{{ $desg->id }}"
                                    data-name="{{ $desg->name }}"
                                    data-dept="{{ $desg->department_id }}"
                                    data-desc="{{ $desg->description }}"
                                    data-role="{{ $desg->system_role ?: 'none' }}">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button type="button"
                                    class="action-btn delete-btn"
                                    onclick="deleteDesignation({{ $desg->id }}, '{{ addslashes($desg->name) }}')">
                                <i class="fas fa-trash-can"></i> Delete
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 15px;" class="dg-pagination-wrap">
        {{ $designations->links() }}
    </div>
    @endif
</div>

{{-- Section: Departments --}}
<div id="section-departments" style="display: none;">
    @if(!$departments->count())
    <div class="dg-empty" style="background:#fff;border-radius:14px;border:1px solid var(--dg-blue-border);">
        <i class="fas fa-folder-open"></i>
        <strong style="display:block;color:#1e3a5f;font-size:15px;margin-bottom:6px;">No Departments Found</strong>
        <p>Get started by creating a new department.</p>
    </div>
    @else
    <div class="dg-table-card">
        <table class="dg-table">
            <thead>
                <tr>
                    <th style="width: 30%;">Department Name</th>
                    <th style="width: 55%;">Description</th>
                    <th style="width: 15%; text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($departments as $dept)
                <tr id="dept-row-{{ $dept->id }}">
                    <td>
                        <div style="font-weight: 700; color: var(--dg-text-dark);">{{ $dept->name }}</div>
                    </td>
                    <td>
                        <div style="color: var(--dg-text-muted);">{{ $dept->description ?: 'No description provided' }}</div>
                    </td>
                    <td style="text-align: center;">
                        <div class="action-btn-group">
                            <button type="button"
                                    class="action-btn"
                                    onclick="openEditDeptPanel(this)"
                                    data-id="{{ $dept->id }}"
                                    data-name="{{ $dept->name }}"
                                    data-desc="{{ $dept->description }}">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button type="button"
                                    class="action-btn delete-btn"
                                    onclick="deleteDepartment({{ $dept->id }}, '{{ addslashes($dept->name) }}')">
                                <i class="fas fa-trash-can"></i> Delete
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- Slide-In Panel for Designation --}}
<div class="dg-panel-backdrop" id="panelBackdrop" onclick="closePanel()"></div>
<div class="dg-panel" id="designationPanel">
    <div class="dg-panel-header">
        <div>
            <h3 id="panelTitle">Add Designation</h3>
            <p id="panelSubtitle">Define a new designation & privilege mapping</p>
        </div>
        <button class="dg-panel-close" onclick="closePanel()">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="dg-panel-body">
        <form id="designationForm">
            <input type="hidden" id="desgId" name="id">
            
            <div class="form-group">
                <label for="desgName">Designation Name <span>*</span></label>
                <input type="text" id="desgName" name="name" class="form-control" placeholder="e.g. Senior Teacher, Receptionist..." required>
            </div>

            <div class="form-group">
                <label for="desgDept">Department <span>*</span></label>
                <select id="desgDept" name="department_id" class="form-control" required>
                    <option value="">Select Department...</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="desgDesc">Description</label>
                <textarea id="desgDesc" name="description" class="form-control" style="height: 100px; resize: none;" placeholder="Provide brief details about this role..."></textarea>
            </div>

            <div class="form-group">
                <label for="desgRole">System Role / Privilege Mapping <span>*</span></label>
                <select id="desgRole" name="system_role" class="form-control" required>
                    <option value="none">None / Basic Staff (No admin privilege)</option>
                    <option value="teacher">Teacher (Access teacher dashboard & classes)</option>
                    <option value="school_admin">School Admin (Full administrative access)</option>
                    <option value="accountant">Accountant (Access fee & expense modules)</option>
                    <option value="driver">Driver (Access transport dashboard)</option>
                </select>
                <div style="margin-top: 8px; font-size: 11px; color: var(--dg-text-muted); line-height: 1.4;">
                    Selecting a system role automatically assigns that dashboard's privileges to anyone assigned this designation.
                </div>
            </div>
        </form>
    </div>

    <div class="dg-panel-footer">
        <button class="dg-panel-save" id="panelSaveBtn" onclick="saveDesignation()">
            <i class="fas fa-check"></i> Save Designation
        </button>
        <button class="dg-panel-cancel" onclick="closePanel()">Cancel</button>
    </div>
</div>

{{-- Slide-In Panel for Department --}}
<div class="dg-panel" id="departmentPanel" style="z-index: 1001;">
    <div class="dg-panel-header">
        <div>
            <h3 id="deptPanelTitle">Add Department</h3>
            <p id="deptPanelSubtitle">Define a new staff department</p>
        </div>
        <button class="dg-panel-close" onclick="closeDeptPanel()">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="dg-panel-body">
        <form id="departmentForm">
            <input type="hidden" id="deptId" name="id">
            
            <div class="form-group">
                <label for="deptName">Department Name <span>*</span></label>
                <input type="text" id="deptName" name="name" class="form-control" placeholder="e.g. Teaching, Non Teaching, Security Guard..." required>
            </div>

            <div class="form-group">
                <label for="deptDesc">Description</label>
                <textarea id="deptDesc" name="description" class="form-control" style="height: 100px; resize: none;" placeholder="Provide brief details about this department..."></textarea>
            </div>
        </form>
    </div>

    <div class="dg-panel-footer">
        <button class="dg-panel-save" id="deptPanelSaveBtn" onclick="saveDepartment()">
            <i class="fas fa-check"></i> Save Department
        </button>
        <button class="dg-panel-cancel" onclick="closeDeptPanel()">Cancel</button>
    </div>
</div>

@endsection

@section('scripts')
<script>
let isEditMode = false;
let isDeptEditMode = false;

function switchTab(tab) {
    document.querySelectorAll('.dg-tab-btn').forEach(btn => btn.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');

    if (tab === 'designations') {
        document.getElementById('section-designations').style.display = 'block';
        document.getElementById('section-departments').style.display = 'none';
        document.getElementById('addDesgBtn').style.display = 'block';
        document.getElementById('addDeptBtn').style.display = 'none';
    } else {
        document.getElementById('section-designations').style.display = 'none';
        document.getElementById('section-departments').style.display = 'block';
        document.getElementById('addDesgBtn').style.display = 'none';
        document.getElementById('addDeptBtn').style.display = 'block';
    }
}

function openCreatePanel() {
    isEditMode = false;
    document.getElementById('desgId').value = '';
    document.getElementById('desgName').value = '';
    document.getElementById('desgDept').value = '';
    document.getElementById('desgDesc').value = '';
    document.getElementById('desgRole').value = 'none';

    document.getElementById('panelTitle').textContent = 'Add Designation';
    document.getElementById('panelSubtitle').textContent = 'Define a new designation & privilege mapping';

    document.getElementById('panelBackdrop').classList.add('open');
    document.getElementById('designationPanel').classList.add('open');
}

function openEditPanel(btnEl) {
    isEditMode = true;
    const id = btnEl.getAttribute('data-id');
    const name = btnEl.getAttribute('data-name');
    const dept = btnEl.getAttribute('data-dept');
    const desc = btnEl.getAttribute('data-desc');
    const role = btnEl.getAttribute('data-role');

    document.getElementById('desgId').value = id;
    document.getElementById('desgName').value = name;
    document.getElementById('desgDept').value = dept === 'null' || !dept ? '' : dept;
    document.getElementById('desgDesc').value = desc === 'null' || !desc ? '' : desc;
    document.getElementById('desgRole').value = role;

    document.getElementById('panelTitle').textContent = 'Edit Designation';
    document.getElementById('panelSubtitle').textContent = `Modify attributes for designation: ${name}`;

    document.getElementById('panelBackdrop').classList.add('open');
    document.getElementById('designationPanel').classList.add('open');
}

function closePanel() {
    document.getElementById('panelBackdrop').classList.remove('open');
    document.getElementById('designationPanel').classList.remove('open');
}

function saveDesignation() {
    const name = document.getElementById('desgName').value.trim();
    const dept = document.getElementById('desgDept').value;
    const desc = document.getElementById('desgDesc').value.trim();
    const role = document.getElementById('desgRole').value;
    const id = document.getElementById('desgId').value;

    if (!name) {
        showToast('Please enter a designation name.');
        return;
    }
    if (!dept) {
        showToast('Please select a department.');
        return;
    }

    const btn = document.getElementById('panelSaveBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

    const url = isEditMode
        ? `{{ url('school/role-management/designations') }}/${id}/update`
        : `{{ route('school.designations.store') }}`;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            name: name,
            department_id: dept,
            description: desc,
            system_role: role
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message);
            closePanel();
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showToast(data.error || 'Failed to save designation.');
        }
    })
    .catch(err => {
        showToast('Error saving designation. Please try again.');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check"></i> Save Designation';
    });
}

function deleteDesignation(id, name) {
    if (!confirm(`Are you sure you want to delete the designation "${name}"?`)) {
        return;
    }

    fetch(`{{ url('school/role-management/designations') }}/${id}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message);
            const row = document.getElementById('desg-row-' + id);
            if (row) row.remove();
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            alert(data.error || 'Failed to delete designation.');
        }
    })
    .catch(err => {
        showToast('Error deleting designation.');
    });
}

// Department Functions
function openCreateDeptPanel() {
    isDeptEditMode = false;
    document.getElementById('deptId').value = '';
    document.getElementById('deptName').value = '';
    document.getElementById('deptDesc').value = '';

    document.getElementById('deptPanelTitle').textContent = 'Add Department';
    document.getElementById('deptPanelSubtitle').textContent = 'Define a new staff department';

    document.getElementById('panelBackdrop').classList.add('open');
    document.getElementById('departmentPanel').classList.add('open');
}

function openEditDeptPanel(btnEl) {
    isDeptEditMode = true;
    const id = btnEl.getAttribute('data-id');
    const name = btnEl.getAttribute('data-name');
    const desc = btnEl.getAttribute('data-desc');

    document.getElementById('deptId').value = id;
    document.getElementById('deptName').value = name;
    document.getElementById('deptDesc').value = desc === 'null' || !desc ? '' : desc;

    document.getElementById('deptPanelTitle').textContent = 'Edit Department';
    document.getElementById('deptPanelSubtitle').textContent = `Modify attributes for department: ${name}`;

    document.getElementById('panelBackdrop').classList.add('open');
    document.getElementById('departmentPanel').classList.add('open');
}

function closeDeptPanel() {
    document.getElementById('panelBackdrop').classList.remove('open');
    document.getElementById('departmentPanel').classList.remove('open');
}

function saveDepartment() {
    const name = document.getElementById('deptName').value.trim();
    const desc = document.getElementById('deptDesc').value.trim();
    const id = document.getElementById('deptId').value;

    if (!name) {
        showToast('Please enter a department name.');
        return;
    }

    const btn = document.getElementById('deptPanelSaveBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

    const url = isDeptEditMode
        ? `{{ url('school/role-management/departments') }}/${id}/update`
        : `{{ route('school.departments.store') }}`;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            name: name,
            description: desc
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message);
            closeDeptPanel();
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showToast(data.error || 'Failed to save department.');
        }
    })
    .catch(err => {
        showToast('Error saving department. Please try again.');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check"></i> Save Department';
    });
}

function deleteDepartment(id, name) {
    if (!confirm(`Are you sure you want to delete the department "${name}"?`)) {
        return;
    }

    fetch(`{{ url('school/role-management/departments') }}/${id}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message);
            const row = document.getElementById('dept-row-' + id);
            if (row) row.remove();
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            alert(data.error || 'Failed to delete department.');
        }
    })
    .catch(err => {
        showToast('Error deleting department.');
    });
}

function showToast(msg) {
    const toast = document.getElementById('appToast') || document.createElement('div');
    if (!toast.id) {
        toast.id = 'appToast';
        document.body.appendChild(toast);
    }
    toast.textContent = msg;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3000);
}
</script>
@endsection
