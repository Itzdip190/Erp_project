@extends('layouts.app')

@section('title', 'Configure Payroll — HR Payroll')

@section('styles')
<style>
    .sal-container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 24px 30px;
        box-sizing: border-box;
    }
    .sal-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(37, 99, 235, 0.05);
        transition: all 0.25s ease;
        overflow: hidden;
    }
    .sal-card-hdr {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff;
        padding: 18px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .sal-config-body {
        padding: 22px 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 20px;
    }
    .sal-config-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .sal-card-body {
        padding: 24px;
    }
    .btn-configure-main {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
        color: #ffffff !important;
        border: none !important;
        padding: 11px 24px !important;
        border-radius: 10px !important;
        font-weight: 700 !important;
        font-size: 13.5px !important;
        letter-spacing: 0.2px !important;
        text-decoration: none !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3) !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    .btn-configure-main:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.45) !important;
        color: #ffffff !important;
    }
    .sal-filter-form {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 24px;
    }
    .sal-filter-search {
        position: relative;
        flex: 1;
        min-width: 260px;
    }
    .sal-filter-dept {
        min-width: 200px;
        font-weight: 600;
    }
    .sal-table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }
    .sal-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13px;
        min-width: 900px;
    }
    .sal-table th {
        background: #eff6ff;
        color: #1e40af;
        font-weight: 700;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 16px;
        border-bottom: 2px solid #dbeafe;
        white-space: nowrap;
    }
    .sal-table td {
        padding: 14px 16px;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
        white-space: nowrap;
        vertical-align: middle;
    }
    .sal-table tr:hover td {
        background-color: #f0f9ff;
    }
    .btn-edit-action {
        padding: 6px 14px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #2563eb;
        border-radius: 8px;
        font-weight: 700;
        font-size: 12px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }
    .btn-edit-action:hover {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
    }
    .sal-input {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        font-size: 13.5px;
        outline: none;
        background: #ffffff;
        color: #1e293b;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        transition: all 0.2s ease;
        box-sizing: border-box;
    }
    .sal-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }

    /* Responsive Mobile Media Queries */
    @media (max-width: 768px) {
        .sal-container {
            padding: 14px 10px !important;
        }
        .sal-card-hdr {
            padding: 14px 16px !important;
        }
        .sal-config-body {
            padding: 18px 16px !important;
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 16px !important;
        }
        .sal-config-left {
            gap: 12px !important;
        }
        .btn-configure-main {
            width: 100% !important;
        }
        .sal-card-body {
            padding: 16px 14px !important;
        }
        .sal-filter-form {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 12px !important;
        }
        .sal-filter-search, .sal-filter-dept {
            width: 100% !important;
            min-width: 100% !important;
        }
        .sal-table th, .sal-table td {
            padding: 10px 12px !important;
            font-size: 12px !important;
        }
    }

    /* Dark Mode Overrides */
    body.dark-mode .sal-card {
        background: #1e293b !important;
        border-color: #334155 !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2) !important;
    }
    body.dark-mode .sal-card-hdr {
        background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%) !important;
        color: #ffffff !important;
    }
    body.dark-mode .sal-card-body, body.dark-mode .sal-config-body {
        background: #1e293b !important;
        color: #f8fafc !important;
    }
    body.dark-mode .sal-title {
        color: #f8fafc !important;
    }
    body.dark-mode .sal-subtext {
        color: #94a3b8 !important;
    }
    body.dark-mode .sal-table-wrap {
        border-color: #334155 !important;
    }
    body.dark-mode .sal-table th {
        background: #0f172a !important;
        color: #93c5fd !important;
        border-color: #334155 !important;
    }
    body.dark-mode .sal-table td {
        color: #f1f5f9 !important;
        border-color: #334155 !important;
    }
    body.dark-mode .sal-table tr:hover td {
        background-color: #0f172a !important;
    }
    body.dark-mode .sal-input {
        background: #0f172a !important;
        color: #f8fafc !important;
        border-color: #334155 !important;
    }
    body.dark-mode .btn-edit-action {
        background: rgba(37, 99, 235, 0.15) !important;
        border-color: #3b82f6 !important;
        color: #60a5fa !important;
    }
    body.dark-mode .btn-edit-action:hover {
        background: #2563eb !important;
        color: #ffffff !important;
    }
    body.dark-mode .sal-badge-type {
        background: #0f172a !important;
        color: #93c5fd !important;
        border-color: #334155 !important;
    }
