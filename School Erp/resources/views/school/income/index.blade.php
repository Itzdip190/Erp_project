@extends('layouts.app')

@section('title', 'Income Control')

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
/* ─── ROOT VARIABLES ─────────────────────────── */
:root {
    --inc-green:      #10b981;
    --inc-green-dark: #047857;
    --inc-green-light:#ecfdf5;
    --inc-green-mid:  #059669;
    --inc-accent:    #34d399;
    --inc-white:     #ffffff;
    --inc-gray:      #f1f5f9;
    --inc-border:    #d1fae5;
    --inc-text:      #1e293b;
    --inc-text2:     #64748b;
    --inc-red:       #ef4444;
    --inc-blue:      #3b82f6;
    --inc-amber:     #f59e0b;
    --shadow-sm: 0 1px 3px rgba(16,185,129,.1);
    --shadow-md: 0 4px 16px rgba(16,185,129,.15);
    --shadow-lg: 0 12px 40px rgba(16,185,129,.2);
}

body.dark-mode {
    --inc-white:     #111827;
    --inc-gray:      #1f2937;
    --inc-border:    #1e293b;
    --inc-text:      #f8fafc;
    --inc-text2:     #94a3b8;
    --inc-green-light:rgba(16, 185, 129, 0.15);
}

/* ─── PAGE HEADER ────────────────────────────── */
.inc-hero {
    background: linear-gradient(135deg, #047857 0%, #10b981 50%, #34d399 100%);
    border-radius: 20px;
    padding: 32px 36px;
    margin-bottom: 28px;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    box-shadow: 0 10px 40px rgba(16,185,129,.4);
}
.inc-hero::before {
    content: '';
    position: absolute;
    top: -80px; right: -80px;
    width: 280px; height: 280px;
    background: rgba(255,255,255,.06);
    border-radius: 50%;
    pointer-events: none;
}
.inc-hero::after {
    content: '';
    position: absolute;
    bottom: -50px; left: -40px;
    width: 180px; height: 180px;
    background: rgba(255,255,255,.04);
    border-radius: 50%;
    pointer-events: none;
}
.inc-hero-text h1 {
    font-size: 26px;
    font-weight: 800;
    color: #fff;
    margin-bottom: 6px;
    letter-spacing: -.3px;
}
.inc-hero-text p {
    color: rgba(255,255,255,.75);
    font-size: 13.5px;
    line-height: 1.5;
    max-width: 460px;
}
.inc-hero-icon {
    width: 80px; height: 80px;
    background: rgba(255,255,255,.12);
    border-radius: 20px;
    display: flex; align-items: center; justify-content: center;
    font-size: 34px; color: rgba(255,255,255,.85);
    flex-shrink: 0;
    border: 1px solid rgba(255,255,255,.2);
    backdrop-filter: blur(4px);
}

/* ─── STAT CARDS ─────────────────────────────── */
.inc-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
    margin-bottom: 28px;
}
.inc-stat {
    background: var(--inc-white);
    border: 1.5px solid var(--inc-border);
    border-radius: 16px;
    padding: 20px 22px;
    box-shadow: var(--shadow-sm);
    display: flex;
    flex-direction: column;
    position: relative;
    transition: transform 0.2s, box-shadow 0.2s;
}
.inc-stat:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}
.inc-stat-title {
    font-size: 11.5px;
    font-weight: 700;
    color: var(--inc-text2);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.inc-stat-value {
    font-size: 24px;
    font-weight: 800;
    color: var(--inc-text);
    margin: 8px 0 4px;
}
.inc-stat-label {
    font-size: 11.5px;
    color: var(--inc-text2);
}
.inc-stat-label span {
    color: var(--inc-green-mid);
    font-weight: 700;
}
.inc-stat-icon {
    position: absolute;
    top: 20px; right: 22px;
    font-size: 20px;
    color: var(--inc-green);
    opacity: 0.35;
}

/* ─── CONTENT GRID ───────────────────────────── */
.inc-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 22px;
    align-items: start;
    margin-bottom: 40px;
}
.inc-card {
    background: var(--inc-white);
    border: 1.5px solid var(--inc-border);
    border-radius: 16px;
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}
.inc-card-hdr {
    padding: 18px 22px;
    border-bottom: 1.5px solid var(--inc-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.inc-card-hdr-left {
    display: flex;
    align-items: center;
    gap: 10px;
}
.inc-card-hdr-icon {
    width: 32px; height: 32px;
    border-radius: 50%;
    background: var(--inc-green-light);
    color: var(--inc-green-mid);
    display: flex; align-items: center; justify-content: center;
    font-size: 14px;
}
.inc-card-title {
    font-size: 15.5px;
    font-weight: 800;
    color: var(--inc-text);
}
.inc-card-body {
    padding: 22px;
}

/* ─── FILTER BAR ─────────────────────────────── */
.inc-filter-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 20px;
}
.inc-filter-group {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
.inc-select {
    height: 38px;
    padding: 0 12px;
    border: 1.5px solid var(--inc-border);
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 600;
    background: var(--inc-white);
    color: var(--inc-text);
    outline: none;
    cursor: pointer;
}
.inc-btn-green {
    background-color: var(--inc-green);
    color: #fff;
    border: none;
    padding: 9px 18px;
    font-size: 13px;
    font-weight: 700;
    border-radius: 30px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: background-color 0.2s, transform 0.15s;
    box-shadow: 0 4px 14px rgba(16,185,129,.25);
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.inc-btn-green:hover {
    background-color: var(--inc-green-dark);
    transform: translateY(-1px);
}

/* ─── TABLE ──────────────────────────────────── */
.inc-table-responsive {
    overflow-x: auto;
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
    padding: 14px 18px;
    border-bottom: 2px solid var(--inc-border);
    text-transform: uppercase;
    font-size: 11px;
    letter-spacing: 0.3px;
}
.inc-table td {
    padding: 14px 18px;
    border-bottom: 1px solid var(--inc-border);
    color: var(--inc-text);
    vertical-align: middle;
}
.inc-table tr:hover {
    background: var(--inc-green-light);
}

.cat-badge {
    background: var(--inc-green-light);
    color: var(--inc-green-mid);
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 11.5px;
    font-weight: 600;
}
.badge {
    padding: 4px 8px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
}
.badge-paid { background-color: #d1fae5; color: #065f46; }
.badge-partial { background-color: #dbeafe; color: #1e40af; }
.badge-pending { background-color: #fef3c7; color: #92400e; }
.badge-cancelled { background-color: #fee2e2; color: #991b1b; }

.tbl-actions {
    display: flex; gap: 8px;
}
.btn-icon {
    width: 28px; height: 28px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    border: none; cursor: pointer; transition: transform 0.15s;
    font-size: 12px;
}
.btn-icon:hover { transform: scale(1.15); }
.btn-icon.edit { background: #fef3c7; color: var(--inc-amber); }
.btn-icon.del { background: #fee2e2; color: var(--inc-red); }

/* ─── MODAL ──────────────────────────────────── */
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
    border: 1.5px solid var(--inc-border);
    border-radius: 16px;
    width: 100%; max-width: 600px;
    box-shadow: var(--shadow-lg);
    overflow: hidden;
    transform: translateY(20px);
    transition: transform 0.28s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.inc-modal-overlay.open .inc-modal {
    transform: translateY(0);
}
.inc-modal-hdr {
    background: var(--inc-green-mid);
    padding: 16px 20px;
    display: flex; align-items: center; justify-content: space-between;
    color: #fff;
}
.inc-modal-hdr h3 {
    margin: 0; font-size: 16px; font-weight: 700;
    display: flex; align-items: center; gap: 8px;
}
.modal-close {
    background: none; border: none; color: #fff; font-size: 18px; cursor: pointer;
}
.inc-modal-body {
    padding: 24px;
}
.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}
.form-grid .span2 {
    grid-column: span 2;
}
.form-group {
    display: flex; flex-direction: column; gap: 6px;
}
.form-group label {
    font-size: 12.5px; font-weight: 700; color: var(--inc-text);
}
.form-group label span { color: var(--inc-red); }
.form-control {
    width: 100%; height: 38px; padding: 8px 12px;
    border: 1.5px solid var(--inc-border); border-radius: 8px;
    font-size: 13px; font-weight: 500; font-family: inherit;
    background: var(--inc-white); color: var(--inc-text);
    outline: none; transition: border-color 0.2s;
}
.form-control:focus {
    border-color: var(--inc-green-mid);
}
textarea.form-control {
    height: 70px;
}
.modal-footer {
    display: flex; align-items: center; justify-content: flex-end; gap: 10px;
    margin-top: 20px;
}
.inc-btn {
    padding: 8px 16px; font-size: 12.5px; font-weight: 700; border-radius: 8px;
    cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 6px;
}
.inc-btn-outline {
    background: transparent; color: var(--inc-text2); border: 1.5px solid var(--inc-border);
}
.inc-btn-outline:hover { background: var(--inc-gray); }
.inc-btn-primary {
    background: var(--inc-green); color: #fff;
}
.inc-btn-primary:hover { background: var(--inc-green-dark); }

/* Charts */
.chart-container {
    height: 220px;
    position: relative;
}
.cat-list {
    display: flex;
    flex-direction: column;
    gap: 14px;
}
.cat-row {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 12.5px;
}
.cat-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
}
.cat-name {
    font-weight: 600;
    color: var(--inc-text);
    width: 90px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.cat-bar-wrap {
    flex: 1;
    height: 6px;
    background: var(--inc-gray);
    border-radius: 3px;
    overflow: hidden;
}
.cat-bar-fill {
    height: 100%;
    border-radius: 3px;
}
.cat-amt {
    font-weight: 700;
    color: var(--inc-text);
    width: 70px;
    text-align: right;
}

#inc-toast {
    position: fixed; bottom: 20px; right: 20px; z-index: 2500;
    display: flex; flex-direction: column; gap: 10px;
}
.toast-msg {
    background: var(--inc-white); border: 1.5px solid var(--inc-border);
    padding: 12px 20px; border-radius: 10px; box-shadow: var(--shadow-md);
    display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 600;
    animation: slideIn 0.3s forwards;
}
@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@media(max-width: 991px) {
    .inc-stats { grid-template-columns: repeat(2, 1fr); }
    .inc-grid { grid-template-columns: 1fr; }
}
@media(max-width: 575px) {
    .inc-stats { grid-template-columns: 1fr; }
}
</style>
@endsection

@section('content')
<div style="padding: 24px;">
    {{-- HERO CARD --}}
    <div class="inc-hero">
        <div class="inc-hero-text">
            <h1>Income Control</h1>
            <p>Track, manage, and monitor all non-academic school revenue streams. Sync vouchers with incoming receipts directly.</p>
        </div>
        <div class="inc-hero-icon"><i class="fas fa-coins"></i></div>
    </div>

    {{-- STATS CARDS --}}
    <div class="inc-stats">
        <div class="inc-stat">
            <span class="inc-stat-title">Income (This Month)</span>
            <span class="inc-stat-value">₹{{ number_format($totalThisMonth, 2) }}</span>
            <span class="inc-stat-label">Active month: <span>{{ Carbon\Carbon::parse($month.'-01')->format('F Y') }}</span></span>
            <i class="fas fa-calendar-alt inc-stat-icon"></i>
        </div>
        <div class="inc-stat">
            <span class="inc-stat-title">Total Income (All Time)</span>
            <span class="inc-stat-value">₹{{ number_format($totalAllTime, 2) }}</span>
            <span class="inc-stat-label">Excluding cancelled transactions</span>
            <i class="fas fa-vault inc-stat-icon"></i>
        </div>
        <div class="inc-stat">
            <span class="inc-stat-title">Pending Approvals</span>
            <span class="inc-stat-value">₹{{ number_format($pendingAmount, 2) }}</span>
            <span class="inc-stat-label">Currently in pending state</span>
            <i class="fas fa-hourglass-half inc-stat-icon"></i>
        </div>
        <div class="inc-stat">
            <span class="inc-stat-title">Transactions Count</span>
            <span class="inc-stat-value">{{ $incomeCount }}</span>
            <span class="inc-stat-label">Transactions this month</span>
            <i class="fas fa-receipt inc-stat-icon"></i>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="inc-filter-bar">
        <form method="GET" action="{{ route('school.income.index') }}" class="inc-filter-group" id="filterForm">
            <input type="month" class="inc-select" name="month" value="{{ $month }}" onchange="this.form.submit()">
            
            <select class="inc-select" name="income_head_id" onchange="this.form.submit()">
                <option value="">All Accounts</option>
                @foreach($incomeHeads as $head)
                <option value="{{ $head->id }}" {{ (string)$incomeHeadId === (string)$head->id ? 'selected' : '' }}>{{ $head->name }}</option>
                @endforeach
            </select>
            
            <select class="inc-select" name="status" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </form>
        <div style="display: flex; gap: 8px;">
            <button class="inc-btn-outline" style="border-color: var(--inc-green-mid); color: var(--inc-green-dark); font-weight: 700; height: 38px; display: inline-flex; align-items: center; gap: 6px; border-radius: 8px;" onclick="openPrintAllConfig('income')">
                Print All Invoices <i class="fas fa-print"></i>
            </button>
            <button class="inc-btn-green" id="addIncomeBtn">
                Add Income <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>

    {{-- CONTENT GRID --}}
    <div class="inc-grid">
        {{-- LEFT: TRANSACTIONS LIST --}}
        <div class="inc-card">
            <div class="inc-card-hdr">
                <div class="inc-card-hdr-left">
                    <div class="inc-card-hdr-icon"><i class="fas fa-list"></i></div>
                    <span class="inc-card-title">Income Ledger</span>
                </div>
            </div>
            <div class="inc-card-body" style="padding: 0;">
                <div class="inc-table-responsive">
                    @if($incomes->isEmpty())
                        <div style="text-align: center; padding: 40px; color: var(--inc-text2);">
                            <i class="fas fa-file-circle-exclamation" style="font-size: 36px; opacity: 0.4; margin-bottom: 12px; display: block; color: var(--inc-green);"></i>
                            No income records found for this period. Click 'Add Income' above.
                        </div>
                    @else
                    <table class="inc-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title / Payer</th>
                                <th>Income Head</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Mode</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="incomeTableBody">
                        @foreach($incomes as $i => $inc)
                        @php
                            $displayStatus = $inc->status;
                            if ($inc->voucher) {
                                $displayStatus = strtolower($inc->voucher->payment_status);
                            }
                        @endphp
                        <tr id="row-{{ $inc->id }}" class="income-row" data-status="{{ $displayStatus }}">
                            <td style="color:var(--inc-text2);">{{ $i + 1 }}</td>
                            <td>
                                <div style="font-weight:600;color:var(--inc-text);">{{ $inc->title }}</div>
                                @if($inc->received_from)<div style="font-size:11.5px;color:var(--inc-text2);">{{ $inc->received_from }}</div>@endif
                            </td>
                            <td><span class="cat-badge"><i class="fas fa-tag"></i> {{ $inc->category_label }}</span></td>
                            <td style="color:var(--inc-text2);">{{ $inc->income_date->format('d M Y') }}</td>
                            <td><strong style="color:var(--inc-green-mid);">₹{{ number_format($inc->amount, 2) }}</strong></td>
                            <td style="color:var(--inc-text2);">{{ ucwords(str_replace('_',' ',$inc->payment_mode)) }}</td>
                            <td>
                                <span class="badge badge-{{ $displayStatus }}">
                                    {{ ucfirst($displayStatus) }}
                                </span>
                            </td>
                            <td>
                                <div class="tbl-actions">
                                    <button class="btn-icon edit" title="Edit" onclick="editIncome({{ $inc->id }}, {{ $inc->toJson() }})">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button class="btn-icon print" title="Print Invoice/Receipt" onclick="openPrintModal('{{ route('school.income.invoice', $inc->id) }}')" style="background: #e0f2fe; color: #0284c7;">
                                        <i class="fas fa-print"></i>
                                    </button>
                                    <button class="btn-icon del" title="Delete" onclick="deleteIncome({{ $inc->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>

        {{-- RIGHT PANEL --}}
        <div style="display:flex;flex-direction:column;gap:22px;">
            {{-- TREND CHART --}}
            <div class="inc-card">
                <div class="inc-card-hdr">
                    <div class="inc-card-hdr-left">
                        <div class="inc-card-hdr-icon"><i class="fas fa-chart-line"></i></div>
                        <span class="inc-card-title">6-Month Trend</span>
                    </div>
                </div>
                <div class="inc-card-body">
                    <div class="chart-container">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- CATEGORY BREAKDOWN --}}
            <div class="inc-card">
                <div class="inc-card-hdr">
                    <div class="inc-card-hdr-left">
                        <div class="inc-card-hdr-icon"><i class="fas fa-pie-chart"></i></div>
                        <span class="inc-card-title">By Account</span>
                    </div>
                </div>
                <div class="inc-card-body">
                    @php
                        $catTotal = $categoryBreakdown->sum();
                        $catColors = ['#10b981','#059669','#34d399','#6ee7b7','#a7f3d0','#047857','#14b8a6'];
                    @endphp
                    @if($categoryBreakdown->isEmpty())
                        <div style="text-align:center;color:var(--inc-text2);padding:30px 0;font-size:13px;">No data for this month.</div>
                    @else
                    <div class="cat-list">
                        @foreach($categoryBreakdown as $catKey => $catAmt)
                        @php
                            $matchingHead = $incomeHeads->firstWhere('id', $catKey);
                            $catLabel = $matchingHead ? $matchingHead->name : 'Other';
                            $catPct = $catTotal > 0 ? round(($catAmt/$catTotal)*100) : 0;
                            $catIdx = $loop->index;
                            $catColor = $catColors[$catIdx % count($catColors)];
                        @endphp
                        <div class="cat-row">
                            <div class="cat-dot" style="background:{{ $catColor }};"></div>
                            <span class="cat-name">{{ $catLabel }}</span>
                            <div class="cat-bar-wrap">
                                <div class="cat-bar-fill" style="width:{{ $catPct }}%; background:{{ $catColor }};"></div>
                            </div>
                            <span class="cat-amt">₹{{ number_format($catAmt, 0) }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ADD / EDIT MODAL --}}
<div class="inc-modal-overlay" id="incomeModal">
    <div class="inc-modal">
        <div class="inc-modal-hdr">
            <h3 id="modalTitle"><i class="fas fa-plus-circle"></i> Add Income</h3>
            <button class="modal-close" id="modalClose"><i class="fas fa-xmark"></i></button>
        </div>
        <div class="inc-modal-body">
            <form id="incomeForm">
                @csrf
                <input type="hidden" id="incomeId" name="income_id">
                <div class="form-grid">
                    <div class="form-group span2">
                        <label>Income Title <span>*</span></label>
                        <input type="text" class="form-control" name="title" id="fTitle" placeholder="e.g. Uniform Sale collection" required>
                    </div>
                    <div class="form-group">
                        <label>Income Head <span>*</span></label>
                        <select class="form-control" name="income_head_id" id="fIncomeHead" required>
                            <option value="">Select Income Head</option>
                            @foreach($incomeHeads as $head)
                            <option value="{{ $head->id }}">{{ $head->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Amount (₹) <span>*</span></label>
                        <input type="number" class="form-control" name="amount" id="fAmount" placeholder="0.00" min="0.01" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Income Date <span>*</span></label>
                        <input type="date" class="form-control" name="income_date" id="fDate" required>
                    </div>
                    <div class="form-group">
                        <label>Payment Mode <span>*</span></label>
                        <select class="form-control" name="payment_mode" id="fMode" required>
                            @foreach($paymentModes as $key => $lbl)
                            <option value="{{ $key }}">{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group span2" id="chequeDetailsContainer" style="display:none; grid-column: span 2; background: rgba(209, 250, 229, 0.4); padding: 12px; border-radius: 8px; border: 1px dashed var(--inc-border); margin-bottom: 10px;">
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; width: 100%;">
                            <div style="display:flex; flex-direction:column; gap:4px;">
                                <label style="font-size:11.5px; font-weight:700;">Bank Name <span style="color:var(--inc-red);">*</span></label>
                                <input type="text" class="form-control" name="bank_name" id="fBankName" placeholder="Bank name">
                            </div>
                            <div style="display:flex; flex-direction:column; gap:4px;">
                                <label style="font-size:11.5px; font-weight:700;">Issue Date <span style="color:var(--inc-red);">*</span></label>
                                <input type="date" class="form-control" name="check_issue_date" id="fCheckIssueDate">
                            </div>
                            <div style="display:flex; flex-direction:column; gap:4px;">
                                <label style="font-size:11.5px; font-weight:700;">Branch <span style="color:var(--inc-red);">*</span></label>
                                <input type="text" class="form-control" name="branch" id="fBranch" placeholder="Branch">
                            </div>
                        </div>
                    </div>
                    <div class="form-group" style="position: relative;">
                        <label>Received From (Payer)</label>
                        <input type="text" class="form-control" name="received_from" id="fReceivedFrom" placeholder="Payer or customer name" autocomplete="off">
                        <div id="payerSuggestions" style="display:none; position: absolute; top: 100%; left: 0; right: 0; background: var(--inc-white); border: 1.5px solid var(--inc-border); border-radius: 8px; z-index: 1000; max-height: 200px; overflow-y: auto; box-shadow: var(--shadow-md);"></div>
                    </div>
                    <div class="form-group">
                        <label>Status <span>*</span></label>
                        <select class="form-control" name="status" id="fStatus" required>
                            <option value="paid">Paid</option>
                            <option value="pending">Pending</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Reference / Cheque No.</label>
                        <input type="text" class="form-control" name="reference_no" id="fRef" placeholder="Optional">
                    </div>
                    <div class="form-group span2">
                        <label>Description</label>
                        <textarea class="form-control" name="description" id="fDesc" placeholder="Additional notes or remarks..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="inc-btn inc-btn-outline" id="modalCancelBtn">Cancel</button>
                    <button type="submit" class="inc-btn inc-btn-primary" id="saveBtn">
                        <i class="fas fa-save"></i> <span>Save Income</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- PRINT INVOICE POPUP MODAL --}}
<div class="inc-modal-overlay" id="printInvoiceModal">
    <div class="inc-modal" style="max-width: 850px; width: 90%;">
        <div class="inc-modal-hdr" style="background: var(--inc-green-dark);">
            <h3><i class="fas fa-print"></i> Generate Invoice / Receipt</h3>
            <button class="modal-close" id="printModalClose" onclick="closePrintModal()"><i class="fas fa-xmark"></i></button>
        </div>
        <div class="inc-modal-body" style="padding: 10px; height: 600px;">
            <iframe id="printInvoiceFrame" src="" style="width: 100%; height: 100%; border: none; border-radius: 8px;"></iframe>
        </div>
    </div>
</div>

{{-- PRINT ALL CONFIG MODAL --}}
<div class="inc-modal-overlay" id="printAllModal">
    <div class="inc-modal" style="max-width: 450px;">
        <div class="inc-modal-hdr" style="background: var(--inc-green-dark);">
            <h3><i class="fas fa-print"></i> Print All Invoices</h3>
            <button class="modal-close" onclick="closePrintAllModal()"><i class="fas fa-xmark"></i></button>
        </div>
        <div class="inc-modal-body" style="padding: 20px;">
            <div class="form-group" style="margin-bottom: 20px;">
                <label style="font-weight: 700; margin-bottom: 8px; display: block; font-size:13px;">How many invoices per A4 page?</label>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px;">
                    <div style="border: 1.5px solid var(--inc-border); border-radius: 8px; padding: 10px; text-align: center; cursor: pointer;" class="per-page-opt" id="opt-1" onclick="selectPerPage(1)">
                        <strong style="display: block; font-size: 16px; color:var(--inc-text);">1</strong>
                        <span style="font-size: 10px; color: var(--inc-text2);">Full Page</span>
                    </div>
                    <div style="border: 1.5px solid var(--inc-green-mid); background: var(--inc-green-light); border-radius: 8px; padding: 10px; text-align: center; cursor: pointer;" class="per-page-opt" id="opt-2" onclick="selectPerPage(2)">
                        <strong style="display: block; font-size: 16px; color:var(--inc-text);">2</strong>
                        <span style="font-size: 10px; color: var(--inc-text2);">Half Page</span>
                    </div>
                    <div style="border: 1.5px solid var(--inc-border); border-radius: 8px; padding: 10px; text-align: center; cursor: pointer;" class="per-page-opt" id="opt-3" onclick="selectPerPage(3)">
                        <strong style="display: block; font-size: 16px; color:var(--inc-text);">3</strong>
                        <span style="font-size: 10px; color: var(--inc-text2);">1/3 Page</span>
                    </div>
                    <div style="border: 1.5px solid var(--inc-border); border-radius: 8px; padding: 10px; text-align: center; cursor: pointer;" class="per-page-opt" id="opt-4" onclick="selectPerPage(4)">
                        <strong style="display: block; font-size: 16px; color:var(--inc-text);">4</strong>
                        <span style="font-size: 10px; color: var(--inc-text2);">1/4 Page</span>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer" style="margin-top:20px; display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="inc-btn inc-btn-outline" onclick="closePrintAllModal()">Cancel</button>
                <button type="button" class="inc-btn inc-btn-primary" onclick="submitPrintAll()">
                    Print <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- TOAST --}}
<div id="inc-toast"></div>
@endsection

@section('scripts')
<script>
// ─── CHART ─────────────────────────────────────────────────────────
const trendCtx = document.getElementById('trendChart').getContext('2d');
const trendChart = new Chart(trendCtx, {
    type: 'bar',
    data: {
        labels: @json($trendMonths),
        datasets: [{
            label: 'Income (₹)',
            data: @json($trendData),
            backgroundColor: 'rgba(16,185,129,.2)',
            borderColor: '#10b981',
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => '₹' + ctx.parsed.y.toLocaleString('en-IN')
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: '#d1fae5' },
                ticks: {
                    callback: v => '₹' + (v >= 1000 ? (v/1000)+'k' : v),
                    font: { size: 11 }
                }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 11 } }
            }
        }
    }
});

