@extends('superadmin.layouts.master')

@section('styles')
<style>
    .sa-directory-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
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
        flex-wrap: wrap;
    }
    .sa-metric-card {
        background: #ffffff;
        border-radius: 14px;
        padding: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        display: flex;
        align-items: center;
        gap: 16px;
        height: 100%;
    }
    .sa-metric-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .badge-status-pending { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; font-weight: 700; }
    .badge-status-contacted { background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd; font-weight: 700; }
    .badge-status-completed { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; font-weight: 700; }
    .table-custom th {
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #f1f5f9 !important;
        padding: 14px 16px !important;
    }
    .table-custom td {
        font-size: 0.88rem;
        color: #1e1b4b;
        vertical-align: middle !important;
        padding: 14px 16px !important;
        border-bottom: 1px solid #f1f5f9 !important;
    }
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 font-heading text-dark fw-bold">Demo Requests</h1>
                <p class="text-muted small mb-0">Manage lead inquiries, live 1-on-1 walkthrough bookings, and Calendly sync requests.</p>
            </div>
            <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
                <a href="{{ route('landing.book-demo') }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill font-heading">
                    <i class="fas fa-external-link-alt me-1"></i> Preview Landing Page
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Metrics Row -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-sm-6">
                <div class="sa-metric-card">
                    <div class="sa-metric-icon bg-primary-subtle text-primary">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div>
                        <div class="text-muted small font-heading">Total Bookings</div>
                        <div class="h4 font-heading fw-bold mb-0 text-dark">{{ number_format($totalCount) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="sa-metric-card">
                    <div class="sa-metric-icon bg-warning-subtle text-warning">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div>
                        <div class="text-muted small font-heading">Pending Requests</div>
                        <div class="h4 font-heading fw-bold mb-0 text-warning">{{ number_format($pendingCount) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="sa-metric-card">
                    <div class="sa-metric-icon bg-info-subtle text-info">
                        <i class="fas fa-phone-volume"></i>
                    </div>
                    <div>
                        <div class="text-muted small font-heading">Contacted Leads</div>
                        <div class="h4 font-heading fw-bold mb-0 text-info">{{ number_format($contactedCount) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="sa-metric-card">
                    <div class="sa-metric-icon bg-success-subtle text-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <div class="text-muted small font-heading">Completed Demos</div>
                        <div class="h4 font-heading fw-bold mb-0 text-success">{{ number_format($completedCount) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Card Section -->
        <div class="sa-directory-card">
            
            <!-- Card Header with Filters -->
            <div class="sa-directory-hdr">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-list-ul text-primary"></i>
                    <h3 class="h6 font-heading mb-0 text-dark">Demo Booking Records</h3>
                </div>

                <form method="GET" action="{{ route('superadmin.demo-requests.index') }}" class="form-inline gap-2">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="Search name, email, phone, institute..." value="{{ request('search') }}">
                    </div>

                    <select name="status" class="form-control form-control-sm">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="contacted" {{ request('status') === 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>

                    <button type="submit" class="btn btn-primary btn-sm rounded-pill font-heading">
                        <i class="fas fa-search me-1"></i> Filter
                    </button>

                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('superadmin.demo-requests.index') }}" class="btn btn-secondary btn-sm rounded-pill font-heading">
                            <i class="fas fa-undo me-1"></i> Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Table Body -->
            <div class="table-responsive">
                <table class="table table-custom table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th># ID</th>
                            <th>Prospect Details</th>
                            <th>Institute & Role</th>
                            <th>Booking Date & Time</th>
                            <th>Source</th>
                            <th>Status</th>
                            <th>Created Date</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($demoBookings as $booking)
                            <tr>
                                <td>
                                    <span class="fw-bold text-secondary">#{{ $booking->id }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $booking->full_name }}</div>
                                    <div class="small text-muted">
                                        <a href="mailto:{{ $booking->email }}"><i class="fas fa-envelope text-primary me-1"></i>{{ $booking->email }}</a>
                                    </div>
                                    <div class="small text-muted">
                                        <a href="tel:{{ $booking->phone }}"><i class="fas fa-phone-alt text-success me-1"></i>{{ $booking->phone }}</a>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $booking->institute_name ?? 'N/A' }}</div>
                                    <div class="small text-secondary">{{ $booking->role }}</div>
                                    @if($booking->student_count)
                                        <span class="badge badge-light text-muted border mt-1">{{ $booking->student_count }} Students</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold text-primary">
                                        <i class="fas fa-calendar-day me-1"></i> {{ $booking->booking_date ?? 'N/A' }}
                                    </div>
                                    <div class="small text-dark fw-bold">
                                        <i class="fas fa-clock me-1"></i> {{ $booking->booking_time ?? 'N/A' }}
                                    </div>
                                    <div class="small text-muted">{{ $booking->timezone ?? 'IST' }}</div>
                                </td>
                                <td>
                                    <span class="badge badge-info px-2 py-1">{{ $booking->source ?? 'Website' }}</span>
                                </td>
                                <td>
                                    @if($booking->status === 'completed')
                                        <span class="badge badge-status-completed px-3 py-1 rounded-pill">Completed</span>
                                    @elseif($booking->status === 'contacted')
                                        <span class="badge badge-status-contacted px-3 py-1 rounded-pill">Contacted</span>
                                    @else
                                        <span class="badge badge-status-pending px-3 py-1 rounded-pill">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="small text-dark">{{ $booking->created_at ? $booking->created_at->format('d M Y') : 'N/A' }}</div>
                                    <div class="small text-muted">{{ $booking->created_at ? $booking->created_at->format('h:i A') : '' }}</div>
                                </td>
                                <td class="text-right">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-light text-primary border rounded-pill me-1 btn-view-booking"
                                            data-id="{{ $booking->id }}"
                                            data-name="{{ $booking->full_name }}"
                                            data-email="{{ $booking->email }}"
                                            data-phone="{{ $booking->phone }}"
                                            data-institute="{{ $booking->institute_name ?? 'N/A' }}"
                                            data-role="{{ $booking->role }}"
                                            data-students="{{ $booking->student_count ?? 'N/A' }}"
                                            data-date="{{ $booking->booking_date ?? 'N/A' }}"
                                            data-time="{{ $booking->booking_time ?? 'N/A' }}"
                                            data-tz="{{ $booking->timezone ?? 'N/A' }}"
                                            data-source="{{ $booking->source ?? 'Website' }}"
                                            data-location="{{ $booking->city }}, {{ $booking->state }}, {{ $booking->country }}"
                                            data-notes="{{ $booking->message }}"
                                            data-status="{{ $booking->status }}">
                                            <i class="fas fa-eye me-1"></i> View
                                        </button>

                                        <!-- Status Change Dropdown -->
                                        <button type="button" class="btn btn-sm btn-light border dropdown-toggle rounded-pill me-1" data-toggle="dropdown">
                                            Status
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <form action="{{ route('superadmin.demo-requests.update-status', $booking->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="pending">
                                                <button type="submit" class="dropdown-item text-warning font-weight-bold">
                                                    <i class="fas fa-hourglass-half me-2"></i> Mark Pending
                                                </button>
                                            </form>
                                            <form action="{{ route('superadmin.demo-requests.update-status', $booking->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="contacted">
                                                <button type="submit" class="dropdown-item text-info font-weight-bold">
                                                    <i class="fas fa-phone-volume me-2"></i> Mark Contacted
                                                </button>
                                            </form>
                                            <form action="{{ route('superadmin.demo-requests.update-status', $booking->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="dropdown-item text-success font-weight-bold">
                                                    <i class="fas fa-check-circle me-2"></i> Mark Completed
                                                </button>
                                            </form>
                                        </div>

                                        <!-- Delete Button -->
                                        <form action="{{ route('superadmin.demo-requests.destroy', $booking->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this demo request record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger border-0 rounded-circle" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 text-secondary"></i>
                                        <p class="h6 mb-1">No Demo Requests Found</p>
                                        <p class="small text-muted">When prospects schedule a demo on the website or Calendly, requests will appear here.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($demoBookings->hasPages())
                <div class="p-3 border-top d-flex justify-content-between align-items-center">
                    <div class="small text-muted">
                        Showing {{ $demoBookings->firstItem() }} to {{ $demoBookings->lastItem() }} of {{ $demoBookings->total() }} entries
                    </div>
                    <div>
                        {{ $demoBookings->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- View Booking Details Modal -->
<div class="modal fade" id="viewDemoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white border-0 py-3">
                <h5 class="modal-title font-heading fw-bold" id="modalTitle">Demo Booking Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="card bg-light border-0 p-3 rounded-3">
                            <span class="text-uppercase text-muted small fw-bold mb-1">Client Contact</span>
                            <div id="mName" class="h6 font-heading text-dark fw-bold mb-1"></div>
                            <div id="mEmail" class="text-primary small mb-1"></div>
                            <div id="mPhone" class="text-secondary small"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light border-0 p-3 rounded-3">
                            <span class="text-uppercase text-muted small fw-bold mb-1">Schedule Info</span>
                            <div id="mDateTime" class="h6 font-heading text-primary fw-bold mb-1"></div>
                            <div id="mTimezone" class="text-dark small mb-1"></div>
                            <div id="mSource" class="small text-muted"></div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0">
                        <tr>
                            <th class="bg-light text-secondary" style="width: 30%;">Institute Name</th>
                            <td id="mInstitute" class="fw-bold"></td>
                        </tr>
                        <tr>
                            <th class="bg-light text-secondary">Role / Designation</th>
                            <td id="mRole"></td>
                        </tr>
                        <tr>
                            <th class="bg-light text-secondary">Student Count</th>
                            <td id="mStudents"></td>
                        </tr>
                        <tr>
                            <th class="bg-light text-secondary">Location</th>
                            <td id="mLocation"></td>
                        </tr>
                        <tr>
                            <th class="bg-light text-secondary">Status</th>
                            <td id="mStatus"></td>
                        </tr>
                    </table>
                </div>

                <div class="mt-3">
                    <label class="font-heading text-dark fw-bold small mb-1">Prospect Notes / Requirements:</label>
                    <div id="mNotes" class="p-3 bg-light rounded-3 text-secondary italic small border-start border-primary border-4"></div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary rounded-pill font-heading" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.btn-view-booking').on('click', function() {
            var btn = $(this);
            $('#mName').text(btn.data('name'));
            $('#mEmail').html('<i class="fas fa-envelope me-1"></i>' + btn.data('email'));
            $('#mPhone').html('<i class="fas fa-phone-alt me-1"></i>' + btn.data('phone'));
            $('#mDateTime').html('<i class="fas fa-calendar-alt me-1"></i> ' + btn.data('date') + ' &nbsp;|&nbsp; ' + btn.data('time'));
            $('#mTimezone').text('Timezone: ' + btn.data('tz'));
            $('#mSource').html('Source: <span class="badge badge-info">' + btn.data('source') + '</span>');
            $('#mInstitute').text(btn.data('institute'));
            $('#mRole').text(btn.data('role'));
            $('#mStudents').text(btn.data('students'));
            $('#mLocation').text(btn.data('location'));
            $('#mNotes').text('"' + (btn.data('notes') || 'No additional notes provided.') + '"');

            var statusStr = btn.data('status');
            var statusBadge = '<span class="badge badge-warning">Pending</span>';
            if (statusStr === 'contacted') {
                statusBadge = '<span class="badge badge-info">Contacted</span>';
            } else if (statusStr === 'completed') {
                statusBadge = '<span class="badge badge-success">Completed</span>';
            }
            $('#mStatus').html(statusBadge);

            $('#viewDemoModal').modal('show');
        });
    });
</script>
@endsection
