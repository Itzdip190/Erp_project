@extends('layouts.app')

@section('title', 'Manage Income Vouchers AccountWise')

@section('styles')
<style>
/* ─── VARIABLES ──────────────────────────────── */
:root {
    --inc-green:      #10b981;
    --inc-green-dark: #047857;
    --inc-green-light:#ecfdf5;
    --inc-green-mid:  #059669;
    --inc-accent:    #34d399;
    --inc-white:     #ffffff;
    --inc-gray:      #f8fafc;
    --inc-border:    #d1fae5;
    --inc-text:      #1e293b;
    --inc-text2:     #64748b;
    --inc-red:       #ef4444;
    --inc-blue:      #3b82f6;
    --inc-amber:     #f59e0b;
    --inc-orange:    #f97316;
    
    --shadow-sm: 0 1px 3px rgba(0,0,0,.05);
    --shadow-md: 0 4px 16px rgba(0,0,0,.08);
}

body.dark-mode {
    --inc-white:     #111827;
    --inc-gray:      #1f2937;
    --inc-border:    #374151;
    --inc-text:      #f8fafc;
    --inc-text2:     #94a3b8;
    --inc-green-light:rgba(16, 185, 129, 0.15);
}

/* ─── CONTAINER & BREADCRUMB ─────────────────── */
.inc-container {
    padding: 24px;
    width: 100%;
}
.inc-hdr-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 12px;
}
.inc-title {
    font-size: 20px;
    font-weight: 800;
    color: var(--inc-text);
}
.inc-btn-green {
    background-color: var(--inc-green);
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
.inc-btn-green:hover {
    background-color: var(--inc-green-dark);
}

/* ─── FILTER BAR ─────────────────────────────── */
.inc-filter-card {
    background: var(--inc-white);
    border: 1px solid var(--inc-border);
    border-radius: 12px;
    padding: 16px 20px;
    margin-bottom: 24px;
    box-shadow: var(--shadow-sm);
}
.inc-filter-form {
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
    color: var(--inc-text);
}
.filter-control {
    height: 36px;
    padding: 6px 12px;
    border: 1px solid var(--inc-border);
    border-radius: 6px;
    font-size: 12.5px;
    font-weight: 600;
    background: var(--inc-white);
    color: var(--inc-text);
    outline: none;
    min-width: 180px;
}
.inc-btn-go {
    background-color: var(--inc-green);
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
.inc-btn-go:hover {
    background-color: var(--inc-green-dark);
}

/* ─── KPI CARDS ──────────────────────────────── */
.inc-kpis {
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
.kpi-card.blue   { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
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

.inc-btn-excel {
    background-color: var(--inc-amber);
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
.inc-btn-excel:hover {
    background-color: #d97706;
    color: #fff;
}

/* ─── TABLE AREA ─────────────────────────────── */
.vouchers-card {
    background: var(--inc-white);
    border: 1px solid var(--inc-border);
    border-radius: 12px;
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}
.sort-row {
    padding: 12px 20px;
    background: var(--inc-gray);
    border-bottom: 1px solid var(--inc-border);
    display: flex;
    justify-content: flex-start;
    align-items: center;
    gap: 8px;
}
.inc-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.inc-table th {
    background: var(--inc-gray);
    color: var(--inc-text);
    font-weight: 700;
    padding: 12px 14px;
    border-bottom: 2px solid var(--inc-border);
    text-transform: uppercase;
    font-size: 11px;
    letter-spacing: 0.3px;
}
.inc-table td {
    padding: 12px 14px;
    border-bottom: 1px solid var(--inc-border);
    color: var(--inc-text);
    vertical-align: middle;
}
.inc-table tr:hover {
    background: var(--inc-green-light);
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
.inc-modal-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(4px);
    z-index: 2000;
    display: flex; align-items: center; justify-content: center;
    opacity: 0; pointer-events: none;
    transition: opacity 0.28s ease;
}
.inc-modal-overlay.open {
    opacity: 1; pointer-events: auto;
}
.inc-modal {
    background: var(--inc-white);
    border: 1px solid var(--inc-border);
    border-radius: 12px;
    width: 100%; max-width: 500px;
    box-shadow: var(--shadow-md);
    overflow: hidden;
    transform: translateY(20px);
    transition: transform 0.28s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.inc-modal-overlay.open .inc-modal {
    transform: translateY(0);
}
.inc-modal-hdr {
    background: var(--inc-green-mid);
    padding: 14px 18px;
    display: flex; align-items: center; justify-content: space-between;
    color: #fff;
}
.inc-modal-hdr.payment-hdr {
    background: var(--inc-green);
}
.inc-modal-hdr h3 {
    margin: 0; font-size: 15px; font-weight: 700;
}
.modal-close {
    background: none; border: none; color: #fff; font-size: 18px; cursor: pointer;
}
.inc-modal-body {
    padding: 20px;
}
.form-group {
    margin-bottom: 14px;
}
.form-group label {
    display: block; font-size: 12px; font-weight: 700;
    color: var(--inc-text); margin-bottom: 4px;
}
.form-group label span { color: var(--inc-red); }
.form-control {
    width: 100%; height: 36px; padding: 6px 10px;
    border: 1px solid var(--inc-border); border-radius: 6px;
    font-size: 12.5px; font-weight: 500; font-family: inherit;
    background: var(--inc-white); color: var(--inc-text);
    outline: none;
}
.form-control:focus {
    border-color: var(--inc-green-mid);
}
textarea.form-control {
    height: 60px;
}
.modal-footer {
    display: flex; align-items: center; justify-content: center;
    margin-top: 18px;
}
.inc-btn-submit {
    background: none;
    border: 1px solid var(--inc-green);
    color: var(--inc-green);
    padding: 8px 30px;
    font-size: 12.5px;
    font-weight: 700;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.2s;
    text-transform: uppercase;
}
.inc-btn-submit:hover {
    background: var(--inc-green);
    color: #fff;
}

/* Toast */
#inc-toast {
    position: fixed; bottom: 20px; right: 20px; z-index: 2500;
    display: flex; flex-direction: column; gap: 10px;
}
.toast-msg {
    background: var(--inc-white); border: 1px solid var(--inc-border);
    padding: 12px 20px; border-radius: 10px; box-shadow: var(--shadow-md);
    display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 600;
    animation: slideIn 0.3s forwards;
}
@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
</style>
@endsection

@section('content')
<div class="inc-container">
    <div class="inc-hdr-row">
        <h2 class="inc-title">Manage Vouchers AccountWise</h2>
        <button class="inc-btn-green" id="addVoucherBtn">
            <i class="fas fa-plus"></i> Add New Voucher
        </button>
    </div>

    {{-- DYNAMIC FILTER BAR --}}
    <div class="inc-filter-card">
        <form method="GET" action="{{ route('school.income.vouchers.accountwise') }}" class="inc-filter-form" id="filterForm">
            <div class="filter-group">
                <label>Select Income Head</label>
                <select class="filter-control" name="income_head_id" id="headSelector" onchange="submitIfSelected()">
                    <option value="">Select Income Head</option>
                    @foreach($incomeHeads as $head)
                    <option value="{{ $head->id }}" {{ (string)$incomeHeadId === (string)$head->id ? 'selected' : '' }}>{{ $head->name }}</option>
                    @endforeach
                </select>
            </div>

            @if($incomeHeadId)
            <div class="filter-group">
                <label>Select Income Account</label>
                <select class="filter-control" disabled>
                    @foreach($incomeHeads as $head)
                        @if((string)$incomeHeadId === (string)$head->id)
                        <option selected>{{ $head->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            @endif

            <div class="filter-group">
                <label>Status</label>
                <select class="filter-control" name="payment_status" onchange="submitIfSelected()">
                    <option value="All" {{ $paymentStatus === 'All' ? 'selected' : '' }}>All</option>
                    <option value="Pending" {{ $paymentStatus === 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Partial" {{ $paymentStatus === 'Partial' ? 'selected' : '' }}>Partial</option>
                    <option value="Paid" {{ $paymentStatus === 'Paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Approval Status</label>
                <select class="filter-control" name="approval_status" onchange="submitIfSelected()">
                    <option value="All" {{ $approvalStatus === 'All' ? 'selected' : '' }}>All</option>
                    <option value="Approved" {{ $approvalStatus === 'Approved' ? 'selected' : '' }}>Approved</option>
                    <option value="Pending" {{ $approvalStatus === 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Rejected" {{ $approvalStatus === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <div class="filter-group" style="flex-direction: row; align-items: center; gap: 8px; margin-top: 18px;">
                <input type="checkbox" name="show_deleted" id="show_deleted" value="1" {{ $showDeleted ? 'checked' : '' }} onchange="submitIfSelected()">
                <label for="show_deleted" style="margin-bottom: 0; cursor: pointer;">Show Deleted</label>
            </div>
        </form>
    </div>

    @if($vouchers !== null)
    {{-- KPI BLOCKS & EXCEL --}}
    <div class="inc-kpis">
        <div class="kpi-card blue">
            <div class="kpi-left">
                <span class="kpi-title">Total</span>
                <span class="kpi-value">{{ number_format($totalAmount, 2) }}</span>
            </div>
            <i class="fas fa-sack-dollar kpi-icon"></i>
        </div>
        <div class="kpi-card green">
            <div class="kpi-left">
                <span class="kpi-title">Total Received</span>
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
            <a href="{{ route('school.income.vouchers.export', request()->all()) }}" class="inc-btn-excel">
                Export Excel <i class="fas fa-download"></i>
            </a>
        </div>
    </div>

    {{-- TABLE DATA --}}
    <div class="vouchers-card">
        <div class="sort-row">
            <span style="font-size: 12px; font-weight: 700; color: var(--inc-text2);">Sort by:</span>
            <select class="filter-control" style="height: 30px; font-size:11.5px; padding: 2px 6px; min-width: 140px;" id="sortSelector" onchange="applySort(this.value)">
                <option value="date_desc" {{ $sortBy === 'date_desc' ? 'selected' : '' }}>Date (Newest)</option>
                <option value="date_asc" {{ $sortBy === 'date_asc' ? 'selected' : '' }}>Date (Oldest)</option>
                <option value="amount_desc" {{ $sortBy === 'amount_desc' ? 'selected' : '' }}>Amount (High to Low)</option>
                <option value="amount_asc" {{ $sortBy === 'amount_asc' ? 'selected' : '' }}>Amount (Low to High)</option>
            </select>
        </div>
        <div style="overflow-x: auto;">
            <table class="inc-table">
                <thead>
                    <tr>
                        <th>Voucher No</th>
                        <th>Amount</th>
                        <th>Received Amount</th>
                        <th>Income Date</th>
                        <th>Income Account</th>
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
                        <td style="color: var(--inc-green-mid); font-weight: 600;">{{ number_format($v->total_paid, 2) }}</td>
                        <td>{{ $v->income_date ? $v->income_date->format('d M Y') : 'N/A' }}</td>
                        <td style="font-weight: 600;">{{ $v->incomeHead->name ?? 'N/A' }}</td>
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
                                <button class="act-icon pay" onclick="openPaymentModal({{ $v->id }}, {{ $v->total_due }}, '{{ $v->voucher_no }}')" title="Register Receipt">
                                    <i class="fas fa-money-bill-wave"></i>
                                </button>
                                <button class="act-icon add-pay" onclick="openPaymentModal({{ $v->id }}, {{ $v->total_due }}, '{{ $v->voucher_no }}')" title="Add Receipt">
                                    <i class="fas fa-plus"></i>
                                </button>
                                @endif
                                <button class="act-icon details" onclick="showDetails('{{ $v->voucher_no }}', '{{ number_format($v->amount,2) }}', '{{ number_format($v->total_paid,2) }}', '{{ $v->income_date ? $v->income_date->format('Y-m-d') : '' }}', '{{ addslashes($v->incomeHead->name ?? 'N/A') }}', '{{ addslashes($v->reason) }}', '{{ addslashes($v->remarks ?? '') }}', '{{ $v->document_path ? asset($v->document_path) : '' }}')" title="Details">
                                    <i class="far fa-file-lines"></i>
                                </button>
                                <button class="act-icon print" onclick="window.print()" title="Print">
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
                                <span style="font-size: 11px; color: var(--inc-text2); font-style: italic;">No actions</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" style="text-align: center; color: var(--inc-text2); padding: 40px;">
                            No vouchers found for this Income head.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div style="text-align: center; color: var(--inc-text2); padding: 60px; background: var(--inc-white); border: 1px solid var(--inc-border); border-radius:12px; box-shadow: var(--shadow-sm);">
        <i class="fas fa-wallet" style="font-size: 40px; color: var(--inc-green); opacity: 0.5; margin-bottom: 16px;"></i>
        <h4>Select an Income Head to view associated vouchers.</h4>
    </div>
    @endif
</div>

{{-- ADD VOUCHER MODAL --}}
<div class="inc-modal-overlay" id="voucherModal">
    <div class="inc-modal">
        <div class="inc-modal-hdr">
            <h3>Add Voucher</h3>
            <button class="modal-close" onclick="closeVoucherModal()"><i class="fas fa-xmark"></i></button>
        </div>
        <div class="inc-modal-body">
            <form id="voucherForm" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Select Income Head</label>
                    <select class="form-control" name="income_head_id" required>
                        <option value="">Select Income Head</option>
                        @foreach($incomeHeads as $head)
                        <option value="{{ $head->id }}" {{ (string)$incomeHeadId === (string)$head->id ? 'selected' : '' }}>{{ $head->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Amount <span>*</span></label>
                    <input type="number" class="form-control" name="amount" placeholder="0.00" min="0.01" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Income Date</label>
                    <input type="date" class="form-control" name="income_date" id="addVoucherDate" required>
                </div>
                <div class="form-group">
                    <label>Reason <span>*</span></label>
                    <input type="text" class="form-control" name="reason" placeholder="Reason for income" required>
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
                    <button type="submit" class="inc-btn-submit" id="addVoucherSubmitBtn">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- VOUCHER PAYMENT (RECEIPT) MODAL --}}
<div class="inc-modal-overlay" id="paymentModal">
    <div class="inc-modal">
        <div class="inc-modal-hdr payment-hdr">
            <h3>Voucher Receipt</h3>
            <button class="modal-close" onclick="closePaymentModal()"><i class="fas fa-xmark"></i></button>
        </div>
        <div class="inc-modal-body">
            <form id="paymentForm">
                @csrf
                <input type="hidden" name="voucher_id" id="payVoucherId">
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" class="form-control" name="receipt_date" id="payDate" required>
                </div>
                <div class="form-group">
                    <label>Invoice/Receipt No:</label>
                    <input type="text" class="form-control" name="invoice_no" id="payInvoice" placeholder="Receipt reference" required>
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
                <div class="form-group" id="chequePayDetailsContainer" style="display:none; background: rgba(209, 250, 229, 0.4); padding: 12px; border-radius: 8px; border: 1px dashed var(--inc-border); margin-bottom: 10px;">
                    <div style="margin-bottom:10px;">
                        <label style="display:block; font-size:12px; font-weight:700; margin-bottom:4px;">Bank Name <span style="color:var(--inc-red);">*</span></label>
                        <input type="text" class="form-control" name="bank_name" id="payBankName" placeholder="Bank name">
                    </div>
                    <div style="margin-bottom:10px;">
                        <label style="display:block; font-size:12px; font-weight:700; margin-bottom:4px;">Issue Date <span style="color:var(--inc-red);">*</span></label>
                        <input type="date" class="form-control" name="check_issue_date" id="payCheckIssueDate">
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; margin-bottom:4px;">Branch <span style="color:var(--inc-red);">*</span></label>
                        <input type="text" class="form-control" name="branch" id="payBranch" placeholder="Branch">
                    </div>
                </div>
                <div class="form-group">
                    <label>Remarks:</label>
                    <input type="text" class="form-control" name="remarks" placeholder="Optional">
                </div>
                <div class="form-group">
                    <label>Amount:</label>
                    <input type="number" class="form-control" name="amount" id="payAmountInput" min="0.01" step="0.01" style="border: 1px solid var(--inc-red);" required>
                    <span id="maxAmountWarning" style="color: var(--inc-red); font-size: 11px; font-weight: 700; margin-top: 4px; display: block;"></span>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="inc-btn-submit" id="paySubmitBtn" style="border-color: var(--inc-green); color: var(--inc-green);">Save Receipt</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- DETAILS MODAL --}}
<div class="inc-modal-overlay" id="detailsModal">
    <div class="inc-modal" style="max-width: 550px;">
        <div class="inc-modal-hdr" style="background: var(--inc-green-dark);">
            <h3>Voucher Details</h3>
            <button class="modal-close" onclick="closeDetailsModal()"><i class="fas fa-xmark"></i></button>
        </div>
        <div class="inc-modal-body">
            <table class="inc-table" style="border: 1px solid var(--inc-border);">
                <tr>
                    <td style="font-weight: 700; background: var(--inc-gray); width: 140px;">Voucher No:</td>
                    <td id="detVocNo"></td>
                </tr>
                <tr>
                    <td style="font-weight: 700; background: var(--inc-gray);">Total Amount:</td>
                    <td id="detAmount"></td>
                </tr>
                <tr>
                    <td style="font-weight: 700; background: var(--inc-gray);">Total Paid:</td>
                    <td id="detPaid"></td>
                </tr>
                <tr>
                    <td style="font-weight: 700; background: var(--inc-gray);">Voucher Date:</td>
                    <td id="detDate"></td>
                </tr>
                <tr>
                    <td style="font-weight: 700; background: var(--inc-gray);">Account Head:</td>
                    <td id="detAccount"></td>
                </tr>
                <tr>
                    <td style="font-weight: 700; background: var(--inc-gray);">Reason:</td>
                    <td id="detReason"></td>
                </tr>
                <tr>
                    <td style="font-weight: 700; background: var(--inc-gray);">Remarks:</td>
                    <td id="detRemarks"></td>
                </tr>
                <tr id="detDocRow">
                    <td style="font-weight: 700; background: var(--exp-gray);">Attachment:</td>
                    <td><a href="" id="detDocLink" target="_blank" style="color: var(--inc-green-mid); font-weight: 700;"><i class="fas fa-paperclip"></i> View Document</a></td>
                </tr>
            </table>
        </div>
    </div>
</div>

{{-- TOAST SYSTEM --}}
<div id="inc-toast"></div>
@endsection

@section('scripts')
<script>
function submitIfSelected() {
    document.getElementById('filterForm').submit();
}

function applySort(val) {
    const url = new URL(window.location.href);
    url.searchParams.set('sort_by', val);
    window.location.href = url.toString();
}

const vModal = document.getElementById('voucherModal');
const pModal = document.getElementById('paymentModal');
const dModal = document.getElementById('detailsModal');

document.getElementById('addVoucherBtn').addEventListener('click', () => {
    document.getElementById('addVoucherDate').value = new Date().toISOString().split('T')[0];
    vModal.classList.add('open');
});

function closeVoucherModal() {
    vModal.classList.remove('open');
    document.getElementById('voucherForm').reset();
}

let activeMaxDue = 0;
function openPaymentModal(voucherId, maxDue, voucherNo) {
    document.getElementById('payVoucherId').value = voucherId;
    document.getElementById('payDate').value = new Date().toISOString().split('T')[0];
    document.getElementById('payInvoice').value = 'REC-' + Date.now();
    document.getElementById('payAmountInput').value = maxDue.toFixed(2);
    document.getElementById('maxAmountWarning').textContent = 'Remaining Due: ₹' + maxDue.toLocaleString('en-IN', {minimumFractionDigits: 2});
    activeMaxDue = maxDue;
    pModal.classList.add('open');
}

function closePaymentModal() {
    pModal.classList.remove('open');
    document.getElementById('paymentForm').reset();
    document.getElementById('chequePayDetailsContainer').style.display = 'none';
}

document.getElementById('payMode').addEventListener('change', function() {
    const container = document.getElementById('chequePayDetailsContainer');
    const bankName = document.getElementById('payBankName');
    const checkDate = document.getElementById('payCheckIssueDate');
    const branch = document.getElementById('payBranch');
    
    if (this.value === 'cheque') {
        container.style.display = 'block';
        bankName.required = true;
        checkDate.required = true;
        branch.required = true;
    } else {
        container.style.display = 'none';
        bankName.required = false;
        checkDate.required = false;
        branch.required = false;
    }
});

function showDetails(vocNo, amount, paid, date, head, reason, remarks, docPath) {
    document.getElementById('detVocNo').textContent = vocNo;
    document.getElementById('detAmount').textContent = '₹ ' + amount;
    document.getElementById('detPaid').textContent = '₹ ' + paid;
    document.getElementById('detDate').textContent = date;
    document.getElementById('detAccount').textContent = head;
    document.getElementById('detReason').textContent = reason;
    document.getElementById('detRemarks').textContent = remarks || '—';
    
    const docRow = document.getElementById('detDocRow');
    if (docPath) {
        docRow.style.display = 'table-row';
        document.getElementById('detDocLink').href = docPath;
    } else {
        docRow.style.display = 'none';
    }
    dModal.classList.add('open');
}

function closeDetailsModal() {
    dModal.classList.remove('open');
}

// Submits
document.getElementById('voucherForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    const btn = document.getElementById('addVoucherSubmitBtn');
    btn.disabled = true;
    btn.textContent = 'Adding...';

    try {
        const res = await fetch('{{ route("school.income.vouchers.store") }}', {
            method: 'POST',
            body: fd,
            headers: { 'Accept': 'application/json' }
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
        showToast('Network error occurred.', 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Add';
    }
});

document.getElementById('paymentForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const amountVal = parseFloat(document.getElementById('payAmountInput').value);
    if (amountVal > activeMaxDue) {
        showToast('Amount cannot exceed the due balance.', 'error');
        return;
    }

    const id = document.getElementById('payVoucherId').value;
    const btn = document.getElementById('paySubmitBtn');
    btn.disabled = true;
    btn.textContent = 'Saving...';

    // Collect data
    const payData = {
        receipt_date: document.getElementById('payDate').value,
        invoice_no: document.getElementById('payInvoice').value,
        payment_mode: document.getElementById('payMode').value,
        bank_name: document.getElementById('payBankName').value,
        check_issue_date: document.getElementById('payCheckIssueDate').value,
        branch: document.getElementById('payBranch').value,
        remarks: this.querySelector('input[name="remarks"]').value,
        amount: amountVal
    };

    try {
        const res = await fetch('{{ url("school/income/vouchers") }}/' + id + '/payments', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payData)
        });
        const json = await res.json();
        if (json.success) {
            showToast(json.message, 'success');
            closePaymentModal();
            setTimeout(() => location.reload(), 900);
        } else {
            showToast(json.message || 'Error registering receipt.', 'error');
        }
    } catch(err) {
        showToast('Network error occurred.', 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Save Receipt';
    }
});

async function rejectVoucher(id) {
    if (!confirm('Reject this voucher? This cannot be undone.')) return;
    try {
        const res = await fetch('{{ url("school/income/vouchers") }}/' + id + '/reject', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        });
        const json = await res.json();
        if (json.success) {
            showToast(json.message, 'success');
            setTimeout(() => location.reload(), 900);
        } else {
            showToast(json.message || 'Error rejecting voucher.', 'error');
        }
    } catch(err) {
        showToast('Network error.', 'error');
    }
}

async function deleteVoucher(id) {
    if (!confirm('Delete this voucher? This will delete all registered receipts/sync values too.')) return;
    try {
        const res = await fetch('{{ url("school/income/vouchers") }}/' + id, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        });
        const json = await res.json();
        if (json.success) {
            showToast(json.message, 'success');
            const row = document.getElementById('row-' + id);
            if (row) {
                row.style.opacity = '0';
                setTimeout(() => row.remove(), 300);
            } else {
                location.reload();
            }
        } else {
            showToast(json.message || 'Error deleting voucher.', 'error');
        }
    } catch(err) {
        showToast('Network error.', 'error');
    }
}

function showToast(msg, type = 'success') {
    const toast = document.getElementById('inc-toast');
    const el = document.createElement('div');
    el.className = 'toast-msg';
    el.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}" style="color:${type==='success'?'#10b981':'#ef4444'}"></i> ${msg}`;
    toast.appendChild(el);
    setTimeout(() => el.remove(), 3500);
}
</script>
@endsection
