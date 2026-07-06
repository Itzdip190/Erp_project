@extends('layouts.app')

@section('title', 'Expenses Control')

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
/* ─── ROOT VARIABLES ─────────────────────────── */
:root {
    --exp-blue:      #1d4ed8;
    --exp-blue-dark: #1e3a8a;
    --exp-blue-light:#eff6ff;
    --exp-blue-mid:  #3b82f6;
    --exp-accent:    #60a5fa;
    --exp-white:     #ffffff;
    --exp-gray:      #f1f5f9;
    --exp-border:    #dbeafe;
    --exp-text:      #1e293b;
    --exp-text2:     #64748b;
    --exp-red:       #ef4444;
    --exp-green:     #10b981;
    --exp-amber:     #f59e0b;
    --shadow-sm: 0 1px 3px rgba(29,78,216,.1);
    --shadow-md: 0 4px 16px rgba(29,78,216,.15);
    --shadow-lg: 0 12px 40px rgba(29,78,216,.2);
}

body.dark-mode {
    --exp-white:     #111827;
    --exp-gray:      #1f2937;
    --exp-border:    #1e293b;
    --exp-text:      #f8fafc;
    --exp-text2:     #94a3b8;
    --exp-blue-light:rgba(29, 78, 216, 0.15);
}

/* ─── PAGE HEADER ────────────────────────────── */
.exp-hero {
    background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 50%, #3b82f6 100%);
    border-radius: 20px;
    padding: 32px 36px;
    margin-bottom: 28px;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    box-shadow: 0 10px 40px rgba(29,78,216,.4);
}
.exp-hero::before {
    content: '';
    position: absolute;
    top: -80px; right: -80px;
    width: 280px; height: 280px;
    background: rgba(255,255,255,.06);
    border-radius: 50%;
    pointer-events: none;
}
.exp-hero::after {
    content: '';
    position: absolute;
    bottom: -50px; left: -40px;
    width: 180px; height: 180px;
    background: rgba(255,255,255,.04);
    border-radius: 50%;
    pointer-events: none;
}
.exp-hero-text h1 {
    font-size: 26px;
    font-weight: 800;
    color: #fff;
    margin-bottom: 6px;
    letter-spacing: -.3px;
}
.exp-hero-text p {
    color: rgba(255,255,255,.75);
    font-size: 13.5px;
    line-height: 1.5;
    max-width: 460px;
}
.exp-hero-icon {
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
.exp-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
    margin-bottom: 28px;
}
.exp-stat {
    background: var(--exp-white);
    border: 1.5px solid var(--exp-border);
    border-radius: 16px;
    padding: 22px 22px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: var(--shadow-sm);
    transition: transform .22s, box-shadow .22s;
    cursor: default;
    position: relative;
    overflow: hidden;
}
.exp-stat::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--exp-blue), var(--exp-accent));
    border-radius: 16px 16px 0 0;
}
.exp-stat:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-md);
}
.exp-stat-icon {
    width: 52px; height: 52px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
}
.exp-stat-icon.blue  { background: rgba(29,78,216,.1);  color: var(--exp-blue); }
.exp-stat-icon.green { background: rgba(16,185,129,.1); color: var(--exp-green); }
.exp-stat-icon.amber { background: rgba(245,158,11,.1); color: var(--exp-amber); }
.exp-stat-icon.red   { background: rgba(239,68,68,.1);  color: var(--exp-red); }
.exp-stat-val {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--exp-text);
    line-height: 1;
}
.exp-stat-lbl {
    font-size: 11.5px;
    font-weight: 600;
    color: var(--exp-text2);
    text-transform: uppercase;
    letter-spacing: .5px;
    margin-top: 4px;
}

/* ─── GRID LAYOUT ────────────────────────────── */
.exp-grid {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 22px;
    margin-bottom: 28px;
}

/* ─── CARDS ──────────────────────────────────── */
.exp-card {
    background: var(--exp-white);
    border: 1.5px solid var(--exp-border);
    border-radius: 16px;
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}
.exp-card-hdr {
    padding: 18px 22px;
    border-bottom: 1px solid var(--exp-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}
.exp-card-hdr-left {
    display: flex; align-items: center; gap: 10px;
}
.exp-card-hdr-icon {
    width: 36px; height: 36px;
    background: linear-gradient(135deg, var(--exp-blue-dark), var(--exp-blue));
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 15px;
}
.exp-card-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--exp-text);
}
.exp-card-body { padding: 22px; }

