<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Daily Task Evaluation Report</title>
    <style>
        @page {
            margin: 20px 25px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        .school-name {
            font-size: 18px;
            font-weight: bold;
            color: #4f46e5;
            text-transform: uppercase;
        }
        .report-title {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 3px;
        }
        .meta-text {
            font-size: 10px;
            color: #64748b;
        }
        .kpi-table {
            width: 100%;
            margin-bottom: 14px;
            border-collapse: collapse;
        }
        .kpi-cell {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 8px 12px;
            text-align: center;
            width: 25%;
        }
        .kpi-val {
            font-size: 14px;
            font-weight: bold;
            color: #4f46e5;
        }
        .kpi-lbl {
            font-size: 9.5px;
            color: #64748b;
            text-transform: uppercase;
            margin-top: 2px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-weight: bold;
            font-size: 10px;
            padding: 6px 8px;
            text-align: left;
            border: 1px solid #334155;
            text-transform: uppercase;
        }
        .data-table td {
            padding: 5px 8px;
            font-size: 10px;
            border: 1px solid #e2e8f0;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-ct {
            background-color: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }
        .badge-st {
            background-color: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }
        .star-text {
            color: #d97706;
            font-weight: bold;
        }
        .footer-table {
            width: 100%;
            margin-top: 30px;
        }
        .sign-box {
            text-align: center;
            border-top: 1px solid #94a3b8;
            padding-top: 5px;
            font-size: 10px;
            font-weight: bold;
            color: #475569;
            width: 25%;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="width: 70%;">
                <div class="school-name">{{ $school ? $school->name : 'SCHOOL ERP' }}</div>
                <div class="report-title">Daily Task Student Evaluation Report</div>
                <div class="meta-text">
                    {{ $school ? $school->address : '' }} {{ $school && $school->phone ? ' | Phone: ' . $school->phone : '' }}
                </div>
            </td>
            <td style="width: 30%; text-align: right; vertical-align: bottom;">
                <div class="meta-text"><strong>Period:</strong> {{ date('d M Y', strtotime($fromDate)) }} to {{ date('d M Y', strtotime($toDate)) }}</div>
                <div class="meta-text"><strong>Generated:</strong> {{ date('d M Y, h:i A') }}</div>
            </td>
        </tr>
    </table>

    <!-- KPIs -->
    <table class="kpi-table">
        <tr>
            <td class="kpi-cell">
                <div class="kpi-val">{{ $stats['total_evaluations'] }}</div>
                <div class="kpi-lbl">Total Tasks Evaluated</div>
            </td>
            <td class="kpi-cell">
                <div class="kpi-val">{{ $stats['avg_rating'] }} / 5.0</div>
                <div class="kpi-lbl">Average Star Rating</div>
            </td>
            <td class="kpi-cell">
                <div class="kpi-val">{{ $stats['total_5_stars'] }}</div>
                <div class="kpi-lbl">5-Star Perfect Ratings</div>
            </td>
            <td class="kpi-cell">
                <div class="kpi-val">{{ $stats['unique_students'] }}</div>
                <div class="kpi-lbl">Students Reviewed</div>
            </td>
        </tr>
    </table>

    <!-- Detailed Evaluation Logs -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 70px;">Date</th>
                <th>Student Details</th>
                <th>Class & Sec</th>
                <th>Mode</th>
                <th>Task / Criteria</th>
                <th style="text-align: center; width: 75px;">Rating / Score</th>
                <th>Teacher Remarks</th>
                <th>Evaluator</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $rec)
                <tr>
                    <td>{{ date('d-m-Y', strtotime($rec->date)) }}</td>
                    <td>
                        <strong>{{ $rec->student_first_name }} {{ $rec->student_last_name }}</strong><br>
                        <span style="color: #64748b; font-size: 9px;">Roll: {{ $rec->student_roll_no ?: '-' }} | Adm: {{ $rec->student_admission_no ?: '-' }}</span>
                    </td>
                    <td>{{ $rec->class_name }} - {{ $rec->section_name }}</td>
                    <td>
                        @if($rec->review_type === 'class_teacher')
                            <span class="badge badge-ct">Class Teacher</span>
                        @else
                            <span class="badge badge-st">{{ $rec->subject_name ?: 'Subject' }}</span>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $rec->question_text }}</strong>
                        @if($rec->head_name)
                            <br><span style="color: #64748b; font-size: 9px;">Category: {{ $rec->head_name }}</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if($rec->rating)
                            <span class="star-text">{{ $rec->rating }} ★</span>
                        @elseif($rec->score !== null)
                            <strong>{{ $rec->score }}</strong>/{{ $rec->max_score ?? 10 }}
                        @elseif($rec->status_option)
                            {{ $rec->status_option }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $rec->remarks ?: ($rec->review_overall_remarks ?: '-') }}</td>
                    <td>{{ trim(($rec->teacher_first_name ?? '') . ' ' . ($rec->teacher_last_name ?? '')) ?: 'Staff' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px; color: #64748b;">
                        No daily task evaluation records found for the selected period.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signatures -->
    <table class="footer-table">
        <tr>
            <td class="sign-box">Prepared By</td>
            <td style="width: 12%;"></td>
            <td class="sign-box">Class / Subject Teacher</td>
            <td style="width: 12%;"></td>
            <td class="sign-box">Principal / School Stamp</td>
        </tr>
    </table>
</body>
</html>
