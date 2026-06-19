@extends('layouts.app')

@section('page-title', 'Staff Download Status')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-chalkboard-teacher" style="color:var(--gold);margin-right:8px;"></i>Staff Download Status</h1>
        <p>Monitor staff login telemetry and verify who hasn't accessed the portal</p>
    </div>
</div>

<!-- Filter Bar -->
<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="padding:15px 20px;">
        <form method="GET" action="{{ route('school.downloads.staff-status') }}" style="display:flex; justify-content:space-between; align-items:flex-end; gap:15px; flex-wrap:wrap;">
            <div style="display:flex; gap:15px; flex-grow:1;">
                <div class="form-group" style="margin-bottom:0; flex:1; min-width:160px;">
                    <label class="form-label" style="font-size:11px; text-transform:uppercase; color:var(--t2);">Select Staff Type</label>
                    <select name="staff_type" class="form-control" style="padding:6px 12px; font-size:13px;" onchange="this.form.submit()">
                        <option value="">All Staff</option>
                        <option value="Teaching" {{ $staffType === 'Teaching' ? 'selected' : '' }}>Teaching Staff</option>
                        <option value="Non-Teaching" {{ $staffType === 'Non-Teaching' ? 'selected' : '' }}>Non-Teaching Staff</option>
                        <option value="Admin" {{ $staffType === 'Admin' ? 'selected' : '' }}>Admin Staff</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0; flex:2; min-width:200px;">
                    <label class="form-label" style="font-size:11px; text-transform:uppercase; color:var(--t2);">Search Staff</label>
                    <input type="text" name="search" class="form-control" style="padding:6px 12px; font-size:13px;" placeholder="Search Staff Name, Email..." value="{{ $search }}">
                </div>
            </div>
            
            <div style="display:flex; gap:8px;">
                <button type="submit" class="btn btn-gold" style="padding:8px 16px; font-size:13px;">
                    <i class="fas fa-search"></i> SEARCH
                </button>
                <button type="button" class="btn btn-outline" style="border-color:#b45309; color:#b45309; padding:8px 16px; font-size:13px;" onclick="showToast('Exporting Staff list...')">
                    <i class="fas fa-download"></i> DOWNLOAD
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Toggle Cards Grid -->
<div class="grid-2" style="margin-bottom:20px; grid-template-columns: 1fr 1fr;">
    <!-- Total Staff Card -->
    <a href="{{ request()->fullUrlWithQuery(['tab' => 'total']) }}" style="text-decoration:none;">
        <div class="card" style="margin-bottom:0; background:{{ $tab === 'total' ? 'rgba(59,130,246,0.1)' : 'rgba(59,130,246,0.03)' }}; border:1px solid {{ $tab === 'total' ? '#3b82f6' : 'transparent' }}; border-left:5px solid #3b82f6; cursor:pointer; transition:transform 0.2s;">
            <div class="card-body" style="padding:18px; display:flex; align-items:center; gap:15px;">
                <div style="width:42px; height:42px; border-radius:8px; background:#3b82f6; display:flex; align-items:center; justify-content:center; color:white; font-size:18px;">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h2 style="font-size:24px; font-weight:800; color:#1e3a8a; margin:0;">{{ $totalStaff->count() }}</h2>
                    <span style="font-size:12px; color:#1d4ed8; font-weight:700;">Total Staffs</span>
                </div>
            </div>
        </div>
    </a>

    <!-- Haven't Logged In Card -->
    <a href="{{ request()->fullUrlWithQuery(['tab' => 'not_logged_in']) }}" style="text-decoration:none;">
        <div class="card" style="margin-bottom:0; background:{{ $tab === 'not_logged_in' ? 'rgba(239,68,68,0.1)' : 'rgba(239,68,68,0.03)' }}; border:1px solid {{ $tab === 'not_logged_in' ? '#ef4444' : 'transparent' }}; border-left:5px solid #ef4444; cursor:pointer; transition:transform 0.2s;">
            <div class="card-body" style="padding:18px; display:flex; align-items:center; gap:15px;">
                <div style="width:42px; height:42px; border-radius:8px; background:#ef4444; display:flex; align-items:center; justify-content:center; color:white; font-size:18px;">
                    <i class="fas fa-times"></i>
                </div>
                <div>
                    <h2 style="font-size:24px; font-weight:800; color:#991b1b; margin:0;">{{ $notLoggedIn->count() }}</h2>
                    <span style="font-size:12px; color:#b91c1c; font-weight:700;">Staffs who haven't logged in</span>
                </div>
            </div>
        </div>
    </a>
</div>

<!-- Table Listing -->
<div class="card">
    <div class="card-hdr" style="display:flex; justify-content:space-between; align-items:center;">
        <h3>Listing: {{ $tab === 'not_logged_in' ? "Staffs who haven't logged in" : 'Total Staffs' }}</h3>
        <span class="badge {{ $tab === 'not_logged_in' ? 'badge-red' : 'badge-blue' }}">{{ $activeList->count() }} Records</span>
    </div>
    <div class="card-body" style="padding:0;">
        <table class="tbl">
            <thead>
                <tr>
                    <th style="width:70px; text-align:center;">#</th>
                    <th>Staff Name</th>
                    <th>Staff Type</th>
                    <th>Mobile Number</th>
                    <th>Email ID</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activeList as $index => $st)
                    <tr>
                        <td style="text-align:center; color:var(--t3); font-weight:500;">
                            {{ sprintf('%02d.', $index + 1) }}
                        </td>
                        <td>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="width:32px; height:32px; border-radius:50%; background:#e2e8f0; display:flex; align-items:center; justify-content:center; color:#64748b;">
                                    <i class="fas fa-user-tie" style="font-size:14px;"></i>
                                </div>
                                <div>
                                    <span style="font-weight:700; color:var(--navy);">{{ $st->first_name }} {{ $st->last_name }}</span>
                                    <small style="display:block; color:var(--t3);">Code: STF-{{ 1000 + $st->id }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-purple" style="font-size:11px;">
                                {{ $st->role ?? 'Teaching' }}
                            </span>
                        </td>
                        <td style="font-weight:500; color:var(--t1);">
                            {{ $st->mobile_number ?? '—' }}
                        </td>
                        <td style="color:var(--t2);">
                            {{ $st->email ?? '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center; padding:40px; color:var(--t3);">
                            <i class="fas fa-user-circle" style="font-size:32px; color:var(--t3); margin-bottom:12px; display:block;"></i>
                            No staff records match the criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
