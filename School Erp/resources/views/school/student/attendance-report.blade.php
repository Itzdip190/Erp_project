@extends('layouts.app')

@section('page-title', 'Attendance Report')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-calendar-alt" style="color:var(--gold);margin-right:8px;"></i>Attendance Analytics Report</h1>
        <p>Monitor student attendance logs, generate monthly records, and filter low attendance alerts</p>
    </div>
    <div class="page-hdr-right">
    </div>
</div>

<div class="card">
    <div class="card-hdr">
        <h3>Class Selector</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('school.student-mgmt.attendance-report') }}">
            <div class="grid-3" style="align-items:end;">
                <div class="form-group" style="margin-bottom:0; grid-column: span 2;">
                    <label class="form-label">Class</label>
                    <select name="class_id" class="form-control" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-gold" style="width:100%;"><i class="fas fa-search"></i> Generate Analytics</button>
            </div>
        </form>
    </div>
</div>

@if($classId)
<div class="card">
    <div class="card-hdr">
        <h3>Attendance Statistics Table</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Admission ID</th>
                    <th>Student Name</th>
                    <th>Total School Days</th>
                    <th>Days Attended</th>
                    <th>Attendance Rate</th>
                    <th>Alert Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportData as $row)
                    @php
                        $alert = $row['rate'] < 75;
                    @endphp
                    <tr>
                        <td><span class="badge badge-blue">{{ $row['student']->admission_number }}</span></td>
                        <td style="font-weight:700;">{{ $row['student']->full_name }}</td>
                        <td>{{ $row['total'] }}</td>
                        <td>{{ $row['present'] }}</td>
                        <td>
                            <strong style="color:{{ $alert ? 'var(--red)' : 'var(--green)' }}">{{ $row['rate'] }}%</strong>
                        </td>
                        <td>
                            @if($alert)
                                <span class="badge badge-danger"><i class="fas fa-triangle-exclamation"></i> Low Attendance (<75%)</span>
                            @else
                                <span class="badge badge-success"><i class="fas fa-circle-check"></i> Satisfactory</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding:30px; color:var(--t3);">No data found for this class selection.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
