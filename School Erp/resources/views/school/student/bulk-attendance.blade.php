@extends('layouts.app')

@section('page-title', 'Student Bulk Attendance')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-calendar-check" style="color:var(--gold);margin-right:8px;"></i>Student Bulk Attendance</h1>
        <p>Mark daily attendance records in bulk for any Class and Section</p>
    </div>
</div>

<div class="card">
    <div class="card-hdr">
        <h3>Class & Section Filters</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('school.student-mgmt.bulk-attendance') }}">
            <div class="grid-4" style="align-items:end;">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Class</label>
                    <select name="class_id" class="form-control" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Section</label>
                    <select name="section_id" class="form-control" required>
                        <option value="">Select Section</option>
                        @foreach($sections as $s)
                            <option value="{{ $s->id }}" {{ $sectionId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Select Date</label>
                    <input type="date" name="date" class="form-control" value="{{ $date }}" required>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <button type="submit" class="btn btn-gold" style="width:100%;"><i class="fas fa-search"></i> Load Roll Call</button>
                </div>
            </div>
        </form>
    </div>
</div>

@if($classId && $sectionId)
<div class="card">
    <div class="card-hdr">
        <h3>Student Attendance Sheet for {{ $date }}</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <form method="POST" action="{{ route('school.attendance.students.store') }}">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">
            <input type="hidden" name="class_id" value="{{ $classId }}">
            <input type="hidden" name="section_id" value="{{ $sectionId }}">

            <table class="tbl">
                <thead>
                    <tr>
                        <th style="width:120px;">Admission ID</th>
                        <th>Student Name</th>
                        <th style="width:80px;">Roll No</th>
                        <th style="width:280px; text-align:center;">Mark Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $st)
                        @php
                            $status = $attendance->get($st->id)?->status ?? 'present';
                        @endphp
                        <tr>
                            <td><span class="badge badge-blue">{{ $st->admission_number }}</span></td>
                            <td style="font-weight:700;">{{ $st->full_name }}</td>
                            <td>{{ $st->roll_number }}</td>
                            <td style="text-align:center;">
                                <div style="display:flex; justify-content:center; gap:16px;">
                                    <label style="cursor:pointer; font-size:12.5px; font-weight:700; color:var(--green);">
                                        <input type="radio" name="attendance[{{ $st->id }}][status]" value="present" {{ $status == 'present' ? 'checked' : '' }}> Present
                                    </label>
                                    <label style="cursor:pointer; font-size:12.5px; font-weight:700; color:var(--red);">
                                        <input type="radio" name="attendance[{{ $st->id }}][status]" value="absent" {{ $status == 'absent' ? 'checked' : '' }}> Absent
                                    </label>
                                    <label style="cursor:pointer; font-size:12.5px; font-weight:700; color:var(--gold);">
                                        <input type="radio" name="attendance[{{ $st->id }}][status]" value="late" {{ $status == 'late' ? 'checked' : '' }}> Late
                                    </label>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center; padding:30px; color:var(--t3);">No students registered in this section.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($students->isNotEmpty())
                <div style="padding:20px; text-align:right; border-top:1px solid var(--border);">
                    <button type="submit" class="btn btn-gold"><i class="fas fa-save"></i> Save Attendance Sheet</button>
                </div>
            @endif
        </form>
    </div>
</div>
@endif
@endsection
