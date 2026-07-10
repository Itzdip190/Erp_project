@extends('layouts.app')

@section('title', 'All Reports')

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
:root {
    --rp-primary:   #4f46e5;
    --rp-dark:      #312e81;
    --rp-light:     #eef2ff;
    --rp-text:      #1e1b4b;
    --rp-text2:     #6b7280;
    --rp-border:    #e0e7ff;
    --rp-white:     #ffffff;
    --rp-shadow-sm: 0 2px 8px rgba(79,70,229,.08);
    --rp-shadow:    0 6px 24px rgba(79,70,229,.13);
    --rp-shadow-lg: 0 16px 48px rgba(79,70,229,.18);
}
body.dark-mode {
    --rp-white:   #111827;
    --rp-light:   #1e1b4b22;
    --rp-border:  #312e8144;
    --rp-text:    #f8fafc;
    --rp-text2:   #94a3b8;
}

/* ── HERO ── */
.rp-hero {
    background: linear-gradient(135deg, #1e1b4b 0%, #4f46e5 55%, #818cf8 100%);
    border-radius: 24px;
    padding: 38px 40px;
    margin-bottom: 32px;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    box-shadow: 0 16px 48px rgba(79,70,229,.45);
}
.rp-hero::before {
    content:'';
    position:absolute; top:-100px; right:-100px;
    width:320px; height:320px;
    background:rgba(255,255,255,.06);
    border-radius:50%;
}
.rp-hero::after {
    content:'';
    position:absolute; bottom:-80px; right:220px;
    width:200px; height:200px;
    background:rgba(255,255,255,.04);
    border-radius:50%;
}
.rp-hero-left { position:relative; z-index:1; }
.rp-hero-title {
    font-size:28px; font-weight:800; color:#fff;
    letter-spacing:-.5px; margin:0 0 6px;
    display:flex; align-items:center; gap:12px;
}
.rp-hero-title i { opacity:.9; }
.rp-hero-subtitle { color:rgba(255,255,255,.75); font-size:14px; font-weight:500; margin:0; }
.rp-hero-right { position:relative; z-index:1; text-align:right; }
.rp-hero-date {
    background:rgba(255,255,255,.15);
    backdrop-filter:blur(8px);
    border:1px solid rgba(255,255,255,.25);
    border-radius:12px;
    padding:10px 20px;
    color:#fff;
    font-size:13px;
    font-weight:600;
    display:inline-flex;
    align-items:center;
    gap:8px;
}
.rp-hero-date i { opacity:.8; }

/* ── QUICK STATS ── */
.rp-stats-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
    margin-bottom: 32px;
}
.rp-stat {
    background: var(--rp-white);
    border: 1px solid var(--rp-border);
    border-radius: 16px;
    padding: 22px 24px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: var(--rp-shadow-sm);
    transition: transform .2s, box-shadow .2s;
}
.rp-stat:hover { transform:translateY(-3px); box-shadow:var(--rp-shadow); }
.rp-stat-icon {
    width:52px; height:52px;
    border-radius:14px;
    display:flex; align-items:center; justify-content:center;
    font-size:22px; flex-shrink:0;
}
.rp-stat-label { font-size:12px; font-weight:600; color:var(--rp-text2); text-transform:uppercase; letter-spacing:.6px; margin-bottom:4px; }
.rp-stat-value { font-size:24px; font-weight:800; color:var(--rp-text); line-height:1; }
.rp-stat-sub { font-size:11px; color:var(--rp-text2); margin-top:3px; }

/* ── SECTION HEADING ── */
.rp-section-hdr {
    display:flex; align-items:center; gap:12px;
    margin-bottom:20px;
}
.rp-section-hdr h2 {
    font-size:18px; font-weight:700; color:var(--rp-text); margin:0;
}
.rp-section-divider { flex:1; height:1px; background:var(--rp-border); }

