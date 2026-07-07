@extends('layouts.app')
@section('title', 'Expense Report')
@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@include('school.reports._styles')
@endsection

@section('content')
@include('school.reports._header', [
    'title'    => 'Expense Report',
    'icon'     => 'fa-receipt',
    'subtitle' => 'School expenditure by categories, payment status and monthly spending trends',
    'gradient' => 'linear-gradient(135deg, #7f1d1d 0%, #dc2626 55%, #fca5a5 100%)'
])

<div style="max-width:1200px; margin:0 auto; padding:0 4px;">

{{-- FILTERS --}}
<div class="sr-filter-card no-print">
    <form method="GET" action="{{ route('school.reports.expenses') }}">
        <div class="sr-filter-row">
            <div class="sr-filter-group">
                <label class="sr-filter-label">From Date</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="sr-filter-input">
            </div>
            <div class="sr-filter-group">
                <label class="sr-filter-label">To Date</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="sr-filter-input">
            </div>
            <div class="sr-filter-group">
                <label class="sr-filter-label">Expense Head</label>
                <select name="expense_head_id" class="sr-filter-input">
                    <option value="">All Heads</option>
                    @foreach($expenseHeads as $head)
                        <option value="{{ $head->id }}" {{ $headId == $head->id ? 'selected' : '' }}>{{ $head->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sr-filter-group">
                <label class="sr-filter-label">Status</label>
                <select name="status" class="sr-filter-input">
                    <option value="">All</option>
                    <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div>
                <button type="submit" class="sr-filter-btn"><i class="fas fa-filter"></i> Apply</button>
            </div>
        </div>
    </form>
</div>

{{-- STAT CARDS --}}
<div class="sr-stats" style="grid-template-columns:repeat(3,1fr);">
    <div class="sr-stat-card">
        <div class="sr-stat-icon" style="background:#fee2e2;"><i class="fas fa-arrow-trend-down" style="color:#dc2626;"></i></div>
        <div>
            <div class="sr-stat-label">Total Expense</div>
            <div class="sr-stat-value" style="color:#dc2626; font-size:19px;">₹ {{ number_format($totalExpense, 2) }}</div>
        </div>
    </div>
    <div class="sr-stat-card">
        <div class="sr-stat-icon" style="background:#fee2e2;"><i class="fas fa-circle-check" style="color:#991b1b;"></i></div>
        <div>
            <div class="sr-stat-label">Paid</div>
            <div class="sr-stat-value" style="color:#991b1b; font-size:19px;">₹ {{ number_format($totalPaid, 2) }}</div>
        </div>
    </div>
    <div class="sr-stat-card">
        <div class="sr-stat-icon" style="background:#fef3c7;"><i class="fas fa-hourglass-half" style="color:#d97706;"></i></div>
        <div>
            <div class="sr-stat-label">Pending</div>
            <div class="sr-stat-value" style="color:#d97706; font-size:19px;">₹ {{ number_format($totalPending, 2) }}</div>
        </div>
    </div>
</div>

{{-- CHARTS ROW --}}
<div class="sr-grid-2" style="margin-bottom:24px; margin-top:20px;">
    <div class="sr-chart-card">
        <div class="sr-chart-hdr"><i class="fas fa-chart-pie" style="color:#dc2626;"></i> Expense by Category</div>
        <div class="sr-chart-body" style="height:260px; position:relative;">
            <canvas id="headChart"></canvas>
        </div>
    </div>
    <div class="sr-chart-card">
        <div class="sr-chart-hdr"><i class="fas fa-credit-card" style="color:#7c3aed;"></i> Payment Mode Breakdown</div>
        <div class="sr-chart-body" style="height:260px; position:relative;">
            <canvas id="modeChart"></canvas>
        </div>
    </div>
</div>

<div class="sr-chart-card" style="margin-bottom:24px;">
    <div class="sr-chart-hdr"><i class="fas fa-chart-bar" style="color:#dc2626;"></i> Monthly Expense Trend (Last 6 Months)</div>
    <div class="sr-chart-body" style="height:220px; position:relative;">
        <canvas id="trendChart"></canvas>
    </div>
</div>

{{-- TABLE --}}
<div class="sr-table-card" id="reportTable">
    <div class="sr-table-header">
        <div class="sr-table-title"><i class="fas fa-table" style="color:#dc2626;"></i> Expense Entries</div>
        <span class="sr-table-meta">{{ $expenses->count() }} record(s)</span>
    </div>
    <div style="overflow-x:auto;">
        <table class="sr-table" id="mainDataTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Expense Head</th>
                    <th>Description</th>
                    <th>Amount (₹)</th>
                    <th>Mode</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $i => $exp)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $exp->expense_date ? \Carbon\Carbon::parse($exp->expense_date)->format('d M Y') : '—' }}</td>
                    <td style="font-weight:600; color:#dc2626;">{{ optional($exp->expenseHead)->name ?? '—' }}</td>
                    <td style="color:var(--sr-text2);">{{ $exp->description ?? '—' }}</td>
                    <td style="font-weight:700;">₹ {{ number_format($exp->amount, 2) }}</td>
                    <td>{{ $exp->payment_mode ?? '—' }}</td>
                    <td>
                        @if($exp->status === 'paid')
                            <span class="sr-badge sr-badge-success">Paid</span>
                        @elseif($exp->status === 'pending')
                            <span class="sr-badge sr-badge-warning">Pending</span>
                        @else
                            <span class="sr-badge sr-badge-danger">{{ ucfirst($exp->status) }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7"><div class="sr-empty"><i class="fas fa-receipt"></i><p>No expense records found for the selected filters.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</div>
@endsection

@section('scripts')
<script>
const headData = @json($headBreakdown);
new Chart(document.getElementById('headChart'), {
    type: 'doughnut',
    data: {
        labels: Object.keys(headData).length ? Object.keys(headData) : ['No Data'],
        datasets: [{
            data: Object.values(headData).length ? Object.values(headData) : [1],
            backgroundColor: ['#ef4444','#f59e0b','#8b5cf6','#3b82f6','#10b981','#06b6d4','#84cc16'],
            borderWidth: 3, borderColor: '#fff'
        }]
    },
    options: { plugins: { legend: { position:'right' } }, maintainAspectRatio: false }
});

const modeData = @json($paymentModes);
new Chart(document.getElementById('modeChart'), {
    type: 'pie',
    data: {
        labels: Object.keys(modeData).length ? Object.keys(modeData) : ['No Data'],
        datasets: [{
            data: Object.values(modeData).length ? Object.values(modeData) : [1],
            backgroundColor: ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6'],
            borderWidth: 3, borderColor: '#fff'
        }]
    },
    options: { plugins: { legend: { position:'right' } }, maintainAspectRatio: false }
});

new Chart(document.getElementById('trendChart'), {
    type: 'bar',
    data: {
        labels: @json($trendMonths),
        datasets: [{
            label: 'Expense ₹',
            data: @json($trendExpense),
            backgroundColor: 'rgba(220,38,38,.75)',
            borderRadius: 8
        }]
    },
    options: {
        plugins: { legend: { display:false } },
        scales: { y: { beginAtZero:true, ticks: { callback: v => '₹' + v.toLocaleString() } } },
        maintainAspectRatio: false
    }
});

function downloadTableCSV() {
    const table = document.getElementById('mainDataTable');
    let csv = [];
    for (let r of table.rows) {
        let row = [];
        for (let c of r.cells) row.push('"' + c.innerText.replace(/"/g,'""').trim() + '"');
        csv.push(row.join(','));
    }
    const blob = new Blob([csv.join('\n')], { type:'text/csv' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'expense_report_{{ now()->format("Y-m-d") }}.csv';
    a.click();
}
</script>
@endsection
