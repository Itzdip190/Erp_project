@extends('layouts.app')

@section('title', 'Earning & Expense Reports')

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

/* ─── DARK MODE OVERRIDES ─────────────────────── */
body.dark-mode {
    --exp-white:     #111827;
    --exp-gray:      #1f2937;
    --exp-border:    #1e293b;
    --exp-text:      #f8fafc;
    --exp-text2:     #94a3b8;
    --exp-blue-light:rgba(29, 78, 216, 0.15);
}

/* ─── PAGE HEADER & HERO ──────────────────────── */
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
.exp-hero-text h1 {
    font-size: 26px;
    font-weight: 800;
    color: #fff;
    margin-bottom: 6px;
    letter-spacing: -.3px;
}
.exp-hero-text p {
    color: rgba(255,255,255,.8);
    font-size: 13.5px;
    line-height: 1.5;
    max-width: 480px;
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
    padding: 22px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: var(--shadow-sm);
    transition: transform .22s, box-shadow .22s;
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
.exp-stat-icon.red   { background: rgba(239,68,68,.1);  color: var(--exp-red); }
.exp-stat-icon.purple{ background: rgba(139,92,246,.1); color: #8b5cf6; }

.exp-stat-val {
    font-size: 1.6rem;
    font-weight: 800;
    color: var(--exp-text);
    line-height: 1.1;
}
.exp-stat-lbl {
    font-size: 11px;
    font-weight: 700;
    color: var(--exp-text2);
    text-transform: uppercase;
    letter-spacing: .5px;
    margin-top: 4px;
}

/* ─── FILTER BAR ─────────────────────────────── */
.exp-filter-bar {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 24px;
    padding: 16px 22px;
    background: var(--exp-blue-light);
    border: 1.5px solid var(--exp-border);
    border-radius: 14px;
}
.exp-filter-bar select,
.exp-filter-bar input[type="date"] {
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
.exp-filter-bar input[type="date"]:focus {
    border-color: var(--exp-blue-mid);
}
.exp-filter-label {
    font-size: 13px;
    font-weight: 700;
    color: var(--exp-text);
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
    color: #fff !important;
    box-shadow: 0 4px 14px rgba(29,78,216,.35);
}
.exp-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(29,78,216,.45);
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

/* ─── GRID LAYOUTS ───────────────────────────── */
.reports-charts-grid {
    display: grid;
    grid-template-columns: 3fr 2fr;
    gap: 22px;
    margin-bottom: 28px;
}
.pie-charts-box {
    display: flex;
    flex-direction: column;
    gap: 22px;
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

/* ─── TABS & SEARCH FOR TABLE ────────────────── */
.table-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 22px;
    background: var(--exp-blue-light);
    border-bottom: 1px solid var(--exp-border);
    gap: 16px;
    flex-wrap: wrap;
}
.table-tabs {
    display: flex;
    gap: 8px;
}
.tab-btn {
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 700;
    cursor: pointer;
    border: 1.5px solid transparent;
    background: transparent;
    color: var(--exp-text2);
    transition: all .2s;
}
.tab-btn:hover {
    color: var(--exp-blue);
    background: rgba(29,78,216,.05);
}
.tab-btn.active {
    background: var(--exp-white);
    color: var(--exp-blue);
    border-color: var(--exp-border);
    box-shadow: var(--shadow-sm);
}
.search-input-wrap {
    position: relative;
    max-width: 280px;
    width: 100%;
}
.search-input-wrap i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--exp-text2);
    font-size: 13px;
}
.search-input-wrap input {
    width: 100%;
    padding: 7px 12px 7px 34px;
    border-radius: 8px;
    border: 1.5px solid var(--exp-border);
    font-size: 13px;
    background: var(--exp-white);
    color: var(--exp-text);
    outline: none;
    transition: border-color .2s;
}
.search-input-wrap input:focus {
    border-color: var(--exp-blue-mid);
}

/* ─── TABLE ──────────────────────────────────── */
.exp-table-wrap { overflow-x: auto; }
table.exp-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
table.exp-table th {
    padding: 12px 16px;
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
    padding: 14px 16px;
    color: var(--exp-text);
    border-bottom: 1px solid var(--exp-border);
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
    font-size: 10.5px;
    font-weight: 700;
    letter-spacing: .3px;
}
.badge-income { background: rgba(16,185,129,.1); color: #059669; }
.badge-expense { background: rgba(239,68,68,.1); color: #dc2626; }

.cat-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    background: rgba(29,78,216,.06);
    color: var(--exp-blue);
}

/* ─── CHARTS CONTAINERS ───────────────────────── */
.chart-container-trend { position: relative; height: 320px; }
.chart-container-pie { position: relative; height: 200px; display: flex; justify-content: center; }

/* ─── PRINT ONLY STYLES ───────────────────────── */
@media print {
    .sidebar, .topbar, .exp-filter-bar, .table-toolbar, .exp-btn, .theme-toggle-btn {
        display: none !important;
    }
    body, .main, .content-wrapper {
        background: #fff !important;
        color: #000 !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .exp-hero {
        background: #eff6ff !important;
        border: 1px solid #1d4ed8 !important;
        box-shadow: none !important;
        color: #000 !important;
    }
    .exp-hero-text h1 { color: #1d4ed8 !important; }
    .exp-hero-text p { color: #333 !important; }
    .exp-hero-icon { display: none !important; }
    .exp-stat {
        border: 1px solid #ccc !important;
        box-shadow: none !important;
        background: #fff !important;
    }
    .reports-charts-grid {
        grid-template-columns: 1fr !important;
        gap: 40px !important;
    }
    .exp-card {
        border: 1px solid #ccc !important;
        box-shadow: none !important;
        background: #fff !important;
        page-break-inside: avoid;
    }
    table.exp-table th {
        background: #f1f5f9 !important;
        color: #000 !important;
        border-bottom: 1.5px solid #000 !important;
    }
    table.exp-table td {
        border-bottom: 1px solid #ddd !important;
    }
}

@media (max-width: 991px) {
    .reports-charts-grid { grid-template-columns: 1fr; }
    .exp-stats { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 600px) {
    .exp-stats { grid-template-columns: 1fr; }
}
</style>
@endsection

@section('content')

{{-- TITLE HERO --}}
<div class="exp-hero">
    <div class="exp-hero-text">
        <h1><i class="fas fa-chart-line" style="margin-right:10px;"></i>Earning & Expense Reports</h1>
        <p>A comprehensive overview of your school's finances. Track fees collected, operational and vehicle expenditures, and monitor net cash flows.</p>
    </div>
    <div class="exp-hero-icon">
        <i class="fas fa-file-invoice-dollar"></i>
    </div>
</div>

{{-- FILTERS PANEL --}}
<form method="GET" action="{{ route('school.expenses.reports') }}" id="reportFilterForm">
<div class="exp-filter-bar">
    <i class="fas fa-filter" style="color: var(--exp-blue); font-size:14px;"></i>
    <span class="exp-filter-label">Range Preset:</span>
    <select name="preset" onchange="toggleCustomDates(this.value); this.form.submit();">
        <option value="this_month" {{ $preset === 'this_month' ? 'selected' : '' }}>This Month</option>
        <option value="last_month" {{ $preset === 'last_month' ? 'selected' : '' }}>Last Month</option>
        <option value="this_year" {{ $preset === 'this_year' ? 'selected' : '' }}>This Year (Jan-Dec)</option>
        <option value="academic_year" {{ $preset === 'academic_year' ? 'selected' : '' }}>Academic Year (Apr-Mar)</option>
        <option value="custom" {{ $preset === 'custom' ? 'selected' : '' }}>Custom Date Range</option>
    </select>

    <div id="custom-date-container" style="display: {{ $preset === 'custom' ? 'flex' : 'none' }}; align-items: center; gap: 8px;">
        <span class="exp-filter-label" style="margin-left:8px;">From:</span>
        <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" onchange="this.form.submit()">
        <span class="exp-filter-label">To:</span>
        <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" onchange="this.form.submit()">
    </div>

    <div class="ml-auto">
        <button type="button" class="exp-btn exp-btn-outline" onclick="window.print()">
            <i class="fas fa-print"></i> Print / PDF Report
        </button>
    </div>
</div>
</form>

{{-- KPI STATUS CARDS --}}
<div class="exp-stats">
    <div class="exp-stat">
        <div class="exp-stat-icon green"><i class="fas fa-arrow-trend-up"></i></div>
        <div>
            <div class="exp-stat-val">₹{{ number_format($totalIncome, 2) }}</div>
            <div class="exp-stat-lbl">Total Earnings (Income)</div>
        </div>
    </div>
    <div class="exp-stat">
        <div class="exp-stat-icon red"><i class="fas fa-arrow-trend-down"></i></div>
        <div>
            <div class="exp-stat-val">₹{{ number_format($totalExpense, 2) }}</div>
            <div class="exp-stat-lbl">Total Expenses</div>
        </div>
    </div>
    <div class="exp-stat">
        <div class="exp-stat-icon {{ $netProfit >= 0 ? 'blue' : 'red' }}">
            <i class="fas {{ $netProfit >= 0 ? 'fa-scale-balanced' : 'fa-triangle-exclamation' }}"></i>
        </div>
        <div>
            <div class="exp-stat-val">₹{{ number_format($netProfit, 2) }}</div>
            <div class="exp-stat-lbl">Net Cash Flow</div>
        </div>
    </div>
    <div class="exp-stat">
        <div class="exp-stat-icon purple"><i class="fas fa-percentage"></i></div>
        <div>
            <div class="exp-stat-val">{{ number_format($expenseRatio, 1) }}%</div>
            <div class="exp-stat-lbl">Expense-to-Income</div>
        </div>
    </div>
</div>

{{-- CHARTS BLOCK --}}
<div class="reports-charts-grid">
    
    {{-- Income vs Expense Trend Bar/Line --}}
    <div class="exp-card">
        <div class="exp-card-hdr">
            <div class="exp-card-hdr-left">
                <div class="exp-card-hdr-icon"><i class="fas fa-chart-column"></i></div>
                <span class="exp-card-title">Income & Expense Trend (Last 6 Months)</span>
            </div>
        </div>
        <div class="exp-card-body">
            <div class="chart-container-trend">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Categorized Pie charts --}}
    <div class="pie-charts-box">
        
        {{-- Expense category pie chart --}}
        <div class="exp-card">
            <div class="exp-card-hdr">
                <div class="exp-card-hdr-left">
                    <div class="exp-card-hdr-icon"><i class="fas fa-chart-pie"></i></div>
                    <span class="exp-card-title">Expense Categories</span>
                </div>
            </div>
            <div class="exp-card-body">
                <div class="chart-container-pie">
                    @if(count($expenseCategoryBreakdown) > 0)
                        <canvas id="expensePieChart"></canvas>
                    @else
                        <div style="display:flex;align-items:center;justify-content:center;height:100%;color:var(--exp-text2);font-size:13px;width:100%;">
                            No expense records in this range.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Income modes pie chart --}}
        <div class="exp-card">
            <div class="exp-card-hdr">
                <div class="exp-card-hdr-left">
                    <div class="exp-card-hdr-icon"><i class="fas fa-chart-pie"></i></div>
                    <span class="exp-card-title">Earning Sources (By Payment Mode)</span>
                </div>
            </div>
            <div class="exp-card-body">
                <div class="chart-container-pie">
                    @if(count($incomeModeBreakdown) > 0)
                        <canvas id="incomePieChart"></canvas>
                    @else
                        <div style="display:flex;align-items:center;justify-content:center;height:100%;color:var(--exp-text2);font-size:13px;width:100%;">
                            No earning records in this range.
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

{{-- DETAILED TRANSACTIONS LIST --}}
<div class="exp-card" style="margin-bottom: 40px;">
    <div class="exp-card-hdr">
        <div class="exp-card-hdr-left">
            <div class="exp-card-hdr-icon"><i class="fas fa-receipt"></i></div>
            <span class="exp-card-title">Statements & Transaction Ledger</span>
        </div>
        <span style="font-size:12px;color:var(--exp-text2);" id="transactions-count">{{ count($transactions) }} records</span>
    </div>

    {{-- Toolbar inside Card for live filtering --}}
    <div class="table-toolbar">
        <div class="table-tabs">
            <button class="tab-btn active" onclick="switchTab('all', this)">All Ledger</button>
            <button class="tab-btn" onclick="switchTab('income', this)">Earnings Only</button>
            <button class="tab-btn" onclick="switchTab('expense', this)">Expenses Only</button>
        </div>
        <div class="search-input-wrap">
            <i class="fas fa-search"></i>
            <input type="text" id="ledgerSearchInput" placeholder="Search title or payee..." onkeyup="filterLedgerTable()">
        </div>
    </div>

    <div class="exp-table-wrap">
        <table class="exp-table" id="ledgerTable">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Category</th>
                    <th>Particulars / Payee</th>
                    <th>Reference / Receipt</th>
                    <th>Payment Mode</th>
                    <th style="text-align: right;">Amount (₹)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                <tr class="tx-row" data-type="{{ $tx->type }}" data-title="{{ strtolower($tx->title) }}" data-payee="{{ strtolower($tx->payee) }}">
                    <td>{{ \Carbon\Carbon::parse($tx->date)->format('d M Y') }}</td>
                    <td>
                        <span class="badge badge-{{ $tx->type }}">
                            {{ ucfirst($tx->type) }}
                        </span>
                    </td>
                    <td><span class="cat-badge">{{ $tx->category }}</span></td>
                    <td>
                        <strong style="color: var(--exp-text); font-size: 13.5px;">{{ $tx->title }}</strong>
                        @if($tx->payee && $tx->payee !== '-')
                            <div style="font-size:11.5px;color:var(--exp-text2);margin-top:2px;">Payee/Student: {{ $tx->payee }}</div>
                        @endif
                    </td>
                    <td><code style="font-size: 12px; font-weight:700;">{{ $tx->ref }}</code></td>
                    <td>{{ $tx->payment_mode }}</td>
                    <td style="text-align: right; font-weight: 800; color: {{ $tx->type === 'income' ? 'var(--exp-green)' : 'var(--exp-red)' }};">
                        {{ $tx->type === 'income' ? '+' : '-' }}₹{{ number_format($tx->amount, 2) }}
                    </td>
                </tr>
                @empty
                <tr id="emptyRow">
                    <td colspan="7" style="text-align: center; padding: 48px; color: var(--exp-text2);">
                        <i class="fas fa-folder-open" style="font-size: 40px; margin-bottom: 12px; display:block; opacity: 0.6;"></i>
                        No transactions recorded for the selected period.
                    </td>
                </tr>
                @endforelse
                <tr id="noResultsRow" style="display: none;">
                    <td colspan="7" style="text-align: center; padding: 48px; color: var(--exp-text2);">
                        <i class="fas fa-magnifying-glass" style="font-size: 40px; margin-bottom: 12px; display:block; opacity: 0.6;"></i>
                        No transactions matches your search.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Toggle custom date range view
function toggleCustomDates(value) {
    const container = document.getElementById('custom-date-container');
    if (value === 'custom') {
        container.style.display = 'flex';
    } else {
        container.style.display = 'none';
        document.getElementById('start_date').value = '';
        document.getElementById('end_date').value = '';
    }
}

// Client-side live search and tab switching
let currentTabFilter = 'all';

function switchTab(type, element) {
    currentTabFilter = type;
    
    // Toggle active class on tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    element.classList.add('active');
    
    filterLedgerTable();
}

function filterLedgerTable() {
    const searchVal = document.getElementById('ledgerSearchInput').value.toLowerCase().trim();
    const rows = document.querySelectorAll('#ledgerTable tbody tr.tx-row');
    const emptyRow = document.getElementById('emptyRow');
    const noResultsRow = document.getElementById('noResultsRow');
    
    let visibleCount = 0;
    
    rows.forEach(row => {
        const type = row.getAttribute('data-type');
        const title = row.getAttribute('data-title');
        const payee = row.getAttribute('data-payee');
        
        const matchesTab = (currentTabFilter === 'all' || type === currentTabFilter);
        const matchesSearch = (!searchVal || title.includes(searchVal) || payee.includes(searchVal));
        
        if (matchesTab && matchesSearch) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Update count label
    document.getElementById('transactions-count').innerText = visibleCount + ' records';
    
    // Toggle no results row
    if (rows.length > 0) {
        if (visibleCount === 0) {
            noResultsRow.style.display = '';
        } else {
            noResultsRow.style.display = 'none';
        }
    }
}

// Render dynamic visual charts
document.addEventListener('DOMContentLoaded', function() {
    // Check dark mode state to configure charts labels colors
    const isDark = document.body.classList.contains('dark-mode');
    const gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.05)';
    const textColor = isDark ? '#f8fafc' : '#1e293b';

    // 1. Trend chart (Last 6 Months)
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($trendMonths) !!},
            datasets: [
                {
                    label: 'Earnings / Fee Income',
                    data: {!! json_encode($trendIncome) !!},
                    backgroundColor: 'rgba(16,185,129, 0.85)',
                    borderColor: '#10b981',
                    borderWidth: 1.5,
                    borderRadius: 6,
                    order: 2
                },
                {
                    label: 'Total Expenditures',
                    data: {!! json_encode($trendExpense) !!},
                    backgroundColor: 'rgba(239,68,68, 0.85)',
                    borderColor: '#ef4444',
                    borderWidth: 1.5,
                    borderRadius: 6,
                    order: 2
                },
                {
                    label: 'Net Balance',
                    data: {!! json_encode(array_map(fn($inc, $exp) => $inc - $exp, $trendIncome, $trendExpense)) !!},
                    type: 'line',
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#1d4ed8',
                    pointHoverRadius: 7,
                    tension: 0.35,
                    fill: false,
                    order: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { color: textColor, font: { family: 'Plus Jakarta Sans', weight: '600', size: 12 } }
                },
                tooltip: {
                    padding: 12,
                    bodyFont: { family: 'Plus Jakarta Sans' },
                    titleFont: { family: 'Plus Jakarta Sans', weight: '700' }
                }
            },
            scales: {
                x: {
                    grid: { color: gridColor },
                    ticks: { color: textColor, font: { family: 'Plus Jakarta Sans', weight: '600' } }
                },
                y: {
                    grid: { color: gridColor },
                    ticks: { 
                        color: textColor, 
                        font: { family: 'Plus Jakarta Sans' },
                        callback: function(value) { return '₹' + value.toLocaleString(); }
                    }
                }
            }
        }
    });

    // 2. Expense Category breakdown Chart (Doughnut / Pie)
    @if(count($expenseCategoryBreakdown) > 0)
    const expCtx = document.getElementById('expensePieChart').getContext('2d');
    new Chart(expCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_keys($expenseCategoryBreakdown)) !!},
            datasets: [{
                data: {!! json_encode(array_values($expenseCategoryBreakdown)) !!},
                backgroundColor: [
                    '#1d4ed8', '#3b82f6', '#8b5cf6', '#10b981', 
                    '#f59e0b', '#ef4444', '#ec4899', '#14b8a6', '#64748b'
                ],
                borderWidth: isDark ? 2 : 1,
                borderColor: isDark ? '#111827' : '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: { color: textColor, font: { family: 'Plus Jakarta Sans', weight: '600', size: 11 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) label += ': ';
                            if (context.raw !== null) {
                                label += '₹' + parseFloat(context.raw).toLocaleString();
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
    @endif

    // 3. Income Mode Breakdown Chart
    @if(count($incomeModeBreakdown) > 0)
    const incCtx = document.getElementById('incomePieChart').getContext('2d');
    new Chart(incCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode(array_keys($incomeModeBreakdown)) !!},
            datasets: [{
                data: {!! json_encode(array_values($incomeModeBreakdown)) !!},
                backgroundColor: [
                    '#10b981', '#1d4ed8', '#f59e0b', '#3b82f6', '#8b5cf6'
                ],
                borderWidth: isDark ? 2 : 1,
                borderColor: isDark ? '#111827' : '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: { color: textColor, font: { family: 'Plus Jakarta Sans', weight: '600', size: 11 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) label += ': ';
                            if (context.raw !== null) {
                                label += '₹' + parseFloat(context.raw).toLocaleString();
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
    @endif
});
</script>
@endsection
