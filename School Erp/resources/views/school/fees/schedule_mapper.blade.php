@extends('layouts.app')

@section('page-title', 'Student Class & Fee Schedule Mapper')

@section('content')
<style>
    .mapper-hdr {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    .mapper-hdr-title h1 {
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 4px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .mapper-hdr-title p {
        font-size: 13px;
        color: #64748b;
        margin: 0;
    }
    .filter-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px 20px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1.5fr;
        gap: 16px;
        align-items: center;
    }
    .floating-field {
        position: relative;
    }
    .floating-field label {
        position: absolute;
        top: -9px;
        left: 12px;
        background: #ffffff;
        padding: 0 5px;
        font-size: 11px;
        font-weight: 700;
        color: #475569;
        z-index: 2;
    }
    .floating-control {
        width: 100%;
        height: 42px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 0 12px;
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
        background: #ffffff;
        outline: none;
        transition: border-color 0.2s;
    }
    .floating-control:focus {
        border-color: #0284c7;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.1);
    }
    .search-input-wrap {
        position: relative;
    }
    .search-input-wrap i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 14px;
    }
    .search-input-wrap input {
        padding-left: 36px;
    }
    .grid-container-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .grid-toolbar {
        background: #004d5a;
        color: #ffffff;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        gap: 20px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    .grid-toolbar-btn {
        background: transparent;
        border: none;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        font-size: 11.5px;
        font-weight: 700;
        opacity: 0.9;
        transition: opacity 0.2s;
    }
    .grid-toolbar-btn:hover {
        opacity: 1;
    }
    .mapper-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .mapper-table th {
        background: #004d5a;
        color: #ffffff;
        padding: 12px 16px;
        text-align: left;
        font-weight: 600;
        font-size: 12px;
        border-right: 1px solid rgba(255,255,255,0.1);
    }
    .mapper-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #e2e8f0;
        vertical-align: middle;
    }
    .student-avatar-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .student-avatar-circle {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 18px;
    }
    .cell-floating-select {
        position: relative;
        width: 100%;
        max-width: 220px;
    }
    .cell-floating-select label {
        position: absolute;
        top: -8px;
        left: 10px;
        background: #ffffff;
        padding: 0 4px;
        font-size: 10px;
        font-weight: 700;
        color: #64748b;
    }
    .cell-floating-select select {
        width: 100%;
        height: 38px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 0 10px;
        font-size: 12.5px;
        font-weight: 600;
        color: #1e293b;
        background: #ffffff;
    }
    .grid-footer {
        background: #ffffff;
        padding: 14px 20px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .toggle-group {
        display: flex;
        align-items: center;
        gap: 24px;
    }
    .switch-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 12px;
        font-weight: 700;
        color: #475569;
    }
    .switch {
        position: relative;
        display: inline-block;
        width: 36px;
        height: 20px;
    }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider {
        position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
        background-color: #cbd5e1; transition: .4s; border-radius: 20px;
    }
    .slider:before {
        position: absolute; content: ""; height: 14px; width: 14px; left: 3px; bottom: 3px;
        background-color: white; transition: .4s; border-radius: 50%;
    }
    input:checked + .slider { background-color: #0284c7; }
    input:checked + .slider:before { transform: translateX(16px); }
    .pagination-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .total-rows-badge {
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 4px 12px;
        font-size: 12px;
        font-weight: 700;
        color: #334155;
        background: #f8fafc;
    }
    .page-btn {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #64748b;
        font-size: 11px;
    }
    .page-btn.active {
        background: #dcfce7;
        color: #15803d;
        border-color: #86efac;
        font-weight: 800;
    }
    .save-btn-container {
        display: flex;
        justify-content: flex-end;
        margin-top: 20px;
    }
    .btn-save-main {
        background: #e2e8f0;
        color: #94a3b8;
        font-weight: 800;
        padding: 10px 28px;
        border-radius: 6px;
        border: none;
        cursor: not-allowed;
        font-size: 13px;
        letter-spacing: 0.5px;
    }
    .btn-save-main.active {
        background: #0284c7;
        color: #ffffff;
        cursor: pointer;
    }

    /* ── TOOLBAR DROPDOWN PANELS ── */
    .toolbar-panel-wrapper {
        position: relative;
        display: inline-block;
    }
    .toolbar-panel {
        display: none;
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        z-index: 9999;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        min-width: 220px;
        padding: 14px;
        color: #1e293b;
        font-size: 13px;
    }
    .toolbar-panel.open { display: block; }
    .toolbar-panel h6 {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.6px;
        margin: 0 0 10px 0;
        padding-bottom: 8px;
        border-bottom: 1px solid #f1f5f9;
    }
    .col-toggle-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 5px 0;
        gap: 8px;
        cursor: pointer;
    }
    .col-toggle-row label {
        font-size: 12.5px;
        font-weight: 600;
        color: #334155;
        cursor: pointer;
        position: static !important;
        background: transparent !important;
        padding: 0 !important;
        top: auto !important; left: auto !important;
    }
    .col-toggle-row input[type="checkbox"] {
        accent-color: #0284c7;
        width: 15px; height: 15px;
        cursor: pointer;
    }
    .filter-field-row {
        margin-bottom: 8px;
    }
    .filter-field-row label {
        font-size: 10px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        display: block;
        margin-bottom: 3px;
        position: static !important;
        background: transparent !important;
        padding: 0 !important;
    }
    .filter-field-row input {
        width: 100%;
        height: 32px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 0 10px;
        font-size: 12px;
        color: #1e293b;
        outline: none;
        box-sizing: border-box;
    }
    .filter-field-row input:focus { border-color: #0284c7; }
    .density-option {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 7px 10px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 12.5px;
        font-weight: 600;
        color: #334155;
        transition: background 0.15s;
    }
    .density-option:hover { background: #f1f5f9; }
    .density-option.active { background: #e0f2fe; color: #0284c7; }
    .density-option i { width: 16px; text-align: center; }
    .export-option {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 10px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 12.5px;
        font-weight: 600;
        color: #334155;
        transition: background 0.15s;
        border: none;
        background: transparent;
        width: 100%;
        text-align: left;
    }
    .export-option:hover { background: #f1f5f9; }
    .export-option i { width: 16px; text-align: center; color: #0284c7; }
    .filter-clear-btn {
        background: none;
        border: none;
        color: #ef4444;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        padding: 0;
        margin-top: 8px;
        display: block;
    }
    /* ── SCHEDULE MAPPER DARK MODE OVERRIDES ── */
    body.dark-mode {
        --border: #1e293b;
        --card-bg: #111827;
        --text-primary: #f8fafc;
        --text-secondary: #94a3b8;
    }
    body.dark-mode .mapper-hdr-title h1 {
        color: #f8fafc !important;
    }
    body.dark-mode .mapper-hdr-title p {
        color: #94a3b8 !important;
    }
    body.dark-mode .filter-card,
    body.dark-mode .grid-container-card,
    body.dark-mode .grid-footer {
        background: #111827 !important;
        border-color: #1e293b !important;
    }
    body.dark-mode .floating-field label,
    body.dark-mode .cell-floating-select label {
        background: #111827 !important;
        color: #94a3b8 !important;
    }
    body.dark-mode .floating-control,
    body.dark-mode .cell-floating-select select {
        background: #1f2937 !important;
        color: #f8fafc !important;
        border-color: #374151 !important;
    }
    body.dark-mode .floating-control:focus,
    body.dark-mode .cell-floating-select select:focus {
        border-color: #38bdf8 !important;
    }
    body.dark-mode .mapper-table td {
        border-bottom-color: #1e293b !important;
    }
    body.dark-mode .mapper-table td span,
    body.dark-mode .student-avatar-cell span {
        color: #f8fafc !important;
    }
    body.dark-mode .total-rows-badge {
        background: #1f2937 !important;
        border-color: #374151 !important;
        color: #f8fafc !important;
    }
    body.dark-mode .page-btn {
        background: #1f2937 !important;
        color: #cbd5e1 !important;
        border-color: #374151 !important;
    }
    body.dark-mode .page-btn.active {
        background: rgba(34, 197, 94, 0.15) !important;
        color: #4ade80 !important;
        border-color: #4ade80 !important;
    }
    body.dark-mode .student-avatar-circle {
        background: #374151 !important;
        color: #94a3b8 !important;
    }
    body.dark-mode .switch-wrap {
        color: #cbd5e1 !important;
    }
    body.dark-mode .student-avatar-cell span {
        color: #f8fafc !important;
    }
</style>

<div class="mapper-hdr">
    <div class="mapper-hdr-title">
        <h1>Student Class & Fee Schedule Mapper <span style="font-size:16px; color:#f97316;">&#9660;</span></h1>
        <p>Fee Management</p>
    </div>
</div>

<form method="GET" action="{{ route('school.fees.schedule-mapper') }}" id="filterForm">
    <div class="filter-card">
        <div class="floating-field">
            <label>Academic Year *</label>
            <select name="academic_year" class="floating-control" onchange="this.form.submit()">
                @foreach($sessions as $sess)
                    @php 
                        $yearVal = $sess->name;
                        if (preg_match('/(\d{4})-\d{2,4}/', $sess->name, $m)) {
                            $yearVal = $m[0];
                        }
                    @endphp
                    <option value="{{ $yearVal }}" {{ (request('academic_year') == $yearVal || (!request('academic_year') && $sess->is_current)) ? 'selected' : '' }}>{{ $sess->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="floating-field">
            <label>Select Class *</label>
            <select name="class_id" class="floating-control" onchange="this.form.submit()">
                <option value="">Select Class</option>
                @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ request('class_id') == $c->id || (request('class_id') == '' && $c->name == 'NUR') ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="floating-field">
            <label>Select Section *</label>
            <select name="section_id" class="floating-control" onchange="this.form.submit()">
                <option value="">Select Section</option>
                @foreach($sections as $s)
                    @php $sName = is_object($s) ? $s->name : $s; @endphp
                    <option value="{{ $sName }}" {{ request('section_id') == $sName || (request('section_id') == '' && $sName == 'A') ? 'selected' : '' }}>{{ $sName }}</option>
                @endforeach
            </select>
        </div>
        <div class="floating-field search-input-wrap">
            <label>Search student</label>
            <i class="fas fa-search"></i>
            <input type="text" name="search" class="floating-control" placeholder="Enter min 3 char to search" value="{{ request('search') }}" onkeyup="if(event.key==='Enter') this.form.submit()">
        </div>
    </div>
</form>

<form method="POST" action="{{ route('school.fees.schedule-mapper') }}" id="saveScheduleForm">
    @csrf
    <div class="grid-container-card">
        <div class="grid-toolbar" style="position:relative;">

            <!-- COLUMNS -->
            <div class="toolbar-panel-wrapper">
                <button type="button" class="grid-toolbar-btn" onclick="togglePanel('colPanel')">
                    <i class="fas fa-columns"></i> COLUMNS
                </button>
                <div class="toolbar-panel" id="colPanel">
                    <h6>Toggle Columns</h6>
                    <div class="col-toggle-row"><label for="col0">Admission ID</label><input type="checkbox" id="col0" checked onchange="toggleColumn(0,this.checked)"></div>
                    <div class="col-toggle-row"><label for="col1">Student Name</label><input type="checkbox" id="col1" checked onchange="toggleColumn(1,this.checked)"></div>
                    <div class="col-toggle-row"><label for="col2">Class</label><input type="checkbox" id="col2" checked onchange="toggleColumn(2,this.checked)"></div>
                    <div class="col-toggle-row"><label for="col3">Section</label><input type="checkbox" id="col3" checked onchange="toggleColumn(3,this.checked)"></div>
                    <div class="col-toggle-row"><label for="col4">Schedule Name</label><input type="checkbox" id="col4" checked onchange="toggleColumn(4,this.checked)"></div>
                </div>
            </div>

            <!-- FILTERS -->
            <div class="toolbar-panel-wrapper">
                <button type="button" class="grid-toolbar-btn" onclick="togglePanel('filterPanel')">
                    <i class="fas fa-filter"></i> FILTERS
                </button>
                <div class="toolbar-panel" id="filterPanel" style="min-width:260px;">
                    <h6>Column Filters</h6>
                    <div class="filter-field-row"><label>Admission ID</label><input type="text" id="flt0" placeholder="Filter..." oninput="applyFilters()"></div>
                    <div class="filter-field-row"><label>Student Name</label><input type="text" id="flt1" placeholder="Filter..." oninput="applyFilters()"></div>
                    <div class="filter-field-row"><label>Class</label><input type="text" id="flt2" placeholder="Filter..." oninput="applyFilters()"></div>
                    <div class="filter-field-row"><label>Section</label><input type="text" id="flt3" placeholder="Filter..." oninput="applyFilters()"></div>
                    <div class="filter-field-row"><label>Schedule Name</label><input type="text" id="flt4" placeholder="Filter..." oninput="applyFilters()"></div>
                    <button type="button" class="filter-clear-btn" onclick="clearFilters()"><i class="fas fa-times-circle"></i> Clear All Filters</button>
                </div>
            </div>

            <!-- DENSITY -->
            <div class="toolbar-panel-wrapper">
                <button type="button" class="grid-toolbar-btn" onclick="togglePanel('densityPanel')">
                    <i class="fas fa-bars"></i> DENSITY
                </button>
                <div class="toolbar-panel" id="densityPanel" style="min-width:180px;">
                    <h6>Row Density</h6>
                    <div class="density-option" id="den-compact" onclick="setDensity('compact')"><i class="fas fa-grip-lines"></i> Compact</div>
                    <div class="density-option active" id="den-standard" onclick="setDensity('standard')"><i class="fas fa-bars"></i> Standard</div>
                    <div class="density-option" id="den-comfortable" onclick="setDensity('comfortable')"><i class="fas fa-align-justify"></i> Comfortable</div>
                </div>
            </div>

            <!-- EXPORT -->
            <div class="toolbar-panel-wrapper">
                <button type="button" class="grid-toolbar-btn" onclick="togglePanel('exportPanel')">
                    <i class="fas fa-download"></i> EXPORT
                </button>
                <div class="toolbar-panel" id="exportPanel" style="min-width:180px;">
                    <h6>Export Data</h6>
                    <button type="button" class="export-option" onclick="exportMapperCSV()"><i class="fas fa-file-csv"></i> Export as CSV</button>
                    <button type="button" class="export-option" onclick="exportMapperExcel()"><i class="fas fa-file-excel"></i> Export as Excel</button>
                </div>
            </div>

        </div>

        <div style="overflow-x:auto;">
            <table class="mapper-table">
                <thead>
                    <tr>
                        <th style="width:15%;">Admission ID</th>
                        <th style="width:25%;">Student Name</th>
                        <th style="width:20%;">Class</th>
                        <th style="width:20%;">Section</th>
                        <th style="width:20%;">Schedule Name</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $student)
                    <tr>
                        <td style="color:#64748b; font-weight:600;">
                            <span style="font-size:11px; margin-right:6px;">{{ sprintf('%02d.', $index + 1) }}</span> {{ $student->admission_id ?? $student->admission_number ?? '150B' }}
                        </td>
                        <td>
                            <div class="student-avatar-cell">
                                @if($student->photo)
                                    <img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}" style="width:38px; height:38px; border-radius:50%; object-fit:cover; flex-shrink:0; border: 1.5px solid var(--border);">
                                @else
                                    <div class="student-avatar-circle">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @endif
                                <span style="font-weight:700; color:#1e293b;">{{ $student->full_name }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="cell-floating-select">
                                <label>Select Class</label>
                                <select disabled>
                                    <option>{{ optional($student->class)->name ?? 'NUR' }}</option>
                                </select>
                            </div>
                        </td>
                        <td>
                            <div class="cell-floating-select">
                                <label>Select Section</label>
                                <select disabled>
                                    <option>{{ optional($student->section)->name ?? 'A' }}</option>
                                </select>
                            </div>
                        </td>
                        <td>
                            <div class="cell-floating-select">
                                <label>Fee Schedule</label>
                                <select name="student_schedules[{{ $student->id }}]" onchange="enableSaveBtn()">
                                    <option value="">Select Schedule</option>
                                    @foreach($schedules as $sch)
                                        <option value="{{ $sch->id }}" {{ ($student->fee_schedule_id == $sch->id || ($loop->first && !$student->fee_schedule_id)) ? 'selected' : '' }}>{{ $sch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center; padding:30px; color:#64748b;">No students found matching the criteria.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="grid-footer">
            <div class="toggle-group">
                <div class="switch-wrap">
                    <span>Show Deactivated Students</span>
                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="switch-wrap">
                    <span>Show Deleted Students</span>
                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider"></span>
                    </label>
                </div>
            </div>

            <div class="pagination-wrap">
                <div class="total-rows-badge">Total Rows: {{ count($students) }}</div>
                <button type="button" class="page-btn"><i class="fas fa-chevron-left"></i></button>
                <button type="button" class="page-btn active">1</button>
                <button type="button" class="page-btn"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </div>

    <div class="save-btn-container">
        <button type="submit" class="btn-save-main active" id="saveMainBtn">SAVE</button>
    </div>
</form>

<script>
function enableSaveBtn() {
    const btn = document.getElementById('saveMainBtn');
    btn.classList.add('active');
    btn.style.cursor = 'pointer';
}

/* ── TOOLBAR PANEL TOGGLE ── */
function togglePanel(id) {
    const panels = document.querySelectorAll('.toolbar-panel');
    panels.forEach(function(p) {
        if (p.id !== id) p.classList.remove('open');
    });
    const panel = document.getElementById(id);
    if (panel) panel.classList.toggle('open');
}
document.addEventListener('click', function(e) {
    if (!e.target.closest('.toolbar-panel-wrapper')) {
        document.querySelectorAll('.toolbar-panel').forEach(function(p) {
            p.classList.remove('open');
        });
    }
});

/* ── COLUMNS TOGGLE ── */
function toggleColumn(colIdx, show) {
    var table = document.querySelector('.mapper-table');
    if (!table) return;
    var rows = table.querySelectorAll('tr');
    rows.forEach(function(row) {
        var cells = row.querySelectorAll('th, td');
        if (cells[colIdx]) {
            cells[colIdx].style.display = show ? '' : 'none';
        }
    });
}

/* ── FILTERS ── */
function applyFilters() {
    var filters = [];
    for (var i = 0; i < 5; i++) {
        var el = document.getElementById('flt' + i);
        filters.push(el ? el.value.trim().toLowerCase() : '');
    }
    var table = document.querySelector('.mapper-table');
    if (!table) return;
    var rows = table.querySelectorAll('tbody tr');
    rows.forEach(function(row) {
        var cells = row.querySelectorAll('td');
        var show = true;
        filters.forEach(function(f, i) {
            if (f && cells[i]) {
                var text = cells[i].innerText.toLowerCase();
                if (text.indexOf(f) === -1) show = false;
            }
        });
        row.style.display = show ? '' : 'none';
    });
}
function clearFilters() {
    for (var i = 0; i < 5; i++) {
        var el = document.getElementById('flt' + i);
        if (el) el.value = '';
    }
    applyFilters();
}

/* ── DENSITY ── */
var densityPads = { compact: '6px 16px', standard: '14px 16px', comfortable: '22px 16px' };
function setDensity(mode) {
    document.querySelectorAll('.density-option').forEach(function(d) { d.classList.remove('active'); });
    var el = document.getElementById('den-' + mode);
    if (el) el.classList.add('active');
    var table = document.querySelector('.mapper-table');
    if (!table) return;
    table.querySelectorAll('tbody td').forEach(function(td) {
        td.style.padding = densityPads[mode];
    });
    document.getElementById('densityPanel').classList.remove('open');
}

/* ── EXPORT CSV ── */
function exportMapperCSV() {
    var table = document.querySelector('.mapper-table');
    if (!table) return;
    var visibleRows = Array.from(table.querySelectorAll('tr')).filter(function(r) { return r.style.display !== 'none'; });
    var csv = visibleRows.map(function(row) {
        var cells = Array.from(row.querySelectorAll('th, td')).filter(function(c) { return c.style.display !== 'none'; });
        return cells.map(function(c) {
            return '"' + (c.innerText || '').trim().replace(/[\r\n]+/g, ' ').replace(/"/g, '""') + '"';
        }).join(',');
    }).join('\n');
    var blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
    var a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'Schedule_Mapper.csv';
    document.body.appendChild(a); a.click(); document.body.removeChild(a);
    document.getElementById('exportPanel').classList.remove('open');
    if (typeof showToast === 'function') showToast('Exported as CSV!');
}

/* ── EXPORT EXCEL (simple HTML table trick) ── */
function exportMapperExcel() {
    var table = document.querySelector('.mapper-table');
    if (!table) return;
    var html = '<table border="1">';
    Array.from(table.querySelectorAll('tr')).filter(function(r) { return r.style.display !== 'none'; }).forEach(function(row) {
        html += '<tr>';
        Array.from(row.querySelectorAll('th, td')).filter(function(c) { return c.style.display !== 'none'; }).forEach(function(cell) {
            var tag = cell.tagName.toLowerCase();
            html += '<' + tag + '>' + (cell.innerText || '').trim().replace(/&/g,'&amp;') + '</' + tag + '>';
        });
        html += '</tr>';
    });
    html += '</table>';
    var blob = new Blob(['\uFEFF' + html], { type: 'application/vnd.ms-excel;charset=utf-8;' });
    var a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'Schedule_Mapper.xls';
    document.body.appendChild(a); a.click(); document.body.removeChild(a);
    document.getElementById('exportPanel').classList.remove('open');
    if (typeof showToast === 'function') showToast('Exported as Excel!');
}
</script>
@endsection