/* ── REPORT CARDS GRID ── */
.rp-cards-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 22px;
    margin-bottom: 36px;
}
.rp-card {
    background: var(--rp-white);
    border: 1px solid var(--rp-border);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: var(--rp-shadow-sm);
    transition: transform .25s, box-shadow .25s;
    cursor: pointer;
    text-decoration: none;
    display: flex;
    flex-direction: column;
}
.rp-card:hover {
    transform: translateY(-6px);
    box-shadow: var(--rp-shadow-lg);
    text-decoration: none;
}
.rp-card-header {
    padding: 28px 24px 22px;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
}
.rp-card-header::after {
    content:'';
    position:absolute; top:-30px; right:-30px;
    width:100px; height:100px;
    background:rgba(255,255,255,.15);
    border-radius:50%;
}
.rp-card-icon {
    width:56px; height:56px;
    background:rgba(255,255,255,.2);
    border-radius:16px;
    display:flex; align-items:center; justify-content:center;
    font-size:24px; color:#fff;
}
.rp-card-badge {
    background:rgba(255,255,255,.25);
    border:1px solid rgba(255,255,255,.4);
    border-radius:20px;
    padding:4px 12px;
    font-size:11px;
    font-weight:700;
    color:#fff;
    letter-spacing:.5px;
}
.rp-card-body { padding: 20px 24px 24px; flex:1; }
.rp-card-title { font-size:17px; font-weight:700; color:var(--rp-text); margin:0 0 8px; }
.rp-card-desc { font-size:13px; color:var(--rp-text2); line-height:1.6; margin:0 0 18px; }
.rp-card-features { display:flex; flex-wrap:wrap; gap:6px; margin-bottom:18px; }
.rp-card-feature {
    background:var(--rp-light);
    color:var(--rp-primary);
    font-size:11px;
    font-weight:600;
    border-radius:20px;
    padding:3px 10px;
}
.rp-card-footer {
    display:flex; align-items:center; justify-content:space-between;
    padding-top:16px;
    border-top: 1px solid var(--rp-border);
    margin-top:auto;
}
.rp-card-cta {
    font-size:13px;
    font-weight:700;
    color:var(--rp-primary);
    display:flex;
    align-items:center;
    gap:6px;
    transition:gap .2s;
}
.rp-card:hover .rp-card-cta { gap:10px; }
.rp-card-cta i { font-size:11px; }
.rp-card-count {
    font-size:11px;
    color:var(--rp-text2);
    font-weight:500;
}

