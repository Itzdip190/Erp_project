@extends('layouts.app')

@php
    $sch = app()->bound('currentSchool') ? app('currentSchool') : (auth()->check() ? auth()->user()->school : null);
    $sess = $sch ? \App\Models\AcademicSession::where('school_id', $sch->id)->where('is_current', true)->first() : null;
@endphp

@section('title', 'Manage Vouchers DateWise')

@section('styles')
<style>
/* ─── VARIABLES ──────────────────────────────── */
:root {
    --exp-blue:      #3b82f6;
    --exp-blue-dark: #1d4ed8;
    --exp-blue-light:#eff6ff;
    --exp-white:     #ffffff;
    --exp-gray:      #f8fafc;
    --exp-border:    #cbd5e1;
    --exp-text:      #1e293b;
    --exp-text2:     #64748b;
    --exp-red:       #ef4444;
    --exp-green:     #10b981;
    --exp-green-hover: #059669;
    --exp-amber:     #f59e0b;
    --exp-orange:    #f97316;
    --exp-yellow:    #eab308;
    
    --shadow-sm: 0 1px 3px rgba(0,0,0,.05);
    --shadow-md: 0 4px 16px rgba(0,0,0,.08);
}

body.dark-mode {
    --exp-white:     #111827;
    --exp-gray:      #1f2937;
    --exp-border:    #374151;
    --exp-text:      #f8fafc;
    --exp-text2:     #94a3b8;
    --exp-blue-light:rgba(59, 130, 246, 0.15);
}

/* ─── CONTAINER & BREADCRUMB ─────────────────── */
.exp-container {
    padding: 24px;
    width: 100%;
}
.exp-hdr-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 12px;
}
.exp-title {
    font-size: 20px;
    font-weight: 800;
    color: var(--exp-text);
}
.exp-btn-green {
    background-color: var(--exp-green);
    color: #fff;
    border: none;
    padding: 9px 18px;
    font-size: 13px;
    font-weight: 700;
    border-radius: 8px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: background-color 0.2s;
}
.exp-btn-green:hover {
    background-color: var(--exp-green-hover);
}

