@extends('layouts.app')

@section('page-title', 'Manage Routes')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-route" style="color:var(--gold);margin-right:8px;"></i>Bus Routes Directory</h1>
        <p>Define names, codes, and details of paths traveled by school transit vehicles</p>
    </div>
</div>

<div class="grid-3">
    <!-- Form Card -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-hdr">
            <h3 id="formTitle">Add Route</h3>
        </div>
        <div class="card-body" style="padding: 20px;">
            <form method="POST" action="{{ route('school.transport.routes') }}" id="routeForm">
                @csrf
                <input type="hidden" name="id" id="routeId">
                
                <div class="form-group">
                    <label class="form-label">Route Name / Code <span style="color:red;">*</span></label>
                    <input type="text" name="name" id="routeName" class="form-control" placeholder="e.g. Route 101 - Kothrud to School" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Route Description</label>
                    <input type="text" name="description" id="routeDescription" class="form-control" placeholder="e.g. West Pune highway shortcut route">
                </div>

                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-gold" style="flex: 1; justify-content: center;">
                        <i class="fa fa-save"></i> Save Route
                    </button>
                    <button type="button" class="btn btn-outline" id="clearBtn" style="display:none;" onclick="resetForm()">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Routes List Table -->
    <div class="card" style="grid-column: span 2;">
        <div class="card-hdr" style="display:flex; justify-content:space-between; align-items:center;">
            <h3>Configured Routes</h3>
            <span style="font-size:12.5px; font-weight:700; color:#8b5cf6; background:#f5f3ff; padding:4px 10px; border-radius:12px;">Total: {{ count($routes) }} Routes</span>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="fee-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="padding:12px 18px; text-align:left;">#</th>
                            <th style="padding:12px 18px; text-align:left;">Route Name</th>
                            <th style="padding:12px 18px; text-align:left;">Description</th>
                            <th style="padding:12px 18px; text-align:center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($routes as $index => $r)
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <td style="padding:14px 18px;"><span class="row-index">{{ $index + 1 }}</span></td>
                                <td style="padding:14px 18px; font-weight:700; color:#1e293b;">{{ $r->name }}</td>
                                <td style="padding:14px 18px; color:#475569;">{{ $r->description ?: '—' }}</td>
                                <td style="padding:14px 18px; text-align:center; white-space:nowrap;">
                                    <button class="btn-action-edit" onclick="editRoute({{ json_encode($r) }})" title="Edit Route">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <form action="{{ route('school.transport.delete') }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this route? This will delete all associated trips.');">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $r->id }}">
                                        <input type="hidden" name="type" value="route">
                                        <button type="submit" class="btn-action-delete" title="Delete Route">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="padding:30px; text-align:center; color:#64748b;">
                                    <i class="fas fa-route" style="font-size:24px; color:#cbd5e1; margin-bottom:10px; display:block;"></i>
                                    No routes logged yet. Create one from the form.
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
    function editRoute(route) {
        document.getElementById('formTitle').innerText = 'Edit Route';
        document.getElementById('routeId').value = route.id;
        document.getElementById('routeName').value = route.name;
        document.getElementById('routeDescription').value = route.description || '';
        document.getElementById('clearBtn').style.display = 'inline-block';
    }

    function resetForm() {
        document.getElementById('formTitle').innerText = 'Add Route';
        document.getElementById('routeId').value = '';
        document.getElementById('routeForm').reset();
        document.getElementById('clearBtn').style.display = 'none';
    }
</script>
@endsection
