@extends('layouts.app')
@section('title', 'Fee Collection Report')
@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@include('school.reports._styles')
@endsection

@section('content')
@include('school.reports._header', [
    'title'    => 'Fee Collection Report',
    'icon'     => 'fa-file-invoice-dollar',
    'subtitle' => 'Fee collection, outstanding dues, payment modes and class-wise summary',
    'gradient' => 'linear-gradient(135deg, #78350f 0%, #d97706 55%, #fbbf24 100%)'
])

<div style="max-width:1200px; margin:0 auto; padding:0 4px;">

{{-- FILTERS --}}
<div class="sr-filter-card no-print">
    <form method="GET" action="{{ route('school.reports.fees') }}">
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
                <label class="sr-filter-label">Status</label>
                <select name="status" class="sr-filter-input">
                    <option value="">All</option>
                    <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="refunded" {{ $status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>
            <div>
                <button type="submit" class="sr-filter-btn"><i class="fas fa-filter"></i> Apply</button>
            </div>
        </div>
    </form>
</div>

{{-- STAT CARDS --}}
<div class="sr-stats" style="grid-template-columns:repeat(4,1fr);">
    <div class="sr-stat-card">
        <div class="sr-stat-icon" style="background:#d1fae5;"><i class="fas fa-circle-check" style="color:#059669;"></i></div>
        <div>
            <div class="sr-stat-label">Total Collected</div>
            <div class="sr-stat-value" style="color:#059669; font-size:18px;">₹ {{ number_format($totalCollected, 2) }}</div>
        </div>
    </div>
    <div class="sr-stat-card">
        <div class="sr-stat-icon" style="background:#fee2e2;"><i class="fas fa-hourglass-half" style="color:#dc2626;"></i></div>
        <div>
            <div class="sr-stat-label">Pending Dues</div>
            <div class="sr-stat-value" style="color:#dc2626; font-size:18px;">₹ {{ number_format($totalPending, 2) }}</div>
        </div>
    </div>
    <div class="sr-stat-card">
        <div class="sr-stat-icon" style="background:#ede9fe;"><i class="fas fa-rotate-left" style="color:#7c3aed;"></i></div>
        <div>
            <div class="sr-stat-label">Refunded</div>
            <div class="sr-stat-value" style="color:#7c3aed; font-size:18px;">₹ {{ number_format($totalRefunded, 2) }}</div>
        </div>
    </div>
    <div class="sr-stat-card">
        <div class="sr-stat-icon" style="background:#fef3c7;"><i class="fas fa-receipt" style="color:#d97706;"></i></div>
        <div>
            <div class="sr-stat-label">Receipts (Filtered)</div>
            <div class="sr-stat-value">{{ $receiptCount }}</div>
        </div>
    </div>
</div>

{{-- CHARTS ROW --}}
<div class="sr-grid-2" style="margin-bottom:24px;">
    <div class="sr-chart-card">
        <div class="sr-chart-hdr"><i class="fas fa-chart-pie" style="color:#d97706;"></i> Payment Mode Breakdown</div>
        <div class="sr-chart-body" style="height:250px; position:relative;">
            <canvas id="modeChart"></canvas>
        </div>
    </div>
    <div class="sr-chart-card">
        <div class="sr-chart-hdr"><i class="fas fa-chart-line" style="color:#1d4ed8;"></i> Monthly Collection Trend</div>
        <div class="sr-chart-body" style="height:250px; position:relative;">
            <canvas id="trendChart"></canvas>
        </div>
    </div>
</div>

{{-- CLASS-WISE FEE SUMMARY --}}
<div class="sr-table-card" style="margin-bottom:24px;">
    <div class="sr-table-header">
        <div class="sr-table-title"><i class="fas fa-school" style="color:#d97706;"></i> Class-wise Fee Summary</div>
        <span class="sr-table-meta">{{ $classFees->count() }} classes</span>
    </div>
    <div style="overflow-x:auto;">
        <table class="sr-table">
            <thead>
                <tr>
                    <th>Class</th>
                    <th>Collected (₹)</th>
                    <th>Pending (₹)</th>
                    <th>Collection Bar</th>
                </tr>
            </thead>
            <tbody>
                @foreach($classFees as $cf)
                @php $totalCF = $cf['paid'] + $cf['pending']; $pct = $totalCF > 0 ? round(($cf['paid']/$totalCF)*100) : 0; @endphp
                <tr>
                    <td style="font-weight:700;">{{ $cf['class'] }}</td>
                    <td style="color:#059669; font-weight:700;">₹ {{ number_format($cf['paid'],2) }}</td>
                    <td style="color:#dc2626; font-weight:700;">₹ {{ number_format($cf['pending'],2) }}</td>
                    <td style="min-width:180px;">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <div style="flex:1; background:#e2e8f0; border-radius:4px; height:10px;">
                                <div style="width:{{ $pct }}%; background:linear-gradient(90deg,#059669,#34d399); height:10px; border-radius:4px; transition:width .5s;"></div>
                            </div>
                            <span style="font-size:12px; font-weight:700; color:var(--sr-text2);">{{ $pct }}%</span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- RECEIPTS TABLE --}}
<div class="sr-table-card" id="reportTable">
    <div class="sr-table-header">
        <div class="sr-table-title"><i class="fas fa-file-alt" style="color:#d97706;"></i> Fee Receipts</div>
        <span class="sr-table-meta">{{ $receipts->count() }} receipt(s)</span>
    </div>
    <div style="overflow-x:auto;">
        <table class="sr-table" id="mainDataTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Receipt No.</th>
                    <th>Student</th>
                    <th>Date</th>
                    <th>Amount (₹)</th>
                    <th>Mode</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($receipts as $i => $r)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-family:monospace; font-weight:700; color:#d97706;">{{ $r->receipt_number ?? '—' }}</td>
                    <td>
                        <div style="font-weight:600;">{{ optional(optional($r->student))->first_name }} {{ optional(optional($r->student))->last_name }}</div>
                        <div style="font-size:11px; color:var(--sr-text2);">{{ optional(optional($r->student)->class)->name ?? '—' }}</div>
                    </td>
                    <td>{{ $r->payment_date ? \Carbon\Carbon::parse($r->payment_date)->format('d M Y') : '—' }}</td>
                    <td style="font-weight:700;">₹ {{ number_format($r->amount_paid, 2) }}</td>
                    <td>{{ $r->payment_mode ?? '—' }}</td>
                    <td>
                        <span class="sr-badge sr-badge-success">Paid</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7"><div class="sr-empty"><i class="fas fa-file-invoice-dollar"></i><p>No receipts found for the selected filters.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</div>
@endsection

@section('scripts')
<script>
const modeData = @json($paymentModes);
new Chart(document.getElementById('modeChart'), {
    type: 'doughnut',
    data: {
        labels: Object.keys(modeData).length ? Object.keys(modeData) : ['No Data'],
        datasets: [{
            data: Object.values(modeData).length ? Object.values(modeData) : [1],
            backgroundColor: ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4'],
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
            label: 'Collected ₹',
            data: @json($trendFees),
            backgroundColor: '#d97706',
            borderRadius: 8,
            borderSkipped: false
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
    a.download = 'fee_report_{{ now()->format("Y-m-d") }}.csv';
    a.click();
}
</script>
@endsection
