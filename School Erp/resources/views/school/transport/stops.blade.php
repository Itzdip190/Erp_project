@extends('layouts.app')
@section('page-title', 'Bus Stops')
@section('content')
@include('school.transport.partials.tp-styles')

<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-map-marker-alt" style="color:var(--gold);margin-right:8px;"></i>Bus Stops & Fares</h1>
        <p>Define stops with separate pickup and drop fares for precise billing</p>
    </div>
    <div class="page-hdr-right">
        <button class="btn btn-gold" onclick="openAddModal()"><i class="fas fa-plus"></i><span>Add Stop</span></button>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

<!-- ── Stops Table ── -->
<div class="card" style="border-radius:16px; border:1px solid var(--border); overflow:hidden; margin-bottom: 24px;">
    <div class="tp-card-hdr">
        <h3>Stops Registry</h3>
        <span class="tp-badge tp-badge-yes">{{ $stops->count() }} Stop{{ $stops->count()!=1?'s':'' }}</span>
    </div>
    <div class="tp-scroll-hint">← Scroll to see all columns</div>
    <div class="tp-table-wrap" style="border:none; border-radius:0;">
        <table class="tp-table">
            <thead>
                <tr>
                    <th style="width: 60px;">#</th>
                    <th>Stop Name</th>
                    <th>Landmark</th>
                    <th style="text-align:center; width: 130px;">Pickup Fare</th>
                    <th style="text-align:center; width: 130px;">Drop Fare</th>
                    <th style="text-align:center; width: 140px;">Total Monthly</th>
                    <th style="text-align:center; width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($stops as $i => $s)
            <tr>
                <td style="color:var(--t3);font-weight:600;">{{ $i+1 }}</td>
                <td>
                    <div style="font-weight:700; color:var(--t1); display:flex; align-items:center; gap:8px;">
                        <i class="fas fa-map-marker-alt" style="color:#d97706; font-size:14px;"></i>
                        <span>{{ $s->name }}</span>
                    </div>
                </td>
                <td style="font-size:13px;color:var(--t2);font-weight:500;">{{ $s->landmark ?: '—' }}</td>
                <td style="text-align:center;"><span class="tp-badge tp-badge-pick">₹{{ number_format($s->pick_fare ?? 0, 2) }}</span></td>
                <td style="text-align:center;"><span class="tp-badge tp-badge-drop">₹{{ number_format($s->drop_fare ?? 0, 2) }}</span></td>
                <td style="text-align:center;">
                    <span class="tp-badge" style="background:#e6f4ea; color:#137333;">
                        ₹{{ number_format(($s->pick_fare ?? 0) + ($s->drop_fare ?? 0) ?: ($s->fare ?? 0), 2) }}
                    </span>
                </td>
                <td style="text-align:center;">
                    <div style="display:flex; justify-content:center; gap:8px; align-items:center;">
                        <button class="tp-btn-edit" onclick="stopEdit({{ json_encode($s) }})" title="Edit"><i class="fa fa-edit"></i></button>
                        <form action="{{ route('school.transport.delete') }}" method="POST" style="display:inline; margin:0;" onsubmit="return confirm('Delete this stop?')">
                            @csrf 
                            <input type="hidden" name="id" value="{{ $s->id }}">
                            <input type="hidden" name="type" value="stop">
                            <button type="submit" class="tp-btn-del" title="Delete"><i class="fa fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">
                    <div class="tp-empty"><i class="fas fa-map-marker-alt"></i><p>No stops defined yet. Use the button above to add stops.</p></div>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ── Bus Stop Form Modal ── -->
<div class="tp-modal-overlay" id="stopModal">
    <div class="tp-modal">
        <div class="tp-modal-hdr">
            <h3 id="formTitle"><i class="fas fa-plus-circle" style="color:var(--gold);margin-right:6px;"></i>Add Stop</h3>
            <button class="tp-modal-close" onclick="closeModal('stopModal')">&times;</button>
        </div>
        <div class="tp-modal-body">
            <form method="POST" action="{{ route('school.transport.stops') }}" id="stopForm">
                @csrf
                <input type="hidden" name="id" id="stopId">
                
                <div class="form-group">
                    <label class="form-label">Stop Name <span style="color:var(--red);">*</span></label>
                    <input type="text" name="name" id="stopName" class="form-control" placeholder="e.g. Deccan Gymkhana" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Landmark</label>
                    <input type="text" name="landmark" id="stopLandmark" class="form-control" placeholder="e.g. Opposite Cafe Goodluck">
                </div>

                <div style="background:var(--page);border-radius:12px;padding:16px;margin-bottom:20px;border:1px solid var(--border);">
                    <div style="font-size:11px;font-weight:800;color:var(--t3);text-transform:uppercase;letter-spacing:.75px;margin-bottom:12px;">Monthly Fare (INR)</div>
                    <div class="tp-g2">
                        <div class="form-group" style="margin:0;">
                            <label class="form-label" style="color:var(--tp-pick);">Pickup Fare ₹</label>
                            <input type="number" step="0.01" name="pick_fare" id="stopPick" class="form-control" value="0" min="0" oninput="calcTotal()">
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label class="form-label" style="color:var(--tp-drop);">Drop Fare ₹</label>
                            <input type="number" step="0.01" name="drop_fare" id="stopDrop" class="form-control" value="0" min="0" oninput="calcTotal()">
                        </div>
                    </div>
                    <div class="tp-fare-total" style="margin-top:16px;">
                        <span class="tp-fare-total-label">Total / Month</span>
                        <span class="tp-fare-total-val" id="fareTotal">₹0.00</span>
                    </div>
                </div>

                <div style="display:flex;gap:10px;">
                    <button type="submit" class="btn btn-gold" style="flex:2;justify-content:center;"><i class="fa fa-save"></i> Save Stop</button>
                    <button type="button" class="btn btn-outline" style="flex:1;justify-content:center;" onclick="closeModal('stopModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function calcTotal() {
    const p = parseFloat(document.getElementById('stopPick').value) || 0;
    const d = parseFloat(document.getElementById('stopDrop').value) || 0;
    document.getElementById('fareTotal').textContent = '₹' + (p + d).toFixed(2);
}

function openAddModal() {
    stopReset();
    openModal('stopModal');
}

function stopEdit(s) {
    document.getElementById('formTitle').innerHTML = '<i class="fas fa-edit" style="color:var(--gold);margin-right:6px;"></i>Edit Stop';
    document.getElementById('stopId').value = s.id;
    document.getElementById('stopName').value = s.name;
    document.getElementById('stopLandmark').value = s.landmark || '';
    document.getElementById('stopPick').value = s.pick_fare || 0;
    document.getElementById('stopDrop').value = s.drop_fare || 0;
    calcTotal();
    openModal('stopModal');
}

function stopReset() {
    document.getElementById('formTitle').innerHTML = '<i class="fas fa-plus-circle" style="color:var(--gold);margin-right:6px;"></i>Add Stop';
    document.getElementById('stopId').value = '';
    document.getElementById('stopForm').reset();
    document.getElementById('fareTotal').textContent = '₹0.00';
}
</script>
@endsection

