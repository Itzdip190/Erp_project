@extends('layouts.app')

@section('title', 'Generate Payroll — HR Payroll')

@section('styles')
<style>
    .gen-container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 24px 30px;
        box-sizing: border-box;
    }
    .gen-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(37, 99, 235, 0.05);
        overflow: hidden;
        margin-bottom: 28px;
    }
    .gen-card-hdr, .gen-card-hdr-blue {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        color: #ffffff;
        padding: 16px 24px;
        font-size: 16px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        letter-spacing: 0.2px;
        flex-wrap: wrap;
    }
    .gen-card-body {
        padding: 26px 30px;
    }
    .gen-form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }
    .gen-form-col {
        width: 100%;
    }
    .gen-label {
        font-size: 11.5px;
        font-weight: 800;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .gen-input {
        width: 100%;
        padding: 11px 16px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        color: #1e293b;
        outline: none;
        background: #ffffff;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        box-sizing: border-box;
        transition: all 0.2s ease;
    }
    .gen-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }
    .btn-generate-main {
        padding: 12px 40px;
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        font-size: 14.5px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-width: 180px;
    }
    .btn-generate-main:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.45);
        color: #ffffff;
    }
    .badge-generated {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
        padding: 5px 14px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }
    .btn-delete-payroll {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
        padding: 6px 14px;
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
    .btn-delete-payroll:hover {
        background: #dc2626;
        color: #ffffff;
        border-color: #dc2626;
    }
    .gen-table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .history-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 620px;
    }
    .history-table th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 18px;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }
    .history-table td {
        padding: 14px 18px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13.5px;
        color: #334155;
        font-weight: 600;
        white-space: nowrap;
    }
    .history-table tr:hover td {
        background: #f8fafc;
    }

    /* Responsive Mobile Media Queries */
    @media (max-width: 768px) {
        .gen-container {
            padding: 14px 10px !important;
        }
        .gen-card-hdr, .gen-card-hdr-blue {
            padding: 14px 16px !important;
            font-size: 15px !important;
        }
        .gen-card-body {
            padding: 18px 16px !important;
        }
        .history-table th, .history-table td {
            padding: 12px 14px !important;
            font-size: 12.5px !important;
        }
    }
    @media (max-width: 540px) {
        .gen-form-grid {
            grid-template-columns: 1fr !important;
            gap: 14px !important;
        }
        .btn-generate-main {
            width: 100% !important;
        }
    }

    /* Dark Mode Overrides */
    body.dark-mode .gen-card {
        background: #1e293b !important;
        border-color: #334155 !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2) !important;
    }
    body.dark-mode .gen-card-hdr, body.dark-mode .gen-card-hdr-blue {
        background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%) !important;
    }
    body.dark-mode .gen-card-body {
        background: #1e293b !important;
        color: #f8fafc !important;
    }
    body.dark-mode .gen-label {
        color: #cbd5e1 !important;
    }
    body.dark-mode .gen-input {
        background: #0f172a !important;
        color: #f8fafc !important;
        border-color: #334155 !important;
    }
    body.dark-mode .history-table th {
        background: #0f172a !important;
        color: #93c5fd !important;
        border-color: #334155 !important;
    }
    body.dark-mode .history-table td {
        color: #f1f5f9 !important;
        border-color: #334155 !important;
    }
    body.dark-mode .history-table tr:hover td {
        background-color: #0f172a !important;
    }
</style>
@endsection

@section('content')
<div class="gen-container">
    <!-- System Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" style="background:#f0fdf4; border-left: 4px solid #22c55e !important; color:#15803d;" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" style="background:#fef2f2; border-left: 4px solid #ef4444 !important; color:#b91c1c;" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Card 1: Generate Payroll -->
    <div class="gen-card">
        <div class="gen-card-hdr">
            <div style="display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-file-invoice-dollar"></i> Generate Payroll
            </div>
        </div>
        <div class="gen-card-body">
            <form action="{{ route('school.payroll.process-generate') }}" method="POST">
                @csrf
                
                <div class="gen-form-grid">
                    <div class="gen-form-col">
                        <label class="gen-label">
                            <i class="fas fa-calendar-alt text-primary"></i> Select Month
                        </label>
                        <select name="salary_month" class="gen-input" required>
                            <option value="">- Select Month -</option>
                            @foreach($months as $m)
                                <option value="{{ $m }}" {{ $selectedMonth === $m ? 'selected' : '' }}>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="gen-form-col">
                        <label class="gen-label">
                            <i class="fas fa-calendar-check text-primary"></i> Select Year
                        </label>
                        <select name="salary_year" class="gen-input" required>
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display: flex; justify-content: center; margin-top: 8px;">
                    <button type="submit" class="btn-generate-main">
                        <i class="fas fa-cog me-1"></i> Generate Payroll
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Card 2: Payroll History -->
    <div class="gen-card">
        <div class="gen-card-hdr-blue">
            <div style="display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-list-ul"></i> Payroll History
            </div>
            <span style="font-size: 12px; font-weight: 700; background: rgba(255,255,255,0.18); color: #ffffff; padding: 4px 12px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.25);">
                {{ $history->count() }} Records
            </span>
        </div>
        <div class="gen-table-wrap">
            <table class="history-table align-middle">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Year</th>
                        <th>Generated On</th>
                        <th>Total Employees</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $item)
                        @php
                            $mName = $item->salary_month ?: date('F', strtotime($item->payroll_month));
                            $yName = $item->salary_year ?: date('Y', strtotime($item->payroll_month));
                        @endphp
                        <tr>
                            <td class="fw-bold text-slate-800">{{ $mName }}</td>
                            <td>{{ $yName }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->generated_on)->format('d M Y') }}</td>
                            <td>
                                <span class="badge bg-light text-dark border px-3 py-1 rounded-pill fw-bold">
                                    {{ $item->total_employees }} Employees
                                </span>
                            </td>
                            <td>
                                <span class="badge-generated">
                                    <i class="fas fa-check-circle"></i> Generated
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <form action="{{ route('school.payroll.delete-generated') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete generated payroll records for {{ $item->payroll_month }}? Salary structures will not be deleted.');" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="payroll_month" value="{{ $item->payroll_month }}">
                                    <button type="submit" class="btn-delete-payroll">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-2x mb-3 text-secondary d-block"></i>
                                <span>No payroll generation history records found. Select month & year above to generate.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
