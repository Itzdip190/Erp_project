@extends('layouts.app')

@section('title', 'Staff Access Control')
@section('page-title', 'Staff Access Control')

@section('content')
<div class="card">
    <div class="card-hdr" style="display:flex; justify-content:space-between; align-items:center;">
        <h3>Manage Staff Portal Access</h3>
        <form method="GET" action="{{ route('school.roles.staff-access') }}" style="display:flex; gap:8px;">
            <input type="text" name="search" class="form-control" placeholder="Search staff..." value="{{ $search }}" style="width:200px; padding:6px 12px;">
            <button type="submit" class="btn btn-primary" style="padding:6px 16px;">Search</button>
        </form>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Staff Name</th>
                        <th>Email</th>
                        <th>Current Role</th>
                        <th>Portal Access Status</th>
                        <th>Update Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td><strong>{{ $user->name }}</strong></td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge badge-purple">
                                {{ ucfirst(str_replace('_', ' ', $user->roles->first()?->name ?? 'None')) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-danger' }}">
                                {{ $user->is_active ? 'Access Active' : 'Access Suspended' }}
                            </span>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('school.roles.staff-access.update', $user->id) }}" style="display:flex; gap:6px; align-items:center;">
                                @csrf
                                @method('PUT')
                                <select name="role" class="form-control" style="width:auto; padding:4px 8px; font-size:12px;">
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ ($user->roles->first()?->name === $role->name) ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                        </option>
                                    @endforeach
                                </select>
                                <select name="is_active" class="form-control" style="width:auto; padding:4px 8px; font-size:12px;">
                                    <option value="1" {{ $user->is_active ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Suspended</option>
                                </select>
                                <button type="submit" class="btn btn-gold" style="padding:4px 10px; font-size:11.5px;">Update</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center; padding:24px; color:var(--t2);">No staff users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div style="padding:16px; border-top:1px solid var(--border);">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
