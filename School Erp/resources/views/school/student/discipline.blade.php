@extends('layouts.app')

@section('page-title', 'Discipline Management')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-gavel" style="color:var(--gold);margin-right:8px;"></i>Discipline Management Registry</h1>
        <p>Log student merits, demerits, behavioral incidents, and disciplinary warning notices</p>
    </div>
    <div class="page-hdr-right">
    </div>
</div>

<div class="grid-3">
    <!-- Log Incident Form -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-hdr">
            <h3>Log Behavioral Entry</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('school.student-mgmt.discipline') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Student</label>
                    <select name="student_id" class="form-control" required>
                        <option value="">Select Student</option>
                        @foreach($students as $st)
                            <option value="{{ $st->id }}">{{ $st->full_name }} ({{ $st->admission_number }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Incident Type</label>
                    <select name="type" class="form-control" required>
                        <option value="merit">Merit (Good Behavior / Award)</option>
                        <option value="demerit">Demerit (Behavioral Alert)</option>
                        <option value="warning">Official Disciplinary Warning</option>
                        <option value="suspension">Suspension Recommendation</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Incident Title</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Disrespecting teacher, Class topper" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description / Action Taken</label>
                    <textarea name="description" class="form-control" style="height:80px;" placeholder="Details of action taken..."></textarea>
                </div>

                <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center;">
                    <i class="fas fa-save"></i> Log Entry
                </button>
            </form>
        </div>
    </div>

    <!-- Incident History Log -->
    <div class="card" style="grid-column: span 2;">
        <div class="card-hdr">
            <h3>Disciplinary Logs Audit History</h3>
        </div>
        <div class="card-body" style="padding:0;">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Student Details</th>
                        <th>Incident</th>
                        <th>Type</th>
                        <th>Action Logged Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong style="color:var(--navy);">Aarav Sharma</strong>
                            <small style="display:block; color:var(--t3);">YIS/2026/00001</small>
                        </td>
                        <td>
                            <div style="font-weight:700;">Interschool Science Contest Winner</div>
                            <small style="color:var(--t2);">Awarded 50 merit points for winning first prize.</small>
                        </td>
                        <td><span class="badge badge-success">Merit</span></td>
                        <td>2026-06-12</td>
                    </tr>
                    <tr>
                        <td>
                            <strong style="color:var(--navy);">Rahul Verma</strong>
                            <small style="display:block; color:var(--t3);">YIS/2026/00003</small>
                        </td>
                        <td>
                            <div style="font-weight:700;">Classroom Disturbance</div>
                            <small style="color:var(--t2);">First warning issued for using mobile in classroom.</small>
                        </td>
                        <td><span class="badge badge-danger">Warning</span></td>
                        <td>2026-06-10</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
