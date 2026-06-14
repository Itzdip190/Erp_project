@extends('layouts.app')

@section('title', 'Daily Attendance Summary')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <h2 style="font-family: 'Syne', sans-serif;">Daily Attendance Overview - {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</h2>
    <div style="display: flex; gap: 1rem;">
        <input type="date" id="reportDate" class="form-input" value="{{ $date }}" max="{{ date('Y-m-d') }}" onchange="window.location.href = '{{ route('school.attendance.students.daily') }}?date=' + this.value">
        <button class="btn-accent" style="background-color: var(--danger);" onclick="sendAbsentSms()">
            <i class="fa fa-envelope"></i> Send Absentee SMS
        </button>
    </div>
</div>

<!-- Stats Counters -->
<div class="grid-3" style="margin-bottom: 2rem;">
    <!-- Present -->
    <div class="glass-card" style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 0;">
        <div style="width: 50px; height: 50px; border-radius: 12px; background-color: rgba(16,185,129,0.1); display: flex; align-items: center; justify-content: center; color: var(--success); font-size: 1.5rem;">
            <i class="fa fa-user-check"></i>
        </div>
        <div>
            <div style="font-size: 1.8rem; font-weight: 800;">{{ $summary['present'] }}</div>
            <div style="color: var(--text-muted); font-size: 0.85rem; font-weight: 700; text-transform: uppercase;">Present Today</div>
        </div>
    </div>

    <!-- Absent -->
    <div class="glass-card" style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 0;">
        <div style="width: 50px; height: 50px; border-radius: 12px; background-color: rgba(239,68,68,0.1); display: flex; align-items: center; justify-content: center; color: var(--danger); font-size: 1.5rem;">
            <i class="fa fa-user-times"></i>
        </div>
        <div>
            <div style="font-size: 1.8rem; font-weight: 800;">{{ $summary['absent'] }}</div>
            <div style="color: var(--text-muted); font-size: 0.85rem; font-weight: 700; text-transform: uppercase;">Absent Today</div>
        </div>
    </div>

    <!-- Leaves -->
    <div class="glass-card" style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 0;">
        <div style="width: 50px; height: 50px; border-radius: 12px; background-color: rgba(59,130,246,0.1); display: flex; align-items: center; justify-content: center; color: var(--accent); font-size: 1.5rem;">
            <i class="fa fa-user-clock"></i>
        </div>
        <div>
            <div style="font-size: 1.8rem; font-weight: 800;">{{ $summary['leave'] }}</div>
            <div style="color: var(--text-muted); font-size: 0.85rem; font-weight: 700; text-transform: uppercase;">On Approved Leave</div>
        </div>
    </div>
</div>

<div style="display: flex; gap: 2rem; flex-wrap: wrap;">
    <!-- Section Breakdown Table -->
    <div class="glass-card" style="flex: 2; min-width: 320px;">
        <h3 style="font-family: 'Syne', sans-serif; margin-bottom: 1.5rem;">Classroom Breakdown</h3>
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Class</th>
                        <th>Section</th>
                        <th>Students</th>
                        <th>Present</th>
                        <th>Absent</th>
                        <th>Leave</th>
                        <th>Attendance Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData as $row)
                        <tr>
                            <td style="font-weight: 700;">{{ $row['class_name'] }}</td>
                            <td>{{ $row['section_name'] }}</td>
                            <td>{{ $row['total_students'] }}</td>
                            <td style="color: var(--success); font-weight: 600;">{{ $row['present'] }}</td>
                            <td style="color: var(--danger); font-weight: 600;">{{ $row['absent'] }}</td>
                            <td>{{ $row['leave'] }}</td>
                            <td>
                                <span class="badge {{ $row['percentage'] >= 75 ? 'badge-success' : 'badge-danger' }}">
                                    {{ $row['percentage'] }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Doughnut Chart Card -->
    <div class="glass-card" style="flex: 1; min-width: 280px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center;">
        <h3 style="font-family: 'Syne', sans-serif; margin-bottom: 1.5rem;">Ratio Representation</h3>
        <div style="width: 200px; height: 200px;">
            <canvas id="dailyRatioChart"></canvas>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Load daily present vs absent Doughnut chart
    const ctx = document.getElementById('dailyRatioChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Present', 'Absent', 'Leave'],
            datasets: [{
                data: [{{ $summary['present'] }}, {{ $summary['absent'] }}, {{ $summary['leave'] }}],
                backgroundColor: ['#10B981', '#EF4444', '#3B82F6'],
                borderColor: '#0F172A',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#F9FAFB'
                    }
                }
            }
        }
    });

    function sendAbsentSms() {
        if(confirm('Are you sure you want to dispatch bulk warning SMS notifications to guardians of all absent students?')) {
            alert('Mock SMS Notification Dispatch: 23 absent SMS messages queued in Redis imports/sms worker queue.');
        }
    }
</script>
@endsection
