@extends('layouts.app')

@section('title', 'New Admission Report')

@section('content')
<div class="page-hdr" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <div class="page-hdr-left" style="display:flex; align-items:center; gap:10px;">
        <h1 style="font-size:24px; font-weight:800; color:var(--navy); margin:0;">New Admission Report</h1>
        <div style="width:24px; height:24px; border-radius:50%; background:#f59e0b; display:flex; align-items:center; justify-content:center; color:#fff;">
            <i class="fas fa-check" style="font-size:12px;"></i>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="card" style="background:#f1f5f9; border-radius:12px; border:1px solid #cbd5e1; box-shadow:var(--shadow); margin-bottom:20px; padding:16px;">
    <form method="GET" action="{{ route('school.admissions.new-admission-report') }}" id="filter-form">
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:12px; align-items:end;">
            
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

            <!-- Select From Date -->
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="font-size:11px; font-weight:700; color:var(--t2); margin-bottom:4px; display:block; text-transform:capitalize;">Select From Date</label>
                <div style="position:relative;">
                    <input type="date" name="from_date" value="{{ $fromDate }}" class="form-control" style="width:100%; height:38px; border-radius:6px; border:1px solid #cbd5e1; padding:0 12px; background:#fff; font-size:13px; color:var(--t1);">
                </div>
            </div>

            <!-- Select To Date -->
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="font-size:11px; font-weight:700; color:var(--t2); margin-bottom:4px; display:block; text-transform:capitalize;">Select To Date</label>
                <div style="position:relative;">
                    <input type="date" name="to_date" value="{{ $toDate }}" class="form-control" style="width:100%; height:38px; border-radius:6px; border:1px solid #cbd5e1; padding:0 12px; background:#fff; font-size:13px; color:var(--t1);">
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

            <!-- Action Buttons -->
            <div style="display:flex; gap:8px;">
                <button type="submit" class="btn" style="background:#d97706; color:#fff; border:1px solid #d97706; border-radius:6px; padding:0 20px; height:38px; font-weight:700; font-size:12px; cursor:pointer; display:flex; align-items:center; justify-content:center;">APPLY</button>
                <a href="{{ route('school.admissions.new-admission-report') }}" class="btn" style="background:#fff; color:#d97706; border:1px solid #d97706; border-radius:6px; padding:0 20px; height:38px; font-weight:700; font-size:12px; text-decoration:none; display:inline-flex; align-items:center; justify-content:center;">CLEAR</a>
            </div>

        </div>
    </form>
</div>