// ─── MODAL HELPERS ──────────────────────────────────────────────────
const modal      = document.getElementById('incomeModal');
const form       = document.getElementById('incomeForm');
const modalTitle = document.getElementById('modalTitle');

const chequeContainer = document.getElementById('chequeDetailsContainer');
const fBankName = document.getElementById('fBankName');
const fCheckIssueDate = document.getElementById('fCheckIssueDate');
const fBranch = document.getElementById('fBranch');
const fMode = document.getElementById('fMode');

function toggleChequeFields() {
    if (fMode.value === 'cheque') {
        chequeContainer.style.display = 'block';
        fBankName.required = true;
        fCheckIssueDate.required = true;
        fBranch.required = true;
    } else {
        chequeContainer.style.display = 'none';
        fBankName.required = false;
        fCheckIssueDate.required = false;
        fBranch.required = false;
    }
}
fMode.addEventListener('change', toggleChequeFields);

function openModal() { modal.classList.add('open'); }
function closeModal() {
    modal.classList.remove('open');
    form.reset();
    document.getElementById('incomeId').value = '';
    chequeContainer.style.display = 'none';
    fBankName.required = false;
    fCheckIssueDate.required = false;
    fBranch.required = false;
}

document.getElementById('addIncomeBtn').addEventListener('click', () => {
    modalTitle.innerHTML = '<i class="fas fa-plus-circle"></i> Add Income';
    document.getElementById('fDate').value = new Date().toISOString().split('T')[0];
    openModal();
});
document.getElementById('modalClose').addEventListener('click', closeModal);
document.getElementById('modalCancelBtn').addEventListener('click', closeModal);
modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

