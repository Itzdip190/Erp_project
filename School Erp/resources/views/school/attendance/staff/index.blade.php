@extends('layouts.app')

@section('page-title', 'Staff Attendance')

@section('content')

<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-user-clock" style="color:var(--gold);margin-right:8px;"></i>Staff Daily Register</h1>
        <p>Mark and track daily staff attendance</p>
    </div>
    <div class="page-hdr-right">
        <a href="{{ route('school.attendance.staff.report') }}" class="btn btn-primary">
            <i class="fas fa-chart-bar"></i> Date Range Reports
        </a>
    </div>
</div>

<!-- Filter Card -->
<div class="card">
    <div class="card-hdr">
        <h3><i class="fas fa-filter" style="color:var(--gold);margin-right:6px;"></i>Select Department</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('school.attendance.staff.index') }}" method="GET">
            <div class="grid-3" style="align-items:end;">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Department</label>
                    <select name="department_id" class="form-control" required>
                        <option value="">Select Department</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $departmentId == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" value="{{ $date }}" max="{{ date('Y-m-d') }}" required>
                </div>
                <div>
                    <button type="submit" class="btn btn-gold" style="width:100%;justify-content:center;">
                        <i class="fas fa-search"></i> Retrieve Staff
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@if($departmentId && $staffList->isNotEmpty())
<form action="{{ route('school.attendance.staff.store') }}" method="POST">
    @csrf
    <input type="hidden" name="date" value="{{ $date }}">
    <input type="hidden" name="department_id" value="{{ $departmentId }}">

    <div class="card">
        <div class="card-hdr">
            <h3><i class="fas fa-clipboard-list" style="color:var(--gold);margin-right:6px;"></i>Marking Register — {{ $date }}</h3>
            <button type="button" class="btn btn-success" onclick="document.querySelectorAll('.status-present').forEach(r=>r.checked=true)" style="font-size:12px;padding:6px 14px;">
                <i class="fas fa-check-double"></i> Set All Present
            </button>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Employee ID</th>
                            <th>Full Name</th>
                            <th>Designation</th>
                            <th style="text-align:center;">Attendance Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staffList as $index => $staff)
                            @php
                                $att = $attendances->get($staff->id);
                                $cs  = $att ? $att->status : 'present';
                            @endphp
                            <tr>
                                <td><span class="badge badge-blue">{{ $staff->employee_id }}</span></td>
                                <td style="font-weight:700;">{{ $staff->full_name }}</td>
                                <td><span style="color:var(--t2);font-size:12px;">{{ $staff->designation?->name ?? '—' }}</span></td>
                                <td>
                                    <input type="hidden" name="attendance[{{ $index }}][staff_id]" value="{{ $staff->id }}">
                                    <div style="display:flex;gap:6px;justify-content:center;">
                                        @foreach(['present'=>['P','var(--green)','badge-success'],'late'=>['L','var(--gold)','badge-warning'],'absent'=>['A','var(--red)','badge-danger'],'leave'=>['LV','var(--blue)','badge-blue']] as $val=>[$lbl,$clr,$bc])
                                        <label style="cursor:pointer;">
                                            <input type="radio" name="attendance[{{ $index }}][status]" value="{{ $val }}" class="status-{{ $val }}" {{ $cs===$val?'checked':'' }} style="display:none;" onchange="this.closest('td').querySelectorAll('label span').forEach(s=>s.style.opacity='.4');this.closest('label').querySelector('span').style.opacity='1';">
                                            <span class="badge {{ $bc }}" style="cursor:pointer;opacity:{{ $cs===$val?'1':'.4' }};transition:.15s;">{{ $lbl }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="padding:16px 20px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;">
                <button type="submit" class="btn btn-primary" style="padding:10px 32px;">
                    <i class="fas fa-save"></i> Save Staff Register
                </button>
            </div>
        </div>
    </div>
</form>
@elseif($departmentId)
<div class="card">
    <div class="card-body" style="text-align:center;padding:48px;color:var(--t3);">
        <i class="fas fa-users" style="font-size:36px;display:block;margin-bottom:12px;color:var(--border);"></i>
        No active staff found in the selected department.
    </div>
</div>
@endif
@endsection
