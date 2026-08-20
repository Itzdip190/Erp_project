<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Payment Disbursal Report — {{ $school?->name ?: 'School ERP' }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 20px;
            color: #1e293b;
            font-size: 11px;
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
            font-size: 13px;
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
            padding: 8px 6px;
            font-size: 10px;
            text-transform: uppercase;
            border: 1px solid #1e3a8a;
        }
        .table td {
            padding: 7px 6px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
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
            display: inline-block;
        }
        .badge-unpaid {
            background-color: #ffedd5;
            color: #c2410c;
            padding: 3px 8px;
            border-radius: 12px;
            font-weight: bold;
            display: inline-block;
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
                <div class="report-title">Salary Payment Disbursal Master Report</div>
                <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                    Month: {{ $month !== 'All' ? $month : 'All Months' }} {{ $year !== 'All' ? $year : 'All Years' }} | Generated: {{ date('d M Y, h:i A') }}
                </div>
            </td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th class="text-center" style="width: 35px;">S.No</th>
                <th style="width: 75px;">Month</th>
                <th style="width: 80px;">Emp ID</th>
                <th>Employee Name</th>
                <th>Department</th>
                <th>Designation</th>
                <th>Bank Name</th>
                <th>Account No</th>
                <th style="width: 70px;">IFSC Code</th>
                <th style="width: 70px;">PAN No</th>
                <th class="text-right" style="width: 75px;">Net Salary</th>
                <th class="text-center" style="width: 55px;">Status</th>
                <th style="width: 75px;">Payment Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $index => $row)
                @php 
                    $st = $row->staff; 
                    $lastPayment = $row->payments?->last();
                    $payDate = $lastPayment ? \Carbon\Carbon::parse($lastPayment->payment_date)->format('d M Y') : 'N/A';
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row->payroll_month }}</td>
                    <td><strong>{{ $st?->employee_id ?: 'EMP-'.$row->staff_id }}</strong></td>
                    <td><strong>{{ $st?->full_name ?: 'N/A' }}</strong></td>
                    <td>{{ $st?->department?->name ?: 'N/A' }}</td>
                    <td>{{ $st?->designation?->name ?: 'N/A' }}</td>
                    <td>{{ $st?->bank_name ?: 'N/A' }}</td>
                    <td>{{ $st?->bank_account_number ?: 'N/A' }}</td>
                    <td>{{ $st?->ifsc_code ?: 'N/A' }}</td>
                    <td>{{ $st?->pan_number ?: 'N/A' }}</td>
                    <td class="text-right"><strong>₹{{ number_format($row->net_payable, 2) }}</strong></td>
                    <td class="text-center">
                        @if(strtolower($row->payment_status) === 'paid')
                            <span class="badge-paid">Paid</span>
                        @else
                            <span class="badge-unpaid">Unpaid</span>
                        @endif
                    </td>
                    <td>{{ $payDate }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="text-center" style="padding: 20px; color: #94a3b8;">
                        No salary payment records found for the selected month.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <script>
        if (window.location.search.indexOf('autoprint=1') !== -1) {
            window.print();
        }
    </script>
</body>
</html>
