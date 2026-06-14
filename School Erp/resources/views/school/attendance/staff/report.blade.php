@extends('layouts.app')

@section('title', 'Staff Attendance Reports')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2 style="font-family: 'Syne', sans-serif;">Staff Range Register Analysis</h2>
    <a href="{{ route('school.attendance.staff.index') }}" class="btn-accent" style="background-color: #4B5563;">
        <i class="fa fa-arrow-left"></i> Back to Register
    </a>
</div>

<!-- Parameters selection filter -->
<div class="glass-card">
    <h3 style="font-family: 'Syne', sans-serif; margin-bottom: 1.5rem;">Select Filters</h3>
    <form action="{{ route('school.attendance.staff.report') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem; align-items: end;">
        <div>
            <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Department</label>
            <select name="department_id" class="form-input" required>
                <option value="">Select Department</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ $departmentId == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Start Date</label>
            <input type="date" name="start_date" class="form-input" value="{{ $startDate }}" required>
        </div>

        <div>
            <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">End Date</label>
            <input type="date" name="end_date" class="form-input" value="{{ $endDate }}" required>
        </div>

        <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn-accent" style="flex: 2; justify-content: center;">
                <i class="fa fa-sync"></i> Calculate
            </button>
            
            @if($departmentId)
                <button type="submit" name="export" value="excel" class="btn-accent" style="background-color: #10B981; flex: 1; justify-content: center;" title="Export Excel">
                    <i class="fa fa-file-excel"></i>
                </button>
            @endif
        </div>
    </form>
</div>

<!-- Table Output -->
@if($departmentId && $staffList->isNotEmpty())
    <div class="glass-card" style="margin-top: 2rem; padding: 1rem;">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Full Name</th>
                        <th>Designation</th>
                        <th style="text-align: center;">Present Days</th>
                        <th style="text-align: center;">Absent Days</th>
                        <th style="text-align: center;">Attendance Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($staffList as $staff)
                        <tr>
                            <td>{{ $staff->employee_id }}</td>
                            <td style="font-weight: 700;">{{ $staff->full_name }}</td>
                            <td>{{ $staff->designation?->name }}</td>
                            <td style="text-align: center; color: var(--success); font-weight: 600;">
                                {{ $staff->attendance_summary['present'] }}
                            </td>
                            <td style="text-align: center; color: var(--danger); font-weight: 600;">
                                {{ $staff->attendance_summary['absent'] }}
                            </td>
                            <td style="text-align: center; font-weight: 700;">
                                <span class="badge {{ $staff->attendance_summary['percentage'] >= 85 ? 'badge-success' : 'badge-danger' }}">
                                    {{ $staff->attendance_summary['percentage'] }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@elseif($departmentId)
    <div class="glass-card" style="margin-top: 2rem; text-align: center; color: var(--text-muted); padding: 3rem;">
        No active staff members found matching criteria.
    </div>
@endif
@endsection
