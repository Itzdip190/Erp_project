<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Staff Directory Report</title>
    <style>
        @page {
            margin: 15px;
            size: a4 landscape;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9px;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .header-banner {
            background: linear-gradient(135deg, #1e293b 0%, #1d4ed8 100%);
            color: #ffffff;
            padding: 12px 18px;
            border-radius: 6px;
            margin-bottom: 12px;
        }
        .header-banner table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-title {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .header-sub {
            font-size: 10px;
            color: #93c5fd;
            margin-top: 2px;
        }
        .meta-text {
            text-align: right;
            font-size: 9px;
            color: #cbd5e1;
        }
        .summary-bar {
            margin-bottom: 12px;
            width: 100%;
            border-collapse: collapse;
        }
        .summary-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 6px 10px;
            text-align: center;
        }
        .summary-num {
            font-size: 13px;
            font-weight: bold;
            color: #1d4ed8;
        }
        .summary-label {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
        }
        table.data-tbl {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }
        table.data-tbl th {
            background-color: #1d4ed8;
            color: #ffffff;
            font-weight: bold;
            padding: 6px 5px;
            border: 1px solid #1e40af;
            text-align: left;
            white-space: nowrap;
        }
        table.data-tbl td {
            padding: 5px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        table.data-tbl tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 7.5px;
            font-weight: bold;
            text-align: center;
        }
        .badge-success {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }
        .badge-danger {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }
        .emp-id {
            font-weight: bold;
            color: #1d4ed8;
        }
        .footer {
            margin-top: 10px;
            font-size: 8px;
            color: #94a3b8;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }
    </style>
</head>
<body>

    <div class="header-banner">
        <table>
            <tr>
                <td>
                    <div class="header-title">{{ mb_strtoupper($school->name ?? 'School Cloud ERP') }}</div>
                    <div class="header-sub">Official Staff Directory Export Report</div>
                </td>
                <td class="meta-text">
                    <div>Generated: {{ date('d M Y, h:i A') }}</div>
                    <div>Total Records: {{ count($staffList) }}</div>
                </td>
            </tr>
        </table>
    </div>

    @php
        $totalStaff = count($staffList);
        $activeStaff = $staffList->where('is_active', true)->count();
        $inactiveStaff = $totalStaff - $activeStaff;
        $teachingCount = $staffList->filter(fn($s) => $s->staff_type === 'Teaching')->count();
    @endphp

    <table class="summary-bar">
        <tr>
            <td width="24%">
                <div class="summary-card">
                    <div class="summary-num">{{ $totalStaff }}</div>
                    <div class="summary-label">Total Staff</div>
                </div>
            </td>
            <td width="1%"></td>
            <td width="24%">
                <div class="summary-card">
                    <div class="summary-num" style="color:#15803d;">{{ $activeStaff }}</div>
                    <div class="summary-label">Active Members</div>
                </div>
            </td>
            <td width="1%"></td>
            <td width="24%">
                <div class="summary-card">
                    <div class="summary-num" style="color:#b91c1c;">{{ $inactiveStaff }}</div>
                    <div class="summary-label">Inactive Members</div>
                </div>
            </td>
            <td width="1%"></td>
            <td width="24%">
                <div class="summary-card">
                    <div class="summary-num" style="color:#7c3aed;">{{ $teachingCount }}</div>
                    <div class="summary-label">Teaching Staff</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="data-tbl">
        <thead>
            <tr>
                <th width="3%">#</th>
                <th width="7%">Emp ID</th>
                <th width="12%">Full Name</th>
                <th width="12%">Email / Phone</th>
                <th width="9%">Dept</th>
                <th width="9%">Designation</th>
                <th width="7%">Staff Type</th>
                <th width="7%">Joining</th>
                <th width="7%">DOB</th>
                <th width="6%">Gender</th>
                <th width="9%">Qualification</th>
                <th width="7%">Basic Pay</th>
                <th width="5%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($staffList as $index => $staff)
                @php $af = $staff->additional_fields ?? []; @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="emp-id">{{ $staff->employee_id }}</td>
                    <td>
                        <strong>{{ $staff->full_name }}</strong>
                        @if(!empty($af['aadhar_number']))
                            <div style="font-size:7px; color:#64748b;">Aadhar: {{ $af['aadhar_number'] }}</div>
                        @endif
                    </td>
                    <td>
                        <div>{{ $staff->email }}</div>
                        <div style="font-size:7.5px; color:#475569;">{{ $staff->phone ?? '—' }}</div>
                    </td>
                    <td>{{ optional($staff->department)->name ?? 'N/A' }}</td>
                    <td>{{ optional($staff->designation)->name ?? 'N/A' }}</td>
                    <td>{{ $staff->staff_type }}</td>
                    <td>{{ $staff->joining_date ? $staff->joining_date->format('d/m/Y') : '—' }}</td>
                    <td>{{ $staff->date_of_birth ? $staff->date_of_birth->format('d/m/Y') : '—' }}</td>
                    <td>{{ ucfirst($staff->gender ?? '—') }}</td>
                    <td>{{ $staff->qualification ?? '—' }}</td>
                    <td>₹{{ number_format($staff->basic_salary ?? 0, 2) }}</td>
                    <td style="text-align:center;">
                        <span class="badge {{ $staff->is_active ? 'badge-success' : 'badge-danger' }}">
                            {{ $staff->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" style="text-align:center; padding:15px; color:#64748b;">No staff records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Confidential — {{ $school->name ?? 'School Cloud ERP' }} Staff Management System | Page 1
    </div>

</body>
</html>
