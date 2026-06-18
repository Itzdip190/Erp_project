@extends('layouts.app')

@section('page-title', 'PTM Attendance')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-handshake" style="color:var(--gold);margin-right:8px;"></i>PTM Meeting Scheduler & Attendance</h1>
        <p>Schedule Parent-Teacher Meetings, send alerts, and track parent attendance logs</p>
    </div>
    <div class="page-hdr-right">
    </div>
</div>

<div class="grid-3">
    <!-- Schedule PTM Card -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-hdr">
            <h3>Schedule Meeting</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('school.student-mgmt.ptm') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Select Class</label>
                    <select name="class_id" class="form-control" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Meeting Date</label>
                    <input type="date" name="date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Meeting Slot (Time)</label>
                    <input type="text" name="time" class="form-control" placeholder="e.g. 10:00 AM - 01:00 PM" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Location / Platform</label>
                    <input type="text" name="location" class="form-control" placeholder="e.g. Main Auditorium, Zoom" required>
                </div>

                <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center;">
                    <i class="fas fa-calendar-plus"></i> Schedule Meeting
                </button>
            </form>
        </div>
    </div>

    <!-- Active Schedules Registry -->
    <div class="card" style="grid-column: span 2;">
        <div class="card-hdr">
            <h3>Meeting Attendance Registry</h3>
        </div>
        <div class="card-body" style="padding:0;">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>PTM Date</th>
                        <th>Target Group</th>
                        <th>Time & Location</th>
                        <th>Parent Turnout</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>2026-06-25</strong></td>
                        <td>Class 9 (All Sections)</td>
                        <td>
                            <div>10:00 AM - 01:00 PM</div>
                            <small style="color:var(--t3);"><i class="fas fa-location-dot"></i> Block C Hall</small>
                        </td>
                        <td>
                            <div style="font-weight:700; color:var(--gold);">Upcoming</div>
                            <small style="color:var(--t2);">Alert notices sent to 45 guardians.</small>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>2026-05-18</strong></td>
                        <td>Class 10 (All Sections)</td>
                        <td>
                            <div>09:30 AM - 12:30 PM</div>
                            <small style="color:var(--t3);"><i class="fas fa-location-dot"></i> Block C Hall</small>
                        </td>
                        <td>
                            <strong style="color:var(--green);">88% Turnout</strong>
                            <small style="display:block; color:var(--t2);">38 present / 4 absent.</small>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
