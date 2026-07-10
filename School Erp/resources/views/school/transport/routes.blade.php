@extends('layouts.app')
@section('page-title', 'Routes')
@section('content')
@include('school.transport.partials.tp-styles')

<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-route" style="color:var(--gold);margin-right:8px;"></i>Bus Routes</h1>
        <p>Configure routes with default pickup and drop fares</p>
    </div>
    <div class="page-hdr-right">
        <button class="btn btn-gold" onclick="openAddModal()"><i class="fas fa-plus"></i><span>Add Route</span></button>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

<!-- ── Routes Table ── -->
<div class="card" style="border-radius:16px; border:1px solid var(--border); overflow:hidden; margin-bottom: 24px;">
    <div class="tp-card-hdr">
        <h3>Configured Routes</h3>
        <span class="tp-badge tp-badge-purple">{{ $routes->count() }} Route{{ $routes->count()!=1?'s':'' }}</span>
    </div>
    <div class="tp-scroll-hint">← Scroll to see all columns</div>
    <div class="tp-table-wrap" style="border:none; border-radius:0;">
        <table class="tp-table">
            <thead>
                <tr>
                    <th style="width: 60px;">#</th>
                    <th>Route Name</th>
                    <th>Description</th>
                    <th style="text-align:center; width: 120px;">Students</th>
                    <th style="text-align:center; width: 140px;">Default Pick Fare</th>
                    <th style="text-align:center; width: 140px;">Default Drop Fare</th>
                    <th style="text-align:center; width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($routes as $i => $r)
            <tr>
                <td style="color:var(--t3);font-weight:600;">{{ $i+1 }}</td>
                <td>
                    <div style="font-weight:700; color:var(--t1); display:flex; align-items:center; gap:8px;">
                        <i class="fas fa-route" style="color:#6366f1; font-size:14px;"></i>
                        <span>{{ $r->name }}</span>
                    </div>
                </td>
                <td style="font-size:13px; color:var(--t2); font-weight:500;">{{ $r->description ?: '—' }}</td>
                <td style="text-align:center;"><span class="tp-badge tp-badge-purple" style="font-size: 12px; padding: 3px 10px;">{{ $r->students_count ?? 0 }}</span></td>
                <td style="text-align:center;"><span class="tp-badge tp-badge-pick">₹{{ number_format($r->pick_fare ?? 0, 2) }}</span></td>
                <td style="text-align:center;"><span class="tp-badge tp-badge-drop">₹{{ number_format($r->drop_fare ?? 0, 2) }}</span></td>
                <td style="text-align:center;">
                    <div style="display:flex; justify-content:center; gap:8px; align-items:center;">
                        <button class="tp-btn-edit" onclick="routeEdit({{ json_encode($r) }})" title="Edit"><i class="fa fa-edit"></i></button>
                        <form action="{{ route('school.transport.delete') }}" method="POST" style="display:inline; margin:0;" onsubmit="return confirm('Delete route? Students will lose assignment.')">
                            @csrf 
                            <input type="hidden" name="id" value="{{ $r->id }}">
                            <input type="hidden" name="type" value="route">
                            <button type="submit" class="tp-btn-del" title="Delete"><i class="fa fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">
                    <div class="tp-empty"><i class="fas fa-route"></i><p>No routes defined yet. Use the button above to configure routes.</p></div>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ── Route Form Modal ── -->
<div class="tp-modal-overlay" id="routeModal">
    <div class="tp-modal">
        <div class="tp-modal-hdr">
            <h3 id="formTitle"><i class="fas fa-plus-circle" style="color:var(--gold);margin-right:6px;"></i>Add Route</h3>
            <button class="tp-modal-close" onclick="closeModal('routeModal')">&times;</button>
        </div>
        <div class="tp-modal-body">
            <form method="POST" action="{{ route('school.transport.routes') }}" id="routeForm">
                @csrf
                <input type="hidden" name="id" id="routeId">
                
                <div class="form-group">
                    <label class="form-label">Route Name <span style="color:var(--red);">*</span></label>
                    <input type="text" name="name" id="routeName" class="form-control" placeholder="e.g. Route A – Kothrud" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" id="routeDesc" class="form-control" placeholder="Brief description">
                </div>
                
                <div style="background:var(--page);border-radius:12px;padding:16px;border:1px solid var(--border);margin-bottom:20px;">
                    <div style="font-size:11px;font-weight:800;color:var(--t3);text-transform:uppercase;letter-spacing:.75px;margin-bottom:12px;">Default Monthly Fares</div>
                    <div class="tp-g2">
                        <div class="form-group" style="margin:0;">
                            <label class="form-label" style="color:var(--tp-pick);">Pickup Fare ₹</label>
                            <input type="number" step="0.01" name="pick_fare" id="routePick" class="form-control" value="0" min="0">
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label class="form-label" style="color:var(--tp-drop);">Drop Fare ₹</label>
                            <input type="number" step="0.01" name="drop_fare" id="routeDrop" class="form-control" value="0" min="0">
                        </div>
                    </div>
                </div>
                
                <div style="display:flex;gap:10px;">
                    <button type="submit" class="btn btn-gold" style="flex:2;justify-content:center;"><i class="fa fa-save"></i> Save Route</button>
                    <button type="button" class="btn btn-outline" style="flex:1;justify-content:center;" onclick="closeModal('routeModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddModal() {
    routeReset();
    openModal('routeModal');
}

function routeEdit(r) {
    document.getElementById('formTitle').innerHTML = '<i class="fas fa-edit" style="color:var(--gold);margin-right:6px;"></i>Edit Route';
    document.getElementById('routeId').value   = r.id;
    document.getElementById('routeName').value = r.name;
    document.getElementById('routeDesc').value = r.description || '';
    document.getElementById('routePick').value = r.pick_fare || 0;
    document.getElementById('routeDrop').value = r.drop_fare || 0;
    openModal('routeModal');
}

function routeReset() {
    document.getElementById('formTitle').innerHTML = '<i class="fas fa-plus-circle" style="color:var(--gold);margin-right:6px;"></i>Add Route';
    document.getElementById('routeId').value = '';
    document.getElementById('routeForm').reset();
}
</script>
@endsection
