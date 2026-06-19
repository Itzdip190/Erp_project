@extends('layouts.app')

@section('page-title', 'Student Activity Logs')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-history" style="color:var(--gold);margin-right:8px;"></i>Student Activity Logs</h1>
        <p>View student login session timestamps and application versions used</p>
    </div>
</div>

<!-- Filter Bar -->
<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="padding:15px 20px;">
        <form method="GET" action="{{ route('school.downloads.student-activity') }}" style="display:flex; justify-content:space-between; align-items:flex-end; gap:15px; flex-wrap:wrap;">
            <div style="display:flex; gap:15px; flex-grow:1; flex-wrap:wrap;">
                <div class="form-group" style="margin-bottom:0; flex:1; min-width:130px;">
                    <label class="form-label" style="font-size:11px; text-transform:uppercase; color:var(--t2);">Academic Year</label>
                    <select name="academic_year" class="form-control" style="padding:6px 12px; font-size:13px;">
                        <option value="2025-2026" selected>Apr 2025 - Mar 2026</option>
                        <option value="2024-2025">Apr 2024 - Mar 2025</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0; flex:1; min-width:130px;">
                    <label class="form-label" style="font-size:11px; text-transform:uppercase; color:var(--t2);">Select Class</label>
                    <select name="class_id" class="form-control" style="padding:6px 12px; font-size:13px;" onchange="this.form.submit()">
                        <option value="">All Classes</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0; flex:1; min-width:130px;">
                    <label class="form-label" style="font-size:11px; text-transform:uppercase; color:var(--t2);">Select Section</label>
                    <select name="section_id" class="form-control" style="padding:6px 12px; font-size:13px;" onchange="this.form.submit()">
                        <option value="">All Sections</option>
                        @foreach($sections as $s)
                            <option value="{{ $s->id }}" {{ $sectionId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0; flex:2; min-width:180px;">
                    <label class="form-label" style="font-size:11px; text-transform:uppercase; color:var(--t2);">Search Student</label>
                    <input type="text" name="search" class="form-control" style="padding:6px 12px; font-size:13px;" placeholder="Student Name/Student ID..." value="{{ $search }}">
                </div>
            </div>
            
            <div style="display:flex; gap:8px;">
                <button type="submit" class="btn btn-gold" style="padding:8px 16px; font-size:13px;">
                    <i class="fas fa-eye"></i> VIEW
                </button>
                <button type="button" class="btn btn-outline" style="border-color:#b45309; color:#b45309; padding:8px 16px; font-size:13px;" onclick="showToast('Exporting student activity logs...')">
                    <i class="fas fa-download"></i> DOWNLOAD
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Table Listing -->
<div class="card">
    <div class="card-hdr" style="display:flex; justify-content:space-between; align-items:center;">
        <h3>Activity Log Grid</h3>
        <span class="badge badge-gold">{{ count($activities) }} Active Logs</span>
    </div>
    <div class="card-body" style="padding:0;">
        <table class="tbl">
            <thead>
                <tr>
                    <th style="width:70px; text-align:center;">#</th>
                    <th style="width:130px;">Admission ID</th>
                    <th>Name</th>
                    <th style="width:100px;">Roll No.</th>
                    <th>Mobile</th>
                    <th>Last Seen</th>
                    <th>App Version</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activities as $index => $act)
                    <tr>
                        <td style="text-align:center; color:var(--t3); font-weight:500;">
                            {{ sprintf('%02d.', $index + 1) }}
                        </td>
                        <td>
                            <span class="badge badge-blue">{{ $act['admission_id'] }}</span>
                        </td>
                        <td>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="width:32px; height:32px; border-radius:50%; background:#e2e8f0; display:flex; align-items:center; justify-content:center; color:#64748b;">
                                    <i class="fas fa-user-graduate" style="font-size:14px;"></i>
                                </div>
                                <span style="font-weight:700; color:var(--navy);">{{ $act['name'] }}</span>
                            </div>
                        </td>
                        <td>
                            {{ $act['roll_no'] }}
                        </td>
                        <td style="font-weight:500; color:var(--t1);">
                            {{ $act['mobile'] }}
                        </td>
                        <td style="color:{{ $act['last_seen'] === 'Never Logged In' ? 'var(--red)' : 'var(--t1)' }}; font-weight:{{ $act['last_seen'] === 'Never Logged In' ? '500' : '700' }};">
                            {{ $act['last_seen'] }}
                        </td>
                        <td>
                            @if($act['app_version'])
                                <span class="badge badge-purple" style="font-size:11px;">{{ $act['app_version'] }}</span>
                            @else
                                <span style="color:var(--t3);">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center; padding:40px; color:var(--t3);">
                            <i class="fas fa-clock" style="font-size:32px; color:var(--t3); margin-bottom:12px; display:block;"></i>
                            No activity log entries found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
