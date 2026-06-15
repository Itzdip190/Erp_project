@extends('layouts.app')

@section('title', 'Bulk Staff Import')
@section('page-title', 'Bulk Staff Import')

@section('content')
<div class="card" style="max-width:700px; margin:0 auto;">
    <div class="card-hdr">
        <h3>Bulk Import Staff from CSV</h3>
    </div>
    <div class="card-body">
        <p style="font-size:13px; color:var(--t2); margin-bottom:16px;">
            Download the format template, fill in your staff data, and upload the completed CSV file here.
        </p>

        <!-- Instructions -->
        <div style="background:#f1f5f9; border:1px solid #cbd5e1; border-radius:8px; padding:16px; margin-bottom:24px; font-size:12px; line-height:1.6;">
            <strong>CSV File Format requirements:</strong>
            <ul style="padding-left:18px; margin-top:8px;">
                <li>The first row must contain columns exactly as follows: <code>employee_id,first_name,last_name,email,phone</code>.</li>
                <li><strong>employee_id</strong> must be unique across all staff profiles.</li>
                <li><strong>email</strong> will be used as the username for portal logins.</li>
                <li>Temporary passwords will be initialized automatically to <code>Welcome@2026!</code>.</li>
            </ul>
        </div>

        <form method="POST" action="{{ route('school.staff.import.post') }}" enctype="multipart/form-name">
            @csrf
            <div class="form-group">
                <label class="form-label">Choose CSV File <span style="color:var(--red);">*</span></label>
                <input type="file" name="csv_file" class="form-control" accept=".csv" required>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:8px; border-top:1px solid var(--border); padding-top:16px; margin-top:20px;">
                <a href="{{ route('school.staff.index') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">Import Staff Members</button>
            </div>
        </form>
    </div>
</div>
@endsection
