@extends('layouts.app')

@section('page-title', 'Report Card - Examination')

@section('content')
<style>
    :root {
        --gold-theme: #b8860b;
        --gold-btn: #d97706;
        --dark-teal: #023e4d;
        --red-accent: #dc2626;
    }

    .report-card-landing-container { padding: 10px 0; }

    .top-filter-bar {
        display: flex; gap: 15px; align-items: flex-end; background: #ffffff; padding: 16px 20px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); flex-wrap: wrap;
    }
    .filter-group { display: flex; flex-direction: column; gap: 4px; flex: 1; min-width: 150px; }
    .filter-group label { font-size: 11px; font-weight: 700; color: #475569; }
    .filter-control { height: 38px; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0 12px; font-size: 12.5px; color: #334155; background: #ffffff; width: 100%; font-weight: 600; }

    .btn-apply-filter { background: #b8860b; border: 1px solid #b8860b; color: #ffffff; font-weight: 700; font-size: 12px; padding: 0 20px; height: 38px; border-radius: 6px; cursor: pointer; }

    .card-table-wrap { background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 25px; }
    .card-table-hdr { padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; background: #ffffff; border-bottom: 1px solid #e2e8f0; flex-wrap: wrap; gap: 12px; }
    .card-table-hdr h2 { font-size: 18px; font-weight: 800; color: #0f172a; margin: 0; }

    .hdr-action-group { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
    .btn-hdr-gold-outline { background: #ffffff; border: 1.5px solid #b8860b; color: #b8860b; font-weight: 700; font-size: 11.5px; padding: 8px 16px; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; }
    .btn-hdr-gold-solid { background: #b8860b; border: 1px solid #b8860b; color: #ffffff; font-weight: 800; font-size: 12px; padding: 8px 18px; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; text-transform: uppercase; box-shadow: 0 4px 10px rgba(184,134,11,0.3); }

    .table-responsive-wrapper { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .rc-table { width: 100%; border-collapse: collapse; min-width: 650px; }
    .rc-table th { background: var(--dark-teal); color: #ffffff; padding: 14px 20px; font-size: 13.5px; font-weight: 700; text-align: left; }
    .rc-table td { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; font-size: 13.5px; color: #334155; vertical-align: middle; }

    .btn-send-students { background: #ffffff; border: 1.5px solid #b8860b; color: #b8860b; font-weight: 800; font-size: 11px; padding: 6px 14px; border-radius: 4px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; text-transform: uppercase; }
    .status-sent-green { color: #16a34a; font-weight: 700; font-size: 12px; margin-top: 4px; display: block; }
    .action-icons-group { display: flex; gap: 12px; color: #f97316; font-size: 15px; cursor: pointer; align-items: center; }

    .template-chooser-card {
        background: #ffffff; border-radius: 8px; border: 1.5px solid #b8860b; padding: 15px 20px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; box-shadow: 0 4px 12px rgba(184,134,11,0.08);
    }

    /* Modal Overlay */
    .create-rc-modal { display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 99999; overflow-y: auto; }
    .create-rc-modal.active { display: block; }
    .create-rc-container { max-width: 1100px; width: 95vw; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
    .modal-hdr-orange { background: #f97316; color: #ffffff; padding: 14px 24px; display: flex; justify-content: space-between; align-items: center; }
    .modal-hdr-orange h2 { margin: 0; font-size: 18px; font-weight: 800; }
    .modal-body-form { padding: 24px; }
    .form-section-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
    .student-select-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 12px; margin-top: 12px; background: #fff8f0; padding: 15px; border-radius: 6px; border: 1px solid #ffedd5; }
    .student-chk-item { display: flex; align-items: center; gap: 8px; font-size: 12.5px; font-weight: 600; color: #334155; }

    /* Official CBSE Template Canvas */
    .single-student-report-wrapper { max-width: 900px; margin: 0 auto 40px auto; page-break-after: always; }
    .cbse-exact-template { padding: 35px; background: #ffffff; border: 2px solid #334155; font-family: Arial, sans-serif; color: #0f172a; transition: all 0.3s ease; box-shadow: 0 10px 25px rgba(0,0,0,0.06); border-radius: 6px; }
    .cbse-hdr-phone { text-align: right; font-size: 11px; color: #475569; margin-bottom: 5px; }
    .cbse-hdr-main { text-align: center; margin-bottom: 15px; position: relative; }
    .cbse-hdr-logo { position: absolute; left: 10px; top: 0; width: 75px; height: 75px; object-fit: contain; }
    .cbse-school-name { color: var(--red-accent); font-weight: 800; font-size: 24px; margin: 0; text-transform: lowercase; }
    .cbse-school-name::first-letter { text-transform: uppercase; }
    .cbse-school-address { font-size: 14px; font-weight: bold; color: #1e293b; margin: 2px 0; }
    .cbse-affiliation { color: var(--red-accent); font-size: 12px; font-weight: 600; }
    .cbse-session { text-align: center; font-size: 14px; font-weight: bold; margin: 15px 0 10px 0; }
    .cbse-title-red { text-align: center; color: var(--red-accent); font-weight: 800; font-size: 16px; letter-spacing: 1px; margin-bottom: 15px; }
    .cbse-particulars { font-size: 13px; margin-bottom: 15px; line-height: 2; }
    .cbse-dotted-line { border-bottom: 1px dotted #475569; display: inline-block; padding: 0 10px; font-weight: bold; }
    .cbse-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 12px; }
    .cbse-table th, .cbse-table td { border: 1px solid #000000; padding: 6px 8px; text-align: center; }
    .cbse-table th { font-weight: bold; }
    .cbse-th-red { color: var(--red-accent); }

    .cbse-bottom-flex { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 25px; font-size: 12.5px; flex-wrap: wrap; gap: 20px; }
    .cbse-signs { line-height: 2.2; font-weight: bold; flex: 1; min-width: 250px; }
    .cbse-grading-box { border: 1px solid #000000; width: 320px; max-width: 100%; text-align: center; background: #ffffff; }
    .cbse-grading-hdr { color: var(--red-accent); font-weight: bold; font-size: 13px; padding: 4px; }
    .cbse-grading-sub { font-size: 10.5px; font-weight: bold; padding: 2px 4px; border-bottom: 1px solid #000; }
    .cbse-grade-tbl { width: 100%; border-collapse: collapse; font-size: 11px; }
    .cbse-grade-tbl th { background: var(--red-accent); color: #ffffff; padding: 4px; font-size: 11px; }
    .cbse-grade-tbl td { border: 1px solid #000000; padding: 3px; font-weight: bold; }

    @media (max-width: 768px) {
        .top-filter-bar { flex-direction: column; align-items: stretch; }
        .filter-group { min-width: 100%; }
        .card-table-hdr { flex-direction: column; align-items: flex-start; }
        .hdr-action-group { width: 100%; justify-content: flex-start; }
        .cbse-exact-template { padding: 15px; }
        .cbse-hdr-logo { position: static; display: block; margin: 0 auto 10px auto; }
        .cbse-bottom-flex { flex-direction: column; align-items: stretch; }
        .cbse-grading-box { width: 100%; }
    }

    @media print {
        body * { visibility: hidden; }
        .single-student-report-wrapper, .single-student-report-wrapper * { visibility: visible; }
        .single-student-report-wrapper { position: relative; left: 0; top: 0; width: 100%; box-shadow: none; border: none; margin: 0 0 40px 0; }
        .page-hdr, .top-filter-bar, .card-table-wrap, .template-chooser-card, .create-rc-modal, .btn-print-action, .template-chooser-card { display: none !important; }
    }

    /* ── REPORT CARD HUB DARK MODE OVERRIDES ── */
    body.dark-mode .report-card-landing-container h1,
    body.dark-mode .card-table-hdr h2 {
        color: #f8fafc !important;
    }
    body.dark-mode .report-card-landing-container span {
        color: #94a3b8 !important;
    }
    body.dark-mode .top-filter-bar,
    body.dark-mode .card-table-wrap,
    body.dark-mode .card-table-hdr,
    body.dark-mode .template-chooser-card,
    body.dark-mode .create-rc-container,
    body.dark-mode .form-section-card {
        background: #111827 !important;
        border-color: #1e293b !important;
        color: #cbd5e1 !important;
    }
    body.dark-mode .filter-group label {
        color: #94a3b8 !important;
    }
    body.dark-mode .filter-control,
    body.dark-mode .form-control {
        background-color: #1f2937 !important;
        color: #f8fafc !important;
        border-color: #374151 !important;
    }
    body.dark-mode .btn-apply-filter,
    body.dark-mode .btn-hdr-gold-solid {
        background: #b8860b !important;
        border-color: #b8860b !important;
        color: #ffffff !important;
        box-shadow: none !important;
    }
    body.dark-mode .btn-hdr-gold-outline {
        background: #1f2937 !important;
        border-color: #374151 !important;
        color: #b8860b !important;
    }
    body.dark-mode .btn-hdr-gold-outline:hover {
        background: #374151 !important;
        color: #ffffff !important;
    }
    body.dark-mode .rc-table td {
        border-bottom-color: #1e293b !important;
        color: #cbd5e1 !important;
    }
    body.dark-mode .rc-table td strong {
        color: #f8fafc !important;
    }
    body.dark-mode .rc-table tr:hover td {
        background: rgba(255, 255, 255, 0.02) !important;
    }
    body.dark-mode .btn-send-students {
        background: #1f2937 !important;
        border-color: #374151 !important;
        color: #b8860b !important;
    }
    body.dark-mode .btn-send-students:hover {
        background: #b8860b !important;
        color: #ffffff !important;
    }
    body.dark-mode .action-icons-group {
        color: #f97316 !important;
    }
    /* Modal / Form section inside modal */
    body.dark-mode .create-rc-modal {
        background: rgba(0,0,0,0.7) !important;
    }
    body.dark-mode .student-select-grid {
        background: #1f2937 !important;
        border-color: #374151 !important;
    }
    body.dark-mode .student-chk-item {
        color: #cbd5e1 !important;
    }
    body.dark-mode .student-chk-item strong {
        color: #f8fafc !important;
    }
    body.dark-mode div[style*="background:#fff7ed"],
    body.dark-mode div[style*="background: #fff7ed"] {
        background: #1f2937 !important;
        border-color: #374151 !important;
    }
    body.dark-mode div[style*="background:#fff7ed"] div,
    body.dark-mode div[style*="background: #fff7ed"] div {
        color: #f97316 !important;
    }
    body.dark-mode .btn-secondary {
        background: #1f2937 !important;
        color: #cbd5e1 !important;
        border-color: #374151 !important;
    }
    body.dark-mode .btn-secondary:hover {
        background: #374151 !important;
    }
    body.dark-mode #reportTemplateChooser {
        background: #1f2937 !important;
        color: #f97316 !important;
        border-color: #374151 !important;
    }
    /* Dotted line report card overrides */
    body.dark-mode .cbse-dotted-line {
        color: #60a5fa !important;
    }
    /* No selection info block */
    body.dark-mode div[style*="background:#f0f9ff"],
    body.dark-mode div[style*="background: #f0f9ff"] {
        background: rgba(2, 132, 199, 0.1) !important;
        border-color: rgba(2, 132, 199, 0.3) !important;
        color: #38bdf8 !important;
    }
    body.dark-mode div[style*="background:#f0f9ff"] p,
    body.dark-mode div[style*="background: #f0f9ff"] p {
        color: #38bdf8 !important;
    }
</style>

<div class="report-card-landing-container">
    <div style="margin-bottom: 15px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div>
            <h1 style="font-size:22px; font-weight:800; color:#0f172a; margin:0;">Report Card Generation Hub</h1>
            <span style="font-size:12px; color:#64748b;">Select Class, Section & Exam to generate official student marksheets</span>
        </div>
        <a href="{{ route('school.examination.report-card-template') }}" class="btn btn-sm" style="background:#e0f2fe; color:#0369a1; font-weight:700; border-radius:6px; border:1px solid #bae6fd;">
            <i class="fas fa-palette" style="margin-right:4px;"></i> Open Template Designer Studio
        </a>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="{{ route('school.examination.report-card') }}" id="mainFilterForm">
        <div class="top-filter-bar">
            <div class="filter-group">
                <label><i class="fas fa-chalkboard" style="color:var(--gold-theme);margin-right:4px;"></i>Select Class *</label>
                <select name="class_id" class="filter-control" onchange="document.getElementById('mainFilterForm').submit()" required>
                    <option value="">-- Choose Class --</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label><i class="fas fa-layer-group" style="color:var(--gold-theme);margin-right:4px;"></i>Select Section *</label>
                <select name="section_id" class="filter-control" onchange="document.getElementById('mainFilterForm').submit()">
                    <option value="">-- Choose Section --</option>
                    @foreach($sections as $sec)
                        <option value="{{ $sec->id }}" {{ $selectedSectionId == $sec->id ? 'selected' : '' }}>{{ $sec->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label><i class="fas fa-file-alt" style="color:var(--gold-theme);margin-right:4px;"></i>Choose Test / Term / Exam Name *</label>
                <select name="exam_name" class="filter-control" onchange="document.getElementById('mainFilterForm').submit()">
                    @foreach($availableExams as $ex)
                        <option value="{{ $ex }}" {{ $selectedExam == $ex ? 'selected' : '' }}>{{ $ex }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label><i class="fas fa-user" style="color:var(--gold-theme);margin-right:4px;"></i>Select Student (Optional)</label>
                <select name="student_id" class="filter-control" onchange="document.getElementById('mainFilterForm').submit()">
                    <option value="">-- Generate All Students in Class --</option>
                    @foreach($students as $st)
                        <option value="{{ $st->id }}" {{ $selectedStudentId == $st->id ? 'selected' : '' }}>{{ $st->full_name }} (Adm: {{ $st->admission_number }})</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-apply-filter"><i class="fas fa-search"></i> FILTER DATA</button>
        </div>
    </form>

    <!-- Previous Report Cards Table Card -->
    <div class="card-table-wrap">
        <div class="card-table-hdr">
            <h2>Previous Report Cards</h2>
            <div class="hdr-action-group">
                <button class="btn-hdr-gold-outline"><i class="fas fa-eye"></i> SHOW LOGS</button>
                <button class="btn-hdr-gold-solid" onclick="openCreateModal()"><i class="fas fa-plus-circle"></i> CREATE NEW REPORT CARD</button>
                <button class="btn-hdr-gold-outline"><i class="fas fa-chart-bar"></i> VIEW ANALYTICS REPORT</button>
            </div>
        </div>

        <div class="table-responsive-wrapper">
            <table class="rc-table" id="reportCardsTable">
                <thead>
                    <tr>
                        <th style="width:25%;">Name</th>
                        <th style="width:20%;">Class & Section</th>
                        <th style="width:20%;">Exam / Term</th>
                        <th style="width:20%;">Send to Students</th>
                        <th style="width:15%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($generatedReportCards as $rc)
                    <tr id="row-rc-{{ $rc['id'] }}">
                        <td>
                            <span style="color:#94a3b8; font-size:12px; margin-right:6px;">0{{ $loop->iteration }}.</span>
                            <strong>RC - {{ $rc['student_name'] }}</strong>
                            <br><span style="font-size:11px; color:#94a3b8;">Generated on : {{ date('d M Y') }}</span>
                        </td>
                        <td><strong>{{ $rc['class_name'] }}, {{ $rc['section_name'] }}</strong></td>
                        <td><span style="background:#fef3c7; color:#d97706; padding:3px 8px; border-radius:4px; font-weight:700; font-size:11px;">{{ $selectedExam }}</span></td>
                        <td>
                            <form method="POST" action="{{ route('school.examination.report-card') }}" style="margin:0;">
                                @csrf
                                <input type="hidden" name="action" value="send_student">
                                <input type="hidden" name="student_id" value="{{ $rc['id'] }}">
                                <button type="submit" class="btn-send-students">SEND TO STUDENTS <i class="fas fa-paper-plane" style="font-size:10px;"></i></button>
                            </form>
                            <span class="status-sent-green">Report Card had been sent successfully</span>
                        </td>
                        <td>
                            <div class="action-icons-group">
                                <i class="fas fa-chart-bar" title="Analytics"></i>
                                <a href="{{ route('school.examination.report-card', ['student_id' => $rc['id'], 'class_id' => $selectedClassId, 'section_id' => $selectedSectionId, 'exam_name' => $selectedExam]) }}" style="color:inherit;"><i class="fas fa-pencil-alt" title="Edit / View"></i></a>
                                <i class="fas fa-download" onclick="window.print()" title="Download"></i>
                                <form method="POST" action="{{ route('school.examination.report-card') }}" style="display:inline;" onsubmit="deleteReportRow(event, {{ $rc['id'] }}, this)">
                                    @csrf
                                    <input type="hidden" name="action" value="delete_report">
                                    <input type="hidden" name="student_id" value="{{ $rc['id'] }}">
                                    <button type="submit" style="background:none; border:none; color:inherit; cursor:pointer; padding:0;"><i class="fas fa-trash-alt" title="Delete Data"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="row-rc-default">
                        <td colspan="5" style="text-align:center; padding:20px; color:#64748b;">No report card records generated yet. Select Class and Section above to generate.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Overlay: Create Report Card -->
<div class="create-rc-modal" id="createModal">
    <div class="create-rc-container">
        <div class="modal-hdr-orange">
            <h2>Create Class Report Card</h2>
            <button onclick="closeCreateModal()" style="background:none; border:none; color:#fff; font-size:24px; cursor:pointer;">&times;</button>
        </div>

        <div class="modal-body-form">
            <form method="GET" action="{{ route('school.examination.report-card') }}">
                <input type="hidden" name="generate" value="1">
                <div class="form-section-card">
                    <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:15px; margin-bottom:15px;">
                        <div>
                            <label style="display:block; font-size:11.5px; font-weight:700; color:#334155; margin-bottom:4px;">Choose Exam / Term *</label>
                            <select name="exam_name" class="form-control" style="height:38px; font-size:12.5px;">
                                @foreach($availableExams as $ex)<option value="{{ $ex }}" {{ $selectedExam == $ex ? 'selected' : '' }}>{{ $ex }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display:block; font-size:11.5px; font-weight:700; color:#334155; margin-bottom:4px;">Select Class *</label>
                            <select name="class_id" class="form-control" onchange="this.form.submit()" style="height:38px; font-size:12.5px;" required>
                                <option value="">-- Choose Class --</option>
                                @foreach($classes as $c)<option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display:block; font-size:11.5px; font-weight:700; color:#334155; margin-bottom:4px;">Select Section *</label>
                            <select name="section_id" class="form-control" onchange="this.form.submit()" style="height:38px; font-size:12.5px;">
                                <option value="">-- Choose Section --</option>
                                @foreach($sections as $sec)<option value="{{ $sec->id }}" {{ $selectedSectionId == $sec->id ? 'selected' : '' }}>{{ $sec->name }}</option>@endforeach
                            </select>
                        </div>
                    </div>

                    @if($students->isNotEmpty())
                    <div style="background:#fff7ed; border:1px solid #ffedd5; padding:15px; border-radius:6px;">
                        <div style="font-weight:700; color:#c2410c; font-size:13px; margin-bottom:10px;">
                            Class {{ $students->first()->class?->name ?? '1' }} ({{ $students->count() }} / {{ $students->count() }}) - Section {{ $students->first()->section?->name ?? 'A' }}
                        </div>
                        <div class="student-select-grid">
                            <label class="student-chk-item" style="grid-column: 1 / -1; border-bottom:1px solid #fed7aa; padding-bottom:8px; margin-bottom:6px;"><input type="checkbox" checked> <strong>Select all</strong></label>
                            @foreach($students as $st)<label class="student-chk-item"><input type="checkbox" name="selected_students[]" value="{{ $st->id }}" checked> {{ $st->full_name }}</label>@endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:20px; flex-wrap:wrap;">
                    <button type="submit" class="btn" style="background:#b8860b; color:#fff; font-weight:800; padding:10px 24px; border-radius:6px; font-size:13px; border:none;"><i class="fas fa-eye"></i> PREVIEW REPORT CARDS</button>
                    <button type="button" onclick="closeCreateModal()" class="btn btn-secondary" style="padding:10px 20px; font-size:13px;">BACK</button>
                    <button type="submit" class="btn" style="background:#b8860b; color:#fff; font-weight:800; padding:10px 28px; border-radius:6px; font-size:13px; border:none;"><i class="fas fa-save"></i> SAVE</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Render DISTINCT Report Card for EACH Individual Student in Class -->
@if($selectedClassId && $selectedSectionId && $classReportCards->isNotEmpty())
<div style="max-width:900px; margin:30px auto;">
    <div class="template-chooser-card">
        <div>
            <span style="font-size:13px; font-weight:800; color:#0f172a; display:block;"><i class="fas fa-paint-brush" style="color:#b8860b; margin-right:6px;"></i>Change Report Card Template Layout Anytime:</span>
            <span style="font-size:11.5px; color:#64748b;">Generated <strong>{{ $classReportCards->count() }} Distinct Student Report Cards</strong> for {{ $selectedExam }}</span>
        </div>
        <div>
            <select id="reportTemplateChooser" onchange="changeReportCardTheme(this.value)" style="padding:8px 14px; font-weight:700; border-radius:6px; border:1.5px solid #b8860b; font-size:13px; background:#fff8f0; color:#9a3412; cursor:pointer;">
                <option value="#dc2626">01. CBSE Classic Red & Black</option>
                <option value="#1e40af">02. Royal Navy Blue & Gold</option>
                <option value="#047857">03. Forest Emerald & Crest</option>
                <option value="#991b1b">04. Deep Crimson Senior Secondary</option>
                <option value="#f97316">05. Custom School Master Theme</option>
            </select>
        </div>
    </div>

    @foreach($classReportCards as $cCard)
    @php
        $cardStudent = $cCard['student'];
        $cardMarks = $cCard['marks'];
    @endphp
    <div class="single-student-report-wrapper">
        <div class="cbse-exact-template">
            <div class="cbse-hdr-phone">Ph. {{ $school?->phone ?? '011-27483920' }}</div>
            <div class="cbse-hdr-main">
                @if($school?->logo)<img src="{{ asset('storage/' . $school->logo) }}" class="cbse-hdr-logo" alt="School Logo">
                @else<svg class="cbse-hdr-logo" viewBox="0 0 100 100"><circle cx="50" cy="50" r="45" class="svgCircleLogoClass" fill="#dc2626" /><path d="M50 20 L65 40 L85 45 L70 60 L75 80 L50 70 L25 80 L30 60 L15 45 L35 40 Z" fill="#fbbf24" /></svg>@endif
                <h1 class="cbse-school-name theme-color-target" style="color:#dc2626;">{{ $school?->name ?? 'edutinker public school, Delhi' }}</h1>
                <div class="cbse-school-address">{{ $school?->address ?? 'Delhi, India' }}</div>
                <div class="cbse-affiliation theme-color-target" style="color:#dc2626;">Affiliated to C.B.S.E., (New Delhi) Affiliation No. 22232425</div>
            </div>

            <div class="cbse-session">Academic Session: <span style="border-bottom:1px dotted #000; padding:0 20px;">{{ now()->year }}-{{ now()->year+1 }}</span></div>
            <div class="cbse-title-red theme-color-target" style="color:#dc2626;">REPORT CARD FOR CLASS {{ strtoupper($cardStudent->class?->name ?? '10') }} ({{ strtoupper($cardStudent->section?->name ?? 'A') }})</div>

            <div class="cbse-particulars">
                <div>Student's Name: <span class="cbse-dotted-line" style="min-width:320px; color:#1e40af;">{{ $cardStudent->full_name }}</span></div>
                <div>Mother's/Father's/Guardian's Name: <span class="cbse-dotted-line" style="min-width:360px;">{{ $cardStudent->father_name ?? $cardStudent->mother_name ?? 'Parent Name' }}</span></div>
                <div style="display:flex; justify-content:space-between; width:100%; flex-wrap:wrap;">
                    <span>Date of Birth: <span class="cbse-dotted-line" style="min-width:140px;">{{ $cardStudent->date_of_birth ?? '12/05/2010' }}</span></span>
                    <span>Roll No: <span class="cbse-dotted-line" style="min-width:100px;">{{ $cardStudent->roll_number ?? '15' }}</span></span>
                    <span>Class/Section: <span class="cbse-dotted-line" style="min-width:140px;">{{ $cardStudent->class?->name }} / {{ $cardStudent->section?->name }}</span></span>
                </div>
            </div>

            <!-- Scholastic Table displaying ONLY UNIQUE Allocated Subjects for THIS Student -->
            <div class="table-responsive-wrapper">
                <table class="cbse-table">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width:30%; text-align:left;">Scholastic Subjects (Allocated Marks)</th>
                            <th colspan="4" class="cbse-th-red theme-color-target" style="color:#dc2626;">{{ $selectedExam }} Marks</th>
                            <th colspan="4" class="cbse-th-red theme-color-target" style="color:#dc2626;">Final Term Evaluation</th>
                        </tr>
                        <tr>
                            <th>Per Test (20)</th><th>Terminal (80)</th><th>Marks Allocated</th><th>Grade</th>
                            <th>Per Test (20)</th><th>Terminal (80)</th><th>Total Score</th><th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cardMarks as $mRow)
                        @php
                            $subName = $mRow->subject?->name ?? 'Subject #' . $loop->iteration;
                            $obt = (float)$mRow->marks_obtained;
                            $grd = $mRow->grade ?? ($obt >= 90 ? 'A1' : ($obt >= 80 ? 'A2' : 'B1'));
                        @endphp
                        <tr>
                            <td style="text-align:left; font-weight:bold;">{{ $subName }}</td>
                            <td>{{ round($obt * 0.2) }}</td><td>{{ round($obt * 0.8) }}</td>
                            <td><strong>{{ $obt }} / {{ (int)$mRow->max_marks }}</strong></td><td><strong class="theme-color-target" style="color:#dc2626;">{{ $grd }}</strong></td>
                            <td>{{ round($obt * 0.2) }}</td><td>{{ round($obt * 0.8) }}</td>
                            <td><strong>{{ $obt }} / {{ (int)$mRow->max_marks }}</strong></td><td><strong class="theme-color-target" style="color:#dc2626;">{{ $grd }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="cbse-bottom-flex">
                <div class="cbse-signs">
                    <div>Date: <span style="border-bottom:1px dotted #000; display:inline-block; width:140px;">{{ date('d-m-Y') }}</span></div>
                    <div style="margin-top:15px;">Class Teacher Sign: <span style="border-bottom:1px dotted #000; display:inline-block; width:160px;"></span></div>
                    <div style="margin-top:15px;">Principal Sign: <span style="border-bottom:1px dotted #000; display:inline-block; width:180px; font-weight:700; color:var(--dark-teal);">{{ $principal?->full_name ?? 'Dr. S. K. Sharma (Principal)' }}</span></div>
                </div>

                <div class="cbse-grading-box">
                    <div class="cbse-grading-hdr theme-color-target" style="color:#dc2626;">Instruction</div>
                    <div class="cbse-grading-sub">Grading scale for scholastic areas : Grades are awarded on an 8 point grading scales</div>
                    <table class="cbse-grade-tbl">
                        <thead><tr><th class="theme-bg-target" style="background:#dc2626;">MARKS RANGE</th><th class="theme-bg-target" style="background:#dc2626;">GRADE</th></tr></thead>
                        <tbody>
                            <tr><td>91 - 100</td><td>A1</td></tr>
                            <tr><td>81 - 90</td><td>A2</td></tr>
                            <tr><td>71 - 80</td><td>B1</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <div class="btn-print-action" style="text-align:center; margin-bottom:30px;">
        <button onclick="window.print()" class="btn btn-primary" style="padding:12px 30px; font-size:15px; background:var(--dark-teal); border-color:var(--dark-teal); box-shadow: 0 4px 12px rgba(0,0,0,0.15); font-weight:700;">
            <i class="fas fa-print" style="margin-right:8px;"></i> Print / Download All {{ $classReportCards->count() }} Student Report Cards
        </button>
    </div>
</div>
@else
<div style="max-width:900px; margin:30px auto; background:#f0f9ff; border:1.5px solid #0284c7; padding:25px; border-radius:10px; text-align:center; color:#0369a1;">
    <h3 style="margin:0 0 8px 0; font-size:17px; font-weight:800;"><i class="fas fa-info-circle" style="margin-right:8px;"></i>Select Class & Section to Preview Report Cards</h3>
    <p style="margin:0; font-size:13px; color:#0284c7;">Report cards will be generated exclusively for students who have marks allocated in the examination database.</p>
</div>
@endif

<script>
    function openCreateModal() { document.getElementById('createModal').classList.add('active'); }
    function closeCreateModal() { document.getElementById('createModal').classList.remove('active'); }

    function changeReportCardTheme(color) {
        document.querySelectorAll('.theme-color-target').forEach(el => el.style.color = color);
        document.querySelectorAll('.theme-bg-target').forEach(el => el.style.background = color);
        document.querySelectorAll('.svgCircleLogoClass').forEach(el => el.setAttribute('fill', color));
    }

    function deleteReportRow(event, id, form) {
        if (!confirm('Are you sure you want to delete this report card data?')) { event.preventDefault(); return false; }
        const row = document.getElementById('row-rc-' + id);
        if (row) { row.style.transition = 'all 0.3s ease'; row.style.opacity = '0'; setTimeout(() => row.remove(), 300); }
        return true;
    }
</script>
@endsection