<!-- Table Card -->
<div class="card" style="background:#fff; border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow); overflow:hidden;">
    <div class="card-hdr" style="padding:16px 20px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border);">
        <h3 style="font-size:15px; font-weight:800; color:#0f172a; margin:0;">Total Students ({{ $students->total() }})</h3>
        
        @if($students->total() > 0)
            <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn" style="background:#e2e8f0; color:#334155; border:1px solid #cbd5e1; border-radius:6px; padding:8px 14px; font-size:12px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                <i class="fas fa-download"></i> GENERATE REPORT
            </a>
        @else
            <button class="btn" disabled style="background:#f1f5f9; color:#94a3b8; border:1px solid #e2e8f0; border-radius:6px; padding:8px 14px; font-size:12px; font-weight:700; cursor:not-allowed; display:inline-flex; align-items:center; gap:6px;">
                <i class="fas fa-download"></i> GENERATE REPORT
            </button>
        @endif
    </div>

    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; text-align:left;">
            <thead>
                <tr style="background:#023e4f; color:#fff; border-bottom:2px solid var(--border);">
                    <th style="padding:14px 16px; font-size:12px; font-weight:700; text-transform:capitalize;">Student Name</th>
                    <th style="padding:14px 16px; font-size:12px; font-weight:700; text-transform:capitalize;">Admission ID</th>
                    <th style="padding:14px 16px; font-size:12px; font-weight:700; text-transform:capitalize;">Academic Year</th>
                    <th style="padding:14px 16px; font-size:12px; font-weight:700; text-transform:capitalize;">Admission Date</th>
                    <th style="padding:14px 16px; font-size:12px; font-weight:700; text-transform:capitalize;">Class</th>
                    <th style="padding:14px 16px; font-size:12px; font-weight:700; text-transform:capitalize;">Section</th>
                    <th style="padding:14px 16px; font-size:12px; font-weight:700; text-transform:capitalize;">Father Name</th>
                    <th style="padding:14px 16px; font-size:12px; font-weight:700; text-transform:capitalize;">Father Mobile Number</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                    <tr style="border-bottom:1px solid #f1f5f9; hover:background:#f8fafc;">
                        <td style="padding:14px 16px; font-size:13px; font-weight:600; color:#1e293b;">{{ $student->full_name }}</td>
                        <td style="padding:14px 16px; font-size:13px; color:#475569;">{{ $student->admission_number }}</td>
                        <td style="padding:14px 16px; font-size:13px; color:#475569;">{{ $student->academicSession?->name }}</td>
                        <td style="padding:14px 16px; font-size:13px; color:#475569;">{{ $student->admission_date ? $student->admission_date->format('d/m/Y') : 'N/A' }}</td>
                        <td style="padding:14px 16px; font-size:13px; color:#475569;">{{ $student->class?->name ?? 'N/A' }}</td>
                        <td style="padding:14px 16px; font-size:13px; color:#475569;">{{ $student->section?->name ?? 'N/A' }}</td>
                        <td style="padding:14px 16px; font-size:13px; color:#475569;">{{ $student->father_name }}</td>
                        <td style="padding:14px 16px; font-size:13px; color:#475569;">{{ $student->father_phone ?? $student->guardian_phone ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="padding:80px 20px; text-align:center;">
                            <!-- Box SVG -->
                            <svg width="120" height="120" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin: 0 auto 16px; display:block;">
                                <path d="M25 45L60 25L95 45V85L60 105L25 85V45Z" fill="#ffb088" />
                                <path d="M60 25L95 45L60 65L25 45L60 25Z" fill="#ffd0b3" />
                                <path d="M25 45L60 65V105L25 85V45Z" fill="#e0956c" />
                                <path d="M95 45V85L60 105V65L95 45Z" fill="#c77b53" />
                                <path d="M25 45L10 32L48 20L60 25L25 45Z" fill="#e0956c" opacity="0.8" />
                                <path d="M95 45L110 32L72 20L60 25L95 45Z" fill="#c77b53" opacity="0.8" />
                                <circle cx="48" cy="80" r="2.5" fill="#58311b" />
                                <circle cx="68" cy="80" r="2.5" fill="#58311b" />
                                <path d="M54 88C54 86 62 86 62 88" stroke="#58311b" stroke-width="1.5" stroke-linecap="round" fill="none" />
                            </svg>
                            <h4 style="font-size:16px; font-weight:700; color:#334155; margin:0 0 4px 0;">No Data Found</h4>
                            <p style="font-size:12px; color:#64748b; margin:0;">No Data Found</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Footer -->
    <div style="display:flex; justify-content:flex-end; align-items:center; padding:16px 20px; border-top:1px solid var(--border); font-size:13px; color:var(--t2); background:#fff;">
        <span style="margin-right:20px; font-size:12px;">Total Rows: <strong>{{ $students->total() }}</strong></span>
        <div style="display:flex; gap:6px;">
            @if($students->onFirstPage())
                <span style="padding:6px 10px; border:1px solid #e2e8f0; border-radius:4px; color:#cbd5e1; background:#f8fafc; cursor:not-allowed;"><i class="fas fa-chevron-left" style="font-size:10px;"></i></span>
            @else
                <a href="{{ $students->previousPageUrl() }}" style="padding:6px 10px; border:1px solid #cbd5e1; border-radius:4px; color:#d97706; background:#fff; text-decoration:none;"><i class="fas fa-chevron-left" style="font-size:10px;"></i></a>
            @endif

            @if($students->hasMorePages())
                <a href="{{ $students->nextPageUrl() }}" style="padding:6px 10px; border:1px solid #cbd5e1; border-radius:4px; color:#d97706; background:#fff; text-decoration:none;"><i class="fas fa-chevron-right" style="font-size:10px;"></i></a>
            @else
                <span style="padding:6px 10px; border:1px solid #e2e8f0; border-radius:4px; color:#cbd5e1; background:#f8fafc; cursor:not-allowed;"><i class="fas fa-chevron-right" style="font-size:10px;"></i></span>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Dynamic Class -> Section update via form submit
    $('#class-select').on('change', function() {
        $('#section-select').val('');
        $('#filter-form').submit();
    });
});
</script>
@endsection
