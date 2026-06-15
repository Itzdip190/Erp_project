@extends('layouts.app')

@section('title', 'Daily MIS Report')
@section('page-title', 'Daily MIS Report')

@section('content')
<div class="card">
    <div class="card-hdr" style="display:flex; justify-content:space-between; align-items:center;">
        <h3>Daily Management Information System (MIS) Report</h3>
        <form method="GET" action="{{ route('school.dashboard.mis-report') }}" style="display:flex; gap:8px; align-items:center;">
            <input type="date" name="date" class="form-control" value="{{ $date->toDateString() }}" style="width:auto; padding:6px 12px;" onchange="this.form.submit()">
            <button type="submit" class="btn btn-primary" style="padding:6px 16px;">Go</button>
        </form>
    </div>
    <div class="card-body">
        <p style="font-size:13px; color:var(--t2); margin-bottom:24px;">
            Daily metrics report summary for <strong>{{ $date->format('l, F j, Y') }}</strong>.
        </p>

        <!-- Attendance Summaries -->
        <div class="grid-3" style="margin-bottom:30px;">
            <div style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); color:#fff; border-radius:12px; padding:20px; box-shadow:var(--shadow);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                    <span style="font-size:12px; font-weight:700; text-transform:uppercase; opacity:0.85;">Student Attendance</span>
                    <i class="fas fa-user-graduate" style="font-size:18px; opacity:0.85;"></i>
                </div>
                <h2 style="font-size:26px; font-weight:800; font-family:'Plus Jakarta Sans',sans-serif; margin-bottom:4px;">{{ $studentRate }}%</h2>
                <p style="font-size:11px; opacity:0.9;">
                    Present: <strong>{{ $studentPresent }}</strong> / Absent: <strong>{{ $studentAbsent }}</strong> / Late: <strong>{{ $studentLate }}</strong>
                </p>
            </div>

            <div style="background:linear-gradient(135deg, #064e3b, #10b981); color:#fff; border-radius:12px; padding:20px; box-shadow:var(--shadow);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                    <span style="font-size:12px; font-weight:700; text-transform:uppercase; opacity:0.85;">Staff Attendance</span>
                    <i class="fas fa-users" style="font-size:18px; opacity:0.85;"></i>
                </div>
                <h2 style="font-size:26px; font-weight:800; font-family:'Plus Jakarta Sans',sans-serif; margin-bottom:4px;">{{ $staffRate }}%</h2>
                <p style="font-size:11px; opacity:0.9;">
                    Present: <strong>{{ $staffPresent }}</strong> / Absent: <strong>{{ $staffAbsent }}</strong> / Late: <strong>{{ $staffLate }}</strong>
                </p>
            </div>

            <div style="background:linear-gradient(135deg, #78350f, #d97706); color:#fff; border-radius:12px; padding:20px; box-shadow:var(--shadow);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                    <span style="font-size:12px; font-weight:700; text-transform:uppercase; opacity:0.85;">Today's Fee Collection</span>
                    <i class="fas fa-indian-rupee-sign" style="font-size:18px; opacity:0.85;"></i>
                </div>
                <h2 style="font-size:26px; font-weight:800; font-family:'Plus Jakarta Sans',sans-serif; margin-bottom:4px;">₹{{ number_format($totalCollection, 2) }}</h2>
                <p style="font-size:11px; opacity:0.9;">
                    Cash: <strong>₹{{ number_format($cashCollection, 2) }}</strong> / Online: <strong>₹{{ number_format($onlineCollection, 2) }}</strong>
                </p>
            </div>
        </div>

        <div class="grid-2">
            <!-- Class breakdown -->
            <div class="card" style="margin-bottom:0;">
                <div class="card-hdr">
                    <h3>Class-wise Student Attendance</h3>
                </div>
                <div class="card-body" style="padding:0;">
                    <div class="table-wrap">
                        <table class="tbl">
                            <thead>
                                <tr>
                                    <th>Class</th>
                                    <th>Section</th>
                                    <th>Total Students</th>
                                    <th>Present Today</th>
                                    <th>Attendance Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($classBreakdown as $row)
                                <tr>
                                    <td><strong>{{ $row['class_name'] }}</strong></td>
                                    <td>{{ $row['section_name'] }}</td>
                                    <td>{{ $row['total'] }}</td>
                                    <td>{{ $row['present'] }}</td>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:8px;">
                                            <div style="flex:1; background:#e5e7eb; height:6px; border-radius:3px; overflow:hidden;">
                                                <div style="background:var(--navy); width:{{ $row['rate'] }}%; height:100%;"></div>
                                            </div>
                                            <span style="font-size:11px; font-weight:700;">{{ $row['rate'] }}%</span>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" style="text-align:center; padding:18px; color:var(--t3);">No attendance logs found for this date.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- New Admissions Today -->
            <div class="card" style="margin-bottom:0;">
                <div class="card-hdr">
                    <h3>New Admissions Today</h3>
                </div>
                <div class="card-body" style="padding:0;">
                    <div class="table-wrap">
                        <table class="tbl">
                            <thead>
                                <tr>
                                    <th>Admission #</th>
                                    <th>Student Name</th>
                                    <th>Class & Section</th>
                                    <th>Gender</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($admissionsToday as $student)
                                <tr>
                                    <td><span style="font-weight:700; color:var(--gold);">{{ $student->admission_number }}</span></td>
                                    <td><strong>{{ $student->full_name }}</strong></td>
                                    <td>{{ optional($student->class)->name ?? 'N/A' }} - {{ optional($student->section)->name ?? 'N/A' }}</td>
                                    <td>{{ ucfirst($student->gender) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" style="text-align:center; padding:24px; color:var(--t2);">No new admissions registered today.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
