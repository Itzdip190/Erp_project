<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $reportTitle ?? 'School Expense Ledger & Audit Report' }}</title>
    <style>
        @page {
            margin: 25px 25px 35px 25px;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9.5px;
            color: #1e293b;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            border-bottom: 2px solid #dc2626;
            padding-bottom: 8px;
        }
        .header-logo {
            width: 55px;
            height: auto;
            max-height: 55px;
        }
        .school-name {
            font-size: 18px;
            font-weight: bold;
            color: #dc2626;
            margin: 0;
            text-transform: uppercase;
        }
        .school-info {
            font-size: 8.5px;
            color: #64748b;
            margin-top: 2px;
        }
        .report-title-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 12px;
        }
        .report-title {
            font-size: 14px;
            font-weight: bold;
            color: #991b1b;
            margin: 0;
        }
        .meta-grid {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
            color: #7f1d1d;
            margin-top: 4px;
        }
        .meta-grid td {
            padding: 2px 0;
        }

        /* KPI Bar */
        .kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .kpi-cell {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 6px 8px;
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

        /* Main Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .data-table th {
            background: #dc2626;
            color: #ffffff;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 7px;
            text-align: left;
            border: 1px solid #dc2626;
        }
        .data-table td {
            padding: 5px 7px;
            font-size: 8.5px;
            border-bottom: 1px solid #e2e8f0;
            border-left: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
        }
        .data-table tr:nth-child(even) td {
            background: #f8fafc;
        }
        .data-table tr.total-row td {
            background: #fef2f2;
            font-weight: bold;
            font-size: 9px;
            color: #991b1b;
            border-top: 2px solid #dc2626;
            border-bottom: 2px solid #dc2626;
        }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger  { background: #fee2e2; color: #991b1b; }
        .badge-gray    { background: #f1f5f9; color: #475569; }

        .footer-note {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 8px;
            color: #94a3b8;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            @if(!empty($school->logo) && file_exists(public_path('storage/' . $school->logo)))
                <td style="width: 65px; vertical-align: middle;">
                    <img src="{{ public_path('storage/' . $school->logo) }}" class="header-logo" alt="Logo">
                </td>
            @endif
            <td style="vertical-align: middle;">
                <h1 class="school-name">{{ $school->name ?? 'SCHOOL ERP' }}</h1>
                <div class="school-info">
                    {{ $school->address ?? '' }} {{ $school->city ? ', ' . $school->city : '' }} {{ $school->phone ? ' | Phone: ' . $school->phone : '' }}
                </div>
            </td>
            <td class="text-right" style="vertical-align: middle;">
                <div style="font-size: 10px; font-weight: bold; color: #dc2626;">OFFICIAL EXPENSE REPORT</div>
                <div style="font-size: 8px; color: #64748b;">Generated: {{ now()->format('d M Y, h:i A') }}</div>
                <div style="font-size: 8px; color: #64748b;">By: {{ auth()->user()->name ?? 'System' }}</div>
            </td>
        </tr>
    </table>

    <!-- Report Title Box -->
    <div class="report-title-box">
        <h2 class="report-title">{{ $reportTitle }}</h2>
        <table class="meta-grid">
            <tr>
                <td><strong>Academic Session:</strong> {{ $sessionName }}</td>
                <td><strong>Date Range:</strong> {{ $dateFrom }} to {{ $dateTo }}</td>
                <td><strong>Filters Applied:</strong> {{ $filterSummary }}</td>
            </tr>
        </table>
    </div>

    <!-- Summary KPI Cards Bar -->
    @if(!empty($kpis))
    <table class="kpi-table">
        <tr>
            @foreach($kpis as $kpi)
            <td class="kpi-cell">
                <div class="kpi-lbl">{{ $kpi['label'] }}</div>
                <div class="kpi-val">{{ $kpi['value'] }}</div>
            </td>
            @endforeach
        </tr>
    </table>
    @endif

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;">#</th>
                <th style="width: 10%;">Voucher No</th>
                <th style="width: 12%;">Date</th>
                <th style="width: 18%;">Expense Head</th>
                <th style="width: 12%;">Category</th>
                <th style="width: 10%;">Payment Mode</th>
                <th style="width: 12%; text-align: right;">Amount</th>
                <th style="width: 14%;">Paid To / Vendor</th>
                <th style="width: 8%; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @php $totalAmt = 0; @endphp
            @forelse($expenses as $idx => $exp)
                @php
                    $amt = (float) $exp->amount;
                    if ($exp->status !== 'cancelled') $totalAmt += $amt;
                @endphp
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td style="font-weight: bold;">{{ $exp->receipt_no ?? ($exp->reference_no ?? ('EXP-' . $exp->id)) }}</td>
                    <td>{{ $exp->expense_date ? \Carbon\Carbon::parse($exp->expense_date)->format('d M Y') : '—' }}</td>
                    <td style="font-weight: bold; color: #991b1b;">{{ optional($exp->expenseHead)->name ?? 'Other' }}</td>
                    <td>{{ $exp->category_label }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $exp->payment_mode ?? 'Cash')) }}</td>
                    <td class="text-right" style="font-weight: bold;">₹{{ number_format($amt, 2) }}</td>
                    <td>{{ $exp->paid_to ?? '—' }}</td>
                    <td class="text-center">
                        <span class="badge {{ $exp->status === 'paid' ? 'badge-success' : ($exp->status === 'pending' ? 'badge-warning' : 'badge-danger') }}">
                            {{ ucfirst($exp->status ?? 'Paid') }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 15px; color: #64748b;">
                        No expense records found matching the criteria.
                    </td>
                </tr>
            @endforelse

            @if(count($expenses) > 0)
                <tr class="total-row">
                    <td colspan="6" class="text-right">GRAND TOTAL EXPENSE:</td>
                    <td class="text-right">₹{{ number_format($totalAmt, 2) }}</td>
                    <td colspan="2"></td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer-note">
        {{ $school->name ?? 'School ERP' }} — School Expense Ledger & Audit Report — Confidentially Generated Record
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
            $font = $fontMetrics->get_font("DejaVu Sans", "bold");
            $size = 7.5;
            $color = array(0.4, 0.4, 0.4);
            $y = $pdf->get_height() - 22;
            $x = $pdf->get_width() - 80;
            $pdf->page_text($x, $y, $text, $font, $size, $color);
        }
    </script>
</body>
</html>
