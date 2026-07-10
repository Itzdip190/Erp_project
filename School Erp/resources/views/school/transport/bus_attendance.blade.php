@extends('layouts.app')
@section('page-title', 'Bus Attendance')
@section('content')
@include('school.transport.partials.tp-styles')

<style>
/* Segmented Toggle for Trip Direction */
.tp-toggle-wrap {
    display: flex;
    background: var(--page);
    border-radius: 12px;
    padding: 4px;
    border: 1px solid var(--border);
}
.tp-toggle-btn {
    flex: 1;
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 700;
    border: none;
    cursor: pointer;
    background: transparent;
    color: var(--t2);
    border-radius: 9px;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}
.tp-toggle-btn:hover {
    color: var(--t1);
}
.tp-toggle-btn.active-trip {
    background: #2563eb !important;
    color: #fff !important;
    box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25) !important;
}

#attTable tr {
    transition: background-color 0.25s ease;
}
#attTable tr:hover td {
    background: rgba(37, 99, 235, 0.02) !important;
}

/* Custom Premium Toggle Switch */
.tp-switch {
    position: relative;
    display: inline-block;
    width: 52px;
    height: 28px;
    vertical-align: middle;
}
.tp-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
.tp-slider {
    position: absolute;
    cursor: pointer;
    inset: 0;
    background-color: #cbd5e1;
    border-radius: 28px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.tp-slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    border-radius: 50%;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    box-shadow: 0 2px 5px rgba(0,0,0,0.15);
    z-index: 2;
}
.tp-slider::after {
    position: absolute;
    content: "✓";
    font-size: 12px;
    font-weight: bold;
    color: #fff;
    left: 9px;
    top: 4px;
    opacity: 0;
    transition: opacity 0.2s ease;
    z-index: 1;
}
.tp-switch input:checked + .tp-slider {
    background-color: #22c55e;
}
.tp-switch input:checked + .tp-slider:before {
    transform: translateX(24px);
}
.tp-switch input:checked + .tp-slider::after {
    opacity: 1;
}

/* Switch Pop Animation */
@keyframes popScale {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}
.tp-switch input:active + .tp-slider:before {
    transform: scale(1.15) translateX(var(--tx, 0));
}

/* Present vs Absent Row Styles with border animations */
#attTable td:first-child {
    border-left: 4px solid transparent;
    transition: border-left-color 0.25s ease;
}
tr.att-present {
    background-color: rgba(34, 197, 94, 0.04) !important;
}
tr.att-present td:first-child {
    border-left-color: #22c55e !important;
}
tr.att-absent {
    background-color: rgba(239, 68, 68, 0.02) !important;
}
tr.att-absent td:first-child {
    border-left-color: #ef4444 !important;
}

/* Success/Check pulse animation */
.pulse-ring {
    position: absolute;
    inset: -6px;
    border: 3px solid #22c55e;
    border-radius: 50%;
    opacity: 0;
    z-index: 0;
    pointer-events: none;
}
.tp-switch input:checked + .tp-slider .pulse-ring {
    animation: pulseRing 0.6s cubic-bezier(0.24, 0, 0.38, 1);
}
@keyframes pulseRing {
    0% { transform: scale(0.6); opacity: 1; }
    100% { transform: scale(1.4); opacity: 0; }
}

</style>

<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-calendar-check" style="color:var(--gold);margin-right:8px;"></i>Bus Attendance</h1>
        <p>Daily boarding records for transport-opted students</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

