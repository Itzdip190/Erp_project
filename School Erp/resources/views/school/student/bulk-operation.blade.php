@extends('layouts.app')

@section('page-title', 'Bulk Student Operation')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-screwdriver-wrench" style="color:var(--gold);margin-right:8px;"></i>Bulk Student Operations</h1>
        <p>Perform batch changes to multiple student profiles, such as bulk status toggle, bulk transfers, or batch deletes</p>
    </div>
</div>

<div class="card">
    <div class="card-hdr">
        <h3>1. Select Students to Edit</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('school.student-mgmt.bulk-operation') }}">
            <div class="grid-3" style="align-items:end;">
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
                <button type="submit" class="btn btn-gold" style="width:100%;"><i class="fas fa-users-cog"></i> Load Group</button>
            </div>
        </form>
    </div>
</div>

@if($classId && $sectionId)
<div class="card" style="margin-top:20px;">
    <div class="card-hdr">
        <h3>2. Choose Operation & Execute</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('school.student-mgmt.bulk-operation') }}">
            @csrf
            
            <div style="background:var(--page); border:1px solid var(--border); border-radius:10px; padding:15px; margin-bottom:20px;">
                <h4 style="font-size:12.5px; font-weight:700; color:var(--navy); margin-bottom:12px;">Bulk Action:</h4>
                <div class="grid-3">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Batch Operation</label>
                        <select name="operation_type" class="form-control" required>
                            <option value="status_active">Toggle Status: Active Enrolled</option>
                            <option value="status_inactive">Toggle Status: Inactive / Suspended</option>
                            <option value="assign_house">Bulk Assign House Group</option>
                            <option value="delete_all">Batch Remove/Delete Student Profiles</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Execution Security Key</label>
                        <input type="password" name="security_key" class="form-control" placeholder="Enter security passphrase" required>
                    </div>
                    <div style="display:flex; align-items:flex-end;">
                        <button type="submit" class="btn btn-danger" style="width:100%; justify-content:center;" onclick="return confirm('Execute batch operation? This change is permanent.');">
                            <i class="fas fa-bolt"></i> Execute Batch Change
                        </button>
                    </div>
                </div>
            </div>

            <table class="tbl">
                <thead>
                    <tr>
                        <th style="width:50px; text-align:center;"><input type="checkbox" checked onclick="let cs = document.querySelectorAll('.sub-sel'); cs.forEach(c => c.checked = this.checked)"></th>
                        <th>Admission ID</th>
                        <th>Student Name</th>
                        <th>Roll No</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $st)
                        <tr>
                            <td style="text-align:center;"><input type="checkbox" name="student_ids[]" value="{{ $st->id }}" checked class="sub-sel"></td>
                            <td><span class="badge badge-blue">{{ $st->admission_number }}</span></td>
                            <td style="font-weight:700;">{{ $st->full_name }}</td>
                            <td>{{ $st->roll_number }}</td>
                            <td>
                                @if($st->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:30px; color:var(--t3);">No student records mapped.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </form>
    </div>
</div>
@endif
@endsection
