@extends('layouts.app')

@section('page-title', 'E-Mail Broadcast Center')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-envelope-open-text" style="color:var(--gold);margin-right:8px;"></i>E-Mail Broadcast Center</h1>
        <p>Send HTML news digests, report sheets, and official circulars directly to parent and employee mailboxes</p>
    </div>
</div>

<div class="card" style="max-width: 650px; margin: 0 auto;">
    <div class="card-hdr">
        <h3>Compose Broadcast Mail</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('school.communication.email') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Recipients Mailing Group</label>
                <select class="form-control" name="group">
                    <option value="parents">All Student Guardians</option>
                    <option value="staff">All School Staff Employees</option>
                    <option value="admins">Admin Access Accounts Only</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Subject</label>
                <input type="text" class="form-control" name="subject" required placeholder="e.g. Monthly Progress Report & Summer Camp Registrations">
            </div>
            <div class="form-group">
                <label class="form-label">Mail Content (Rich HTML editor simulation)</label>
                <textarea class="form-control" name="body" rows="8" required placeholder="Type email message body here..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
                <i class="fas fa-paper-plane"></i> Dispatch Email Queue
            </button>
        </form>
    </div>
</div>
@endsection