<!-- ── Controls bar ────────────────────────────────────────────── -->
<div class="card" style="margin-bottom:20px; border-radius:16px; border:1px solid var(--border); overflow:hidden;">
    <div class="card-body" style="padding:16px 20px;">
        <form method="GET" action="{{ route('school.transport.bus-attendance') }}" id="filterForm">
            <div style="display:flex; flex-wrap:wrap; gap:16px; align-items:center;">
                <!-- Date nav -->
                @php $prev = date('Y-m-d',strtotime($selectedDate.' -1 day')); $next = date('Y-m-d',strtotime($selectedDate.' +1 day')); @endphp
                <div class="tp-date-nav">
                    <a href="?date={{ $prev }}&trip_type={{ $selectedTripType }}" class="tp-nav-arrow">&#8249;</a>
                    <input type="date" name="date" class="form-control" value="{{ $selectedDate }}" style="width:160px; font-weight:600;" onchange="this.form.submit()">
                    <a href="?date={{ $next }}&trip_type={{ $selectedTripType }}" class="tp-nav-arrow">&#8250;</a>
                    <a href="?date={{ date('Y-m-d') }}&trip_type={{ $selectedTripType }}" class="btn btn-outline" style="padding:6px 12px; font-size:12px; font-weight:700;">Today</a>
                </div>

                <!-- Trip type toggle -->
                <div class="tp-toggle-wrap" style="width:auto; min-width: 200px;">
                    <button type="button" class="tp-toggle-btn {{ $selectedTripType==='pickup' ? 'active-trip' : '' }}" onclick="switchTrip('pickup')">
                        <i class="fas fa-arrow-right"></i> Pickup
                    </button>
                    <button type="button" class="tp-toggle-btn {{ $selectedTripType==='drop' ? 'active-trip' : '' }}" onclick="switchTrip('drop')">
                        <i class="fas fa-arrow-left"></i> Drop
                    </button>
                </div>
                <input type="hidden" name="trip_type" id="tripTypeHidden" value="{{ $selectedTripType }}">

                <!-- Stats -->
                @php
                    $presentCount = count(array_filter($savedRecords, fn($s)=>$s==='present'));
                    $total        = count($students);
                    $absentCount  = $total - $presentCount;
                @endphp
                <div style="margin-left:auto; display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                    <span class="tp-badge tp-badge-yes" style="font-size:12px; padding:6px 14px; border-radius:10px; font-weight:700;">
                        <i class="fas fa-check-circle"></i> {{ $presentCount }} Present
                    </span>
                    <span class="tp-badge tp-badge-no" style="font-size:12px; padding:6px 14px; border-radius:10px; font-weight:700; background:#fef2f2; color:#ef4444;">
                        <i class="fas fa-times-circle"></i> {{ $absentCount }} Absent
                    </span>
                    <span class="tp-badge tp-badge-purple" style="font-size:12px; padding:6px 14px; border-radius:10px; font-weight:700;">
                        {{ $total }} Total
                    </span>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ── Attendance table ────────────────────────────────────────── -->
