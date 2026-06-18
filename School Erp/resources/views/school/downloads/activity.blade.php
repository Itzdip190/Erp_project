@extends('layouts.app')

@section('page-title', 'User Session Activity & Audit Logs')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-history" style="color:var(--gold);margin-right:8px;"></i>User Session Activity</h1>
        <p>Monitor user login logs, access histories, failed authentication attempts, IP geo audit records, and security telemetry</p>
    </div>
    <div class="page-hdr-right">
    </div>
</div>

<div class="card">
    <div class="card-hdr">
        <h3>Login Audit Trails</h3>
        <span style="font-size:12.5px; color:var(--t2);">Total Session Records Checked: {{ $logs->total() }}</span>
    </div>
    <div class="card-body" style="padding:0;">
        <table class="tbl">
            <thead>
                <tr>
                    <th>User / Login Attempt</th>
                    <th>Role Category</th>
                    <th>IP Address</th>
                    <th>Device Info / Browser</th>
                    <th>Login Status</th>
                    <th>Timestamp (Local)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>
                            @if($log->user)
                                <strong style="color:var(--navy);">{{ $log->user->name }}</strong>
                                <small style="display:block; color:var(--t3);">{{ $log->user->email }}</small>
                            @else
                                <strong style="color:var(--red);">{{ $log->email_attempted }}</strong>
                                <small style="display:block; color:var(--t3);">Unregistered Email Attempt</small>
                            @endif
                        </td>
                        <td>
                            @if($log->user)
                                <span class="badge badge-blue">{{ ucfirst($log->user->roles->first()?->name ?? 'User') }}</span>
                            @else
                                <span class="badge badge-danger">GUEST</span>
                            @endif
                        </td>
                        <td>
                            <code style="font-family:monospace; background:var(--page); padding:2px 6px; border-radius:4px; font-size:12px; color:var(--purple);">
                                {{ $log->ip_address }}
                            </code>
                        </td>
                        <td>
                            <span style="font-size:12px; color:var(--t2);" title="{{ $log->user_agent }}">
                                {{ Str::limit($log->user_agent, 45) }}
                            </span>
                        </td>
                        <td>
                            @if($log->status === 'success')
                                <span class="badge badge-success"><i class="fas fa-check-circle"></i> Success</span>
                            @else
                                <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Failed</span>
                            @endif
                        </td>
                        <td>
                            <span style="font-size:12px; color:var(--t2);">{{ $log->created_at->format('Y-m-d h:i:s A') }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding:40px; color:var(--t3);">
                            <i class="fas fa-shield-halved" style="font-size:32px; color:var(--t3); margin-bottom:12px; display:block;"></i>
                            No activity logs recorded.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Pagination Links -->
        @if($logs->hasPages())
            <div style="padding:20px; border-top:1px solid var(--border); display:flex; justify-content:center;">
                {!! $logs->links() !!}
            </div>
        @endif
    </div>
</div>
@endsection
