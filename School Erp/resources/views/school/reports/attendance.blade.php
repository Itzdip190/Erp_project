@extends('layouts.app')
@section('title', 'Attendance Report')
@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@include('school.reports._styles')
@endsection

@section('content')
@include('school.reports._header', [
    'title'    => 'Attendance Report',
    'icon'     => 'fa-calendar-check',
    'subtitle' => 'Daily attendance trends, class-wise rates, and status breakdown',
    'gradient' => 'linear-gradient(135deg, #065f46 0%, #059669 55%, #34d399 100%)'
])

<div style="max-width:1200px; margin:0 auto; padding:0 4px;">

{{-- FILTERS --}}
<div class="sr-filter-card no-print">
    <form method="GET" action="{{ route('school.reports.attendance') }}">
        <div class="sr-filter-row">
            <div class="sr-filter-group">
                <label class="sr-filter-label">Class</label>
                <select name="class_id" class="sr-filter-input" onchange="loadSections(this.value,'sectionFilter')">
                    <option value="">All Classes</option>
                    @foreach($classes as $cls)
                        <option value="{{ $cls->id }}" {{ $classId == $cls->id ? 'selected' : '' }}>{{ $cls->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sr-filter-group">
                <label class="sr-filter-label">Section</label>
                <select name="section_id" class="sr-filter-input" id="sectionFilter">
                    <option value="">All Sections</option>
                    @foreach($sections as $sec)
                        <option value="{{ $sec->id }}" {{ $sectionId == $sec->id ? 'selected' : '' }}>{{ $sec->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sr-filter-group">
                <label class="sr-filter-label">From Date</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="sr-filter-input">
            </div>
            <div class="sr-filter-group">
                <label class="sr-filter-label">To Date</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="sr-filter-input">
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
        <div class="sr-stat-icon" style="background:#d1fae5;"><i class="fas fa-check-circle" style="color:#059669;"></i></div>
        <div>
            <div class="sr-stat-label">Present</div>
            <div class="sr-stat-value" style="color:#059669;">{{ number_format($present) }}</div>
        </div>
    </div>
    <div class="sr-stat-card">
        <div class="sr-stat-icon" style="background:#fee2e2;"><i class="fas fa-times-circle" style="color:#dc2626;"></i></div>
        <div>
            <div class="sr-stat-label">Absent</div>
            <div class="sr-stat-value" style="color:#dc2626;">{{ number_format($absent) }}</div>
        </div>
    </div>
    <div class="sr-stat-card">
        <div class="sr-stat-icon" style="background:#fef3c7;"><i class="fas fa-clock" style="color:#d97706;"></i></div>
        <div>
            <div class="sr-stat-label">Late</div>
            <div class="sr-stat-value" style="color:#d97706;">{{ number_format($late) }}</div>
        </div>
    </div>
    <div class="sr-stat-card">
        <div class="sr-stat-icon" style="background:#ede9fe;"><i class="fas fa-umbrella-beach" style="color:#7c3aed;"></i></div>
        <div>
            <div class="sr-stat-label">On Leave</div>
            <div class="sr-stat-value" style="color:#7c3aed;">{{ number_format($leave) }}</div>
        </div>
    </div>
</div>

{{-- CHARTS ROW --}}
<div class="sr-grid-2" style="margin-bottom:24px;">
    <div class="sr-chart-card">
        <div class="sr-chart-hdr"><i class="fas fa-chart-pie" style="color:#059669;"></i> Attendance Status Breakdown</div>
        <div class="sr-chart-body" style="height:260px; position:relative;">
            <canvas id="statusChart"></canvas>
        </div>
    </div>
    <div class="sr-chart-card">
        <div class="sr-chart-hdr"><i class="fas fa-percent" style="color:#1d4ed8;"></i> Class-wise Attendance Rate</div>
        <div class="sr-chart-body" style="height:260px; position:relative; overflow-x:auto;">
            <canvas id="classRateChart"></canvas>
        </div>
    </div>
</div>

<div class="sr-chart-card" style="margin-bottom:24px;">
    <div class="sr-chart-hdr"><i class="fas fa-chart-line" style="color:#059669;"></i> 30-Day Attendance Trend (Present vs Absent)</div>
    <div class="sr-chart-body" style="height:230px; position:relative; overflow-x:auto;">
        <canvas id="trendChart"></canvas>
    </div>
</div>

{{-- CLASS TABLE --}}
<div class="sr-table-card" id="reportTable">
    <div class="sr-table-header">
        <div class="sr-table-title"><i class="fas fa-table" style="color:#059669;"></i> Class-wise Summary</div>
        <span class="sr-table-meta">{{ $classAttendance->count() }} classes</span>
    </div>
    <div style="overflow-x:auto;">
        <table class="sr-table" id="mainDataTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Class</th>
                    <th>Total Records</th>
                    <th>Present</th>
                    <th>Attendance Rate</th>
                    <th>Rating</th>
                </tr>
            </thead>
            <tbody>
                @forelse($classAttendance as $i => $cls)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-weight:700; color:var(--sr-text);">{{ $cls['class'] }}</td>
                    <td>{{ number_format($cls['total']) }}</td>
                    <td>{{ number_format($cls['present']) }}</td>
                    <td>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div style="flex:1; background:#e2e8f0; border-radius:4px; height:8px;">
                                <div style="width:{{ $cls['rate'] }}%; background:{{ $cls['rate'] >= 80 ? '#10b981' : ($cls['rate'] >= 60 ? '#f59e0b' : '#ef4444') }}; height:8px; border-radius:4px;"></div>
                            </div>
                            <span style="font-weight:700; font-size:13px;">{{ $cls['rate'] }}%</span>
                        </div>
                    </td>
                    <td>
                        @if($cls['rate'] >= 80)
                            <span class="sr-badge sr-badge-success">Excellent</span>
                        @elseif($cls['rate'] >= 60)
                            <span class="sr-badge sr-badge-warning">Average</span>
                        @else
                            <span class="sr-badge sr-badge-danger">Poor</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6"><div class="sr-empty"><i class="fas fa-calendar-check"></i><p>No attendance data for the selected range.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</div>
@endsection

@section('scripts')
<script>
// Status Donut
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Present', 'Absent', 'Late', 'On Leave'],
        datasets: [{
            data: [{{ $present }}, {{ $absent }}, {{ $late }}, {{ $leave }}],
            backgroundColor: ['#10b981','#ef4444','#f59e0b','#8b5cf6'],
            borderWidth: 3, borderColor: '#fff'
        }]
    },
    options: { plugins: { legend: { position:'right' } }, maintainAspectRatio: false }
});

// Class Rate Bar
const classNames = @json($classAttendance->pluck('class'));
const classRates = @json($classAttendance->pluck('rate'));
new Chart(document.getElementById('classRateChart'), {
    type: 'bar',
    data: {
        labels: classNames,
        datasets: [{
            label: 'Attendance %',
            data: classRates,
            backgroundColor: classRates.map(r => r >= 80 ? '#10b981' : r >= 60 ? '#f59e0b' : '#ef4444'),
            borderRadius: 6
        }]
    },
    options: {
        plugins: { legend: { display:false } },
        scales: { y: { beginAtZero:true, max:100, ticks: { callback: v => v + '%' } } },
        maintainAspectRatio: false
    }
});

// Trend Line
new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: @json($trendDays),
        datasets: [
            {
                label: 'Present',
                data: @json($trendPres),
                borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,.1)',
                fill: true, tension: 0.4, pointRadius: 3
            },
            {
                label: 'Absent',
                data: @json($trendAbs),
                borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,.05)',
                fill: true, tension: 0.4, pointRadius: 3
            }
        ]
    },
    options: {
        plugins: { legend: { position:'top' } },
        scales: { y: { beginAtZero:true } },
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
    const blob = new Blob([csv.join('\n')], { type: 'text/csv' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'attendance_report_{{ now()->format("Y-m-d") }}.csv';
    a.click();
}

function loadSections(classId, targetId) {
    if (!classId) { document.getElementById(targetId).innerHTML = '<option value="">All Sections</option>'; return; }
    fetch('{{ route("school.reports.sections") }}?class_id=' + classId)
        .then(r => r.json())
        .then(data => {
            let html = '<option value="">All Sections</option>';
            data.forEach(s => html += `<option value="${s.id}">${s.name}</option>`);
            document.getElementById(targetId).innerHTML = html;
        });
}
</script>
@endsection
