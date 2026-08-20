<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Statement — {{ $school?->name ?: 'School ERP' }}</title>
    <style>
        @page {
            margin: 15px 20px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.4;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 10px;
        }
        .school-title {
            font-size: 20px;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 3px;
            text-transform: uppercase;
        }
        .report-title {
            font-size: 13px;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .meta-info {
            font-size: 10.5px;
            color: #64748b;
            margin-top: 4px;
        }
        
        .summary-box {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .summary-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
            text-align: center;
        }
        .summary-label {
            font-size: 9px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
        }
        .summary-val {
            font-size: 14px;
            font-weight: bold;
            color: #1e3a8a;
            margin-top: 2px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .table th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 8px 10px;
            font-size: 10px;
            text-transform: uppercase;
            border: 1px solid #1e3a8a;
        }
        .table td {
            padding: 7px 10px;
            border: 1px solid #e2e8f0;
            font-size: 10.5px;
            vertical-align: middle;
        }
        .table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-bank { background-color: #dbeafe; color: #1e40af; }
        .badge-cash { background-color: #dcfce7; color: #166534; }
        .badge-other { background-color: #f3e8ff; color: #6b21a8; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .text-danger { color: #dc2626; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td>
                <div class="school-title">{{ $school?->name ?: 'SCHOOL ERP MANAGEMENT SYSTEM' }}</div>
                <div class="report-title">
                    Payroll Financial Account Statement
                    @if($selectedStaff)
                        &mdash; {{ $selectedStaff->full_name }} ({{ $selectedStaff->employee_id ?: 'EMP-'.$selectedStaff->id }})
                    @endif
                </div>
                <div class="meta-info">
                    @if($fromDate || $toDate || $month || $year)
                        Filter Range: 
                        @if($fromDate) From {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} @endif
                        @if($toDate) To {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }} @endif
                        @if($month) Month: {{ is_numeric($month) ? date('F', mktime(0, 0, 0, (int)$month, 10)) : $month }} @endif
                        @if($year) Year: {{ $year }} @endif
                        | 
                    @endif
                    Generated Date: {{ $generatedDate }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Summary Metrics Bar -->
    <table class="summary-box">
        <tr>
            <td style="width: 25%; padding-right: 5px;">
                <div class="summary-card">
                    <div class="summary-label">Total Outflow</div>
                    <div class="summary-val text-danger">₹{{ number_format($totalDisbursed, 2) }}</div>
                </div>
            </td>
            <td style="width: 25%; padding: 0 5px;">
                <div class="summary-card">
                    <div class="summary-label">Bank Transfers</div>
                    <div class="summary-val">₹{{ number_format($bankDisbursed, 2) }}</div>
                </div>
            </td>
            <td style="width: 25%; padding: 0 5px;">
                <div class="summary-card">
                    <div class="summary-label">Cash Disbursals</div>
                    <div class="summary-val" style="color: #166534;">₹{{ number_format($cashDisbursed, 2) }}</div>
                </div>
            </td>
            <td style="width: 25%; padding-left: 5px;">
                <div class="summary-card">
                    <div class="summary-label">Cheque / UPI / Digital</div>
                    <div class="summary-val" style="color: #6b21a8;">₹{{ number_format($chequeUpiDisbursed, 2) }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Statement Table -->
    <table class="table">
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">S.No</th>
                <th style="width: 12%;">Date</th>
                <th style="width: 15%;">Reference / Voucher</th>
                <th style="width: 25%;">Staff Beneficiary</th>
                <th style="width: 15%;">Department</th>
                <th style="width: 13%;">Disbursal Type</th>
                <th style="width: 15%;">Payment Channel</th>
                <th class="text-right" style="width: 15%;">Debit Outflow (₹)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($statementList as $index => $item)
                @php
                    $st = $item->staff;
                    $method = strtolower($item->payment_method);
                    $badgeClass = ($method === 'bank_transfer' || $method === 'bank') ? 'badge-bank' : (($method === 'cash') ? 'badge-cash' : 'badge-other');
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->payment_date)->format('d M, Y') }}</td>
                    <td><code>{{ $item->reference_no ?: '#PAY-'.str_pad($item->id, 5, '0', STR_PAD_LEFT) }}</code></td>
                    <td>
                        <div class="fw-bold">{{ $st?->full_name ?: 'N/A' }}</div>
                        <div style="font-size: 9.5px; color: #64748b;">{{ $st?->employee_id ?: 'EMP-'.$item->staff_id }}</div>
                    </td>
                    <td>{{ $st?->department?->name ?: 'General' }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $item->payment_type)) }}</td>
                    <td>
                        <span class="badge {{ $badgeClass }}">
                            {{ strtoupper(str_replace('_', ' ', $item->payment_method)) }}
                        </span>
                    </td>
                    <td class="text-right fw-bold text-danger">
                        -₹{{ number_format($item->amount, 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 25px; color: #94a3b8;">
                        No account statement transactions found for the selected criteria.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($statementList->isNotEmpty())
            <tfoot>
                <tr style="background-color: #f1f5f9; font-weight: bold;">
                    <td colspan="7" class="text-right" style="padding: 10px; font-size: 11px; text-transform: uppercase;">
                        Total Outflow Statement:
                    </td>
                    <td class="text-right text-danger" style="padding: 10px; font-size: 12px;">
                        -₹{{ number_format($totalDisbursed, 2) }}
                    </td>
                </tr>
            </tfoot>
        @endif
    </table>

</body>
</html>
