@extends('layouts.app')

@section('page-title', 'Transport Basics')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-bus" style="color:var(--gold);margin-right:8px;"></i>Transport Management Dashboard</h1>
        <p>Overview of school fleet operations, stops, routes, mapped student tracking, and maintenance logs</p>
    </div>
</div>

<!-- Fleet KPI Stats -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <!-- Vehicles Card -->
    <div class="card" style="border-left: 4px solid #2563eb; background: #ffffff;">
        <div class="card-body" style="display: flex; justify-content: space-between; align-items: center; padding: 24px;">
            <div>
                <div style="font-size: 13px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 4px;">Vehicles (Fleet)</div>
                <div style="font-size: 28px; font-weight: 800; color: #1e3a8a;">{{ $vehiclesCount }}</div>
            </div>
            <div style="font-size: 32px; color: #2563eb; background: #eff6ff; padding: 12px; border-radius: 12px;">
                <i class="fas fa-truck-monster"></i>
            </div>
        </div>
    </div>

    <!-- Routes Card -->
    <div class="card" style="border-left: 4px solid #10b981; background: #ffffff;">
        <div class="card-body" style="display: flex; justify-content: space-between; align-items: center; padding: 24px;">
            <div>
                <div style="font-size: 13px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 4px;">Active Routes</div>
                <div style="font-size: 28px; font-weight: 800; color: #10b981;">{{ $routesCount }}</div>
            </div>
            <div style="font-size: 32px; color: #10b981; background: #ecfdf5; padding: 12px; border-radius: 12px;">
                <i class="fas fa-route"></i>
            </div>
        </div>
    </div>

    <!-- Stops Card -->
    <div class="card" style="border-left: 4px solid #d97706; background: #ffffff;">
        <div class="card-body" style="display: flex; justify-content: space-between; align-items: center; padding: 24px;">
            <div>
                <div style="font-size: 13px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 4px;">Bus Stops</div>
                <div style="font-size: 28px; font-weight: 800; color: #d97706;">{{ $stopsCount }}</div>
            </div>
            <div style="font-size: 32px; color: #d97706; background: #fffbeb; padding: 12px; border-radius: 12px;">
                <i class="fas fa-map-marker-alt"></i>
            </div>
        </div>
    </div>

    <!-- Mapped Students Card -->
    <div class="card" style="border-left: 4px solid #8b5cf6; background: #ffffff;">
        <div class="card-body" style="display: flex; justify-content: space-between; align-items: center; padding: 24px;">
            <div>
                <div style="font-size: 13px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 4px;">Mapped Students</div>
                <div style="font-size: 28px; font-weight: 800; color: #8b5cf6;">{{ $mappedStudentsCount }}</div>
            </div>
            <div style="font-size: 32px; color: #8b5cf6; background: #f5f3ff; padding: 12px; border-radius: 12px;">
                <i class="fas fa-user-graduate"></i>
            </div>
        </div>
    </div>

    <!-- Total Expenses Card -->
    <div class="card" style="border-left: 4px solid #ef4444; background: #ffffff;">
        <div class="card-body" style="display: flex; justify-content: space-between; align-items: center; padding: 24px;">
            <div>
                <div style="font-size: 13px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 4px;">Expenses (INR)</div>
                <div style="font-size: 26px; font-weight: 800; color: #ef4444;">₹{{ number_format($totalExpenses, 2) }}</div>
            </div>
            <div style="font-size: 32px; color: #ef4444; background: #fef2f2; padding: 12px; border-radius: 12px;">
                <i class="fas fa-wallet"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid-2" style="margin-top: 20px;">
    <!-- Shortcuts -->
    <div class="card">
        <div class="card-hdr">
            <h3><i class="fas fa-cog" style="color:var(--gold);margin-right:6px;"></i>Quick Fleet Management Actions</h3>
        </div>
        <div class="card-body" style="padding: 24px;">
            <p style="font-size:13.5px; color:#64748b; margin-bottom:20px;">Easily jump to specific transport modules to update settings, manage buses, map routes, record attendance, and log expenses.</p>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <a href="{{ route('school.transport.vehicles') }}" class="btn btn-outline" style="padding:16px; border-radius:12px; flex-direction:column; text-align:center; gap:8px;">
                    <i class="fas fa-bus-alt" style="font-size:20px; color:#2563eb;"></i>
                    <strong style="color:var(--navy);font-size:13px;">Manage Vehicles</strong>
                </a>
                <a href="{{ route('school.transport.routes') }}" class="btn btn-outline" style="padding:16px; border-radius:12px; flex-direction:column; text-align:center; gap:8px;">
                    <i class="fas fa-route" style="font-size:20px; color:#10b981;"></i>
                    <strong style="color:var(--navy);font-size:13px;">Manage Routes</strong>
                </a>
                <a href="{{ route('school.transport.student-mapping') }}" class="btn btn-outline" style="padding:16px; border-radius:12px; flex-direction:column; text-align:center; gap:8px;">
                    <i class="fas fa-user-friends" style="font-size:20px; color:#8b5cf6;"></i>
                    <strong style="color:var(--navy);font-size:13px;">Student Mapping</strong>
                </a>
                <a href="{{ route('school.transport.bus-attendance') }}" class="btn btn-outline" style="padding:16px; border-radius:12px; flex-direction:column; text-align:center; gap:8px;">
                    <i class="fas fa-calendar-check" style="font-size:20px; color:#d97706;"></i>
                    <strong style="color:var(--navy);font-size:13px;">Mark Bus Attendance</strong>
                </a>
            </div>
        </div>
    </div>

    <!-- Fleet Status Card -->
    <div class="card">
        <div class="card-hdr">
            <h3><i class="fas fa-circle-info" style="color:var(--gold);margin-right:6px;"></i>System Status & Settings</h3>
        </div>
        <div class="card-body" style="padding: 24px;">
            <div style="display:flex; flex-direction:column; gap:16px;">
                <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #f1f5f9; padding-bottom:10px;">
                    <span style="font-weight:600; font-size:13px;">Transport Module Integration</span>
                    <span class="badge badge-success" style="background:#dcfce7; color:#15803d; padding:4px 10px; border-radius:12px; font-weight:700; font-size:11px;">ACTIVE</span>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #f1f5f9; padding-bottom:10px;">
                    <span style="font-weight:600; font-size:13px;">Basic Fees Integration Status</span>
                    <span class="badge badge-info" style="background:#e0f2fe; color:#0369a1; padding:4px 10px; border-radius:12px; font-weight:700; font-size:11px;">AUTO-SYNC ENABLED</span>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #f1f5f9; padding-bottom:10px;">
                    <span style="font-weight:600; font-size:13px;">Standard Dispatch Route</span>
                    <span style="font-size:13px; color:#64748b; font-weight:500;">School Main Campus Hub</span>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; padding-bottom:10px;">
                    <span style="font-weight:600; font-size:13px;">Support Desk Phone</span>
                    <span style="font-size:13px; color:#2563eb; font-weight:700;">+91 99887 76655</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