/* ─── FILTER BAR ─────────────────────────────── */
.exp-filter-bar {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 22px;
    padding: 16px 22px;
    background: var(--exp-blue-light);
    border: 1.5px solid var(--exp-border);
    border-radius: 14px;
}
.exp-filter-bar select,
.exp-filter-bar input[type="month"] {
    padding: 8px 12px;
    border: 1.5px solid var(--exp-border);
    border-radius: 9px;
    font-size: 13px;
    color: var(--exp-text);
    background: var(--exp-white);
    outline: none;
    transition: border-color .2s;
    cursor: pointer;
}
.exp-filter-bar select:focus,
.exp-filter-bar input[type="month"]:focus {
    border-color: var(--exp-blue-mid);
}
.exp-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 18px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all .2s;
    text-decoration: none;
}
.exp-btn-primary {
    background: linear-gradient(135deg, var(--exp-blue-dark), var(--exp-blue));
    color: #fff;
    box-shadow: 0 4px 14px rgba(29,78,216,.35);
}
.exp-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(29,78,216,.45);
    color: #fff;
}
.exp-btn-outline {
    background: var(--exp-white);
    color: var(--exp-blue);
    border: 1.5px solid var(--exp-border);
}
.exp-btn-outline:hover {
    border-color: var(--exp-blue-mid);
    background: var(--exp-blue-light);
}
.ml-auto { margin-left: auto; }

/* ─── TABLE ──────────────────────────────────── */
.exp-table-wrap { overflow-x: auto; }
table.exp-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
table.exp-table th {
    padding: 11px 14px;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--exp-blue);
    background: var(--exp-blue-light);
    border-bottom: 2px solid var(--exp-border);
}
table.exp-table td {
    padding: 13px 14px;
    color: var(--exp-text);
    border-bottom: 1px solid var(--exp-gray);
    vertical-align: middle;
}
table.exp-table tr:last-child td { border-bottom: none; }
table.exp-table tbody tr:hover { background: var(--exp-blue-light); }

