@extends('superadmin.layouts.master')

@section('styles')
<style>
    /* ─── SCHOOLS DIRECTORY ────────────────────────────────────── */
    .sa-directory-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.03);
        overflow: hidden;
        margin-bottom: 30px;
    }

    .sa-directory-hdr {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    
    .sa-directory-hdr-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .sa-directory-hdr-icon {
        width: 38px; height: 38px;
        border-radius: 10px;
        background: rgba(99,102,241,0.1);
        color: #6366f1;
        display: flex; align-items: center; justify-content: center;
        font-size: 15px;
    }
    .sa-directory-hdr h3 { font-size: 15px; font-weight: 800; color: #1e1b4b; margin: 0; }
    .sa-directory-hdr p { font-size: 11px; color: #64748b; margin: 2px 0 0; }

    .table-custom th {
        font-size: 0.72rem;
        font-weight: 800;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.8px;
        border-top: none !important;
        border-bottom: 2px solid #f3f4f6 !important;
        padding: 14px 16px !important;
    }

    .table-custom td {
        font-size: 0.88rem;
        color: #1e1b4b;
        vertical-align: middle !important;
        padding: 16px 16px !important;
        border-bottom: 1px solid #f3f4f6 !important;
        border-top: none !important;
    }

    .school-info-cell {
        display: flex;
        flex-direction: column;
    }
    .school-name {
        font-weight: 700;
        color: #1e1b4b;
        font-size: 0.92rem;
    }
    .school-code {
        font-size: 0.75rem;
        color: #6366f1;
        font-weight: 700;
        margin-top: 2px;
    }

    .admin-info-cell {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .admin-name {
        font-weight: 600;
        color: #374151;
    }
    .admin-meta {
        font-size: 0.78rem;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .badge-status-active {
        background-color: #ecfdf5;
        color: #10b981;
        font-weight: 700;
        font-size: 0.72rem;
        padding: 4px 8px;
        border-radius: 8px;
        display: inline-block;
    }

    .badge-status-suspended {
        background-color: #fef2f2;
        color: #ef4444;
        font-weight: 700;
        font-size: 0.72rem;
        padding: 4px 8px;
        border-radius: 8px;
        display: inline-block;
    }

    .badge-status-inactive {
        background-color: #f3f4f6;
        color: #6b7280;
        font-weight: 700;
        font-size: 0.72rem;
        padding: 4px 8px;
        border-radius: 8px;
        display: inline-block;
    }

    .badge-plan {
        background-color: rgba(99, 102, 241, 0.1);
        color: #6366f1;
        font-weight: 700;
        font-size: 0.72rem;
        padding: 4px 8px;
        border-radius: 8px;
        display: inline-block;
    }

    .badge-no-plan {
        background-color: #f3f4f6;
        color: #9ca3af;
        font-weight: 600;
        font-size: 0.72rem;
        padding: 4px 8px;
        border-radius: 8px;
        display: inline-block;
    }

    .btn-sa-action {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8rem;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none !important;
    }

    .btn-login-direct {
        background-color: #e0e7ff;
        color: #4f46e5;
    }

    .btn-login-direct:hover {
        background-color: #4f46e5;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(79, 70, 229, 0.2);
    }

    .btn-edit-school {
        background-color: #fef3c7;
        color: #d97706;
    }

    .btn-edit-school:hover {
        background-color: #d97706;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(217, 119, 6, 0.2);
    }

    .btn-delete-school {
        background-color: #fee2e2;
        color: #ef4444;
    }

    .btn-delete-school:hover {
        background-color: #ef4444;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2);
    }

    .btn-toggle-school {
        background-color: #f3f4f6;
        color: #4b5563;
    }

    .btn-toggle-school:hover {
        background-color: #4b5563;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(75, 85, 99, 0.2);
    }

    /* Dark mode overrides */
    body.dark-mode .sa-directory-card { background: #111827 !important; border-color: #1e293b !important; }
    body.dark-mode .sa-directory-hdr { border-bottom-color: #1e293b !important; }
    body.dark-mode .sa-directory-hdr h3 { color: #f1f5f9 !important; }
    body.dark-mode .sa-directory-hdr p { color: #64748b !important; }
    body.dark-mode .table-custom th { border-bottom-color: #1e293b !important; color: #94a3b8 !important; }
    body.dark-mode .table-custom td { border-bottom-color: #1e293b !important; color: #cbd5e1 !important; }
    body.dark-mode .school-name { color: #f1f5f9 !important; }
    body.dark-mode .admin-name { color: #e2e8f0 !important; }
    body.dark-mode .admin-meta { color: #94a3b8 !important; }
    body.dark-mode .btn-login-direct { background-color: #1e1b4b !important; color: #a5b4fc !important; }
    body.dark-mode .btn-login-direct:hover { background-color: #4f46e5 !important; color: #ffffff !important; }
    body.dark-mode .btn-edit-school { background-color: rgba(217, 119, 6, 0.15) !important; color: #fcd34d !important; }
    body.dark-mode .btn-edit-school:hover { background-color: #d97706 !important; color: #ffffff !important; }
    body.dark-mode .btn-delete-school { background-color: rgba(239, 68, 68, 0.15) !important; color: #fca5a5 !important; }
    body.dark-mode .btn-delete-school:hover { background-color: #ef4444 !important; color: #ffffff !important; }
    body.dark-mode .btn-toggle-school { background-color: #374151 !important; color: #cbd5e1 !important; }
    body.dark-mode .btn-toggle-school:hover { background-color: #4b5563 !important; color: #ffffff !important; }
</style>
@endsection

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px;">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px;">
        <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="sa-directory-card">
    <div class="sa-directory-hdr">
        <div class="sa-directory-hdr-left">
            <div class="sa-directory-hdr-icon">
                <i class="fas fa-school"></i>
            </div>
            <div>
                <h3>All Registered Schools</h3>
                <p>Manage tenant profiles, view administrative details, check subscriptions, and log in directly</p>
            </div>
        </div>
        <div>
            <a href="{{ route('superadmin.schools.create') }}" class="btn btn-primary" style="border-radius: 10px; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fas fa-plus-circle"></i> Onboard New School
            </a>
        </div>
    </div>

    <div class="p-0">
        <div class="table-responsive">
            <table class="table table-custom m-0">
                <thead>
                    <tr>
                        <th>School Profile</th>
                        <th>Administrator Details</th>
                        <th>Active Subscription</th>
                        <th>Status</th>
                        <th class="text-right">Access Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schools as $school)
                        @php
                            // Get the first user belonging to this school who is a school admin
                            $admin = $school->users->first(function($user) {
                                return $user->hasRole('school_admin');
                            });
                            $sub = $school->subscriptions->first();
                        @endphp
                        <tr>
                            <td>
                                <div class="school-info-cell">
                                    <span class="school-name">{{ $school->name }}</span>
                                    <span class="school-code">
                                        <i class="fas fa-tag mr-1"></i> CODE: {{ $school->code }}
                                        @if($school->state) &bull; <span class="badge badge-secondary" style="font-size: 10px; background-color: #e2e8f0; color: #475569; font-weight: bold; padding: 2px 6px; border-radius: 4px;">{{ $school->state }}</span> @endif
                                        @if($school->school_type) &bull; <span class="text-indigo" style="font-weight: 600; color: #4f46e5;">{{ $school->school_type }}</span> @endif
                                    </span>
                                    @if($school->director_name || $school->email)
                                        <small class="text-muted mt-1">
                                            @if($school->director_name) <strong>Dir:</strong> {{ $school->director_name }} @endif
                                            @if($school->email) &bull; <i class="far fa-envelope mr-1"></i>{{ $school->email }} @endif
                                        </small>
                                    @endif
                                    @if($school->custom_domain)
                                        <span class="text-muted text-xs mt-1" style="font-size: 0.75rem;">
                                            <i class="fas fa-globe mr-1"></i> {{ $school->custom_domain }}
                                        </span>
                                    @endif
                                    <div class="mt-2 pt-2" style="border-top:1px dashed #cbd5e1; font-size:11.5px; display:flex; gap:12px; flex-wrap:wrap;">
                                        <span style="color:#10b981; font-weight:700;" title="Active Students"><i class="fas fa-user-graduate"></i> Active: {{ $school->active_students_count ?? 0 }}</span>
                                        <span>
                                            <a href="{{ route('superadmin.schools.inactive-students', $school->id) }}" style="color:#ef4444; font-weight:700; text-decoration:underline;" title="Manage Inactive Students">
                                                <i class="fas fa-user-slash"></i> Inactive: {{ $school->inactive_students_count ?? 0 }}
                                            </a>
                                        </span>
                                        <span style="color:#64748b; font-weight:700;" title="Staff Members"><i class="fas fa-user-tie"></i> Staff: {{ $school->staff_count ?? 0 }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="admin-info-cell">
                                    @if($admin)
                                        <span class="admin-name">{{ $admin->name }}</span>
                                        <span class="admin-meta"><i class="far fa-id-badge"></i> ID: {{ $admin->id }}</span>
                                        <span class="admin-meta"><i class="far fa-envelope"></i> {{ $admin->email }}</span>
                                        @if($admin->phone)
                                            <span class="admin-meta"><i class="fas fa-phone-alt"></i> {{ $admin->phone }}</span>
                                        @endif
                                    @else
                                        <span class="text-muted italic" style="font-size: 0.82rem;">No administrator registered</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($sub && $sub->plan)
                                    <div>
                                        <span class="badge-plan mb-1">{{ $sub->plan->name }}</span>
                                        <div class="text-xs text-muted" style="font-size: 0.78rem;">
                                            Ends: {{ $sub->subscription_ends_at ? $sub->subscription_ends_at->format('M d, Y') : 'Never' }}
                                        </div>
                                    </div>
                                @else
                                    <span class="badge-no-plan">No Subscription</span>
                                @endif
                            </td>
                            <td>
                                @if($school->status === 'active')
                                    <span class="badge-status-active">Active</span>
                                @elseif($school->status === 'suspended')
                                    <span class="badge-status-suspended">Suspended</span>
                                @else
                                    <span class="badge-status-inactive">Inactive</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="d-flex justify-content-end gap-2 align-items-center">
                                    {{-- Direct Login --}}
                                    @if($admin)
                                        <form action="{{ route('superadmin.schools.impersonate', $school->id) }}" method="POST" style="display:inline-block; margin:0;">
                                            @csrf
                                            <button type="submit" class="btn-sa-action btn-login-direct" title="Login Directly to Dashboard">
                                                <i class="fas fa-sign-in-alt"></i> Login
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn-sa-action btn-login-direct" disabled style="opacity:0.5; cursor:not-allowed;" title="No Admin Registered">
                                            <i class="fas fa-sign-in-alt"></i> Login
                                        </button>
                                    @endif

                                    {{-- Quick Toggle Status --}}
                                    <form action="{{ route('superadmin.schools.toggle-status', $school->id) }}" method="POST" style="display:inline-block; margin:0;">
                                        @csrf
                                        <button type="submit" class="btn-sa-action btn-toggle-school" title="{{ $school->status === 'active' ? 'Suspend School' : 'Activate School' }}">
                                            @if($school->status === 'active')
                                                <i class="fas fa-pause-circle"></i> Suspend
                                            @else
                                                <i class="fas fa-play-circle"></i> Activate
                                            @endif
                                        </button>
                                    </form>

                                    {{-- Edit --}}
                                    <a href="{{ route('superadmin.schools.edit', $school->id) }}" class="btn-sa-action btn-edit-school" title="Edit School Details & Reset Password">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>

                                    {{-- Delete --}}
                                    <form action="{{ route('superadmin.schools.destroy', $school->id) }}" method="POST" style="display:inline-block; margin:0;" onsubmit="return confirm('Are you absolutely sure you want to delete this school and ALL its users/students? This action is irreversible!');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-sa-action btn-delete-school" title="Delete School">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No schools registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