/* Gradients for cards */
.rp-grad-student   { background: linear-gradient(135deg, #1e3a8a, #3b82f6); }
.rp-grad-attend    { background: linear-gradient(135deg, #065f46, #10b981); }
.rp-grad-fee       { background: linear-gradient(135deg, #92400e, #f59e0b); }
.rp-grad-sibling   { background: linear-gradient(135deg, #6b21a8, #a855f7); }
.rp-grad-income    { background: linear-gradient(135deg, #065f46, #34d399); }
.rp-grad-expense   { background: linear-gradient(135deg, #991b1b, #ef4444); }

/* ── FILTER CARD ── */
.rp-filter-card {
    background: var(--rp-white);
    border: 1px solid var(--rp-border);
    border-radius: 16px;
    padding: 20px 24px;
    margin-bottom: 28px;
    box-shadow: var(--rp-shadow-sm);
}
.rp-filter-row { display:flex; flex-wrap:wrap; gap:14px; align-items:flex-end; }
.rp-filter-group { display:flex; flex-direction:column; gap:5px; min-width:180px; flex:1; }
.rp-filter-label { font-size:12px; font-weight:600; color:var(--rp-text2); text-transform:uppercase; letter-spacing:.5px; }
.rp-filter-input {
    padding:9px 13px; border:1.5px solid var(--rp-border); border-radius:9px;
    font-size:13px; font-weight:500; background:rgba(79,70,229,0.03); color:var(--rp-text);
    transition:border-color .15s;
}
.rp-filter-input:focus { outline:none; border-color:var(--rp-primary); }
.rp-filter-btn {
    padding:9px 20px; border-radius:9px;
    font-size:13px; font-weight:700; cursor:pointer; border:none;
    background:linear-gradient(135deg,#4f46e5,#818cf8);
    color:#fff; display:flex; align-items:center; gap:8px;
    transition:all .2s; white-space:nowrap;
}
.rp-filter-btn:hover { transform:translateY(-1px); box-shadow:0 4px 14px rgba(79,70,229,.3); }

.rp-hero-actions { display: flex; gap: 10px; align-items: center; justify-content: flex-end; margin-top: 10px; }
.rp-btn {
    display:inline-flex; align-items:center; gap:7px;
    padding:10px 18px; border-radius:10px;
    font-size:13px; font-weight:700; cursor:pointer;
    border:none; text-decoration:none; transition:all .2s;
}
.rp-btn-white {
    background:rgba(255,255,255,.95); color:#1e293b;
}
.rp-btn-white:hover { background:#fff; transform:translateY(-1px); box-shadow:0 4px 12px rgba(0,0,0,.15); }
.rp-btn-outline {
    background:rgba(255,255,255,.15);
    border:1px solid rgba(255,255,255,.35);
    color:#fff;
    backdrop-filter:blur(4px);
}
.rp-btn-outline:hover { background:rgba(255,255,255,.25); }

/* ── RESPONSIVE ── */
@media(max-width:1100px) {
    .rp-cards-grid { grid-template-columns: repeat(2,1fr); }
    .rp-stats-row  { grid-template-columns: repeat(2,1fr); }
    .cat-card-grid { grid-template-columns: repeat(2,1fr); }
}
@media(max-width:640px) {
    .rp-cards-grid { grid-template-columns: 1fr; }
    .rp-stats-row  { grid-template-columns: 1fr; }
    .cat-card-grid { grid-template-columns: 1fr; }
    .rp-hero { flex-direction:column; text-align:center; }
    .rp-hero-actions { justify-content: center; }
}

/* ── NEW DETAILED REPORT CARDS ── */
.cat-section-title {
    font-size: 19px;
    font-weight: 800;
    color: var(--rp-text);
    margin: 32px 0 16px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}
.cat-card-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}
.cat-card {
    background: #ffffff;
    border: 1px solid var(--rp-border);
    border-radius: 16px;
    padding: 24px;
    position: relative;
    display: flex;
    flex-direction: column;
    min-height: 180px;
    box-shadow: var(--rp-shadow-sm);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    text-decoration: none !important;
}
.cat-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--rp-shadow);
    text-decoration: none !important;
}
.cat-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
}
.cat-card-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--rp-text);
    margin: 0;
}
.cat-card-icon {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}
.cat-card-desc {
    font-size: 12px;
    color: var(--rp-text2);
    line-height: 1.5;
    margin-bottom: 18px;
    flex-grow: 1;
}
.cat-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid var(--rp-border);
    padding-top: 12px;
    margin-top: auto;
}
.cat-card-link {
    font-size: 12px;
    font-weight: 700;
    color: var(--rp-primary);
    display: flex;
    align-items: center;
    gap: 4px;
}
.cat-card-arrow {
    color: var(--rp-primary);
    font-size: 12px;
    transition: transform 0.2s;
}
.cat-card:hover .cat-card-arrow {
    transform: translateX(4px);
}

body.dark-mode .cat-card {
    background: #111827 !important;
    border-color: #1e293b !important;
}
body.dark-mode .cat-card-footer {
    border-top-color: #1e293b !important;
}
</style>
@endsection

@section('content')
<div style="max-width:1200px; margin:0 auto; padding:0 4px;">

    {{-- HERO --}}
    <div class="rp-hero">
        <div class="rp-hero-left">
            <h1 class="rp-hero-title">
                <i class="fas fa-chart-pie"></i>
                All Reports
            </h1>
            <p class="rp-hero-subtitle">Comprehensive analytics & reports for your school — from students to finances</p>
        </div>
        <div class="rp-hero-right">
            <div class="rp-hero-date" style="margin-bottom: 8px;">
                <i class="fas fa-calendar-alt"></i>
                <span id="liveDate">{{ now()->format('D, d M Y') }}</span>
                &nbsp;|&nbsp;
                <span id="liveClock">{{ now()->format('h:i A') }}</span>
            </div>
            <div class="rp-hero-actions no-print">
                <button class="rp-btn rp-btn-white" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                <button class="rp-btn rp-btn-outline" onclick="downloadOverviewCSV()"><i class="fas fa-download"></i> Download CSV</button>
            </div>
        </div>
    </div>

    {{-- DATE FILTER --}}
    <div class="rp-filter-card no-print">
        <form method="GET" action="{{ route('school.reports.index') }}">
            <div class="rp-filter-row">
                <div class="rp-filter-group">
                    <label class="rp-filter-label">Filter From Date</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="rp-filter-input">
                </div>
                <div class="rp-filter-group">
                    <label class="rp-filter-label">Filter To Date</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="rp-filter-input">
                </div>
                <div>
                    <button type="submit" class="rp-filter-btn"><i class="fas fa-filter"></i> Apply Dates</button>
                </div>
            </div>
        </form>
    </div>

    {{-- QUICK STATS --}}
    <div class="rp-stats-row">
        <div class="rp-stat">
            <div class="rp-stat-icon" style="background:linear-gradient(135deg,#dbeafe,#bfdbfe);">
                <i class="fas fa-user-graduate" style="color:#1d4ed8;"></i>
            </div>
            <div>
                <div class="rp-stat-label">Active Students</div>
                <div class="rp-stat-value">{{ number_format($totalStudents) }}</div>
                <div class="rp-stat-sub">Currently enrolled</div>
            </div>
        </div>
        <div class="rp-stat">
            <div class="rp-stat-icon" style="background:linear-gradient(135deg,#fef3c7,#fde68a);">
                <i class="fas fa-file-invoice-dollar" style="color:#b45309;"></i>
            </div>
            <div>
                <div class="rp-stat-label">Fees Due</div>
                <div class="rp-stat-value">{{ number_format($totalFeesDue, 0) }}</div>
                <div class="rp-stat-sub">Outstanding balance</div>
            </div>
        </div>
        <div class="rp-stat">
            <div class="rp-stat-icon" style="background:linear-gradient(135deg,#d1fae5,#a7f3d0);">
                <i class="fas fa-arrow-trend-up" style="color:#065f46;"></i>
            </div>
            <div>
                <div class="rp-stat-label">Total Income</div>
                <div class="rp-stat-value">{{ number_format($totalIncome, 0) }}</div>
                <div class="rp-stat-sub">All-time income</div>
            </div>
        </div>
        <div class="rp-stat">
            <div class="rp-stat-icon" style="background:linear-gradient(135deg,#fee2e2,#fecaca);">
                <i class="fas fa-arrow-trend-down" style="color:#991b1b;"></i>
            </div>
            <div>
                <div class="rp-stat-label">Total Expense</div>
                <div class="rp-stat-value">{{ number_format($totalExpense, 0) }}</div>
                <div class="rp-stat-sub">All-time expenses</div>
            </div>
        </div>
    </div>

    {{-- ── SCHOOL ANALYTICS REPORTS ── --}}
    <div class="rp-section-hdr">
        <h2><i class="fas fa-folder-open" style="color:var(--rp-primary); margin-right:8px;"></i>School Analytics Reports</h2>
        <div class="rp-section-divider"></div>
        <span style="font-size:12px; font-weight:600; color:var(--rp-text2); white-space:nowrap;">6 Reports</span>
    </div>

    <div class="rp-cards-grid">

        {{-- Student Report --}}
        <a href="{{ route('school.reports.student') }}" class="rp-card">
            <div class="rp-card-header rp-grad-student">
                <div class="rp-card-icon"><i class="fas fa-user-graduate"></i></div>
                <span class="rp-card-badge">STUDENTS</span>
            </div>
            <div class="rp-card-body">
                <div class="rp-card-title">Student Report</div>
                <div class="rp-card-desc">Complete student demographics, enrolment status, class-wise distribution and monthly admission trends.</div>
                <div class="rp-card-features">
                    <span class="rp-card-feature">Gender Pie Chart</span>
                    <span class="rp-card-feature">Class-wise Bar</span>
                    <span class="rp-card-feature">Filters</span>
                    <span class="rp-card-feature">Export</span>
                </div>
                <div class="rp-card-footer">
                    <span class="rp-card-cta">Open Report <i class="fas fa-arrow-right"></i></span>
                    <span class="rp-card-count">{{ number_format($totalStudents) }} active</span>
                </div>
            </div>
        </a>

        {{-- Attendance Report --}}
        <a href="{{ route('school.reports.attendance') }}" class="rp-card">
            <div class="rp-card-header rp-grad-attend">
                <div class="rp-card-icon"><i class="fas fa-calendar-check"></i></div>
                <span class="rp-card-badge">ATTENDANCE</span>
            </div>
            <div class="rp-card-body">
                <div class="rp-card-title">Attendance Report</div>
                <div class="rp-card-desc">Daily present/absent/late/leave statistics, 30-day trend, and class-wise attendance rate analysis.</div>
                <div class="rp-card-features">
                    <span class="rp-card-feature">Daily Trend</span>
                    <span class="rp-card-feature">Class Rate</span>
                    <span class="rp-card-feature">Donut Chart</span>
                    <span class="rp-card-feature">Date Range</span>
                </div>
                <div class="rp-card-footer">
                    <span class="rp-card-cta">Open Report <i class="fas fa-arrow-right"></i></span>
                    <span class="rp-card-count">Live data</span>
                </div>
            </div>
        </a>

        {{-- Fee Report --}}
        <a href="{{ route('school.reports.fees') }}" class="rp-card">
            <div class="rp-card-header rp-grad-fee">
                <div class="rp-card-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                <span class="rp-card-badge">FEES</span>
            </div>
            <div class="rp-card-body">
                <div class="rp-card-title">Fee Report</div>
                <div class="rp-card-desc">Fee collection, pending dues, payment mode breakdown, class-wise summary, and monthly collection trends.</div>
                <div class="rp-card-features">
                    <span class="rp-card-feature">Collection Trend</span>
                    <span class="rp-card-feature">Mode Pie Chart</span>
                    <span class="rp-card-feature">Class-wise</span>
                    <span class="rp-card-feature">Export</span>
                </div>
                <div class="rp-card-footer">
                    <span class="rp-card-cta">Open Report <i class="fas fa-arrow-right"></i></span>
                    <span class="rp-card-count" style="color:#d97706; font-weight:700;">₹{{ number_format($totalFeesDue, 0) }} due</span>
                </div>
            </div>
        </a>

        {{-- Sibling Report --}}
        <a href="{{ route('school.reports.siblings') }}" class="rp-card">
            <div class="rp-card-header rp-grad-sibling">
                <div class="rp-card-icon"><i class="fas fa-users"></i></div>
                <span class="rp-card-badge">SIBLINGS</span>
            </div>
            <div class="rp-card-body">
                <div class="rp-card-title">Sibling Report</div>
                <div class="rp-card-desc">Identify families with multiple students. Useful for sibling fee discounts and parent communication.</div>
                <div class="rp-card-features">
                    <span class="rp-card-feature">Family Groups</span>
                    <span class="rp-card-feature">Size Chart</span>
                    <span class="rp-card-feature">Class Filter</span>
                    <span class="rp-card-feature">Discount Ready</span>
                </div>
                <div class="rp-card-footer">
                    <span class="rp-card-cta">Open Report <i class="fas fa-arrow-right"></i></span>
                    <span class="rp-card-count">Family analysis</span>
                </div>
            </div>
        </a>

        {{-- Income Report --}}
        <a href="{{ route('school.reports.income') }}" class="rp-card">
            <div class="rp-card-header rp-grad-income">
                <div class="rp-card-icon"><i class="fas fa-coins"></i></div>
                <span class="rp-card-badge">INCOME</span>
            </div>
            <div class="rp-card-body">
                <div class="rp-card-title">Income Report</div>
                <div class="rp-card-desc">Non-fee income tracking by heads, payment modes, monthly trends, and status breakdown with pie charts.</div>
                <div class="rp-card-features">
                    <span class="rp-card-feature">Head Breakdown</span>
                    <span class="rp-card-feature">Monthly Trend</span>
                    <span class="rp-card-feature">Mode Chart</span>
                    <span class="rp-card-feature">Export</span>
                </div>
                <div class="rp-card-footer">
                    <span class="rp-card-cta">Open Report <i class="fas fa-arrow-right"></i></span>
                    <span class="rp-card-count" style="color:#059669; font-weight:700;">₹{{ number_format($totalIncome, 0) }} total</span>
                </div>
            </div>
        </a>

        {{-- Expense Report --}}
        <a href="{{ route('school.reports.expenses') }}" class="rp-card">
            <div class="rp-card-header rp-grad-expense">
                <div class="rp-card-icon"><i class="fas fa-receipt"></i></div>
                <span class="rp-card-badge">EXPENSES</span>
            </div>
            <div class="rp-card-body">
                <div class="rp-card-title">Expense Report</div>
                <div class="rp-card-desc">School expenditure by categories, payment status, monthly spending trends, and head-wise pie analysis.</div>
                <div class="rp-card-features">
                    <span class="rp-card-feature">Category Pie</span>
                    <span class="rp-card-feature">Trend Chart</span>
                    <span class="rp-card-feature">Status Filter</span>
                    <span class="rp-card-feature">Export</span>
                </div>
                <div class="rp-card-footer">
                    <span class="rp-card-cta">Open Report <i class="fas fa-arrow-right"></i></span>
                    <span class="rp-card-count" style="color:#ef4444; font-weight:700;">₹{{ number_format($totalExpense, 0) }} total</span>
                </div>
            </div>
        </a>

    </div>

    {{-- ── FEE & FINANCE REPORTS ── --}}
    {{-- REPORT CARDS SECTION --}}
    <div class="rp-section-hdr" style="margin-top: 12px;">
        <h2><i class="fas fa-chart-bar" style="color:var(--rp-primary); margin-right:8px;"></i>Fee & Finance Reports</h2>
        <div class="rp-section-divider"></div>
        <span style="font-size:12px; font-weight:600; color:var(--rp-text2); white-space:nowrap;">10 Reports Available</span>
    </div>

    <div class="rp-cards-grid">

        {{-- 1. Route Wise Transport --}}
        <a href="{{ route('school.reports.detail', 'route_wise_transport') }}" class="rp-card">
            <div class="rp-card-header" style="background: linear-gradient(135deg, #064e3b, #10b981);">
                <div class="rp-card-icon"><i class="fas fa-route"></i></div>
                <span class="rp-card-badge">TRANSPORT</span>
            </div>
            <div class="rp-card-body">
                <div class="rp-card-title">Route Wise Transport Report</div>
                <div class="rp-card-desc">Student-wise listing grouped by route with boarding stop, vehicle number and monthly transport details.</div>
                <div class="rp-card-features">
                    <span class="rp-card-feature">Route Grouping</span>
                    <span class="rp-card-feature">Stop Details</span>
                    <span class="rp-card-feature">Export Excel</span>
                </div>
                <div class="rp-card-footer">
                    <span class="rp-card-cta">Open Report <i class="fas fa-arrow-right"></i></span>
                    <span class="rp-card-count" id="cnt-route-transport">{{ \App\Models\Student::where('school_id', auth()->user()->school_id ?? 0)->whereNotNull('transport_route')->count() }} students</span>
                </div>
            </div>
        </a>

        {{-- 2. Transport Wise Report --}}
        <a href="{{ route('school.reports.detail', 'transport_wise_report') }}" class="rp-card">
            <div class="rp-card-header" style="background: linear-gradient(135deg, #1e3a5f, #3b82f6);">
                <div class="rp-card-icon"><i class="fas fa-bus"></i></div>
                <span class="rp-card-badge">TRANSPORT</span>
            </div>
            <div class="rp-card-body">
                <div class="rp-card-title">Transport Wise Report</div>
                <div class="rp-card-desc">Fee collection summary for transport category — shows collected and pending amounts per student.</div>
                <div class="rp-card-features">
                    <span class="rp-card-feature">Fee Summary</span>
                    <span class="rp-card-feature">Class Filter</span>
                    <span class="rp-card-feature">Export Excel</span>
                </div>
                <div class="rp-card-footer">
                    <span class="rp-card-cta">Open Report <i class="fas fa-arrow-right"></i></span>
                    <span class="rp-card-count">Live data</span>
                </div>
            </div>
        </a>

        {{-- 3. Concession & Fine Report --}}
        <a href="{{ route('school.reports.detail', 'concession_fine_report') }}" class="rp-card">
            <div class="rp-card-header" style="background: linear-gradient(135deg, #78350f, #f59e0b);">
                <div class="rp-card-icon"><i class="fas fa-balance-scale"></i></div>
                <span class="rp-card-badge">FINES</span>
            </div>
            <div class="rp-card-body">
                <div class="rp-card-title">Concession & Fine Report</div>
                <div class="rp-card-desc">Combined overview of all fines (fixed/daily) and concession schemes defined for your school.</div>
                <div class="rp-card-features">
                    <span class="rp-card-feature">Fine Types</span>
                    <span class="rp-card-feature">Concessions</span>
                    <span class="rp-card-feature">Status Filter</span>
                </div>
                <div class="rp-card-footer">
                    <span class="rp-card-cta">Open Report <i class="fas fa-arrow-right"></i></span>
                    @php $schoolId = auth()->user()->school_id ?? 0; @endphp
                    <span class="rp-card-count">{{ \Illuminate\Support\Facades\DB::table('fee_fines')->where('school_id',$schoolId)->count() }} fines · {{ \Illuminate\Support\Facades\DB::table('fee_discounts')->where('school_id',$schoolId)->count() }} discounts</span>
                </div>
            </div>
        </a>

        {{-- 4. Discount Report (Detailed) --}}
        <a href="{{ route('school.reports.detail', 'discount_report_detailed') }}" class="rp-card">
            <div class="rp-card-header" style="background: linear-gradient(135deg, #881337, #f43f5e);">
                <div class="rp-card-icon"><i class="fas fa-tag"></i></div>
                <span class="rp-card-badge">DISCOUNTS</span>
            </div>
            <div class="rp-card-body">
                <div class="rp-card-title">Discount Report</div>
                <div class="rp-card-desc">Student-level breakdown of every discount and concession scheme applied, with remarks.</div>
                <div class="rp-card-features">
                    <span class="rp-card-feature">Per Student</span>
                    <span class="rp-card-feature">Scheme Detail</span>
                    <span class="rp-card-feature">Export Excel</span>
                </div>
                <div class="rp-card-footer">
                    <span class="rp-card-cta">Open Report <i class="fas fa-arrow-right"></i></span>
                    <span class="rp-card-count">{{ \Illuminate\Support\Facades\DB::table('fee_discounts')->where('school_id',$schoolId)->count() }} schemes</span>
                </div>
            </div>
        </a>

        {{-- 5. Dues Report --}}
        <a href="{{ route('school.reports.detail', 'dues_report') }}" class="rp-card">
            <div class="rp-card-header" style="background: linear-gradient(135deg, #4c1d95, #8b5cf6);">
                <div class="rp-card-icon"><i class="fas fa-file-invoice"></i></div>
                <span class="rp-card-badge">DUES</span>
            </div>
            <div class="rp-card-body">
                <div class="rp-card-title">Dues Report</div>
                <div class="rp-card-desc">Complete list of students with outstanding fee dues. Includes due dates, amounts and payment status per fee head.</div>
                <div class="rp-card-features">
                    <span class="rp-card-feature">Due Date</span>
                    <span class="rp-card-feature">Fee Head</span>
                    <span class="rp-card-feature">Class Filter</span>
                </div>
                <div class="rp-card-footer">
                    <span class="rp-card-cta">Open Report <i class="fas fa-arrow-right"></i></span>
                    <span class="rp-card-count" style="color:#7c3aed; font-weight:700;">₹{{ number_format($totalFeesDue, 0) }} due</span>
                </div>
            </div>
        </a>

        {{-- 6. Paid Report --}}
        <a href="{{ route('school.reports.detail', 'paid_report') }}" class="rp-card">
            <div class="rp-card-header" style="background: linear-gradient(135deg, #064e3b, #059669);">
                <div class="rp-card-icon"><i class="fas fa-circle-check"></i></div>
                <span class="rp-card-badge">PAID</span>
            </div>
            <div class="rp-card-body">
                <div class="rp-card-title">Paid Fees Report</div>
                <div class="rp-card-desc">All payment receipts with mode, transaction ID and collected amounts for the selected date range.</div>
                <div class="rp-card-features">
                    <span class="rp-card-feature">Date Range</span>
                    <span class="rp-card-feature">Payment Mode</span>
                    <span class="rp-card-feature">Receipt No.</span>
                </div>
                <div class="rp-card-footer">
                    <span class="rp-card-cta">Open Report <i class="fas fa-arrow-right"></i></span>
                    <span class="rp-card-count">Live data</span>
                </div>
            </div>
        </a>

        {{-- 7. Refund Report --}}
        <a href="{{ route('school.reports.detail', 'refund_report') }}" class="rp-card">
            <div class="rp-card-header" style="background: linear-gradient(135deg, #0c4a6e, #0284c7);">
                <div class="rp-card-icon"><i class="fas fa-rotate-left"></i></div>
                <span class="rp-card-badge">REFUNDS</span>
            </div>
            <div class="rp-card-body">
                <div class="rp-card-title">Refund Report</div>
                <div class="rp-card-desc">All fee refunds processed for students with refund date, reason and amount details.</div>
                <div class="rp-card-features">
                    <span class="rp-card-feature">Date Range</span>
                    <span class="rp-card-feature">Reason</span>
                    <span class="rp-card-feature">Student Wise</span>
                </div>
                <div class="rp-card-footer">
                    <span class="rp-card-cta">Open Report <i class="fas fa-arrow-right"></i></span>
                    <span class="rp-card-count">{{ \Illuminate\Support\Facades\DB::table('fee_refunds')->where('school_id',$schoolId)->count() }} refunds</span>
                </div>
            </div>
        </a>

        {{-- Student-wise Refund Report --}}
        <a href="{{ route('school.reports.detail', 'studentwise_refund') }}" class="rp-card">
            <div class="rp-card-header" style="background: linear-gradient(135deg, #0f172a, #3b82f6);">
                <div class="rp-card-icon"><i class="fas fa-users-viewfinder"></i></div>
                <span class="rp-card-badge">REFUNDS</span>
            </div>
            <div class="rp-card-body">
                <div class="rp-card-title">Student-wise Refund Report</div>
                <div class="rp-card-desc">Overview of fee refunds grouped by student, showing refund counts and total refunded amounts.</div>
                <div class="rp-card-features">
                    <span class="rp-card-feature">Grouped by Student</span>
                    <span class="rp-card-feature">Total Refunded</span>
                    <span class="rp-card-feature">Refund Counts</span>
                </div>
                <div class="rp-card-footer">
                    <span class="rp-card-cta">Open Report <i class="fas fa-arrow-right"></i></span>
                    <span class="rp-card-count">{{ \Illuminate\Support\Facades\DB::table('fee_refunds')->where('school_id',$schoolId)->distinct('student_id')->count() }} students</span>
                </div>
            </div>
        </a>

        {{-- 8. Estimated Fees --}}
        <a href="{{ route('school.reports.detail', 'estimated_fees') }}" class="rp-card">
            <div class="rp-card-header" style="background: linear-gradient(135deg, #7c2d12, #ea580c);">
                <div class="rp-card-icon"><i class="fas fa-calculator"></i></div>
                <span class="rp-card-badge">ESTIMATED</span>
            </div>
            <div class="rp-card-body">
                <div class="rp-card-title">Estimated Fees Report</div>
                <div class="rp-card-desc">Projected fee revenue per class based on fee structures and current student enrollment numbers.</div>
                <div class="rp-card-features">
                    <span class="rp-card-feature">Per Class</span>
                    <span class="rp-card-feature">Fee Head</span>
                    <span class="rp-card-feature">Grand Total</span>
                </div>
                <div class="rp-card-footer">
                    <span class="rp-card-cta">Open Report <i class="fas fa-arrow-right"></i></span>
                    <span class="rp-card-count">{{ \Illuminate\Support\Facades\DB::table('fee_structures')->where('school_id',$schoolId)->count() }} fee heads</span>
                </div>
            </div>
        </a>

        {{-- 9. Consolidated Fees --}}
        <a href="{{ route('school.reports.detail', 'consolidated_fees') }}" class="rp-card">
            <div class="rp-card-header" style="background: linear-gradient(135deg, #831843, #db2777);">
                <div class="rp-card-icon"><i class="fas fa-layer-group"></i></div>
                <span class="rp-card-badge">CONSOLIDATED</span>
            </div>
            <div class="rp-card-body">
                <div class="rp-card-title">Consolidated Fees Report</div>
                <div class="rp-card-desc">Full student-wise ledger: total assigned fees, paid amount, dues, refunds and net balance in one view.</div>
                <div class="rp-card-features">
                    <span class="rp-card-feature">Full Ledger</span>
                    <span class="rp-card-feature">Net Balance</span>
                    <span class="rp-card-feature">All Students</span>
                </div>
                <div class="rp-card-footer">
                    <span class="rp-card-cta">Open Report <i class="fas fa-arrow-right"></i></span>
                    <span class="rp-card-count">{{ number_format($totalStudents) }} students</span>
                </div>
            </div>
        </a>

    </div>

</div>
@endsection

@section('scripts')
<script>
function updateClock() {
    const now = new Date();
    const el = document.getElementById('liveClock');
    if (el) el.textContent = now.toLocaleTimeString('en-US', { hour:'2-digit', minute:'2-digit', hour12:true });
}
updateClock();
setInterval(updateClock, 10000);

function downloadOverviewCSV() {
    let data = [
        ["Report Summary", "Value", "Description"],
        ["Active Students", "{{ $totalStudents }}", "Currently enrolled students"],
        ["Outstanding Fees Due", "₹{{ number_format($totalFeesDue, 2) }}", "Total balance outstanding"],
        ["Total Income (Filtered)", "₹{{ number_format($totalIncome, 2) }}", "From {{ $dateFrom }} to {{ $dateTo }}"],
        ["Total Expense (Filtered)", "₹{{ number_format($totalExpense, 2) }}", "From {{ $dateFrom }} to {{ $dateTo }}"]
    ];
    let csv = data.map(row => row.map(val => `"${val.toString().replace(/"/g, '""')}"`).join(",")).join("\n");
    let blob = new Blob([csv], { type: 'text/csv' });
    let a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'school_reports_overview_{{ now()->format("Y-m-d") }}.csv';
    a.click();
}
</script>
@endsection
