<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Salary Payslip - {{ $payroll->payroll_month }} - {{ $staff?->full_name }}</title>
    <style>
        @page {
            margin: 20px;
            size: a4 portrait;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 0;
            font-size: 12px;
            line-height: 1.4;
            background-color: #ffffff;
        }
        .wrapper {
            border: 2px solid #1e3a8a;
            border-radius: 8px;
            padding: 20px;
            background-color: #ffffff;
        }
        /* Header Table */
        .hdr-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 12px;
        }
        .hdr-logo {
            width: 80px;
            vertical-align: middle;
        }
        .hdr-logo img {
            max-width: 75px;
            max-height: 75px;
            object-fit: contain;
        }
        .hdr-school-info {
            vertical-align: middle;
            padding-left: 10px;
        }
        .school-name {
            font-size: 20px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .school-contact {
            font-size: 11px;
            color: #475569;
            margin: 2px 0;
        }
        .hdr-title-box {
            text-align: right;
            vertical-align: middle;
        }
        .payslip-badge {
            background-color: #1e3a8a;
            color: #ffffff;
            font-size: 13px;
            font-weight: bold;
            padding: 6px 14px;
            border-radius: 4px;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .payslip-subtext {
            font-size: 11px;
            color: #1e3a8a;
            font-weight: bold;
            margin-top: 6px;
        }

        /* Section Card Headers */
        .sec-header {
            background-color: #1e3a8a;
            color: #ffffff;
            font-size: 12px;
            font-weight: bold;
            padding: 6px 10px;
            margin-top: 15px;
            margin-bottom: 10px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* Info Grid Table */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
        }
        .info-table td {
            padding: 6px 10px;
            border: 1px solid #e2e8f0;
            width: 25%;
            font-size: 11px;
        }
        .info-label {
            color: #64748b;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            display: block;
            margin-bottom: 2px;
        }
        .info-val {
            color: #0f172a;
            font-weight: bold;
            font-size: 12px;
        }

        /* Breakdown Table */
        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .breakdown-table th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 7px 10px;
            border: 1px solid #1e3a8a;
        }
        .breakdown-table td {
            padding: 6px 10px;
            border: 1px solid #cbd5e1;
            font-size: 11px;
            vertical-align: middle;
        }
        .amount-col {
            text-align: right;
            font-weight: bold;
            color: #0f172a;
        }
        .subtotal-row td {
            background-color: #e0f2fe;
            font-weight: bold;
            color: #1e3a8a;
            border-top: 2px solid #0284c7;
        }

        /* Summary Box */
        .net-summary-box {
            background-color: #1e3a8a;
            color: #ffffff;
            border-radius: 6px;
            padding: 12px 16px;
            margin-top: 15px;
            margin-bottom: 15px;
        }
        .net-table {
            width: 100%;
            border-collapse: collapse;
        }
        .net-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #93c5fd;
            font-weight: bold;
        }
        .net-amount {
            font-size: 22px;
            font-weight: bold;
            color: #ffffff;
            margin-top: 4px;
        }
        .net-words {
            font-size: 11px;
            color: #e0f2fe;
            font-style: italic;
            margin-top: 4px;
        }
        .status-pill {
            background-color: #16a34a;
            color: #ffffff;
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
        }

        /* Signatures */
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
            margin-bottom: 15px;
        }
        .sig-cell {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
        }
        .sig-line {
            border-top: 1px dashed #64748b;
            width: 70%;
            margin: 0 auto 6px auto;
        }
        .sig-text {
            font-size: 11px;
            font-weight: bold;
            color: #334155;
        }

        /* Footer Bar */
        .footer-bar {
            border-top: 1px solid #cbd5e1;
            padding-top: 8px;
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Header -->
        <table class="hdr-table">
            <tr>
                @if(!empty($school?->logo) && file_exists(public_path($school->logo)))
                    <td class="hdr-logo">
                        <img src="{{ public_path($school->logo) }}" alt="Logo">
                    </td>
                @endif
                <td class="hdr-school-info">
                    <div class="school-name">{{ $school?->name ?: 'EDUCATIONAL ERP ACADEMY' }}</div>
                    <div class="school-contact">
                        <strong>Address:</strong> {{ $school?->address ?: 'School Campus, Main City Road' }}
                    </div>
                    <div class="school-contact">
                        <strong>Phone:</strong> {{ $school?->phone ?: 'N/A' }} | 
                        <strong>Email:</strong> {{ $school?->email ?: 'info@school.edu' }}
                    </div>
                </td>
                <td class="hdr-title-box">
                    <div class="payslip-badge">Salary Payslip</div>
                    <div class="payslip-subtext">MONTH: {{ strtoupper($payroll->payroll_month) }}</div>
                </td>
            </tr>
        </table>

        <!-- Employee Info Grid -->
        <div class="sec-header">Employee Details</div>
        <table class="info-table">
            <tr>
                <td>
                    <span class="info-label">Employee Name</span>
                    <span class="info-val">{{ $staff?->full_name }}</span>
                </td>
                <td>
                    <span class="info-label">Employee ID</span>
                    <span class="info-val">{{ $staff?->employee_id ?: 'EMP-' . $payroll->staff_id }}</span>
                </td>
                <td>
                    <span class="info-label">Department</span>
                    <span class="info-val">{{ $staff?->department?->name ?: 'General' }}</span>
                </td>
                <td>
                    <span class="info-label">Designation</span>
                    <span class="info-val">{{ $staff?->designation?->name ?: 'Staff' }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="info-label">Salary Month</span>
                    <span class="info-val">{{ $payroll->payroll_month }}</span>
                </td>
                <td>
                    <span class="info-label">Payment Date</span>
                    <span class="info-val">{{ $paymentDate }}</span>
                </td>
                <td>
                    <span class="info-label">Payable Days</span>
                    <span class="info-val">{{ $payroll->payable_days }} / {{ $payroll->total_days }} Days</span>
                </td>
                <td>
                    <span class="info-label">Payment Status</span>
                    <span class="info-val" style="color: #16a34a;">{{ strtoupper($payroll->payment_status) }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="info-label">Bank Name</span>
                    <span class="info-val">{{ $staff?->bank_name ?: 'N/A' }}</span>
                </td>
                <td>
                    <span class="info-label">Account Number</span>
                    <span class="info-val">{{ $staff?->bank_account_number ?: 'N/A' }}</span>
                </td>
                <td>
                    <span class="info-label">IFSC Code</span>
                    <span class="info-val">{{ $staff?->ifsc_code ?: 'N/A' }}</span>
                </td>
                <td>
                    <span class="info-label">PAN Number</span>
                    <span class="info-val">{{ $staff?->pan_number ?: 'N/A' }}</span>
                </td>
            </tr>
        </table>

        <!-- Earnings & Deductions Breakdown -->
        <div class="sec-header">Salary Computation Breakdown</div>
        <table class="breakdown-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Earnings / Allowances</th>
                    <th style="width: 50%;">Deductions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 0; vertical-align: top;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="border: none;">Basic Salary</td>
                                <td class="amount-col" style="border: none;">&#8377;{{ number_format($basicSalary, 2) }}</td>
                            </tr>
                            <tr>
                                <td style="border: none;">House Rent Allowance (HRA)</td>
                                <td class="amount-col" style="border: none;">&#8377;{{ number_format($hra, 2) }}</td>
                            </tr>
                            <tr>
                                <td style="border: none;">Dearness Allowance (DA)</td>
                                <td class="amount-col" style="border: none;">&#8377;{{ number_format($da, 2) }}</td>
                            </tr>
                            <tr>
                                <td style="border: none;">Transport Allowance (TA)</td>
                                <td class="amount-col" style="border: none;">&#8377;{{ number_format($ta, 2) }}</td>
                            </tr>
                            <tr>
                                <td style="border: none;">Other Allowances</td>
                                <td class="amount-col" style="border: none;">&#8377;{{ number_format($allowance, 2) }}</td>
                            </tr>
                        </table>
                    </td>
                    <td style="padding: 0; vertical-align: top;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="border: none;">Provident Fund (PF)</td>
                                <td class="amount-col" style="border: none;">&#8377;{{ number_format($pf, 2) }}</td>
                            </tr>
                            <tr>
                                <td style="border: none;">Employee State Insurance (ESI)</td>
                                <td class="amount-col" style="border: none;">&#8377;{{ number_format($esi, 2) }}</td>
                            </tr>
                            <tr>
                                <td style="border: none;">Professional Tax</td>
                                <td class="amount-col" style="border: none;">&#8377;{{ number_format($profTax, 2) }}</td>
                            </tr>
                            <tr>
                                <td style="border: none;">Tax Deducted at Source (TDS)</td>
                                <td class="amount-col" style="border: none;">&#8377;{{ number_format($tds, 2) }}</td>
                            </tr>
                            <tr>
                                <td style="border: none; font-weight: bold; color: #b91c1c;">Attendance Deduction</td>
                                <td class="amount-col" style="border: none; color: #dc2626;">&#8377;{{ number_format($attendanceDeduction ?? ($payroll->attendance_deduction ?: 0), 2) }}</td>
                            </tr>
                            <tr>
                                <td style="border: none;">Other Deductions</td>
                                <td class="amount-col" style="border: none;">&#8377;{{ number_format($otherDeductions, 2) }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr class="subtotal-row">
                    <td>
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="border: none; font-weight: bold;">Gross Earnings Total</td>
                                <td class="amount-col" style="border: none; font-weight: bold; color: #1e3a8a;">&#8377;{{ number_format($grossSalary, 2) }}</td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="border: none; font-weight: bold;">Total Deductions</td>
                                <td class="amount-col" style="border: none; font-weight: bold; color: #b91c1c;">&#8377;{{ number_format($totalDeductions, 2) }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Net Salary Summary Box -->
        <div class="net-summary-box">
            <table class="net-table">
                <tr>
                    <td>
                        <div class="net-title">Net Payable Salary Disbursed</div>
                        <div class="net-amount">&#8377;{{ number_format($netSalary, 2) }}</div>
                        <div class="net-words">Amount in words: {{ $netInWords }}</div>
                    </td>
                    <td style="text-align: right; vertical-align: middle;">
                        <div class="status-pill">&#10004; DISBURSED & PAID</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Signatures -->
        <table class="sig-table">
            <tr>
                <td class="sig-cell">
                    <div class="sig-line"></div>
                    <div class="sig-text">Employee Signature</div>
                </td>
                <td class="sig-cell">
                    <div class="sig-line"></div>
                    <div class="sig-text">Authorized Signatory / Accountant Stamp</div>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer-bar">
            This is a system-generated salary slip and does not require a physical signature.<br>
            Generated on: {{ $generatedDate }} &bull; {{ $school?->name ?: 'School ERP System' }}
        </div>
    </div>
</body>
</html>
