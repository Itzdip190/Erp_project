@extends('layouts.app')
@section('page-title', 'Vehicles')
@section('content')
@include('school.transport.partials.tp-styles')

<style>
.vehicles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
}
.vehicle-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01), 0 2px 4px -1px rgba(0, 0, 0, 0.006);
}
.vehicle-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
    border-color: rgba(37, 99, 235, 0.2);
}
.vehicle-card-head {
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    border-bottom: 1px solid var(--border);
    background: rgba(37, 99, 235, 0.01);
}
.vehicle-card-body {
    padding: 18px 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.vehicle-info-row {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13.5px;
    color: var(--t2);
    font-weight: 500;
}
.vehicle-info-row i {
    width: 18px;
    text-align: center;
    flex-shrink: 0;
    font-size: 14px;
}
.vehicle-card-foot {
    padding: 14px 20px;
    border-top: 1px solid var(--border);
    display: flex;
    gap: 10px;
    background: var(--page);
}
</style>

<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-bus-alt" style="color:var(--gold);margin-right:8px;"></i>Fleet Vehicles</h1>
        <p>Manage buses, drivers, and seating capacity</p>
    </div>
    <div class="page-hdr-right">
        <button class="btn btn-gold" onclick="openAddModal()"><i class="fas fa-plus"></i><span>Add Vehicle</span></button>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

<!-- ── Vehicles List ── -->
<div style="margin-bottom: 24px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <span style="font-size:15px;font-weight:700;color:var(--t1);">{{ $vehicles->count() }} Vehicle{{ $vehicles->count()!=1?'s':'' }}</span>
    </div>

    @if($vehicles->isEmpty())
    <div class="card" style="border-radius:16px; border:1px solid var(--border);"><div class="tp-empty"><i class="fas fa-bus-alt"></i><p>No vehicles yet. Add one using the button above.</p></div></div>
    @else
    <div class="vehicles-grid">
        @foreach($vehicles as $v)
        <div class="vehicle-card">
            <div class="vehicle-card-head">
                <div class="tp-plate" style="flex:1;">{{ $v->vehicle_no }}</div>
                <span class="tp-badge {{ $v->status ? 'tp-badge-yes' : 'tp-badge-no' }}">
                    <i class="fas {{ $v->status ? 'fa-check' : 'fa-times' }}"></i> {{ $v->status ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <div class="vehicle-card-body">
                <div class="vehicle-info-row"><i class="fas fa-bus" style="color:#6366f1;"></i><span>{{ $v->vehicle_model ?: '—' }}</span></div>
                <div class="vehicle-info-row"><i class="fas fa-user-tie" style="color:#0ea5e9;"></i><span>{{ $v->driver_name ?: 'No driver' }}</span></div>
                <div class="vehicle-info-row"><i class="fas fa-phone-alt" style="color:#10b981;"></i><span>{{ $v->driver_phone ?: '—' }}</span></div>
                <div class="vehicle-info-row"><i class="fas fa-users" style="color:#d97706;"></i><span>{{ $v->capacity }} seats</span></div>
            </div>
            <div class="vehicle-card-foot">
                <button class="btn btn-outline" style="flex:1;padding:8px;font-size:12.5px;justify-content:center;" onclick="vEdit({{ json_encode($v) }})">
                    <i class="fa fa-edit"></i> Edit
                </button>
                <form action="{{ route('school.transport.delete') }}" method="POST" style="flex:1;" onsubmit="return confirm('Remove this vehicle?')">
                    @csrf
                    <input type="hidden" name="id" value="{{ $v->id }}">
                    <input type="hidden" name="type" value="vehicle">
                    <button type="submit" class="btn" style="width:100%;padding:8px;font-size:12.5px;background:#fef2f2;color:#ef4444;border:1px solid #fecaca;border-radius:10px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:6px;transition: all 0.15s;" onmouseover="this.style.backgroundColor='#fee2e2'" onmouseout="this.style.backgroundColor='#fef2f2'">
                        <i class="fa fa-trash"></i> Remove
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<!-- ── Vehicle Form Modal ── -->
<div class="tp-modal-overlay" id="vehicleModal">
    <div class="tp-modal">
        <div class="tp-modal-hdr">
            <h3 id="formTitle"><i class="fas fa-plus-circle" style="color:var(--gold);margin-right:6px;"></i>Add Vehicle</h3>
            <button class="tp-modal-close" onclick="closeModal('vehicleModal')">&times;</button>
        </div>
        <div class="tp-modal-body">
            <form method="POST" action="{{ route('school.transport.vehicles') }}" id="vForm">
                @csrf
                <input type="hidden" name="id" id="vId">
                
                <div class="form-group">
                    <label class="form-label">Registration No. <span style="color:var(--red);">*</span></label>
                    <input type="text" name="vehicle_no" id="vNo" class="form-control" placeholder="e.g. MH-12-AB-1234" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Model / Type</label>
                    <input type="text" name="vehicle_model" id="vModel" class="form-control" placeholder="e.g. Tata Starbus 40-Seater">
                </div>
                
                <div class="tp-g2">
                    <div class="form-group">
                        <label class="form-label">Driver Name</label>
                        <input type="text" name="driver_name" id="vDriver" class="form-control" placeholder="e.g. Ramesh Kumar">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Driver Phone</label>
                        <input type="text" name="driver_phone" id="vPhone" class="form-control" placeholder="e.g. 9876543210">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Seating Capacity <span style="color:var(--red);">*</span></label>
                    <input type="number" name="capacity" id="vCap" class="form-control" value="40" min="1" required>
                </div>

                <div class="form-group" id="vStatusGroup" style="display:none;">
                    <label class="form-label">Status</label>
                    <select name="status" id="vStatus" class="form-control">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                
                <div style="display:flex;gap:10px;margin-top:20px;">
                    <button type="submit" class="btn btn-gold" style="flex:2;justify-content:center;"><i class="fa fa-save"></i> Save</button>
                    <button type="button" class="btn btn-outline" style="flex:1;justify-content:center;" onclick="closeModal('vehicleModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddModal() {
    vReset();
    document.getElementById('vStatusGroup').style.display = 'none';
    openModal('vehicleModal');
}

function vEdit(v) {
    document.getElementById('formTitle').innerHTML = '<i class="fas fa-edit" style="color:var(--gold);margin-right:6px;"></i>Edit Vehicle';
    document.getElementById('vId').value     = v.id;
    document.getElementById('vNo').value     = v.vehicle_no;
    document.getElementById('vModel').value  = v.vehicle_model || '';
    document.getElementById('vDriver').value = v.driver_name  || '';
    document.getElementById('vPhone').value  = v.driver_phone || '';
    document.getElementById('vCap').value    = v.capacity;
    document.getElementById('vStatus').value  = v.status ? '1' : '0';
    document.getElementById('vStatusGroup').style.display = 'block';
    openModal('vehicleModal');
}

function vReset() {
    document.getElementById('formTitle').innerHTML = '<i class="fas fa-plus-circle" style="color:var(--gold);margin-right:6px;"></i>Add Vehicle';
    document.getElementById('vId').value = '';
    document.getElementById('vForm').reset();
    document.getElementById('vStatusGroup').style.display = 'none';
}
</script>
@endsection
