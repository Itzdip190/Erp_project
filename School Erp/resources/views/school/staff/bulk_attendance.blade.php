@extends('layouts.app')

@section('title', 'Staff Mark Bulk Attendance')
@section('page-title', 'Staff Mark Bulk Attendance')

@section('content')
<div class="card" style="margin-bottom:20px;">
    <div class="card-hdr">
        <h3>Select Date & Department</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('school.staff.bulk-attendance') }}" class="grid-3" style="align-items:flex-end;">
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Attendance Date</label>
                <input type="date" name="date" class="form-control" value="{{ $date }}">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Department</label>
                <select name="department_id" class="form-control" required>
                    <option value="">Select Department</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ $deptId == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <button type="submit" class="btn btn-gold" style="width:100%;">Load Staff members</button>
            </div>
        </form>
    </div>
</div>

@if($deptId)
<div class="card">
    <div class="card-hdr">
        <h3>Mark Attendance for {{ $date }}</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <form method="POST" action="{{ route('school.staff.bulk-attendance.post') }}">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">

            <div class="table-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Employee ID</th>
                            <th>Staff Name</th>
                            <th style="text-align:center;">Present</th>
                            <th style="text-align:center;">Late</th>
                            <th style="text-align:center;">Absent</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staffMembers as $staff)
                        @php
                            $status = $attendance->get($staff->id)?->status ?? 'present';
                        @endphp
                        <tr>
                            <td><strong style="color:var(--gold);">{{ $staff->employee_id }}</strong></td>
                            <td><strong>{{ $staff->full_name }}</strong></td>
                            <td style="text-align:center;">
                                <input type="radio" name="attendance[{{ $staff->id }}][status]" value="present" {{ $status === 'present' ? 'checked' : '' }}>
                            </td>
                            <td style="text-align:center;">
                                <input type="radio" name="attendance[{{ $staff->id }}][status]" value="late" {{ $status === 'late' ? 'checked' : '' }}>
                            </td>
                            <td style="text-align:center;">
                                <input type="radio" name="attendance[{{ $staff->id }}][status]" value="absent" {{ $status === 'absent' ? 'checked' : '' }}>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:24px; color:var(--t2);">No active staff members found in this department.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($staffMembers->count() > 0)
            <div style="display:flex; justify-content:flex-end; padding:16px; border-top:1px solid var(--border);">
                <button type="submit" class="btn btn-primary">Save Attendance</button>
            </div>
            @endif
        </form>
    </div>
</div>
@endif
@endsection
