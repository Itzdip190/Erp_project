@extends('layouts.app')
@section('title', 'Income Report')
@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@include('school.reports._styles')
@endsection

@section('content')
@include('school.reports._header', [
    'title'    => 'Income Report',
    'icon'     => 'fa-coins',
    'subtitle' => 'Non-fee income by heads, payment modes, monthly trends and status',
    'gradient' => 'linear-gradient(135deg, #065f46 0%, #059669 55%, #6ee7b7 100%)'
])

<div style="max-width:1200px; margin:0 auto; padding:0 4px;">

{{-- FILTERS --}}
<div class="sr-filter-card no-print">
    <form method="GET" action="{{ route('school.reports.income') }}">
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
                <label class="sr-filter-label">Income Head</label>
                <select name="income_head_id" class="sr-filter-input">
                    <option value="">All Heads</option>
                    @foreach($incomeHeads as $head)
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
        <div class="sr-stat-icon" style="background:#d1fae5;"><i class="fas fa-arrow-trend-up" style="color:#059669;"></i></div>
        <div>
            <div class="sr-stat-label">Total Income</div>
            <div class="sr-stat-value" style="color:#059669; font-size:19px;">₹ {{ number_format($totalIncome, 2) }}</div>
        </div>
    </div>
    <div class="sr-stat-card">
        <div class="sr-stat-icon" style="background:#d1fae5;"><i class="fas fa-circle-check" style="color:#047857;"></i></div>
        <div>
            <div class="sr-stat-label">Paid</div>
            <div class="sr-stat-value" style="color:#047857; font-size:19px;">₹ {{ number_format($totalPaid, 2) }}</div>
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
        <div class="sr-chart-hdr"><i class="fas fa-chart-pie" style="color:#059669;"></i> Income by Head</div>
        <div class="sr-chart-body" style="height:260px; position:relative;">
            <canvas id="headChart"></canvas>
        </div>
    </div>
    <div class="sr-chart-card">
        <div class="sr-chart-hdr"><i class="fas fa-credit-card" style="color:#1d4ed8;"></i> Payment Mode Breakdown</div>
        <div class="sr-chart-body" style="height:260px; position:relative;">
            <canvas id="modeChart"></canvas>
        </div>
    </div>
</div>

<div class="sr-chart-card" style="margin-bottom:24px;">
    <div class="sr-chart-hdr"><i class="fas fa-chart-bar" style="color:#059669;"></i> Monthly Income Trend (Last 6 Months)</div>
    <div class="sr-chart-body" style="height:220px; position:relative;">
        <canvas id="trendChart"></canvas>
    </div>
</div>

{{-- TABLE --}}
<div class="sr-table-card" id="reportTable">
    <div class="sr-table-header">
        <div class="sr-table-title"><i class="fas fa-table" style="color:#059669;"></i> Income Entries</div>
        <span class="sr-table-meta">{{ $incomes->count() }} record(s)</span>
    </div>
    <div style="overflow-x:auto;">
        <table class="sr-table" id="mainDataTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Income Head</th>
                    <th>Description</th>
                    <th>Amount (₹)</th>
                    <th>Mode</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($incomes as $i => $inc)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $inc->income_date ? \Carbon\Carbon::parse($inc->income_date)->format('d M Y') : '—' }}</td>
                    <td style="font-weight:600; color:#059669;">{{ optional($inc->incomeHead)->name ?? '—' }}</td>
                    <td style="color:var(--sr-text2);">{{ $inc->description ?? '—' }}</td>
                    <td style="font-weight:700;">₹ {{ number_format($inc->amount, 2) }}</td>
                    <td>{{ $inc->payment_mode ?? '—' }}</td>
                    <td>
                        @if($inc->status === 'paid')
                            <span class="sr-badge sr-badge-success">Paid</span>
                        @elseif($inc->status === 'pending')
                            <span class="sr-badge sr-badge-warning">Pending</span>
                        @else
                            <span class="sr-badge sr-badge-danger">{{ ucfirst($inc->status) }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7"><div class="sr-empty"><i class="fas fa-coins"></i><p>No income records found.</p></div></td></tr>
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
            backgroundColor: ['#10b981','#3b82f6','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#84cc16'],
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
            label: 'Income ₹',
            data: @json($trendIncome),
            backgroundColor: 'rgba(16,185,129,.8)',
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
    a.download = 'income_report_{{ now()->format("Y-m-d") }}.csv';
    a.click();
}
</script>
@endsection
