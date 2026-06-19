@extends('layouts.app')

@section('page-title', 'SMS Gateway Portal')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-sms" style="color:var(--gold);margin-right:8px;"></i>SMS Broadcast Center</h1>
        <p>Send high-priority SMS reminders directly to the parent phone numbers configured in directory files</p>
    </div>
</div>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-hdr">
        <h3>Dispatch SMS Notification</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('school.communication.sms') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Send To Group</label>
                <select class="form-control" name="target">
                    <option value="parents_all">All Guardian Primary Contacts</option>
                    <option value="parents_grade_1">Parents of Grade 1</option>
                    <option value="parents_grade_2">Parents of Grade 2</option>
                    <option value="staff_all">All School Staff Employees</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">SMS Template</label>
                <select class="form-control" name="template">
                    <option value="custom">-- Custom Message --</option>
                    <option value="absent">Attendance: [Student] is absent on [Date].</option>
                    <option value="fee">Fees Reminder: Outstanding dues [Amount] on [Due Date].</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Message Text</label>
                <textarea class="form-control" name="message" rows="5" required placeholder="Write SMS contents here... (Max 160 chars for 1 credit)"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
                <i class="fas fa-paper-plane"></i> Dispatch SMS Broadcast
            </button>
        </form>
    </div>
</div>
@endsection
