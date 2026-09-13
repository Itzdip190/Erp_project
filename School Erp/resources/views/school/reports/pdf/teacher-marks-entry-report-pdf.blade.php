<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Teacher Marks Entry Report - {{ $selectedExam ?: 'All Exams' }}</title>
    <style>
        @page {
            margin: 22px 24px 30px 24px;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            color: #1e293b;
            line-height: 1.25;
            margin: 0;
            padding: 0;
        }

        /* Header */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            border-bottom: 2px solid #4338ca;
            padding-bottom: 8px;
        }
        .header-logo {
            width: 55px;
            height: auto;
            max-height: 55px;
        }
        .school-name {
            font-size: 17px;
            font-weight: bold;
            color: #1e1b4b;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .school-info {
            font-size: 8.5px;
            color: #64748b;
            margin-top: 2px;
        }

        /* Title Box */
        .report-title-box {
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            border-radius: 6px;
            padding: 7px 12px;
            margin-bottom: 12px;
        }
        .report-title {
            font-size: 13px;
            font-weight: bold;
            color: #3730a3;
            margin: 0 0 4px 0;
            text-transform: uppercase;
        }
        .meta-grid {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
            color: #475569;
        }
        .meta-grid td {
            padding: 1px 0;
        }

        /* KPI Bar */
        .kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .kpi-cell {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 5px 6px;
            text-align: center;
        }
        .kpi-lbl {
            font-size: 7.5px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
        }
        .kpi-val {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 2px;
        }

        /* Section Headings */
        .section-hdr {
            font-size: 10.5px;
            font-weight: bold;
            color: #312e81;
            margin: 12px 0 6px 0;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-bottom: 1.5px solid #e0e7ff;
            padding-bottom: 3px;
        }

        /* Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 8.5px;
            table-layout: fixed;
        }
        .data-table tr {
            page-break-inside: avoid;
        }
        .data-table th {
            background: #312e81;
            color: #ffffff;
            font-weight: bold;
            padding: 5px 5px;
            text-align: left;
            border: 1px solid #1e1b4b;
            text-transform: uppercase;
            font-size: 7.5px;
            word-wrap: break-word;
        }
        .data-table td {
            padding: 4px 5px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
            word-wrap: break-word;
        }
        .data-table tr:nth-child(even) td {
            background: #f8fafc;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-completed {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-in_progress {
            background: #fef3c7;
            color: #92400e;
        }
        .badge-pending {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Signatures */
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .sig-line {
            border-top: 1px solid #94a3b8;
            width: 80%;
            margin-top: 35px;
            margin-bottom: 4px;
        }
        .sig-text {
            font-size: 8.5px;
            color: #475569;
            font-weight: bold;
        }

        .footer-note {
            font-size: 8px;
            color: #94a3b8;
            text-align: center;
            margin-top: 10px;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <table class="header-table">
        <tr>
            @if(!empty($school->logo) && file_exists(public_path('storage/' . $school->logo)))
                <td style="width: 60px; vertical-align: middle;">
                    <img src="{{ public_path('storage/' . $school->logo) }}" class="header-logo" alt="Logo">
                </td>
            @endif
            <td style="vertical-align: middle;">
                <h1 class="school-name">{{ $school->name ?? 'School ERP System' }}</h1>
                <div class="school-info">
                    @if(!empty($school->address)) {{ $school->address }} @endif
                    @if(!empty($school->phone)) | Phone: {{ $school->phone }} @endif
                    @if(!empty($school->email)) | Email: {{ $school->email }} @endif
                    @if(!empty($school->affiliation_number)) | Affiliation No: {{ $school->affiliation_number }} @endif
                </div>
            </td>
            <td style="text-align: right; vertical-align: middle; width: 180px;">
                <div style="font-size: 8px; color: #64748b;">Generated On:</div>
                <div style="font-size: 9.5px; font-weight: bold; color: #1e293b;">{{ now()->format('d M Y, h:i A') }}</div>
                <div style="font-size: 8px; color: #64748b; margin-top: 2px;">Session: {{ $sessionName ?? 'Current' }}</div>
            </td>
        </tr>
    </table>

    {{-- TITLE BOX --}}
    <div class="report-title-box">
        <h2 class="report-title">Teacher Marks Entry Report - Progress & Submission Summary</h2>
        <table class="meta-grid">
            <tr>
                <td style="width: 25%;"><strong>Examination:</strong> {{ $selectedExam ?: 'All Examinations' }}</td>
                <td style="width: 25%;"><strong>Assessment:</strong> {{ $selectedAssessment ?: 'All Assessments' }}</td>
                <td style="width: 20%;"><strong>Academic Session:</strong> {{ $sessionName }}</td>
                <td style="width: 30%;"><strong>Filters Applied:</strong> {{ $filterSummary ?? 'All Records' }}</td>
            </tr>
        </table>
    </div>

    {{-- KPI BAR --}}
    <table class="kpi-table">
        <tr>
            <td class="kpi-cell" style="width: 12.5%;">
                <div class="kpi-lbl">Allocations</div>
                <div class="kpi-val">{{ $kpis['total_allocations'] }}</div>
            </td>
            <td class="kpi-cell" style="width: 12.5%;">
                <div class="kpi-lbl" style="color: #059669;">Completed</div>
                <div class="kpi-val" style="color: #059669;">{{ $kpis['completed_allocations'] }}</div>
            </td>
            <td class="kpi-cell" style="width: 12.5%;">
                <div class="kpi-lbl" style="color: #d97706;">In Progress</div>
                <div class="kpi-val" style="color: #d97706;">{{ $kpis['in_progress_allocations'] }}</div>
            </td>
            <td class="kpi-cell" style="width: 12.5%;">
                <div class="kpi-lbl" style="color: #dc2626;">Not Started</div>
                <div class="kpi-val" style="color: #dc2626;">{{ $kpis['pending_allocations'] }}</div>
            </td>
            <td class="kpi-cell" style="width: 12.5%;">
                <div class="kpi-lbl">Enrolled Students</div>
                <div class="kpi-val">{{ number_format($kpis['total_students']) }}</div>
            </td>
            <td class="kpi-cell" style="width: 12.5%;">
                <div class="kpi-lbl">Marks Entered</div>
                <div class="kpi-val" style="color: #059669;">{{ number_format($kpis['total_entered']) }}</div>
            </td>
            <td class="kpi-cell" style="width: 12.5%;">
                <div class="kpi-lbl">Completion %</div>
                <div class="kpi-val" style="color: #3730a3;">{{ $kpis['completion_rate'] }}%</div>
            </td>
            <td class="kpi-cell" style="width: 12.5%;">
                <div class="kpi-lbl">Overall Avg</div>
                <div class="kpi-val" style="color: #7e22ce;">{{ $kpis['overall_avg_marks'] }}</div>
            </td>
        </tr>
    </table>

    {{-- SECTION 1: TEACHER MARKS ENTRY PROGRESS SUMMARY --}}
    <div class="section-hdr">1. Teacher Submission Progress Overview</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 22px; text-align: center;">#</th>
                <th>Teacher Name & Assigned Role</th>
                <th style="width: 65px;">Class & Sec</th>
                <th>Subject</th>
                <th style="width: 80px;">Exam Name</th>
                <th style="width: 80px;">Assessment</th>
                <th style="width: 45px; text-align: center;">Total</th>
                <th style="width: 45px; text-align: center;">Entered</th>
                <th style="width: 45px; text-align: center;">Pending</th>
                <th style="width: 55px; text-align: center;">Completion</th>
                <th style="width: 60px; text-align: center;">Status</th>
                <th style="width: 70px; text-align: right;">Avg / Max</th>
            </tr>
        </thead>
        <tbody>
            @forelse($allocations as $idx => $alloc)
                @php
                    $badgeClass = $alloc['status'] === 'completed' ? 'badge-completed' : ($alloc['status'] === 'in_progress' ? 'badge-in_progress' : 'badge-pending');
                    $statusName = $alloc['status'] === 'completed' ? 'Completed' : ($alloc['status'] === 'in_progress' ? 'In Progress' : 'Pending');
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $alloc['teacher_name'] }}</strong>
                        <div style="font-size: 7.5px; color: #64748b;">Role: {{ $alloc['teacher_role'] }}</div>
                    </td>
                    <td>{{ $alloc['class_name'] }} - {{ $alloc['section_name'] }}</td>
                    <td>
                        {{ $alloc['subject_name'] }}
                        @if(!empty($alloc['subject_code']))
                            <span style="color: #64748b; font-size: 7.5px;">({{ $alloc['subject_code'] }})</span>
                        @endif
                    </td>
                    <td>{{ $alloc['exam_name'] }}</td>
                    <td>{{ $alloc['assessment_name'] ?? ($selectedAssessment ?: 'All Assessments') }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $alloc['total_students'] }}</td>
                    <td style="text-align: center; color: #059669; font-weight: bold;">{{ $alloc['entered_count'] }}</td>
                    <td style="text-align: center; color: {{ $alloc['pending_count'] > 0 ? '#dc2626' : '#64748b' }}; font-weight: bold;">
                        {{ $alloc['pending_count'] }}
                    </td>
                    <td style="text-align: center; font-weight: bold;">{{ $alloc['completion_pct'] }}%</td>
                    <td style="text-align: center;">
                        <span class="badge {{ $badgeClass }}">{{ $statusName }}</span>
                    </td>
                    <td style="text-align: right;">
                        @if($alloc['entered_count'] > 0)
                            <strong>{{ $alloc['avg_marks'] }}</strong> / {{ $alloc['max_marks_total'] }}
                        @else
                            <span style="color: #94a3b8;">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" style="text-align: center; padding: 15px; color: #64748b;">No allocations found matching the criteria.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- SECTION 2: STUDENT MARKS BREAKDOWN --}}
    @if($studentRows->count() > 0)
        <div style="page-break-before: auto; margin-top: 15px;">
            <div class="section-hdr">2. Student-Wise Marks Entered Ledger ("Kitna Kitna Marks Enter Kiya")</div>
            @if(!empty($isCapped))
                <div style="margin: 4px 0 8px 0; padding: 4px 8px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 3px; font-size: 7.5px; color: #92400e;">
                    <strong>Notice:</strong> Showing top {{ $pdfLimit }} student records out of {{ number_format($totalStudentRowsCount) }} entries for instant PDF rendering. Please use <strong>Excel Export</strong> for complete unlimited dataset download.
                </div>
            @endif
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 22px; text-align: center;">#</th>
                        <th style="width: 38px;">Roll No</th>
                        <th style="width: 48px;">Adm No</th>
                        <th>Student Name</th>
                        <th style="width: 65px;">Class/Sec</th>
                        <th>Subject</th>
                        <th>Teacher</th>
                        <th style="width: 70px;">Exam</th>
                        <th style="width: 70px;">Assessment</th>
                        <th style="width: 42px; text-align: right;">Marks</th>
                        <th style="width: 32px; text-align: right;">Max</th>
                        <th style="width: 38px; text-align: center;">%</th>
                        <th style="width: 32px; text-align: center;">Grade</th>
                        <th style="width: 48px; text-align: center;">Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($studentRows as $sIdx => $st)
                        <tr>
                            <td style="text-align: center;">{{ $sIdx + 1 }}</td>
                            <td style="font-weight: bold; color: #4338ca;">{{ $st['roll_no'] }}</td>
                            <td style="color: #64748b;">{{ $st['admission_no'] }}</td>
                            <td><strong>{{ $st['student_name'] }}</strong></td>
                            <td>{{ $st['class_name'] }}-{{ $st['section_name'] }}</td>
                            <td>{{ $st['subject_name'] }}</td>
                            <td>{{ $st['teacher_name'] }}</td>
                            <td>{{ $st['exam_name'] ?? ($selectedExam ?: 'All Exams') }}</td>
                            <td>{{ $st['assessment_name'] ?: ($selectedAssessment ?: 'All Assessments') }}</td>
                            <td style="text-align: right; font-weight: bold;">
                                @if($st['is_entered'])
                                    <span style="color: #059669;">{{ number_format($st['marks_obtained'], 2) }}</span>
                                @else
                                    <span style="color: #dc2626; font-style: italic;">Pending</span>
                                @endif
                            </td>
                            <td style="text-align: right; color: #64748b;">{{ number_format($st['max_marks'], 0) }}</td>
                            <td style="text-align: center; font-weight: bold;">
                                @if($st['is_entered'] && $st['percentage'] !== null)
                                    {{ $st['percentage'] }}%
                                @else
                                    —
                                @endif
                            </td>
                            <td style="text-align: center; font-weight: bold;">{{ $st['grade'] }}</td>
                            <td style="text-align: center;">
                                @if(strtolower($st['attendance_status']) === 'present')
                                    <span style="color: #059669; font-weight: bold;">Present</span>
                                @elseif(strtolower($st['attendance_status']) === 'absent')
                                    <span style="color: #dc2626; font-weight: bold;">Absent</span>
                                @else
                                    <span style="color: #64748b;">{{ ucfirst($st['attendance_status']) }}</span>
                                @endif
                            </td>
                            <td style="color: #64748b;">{{ $st['remarks'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- SIGNATURES --}}
    <table class="sig-table">
        <tr>
            <td style="width: 33.33%; text-align: center;">
                <div class="sig-line"></div>
                <div class="sig-text">Prepared By</div>
            </td>
            <td style="width: 33.33%; text-align: center;">
                <div class="sig-line"></div>
                <div class="sig-text">Examination Incharge / Controller</div>
            </td>
            <td style="width: 33.33%; text-align: center;">
                <div class="sig-line"></div>
                <div class="sig-text">Principal / Authorized Signatory</div>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        This is a computer-generated document from {{ $school->name ?? 'School ERP' }} &bull; Confirmed Marks Entry Audit Report &bull; Page 1 of 1
    </div>

</body>
</html>
