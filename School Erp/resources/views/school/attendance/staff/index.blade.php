@extends('layouts.app')

@section('title', 'Mark Staff Attendance')

@section('content')
@if(session('success'))
    <div class="glass-card" style="background-color: rgba(16, 185, 129, 0.15); border-color: var(--success); color: var(--success); padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
        <i class="fa fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <h2 style="font-family: 'Syne', sans-serif;">Staff Daily Register</h2>
    <a href="{{ route('school.attendance.staff.report') }}" class="btn-accent" style="background-color: #8B5CF6;">
        <i class="fa fa-chart-bar"></i> Date Range Reports
    </a>
</div>

<!-- Parameter Select form -->
<div class="glass-card">
    <h3 style="font-family: 'Syne', sans-serif; margin-bottom: 1.5rem;">Select Department</h3>
    <form action="{{ route('school.attendance.staff.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; align-items: end;">
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
            <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Date</label>
            <input type="date" name="date" class="form-input" value="{{ $date }}" max="{{ date('Y-m-d') }}" required>
        </div>

        <div>
            <button type="submit" class="btn-accent" style="width: 100%; justify-content: center;">
                <i class="fa fa-search"></i> Retrieve Staff
            </button>
        </div>
    </form>
</div>

<!-- Bulk marking register form -->
@if($departmentId && $staffList->isNotEmpty())
    <form action="{{ route('school.attendance.staff.store') }}" method="POST" style="margin-top: 2rem;">
        @csrf
        <input type="hidden" name="date" value="{{ $date }}">
        <input type="hidden" name="department_id" value="{{ $departmentId }}">

        <div class="glass-card" style="padding: 1rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; border-bottom: 1px solid var(--border); margin-bottom: 1.5rem;">
                <h3 style="font-family: 'Syne', sans-serif;">Marking Register</h3>
                <button type="button" class="btn-accent" style="background-color: var(--success); padding: 0.5rem 1rem; font-size: 0.85rem;" onclick="$('.status-present').prop('checked', true)">
                    <i class="fa fa-check-double"></i> Set All Present
                </button>
            </div>

            <div class="table-responsive">
                <table class="custom-table" style="margin-top: 0;">
                    <thead>
                        <tr>
                            <th style="width: 20%;">Employee ID</th>
                            <th style="width: 30%;">Full Name</th>
                            <th style="width: 20%;">Designation</th>
                            <th style="text-align: center; width: 30%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staffList as $index => $staff)
                            @php
                                $attendance = $attendances->get($staff->id);
                                $currentStatus = $attendance ? $attendance->status : 'present';
                            @endphp
                            <tr>
                                <td>{{ $staff->employee_id }}</td>
                                <td style="font-weight: 700;">{{ $staff->full_name }}</td>
                                <td>{{ $staff->designation?->name }}</td>
                                <td>
                                    <input type="hidden" name="attendance[{{ $index }}][staff_id]" value="{{ $staff->id }}">
                                    
                                    <div style="display: flex; gap: 0.5rem; justify-content: center; align-items: center;">
                                        <!-- Present -->
                                        <label style="cursor: pointer; padding: 0.35rem 0.6rem; border-radius: 8px; border: 1px solid var(--border); display: inline-flex; align-items: center; gap: 4px; font-size: 0.85rem; font-weight: 700;">
                                            <input type="radio" name="attendance[{{ $index }}][status]" value="present" class="status-present" {{ $currentStatus === 'present' ? 'checked' : '' }}>
                                            <span style="color: var(--success);">P</span>
                                        </label>
                                        
                                        <!-- Late -->
                                        <label style="cursor: pointer; padding: 0.35rem 0.6rem; border-radius: 8px; border: 1px solid var(--border); display: inline-flex; align-items: center; gap: 4px; font-size: 0.85rem; font-weight: 700;">
                                            <input type="radio" name="attendance[{{ $index }}][status]" value="late" {{ $currentStatus === 'late' ? 'checked' : '' }}>
                                            <span style="color: var(--warning);">L</span>
                                        </label>

                                        <!-- Absent -->
                                        <label style="cursor: pointer; padding: 0.35rem 0.6rem; border-radius: 8px; border: 1px solid var(--border); display: inline-flex; align-items: center; gap: 4px; font-size: 0.85rem; font-weight: 700;">
                                            <input type="radio" name="attendance[{{ $index }}][status]" value="absent" {{ $currentStatus === 'absent' ? 'checked' : '' }}>
                                            <span style="color: var(--danger);">A</span>
                                        </label>

                                        <!-- Leave -->
                                        <label style="cursor: pointer; padding: 0.35rem 0.6rem; border-radius: 8px; border: 1px solid var(--border); display: inline-flex; align-items: center; gap: 4px; font-size: 0.85rem; font-weight: 700;">
                                            <input type="radio" name="attendance[{{ $index }}][status]" value="leave" {{ $currentStatus === 'leave' ? 'checked' : '' }}>
                                            <span style="color: #60A5FA;">LV</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 2rem; padding: 1rem; border-top: 1px solid var(--border); display: flex; justify-content: flex-end;">
                <button type="submit" class="btn-accent" style="padding: 1rem 3rem; font-size: 1.1rem;">
                    <i class="fa fa-save"></i> Save Staff Register
                </button>
            </div>
        </div>
    </form>
@elseif($departmentId)
    <div class="glass-card" style="margin-top: 2rem; text-align: center; color: var(--text-muted); padding: 3rem;">
        No active staff found in the selected department.
    </div>
@endif
@endsection
