@extends('layouts.app')

@section('page-title', 'Leave Basics & Policy Settings')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-sliders-h" style="color:var(--gold);margin-right:8px;"></i>Leave Basics & Policies</h1>
        <p>Configure academic year leave quotas and approval chains for staff and students</p>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-hdr">
            <h3>Staff Leave Quotas</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('school.leave.basics') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Sick Leave (Days per year)</label>
                    <input type="number" class="form-control" name="sick_leave" value="12">
                </div>
                <div class="form-group">
                    <label class="form-label">Casual Leave (Days per year)</label>
                    <input type="number" class="form-control" name="casual_leave" value="15">
                </div>
                <div class="form-group">
                    <label class="form-label">Maternity/Paternity Leave (Days)</label>
                    <input type="number" class="form-control" name="maternity_leave" value="90">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
                    <i class="fas fa-save"></i> Save Staff Quotas
                </button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-hdr">
            <h3>Student Leave Settings</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('school.leave.basics') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Maximum consecutive medical leave days without certificate</label>
                    <input type="number" class="form-control" name="max_student_consecutive" value="3">
                </div>
                <div class="form-group">
                    <label class="form-label">Approval Chain</label>
                    <select class="form-control" name="approval_chain">
                        <option value="class_teacher">Class Teacher Only</option>
                        <option value="principal">Class Teacher -> Principal</option>
                        <option value="coordinator">Class Teacher -> Coordinator -> Principal</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center;">
                    <i class="fas fa-check-double"></i> Save Approval Rules
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
