@extends('layouts.app')

@section('title', 'Deposit Amount — HR Payroll')

@section('styles')
<style>
    .dep-container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 24px 30px;
        box-sizing: border-box;
    }
    .dep-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .dep-breadcrumb a {
        color: #2563eb;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .dep-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(37, 99, 235, 0.05);
        overflow: hidden;
        margin-bottom: 24px;
    }
    .dep-card-hdr {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        color: #ffffff;
        padding: 16px 24px;
        font-size: 16px;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 10px;
        letter-spacing: 0.2px;
    }
    .dep-card-hdr-sub {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        color: #ffffff;
        padding: 14px 24px;
        font-size: 14.5px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .dep-card-body {
        padding: 24px 28px;
    }
    .dep-card-inner {
        padding: 24px;
    }
    .dep-input {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 600;
        color: #1e293b;
        outline: none;
        background: #ffffff;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        transition: all 0.2s ease;
        box-sizing: border-box;
    }
    .dep-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }
    .dep-search-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 20px;
        margin-bottom: 28px;
    }
    .dep-form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }
    .dep-btn-group {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }
    .btn-submit-blue {
        padding: 11px 32px;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-width: 140px;
    }
    .btn-submit-blue:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.45);
        color: #ffffff;
    }
    .btn-discard-red {
        padding: 11px 32px;
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 14px rgba(239, 68, 68, 0.25);
        transition: all 0.2s ease;
        min-width: 140px;
    }
    .btn-discard-red:hover {
        transform: translateY(-2px);
        color: #ffffff;
        box-shadow: 0 6px 18px rgba(239, 68, 68, 0.35);
    }

    /* Top Info Boxes */
    .dep-summary-layout {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }
    .emp-info-box {
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        border-radius: 14px;
        padding: 18px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }
    .emp-info-item {
        display: flex;
        flex-direction: column;
    }
    .emp-info-label {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    .emp-info-val {
        font-size: 13.5px;
        font-weight: 800;
        color: #0f172a;
    }
    .balance-summary-card {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 14px;
        padding: 18px 24px;
        min-width: 200px;
        box-shadow: 0 2px 10px rgba(16, 185, 129, 0.08);
    }
    .balance-hdr {
        font-size: 12px;
        font-weight: 700;
        color: #15803d;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 6px;
    }
    .balance-amount {
        font-size: 24px;
        font-weight: 900;
        color: #166534;
    }

    /* Table styles */
    .dep-table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
    }
    .dep-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13px;
        min-width: 600px;
    }
    .dep-table th {
        background: #eff6ff;
        color: #1e40af;
        font-weight: 700;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 13px 16px;
        border-bottom: 2px solid #dbeafe;
        white-space: nowrap;
    }
    .dep-table td {
        padding: 13px 16px;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        white-space: nowrap;
    }
    .dep-table tr:hover td {
        background-color: #f0f9ff;
    }

    /* Responsive Mobile Media Queries */
    @media (max-width: 992px) {
        .dep-summary-layout {
            grid-template-columns: 1fr;
            gap: 16px;
        }
    }
    @media (max-width: 768px) {
        .dep-container {
            padding: 14px 10px !important;
        }
        .dep-card-hdr {
            padding: 14px 16px !important;
            font-size: 15px !important;
        }
        .dep-card-hdr-sub {
            padding: 12px 16px !important;
            font-size: 13.5px !important;
        }
        .dep-card-body {
            padding: 16px 14px !important;
        }
        .dep-card-inner {
            padding: 16px 12px !important;
        }
        .emp-info-box {
            padding: 14px 16px !important;
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 14px !important;
        }
        .balance-summary-card {
            padding: 14px 16px !important;
        }
        .balance-amount {
            font-size: 20px !important;
        }
        .dep-table th, .dep-table td {
            padding: 10px 12px !important;
            font-size: 12px !important;
        }
    }
    @media (max-width: 540px) {
        .dep-search-grid {
            grid-template-columns: 1fr !important;
            gap: 14px !important;
        }
        .dep-form-grid {
            grid-template-columns: 1fr !important;
            gap: 14px !important;
        }
        .emp-info-box {
            grid-template-columns: 1fr !important;
        }
        .dep-btn-group {
            flex-direction: column !important;
            width: 100% !important;
            gap: 10px !important;
        }
        .dep-btn-group .btn-submit-blue,
        .dep-btn-group .btn-discard-red {
            width: 100% !important;
            min-width: 100% !important;
        }
    }

    /* Dark Mode Overrides */
    body.dark-mode .dep-card {
        background: #1e293b !important;
        border-color: #334155 !important;
    }
    body.dark-mode .dep-card-hdr {
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%) !important;
    }
    body.dark-mode .dep-input {
        background: #0f172a !important;
        color: #f8fafc !important;
        border-color: #334155 !important;
    }
    body.dark-mode .emp-info-box {
        background: #0f172a !important;
        border-color: #334155 !important;
    }
    body.dark-mode .emp-info-val {
        color: #f8fafc !important;
    }
    body.dark-mode .balance-summary-card {
        background: #064e3b !important;
        border-color: #047857 !important;
    }
    body.dark-mode .balance-hdr {
        color: #a7f3d0 !important;
    }
    body.dark-mode .balance-amount {
        color: #ffffff !important;
    }
    body.dark-mode .dep-table-wrap {
        border-color: #334155 !important;
    }
    body.dark-mode .dep-table th {
        background: #0f172a !important;
        color: #93c5fd !important;
        border-color: #334155 !important;
    }
    body.dark-mode .dep-table td {
        color: #f1f5f9 !important;
        border-color: #334155 !important;
    }
    body.dark-mode .dep-table tr:hover td {
        background-color: #0f172a !important;
    }