/* ─── BADGES ─────────────────────────────────── */
.badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 9px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .3px;
}
.badge-paid    { background: rgba(16,185,129,.1);  color: #059669; }
.badge-pending { background: rgba(245,158,11,.12); color: #b45309; }
.badge-cancelled { background: rgba(239,68,68,.1); color: #dc2626; }

.cat-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 9px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 600;
    background: rgba(29,78,216,.08);
    color: var(--exp-blue);
}

/* ─── ACTION BTNS ────────────────────────────── */
.tbl-actions { display: flex; gap: 6px; }
.btn-icon {
    width: 30px; height: 30px;
    border-radius: 8px;
    border: 1.5px solid var(--exp-border);
    background: var(--exp-white);
    display: flex; align-items: center; justify-content: center;
    font-size: 12px;
    cursor: pointer;
    transition: all .2s;
}
.btn-icon:hover { transform: scale(1.1); }
.btn-icon.edit  { color: var(--exp-blue);  border-color: rgba(29,78,216,.2); }
.btn-icon.del   { color: var(--exp-red);   border-color: rgba(239,68,68,.2); }
.btn-icon.edit:hover { background: rgba(29,78,216,.1); }
.btn-icon.del:hover  { background: rgba(239,68,68,.1); }

/* ─── EMPTY STATE ────────────────────────────── */
.exp-empty {
    text-align: center;
    padding: 60px 20px;
    color: var(--exp-text2);
}
.exp-empty i {
    font-size: 48px;
    color: var(--exp-border);
    margin-bottom: 16px;
}
.exp-empty h3 { font-size: 16px; font-weight: 700; margin-bottom: 6px; }
.exp-empty p  { font-size: 13px; }

/* ─── CHART ──────────────────────────────────── */
.chart-container { position: relative; height: 220px; }

/* ─── CATEGORY LIST ──────────────────────────── */
.cat-list { display: flex; flex-direction: column; gap: 10px; }
.cat-row {
    display: flex;
    align-items: center;
    gap: 10px;
}
.cat-dot {
    width: 10px; height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}
.cat-name { font-size: 12.5px; font-weight: 600; color: var(--exp-text); flex: 1; }
.cat-bar-wrap {
    flex: 2;
    height: 6px;
    background: var(--exp-gray);
    border-radius: 10px;
    overflow: hidden;
}
.cat-bar-fill {
    height: 100%;
    border-radius: 10px;
    background: linear-gradient(90deg, var(--exp-blue), var(--exp-accent));
    transition: width .5s ease;
}
.cat-amt { font-size: 12.5px; font-weight: 700; color: var(--exp-blue); min-width: 80px; text-align: right; }

/* ─── MODAL ──────────────────────────────────── */
.exp-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.45);
    z-index: 9000;
    display: none;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(2px);
}
.exp-modal-overlay.open { display: flex; }
.exp-modal {
    background: var(--exp-white);
    border-radius: 20px;
    width: 100%;
    max-width: 580px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: var(--shadow-lg);
    animation: slideUp .3s cubic-bezier(.34,1.56,.64,1);
}
@keyframes slideUp {
    from { opacity: 0; transform: translateY(40px) scale(.96); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
.exp-modal-hdr {
    background: linear-gradient(135deg, var(--exp-blue-dark), var(--exp-blue));
    padding: 22px 26px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-radius: 20px 20px 0 0;
}
.exp-modal-hdr h3 { color: #fff; font-size: 17px; font-weight: 700; }
.modal-close {
    background: rgba(255,255,255,.15);
    border: none;
    color: #fff;
    width: 30px; height: 30px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    display: flex; align-items: center; justify-content: center;
    transition: background .2s;
}
.modal-close:hover { background: rgba(255,255,255,.3); }
.exp-modal-body { padding: 26px; }

/* ─── FORM ───────────────────────────────────── */
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
.form-grid.full { grid-template-columns: 1fr; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group.span2 { grid-column: 1 / -1; }
.form-group label {
    font-size: 12.5px;
    font-weight: 600;
    color: var(--exp-text);
}
.form-group label span { color: var(--exp-red); }
.form-control {
    padding: 9px 13px;
    border: 1.5px solid var(--exp-border);
    border-radius: 10px;
    font-size: 13.5px;
    color: var(--exp-text);
    background: var(--exp-white);
    outline: none;
    transition: border-color .2s, box-shadow .2s;
    font-family: inherit;
    width: 100%;
}
.form-control:focus {
    border-color: var(--exp-blue-mid);
    box-shadow: 0 0 0 3px rgba(59,130,246,.15);
}
textarea.form-control { resize: vertical; min-height: 80px; }

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 22px;
    padding-top: 18px;
    border-top: 1px solid var(--exp-border);
}

/* ─── TOAST ──────────────────────────────────── */
#exp-toast {
    position: fixed;
    bottom: 28px;
    right: 28px;
    z-index: 99999;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.toast-msg {
    background: var(--exp-white);
    border-left: 4px solid var(--exp-blue);
    border-radius: 10px;
    padding: 13px 18px;
    box-shadow: var(--shadow-md);
    font-size: 13.5px;
    font-weight: 600;
    color: var(--exp-text);
    display: flex;
    align-items: center;
    gap: 10px;
    animation: toastIn .3s ease;
    min-width: 260px;
}
.toast-msg.success { border-color: var(--exp-green); }
.toast-msg.error   { border-color: var(--exp-red); }
@keyframes toastIn {
    from { opacity: 0; transform: translateX(40px); }
    to   { opacity: 1; transform: translateX(0); }
}

/* ─── RESPONSIVE ─────────────────────────────── */
@media (max-width: 1100px) {
    .exp-stats { grid-template-columns: repeat(2, 1fr); }
    .exp-grid  { grid-template-columns: 1fr; }
}
@media (max-width: 640px) {
    .exp-stats { grid-template-columns: 1fr 1fr; }
    .form-grid { grid-template-columns: 1fr; }
}
</style>
@endsection

@section('content')

{{-- HERO BANNER --}}
<div class="exp-hero">
    <div class="exp-hero-text">
        <h1><i class="fas fa-wallet" style="margin-right:10px;"></i>Expenses Control</h1>
        <p>Track, manage, and analyse all school expenses in one place. Every transaction reflects automatically on your dashboard.</p>
    </div>
    <div class="exp-hero-icon">
        <i class="fas fa-chart-pie"></i>
    </div>
</div>

{{-- STAT CARDS --}}
<div class="exp-stats">
    <div class="exp-stat">
        <div class="exp-stat-icon blue"><i class="fas fa-rupee-sign"></i></div>
        <div>
            <div class="exp-stat-val">₹{{ number_format($totalThisMonth, 0) }}</div>
            <div class="exp-stat-lbl">This Month</div>
        </div>
    </div>
    <div class="exp-stat">
        <div class="exp-stat-icon green"><i class="fas fa-database"></i></div>
        <div>
            <div class="exp-stat-val">₹{{ number_format($totalAllTime, 0) }}</div>
            <div class="exp-stat-lbl">Total Expenses</div>
        </div>
    </div>
    <div class="exp-stat">
        <div class="exp-stat-icon amber"><i class="fas fa-clock"></i></div>
        <div>
            <div class="exp-stat-val">₹{{ number_format($pendingAmount, 0) }}</div>
            <div class="exp-stat-lbl">Pending Amount</div>
        </div>
    </div>
    <div class="exp-stat">
        <div class="exp-stat-icon red"><i class="fas fa-receipt"></i></div>
        <div>
            <div class="exp-stat-val">{{ $expenseCount }}</div>
            <div class="exp-stat-lbl">Transactions</div>
        </div>
    </div>
</div>

{{-- FILTER BAR --}}
<form method="GET" action="{{ route('school.expenses.index') }}" id="filterForm">
<div class="exp-filter-bar">
    <i class="fas fa-filter" style="color: var(--exp-blue); font-size:14px;"></i>
    <input type="month" name="month" value="{{ $month }}" onchange="this.form.submit()">
    <select name="category" onchange="this.form.submit()">
        <option value="">All Categories</option>
        @foreach($categories as $key => $label)
            <option value="{{ $key }}" {{ $category === $key ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    <select name="status" onchange="this.form.submit()">
        <option value="">All Statuses</option>
        <option value="paid"      {{ $status === 'paid'      ? 'selected' : '' }}>Paid</option>
        <option value="pending"   {{ $status === 'pending'   ? 'selected' : '' }}>Pending</option>
        <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
    </select>
    <div class="ml-auto" style="display:flex;gap:10px;">
        <button type="button" class="exp-btn exp-btn-primary" id="addExpenseBtn">
            <i class="fas fa-plus"></i> Add Expense
        </button>
    </div>
</div>
</form>

{{-- MAIN GRID --}}
<div class="exp-grid">

    {{-- EXPENSES TABLE --}}
    <div class="exp-card">
        <div class="exp-card-hdr">
            <div class="exp-card-hdr-left">
                <div class="exp-card-hdr-icon"><i class="fas fa-list"></i></div>
                <span class="exp-card-title">Expense Transactions</span>
            </div>
            <span style="font-size:12px;color:var(--exp-text2);">{{ $expenses->count() }} records</span>
        </div>
        <div class="exp-table-wrap">
            @if($expenses->isEmpty())
            <div class="exp-empty">
                <i class="fas fa-receipt"></i>
                <h3>No expenses found</h3>
                <p>No expense records match your filter. Try adjusting the filters or add a new expense.</p>
                <button class="exp-btn exp-btn-primary" style="margin-top:14px;" id="addExpenseBtn2">
                    <i class="fas fa-plus"></i> Add First Expense
                </button>
            </div>
            @else
            <table class="exp-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title / Payee</th>
                        <th>Category</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Mode</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="expenseTableBody">
                @foreach($expenses as $i => $exp)
                <tr id="row-{{ $exp->id }}">
                    <td style="color:var(--exp-text2);">{{ $i + 1 }}</td>
                    <td>
                        <div style="font-weight:600;color:var(--exp-text);">{{ $exp->title }}</div>
                        @if($exp->paid_to)<div style="font-size:11.5px;color:var(--exp-text2);">{{ $exp->paid_to }}</div>@endif
                    </td>
                    <td><span class="cat-badge"><i class="fas fa-tag"></i> {{ $exp->category_label }}</span></td>
                    <td style="color:var(--exp-text2);">{{ $exp->expense_date->format('d M Y') }}</td>
                    <td><strong style="color:var(--exp-blue);">₹{{ number_format($exp->amount, 2) }}</strong></td>
                    <td style="color:var(--exp-text2);">{{ ucwords(str_replace('_',' ',$exp->payment_mode)) }}</td>
                    <td>
                        <span class="badge badge-{{ $exp->status }}">
                            {{ ucfirst($exp->status) }}
                        </span>
                    </td>
                    <td>
                        <div class="tbl-actions">
                            <button class="btn-icon edit" title="Edit" onclick="editExpense({{ $exp->id }}, {{ $exp->toJson() }})">
                                <i class="fas fa-pen"></i>
                            </button>
                            <button class="btn-icon del" title="Delete" onclick="deleteExpense({{ $exp->id }})">
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

    {{-- RIGHT PANEL --}}
    <div style="display:flex;flex-direction:column;gap:22px;">

        {{-- TREND CHART --}}
        <div class="exp-card">
            <div class="exp-card-hdr">
                <div class="exp-card-hdr-left">
                    <div class="exp-card-hdr-icon"><i class="fas fa-chart-line"></i></div>
                    <span class="exp-card-title">6-Month Trend</span>
                </div>
            </div>
            <div class="exp-card-body">
                <div class="chart-container">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>

        {{-- CATEGORY BREAKDOWN --}}
        <div class="exp-card">
            <div class="exp-card-hdr">
                <div class="exp-card-hdr-left">
                    <div class="exp-card-hdr-icon"><i class="fas fa-pie-chart"></i></div>
                    <span class="exp-card-title">By Category</span>
                </div>
            </div>
            <div class="exp-card-body">
                @php
                    $catTotal = $categoryBreakdown->sum();
                    $catColors = ['#1d4ed8','#3b82f6','#60a5fa','#93c5fd','#bfdbfe','#1e3a8a','#2563eb'];
                @endphp
                @if($categoryBreakdown->isEmpty())
                    <div style="text-align:center;color:var(--exp-text2);padding:30px 0;font-size:13px;">No data for this month.</div>
                @else
                <div class="cat-list">
                    @foreach($categoryBreakdown as $catKey => $catAmt)
                    @php
                        $catLabel = $categories[$catKey] ?? ucfirst($catKey);
                        $catPct = $catTotal > 0 ? round(($catAmt/$catTotal)*100) : 0;
                        $catIdx = array_search($catKey, array_keys($categories));
                        $catColor = $catIdx !== false ? $catColors[$catIdx % count($catColors)] : '#1d4ed8';
                    @endphp
                    <div class="cat-row">
                        <div class="cat-dot" style="background:var(--exp-blue);"></div>
                        <span class="cat-name">{{ $catLabel }}</span>
                        <div class="cat-bar-wrap">
                            <div class="cat-bar-fill" style="width:{{ $catPct }}%;"></div>
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

{{-- ADD / EDIT MODAL --}}
<div class="exp-modal-overlay" id="expenseModal">
    <div class="exp-modal">
        <div class="exp-modal-hdr">
            <h3 id="modalTitle"><i class="fas fa-plus-circle"></i> Add Expense</h3>
            <button class="modal-close" id="modalClose"><i class="fas fa-xmark"></i></button>
        </div>
        <div class="exp-modal-body">
            <form id="expenseForm">
                @csrf
                <input type="hidden" id="expenseId" name="expense_id">
                <div class="form-grid">
                    <div class="form-group span2">
                        <label>Expense Title <span>*</span></label>
                        <input type="text" class="form-control" name="title" id="fTitle" placeholder="e.g. Monthly Electricity Bill" required>
                    </div>
                    <div class="form-group">
                        <label>Category <span>*</span></label>
                        <select class="form-control" name="category" id="fCategory" required>
                            <option value="">Select category</option>
                            @foreach($categories as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Amount (₹) <span>*</span></label>
                        <input type="number" class="form-control" name="amount" id="fAmount" placeholder="0.00" min="0.01" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Expense Date <span>*</span></label>
                        <input type="date" class="form-control" name="expense_date" id="fDate" required>
                    </div>
                    <div class="form-group">
                        <label>Payment Mode <span>*</span></label>
                        <select class="form-control" name="payment_mode" id="fMode" required>
                            @foreach($paymentModes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Paid To / Vendor</label>
                        <input type="text" class="form-control" name="paid_to" id="fPaidTo" placeholder="Vendor or payee name">
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
                    <button type="button" class="exp-btn exp-btn-outline" id="modalCancelBtn">Cancel</button>
                    <button type="submit" class="exp-btn exp-btn-primary" id="saveBtn">
                        <i class="fas fa-save"></i> <span>Save Expense</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- TOAST --}}
<div id="exp-toast"></div>

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
            label: 'Expenses (₹)',
            data: @json($trendData),
            backgroundColor: 'rgba(29,78,216,.2)',
            borderColor: '#1d4ed8',
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
                grid: { color: '#dbeafe' },
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
const modal      = document.getElementById('expenseModal');
const form       = document.getElementById('expenseForm');
const modalTitle = document.getElementById('modalTitle');

function openModal() { modal.classList.add('open'); }
function closeModal() { modal.classList.remove('open'); form.reset(); document.getElementById('expenseId').value = ''; }

document.getElementById('addExpenseBtn').addEventListener('click', () => {
    modalTitle.innerHTML = '<i class="fas fa-plus-circle"></i> Add Expense';
    document.getElementById('fDate').value = new Date().toISOString().split('T')[0];
    openModal();
});
const addBtn2 = document.getElementById('addExpenseBtn2');
if (addBtn2) addBtn2.addEventListener('click', () => {
    modalTitle.innerHTML = '<i class="fas fa-plus-circle"></i> Add Expense';
    document.getElementById('fDate').value = new Date().toISOString().split('T')[0];
    openModal();
});
document.getElementById('modalClose').addEventListener('click', closeModal);
document.getElementById('modalCancelBtn').addEventListener('click', closeModal);
modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

// ─── EDIT EXPENSE ───────────────────────────────────────────────────
function editExpense(id, data) {
    document.getElementById('expenseId').value = id;
    document.getElementById('fTitle').value    = data.title    || '';
    document.getElementById('fCategory').value = data.category || '';
    document.getElementById('fAmount').value   = data.amount   || '';
    document.getElementById('fDate').value     = data.expense_date ? data.expense_date.substring(0,10) : '';
    document.getElementById('fMode').value     = data.payment_mode || 'cash';
    document.getElementById('fPaidTo').value   = data.paid_to  || '';
    document.getElementById('fStatus').value   = data.status   || 'paid';
    document.getElementById('fRef').value      = data.reference_no || '';
    document.getElementById('fDesc').value     = data.description  || '';
    modalTitle.innerHTML = '<i class="fas fa-pen"></i> Edit Expense';
    openModal();
}

// ─── SUBMIT FORM ────────────────────────────────────────────────────
form.addEventListener('submit', async function(e) {
    e.preventDefault();
    const id  = document.getElementById('expenseId').value;
    const url = id
        ? '{{ url("school/expenses") }}/' + id
        : '{{ route("school.expenses.store") }}';
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
            setTimeout(() => location.reload(), 900);
        } else {
            showToast(json.message || 'Error saving.', 'error');
        }
    } catch(err) {
        showToast('Network error. Please try again.', 'error');
    } finally {
        saveBtn.disabled = false;
        saveBtn.querySelector('span').textContent = 'Save Expense';
    }
});

// ─── DELETE ─────────────────────────────────────────────────────────
async function deleteExpense(id) {
    if (!confirm('Delete this expense? This action cannot be undone.')) return;
    try {
        const res = await fetch('{{ url("school/expenses") }}/' + id, {
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
function showToast(msg, type = 'success') {
    const toast = document.getElementById('exp-toast');
    const el = document.createElement('div');
    el.className = 'toast-msg ' + type;
    el.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}" style="color:${type==='success'?'#10b981':'#ef4444'}"></i> ${msg}`;
    toast.appendChild(el);
    setTimeout(() => el.remove(), 3500);
}
</script>
@endsection
