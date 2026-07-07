@extends('layouts.app')

@section('title', 'Cash Drawer & Counter Reconciliation')

@section('styles')
<style>
/* ─── VARIABLES & DESIGN SYSTEM ──────────────── */
:root {
    --cash-green:      #059669;
    --cash-green-dark: #047857;
    --cash-green-light:#ecfdf5;
    --cash-accent:     #10b981;
    --cash-white:      #ffffff;
    --cash-gray:       #f8fafc;
    --cash-border:     #d1fae5;
    --cash-text:       #1e293b;
    --cash-text2:      #64748b;
    --cash-red:        #ef4444;
    --cash-red-light:  #fef2f2;
    --cash-amber:      #f59e0b;
    --cash-blue:       #3b82f6;
    
    --shadow-sm: 0 1px 3px rgba(0,0,0,.05);
    --shadow-md: 0 4px 20px rgba(5, 150, 105, 0.08);
    --shadow-lg: 0 10px 30px rgba(5, 150, 105, 0.15);
}

body.dark-mode {
    --cash-white:      #111827;
    --cash-gray:       #1f2937;
    --cash-border:     #374151;
    --cash-text:       #f8fafc;
    --cash-text2:      #94a3b8;
    --cash-green-light:rgba(16, 185, 129, 0.12);
}

.cash-container {
    padding: 24px;
    width: 100%;
}

