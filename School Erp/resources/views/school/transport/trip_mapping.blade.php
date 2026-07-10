@extends('layouts.app')
@section('page-title', 'Trip Mapping')
@section('content')
@include('school.transport.partials.tp-styles')

<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-network-wired" style="color:var(--gold);margin-right:8px;"></i>Vehicle Trip Mapping</h1>
        <p>Assign vehicles to routes with pickup and drop schedule times</p>
    </div>
    <div class="page-hdr-right">
        <button class="btn btn-gold" onclick="openAddModal()"><i class="fas fa-plus"></i><span>Map Trip</span></button>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

<!-- ── Trips Table ── -->
<div class="card" style="border-radius:16px; border:1px solid var(--border); overflow:hidden; margin-bottom: 24px;">
    <div class="tp-card-hdr">
        <h3>Scheduled Trips</h3>
        <span class="tp-badge tp-badge-gold">{{ $trips->count() }} Trip{{ $trips->count()!=1?'s':'' }}</span>
    </div>
    <div class="tp-scroll-hint">← Scroll to see all columns</div>
    <div class="tp-table-wrap" style="border:none; border-radius:0;">
        <table class="tp-table">
            <thead>
                <tr>
                    <th style="width: 60px;">#</th>
                    <th>Trip Name</th>
                    <th>Route</th>
                    <th>Vehicle</th>
                    <th style="text-align:center; width: 130px;">Direction</th>
                    <th style="text-align:center; width: 220px;">Schedule Time</th>
                    <th style="text-align:center; width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($trips as $i => $t)
            <tr>
                <td style="color:var(--t3);font-weight:600;">{{ $i+1 }}</td>
                <td>
                    <div style="font-weight:700; color:var(--t1);">{{ $t->trip_name }}</div>
                </td>
                <td style="font-size:13px; color:var(--t2); font-weight:500;">
                    <div style="display:flex; align-items:center; gap:6px;">
                        <i class="fas fa-route" style="color:#6366f1;"></i>
                        <span>{{ $t->route?->name ?? '—' }}</span>
                    </div>
                </td>
                <td>
                    <span class="tp-plate" style="font-size:11.5px; padding: 4px 10px;">{{ $t->vehicle?->vehicle_no ?? '—' }}</span>
                </td>
                <td style="text-align:center;">
                    @if($t->type==='pickup') 
                        <span class="tp-badge tp-trip-pick"><i class="fas fa-arrow-right"></i> Pickup</span>
                    @elseif($t->type==='drop')  
                        <span class="tp-badge tp-trip-drop"><i class="fas fa-arrow-left"></i> Drop</span>
                    @else                        
                        <span class="tp-badge tp-trip-both"><i class="fas fa-exchange-alt"></i> Both</span>
                    @endif
                </td>
                <td style="text-align:center; font-size:13px; color:var(--t2); font-weight:600; white-space:nowrap;">
                    @if($t->start_time || $t->end_time)
                        <i class="far fa-clock" style="margin-right:4px; color:#2563eb;"></i>{{ \Carbon\Carbon::parse($t->start_time)->format('h:i A') }} &rarr; {{ \Carbon\Carbon::parse($t->end_time)->format('h:i A') }}
                    @else
                        —
                    @endif
                </td>
                <td style="text-align:center;">
                    <div style="display:flex; justify-content:center; gap:8px; align-items:center;">
                        <button class="tp-btn-edit" onclick="tripEdit({{ json_encode($t) }})" title="Edit"><i class="fa fa-edit"></i></button>
                        <form action="{{ route('school.transport.delete') }}" method="POST" style="display:inline; margin:0;" onsubmit="return confirm('Delete this trip?')">
                            @csrf 
                            <input type="hidden" name="id" value="{{ $t->id }}">
                            <input type="hidden" name="type" value="trip">
                            <button type="submit" class="tp-btn-del" title="Delete"><i class="fa fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">
                    <div class="tp-empty"><i class="fas fa-network-wired"></i><p>No trips scheduled yet. Use the button above to schedule a trip.</p></div>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ── Trip Mapping Form Modal ── -->
<div class="tp-modal-overlay" id="tripModal">
    <div class="tp-modal">
        <div class="tp-modal-hdr">
            <h3 id="formTitle"><i class="fas fa-plus-circle" style="color:var(--gold);margin-right:6px;"></i>Map Trip</h3>
            <button class="tp-modal-close" onclick="closeModal('tripModal')">&times;</button>
        </div>
        <div class="tp-modal-body">
            <form method="POST" action="{{ route('school.transport.trip-mapping') }}" id="tripForm">
                @csrf
                <input type="hidden" name="id" id="tripId">
                
                <div class="form-group">
                    <label class="form-label">Trip Name <span style="color:var(--red);">*</span></label>
                    <input type="text" name="trip_name" id="tripName" class="form-control" placeholder="e.g. Morning Pickup – Route A" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Vehicle <span style="color:var(--red);">*</span></label>
                    <select name="vehicle_id" id="tripVehicle" class="form-control" required>
                        <option value="">— Select Vehicle —</option>
                        @foreach($vehicles as $v)
                        <option value="{{ $v->id }}">{{ $v->vehicle_no }} ({{ $v->driver_name ?: 'No Driver' }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Route <span style="color:var(--red);">*</span></label>
                    <select name="route_id" id="tripRoute" class="form-control" required>
                        <option value="">— Select Route —</option>
                        @foreach($routes as $r)
                        <option value="{{ $r->id }}">{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Direction <span style="color:var(--red);">*</span></label>
                    <select name="type" id="tripType" class="form-control" required>
                        <option value="pickup">🔵 Pickup (Morning)</option>
                        <option value="drop">🟠 Drop (Afternoon)</option>
                        <option value="both">🟢 Both Directions</option>
                    </select>
                </div>
                
                <div class="tp-g2">
                    <div class="form-group">
                        <label class="form-label" style="color:var(--tp-pick);">Start Time</label>
                        <input type="time" name="start_time" id="tripStart" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="color:var(--tp-drop);">End Time</label>
                        <input type="time" name="end_time" id="tripEnd" class="form-control">
                    </div>
                </div>
                
                <div style="display:flex;gap:10px;margin-top:20px;">
                    <button type="submit" class="btn btn-gold" style="flex:2;justify-content:center;"><i class="fa fa-save"></i> Save Trip</button>
                    <button type="button" class="btn btn-outline" style="flex:1;justify-content:center;" onclick="closeModal('tripModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddModal() {
    tripReset();
    openModal('tripModal');
}

function tripEdit(t) {
    document.getElementById('formTitle').innerHTML = '<i class="fas fa-edit" style="color:var(--gold);margin-right:6px;"></i>Edit Trip';
    document.getElementById('tripId').value      = t.id;
    document.getElementById('tripName').value    = t.trip_name;
    document.getElementById('tripVehicle').value = t.vehicle_id;
    document.getElementById('tripRoute').value   = t.route_id;
    document.getElementById('tripType').value    = t.type;
    document.getElementById('tripStart').value   = t.start_time || '';
    document.getElementById('tripEnd').value     = t.end_time   || '';
    openModal('tripModal');
}

function tripReset() {
    document.getElementById('formTitle').innerHTML = '<i class="fas fa-plus-circle" style="color:var(--gold);margin-right:6px;"></i>Map Trip';
    document.getElementById('tripId').value = '';
    document.getElementById('tripForm').reset();
}
</script>
@endsection

