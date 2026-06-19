@extends('layouts.app')

@section('page-title', 'Bulk Staff Import')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-file-import" style="color:var(--gold);margin-right:8px;"></i>Bulk Staff Import Wizard</h1>
        <p>Import thousands of staff members and teachers in one click using CSV templates</p>
    </div>
    <div class="page-hdr-right">
        <a href="{{ route('school.staff.index') }}" class="btn btn-outline">
            <i class="fa fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<div class="grid-2">
    <!-- Import Form Card -->
    <div class="card">
        <div class="card-hdr">
            <h3>Upload Spreadsheet</h3>
        </div>
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger" style="margin-bottom: 16px;">
                    {{ session('error') }}
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success" style="margin-bottom: 16px;">
                    {{ session('success') }}
                </div>
            @endif

            <p style="color:var(--t2); font-size:13px; line-height:1.6; margin-bottom:20px;">
                Download the CSV template, populate staff records, and upload it here. The import process will execute and set default login credentials for each staff member.
            </p>
            
            <form method="POST" action="{{ route('school.staff.import.post') }}" enctype="multipart/form-data" style="display:flex; flex-direction:column; gap:16px;">
                @csrf
                <div class="form-group">
                    <label class="form-label">Select CSV File <span style="color:var(--red);">*</span></label>
                    <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                </div>
                
                <button type="submit" class="btn btn-gold" style="justify-content:center; padding:12px;">
                    <i class="fas fa-cloud-upload-alt"></i> Upload & Process Import
                </button>
            </form>
        </div>
    </div>

    <!-- Instructions Card -->
    <div class="card">
        <div class="card-hdr">
            <h3>Instructions & Template</h3>
        </div>
        <div class="card-body">
            <h4 style="font-size:13px; font-weight:700; color:var(--navy); margin-bottom:12px;">Download Blank Template:</h4>
            <a href="{{ route('school.staff.import-template') }}" class="btn btn-outline" style="margin-bottom:20px;">
                <i class="fas fa-download"></i> Download CSV Template
            </a>

            <h4 style="font-size:13px; font-weight:700; color:var(--navy); margin-bottom:8px;">Rules & Formats:</h4>
            <ul style="list-style-type:square; padding-left:16px; font-size:12.5px; color:var(--t2); display:flex; flex-direction:column; gap:8px;">
                <li>The first row must contain columns exactly as follows: <code>employee_id,first_name,last_name,email,phone</code>.</li>
                <li><strong>employee_id:</strong> Must be unique across all staff profiles.</li>
                <li><strong>first_name, last_name:</strong> Required text fields.</li>
                <li><strong>email:</strong> Will be used as the username for portal logins.</li>
                <li><strong>Temporary Passwords:</strong> Will be initialized automatically to <code>Welcome@2026!</code>.</li>
            </ul>
        </div>
    </div>
</div>
@endsection
