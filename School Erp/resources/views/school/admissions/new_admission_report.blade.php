@extends('layouts.app')

@section('page-title', 'New Admission Report')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-file-invoice" style="color:var(--gold);margin-right:8px;"></i>New Admissions Report</h1>
        <p>Comprehensive report of student intake and enrollment statistics for the current academic session</p>
    </div>
    <div class="page-hdr-right">
        <button onclick="window.print()" class="btn btn-gold"><i class="fas fa-print"></i> Print Report</button>
    </div>
</div>

<div class="card">
    <div class="card-hdr" style="display:flex; justify-content:space-between; align-items:center;">
        <h3>Recent Enrollments</h3>
        <span style="font-size:12px; color:var(--t2);">Academic Session: 2026-2027</span>
    </div>
    <div class="card-body">
        @php
            $schoolId = auth()->user()->school_id;
            $recentStudents = \App\Models\Student::where('school_id', $schoolId)->with('class')->orderBy('created_at', 'desc')->get();
        @endphp

        <div class="table-responsive">
            <table class="table" style="width:100%; border-collapse: collapse; text-align:left;">
                <thead>
                    <tr style="border-bottom:2px solid var(--border); color:var(--navy); font-weight:600;">
                        <th style="padding:12px 10px;">Adm No.</th>
                        <th style="padding:12px 10px;">Student Name</th>
                        <th style="padding:12px 10px;">Class</th>
                        <th style="padding:12px 10px;">Guardian Details</th>
                        <th style="padding:12px 10px;">Admission Date</th>
                        <th style="padding:12px 10px; text-align:right;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentStudents as $student)
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:12px 10px; font-weight:600; color:var(--gold);">{{ $student->admission_number }}</td>
                        <td style="padding:12px 10px;">
                            <div style="font-weight:600; color:var(--navy);">{{ $student->first_name }} {{ $student->last_name }}</div>
                            <span style="font-size:11px; color:var(--t3);">DOB: {{ $student->date_of_birth }}</span>
                        </td>
                        <td style="padding:12px 10px;">{{ $student->class->name ?? 'N/A' }}</td>
                        <td style="padding:12px 10px;">
                            <div style="font-size:13px; color:var(--t2);">{{ $student->guardian_name }}</div>
                            <span style="font-size:11px; color:var(--t3);">{{ $student->guardian_phone }}</span>
                        </td>
                        <td style="padding:12px 10px;">{{ \Carbon\Carbon::parse($student->admission_date)->format('d M, Y') }}</td>
                        <td style="padding:12px 10px; text-align:right;">
                            <span class="badge badge-success">Enrolled</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center; color:var(--t3); padding:40px;">No recently admitted students found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