// ─── EDIT INCOME ────────────────────────────────────────────────────
function editIncome(id, data) {
    document.getElementById('incomeId').value = id;
    document.getElementById('fTitle').value    = data.title    || '';
    document.getElementById('fIncomeHead').value = data.income_head_id || '';
    document.getElementById('fAmount').value   = data.amount   || '';
    document.getElementById('fDate').value     = data.income_date ? data.income_date.substring(0,10) : '';
    document.getElementById('fMode').value     = data.payment_mode || 'cash';
    fBankName.value = data.bank_name || '';
    fCheckIssueDate.value = data.check_issue_date ? data.check_issue_date.substring(0,10) : '';
    fBranch.value = data.branch || '';
    toggleChequeFields();
    document.getElementById('fReceivedFrom').value = data.received_from || '';
    document.getElementById('fStatus').value   = data.status   || 'paid';
    document.getElementById('fRef').value      = data.reference_no || '';
    document.getElementById('fDesc').value     = data.description  || '';
    modalTitle.innerHTML = '<i class="fas fa-pen"></i> Edit Income';
    openModal();
}

// ─── SUBMIT FORM ────────────────────────────────────────────────────
form.addEventListener('submit', async function(e) {
    e.preventDefault();
    const id  = document.getElementById('incomeId').value;
    const url = id
        ? '{{ url("school/income") }}/' + id
        : '{{ route("school.income.store") }}';
    const method = id ? 'PUT' : 'POST';

    const data = Object.fromEntries(new FormData(form));
    data._token = '{{ csrf_token() }}';
    if (method === 'PUT') data._method = 'PUT';

    const saveBtn = document.getElementById('saveBtn');
    saveBtn.disabled = true;
    saveBtn.querySelector('span').textContent = 'Saving...';

    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({ ...data, _method: method === 'PUT' ? 'PUT' : undefined })
        });
        const json = await res.json();
        if (json.success) {
            showToast(json.message, 'success');
            closeModal();
            if (json.invoice_url) {
                openPrintModal(json.invoice_url);
            } else {
                setTimeout(() => location.reload(), 900);
            }
        } else {
            showToast(json.message || 'Error saving.', 'error');
        }
    } catch(err) {
        showToast('Network error. Please try again.', 'error');
    } finally {
        saveBtn.disabled = false;
        saveBtn.querySelector('span').textContent = 'Save Income';
    }
});

