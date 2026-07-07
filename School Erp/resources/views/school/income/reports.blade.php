@extends('layouts.app')

@section('title', 'Income Reports')

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

/* ─── PAGE HEADER & HERO ──────────────────────── */
.inc-hero {
    background: linear-gradient(135deg, #047857 0%, #059669 50%, #10b981 100%);
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
.inc-hero-text h1 {
    font-size: 26px;
    font-weight: 800;
    color: #fff;
    margin-bottom: 6px;
    letter-spacing: -.3px;
}
.inc-hero-text p {
    color: rgba(255,255,255,.8);
    font-size: 13.5px;
    line-height: 1.5;
    max-width: 480px;
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
    padding: 22px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: var(--shadow-sm);
    transition: transform .22s, box-shadow .22s;
    position: relative;
    overflow: hidden;
}
.inc-stat::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--inc-green-dark), var(--inc-accent));
    border-radius: 16px 16px 0 0;
}
.inc-stat:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-md);
}
.inc-stat-icon {
    width: 52px; height: 52px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
}
.inc-stat-icon.green { background: rgba(16,185,129,.1); color: var(--inc-green); }
.inc-stat-icon.blue  { background: rgba(59,130,246,.1);  color: var(--inc-blue); }
.inc-stat-icon.amber { background: rgba(245,158,11,.1);  color: var(--inc-amber); }
.inc-stat-icon.teal  { background: rgba(20,184,166,.1);  color: #14b8a6; }

.inc-stat-val {
    font-size: 1.6rem;
    font-weight: 800;
    color: var(--inc-text);
    line-height: 1.1;
}
.inc-stat-lbl {
    font-size: 11px;
    font-weight: 700;
    color: var(--inc-text2);
    text-transform: uppercase;
    letter-spacing: .5px;
    margin-top: 4px;
}

/* ─── FILTER BAR ─────────────────────────────── */
.inc-filter-bar {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 24px;
    padding: 16px 22px;
    background: var(--inc-green-light);
    border: 1.5px solid var(--inc-border);
    border-radius: 14px;
}
.inc-filter-bar select,
.inc-filter-bar input[type="date"] {
    padding: 8px 12px;
    border: 1.5px solid var(--inc-border);
    border-radius: 9px;
    font-size: 13px;
    color: var(--inc-text);
    background: var(--inc-white);
    outline: none;
    transition: border-color .2s;
    cursor: pointer;
}
.inc-filter-bar select:focus,
.inc-filter-bar input[type="date"]:focus {
    border-color: var(--inc-green-mid);
}
.inc-filter-label {
    font-size: 13px;
    font-weight: 700;
    color: var(--inc-text);
}
.inc-btn {
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
.inc-btn-primary {
    background: linear-gradient(135deg, var(--inc-green-dark), var(--inc-green));
    color: #fff !important;
    box-shadow: 0 4px 14px rgba(16,185,129,.35);
}
.inc-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(16,185,129,.45);
}
.inc-btn-outline {
    background: var(--inc-white);
    color: var(--inc-green-mid) !important;
    border: 1.5px solid var(--inc-border);
}
.inc-btn-outline:hover {
    background: var(--inc-green-light);
    border-color: var(--inc-green-mid);
}

/* ─── CHARTS GRID ─────────────────────────────── */
.inc-charts-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 22px;
    margin-bottom: 28px;
}
.inc-card {
    background: var(--inc-white);
    border: 1.5px solid var(--inc-border);
    border-radius: 16px;
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}
.inc-card.full-row { grid-column: 1 / -1; }
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
    width: 34px; height: 34px;
    border-radius: 10px;
    background: var(--inc-green-light);
    color: var(--inc-green-mid);
    display: flex; align-items: center; justify-content: center;
    font-size: 15px;
}
.inc-card-title {
    font-size: 15px;
    font-weight: 800;
    color: var(--inc-text);
}
.inc-card-body { padding: 22px; }

.chart-wrap {
    position: relative;
    height: 200px;
}

