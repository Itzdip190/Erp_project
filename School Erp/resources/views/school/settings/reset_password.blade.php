@extends('layouts.app')

@section('title', 'Reset Password')
@section('page-title', 'Reset Password')

@section('content')
<div class="card" style="max-width:850px; margin:0 auto;">
    <div class="card-hdr">
        <h3>Reset User Passwords</h3>
    </div>
    <div class="card-body">
        <p style="font-size:13px; color:var(--t2); margin-bottom:20px;">
            Search for any student, parent, teacher or staff member in your school to change their password securely.
        </p>

        <form method="GET" action="{{ route('school.settings.reset-password') }}" style="display:flex; gap:8px; margin-bottom:24px;">
            <input type="text" name="search" class="form-control" placeholder="Search by name, email or phone..." value="{{ $search }}" required>
            <button type="submit" class="btn btn-primary" style="padding:10px 24px;">Search</button>
        </form>

        @if($search)
        <h4 style="font-size:13px; font-weight:700; margin-bottom:12px; color:var(--navy);">Search Results:</h4>
        <div class="table-wrap" style="border:1px solid var(--border); border-radius:8px;">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>User Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td><strong>{{ $user->name }}</strong></td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge badge-purple">
                                {{ ucfirst(str_replace('_', ' ', $user->roles->first()?->name ?? 'User')) }}
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-gold" style="padding:4px 10px; font-size:11.5px;" onclick="openResetModal({{ $user->id }}, '{{ addslashes($user->name) }}')">
                                Reset Password
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center; padding:24px; color:var(--t2);">No users matched your query.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

<!-- Reset Password Modal -->
<div id="resetModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
    <div class="card" style="width:400px; margin:0; box-shadow:var(--shadow-lg);">
        <div class="card-hdr">
            <h3 id="modalUserTitle">Reset Password</h3>
            <button type="button" onclick="closeResetModal()" style="background:none; border:none; font-size:16px; cursor:pointer;">&times;</button>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('school.settings.reset-password.post') }}">
                @csrf
                <input type="hidden" name="user_id" id="modalUserId">
                <div class="form-group">
                    <label class="form-label">New Password <span style="color:var(--red);">*</span></label>
                    <input type="password" name="password" class="form-control" required placeholder="Minimum 8 characters">
                </div>
                <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:20px;">
                    <button type="button" class="btn btn-outline" onclick="closeResetModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openResetModal(userId, userName) {
    document.getElementById('modalUserId').value = userId;
    document.getElementById('modalUserTitle').innerText = 'Reset Password for ' + userName;
    document.getElementById('resetModal').style.display = 'flex';
}
function closeResetModal() {
    document.getElementById('resetModal').style.display = 'none';
}
</script>
@endsection