</style>
@endsection

@section('content')
<div class="sal-container">

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

    <!-- Configure Payroll Settings Card (Single Main Action Card) -->
    <div class="sal-card" style="margin-bottom: 24px;">
        <div class="sal-config-body">
            <div class="sal-config-left">
                <div style="width: 50px; height: 50px; border-radius: 14px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);">
                    <i class="fas fa-coins"></i>
                </div>
                <div>
                    <h2 class="sal-title" style="font-size: 17px; font-weight: 800; color: #1e3a8a; margin: 0 0 4px 0; letter-spacing: -0.01em;">
                        Configure Payroll Settings
                    </h2>
                    <p class="sal-subtext" style="font-size: 12.5px; color: #64748b; margin: 0; line-height: 1.4;">
                        Configure basic salary, allowances, statutory deductions, and effective structure per employee.
                    </p>
                </div>
            </div>
            <div>
                <a href="{{ route('school.payroll.salary-structure.configure') }}" class="btn-configure-main">
                    <i class="fas fa-sliders-h"></i> Configure Payroll
                </a>
            </div>
        </div>
    </div>

    <!-- Salary List Card -->
    <div class="sal-card">
        
        <!-- Header Banner (Vibrant Blue) -->
        <div class="sal-card-hdr">
            <div style="font-size: 16px; font-weight: 800; display: flex; align-items: center; gap: 10px; letter-spacing: 0.2px;">
                <i class="fas fa-table-list" style="color: #ffffff;"></i> Salary List
            </div>
            <div style="font-size: 12px; font-weight: 700; color: #1e40af; background: #ffffff; padding: 5px 14px; border-radius: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
                <i class="fas fa-user-check me-1" style="color: #2563eb;"></i> {{ $configuredCount }} Configured
            </div>
        </div>

        <div class="sal-card-body">
            <!-- Filter Bar -->
            <form method="GET" action="{{ route('school.payroll.salary-structure') }}" class="sal-filter-form">
                <div class="sal-filter-search">
                    <i class="fas fa-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px;"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search employee name or ID..." class="sal-input" style="padding-left: 38px;">
                </div>

                <div class="sal-filter-dept">
                    <select name="department_id" class="sal-input" style="font-weight: 600;">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $deptId == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <button type="submit" class="btn-configure-main" style="padding: 10px 22px !important;">
                        <i class="fas fa-filter"></i> Filter
                    </button>

                    @if($search || $deptId)
                        <a href="{{ route('school.payroll.salary-structure') }}" style="color: #ef4444; text-decoration: none; font-size: 13px; font-weight: 700; padding: 10px 16px; border: 1px solid #fecaca; background: #fef2f2; border-radius: 10px; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fas fa-sync-alt"></i> Reset
                        </a>
                    @endif
                </div>
            </form>

            <!-- Table Container -->
            <div class="sal-table-wrap">
                <table class="sal-table">
                    <thead>
                        <tr>
                            <th style="text-align: center; width: 60px;">S.NO</th>
                            <th>EMPLOYEE ID</th>
                            <th>NAME</th>
                            <th style="text-align: right;">BASIC SALARY</th>
                            <th style="text-align: right;">HRA</th>
                            <th style="text-align: right;">DA</th>
                            <th style="text-align: right;">TA</th>
                            <th style="text-align: right;">ALLOWANCE</th>
                            <th style="text-align: right;">PF</th>
                            <th style="text-align: right;">ESI</th>
                            <th style="text-align: right;">TDS</th>
                            <th style="text-align: right;">PROF. TAX</th>
                            <th style="text-align: center;">SALARY TYPE</th>
                            <th style="text-align: center;">EFFECTIVE FROM</th>
                            <th style="text-align: center;">EDIT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salaryStructures as $index => $struct)
                            @php
                                $st = $struct->staff;
                            @endphp
                            <tr>
                                <td style="text-align: center; color: #64748b; font-weight: 600;">
                                    {{ $salaryStructures->firstItem() + $index }}
                                </td>
                                <td style="font-weight: 700; color: #2563eb; font-family: monospace; font-size: 13px;">
                                    {{ $st?->employee_id ?: 'EMP-'.str_pad($st?->id, 4, '0', STR_PAD_LEFT) }}
                                </td>
                                <td>
                                    <div class="sal-title" style="font-weight: 700; color: #0f172a; font-size: 13.5px;">{{ $st?->full_name }}</div>
                                    <div class="sal-subtext" style="font-size: 11.5px; color: #64748b;">{{ $st?->designation?->name ?: ($st?->department?->name ?: 'Staff Member') }}</div>
                                </td>
                                <td style="text-align: right; font-weight: 800; color: #2563eb;">
                                    ₹{{ number_format($struct->basic_salary, 2) }}
                                </td>
                                <td style="text-align: right; font-weight: 600;">₹{{ number_format($struct->hra, 2) }}</td>
                                <td style="text-align: right; font-weight: 600;">₹{{ number_format($struct->da, 2) }}</td>
                                <td style="text-align: right; font-weight: 600;">₹{{ number_format($struct->ta, 2) }}</td>
                                <td style="text-align: right; font-weight: 600;">₹{{ number_format($struct->allowance, 2) }}</td>
                                <td style="text-align: right; color: #dc2626; font-weight: 600;">₹{{ number_format($struct->pf, 2) }}</td>
                                <td style="text-align: right; color: #dc2626; font-weight: 600;">₹{{ number_format($struct->esi, 2) }}</td>
                                <td style="text-align: right; color: #dc2626; font-weight: 600;">₹{{ number_format($struct->tds, 2) }}</td>
                                <td style="text-align: right; color: #dc2626; font-weight: 600;">₹{{ number_format($struct->prof_tax, 2) }}</td>
                                <td style="text-align: center;">
                                    <span class="sal-badge-type" style="background: #eff6ff; color: #1d4ed8; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; border: 1px solid #bfdbfe; text-transform: uppercase;">
                                        {{ $struct->salary_type }}
                                    </span>
                                </td>
                                <td style="text-align: center; font-weight: 600; font-size: 12.5px;">
                                    {{ $struct->effective_from ? \Carbon\Carbon::parse($struct->effective_from)->format('d M Y') : 'N/A' }}
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{ route('school.payroll.salary-structure.configure', ['id' => $struct->id]) }}" class="btn-edit-action">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="15" style="padding: 50px 20px; text-align: center; color: #64748b;">
                                    <div style="max-width: 360px; margin: 0 auto;">
                                        <div style="width: 64px; height: 64px; border-radius: 50%; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 28px; margin: 0 auto 16px auto;">
                                            <i class="fas fa-file-invoice-dollar"></i>
                                        </div>
                                        <div class="sal-title" style="font-weight: 800; font-size: 15px; color: #1e293b; margin-bottom: 6px;">No salary structures configured yet.</div>
                                        <div class="sal-subtext" style="font-size: 12.5px; color: #64748b; line-height: 1.5;">Click on "Configure Payroll" button above to set up employee salary structure.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($salaryStructures->hasPages())
                <div style="margin-top: 24px; display: flex; justify-content: flex-end;">
                    {{ $salaryStructures->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
