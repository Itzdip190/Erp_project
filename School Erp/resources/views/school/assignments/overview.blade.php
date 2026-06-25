@extends('layouts.app')

@section('title', 'Class Overview')

@section('styles')
<style>
    /* Premium overhauls for Class Overview */
    .overview-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 12px;
        box-shadow: var(--shadow);
        overflow: hidden;
        margin-top: 18px;
    }

    .table-container-scroll {
        overflow-x: auto;
        width: 100%;
    }

    table.tbl-overview {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
        color: var(--t1);
    }

    table.tbl-overview th {
        background: #0a4b5c;
        color: #ffffff;
        font-weight: 700;
        font-size: 10.5px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        padding: 10px 12px;
        border-right: 1px solid rgba(255, 255, 255, 0.1);
        border-bottom: 2px solid var(--border);
        text-align: left;
        white-space: nowrap;
    }

    table.tbl-overview td {
        padding: 10px 12px;
        border-right: 1px solid var(--border);
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
        white-space: nowrap;
    }

    table.tbl-overview td:last-child, table.tbl-overview th:last-child {
        border-right: none;
    }

    table.tbl-overview tr:hover td {
        background: rgba(10, 75, 92, 0.02);
    }

    /* Column colors based on screenshot */
    .text-orange-highlight {
        color: #d97706;
        font-weight: 700;
    }

    .text-bold {
        font-weight: 700;
    }

    /* iOS style switch toggle */
    .switch-container {
        display: inline-flex;
        align-items: center;
        cursor: pointer;
        user-select: none;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 22px;
        margin-left: 8px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e1;
        transition: .25s ease;
        border-radius: 22px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .25s ease;
        border-radius: 50%;
    }

    input:checked + .slider {
        background-color: #10b981;
    }

    input:checked + .slider:before {
        transform: translateX(22px);
    }

    .info-tooltip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 15px;
        height: 15px;
        background: rgba(255,255,255,0.22);
        border-radius: 50%;
        font-size: 8px;
        color: #fff;
        cursor: pointer;
        margin-left: 5px;
        border: 1.5px solid rgba(255,255,255,0.45);
        flex-shrink: 0;
        transition: background 0.18s, border-color 0.18s;
        user-select: none;
        vertical-align: middle;
    }
    .info-tooltip:hover,
    .info-tooltip.active {
        background: rgba(255,255,255,0.45);
        border-color: #fff;
    }
    .info-tooltip.dark {
        background: #e0f2fe;
        color: #0369a1;
        border: 1.5px solid #7dd3fc;
    }
    .info-tooltip.dark:hover,
    .info-tooltip.dark.active {
        background: #0ea5e9;
        border-color: #0284c7;
        color: #fff;
    }

    /* Popover box */
    .ov-popover {
        display: none;
        position: fixed;
        z-index: 9999;
        background: #1e293b;
        color: #e2e8f0;
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 12.5px;
        line-height: 1.55;
        max-width: 270px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.28);
        pointer-events: none;
    }
    .ov-popover.visible {
        display: block;
        animation: ovPop 0.18s ease;
    }
    .ov-popover::before {
        content: '';
        position: absolute;
        top: -6px;
        left: 18px;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-bottom: 6px solid #1e293b;
    }
    .ov-popover-title {
        font-weight: 700;
        font-size: 11.5px;
        color: #7dd3fc;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    @keyframes ovPop {
        from { opacity: 0; transform: translateY(-4px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Segmented selector buttons */
    .view-selector-btn {
        padding: 8px 20px;
        font-size: 12px;
        font-weight: 700;
        border: 1px solid #d97706;
        cursor: pointer;
        transition: all 0.2s;
        border-radius: 4px;
    }

    .view-selector-btn.active {
        background: #d97706;
        color: #fff;
    }

    .view-selector-btn.inactive {
        background: #fff;
        color: #d97706;
    }

    .view-selector-btn.inactive:hover {
        background: rgba(217, 119, 6, 0.05);
    }

    /* Edit Select */
    .inline-edit-select {
        width: 100%;
        padding: 4px;
        font-size: 12px;
        border-radius: 4px;
        border: 1px solid #d97706;
        outline: none;
        background: #fff;
    }

    table.tbl-overview tfoot td {
        background: #eae6f3;
        color: var(--navy);
        font-weight: 700;
        font-size: 12.5px;
        padding: 10px 12px;
        border-right: 1px solid var(--border);
        border-top: 2px solid #cbd5e1;
        vertical-align: middle;
        white-space: nowrap;
    }

    table.tbl-overview tfoot td:last-child {
        border-right: none;
    }
</style>
@endsection

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1>
            Class Overview
            <span style="display:inline-flex; align-items:center; justify-content:center; width:22px; height:22px; background:rgba(245,158,11,0.2); border-radius:50%; font-size:12px; color:#f59e0b; margin-left:6px;">
                <i class="fas fa-chevron-down"></i>
            </span>
        </h1>
        <p style="color:var(--t2); font-size:12px;">Class, Subject & Teacher Assignment</p>
    </div>
</div>

<!-- Filters Panel -->
<div class="card" style="margin-bottom:18px;">
    <div class="card-body" style="padding:16px 20px;">
        <form action="{{ route('school.assignments.class-overview') }}" method="GET" id="overviewFilterForm">
            <!-- Hidden inputs to preserve view mode -->
            <input type="hidden" name="view_mode" id="viewModeInput" value="{{ $viewMode }}">

            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
                <!-- Left Filters Dropdowns -->
                <div style="display:flex; align-items:center; gap:12px; flex:1; min-width:300px;">
                    <div style="width:160px;">
                        <label class="form-label" style="font-weight:700; font-size:10px;">Academic Year *</label>
                        <select name="academic_session_id" class="form-control" onchange="this.form.submit()">
                            @foreach($academicSessions as $ses)
                                <option value="{{ $ses->id }}" {{ $sessionId == $ses->id ? 'selected' : '' }}>
                                    {{ $ses->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="width:180px;">
                        <label class="form-label" style="font-weight:700; font-size:10px;">Select Class *</label>
                        <select name="class_id" class="form-control" onchange="this.form.submit()">
                            <option value="">Select Class</option>
                            @foreach($classList as $cls)
                                <option value="{{ $cls->id }}" {{ $classFilterId == $cls->id ? 'selected' : '' }}>
                                    {{ $cls->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if($viewMode === 'section')
                    <div style="width:180px;">
                        <label class="form-label" style="font-weight:700; font-size:10px;">Select Section *</label>
                        <select name="section_id" class="form-control" onchange="this.form.submit()">
                            <option value="">Select Section</option>
                            @foreach($sectionList as $sec)
                                <option value="{{ $sec->id }}" {{ $sectionFilterId == $sec->id ? 'selected' : '' }}>
                                    {{ $sec->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>

                <!-- Right Toggle Switch -->
                <div>
                    <label class="switch-container">
                        <span style="font-size:11px; font-weight:700; color:var(--navy);">Include deactivated students in old/new admissions</span>
                        <div class="switch">
                            <input type="checkbox" name="include_deactivated" id="includeDeactivatedToggle" value="true" {{ $includeDeactivated ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="slider round"></span>
                        </div>
                    </label>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- View selectors & Download Bar -->
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; flex-wrap:wrap; gap:10px;">
    <div style="display:flex; gap:8px;">
        <button type="button" onclick="setViewMode('section')" class="view-selector-btn {{ $viewMode === 'section' ? 'active' : 'inactive' }}">
            SECTION VIEW
        </button>
        <button type="button" onclick="setViewMode('class')" class="view-selector-btn {{ $viewMode === 'class' ? 'active' : 'inactive' }}">
            CLASS VIEW
        </button>
    </div>
    <div style="text-align:right;">
        <button onclick="downloadCSV()" class="btn btn-outline" style="border-radius:6px; font-weight:700; font-size:12px; padding:8px 16px; border-color:#d97706; color:#d97706;">
            <i class="fas fa-download"></i> DOWNLOAD
        </button>
    </div>
</div>

<!-- Instruction Note -->
<div style="text-align:right; margin-bottom:12px;">
    <p style="color:var(--red); font-size:11px; font-style:italic; font-weight:600;">
        *class teacher can be assigned or changed from here itself by double clicking on class teacher cell
    </p>
</div>

<!-- Table Card -->
<div class="overview-card">
    <div class="table-container-scroll">
        <table class="tbl-overview" id="overviewGridTable">
            <thead>
                <tr>
                    <th rowspan="2" style="width:40px; text-align:center;">#</th>
                    <th rowspan="2" style="border-right:1px solid rgba(255,255,255,0.15);">Class & Section</th>
                    <th rowspan="2" style="border-right:1px solid rgba(255,255,255,0.15);">Class Teacher</th>
                    <th rowspan="2" style="border-right:1px solid rgba(255,255,255,0.15);">Total Subjects</th>
                    <th rowspan="2" style="border-right:1px solid rgba(255,255,255,0.15);">Time Table Created</th>
                    
                    <!-- Old Admissions group -->
                    <th colspan="2" style="text-align:center; border-right:1px solid rgba(255,255,255,0.15);">Old Admissions</th>
                    
                    <th rowspan="2" style="border-right:1px solid rgba(255,255,255,0.15);">New Admissions</th>
                    <th rowspan="2" style="border-right:1px solid rgba(255,255,255,0.15);">
                        Today's Admissions
                        <span class="info-tooltip" data-info-key="todayadmissions"><i class="fas fa-info"></i></span>
                    </th>
                    
                    <!-- TC Students group -->
                    <th colspan="2" style="text-align:center; border-right:1px solid rgba(255,255,255,0.15);">
                        TC Students
                        <span class="info-tooltip" data-info-key="tcstudents"><i class="fas fa-info"></i></span>
                    </th>
                    
                    <th rowspan="2" style="border-right:1px solid rgba(255,255,255,0.15);">
                        Irregular Students
                        <span class="info-tooltip" data-info-key="irregular"><i class="fas fa-info"></i></span>
                    </th>
                    <th rowspan="2" style="border-right:1px solid rgba(255,255,255,0.15);">Deactivated Students</th>
                    <th rowspan="2" style="border-right:1px solid rgba(255,255,255,0.15);">
                        Total Students
                        <span class="info-tooltip" data-info-key="totalstudents"><i class="fas fa-info"></i></span>
                    </th>
                    
                    <!-- Deleted Students group -->
                    <th colspan="2" style="text-align:center; border-right:1px solid rgba(255,255,255,0.15);">
                        Deleted Students
                        <span class="info-tooltip" data-info-key="deletedstudents"><i class="fas fa-info"></i></span>
                    </th>
                    
                    <th rowspan="2" style="text-align:center;">Active Students</th>
                </tr>
                <tr>
                    <th style="text-align:center; border-right:1px solid rgba(255,255,255,0.15);">Promoted</th>
                    <th style="text-align:center; border-right:1px solid rgba(255,255,255,0.15);">Repeated</th>
                    <th style="text-align:center; border-right:1px solid rgba(255,255,255,0.15);">Old Student TC</th>
                    <th style="text-align:center; border-right:1px solid rgba(255,255,255,0.15);">New Student TC</th>
                    <th style="text-align:center; border-right:1px solid rgba(255,255,255,0.15);">Old Student deleted</th>
                    <th style="text-align:center; border-right:1px solid rgba(255,255,255,0.15);">New Student deleted</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportData as $row)
                    <tr>
                        <td style="text-align:center; color:var(--t2); font-weight:600;">
                            {{ sprintf('%02d', $loop->iteration) }}
                        </td>
                        <td style="font-weight:700; color:var(--navy);">
                            {{ $row['label'] }}
                        </td>
                        
                        <!-- Class Teacher cell (editable on double-click if in section view) -->
                        @if($viewMode === 'section')
                            <td class="editable-teacher-cell" 
                                data-section-id="{{ $row['section_id'] }}" 
                                data-current-teacher-id="{{ $row['class_teacher_id'] }}"
                                style="font-weight:600; cursor:pointer;"
                                title="Double-click to edit class teacher">
                                {{ $row['class_teacher'] }}
                            </td>
                        @else
                            <td style="color:var(--t3); font-style:italic;">
                                {{ $row['class_teacher'] }}
                            </td>
                        @endif

                        <td class="text-orange-highlight" style="text-align:center;">
                            {{ $row['total_subjects'] }}
                        </td>
                        <td style="font-weight:600; color:var(--t2);">
                            {{ $row['timetable_created'] }}
                        </td>
                        <td style="text-align:center;">{{ $row['promoted'] }}</td>
                        <td style="text-align:center;">{{ $row['repeated'] }}</td>
                        <td style="text-align:center;">{{ $row['new_admissions'] }}</td>
                        <td style="text-align:center;">{{ $row['today_admissions'] }}</td>
                        <td style="text-align:center;">{{ $row['old_student_tc'] }}</td>
                        <td style="text-align:center;">{{ $row['new_student_tc'] }}</td>
                        <td style="text-align:center;">{{ $row['irregular'] }}</td>
                        <td class="text-orange-highlight" style="text-align:center;">{{ $row['deactivated'] }}</td>
                        <td class="text-orange-highlight" style="text-align:center;">{{ $row['total_students'] }}</td>
                        <td style="text-align:center;">{{ $row['old_deleted'] }}</td>
                        <td style="text-align:center;">{{ $row['new_deleted'] }}</td>
                        <td class="text-orange-highlight" style="text-align:center;">{{ $row['active_students'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="17" style="text-align:center; padding:32px; color:var(--t3);">
                            No structures found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td style="text-align:center; color:var(--t2); font-weight:600;"></td>
                    <td style="font-weight:700; color:var(--navy);">Total</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align:center;">{{ $totals['promoted'] }}</td>
                    <td style="text-align:center;">{{ $totals['repeated'] }}</td>
                    <td style="text-align:center;">{{ $totals['new_admissions'] }}</td>
                    <td style="text-align:center;">{{ $totals['today_admissions'] }}</td>
                    <td style="text-align:center;">{{ $totals['old_student_tc'] }}</td>
                    <td style="text-align:center;">{{ $totals['new_student_tc'] }}</td>
                    <td style="text-align:center;">{{ $totals['irregular'] }}</td>
                    <td class="text-orange-highlight" style="text-align:center;">{{ $totals['deactivated'] }}</td>
                    <td class="text-orange-highlight" style="text-align:center;">{{ $totals['total_students'] }}</td>
                    <td style="text-align:center;">{{ $totals['old_deleted'] }}</td>
                    <td style="text-align:center;">{{ $totals['new_deleted'] }}</td>
                    <td class="text-orange-highlight" style="text-align:center;">{{ $totals['active_students'] }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Table Pagination (above footer) -->
    <div style="background:#fff; padding:10px 20px; display:flex; align-items:center; justify-content:flex-end; border-top:1px solid var(--border);">
        <div style="display:flex; align-items:center; gap:12px; font-size:12px; color:var(--t2); font-weight:600;">
            <span>1-{{ count($reportData) }} of {{ count($reportData) }}</span>
            <div style="display:flex; gap:4px;">
                <button type="button" class="btn btn-outline" style="padding:2px 8px; font-size:10px; cursor:not-allowed;" disabled>
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button type="button" class="btn btn-outline" style="padding:2px 8px; font-size:10px; cursor:not-allowed;" disabled>
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Global Info Popover -->
<div id="ovInfoPopover" class="ov-popover">
    <div class="ov-popover-title">
        <i class="fas fa-info-circle"></i>
        <span id="ovPopTitle"></span>
    </div>
    <div id="ovPopBody"></div>
</div>

<!-- Dynamic Toast element -->
<div id="appToast"></div>

@endsection

@section('scripts')
<script>
    // Change view modes between SECTION and CLASS
    function setViewMode(mode) {
        document.getElementById('viewModeInput').value = mode;
        document.getElementById('overviewFilterForm').submit();
    }

    // Success Toast
    function showToast(message, isError = false) {
        let toast = document.getElementById('appToast');
        toast.innerText = message;
        toast.style.background = isError ? 'var(--red)' : 'var(--navy)';
        toast.style.borderLeft = isError ? '3px solid #ff8888' : '3px solid var(--gold)';
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    // Double-click inline class teacher editor
    $(document).on('dblclick', '.editable-teacher-cell', function() {
        let cell = $(this);
        if (cell.hasClass('editing')) return;

        cell.addClass('editing');
        let sectionId = cell.attr('data-section-id');
        let currentTeacherId = cell.attr('data-current-teacher-id');
        let teachersList = @json($teachers);

        let select = $('<select class="inline-edit-select"></select>');
        select.append('<option value="">Not Assigned</option>');

        teachersList.forEach(function(t) {
            let isSelected = t.id == currentTeacherId ? 'selected' : '';
            select.append('<option value="' + t.id + '" ' + isSelected + '>' + t.first_name + ' ' + t.last_name + '</option>');
        });

        cell.html(select);
        select.focus();

        // Handle Change/Blur save
        select.on('change blur', function(e) {
            // Prevent multiple saves on change followed by blur
            if (!cell.hasClass('editing')) return;
            cell.removeClass('editing');

            let selectedTeacherId = select.val();
            let selectedTeacherName = select.find('option:selected').text();

            $.ajax({
                url: '/school/assignments/sections/' + sectionId + '/class-teacher',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    class_teacher_id: selectedTeacherId
                },
                success: function(response) {
                    if (response.success) {
                        cell.html(response.teacher_name);
                        cell.attr('data-current-teacher-id', selectedTeacherId);
                        showToast(response.message);
                    } else {
                        restoreCell(cell, currentTeacherId, teachersList);
                        showToast('Error saving changes', true);
                    }
                },
                error: function() {
                    restoreCell(cell, currentTeacherId, teachersList);
                    showToast('Failed to connect to server', true);
                }
            });
        });
    });

    function restoreCell(cell, teacherId, teachersList) {
        let name = 'Not Assigned';
        if (teacherId) {
            let found = teachersList.find(t => t.id == teacherId);
            if (found) name = found.first_name + ' ' + found.last_name;
        }
        cell.html(name);
    }

    // CSV Download
    function downloadCSV() {
        let csv = [];
        let rows = document.querySelectorAll("#overviewGridTable tr");
        
        for (let i = 0; i < rows.length; i++) {
            let row = [], cols = rows[i].querySelectorAll("td, th");
            
            for (let j = 0; j < cols.length; j++) {
                // Clean text content
                let text = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, "").trim();
                text = text.replace(/"/g, '""'); // escape double quotes
                row.push('"' + text + '"');
            }
            csv.push(row.join(","));
        }

        // Add totals row
        let totalsRow = [];
        totalsRow.push('"Total"');
        totalsRow.push('""');
        totalsRow.push('""');
        totalsRow.push('""');
        totalsRow.push('""');
        totalsRow.push('"' + '{{ $totals['promoted'] }}' + '"');
        totalsRow.push('"' + '{{ $totals['repeated'] }}' + '"');
        totalsRow.push('"' + '{{ $totals['new_admissions'] }}' + '"');
        totalsRow.push('"' + '{{ $totals['today_admissions'] }}' + '"');
        totalsRow.push('"' + '{{ $totals['old_student_tc'] }}' + '"');
        totalsRow.push('"' + '{{ $totals['new_student_tc'] }}' + '"');
        totalsRow.push('"' + '{{ $totals['irregular'] }}' + '"');
        totalsRow.push('"' + '{{ $totals['deactivated'] }}' + '"');
        totalsRow.push('"' + '{{ $totals['total_students'] }}' + '"');
        totalsRow.push('"' + '{{ $totals['old_deleted'] }}' + '"');
        totalsRow.push('"' + '{{ $totals['new_deleted'] }}' + '"');
        totalsRow.push('"' + '{{ $totals['active_students'] }}' + '"');
        csv.push(totalsRow.join(","));

        let csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
        let downloadLink = document.createElement("a");
        downloadLink.download = "class_overview_report.csv";
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = "none";
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    }
    // ── Info Button Popovers ─────────────────────────────────────────────────
    const OV_INFO = {
        todayadmissions: {
            title: "Today's Admissions",
            body:  "Students who were newly admitted on today's date. This count resets daily and helps track day-level admission activity per class-section."
        },
        tcstudents: {
            title: "TC Students",
            body:  "Students who have been issued a Transfer Certificate (TC). Old Student TC refers to students who joined last year or before and left; New Student TC refers to students admitted this year who have already left."
        },
        irregular: {
            title: "Irregular Students",
            body:  "Active students flagged as highly irregular — those whose overall attendance rate for the current academic year falls below 75%. These students may need follow-up from the class teacher."
        },
        totalstudents: {
            title: "Total Students",
            body:  "The total number of registered students in this class-section, including both active and deactivated students. Excludes hard-deleted records."
        },
        deletedstudents: {
            title: "Deleted Students",
            body:  "Students who have been permanently removed from the active registry. Old Deleted refers to students from previous years; New Deleted refers to students admitted in the current academic year who were then deleted."
        }
    };

    let ovActiveTooltip = null;
    const ovPopover = document.getElementById('ovInfoPopover');

    document.querySelectorAll('.info-tooltip[data-info-key]').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const key = btn.getAttribute('data-info-key');
            const def = OV_INFO[key];
            if (!def) return;

            if (ovActiveTooltip === btn && ovPopover.classList.contains('visible')) {
                closeOvPopover(); return;
            }
            if (ovActiveTooltip) ovActiveTooltip.classList.remove('active');
            ovActiveTooltip = btn;
            btn.classList.add('active');

            document.getElementById('ovPopTitle').textContent = def.title;
            document.getElementById('ovPopBody').textContent  = def.body;

            const rect = btn.getBoundingClientRect();
            ovPopover.style.top  = (rect.bottom + 10 + window.scrollY) + 'px';
            ovPopover.style.left = Math.min(rect.left - 10, window.innerWidth - 285) + 'px';

            ovPopover.classList.remove('visible');
            void ovPopover.offsetWidth; // reflow
            ovPopover.classList.add('visible');
        });
    });

    function closeOvPopover() {
        ovPopover.classList.remove('visible');
        if (ovActiveTooltip) { ovActiveTooltip.classList.remove('active'); ovActiveTooltip = null; }
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.info-tooltip')) closeOvPopover();
    });
</script>
@endsection