/* ─── TRANSACTION TABLE ───────────────────────── */
.inc-table-wrap { overflow-x: auto; }
.inc-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.inc-table thead th {
    background: var(--inc-gray);
    padding: 13px 16px;
    text-align: left;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    border-bottom: 2px solid var(--inc-border);
    white-space: nowrap;
}
.inc-table tbody td {
    padding: 13px 16px;
    border-bottom: 1px solid var(--inc-border);
    vertical-align: middle;
    color: var(--inc-text);
}
.inc-table tbody tr:hover { background: var(--inc-green-light); }
.inc-table tfoot td {
    padding: 13px 16px;
    font-weight: 800;
    background: var(--inc-green-light);
    border-top: 2px solid var(--inc-border);
}

.badge-type-income  { background: #d1fae5; color: #065f46; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; }
.badge-type-fee     { background: #dbeafe; color: #1e40af; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; }
.badge-cat { background: var(--inc-green-light); color: var(--inc-green-mid); padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 600; }

/* ─── PRINT & MISC ───────────────────────────── */
.print-hide { }
@media print {
    .print-hide { display: none !important; }
    body { background: white !important; }
    .inc-card { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
}
@media (max-width: 900px) {
    .inc-charts-grid { grid-template-columns: 1fr 1fr; }
    .inc-stats { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 600px) {
    .inc-charts-grid { grid-template-columns: 1fr; }
    .inc-stats { grid-template-columns: 1fr; }
}
</style>
@endsection

@section('content')
<div style="padding: 24px;">

    {{-- HERO --}}
    <div class="inc-hero print-hide">
        <div class="inc-hero-text">
            <h1><i class="fas fa-chart-pie" style="margin-right:8px; opacity:.85;"></i>Income Reports</h1>
            <p>Comprehensive analytics on all school income streams. Combine voucher receipts and student fee collections in one unified view.</p>
        </div>
        <div class="inc-hero-icon"><i class="fas fa-coins"></i></div>
    </div>

    {{-- FILTER BAR --}}
    <form method="GET" action="{{ route('school.income.reports') }}" class="inc-filter-bar print-hide">
        <span class="inc-filter-label"><i class="fas fa-sliders"></i> Filter:</span>

        <select name="preset" id="presetSelect" onchange="toggleCustomDates(this.value); this.form.submit()">
            <option value="this_month"     {{ $preset === 'this_month'    ? 'selected' : '' }}>This Month</option>
            <option value="last_month"     {{ $preset === 'last_month'    ? 'selected' : '' }}>Last Month</option>
            <option value="this_year"      {{ $preset === 'this_year'     ? 'selected' : '' }}>This Year</option>
            <option value="academic_year"  {{ $preset === 'academic_year' ? 'selected' : '' }}>Academic Year</option>
            <option value="custom"         {{ $preset === 'custom'        ? 'selected' : '' }}>Custom Range</option>
        </select>

        <div id="customDateGroup" style="display:{{ $preset === 'custom' ? 'flex' : 'none' }}; gap: 10px; align-items: center;">
            <input type="date" name="start_date" value="{{ $startDate }}">
            <span style="font-size:13px; color:var(--inc-text2);">to</span>
            <input type="date" name="end_date" value="{{ $endDate }}">
        </div>

        <button type="submit" class="inc-btn inc-btn-primary"><i class="fas fa-arrow-right"></i> Apply</button>
        <a href="{{ route('school.income.reports') }}" class="inc-btn inc-btn-outline"><i class="fas fa-rotate-left"></i> Reset</a>
        <button type="button" class="inc-btn inc-btn-outline" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
    </form>

    {{-- REPORTING PERIOD LABEL --}}
    <div style="font-size: 13px; color: var(--inc-text2); margin-bottom: 22px; font-weight: 600;">
        <i class="fas fa-calendar-range"></i>
        Reporting Period: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} — {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
    </div>

    {{-- STAT CARDS --}}
    <div class="inc-stats">
        <div class="inc-stat">
            <div class="inc-stat-icon green"><i class="fas fa-coins"></i></div>
            <div>
                <div class="inc-stat-val">₹{{ number_format($totalIncome, 2) }}</div>
                <div class="inc-stat-lbl">Total Income</div>
            </div>
        </div>
        <div class="inc-stat">
            <div class="inc-stat-icon blue"><i class="fas fa-graduation-cap"></i></div>
            <div>
                <div class="inc-stat-val">₹{{ number_format($totalFeeIncome, 2) }}</div>
                <div class="inc-stat-lbl">Student Fee Income</div>
            </div>
        </div>
        <div class="inc-stat">
            <div class="inc-stat-icon teal"><i class="fas fa-wallet"></i></div>
            <div>
                <div class="inc-stat-val">₹{{ number_format($totalSchoolIncome, 2) }}</div>
                <div class="inc-stat-lbl">Other School Income</div>
            </div>
        </div>
        <div class="inc-stat">
            <div class="inc-stat-icon amber"><i class="fas fa-receipt"></i></div>
            <div>
                <div class="inc-stat-val">{{ $transactions->count() }}</div>
                <div class="inc-stat-lbl">Total Transactions</div>
            </div>
        </div>
    </div>

    {{-- CHARTS --}}
    <div class="inc-charts-grid">
        {{-- Chart 1: Category Breakdown (Pie) --}}
        <div class="inc-card">
            <div class="inc-card-hdr">
                <div class="inc-card-hdr-left">
                    <div class="inc-card-hdr-icon"><i class="fas fa-chart-pie"></i></div>
                    <span class="inc-card-title">By Category</span>
                </div>
            </div>
            <div class="inc-card-body">
                @if(array_sum($incomeCategoryBreakdown) == 0)
                    <div style="text-align:center;padding:40px 0;color:var(--inc-text2);">No data.</div>
                @else
                <div class="chart-wrap">
                    <canvas id="catChart"></canvas>
                </div>
                @endif
            </div>
        </div>

        {{-- Chart 2: Payment Mode (Doughnut) --}}
        <div class="inc-card">
            <div class="inc-card-hdr">
                <div class="inc-card-hdr-left">
                    <div class="inc-card-hdr-icon"><i class="fas fa-credit-card"></i></div>
                    <span class="inc-card-title">By Payment Mode</span>
                </div>
            </div>
            <div class="inc-card-body">
                @if(array_sum($incomeModeBreakdown) == 0)
                    <div style="text-align:center;padding:40px 0;color:var(--inc-text2);">No data.</div>
                @else
                <div class="chart-wrap">
                    <canvas id="modeChart"></canvas>
                </div>
                @endif
            </div>
        </div>

        {{-- Chart 3: Monthly Trend (Line) --}}
        <div class="inc-card">
            <div class="inc-card-hdr">
                <div class="inc-card-hdr-left">
                    <div class="inc-card-hdr-icon"><i class="fas fa-chart-line"></i></div>
                    <span class="inc-card-title">6-Month Trend</span>
                </div>
            </div>
            <div class="inc-card-body">
                <div class="chart-wrap">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- TRANSACTIONS TABLE --}}
    <div class="inc-card">
        <div class="inc-card-hdr">
            <div class="inc-card-hdr-left">
                <div class="inc-card-hdr-icon"><i class="fas fa-table-list"></i></div>
                <span class="inc-card-title">Income Transactions</span>
            </div>
            <div class="print-hide" style="font-size:12.5px;color:var(--inc-text2); font-weight:600;">
                {{ $transactions->count() }} record(s)
            </div>
        </div>
        <div class="inc-card-body" style="padding: 0;">
            <div class="inc-table-wrap">
                <table class="inc-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Date</th>
                            <th>Payment Mode</th>
                            <th>Reference</th>
                            <th>Payer</th>
                            <th style="text-align:right;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $i => $txn)
                        <tr>
                            <td style="color:var(--inc-text2);">{{ $i + 1 }}</td>
                            <td>
                                @if($txn->type === 'fee_income')
                                    <span class="badge-type-fee"><i class="fas fa-graduation-cap"></i> Fee</span>
                                @else
                                    <span class="badge-type-income"><i class="fas fa-coins"></i> Income</span>
                                @endif
                            </td>
                            <td style="font-weight:600; max-width:220px;">{{ $txn->title }}</td>
                            <td><span class="badge-cat">{{ $txn->category }}</span></td>
                            <td style="color:var(--inc-text2);">{{ \Carbon\Carbon::parse($txn->date)->format('d M Y') }}</td>
                            <td style="color:var(--inc-text2);">{{ $txn->payment_mode }}</td>
                            <td style="color:var(--inc-text2); font-size:11.5px;">{{ $txn->ref }}</td>
                            <td style="color:var(--inc-text2);">{{ $txn->payer }}</td>
                            <td style="text-align:right; font-weight:700; color:var(--inc-green-mid);">₹{{ number_format($txn->amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" style="text-align:center;padding:40px;color:var(--inc-text2);">
                                <i class="fas fa-circle-info" style="font-size:28px; opacity:0.4; margin-bottom:10px; display:block; color:var(--inc-green);"></i>
                                No income records for the selected period.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($transactions->count())
                    <tfoot>
                        <tr>
                            <td colspan="8" style="text-align:right; color:var(--inc-green-dark);">Grand Total Income</td>
                            <td style="text-align:right; color:var(--inc-green-dark); font-size:16px;">₹{{ number_format($totalIncome, 2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
function toggleCustomDates(val) {
    document.getElementById('customDateGroup').style.display = (val === 'custom') ? 'flex' : 'none';
}

@if(array_sum($incomeCategoryBreakdown) > 0)
// Category Pie Chart
const catCtx = document.getElementById('catChart').getContext('2d');
new Chart(catCtx, {
    type: 'pie',
    data: {
        labels: {!! json_encode(array_keys($incomeCategoryBreakdown)) !!},
        datasets: [{
            data: {!! json_encode(array_values($incomeCategoryBreakdown)) !!},
            backgroundColor: [
                '#10b981','#059669','#34d399','#6ee7b7','#047857',
                '#14b8a6','#0d9488','#2dd4bf','#5eead4','#99f6e4'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 10, boxWidth: 12 } },
            tooltip: { callbacks: { label: ctx => ctx.label + ': ₹' + ctx.parsed.toLocaleString('en-IN') }}
        }
    }
});
@endif

@if(array_sum($incomeModeBreakdown) > 0)
// Payment Mode Doughnut Chart
const modeCtx = document.getElementById('modeChart').getContext('2d');
new Chart(modeCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode(array_keys($incomeModeBreakdown)) !!},
        datasets: [{
            data: {!! json_encode(array_values($incomeModeBreakdown)) !!},
            backgroundColor: ['#3b82f6','#8b5cf6','#f59e0b','#ef4444','#10b981','#14b8a6'],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '65%',
        plugins: {
            legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 10, boxWidth: 12 } },
            tooltip: { callbacks: { label: ctx => ctx.label + ': ₹' + ctx.parsed.toLocaleString('en-IN') }}
        }
    }
});
@endif

// Monthly Trend Line Chart
const trendCtx = document.getElementById('trendChart').getContext('2d');
new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: @json($trendMonths),
        datasets: [{
            label: 'Income (₹)',
            data: @json($trendIncome),
            borderColor: '#10b981',
            backgroundColor: 'rgba(16,185,129,0.12)',
            borderWidth: 2.5,
            pointBackgroundColor: '#10b981',
            pointRadius: 5,
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => '₹' + ctx.parsed.y.toLocaleString('en-IN') }}
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: '#d1fae5' },
                ticks: { callback: v => '₹' + (v >= 1000 ? (v/1000)+'k' : v), font: { size: 10 } }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 10 } }
            }
        }
    }
});
</script>
@endsection
