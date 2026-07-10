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
                    <th><i class="fas fa-phone" style="color:#7c3aed;"></i> Phone No.</th>
                    <th><i class="fas fa-user-tie" style="color:#7c3aed;"></i> Father Name</th>
                    <th><i class="fas fa-envelope" style="color:#7c3aed;"></i> Email</th>
                    <th>Students</th>
                    <th>Classes</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siblingGroups as $i => $group)
                @php
                    $firstStudent = $group->first();
                    $fatherName = $group->pluck('father_name')->filter()->first() ?? '—';
                    $guardianPhone = $firstStudent->guardian_phone ?? '—';
                    $guardianEmail = $firstStudent->guardian_email ?? $firstStudent->father_email ?? '—';
                @endphp
                <tr>
                    <td style="font-weight:700; color:#7c3aed;">{{ $i + 1 }}</td>
                    <td>
                        <span style="font-family:monospace; font-weight:700; color:#7c3aed; font-size:13px;">
                            {{ $guardianPhone }}
                        </span>
                    </td>
                    <td style="font-weight:600; color:var(--sr-text);">{{ $fatherName }}</td>
                    <td>
                        @if($guardianEmail !== '—')
                        <a href="mailto:{{ $guardianEmail }}" style="color:#7c3aed; font-size:12px; word-break:break-all;">{{ $guardianEmail }}</a>
                        @else
                        <span style="color:var(--sr-text2);">—</span>
                        @endif
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
                    <td>
                        <span class="sr-badge sr-badge-info">{{ $group->count() }} children</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
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
    // Build CSV with separate columns: #, Phone No., Father Name, Email, Student Name, Admission No, Class/Section
    const siblingGroups = @json($siblingGroups->map(function($group) {
        $first = $group->first();
        return [
            'phone'       => $first->guardian_phone ?? '',
            'father_name' => $group->pluck('father_name')->filter()->first() ?? '',
            'email'       => $first->guardian_email ?? $first->father_email ?? '',
            'students'    => $group->map(fn($s) => [
                'name'    => $s->first_name . ' ' . $s->last_name,
                'admno'   => $s->admission_number,
                'class'   => optional($s->class)->name . ' / ' . optional($s->section)->name,
                'gender'  => $s->gender,
                'status'  => $s->is_active ? 'Active' : 'Inactive',
            ])->values()
        ];
    })->values());

    const headers = ['#', 'Phone Number', 'Father Name', 'Email', 'Student Name', 'Admission No.', 'Class & Section', 'Gender', 'Status'];
    let rows = [headers.map(h => '"' + h + '"').join(',')];

    siblingGroups.forEach((group, gi) => {
        group.students.forEach((student, si) => {
            const row = [
                si === 0 ? (gi + 1) : '',
                si === 0 ? group.phone    : '',
                si === 0 ? group.father_name : '',
                si === 0 ? group.email    : '',
                student.name,
                student.admno,
                student.class,
                student.gender,
                student.status,
            ];
            rows.push(row.map(v => '"' + String(v ?? '').replace(/"/g, '""') + '"').join(','));
        });
    });

    const blob = new Blob([rows.join('\n')], { type: 'text/csv' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'sibling_report_{{ now()->format("Y-m-d") }}.csv';
    a.click();
}
</script>
@endsection