// ─── DELETE ─────────────────────────────────────────────────────────
async function deleteIncome(id) {
    if (!confirm('Delete this income? This action cannot be undone.')) return;
    try {
        const res = await fetch('{{ url("school/income") }}/' + id, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        });
        const json = await res.json();
        if (json.success) {
            showToast(json.message, 'success');
            const row = document.getElementById('row-' + id);
            if (row) { row.style.opacity = '0'; row.style.transform = 'translateX(-20px)'; row.style.transition = '.3s'; setTimeout(() => row.remove(), 300); }
        } else {
            showToast(json.message || 'Error deleting.', 'error');
        }
    } catch(err) {
        showToast('Network error.', 'error');
    }
}

// ─── TOAST ──────────────────────────────────────────────────────────
// ─── PRINT MODAL HELPERS ──────────────────────────────────────────
function openPrintModal(url) {
    const frame = document.getElementById('printInvoiceFrame');
    frame.src = url + '?print=1';
    document.getElementById('printInvoiceModal').classList.add('open');
}

function closePrintModal() {
    document.getElementById('printInvoiceModal').classList.remove('open');
    document.getElementById('printInvoiceFrame').src = '';
    location.reload();
}

// ─── AUTOCONTROL PAYER SUGGESTIONS ───────────────────────────────────
const fReceivedFrom = document.getElementById('fReceivedFrom');
const suggestionsDiv = document.getElementById('payerSuggestions');

