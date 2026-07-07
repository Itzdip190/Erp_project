@extends('layouts.app')
@section('title', 'Sibling Report')
@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@include('school.reports._styles')
@endsection

@section('content')
@include('school.reports._header', [
    'title'    => 'Sibling Report',
    'icon'     => 'fa-people-group',
    'subtitle' => 'Families with multiple enrolled students — for discount & communication',
    'gradient' => 'linear-gradient(135deg, #4a1772 0%, #7c3aed 55%, #a78bfa 100%)'
])

<div style="max-width:1200px; margin:0 auto; padding:0 4px;">

{{-- FILTERS --}}
<div class="sr-filter-card no-print">
    <form method="GET" action="{{ route('school.reports.siblings') }}">
        <div class="sr-filter-row">
            <div class="sr-filter-group">
                <label class="sr-filter-label">Class</label>
                <select name="class_id" class="sr-filter-input">
                    <option value="">All Classes</option>
                    @foreach($classes as $cls)
                        <option value="{{ $cls->id }}" {{ $classId == $cls->id ? 'selected' : '' }}>{{ $cls->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="sr-filter-btn"><i class="fas fa-filter"></i> Apply</button>
            </div>
        </div>
    </form>
</div>

{{-- STAT CARDS --}}
<div class="sr-stats" style="grid-template-columns:repeat(3,1fr); margin-bottom:24px;">
    <div class="sr-stat-card">
        <div class="sr-stat-icon" style="background:#ede9fe;"><i class="fas fa-house-chimney-user" style="color:#7c3aed;"></i></div>
        <div>
            <div class="sr-stat-label">Sibling Families</div>
            <div class="sr-stat-value" style="color:#7c3aed;">{{ $totalFamilies }}</div>
        </div>
    </div>
    <div class="sr-stat-card">
        <div class="sr-stat-icon" style="background:#dbeafe;"><i class="fas fa-users" style="color:#1d4ed8;"></i></div>
        <div>
            <div class="sr-stat-label">Students in Families</div>
            <div class="sr-stat-value" style="color:#1d4ed8;">{{ $totalSiblingStudents }}</div>
        </div>
    </div>
    <div class="sr-stat-card">
        <div class="sr-stat-icon" style="background:#d1fae5;"><i class="fas fa-tags" style="color:#059669;"></i></div>
        <div>
            <div class="sr-stat-label">Avg Siblings / Family</div>
            <div class="sr-stat-value" style="color:#059669;">
                {{ $totalFamilies > 0 ? round($totalSiblingStudents / $totalFamilies, 1) : 0 }}
            </div>
        </div>
    </div>
</div>

{{-- CHART --}}
<div class="sr-chart-card" style="margin-bottom:24px;">
    <div class="sr-chart-hdr"><i class="fas fa-chart-pie" style="color:#7c3aed;"></i> Family Size Distribution</div>
    <div class="sr-chart-body" style="height:250px; position:relative;">
        <canvas id="sizeChart"></canvas>
    </div>
</div>

{{-- SIBLING GROUPS TABLE --}}
<div class="sr-table-card" id="reportTable">
    <div class="sr-table-header">
        <div class="sr-table-title"><i class="fas fa-people-group" style="color:#7c3aed;"></i> Sibling Groups</div>
        <span class="sr-table-meta">{{ $totalFamilies }} families</span>
    </div>
    <div style="overflow-x:auto;">
        <table class="sr-table" id="mainDataTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Guardian Contact</th>
                    <th>Children</th>
                    <th>Students</th>
                    <th>Classes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siblingGroups as $i => $group)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-family:monospace; font-weight:700; color:#7c3aed;">{{ $group->first()->guardian_phone }}</td>
                    <td>
                        <span class="sr-badge sr-badge-info">{{ $group->count() }} children</span>
                    </td>
                    <td>
                        @foreach($group as $student)
                        <div style="font-size:13px; font-weight:600; color:var(--sr-text);">
                            {{ $student->first_name }} {{ $student->last_name }}
                            <span style="font-size:11px; color:var(--sr-text2);">({{ $student->admission_number }})</span>
                        </div>
                        @endforeach
                    </td>
                    <td>
                        @foreach($group as $student)
                        <div style="font-size:12px; color:var(--sr-text2);">{{ optional($student->class)->name ?? '—' }} / {{ optional($student->section)->name ?? '—' }}</div>
                        @endforeach
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="sr-empty">
                            <i class="fas fa-people-group"></i>
                            <p>No sibling groups found. Students sharing the same guardian phone are grouped as siblings.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</div>
@endsection

@section('scripts')
<script>
const sizeData = @json($sizeDistribution);
new Chart(document.getElementById('sizeChart'), {
    type: 'pie',
    data: {
        labels: Object.keys(sizeData).length ? Object.keys(sizeData) : ['No Data'],
        datasets: [{
            data: Object.values(sizeData).length ? Object.values(sizeData) : [1],
            backgroundColor: ['#7c3aed','#3b82f6','#10b981','#f59e0b','#ef4444'],
            borderWidth: 3, borderColor: '#fff'
        }]
    },
    options: {
        plugins: { legend: { position:'right' } },
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
    a.download = 'sibling_report_{{ now()->format("Y-m-d") }}.csv';
    a.click();
}
</script>
@endsection
