@extends('layouts.app')
@section('page-title', 'Transport Dashboard')
@section('content')
@include('school.transport.partials.tp-styles')

<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-bus-alt" style="color:var(--gold);margin-right:8px;"></i>Transport Hub</h1>
        <p>Fleet overview, student assignments, routes, and maintenance at a glance</p>
    </div>
    <div class="page-hdr-right">
        <a href="{{ route('school.transport.vehicles') }}" class="btn btn-gold"><i class="fas fa-plus"></i><span>Add Vehicle</span></a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

<!-- ── KPI Stats ────────────────────────────────────────────── -->
<div class="tp-stats">
    <div class="tp-stat" style="--sc:#2563eb;--sb:#eff6ff;">
        <div class="tp-stat-icon"><i class="fas fa-bus-alt"></i></div>
        <div>
            <div class="tp-stat-label">Fleet</div>
            <div class="tp-stat-val">{{ $vehiclesCount }}</div>
        </div>
    </div>
    <div class="tp-stat" style="--sc:#1d4ed8;--sb:#dbeafe;">
        <div class="tp-stat-icon"><i class="fas fa-route"></i></div>
        <div>
            <div class="tp-stat-label">Routes</div>
            <div class="tp-stat-val">{{ $routesCount }}</div>
        </div>
    </div>
    <div class="tp-stat" style="--sc:#0ea5e9;--sb:#e0f2fe;">
        <div class="tp-stat-icon"><i class="fas fa-map-marker-alt"></i></div>
        <div>
            <div class="tp-stat-label">Stops</div>
            <div class="tp-stat-val">{{ $stopsCount }}</div>
        </div>
    </div>
    <div class="tp-stat" style="--sc:#7c3aed;--sb:#f5f3ff;">
        <div class="tp-stat-icon"><i class="fas fa-user-graduate"></i></div>
        <div>
            <div class="tp-stat-label">Students</div>
            <div class="tp-stat-val">{{ $mappedStudentsCount }}</div>
        </div>
    </div>
    <div class="tp-stat" style="--sc:#0ea5e9;--sb:#e0f2fe;">
        <div class="tp-stat-icon"><i class="fas fa-wallet"></i></div>
        <div>
            <div class="tp-stat-label">Expenses</div>
            <div class="tp-stat-val" style="font-size:18px;">₹{{ number_format($totalExpenses,0) }}</div>
        </div>
    </div>
    <div class="tp-stat" style="--sc:#2563eb;--sb:#eff6ff;">
        <div class="tp-stat-icon"><i class="fas fa-calendar-check"></i></div>
        <div>
            <div class="tp-stat-label">Trips</div>
            <div class="tp-stat-val">{{ $tripsCount }}</div>
        </div>
    </div>
</div>

<!-- ── Quick Actions + Recent Expenses ────────────────────────── -->
<div class="grid-2" style="margin-bottom: 24px;">
    <!-- Quick actions -->
    <div class="card" style="border-radius:16px; border:1px solid var(--border); overflow:hidden;">
        <div class="tp-card-hdr">
            <h3><i class="fas fa-bolt" style="color:var(--gold);margin-right:6px;"></i>Quick Actions</h3>
        </div>
        <div class="card-body" style="padding:20px;">
            <div class="tp-quick-grid">
                <a href="{{ route('school.transport.vehicles') }}" class="tp-quick-btn" style="--qc:#2563eb;--qbg:#eff6ff;">
                    <i class="fas fa-bus-alt"></i><span>Vehicles</span>
                </a>
                <a href="{{ route('school.transport.routes') }}" class="tp-quick-btn" style="--qc:#16a34a;--qbg:#f0fdf4;">
                    <i class="fas fa-route"></i><span>Routes</span>
                </a>
                <a href="{{ route('school.transport.stops') }}" class="tp-quick-btn" style="--qc:#d97706;--qbg:#fffbeb;">
                    <i class="fas fa-map-marker-alt"></i><span>Stops</span>
                </a>
                <a href="{{ route('school.transport.student-mapping') }}" class="tp-quick-btn" style="--qc:#7c3aed;--qbg:#f5f3ff;">
                    <i class="fas fa-user-tag"></i><span>Assign Students</span>
                </a>
                <a href="{{ route('school.transport.bus-attendance') }}" class="tp-quick-btn" style="--qc:#0ea5e9;--qbg:#e0f2fe;">
                    <i class="fas fa-calendar-check"></i><span>Bus Attendance</span>
                </a>
                <a href="{{ route('school.transport.expenses') }}" class="tp-quick-btn" style="--qc:#ef4444;--qbg:#fef2f2;">
                    <i class="fas fa-receipt"></i><span>Expenses</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent expenses -->
    <div class="card" style="border-radius:16px; border:1px solid var(--border); overflow:hidden;">
        <div class="tp-card-hdr">
            <h3><i class="fas fa-receipt" style="color:var(--gold);margin-right:6px;"></i>Recent Expenses</h3>
            <a href="{{ route('school.transport.expenses') }}" style="font-size:13px;color:#2563eb;font-weight:700;text-decoration:none;transition: color 0.15s;" onmouseover="this.style.color='#1d4ed8'" onmouseout="this.style.color='#2563eb'">View All →</a>
        </div>
        <div class="card-body" style="padding:0;">
            @forelse($recentExpenses as $ex)
            <div style="display:flex;align-items:center;gap:14px;padding:14px 20px;border-bottom:1px solid var(--border); transition: background-color 0.15s;" onmouseover="this.style.backgroundColor='rgba(37,99,235,0.01)'" onmouseout="this.style.backgroundColor='transparent'">
                <div style="width:40px;height:40px;border-radius:10px;background:#fef2f2;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas fa-gas-pump" style="color:#ef4444;font-size:16px;"></i>
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:700;font-size:13.5px;color:var(--t1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:2px;">{{ $ex->expense_type }}</div>
                    <div style="font-size:12px;color:var(--t2);font-weight:500;">{{ $ex->vehicle?->vehicle_no }} &middot; {{ $ex->date->format('d M, Y') }}</div>
                </div>
                <div style="font-weight:800;font-size:15px;color:#ef4444;white-space:nowrap;">₹{{ number_format($ex->amount,0) }}</div>
            </div>
            @empty
            <div class="tp-empty"><i class="fas fa-receipt"></i><p>No expenses logged yet</p></div>
            @endforelse
        </div>
    </div>
</div>

<!-- ── Transport Fee Policy Banner ─────────────────────────────── -->
<div class="tp-alert-info">
    <i class="fas fa-info-circle"></i>
    <div>
        <strong style="font-size:14.5px;display:block;margin-bottom:4px;font-weight:700;">Transport Fee Policy — Opt-In Only</strong>
        <span style="font-size:13px;opacity:.95;line-height:1.6;font-weight:500;">
            Transport fees are <strong>only charged to students with an assigned route</strong>.
            Students without a route assignment are never billed for transport.
            Assign routes in <a href="{{ route('school.transport.student-mapping') }}" style="color:#fff;text-decoration:underline;font-weight:700;transition:opacity 0.15s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">Student Route Mapping →</a>
        </span>
    </div>
</div>

@endsection