fReceivedFrom.addEventListener('input', async function() {
    const query = this.value.trim();
    if (query.length < 2) {
        suggestionsDiv.style.display = 'none';
        return;
    }
    try {
        const res = await fetch('{{ route("school.income.search-payer") }}?query=' + encodeURIComponent(query));
        const data = await res.json();
        if (data.length > 0) {
            suggestionsDiv.innerHTML = data.map(item => `
                <div class="suggestion-item" style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid var(--inc-border); font-size:12.5px;" onclick="selectPayer('${item.raw_name.replace(/'/g, "\\'")}')">
                    <i class="fas fa-${item.type === 'student' ? 'user-graduate' : 'user-tie'}" style="margin-right: 6px; color: var(--inc-green);"></i>
                    <strong>${item.name}</strong>
                </div>
            `).join('');
            suggestionsDiv.style.display = 'block';
        } else {
            suggestionsDiv.style.display = 'none';
        }
    } catch (e) {
        console.error(e);
    }
});

function selectPayer(name) {
    fReceivedFrom.value = name;
    suggestionsDiv.style.display = 'none';
}

document.addEventListener('click', function(e) {
    if (e.target !== fReceivedFrom && e.target !== suggestionsDiv) {
        suggestionsDiv.style.display = 'none';
    }
});