</style>
@endsection

@section('content')
<div class="dep-container">

    @if(session('success'))
        <div style="padding: 14px 18px; background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; border-radius: 12px; font-size: 13.5px; font-weight: 600; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 8px rgba(16,185,129,0.1);">
            <div><i class="fas fa-check-circle" style="margin-right: 8px; font-size: 16px;"></i> {{ session('success') }}</div>
            <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; color: #065f46; font-size: 16px; cursor: pointer;">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div style="padding: 14px 18px; background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; border-radius: 12px; font-size: 13.5px; font-weight: 600; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 8px rgba(239,68,68,0.1);">
            <div><i class="fas fa-exclamation-circle" style="margin-right: 8px; font-size: 16px;"></i> {{ session('error') }}</div>
            <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; color: #991b1b; font-size: 16px; cursor: pointer;">&times;</button>
        </div>
    @endif

    @if ($errors->any())
        <div style="padding: 14px 18px; background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; border-radius: 12px; font-size: 13.5px; font-weight: 600; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(239,68,68,0.1);">
            <i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i> Please correct the highlights in the deposit form.
        </div>
    @endif

    @if(!$selectedStaff)
        <!-- PAGE 1: EMPLOYEE SEARCH SCREEN -->
        <div class="dep-card">
            <!-- Banner Header -->
            <div class="dep-card-hdr">
                <i class="fas fa-search"></i> Employee Search
            </div>

            <div class="dep-card-body">
                <div class="dep-card" style="border-radius: 12px; margin-bottom: 0;">
                    <div class="dep-card-hdr-sub">
                        <i class="fas fa-user-gear"></i> Find Employee
                    </div>
                    <div class="dep-card-inner">
                        <form method="GET" action="{{ route('school.payroll.deposit-amount') }}">
                            <div class="dep-search-grid">
                                <div>
                                    <label style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                                        Select Employee Name
                                    </label>
                                    <select name="staff_id" id="search_staff_id" class="dep-input" onchange="if(this.value){ document.getElementById('search_employee_id').value = ''; }">
                                        <option value="">-- Select Employee --</option>
                                        @foreach($staffList as $st)
                                            <option value="{{ $st->id }}" {{ old('staff_id') == $st->id ? 'selected' : '' }}>
                                                {{ $st->full_name }} {{ $st->employee_id ? '('.$st->employee_id.')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                                        Enter Employee ID
                                    </label>
                                    <input type="text" name="employee_id" id="search_employee_id" class="dep-input" placeholder="Enter Employee Code ..." value="{{ old('employee_id', $employeeIdInput) }}">
                                </div>
                            </div>

                            <div class="dep-btn-group">
                                <button type="submit" class="btn-submit-blue">
                                    <i class="fas fa-check"></i> Submit
                                </button>
                                <a href="{{ route('school.payroll.salary-structure') }}" class="btn-discard-red">
                                    Back
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- PAGE 2: DEPOSIT AMOUNT FORM SCREEN -->
        <div class="dep-card">
            <!-- Header Banner -->
            <div class="dep-card-hdr">
                <i class="fas fa-circle-plus"></i> Deposit Amount
            </div>

            <div class="dep-card-body">
                <!-- Employee Summary & Balance Banner -->
                <div class="dep-summary-layout">
                    <!-- Employee Details -->
                    <div class="emp-info-box">
                        <div class="emp-info-item">
                            <span class="emp-info-label"><i class="fas fa-user me-1"></i> Employee Name</span>
                            <span class="emp-info-val">{{ $selectedStaff->full_name }}</span>
                        </div>
                        <div class="emp-info-item">
                            <span class="emp-info-label"><i class="fas fa-id-card me-1"></i> Emp ID</span>
                            <span class="emp-info-val" style="color: #2563eb; font-family: monospace;">{{ $selectedStaff->employee_id ?: 'EMP-'.str_pad($selectedStaff->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="emp-info-item">
                            <span class="emp-info-label"><i class="fas fa-briefcase me-1"></i> Designation</span>
                            <span class="emp-info-val">{{ $selectedStaff->designation?->name ?: ($selectedStaff->department?->name ?: 'Staff') }}</span>
                        </div>
                        <div class="emp-info-item">
                            <span class="emp-info-label"><i class="fas fa-coins me-1"></i> Net Payable Salary</span>
                            <span class="emp-info-val" style="color: #059669;">₹{{ number_format($configuredSalary, 2) }}</span>
                        </div>
                        <div class="emp-info-item">
                            <span class="emp-info-label"><i class="fas fa-phone me-1"></i> Mobile</span>
                            <span class="emp-info-val">{{ $selectedStaff->phone ?: 'N/A' }}</span>
                        </div>
                    </div>

                    <!-- Balance Summary Card -->
                    <div class="balance-summary-card">
                        <div class="balance-hdr">
                            <i class="fas fa-wallet"></i> Balance
                        </div>
                        <div style="font-size: 11.5px; font-weight: 700; color: #15803d; margin-bottom: 4px;">
                            Current Balance
                        </div>
                        <div class="balance-amount">
                            ₹{{ number_format($currentBalance, 2) }}
                        </div>
                    </div>
                </div>

                <!-- Deposit Form Card -->
                @if(isset($isPayrollGenerated) && !$isPayrollGenerated)
                    <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 16px 20px; font-size: 13.5px; font-weight: 700; color: #991b1b; margin-bottom: 24px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 18px; color: #dc2626;"></i>
                        <span>Please generate payroll first. Salary deposit is disabled until payroll has been generated for {{ $selectedStaff->full_name }}.</span>
                    </div>
                @endif

                <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 14px; overflow: hidden; margin-bottom: 32px;">
                    <div style="background: #fef3c7; padding: 12px 20px; font-size: 14px; font-weight: 800; color: #b45309; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-circle-plus"></i> Deposit
                    </div>
                    <div class="dep-card-inner">
                        <form action="{{ route('school.payroll.deposit-amount.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="staff_id" value="{{ $selectedStaff->id }}">

                            <div class="dep-form-grid">
                                <div>
                                    <label style="display: block; font-size: 12.5px; font-weight: 700; color: #92400e; margin-bottom: 8px;">
                                        Amount <span style="color: #dc2626;">*</span>
                                    </label>
                                    <input type="number" step="0.01" min="0.01" max="{{ $configuredSalary }}" name="amount" class="dep-input" placeholder="Amount (Max ₹{{ number_format($configuredSalary, 2) }})" value="{{ old('amount') }}" required>
                                    @if($configuredSalary > 0)
                                        <small style="color: #64748b; font-size: 11px; margin-top: 4px; display: block; font-weight: 600;">Max Deposit Limit: <strong>₹{{ number_format($configuredSalary, 2) }}</strong></small>
                                    @else
                                        <small style="color: #dc2626; font-size: 11px; margin-top: 4px; display: block; font-weight: 600;"><i class="fas fa-exclamation-triangle me-1"></i> Salary not configured.</small>
                                    @endif
                                </div>

                                <div>
                                    <label style="display: block; font-size: 12.5px; font-weight: 700; color: #92400e; margin-bottom: 8px;">
                                        Payment Mode <span style="color: #dc2626;">*</span>
                                    </label>
                                    <select name="payment_mode" class="dep-input" required>
                                        <option value="">- Select Mode -</option>
                                        <option value="Cash" {{ old('payment_mode') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="Bank Transfer" {{ old('payment_mode') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        <option value="UPI" {{ old('payment_mode') == 'UPI' ? 'selected' : '' }}>UPI</option>
                                        <option value="Cheque" {{ old('payment_mode') == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                        <option value="Other" {{ old('payment_mode') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>

                                <div>
                                    <label style="display: block; font-size: 12.5px; font-weight: 700; color: #92400e; margin-bottom: 8px;">
                                        Transaction Type <span style="color: #dc2626;">*</span>
                                    </label>
                                    <select name="transaction_type" class="dep-input" required>
                                        <option value="">- Select Type -</option>
                                        <option value="Salary Advance" {{ old('transaction_type') == 'Salary Advance' ? 'selected' : '' }}>Salary Advance</option>
                                        <option value="Deposit" {{ old('transaction_type', 'Deposit') == 'Deposit' ? 'selected' : '' }}>Deposit</option>
                                        <option value="Adjustment" {{ old('transaction_type') == 'Adjustment' ? 'selected' : '' }}>Adjustment</option>
                                        <option value="Other" {{ old('transaction_type') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>

                                <div>
                                    <label style="display: block; font-size: 12.5px; font-weight: 700; color: #92400e; margin-bottom: 8px;">
                                        Remark
                                    </label>
                                    <input type="text" name="remark" class="dep-input" placeholder="Remark (optional)" value="{{ old('remark') }}">
                                </div>
                            </div>

                            <div class="dep-btn-group">
                                @if(isset($isPayrollGenerated) && !$isPayrollGenerated)
                                    <button type="button" class="btn-submit-blue" style="background: #94a3b8; cursor: not-allowed; opacity: 0.7;" disabled title="Please generate payroll first">
                                        <i class="fas fa-lock"></i> Please generate payroll first
                                    </button>
                                @else
                                    <button type="submit" class="btn-submit-blue" style="background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);">
                                        <i class="fas fa-check"></i> Deposit
                                    </button>
                                @endif
                                <a href="{{ route('school.payroll.deposit-amount') }}" class="btn-discard-red">
                                    Discard
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Transaction History Table -->
                <div class="dep-table-wrap">
                    <div style="background: #f8fafc; padding: 14px 20px; font-size: 14px; font-weight: 800; color: #1e293b; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                        <div><i class="fas fa-history me-1 text-primary"></i> Deposit Transaction History</div>
                        <span style="font-size: 12px; font-weight: 700; background: #eff6ff; color: #2563eb; padding: 4px 12px; border-radius: 12px;">{{ $depositHistory->count() }} Records</span>
                    </div>

                    <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                        <table class="dep-table">
                            <thead>
                                <tr>
                                    <th>DATE</th>
                                    <th>TRANSACTION TYPE</th>
                                    <th>PAYMENT MODE</th>
                                    <th style="text-align: right;">AMOUNT</th>
                                    <th style="text-align: right;">BALANCE AFTER</th>
                                    <th>REMARK</th>
                                    <th style="text-align: center;">STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($depositHistory as $dep)
                                    <tr>
                                        <td style="font-weight: 600; color: #475569;">
                                            {{ $dep->created_at->format('d M, Y h:i A') }}
                                        </td>
                                        <td>
                                            <span style="font-weight: 700; color: #1e3a8a; background: #eff6ff; padding: 4px 10px; border-radius: 6px; font-size: 12px;">
                                                {{ $dep->transaction_type }}
                                            </span>
                                        </td>
                                        <td style="font-weight: 600; color: #334155;">
                                            <i class="fas fa-credit-card me-1 text-secondary"></i> {{ $dep->payment_mode }}
                                        </td>
                                        <td style="text-align: right; font-weight: 800; color: #16a34a; font-size: 14px;">
                                            +₹{{ number_format($dep->amount, 2) }}
                                        </td>
                                        <td style="text-align: right; font-weight: 800; color: #1e293b;">
                                            ₹{{ number_format($dep->balance_after_transaction, 2) }}
                                        </td>
                                        <td style="color: #64748b; font-size: 12.5px;">
                                            {{ $dep->remark ?: 'N/A' }}
                                        </td>
                                        <td style="text-align: center;">
                                            <span style="background: #dcfce7; color: #15803d; padding: 4px 10px; border-radius: 12px; font-size: 11.5px; font-weight: 700;">
                                                <i class="fas fa-check-circle me-1"></i> Completed
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" style="padding: 40px; text-align: center; color: #64748b;">
                                            <i class="fas fa-receipt fa-2x mb-2 text-secondary" style="display: block;"></i>
                                            No deposit transactions recorded yet for {{ $selectedStaff->full_name }}.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
