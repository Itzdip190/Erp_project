@extends('layouts.app')
@section('title', 'Student Report')
@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@include('school.reports._styles')
@endsection

@section('content')
@include('school.reports._header', [
    'title'    => 'Student Report',
    'icon'     => 'fa-user-graduate',
    'subtitle' => 'Complete student demographics, class distribution and admission trends',
    'gradient' => 'linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 55%, #3b82f6 100%)'
])

<div style="max-width:1200px; margin:0 auto; padding:0 4px;">

{{-- FILTERS --}}
<div class="sr-filter-card no-print">
    <form method="GET" action="{{ route('school.reports.student') }}">
        <div class="sr-filter-row">
            <div class="sr-filter-group">
                <label class="sr-filter-label">Class</label>
                <select name="class_id" class="sr-filter-input" id="classFilter" onchange="loadSections(this.value,'sectionFilter')">
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
                <label class="sr-filter-label">Gender</label>
                <select name="gender" class="sr-filter-input">
                    <option value="">All</option>
                    <option value="Male" {{ $gender === 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ $gender === 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Other" {{ $gender === 'Other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="sr-filter-group">
                <label class="sr-filter-label">Status</label>
                <select name="status" class="sr-filter-input">
                    <option value="">All</option>
                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="sr-filter-group">
                <label class="sr-filter-label">From</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="sr-filter-input">
            </div>
            <div class="sr-filter-group">
                <label class="sr-filter-label">To</label>
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
        <div class="sr-stat-icon" style="background:#dbeafe;"><i class="fas fa-users" style="color:#1d4ed8;"></i></div>
        <div>
            <div class="sr-stat-label">Total (Filtered)</div>
            <div class="sr-stat-value">{{ $students->count() }}</div>
        </div>
    </div>
    <div class="sr-stat-card">
        <div class="sr-stat-icon" style="background:#d1fae5;"><i class="fas fa-user-check" style="color:#065f46;"></i></div>
        <div>
            <div class="sr-stat-label">Active</div>
            <div class="sr-stat-value">{{ $totalActive }}</div>
        </div>
    </div>
    <div class="sr-stat-card">
        <div class="sr-stat-icon" style="background:#fee2e2;"><i class="fas fa-user-xmark" style="color:#991b1b;"></i></div>
        <div>
            <div class="sr-stat-label">Inactive</div>
            <div class="sr-stat-value">{{ $totalInactive }}</div>
        </div>
    </div>
    <div class="sr-stat-card">
        <div class="sr-stat-icon" style="background:#fef3c7;"><i class="fas fa-school" style="color:#92400e;"></i></div>
        <div>
            <div class="sr-stat-label">Classes</div>
            <div class="sr-stat-value">{{ $classes->count() }}</div>
        </div>
    </div>
</div>

{{-- CHARTS ROW --}}
<div class="sr-grid-2" style="margin-bottom:24px;">
    <div class="sr-chart-card">
        <div class="sr-chart-hdr"><i class="fas fa-venus-mars" style="color:#4f46e5;"></i> Gender Distribution</div>
        <div class="sr-chart-body" style="height:240px; position:relative;">
            <canvas id="genderChart"></canvas>
        </div>
    </div>
    <div class="sr-chart-card">
        <div class="sr-chart-hdr"><i class="fas fa-school" style="color:#1d4ed8;"></i> Class-wise Student Count</div>
        <div class="sr-chart-body" style="height:240px; position:relative; overflow-x:auto;">
            <canvas id="classChart"></canvas>
        </div>
    </div>
</div>

<div class="sr-chart-card" style="margin-bottom:24px;">
    <div class="sr-chart-hdr"><i class="fas fa-chart-line" style="color:#10b981;"></i> Monthly Admissions (Last 12 Months)</div>
    <div class="sr-chart-body" style="height:220px; position:relative;">
        <canvas id="admissionChart"></canvas>
    </div>
</div>

{{-- TABLE --}}
<div class="sr-table-card" id="reportTable">
    <div class="sr-table-header">
        <div class="sr-table-title"><i class="fas fa-table" style="color:#4f46e5;"></i> Student List</div>
        <span class="sr-table-meta">{{ $students->count() }} record(s)</span>
    </div>
    <div style="overflow-x:auto;">
        <table class="sr-table" id="mainDataTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Admission No.</th>
                    <th>Name</th>
                    <th>Class</th>
                    <th>Section</th>
                    <th>Gender</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th>Joining Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $i => $student)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-family:monospace; font-weight:700; color:#4f46e5;">{{ $student->admission_number }}</td>
                    <td>
                        <div style="font-weight:600; color:var(--sr-text);">{{ $student->first_name }} {{ $student->last_name }}</div>
                        <div style="font-size:11px; color:var(--sr-text2);">{{ $student->guardian_phone ?? '—' }}</div>
                    </td>
                    <td>{{ optional($student->class)->name ?? '—' }}</td>
                    <td>{{ optional($student->section)->name ?? '—' }}</td>
                    <td>{{ $student->gender ?? '—' }}</td>
                    <td>{{ $student->guardian_phone ?? '—' }}</td>
                    <td>
                        @if($student->is_active)
                            <span class="sr-badge sr-badge-success">Active</span>
                        @else
                            <span class="sr-badge sr-badge-danger">Inactive</span>
                        @endif
                    </td>
                    <td>{{ $student->created_at ? $student->created_at->format('d M Y') : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="9"><div class="sr-empty"><i class="fas fa-user-graduate"></i><p>No students match the selected filters.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</div>
@endsection

@section('scripts')
<script>
// Gender Pie Chart
const genderData = @json($genderBreakdown);
new Chart(document.getElementById('genderChart'), {
    type: 'doughnut',
    data: {
        labels: Object.keys(genderData),
        datasets: [{
            data: Object.values(genderData),
            backgroundColor: ['#3b82f6','#ec4899','#a78bfa','#f59e0b'],
            borderWidth: 3, borderColor: '#fff'
        }]
    },
    options: { plugins: { legend: { position:'right' } }, maintainAspectRatio: false }
});

// Class Bar Chart
const classData = @json($classWise);
new Chart(document.getElementById('classChart'), {
    type: 'bar',
    data: {
        labels: Object.keys(classData),
        datasets: [{
            label: 'Students',
            data: Object.values(classData),
            backgroundColor: '#4f46e5',
            borderRadius: 6
        }]
    },
    options: {
        plugins: { legend: { display:false } },
        scales: { y: { beginAtZero:true, ticks: { stepSize:1 } } },
        maintainAspectRatio: false
    }
});

// Admission Trend
const admLabels = @json($monthlyLabels);
const admData   = @json($monthlyAdmissions);
new Chart(document.getElementById('admissionChart'), {
    type: 'line',
    data: {
        labels: admLabels,
        datasets: [{
            label: 'Admissions',
            data: admData,
            borderColor: '#4f46e5',
            backgroundColor: 'rgba(79,70,229,.1)',
            fill: true, tension: 0.4,
            pointRadius: 4, pointBackgroundColor: '#4f46e5'
        }]
    },
    options: {
        plugins: { legend: { display:false } },
        scales: { y: { beginAtZero:true } },
        maintainAspectRatio: false
    }
});

// CSV Download
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
    a.download = 'student_report_{{ now()->format("Y-m-d") }}.csv';
    a.click();
}

// Lazy-load sections
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
