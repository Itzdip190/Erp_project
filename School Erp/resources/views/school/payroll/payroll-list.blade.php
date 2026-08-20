@extends('layouts.app')

@section('title', 'Payroll List — HR Payroll')

@section('styles')
<style>
    .plist-container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 24px 30px;
        box-sizing: border-box;
    }
    .plist-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .plist-breadcrumb a {
        color: #2563eb;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    /* Stage 1: Month Selection Card */
    .month-select-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 420px;
        padding: 40px 20px;
    }
    .month-select-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        width: 100%;
        max-width: 480px;
        overflow: hidden;
    }
    .month-select-hdr {
        background: #1e3a8a;
        color: #ffffff;
        padding: 16px 24px;
        font-size: 15px;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 10px;
        letter-spacing: 0.3px;
    }
    .btn-view-salary-list {
        width: 100%;
        padding: 12px 24px;
        background: #1e3a8a;
        color: #ffffff;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.25);
    }
    .btn-view-salary-list:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(30, 58, 138, 0.35);
        color: #ffffff;
    }

    /* Stage 2: Full Table Card */
    .plist-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(37, 99, 235, 0.05);
        overflow: hidden;
        margin-bottom: 24px;
    }
    .plist-card-hdr {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        color: #ffffff;
        padding: 16px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }
    .plist-hdr-left {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .plist-hdr-title {
        font-size: 15.5px;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 8px;
        letter-spacing: 0.2px;
    }
    .plist-hdr-right {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .btn-change-month {
        background: rgba(255, 255, 255, 0.18);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.3);
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }
    .btn-change-month:hover {
        background: rgba(255, 255, 255, 0.3);
        color: #ffffff;
    }

    /* Filter & Search */
    .plist-filter-row {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 14px;
        background: #f8fafc;
        padding: 16px 24px;
        border-bottom: 1px solid #e2e8f0;
    }
    .plist-filter-search-box {
        flex: 1;
        min-width: 240px;
    }
    .plist-label {
        font-size: 11.5px;
        font-weight: 800;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .plist-input {
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
        box-sizing: border-box;
        transition: all 0.2s ease;
    }
    .plist-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }
    
    /* Export Buttons */
    .btn-export-pdf {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: #ffffff !important;
        border: none;
        padding: 7px 14px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.25);
        transition: all 0.2s ease;
    }
    .btn-export-pdf:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.35);
    }
    .btn-export-excel {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff !important;
        border: none;
        padding: 7px 14px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);
        transition: all 0.2s ease;
    }
    .btn-export-excel:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
    }

    /* Badges */
    .badge-paid {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
        padding: 4px 12px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 11.5px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }
    .badge-unpaid {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
        padding: 4px 12px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 11.5px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }

    /* Table Styles */
    .plist-table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .plist-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 850px;
    }
    .plist-table th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 13px 16px;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }
    .plist-table td {
        padding: 13px 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        color: #334155;
        font-weight: 600;
        white-space: nowrap;
    }
    .plist-table tr:hover td {
        background: #f8fafc;
    }
    .btn-view-details {
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
        padding: 5px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
    }
    .btn-view-details:hover {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }

    /* Premium Overlay Modal */
    .plist-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
        box-sizing: border-box;
    }
    .plist-modal-overlay.open {
        display: flex !important;
    }
    .plist-modal-box {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
        width: 100%;
        max-width: 760px;
        max-height: 90vh;
        overflow-y: auto;
        box-sizing: border-box;
        animation: plistModalPop 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        border: 1px solid #e2e8f0;
    }
    @keyframes plistModalPop {
        from { opacity: 0; transform: scale(0.94) translateY(12px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .plist-modal-hdr {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        color: #ffffff;
        padding: 16px 20px;
        font-size: 15.5px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }
    .plist-modal-close {
        background: rgba(255,255,255,0.18);
        border: none;
        color: #ffffff;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }
    .plist-modal-close:hover {
        background: rgba(255,255,255,0.35);
    }
    .plist-breakdown-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
        margin-bottom: 24px;
    }

    /* Responsive Mobile Media Queries */
    @media (max-width: 768px) {
        .plist-container {
            padding: 14px 10px !important;
        }
        .plist-card-hdr {
            padding: 14px 16px !important;
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 12px !important;
        }
        .plist-hdr-left {
            justify-content: space-between !important;
            width: 100% !important;
        }
        .plist-hdr-right {
            display: flex !important;
            width: 100% !important;
            gap: 10px !important;
        }
        .plist-hdr-right .btn-export-pdf,
        .plist-hdr-right .btn-export-excel {
            flex: 1 !important;
            justify-content: center !important;
            padding: 8px 12px !important;
        }
        .plist-filter-row {
            padding: 14px 14px !important;
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 12px !important;
        }
        .plist-filter-search-box {
            width: 100% !important;
            min-width: 100% !important;
        }
        .plist-table th, .plist-table td {
            padding: 10px 12px !important;
            font-size: 12px !important;
        }
    }
    @media (max-width: 580px) {
        .plist-breakdown-grid {
            grid-template-columns: 1fr !important;
            gap: 14px !important;
        }
    }

    /* Dark Mode Overrides */
    body.dark-mode .plist-card, body.dark-mode .month-select-card {
        background: #1e293b !important;
        border-color: #334155 !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2) !important;
    }
    body.dark-mode .plist-card-hdr, body.dark-mode .month-select-hdr {
        background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%) !important;
    }
    body.dark-mode .plist-filter-row {
        background: #0f172a !important;
        border-color: #334155 !important;
    }
    body.dark-mode .plist-label {
        color: #cbd5e1 !important;
    }
    body.dark-mode .plist-input {
        background: #1e293b !important;
        color: #f8fafc !important;
        border-color: #334155 !important;
    }
    body.dark-mode .plist-table th {
        background: #0f172a !important;
        color: #93c5fd !important;
        border-color: #334155 !important;
    }
    body.dark-mode .plist-table td {
        color: #f1f5f9 !important;
        border-color: #334155 !important;
    }
    body.dark-mode .plist-table tr:hover td {
        background-color: #0f172a !important;
    }
    body.dark-mode .plist-modal-box {
        background: #1e293b !important;
        border-color: #334155 !important;
        color: #f8fafc !important;
    }
</style>
@endsection

@section('content')
<div class="plist-container">
    @if(!$hasSelectedMonth)
        <!-- ================= STAGE 1: SELECT SALARY MONTH (INITIAL VIEW) ================= -->
        <div class="month-select-wrapper">
            <div class="month-select-card">
                <div class="month-select-hdr">
                    <i class="fas fa-calendar-alt"></i> Select Salary Month
                </div>
                <div style="padding: 24px 28px;">
                    <form method="GET" action="{{ route('school.payroll.payroll-list') }}">
                        <input type="hidden" name="view_list" value="1">
                        
                        <div style="margin-bottom: 24px;">
                            <label style="display: flex; align-items: center; gap: 6px; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                                <i class="fas fa-calendar text-primary"></i> Select Month - Year
                            </label>
                            
                            @if($availableMonths->isNotEmpty())
                                <select name="month_year" class="plist-input" required>
                                    @foreach($availableMonths as $av)
                                        <option value="{{ $av->payroll_month }}" {{ (isset($selectedMonth) && ($selectedMonth === $av->salary_month || str_contains($av->payroll_month, $selectedMonth))) || (!isset($selectedMonth) && str_contains($av->payroll_month, date('F'))) ? 'selected' : '' }}>
                                            {{ str_replace(' ', ' - ', $av->payroll_month) }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <select name="month_year" class="plist-input" required>
                                    @foreach($months as $m)
                                        <option value="{{ $m }} {{ date('Y') }}" {{ date('F') === $m ? 'selected' : '' }}>
                                            {{ $m }} - {{ date('Y') }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>

                        <button type="submit" class="btn-view-salary-list">
                            View Salary List
                        </button>
                    </form>
                </div>
            </div>
        </div>

    @else
        <!-- ================= STAGE 2: PAYROLL LIST TABLE ================= -->
        <div class="plist-card">
            <!-- Header Banner -->
            <div class="plist-card-hdr">
                <div class="plist-hdr-left">
                    <div class="plist-hdr-title">
                        <i class="fas fa-table-list"></i> PAYROLL LIST / FINALISED SALARY
                    </div>
                    <a href="{{ route('school.payroll.payroll-list') }}" class="btn-change-month">
                        <i class="fas fa-calendar-alt"></i> Change Month ({{ $monthYearInput }})
                    </a>
                </div>
                <div class="plist-hdr-right">
                    <a href="{{ route('school.payroll.payroll-list', array_merge(request()->all(), ['export' => 'pdf'])) }}" target="_blank" class="btn-export-pdf">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                    <a href="{{ route('school.payroll.payroll-list', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn-export-excel">
                        <i class="fas fa-file-excel"></i> Excel
                    </a>
                </div>
            </div>

            <!-- Filter & Search Bar -->
            <form method="GET" action="{{ route('school.payroll.payroll-list') }}">
                <input type="hidden" name="view_list" value="1">
                <input type="hidden" name="month_year" value="{{ $monthYearInput }}">

                <div class="plist-filter-row">
                    <div class="plist-filter-search-box">
                        <label class="plist-label"><i class="fas fa-search text-primary"></i> Search Employee</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0" style="border-radius: 10px 0 0 10px; border-color: #cbd5e1;">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" name="search" class="plist-input border-start-0" style="border-radius: 0 10px 10px 0;" placeholder="Search Name or Employee ID..." value="{{ $search }}">
                        </div>
                    </div>

                    <div style="display: flex; gap: 8px; align-items: center;">
                        <button type="submit" class="btn btn-primary fw-bold text-white px-4" style="background:#2563eb; border-radius: 10px; padding-top: 10px; padding-bottom: 10px;">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('school.payroll.payroll-list', ['month_year' => $monthYearInput, 'view_list' => 1]) }}" class="btn btn-light border fw-bold text-slate-600 px-3" style="border-radius: 10px; padding-top: 10px; padding-bottom: 10px;" title="Reset Search">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    </div>
                </div>
            </form>

            <!-- Table -->
            <div class="plist-table-wrap">
                <table class="plist-table align-middle">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">S.No</th>
                            <th>Salary Month</th>
                            <th>Emp ID</th>
                            <th>Employee Name</th>
                            <th>Department</th>
                            <th>Designation</th>
                            <th class="text-end">Gross Salary</th>
                            <th class="text-end" style="color: #ea580c;">Att. Deduction</th>
                            <th class="text-end">Total Deduction</th>
                            <th class="text-end">Net Salary</th>
                            <th class="text-center">Payment Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payrolls as $index => $row)
                            @php
                                $st = $row->staff;
                                $isPaid = strtolower($row->payment_status) === 'paid';
                            @endphp
                            <tr>
                                <td class="text-center text-muted fw-bold">{{ $payrolls->firstItem() + $index }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border px-3 py-1 rounded-pill fw-semibold">
                                        {{ str_replace(' ', '-', $row->payroll_month) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-primary">{{ $st?->employee_id ?: 'EMP-'.$row->staff_id }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-slate-800">{{ $st?->full_name ?: 'N/A' }}</span>
                                </td>
                                <td>{{ $st?->department?->name ?: 'N/A' }}</td>
                                <td>{{ $st?->designation?->name ?: 'N/A' }}</td>
                                <td class="text-end fw-bold text-slate-800">₹{{ number_format($row->gross_salary, 2) }}</td>
                                <td class="text-end fw-bold" style="color: #ea580c;">
                                    ₹{{ number_format($row->attendance_deduction ?: 0, 2) }}
                                    @if((float)$row->attendance_deduction_days > 0)
                                        <div style="font-size: 10px; color: #64748b; font-weight: 600;">({{ (float)$row->attendance_deduction_days }}d extra)</div>
                                    @endif
                                </td>
                                <td class="text-end fw-bold text-rose-600">₹{{ number_format($row->deductions, 2) }}</td>
                                <td class="text-end fw-bold text-success" style="font-size: 14px;">₹{{ number_format($row->net_payable, 2) }}</td>
                                <td class="text-center">
                                    @if($isPaid)
                                        <span class="badge-paid"><i class="fas fa-check-circle me-1"></i> Paid</span>
                                    @else
                                        <span class="badge-unpaid"><i class="fas fa-clock me-1"></i> Unpaid</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn-view-details" onclick="openPayrollModal({{ $row->id }})">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-5 text-muted">
                                    <i class="fas fa-folder-open fa-2x mb-3 text-secondary d-block"></i>
                                    <span>No payroll records found for {{ $monthYearInput }}.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($payrolls->hasPages())
                <div class="p-4 border-top">
                    {{ $payrolls->links() }}
                </div>
            @endif
        </div>
    @endif
</div>

<!-- ================= PREMIUM DETAILS MODAL POPUP COLLECTION ================= -->
@foreach($payrolls as $row)
    @php
        $st = $row->staff;
        $isPaid = strtolower($row->payment_status) === 'paid';
        $struct = $st?->salaryStructure;
    @endphp
    <div class="plist-modal-overlay" id="viewModal{{ $row->id }}" onclick="if(event.target === this) closePayrollModal({{ $row->id }})">
        <div class="plist-modal-box">
            <!-- Modal Header -->
            <div class="plist-modal-hdr">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-file-invoice-dollar"></i> Payroll Details — {{ $st?->full_name }} ({{ $row->payroll_month }})
                </div>
                <button type="button" class="plist-modal-close" onclick="closePayrollModal({{ $row->id }})">&times;</button>
            </div>

            <!-- Modal Content -->
            <div style="padding: 22px;">
                <!-- Employee Summary Pill Box -->
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
                    <div>
                        <small style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Employee ID</small>
                        <div style="font-size: 14px; font-weight: 800; color: #1e3a8a;">{{ $st?->employee_id ?: 'EMP-'.$row->staff_id }}</div>
                    </div>
                    <div>
                        <small style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Department & Designation</small>
                        <div style="font-size: 14px; font-weight: 700; color: #0f172a;">{{ $st?->department?->name ?: 'N/A' }} / {{ $st?->designation?->name ?: 'N/A' }}</div>
                    </div>
                    <div>
                        <small style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; display: block; margin-bottom: 2px;">Payment Status</small>
                        @if($isPaid)
                            <span class="badge-paid"><i class="fas fa-check-circle me-1"></i> Paid</span>
                        @else
                            <span class="badge-unpaid"><i class="fas fa-clock me-1"></i> Unpaid</span>
                        @endif
                    </div>
                </div>

                <div style="font-size: 13.5px; font-weight: 800; color: #1e3a8a; margin-bottom: 14px; display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-calculator"></i> Salary Structure Breakdown (Read-Only)
                </div>

                <!-- 2 Column Breakdown Grid -->
                <div class="plist-breakdown-grid">
                    <!-- Earnings Card -->
                    <div style="border: 1px solid #bbf7d0; border-radius: 12px; padding: 16px; background: #ffffff;">
                        <div style="font-size: 12.5px; font-weight: 800; color: #15803d; border-bottom: 2px solid #dcfce7; padding-bottom: 8px; margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                            <i class="fas fa-arrow-trend-up"></i> EARNINGS & ALLOWANCES
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f1f5f9; font-size: 13px;">
                            <span style="color: #64748b; font-weight: 600;">Basic Salary:</span>
                            <span style="font-weight: 700; color: #0f172a;">₹{{ number_format($row->basic_salary, 2) }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f1f5f9; font-size: 13px;">
                            <span style="color: #64748b; font-weight: 600;">HRA:</span>
                            <span style="font-weight: 600; color: #334155;">₹{{ number_format($struct?->hra ?: 0, 2) }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f1f5f9; font-size: 13px;">
                            <span style="color: #64748b; font-weight: 600;">DA:</span>
                            <span style="font-weight: 600; color: #334155;">₹{{ number_format($struct?->da ?: 0, 2) }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f1f5f9; font-size: 13px;">
                            <span style="color: #64748b; font-weight: 600;">TA:</span>
                            <span style="font-weight: 600; color: #334155;">₹{{ number_format($struct?->ta ?: 0, 2) }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f1f5f9; font-size: 13px;">
                            <span style="color: #64748b; font-weight: 600;">Other Allowances:</span>
                            <span style="font-weight: 600; color: #334155;">₹{{ number_format($struct?->allowance ?: 0, 2) }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding-top: 12px; font-size: 14px; font-weight: 800;">
                            <span style="color: #0f172a;">Gross Salary:</span>
                            <span style="color: #2563eb;">₹{{ number_format($row->gross_salary, 2) }}</span>
                        </div>
                    </div>

                    <!-- Deductions Card -->
                    <div style="border: 1px solid #fecaca; border-radius: 12px; padding: 16px; background: #ffffff;">
                        <div style="font-size: 12.5px; font-weight: 800; color: #b91c1c; border-bottom: 2px solid #fee2e2; padding-bottom: 8px; margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                            <i class="fas fa-arrow-trend-down"></i> DEDUCTIONS & TAXES
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f1f5f9; font-size: 13px;">
                            <span style="color: #64748b; font-weight: 600;">PF:</span>
                            <span style="font-weight: 600; color: #334155;">₹{{ number_format($struct?->pf ?: 0, 2) }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f1f5f9; font-size: 13px;">
                            <span style="color: #64748b; font-weight: 600;">ESI:</span>
                            <span style="font-weight: 600; color: #334155;">₹{{ number_format($struct?->esi ?: 0, 2) }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f1f5f9; font-size: 13px;">
                            <span style="color: #64748b; font-weight: 600;">TDS:</span>
                            <span style="font-weight: 600; color: #334155;">₹{{ number_format($struct?->tds ?: 0, 2) }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f1f5f9; font-size: 13px;">
                            <span style="color: #64748b; font-weight: 600;">Prof. Tax:</span>
                            <span style="font-weight: 600; color: #334155;">₹{{ number_format($struct?->prof_tax ?: 0, 2) }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f1f5f9; font-size: 13px;">
                            <span style="color: #ea580c; font-weight: 700;">Att. Deduction:</span>
                            <span style="font-weight: 700; color: #ea580c;">₹{{ number_format($row->attendance_deduction ?: 0, 2) }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding-top: 12px; font-size: 14px; font-weight: 800;">
                            <span style="color: #0f172a;">Total Deductions:</span>
                            <span style="color: #dc2626;">₹{{ number_format($row->deductions, 2) }}</span>
                        </div>
                    </div>
                </div>

                @php
                    $attDedVal = (float)($row->attendance_deduction ?: 0);
                    $unpaidDeductionDays = (float)($row->attendance_deduction_days ?: 0);
                    $grossSalVal = (float)($row->gross_salary ?: 0);
                    $dailyRateVal = round($grossSalVal / 30, 2);
                    $absentCountVal = (float)($row->absent_days ?: 0);
                    $halfCountVal = (float)($row->half_days ?: 0);
                    $halfEquivVal = $halfCountVal * 0.5;
                    $effectiveAbsenceVal = $absentCountVal + $halfEquivVal;
                    $clAdjustedVal = max(0, $effectiveAbsenceVal - $unpaidDeductionDays);
                @endphp
                @if($attDedVal > 0 || $unpaidDeductionDays > 0 || $effectiveAbsenceVal > 0)
                    <div style="background: #fff7ed; border: 1px solid #ffedd5; border-radius: 12px; padding: 14px 18px; margin-top: 16px; margin-bottom: 16px;">
                        <div style="font-size: 13px; font-weight: 800; color: #c2410c; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                            <i class="fas fa-calculator"></i> Attendance Deduction Calculation Breakdown
                        </div>
                        <div style="font-size: 12.5px; color: #475569; line-height: 1.6;">
                            <div><strong>Gross Salary:</strong> ₹{{ number_format($grossSalVal, 2) }}</div>
                            <div><strong>Salary Calculation:</strong> ₹{{ number_format($grossSalVal, 2) }} &divide; 30 = <strong>₹{{ number_format($dailyRateVal, 2) }}/day</strong></div>
                            <div style="margin-top: 4px;"><strong>Attendance Breakdown:</strong></div>
                            <ul style="margin: 2px 0 6px 18px; padding: 0; list-style-type: disc;">
                                <li>Absent: <strong>{{ (float)$absentCountVal }} days</strong></li>
                                @if($halfCountVal > 0)
                                    <li>Half Days: <strong>{{ (float)$halfCountVal }}</strong> (Half Days Equivalent: <strong>{{ (float)$halfEquivVal }} full day</strong>)</li>
                                @endif
                            </ul>
                            <div><strong>Effective Absence:</strong> @if($halfCountVal > 0) {{ (float)$absentCountVal }} + {{ (float)$halfEquivVal }} = @endif <strong>{{ (float)$effectiveAbsenceVal }} days</strong></div>
                            @if($clAdjustedVal > 0)
                                <div style="margin-top: 4px;"><strong>CL Adjustment:</strong></div>
                                <div style="margin-left: 10px;">Available CL: <strong>{{ (float)$clAdjustedVal }} days</strong></div>
                                <div style="margin-left: 10px;">{{ (float)$effectiveAbsenceVal }} Effective Absence Days &minus; {{ (float)$clAdjustedVal }} CL Days = <strong>{{ (float)$unpaidDeductionDays }} Unpaid Deduction Days</strong></div>
                            @else
                                <div><strong>Unpaid Deduction Days:</strong> <strong>{{ (float)$unpaidDeductionDays }} days</strong></div>
                            @endif
                            <div style="margin-top: 6px; font-size: 13px; font-weight: 800; color: #9a3412;">
                                <strong>Attendance Deduction:</strong> ₹{{ number_format($dailyRateVal, 2) }} &times; {{ (float)$unpaidDeductionDays }} = <strong>₹{{ number_format($attDedVal, 2) }}</strong>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Net Salary Highlight Banner -->
                <div style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); color: #ffffff; padding: 16px 20px; border-radius: 12px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);">
                    <span style="font-size: 13.5px; font-weight: 800; letter-spacing: 0.5px;">NET PAYABLE SALARY:</span>
                    <span style="font-size: 20px; font-weight: 900; letter-spacing: 0.5px;">₹{{ number_format($row->net_payable, 2) }}</span>
                </div>
            </div>

            <!-- Modal Footer -->
            <div style="padding: 16px 24px; background: #f8fafc; border-top: 1px solid #e2e8f0; text-align: right; border-radius: 0 0 20px 20px;">
                <button type="button" class="btn btn-secondary fw-bold px-4" style="border-radius: 8px; font-size: 13px;" onclick="closePayrollModal({{ $row->id }})">Close</button>
            </div>
        </div>
    </div>
@endforeach

<script>
    function openPayrollModal(id) {
        var modal = document.getElementById('viewModal' + id);
        if (modal) {
            modal.classList.add('open');
            document.body.style.overflow = 'hidden';
        }
    }
    function closePayrollModal(id) {
        var modal = document.getElementById('viewModal' + id);
        if (modal) {
            modal.classList.remove('open');
            document.body.style.overflow = '';
        }
    }
</script>
@endsection