// Styles for hover
const suggStyle = document.createElement('style');
suggStyle.innerHTML = `
    .suggestion-item:hover {
        background-color: var(--inc-green-light) !important;
    }
`;
document.head.appendChild(suggStyle);

function showToast(msg, type = 'success') {
    const toast = document.getElementById('inc-toast');
    const el = document.createElement('div');
    el.className = 'toast-msg';
    el.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}" style="color:${type==='success'?'#10b981':'#ef4444'}"></i> ${msg}`;
    toast.appendChild(el);
    setTimeout(() => el.remove(), 3500);
}

// ─── PRINT ALL CONFIG CONTROLLERS ────────────────────────────────────
let printAllType = 'income';
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
        el.style.borderColor = 'var(--inc-border)';
        el.style.background = 'transparent';
    });
    const selectedOpt = document.getElementById('opt-' + num);
    if (selectedOpt) {
        selectedOpt.style.borderColor = 'var(--inc-green-mid)';
        selectedOpt.style.background = 'var(--inc-green-light)';
    }
}

function submitPrintAll() {
    const rows = document.querySelectorAll('#incomeTableBody tr[id^="row-"]');
    const ids = [];
    rows.forEach(r => {
        const id = r.getAttribute('id').replace('row-', '');
        ids.push(id);
    });
    
    if (ids.length === 0) {
        showToast('No records available to print.', 'error');
        return;
    }
    
    closePrintAllModal();
    const url = '{{ route("school.income.print-all") }}?ids=' + ids.join(',') + '&per_page=' + selectedPerPage + '&type=' + printAllType + '&print=1';
    window.open(url, '_blank', 'width=950,height=750');
}
</script>
@endsection
