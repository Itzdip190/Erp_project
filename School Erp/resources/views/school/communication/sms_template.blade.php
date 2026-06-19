@extends('layouts.app')

@section('page-title', 'SMS templates')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-file-invoice" style="color:var(--gold);margin-right:8px;"></i>SMS Templates Manager</h1>
        <p>Pre-configure approved SMS formats (DLT templates) to speed up official communications</p>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-hdr">
            <h3>Add DLT approved SMS Template</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('school.communication.sms-template') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Template Name</label>
                    <input type="text" class="form-control" name="name" required placeholder="e.g. Attendance Absent Alert">
                </div>
                <div class="form-group">
                    <label class="form-label">DLT Content ID (Required)</label>
                    <input type="text" class="form-control" name="dlt_id" required placeholder="e.g. 12071618293922">
                </div>
                <div class="form-group">
                    <label class="form-label">Template Content</label>
                    <textarea class="form-control" name="message" rows="5" required placeholder="e.g. Dear Parent, your child {#var#} was absent on {#var#}. Please verify."></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
                    <i class="fas fa-save"></i> Save SMS Template
                </button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-hdr">
            <h3>Saved Templates Directory</h3>
        </div>
        <div class="card-body">
            <div style="display:flex; flex-direction:column; gap:12px;">
                <div style="padding:12px; background:var(--page); border:1px solid var(--border); border-radius:9px;">
                    <strong>Fee Outstanding Due Alert</strong> <span style="font-size:10px; color:var(--t3);">(DLT ID: 12071822394)</span>
                    <p style="font-size:12px; color:var(--t2); margin-top:5px;">Dear Parent, the tuition fees due amount of Rs. {#var#} is pending. Please pay before {#var#}.</p>
                </div>
                <div style="padding:12px; background:var(--page); border:1px solid var(--border); border-radius:9px;">
                    <strong>Emergency Holiday Announcement</strong> <span style="font-size:10px; color:var(--t3);">(DLT ID: 12071852932)</span>
                    <p style="font-size:12px; color:var(--t2); margin-top:5px;">Dear Parent/Staff, the school will remain closed on {#var#} due to {#var#}. Regards.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