/* Hero Section */
.cash-hero {
    background: linear-gradient(135deg, #047857 0%, #059669 60%, #10b981 100%);
    border-radius: 16px;
    padding: 28px 32px;
    margin-bottom: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #fff;
    box-shadow: var(--shadow-lg);
    position: relative;
    overflow: hidden;
}
.cash-hero::after {
    content: '';
    position: absolute;
    right: -40px;
    bottom: -40px;
    width: 180px;
    height: 180px;
    background: rgba(255,255,255,0.06);
    border-radius: 50%;
}
.cash-hero-text h1 {
    font-size: 24px;
    font-weight: 800;
    margin-bottom: 4px;
    letter-spacing: -0.3px;
}
.cash-hero-text p {
    font-size: 13.5px;
    color: rgba(255, 255, 255, 0.85);
    max-width: 500px;
    line-height: 1.5;
}
.cash-hero-icon {
    font-size: 40px;
    opacity: 0.85;
    background: rgba(255,255,255,0.12);
    width: 76px; height: 76px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    border: 1px solid rgba(255,255,255,0.25);
    backdrop-filter: blur(4px);
}

/* KPI Blocks */
.cash-kpis {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 24px;
}
.cash-kpi {
    background: var(--cash-white);
    border: 1.5px solid var(--cash-border);
    border-radius: 14px;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: var(--shadow-sm);
    transition: transform 0.2s, box-shadow 0.2s;
}
.cash-kpi:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}
.kpi-details {
    display: flex;
    flex-direction: column;
}
.kpi-label {
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--cash-text2);
    letter-spacing: 0.5px;
}
.kpi-val {
    font-size: 22px;
    font-weight: 800;
    color: var(--cash-text);
    margin-top: 6px;
}
.kpi-icon-box {
    width: 44px; height: 44px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
}
.kpi-icon-box.green { background: #d1fae5; color: #065f46; }
.kpi-icon-box.red { background: #fee2e2; color: #991b1b; }
.kpi-icon-box.blue { background: #dbeafe; color: #1e40af; }

/* Content Grid */
.cash-grid {
    display: grid;
    grid-template-columns: 1.3fr 1fr;
    gap: 24px;
    align-items: start;
}

.cash-card {
    background: var(--cash-white);
    border: 1.5px solid var(--cash-border);
    border-radius: 14px;
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}
.cash-card-hdr {
    padding: 16px 20px;
    border-bottom: 1.5px solid var(--cash-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.cash-card-hdr-left {
    display: flex;
    align-items: center;
    gap: 10px;
}
.cash-card-hdr-icon {
    width: 30px; height: 30px;
    border-radius: 50%;
    background: var(--cash-green-light);
    color: var(--cash-green-dark);
    display: flex; align-items: center; justify-content: center;
    font-size: 13px;
}
.cash-card-title {
    font-size: 15px;
    font-weight: 800;
    color: var(--cash-text);
}
.cash-card-body {
    padding: 20px;
}

/* Reconciliation Calculator */
.recon-table {
    width: 100%;
    border-collapse: collapse;
}
.recon-table th {
    padding: 8px 10px;
    background: var(--cash-gray);
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--cash-text2);
    border-bottom: 1px solid var(--cash-border);
    text-align: left;
}
.recon-table td {
    padding: 8px 10px;
    border-bottom: 1px solid var(--cash-border);
    font-size: 13px;
    vertical-align: middle;
}
.note-input {
    width: 80px;
    height: 32px;
    padding: 4px 8px;
    border: 1.5px solid var(--cash-border);
    border-radius: 6px;
    font-weight: 700;
    text-align: center;
    background: var(--cash-white);
    color: var(--cash-text);
    outline: none;
}
.note-input:focus {
    border-color: var(--cash-accent);
}
.note-row-total {
    font-weight: 700;
    color: var(--cash-text);
}

.recon-summary {
    background: var(--cash-gray);
    border-radius: 10px;
    padding: 16px;
    margin-top: 16px;
    border: 1px solid var(--cash-border);
}
.recon-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    font-size: 13px;
}
.recon-row:last-child {
    margin-bottom: 0;
    padding-top: 10px;
    border-top: 1px dashed var(--cash-border);
}
.recon-lbl {
    font-weight: 600;
    color: var(--cash-text2);
}
.recon-val {
    font-weight: 700;
    color: var(--cash-text);
}

/* Variance Statuses */
.status-recon {
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    display: inline-block;
}
.status-recon.match { background: #d1fae5; color: #065f46; }
.status-recon.short { background: #fee2e2; color: #991b1b; }
.status-recon.surplus { background: #ffedd5; color: #9a3412; }

/* Filter Bar */
.filter-bar {
    display: flex;
    align-items: center;
    gap: 12px;
}
.filter-date {
    height: 32px;
    padding: 4px 8px;
    border: 1.5px solid var(--cash-border);
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    background: var(--cash-white);
    color: var(--cash-text);
    outline: none;
}

/* Table styling */
.cash-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.cash-table th {
    background: var(--cash-gray);
    color: var(--cash-text);
    font-weight: 700;
    padding: 12px 14px;
    border-bottom: 2px solid var(--cash-border);
    text-transform: uppercase;
    font-size: 11px;
    letter-spacing: 0.3px;
}
.cash-table td {
    padding: 12px 14px;
    border-bottom: 1px solid var(--cash-border);
    color: var(--cash-text);
    vertical-align: middle;
}
.cash-table tr:hover {
    background: var(--cash-green-light);
}

.badge-flow {
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
}
.badge-flow.in { background: #d1fae5; color: #047857; }
.badge-flow.out { background: #fee2e2; color: #b91c1c; }
.badge-flow.transfer { background: #dbeafe; color: #1d4ed8; }

@media(max-width: 991px) {
    .cash-kpis { grid-template-columns: 1fr; }
    .cash-grid { grid-template-columns: 1fr; }
}
</style>
@endsection

@section('content')
<div class="cash-container">
    <!-- Hero Block -->
    <div class="cash-hero">
        <div class="cash-hero-text">
            <h1>Cash Drawer Counter & Reconciliation</h1>
            <p>Monitor physical cash balances, audit daily cash flows (sales, fee receipts, expenses, transfers), and perform denomination-wise reconciliation checks.</p>
        </div>
        <div class="cash-hero-icon"><i class="fas fa-cash-register"></i></div>
    </div>

    <!-- KPI Blocks -->
    <div class="cash-kpis">
        <div class="cash-kpi">
            <div class="kpi-details">
                <span class="kpi-label">Total Cash Inflow</span>
                <span class="kpi-val">₹{{ number_format($totalCashIn, 2) }}</span>
            </div>
            <div class="kpi-icon-box green"><i class="fas fa-arrow-trend-up"></i></div>
        </div>
        <div class="cash-kpi">
            <div class="kpi-details">
                <span class="kpi-label">Total Cash Outflow</span>
                <span class="kpi-val">₹{{ number_format($totalCashOut, 2) }}</span>
            </div>
            <div class="kpi-icon-box red"><i class="fas fa-arrow-trend-down"></i></div>
        </div>
        <div class="cash-kpi" style="border-color: #34d399; background: var(--cash-green-light);">
            <div class="kpi-details">
                <span class="kpi-label" style="color: var(--cash-green-dark);">System Cash on Hand</span>
                <span class="kpi-val" style="color: var(--cash-green-dark);">₹{{ number_format($cashOnHand, 2) }}</span>
            </div>
            <div class="kpi-icon-box blue"><i class="fas fa-vault"></i></div>
        </div>
    </div>

    <!-- Grid View -->
    <div class="cash-grid">
        <!-- Left: Cash Ledger List -->
        <div class="cash-card">
            <div class="cash-card-hdr">
                <div class="cash-card-hdr-left">
                    <div class="cash-card-hdr-icon"><i class="fas fa-list-check"></i></div>
                    <span class="cash-card-title">Cash Transaction Ledger</span>
                </div>
                <div class="filter-bar">
                    <form method="GET" action="{{ route('school.income.cash-drawer') }}" style="display:flex; gap:10px;">
                        <input type="date" class="filter-date" name="start_date" value="{{ $startDate }}" onchange="this.form.submit()">
                        <input type="date" class="filter-date" name="end_date" value="{{ $endDate }}" onchange="this.form.submit()">
                    </form>
                </div>
            </div>
            <div class="cash-card-body" style="padding: 0;">
                <div style="overflow-x: auto;">
                    @if($ledger->isEmpty())
                        <div style="text-align: center; padding: 40px; color: var(--cash-text2);">
                            <i class="fas fa-file-invoice-dollar" style="font-size: 32px; opacity: 0.3; margin-bottom: 10px; display: block; color: var(--cash-green);"></i>
                            No cash transactions recorded for the selected period.
                        </div>
                    @else
                    <table class="cash-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Ledger Account / Source</th>
                                <th>Reference</th>
                                <th style="text-align: right;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ledger as $item)
                            <tr>
                                <td style="color: var(--cash-text2);">{{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}</td>
                                <td>
                                    @if($item->is_inflow === true)
                                        <span class="badge-flow in"><i class="fas fa-arrow-down-long"></i> Cash In</span>
                                    @elseif($item->is_inflow === false)
                                        <span class="badge-flow out"><i class="fas fa-arrow-up-long"></i> Cash Out</span>
                                    @else
                                        <span class="badge-flow transfer"><i class="fas fa-right-left"></i> Transfer</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-weight: 600;">{{ $item->source }}</div>
                                    <div style="font-size: 11.5px; color: var(--cash-text2);">Head: {{ $item->head }}</div>
                                </td>
                                <td style="color: var(--cash-text2); font-weight: 500;">{{ $item->ref }}</td>
                                <td style="text-align: right; font-weight: 700; color: {{ $item->is_inflow === true ? 'var(--cash-green)' : ($item->is_inflow === false ? 'var(--cash-red)' : 'var(--cash-blue)') }};">
                                    {{ $item->is_inflow === false ? '-' : '+' }}₹{{ number_format($item->amount, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right: Denomination Audit Calculator -->
        <div class="cash-card">
            <div class="cash-card-hdr">
                <div class="cash-card-hdr-left">
                    <div class="cash-card-hdr-icon"><i class="fas fa-calculator"></i></div>
                    <span class="cash-card-title">Physical Cash Auditor</span>
                </div>
                <button class="pill-btn-blue" onclick="clearCalculator()" style="padding: 4px 10px; font-size:11px;">Reset</button>
            </div>
            <div class="cash-card-body">
                <table class="recon-table">
                    <thead>
                        <tr>
                            <th>Denomination</th>
                            <th style="text-align: center;">Note Count</th>
                            <th style="text-align: right;">Total (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $denominations = [2000, 500, 200, 100, 50, 20, 10, 5, 2, 1];
                        @endphp
                        @foreach($denominations as $denom)
                        <tr>
                            <td style="font-weight: 700; color: var(--cash-text2);">₹{{ $denom }}</td>
                            <td style="text-align: center;">
                                <input type="number" class="note-input denom-qty" data-value="{{ $denom }}" min="0" value="0" oninput="calculateTotal()">
                            </td>
                            <td style="text-align: right;" class="note-row-total" id="total-{{ $denom }}">₹0.00</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="recon-summary">
                    <div class="recon-row">
                        <span class="recon-lbl">System Cash Balance:</span>
                        <span class="recon-val" id="sys-cash-val" data-cash="{{ $cashOnHand }}">₹{{ number_format($cashOnHand, 2) }}</span>
                    </div>
                    <div class="recon-row">
                        <span class="recon-lbl">Total Physical Counted:</span>
                        <span class="recon-val" style="color: var(--cash-green-dark);" id="phys-cash-val">₹0.00</span>
                    </div>
                    <div class="recon-row">
                        <span class="recon-lbl">Audited Discrepancy:</span>
                        <span class="recon-val" id="variance-val">₹{{ number_format(-$cashOnHand, 2) }}</span>
                    </div>
                    <div class="recon-row" style="justify-content: center; padding-top: 10px;">
                        <span class="status-recon short" id="audit-status">Shortage</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function calculateTotal() {
    let totalPhysical = 0;
    const inputs = document.querySelectorAll('.denom-qty');
    
    inputs.forEach(input => {
        const denomVal = parseInt(input.getAttribute('data-value'));
        const qty = parseInt(input.value) || 0;
        const rowTotal = denomVal * qty;
        
        document.getElementById('total-' + denomVal).textContent = '₹' + rowTotal.toLocaleString('en-IN', {minimumFractionDigits: 2});
        totalPhysical += rowTotal;
    });

    // Update totals
    document.getElementById('phys-cash-val').textContent = '₹' + totalPhysical.toLocaleString('en-IN', {minimumFractionDigits: 2});
    
    const systemCash = parseFloat(document.getElementById('sys-cash-val').getAttribute('data-cash'));
    const variance = totalPhysical - systemCash;
    
    const varianceEl = document.getElementById('variance-val');
    const statusEl = document.getElementById('audit-status');
    
    if (variance === 0) {
        varianceEl.textContent = '₹0.00';
        varianceEl.style.color = 'var(--cash-green-dark)';
        statusEl.textContent = 'Balanced';
        statusEl.className = 'status-recon match';
    } else if (variance > 0) {
        varianceEl.textContent = '+₹' + variance.toLocaleString('en-IN', {minimumFractionDigits: 2});
        varianceEl.style.color = 'var(--cash-accent)';
        statusEl.textContent = 'Surplus';
        statusEl.className = 'status-recon surplus';
    } else {
        varianceEl.textContent = '-₹' + Math.abs(variance).toLocaleString('en-IN', {minimumFractionDigits: 2});
        varianceEl.style.color = 'var(--cash-red)';
        statusEl.textContent = 'Shortage';
        statusEl.className = 'status-recon short';
    }
}

function clearCalculator() {
    document.querySelectorAll('.denom-qty').forEach(input => {
        input.value = 0;
    });
    calculateTotal();
}

// Run initially
calculateTotal();
</script>
@endsection
