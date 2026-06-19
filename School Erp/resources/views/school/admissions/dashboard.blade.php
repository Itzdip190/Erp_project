@extends('layouts.app')

@section('page-title', 'Admissions Dashboard')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-chart-line" style="color:var(--gold);margin-right:8px;"></i>Admissions Dashboard</h1>
        <p>Real-time intake tracking, conversion rates, and funnel analytics</p>
    </div>
</div>

<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:20px; margin-bottom:35px;">
    <div class="card" style="border-left: 4px solid var(--navy);">
        <div class="card-body" style="display:flex; justify-content:space-between; align-items:center; padding:20px;">
            <div>
                <div style="font-size:12px; color:var(--t2); text-transform:uppercase; font-weight:600; letter-spacing:0.5px;">Total Enquiry Leads</div>
                <div style="font-size:28px; font-weight:700; color:var(--navy); margin-top:5px;">{{ $newCount + $contactedCount + $enrolledCount + $rejectedCount }}</div>
            </div>
            <i class="fas fa-users" style="font-size:32px; color:var(--navy); opacity:0.25;"></i>
        </div>
    </div>
    
    <div class="card" style="border-left: 4px solid #f39c12;">
        <div class="card-body" style="display:flex; justify-content:space-between; align-items:center; padding:20px;">
            <div>
                <div style="font-size:12px; color:var(--t2); text-transform:uppercase; font-weight:600; letter-spacing:0.5px;">New Prospect Leads</div>
                <div style="font-size:28px; font-weight:700; color:#f39c12; margin-top:5px;">{{ $newCount }}</div>
            </div>
            <i class="fas fa-user-plus" style="font-size:32px; color:#f39c12; opacity:0.25;"></i>
        </div>
    </div>

    <div class="card" style="border-left: 4px solid #3498db;">
        <div class="card-body" style="display:flex; justify-content:space-between; align-items:center; padding:20px;">
            <div>
                <div style="font-size:12px; color:var(--t2); text-transform:uppercase; font-weight:600; letter-spacing:0.5px;">Contacted / In Process</div>
                <div style="font-size:28px; font-weight:700; color:#3498db; margin-top:5px;">{{ $contactedCount }}</div>
            </div>
            <i class="fas fa-phone-alt" style="font-size:32px; color:#3498db; opacity:0.25;"></i>
        </div>
    </div>

    <div class="card" style="border-left: 4px solid #2ecc71;">
        <div class="card-body" style="display:flex; justify-content:space-between; align-items:center; padding:20px;">
            <div>
                <div style="font-size:12px; color:var(--t2); text-transform:uppercase; font-weight:600; letter-spacing:0.5px;">Converted / Enrolled</div>
                <div style="font-size:28px; font-weight:700; color:#2ecc71; margin-top:5px;">{{ $enrolledCount }}</div>
            </div>
            <i class="fas fa-user-check" style="font-size:32px; color:#2ecc71; opacity:0.25;"></i>
        </div>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-hdr">
            <h3>Intake Funnel Conversion</h3>
        </div>
        <div class="card-body" style="display:flex; flex-direction:column; gap:20px;">
            @php
                $total = $newCount + $contactedCount + $enrolledCount + $rejectedCount;
                $convRate = $total > 0 ? round(($enrolledCount / $total) * 100, 1) : 0;
            @endphp
            <div>
                <div style="display:flex; justify-content:space-between; font-size:13px; color:var(--navy); font-weight:600; margin-bottom:8px;">
                    <span>Admission Conversion Rate</span>
                    <span>{{ $convRate }}%</span>
                </div>
                <div style="height:10px; background:var(--border); border-radius:5px; overflow:hidden;">
                    <div style="width:{{ $convRate }}%; height:100%; background:var(--gold); border-radius:5px;"></div>
                </div>
            </div>

            <div style="display:flex; flex-direction:column; gap:12px; margin-top:10px;">
                <div style="display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid var(--border);">
                    <span style="font-size:13px; color:var(--t2);"><i class="fas fa-circle" style="color:#2ecc71; font-size:10px; margin-right:8px;"></i>Enrolled Students</span>
                    <strong style="color:var(--navy);">{{ $enrolledCount }}</strong>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid var(--border);">
                    <span style="font-size:13px; color:var(--t2);"><i class="fas fa-circle" style="color:#f39c12; font-size:10px; margin-right:8px;"></i>Pending Evaluation (New)</span>
                    <strong style="color:var(--navy);">{{ $newCount }}</strong>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid var(--border);">
                    <span style="font-size:13px; color:var(--t2);"><i class="fas fa-circle" style="color:#3498db; font-size:10px; margin-right:8px;"></i>Contacted & Scheduling</span>
                    <strong style="color:var(--navy);">{{ $contactedCount }}</strong>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; padding:8px 0;">
                    <span style="font-size:13px; color:var(--t2);"><i class="fas fa-circle" style="color:#e74c3c; font-size:10px; margin-right:8px;"></i>Rejected Leads</span>
                    <strong style="color:var(--navy);">{{ $rejectedCount }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-hdr">
            <h3>Quick Admissions Operations</h3>
        </div>
        <div class="card-body" style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
            <a href="{{ route('school.admissions.process') }}" class="btn btn-secondary" style="display:flex; flex-direction:column; gap:8px; padding:20px; align-items:center; text-align:center; text-decoration:none;">
                <i class="fas fa-tasks" style="font-size:24px; color:var(--gold);"></i>
                <span style="font-size:12px; font-weight:600; color:var(--navy);">Admission Process</span>
            </a>
            <a href="{{ route('school.admissions.enquiry-leads') }}" class="btn btn-secondary" style="display:flex; flex-direction:column; gap:8px; padding:20px; align-items:center; text-align:center; text-decoration:none;">
                <i class="fas fa-filter" style="font-size:24px; color:var(--gold);"></i>
                <span style="font-size:12px; font-weight:600; color:var(--navy);">Enquiry Leads</span>
            </a>
            <a href="{{ route('school.admissions.interaction-evaluation') }}" class="btn btn-secondary" style="display:flex; flex-direction:column; gap:8px; padding:20px; align-items:center; text-align:center; text-decoration:none;">
                <i class="fas fa-calendar-check" style="font-size:24px; color:var(--gold);"></i>
                <span style="font-size:12px; font-weight:600; color:var(--navy);">Evaluations</span>
            </a>
            <a href="{{ route('school.admissions.admission') }}" class="btn btn-secondary" style="display:flex; flex-direction:column; gap:8px; padding:20px; align-items:center; text-align:center; text-decoration:none;">
                <i class="fas fa-check-double" style="font-size:24px; color:var(--gold);"></i>
                <span style="font-size:12px; font-weight:600; color:var(--navy);">Enroll Student</span>
            </a>
        </div>
    </div>
</div>
@endsection
