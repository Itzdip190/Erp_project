<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Attendance Verification Report — {{ $school?->name ?: 'School ERP' }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 15px;
            color: #1e293b;
            font-size: 10px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .school-title {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 3px;
        }
        .report-title {
            font-size: 12px;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .meta-text {
            font-size: 9.5px;
            color: #64748b;
        }
        .kpi-summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
        }
        .kpi-summary-table td {
            padding: 8px 10px;
            text-align: center;
            border-right: 1px solid #e2e8f0;
        }
        .kpi-summary-table td:last-child {
            border-right: none;
        }
        .kpi-num {
            font-size: 14px;
            font-weight: bold;
            color: #1e3a8a;
        }
        .kpi-label {
            font-size: 8.5px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
        }
        .dept-title-box {
            background: #1e40af;
            color: #ffffff;
            font-weight: bold;
            font-size: 11px;
            padding: 6px 10px;
            margin-top: 15px;
            margin-bottom: 4px;
            border-radius: 4px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .table th {
            background-color: #f1f5f9;
            color: #1e40af;
            font-weight: bold;
            text-align: center;
            padding: 6px 4px;
            font-size: 8.5px;
            text-transform: uppercase;
            border: 1px solid #cbd5e1;
        }
        .table td {
            padding: 5px 4px;
            border: 1px solid #e2e8f0;
            font-size: 8.5px;
            text-align: center;
        }
        .table td.text-left {
            text-align: left;
        }
        .table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge {
            padding: 2px 5px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 8px;
            display: inline-block;
        }
        .badge-present { background: #dcfce7; color: #166534; }
        .badge-absent { background: #fee2e2; color: #991b1b; }
        .badge-leave { background: #f3e8ff; color: #6b21a8; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 15px;">
        <button onclick="window.print()" style="background: #1e3a8a; color: white; border: none; padding: 8px 16px; font-weight: bold; border-radius: 4px; cursor: pointer;">
            Print / Save as PDF
        </button>
    </div>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="width: 70%;">
                <div class="school-title">{{ $school?->name ?: 'SCHOOL ERP' }}</div>
                <div class="report-title">PAYROLL ATTENDANCE VERIFICATION REPORT</div>
                <div class="meta-text">{{ $school?->address ?: 'School Campus' }} | {{ $school?->phone ?: '' }}</div>
            </td>
            <td style="width: 30%; text-align: right;" class="meta-text">
                <div><strong>Cycle Period:</strong> {{ $data['date_range_display'] }}</div>
                <div><strong>Payroll Month:</strong> {{ $data['payroll_month'] }}</div>
                <div><strong>Generated:</strong> {{ $generatedDate }}</div>
            </td>
        </tr>
    </table>

    <!-- Overall Summary Box -->
    <table class="kpi-summary-table">
        <tr>
            <td>
                <div class="kpi-num">{{ $data['global_kpi']['total_employees'] }}</div>
                <div class="kpi-label">Total Staff</div>
            </td>
            <td>
                <div class="kpi-num" style="color: #16a34a;">{{ $data['global_kpi']['present_employees'] }}</div>
                <div class="kpi-label">Regular Active</div>
            </td>
            <td>
                <div class="kpi-num" style="color: #9333ea;">{{ $data['global_kpi']['employees_on_leave'] }}</div>
                <div class="kpi-label">On Leave</div>
            </td>
            <td>
                <div class="kpi-num" style="color: #dc2626;">{{ $data['global_kpi']['employees_absent'] }}</div>
                <div class="kpi-label">Recorded Absences</div>
            </td>
            <td>
                <div class="kpi-num" style="color: #0284c7;">{{ $data['global_kpi']['average_attendance_pct'] }}%</div>
                <div class="kpi-label">Average Attendance</div>
            </td>
            <td>
                <div class="kpi-num">{{ $data['days_in_month'] }} Days</div>
                <div class="kpi-label">Working: {{ $data['month_working_days'] }} | Sun: {{ $data['month_sundays'] }} | Hol: {{ $data['month_holidays'] }}</div>
            </td>
        </tr>
    </table>

    <!-- Department Sections -->
    @foreach($data['department_cards'] as $deptCard)
        <div class="dept-title-box">
            DEPARTMENT: {{ strtoupper($deptCard['name']) }} (Total Staff: {{ $deptCard['total_staff'] }} | Avg Attendance: {{ $deptCard['avg_attendance_pct'] }}%)
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 25px;">#</th>
                    <th class="text-left" style="width: 140px;">Employee Name</th>
                    <th style="width: 65px;">Emp ID</th>
                    <th class="text-left" style="width: 85px;">Designation</th>
                    <th style="width: 40px;">Present</th>
                    <th style="width: 40px;">Absent</th>
                    <th style="width: 40px;">Holidays</th>
                    <th style="width: 40px;">Half Days</th>
                    <th style="width: 50px;">Paid Leaves</th>
                    <th style="width: 50px;">Unpaid Leaves</th>
                    <th style="width: 55px;">Work Days</th>
                    <th style="width: 50px;">Att. %</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deptCard['staff_rows'] as $idx => $row)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td class="text-left"><strong>{{ $row['name'] }}</strong></td>
                        <td>{{ $row['employee_id'] }}</td>
                        <td class="text-left">{{ $row['designation'] }}</td>
                        <td><span class="badge badge-present">{{ $row['present_days'] }}</span></td>
                        <td><span class="badge badge-absent">{{ $row['absent_days'] }}</span></td>
                        <td>{{ $row['holidays'] }}</td>
                        <td>{{ $row['half_days'] }}</td>
                        <td><span class="badge badge-leave">{{ $row['paid_leaves'] }}</span></td>
                        <td>{{ $row['unpaid_leaves'] }}</td>
                        <td>{{ $row['working_days'] }} / {{ $row['total_days'] }}</td>
                        <td><strong>{{ $row['attendance_pct'] }}%</strong></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="text-center" style="padding: 10px; color: #94a3b8;">
                            No staff records in this department.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endforeach

    <div style="margin-top: 25px; border-top: 1px solid #cbd5e1; padding-top: 8px; font-size: 8.5px; color: #64748b;" class="meta-text">
        <table style="width: 100%;">
            <tr>
                <td>Verified by School HR / Administrator: ___________________________</td>
                <td style="text-align: right;">Signature / Stamp: ___________________________</td>
            </tr>
        </table>
    </div>
</body>
</html>
