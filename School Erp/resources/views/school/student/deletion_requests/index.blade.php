@extends('layouts.app')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-user-shield" style="color:#dc2626;margin-right:8px;"></i>Student Deletion Requests</h1>
        <p>Review deletion approval requests and manage permanent student deletion audit history</p>
    </div>
    <div class="page-hdr-right">
        <a href="{{ route('school.students.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Back to Directory
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success" style="background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; padding:12px 16px; border-radius:8px; margin-bottom:20px;">
        <i class="fas fa-check-circle" style="margin-right:6px;"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger" style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:12px 16px; border-radius:8px; margin-bottom:20px;">
        <i class="fas fa-exclamation-circle" style="margin-right:6px;"></i> {{ session('error') }}
    </div>
@endif

<!-- Deletion Requests Card -->
<div class="card">
    <div class="card-hdr" style="padding: 14px 20px; display:flex; justify-content:space-between; align-items:center;">
        <h3 style="margin:0; font-size:14.5px; font-weight:700;">
            <i class="fas fa-list-alt" style="color:#1d4ed8;margin-right:8px;"></i>Approval & Audit Requests Log
            <span class="badge badge-blue" style="margin-left:8px; font-size:11.5px; padding:4px 10px; border-radius:12px;">{{ $requests->total() }}</span>
        </h3>
    </div>

    <!-- Filter Tabs -->
    <div class="student-tabs-wrapper" style="border-bottom:1px solid var(--border); padding: 0 16px; background:var(--card);">
        <div style="display:flex; gap:10px; padding:10px 0;">
            <a href="{{ route('school.students.deletion-requests.index', ['status' => 'all']) }}" 
               class="btn {{ $status === 'all' ? 'btn-erp-primary' : 'btn-outline' }}" style="font-size:12px; padding:6px 14px; border-radius:6px;">
                All Records
            </a>
            <a href="{{ route('school.students.deletion-requests.index', ['status' => 'pending']) }}" 
               class="btn {{ $status === 'pending' ? 'btn-erp-primary' : 'btn-outline' }}" style="font-size:12px; padding:6px 14px; border-radius:6px; background: {{ $status === 'pending' ? '#f59e0b' : '' }}; border-color: {{ $status === 'pending' ? '#f59e0b' : '' }};">
                <i class="fas fa-clock"></i> Pending Approval
            </a>
            <a href="{{ route('school.students.deletion-requests.index', ['status' => 'approved']) }}" 
               class="btn {{ $status === 'approved' ? 'btn-erp-primary' : 'btn-outline' }}" style="font-size:12px; padding:6px 14px; border-radius:6px; background: {{ $status === 'approved' ? '#10b981' : '' }}; border-color: {{ $status === 'approved' ? '#10b981' : '' }};">
                <i class="fas fa-check-circle"></i> Approved
            </a>
            <a href="{{ route('school.students.deletion-requests.index', ['status' => 'rejected']) }}" 
               class="btn {{ $status === 'rejected' ? 'btn-erp-primary' : 'btn-outline' }}" style="font-size:12px; padding:6px 14px; border-radius:6px; background: {{ $status === 'rejected' ? '#ef4444' : '' }}; border-color: {{ $status === 'rejected' ? '#ef4444' : '' }};">
                <i class="fas fa-times-circle"></i> Rejected
            </a>
        </div>
    </div>

    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Admission ID</th>
                        <th>Student Name</th>
                        <th>Class & Sec</th>
                        <th>Requested By</th>
                        <th>Requested Time</th>
                        <th style="text-align:center;">Status</th>
                        <th>Approved / Rejected Info</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr>
                        <td><strong>{{ $req->admission_number ?? '—' }}</strong></td>
                        <td>
                            <strong style="color:var(--t1);">{{ $req->student_name }}</strong>
                            @if($req->student_id)
                                <div style="font-size:11px;color:var(--t3);">Student ID: #{{ $req->student_id }}</div>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $req->class_name ?? '—' }}</strong>
                            <span style="font-size:12px;color:var(--t2);">({{ $req->section_name ?? '—' }})</span>
                        </td>
                        <td>
                            <div>{{ $req->requested_by_name ?? 'Staff' }}</div>
                            <div style="font-size:11px;color:var(--t3);">User ID: #{{ $req->requested_by }}</div>
                        </td>
                        <td>
                            <div>{{ $req->requested_at ? $req->requested_at->format('d M Y') : $req->created_at->format('d M Y') }}</div>
                            <div style="font-size:11px;color:var(--t3);">{{ $req->requested_at ? $req->requested_at->format('h:i A') : $req->created_at->format('h:i A') }}</div>
                        </td>
                        <td style="text-align:center;">
                            @if($req->status === 'pending')
                                <span class="badge" style="background:#fef3c7; color:#d97706; border:1px solid #fde68a; padding:4px 10px; border-radius:12px; font-weight:700;">
                                    <i class="fas fa-clock" style="margin-right:4px;"></i>Pending Approval
                                </span>
                            @elseif($req->status === 'approved')
                                <span class="badge" style="background:#d1fae5; color:#059669; border:1px solid #a7f3d0; padding:4px 10px; border-radius:12px; font-weight:700;">
                                    <i class="fas fa-check-circle" style="margin-right:4px;"></i>Approved
                                </span>
                            @else
                                <span class="badge" style="background:#fee2e2; color:#dc2626; border:1px solid #fecaca; padding:4px 10px; border-radius:12px; font-weight:700;">
                                    <i class="fas fa-times-circle" style="margin-right:4px;"></i>Rejected
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($req->status === 'approved')
                                <div style="color:#059669; font-weight:600; font-size:12px;">By: {{ $req->approved_by_name ?? 'School Admin' }}</div>
                                <div style="font-size:11px; color:var(--t3);">At: {{ $req->approved_at ? $req->approved_at->format('d M Y, h:i A') : '—' }}</div>
                            @elseif($req->status === 'rejected')
                                <div style="color:#dc2626; font-weight:600; font-size:12px;">By: {{ $req->rejected_by_name ?? 'School Admin' }}</div>
                                <div style="font-size:11px; color:var(--t3);">At: {{ $req->rejected_at ? $req->rejected_at->format('d M Y, h:i A') : '—' }}</div>
                                @if($req->rejection_reason)
                                    <div style="font-size:11px; color:#991b1b; font-style:italic;">Reason: {{ $req->rejection_reason }}</div>
                                @endif
                            @else
                                <span style="color:var(--t3); font-style:italic; font-size:12px;">Awaiting Review</span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            @if($req->status === 'pending')
                                @if(auth()->check() && (auth()->user()->hasRole('school_admin') || auth()->user()->hasRole('superadmin') || auth()->user()->role === 'school_admin' || auth()->user()->role === 'superadmin'))
                                    <div style="display:inline-flex; gap:6px;">
                                        <form action="{{ route('school.students.deletion-requests.approve', $req->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to approve this student deletion? The student will be permanently deleted.');">
                                            @csrf
                                            <button type="submit" class="btn btn-success" style="padding:5px 12px; font-size:11.5px; background:#10b981; border-color:#10b981; color:#fff;" title="Approve Deletion">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        </form>

                                        <form action="{{ route('school.students.deletion-requests.reject', $req->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to reject this deletion request?');">
                                            @csrf
                                            <button type="submit" class="btn btn-danger" style="padding:5px 12px; font-size:11.5px;" title="Reject Deletion">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span style="font-size:11px; color:var(--t3); font-style:italic;">Admin Only</span>
                                @endif
                            @else
                                <span style="font-size:11px; color:var(--t3); font-style:italic;">Completed</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center; padding:48px; color:var(--t3);">
                            <i class="fas fa-shield-alt" style="font-size:36px; display:block; margin-bottom:12px; color:var(--border);"></i>
                            <div>No student deletion requests found.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="padding:14px 20px; border-top:1px solid var(--border); display:flex; justify-content:flex-end;">
            {{ $requests->appends(request()->all())->links('partials.pagination') }}
        </div>
    </div>
</div>
@endsection
