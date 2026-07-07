@extends('superadmin.layouts.master')

@section('styles')
<style>
    /* ─── SCHOOL REQUESTS DIRECTORY ────────────────────────────────────── */
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

    .badge-status-pending {
        background-color: #fef3c7;
        color: #d97706;
        font-weight: 700;
        font-size: 0.72rem;
        padding: 4px 8px;
        border-radius: 8px;
        display: inline-block;
    }

    .badge-status-approved {
        background-color: #ecfdf5;
        color: #10b981;
        font-weight: 700;
        font-size: 0.72rem;
        padding: 4px 8px;
        border-radius: 8px;
        display: inline-block;
    }

    .badge-status-rejected {
        background-color: #fef2f2;
        color: #ef4444;
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

    .btn-approve-request {
        background-color: #ecfdf5;
        color: #10b981;
    }

    .btn-approve-request:hover {
        background-color: #10b981;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);
    }

    .btn-reject-request {
        background-color: #fee2e2;
        color: #ef4444;
    }

    .btn-reject-request:hover {
        background-color: #ef4444;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2);
    }

    /* Custom nav tabs */
    .sa-tabs {
        border-bottom: 2px solid #f1f5f9;
        background: #ffffff;
        padding: 0 24px;
    }
    .sa-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        padding: 16px 20px;
        font-size: 13.5px;
        font-weight: 700;
        color: #64748b;
        transition: all 0.2s;
    }
    .sa-tabs .nav-link:hover {
        color: #6366f1;
    }
    .sa-tabs .nav-link.active {
        background: transparent;
        color: #6366f1;
        border-bottom-color: #6366f1;
    }

    /* Dark mode overrides */
    body.dark-mode .sa-directory-card { background: #111827 !important; border-color: #1e293b !important; }
    body.dark-mode .sa-directory-hdr { border-bottom-color: #1e293b !important; }
    body.dark-mode .sa-directory-hdr h3 { color: #f1f5f9 !important; }
    body.dark-mode .sa-directory-hdr p { color: #64748b !important; }
    body.dark-mode .sa-tabs { background: #111827 !important; border-bottom-color: #1e293b !important; }
    body.dark-mode .sa-tabs .nav-link { color: #94a3b8 !important; }
    body.dark-mode .sa-tabs .nav-link.active { color: #818cf8 !important; border-bottom-color: #818cf8 !important; }
    body.dark-mode .table-custom th { border-bottom-color: #1e293b !important; color: #94a3b8 !important; }
    body.dark-mode .table-custom td { border-bottom-color: #1e293b !important; color: #cbd5e1 !important; }
    body.dark-mode .school-name { color: #f1f5f9 !important; }
    body.dark-mode .admin-name { color: #e2e8f0 !important; }
    body.dark-mode .admin-meta { color: #94a3b8 !important; }
    body.dark-mode .btn-approve-request { background-color: rgba(16, 185, 129, 0.15) !important; color: #34d399 !important; }
    body.dark-mode .btn-approve-request:hover { background-color: #10b981 !important; color: #ffffff !important; }
    body.dark-mode .btn-reject-request { background-color: rgba(239, 68, 68, 0.15) !important; color: #fca5a5 !important; }
    body.dark-mode .dark-mode .btn-reject-request:hover { background-color: #ef4444 !important; color: #ffffff !important; }
</style>
@endsection

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px; background-color: #ecfdf5; color: #065f46; border: none;">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        <button type="button" class="close text-success" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px; background-color: #fef2f2; color: #991b1b; border: none;">
        <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
        <button type="button" class="close text-danger" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="sa-directory-card">
    <div class="sa-directory-hdr">
        <div class="sa-directory-hdr-left">
            <div class="sa-directory-hdr-icon">
                <i class="fas fa-file-signature"></i>
            </div>
            <div>
                <h3>School Registration Requests</h3>
                <p>Review and approve new school sign-up requests, select plans, and automatically provision administrator accounts</p>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs sa-tabs" id="requestTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="pending-tab" data-toggle="tab" href="#pending" role="tab" aria-controls="pending" aria-selected="true">
                Pending Requests <span class="badge badge-warning ml-1">{{ $pendingRequests->count() }}</span>
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="approved-tab" data-toggle="tab" href="#approved" role="tab" aria-controls="approved" aria-selected="false">
                Approved Requests <span class="badge badge-success ml-1">{{ $approvedRequests->count() }}</span>
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="rejected-tab" data-toggle="tab" href="#rejected" role="tab" aria-controls="rejected" aria-selected="false">
                Rejected Requests <span class="badge badge-danger ml-1">{{ $rejectedRequests->count() }}</span>
            </a>
        </li>
    </ul>

    <div class="tab-content" id="requestTabsContent">
        
        <!-- Tab 1: PENDING -->
        <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
            @if($pendingRequests->isEmpty())
                <div class="py-5 text-center">
                    <img src="https://illustrations.popsy.co/blue/waiting-list.svg" alt="No requests" style="height: 160px; margin-bottom: 15px;">
                    <h5 class="text-muted">No Pending Requests</h5>
                    <p class="text-muted font-weight-normal mb-0">All school registration requests have been processed.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th>School Details</th>
                                <th>Administrator</th>
                                <th>Requested Plan</th>
                                <th>Submitted At</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingRequests as $req)
                                <tr>
                                    <td>
                                        <div class="school-info-cell">
                                            <span class="school-name">{{ $req->name }}</span>
                                            <span class="school-code">
                                                <i class="fas fa-tag mr-1"></i> {{ $req->code }}
                                                @if($req->state) &bull; <span class="badge badge-secondary" style="font-size: 10px; background-color: #e2e8f0; color: #475569; font-weight: bold; padding: 2px 6px; border-radius: 4px;">{{ $req->state }}</span> @endif
                                                @if($req->school_type) &bull; <span class="text-indigo" style="font-weight: 600; color: #4f46e5;">{{ $req->school_type }}</span> @endif
                                            </span>
                                            @if($req->director_name || $req->email || $req->phone)
                                                <small class="text-muted mt-1">
                                                    @if($req->director_name) <strong>Dir:</strong> {{ $req->director_name }} @endif
                                                    @if($req->email) &bull; <i class="far fa-envelope mr-1"></i>{{ $req->email }} @endif
                                                    @if($req->phone) &bull; <i class="fas fa-phone mr-1"></i>{{ $req->phone }} @endif
                                                </small>
                                            @endif
                                            @if($req->academic_session_name)
                                                <small class="text-primary mt-1" style="font-weight: 600; color: #2563eb;">
                                                    <i class="far fa-calendar-alt mr-1"></i> Session: {{ $req->academic_session_name }} 
                                                    ({{ \Carbon\Carbon::parse($req->academic_session_start_date)->format('M Y') }} - {{ \Carbon\Carbon::parse($req->academic_session_end_date)->format('M Y') }})
                                                </small>
                                            @endif
                                            @if($req->address)
                                                <small class="text-muted mt-1">
                                                    <i class="fas fa-map-marker-alt mr-1"></i>{{ Str::limit($req->address, 50) }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="admin-info-cell">
                                            <span class="admin-name">{{ $req->admin_name }}</span>
                                            <span class="admin-meta"><i class="far fa-envelope"></i> {{ $req->admin_email }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($req->plan)
                                            <span class="badge-plan"><i class="fas fa-layer-group mr-1"></i> {{ $req->plan->name }}</span>
                                        @else
                                            <span class="text-muted font-italic">No plan selected</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $req->created_at->format('M d, Y h:i A') }}</span>
                                    </td>
                                    <td class="text-right">
                                        <div class="d-flex justify-content-end gap-2" style="gap: 8px;">
                                            <form action="{{ route('superadmin.school-requests.approve', $req->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to approve this request? This will create the school and admin user.');">
                                                @csrf
                                                <button type="submit" class="btn-sa-action btn-approve-request">
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                            </form>
                                            <button type="button" class="btn-sa-action btn-reject-request" data-toggle="modal" data-target="#rejectModal" data-id="{{ $req->id }}" data-name="{{ $req->name }}">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Tab 2: APPROVED -->
        <div class="tab-pane fade" id="approved" role="tabpanel" aria-labelledby="approved-tab">
            @if($approvedRequests->isEmpty())
                <div class="py-5 text-center">
                    <h5 class="text-muted">No Approved Requests</h5>
                    <p class="text-muted font-weight-normal mb-0">Approved requests will be listed here.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th>School Details</th>
                                <th>Administrator</th>
                                <th>Requested Plan</th>
                                <th>Processed At</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($approvedRequests as $req)
                                <tr>
                                    <td>
                                        <div class="school-info-cell">
                                            <span class="school-name">{{ $req->name }}</span>
                                            <span class="school-code">
                                                <i class="fas fa-tag mr-1"></i> {{ $req->code }}
                                                @if($req->state) &bull; <span class="badge badge-secondary" style="font-size: 10px; background-color: #e2e8f0; color: #475569; font-weight: bold; padding: 2px 6px; border-radius: 4px;">{{ $req->state }}</span> @endif
                                                @if($req->school_type) &bull; <span class="text-indigo" style="font-weight: 600; color: #4f46e5;">{{ $req->school_type }}</span> @endif
                                            </span>
                                            @if($req->director_name || $req->email)
                                                <small class="text-muted mt-1">
                                                    @if($req->director_name) <strong>Dir:</strong> {{ $req->director_name }} @endif
                                                    @if($req->email) &bull; <i class="far fa-envelope mr-1"></i>{{ $req->email }} @endif
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="admin-info-cell">
                                            <span class="admin-name">{{ $req->admin_name }}</span>
                                            <span class="admin-meta"><i class="far fa-envelope"></i> {{ $req->admin_email }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($req->plan)
                                            <span class="badge-plan">{{ $req->plan->name }}</span>
                                        @else
                                            <span class="text-muted font-italic">No plan selected</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $req->updated_at->format('M d, Y h:i A') }}</span>
                                    </td>
                                    <td>
                                        <span class="badge-status-approved"><i class="fas fa-check-circle mr-1"></i> Approved</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Tab 3: REJECTED -->
        <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
            @if($rejectedRequests->isEmpty())
                <div class="py-5 text-center">
                    <h5 class="text-muted">No Rejected Requests</h5>
                    <p class="text-muted font-weight-normal mb-0">Rejected requests will be listed here.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th>School Details</th>
                                <th>Administrator</th>
                                <th>Rejection Reason</th>
                                <th>Processed At</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rejectedRequests as $req)
                                <tr>
                                    <td>
                                        <div class="school-info-cell">
                                            <span class="school-name">{{ $req->name }}</span>
                                            <span class="school-code">
                                                <i class="fas fa-tag mr-1"></i> {{ $req->code }}
                                                @if($req->state) &bull; <span class="badge badge-secondary" style="font-size: 10px; background-color: #e2e8f0; color: #475569; font-weight: bold; padding: 2px 6px; border-radius: 4px;">{{ $req->state }}</span> @endif
                                                @if($req->school_type) &bull; <span class="text-indigo" style="font-weight: 600; color: #4f46e5;">{{ $req->school_type }}</span> @endif
                                            </span>
                                            @if($req->director_name || $req->email)
                                                <small class="text-muted mt-1">
                                                    @if($req->director_name) <strong>Dir:</strong> {{ $req->director_name }} @endif
                                                    @if($req->email) &bull; <i class="far fa-envelope mr-1"></i>{{ $req->email }} @endif
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="admin-info-cell">
                                            <span class="admin-name">{{ $req->admin_name }}</span>
                                            <span class="admin-meta"><i class="far fa-envelope"></i> {{ $req->admin_email }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-danger font-weight-bold" style="font-size: 0.82rem;">
                                            {{ $req->rejected_reason ?? 'No reason provided' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $req->updated_at->format('M d, Y h:i A') }}</span>
                                    </td>
                                    <td>
                                        <span class="badge-status-rejected"><i class="fas fa-times-circle mr-1"></i> Rejected</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</div>

<!-- Reject Rejection Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="" method="POST" id="rejectForm">
            @csrf
            <div class="modal-content" style="border-radius: 16px;">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="rejectModalLabel">Reject Registration Request</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>You are about to reject the request from <strong id="rejectSchoolName"></strong>. If you wish, you can specify a reason below which will be recorded.</p>
                    <div class="form-group">
                        <label for="rejected_reason" class="font-weight-bold">Rejection Reason</label>
                        <textarea class="form-control" name="rejected_reason" id="rejected_reason" rows="4" placeholder="e.g. Invalid school code or duplicate name requested." style="border-radius: 10px; resize: none;"></textarea>
                    </div>
                </div>
                <div class="modal-body pt-0 d-flex justify-content-end gap-2" style="gap: 8px;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 10px; font-weight: 600;">Cancel</button>
                    <button type="submit" class="btn btn-danger" style="border-radius: 10px; font-weight: 600;">Reject Request</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $('#rejectModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var name = button.data('name');
        
        var modal = $(this);
        modal.find('#rejectSchoolName').text(name);
        
        // Update form action dynamically
        var actionUrl = "{{ route('superadmin.school-requests.reject', ':id') }}";
        actionUrl = actionUrl.replace(':id', id);
        modal.find('#rejectForm').attr('action', actionUrl);
    });
</script>
@endsection
