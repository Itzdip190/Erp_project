@extends('layouts.app')

@section('title', 'Attendance Reports & Analytics')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2 style="font-family: 'Syne', sans-serif;">Attendance Analytics Dashboard</h2>
    <a href="{{ route('school.attendance.students.index') }}" class="btn-accent" style="background-color: #4B5563;">
        <i class="fa fa-arrow-left"></i> Back to Register
    </a>
</div>

<!-- Charts Row -->
<div style="display: flex; gap: 2rem; flex-wrap: wrap; margin-bottom: 2rem;">
    <!-- Line Chart: Monthly Trends -->
    <div class="glass-card" style="flex: 1.5; min-width: 300px;">
        <h3 style="font-family: 'Syne', sans-serif; margin-bottom: 1.5rem; color: var(--accent);">Monthly Attendance Trends</h3>
        <div style="height: 250px;">
            <canvas id="monthlyTrendChart"></canvas>
        </div>
    </div>
    
    <!-- Bar Chart: Class Performance -->
    <div class="glass-card" style="flex: 1; min-width: 250px;">
        <h3 style="font-family: 'Syne', sans-serif; margin-bottom: 1.5rem; color: var(--success);">Performance by Class</h3>
        <div style="height: 250px;">
            <canvas id="classPerformanceChart"></canvas>
        </div>
    </div>
</div>

<!-- Top Absentees List -->
<div class="glass-card">
    <h3 style="font-family: 'Syne', sans-serif; margin-bottom: 1.5rem; color: var(--danger);"><i class="fa fa-exclamation-triangle"></i> Top 10 Absentees (This Session)</h3>
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Admission ID</th>
                    <th>Student Name</th>
                    <th>Class / Section</th>
                    <th>Guardian Name</th>
                    <th>Guardian Phone</th>
                    <th style="text-align: center;">Total Absent Days</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topAbsentees as $record)
                    @if($record->student)
                        <tr>
                            <td style="font-weight: 700;">{{ $record->student->admission_number }}</td>
                            <td style="font-weight: 600;">{{ $record->student->full_name }}</td>
                            <td>{{ $record->student->class?->name }} - {{ $record->student->section?->name }}</td>
                            <td>{{ $record->student->guardian_name }}</td>
                            <td>{{ $record->student->guardian_phone }}</td>
                            <td style="text-align: center; color: var(--danger); font-weight: 700; font-size: 1.1rem;">
                                {{ $record->absent_count }}
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-muted);">No absenteeism records logged.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Monthly trend line chart
    const trendCtx = document.getElementById('monthlyTrendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: ['Jun 2025', 'Jul 2025', 'Aug 2025', 'Sep 2025', 'Oct 2025', 'Nov 2025', 'Dec 2025', 'Jan 2026', 'Feb 2026', 'Mar 2026', 'Apr 2026', 'May 2026'],
            datasets: [{
                label: 'Attendance rate (%)',
                data: [92, 94, 95, 93, 94.5, 96, 95.8, 91.5, 92.4, 95.1, 96.2, 95.5],
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { min: 80, max: 100, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#9CA3AF' } },
                x: { grid: { display: false }, ticks: { color: '#9CA3AF' } }
            }
        }
    });

    // Class performance bar chart
    const performanceCtx = document.getElementById('classPerformanceChart').getContext('2d');
    new Chart(performanceCtx, {
        type: 'bar',
        data: {
            labels: ['Class 9', 'Class 10'],
            datasets: [{
                label: 'Attendance Rate',
                data: [94.2, 96.8],
                backgroundColor: ['#10B981', '#3B82F6'],
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { min: 80, max: 100, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#9CA3AF' } },
                x: { grid: { display: false }, ticks: { color: '#9CA3AF' } }
            }
        }
    });
</script>
@endsection
