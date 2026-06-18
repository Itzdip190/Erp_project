@extends('layouts.app')

@section('page-title', 'New Admission Report')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-chart-line" style="color:var(--gold);margin-right:8px;"></i>New Admission Report</h1>
        <p>Demographic analytics, monthly registration trends, and class distribution ratios</p>
    </div>
</div>

<div class="grid-3">
    <!-- Stat 1 -->
    <div class="glass-card" style="display:flex; align-items:center; gap:1rem;">
        <div style="font-size:2rem; color:var(--gold); background:var(--gold-bg); width:50px; height:50px; border-radius:50%; display:flex; align-items:center; justify-content:center;">
            <i class="fas fa-user-plus"></i>
        </div>
        <div>
            <div style="font-size:1.8rem; font-weight:800;">{{ $totalAdmitted }}</div>
            <div style="font-size:11px; text-transform:uppercase; color:var(--t2); font-weight:700;">Total New Admissions</div>
        </div>
    </div>
    
    <!-- Stat 2 -->
    <div class="glass-card" style="display:flex; align-items:center; gap:1rem;">
        <div style="font-size:2rem; color:var(--blue); background:rgba(59,130,246,0.12); width:50px; height:50px; border-radius:50%; display:flex; align-items:center; justify-content:center;">
            <i class="fas fa-mars"></i>
        </div>
        <div>
            <div style="font-size:1.8rem; font-weight:800;">{{ $maleCount }}</div>
            <div style="font-size:11px; text-transform:uppercase; color:var(--t2); font-weight:700;">Male Students Enrolled</div>
        </div>
    </div>

    <!-- Stat 3 -->
    <div class="glass-card" style="display:flex; align-items:center; gap:1rem;">
        <div style="font-size:2rem; color:var(--purple); background:rgba(139,92,246,0.12); width:50px; height:50px; border-radius:50%; display:flex; align-items:center; justify-content:center;">
            <i class="fas fa-venus"></i>
        </div>
        <div>
            <div style="font-size:1.8rem; font-weight:800;">{{ $femaleCount }}</div>
            <div style="font-size:11px; text-transform:uppercase; color:var(--t2); font-weight:700;">Female Students Enrolled</div>
        </div>
    </div>
</div>

<div class="grid-2" style="margin-top:20px;">
    <!-- Monthly Admissions Chart stub -->
    <div class="card">
        <div class="card-hdr">
            <h3>Monthly Admission Registration Trend</h3>
        </div>
        <div class="card-body">
            <canvas id="monthlyAdmissionsChart" style="max-height: 250px;"></canvas>
        </div>
    </div>

    <!-- Class-wise Distribution -->
    <div class="card">
        <div class="card-hdr">
            <h3>Distribution by Class Level</h3>
        </div>
        <div class="card-body" style="padding:0;">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Class Level</th>
                        <th>Student Count</th>
                        <th>Ratio</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classDistribution as $row)
                        @php
                            $pct = $totalAdmitted > 0 ? round(($row->count / $totalAdmitted) * 100) : 0;
                        @endphp
                        <tr>
                            <td style="font-weight:700;">{{ $row->class?->name ?? 'Class ' . $row->class_id }}</td>
                            <td>{{ $row->count }}</td>
                            <td>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <div style="flex:1; height:6px; background:var(--border); border-radius:3px; overflow:hidden;">
                                        <div style="width:{{ $pct }}%; height:100%; background:var(--gold); border-radius:3px;"></div>
                                    </div>
                                    <span style="font-size:11px; font-weight:700; color:var(--t2);">{{ $pct }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align:center; padding:30px; color:var(--t3);">No data found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    new Chart(document.getElementById('monthlyAdmissionsChart'), {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Student Registrations',
                data: [12, 19, 3, 5, 2, 24],
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245,158,11,0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
});
</script>
@endsection