<div class="card" style="border-radius:16px; border:1px solid var(--border); overflow:hidden; margin-bottom: 24px;">
    <div class="tp-card-hdr">
        <h3 style="display:flex; align-items:center; gap:8px;">
            <i class="far fa-calendar-alt" style="color:var(--gold);"></i>
            <span style="font-weight:800; color:var(--t1);">{{ date('d M, Y',strtotime($selectedDate)) }}</span>
            <span style="color:var(--t3); font-weight:400;">&middot;</span>
            <span class="tp-badge {{ $selectedTripType==='pickup' ? 'tp-trip-pick' : 'tp-trip-drop' }}" style="font-size:12px; padding:4px 12px; border-radius:10px;">
                <i class="fas {{ $selectedTripType==='pickup' ? 'fa-arrow-right' : 'fa-arrow-left' }}"></i> {{ ucfirst($selectedTripType) }} Direction
            </span>
        </h3>
        @if(count($students)>0)
        <div style="display:flex; gap:8px;">
            <button type="button" class="btn btn-outline" style="padding:6px 12px; font-size:12px; font-weight:700; color:#16a34a; border-color:#bbf7d0; background:#f0fdf4;" onclick="markAll(true)"><i class="fas fa-check"></i> All Present</button>
            <button type="button" class="btn btn-outline" style="padding:6px 12px; font-size:12px; font-weight:700; color:#ef4444; border-color:#fecaca; background:#fef2f2;" onclick="markAll(false)"><i class="fas fa-times"></i> All Absent</button>
        </div>
        @endif
    </div>
    
    <form method="POST" action="{{ route('school.transport.bus-attendance') }}?date={{ $selectedDate }}&trip_type={{ $selectedTripType }}">
        @csrf
        <div class="tp-scroll-hint">← Scroll to see all columns</div>
        <div class="tp-table-wrap" style="border:none; border-radius:0;">
            <table class="tp-table">
                <thead>
                    <tr>
                        <th style="text-align:center; width: 100px;">Status</th>
                        <th>Student</th>
                        <th>Class</th>
                        <th>Route</th>
                        <th>Stop</th>
                        <th style="text-align:center; width: 120px;">Vehicle</th>
                        <th>{{ $selectedTripType==='pickup' ? 'Pickup Location' : 'Drop Location' }}</th>
                    </tr>
                </thead>
                <tbody id="attTable">
                @forelse($students as $st)
                    @php $status = $savedRecords[$st->id] ?? 'absent'; @endphp
                    <tr class="{{ $status==='present' ? 'att-present' : 'att-absent' }}" id="row-{{ $st->id }}">
                        <td style="text-align:center; vertical-align: middle;">
                            <label class="tp-switch">
                                <input type="checkbox" name="attendance[{{ $st->id }}]" value="present"
                                    class="att-chk" {{ $status==='present' ? 'checked' : '' }}
                                    onchange="rowStyle('{{ $st->id }}',this.checked)">
                                <span class="tp-slider"><span class="pulse-ring"></span></span>
                            </label>
                        </td>
                        <td>
                            <div style="font-weight:700; font-size:13.5px; color:var(--t1);">{{ $st->full_name }}</div>
                            <div style="font-size:11.5px; color:var(--t3); font-weight:500;">Adm: {{ $st->admission_number }}</div>
                        </td>
                        <td style="font-size:13px; font-weight:600; color:var(--t2); white-space:nowrap;">{{ $st->class?->name }} – {{ $st->section?->name }}</td>
                        <td style="font-size:13px; font-weight:600; color:var(--t1);">
                            <div style="display:flex; align-items:center; gap:6px;">
                                <i class="fas fa-route" style="color:#6366f1;"></i>
                                <span>{{ $st->transport_route }}</span>
                            </div>
                        </td>
                        <td style="font-size:12.5px; color:var(--t2); font-weight:500;">{{ $st->transport_stop ?: '—' }}</td>
                        <td style="text-align:center; font-weight:700; color:#2563eb; font-size:13px;">
                            <span class="tp-plate" style="font-size:11.5px; padding:4px 10px;">{{ $selectedTripType==='pickup' ? ($st->transport_vehicle_code??'—') : ($st->transport_drop_vehicle_code??'—') }}</span>
                        </td>
                        <td style="font-size:12.5px; color:var(--t2); font-weight:500;">
                            @if($selectedTripType==='pickup')
                                {{ $st->transport_pickup_location ?: ($st->transport_stop ?: '—') }}
                            @else
                                {{ $st->transport_drop_location ?: ($st->transport_stop ?: '—') }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="tp-empty">
                                <i class="fas fa-bus"></i>
                                <p>No transport-opted students found.</p>
                                <a href="{{ route('school.transport.student-mapping') }}" class="btn btn-gold" style="margin-top:16px;"><i class="fas fa-user-tag"></i> Assign Students</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if(count($students)>0)
        <div style="padding:16px 20px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:12px; align-items:center;">
            <span style="font-size:13px; color:var(--t2); font-weight:500;">{{ date('d M, Y',strtotime($selectedDate)) }} &middot; {{ ucfirst($selectedTripType) }}</span>
            <button type="submit" class="btn btn-gold"><i class="fa fa-save"></i> Save Attendance</button>
        </div>
        @endif
    </form>
</div>

<script>
function switchTrip(type) {
    document.getElementById('tripTypeHidden').value = type;
    document.getElementById('filterForm').submit();
}
function rowStyle(id, present) {
    const row = document.getElementById('row-'+id);
    if(row) {
        row.className = present ? 'att-present' : 'att-absent';
    }
}
function markAll(present) {
    document.querySelectorAll('.att-chk').forEach(c => {
        c.checked = present;
        // Parse ID from name, which is formatted like name="attendance[123]"
        const match = c.name.match(/\[(\d+)\]/);
        if (match && match[1]) {
            rowStyle(match[1], present);
        }
    });
}
</script>
@endsection

