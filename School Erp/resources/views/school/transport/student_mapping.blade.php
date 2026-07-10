@extends('layouts.app')
@section('page-title', 'Student Route Assignment')
@section('content')
@include('school.transport.partials.tp-styles')

<style>
/* Segmented Toggle Control */
.tp-toggle-wrap {
    display: flex;
    background: var(--page);
    border-radius: 12px;
    padding: 4px;
    border: 1px solid var(--border);
}
.tp-toggle-btn {
    flex: 1;
    padding: 10px 16px;
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
.tp-toggle-btn.active-yes {
    background: #16a34a !important;
    color: #fff !important;
    box-shadow: 0 4px 10px rgba(22, 163, 74, 0.2) !important;
}
.tp-toggle-btn.active-no {
    background: #ef4444 !important;
    color: #fff !important;
    box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2) !important;
}

/* Calendar Styles */
.tp-cal { border: 1px solid var(--border); border-radius: 12px; overflow: hidden; background: var(--white); }
.tp-cal-hdr { background: #1e3a8a; color: #fff; padding: 10px 14px; display: flex; align-items: center; justify-content: space-between; }
.tp-cal-hdr button { background: none; border: none; color: #fff; cursor: pointer; font-size: 18px; padding: 0 8px; font-weight: 700; }
.tp-cal-hdr span { font-weight: 700; font-size: 13.5px; letter-spacing: 0.5px; }
.tp-cal-days-hdr { display: grid; grid-template-columns: repeat(7, 1fr); background: var(--page); border-bottom: 1px solid var(--border); }
.tp-cal-dname { text-align: center; font-size: 11px; font-weight: 700; color: var(--t3); padding: 8px 2px; text-transform: uppercase; }
.tp-cal-grid  { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; padding: 6px; }
.tp-cal-day {
    text-align: center; padding: 8px 2px; font-size: 12px; cursor: pointer;
    border-radius: 8px; transition: all 0.15s ease; color: var(--t1);
    font-weight: 600;
}
.tp-cal-day:hover { background: #eff6ff; color: #2563eb; }
.tp-cal-day.selected { background: #2563eb; color: #fff; font-weight: 700; box-shadow: 0 4px 10px rgba(37,99,235,0.25); }
.tp-cal-day.today    { border: 2px solid #2563eb; font-weight: 700; }
.tp-cal-day.other-m  { color: var(--t3); opacity: 0.5; }
.tp-cal-day.weekend  { color: #94a3b8; }
</style>

<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-user-tag" style="color:var(--gold);margin-right:8px;"></i>Student Route Assignment</h1>
        <p>Assign routes, fares, pickup/drop locations, and calendar schedules</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

<!-- Fee policy banner -->
<div class="tp-alert-info">
    <i class="fas fa-info-circle"></i>
    <div>
        <strong style="font-size:14.5px;display:block;margin-bottom:4px;font-weight:700;">Fare Policy</strong>
        <span style="font-size:13px;opacity:.95;line-height:1.6;font-weight:500;">Transport fees are applied <strong>only to students with an assigned route (Yes)</strong>. Students marked <strong>No</strong> are never billed for transport.</span>
    </div>
</div>

<!-- ── Filters ───────────────────────────────────────────────── -->
<div class="card" style="margin-bottom:20px; border-radius:16px; border:1px solid var(--border); overflow:hidden;">
    <div class="card-body" style="padding:20px;">
        <form method="GET" action="{{ route('school.transport.student-mapping') }}" class="tp-filters">
            <div class="form-group">
                <label class="form-label">Class</label>
                <select name="class_id" class="form-control">
                    <option value="">All Classes</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id')==$c->id?'selected':'' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Section</label>
                <select name="section_id" class="form-control">
                    <option value="">All Sections</option>
                    @foreach($sections as $s)
                        <option value="{{ $s->id }}" {{ request('section_id')==$s->id?'selected':'' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Transport</label>
                <select name="transport_filter" class="form-control">
                    <option value="">All Students</option>
                    <option value="opted"     {{ request('transport_filter')==='opted'     ?'selected':'' }}>Opted In</option>
                    <option value="not_opted" {{ request('transport_filter')==='not_opted' ?'selected':'' }}>Not Opted</option>
                </select>
            </div>
            <div class="form-group" style="flex:1.5;min-width:180px;">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Name or admission no..." value="{{ request('search') }}">
            </div>
            <div style="display:flex; gap:8px; margin-bottom:18px;">
                <button type="submit" class="btn btn-gold" style="padding:10px 20px;"><i class="fa fa-filter"></i> Filter</button>
                <a href="{{ route('school.transport.student-mapping') }}" class="btn btn-outline" style="padding:10px 18px;">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- ── Students Table ──────────────────────────────────────────── -->
<div class="card" style="border-radius:16px; border:1px solid var(--border); overflow:hidden; margin-bottom: 24px;">
    <div class="tp-card-hdr">
        <h3>Students ({{ $students->count() }} / {{ $students->total() }})</h3>
        <span class="tp-badge tp-badge-purple">{{ $students->total() }} Total</span>
    </div>
    <div class="tp-scroll-hint">← Scroll to see all columns</div>
    <div class="tp-table-wrap" style="border:none; border-radius:0;">
        <table class="tp-table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Class</th>
                    <th style="text-align:center; width: 120px;">Transport</th>
                    <th>Route / Stop</th>
                    <th>Vehicle</th>
                    <th style="text-align:center; width: 140px;">Fares</th>
                    <th style="text-align:center; width: 160px;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($students as $st)
            <tr>
                <td>
                    <div style="font-weight:700; font-size:13.5px; color:var(--t1);">{{ $st->full_name }}</div>
                    <div style="font-size:11.5px; color:var(--t3); font-weight:500;">Adm: {{ $st->admission_number }}</div>
                </td>
                <td style="font-weight:600; color:var(--t2); white-space:nowrap;">{{ $st->class?->name }} – {{ $st->section?->name }}</td>
                <td style="text-align:center;">
                    @if($st->transport_opted && $st->transport_route)
                        <span class="tp-badge tp-badge-yes"><i class="fas fa-check-circle"></i> YES</span>
                    @else
                        <span class="tp-badge tp-badge-no"><i class="fas fa-times-circle"></i> NO</span>
                    @endif
                </td>
                <td style="min-width:160px;">
                    @if($st->transport_opted && $st->transport_route)
                        <div style="font-weight:700; font-size:13px; color:#16a34a; display:flex; align-items:center; gap:4px; margin-bottom:2px;">
                            <i class="fas fa-route" style="color:#6366f1;"></i>
                            <span>{{ $st->transport_route }}</span>
                        </div>
                        @if($st->transport_stop)
                            <div style="font-size:11.5px; color:var(--t3); display:flex; align-items:center; gap:4px; font-weight:500;">
                                <i class="fas fa-map-marker-alt" style="color:#d97706;"></i>
                                <span>{{ $st->transport_stop }}</span>
                            </div>
                        @endif
                    @else
                        <span style="color:var(--t3); font-size:12.5px; font-style:italic; font-weight:500;">Not assigned</span>
                    @endif
                </td>
                <td style="font-size:12.5px;">
                    @if($st->transport_opted)
                        <div style="color:var(--tp-pick); font-weight:700; margin-bottom:2px;"><i class="fas fa-arrow-right" style="font-size:11px;"></i> {{ $st->transport_vehicle_code ?: '—' }}</div>
                        <div style="color:var(--tp-drop); font-weight:700;"><i class="fas fa-arrow-left" style="font-size:11px;"></i> {{ $st->transport_drop_vehicle_code ?: '—' }}</div>
                    @else
                        <span style="color:var(--t3);">—</span>
                    @endif
                </td>
                <td style="text-align:center; white-space:nowrap;">
                    @if($st->transport_opted)
                        <div class="tp-badge tp-badge-pick" style="justify-content:center; margin-bottom:4px; min-width: 80px;">P: ₹{{ number_format($st->transport_pick_fare ?? 0,0) }}</div>
                        <div class="tp-badge tp-badge-drop" style="justify-content:center; min-width: 80px;">D: ₹{{ number_format($st->transport_drop_fare ?? 0,0) }}</div>
                    @else 
                        <span style="color:var(--t3);">—</span> 
                    @endif
                </td>
                <td style="text-align:center;">
                    <div style="display:flex; justify-content:center; gap:8px; align-items:center;">
                        <button class="btn btn-outline" style="padding:6px 12px; font-size:12px; font-weight:700;" onclick="openMappingModal({{ json_encode($st) }})">
                            <i class="fas fa-bus"></i> Assign
                        </button>
                        @if($st->transport_opted && $st->transport_route)
                            <a href="{{ route('school.transport.export-calendar', $st->id) }}" class="tp-btn-edit" title="Download iCal" style="text-decoration:none;">
                                <i class="fas fa-file-download"></i>
                            </a>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7"><div class="tp-empty"><i class="fas fa-user-friends"></i><p>No students found. Adjust filters.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px; border-top:1px solid var(--border);">{{ $students->links() }}</div>
</div>

<!-- ══════ ASSIGNMENT MODAL ══════════════════════════════════════ -->
<div class="tp-modal-overlay" id="mappingModal">
    <div class="tp-modal">
        <div class="tp-modal-hdr">
            <div>
                <h3 id="modalName" style="font-size:16px; font-weight:800; color:var(--t1);"></h3>
                <div id="modalAdm" style="font-size:12px; color:var(--t3); margin-top:2px; font-weight:600;"></div>
            </div>
            <button class="tp-modal-close" onclick="closeMappingModal()">&times;</button>
        </div>
        <div class="tp-modal-body">
            <form method="POST" action="{{ route('school.transport.student-mapping') }}" id="mappingForm">
                @csrf
                <input type="hidden" name="student_id" id="mStudentId">
                <input type="hidden" name="transport_calendar_start" id="mCalStart">

                <!-- Opted toggle -->
                <div style="margin-bottom:20px;">
                    <div class="tp-modal-section-title"><i class="fas fa-bus"></i> Transport Opted</div>
                    <div class="tp-toggle-wrap">
                        <button type="button" class="tp-toggle-btn" id="btnYes" onclick="setOpted(true)">
                            <i class="fas fa-check-circle"></i>Yes – Assign Route
                        </button>
                        <button type="button" class="tp-toggle-btn" id="btnNo" onclick="setOpted(false)">
                            <i class="fas fa-times-circle"></i>No Transport
                        </button>
                    </div>
                    <div id="optHint" style="text-align:center; font-size:12px; color:var(--t3); margin-top:6px; font-weight:500;">No route = no transport fee charged</div>
                </div>

                <!-- Transport details (shown when opted = Yes) -->
                <div id="tpDetails" style="display:none;">

                    <!-- Route + Stop -->
                    <div style="margin-bottom:20px;">
                        <div class="tp-modal-section-title"><i class="fas fa-route"></i> Route & Stop</div>
                        <div class="tp-g2" style="margin-bottom:12px;">
                            <div class="form-group" style="margin:0;">
                                <label class="form-label">Route <span style="color:var(--red);">*</span></label>
                                <select name="transport_route_id" id="mRouteId" class="form-control" onchange="onRoute(this)">
                                    <option value="">— Select —</option>
                                    @foreach($routes as $r)
                                        <option value="{{ $r->id }}" data-name="{{ $r->name }}" data-pick="{{ $r->pick_fare }}" data-drop="{{ $r->drop_fare }}">{{ $r->name }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="transport_route" id="mRoute">
                            </div>
                            <div class="form-group" style="margin:0;">
                                <label class="form-label">Stop</label>
                                <select name="transport_stop" id="mStop" class="form-control" onchange="onStop(this)">
                                    <option value="">— No Stop —</option>
                                    @foreach($stops as $s)
                                        <option value="{{ $s->name }}" data-pick="{{ $s->pick_fare ?? $s->fare }}" data-drop="{{ $s->drop_fare ?? 0 }}">{{ $s->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label class="form-label">Transport Month</label>
                            <input type="text" name="transport_month" id="mMonth" class="form-control" placeholder="e.g. July 2026">
                        </div>
                    </div>

                    <!-- Locations -->
                    <div style="margin-bottom:20px;">
                        <div class="tp-modal-section-title"><i class="fas fa-map-marked-alt"></i> Pickup & Drop Locations</div>
                        <div class="form-group" style="margin-bottom: 12px;">
                            <label class="form-label" style="color:var(--tp-pick);">Pickup Location / Address</label>
                            <input type="text" name="transport_pickup_location" id="mPickLoc" class="form-control" placeholder="Full pickup address">
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label class="form-label" style="color:var(--tp-drop);">Drop Location / Address</label>
                            <input type="text" name="transport_drop_location" id="mDropLoc" class="form-control" placeholder="Full drop address (or same as pickup)">
                        </div>
                    </div>

                    <!-- Vehicles -->
                    <div style="margin-bottom:20px;">
                        <div class="tp-modal-section-title"><i class="fas fa-bus"></i> Vehicle Assignment</div>
                        <div class="tp-g2">
                            <div class="form-group" style="margin:0;">
                                <label class="form-label" style="color:var(--tp-pick);">Pickup Vehicle</label>
                                <select name="transport_vehicle_code" id="mPickVeh" class="form-control">
                                    <option value="">None</option>
                                    @foreach($vehicles as $v)<option value="{{ $v->vehicle_no }}">{{ $v->vehicle_no }}</option>@endforeach
                                </select>
                            </div>
                            <div class="form-group" style="margin:0;">
                                <label class="form-label" style="color:var(--tp-drop);">Drop Vehicle</label>
                                <select name="transport_drop_vehicle_code" id="mDropVeh" class="form-control">
                                    <option value="">None</option>
                                    @foreach($vehicles as $v)<option value="{{ $v->vehicle_no }}">{{ $v->vehicle_no }}</option>@endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Fares -->
                    <div style="margin-bottom:20px;">
                        <div class="tp-modal-section-title"><i class="fas fa-rupee-sign"></i> Monthly Fares</div>
                        <div class="tp-g2" style="margin-bottom:12px;">
                            <div class="form-group" style="margin:0;">
                                <label class="form-label" style="color:var(--tp-pick);">Pickup Fare ₹/mo</label>
                                <input type="number" step="0.01" name="transport_pick_fare" id="mPickFare" class="form-control" value="0" min="0" oninput="calcFare()">
                            </div>
                            <div class="form-group" style="margin:0;">
                                <label class="form-label" style="color:var(--tp-drop);">Drop Fare ₹/mo</label>
                                <input type="number" step="0.01" name="transport_drop_fare" id="mDropFare" class="form-control" value="0" min="0" oninput="calcFare()">
                            </div>
                        </div>
                        <div class="tp-fare-total">
                            <span class="tp-fare-total-label">Total Monthly Fare</span>
                            <span class="tp-fare-total-val" id="mFareTotal">₹0.00</span>
                        </div>
                    </div>

                    <!-- Schedule + Calendar -->
                    <div style="margin-bottom:20px;">
                        <div class="tp-modal-section-title"><i class="fas fa-calendar-alt"></i> Schedule & Calendar Start</div>
                        <div class="tp-g2" style="margin-bottom:16px;">
                            <div class="form-group" style="margin:0;">
                                <label class="form-label" style="color:var(--tp-pick);">Pickup Time</label>
                                <input type="time" name="transport_pickup_time" id="mPickTime" class="form-control" value="07:30">
                            </div>
                            <div class="form-group" style="margin:0;">
                                <label class="form-label" style="color:var(--tp-drop);">Drop Time</label>
                                <input type="time" name="transport_drop_time" id="mDropTime" class="form-control" value="15:00">
                            </div>
                        </div>
                        <!-- Mini calendar -->
                        <label class="form-label" style="margin-bottom:8px;"><i class="fas fa-calendar" style="margin-right:4px;color:#2563eb;"></i>Transport Start Date</label>
                        <div class="tp-cal">
                            <div class="tp-cal-hdr">
                                <button type="button" onclick="calNav(-1)">&#8249;</button>
                                <span id="calLabel"></span>
                                <button type="button" onclick="calNav(1)">&#8250;</button>
                            </div>
                            <div class="tp-cal-days-hdr">
                                <div class="tp-cal-dname">Su</div><div class="tp-cal-dname">Mo</div><div class="tp-cal-dname">Tu</div>
                                <div class="tp-cal-dname">We</div><div class="tp-cal-dname">Th</div><div class="tp-cal-dname">Fr</div><div class="tp-cal-dname">Sa</div>
                            </div>
                            <div class="tp-cal-grid" id="calGrid"></div>
                        </div>
                        <div style="text-align:center;font-size:12.5px;color:var(--t2);margin-top:8px;font-weight:600;">
                            Selected: <span id="calDisplay" style="color:#2563eb;font-weight:800;">None</span>
                        </div>
                    </div>
                </div>

                <div style="display:flex;justify-content:flex-end;gap:10px;padding-top:10px;border-top:1px solid var(--border);margin-top:12px;">
                    <button type="button" class="btn btn-outline" onclick="closeMappingModal()">Cancel</button>
                    <button type="submit" class="btn btn-gold"><i class="fa fa-save"></i> Save Assignment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// ── Modal open/close ─────────────────────────────────────────
function openMappingModal(s) {
    document.getElementById('modalName').textContent = (s.first_name || '') + ' ' + (s.last_name || '');
    document.getElementById('modalAdm').textContent  = 'Admission: ' + s.admission_number;
    document.getElementById('mStudentId').value  = s.id;
    document.getElementById('mMonth').value      = s.transport_month || '';
    document.getElementById('mPickLoc').value    = s.transport_pickup_location || '';
    document.getElementById('mDropLoc').value    = s.transport_drop_location   || '';
    document.getElementById('mPickTime').value   = s.transport_pickup_time || '07:30';
    document.getElementById('mDropTime').value   = s.transport_drop_time   || '15:00';
    document.getElementById('mPickFare').value   = s.transport_pick_fare  || 0;
    document.getElementById('mDropFare').value   = s.transport_drop_fare  || 0;
    setVal('mRouteId', s.transport_route_id);
    document.getElementById('mRoute').value = s.transport_route || '';
    setVal('mStop',    s.transport_stop);
    setVal('mPickVeh', s.transport_vehicle_code);
    setVal('mDropVeh', s.transport_drop_vehicle_code);
    calcFare();
    // Calendar
    if (s.transport_calendar_start) {
        calSelected = new Date(s.transport_calendar_start + 'T00:00:00');
        document.getElementById('mCalStart').value = s.transport_calendar_start;
        document.getElementById('calDisplay').textContent = calSelected.toLocaleDateString('en-IN',{day:'2-digit',month:'short',year:'numeric'});
    } else {
        calSelected = null;
        document.getElementById('mCalStart').value = '';
        document.getElementById('calDisplay').textContent = 'None';
    }
    renderCal();
    const opted = !!(s.transport_opted && s.transport_route);
    setOpted(opted);
    
    // Add open class to overlay
    const modal = document.getElementById('mappingModal');
    modal.classList.add('open');
    document.body.classList.add('modal-open');
}

function closeMappingModal() {
    const modal = document.getElementById('mappingModal');
    modal.classList.remove('open');
    document.body.classList.remove('modal-open');
}

// Click outside close
document.addEventListener('DOMContentLoaded', function() {
    const m = document.getElementById('mappingModal');
    if (m) {
        m.addEventListener('click', e => { if(e.target===m) closeMappingModal(); });
    }
});

function setVal(id, val) { const el=document.getElementById(id); if(el) el.value=val||''; }

function setOpted(yes) {
    const btnYes = document.getElementById('btnYes');
    const btnNo = document.getElementById('btnNo');
    if (yes) {
        btnYes.className = 'tp-toggle-btn active-yes';
        btnNo.className  = 'tp-toggle-btn';
        document.getElementById('tpDetails').style.display = 'block';
    } else {
        btnYes.className = 'tp-toggle-btn';
        btnNo.className  = 'tp-toggle-btn active-no';
        document.getElementById('tpDetails').style.display = 'none';
        document.getElementById('mRouteId').value = '';
        document.getElementById('mRoute').value = '';
    }
}

function onRoute(sel) {
    const o = sel.options[sel.selectedIndex];
    document.getElementById('mRoute').value = o.dataset.name || '';
    if(o.dataset.pick) document.getElementById('mPickFare').value = parseFloat(o.dataset.pick).toFixed(2);
    if(o.dataset.drop) document.getElementById('mDropFare').value = parseFloat(o.dataset.drop).toFixed(2);
    calcFare();
}
function onStop(sel) {
    const o = sel.options[sel.selectedIndex];
    if(o.dataset.pick) document.getElementById('mPickFare').value = parseFloat(o.dataset.pick).toFixed(2);
    if(o.dataset.drop) document.getElementById('mDropFare').value = parseFloat(o.dataset.drop).toFixed(2);
    calcFare();
}
function calcFare() {
    const p = parseFloat(document.getElementById('mPickFare').value) || 0;
    const d = parseFloat(document.getElementById('mDropFare').value) || 0;
    document.getElementById('mFareTotal').textContent = '₹' + (p+d).toFixed(2);
}

// ── Mini calendar ────────────────────────────────────────────
const MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];
let calY = new Date().getFullYear(), calM = new Date().getMonth(), calSelected = null;
function renderCal() {
    document.getElementById('calLabel').textContent = MONTHS[calM] + ' ' + calY;
    const today = new Date(), first = new Date(calY,calM,1), last = new Date(calY,calM+1,0);
    let html = '';
    for(let i=0;i<first.getDay();i++) {
        const d=new Date(calY,calM,-(first.getDay()-i-1));
        html+=`<div class="tp-cal-day other-m">${d.getDate()}</div>`;
    }
    for(let d=1;d<=last.getDate();d++) {
        const dt=new Date(calY,calM,d);
        let cls='tp-cal-day';
        if(dt.getDay()===0||dt.getDay()===6) cls+=' weekend';
        if(dt.toDateString()===today.toDateString()) cls+=' today';
        if(calSelected && dt.toDateString()===calSelected.toDateString()) cls+=' selected';
        html+=`<div class="${cls}" onclick="pickDay(${calY},${calM+1},${d})">${d}</div>`;
    }
    document.getElementById('calGrid').innerHTML = html;
}
function pickDay(y,m,d) {
    const pad=n=>String(n).padStart(2,'0');
    const s=`${y}-${pad(m)}-${pad(d)}`;
    calSelected=new Date(y,m-1,d);
    document.getElementById('mCalStart').value=s;
    document.getElementById('calDisplay').textContent=calSelected.toLocaleDateString('en-IN',{day:'2-digit',month:'short',year:'numeric'});
    renderCal();
}
function calNav(dir) {
    calM+=dir;
    if(calM>11){calM=0;calY++;}
    else if(calM<0){calM=11;calY--;}
    renderCal();
}
renderCal();
</script>
@endsection

