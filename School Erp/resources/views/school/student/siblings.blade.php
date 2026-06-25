@extends('layouts.app')

@section('title', 'Siblings List')

@section('content')
<div class="page-hdr" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <div class="page-hdr-left" style="display:flex; align-items:center; gap:10px;">
        <h1 style="font-size:24px; font-weight:800; color:var(--navy); margin:0;">Siblings List</h1>
        <div style="width:24px; height:24px; border-radius:50%; background:#f59e0b; display:flex; align-items:center; justify-content:center; color:#fff;">
            <i class="fas fa-check" style="font-size:12px;"></i>
        </div>
    </div>
</div>

<form method="GET" action="{{ route('school.student-mgmt.siblings') }}" id="siblingsForm">
    <!-- Filters Card -->
    <div class="card" style="background:#f1f5f9; border-radius:12px; border:1px solid #cbd5e1; box-shadow:var(--shadow); margin-bottom:12px; padding:16px;">
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:12px; align-items:end;">
            
            <!-- Academic Year Dropdown -->
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="font-size:11px; font-weight:700; color:var(--t2); margin-bottom:4px; display:block; text-transform:capitalize;">Academic Year *</label>
                <div style="position:relative;">
                    <i class="far fa-calendar-alt" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#64748b; z-index:10;"></i>
                    <select name="academic_session_id" class="form-control" required style="width:100%; height:38px; border-radius:6px; border:1px solid #cbd5e1; padding:0 12px 0 34px; background:#fff; font-size:13px; color:var(--t1);" onchange="this.form.submit()">
                        @foreach($academicSessions as $session)
                            <option value="{{ $session->id }}" {{ $sessionId == $session->id ? 'selected' : '' }}>
                                {{ $session->start_date ? $session->start_date->format('M Y') : $session->name }} - {{ $session->end_date ? $session->end_date->format('M Y') : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Search input -->
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="font-size:11px; font-weight:700; color:var(--t2); margin-bottom:4px; display:block; text-transform:capitalize;">Search</label>
                <div style="position:relative;">
                    <i class="fas fa-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#64748b; z-index:10;"></i>
                    <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search by Student name/ father name/ admiss" style="width:100%; height:38px; border-radius:6px; border:1px solid #cbd5e1; padding:0 12px 0 34px; background:#fff; font-size:13px; color:var(--t1);">
                </div>
            </div>

            <!-- Select Class -->
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="font-size:11px; font-weight:700; color:var(--t2); margin-bottom:4px; display:block; text-transform:capitalize;">Select Class</label>
                <div style="position:relative;">
                    <i class="fas fa-book" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#64748b; z-index:10;"></i>
                    <select name="class_id" id="class-select" class="form-control" style="width:100%; height:38px; border-radius:6px; border:1px solid #cbd5e1; padding:0 12px 0 34px; background:#fff; font-size:13px; color:var(--t1);">
                        <option value="">Select Class</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Select Section -->
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="font-size:11px; font-weight:700; color:var(--t2); margin-bottom:4px; display:block; text-transform:capitalize;">Select Section</label>
                <div style="position:relative;">
                    <i class="fas fa-border-all" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#64748b; z-index:10;"></i>
                    <select name="section_id" id="section-select" class="form-control" style="width:100%; height:38px; border-radius:6px; border:1px solid #cbd5e1; padding:0 12px 0 34px; background:#fff; font-size:13px; color:var(--t1);">
                        <option value="">Select Section</option>
                        @foreach($sections as $s)
                            <option value="{{ $s->id }}" {{ $sectionId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

        </div>
    </div>

    <!-- Note Section -->
    <div style="margin: 10px 0 16px 0; font-size: 13px; line-height: 1.5; color: #dc2626;">
        <strong>*Note:</strong> Siblings are detected basis father phone number (in case student login is with father phone number) or father email ID (in case student login is with email). If any of them are same in student details, then they will be considered as siblings
    </div>

    <!-- Action bar -->
    @php
        $totalStudentsCount = collect($groups)->sum(fn($g) => count($g['students']));
    @endphp
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:16px;">
        
        <!-- Left: Stats Card -->
        <div style="display:flex; align-items:center; background:#0ea5e9; color:#fff; border-radius:8px; padding:10px 18px; gap:12px; box-shadow:var(--shadow); min-width:160px;">
            <i class="fas fa-users-rectangle" style="font-size:24px;"></i>
            <div>
                <div style="font-size:20px; font-weight:800; line-height:1.1;">{{ $totalStudentsCount }}</div>
                <div style="font-size:11px; font-weight:600; opacity:0.95;">Student Count</div>
            </div>
        </div>

        <!-- Right: Actions -->
        <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
            
            <!-- Toggle Switch -->
            <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer; user-select:none; font-size:13px; font-weight:600; color:var(--t2);">
                <span>Include deactivated students</span>
                <div style="position:relative; width:36px; height:20px;">
                    <input type="checkbox" name="include_deactivated" value="1" {{ $includeDeactivated ? 'checked' : '' }} style="opacity:0; width:0; height:0;" id="deactivated-toggle">
                    <span style="position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background-color:{{ $includeDeactivated ? '#d97706' : '#cbd5e1' }}; transition:.3s; border-radius:20px;">
                        <span style="position:absolute; content:''; height:14px; width:14px; left:3px; bottom:3px; background-color:white; transition:.3s; border-radius:50%; transform: {{ $includeDeactivated ? 'translateX(16px)' : 'translateX(0)' }}"></span>
                    </span>
                </div>
            </label>

            <!-- Buttons -->
            <div style="display:flex; gap:8px;">
                <button type="submit" class="btn" style="background:#fff; color:#d97706; border:1px solid #d97706; border-radius:6px; padding:0 20px; height:38px; font-weight:700; font-size:12px; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
                    <i class="far fa-eye"></i> VIEW
                </button>
                
                @if($totalStudentsCount > 0)
                    <button type="button" onclick="downloadReport()" class="btn" style="background:#d97706; color:#fff; border:1px solid #d97706; border-radius:6px; padding:0 20px; height:38px; font-weight:700; font-size:12px; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
                        <i class="fas fa-download"></i> DOWNLOAD
                    </button>
                @else
                    <button type="button" disabled class="btn" style="background:#f1f5f9; color:#94a3b8; border:1px solid #e2e8f0; border-radius:6px; padding:0 20px; height:38px; font-weight:700; font-size:12px; cursor:not-allowed; display:inline-flex; align-items:center; gap:6px;">
                        <i class="fas fa-download"></i> DOWNLOAD
                    </button>
                @endif
            </div>

        </div>
    </div>
</form>

<!-- Table Card -->
<div class="card" style="background:#fff; border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow); overflow:hidden;">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; text-align:left;">
            <thead>
                <tr style="background:#023e4f; color:#fff; border-bottom:2px solid var(--border);">
                    <th style="padding:14px 16px; font-size:12px; font-weight:700; text-transform:capitalize;">Father Detail</th>
                    <th style="padding:14px 16px; font-size:12px; font-weight:700; text-transform:capitalize;">Student Name</th>
                    <th style="padding:14px 16px; font-size:12px; font-weight:700; text-transform:capitalize;">Admission ID</th>
                    <th style="padding:14px 16px; font-size:12px; font-weight:700; text-transform:capitalize;">Class & Section</th>
                    <th style="padding:14px 16px; font-size:12px; font-weight:700; text-transform:capitalize;">Gender</th>
                    <th style="padding:14px 16px; font-size:12px; font-weight:700; text-transform:capitalize;">Status</th>
                    <th style="padding:14px 16px; font-size:12px; font-weight:700; text-transform:capitalize;">Date of Admission</th>
                </tr>
            </thead>
            <tbody>
                @php $groupIndex = 1; @endphp
                @forelse($groups as $group)
                    @foreach($group['students'] as $idx => $student)
                        <tr style="border-bottom:1px solid #f1f5f9; hover:background:#f8fafc;">
                            @if($idx === 0)
                                <td rowspan="{{ count($group['students']) }}" style="padding:14px 16px; font-size:13px; font-weight:600; color:#1e293b; background:#f8fafc; border-right:1px solid #e2e8f0; vertical-align:top;">
                                    <div style="display:flex; flex-direction:column; gap:6px;">
                                        <div style="display:flex; align-items:center; gap:6px;">
                                            <span style="font-weight:800; color:#334155;">{{ $groupIndex }}.</span>
                                            <i class="fas fa-phone" style="color:#d97706; font-size:12px;"></i>
                                            <span style="color:#0f172a;">{{ $group['phone'] }}</span>
                                        </div>
                                        <div style="font-size:11px; color:#64748b; font-weight:500;">
                                            <div>{{ $group['guardian_name'] }}</div>
                                            <div style="font-style:italic;">{{ $group['email'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                @php $groupIndex++; @endphp
                            @endif
                            <td style="padding:14px 16px; font-size:13px; color:#1e293b;">
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <div style="width:28px; height:28px; border-radius:50%; background:#e2e8f0; display:flex; align-items:center; justify-content:center; color:#94a3b8; flex-shrink:0;">
                                        <i class="fas fa-user" style="font-size:12px;"></i>
                                    </div>
                                    <span style="font-weight:600;">{{ $student->full_name }}</span>
                                </div>
                            </td>
                            <td style="padding:14px 16px; font-size:13px; color:#475569;">{{ $student->admission_number }}</td>
                            <td style="padding:14px 16px; font-size:13px; color:#475569;">{{ $student->class?->name ?? 'N/A' }} - {{ $student->section?->name ?? 'N/A' }}</td>
                            <td style="padding:14px 16px; font-size:13px; color:#475569;">{{ $student->gender ? ucfirst($student->gender) : 'N/A' }}</td>
                            <td style="padding:14px 16px; font-size:13px;">
                                @if($student->is_active)
                                    <span style="color:#10b981; font-weight:700;">Active</span>
                                @else
                                    <span style="color:#ef4444; font-weight:700;">Inactive</span>
                                @endif
                            </td>
                            <td style="padding:14px 16px; font-size:13px; color:#475569;">{{ $student->admission_date ? $student->admission_date->format('d/m/Y') : 'N/A' }}</td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="7" style="padding:60px 20px; text-align:center; color:var(--t3);">
                            <i class="fas fa-folder-open" style="font-size:32px; display:block; margin-bottom:10px; color:var(--border);"></i>
                            No sibling groups identified in the database logs.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Dynamic Class -> Section update via form submit
    $('#class-select').on('change', function() {
        $('#section-select').val('');
        $('#siblingsForm').submit();
    });

    // Auto submit form when include deactivated toggle changes
    $('#deactivated-toggle').on('change', function() {
        $('#siblingsForm').submit();
    });
});

// Download Report as Excel
function downloadReport() {
    const form = document.getElementById('siblingsForm');
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'export';
    input.value = 'excel';
    form.appendChild(input);
    form.submit();
    input.remove(); // remove it so subsequent submits don't export
}
</script>
@endsection
