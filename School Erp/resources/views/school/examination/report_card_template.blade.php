@extends('layouts.app')

@section('page-title', 'Template Selection - Examination')

@section('content')
<style>
    :root {
        --dark-teal: #023e4d;
        --orange-btn: #f97316;
        --blue-primary: #1e40af;
        --red-accent: #dc2626;
    }

    .template-selection-container { padding: 10px 0; }
    .tmpl-hdr-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
    .academic-year-select { background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 6px 12px; font-size: 13px; color: #334155; font-weight: 600; }
    .tmpl-action-btns { display: flex; gap: 12px; flex-wrap: wrap; }
    .btn-logs { background: #ffffff; border: 1px solid #b8860b; color: #b8860b; font-weight: 700; font-size: 12px; padding: 8px 16px; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; }
    .btn-create-tmpl { background: #ffffff; border: 1.5px solid #b8860b; color: #b8860b; font-weight: 800; font-size: 12px; padding: 8px 18px; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; text-transform: uppercase; }
    .badge-pro { background: #f97316; color: #ffffff; font-size: 9px; font-weight: 800; padding: 1px 4px; border-radius: 3px; margin-left: 4px; }

    .table-responsive-wrapper { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .tmpl-table-card { background: #ffffff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #e2e8f0; }
    .tmpl-table { width: 100%; border-collapse: collapse; min-width: 600px; }
    .tmpl-table th { background: var(--dark-teal); color: #ffffff; padding: 14px 20px; font-size: 13.5px; font-weight: 700; text-align: left; }
    .tmpl-table td { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; font-size: 13.5px; color: #334155; }
    .tmpl-num { color: #94a3b8; font-size: 12px; margin-right: 8px; }
    .btn-edit-pencil { background: #fff7ed; border: 1px solid #fdba74; color: #f97316; padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; }
    .btn-edit-pencil:hover { background: #f97316; color: #fff; }

    .edit-template-modal { display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: #ffffff; z-index: 99999; overflow-y: auto; }
    .edit-template-modal.active { display: block; }
    .edit-modal-hdr { background: #f97316; color: #ffffff; padding: 14px 24px; display: flex; justify-content: space-between; align-items: center; }
    .edit-modal-hdr h2 { margin: 0; font-size: 20px; font-weight: 800; }

    .edit-workspace-grid { display: grid; grid-template-columns: 1fr; gap: 20px; padding: 20px; align-items: start; }
    .fields-card { background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.05); max-height: calc(100vh - 120px); overflow-y: auto; }
    .fields-header { background: #f8fafc; padding: 14px 18px; border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; z-index: 10; }
    .fields-header h2 { font-size: 16px; font-weight: 700; color: #1e293b; margin: 0 0 2px 0; }

    .accordion-item { border-bottom: 1px solid #f1f5f9; }
    .accordion-btn { width: 100%; text-align: left; padding: 10px 16px; background: #ffffff; border: none; font-size: 13px; font-weight: 600; color: #334155; display: flex; justify-content: space-between; align-items: center; cursor: pointer; transition: all 0.2s ease; }
    .accordion-btn:hover { background: #f8fafc; color: var(--blue-primary); }
    .accordion-btn i.arrow { transition: transform 0.2s; font-size: 11px; color: #94a3b8; }
    .accordion-btn.active i.arrow { transform: rotate(180deg); color: var(--blue-primary); }
    .accordion-content { display: none; padding: 8px 16px 12px 16px; background: #fafafa; }
    .accordion-content.open { display: block; }

    .field-tag-item { display: flex; align-items: center; justify-content: space-between; padding: 6px 10px; margin-bottom: 6px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 12px; color: #334155; font-weight: 600; }
    .btn-add-tag { background: var(--blue-primary); color: #ffffff; border: none; border-radius: 4px; width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; font-size: 11px; }

    .customizer-studio-bar { background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 16px; margin-bottom: 20px; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: center; }
    .customizer-studio-bar label { display: block; font-size: 11.5px; font-weight: 700; color: #334155; margin-bottom: 4px; }
    .customizer-studio-bar input[type="color"] { width: 100%; height: 38px; border: 1px solid #cbd5e1; border-radius: 6px; cursor: pointer; padding: 2px; }
    .customizer-studio-bar select { width: 100%; height: 38px; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0 10px; font-size: 12.5px; font-weight: 600; }

    .class-alloc-box { background: #fff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 12px; max-height: 100px; overflow-y: auto; font-size: 12px; display: flex; flex-wrap: wrap; gap: 10px; }

    .cbse-exact-template { padding: 35px; background: #ffffff; border: 2px solid #334155; font-family: Arial, sans-serif; color: #0f172a; min-height: 850px; transition: all 0.3s; }
    .cbse-hdr-phone { text-align: right; font-size: 11px; color: #475569; margin-bottom: 5px; }
    .cbse-hdr-main { text-align: center; margin-bottom: 15px; position: relative; }
    .cbse-hdr-logo { position: absolute; left: 10px; top: 0; width: 75px; height: 75px; object-fit: contain; }
    .cbse-school-name { color: #dc2626; font-weight: 800; font-size: 24px; margin: 0; text-transform: lowercase; transition: color 0.3s; }
    .cbse-school-name::first-letter { text-transform: uppercase; }
    .cbse-school-address { font-size: 14px; font-weight: bold; color: #1e293b; margin: 2px 0; }
    .cbse-affiliation { color: #dc2626; font-size: 12px; font-weight: 600; transition: color 0.3s; }
    .cbse-session { text-align: center; font-size: 14px; font-weight: bold; margin: 15px 0 10px 0; }
    .cbse-title-red { text-align: center; color: #dc2626; font-weight: 800; font-size: 16px; letter-spacing: 1px; margin-bottom: 15px; transition: color 0.3s; }
    .cbse-particulars { font-size: 13px; margin-bottom: 15px; line-height: 2; }
    .cbse-dotted-line { border-bottom: 1px dotted #475569; display: inline-block; padding: 0 10px; font-weight: bold; color: #1e40af; }
    .cbse-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 12px; }
    .cbse-table th, .cbse-table td { border: 1px solid #000000; padding: 6px 8px; text-align: center; }
    .cbse-table th { font-weight: bold; }
    .cbse-th-red { color: #dc2626; transition: color 0.3s; }
    .cbse-bottom-flex { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 25px; font-size: 12.5px; flex-wrap: wrap; gap: 20px; }
    .cbse-signs { line-height: 2.2; font-weight: bold; }
    .cbse-grading-box { border: 1px solid #000000; width: 320px; max-width: 100%; text-align: center; }
    .cbse-grading-hdr { color: #dc2626; font-weight: bold; font-size: 13px; padding: 4px; transition: color 0.3s; }
    .cbse-grading-sub { font-size: 10.5px; font-weight: bold; padding: 2px 4px; border-bottom: 1px solid #000; }
    .cbse-grade-tbl { width: 100%; border-collapse: collapse; font-size: 11px; }
    .cbse-grade-tbl th { background: #dc2626; color: #ffffff; padding: 4px; font-size: 11px; transition: background 0.3s; }
    .cbse-grade-tbl td { border: 1px solid #000000; padding: 3px; font-weight: bold; }

    @media (max-width: 768px) {
        .edit-workspace-grid { grid-template-columns: 1fr; }
        .customizer-studio-bar { grid-template-columns: 1fr; }
        .cbse-exact-template { padding: 15px; }
        .cbse-hdr-logo { position: static; display: block; margin: 0 auto 10px auto; }
    }

    /* ── REPORT CARD TEMPLATE DARK MODE OVERRIDES ── */
    body.dark-mode {
        --text-dark: #f8fafc;
        --white: #111827;
    }
    body.dark-mode .template-selection-container h1,
    body.dark-mode .tmpl-hdr-flex h1,
    body.dark-mode .edit-modal-hdr h2 {
        color: #f8fafc !important;
    }
    body.dark-mode .tmpl-hdr-flex span,
    body.dark-mode .edit-modal-hdr p,
    body.dark-mode .tmpl-num {
        color: #94a3b8 !important;
    }
    body.dark-mode .academic-year-select,
    body.dark-mode .btn-logs {
        background: #1f2937 !important;
        border-color: #374151 !important;
        color: #f8fafc !important;
    }
    body.dark-mode .btn-logs:hover {
        background: #374151 !important;
        color: #ffffff !important;
    }
    body.dark-mode .btn-create-tmpl {
        background: #1f2937 !important;
        border-color: #d97706 !important;
        color: #d97706 !important;
    }
    body.dark-mode .btn-create-tmpl:hover {
        background: #d97706 !important;
        color: #ffffff !important;
    }
    body.dark-mode .tmpl-table-card {
        background: #111827 !important;
        border-color: #1e293b !important;
        box-shadow: none !important;
    }
    body.dark-mode .tmpl-table td {
        border-bottom-color: #1e293b !important;
        color: #cbd5e1 !important;
    }
    body.dark-mode .tmpl-table td strong {
        color: #f8fafc !important;
    }
    body.dark-mode .tmpl-table tr:hover td {
        background: rgba(255, 255, 255, 0.02) !important;
    }
    body.dark-mode .btn-edit-pencil {
        background: rgba(249, 115, 22, 0.15) !important;
        border-color: rgba(249, 115, 22, 0.3) !important;
        color: #f97316 !important;
    }
    body.dark-mode .btn-edit-pencil:hover {
        background: #f97316 !important;
        color: #ffffff !important;
    }
    body.dark-mode span[id^="alloc-badge-"] {
        background: rgba(3, 105, 161, 0.15) !important;
        color: #38bdf8 !important;
    }
    /* Modal / Workspace */
    body.dark-mode .edit-template-modal {
        background: #0f172a !important;
    }
    body.dark-mode .edit-modal-hdr {
        background: #1f2937 !important;
        border-bottom: 1px solid #1e293b !important;
    }
    body.dark-mode .fields-card {
        background: #111827 !important;
        border-color: #1e293b !important;
    }
    body.dark-mode .fields-header {
        background: #1f2937 !important;
        border-bottom-color: #1e293b !important;
    }
    body.dark-mode .fields-header h2 {
        color: #f8fafc !important;
    }
    body.dark-mode .accordion-btn {
        background: #111827 !important;
        color: #cbd5e1 !important;
    }
    body.dark-mode .accordion-btn:hover {
        background: #1f2937 !important;
        color: #818cf8 !important;
    }
    body.dark-mode .accordion-content {
        background: #0f172a !important;
    }
    body.dark-mode .field-tag-item {
        background: #1f2937 !important;
        border-color: #374151 !important;
        color: #f8fafc !important;
    }
    body.dark-mode .customizer-studio-bar {
        background: #111827 !important;
        border-color: #1e293b !important;
    }
    body.dark-mode .customizer-studio-bar label {
        color: #cbd5e1 !important;
    }
    body.dark-mode .customizer-studio-bar select {
        background: #1f2937 !important;
        border-color: #374151 !important;
        color: #f8fafc !important;
    }
    body.dark-mode .class-alloc-box {
        background: #1f2937 !important;
        border-color: #374151 !important;
        color: #cbd5e1 !important;
    }
    body.dark-mode .class-alloc-box label {
        color: #cbd5e1 !important;
    }
</style>

<div class="template-selection-container" id="selectionView">
    <div style="margin-bottom: 15px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div>
            <h1 style="font-size:22px; font-weight:800; color:#0f172a; margin:0;">Template Selection Studio</h1>
            <span style="font-size:12px; color:#64748b;">Examination Module — Choose, allocate classes, and design report cards</span>
        </div>
    </div>

    <div class="tmpl-hdr-flex">
        <div>
            <label style="display:block; font-size:11px; font-weight:700; color:#475569; margin-bottom:4px;">Academic Year *</label>
            <select class="academic-year-select"><option>📅 Apr 2025 - Mar 2026</option></select>
        </div>
        <div class="tmpl-action-btns">
            <button class="btn-logs"><i class="fas fa-eye"></i> LOGS</button>
            <button class="btn-create-tmpl" onclick="openEditModal('Create New Custom School Template', 'template_5')">
                <i class="fas fa-plus"></i> CREATE NEW TEMPLATE <span class="badge-pro">PRO+</span>
            </button>
        </div>
    </div>

    <div class="table-responsive-wrapper">
        <div class="tmpl-table-card">
            <table class="tmpl-table">
                <thead>
                    <tr>
                        <th style="width:45%;">Template Name</th>
                        <th style="width:40%;">Allocated Classes & Sections</th>
                        <th style="width:15%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($templatesList as $tmpl)
                    <tr>
                        <td><span class="tmpl-num">0{{ $tmpl['id'] }}.</span> <strong>{{ $tmpl['name'] }}</strong></td>
                        <td><span style="background:#e0f2fe; color:#0369a1; padding:4px 10px; border-radius:12px; font-weight:700; font-size:12px;" id="alloc-badge-{{ $tmpl['code'] }}"><i class="fas fa-chalkboard" style="margin-right:4px;"></i>{{ $tmpl['classes'] }}</span></td>
                        <td>
                            <button class="btn-edit-pencil" onclick="openEditModal('{{ $tmpl['name'] }}', '{{ $tmpl['code'] }}', '{{ $tmpl['primary_color'] }}')" title="Edit Template, Colors & Allocate Classes">
                                <i class="fas fa-edit"></i> Edit / Allocate
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Workspace: Edit, Design & Allocate Classes -->
<div class="edit-template-modal {{ $viewMode == 'edit' ? 'active' : '' }}" id="editModal">
    <div class="edit-modal-hdr">
        <div>
            <h2 id="modalTemplateTitle">Edit & Design Template</h2>
            <p>Allocate classes, customize colors, fonts, and database placeholder fields</p>
        </div>
        <button onclick="closeEditModal()" style="background:none; border:none; color:#fff; font-size:24px; cursor:pointer;">&times;</button>
    </div>

    <div class="edit-workspace-grid">

        <div style="background:#fff; border-radius:8px; border:1px solid #e2e8f0; padding:20px;">
            <form method="POST" action="{{ route('school.examination.report-card-template') }}" onsubmit="saveCanvasContent()">
                @csrf
                <input type="hidden" name="template_content" id="template_content_input">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; border-bottom:1px solid #e2e8f0; padding-bottom:12px; flex-wrap:wrap; gap:10px;">
                    <div>
                        <select id="templateSwitcher" onchange="switchTemplateCode(this.value)" style="padding:8px 14px; font-weight:700; border-radius:6px; border:1px solid #cbd5e1; font-size:13px; background:#f8fafc;">
                            @foreach($templatesList as $tmpl)
                                <option value="{{ $tmpl['code'] }}" data-color="{{ $tmpl['primary_color'] }}" data-font="{{ $tmpl['font'] }}">{{ $tmpl['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display:flex; gap:10px;">
                        <button type="button" onclick="closeEditModal()" class="btn btn-secondary" style="background:#e2e8f0; color:#334155; border:none; padding:8px 16px; font-weight:600;">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </button>
                        <button type="submit" class="btn btn-warning" style="background:#f97316; color:#fff; border:none; padding:8px 24px; font-weight:800; box-shadow: 0 4px 10px rgba(249,115,22,0.3);">
                            <i class="fas fa-save"></i> Save Template & Allocations
                        </button>
                    </div>
                </div>

                <!-- Customizer Bar with Class Allocation Checkboxes -->
                <div class="customizer-studio-bar">
                    <div style="grid-column: 1 / -1; background:#eff6ff; padding:12px 15px; border-radius:6px; border:1px solid #bfdbfe; margin-bottom:5px;">
                        <label style="color:#1e40af; font-size:12.5px;"><i class="fas fa-check-double" style="margin-right:6px;"></i>Allocate Classes to this Template *</label>
                        <div class="class-alloc-box" id="classAllocBox">
                            @foreach($classes as $c)
                            <label style="display:inline-flex; align-items:center; gap:6px; margin:0; cursor:pointer; font-weight:600;">
                                <input type="checkbox" name="allocated_classes[]" value="{{ $c->name }}" checked onchange="updateAllocatedClassesDisplay()"> {{ $c->name }}
                            </label>
                            @endforeach
                            <label style="display:inline-flex; align-items:center; gap:6px; margin:0; cursor:pointer; font-weight:600;"><input type="checkbox" checked onchange="updateAllocatedClassesDisplay()"> Class 1 A</label>
                            <label style="display:inline-flex; align-items:center; gap:6px; margin:0; cursor:pointer; font-weight:600;"><input type="checkbox" checked onchange="updateAllocatedClassesDisplay()"> Class 2 A</label>
                            <label style="display:inline-flex; align-items:center; gap:6px; margin:0; cursor:pointer; font-weight:600;"><input type="checkbox" checked onchange="updateAllocatedClassesDisplay()"> Class 10 A</label>
                        </div>
                    </div>

                    <div>
                        <label>School Theme Color</label>
                        <input type="color" id="primaryColorPicker" value="#dc2626" onchange="updateTemplateColors(this.value)">
                    </div>
                    <div>
                        <label>Typography Font</label>
                        <select id="fontSelector" onchange="updateTemplateFont(this.value)">
                            <option value="Arial, sans-serif">Arial / Standard</option>
                            <option value="Plus Jakarta Sans, sans-serif">Plus Jakarta Sans</option>
                            <option value="Georgia, serif">Georgia / Serif</option>
                        </select>
                    </div>
                    <div>
                        <label>Border Frame Style</label>
                        <select id="borderSelector" onchange="updateTemplateBorder(this.value)">
                            <option value="2px solid #334155">Standard Dark Border</option>
                            <option value="4px double #dc2626">Double Theme Border</option>
                        </select>
                    </div>
                </div>

                <!-- Template Canvas Preview -->
                <div class="cbse-exact-template" id="activeCanvas">
                    <div class="cbse-hdr-phone">Ph. {$SchoolPhone}</div>
                    <div class="cbse-hdr-main">
                        <svg class="cbse-hdr-logo" viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="45" fill="#047857" />
                            <path d="M50 20 L65 40 L85 45 L70 60 L75 80 L50 70 L25 80 L30 60 L15 45 L35 40 Z" fill="#fbbf24" />
                        </svg>
                        <h1 class="cbse-school-name" id="canvasSchoolName">{$SchoolName}</h1>
                        <div class="cbse-school-address">{$SchoolAddress}</div>
                        <div class="cbse-affiliation" id="canvasAffiliation">Affiliated to C.B.S.E., (New Delhi) Affiliation No. {$AffiliationNo}</div>
                    </div>

                    <div class="cbse-session">Academic Session: <span style="border-bottom:1px dotted #000; padding:0 20px;">2025-2026</span></div>
                    <div class="cbse-title-red" id="canvasTitle">REPORT CARD FOR CLASS</div>

                    <div class="cbse-particulars" id="canvasParticulars">
                        <div>Student's Name: <span class="cbse-dotted-line" style="min-width:320px;">{$StudentName}</span></div>
                        <div>Mother's/Father's/Guardian's Name: <span class="cbse-dotted-line" style="min-width:360px;">{$FatherName}</span></div>
                        <div style="display:flex; justify-content:space-between; width:100%; flex-wrap:wrap;">
                            <span>Date of Birth: <span class="cbse-dotted-line" style="min-width:140px;">{$DOB}</span></span>
                            <span>Roll No: <span class="cbse-dotted-line" style="min-width:100px;">{$RollNo}</span></span>
                            <span>Class/Section: <span class="cbse-dotted-line" style="min-width:140px;">{$ClassSection}</span></span>
                        </div>
                    </div>

                    <div class="table-responsive-wrapper">
                        <table class="cbse-table">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="width:25%; text-align:left;">Scholastic Areas (Database Subjects)</th>
                                    <th colspan="4" class="cbse-th-red canvas-theme-color">Term-1 (100 marks)</th>
                                    <th colspan="4" class="cbse-th-red canvas-theme-color">Term-2 (100 marks)</th>
                                </tr>
                                <tr>
                                    <th>Per Test (20)</th><th>Half Yearly (80)</th><th>Marks Obtained (100)</th><th>Grade</th>
                                    <th>Per Test (20)</th><th>Half Yearly (80)</th><th>Marks Obtained (100)</th><th>Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $subList = ['English', 'Maths', 'Science', 'Social Science', 'Hindi']; @endphp
                                @foreach($subList as $sName)
                                <tr><td style="text-align:left; font-weight:bold;">{{ $sName }}</td><td>18</td><td>72</td><td>90</td><td>A1</td><td>19</td><td>73</td><td>92</td><td>A1</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Clean Aligned Bottom Layout for Signatures and Instruction Box -->
                    <div class="cbse-bottom-flex">
                        <div class="cbse-signs">
                            <div>Date: <span style="border-bottom:1px dotted #000; display:inline-block; width:140px;">...........................</span></div>
                            <div style="margin-top:15px;">Class Teacher Sign: <span style="border-bottom:1px dotted #000; display:inline-block; width:160px;">...........................</span></div>
                            <div style="margin-top:15px;">Principal Sign: <span style="border-bottom:1px dotted #000; display:inline-block; width:180px;">{$PrincipalSign}</span></div>
                        </div>

                        <div class="cbse-grading-box">
                            <div class="cbse-grading-hdr canvas-theme-color" id="canvasGradingHdr">Instruction</div>
                            <div class="cbse-grading-sub">Grading scale for scholastic areas : Grades are awarded on an 8 point grading scales</div>
                            <table class="cbse-grade-tbl">
                                <thead><tr><th class="canvas-bg-color" id="canvasGradingTh">MARKS RANGE</th><th class="canvas-bg-color">GRADE</th></tr></thead>
                                <tbody>
                                    <tr><td>91 - 100</td><td>A1</td></tr>
                                    <tr><td>81 - 90</td><td>A2</td></tr>
                                    <tr><td>71 - 80</td><td>B1</td></tr>
                                    <tr><td>61 - 70</td><td>B2</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openEditModal(templateName, code, color) {
        document.getElementById('modalTemplateTitle').innerText = "Edit Template & Allocate Classes - " + templateName;
        document.getElementById('editModal').classList.add('active');
        if (code) document.getElementById('templateSwitcher').value = code;
        if (color) { document.getElementById('primaryColorPicker').value = color; updateTemplateColors(color); }
    }
    function closeEditModal() { document.getElementById('editModal').classList.remove('active'); }
    function switchTemplateCode(code) {
        const selOpt = document.querySelector('#templateSwitcher option[value="' + code + '"]');
        if (selOpt) {
            const color = selOpt.getAttribute('data-color') || '#dc2626';
            const font = selOpt.getAttribute('data-font') || 'Arial, sans-serif';
            document.getElementById('primaryColorPicker').value = color;
            document.getElementById('fontSelector').value = font;
            updateTemplateColors(color); updateTemplateFont(font);
        }
    }
    function updateTemplateColors(color) {
        document.getElementById('canvasSchoolName').style.color = color;
        document.getElementById('canvasAffiliation').style.color = color;
        document.getElementById('canvasTitle').style.color = color;
        document.querySelectorAll('.canvas-theme-color').forEach(el => el.style.color = color);
    }
    function updateTemplateFont(fontFamily) { document.getElementById('activeCanvas').style.fontFamily = fontFamily; }
    function updateTemplateBorder(borderVal) { document.getElementById('activeCanvas').style.border = borderVal; }
    function toggleAccordion(btn) { btn.classList.toggle('active'); btn.nextElementSibling.classList.toggle('open'); }
    function insertTag(tag) {
        const particulars = document.getElementById('canvasParticulars');
        if (particulars) {
            const tagSpan = document.createElement('span');
            tagSpan.style.background = '#e0f2fe'; tagSpan.style.color = '#0369a1'; tagSpan.style.padding = '2px 8px'; tagSpan.style.borderRadius = '4px'; tagSpan.style.margin = '4px'; tagSpan.style.fontWeight = 'bold'; tagSpan.style.fontSize = '12px'; tagSpan.innerText = tag;
            particulars.appendChild(tagSpan);
        }
    }
    function updateAllocatedClassesDisplay() {
        const code = document.getElementById('templateSwitcher').value;
        const checked = Array.from(document.querySelectorAll('#classAllocBox input[type="checkbox"]:checked')).map(cb => cb.parentNode.textContent.trim());
        const badge = document.getElementById('alloc-badge-' + code);
        if (badge && checked.length > 0) {
            badge.innerHTML = '<i class="fas fa-chalkboard" style="margin-right:4px;"></i>' + checked.slice(0, 3).join(', ') + (checked.length > 3 ? ' +' + (checked.length - 3) + ' more' : '');
        }
    }
    function saveCanvasContent() { document.getElementById('template_content_input').value = document.getElementById('activeCanvas').innerHTML; }
</script>
@endsection
