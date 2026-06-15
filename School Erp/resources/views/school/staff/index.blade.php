@extends('layouts.app')

@section('title', 'Staff Directory')
@section('page-title', 'Staff Directory')

@section('content')
<div class="card" style="margin-bottom:20px;">
    <div class="card-hdr" style="display:flex; justify-content:space-between; align-items:center;">
        <h3>Staff Filters</h3>
        <a href="{{ route('school.staff.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Staff</a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('school.staff.index') }}" class="grid-4" style="align-items:flex-end;">
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="ID, name, email..." value="{{ $search }}">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Department</label>
                <select name="department_id" class="form-control">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ $deptId == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Designation</label>
                <select name="designation_id" class="form-control">
                    <option value="">All Designations</option>
                    @foreach($designations as $desg)
                        <option value="{{ $desg->id }}" {{ $desgId == $desg->id ? 'selected' : '' }}>{{ $desg->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <button type="submit" class="btn btn-gold" style="width:100%;">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-hdr">
        <h3>Staff Directory</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th>Email / Phone</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staffList as $staff)
                    <tr>
                        <td><strong style="color:var(--gold);">{{ $staff->employee_id }}</strong></td>
                        <td>
                            <div style="display:flex; align-items:center; gap:8px;">
                                <div style="width:28px; height:28px; border-radius:50%; background:#e2e8f0; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                                    @if($staff->photo)
                                        <img src="{{ Storage::disk('public')->url($staff->photo) }}" style="width:100%; height:100%; object-fit:cover;">
                                    @else
                                        <i class="fas fa-user-tie" style="font-size:12px; color:#94a3b8;"></i>
                                    @endif
                                </div>
                                <strong>{{ $staff->full_name }}</strong>
                            </div>
                        </td>
                        <td>{{ optional($staff->department)->name ?? 'N/A' }}</td>
                        <td>{{ optional($staff->designation)->name ?? 'N/A' }}</td>
                        <td>
                            <div style="font-size:12px; color:var(--t2);">{{ $staff->email }}</div>
                            <div style="font-size:11px; color:var(--t3);">{{ $staff->phone ?? '—' }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $staff->is_active ? 'badge-success' : 'badge-danger' }}">
                                {{ $staff->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <a href="{{ route('school.staff.edit', $staff->id) }}" class="btn btn-outline" style="padding:4px 8px; font-size:11px;">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('school.staff.destroy', $staff->id) }}" onsubmit="return confirm('Are you sure you want to delete this staff member?')" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding:4px 8px; font-size:11px;">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center; padding:24px; color:var(--t2);">No staff members found matching parameters.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($staffList->hasPages())
    <div style="padding:16px; border-top:1px solid var(--border);">
        {{ $staffList->links() }}
    </div>
    @endif
</div>
@endsection
