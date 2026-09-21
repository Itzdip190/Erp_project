@extends('layouts.app')

@section('title', 'Staff Portal')
@section('page-title', 'Staff Portal')

@section('styles')
<style>
    .staff-portal-wrap {
        padding: 10px 0 30px;
    }
    .sp-hero {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 60%, #3b82f6 100%);
        border-radius: 18px;
        padding: 28px 32px;
        color: #fff;
        margin-bottom: 24px;
        box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.25);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 20px;
    }
    .sp-hero-profile {
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .sp-hero-avatar {
        width: 72px;
        height: 72px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid rgba(255, 255, 255, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        font-weight: 800;
        color: #fff;
        overflow: hidden;
        flex-shrink: 0;
    }
    .sp-hero-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .sp-hero-info h2 {
        font-size: 22px;
        font-weight: 800;
        margin: 0 0 6px;
        letter-spacing: -0.3px;
    }
    .sp-hero-badges {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .sp-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .sp-badge-desg {
        background: rgba(255, 255, 255, 0.22);
        color: #fff;
    }
    .sp-badge-dept {
        background: rgba(255, 255, 255, 0.14);
        color: #e0f2fe;
    }
    .sp-badge-emp {
        background: #fef08a;
        color: #854d0e;
        font-weight: 700;
    }

    /* Stat Cards */
    .sp-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .sp-stat-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform .2s, box-shadow .2s;
    }
    .sp-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.06);
    }
    .sp-stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .sp-stat-val {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }
    .sp-stat-lbl {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        margin-top: 3px;
    }

    /* Action Buttons */
    .sp-actions-bar {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 14px;
        margin-bottom: 24px;
    }
    .sp-act-btn {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 20px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        text-decoration: none;
        color: #1e293b;
        font-weight: 700;
        font-size: 14px;
        transition: all .2s;
    }
    .sp-act-btn:hover {
        border-color: #3b82f6;
        background: #eff6ff;
        color: #1d4ed8;
        transform: translateY(-2px);
        text-decoration: none;
    }
    .sp-act-btn i {
        font-size: 20px;
        color: #2563eb;
    }

    /* Content 2-Column */
    .sp-main-grid {
        display: grid;
        grid-template-columns: 1.4fr 1fr;
        gap: 20px;
    }
    @media (max-width: 991px) {
        .sp-main-grid { grid-template-columns: 1fr; }
    }
    .sp-box {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .sp-box-hdr {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
    }
    .sp-box-title {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .sp-box-title i {
        color: #2563eb;
    }
    .sp-item-row {
        padding: 12px 14px;
        border-radius: 10px;
        background: #f8fafc;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .sp-item-left h5 {
        font-size: 13.5px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 4px;
    }
    .sp-item-left p {
        font-size: 12px;
        color: #64748b;
        margin: 0;
    }
    .sp-status-badge {
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 700;
        text-transform: capitalize;
    }
    .status-approved, .status-present { background: #dcfce7; color: #15803d; }
    .status-pending, .status-late { background: #fef9c3; color: #a16207; }
    .status-rejected, .status-absent { background: #fee2e2; color: #b91c1c; }
    .status-half_day { background: #ffedd5; color: #c2410c; }
</style>
@endsection

@section('content')
<div class="staff-portal-wrap container-fluid">

    {{-- Hero Profile Banner --}}
    <div class="sp-hero">
        <div class="sp-hero-profile">
            <div class="sp-hero-avatar">
                @if($staff && $staff->photo)
                    <img src="{{ asset('storage/' . $staff->photo) }}" alt="{{ $staff->first_name }}">
                @else
                    {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}
                @endif
            </div>
            <div class="sp-hero-info">
                <h2>Welcome, {{ auth()->user()->name }}</h2>
                <div class="sp-hero-badges">
                    <span class="sp-badge sp-badge-desg">
                        <i class="fas fa-id-badge"></i> {{ $staff?->designation?->name ?? 'Staff Member' }}
                    </span>
                    @if($staff?->department)
                    <span class="sp-badge sp-badge-dept">
                        <i class="fas fa-building"></i> {{ $staff->department->name }}
                    </span>
                    @endif
                    @if($staff?->employee_id)
                    <span class="sp-badge sp-badge-emp">
                        ID: {{ $staff->employee_id }}
                    </span>
                    @endif
                </div>
            </div>
        </div>
        <div>
            <span style="font-size: 13px; font-weight: 600; background: rgba(255,255,255,0.18); padding: 6px 14px; border-radius: 20px;">
                <i class="fas fa-calendar-day"></i> {{ now()->format('l, d M Y') }}
            </span>
        </div>
    </div>

    {{-- Monthly Attendance Statistics --}}
    <div class="sp-stats-grid">
        <div class="sp-stat-card">
            <div class="sp-stat-icon" style="background: #ecfdf5; color: #10b981;">
                <i class="fas fa-user-check"></i>
            </div>
            <div>
                <div class="sp-stat-val">{{ $attendanceStats['present'] }}</div>
                <div class="sp-stat-lbl">Days Present (This Month)</div>
            </div>
        </div>
        <div class="sp-stat-card">
            <div class="sp-stat-icon" style="background: #fefce8; color: #ca8a04;">
                <i class="fas fa-user-clock"></i>
            </div>
            <div>
                <div class="sp-stat-val">{{ $attendanceStats['late'] }}</div>
                <div class="sp-stat-lbl">Days Late</div>
            </div>
        </div>
        <div class="sp-stat-card">
            <div class="sp-stat-icon" style="background: #fff7ed; color: #ea580c;">
                <i class="fas fa-adjust"></i>
            </div>
            <div>
                <div class="sp-stat-val">{{ $attendanceStats['half_day'] }}</div>
                <div class="sp-stat-lbl">Half Days</div>
            </div>
        </div>
        <div class="sp-stat-card">
            <div class="sp-stat-icon" style="background: #fef2f2; color: #ef4444;">
                <i class="fas fa-user-times"></i>
            </div>
            <div>
                <div class="sp-stat-val">{{ $attendanceStats['absent'] }}</div>
                <div class="sp-stat-lbl">Days Absent</div>
            </div>
        </div>
    </div>

    {{-- Quick Action Shortcuts --}}
    <div class="sp-actions-bar">
        <a href="{{ route('teacher.leave.apply') }}" class="sp-act-btn">
            <i class="fas fa-calendar-plus"></i>
            <span>Apply for Leave</span>
        </a>
        <a href="{{ route('school.gate-passes.staff.index') }}" class="sp-act-btn">
            <i class="fas fa-id-card"></i>
            <span>My Gatepass</span>
        </a>
        <a href="{{ route('teacher.notices.index') }}" class="sp-act-btn">
            <i class="fas fa-bullhorn"></i>
            <span>Notice Board</span>
        </a>
    </div>

    {{-- 2-Column Main Content --}}
    <div class="sp-main-grid">

        {{-- Left: Leaves & Gatepasses --}}
        <div>
            {{-- Leave Applications --}}
            <div class="sp-box">
                <div class="sp-box-hdr">
                    <div class="sp-box-title">
                        <i class="fas fa-plane-departure"></i>
                        <span>My Recent Leave Applications</span>
                    </div>
                    <a href="{{ route('teacher.leave.apply') }}" style="font-size: 12.5px; font-weight: 700; color: #2563eb; text-decoration: none;">
                        + New Application
                    </a>
                </div>
                @if($recentLeaves->isEmpty())
                    <div style="text-align: center; padding: 25px; color: #94a3b8; font-size: 13.5px;">
                        <i class="fas fa-check-circle" style="font-size: 32px; color: #cbd5e1; display: block; margin-bottom: 8px;"></i>
                        No recent leave applications.
                    </div>
                @else
                    @foreach($recentLeaves as $leave)
                        <div class="sp-item-row">
                            <div class="sp-item-left">
                                <h5>{{ $leave->leave_type ?? 'Staff Leave' }}</h5>
                                <p>{{ \Carbon\Carbon::parse($leave->from_date)->format('d M') }} — {{ \Carbon\Carbon::parse($leave->to_date)->format('d M Y') }} ({{ $leave->days_count ?? 1 }} Days)</p>
                            </div>
                            <span class="sp-status-badge status-{{ strtolower($leave->status ?? 'pending') }}">
                                {{ ucfirst($leave->status ?? 'pending') }}
                            </span>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- Recent Gatepasses --}}
            <div class="sp-box">
                <div class="sp-box-hdr">
                    <div class="sp-box-title">
                        <i class="fas fa-door-open"></i>
                        <span>My Recent Gatepasses</span>
                    </div>
                    <a href="{{ route('school.gate-passes.staff.index') }}" style="font-size: 12.5px; font-weight: 700; color: #2563eb; text-decoration: none;">
                        View All
                    </a>
                </div>
                @if($recentGatepasses->isEmpty())
                    <div style="text-align: center; padding: 25px; color: #94a3b8; font-size: 13.5px;">
                        <i class="fas fa-shield-alt" style="font-size: 32px; color: #cbd5e1; display: block; margin-bottom: 8px;"></i>
                        No gatepass requests recorded.
                    </div>
                @else
                    @foreach($recentGatepasses as $gp)
                        <div class="sp-item-row">
                            <div class="sp-item-left">
                                <h5>{{ $gp->reason ?: 'Official / Personal Departure' }}</h5>
                                <p>Pass #{{ $gp->pass_number }} • Out: {{ $gp->out_time ? \Carbon\Carbon::parse($gp->out_time)->format('h:i A') : 'Pending' }}</p>
                            </div>
                            <span class="sp-status-badge status-{{ strtolower($gp->status ?? 'pending') }}">
                                {{ ucfirst($gp->status ?? 'pending') }}
                            </span>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- Right: Notices & School Calendar Events --}}
        <div>
            {{-- Notices --}}
            <div class="sp-box">
                <div class="sp-box-hdr">
                    <div class="sp-box-title">
                        <i class="fas fa-bullhorn"></i>
                        <span>Announcements & Circulars</span>
                    </div>
                    <a href="{{ route('teacher.notices.index') }}" style="font-size: 12.5px; font-weight: 700; color: #2563eb; text-decoration: none;">
                        View All
                    </a>
                </div>
                @if($notices->isEmpty())
                    <div style="text-align: center; padding: 25px; color: #94a3b8; font-size: 13.5px;">
                        <i class="fas fa-info-circle" style="font-size: 32px; color: #cbd5e1; display: block; margin-bottom: 8px;"></i>
                        No active notices at this moment.
                    </div>
                @else
                    @foreach($notices as $notice)
                        <div class="sp-item-row">
                            <div class="sp-item-left">
                                <h5>{{ $notice->title }}</h5>
                                <p>{{ \Carbon\Carbon::parse($notice->date ?? $notice->created_at)->format('d M Y') }}</p>
                            </div>
                            <i class="fas fa-chevron-right" style="color: #94a3b8; font-size: 12px;"></i>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- Upcoming Events --}}
            <div class="sp-box">
                <div class="sp-box-hdr">
                    <div class="sp-box-title">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Upcoming Events & Holidays</span>
                    </div>
                </div>
                @if($upcomingEvents->isEmpty())
                    <div style="text-align: center; padding: 25px; color: #94a3b8; font-size: 13.5px;">
                        <i class="fas fa-calendar-check" style="font-size: 32px; color: #cbd5e1; display: block; margin-bottom: 8px;"></i>
                        No upcoming events scheduled.
                    </div>
                @else
                    @foreach($upcomingEvents as $evt)
                        <div class="sp-item-row">
                            <div class="sp-item-left">
                                <h5>{{ $evt->title }}</h5>
                                <p>{{ \Carbon\Carbon::parse($evt->start_date)->format('d M Y') }}</p>
                            </div>
                            <span class="sp-badge sp-badge-dept" style="color: #1e40af; background: #dbeafe;">
                                {{ ucfirst($evt->type ?? 'Event') }}
                            </span>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

    </div>

</div>
@endsection
