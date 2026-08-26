@php
    if (!function_exists('formatSalaryNum')) {
        function formatSalaryNum($val) {
            $num = (float)$val;
            if ($num == 0) return '0.00';
            return number_format($num, 2);
        }
    }

    $earnings = $earningsList ?? [
        ['name' => 'Basic', 'amount' => (float)($basicSalary ?? 0)],
        ['name' => 'Incentive Pay', 'amount' => (float)($allowance ?? 0)],
        ['name' => 'House Rent Allowance', 'amount' => (float)($hra ?? 0)],
        ['name' => 'Dearness Allowance', 'amount' => (float)($da ?? 0)],
        ['name' => 'Transport Allowance', 'amount' => (float)($ta ?? 0)],
    ];

    $deductions = $deductionsList ?? [
        ['name' => 'Provident Fund', 'amount' => (float)($pf ?? 0)],
        ['name' => 'Professional Tax', 'amount' => (float)($profTax ?? 0)],
        ['name' => 'Attendance Deduction', 'amount' => (float)($attendanceDeduction ?? 0)],
    ];

    // Filter out zero amount items if more than 3 items exist, but keep basic structure clean
    $displayEarnings = array_values(array_filter($earnings, function($it) {
        return (float)($it['amount'] ?? 0) > 0 || strtolower($it['name']) === 'basic';
    }));
    if (empty($displayEarnings)) {
        $displayEarnings[] = ['name' => 'Basic', 'amount' => (float)($basicSalary ?? 0)];
    }

    $displayDeductions = array_values(array_filter($deductions, function($it) {
        return (float)($it['amount'] ?? 0) > 0;
    }));

    $maxRows = max(count($displayEarnings), count($displayDeductions), 3);
    $totalGrossAmount = (float)($grossSalary ?? 0);
    $totalDeductionsAmount = (float)($totalDeductions ?? 0);
    $netPayAmount = (float)($netSalary ?? 0);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Payslip - {{ $payPeriod ?? ($payroll->payroll_month ?? 'Salary Slip') }} - {{ $employeeName ?? ($staff?->full_name ?? 'Employee') }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 18mm 18mm 16mm 18mm;
        }
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            color: #1e293b;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            font-size: 11px;
            line-height: 1.4;
        }

        /* Container */
        .payslip-wrap {
            width: 100%;
            max-width: 720px;
            margin: 0 auto;
            background: #ffffff;
        }

        /* 1. Header Layout with Color & Logo */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .header-table td {
            vertical-align: middle;
            padding: 0;
        }
        .header-logo-cell {
            width: 80px;
            text-align: left;
            padding-right: 14px !important;
        }
        .school-logo {
            max-height: 62px;
            max-width: 80px;
            object-fit: contain;
            display: block;
        }
        .school-logo-placeholder {
            width: 58px;
            height: 58px;
            background-color: #1e3a8a;
            color: #ffffff;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            line-height: 58px;
            border-radius: 6px;
        }
        .header-text-cell {
            text-align: left;
        }
        .school-name {
            font-size: 16px;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .school-subtext {
            font-size: 10px;
            color: #475569;
            line-height: 1.35;
        }
        .header-badge-cell {
            width: 130px;
            text-align: right;
            vertical-align: top;
        }
        .payslip-badge {
            background-color: #1e3a8a;
            color: #ffffff;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 1px;
            padding: 5px 14px;
            border-radius: 4px;
            display: inline-block;
            text-align: center;
        }
        .payslip-badge-sub {
            font-size: 10px;
            font-weight: bold;
            color: #1e3a8a;
            margin-top: 4px;
            text-transform: uppercase;
            text-align: right;
        }
        .header-accent-divider {
            width: 100%;
            height: 2.5px;
            background-color: #1e3a8a;
            margin-top: 10px;
            margin-bottom: 16px;
        }

        /* 2. Metadata Grid (7-column table with 100% aligned colons) */
        .meta-grid-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 6px 10px;
        }
        .meta-grid-table td {
            padding: 4px 6px;
            font-size: 11px;
            vertical-align: middle;
        }
        .m-lbl {
            width: 18%;
            font-weight: bold;
            color: #475569;
            text-align: left;
            white-space: nowrap;
        }
        .m-colon {
            width: 2%;
            font-weight: bold;
            color: #64748b;
            text-align: center;
        }
        .m-val {
            width: 28%;
            font-weight: bold;
            color: #0f172a;
            text-align: left;
        }
        .m-spacer {
            width: 4%;
        }

        /* 3. Main Salary Table with Colored Headers */
        .salary-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #cbd5e1;
            margin-bottom: 18px;
            font-size: 11px;
        }
        .salary-table thead th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 7px 10px;
            border-top: 1px solid #1e3a8a;
            border-bottom: 1px solid #1e3a8a;
        }
        .th-earn-title {
            width: 32%;
            text-align: left;
            border-right: 1px solid #3b82f6;
        }
        .th-earn-amt {
            width: 18%;
            text-align: right;
            border-right: 1.5px solid #cbd5e1;
        }
        .th-ded-title {
            width: 32%;
            text-align: left;
            border-right: 1px solid #3b82f6;
        }
        .th-ded-amt {
            width: 18%;
            text-align: right;
        }

        .salary-table tbody td {
            padding: 5px 10px;
            color: #1e293b;
            font-size: 11px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }
        .td-earn-title {
            border-right: 1px solid #f1f5f9;
        }
        .td-earn-amt {
            text-align: right;
            font-weight: bold;
            color: #0f172a;
            border-right: 1.5px solid #cbd5e1;
        }
        .td-ded-title {
            border-right: 1px solid #f1f5f9;
        }
        .td-ded-amt {
            text-align: right;
            font-weight: bold;
            color: #0f172a;
        }

        /* Table Totals */
        .tr-totals td {
            background-color: #f8fafc;
            border-top: 1.5px solid #cbd5e1 !important;
            padding: 6px 10px;
        }
        .total-earn-label {
            text-align: left;
            font-weight: bold;
            color: #1e3a8a;
            border-right: 1px solid #e2e8f0;
        }
        .total-earn-amount {
            text-align: right;
            font-weight: bold;
            color: #1e3a8a;
            border-right: 1.5px solid #cbd5e1;
        }
        .total-ded-label {
            text-align: left;
            font-weight: bold;
            color: #dc2626;
            border-right: 1px solid #e2e8f0;
        }
        .total-ded-amount {
            text-align: right;
            font-weight: bold;
            color: #dc2626;
        }

        /* Net Pay Row */
        .tr-netpay td {
            border-top: 1px solid #e2e8f0;
        }
        .netpay-label {
            background-color: #eff6ff;
            text-align: left;
            font-weight: bold;
            color: #1e3a8a;
            font-size: 11.5px;
            border-top: 1.5px solid #93c5fd !important;
            border-bottom: 1.5px solid #1e3a8a !important;
            border-right: 1px solid #bfdbfe;
        }
        .netpay-amount {
            background-color: #eff6ff;
            text-align: right;
            font-weight: bold;
            color: #1e3a8a;
            font-size: 12px;
            border-top: 1.5px solid #93c5fd !important;
            border-bottom: 1.5px solid #1e3a8a !important;
        }

        /* 4. Net Amount Card & Words */
        .net-card-container {
            width: 100%;
            margin: 14px 0 24px 0;
            padding: 10px 14px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #1e3a8a;
            border-radius: 4px;
            text-align: center;
        }
        .net-digits {
            font-size: 15px;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 3px;
        }
        .net-words {
            font-size: 11px;
            font-weight: bold;
            color: #334155;
            text-transform: capitalize;
        }

        /* 5. Signatures */
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 28px;
            margin-bottom: 24px;
        }
        .sig-table td {
            vertical-align: bottom;
            padding: 0;
        }
        .sig-col-left {
            width: 50%;
            text-align: left;
        }
        .sig-col-right {
            width: 50%;
            text-align: right;
        }
        .sig-title {
            font-size: 11px;
            font-weight: bold;
            color: #334155;
            margin-bottom: 35px;
        }
        .sig-line-left {
            width: 180px;
            border-bottom: 1.5px solid #475569;
        }
        .sig-line-right {
            width: 180px;
            border-bottom: 1.5px solid #475569;
            margin-left: auto;
        }

        /* 6. Footer */
        .system-footer {
            text-align: center;
            font-size: 10px;
            color: #64748b;
            border-top: 1px dashed #cbd5e1;
            padding-top: 8px;
        }

        /* Screen toolbar when viewed directly in browser */
        .no-print-toolbar {
            max-width: 720px;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .btn-toolbar {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #1e293b;
        }
        .btn-toolbar.btn-primary {
            background: #1e3a8a;
            color: #ffffff;
            border-color: #1e3a8a;
        }

        @media print {
            .no-print-toolbar {
                display: none !important;
            }
            body {
                padding: 0;
                background: #ffffff;
            }
            .payslip-wrap {
                max-width: 100%;
                padding: 0;
            }
        }
    </style>
</head>
<body>

    @if(!request()->has('export') && !app()->runningInConsole() && !isset($isPdfRender))
        <div class="no-print-toolbar">
            <a href="javascript:history.back()" class="btn-toolbar">
                &larr; Back
            </a>
            <div style="display: flex; gap: 8px;">
                <button type="button" class="btn-toolbar btn-primary" onclick="window.print()">
                    Print Payslip
                </button>
                <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="btn-toolbar btn-primary">
                    Download PDF
                </a>
            </div>
        </div>
    @endif

    <div class="payslip-wrap">

        <!-- 1. Header with Logo & Professional Styling -->
        <table class="header-table">
            <tr>
                @if(!empty($schoolLogoBase64))
                    <td class="header-logo-cell">
                        <img src="{{ $schoolLogoBase64 }}" class="school-logo" alt="School Logo">
                    </td>
                @else
                    <td class="header-logo-cell">
                        <div class="school-logo-placeholder">
                            {{ strtoupper(substr($school?->name ?? 'S', 0, 1)) }}
                        </div>
                    </td>
                @endif
                <td class="header-text-cell">
                    <div class="school-name">{{ $school?->name ?: 'DELHI PUBLIC SCHOOL' }}</div>
                    @if(!empty($schoolAddress1))
                        <div class="school-subtext">{{ $schoolAddress1 }}</div>
                    @endif
                    <div class="school-subtext">
                        @if(!empty($schoolAddress2)) {!! $schoolAddress2 !!} @endif
                        @if(!empty($school?->phone)) &bull; Phone: {{ $school->phone }} @endif
                        @if(!empty($school?->email)) &bull; Email: {{ $school->email }} @endif
                    </div>
                </td>
                <td class="header-badge-cell">
                    <div class="payslip-badge">PAYSLIP</div>
                    <div class="payslip-badge-sub">{{ $payPeriod ?? ($payroll->payroll_month ?: 'JULY 2026') }}</div>
                </td>
            </tr>
        </table>

        <div class="header-accent-divider"></div>

        <!-- 2. Metadata Grid (7-column strictly aligned table) -->
        <table class="meta-grid-table">
            <tr>
                <td class="m-lbl">Date of Joining</td>
                <td class="m-colon">:</td>
                <td class="m-val">{{ $joiningDate ?? ($staff?->joining_date ? $staff->joining_date->format('Y-m-d') : '—') }}</td>
                <td class="m-spacer"></td>
                <td class="m-lbl">Employee Name</td>
                <td class="m-colon">:</td>
                <td class="m-val">{{ $employeeName ?? ($staff?->full_name ?: 'Employee') }}</td>
            </tr>
            <tr>
                <td class="m-lbl">Pay Period</td>
                <td class="m-colon">:</td>
                <td class="m-val">{{ $payPeriod ?? ($payroll->payroll_month ?: 'July 2026') }}</td>
                <td class="m-spacer"></td>
                <td class="m-lbl">Designation</td>
                <td class="m-colon">:</td>
                <td class="m-val">{{ $designation ?? ($staff?->designation?->name ?: ($staff?->staff_type ?: 'Teacher')) }}</td>
            </tr>
            <tr>
                <td class="m-lbl">Worked Days</td>
                <td class="m-colon">:</td>
                <td class="m-val">{{ $workedDays ?? '24.5 / 31' }}</td>
                <td class="m-spacer"></td>
                <td class="m-lbl">Department</td>
                <td class="m-colon">:</td>
                <td class="m-val">{{ $department ?? ($staff?->department?->name ?: 'Academics') }}</td>
            </tr>
        </table>

        <!-- 3. Earnings & Deductions Table -->
        <table class="salary-table">
            <thead>
                <tr>
                    <th class="th-earn-title">Earnings</th>
                    <th class="th-earn-amt">Amount</th>
                    <th class="th-ded-title">Deductions</th>
                    <th class="th-ded-amt">Amount</th>
                </tr>
            </thead>
            <tbody>
                @for($i = 0; $i < $maxRows; $i++)
                    @php
                        $earnItem = $displayEarnings[$i] ?? null;
                        $dedItem = $displayDeductions[$i] ?? null;
                    @endphp
                    <tr>
                        <td class="td-earn-title">{{ $earnItem ? $earnItem['name'] : '' }}</td>
                        <td class="td-earn-amt">{{ $earnItem ? formatSalaryNum($earnItem['amount']) : '' }}</td>
                        <td class="td-ded-title">{{ $dedItem ? $dedItem['name'] : '' }}</td>
                        <td class="td-ded-amt">{{ $dedItem ? formatSalaryNum($dedItem['amount']) : '' }}</td>
                    </tr>
                @endfor

                {{-- Total Earnings & Total Deductions Row --}}
                <tr class="tr-totals">
                    <td class="total-earn-label">Total Earnings</td>
                    <td class="total-earn-amount">&#8377; {{ formatSalaryNum($totalGrossAmount) }}</td>
                    <td class="total-ded-label">Total Deductions</td>
                    <td class="total-ded-amount">&#8377; {{ formatSalaryNum($totalDeductionsAmount) }}</td>
                </tr>

                {{-- Net Pay Row --}}
                <tr class="tr-netpay">
                    <td style="background-color: #ffffff; border-top: none;">&nbsp;</td>
                    <td style="background-color: #ffffff; border-top: none; border-right: 1.5px solid #cbd5e1;">&nbsp;</td>
                    <td class="netpay-label">Net Pay</td>
                    <td class="netpay-amount">&#8377; {{ formatSalaryNum($netPayAmount) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- 4. Net Amount and Words Card -->
        <div class="net-card-container">
            <div class="net-digits">
                Net Pay: &#8377; {{ formatSalaryNum($netPayAmount) }}
            </div>
            <div class="net-words">
                ({{ $netInWords ?? 'Eleven Thousand Seven Hundred Fifty Rupees Only' }})
            </div>
        </div>

        <!-- 5. Signature Lines -->
        <table class="sig-table">
            <tr>
                <td class="sig-col-left">
                    <div class="sig-title">Employer Signature</div>
                    <div class="sig-line-left"></div>
                </td>
                <td class="sig-col-right">
                    <div class="sig-title">Employee Signature</div>
                    <div class="sig-line-right"></div>
                </td>
            </tr>
        </table>

        <!-- 6. System Generated Footer -->
        <div class="system-footer">
            This is a system generated payslip and does not require a physical signature.
        </div>

    </div>

</body>
</html>
