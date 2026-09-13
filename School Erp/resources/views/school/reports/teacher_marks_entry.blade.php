@extends('layouts.app')

@section('title', 'Teacher Marks Entry Report')
@section('page-title', 'Teacher Marks Entry Report')

@section('styles')
<style>
    :root {
        --tme-primary: #4f46e5;
        --tme-primary-hover: #4338ca;
        --tme-primary-light: #eef2ff;
        --tme-border: #e2e8f0;
        --tme-text: #1e293b;
        --tme-muted: #64748b;
        --tme-card-bg: #ffffff;
        --tme-radius: 12px;
        --tme-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        --tme-shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.08);
    }

    body.dark-mode {
        --tme-primary: #6366f1;
        --tme-primary-hover: #818cf8;
        --tme-primary-light: rgba(99, 102, 241, 0.15);
        --tme-border: #334155;
        --tme-text: #f8fafc;
        --tme-muted: #94a3b8;
        --tme-card-bg: #1e293b;
    }

    .tme-wrapper {
        max-width: 100%;
        margin: 0 auto;
        padding: 4px 16px 36px 16px;
    }

    /* ─── PAGE HEADER & TOOLBAR ─── */
    .tme-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--tme-border);
    }
    .tme-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--tme-primary);
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        margin-bottom: 6px;
        transition: transform 0.2s;
    }
    .tme-back-btn:hover {
        transform: translateX(-3px);
        color: var(--tme-primary-hover);
        text-decoration: none;
    }
    .tme-title-area h1 {
        font-size: 22px;
        font-weight: 800;
        color: var(--tme-text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
        letter-spacing: -0.3px;
    }
    .tme-exam-badge {
        font-size: 12px;
        font-weight: 700;
        background: var(--tme-primary-light);
        color: var(--tme-primary);
        padding: 3px 10px;
        border-radius: 20px;
        border: 1px solid rgba(79, 70, 229, 0.2);
    }
    .tme-title-area p {
        font-size: 13px;
        color: var(--tme-muted);
        margin: 4px 0 0 0;
    }

    /* Action Toolbar */
    .tme-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .btn-tme {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 16px;
        border-radius: 9px;
        font-size: 12.5px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s;
        border: none;
        line-height: 1.2;
    }
    .btn-tme-excel {
        background: #059669;
        color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(5, 150, 105, 0.25);
    }
    .btn-tme-excel:hover {
        background: #047857;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.35);
    }
    .btn-tme-pdf {
        background: #dc2626;
        color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(220, 38, 38, 0.25);
    }
    .btn-tme-pdf:hover {
        background: #b91c1c;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.35);
    }
    .btn-tme-outline {
        background: var(--tme-card-bg);
        border: 1px solid var(--tme-border);
        color: var(--tme-text) !important;
    }
    .btn-tme-outline:hover {
        background: var(--tme-primary-light);
        border-color: var(--tme-primary);
        color: var(--tme-primary) !important;
    }

    /* ─── FILTER CARD ─── */
    .tme-filter-card {
        background: var(--tme-card-bg);
        border: 1px solid var(--tme-border);
        border-radius: var(--tme-radius);
        padding: 16px 18px;
        margin-bottom: 22px;
        box-shadow: var(--tme-shadow);
    }
    .tme-filter-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px 14px;
        align-items: flex-end;
    }
    @media (max-width: 1200px) {
        .tme-filter-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    @media (max-width: 860px) {
        .tme-filter-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 580px) {
        .tme-filter-grid {
            grid-template-columns: 1fr;
        }
    }
    .tme-filter-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .tme-filter-label {
        font-size: 11px;
        font-weight: 700;
        color: var(--tme-muted);
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .tme-filter-label i {
        color: var(--tme-primary);
        font-size: 10.5px;
    }
    .tme-filter-select, .tme-filter-input {
        width: 100%;
        height: 38px;
        border: 1px solid var(--tme-border);
        border-radius: 8px;
        padding: 0 10px;
        font-size: 13px;
        font-weight: 500;
        background: #f8fafc;
        color: var(--tme-text);
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    body.dark-mode .tme-filter-select, body.dark-mode .tme-filter-input {
        background: #0f172a;
    }
    .tme-filter-select:focus, .tme-filter-input:focus {
        border-color: var(--tme-primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        background: var(--tme-card-bg);
    }
    .tme-btn-apply {
        height: 38px;
        padding: 0 16px;
        background: var(--tme-primary);
        color: #ffffff;
        border: none;
        border-radius: 8px;
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
        white-space: nowrap;
    }
    .tme-btn-apply:hover {
        background: var(--tme-primary-hover);
        transform: translateY(-1px);
        box-shadow: 0 3px 10px rgba(79, 70, 229, 0.25);
    }
    .tme-btn-reset {
        height: 38px;
        padding: 0 12px;
        background: #f1f5f9;
        border: 1px solid var(--tme-border);
        border-radius: 8px;
        color: var(--tme-muted);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s;
    }
    .tme-btn-reset:hover {
        background: #e2e8f0;
        color: #dc2626;
        text-decoration: none;
    }

    /* ─── 6-CARD KPI GRID ─── */
    .tme-kpi-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 12px;
        margin-bottom: 22px;
    }
    @media (max-width: 1200px) {
        .tme-kpi-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    @media (max-width: 640px) {
        .tme-kpi-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    .tme-kpi-card {
        background: var(--tme-card-bg);
        border: 1px solid var(--tme-border);
        border-radius: 10px;
        padding: 14px 16px;
        box-shadow: var(--tme-shadow);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .tme-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--tme-shadow-lg);
    }
    .tme-kpi-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 3.5px;
        height: 100%;
    }
    .kpi-border-indigo::before { background: #4f46e5; }
    .kpi-border-emerald::before { background: #10b981; }
    .kpi-border-amber::before { background: #f59e0b; }
    .kpi-border-rose::before { background: #ef4444; }
    .kpi-border-blue::before { background: #3b82f6; }
    .kpi-border-purple::before { background: #8b5cf6; }

    .tme-kpi-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }
    .tme-kpi-label {
        font-size: 11px;
        font-weight: 700;
        color: var(--tme-muted);
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    .tme-kpi-icon {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        flex-shrink: 0;
    }
    .tme-kpi-val {
        font-size: 22px;
        font-weight: 800;
        color: var(--tme-text);
        line-height: 1.1;
        margin-bottom: 4px;
    }
    .tme-kpi-sub {
        font-size: 11.5px;
        color: var(--tme-muted);
    }

    /* ─── SEGMENTED TABS ─── */
    .tme-tabs-wrap {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 16px;
    }
    .tme-tabs {
        display: inline-flex;
        background: #f1f5f9;
        padding: 4px;
        border-radius: 10px;
        border: 1px solid var(--tme-border);
    }
    body.dark-mode .tme-tabs {
        background: #0f172a;
    }
    .tme-tab-btn {
        padding: 8px 18px;
        border-radius: 7px;
        font-size: 13px;
        font-weight: 700;
        border: none;
        background: transparent;
        color: var(--tme-muted);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        transition: all 0.2s;
    }
    .tme-tab-btn:hover {
        color: var(--tme-primary);
    }
    .tme-tab-btn.active {
        background: var(--tme-card-bg);
        color: var(--tme-primary);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }
    .tme-tab-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 10px;
        background: #e2e8f0;
        color: #475569;
    }
    .tme-tab-btn.active .tme-tab-badge {
        background: var(--tme-primary-light);
        color: var(--tme-primary);
    }

    /* ─── DATA TABLES ─── */
    .tme-table-box {
        background: var(--tme-card-bg);
        border: 1px solid var(--tme-border);
        border-radius: var(--tme-radius);
        overflow: hidden;
        box-shadow: var(--tme-shadow);
    }
    .tme-table-hdr {
        padding: 12px 18px;
        background: #f8fafc;
        border-bottom: 1px solid var(--tme-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }
    body.dark-mode .tme-table-hdr {
        background: #0f172a;
    }
    .tme-table-title {
        font-size: 14px;
        font-weight: 800;
        color: var(--tme-text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .tme-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
        color: var(--tme-text);
    }
    .tme-table th {
        background: #f8fafc;
        color: #475569;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 11px 14px;
        border-bottom: 1.5px solid var(--tme-border);
        text-align: left;
        white-space: nowrap;
    }
    body.dark-mode .tme-table th {
        background: #0f172a;
        color: #94a3b8;
    }
    .tme-table td {
        padding: 11px 14px;
        border-bottom: 1px solid var(--tme-border);
        vertical-align: middle;
    }
    .tme-table tbody tr:hover td {
        background: rgba(79, 70, 229, 0.025);
    }
    .tme-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Badges & Status */
    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        white-space: nowrap;
    }
    .status-completed {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }
    .status-in_progress {
        background: #fffbeb;
        color: #d97706;
        border: 1px solid #fde68a;
    }
    .status-pending {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    .badge-role {
        display: inline-block;
        padding: 2px 7px;
        border-radius: 5px;
        font-size: 10.5px;
        font-weight: 600;
        background: #e0e7ff;
        color: #4338ca;
        white-space: nowrap;
    }
    .badge-role.ct {
        background: #fdf2f8;
        color: #be185d;
    }

    /* Progress bar */
    .tme-progress-bar {
        width: 100%;
        height: 6px;
        background: #e2e8f0;
        border-radius: 3px;
        overflow: hidden;
        margin-top: 4px;
    }
    .tme-progress-fill {
        height: 100%;
        border-radius: 3px;
    }
    .fill-green { background: #10b981; }
    .fill-amber { background: #f59e0b; }
    .fill-red   { background: #ef4444; }

    /* Teacher Avatar */
    .tme-avatar-sm {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 12.5px;
        flex-shrink: 0;
    }

    /* Modal */
    .tme-modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }
    .tme-modal-backdrop.open {
        display: flex;
    }
    .tme-modal-dialog {
        background: var(--tme-card-bg);
        border-radius: 16px;
        width: 100%;
        max-width: 950px;
        max-height: 88vh;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    .tme-modal-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--tme-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f8fafc;
    }
    body.dark-mode .tme-modal-header {
        background: #0f172a;
    }
    .tme-modal-body {
        padding: 16px 20px;
        overflow-y: auto;
    }
    .tme-modal-footer {
        padding: 12px 20px;
        border-top: 1px solid var(--tme-border);
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        background: #f8fafc;
    }
    body.dark-mode .tme-modal-footer {
        background: #0f172a;
    }
</style>
@endsection

@section('content')
<div class="tme-wrapper">

    {{-- ── 1. HEADER TOOLBAR ── --}}
    <div class="tme-header">
        <div class="tme-title-area">
            <a href="{{ route('school.reports.index') }}" class="tme-back-btn no-print">
                <i class="fas fa-arrow-left"></i> Back to All Reports
            </a>
            <h1>
                <i class="fas fa-clipboard-check" style="color: var(--tme-primary);"></i>
                Teacher Marks Entry Report
                @if($selectedExam)
                    <span class="tme-exam-badge"><i class="fas fa-bookmark"></i> {{ $selectedExam }}</span>
                @endif
                @if(!empty($selectedAssessment))
                    <span class="tme-exam-badge" style="background: #ecfdf5; color: #047857; border-color: rgba(4, 120, 87, 0.25);"><i class="fas fa-tasks"></i> {{ $selectedAssessment }}</span>
                @endif
            </h1>
            <p>Monitor marks entry progress by Class Teachers and Subject Teachers across all classes and subjects.</p>
        </div>
        <div class="tme-actions no-print">
            <span style="font-size: 12px; color: var(--tme-muted); font-weight: 600; margin-right: 4px;">
                <i class="fas fa-clock"></i> <span id="clockDisplay">{{ now()->format('d M Y, h:i A') }}</span>
            </span>

            {{-- Export Excel --}}
            <a href="{{ route('school.reports.teacher-marks-entry.export-excel', request()->all()) }}" 
               onclick="event.preventDefault(); exportReport('excel')"
               class="btn-tme btn-tme-excel" 
               title="Download Multi-Sheet Excel Workbook">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>

            {{-- Export PDF --}}
            <a href="{{ route('school.reports.teacher-marks-entry.export-pdf', request()->all()) }}" 
               onclick="event.preventDefault(); exportReport('pdf')"
               target="_blank" 
               class="btn-tme btn-tme-pdf" 
               title="Download Landscape PDF Document">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>

            {{-- Print --}}
            <button type="button" class="btn-tme btn-tme-outline" onclick="window.print()" title="Print this view">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>

    {{-- ── 2. BALANCED FILTER CARD ── --}}
    <div class="tme-filter-card no-print">
        <form method="GET" action="{{ route('school.reports.teacher-marks-entry') }}" id="filterForm">
            <div class="tme-filter-grid">

                {{-- Exam --}}
                <div class="tme-filter-item">
                    <label class="tme-filter-label"><i class="fas fa-file-alt"></i> Examination</label>
                    <select name="exam_name" class="tme-filter-select" onchange="const aSel = this.form.querySelector('select[name=assessment_name]'); if(aSel) aSel.value = ''; this.form.submit()">
                        <option value="">-- All Exams --</option>
                        @foreach($examList as $ex)
                            <option value="{{ $ex }}" {{ $selectedExam == $ex ? 'selected' : '' }}>{{ $ex }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Assessment Filter --}}
                <div class="tme-filter-item">
                    <label class="tme-filter-label"><i class="fas fa-tasks"></i> Assessment</label>
                    <select name="assessment_name" class="tme-filter-select" onchange="this.form.submit()">
                        <option value="">-- All Assessments --</option>
                        @foreach($assessmentList as $ass)
                            <option value="{{ $ass }}" {{ ($selectedAssessment ?? '') == $ass ? 'selected' : '' }}>{{ $ass }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Teacher --}}
                <div class="tme-filter-item">
                    <label class="tme-filter-label"><i class="fas fa-chalkboard-teacher"></i> Teacher</label>
                    <select name="teacher_id" class="tme-filter-select" onchange="this.form.submit()">
                        <option value="">-- All Teachers --</option>
                        @foreach($teachers as $t)
                            @php
                                $tName = trim(($t->first_name ?? '') . ' ' . ($t->last_name ?? ''));
                                if (empty($tName) && $t->user) $tName = $t->user->name;
                            @endphp
                            <option value="{{ $t->id }}" {{ $teacherId == $t->id ? 'selected' : '' }}>
                                {{ $tName ?: ('Teacher #' . $t->id) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Teacher Role --}}
                <div class="tme-filter-item">
                    <label class="tme-filter-label"><i class="fas fa-user-tag"></i> Teacher Role</label>
                    <select name="role" class="tme-filter-select" onchange="this.form.submit()">
                        <option value="all" {{ $roleFilter == 'all' ? 'selected' : '' }}>All Roles</option>
                        <option value="subject_teacher" {{ $roleFilter == 'subject_teacher' ? 'selected' : '' }}>Subject Teacher</option>
                        <option value="class_teacher" {{ $roleFilter == 'class_teacher' ? 'selected' : '' }}>Class Teacher</option>
                    </select>
                </div>

                {{-- Class --}}
                <div class="tme-filter-item">
                    <label class="tme-filter-label"><i class="fas fa-graduation-cap"></i> Class</label>
                    <select name="class_id" class="tme-filter-select" onchange="const sSel = this.form.querySelector('select[name=section_id]'); if(sSel) sSel.value = ''; const subSel = this.form.querySelector('select[name=subject_id]'); if(subSel) subSel.value = ''; this.form.submit()">
                        <option value="">-- All Classes --</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Section --}}
                <div class="tme-filter-item">
                    <label class="tme-filter-label"><i class="fas fa-layer-group"></i> Section</label>
                    <select name="section_id" class="tme-filter-select" onchange="this.form.submit()">
                        <option value="">-- All Sections --</option>
                        @foreach($sections as $sec)
                            <option value="{{ $sec->id }}" {{ $sectionId == $sec->id ? 'selected' : '' }}>{{ $sec->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Subject --}}
                <div class="tme-filter-item">
                    <label class="tme-filter-label"><i class="fas fa-book"></i> Subject</label>
                    <select name="subject_id" class="tme-filter-select" onchange="this.form.submit()">
                        <option value="">-- All Subjects --</option>
                        @foreach($subjects as $sub)
                            <option value="{{ $sub->id }}" {{ $subjectId == $sub->id ? 'selected' : '' }}>{{ $sub->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Entry Status --}}
                <div class="tme-filter-item">
                    <label class="tme-filter-label"><i class="fas fa-toggle-on"></i> Entry Status</label>
                    <select name="status" class="tme-filter-select" onchange="this.form.submit()">
                        <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="completed" {{ $statusFilter == 'completed' ? 'selected' : '' }}>Completed (100%)</option>
                        <option value="in_progress" {{ $statusFilter == 'in_progress' ? 'selected' : '' }}>In Progress (1-99%)</option>
                        <option value="pending" {{ $statusFilter == 'pending' ? 'selected' : '' }}>Not Started (0%)</option>
                    </select>
                </div>

                {{-- Search & Actions (In-line) --}}
                <div class="tme-filter-item">
                    <label class="tme-filter-label"><i class="fas fa-search"></i> Search Keyword</label>
                    <div style="display: flex; gap: 6px;">
                        <input type="text" name="search" value="{{ $search }}" class="tme-filter-input" placeholder="Teacher, Student...">
                        <button type="submit" class="tme-btn-apply" title="Apply Filter">
                            <i class="fas fa-filter"></i> Apply
                        </button>
                        @if($classId || $sectionId || $subjectId || $teacherId || $statusFilter !== 'all' || $roleFilter !== 'all' || !empty($selectedAssessment) || $search)
                            <a href="{{ route('school.reports.teacher-marks-entry', ['exam_name' => $selectedExam]) }}" class="tme-btn-reset" title="Reset All Filters">
                                <i class="fas fa-undo"></i>
                            </a>
                        @endif
                    </div>
                </div>

            </div>
        </form>
    </div>

    {{-- ── 3. CRISP 6-CARD BALANCED KPI GRID ── --}}
    <div class="tme-kpi-grid">

        {{-- 1. Total Allocations --}}
        <div class="tme-kpi-card kpi-border-indigo">
            <div class="tme-kpi-top">
                <span class="tme-kpi-label">Allocations</span>
                <div class="tme-kpi-icon" style="background: #e0e7ff; color: #4338ca;">
                    <i class="fas fa-th-large"></i>
                </div>
            </div>
            <div>
                <div class="tme-kpi-val">{{ number_format($kpis['total_allocations']) }}</div>
                <div class="tme-kpi-sub">Teaching batches</div>
            </div>
        </div>

        {{-- 2. Completed --}}
        <div class="tme-kpi-card kpi-border-emerald">
            <div class="tme-kpi-top">
                <span class="tme-kpi-label">Completed</span>
                <div class="tme-kpi-icon" style="background: #d1fae5; color: #059669;">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div>
                <div class="tme-kpi-val" style="color: #059669;">{{ number_format($kpis['completed_allocations']) }}</div>
                <div class="tme-kpi-sub">100% submitted</div>
            </div>
        </div>

        {{-- 3. In Progress --}}
        <div class="tme-kpi-card kpi-border-amber">
            <div class="tme-kpi-top">
                <span class="tme-kpi-label">In Progress</span>
                <div class="tme-kpi-icon" style="background: #fef3c7; color: #d97706;">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div>
                <div class="tme-kpi-val" style="color: #d97706;">{{ number_format($kpis['in_progress_allocations']) }}</div>
                <div class="tme-kpi-sub">Partially entered</div>
            </div>
        </div>

        {{-- 4. Not Started / Pending --}}
        <div class="tme-kpi-card kpi-border-rose">
            <div class="tme-kpi-top">
                <span class="tme-kpi-label">Not Started</span>
                <div class="tme-kpi-icon" style="background: #fee2e2; color: #dc2626;">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
            </div>
            <div>
                <div class="tme-kpi-val" style="color: #dc2626;">{{ number_format($kpis['pending_allocations']) }}</div>
                <div class="tme-kpi-sub">0 marks entered</div>
            </div>
        </div>

        {{-- 5. Marks Submitted vs Enrolled --}}
        <div class="tme-kpi-card kpi-border-blue">
            <div class="tme-kpi-top">
                <span class="tme-kpi-label">Submitted</span>
                <div class="tme-kpi-icon" style="background: #dbeafe; color: #1d4ed8;">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
            <div>
                <div class="tme-kpi-val">
                    {{ number_format($kpis['total_entered']) }}
                    <span style="font-size: 13px; font-weight: 500; color: var(--tme-muted);">/ {{ number_format($kpis['total_students']) }}</span>
                </div>
                <div class="tme-progress-bar">
                    <div class="tme-progress-fill {{ $kpis['completion_rate'] >= 100 ? 'fill-green' : ($kpis['completion_rate'] > 0 ? 'fill-amber' : 'fill-red') }}" 
                         style="width: {{ min(100, $kpis['completion_rate']) }}%;"></div>
                </div>
                <div class="tme-kpi-sub" style="margin-top: 4px;">{{ $kpis['completion_rate'] }}% overall rate</div>
            </div>
        </div>

        {{-- 6. Overall Average Score --}}
        <div class="tme-kpi-card kpi-border-purple">
            <div class="tme-kpi-top">
                <span class="tme-kpi-label">Overall Average</span>
                <div class="tme-kpi-icon" style="background: #f3e8ff; color: #7e22ce;">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <div>
                <div class="tme-kpi-val" style="color: #7e22ce;">{{ $kpis['overall_avg_marks'] }}</div>
                <div class="tme-kpi-sub">Across entered marks</div>
            </div>
        </div>

    </div>

    {{-- ── 4. SEGMENTED TABS ── --}}
    <div class="tme-tabs-wrap no-print">
        <div class="tme-tabs">
            <button type="button" class="tme-tab-btn active" id="btnTabSummary" onclick="switchView('summary')">
                <i class="fas fa-chalkboard-teacher"></i> Teacher Summary
                <span class="tme-tab-badge">{{ $allocations->count() }}</span>
            </button>
            <button type="button" class="tme-tab-btn" id="btnTabDetails" onclick="switchView('details')">
                <i class="fas fa-list-ol"></i> Student Marks Ledger
                <span class="tme-tab-badge">{{ $studentRows->count() }}</span>
            </button>
        </div>
        <div style="font-size: 12.5px; color: var(--tme-muted); font-weight: 600;">
            Session: <strong>{{ $sessionName }}</strong>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════
         VIEW 1: TEACHER MARKS ENTRY SUMMARY
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div id="viewSummary" class="tme-table-box">
        <div class="tme-table-hdr">
            <h3 class="tme-table-title">
                <i class="fas fa-tasks" style="color: var(--tme-primary);"></i>
                Teacher Marks Entry Progress
            </h3>
            <span style="font-size: 12px; color: var(--tme-muted); font-weight: 600;">
                Showing {{ $allocations->count() }} teaching units
            </span>
        </div>

        <div style="overflow-x: auto;">
            <table class="tme-table">
                <thead>
                    <tr>
                        <th style="width: 38px; text-align: center;">#</th>
                        <th>Teacher Name & Role</th>
                        <th>Class & Sec</th>
                        <th>Subject</th>
                        <th style="text-align: center; width: 65px;">Total</th>
                        <th style="text-align: center; width: 65px;">Entered</th>
                        <th style="text-align: center; width: 65px;">Pending</th>
                        <th style="min-width: 120px;">Progress</th>
                        <th style="text-align: center; width: 100px;">Status</th>
                        <th style="text-align: right; width: 100px;">Avg Score</th>
                        <th style="text-align: center; width: 95px;" class="no-print">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allocations as $idx => $alloc)
                        @php
                            $statusClass = 'status-' . $alloc['status'];
                            $fillClass = $alloc['status'] === 'completed' ? 'fill-green' : ($alloc['status'] === 'in_progress' ? 'fill-amber' : 'fill-red');
                            $statusLabel = $alloc['status'] === 'completed' ? 'Completed' : ($alloc['status'] === 'in_progress' ? 'In Progress' : 'Pending');
                            $firstLetter = strtoupper(substr($alloc['teacher_name'], 0, 1));
                        @endphp
                        <tr>
                            <td style="text-align: center; color: var(--tme-muted); font-weight: 700;">{{ $idx + 1 }}</td>
                            
                            {{-- Teacher --}}
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div class="tme-avatar-sm">{{ $firstLetter }}</div>
                                    <div>
                                        <div style="font-weight: 700; color: var(--tme-text);">{{ $alloc['teacher_name'] }}</div>
                                        <div style="display: flex; gap: 4px; margin-top: 2px; align-items: center;">
                                            <span class="badge-role {{ str_contains($alloc['teacher_role'], 'Class') ? 'ct' : '' }}">
                                                {{ $alloc['teacher_role'] }}
                                            </span>
                                            @if(!empty($alloc['class_teacher_name']) && $alloc['class_teacher_name'] !== $alloc['teacher_name'])
                                                <span style="font-size: 10.5px; color: var(--tme-muted);">(CT: {{ $alloc['class_teacher_name'] }})</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Class & Sec --}}
                            <td>
                                <span style="font-weight: 700; color: var(--tme-text);">{{ $alloc['class_name'] }}</span>
                                <span style="font-size: 11px; color: var(--tme-muted); background: #f1f5f9; padding: 2px 6px; border-radius: 4px; margin-left: 3px;">
                                    Sec: {{ $alloc['section_name'] }}
                                </span>
                            </td>

                            {{-- Subject --}}
                            <td>
                                <span style="font-weight: 600;">{{ $alloc['subject_name'] }}</span>
                                @if(!empty($alloc['subject_code']))
                                    <span style="font-size: 10.5px; color: var(--tme-muted); font-weight: normal;">({{ $alloc['subject_code'] }})</span>
                                @endif
                            </td>

                            {{-- Total Students --}}
                            <td style="text-align: center; font-weight: 700;">
                                {{ $alloc['total_students'] }}
                            </td>

                            {{-- Entered --}}
                            <td style="text-align: center; font-weight: 800; color: {{ $alloc['entered_count'] > 0 ? '#059669' : 'var(--tme-muted)' }};">
                                {{ $alloc['entered_count'] }}
                            </td>

                            {{-- Pending --}}
                            <td style="text-align: center; font-weight: 800; color: {{ $alloc['pending_count'] > 0 ? '#dc2626' : 'var(--tme-muted)' }};">
                                {{ $alloc['pending_count'] }}
                            </td>

                            {{-- Progress --}}
                            <td>
                                <div style="display: flex; justify-content: space-between; font-size: 11px; font-weight: 700; color: var(--tme-muted);">
                                    <span>{{ $alloc['completion_pct'] }}%</span>
                                    <span>{{ $alloc['entered_count'] }}/{{ $alloc['total_students'] }}</span>
                                </div>
                                <div class="tme-progress-bar">
                                    <div class="tme-progress-fill {{ $fillClass }}" style="width: {{ min(100, $alloc['completion_pct']) }}%;"></div>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td style="text-align: center;">
                                <span class="badge-status {{ $statusClass }}">
                                    @if($alloc['status'] === 'completed')
                                        <i class="fas fa-check"></i>
                                    @elseif($alloc['status'] === 'in_progress')
                                        <i class="fas fa-spinner fa-spin"></i>
                                    @else
                                        <i class="fas fa-clock"></i>
                                    @endif
                                    {{ $statusLabel }}
                                </span>
                            </td>

                            {{-- Avg Score --}}
                            <td style="text-align: right;">
                                @if($alloc['entered_count'] > 0)
                                    <div style="font-weight: 700; color: var(--tme-primary);">
                                        {{ $alloc['avg_marks'] }}
                                    </div>
                                    <div style="font-size: 10px; color: var(--tme-muted);">
                                        / {{ $alloc['max_marks_total'] }}
                                    </div>
                                @else
                                    <span style="color: #94a3b8; font-size: 11.5px; font-style: italic;">—</span>
                                @endif
                            </td>

                            {{-- Action --}}
                            <td style="text-align: center;" class="no-print">
                                <button type="button" 
                                        class="btn-tme btn-tme-outline" 
                                        style="padding: 5px 10px; font-size: 11.5px;"
                                        onclick="openStudentMarksModal('{{ $alloc['key'] }}', '{{ addslashes($alloc['teacher_name']) }}', '{{ addslashes($alloc['class_name'] . ' - ' . $alloc['section_name']) }}', '{{ addslashes($alloc['subject_name']) }}')">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" style="text-align: center; padding: 40px 16px; color: var(--tme-muted);">
                                <i class="fas fa-inbox" style="font-size: 36px; color: #cbd5e1; margin-bottom: 8px;"></i>
                                <div style="font-size: 15px; font-weight: 700;">No Marks Entry Records Found</div>
                                <div style="font-size: 12.5px;">Try selecting another examination or resetting your filters.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════
         VIEW 2: STUDENT MARKS LEDGER
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div id="viewDetails" class="tme-table-box" style="display: none;">
        <div class="tme-table-hdr">
            <h3 class="tme-table-title">
                <i class="fas fa-user-graduate" style="color: #059669;"></i>
                Student Marks Ledger
            </h3>
            <span style="font-size: 12px; color: var(--tme-muted); font-weight: 600;">
                Total: {{ $studentRows->count() }} Student Marks Entries
            </span>
        </div>

        <div style="overflow-x: auto;">
            <table class="tme-table">
                <thead>
                    <tr>
                        <th style="width: 38px; text-align: center;">#</th>
                        <th style="width: 70px;">Roll No</th>
                        <th style="width: 80px;">Adm No</th>
                        <th>Student Name</th>
                        <th>Class & Sec</th>
                        <th>Subject</th>
                        <th>Exam & Assessment</th>
                        <th>Assigned Teacher</th>
                        <th style="text-align: right; width: 90px;">Marks</th>
                        <th style="text-align: right; width: 70px;">Max</th>
                        <th style="text-align: center; width: 65px;">%</th>
                        <th style="text-align: center; width: 60px;">Grade</th>
                        <th style="text-align: center; width: 80px;">Attendance</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($studentRows as $sIdx => $st)
                        <tr>
                            <td style="text-align: center; color: var(--tme-muted); font-weight: 700;">{{ $sIdx + 1 }}</td>
                            <td style="font-weight: 700; color: var(--tme-primary);">{{ $st['roll_no'] }}</td>
                            <td style="color: var(--tme-muted);">{{ $st['admission_no'] }}</td>
                            <td style="font-weight: 700;">{{ $st['student_name'] }}</td>
                            <td>{{ $st['class_name'] }} - {{ $st['section_name'] }}</td>
                            <td style="font-weight: 600;">{{ $st['subject_name'] }}</td>
                            <td>
                                <div style="font-weight: 700; color: var(--tme-text); font-size: 12px;">{{ $st['exam_name'] ?? ($selectedExam ?: 'All Exams') }}</div>
                                <div style="font-size: 10.5px; color: #059669; font-weight: 600; margin-top: 2px;">
                                    <i class="fas fa-tasks" style="font-size: 9.5px;"></i> {{ $st['assessment_name'] ?: ($selectedAssessment ?: 'All Assessments') }}
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600; font-size: 12px;">{{ $st['teacher_name'] }}</div>
                                <span style="font-size: 10px; color: var(--tme-muted);">{{ $st['teacher_role'] }}</span>
                            </td>

                            {{-- Marks Obtained --}}
                            <td style="text-align: right; font-weight: 800;">
                                @if($st['is_entered'])
                                    <span style="color: #059669; font-size: 13.5px;">{{ number_format($st['marks_obtained'], 2) }}</span>
                                @else
                                    <span style="color: #dc2626; font-size: 11.5px; font-style: italic;">Pending</span>
                                @endif
                            </td>

                            {{-- Max Marks --}}
                            <td style="text-align: right; color: var(--tme-muted); font-weight: 600;">
                                {{ number_format($st['max_marks'], 0) }}
                            </td>

                            {{-- Percentage --}}
                            <td style="text-align: center; font-weight: 700;">
                                @if($st['is_entered'] && $st['percentage'] !== null)
                                    <span style="color: {{ $st['percentage'] >= 75 ? '#059669' : ($st['percentage'] >= 40 ? '#d97706' : '#dc2626') }};">
                                        {{ $st['percentage'] }}%
                                    </span>
                                @else
                                    <span style="color: #94a3b8;">—</span>
                                @endif
                            </td>

                            {{-- Grade --}}
                            <td style="text-align: center;">
                                @if($st['is_entered'] && $st['grade'] !== '—')
                                    <span style="background: #e0e7ff; color: #4338ca; font-weight: 800; padding: 2px 7px; border-radius: 4px; font-size: 11px;">
                                        {{ $st['grade'] }}
                                    </span>
                                @else
                                    <span style="color: #94a3b8;">—</span>
                                @endif
                            </td>

                            {{-- Attendance --}}
                            <td style="text-align: center;">
                                @if(strtolower($st['attendance_status']) === 'present')
                                    <span style="color: #059669; font-weight: 700; font-size: 11.5px;">
                                        <i class="fas fa-check"></i> Present
                                    </span>
                                @elseif(strtolower($st['attendance_status']) === 'absent')
                                    <span style="color: #dc2626; font-weight: 700; font-size: 11.5px;">
                                        <i class="fas fa-times"></i> Absent
                                    </span>
                                @else
                                    <span style="color: var(--tme-muted); font-size: 11px;">
                                        {{ ucfirst($st['attendance_status']) }}
                                    </span>
                                @endif
                            </td>

                            {{-- Remarks --}}
                            <td style="color: var(--tme-muted); font-size: 11.5px;">{{ $st['remarks'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" style="text-align: center; padding: 40px 16px; color: var(--tme-muted);">
                                <i class="fas fa-inbox" style="font-size: 36px; color: #cbd5e1; margin-bottom: 8px;"></i>
                                <div style="font-size: 15px; font-weight: 700;">No Student Records Found</div>
                                <div style="font-size: 12.5px;">No student marks match the applied filter criteria.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ── 5. QUICK VIEW STUDENT MARKS MODAL ── --}}
<div id="studentMarksModal" class="tme-modal-backdrop" onclick="closeStudentMarksModal(event)">
    <div class="tme-modal-dialog" onclick="event.stopPropagation()">
        <div class="tme-modal-header">
            <div>
                <h3 style="font-size: 16px; font-weight: 800; color: var(--tme-text); margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-graduation-cap" style="color: var(--tme-primary);"></i>
                    <span id="modalHeaderTitle">Student Marks Breakdown</span>
                </h3>
                <div id="modalHeaderSub" style="font-size: 12px; color: var(--tme-muted); margin-top: 2px;"></div>
            </div>
            <button type="button" class="tme-btn-reset" onclick="closeStudentMarksModalDirect()" style="border: none; background: transparent; font-size: 16px;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="tme-modal-body">
            <div style="overflow-x: auto;">
                <table class="tme-table" id="modalStudentTable">
                    <thead>
                        <tr>
                            <th style="width: 35px; text-align: center;">#</th>
                            <th>Roll No</th>
                            <th>Adm No</th>
                            <th>Student Name</th>
                            <th style="text-align: right;">Marks Obtained</th>
                            <th style="text-align: right;">Max Marks</th>
                            <th style="text-align: center;">%</th>
                            <th style="text-align: center;">Grade</th>
                            <th style="text-align: center;">Attendance</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="modalStudentTableBody">
                        {{-- Injected dynamically --}}
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tme-modal-footer">
            <button type="button" class="btn-tme btn-tme-outline" onclick="closeStudentMarksModalDirect()">
                Close
            </button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Live clock
function updateLiveClock() {
    const now = new Date();
    const el = document.getElementById('clockDisplay');
    if (el) el.textContent = now.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) + ', ' + now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
}
setInterval(updateLiveClock, 15000);

// Switch Views
function switchView(mode) {
    const viewSummary = document.getElementById('viewSummary');
    const viewDetails = document.getElementById('viewDetails');
    const btnSummary  = document.getElementById('btnTabSummary');
    const btnDetails  = document.getElementById('btnTabDetails');

    if (mode === 'details') {
        viewSummary.style.display = 'none';
        viewDetails.style.display = 'block';
        btnSummary.classList.remove('active');
        btnDetails.classList.add('active');
    } else {
        viewSummary.style.display = 'block';
        viewDetails.style.display = 'none';
        btnSummary.classList.add('active');
        btnDetails.classList.remove('active');
    }
}

// Student marks data store for modal
const allocationsStore = @json($allocations->keyBy('key'));

function openStudentMarksModal(key, teacherName, classSec, subjectName) {
    const alloc = allocationsStore[key];
    if (!alloc) return;

    document.getElementById('modalHeaderTitle').textContent = subjectName + ' (' + classSec + ')';
    document.getElementById('modalHeaderSub').textContent = 'Teacher: ' + teacherName + ' | Exam: ' + alloc.exam_name + ' | Total Students: ' + alloc.total_students + ' (Entered: ' + alloc.entered_count + ', Pending: ' + alloc.pending_count + ')';

    const tbody = document.getElementById('modalStudentTableBody');
    tbody.innerHTML = '';

    if (!alloc.students || alloc.students.length === 0) {
        tbody.innerHTML = '<tr><td colspan="10" style="text-align:center; padding: 24px; color:#94a3b8;">No student records found for this class.</td></tr>';
    } else {
        alloc.students.forEach((s, i) => {
            const tr = document.createElement('tr');
            const obtainedHtml = s.is_entered 
                ? '<strong style="color:#059669;">' + Number(s.marks_obtained).toFixed(2) + '</strong>'
                : '<span style="color:#dc2626; font-style:italic;">Pending</span>';
            const pctHtml = (s.is_entered && s.percentage !== null)
                ? '<span style="font-weight:700; color:' + (s.percentage >= 75 ? '#059669' : (s.percentage >= 40 ? '#d97706' : '#dc2626')) + ';">' + s.percentage + '%</span>'
                : '—';
            const gradeHtml = (s.is_entered && s.grade !== '—')
                ? '<span style="background:#e0e7ff; color:#4338ca; font-weight:800; padding:2px 7px; border-radius:4px; font-size:11px;">' + s.grade + '</span>'
                : '—';
            const attendHtml = (s.attendance_status.toLowerCase() === 'present')
                ? '<span style="color:#059669; font-weight:700;"><i class="fas fa-check"></i> Present</span>'
                : (s.attendance_status.toLowerCase() === 'absent' 
                    ? '<span style="color:#dc2626; font-weight:700;"><i class="fas fa-times"></i> Absent</span>'
                    : '<span style="color:#64748b;">' + s.attendance_status + '</span>');

            tr.innerHTML = `
                <td style="text-align:center; color:#64748b; font-weight:600;">${i + 1}</td>
                <td style="font-weight:700; color:#4f46e5;">${s.roll_no}</td>
                <td style="color:#64748b;">${s.admission_no}</td>
                <td style="font-weight:700; color:#1e293b;">${s.student_name}</td>
                <td style="text-align:right;">${obtainedHtml}</td>
                <td style="text-align:right; color:#64748b; font-weight:600;">${Number(s.max_marks).toFixed(0)}</td>
                <td style="text-align:center;">${pctHtml}</td>
                <td style="text-align:center;">${gradeHtml}</td>
                <td style="text-align:center;">${attendHtml}</td>
                <td style="color:#64748b; font-size:11.5px;">${s.remarks || '—'}</td>
            `;
            tbody.appendChild(tr);
        });
    }

    document.getElementById('studentMarksModal').classList.add('open');
}

function closeStudentMarksModal(e) {
    if (e.target.id === 'studentMarksModal') {
        document.getElementById('studentMarksModal').classList.remove('open');
    }
}
function closeStudentMarksModalDirect() {
    document.getElementById('studentMarksModal').classList.remove('open');
}

function exportReport(type) {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams();
    for (const [key, value] of formData.entries()) {
        if (value !== '' && value !== null) {
            params.append(key, value);
        }
    }
    const baseUrl = type === 'excel' 
        ? "{{ route('school.reports.teacher-marks-entry.export-excel') }}" 
        : "{{ route('school.reports.teacher-marks-entry.export-pdf') }}";
    
    const fullUrl = baseUrl + (params.toString() ? ('?' + params.toString()) : '');
    if (type === 'pdf') {
        window.open(fullUrl, '_blank');
    } else {
        window.location.href = fullUrl;
    }
}
</script>
@endsection