/* ─── FILTER BAR ─────────────────────────────── */
.exp-filter-card {
    background: var(--exp-white);
    border: 1px solid var(--exp-border);
    border-radius: 12px;
    padding: 16px 20px;
    margin-bottom: 24px;
    box-shadow: var(--shadow-sm);
}
.exp-filter-form {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}
.filter-group {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.filter-group label {
    font-size: 11.5px;
    font-weight: 700;
    color: var(--exp-text);
}
.filter-control {
    height: 36px;
    padding: 6px 12px;
    border: 1px solid var(--exp-border);
    border-radius: 6px;
    font-size: 12.5px;
    font-weight: 600;
    background: var(--exp-white);
    color: var(--exp-text);
    outline: none;
}
.exp-btn-go {
    background-color: #2563eb;
    color: #fff;
    border: none;
    height: 36px;
    padding: 0 16px;
    font-size: 12.5px;
    font-weight: 700;
    border-radius: 6px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    align-self: flex-end;
}
.exp-btn-go:hover {
    background-color: #1d4ed8;
}

/* ─── KPI CARDS ──────────────────────────────── */
.exp-kpis {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
    align-items: center;
}
.kpi-card {
    border-radius: 12px;
    color: #fff;
    padding: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: var(--shadow-sm);
    min-height: 80px;
}
.kpi-card.blue   { background: linear-gradient(135deg, #60a5fa 0%, #2563eb 100%); }
.kpi-card.green  { background: linear-gradient(135deg, #34d399 0%, #059669 100%); }
.kpi-card.orange { background: linear-gradient(135deg, #fb923c 0%, #ea580c 100%); }

.kpi-left {
    display: flex;
    flex-direction: column;
}
.kpi-title {
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    opacity: 0.9;
}
.kpi-value {
    font-size: 20px;
    font-weight: 800;
    margin-top: 4px;
}
.kpi-icon {
    font-size: 28px;
    opacity: 0.45;
}

.exp-btn-excel {
    background-color: var(--exp-yellow);
    color: #1e293b;
    border: none;
    padding: 8px 16px;
    font-size: 12.5px;
    font-weight: 700;
    border-radius: 6px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
}
.exp-btn-excel:hover {
    background-color: #d97706;
    color: #fff;
}

/* ─── TABLE AREA ─────────────────────────────── */
.vouchers-card {
    background: var(--exp-white);
    border: 1px solid var(--exp-border);
    border-radius: 12px;
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}
.sort-row {
    padding: 12px 20px;
    background: var(--exp-gray);
    border-bottom: 1px solid var(--exp-border);
    display: flex;
    justify-content: flex-start;
    align-items: center;
    gap: 8px;
}
.exp-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.exp-table th {
    background: var(--exp-gray);
    color: var(--exp-text);
    font-weight: 700;
    padding: 12px 14px;
    border-bottom: 2px solid var(--exp-border);
    text-transform: uppercase;
    font-size: 11px;
    letter-spacing: 0.3px;
}
.exp-table td {
    padding: 12px 14px;
    border-bottom: 1px solid var(--exp-border);
    color: var(--exp-text);
    vertical-align: middle;
}
.exp-table tr:hover {
    background: var(--exp-blue-light);
}

/* Status Badges */
.badge {
    padding: 4px 8px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    display: inline-block;
}
.badge.approved { background-color: #d1fae5; color: #065f46; }
.badge.pending  { background-color: #fef3c7; color: #92400e; }
.badge.rejected { background-color: #fee2e2; color: #991b1b; }
.badge.partial  { background-color: #ffedd5; color: #9a3412; }
.badge.paid     { background-color: #d1fae5; color: #065f46; }
.badge.deleted  { background-color: #e2e8f0; color: #475569; }

/* ─── ACTION ICONS ───────────────────────────── */
.act-icon {
    font-size: 15px;
    cursor: pointer;
    transition: transform 0.15s;
    background: none; border: none; padding: 4px;
    display: inline-flex; align-items: center; justify-content: center;
}
.act-icon:hover { transform: scale(1.2); }
.act-icon.pay      { color: #d97706; }
.act-icon.details  { color: #3b82f6; }
.act-icon.add-pay  { color: #10b981; }
.act-icon.print    { color: #64748b; }
.act-icon.reject   { color: #ef4444; }
.act-icon.delete   { color: #ef4444; }

/* ─── MODALS ─────────────────────────────────── */
.exp-modal-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(4px);
    z-index: 2000;
    display: flex; align-items: center; justify-content: center;
    opacity: 0; pointer-events: none;
    transition: opacity 0.28s ease;
}
.exp-modal-overlay.open {
    opacity: 1; pointer-events: auto;
}
.exp-modal {
    background: var(--exp-white);
    border: 1px solid var(--exp-border);
    border-radius: 12px;
    width: 100%; max-width: 500px;
    box-shadow: var(--shadow-md);
    overflow: hidden;
    transform: translateY(20px);
    transition: transform 0.28s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.exp-modal-overlay.open .exp-modal {
    transform: translateY(0);
}
.exp-modal-hdr {
    background: #0ea5e9; /* Light blue/teal bar */
    padding: 14px 18px;
    display: flex; align-items: center; justify-content: space-between;
    color: #fff;
}
.exp-modal-hdr.payment-hdr {
    background: var(--exp-green);
}
.exp-modal-hdr h3 {
    margin: 0; font-size: 15px; font-weight: 700;
}
.modal-close {
    background: none; border: none; color: #fff; font-size: 18px; cursor: pointer;
}
.exp-modal-body {
    padding: 20px;
}
.form-group {
    margin-bottom: 14px;
}
.form-group label {
    display: block; font-size: 12px; font-weight: 700;
    color: var(--exp-text); margin-bottom: 4px;
}
.form-group label span { color: var(--exp-red); }
.form-control {
    width: 100%; height: 36px; padding: 6px 10px;
    border: 1px solid var(--exp-border); border-radius: 6px;
    font-size: 12.5px; font-weight: 500; font-family: inherit;
    background: var(--exp-white); color: var(--exp-text);
    outline: none;
}
.form-control:focus {
    border-color: #3b82f6;
}
textarea.form-control {
    height: 60px;
}
.modal-footer {
    display: flex; align-items: center; justify-content: center;
    margin-top: 18px;
}
.exp-btn-submit {
    background: none;
    border: 1px solid #3b82f6;
    color: #3b82f6;
    padding: 8px 30px;
    font-size: 12.5px;
    font-weight: 700;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.2s;
    text-transform: uppercase;
}
.exp-btn-submit:hover {
    background: #3b82f6;
    color: #fff;
}

/* Toast */
#exp-toast {
    position: fixed; bottom: 20px; right: 20px; z-index: 2500;
    display: flex; flex-direction: column; gap: 10px;
}
.toast-msg {
    background: var(--exp-white); border: 1px solid var(--exp-border);
    padding: 12px 20px; border-radius: 10px; box-shadow: var(--shadow-md);
    display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 600;
    animation: slideIn 0.3s forwards;
}
@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

/* Invoice Modal Styles */
#invoiceReceiptModal .exp-modal {
    max-width: 650px;
    background: #fff;
    color: #000;
}
body.dark-mode #invoiceReceiptModal .exp-modal {
    background: #fff !important;
    color: #000 !important;
}
body.dark-mode #invoiceReceiptModal .modal-close {
    color: #000 !important;
}
.receipt-card {
    background: #fff;
    padding: 24px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-family: 'Outfit', 'Inter', sans-serif;
    color: #000 !important;
}
.receipt-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid #000;
    padding-bottom: 12px;
    margin-bottom: 16px;
}
.receipt-logo {
    width: 64px;
    height: 64px;
    object-fit: contain;
}
.receipt-logo-placeholder {
    width: 64px;
    height: 64px;
    border: 1px dashed #cbd5e1;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #94a3b8;
}
.receipt-school-info {
    text-align: right;
}
.receipt-school-name {
    font-size: 18px;
    font-weight: 800;
    text-transform: uppercase;
    color: #1e3a8a;
    margin: 0;
}
.receipt-school-address {
    font-size: 11px;
    color: #475569;
    margin: 2px 0 0 0;
}
.receipt-title-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}
.receipt-title {
    font-size: 14px;
    font-weight: 800;
    border: 1.5px solid #000;
    padding: 3px 10px;
    text-transform: uppercase;
    color: #000 !important;
}
.receipt-meta-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px 24px;
    font-size: 12px;
    margin-bottom: 16px;
    background: #f8fafc;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
}
.receipt-meta-item {
    display: flex;
    justify-content: space-between;
}
.receipt-meta-lbl {
    font-weight: 700;
    color: #475569;
}
.receipt-meta-val {
    font-weight: 600;
    color: #0f172a;
}
.receipt-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 16px;
    font-size: 12px;
    color: #000 !important;
}
.receipt-table th, .receipt-table td {
    border: 1px solid #cbd5e1;
    padding: 8px 12px;
    color: #000 !important;
}
.receipt-table th {
    background: #f1f5f9;
    font-weight: 700;
    text-transform: uppercase;
    text-align: left;
}
.receipt-words {
    font-size: 11px;
    font-style: italic;
    margin-bottom: 16px;
    font-weight: 600;
    color: #334155;
    text-transform: uppercase;
}
.receipt-signatures {
    display: flex;
    justify-content: space-between;
    margin-top: 36px;
    font-size: 12px;
}
.receipt-sig-line {
    border-top: 1px solid #000;
    width: 120px;
    text-align: center;
    padding-top: 4px;
    font-weight: 700;
}

/* Printing styles */
@media print {
    /* Hide layout structures completely */
    .sidebar, .topbar, .exp-container, .exp-modal-hdr, .receipt-actions-row, .modal-close {
        display: none !important;
    }
    
    /* Reset main and page wrappers */
    .main {
        margin-left: 0 !important;
        padding: 0 !important;
    }
    .pg {
        padding: 0 !important;
        margin: 0 !important;
    }
    
    /* Ensure the modal overlay behaves as a normal full-width block */
    #invoiceReceiptModal {
        position: relative !important;
        background: #fff !important;
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
        z-index: auto !important;
        inset: auto !important;
    }
    #invoiceReceiptModal .exp-modal {
        box-shadow: none !important;
        border: none !important;
        max-width: 100% !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        transform: none !important;
        background: #fff !important;
    }
    .receipt-card {
        border: none !important;
        padding: 0 !important;
    }
}
</style>
@endsection

@section('content')
<div class="exp-container">
    <div class="exp-hdr-row">
        <h2 class="exp-title">Manage Vouchers DateWise</h2>
        <div style="display: flex; gap: 8px;">
            <button class="exp-btn-outline" style="border-color: var(--exp-blue); color: var(--exp-blue-dark); padding: 8px 16px; border-radius:20px; font-weight:700; font-size:12.5px; cursor:pointer;" onclick="openPrintAllConfig('voucher')">
                Print All Vouchers <i class="fas fa-print"></i>
            </button>
            <button class="exp-btn-green" id="addVoucherBtn">
                <i class="fas fa-plus"></i> Add New Voucher
            </button>
        </div>
    </div>

    {{-- FILTERS --}}
    <div class="exp-filter-card">
        <form method="GET" action="{{ route('school.expenses.vouchers.datewise') }}" class="exp-filter-form" id="filterForm">
            <div class="filter-group">
                <label>Start Date</label>
                <input type="date" class="filter-control" name="start_date" value="{{ $startDate }}">
            </div>
            <div class="filter-group">
                <label>End Date</label>
                <input type="date" class="filter-control" name="end_date" value="{{ $endDate }}">
            </div>
            <div class="filter-group">
                <label>Status</label>
                <select class="filter-control" name="payment_status">
                    <option value="All" {{ $paymentStatus === 'All' ? 'selected' : '' }}>All</option>
                    <option value="Pending" {{ $paymentStatus === 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Partial" {{ $paymentStatus === 'Partial' ? 'selected' : '' }}>Partial</option>
                    <option value="Paid" {{ $paymentStatus === 'Paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Approval Status</label>
                <select class="filter-control" name="approval_status">
                    <option value="All" {{ $approvalStatus === 'All' ? 'selected' : '' }}>All</option>
                    <option value="Approved" {{ $approvalStatus === 'Approved' ? 'selected' : '' }}>Approved</option>
                    <option value="Pending" {{ $approvalStatus === 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Rejected" {{ $approvalStatus === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Expense Head</label>
                <select class="filter-control" name="expense_head_id">
                    <option value="All">All</option>
                    @foreach($expenseHeads as $head)
                    <option value="{{ $head->id }}" {{ (string)$expenseHeadId === (string)$head->id ? 'selected' : '' }}>{{ $head->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group" style="flex-direction: row; align-items: center; gap: 8px; margin-top: 18px;">
                <input type="checkbox" name="show_deleted" id="show_deleted" value="1" {{ $showDeleted ? 'checked' : '' }}>
                <label for="show_deleted" style="margin-bottom: 0; cursor: pointer;">Show Deleted</label>
            </div>
            <button type="submit" class="exp-btn-go">Go <i class="fas fa-arrow-right"></i></button>
        </form>
    </div>

    {{-- KPI BLOCKS & EXCEL --}}
    <div class="exp-kpis">
        <div class="kpi-card blue">
            <div class="kpi-left">
                <span class="kpi-title">Total</span>
                <span class="kpi-value">{{ number_format($totalAmount, 2) }}</span>
            </div>
            <i class="fas fa-sack-dollar kpi-icon"></i>
        </div>
        <div class="kpi-card green">
            <div class="kpi-left">
                <span class="kpi-title">Total Paid</span>
                <span class="kpi-value">{{ number_format($totalPaid, 2) }}</span>
            </div>
            <i class="fas fa-circle-check kpi-icon"></i>
        </div>
        <div class="kpi-card orange">
            <div class="kpi-left">
                <span class="kpi-title">Total Due</span>
                <span class="kpi-value">{{ number_format($totalDue, 2) }}</span>
            </div>
            <i class="fas fa-circle-question kpi-icon"></i>
        </div>
        <div>
            <a href="{{ route('school.expenses.vouchers.export', request()->all()) }}" class="exp-btn-excel">
                Export Excel <i class="fas fa-download"></i>
            </a>
        </div>
    </div>

    {{-- TABLE DATA --}}
    <div class="vouchers-card">
        <div class="sort-row">
            <span style="font-size: 12px; font-weight: 700; color: var(--exp-text2);">Sort by:</span>
            <select class="filter-control" style="height: 30px; font-size:11.5px; padding: 2px 6px;" id="sortSelector" onchange="applySort(this.value)">
                <option value="date_desc" {{ $sortBy === 'date_desc' ? 'selected' : '' }}>Date (Newest)</option>
                <option value="date_asc" {{ $sortBy === 'date_asc' ? 'selected' : '' }}>Date (Oldest)</option>
                <option value="amount_desc" {{ $sortBy === 'amount_desc' ? 'selected' : '' }}>Amount (High to Low)</option>
                <option value="amount_asc" {{ $sortBy === 'amount_asc' ? 'selected' : '' }}>Amount (Low to High)</option>
            </select>
        </div>
        <div style="overflow-x: auto;">
            <table class="exp-table">
                <thead>
                    <tr>
                        <th>Voucher No</th>
                        <th>Amount</th>
                        <th>Paid Amount</th>
                        <th>Expense Date</th>
                        <th>Expense Account</th>
                        <th>Created By</th>
                        <th>Reason</th>
                        <th>Remark</th>
                        <th>Approval Status</th>
                        <th>Payment Status</th>
                        <th style="width: 180px; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vouchers as $v)
                    <tr id="row-{{ $v->id }}" @if($v->deleted_at) style="opacity: 0.65; background-color: #f1f5f9;" @endif>
                        <td style="font-weight: 700;">{{ $v->voucher_no }}</td>
                        <td style="font-weight: 700;">{{ number_format($v->amount, 2) }}</td>
                        <td style="color: var(--exp-green); font-weight: 600;">{{ number_format($v->total_paid, 2) }}</td>
                        <td>{{ $v->expense_date ? $v->expense_date->format('d M Y') : 'N/A' }}</td>
                        <td style="font-weight: 600;">{{ $v->expenseHead->name ?? 'N/A' }}</td>
                        <td>{{ $v->creator->name ?? 'N/A' }}</td>
                        <td>{{ $v->reason }}</td>
                        <td>{{ $v->remarks ?? '—' }}</td>
                        <td>
                            @if($v->deleted_at)
                            <span class="badge deleted">Deleted</span>
                            @else
                            <span class="badge {{ strtolower($v->approval_status) }}">{{ $v->approval_status }}</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $pBadge = strtolower($v->payment_status);
                                if ($v->total_paid > 0 && $v->total_paid < $v->amount) {
                                    $pBadge = 'partial';
                                    $statusTxt = 'Partial';
                                } elseif ($v->total_paid >= $v->amount) {
                                    $pBadge = 'paid';
                                    $statusTxt = 'Paid';
                                } else {
                                    $pBadge = 'pending';
                                    $statusTxt = 'Pending';
                                }
                            @endphp
                            <span class="badge {{ $pBadge }}">{{ $statusTxt }}</span>
                        </td>
                        <td style="text-align: center;">
                            @if(!$v->deleted_at)
                                @if($v->total_due > 0 && $v->approval_status !== 'Rejected')
                                <button class="act-icon pay" onclick="openPaymentModal({{ $v->id }}, {{ $v->total_due }}, '{{ $v->voucher_no }}')" title="Voucher Payment">
                                    <i class="fas fa-money-bill-wave"></i>
                                </button>
                                <button class="act-icon add-pay" onclick="openPaymentModal({{ $v->id }}, {{ $v->total_due }}, '{{ $v->voucher_no }}')" title="Add Payment">
                                    <i class="fas fa-plus"></i>
                                </button>
                                @endif
                                <button class="act-icon details" onclick="showDetails('{{ $v->voucher_no }}', '{{ number_format($v->amount,2) }}', '{{ number_format($v->total_paid,2) }}', '{{ $v->expense_date ? $v->expense_date->format('Y-m-d') : '' }}', '{{ addslashes($v->expenseHead->name ?? 'N/A') }}', '{{ addslashes($v->reason) }}', '{{ addslashes($v->remarks ?? '') }}', '{{ $v->document_path ? asset($v->document_path) : '' }}')" title="Details">
                                    <i class="far fa-file-lines"></i>
                                </button>
                                <button class="act-icon print" onclick="window.open('{{ route("school.expenses.print-all") }}?ids={{ $v->id }}&per_page=1&type=voucher&print=1', '_blank', 'width=950,height=750')" title="Print Receipt">
                                    <i class="fas fa-print"></i>
                                </button>
                                @if($v->approval_status !== 'Rejected')
                                <button class="act-icon reject" onclick="rejectVoucher({{ $v->id }})" title="Cancel/Decline">
                                    <i class="fas fa-circle-xmark"></i>
                                </button>
                                @endif
                                <button class="act-icon delete" onclick="deleteVoucher({{ $v->id }})" title="Delete">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                            @else
                                <span style="font-size: 11px; color: var(--exp-text2); font-style: italic;">No actions</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" style="text-align: center; color: var(--exp-text2); padding: 40px;">
                            No vouchers found for this date range. Click the button above to add one.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ADD VOUCHER MODAL (Screenshot 4) --}}
<div class="exp-modal-overlay" id="voucherModal">
    <div class="exp-modal">
        <div class="exp-modal-hdr">
            <h3>Add Voucher</h3>
            <button class="modal-close" onclick="closeVoucherModal()"><i class="fas fa-xmark"></i></button>
        </div>
        <div class="exp-modal-body">
            <form id="voucherForm" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Select Expense Head</label>
                    <select class="form-control" name="expense_head_id" required>
                        <option value="">Select Expense Head</option>
                        @foreach($expenseHeads as $head)
                        <option value="{{ $head->id }}">{{ $head->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Amount <span>*</span></label>
                    <input type="number" class="form-control" name="amount" placeholder="0.00" min="0.01" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Expense Date</label>
                    <input type="date" class="form-control" name="expense_date" id="addVoucherDate" required>
                </div>
                <div class="form-group">
                    <label>Reason <span>*</span></label>
                    <input type="text" class="form-control" name="reason" placeholder="Reason for expense" required>
                </div>
                <div class="form-group">
                    <label>Remarks</label>
                    <textarea class="form-control" name="remarks" placeholder="Optional notes..."></textarea>
                </div>
                <div class="form-group">
                    <label>Document</label>
                    <input type="file" class="form-control" name="document" style="padding-top: 5px;">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="exp-btn-submit" id="addVoucherSubmitBtn">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- VOUCHER PAYMENT MODAL (Screenshot 5) --}}
<div class="exp-modal-overlay" id="paymentModal">
    <div class="exp-modal">
        <div class="exp-modal-hdr payment-hdr">
            <h3>Voucher Payment</h3>
            <button class="modal-close" onclick="closePaymentModal()"><i class="fas fa-xmark"></i></button>
        </div>
        <div class="exp-modal-body">
            <form id="paymentForm">
                @csrf
                <input type="hidden" name="voucher_id" id="payVoucherId">
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" class="form-control" name="payment_date" id="payDate" required>
                </div>
                <div class="form-group">
                    <label>Invoice No:</label>
                    <input type="text" class="form-control" name="invoice_no" id="payInvoice" placeholder="Invoice reference" required>
                </div>
                <div class="form-group">
                    <label>Mode:</label>
                    <select class="form-control" name="payment_mode" id="payMode" required>
                        <option value="cash">CASH</option>
                        <option value="bank_transfer">BANK TRANSFER</option>
                        <option value="cheque">CHEQUE</option>
                        <option value="upi">UPI</option>
                    </select>
                </div>
                <div class="form-group" id="chequePayDetailsContainer" style="display:none; background: rgba(241, 245, 249, 0.5); padding: 12px; border-radius: 8px; border: 1px dashed var(--exp-border); margin-bottom: 10px;">
                    <div style="margin-bottom:10px;">
                        <label style="display:block; font-size:12px; font-weight:700; margin-bottom:4px;">Bank Name <span style="color:var(--exp-red);">*</span></label>
                        <input type="text" class="form-control" name="bank_name" id="payBankName" placeholder="Bank name">
                    </div>
                    <div style="margin-bottom:10px;">
                        <label style="display:block; font-size:12px; font-weight:700; margin-bottom:4px;">Issue Date <span style="color:var(--exp-red);">*</span></label>
                        <input type="date" class="form-control" name="check_issue_date" id="payCheckIssueDate">
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; margin-bottom:4px;">Branch <span style="color:var(--exp-red);">*</span></label>
                        <input type="text" class="form-control" name="branch" id="payBranch" placeholder="Branch">
                    </div>
                </div>
                <div class="form-group">
                    <label>Remarks:</label>
                    <input type="text" class="form-control" name="remarks" placeholder="Optional">
                </div>
                <div class="form-group">
                    <label>Amount:</label>
                    <input type="number" class="form-control" name="amount" id="payAmountInput" min="0.01" step="0.01" style="border: 1px solid var(--exp-red);" required>
                    <span id="maxAmountWarning" style="color: var(--exp-red); font-size: 11px; font-weight: 700; margin-top: 4px; display: block;"></span>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="exp-btn-submit" id="paySubmitBtn" style="border-color: var(--exp-green); color: var(--exp-green);">Pay</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- DETAILS MODAL --}}
<div class="exp-modal-overlay" id="detailsModal">
    <div class="exp-modal" style="max-width: 550px;">
        <div class="exp-modal-hdr" style="background: #1e3a8a;">
            <h3>Voucher Details</h3>
            <button class="modal-close" onclick="closeDetailsModal()"><i class="fas fa-xmark"></i></button>
        </div>
        <div class="exp-modal-body">
            <table class="exp-table" style="border: 1px solid var(--exp-border);">
                <tr>
                    <td style="font-weight: 700; background: var(--exp-gray); width: 140px;">Voucher No:</td>
                    <td id="detVocNo"></td>
                </tr>
                <tr>
                    <td style="font-weight: 700; background: var(--exp-gray);">Total Amount:</td>
                    <td id="detAmount"></td>
                </tr>
                <tr>
                    <td style="font-weight: 700; background: var(--exp-gray);">Total Paid:</td>
                    <td id="detPaid"></td>
                </tr>
                <tr>
                    <td style="font-weight: 700; background: var(--exp-gray);">Voucher Date:</td>
                    <td id="detDate"></td>
                </tr>
                <tr>
                    <td style="font-weight: 700; background: var(--exp-gray);">Account Head:</td>
                    <td id="detAccount"></td>
                </tr>
                <tr>
                    <td style="font-weight: 700; background: var(--exp-gray);">Reason:</td>
                    <td id="detReason"></td>
                </tr>
                <tr>
                    <td style="font-weight: 700; background: var(--exp-gray);">Remarks:</td>
                    <td id="detRemarks"></td>
                </tr>
                <tr id="detDocRow">
                    <td style="font-weight: 700; background: var(--exp-gray);">Attachment:</td>
                    <td>
                        <a href="#" id="detDocLink" target="_blank" class="exp-btn-excel" style="padding: 4px 10px; font-size:11px;">
                            View File <i class="fas fa-paperclip"></i>
                        </a>
                    </td>
                </tr>
            </table>
            <div class="modal-footer">
                <button type="button" class="exp-btn exp-btn-outline" onclick="closeDetailsModal()">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- PRINT ALL CONFIG MODAL --}}
<div class="exp-modal-overlay" id="printAllModal">
    <div class="exp-modal" style="max-width: 450px;">
        <div class="exp-modal-hdr" style="background: var(--exp-blue-dark);">
            <h3><i class="fas fa-print"></i> Print All Vouchers</h3>
            <button class="modal-close" onclick="closePrintAllModal()"><i class="fas fa-xmark"></i></button>
        </div>
        <div class="exp-modal-body" style="padding: 20px;">
            <div class="form-group" style="margin-bottom: 20px;">
                <label style="font-weight: 700; margin-bottom: 8px; display: block; font-size:13px;">How many vouchers per A4 page?</label>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px;">
                    <div style="border: 1.5px solid var(--exp-border); border-radius: 8px; padding: 10px; text-align: center; cursor: pointer;" class="per-page-opt" id="opt-1" onclick="selectPerPage(1)">
                        <strong style="display: block; font-size: 16px; color:var(--exp-text);">1</strong>
                        <span style="font-size: 10px; color: var(--exp-text2);">Full Page</span>
                    </div>
                    <div style="border: 1.5px solid var(--exp-blue); background: var(--exp-blue-light); border-radius: 8px; padding: 10px; text-align: center; cursor: pointer;" class="per-page-opt" id="opt-2" onclick="selectPerPage(2)">
                        <strong style="display: block; font-size: 16px; color:var(--exp-text);">2</strong>
                        <span style="font-size: 10px; color: var(--exp-text2);">Half Page</span>
                    </div>
                    <div style="border: 1.5px solid var(--exp-border); border-radius: 8px; padding: 10px; text-align: center; cursor: pointer;" class="per-page-opt" id="opt-3" onclick="selectPerPage(3)">
                        <strong style="display: block; font-size: 16px; color:var(--exp-text);">3</strong>
                        <span style="font-size: 10px; color: var(--exp-text2);">1/3 Page</span>
                    </div>
                    <div style="border: 1.5px solid var(--exp-border); border-radius: 8px; padding: 10px; text-align: center; cursor: pointer;" class="per-page-opt" id="opt-4" onclick="selectPerPage(4)">
                        <strong style="display: block; font-size: 16px; color:var(--exp-text);">4</strong>
                        <span style="font-size: 10px; color: var(--exp-text2);">1/4 Page</span>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer" style="margin-top:20px; display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="exp-btn exp-btn-outline" style="padding: 8px 16px; font-weight:700; border-radius:8px; border: 1.5px solid var(--exp-border); cursor:pointer;" onclick="closePrintAllModal()">Cancel</button>
                <button type="button" class="exp-btn" style="padding: 8px 16px; font-weight:700; border-radius:8px; width:auto; border: 1px solid var(--exp-blue); background:var(--exp-blue); color:#fff;" onclick="submitPrintAll()">
                    Print <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- INVOICE RECEIPT MODAL --}}
<div class="exp-modal-overlay" id="invoiceReceiptModal">
    <div class="exp-modal" style="max-width: 650px;">
        <div class="exp-modal-hdr" style="background: #1e3a8a;">
            <h3>Invoice Receipt</h3>
            <button class="modal-close" onclick="closeInvoiceReceiptModal()"><i class="fas fa-xmark"></i></button>
        </div>
        <div class="exp-modal-body" style="background: #fff; padding: 20px;">
            <div id="receiptPrintArea" class="receipt-card">
                <div class="receipt-header">
                    <div class="receipt-brand">
                        @if($sch && !empty($sch->logo) && Storage::disk('public')->exists($sch->logo))
                            <img class="receipt-logo" src="{{ Storage::disk('public')->url($sch->logo) }}" alt="{{ $sch->name }}">
                        @else
                            <div class="receipt-logo-placeholder">
                                <i class="fas fa-school"></i>
                            </div>
                        @endif
                    </div>
                    <div class="receipt-school-info">
                        <h2 class="receipt-school-name">{{ $sch->name ?? 'Lord Krishna Educational Academy' }}</h2>
                        <p class="receipt-school-address">{{ $sch->address ?? 'Agra Road Mainpuri' }}</p>
                        <p class="receipt-school-address">Phone: {{ $sch->phone ?? 'N/A' }} | Session: {{ $sess->session_title ?? '2026-27' }}</p>
                    </div>
                </div>
                
                <div class="receipt-title-row">
                    <span class="receipt-title">Payment Receipt</span>
                    <span style="font-size:11.5px; font-weight:700; color:#475569;">Date: <span id="recDate"></span></span>
                </div>

                <div class="receipt-meta-grid">
                    <div class="receipt-meta-item">
                        <span class="receipt-meta-lbl">Voucher No:</span>
                        <span class="receipt-meta-val" id="recVoucherNo"></span>
                    </div>
                    <div class="receipt-meta-item">
                        <span class="receipt-meta-lbl">Payment Mode:</span>
                        <span class="receipt-meta-val" id="recPaymentMode" style="text-transform: uppercase;"></span>
                    </div>
                    <div class="receipt-meta-item">
                        <span class="receipt-meta-lbl">Expense Account:</span>
                        <span class="receipt-meta-val" id="recExpenseHead"></span>
                    </div>
                    <div class="receipt-meta-item">
                        <span class="receipt-meta-lbl">Invoice / Ref No:</span>
                        <span class="receipt-meta-val" id="recInvoiceNo"></span>
                    </div>
                    <div class="receipt-meta-item" style="grid-column: span 2;">
                        <span class="receipt-meta-lbl">Reason:</span>
                        <span class="receipt-meta-val" id="recReason"></span>
                    </div>
                    <div class="receipt-meta-item" style="grid-column: span 2; display: none;" id="recChequeDetailsRow">
                        <span class="receipt-meta-lbl">Cheque Info:</span>
                        <span class="receipt-meta-val" id="recChequeDetails"></span>
                    </div>
                </div>

                <table class="receipt-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">S.No.</th>
                            <th>Particulars</th>
                            <th style="width: 110px; text-align: right;">Amount (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td id="recParticulars"></td>
                            <td style="text-align: right; font-weight:700;" id="recTableAmount"></td>
                        </tr>
                        <tr style="background:#f8fafc;">
                            <td colspan="2" style="text-align: right; font-weight: 700;">Total Amount:</td>
                            <td style="text-align: right; font-weight: 700;" id="recTotalAmount"></td>
                        </tr>
                        <tr style="background:#f8fafc;">
                            <td colspan="2" style="text-align: right; font-weight: 700; color:#10b981;">Amount Paid:</td>
                            <td style="text-align: right; font-weight: 700; color:#10b981;" id="recAmountPaid"></td>
                        </tr>
                        <tr style="background:#f8fafc;">
                            <td colspan="2" style="text-align: right; font-weight: 700; color:#f97316;">Remaining Due:</td>
                            <td style="text-align: right; font-weight: 700; color:#f97316;" id="recRemainingDue"></td>
                        </tr>
                    </tbody>
                </table>

                <div class="receipt-words">
                    Amount in words: (<span id="recWords"></span>)
                </div>

                <div class="receipt-signatures">
                    <div class="receipt-sig-line" style="border-top:none;">Cleared</div>
                    <div class="receipt-sig-line">Cashier</div>
                </div>
            </div>

            <div class="modal-footer receipt-actions-row">
                <button type="button" class="exp-btn exp-btn-outline" onclick="closeInvoiceReceiptModal()" style="border-color:#64748b; color:#64748b;">Close & Sync</button>
                <button type="button" class="exp-btn exp-btn-primary" onclick="printReceipt()" style="background:#10b981; border-color:#10b981;">
                    <i class="fas fa-print"></i> Print Receipt
                </button>
            </div>
        </div>
    </div>
</div>

<div id="exp-toast"></div>
@endsection

@section('scripts')
<script>
// Toggle Add Voucher Modal
const vModal = document.getElementById('voucherModal');
const vForm  = document.getElementById('voucherForm');

document.getElementById('addVoucherBtn').addEventListener('click', () => {
    document.getElementById('addVoucherDate').value = new Date().toISOString().split('T')[0];
    vModal.classList.add('open');
});
function closeVoucherModal() { vModal.classList.remove('open'); vForm.reset(); }

vForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    const submitBtn = document.getElementById('addVoucherSubmitBtn');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Adding...';

    const formData = new FormData(vForm);

    try {
        const res = await fetch('{{ route("school.expenses.vouchers.store") }}', {
            method: 'POST',
            body: formData,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        });
        const json = await res.json();
        if (json.success) {
            showToast(json.message, 'success');
            closeVoucherModal();
            setTimeout(() => location.reload(), 900);
        } else {
            showToast(json.message || 'Error creating voucher.', 'error');
        }
    } catch(err) {
        showToast('Network error.', 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Add';
    }
});

// Toggle Payment Modal
const pModal = document.getElementById('paymentModal');
const pForm  = document.getElementById('paymentForm');
let maxPaymentAllowed = 0;

const chequePayContainer = document.getElementById('chequePayDetailsContainer');
const payBankName = document.getElementById('payBankName');
const payCheckIssueDate = document.getElementById('payCheckIssueDate');
const payBranch = document.getElementById('payBranch');
const payMode = document.getElementById('payMode');

function togglePayChequeFields() {
    if (payMode.value === 'cheque') {
        chequePayContainer.style.display = 'block';
        payBankName.required = true;
        payCheckIssueDate.required = true;
        payBranch.required = true;
    } else {
        chequePayContainer.style.display = 'none';
        payBankName.required = false;
        payCheckIssueDate.required = false;
        payBranch.required = false;
    }
}
payMode.addEventListener('change', togglePayChequeFields);

function openPaymentModal(id, due, vNo) {
    document.getElementById('payVoucherId').value = id;
    document.getElementById('payDate').value = new Date().toISOString().split('T')[0];
    document.getElementById('payInvoice').value = Math.floor(Date.now() / 100000) % 10000;
    maxPaymentAllowed = due;
    document.getElementById('maxAmountWarning').textContent = '*max amount ' + due.toFixed(2);
    document.getElementById('payAmountInput').value = due.toFixed(2);
    document.getElementById('payAmountInput').max = due;
    pModal.classList.add('open');
}
function closePaymentModal() {
    pModal.classList.remove('open');
    pForm.reset();
    chequePayContainer.style.display = 'none';
    payBankName.required = false;
    payCheckIssueDate.required = false;
    payBranch.required = false;
}

pForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    const id = document.getElementById('payVoucherId').value;
    const amountVal = parseFloat(document.getElementById('payAmountInput').value);

    if (amountVal > maxPaymentAllowed) {
        showToast('Amount exceeds remaining due of ₹' + maxPaymentAllowed.toFixed(2), 'error');
        return;
    }

    const payBtn = document.getElementById('paySubmitBtn');
    payBtn.disabled = true;
    payBtn.textContent = 'Paying...';

    const data = Object.fromEntries(new FormData(pForm));

    try {
        const res = await fetch(`{{ url("school/expenses/vouchers") }}/${id}/payments`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify(data)
        });
        const json = await res.json();
        if (json.success) {
            showToast(json.message, 'success');
            closePaymentModal();
            // Show receipt modal
            showInvoiceReceipt(json.payment, json.payment.voucher);
        } else {
            showToast(json.message || 'Error executing payment.', 'error');
        }
    } catch(err) {
        showToast('Network error.', 'error');
    } finally {
        payBtn.disabled = false;
        payBtn.textContent = 'Pay';
    }
});

// Reject Voucher
async function rejectVoucher(id) {
    if (!confirm('Reject this voucher? This will cancel further actions.')) return;
    try {
        const res = await fetch(`{{ url("school/expenses/vouchers") }}/${id}/reject`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        });
        const json = await res.json();
        if (json.success) {
            showToast(json.message, 'success');
            setTimeout(() => location.reload(), 900);
        } else {
            showToast(json.message, 'error');
        }
    } catch(e) {
        showToast('Error updating voucher status.', 'error');
    }
}

// Delete Voucher
async function deleteVoucher(id) {
    if (!confirm('Delete this voucher? This will also remove all logged payments against it.')) return;
    try {
        const res = await fetch(`{{ url("school/expenses/vouchers") }}/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        });
        const json = await res.json();
        if (json.success) {
            showToast(json.message, 'success');
            const row = document.getElementById('row-' + id);
            if (row) {
                row.style.opacity = '0';
                row.style.transform = 'translateY(-20px)';
                row.style.transition = '.3s';
                setTimeout(() => row.remove(), 300);
            }
            setTimeout(() => location.reload(), 600);
        } else {
            showToast(json.message, 'error');
        }
    } catch(e) {
        showToast('Error deleting voucher.', 'error');
    }
}

// View Details Modal
const dModal = document.getElementById('detailsModal');
function showDetails(vNo, amt, paid, date, account, reason, remarks, docUrl) {
    document.getElementById('detVocNo').textContent = vNo;
    document.getElementById('detAmount').textContent = '₹' + amt;
    document.getElementById('detPaid').textContent = '₹' + paid;
    document.getElementById('detDate').textContent = date;
    document.getElementById('detAccount').textContent = account;
    document.getElementById('detReason').textContent = reason;
    document.getElementById('detRemarks').textContent = remarks || '—';
    
    const docRow = document.getElementById('detDocRow');
    const docLink = document.getElementById('detDocLink');
    if (docUrl) {
        docLink.href = docUrl;
        docRow.style.display = '';
    } else {
        docRow.style.display = 'none';
    }
    
    dModal.classList.add('open');
}
function closeDetailsModal() { dModal.classList.remove('open'); }

// Sorting & Toast
function applySort(val) {
    const url = new URL(location.href);
    url.searchParams.set('sort_by', val);
    location.href = url.toString();
}

function showToast(msg, type = 'success') {
    const toast = document.getElementById('exp-toast');
    const el = document.createElement('div');
    el.className = 'toast-msg ' + type;
    el.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}" style="color:${type==='success'?'#10b981':'#ef4444'}"></i> ${msg}`;
    toast.appendChild(el);
    setTimeout(() => el.remove(), 3500);
}

// Share all preloaded vouchers with frontend
const allVouchers = @json($vouchers);

const receiptModal = document.getElementById('invoiceReceiptModal');

// Number to Words converter (Indian formatting)
function numberToWords(num) {
    const a = ['', 'one ', 'two ', 'three ', 'four ', 'five ', 'six ', 'seven ', 'eight ', 'nine ', 'ten ', 'eleven ', 'twelve ', 'thirteen ', 'fourteen ', 'fifteen ', 'sixteen ', 'seventeen ', 'eighteen ', 'nineteen '];
    const b = ['', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];
    
    num = Math.floor(num);
    if (num === 0) return 'zero';
    
    function g(n) {
        if (n < 20) return a[n];
        let d = n % 10;
        return b[Math.floor(n / 10)] + (d ? '-' + a[d] : ' ');
    }
    
    let str = '';
    let crore = Math.floor(num / 10000000);
    num %= 10000000;
    let lakh = Math.floor(num / 100000);
    num %= 100000;
    let thousand = Math.floor(num / 1000);
    num %= 1000;
    let hundred = Math.floor(num / 100);
    num %= 100;
    
    if (crore) str += g(crore) + 'crore ';
    if (lakh) str += g(lakh) + 'lakh ';
    if (thousand) str += g(thousand) + 'thousand ';
    if (hundred) str += g(hundred) + 'hundred ';
    if (num) {
        if (str !== '') str += 'and ';
        str += g(num);
    }
    return str.trim() + ' only';
}

function openInvoiceReceiptModal(voucherId) {
    const v = allVouchers.find(item => item.id == voucherId);
    if (!v) return;
    
    // Use last payment or mock if none
    const payment = (v.payments && v.payments.length > 0) 
        ? v.payments[v.payments.length - 1] 
        : {
            payment_date: v.expense_date || new Date().toISOString().split('T')[0],
            payment_mode: 'Pending',
            invoice_no: 'N/A',
            amount: 0
        };
        
    showInvoiceReceipt(payment, v);
}

function showInvoiceReceipt(payment, voucher) {
    const dateObj = new Date(payment.payment_date);
    const formattedDate = dateObj.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    
    document.getElementById('recDate').textContent = formattedDate;
    document.getElementById('recVoucherNo').textContent = voucher.voucher_no;
    document.getElementById('recPaymentMode').textContent = payment.payment_mode ? payment.payment_mode.replace('_', ' ') : 'Pending';
    
    let headName = 'N/A';
    if (voucher.expense_head && voucher.expense_head.name) {
        headName = voucher.expense_head.name;
    } else if (voucher.expense_head_id) {
        const opt = document.querySelector(`select[name="expense_head_id"] option[value="${voucher.expense_head_id}"]`);
        if (opt && opt.value !== 'All') {
            headName = opt.textContent;
        }
    }
    document.getElementById('recExpenseHead').textContent = headName;
    document.getElementById('recInvoiceNo').textContent = payment.invoice_no || 'N/A';
    document.getElementById('recReason').textContent = voucher.reason;
    document.getElementById('recParticulars').textContent = voucher.reason + (payment.remarks ? ' ('+payment.remarks+')' : '');
    
    // Amounts
    const currentPaid = parseFloat(payment.amount || 0);
    const voucherAmount = parseFloat(voucher.amount || 0);
    
    let totalPaid = 0;
    if (voucher.payments) {
        totalPaid = voucher.payments.reduce((sum, p) => sum + parseFloat(p.amount || 0), 0);
    } else {
        totalPaid = currentPaid;
    }
    const remainingDue = Math.max(0, voucherAmount - totalPaid);
    
    document.getElementById('recTableAmount').textContent = voucherAmount.toFixed(2);
    document.getElementById('recTotalAmount').textContent = '₹' + voucherAmount.toFixed(2);
    document.getElementById('recAmountPaid').textContent = '₹' + totalPaid.toFixed(2);
    document.getElementById('recRemainingDue').textContent = '₹' + remainingDue.toFixed(2);
    
    document.getElementById('recWords').textContent = numberToWords(totalPaid);
    
    // Cheque Info
    const chqRow = document.getElementById('recChequeDetailsRow');
    if (payment.payment_mode === 'cheque') {
        const issueDate = payment.check_issue_date ? new Date(payment.check_issue_date).toLocaleDateString('en-GB') : 'N/A';
        document.getElementById('recChequeDetails').textContent = `Bank: ${payment.bank_name || 'N/A'}, Issue Date: ${issueDate}, Branch: ${payment.branch || 'N/A'}`;
        chqRow.style.display = '';
    } else {
        chqRow.style.display = 'none';
    }
    
    receiptModal.classList.add('open');
}

function closeInvoiceReceiptModal() {
    receiptModal.classList.remove('open');
    location.reload();
}

function printReceipt() {
    window.print();
}

// ─── PRINT ALL CONFIG CONTROLLERS ────────────────────────────────────
let printAllType = 'voucher';
let selectedPerPage = 2;

function openPrintAllConfig(type) {
    printAllType = type;
    document.getElementById('printAllModal').classList.add('open');
}

function closePrintAllModal() {
    document.getElementById('printAllModal').classList.remove('open');
}

function selectPerPage(num) {
    selectedPerPage = num;
    document.querySelectorAll('.per-page-opt').forEach(el => {
        el.style.borderColor = 'var(--exp-border)';
        el.style.background = 'transparent';
    });
    const selectedOpt = document.getElementById('opt-' + num);
    if (selectedOpt) {
        selectedOpt.style.borderColor = 'var(--exp-blue)';
        selectedOpt.style.background = 'var(--exp-blue-light)';
    }
}

function submitPrintAll() {
    const rows = document.querySelectorAll('tbody tr[id^="row-"]');
    const ids = [];
    rows.forEach(r => {
        const id = r.getAttribute('id').replace('row-', '');
        ids.push(id);
    });
    
    if (ids.length === 0) {
        alert('No records available to print.');
        return;
    }
    
    closePrintAllModal();
    const url = '{{ route("school.expenses.print-all") }}?ids=' + ids.join(',') + '&per_page=' + selectedPerPage + '&type=' + printAllType + '&print=1';
    window.open(url, '_blank', 'width=950,height=750');
}
</script>
@endsection
