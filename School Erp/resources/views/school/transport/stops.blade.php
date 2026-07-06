@extends('layouts.app')

@section('page-title', 'Manage Stops')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-map-marker-alt" style="color:var(--gold);margin-right:8px;"></i>Bus Stops & Fares</h1>
        <p>Define student pickup/drop stops and set distance-based transport fare pricing</p>
    </div>
</div>

<div class="grid-3">
    <!-- Form Card -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-hdr">
            <h3 id="formTitle">Add Bus Stop</h3>
        </div>
        <div class="card-body" style="padding: 20px;">
            <form method="POST" action="{{ route('school.transport.stops') }}" id="stopForm">
                @csrf
                <input type="hidden" name="id" id="stopId">
                
                <div class="form-group">
                    <label class="form-label">Stop Name <span style="color:red;">*</span></label>
                    <input type="text" name="name" id="stopName" class="form-control" placeholder="e.g. Deccan Gymkhana" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Landmark / Description</label>
                    <input type="text" name="landmark" id="stopLandmark" class="form-control" placeholder="e.g. Opposite Cafe Goodluck">
                </div>

                <div class="form-group">
                    <label class="form-label">Monthly Transport Fare (INR) <span style="color:red;">*</span></label>
                    <input type="number" step="0.01" name="fare" id="stopFare" class="form-control" value="0.00" min="0" required>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-gold" style="flex: 1; justify-content: center;">
                        <i class="fa fa-save"></i> Save Stop
                    </button>
                    <button type="button" class="btn btn-outline" id="clearBtn" style="display:none;" onclick="resetForm()">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Stops List Table -->
    <div class="card" style="grid-column: span 2;">
        <div class="card-hdr" style="display:flex; justify-content:space-between; align-items:center;">
            <h3>Seeded & Custom Stops</h3>
            <span style="font-size:12.5px; font-weight:700; color:#10b981; background:#ecfdf5; padding:4px 10px; border-radius:12px;">Total: {{ count($stops) }} Stops</span>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="fee-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="padding:12px 18px; text-align:left;">#</th>
                            <th style="padding:12px 18px; text-align:left;">Stop Name</th>
                            <th style="padding:12px 18px; text-align:left;">Landmark</th>
                            <th style="padding:12px 18px; text-align:right;">Fare (Monthly)</th>
                            <th style="padding:12px 18px; text-align:center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stops as $index => $s)
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <td style="padding:14px 18px;"><span class="row-index">{{ $index + 1 }}</span></td>
                                <td style="padding:14px 18px; font-weight:700; color:#1e293b;">{{ $s->name }}</td>
                                <td style="padding:14px 18px; color:#475569;">{{ $s->landmark ?: '—' }}</td>
                                <td style="padding:14px 18px; text-align:right; font-weight:700; color:#10b981;">₹{{ number_format($s->fare, 2) }}</td>
                                <td style="padding:14px 18px; text-align:center; white-space:nowrap;">
                                    <button class="btn-action-edit" onclick="editStop({{ json_encode($s) }})" title="Edit Stop">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <form action="{{ route('school.transport.delete') }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this stop?');">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $s->id }}">
                                        <input type="hidden" name="type" value="stop">
                                        <button type="submit" class="btn-action-delete" title="Delete Stop">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding:30px; text-align:center; color:#64748b;">
                                    <i class="fas fa-map-marker-alt" style="font-size:24px; color:#cbd5e1; margin-bottom:10px; display:block;"></i>
                                    No stops logged yet. Create one from the form.
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
    function editStop(stop) {
        document.getElementById('formTitle').innerText = 'Edit Bus Stop';
        document.getElementById('stopId').value = stop.id;
        document.getElementById('stopName').value = stop.name;
        document.getElementById('stopLandmark').value = stop.landmark || '';
        document.getElementById('stopFare').value = stop.fare;
        document.getElementById('clearBtn').style.display = 'inline-block';
    }

    function resetForm() {
        document.getElementById('formTitle').innerText = 'Add Bus Stop';
        document.getElementById('stopId').value = '';
        document.getElementById('stopForm').reset();
        document.getElementById('clearBtn').style.display = 'none';
    }
</script>
@endsection
