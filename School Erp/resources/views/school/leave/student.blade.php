@extends('layouts.app')

@section('page-title', 'Student Leave Applications')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-graduation-cap" style="color:var(--gold);margin-right:8px;"></i>Student Leave Management</h1>
        <p>Review and act on leave applications submitted by parents/students</p>
    </div>
</div>

<div class="card">
    <div class="card-hdr">
        <h3>Recent Student Applications</h3>
    </div>
    <div class="card-body">
        <div class="table-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Leave Type</th>
                        <th>Dates</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $app)
                    <tr>
                        <td>
                            <strong>{{ $app->user->name }}</strong><br>
                            <span style="font-size:11px;color:var(--t2);">{{ $app->user->email }}</span>
                        </td>
                        <td><span class="badge badge-purple">{{ $app->leave_type }}</span></td>
                        <td>
                            {{ $app->start_date }} to {{ $app->end_date }}
                        </td>
                        <td>{{ $app->reason }}</td>
                        <td>
                            @if($app->status === 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @elseif($app->status === 'approved')
                                <span class="badge badge-success">Approved</span>
                            @else
                                <span class="badge badge-danger">Rejected</span>
                            @endif
                        </td>
                        <td>
                            @if($app->status === 'pending')
                            <div style="display:flex; gap:6px;">
                                <form method="POST" action="{{ route('school.leave.student') }}">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $app->id }}">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-success" style="padding:4px 8px; font-size:11px;"><i class="fas fa-check"></i> Approve</button>
                                </form>
                                <form method="POST" action="{{ route('school.leave.student') }}">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $app->id }}">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="btn btn-danger" style="padding:4px 8px; font-size:11px;"><i class="fas fa-times"></i> Reject</button>
                                </form>
                            </div>
                            @else
                                <span style="font-size:11px;color:var(--t3);">No Action Required</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding:30px; color:var(--t3);">No student leave applications found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
