@extends('layouts.app')

@section('page-title', 'Manage Vehicles')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-bus-alt" style="color:var(--gold);margin-right:8px;"></i>Vehicles Directory</h1>
        <p>Log and configure details of all transport buses, mini-vans, drivers, and capacity limits</p>
    </div>
</div>

<div class="grid-3">
    <!-- Form Card -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-hdr">
            <h3 id="formTitle">Add Vehicle Details</h3>
        </div>
        <div class="card-body" style="padding: 20px;">
            <form method="POST" action="{{ route('school.transport.vehicles') }}" id="vehicleForm">
                @csrf
                <input type="hidden" name="id" id="vehicleId">
                
                <div class="form-group">
                    <label class="form-label">Vehicle Registration No. <span style="color:red;">*</span></label>
                    <input type="text" name="vehicle_no" id="vehicleNo" class="form-control" placeholder="e.g. MH-12-AB-1234" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Vehicle Model / Description</label>
                    <input type="text" name="vehicle_model" id="vehicleModel" class="form-control" placeholder="e.g. Tata Starbus 40-Seater">
                </div>

                <div class="form-group">
                    <label class="form-label">Driver Name</label>
                    <input type="text" name="driver_name" id="driverName" class="form-control" placeholder="e.g. Ramesh Kumar">
                </div>

                <div class="form-group">
                    <label class="form-label">Driver Phone Number</label>
                    <input type="text" name="driver_phone" id="driverPhone" class="form-control" placeholder="e.g. 9876543210">
                </div>

                <div class="form-group">
                    <label class="form-label">Seating Capacity <span style="color:red;">*</span></label>
                    <input type="number" name="capacity" id="vehicleCapacity" class="form-control" value="40" min="1" required>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-gold" style="flex: 1; justify-content: center;">
                        <i class="fa fa-save"></i> Save Details
                    </button>
                    <button type="button" class="btn btn-outline" id="clearBtn" style="display:none;" onclick="resetForm()">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Vehicles List Table -->
    <div class="card" style="grid-column: span 2;">
        <div class="card-hdr" style="display:flex; justify-content:space-between; align-items:center;">
            <h3>Vehicles Fleet List</h3>
            <span style="font-size:12.5px; font-weight:700; color:#2563eb; background:#eff6ff; padding:4px 10px; border-radius:12px;">Total: {{ count($vehicles) }} Vehicles</span>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="fee-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="padding:12px 18px; text-align:left;">#</th>
                            <th style="padding:12px 18px; text-align:left;">Registration No.</th>
                            <th style="padding:12px 18px; text-align:left;">Model/Make</th>
                            <th style="padding:12px 18px; text-align:left;">Driver Info</th>
                            <th style="padding:12px 18px; text-align:center;">Capacity</th>
                            <th style="padding:12px 18px; text-align:center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehicles as $index => $v)
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <td style="padding:14px 18px;"><span class="row-index">{{ $index + 1 }}</span></td>
                                <td style="padding:14px 18px; font-weight:700; color:#1e293b;">{{ $v->vehicle_no }}</td>
                                <td style="padding:14px 18px; color:#475569;">{{ $v->vehicle_model ?: '—' }}</td>
                                <td style="padding:14px 18px;">
                                    <div style="font-weight:600;">{{ $v->driver_name ?: '—' }}</div>
                                    <small style="color:#64748b;">{{ $v->driver_phone ?: '—' }}</small>
                                </td>
                                <td style="padding:14px 18px; text-align:center; font-weight:700; color:#2563eb;">{{ $v->capacity }}</td>
                                <td style="padding:14px 18px; text-align:center; white-space:nowrap;">
                                    <button class="btn-action-edit" onclick="editVehicle({{ json_encode($v) }})" title="Edit Vehicle">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <form action="{{ route('school.transport.delete') }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this vehicle? This will delete all mapped trips.');">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $v->id }}">
                                        <input type="hidden" name="type" value="vehicle">
                                        <button type="submit" class="btn-action-delete" title="Delete Vehicle">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding:30px; text-align:center; color:#64748b;">
                                    <i class="fas fa-bus-alt" style="font-size:24px; color:#cbd5e1; margin-bottom:10px; display:block;"></i>
                                    No vehicles logged yet. Add one from the form.
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
    function editVehicle(vehicle) {
        document.getElementById('formTitle').innerText = 'Edit Vehicle Details';
        document.getElementById('vehicleId').value = vehicle.id;
        document.getElementById('vehicleNo').value = vehicle.vehicle_no;
        document.getElementById('vehicleModel').value = vehicle.vehicle_model || '';
        document.getElementById('driverName').value = vehicle.driver_name || '';
        document.getElementById('driverPhone').value = vehicle.driver_phone || '';
        document.getElementById('vehicleCapacity').value = vehicle.capacity;
        document.getElementById('clearBtn').style.display = 'inline-block';
    }

    function resetForm() {
        document.getElementById('formTitle').innerText = 'Add Vehicle Details';
        document.getElementById('vehicleId').value = '';
        document.getElementById('vehicleForm').reset();
        document.getElementById('clearBtn').style.display = 'none';
    }
</script>
@endsection
