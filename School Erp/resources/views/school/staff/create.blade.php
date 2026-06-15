@extends('layouts.app')

@section('title', 'Add Staff')
@section('page-title', 'Add Staff')

@section('content')
<div class="card" style="max-width:900px; margin:0 auto;">
    <div class="card-hdr">
        <h3>Register New Staff Member</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('school.staff.store') }}" enctype="multipart/form-name">
            @csrf

            <h4 style="font-size:13px; font-weight:700; text-transform:uppercase; color:var(--navy); border-bottom:1px solid var(--border); padding-bottom:6px; margin-bottom:16px;">
                1. Basic Info
            </h4>
            <div class="grid-3">
                <div class="form-group">
                    <label class="form-label">Employee ID <span style="color:var(--red);">*</span></label>
                    <input type="text" name="employee_id" class="form-control" value="{{ old('employee_id') }}" required placeholder="e.g. EMP001">
                </div>
                <div class="form-group">
                    <label class="form-label">First Name <span style="color:var(--red);">*</span></label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Last Name <span style="color:var(--red);">*</span></label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
                </div>
            </div>

            <div class="grid-3">
                <div class="form-group">
                    <label class="form-label">Email <span style="color:var(--red);">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="User login email">
                </div>
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Employment Type <span style="color:var(--red);">*</span></label>
                    <select name="employment_type" class="form-control" required>
                        <option value="permanent">Permanent</option>
                        <option value="contract">Contract</option>
                        <option value="part_time">Part Time</option>
                    </select>
                </div>
            </div>

            <h4 style="font-size:13px; font-weight:700; text-transform:uppercase; color:var(--navy); border-bottom:1px solid var(--border); padding-bottom:6px; margin-top:20px; margin-bottom:16px;">
                2. Department & Designation
            </h4>
            <div class="grid-3">
                <div class="form-group">
                    <label class="form-label">Department <span style="color:var(--red);">*</span></label>
                    <select name="department_id" class="form-control" required>
                        <option value="">Select Department</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Designation <span style="color:var(--red);">*</span></label>
                    <select name="designation_id" class="form-control" required>
                        <option value="">Select Designation</option>
                        @foreach($designations as $desg)
                            <option value="{{ $desg->id }}">{{ $desg->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Joining Date <span style="color:var(--red);">*</span></label>
                    <input type="date" name="joining_date" class="form-control" value="{{ old('joining_date', today()->toDateString()) }}" required>
                </div>
            </div>

            <div class="grid-3">
                <div class="form-group">
                    <label class="form-label">Basic Salary <span style="color:var(--red);">*</span></label>
                    <input type="number" name="basic_salary" class="form-control" value="{{ old('basic_salary', '0.00') }}" step="0.01" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Status <span style="color:var(--red);">*</span></label>
                    <select name="is_active" class="form-control" required>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Staff Photo</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:24px; border-top:1px solid var(--border); padding-top:16px;">
                <a href="{{ route('school.staff.index') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Staff Member</button>
            </div>
        </form>
    </div>
</div>
@endsection
