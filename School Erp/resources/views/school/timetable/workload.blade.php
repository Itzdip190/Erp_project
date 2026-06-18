@extends('layouts.app')

@section('page-title', 'Teacher Workload')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-chart-pie" style="color:var(--gold);margin-right:8px;"></i>Teacher Workload Metrics</h1>
        <p>Weekly teaching period tracking, load balance reports, and assignment metrics</p>
    </div>
</div>

<div class="card">
    <div class="card-hdr">
        <h3>Teacher Workload Summary List</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Teacher Name</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th>Weekly Assigned Periods</th>
                        <th>Workload Level</th>
                        <th style="width:200px;">Load Balance Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($workloads as $row)
                        @php
                            $pCount = $row['periods'];
                            // Calculate percentage workload based on maximum standard of 24 periods per week
                            $pct = min(100, round(($pCount / 20) * 100));
                        @endphp
                        <tr>
                            <td>
                                <div style="font-weight:700; color:var(--navy);">{{ $row['teacher']->full_name }}</div>
                                <small style="color:var(--t3);">ID: {{ $row['teacher']->employee_id }}</small>
                            </td>
                            <td>{{ $row['teacher']->department?->name ?? 'Academics' }}</td>
                            <td>{{ $row['teacher']->designation?->name ?? 'Teacher' }}</td>
                            <td>
                                <strong style="font-size:15px; color:var(--navy);">{{ $pCount }}</strong>
                                <span style="font-size:11px; color:var(--t3);">/ week</span>
                            </td>
                            <td>
                                <span class="badge" style="background:{{ $row['color'] }}44; color:{{ $row['color'] }};">
                                    {{ $row['status'] }}
                                </span>
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <div style="flex:1; height:6px; background:var(--border); border-radius:3px; overflow:hidden;">
                                        <div style="width:{{ $pct }}%; height:100%; background:{{ $row['color'] }}; border-radius:3px;"></div>
                                    </div>
                                    <span style="font-size:11.5px; font-weight:700; color:var(--t2);">{{ $pct }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:30px; color:var(--t3);">No active staff records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
