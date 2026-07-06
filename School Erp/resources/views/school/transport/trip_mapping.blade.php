@extends('layouts.app')

@section('page-title', 'Vehicle Trip Mapping')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-network-wired" style="color:var(--gold);margin-right:8px;"></i>Vehicle Trip Mapping</h1>
        <p>Map fleet vehicles to active routes and set pick up / drop schedule times</p>
    </div>
</div>

<div class="grid-3">
    <!-- Form Card -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-hdr">
            <h3 id="formTitle">Map Trip Schedule</h3>
        </div>
        <div class="card-body" style="padding: 20px;">
            <form method="POST" action="{{ route('school.transport.trip-mapping') }}" id="tripForm">
                @csrf
                <input type="hidden" name="id" id="tripId">
                
                <div class="form-group">
                    <label class="form-label">Trip Description / Name <span style="color:red;">*</span></label>
                    <input type="text" name="trip_name" id="tripName" class="form-control" placeholder="e.g. Morning Pickup" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Select Vehicle (Bus) <span style="color:red;">*</span></label>
                    <select name="vehicle_id" id="tripVehicle" class="form-control" required>
                        <option value="">Select Vehicle</option>
                        @foreach($vehicles as $v)
                            <option value="{{ $v->id }}">{{ $v->vehicle_no }} ({{ $v->vehicle_model ?: 'Bus' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Select Route <span style="color:red;">*</span></label>
                    <select name="route_id" id="tripRoute" class="form-control" required>
                        <option value="">Select Route</option>
                        @foreach($routes as $r)
                            <option value="{{ $r->id }}">{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Trip Direction / Type <span style="color:red;">*</span></label>
                    <select name="type" id="tripType" class="form-control" required>
                        <option value="pickup">Pick Up (Morning)</option>
                        <option value="drop">Drop (Afternoon)</option>
                        <option value="both">Both Directions</option>
                    </select>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Start Time</label>
                        <input type="text" name="start_time" id="startTime" class="form-control" placeholder="e.g. 07:00 AM">
                    </div>
                    <div class="form-group">
                        <label class="form-label">End Time</label>
                        <input type="text" name="end_time" id="endTime" class="form-control" placeholder="e.g. 08:00 AM">
                    </div>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-gold" style="flex: 1; justify-content: center;">
                        <i class="fa fa-save"></i> Save Schedule
                    </button>
                    <button type="button" class="btn btn-outline" id="clearBtn" style="display:none;" onclick="resetForm()">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Trips List Table -->
    <div class="card" style="grid-column: span 2;">
        <div class="card-hdr" style="display:flex; justify-content:space-between; align-items:center;">
            <h3>Active Schedules</h3>
            <span style="font-size:12.5px; font-weight:700; color:#d97706; background:#fffbeb; padding:4px 10px; border-radius:12px;">Total: {{ count($trips) }} Mappings</span>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="fee-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="padding:12px 18px; text-align:left;">#</th>
                            <th style="padding:12px 18px; text-align:left;">Trip Details</th>
                            <th style="padding:12px 18px; text-align:left;">Route</th>
                            <th style="padding:12px 18px; text-align:left;">Bus No</th>
                            <th style="padding:12px 18px; text-align:center;">Type</th>
                            <th style="padding:12px 18px; text-align:center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trips as $index => $t)
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <td style="padding:14px 18px;"><span class="row-index">{{ $index + 1 }}</span></td>
                                <td style="padding:14px 18px; font-weight:700; color:#1e293b;">
                                    <div>{{ $t->trip_name }}</div>
                                    <small style="color:#64748b; font-weight:500;"><i class="far fa-clock" style="margin-right:3px;"></i> {{ $t->start_time ?: '—' }} - {{ $t->end_time ?: '—' }}</small>
                                </td>
                                <td style="padding:14px 18px; color:#475569;">{{ $t->route?->name }}</td>
                                <td style="padding:14px 18px; font-weight:600; color:#2563eb;">{{ $t->vehicle?->vehicle_no }}</td>
                                <td style="padding:14px 18px; text-align:center;">
                                    @if($t->type === 'pickup')
                                        <span style="background:#e0f2fe; color:#0369a1; padding:3px 8px; border-radius:6px; font-weight:700; font-size:11px; text-transform:uppercase;">Pick Up</span>
                                    @elseif($t->type === 'drop')
                                        <span style="background:#fef3c7; color:#d97706; padding:3px 8px; border-radius:6px; font-weight:700; font-size:11px; text-transform:uppercase;">Drop</span>
                                    @else
                                        <span style="background:#dcfce7; color:#15803d; padding:3px 8px; border-radius:6px; font-weight:700; font-size:11px; text-transform:uppercase;">Both</span>
                                    @endif
                                </td>
                                <td style="padding:14px 18px; text-align:center; white-space:nowrap;">
                                    <button class="btn-action-edit" onclick="editTrip({{ json_encode($t) }})" title="Edit Trip Schedule">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <form action="{{ route('school.transport.delete') }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this trip schedule?');">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $t->id }}">
                                        <input type="hidden" name="type" value="trip">
                                        <button type="submit" class="btn-action-delete" title="Delete Trip Schedule">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding:30px; text-align:center; color:#64748b;">
                                    <i class="fas fa-network-wired" style="font-size:24px; color:#cbd5e1; margin-bottom:10px; display:block;"></i>
                                    No trips scheduled yet. Map one from the form.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function editTrip(trip) {
        document.getElementById('formTitle').innerText = 'Edit Trip Mapping';
        document.getElementById('tripId').value = trip.id;
        document.getElementById('tripName').value = trip.trip_name;
        document.getElementById('tripVehicle').value = trip.vehicle_id;
        document.getElementById('tripRoute').value = trip.route_id;
        document.getElementById('tripType').value = trip.type;
        document.getElementById('startTime').value = trip.start_time || '';
        document.getElementById('endTime').value = trip.end_time || '';
        document.getElementById('clearBtn').style.display = 'inline-block';
    }

    function resetForm() {
        document.getElementById('formTitle').innerText = 'Map Trip Schedule';
        document.getElementById('tripId').value = '';
        document.getElementById('tripForm').reset();
        document.getElementById('clearBtn').style.display = 'none';
    }
</script>
@endsection
