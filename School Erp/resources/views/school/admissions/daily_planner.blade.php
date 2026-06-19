@extends('layouts.app')

@section('page-title', 'Admissions Daily Planner')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-calendar-alt" style="color:var(--gold);margin-right:8px;"></i>Admissions Daily Planner</h1>
        <p>Schedule counseling sessions, parent-student interactions, and document verification slots</p>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-hdr">
            <h3>Schedule Admission Event</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('school.admissions.daily-planner') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Agenda Title</label>
                    <input type="text" class="form-control" name="title" required placeholder="e.g. Interaction with Amit Sen">
                </div>
                <div class="form-group">
                    <label class="form-label">Event Type</label>
                    <select name="type" class="form-control" required>
                        <option value="interaction">Parent-Student Interaction</option>
                        <option value="verification">Document Verification</option>
                        <option value="counseling">Counseling Session</option>
                        <option value="followup">Follow-up Call</option>
                    </select>
                </div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                    <div class="form-group">
                        <label class="form-label">Date</label>
                        <input type="date" class="form-control" name="date" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Time</label>
                        <input type="time" class="form-control" name="time" required value="10:00">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Notes / Instructions</label>
                    <textarea class="form-control" name="notes" rows="3" placeholder="Add details..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
                    <i class="fas fa-calendar-plus"></i> Add to Planner
                </button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-hdr">
            <h3>Admissions Agenda Timeline</h3>
        </div>
        <div class="card-body" style="max-height: 480px; overflow-y:auto;">
            <div style="display:flex; flex-direction:column; gap:15px; border-left: 2px solid var(--border); padding-left:15px; margin-left:10px;">
                
                <div style="position:relative;">
                    <div style="position:absolute; left:-22px; top:4px; width:12px; height:12px; border-radius:50%; background:#f39c12; border:2px solid var(--page);"></div>
                    <div style="font-weight:600; color:var(--navy); font-size:14px;">Parent Interview - Aarav Mehta</div>
                    <div style="font-size:11px; color:var(--t2); margin-top:2px;"><i class="far fa-clock"></i> Today, 10:00 AM | <span style="color:#f39c12; font-weight:600;">Counseling</span></div>
                    <div style="font-size:12px; color:var(--t3); margin-top:5px;">Discussing Grade 1 curriculum and school transport route options.</div>
                </div>

                <div style="position:relative;">
                    <div style="position:absolute; left:-22px; top:4px; width:12px; height:12px; border-radius:50%; background:#3498db; border:2px solid var(--page);"></div>
                    <div style="font-weight:600; color:var(--navy); font-size:14px;">Evaluation Session - Diya Sen</div>
                    <div style="font-size:11px; color:var(--t2); margin-top:2px;"><i class="far fa-clock"></i> Monday, 11:30 AM | <span style="color:#3498db; font-weight:600;">Interaction</span></div>
                    <div style="font-size:12px; color:var(--t3); margin-top:5px;">Scheduled written test followed by oral assessment.</div>
                </div>

                <div style="position:relative;">
                    <div style="position:absolute; left:-22px; top:4px; width:12px; height:12px; border-radius:50%; background:#2ecc71; border:2px solid var(--page);"></div>
                    <div style="font-weight:600; color:var(--navy); font-size:14px;">Document Check - Rohan Das</div>
                    <div style="font-size:11px; color:var(--t2); margin-top:2px;"><i class="far fa-clock"></i> Next Tuesday, 02:00 PM | <span style="color:#2ecc71; font-weight:600;">Verification</span></div>
                    <div style="font-size:12px; color:var(--t3); margin-top:5px;">Verification of transfer certificate and previous report sheets.</div>
                </div>

                <div style="position:relative;">
                    <div style="position:absolute; left:-22px; top:4px; width:12px; height:12px; border-radius:50%; background:#95a5a6; border:2px solid var(--page);"></div>
                    <div style="font-weight:600; color:var(--navy); font-size:14px;">Outreach Calls - Online Leads</div>
                    <div style="font-size:11px; color:var(--t2); margin-top:2px;"><i class="far fa-clock"></i> Daily, 04:00 PM | <span style="color:#95a5a6; font-weight:600;">Follow-up</span></div>
                    <div style="font-size:12px; color:var(--t3); margin-top:5px;">Check-in with new inquiry leads registered via the web portal.</div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
