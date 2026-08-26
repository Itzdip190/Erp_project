@extends('layouts.app')

@section('title', 'Staff 360° Profile — ' . $staff->full_name)
@section('page-title', 'Staff 360° Profile')

@section('styles')
<style>
.s360-header {
    background: linear-gradient(135deg, #1e293b 0%, #1d4ed8 100%);
    border-radius: 16px;
    padding: 24px;
    color: #ffffff;
    margin-bottom: 24px;
    box-shadow: 0 10px 25px -5px rgba(29, 78, 216, 0.25);
}
.s360-avatar {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    border: 4px solid rgba(255, 255, 255, 0.3);
    object-fit: cover;
    background: #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    color: #64748b;
}
.s360-tab-nav {
    display: flex;
    gap: 8px;
    border-bottom: 2px solid #e2e8f0;
    margin-bottom: 20px;
    overflow-x: auto;
    padding-bottom: 4px;
}
.s360-tab-btn {
    padding: 10px 18px;
    font-weight: 700;
    font-size: 13px;
    color: #64748b;
    border: none;
    background: transparent;
    border-bottom: 3px solid transparent;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.s360-tab-btn:hover {
    color: #1d4ed8;
    background: rgba(29, 78, 216, 0.04);
    border-radius: 8px 8px 0 0;
}
.s360-tab-btn.active {
    color: #1d4ed8;
    border-bottom-color: #1d4ed8;
}
.s360-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
}
.s360-card-title {
    font-size: 15px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 10px;
}
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
}
.info-item {
    display: flex;
    flex-direction: column;
}
.info-label {
    font-size: 11.5px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-bottom: 4px;
}
.info-val {
    font-size: 14px;
    font-weight: 600;
    color: #0f172a;
    word-break: break-word;
}
.kpi-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px;
    text-align: center;
}
.kpi-num {
    font-size: 24px;
    font-weight: 800;
    color: #1d4ed8;
}
.kpi-txt {
    font-size: 11px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    margin-top: 4px;
}
.tab-content {
    display: none;
}
.tab-content.active {
    display: block;
}
.cred-box {
    background: #f0f9ff;
    border: 1px dashed #0284c7;
    border-radius: 12px;
    padding: 20px;
}
</style>
@endsection

@section('content')

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:20px; background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; border-radius:10px; padding:12px 16px; font-weight:600;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger" style="margin-bottom:20px; background:#fee2e2; color:#b91c1c; border:1px solid #fecaca; border-radius:10px; padding:12px 16px; font-weight:600;">
    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
</div>
@endif

<!-- 360 Header Banner -->
<div class="s360-header">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
        <div style="display:flex; align-items:center; gap:20px;">
            <div>
                @if($staff->photo)
                    <img src="{{ Storage::disk('public')->url($staff->photo) }}" class="s360-avatar" alt="{{ $staff->full_name }}">
                @else
                    <div class="s360-avatar">
                        <i class="fas fa-user-tie"></i>
                    </div>
                @endif
            </div>
            <div>
                <div style="display:flex; align-items:center; gap:10px;">
                    <h2 style="margin:0; font-size:24px; font-weight:800;">{{ $staff->full_name }}</h2>
                    <span class="badge {{ $staff->is_active ? 'badge-success' : 'badge-danger' }}" style="font-size:11px; padding:4px 10px; border-radius:20px;">
                        {{ $staff->is_active ? 'Active Status' : 'Inactive Status' }}
                    </span>
                </div>
                <div style="font-size:14px; opacity:0.9; margin-top:4px; font-weight:500;">
                    <span style="background:rgba(255,255,255,0.2); padding:2px 8px; border-radius:6px; font-weight:700;">{{ $staff->employee_id }}</span>
                    &bull; {{ optional($staff->designation)->name ?? 'No Designation' }} ({{ optional($staff->department)->name ?? 'General' }})
                </div>
                <div style="font-size:12px; opacity:0.8; margin-top:6px; display:flex; gap:16px;">
                    <span><i class="fas fa-envelope"></i> {{ $staff->email }}</span>
                    <span><i class="fas fa-phone"></i> {{ $staff->phone ?? 'N/A' }}</span>
                    <span><i class="fas fa-calendar-alt"></i> Joined: {{ $staff->joining_date ? $staff->joining_date->format('d M Y') : 'N/A' }}</span>
                </div>
            </div>
        </div>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="{{ route('school.staff.edit', $staff->id) }}" class="btn" style="background:#ffffff; color:#1d4ed8; font-weight:700; border-radius:8px; padding:8px 16px; text-decoration:none;"><i class="fas fa-edit"></i> Edit Profile</a>
            <a href="{{ route('school.staff.download-pdf', $staff->id) }}" target="_blank" class="btn" style="background:rgba(255,255,255,0.2); color:#ffffff; font-weight:700; border-radius:8px; padding:8px 16px; text-decoration:none; border:1px solid rgba(255,255,255,0.4);"><i class="fas fa-file-pdf"></i> Download PDF</a>
            <a href="{{ route('school.staff.index') }}" class="btn" style="background:rgba(255,255,255,0.1); color:#ffffff; font-weight:600; border-radius:8px; padding:8px 16px; text-decoration:none;"><i class="fas fa-arrow-left"></i> Back to Directory</a>
        </div>
    </div>
</div>

<!-- KPI Summary Widgets -->
<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:16px; margin-bottom:24px;">
    <div class="kpi-box">
        <div class="kpi-num" style="color:#1d4ed8;">{{ $attPercentage }}%</div>
        <div class="kpi-txt">Attendance Score</div>
    </div>
    <div class="kpi-box">
        <div class="kpi-num" style="color:#10b981;">{{ count($classTeacherSections) + count($subjectAssignments) }}</div>
        <div class="kpi-txt">Class Assignments</div>
    </div>
    <div class="kpi-box">
        <div class="kpi-num" style="color:#8b5cf6;">{{ count($leaveApplications) }}</div>
        <div class="kpi-txt">Total Leave Requests</div>
    </div>
    <div class="kpi-box">
        <div class="kpi-num" style="color:#f59e0b;">₹{{ number_format($staff->basic_salary ?? 0, 0) }}</div>
        <div class="kpi-txt">Basic Monthly Pay</div>
    </div>
</div>

<!-- 360 Navigation Tabs -->
<div class="s360-tab-nav">
    <button class="s360-tab-btn active" onclick="switch360Tab('overview', this)" id="btn-tab-overview"><i class="fas fa-user"></i> Overview & Info</button>
    <button class="s360-tab-btn" onclick="switch360Tab('credentials', this)" id="btn-tab-credentials"><i class="fas fa-key"></i> ID & Passwords</button>
    <button class="s360-tab-btn" onclick="switch360Tab('classes', this)" id="btn-tab-classes"><i class="fas fa-chalkboard-teacher"></i> Class Assignments</button>
    <button class="s360-tab-btn" onclick="switch360Tab('attendance', this)" id="btn-tab-attendance"><i class="fas fa-user-check"></i> Attendance</button>
    <button class="s360-tab-btn" onclick="switch360Tab('leaves', this)" id="btn-tab-leaves"><i class="fas fa-calendar-minus"></i> Leaves</button>
    <button class="s360-tab-btn" onclick="switch360Tab('payments', this)" id="btn-tab-payments"><i class="fas fa-wallet"></i> Payments & Salary</button>
    <button class="s360-tab-btn" onclick="switch360Tab('documents', this)" id="btn-tab-documents"><i class="fas fa-folder-open"></i> Documents</button>
    <button class="s360-tab-btn" onclick="switch360Tab('gatepass', this)" id="btn-tab-gatepass"><i class="fas fa-ticket-alt"></i> Gate Pass</button>
</div>

<!-- TAB 1: OVERVIEW -->
<div id="tab-overview" class="tab-content active">
    <div class="s360-card">
        <div class="s360-card-title"><i class="fas fa-address-card" style="color:#1d4ed8;"></i> Personal Details</div>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Full Name</span>
                <span class="info-val">{{ $staff->full_name }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Employee ID</span>
                <span class="info-val" style="color:#1d4ed8;">{{ $staff->employee_id }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Gender</span>
                <span class="info-val">{{ ucfirst($staff->gender ?? 'N/A') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Date of Birth</span>
                <span class="info-val">{{ $staff->date_of_birth ? $staff->date_of_birth->format('d M Y') : 'N/A' }} ({{ $staff->detailed_age }})</span>
            </div>
            <div class="info-item">
                <span class="info-label">Blood Group</span>
                <span class="info-val">{{ $staff->blood_group ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Aadhar Number</span>
                <span class="info-val">{{ $staff->additional_fields['aadhar_number'] ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">PAN Number</span>
                <span class="info-val">{{ $staff->pan_number ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Highest Qualification</span>
                <span class="info-val">{{ $staff->qualification ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Experience</span>
                <span class="info-val">{{ $staff->experience_years ? $staff->experience_years . ' Years' : 'Freshers / N/A' }}</span>
            </div>
        </div>
    </div>

    <div class="s360-card">
        <div class="s360-card-title"><i class="fas fa-briefcase" style="color:#10b981;"></i> Employment & Contact Information</div>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Department</span>
                <span class="info-val">{{ optional($staff->department)->name ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Designation</span>
                <span class="info-val">{{ optional($staff->designation)->name ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Staff Category</span>
                <span class="info-val">{{ $staff->staff_type }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Employment Type</span>
                <span class="info-val">{{ ucfirst($staff->employment_type ?? 'Permanent') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Joining Date</span>
                <span class="info-val">{{ $staff->joining_date ? $staff->joining_date->format('d M Y') : 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Primary Mobile</span>
                <span class="info-val">{{ $staff->phone ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Alternate Phone</span>
                <span class="info-val">{{ $staff->additional_fields['alternate_phone'] ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Primary Email</span>
                <span class="info-val">{{ $staff->email }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Father / Mother Name</span>
                <span class="info-val">{{ $staff->additional_fields['father_name'] ?? $staff->additional_fields['mother_name'] ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Spouse Name</span>
                <span class="info-val">{{ $staff->additional_fields['spouse_name'] ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <div class="s360-card">
        <div class="s360-card-title"><i class="fas fa-map-marker-alt" style="color:#ef4444;"></i> Address & Location</div>
        <div class="info-grid">
            <div class="info-item" style="grid-column: span 2;">
                <span class="info-label">Permanent Address</span>
                <span class="info-val">{{ $staff->address ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">City</span>
                <span class="info-val">{{ $staff->city ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">State</span>
                <span class="info-val">{{ $staff->state ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Pincode</span>
                <span class="info-val">{{ $staff->pincode ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Country</span>
                <span class="info-val">{{ $staff->additional_fields['country'] ?? 'India' }}</span>
            </div>
        </div>
    </div>
</div>

<!-- TAB 2: ID & LOGIN CREDENTIALS -->
<div id="tab-credentials" class="tab-content">
    <div class="s360-card">
        <div class="s360-card-title"><i class="fas fa-id-card-alt" style="color:#1d4ed8;"></i> Staff Account & Credentials Details</div>
        
        <div class="cred-box" style="margin-bottom:20px;">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
                <div>
                    <h4 style="margin:0 0 6px 0; color:#0369a1; font-size:16px;"><i class="fas fa-lock"></i> Account Credentials & Access Portal</h4>
                    <p style="margin:0; font-size:13px; color:#334155;">System login parameters and access credentials for this staff member.</p>
                </div>
                <div>
                    <button class="btn btn-primary" onclick="openResetPasswordModal()"><i class="fas fa-key"></i> Reset Password</button>
                </div>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Staff Employee ID</span>
                <span class="info-val" style="color:#1d4ed8; font-size:16px;">{{ $staff->employee_id }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Login Username / Email</span>
                <span class="info-val" style="font-size:15px; color:#0f172a;">{{ $staff->email }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">System User ID</span>
                <span class="info-val">#{{ $staff->user_id ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Assigned Role</span>
                <span class="info-val">
                    <span class="badge" style="background:#e0e7ff; color:#3730a3; font-size:12px; padding:4px 10px;">
                        {{ $staff->user?->roles->first()?->name ?? 'teacher' }}
                    </span>
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Account Status</span>
                <span class="info-val">
                    <span class="badge {{ $staff->is_active ? 'badge-success' : 'badge-danger' }}">
                        {{ $staff->is_active ? 'Active Login Enabled' : 'Disabled' }}
                    </span>
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Default Password Information</span>
                <span class="info-val" style="color:#059669; font-weight:700;">Welcome@2026!</span>
            </div>
        </div>
    </div>
</div>

<!-- TAB 3: CLASS ASSIGNMENTS -->
<div id="tab-classes" class="tab-content">
    <div class="s360-card">
        <div class="s360-card-title"><i class="fas fa-user-shield" style="color:#10b981;"></i> Class Teacher Responsibilities</div>
        @if($classTeacherSections->count() > 0)
            <table class="tbl" style="width:100%;">
                <thead>
                    <tr>
                        <th>Class</th>
                        <th>Section</th>
                        <th>Capacity</th>
                        <th>Role Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($classTeacherSections as $sec)
                    <tr>
                        <td><strong>{{ optional($sec->schoolClass)->name ?? 'Class' }}</strong></td>
                        <td><span class="badge" style="background:#e2e8f0; color:#334155;">Section {{ $sec->name }}</span></td>
                        <td>{{ $sec->capacity ?? 'N/A' }} Students</td>
                        <td><span class="badge badge-success"><i class="fas fa-star"></i> Class Teacher</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="text-align:center; padding:20px; color:#64748b;">No Class Teacher responsibilities currently assigned.</div>
        @endif
    </div>

    <div class="s360-card">
        <div class="s360-card-title"><i class="fas fa-book" style="color:#1d4ed8;"></i> Subject Teaching Assignments</div>
        @if($subjectAssignments->count() > 0)
            <table class="tbl" style="width:100%;">
                <thead>
                    <tr>
                        <th>Subject Name</th>
                        <th>Class & Section</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subjectAssignments as $subAss)
                    <tr>
                        <td><strong>{{ optional($subAss->subject)->name ?? 'Subject' }}</strong></td>
                        <td>{{ optional($subAss->section->schoolClass)->name ?? '' }} - Section {{ optional($subAss->section)->name ?? '' }}</td>
                        <td><span class="badge" style="background:#f1f5f9; color:#475569;">{{ ucfirst(optional($subAss->subject)->type ?? 'Theory') }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="text-align:center; padding:20px; color:#64748b;">No subject teaching assignments logged.</div>
        @endif
    </div>
</div>

<!-- TAB 4: ATTENDANCE -->
<div id="tab-attendance" class="tab-content">
    <div class="s360-card">
        <div class="s360-card-title"><i class="fas fa-calendar-check" style="color:#10b981;"></i> Attendance Logs (Recent 30 Days)</div>
        <div style="display:flex; gap:16px; margin-bottom:20px;">
            <span class="badge badge-success" style="padding:6px 12px; font-size:12px;">Present: {{ $presentDays }} Days</span>
            <span class="badge badge-danger" style="padding:6px 12px; font-size:12px;">Absent: {{ $absentDays }} Days</span>
            <span class="badge" style="background:#fef08a; color:#a16207; padding:6px 12px; font-size:12px;">Late: {{ $lateDays }} Days</span>
        </div>

        @if($recentAttendance->count() > 0)
            <table class="tbl" style="width:100%;">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Status</th>
                        <th>In Time</th>
                        <th>Out Time</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentAttendance as $att)
                    <tr>
                        <td><strong>{{ \Carbon\Carbon::parse($att->date)->format('d M Y, D') }}</strong></td>
                        <td>
                            @if($att->status === 'present')
                                <span class="badge badge-success">Present</span>
                            @elseif($att->status === 'absent')
                                <span class="badge badge-danger">Absent</span>
                            @else
                                <span class="badge" style="background:#fef08a; color:#a16207;">Late</span>
                            @endif
                        </td>
                        <td>{{ $att->in_time ? \Carbon\Carbon::parse($att->in_time)->format('h:i A') : '—' }}</td>
                        <td>{{ $att->out_time ? \Carbon\Carbon::parse($att->out_time)->format('h:i A') : '—' }}</td>
                        <td>{{ $att->remarks ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="text-align:center; padding:20px; color:#64748b;">No recent attendance records found.</div>
        @endif
    </div>
</div>

<!-- TAB 5: LEAVES -->
<div id="tab-leaves" class="tab-content">
    <div class="s360-card">
        <div class="s360-card-title"><i class="fas fa-layer-group" style="color:#8b5cf6;"></i> Leave Balances</div>
        @if($leaveBalances->count() > 0)
            <table class="tbl" style="width:100%; margin-bottom:20px;">
                <thead>
                    <tr>
                        <th>Leave Type</th>
                        <th>Allowed Days</th>
                        <th>Availed Days</th>
                        <th>Remaining Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaveBalances as $lb)
                    <tr>
                        <td><strong>{{ optional($lb->leaveType)->name ?? 'Leave' }}</strong></td>
                        <td>{{ $lb->allowed }} Days</td>
                        <td><span style="color:#ef4444; font-weight:700;">{{ $lb->availed }}</span> Days</td>
                        <td><span style="color:#10b981; font-weight:700;">{{ max(0, $lb->allowed - $lb->availed) }}</span> Days</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="padding:15px; color:#64748b; font-size:13px;">Standard Annual Leave Allocation: 12 Days per Annum.</div>
        @endif
    </div>

    <div class="s360-card">
        <div class="s360-card-title"><i class="fas fa-file-alt" style="color:#1d4ed8;"></i> Leave Applications History</div>
        @if($leaveApplications->count() > 0)
            <table class="tbl" style="width:100%;">
                <thead>
                    <tr>
                        <th>Leave Type</th>
                        <th>Dates</th>
                        <th>Total Days</th>
                        <th>Reason</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaveApplications as $la)
                    <tr>
                        <td><strong>{{ $la->leave_type_name }}</strong></td>
                        <td>{{ $la->start_date->format('d M Y') }} to {{ $la->end_date->format('d M Y') }}</td>
                        <td>{{ $la->total_days }} Days</td>
                        <td>{{ $la->reason ?? '—' }}</td>
                        <td>
                            @if($la->status === 'approved')
                                <span class="badge badge-success">Approved</span>
                            @elseif($la->status === 'rejected')
                                <span class="badge badge-danger">Rejected</span>
                            @else
                                <span class="badge" style="background:#fef08a; color:#a16207;">Pending</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="text-align:center; padding:20px; color:#64748b;">No leave applications on record.</div>
        @endif
    </div>
</div>

<!-- TAB 6: PAYMENTS & SALARY -->
<div id="tab-payments" class="tab-content">
    <div class="s360-card">
        <div class="s360-card-title"><i class="fas fa-money-check-alt" style="color:#10b981;"></i> Salary Structure & Payroll Setup</div>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Basic Salary</span>
                <span class="info-val" style="color:#10b981; font-size:16px;">₹{{ number_format($staff->basic_salary ?? 0, 2) }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Bank Name</span>
                <span class="info-val">{{ $staff->bank_name ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Bank Account Number</span>
                <span class="info-val">{{ $staff->bank_account_number ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">IFSC Code</span>
                <span class="info-val">{{ $staff->ifsc_code ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Payroll Account Balance</span>
                <span class="info-val" style="color:#1d4ed8; font-size:16px;">₹{{ number_format($staff->payroll_balance ?? 0, 2) }}</span>
            </div>
        </div>
    </div>

    <div class="s360-card">
        <div class="s360-card-title"><i class="fas fa-receipt" style="color:#1d4ed8;"></i> Generated Payslips History</div>
        @if($payrolls->count() > 0)
            <table class="tbl" style="width:100%;">
                <thead>
                    <tr>
                        <th>Month / Year</th>
                        <th>Payable Days</th>
                        <th>Gross Pay</th>
                        <th>Net Pay</th>
                        <th>Payment Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payrolls as $pr)
                    <tr>
                        <td><strong>{{ $pr->salary_month }} / {{ $pr->salary_year }}</strong></td>
                        <td>{{ $pr->payable_days }} Days</td>
                        <td>₹{{ number_format($pr->gross_salary, 2) }}</td>
                        <td><strong style="color:#10b981;">₹{{ number_format($pr->net_payable, 2) }}</strong></td>
                        <td>
                            <span class="badge {{ $pr->payment_status === 'paid' ? 'badge-success' : 'badge-danger' }}">
                                {{ ucfirst($pr->payment_status ?? 'Unpaid') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="text-align:center; padding:20px; color:#64748b;">No generated payslips on record yet.</div>
        @endif
    </div>
</div>

<!-- TAB 7: DOCUMENTS -->
<div id="tab-documents" class="tab-content">
    <div class="s360-card">
        <div class="s360-card-hdr" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <div class="s360-card-title" style="margin-bottom:0; border:none; padding:0;"><i class="fas fa-folder-open" style="color:#f59e0b;"></i> Staff Attachment Documents</div>
            <button class="btn btn-primary" onclick="openUploadDocModal()"><i class="fas fa-upload"></i> Upload Document</button>
        </div>

        @if(!empty($documents) && count($documents) > 0)
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
                @foreach($documents as $doc)
                <div style="border:1px solid #e2e8f0; border-radius:10px; padding:14px; background:#f8fafc; display:flex; align-items:center; justify-content:space-between;">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <i class="fas fa-file-pdf" style="font-size:24px; color:#ef4444;"></i>
                        <div>
                            <div style="font-weight:700; font-size:13px; color:#1e293b;">{{ $doc['title'] }}</div>
                            <div style="font-size:11px; color:#64748b;">{{ $doc['uploaded_at'] ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <a href="{{ Storage::disk('public')->url($doc['path']) }}" target="_blank" class="btn btn-outline" style="padding:4px 8px; font-size:11px;"><i class="fas fa-download"></i></a>
                </div>
                @endforeach
            </div>
        @else
            <div style="text-align:center; padding:30px; color:#64748b; background:#f8fafc; border-radius:10px; border:1px dashed #cbd5e1;">
                <i class="fas fa-cloud-upload-alt" style="font-size:32px; color:#94a3b8; margin-bottom:10px;"></i>
                <div>No custom uploaded documents attached yet. Click 'Upload Document' above to add certificates, resume, or ID proofs.</div>
            </div>
        @endif
    </div>
</div>

<!-- Password Reset Modal -->
<div id="resetPasswordModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15,23,42,0.6); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#ffffff; width:90%; max-width:440px; border-radius:16px; padding:24px; box-shadow:0 20px 25px -5px rgba(0,0,0,0.1);">
        <h3 style="margin-top:0; color:#1e293b;"><i class="fas fa-key" style="color:#1d4ed8;"></i> Reset Password</h3>
        <p style="font-size:13px; color:#64748b;">Update login password for staff user <strong>{{ $staff->email }}</strong>.</p>
        <form method="POST" action="{{ route('school.staff.reset-password', $staff->id) }}">
            @csrf
            <div class="form-group" style="margin-bottom:16px;">
                <label class="form-label">New Password</label>
                <input type="password" name="new_password" class="form-control" placeholder="Enter new password (min 6 chars)" required minlength="6">
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-outline" onclick="closeResetPasswordModal()">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Password</button>
            </div>
        </form>
    </div>
</div>

<!-- TAB 8: GATE PASS -->
<div id="tab-gatepass" class="tab-content">
    @include('school.staff.gate-pass.tab-content')
</div>

<!-- Upload Document Modal -->
<div id="uploadDocModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15,23,42,0.6); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#ffffff; width:90%; max-width:480px; border-radius:16px; padding:24px; box-shadow:0 20px 25px -5px rgba(0,0,0,0.1);">
        <h3 style="margin-top:0; color:#1e293b;"><i class="fas fa-upload" style="color:#10b981;"></i> Upload Staff Document</h3>
        <form method="POST" action="{{ route('school.staff.upload-document', $staff->id) }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group" style="margin-bottom:16px;">
                <label class="form-label">Document Title (e.g. Aadhar Card, Degree)</label>
                <input type="text" name="document_title" class="form-control" placeholder="Document title..." required>
            </div>
            <div class="form-group" style="margin-bottom:16px;">
                <label class="form-label">File Attachment (PDF, Image, Doc)</label>
                <input type="file" name="document_file" class="form-control" required>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-outline" onclick="closeUploadDocModal()">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-cloud-upload-alt"></i> Upload Now</button>
            </div>
        </form>
    </div>
</div>

<script>
function switch360Tab(tabId, btn) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.s360-tab-btn').forEach(el => el.classList.remove('active'));
    
    const targetTab = document.getElementById('tab-' + tabId);
    if (targetTab) targetTab.classList.add('active');
    
    if (btn) {
        btn.classList.add('active');
    } else {
        const matchingBtn = document.getElementById('btn-tab-' + tabId);
        if (matchingBtn) matchingBtn.classList.add('active');
    }
}

function openResetPasswordModal() {
    document.getElementById('resetPasswordModal').style.display = 'flex';
}

function closeResetPasswordModal() {
    document.getElementById('resetPasswordModal').style.display = 'none';
}

function openUploadDocModal() {
    document.getElementById('uploadDocModal').style.display = 'flex';
}

function closeUploadDocModal() {
    document.getElementById('uploadDocModal').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab');
    if (activeTab && document.getElementById('tab-' + activeTab)) {
        switch360Tab(activeTab);
    }
});
</script>
@endsection
