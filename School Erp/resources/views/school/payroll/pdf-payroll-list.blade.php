<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll List Report — {{ $school?->name ?: 'School ERP' }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 20px;
            color: #1e293b;
            font-size: 12px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .school-title {
            font-size: 20px;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 4px;
        }
        .report-title {
            font-size: 14px;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .table th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 8px 10px;
            font-size: 11px;
            text-transform: uppercase;
        }
        .table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }
        .table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge-paid {
            background-color: #dcfce7;
            color: #166534;
            padding: 3px 8px;
            border-radius: 12px;
            font-weight: bold;
        }
        .badge-unpaid {
            background-color: #ffedd5;
            color: #c2410c;
            padding: 3px 8px;
            border-radius: 12px;
            font-weight: bold;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="background: #1e3a8a; color: white; border: none; padding: 10px 20px; font-weight: bold; border-radius: 6px; cursor: pointer;">
            Print / Save as PDF
        </button>
    </div>

    <table class="header-table">
        <tr>
            <td>
                <div class="school-title">{{ $school?->name ?: 'School ERP' }}</div>
                <div class="report-title">Payroll List Master Report</div>
                <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                    Filter: {{ $month !== 'All' ? $month : 'All Months' }} {{ $year !== 'All' ? $year : 'All Years' }} | Generated: {{ date('d M Y, h:i A') }}
                </div>
            </td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th class="text-center">S.No</th>
                <th>Employee ID</th>
                <th>Employee Name</th>
                <th>Department</th>
                <th>Designation</th>
                <th>Salary Month</th>
                <th class="text-right">Gross Salary</th>
                <th class="text-right">Att. Ded.</th>
                <th class="text-right">Total Ded.</th>
                <th class="text-right">Net Salary</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $index => $row)
                @php $st = $row->staff; @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $st?->employee_id ?: 'EMP-'.$row->staff_id }}</strong></td>
                    <td>{{ $st?->full_name ?: 'N/A' }}</td>
                    <td>{{ $st?->department?->name ?: 'N/A' }}</td>
                    <td>{{ $st?->designation?->name ?: 'N/A' }}</td>
                    <td>{{ $row->payroll_month }}</td>
                    <td class="text-right">₹{{ number_format($row->gross_salary, 2) }}</td>
                    <td class="text-right" style="color: #c2410c;">₹{{ number_format($row->attendance_deduction ?: 0, 2) }}</td>
                    <td class="text-right">₹{{ number_format($row->deductions, 2) }}</td>
                    <td class="text-right"><strong>₹{{ number_format($row->net_payable, 2) }}</strong></td>
                    <td class="text-center">
                        @if(strtolower($row->payment_status) === 'paid')
                            <span class="badge-paid">Paid</span>
                        @else
                            <span class="badge-unpaid">Unpaid</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 20px; color: #94a3b8;">
                        No payroll records found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <script>
        // Auto trigger print dialog if requested
        if (window.location.search.indexOf('autoprint=1') !== -1) {
            window.print();
        }
    </script>
</body>
</html>
