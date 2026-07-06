@extends('layouts.app')

@section('title', 'Student Profile')

@section('content')
<style>
    :root {
        --accent: var(--gold, #f59e0b);
        --accent-rgb: 245, 158, 11;
        --text-muted: var(--t2, #6b7280);
    }
    
    body.dark-mode {
        --text-muted: #94a3b8;
    }

    /* Redesigned Glass Card */
    .glass-card {
        background: var(--card, #ffffff);
        border: 1px solid var(--border, #e5e7eb);
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        backdrop-filter: blur(12px);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    body.dark-mode .glass-card {
        background: rgba(17, 24, 37, 0.85) !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3) !important;
    }

    .glass-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 35px -5px rgba(0, 0, 0, 0.08);
    }

    /* Profile Side Card Customizations */
    .avatar-wrapper {
        position: relative;
        width: 140px;
        height: 140px;
        margin: 0 auto 1.5rem;
    }

    .avatar-glow {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(var(--accent-rgb), 0.2) 0%, transparent 70%);
        animation: pulse-glow 3s infinite;
        z-index: 1;
    }

    .avatar-image {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background-size: cover;
        background-position: center;
        border: 4px solid var(--accent);
        box-shadow: var(--shadow-lg);
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .avatar-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(var(--accent-rgb), 0.1);
        color: var(--accent);
    }

    /* Tab Design */
    .custom-tabs {
        display: flex;
        gap: 0.5rem;
        background: rgba(0, 0, 0, 0.03);
        padding: 6px;
        border-radius: 12px;
        border: 1px solid var(--border);
        margin-bottom: 2rem;
    }

    body.dark-mode .custom-tabs {
        background: rgba(255, 255, 255, 0.03);
    }

    .tab-trigger {
        flex: 1;
        background: transparent;
        border: none;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-muted);
        padding: 10px 16px;
        cursor: pointer;
        border-radius: 9px;
        transition: all 0.25s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .tab-trigger.active {
        background: var(--accent) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(var(--accent-rgb), 0.25);
    }

    .tab-trigger:not(.active):hover {
        background: rgba(0, 0, 0, 0.05);
        color: var(--t1);
    }

    body.dark-mode .tab-trigger:not(.active):hover {
        background: rgba(255, 255, 255, 0.05);
    }

    /* Info Grid Item */
    .info-tile {
        background: rgba(0, 0, 0, 0.015);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 16px;
        transition: all 0.2s ease;
    }

    body.dark-mode .info-tile {
        background: rgba(255, 255, 255, 0.01) !important;
        border-color: rgba(255, 255, 255, 0.05) !important;
    }

    .info-tile:hover {
        border-color: var(--accent);
        background: rgba(var(--accent-rgb), 0.02);
    }

    .info-label {
        display: flex;
        align-items: center;
        gap: 6px;
        color: var(--text-muted);
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 6px;
    }

    .info-value {
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--t1);
    }

    /* Animations */
    @keyframes pulse-glow {
        0% { transform: scale(0.95); opacity: 0.5; }
        50% { transform: scale(1.1); opacity: 0.8; }
        100% { transform: scale(0.95); opacity: 0.5; }
    }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2 style="font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.8rem;">Profile 360°</h2>
    <div style="display: flex; gap: 1rem;">
        <a href="{{ route('school.students.download-pdf', $student->id) }}" class="btn-accent" style="background-color: #10B981; color: white; display: flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 10px; font-weight: bold; text-decoration: none; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);">
            <i class="fa fa-download"></i> Download PDF
        </a>
        <a href="{{ route('school.students.edit', $student->id) }}" class="btn-accent" style="background-color: var(--accent); color: white; display: flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 10px; font-weight: bold; text-decoration: none; box-shadow: 0 4px 12px rgba(var(--accent-rgb), 0.2);">
            <i class="fa fa-edit"></i> Edit Record
        </a>
        <a href="{{ route('school.students.index') }}" class="btn-accent" style="background-color: #4B5563; color: white; display: flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 10px; font-weight: bold; text-decoration: none;">
            <i class="fa fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<div style="display: flex; gap: 2rem; align-items: flex-start; flex-wrap: wrap;">
    <!-- Profile Card (Left panel) -->
    <div class="glass-card" style="flex: 1; min-width: 300px; text-align: center;">
        <div class="avatar-wrapper">
            <div class="avatar-glow"></div>
            <div class="avatar-image" style="background-image: url('{{ $student->photo_url }}');">
                @if(!$student->photo)
                    <div class="avatar-placeholder">
                        <i class="fa fa-user" style="font-size: 4rem;"></i>
                    </div>
                @endif
            </div>
        </div>
        
        <h3 style="font-family: 'Syne', sans-serif; font-size: 1.6rem; font-weight: 800; margin-bottom: 0.5rem; color: var(--t1);">{{ $student->full_name }}</h3>
        <span class="badge badge-success" style="background-color: rgba(16, 185, 129, 0.15); color: #10b981; font-weight: 700; border-radius: 30px; padding: 6px 16px; font-size: 0.85rem; border: 1px solid rgba(16, 185, 129, 0.2); display: inline-block; margin-bottom: 2rem;">
            ID: {{ $student->admission_number }}
        </span>
        
        <div style="border-top: 1px solid var(--border); padding-top: 1.5rem; text-align: left; display: flex; flex-direction: column; gap: 1rem; font-size: 0.95rem;">
            <div style="display: flex; justify-content: space-between;"><strong style="color: var(--text-muted); font-weight: 600;">Class / Sec:</strong> <span style="font-weight: 700; color: var(--t1);">{{ $student->class?->name }} - {{ $student->section?->name }}</span></div>
            <div style="display: flex; justify-content: space-between;"><strong style="color: var(--text-muted); font-weight: 600;">Roll Number:</strong> <span style="font-weight: 700; color: var(--t1);">{{ $student->roll_number ?? 'N/A' }}</span></div>
            <div style="display: flex; justify-content: space-between;"><strong style="color: var(--text-muted); font-weight: 600;">Gender:</strong> <span style="font-weight: 700; color: var(--t1);">{{ ucfirst($student->gender) }}</span></div>
            <div style="display: flex; justify-content: space-between;"><strong style="color: var(--text-muted); font-weight: 600;">Age:</strong> <span style="font-weight: 700; color: var(--t1); font-size: 12.5px;">{{ $student->detailed_age }}</span></div>
        </div>
    </div>

    <!-- Tabbed details wrapper (Right panel) -->
    <div style="flex: 2.2; min-width: 320px; width: 100%;">
        <!-- Tabs selection bar -->
        <div class="custom-tabs" style="flex-wrap: wrap;">
            <button class="tab-trigger active" onclick="switchTab('general')" id="btn-tab-general">
                <i class="fa fa-info-circle"></i> General Info
            </button>
            <button class="tab-trigger" onclick="switchTab('guardian')" id="btn-tab-guardian">
                <i class="fa fa-users"></i> Guardian
            </button>
            <button class="tab-trigger" onclick="switchTab('attendance')" id="btn-tab-attendance">
                <i class="fa fa-calendar-check"></i> Attendance
            </button>
            <button class="tab-trigger" onclick="switchTab('siblings')" id="btn-tab-siblings">
                <i class="fa fa-user-friends"></i> Siblings
            </button>
            <button class="tab-trigger" onclick="switchTab('exams')" id="btn-tab-exams">
                <i class="fa fa-file-invoice"></i> Exams
            </button>
            <button class="tab-trigger" onclick="switchTab('transport')" id="btn-tab-transport">
                <i class="fa fa-bus"></i> Transport
            </button>
            <button class="tab-trigger" onclick="switchTab('fees')" id="btn-tab-fees">
                <i class="fa fa-credit-card"></i> Fees
            </button>
        </div>

        <!-- TAB CONTENT: General -->
        <div id="tab-general" class="tab-content glass-card">
            <h3 style="font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.3rem; margin-bottom: 1.5rem; color: var(--accent); display: flex; align-items: center; gap: 8px;">
                <i class="fa fa-user-tag"></i> Student Personal Profile
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem;">
                <div class="info-tile">
                    <span class="info-label"><i class="fa fa-calendar-alt"></i> Date of Birth</span>
                    <span class="info-value">{{ $student->date_of_birth->format('d M Y') }}</span>
                </div>
                <div class="info-tile">
                    <span class="info-label"><i class="fa fa-tint"></i> Blood Group</span>
                    <span class="info-value">{{ $student->blood_group ?? 'N/A' }}</span>
                </div>
                <div class="info-tile">
                    <span class="info-label"><i class="fa fa-pray"></i> Religion</span>
                    <span class="info-value">{{ $student->religion ?? 'N/A' }}</span>
                </div>
                <div class="info-tile">
                    <span class="info-label"><i class="fa fa-id-card"></i> Caste</span>
                    <span class="info-value">{{ $student->caste ?? 'N/A' }}</span>
                </div>
                <div class="info-tile">
                    <span class="info-label"><i class="fa fa-calendar-check"></i> Admission Date</span>
                    <span class="info-value">{{ $student->admission_date->format('d M Y') }}</span>
                </div>
                <div class="info-tile">
                    <span class="info-label"><i class="fa fa-toggle-on"></i> Status</span>
                    <span class="info-value">
                        @if($student->is_active)
                            <span style="color: #10b981;"><i class="fa fa-circle" style="font-size: 8px; vertical-align: middle; margin-right: 4px;"></i> Active Enrolled</span>
                        @else
                            <span style="color: var(--red);"><i class="fa fa-circle" style="font-size: 8px; vertical-align: middle; margin-right: 4px;"></i> Suspended</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- TAB CONTENT: Guardian -->
        <div id="tab-guardian" class="tab-content glass-card" style="display: none;">
            <h3 style="font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.3rem; margin-bottom: 1.5rem; color: var(--accent); display: flex; align-items: center; gap: 8px;">
                <i class="fa fa-users-cog"></i> Guardian Information
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                <div class="info-tile">
                    <span class="info-label"><i class="fa fa-user-shield"></i> Guardian Name</span>
                    <span class="info-value">{{ $student->guardian_name }}</span>
                </div>
                <div class="info-tile">
                    <span class="info-label"><i class="fa fa-heart"></i> Relationship</span>
                    <span class="info-value">{{ ucfirst($student->guardian_relationship) }}</span>
                </div>
                <div class="info-tile">
                    <span class="info-label"><i class="fa fa-phone"></i> Phone Number</span>
                    <span class="info-value">{{ $student->guardian_phone }}</span>
                </div>
                <div class="info-tile">
                    <span class="info-label"><i class="fa fa-envelope"></i> Email Address</span>
                    <span class="info-value" style="word-break: break-all;">{{ $student->guardian_email ?? 'N/A' }}</span>
                </div>
            </div>
            
            <h4 style="font-family: 'Syne', sans-serif; font-weight: 700; margin-bottom: 1rem; color: var(--t1); border-top: 1px solid var(--border); padding-top: 1.5rem; display: flex; align-items: center; gap: 8px;">
                <i class="fa fa-home" style="color: var(--accent);"></i> Residential Address
            </h4>
            <div style="background: rgba(0, 0, 0, 0.015); border: 1px solid var(--border); border-radius: 12px; padding: 20px; line-height: 1.8; color: var(--t1);">
                <strong style="color: var(--accent);">Address:</strong> {{ $student->address }}<br>
                <strong style="color: var(--accent);">City/State/Zip:</strong> {{ $student->city }}, {{ $student->state }} - {{ $student->pincode }}
            </div>
        </div>

        <!-- TAB CONTENT: Attendance -->
        <div id="tab-attendance" class="tab-content glass-card" style="display: none;">
            <h3 style="font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.3rem; margin-bottom: 1.5rem; color: var(--accent); display: flex; align-items: center; gap: 8px;">
                <i class="fa fa-calendar-check"></i> Attendance Report
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                <div class="info-tile" style="text-align: center;">
                    <span class="info-label" style="justify-content: center;"><i class="fa fa-percent"></i> Rate</span>
                    <span class="info-value" style="font-size: 1.5rem; color: #10b981;">{{ $attendancePercentage }}%</span>
                </div>
                <div class="info-tile" style="text-align: center;">
                    <span class="info-label" style="justify-content: center;"><i class="fa fa-calendar"></i> Total Days</span>
                    <span class="info-value" style="font-size: 1.5rem;">{{ $totalDays }}</span>
                </div>
                <div class="info-tile" style="text-align: center;">
                    <span class="info-label" style="justify-content: center; color: #10b981;"><i class="fa fa-check-circle"></i> Present</span>
                    <span class="info-value" style="font-size: 1.5rem; color: #10b981;">{{ $presentDays }}</span>
                </div>
                <div class="info-tile" style="text-align: center;">
                    <span class="info-label" style="justify-content: center; color: var(--red);"><i class="fa fa-times-circle"></i> Absent</span>
                    <span class="info-value" style="font-size: 1.5rem; color: var(--red);">{{ $absentDays }}</span>
                </div>
                <div class="info-tile" style="text-align: center;">
                    <span class="info-label" style="justify-content: center; color: #f59e0b;"><i class="fa fa-clock"></i> Late</span>
                    <span class="info-value" style="font-size: 1.5rem; color: #f59e0b;">{{ $lateDays }}</span>
                </div>
            </div>

            <h4 style="font-family: 'Syne', sans-serif; font-weight: 700; margin-bottom: 1rem; color: var(--t1); border-top: 1px solid var(--border); padding-top: 1.5rem;">
                Recent Logs
            </h4>
            <div class="table-wrap" style="max-height: 250px; overflow-y: auto;">
                <table class="tbl" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances->take(15) as $att)
                            <tr>
                                <td style="font-weight: 600;">{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</td>
                                <td>
                                    @if($att->status === 'present')
                                        <span class="badge badge-success">Present</span>
                                    @elseif($att->status === 'absent')
                                        <span class="badge badge-danger">Absent</span>
                                    @elseif($att->status === 'late')
                                        <span class="badge badge-warning">Late</span>
                                    @else
                                        <span class="badge badge-blue">{{ ucfirst($att->status) }}</span>
                                    @endif
                                </td>
                                <td style="color: var(--text-muted);">{{ $att->remarks ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: var(--text-muted); padding: 15px;">No attendance records registered.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB CONTENT: Siblings -->
        <div id="tab-siblings" class="tab-content glass-card" style="display: none;">
            <h3 style="font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.3rem; margin-bottom: 1.5rem; color: var(--accent); display: flex; align-items: center; gap: 8px;">
                <i class="fa fa-user-friends"></i> Sibling Information
            </h3>
            <div class="table-wrap">
                <table class="tbl" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Admission ID</th>
                            <th>Full Name</th>
                            <th>Class & Section</th>
                            <th>Roll Number</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siblings as $sib)
                            <tr>
                                <td><span class="badge badge-blue">{{ $sib->admission_number }}</span></td>
                                <td style="font-weight: 700;">{{ $sib->full_name }}</td>
                                <td>{{ $sib->class?->name ?? 'N/A' }} - {{ $sib->section?->name ?? 'N/A' }}</td>
                                <td>{{ $sib->roll_number ?? 'N/A' }}</td>
                                <td style="text-align: right;">
                                    <a href="{{ route('school.students.show', $sib->id) }}" class="btn-accent" style="background-color: var(--accent); color: white; padding: 6px 12px; border-radius: 6px; font-size: 11px; text-decoration: none; font-weight: bold; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fa fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 20px;">No registered siblings found in this school.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB CONTENT: Exams -->
        <div id="tab-exams" class="tab-content glass-card" style="display: none;">
            <h3 style="font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.3rem; margin-bottom: 1.5rem; color: var(--accent); display: flex; align-items: center; gap: 8px;">
                <i class="fa fa-file-invoice"></i> Academic Exam Marks
            </h3>
            <div class="table-wrap">
                <table class="tbl" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Exam Name</th>
                            <th>Subject</th>
                            <th>Obtained Marks</th>
                            <th>Max Marks</th>
                            <th>Grade</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($marks as $m)
                            <tr>
                                <td style="font-weight: 700; color: var(--navy);">{{ $m->exam_name }}</td>
                                <td>{{ $m->subject?->name ?? 'N/A' }}</td>
                                <td style="font-weight: bold; color: {{ ($m->marks_obtained / ($m->max_marks ?: 100)) < 0.4 ? 'var(--red)' : '#10b981' }}">{{ $m->marks_obtained }}</td>
                                <td>{{ $m->max_marks }}</td>
                                <td><span class="badge badge-success">{{ $m->grade ?? 'N/A' }}</span></td>
                                <td style="color: var(--text-muted);">{{ $m->remarks ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 20px;">No exam results or marks records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB CONTENT: Transport -->
        <div id="tab-transport" class="tab-content glass-card" style="display: none;">
            <h3 style="font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.3rem; margin-bottom: 1.5rem; color: var(--accent); display: flex; align-items: center; gap: 8px;">
                <i class="fa fa-bus"></i> Transport Mapping Details
            </h3>
            @if($student->transport_route || $student->transport_stop || $student->transport_vehicle_code)
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem;">
                    <div class="info-tile">
                        <span class="info-label"><i class="fa fa-route"></i> Transport Route</span>
                        <span class="info-value">{{ $student->transport_route ?? 'N/A' }}</span>
                    </div>
                    <div class="info-tile">
                        <span class="info-label"><i class="fa fa-map-marker-alt"></i> Bus Stop / Location</span>
                        <span class="info-value">{{ $student->transport_stop ?? 'N/A' }}</span>
                    </div>
                    <div class="info-tile">
                        <span class="info-label"><i class="fa fa-bus-alt"></i> Pickup Vehicle Code</span>
                        <span class="info-value">{{ $student->transport_vehicle_code ?? 'N/A' }}</span>
                    </div>
                    <div class="info-tile">
                        <span class="info-label"><i class="fa fa-shuttle-van"></i> Dropoff Vehicle Code</span>
                        <span class="info-value">{{ $student->transport_drop_vehicle_code ?? 'N/A' }}</span>
                    </div>
                    <div class="info-tile">
                        <span class="info-label"><i class="fa fa-calendar-alt"></i> Mapped Month</span>
                        <span class="info-value">{{ $student->transport_month ?? 'N/A' }}</span>
                    </div>
                </div>
            @else
                <div style="background: rgba(0, 0, 0, 0.015); border: 1px solid var(--border); border-radius: 12px; padding: 30px; text-align: center; color: var(--text-muted);">
                    <i class="fa fa-bus" style="font-size: 2.5rem; margin-bottom: 10px; color: #cbd5e1;"></i>
                    <p style="margin: 0;">This student is not currently mapped to any school transport routes.</p>
                </div>
            @endif
        </div>

        <!-- TAB CONTENT: Fees -->
        <div id="tab-fees" class="tab-content glass-card" style="display: none;">
            <h3 style="font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.3rem; margin-bottom: 1.5rem; color: var(--accent); display: flex; align-items: center; gap: 8px;">
                <i class="fa fa-credit-card"></i> Financial Fees Ledger
            </h3>
            
            <div style="display: flex; gap: 2rem; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 1.5rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
                <div>
                    <span style="display:block; color: var(--text-muted); font-size: 0.85rem; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Opening Dues</span>
                    <span style="font-size: 1.6rem; font-weight: 800; color: var(--red);">₹{{ number_format($student->opening_due_balance, 2) }}</span>
                </div>
                <div>
                    <span style="display:block; color: var(--text-muted); font-size: 0.85rem; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Total Assigned Fees</span>
                    <span style="font-size: 1.6rem; font-weight: 800; color: var(--navy);">₹{{ number_format($fees->sum('amount'), 2) }}</span>
                </div>
                <div>
                    <span style="display:block; color: var(--text-muted); font-size: 0.85rem; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Total Paid</span>
                    <span style="font-size: 1.6rem; font-weight: 800; color: #10b981;">₹{{ number_format($fees->sum('paid_amount'), 2) }}</span>
                </div>
                <div>
                    <span style="display:block; color: var(--text-muted); font-size: 0.85rem; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Outstanding Balance</span>
                    <span style="font-size: 1.6rem; font-weight: 800; color: var(--red);">₹{{ number_format(max(0, $fees->sum('amount') - $fees->sum('paid_amount')), 2) }}</span>
                </div>
            </div>

            <div class="table-wrap">
                <table class="tbl" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Installment</th>
                            <th>Due Date</th>
                            <th>Amount</th>
                            <th>Paid Amount</th>
                            <th>Status</th>
                            <th>Invoice Number</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fees as $f)
                            <tr>
                                <td style="font-weight: 700; color: var(--navy);">{{ $f->category?->name ?? 'N/A' }}</td>
                                <td>Installment #{{ $f->installment_no }}</td>
                                <td>{{ $f->due_date ? \Carbon\Carbon::parse($f->due_date)->format('d M Y') : '—' }}</td>
                                <td style="font-weight: 600;">₹{{ number_format($f->amount, 2) }}</td>
                                <td style="font-weight: 600; color: #10b981;">₹{{ number_format($f->paid_amount, 2) }}</td>
                                <td>
                                    @if(strtolower($f->status) === 'paid')
                                        <span class="badge badge-success">Paid</span>
                                    @elseif(strtolower($f->status) === 'partial' || strtolower($f->status) === 'partially_paid')
                                        <span class="badge badge-warning">Partial</span>
                                    @else
                                        <span class="badge badge-danger">Unpaid</span>
                                    @endif
                                </td>
                                <td><code style="font-size:12px;">{{ $f->invoice_no ?? '—' }}</code></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 20px;">No active fee allocations or schedule mapping records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function switchTab(tabId) {
        $('.tab-content').hide();
        $('#tab-' + tabId).fadeIn(200);

        $('.tab-trigger').removeClass('active');
        
        // Find trigger by matching onclick or id
        $('#btn-tab-' + tabId).addClass('active');
    }
</script>
@endsection

