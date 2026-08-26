@extends('layouts.app')

@section('title', 'Trial Balance - Account Books')
@section('page-title', 'Trial Balance')

@section('styles')
<style>
/* ─── TRIAL BALANCE STYLES ─── */
:root {
    --tb-primary: #0038b8;
    --tb-primary-dark: #002d9c;
    --tb-primary-light: #eff4ff;
    --tb-border: #e2e8f0;
    --tb-discard-btn: #ef4444;
    --tb-discard-hover: #dc2626;
    --tb-excel-green: #107c41;
}

body.dark-mode {
    --tb-border: #334155;
    --tb-primary-light: rgba(0, 56, 184, 0.15);
}

.tb-card {
    background: #ffffff;
    border: 1px solid var(--tb-border);
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    margin-bottom: 20px;
    overflow: hidden;
}

body.dark-mode .tb-card {
    background: #1e293b;
}

/* Card Headers with Image 2 Royal Blue */
.tb-card-header {
    background: #0038b8;
    background: linear-gradient(135deg, #0038b8 0%, #084ac9 100%);
    color: #ffffff;
    padding: 13px 20px;
    font-weight: 700;
    font-size: 14.5px;
    letter-spacing: 0.2px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.tb-card-header i {
    font-size: 15px;
    opacity: 0.95;
}

.tb-card-body {
    padding: 18px 20px;
}

/* Filter Controls - Horizontal Grid */
.tb-filter-grid {
    display: grid;
    grid-template-columns: 1.2fr 1fr 1fr auto;
    gap: 16px;
    align-items: flex-end;
}

@media (max-width: 991px) {
    .tb-filter-grid {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 576px) {
    .tb-filter-grid {
        grid-template-columns: 1fr;
    }
}

.tb-filter-item {
    display: flex;
    flex-direction: column;
}

.tb-filter-label {
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 6px;
    display: block;
}

body.dark-mode .tb-filter-label {
    color: #94a3b8;
}

.tb-form-control {
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 13px;
    padding: 8px 14px;
    height: 40px;
    color: #1e293b;
    background-color: #ffffff;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    width: 100%;
}

body.dark-mode .tb-form-control {
    background-color: #0f172a;
    border-color: #334155;
    color: #f8fafc;
}

.tb-form-control:focus {
    border-color: var(--tb-primary);
    outline: 0;
    box-shadow: 0 0 0 3px rgba(0, 56, 184, 0.15);
}

/* Discard Button */
.btn-tb-discard {
    background-color: var(--tb-discard-btn);
    color: #ffffff;
    font-size: 13px;
    font-weight: 600;
    padding: 8px 24px;
    height: 40px;
    border-radius: 6px;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    text-decoration: none;
    transition: all 0.2s;
    white-space: nowrap;
}

.btn-tb-discard:hover {
    background-color: var(--tb-discard-hover);
    color: #ffffff;
    transform: translateY(-1px);
}

/* Excel Download Button (Image 1 Green Badge) */
.btn-excel-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 5px;
    color: var(--tb-excel-green);
    text-decoration: none;
    transition: all 0.2s ease-in-out;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}

.btn-excel-badge:hover {
    background: #f0fdf4;
    border-color: #86efac;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16, 124, 65, 0.2);
}

.btn-excel-badge svg {
    display: block;
    width: 28px;
    height: 28px;
}

/* Table Styling */
.tb-table-responsive {
    overflow-x: auto;
}

.tb-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
    color: #1e293b;
    margin-bottom: 0;
}

body.dark-mode .tb-table {
    color: #e2e8f0;
}

.tb-table th {
    background: #f8fafc;
    color: #334155;
    font-weight: 700;
    font-size: 12.5px;
    padding: 14px 16px;
    border-bottom: 1.5px solid var(--tb-border);
    border-top: 1px solid var(--tb-border);
    white-space: nowrap;
}

body.dark-mode .tb-table th {
    background: #0f172a;
    color: #cbd5e1;
}

.tb-table td {
    padding: 12px 16px;
    border-bottom: 1px solid var(--tb-border);
    vertical-align: middle;
}

.tb-table tbody tr {
    transition: background-color 0.15s;
}

.tb-table tbody tr:hover {
    background-color: #f8fafc;
}

body.dark-mode .tb-table tbody tr:hover {
    background-color: #1e293b;
}

.tb-table tfoot th,
.tb-table tfoot td {
    background: #f8fafc;
    font-weight: 800;
    font-size: 13.5px;
    padding: 14px 16px;
    border-top: 2px solid #cbd5e1;
    border-bottom: 2px solid #cbd5e1;
}

body.dark-mode .tb-table tfoot th,
body.dark-mode .tb-table tfoot td {
    background: #0f172a;
    border-color: #334155;
}

/* Account Type Badges */
.tb-badge {
    font-size: 11px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 6px;
    display: inline-block;
    letter-spacing: 0.2px;
}

.tb-badge-asset {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
}

.tb-badge-income {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
}

.tb-badge-expense {
    background: #fff7ed;
    color: #c2410c;
    border: 1px solid #fed7aa;
}

.tb-badge-liability {
    background: #faf5ff;
    color: #7e22ce;
    border: 1px solid #e9d5ff;
}

body.dark-mode .tb-badge-asset {
    background: rgba(29, 78, 216, 0.2);
    color: #93c5fd;
    border-color: rgba(147, 197, 253, 0.3);
}
body.dark-mode .tb-badge-income {
    background: rgba(4, 120, 87, 0.2);
    color: #6ee7b7;
    border-color: rgba(110, 231, 183, 0.3);
}
body.dark-mode .tb-badge-expense {
    background: rgba(194, 65, 12, 0.2);
    color: #fdba74;
    border-color: rgba(253, 186, 116, 0.3);
}
body.dark-mode .tb-badge-liability {
    background: rgba(126, 34, 206, 0.2);
    color: #d8b4fe;
    border-color: rgba(216, 180, 254, 0.3);
}
</style>
@endsection

@section('content')
<div class="container-fluid px-0 pt-2">
    <!-- 1. Search Trial Balance Card (Horizontal Filter Bar) -->
    <div class="tb-card">
        <div class="tb-card-header">
            <i class="fa-solid fa-list-check"></i>
            <span>Search Trial Balance</span>
        </div>
        <div class="tb-card-body">
            <form id="trialBalanceFilterForm" action="{{ route('school.account-books.trial-balance') }}" method="GET">
                <div class="tb-filter-grid">
                    <!-- Session Select -->
                    <div class="tb-filter-item">
                        <label class="tb-filter-label">Session</label>
                        <select name="session_id" class="tb-form-control form-select" onchange="document.getElementById('trialBalanceFilterForm').submit();">
                            @foreach($academicSessions as $session)
                                <option value="{{ $session->id }}" {{ ($selectedSession && $selectedSession->id == $session->id) ? 'selected' : '' }}>
                                    {{ $session->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- From Date -->
                    <div class="tb-filter-item">
                        <label class="tb-filter-label">From Date</label>
                        <input type="date" name="from_date" value="{{ $fromDate }}" class="tb-form-control" onchange="document.getElementById('trialBalanceFilterForm').submit();">
                    </div>

                    <!-- To Date -->
                    <div class="tb-filter-item">
                        <label class="tb-filter-label">To Date</label>
                        <input type="date" name="to_date" value="{{ $toDate }}" class="tb-form-control" onchange="document.getElementById('trialBalanceFilterForm').submit();">
                    </div>

                    <!-- Discard Button -->
                    <div class="tb-filter-item">
                        <a href="{{ route('school.account-books.trial-balance') }}" class="btn-tb-discard" title="Reset Filters">
                            <span>Discard</span>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. Trial Balance List Card -->
    <div class="tb-card">
        <div class="tb-card-header">
            <i class="fa-solid fa-table-cells"></i>
            <span>Trial Balance List</span>
        </div>
        <div class="tb-card-body">
            <!-- Top Controls (Excel Export) -->
            <div class="d-flex justify-content-end align-items-center mb-3">
                <a href="{{ route('school.account-books.trial-balance.export-excel', request()->query()) }}" class="btn-excel-badge" title="Download Excel Spreadsheet" data-bs-toggle="tooltip">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
                        <path fill="#4CAF50" d="M41,10H25v28h16c0.552,0,1-0.448,1-1V11C42,10.448,41.552,10,41,10z"/>
                        <path fill="#FFF" d="M32 15H39V18H32zM32 25H39V28H32zM32 30H39V33H32zM32 20H39V23H32zM25 15H30V18H25zM25 25H30V28H25zM25 30H30V33H25zM25 20H30V23H25z"/>
                        <path fill="#2E7D32" d="M27 42L6 38 6 10 27 6z"/>
                        <path fill="#FFF" d="M19.129,31l-2.411-4.561c-0.092-0.171-0.186-0.483-0.284-0.938h-0.037c-0.046,0.205-0.154,0.533-0.322,0.984L13.571,31h-3.041l3.882-5.904l-3.565-5.918h3.136l2.128,4.352c0.153,0.341,0.274,0.672,0.364,0.995h0.037c0.076-0.264,0.203-0.603,0.383-1.018l2.256-4.329h2.956l-3.666,5.885L22.217,31H19.129z"/>
                    </svg>
                </a>
            </div>

            <!-- Table -->
            <div class="tb-table-responsive">
                <table class="tb-table" id="trialBalanceTable">
                    <thead>
                        <tr>
                            <th style="width: 60px;">S/N</th>
                            <th>Account Head</th>
                            <th style="width: 150px;">Account Type</th>
                            <th class="text-end" style="width: 180px;">Debit</th>
                            <th class="text-end" style="width: 180px;">Credit</th>
                            <th class="text-end" style="width: 180px;">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                            <tr>
                                <td class="fw-semibold text-muted">{{ $row['sn'] }}</td>
                                <td class="fw-bold" style="color: var(--t1, #1e293b);">{{ $row['head_name'] }}</td>
                                <td>
                                    @php
                                        $typeClass = match(strtolower($row['account_type'])) {
                                            'asset'     => 'tb-badge-asset',
                                            'income'    => 'tb-badge-income',
                                            'expense'   => 'tb-badge-expense',
                                            'liability' => 'tb-badge-liability',
                                            default     => 'tb-badge-asset'
                                        };
                                    @endphp
                                    <span class="tb-badge {{ $typeClass }}">{{ $row['account_type'] }}</span>
                                </td>
                                <td class="text-end font-monospace fw-semibold">
                                    {{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '0.00' }}
                                </td>
                                <td class="text-end font-monospace fw-semibold">
                                    {{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '0.00' }}
                                </td>
                                <td class="text-end font-monospace fw-bold {{ $row['balance'] < 0 ? 'text-danger' : 'text-dark' }}">
                                    {{ number_format($row['balance'], 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-scale-balanced fa-2x mb-2 d-block opacity-25"></i>
                                    No accounts or transactions found for the selected session and date range.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td></td>
                            <td class="fw-bold">Total</td>
                            <td></td>
                            <td class="text-end font-monospace fw-bold" style="color: var(--tb-primary);">
                                {{ number_format($totalDebit, 2) }}
                            </td>
                            <td class="text-end font-monospace fw-bold" style="color: var(--tb-primary);">
                                {{ number_format($totalCredit, 2) }}
                            </td>
                            <td class="text-end font-monospace fw-bold {{ $netBalance < 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($netBalance, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection


