@extends('layouts.app')

@section('page-title', 'Notification Settings')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-bell-concierge" style="color:var(--gold);margin-right:8px;"></i>Notification Settings</h1>
        <p>Configure which automated events dispatch emails, push notifications, and SMS triggers</p>
    </div>
</div>

<div class="card" style="max-width: 600px; margin:0 auto;">
    <div class="card-hdr">
        <h3>Trigger Tracing Configuration</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('school.communication.settings') }}">
            @csrf
            <div style="display:flex; flex-direction:column; gap:16px; margin-bottom:30px;">
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-weight:700; color:var(--navy);">
                    <input type="checkbox" name="notif_attendance" checked style="width:16px; height:16px;">
                    Send Student Absent Alerts instantly (Push/SMS)
                </label>
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-weight:700; color:var(--navy);">
                    <input type="checkbox" name="notif_fees" checked style="width:16px; height:16px;">
                    Send fee invoice creation emails & due reminders
                </label>
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-weight:700; color:var(--navy);">
                    <input type="checkbox" name="notif_exams" checked style="width:16px; height:16px;">
                    Publish marks/report cards immediately to student app
                </label>
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-weight:700; color:var(--navy);">
                    <input type="checkbox" name="notif_leave" checked style="width:16px; height:16px;">
                    Send push notification when leaves are approved/rejected
                </label>
            </div>

            <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center; padding:12px;">
                <i class="fas fa-save"></i> Save Configurations
            </button>
        </form>
    </div>
</div>
@endsection
