@extends('superadmin.layouts.master')

@section('styles')
<style>
    .restore-page-wrap {
        max-width: 900px;
        margin: 0 auto;
        padding: 24px 16px;
    }

    .restore-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
        border-radius: 20px;
        padding: 28px 32px;
        color: #fff;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        box-shadow: 0 10px 25px -5px rgba(30, 27, 75, 0.3);
    }
    .restore-hero-left {
        display: flex;
        align-items: center;
        gap: 18px;
    }
    .restore-hero-icon {
        width: 60px; height: 60px;
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.25);
        border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 26px;
        flex-shrink: 0;
        color: #67e8f9;
    }
    .restore-hero-text h1 { font-size: 1.35rem; font-weight: 800; margin: 0 0 6px; color: #fff; }
    .restore-hero-text p  { font-size: 0.88rem; opacity: 0.85; margin: 0; color: #cbd5e1; }

    /* Diagnostic Stats Grid */
    .diag-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .diag-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 18px 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform .2s, box-shadow .2s;
    }
    .diag-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.06);
    }
    .diag-card-hdr {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }
    .diag-card-title {
        font-size: 0.82rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .diag-card-icon {
        width: 36px; height: 36px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px;
    }
    .diag-card-num {
        font-size: 1.65rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }
    .diag-card-sub {
        font-size: 0.78rem;
        color: #94a3b8;
        margin-top: 4px;
    }
    .diag-badge-warning {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fca5a5;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 11px;
    }
    .diag-badge-success {
        background: #f0fdf4;
        color: #16a34a;
        border: 1px solid #bbf7d0;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 11px;
    }

    /* Date Filter Card */
    .date-filter-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 18px 24px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
    }
    .date-filter-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .date-filter-label {
        font-weight: 800;
        font-size: 0.92rem;
        color: #1e1b4b;
    }

    /* Options Form Card */
    .restore-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 28px;
        margin-bottom: 24px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.04);
    }
    .restore-card-title {
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 8px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .restore-card-subtitle {
        font-size: 0.85rem;
        color: #64748b;
        margin: 0 0 20px;
    }

    /* Module Box */
    .module-item {
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px 20px;
        margin-bottom: 16px;
        background: #fafafa;
        transition: all .2s;
    }
    .module-item:hover {
        border-color: #6366f1;
        background: #fdfdfe;
    }
    .module-item.active {
        border-color: #4f46e5;
        background: #f5f3ff;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.08);
    }
    .module-hdr {
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
    }
    .module-hdr-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .module-checkbox {
        width: 20px;
        height: 20px;
        cursor: pointer;
        accent-color: #4f46e5;
    }
    .module-title {
        font-weight: 800;
        font-size: 0.95rem;
        color: #1e1b4b;
        margin: 0 0 3px;
    }
    .module-desc {
        font-size: 0.82rem;
        color: #64748b;
        margin: 0;
    }
    .module-settings {
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px dashed #cbd5e1;
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        align-items: center;
    }
    .setting-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .setting-label {
        font-size: 0.8rem;
        font-weight: 700;
        color: #475569;
    }
    .setting-select, .setting-input {
        padding: 6px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 0.82rem;
        background: #fff;
        color: #1e293b;
        outline: none;
    }
    .setting-select:focus, .setting-input:focus {
        border-color: #4f46e5;
    }

    /* Submit Section */
    .restore-submit-box {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: 2px dashed #cbd5e1;
        border-radius: 16px;
        padding: 24px;
        text-align: center;
        margin-top: 24px;
    }
    .btn-restore-exec {
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        color: #fff;
        font-size: 1rem;
        font-weight: 800;
        padding: 14px 36px;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 6px 20px rgba(79, 70, 229, 0.35);
        transition: all .2s;
    }
    .btn-restore-exec:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(79, 70, 229, 0.45);
        background: linear-gradient(135deg, #4338ca 0%, #312e81 100%);
        color: #fff;
    }
    .btn-restore-exec:active {
        transform: translateY(0);
    }

    /* Dark Mode */
    body.dark-mode .restore-hero { background: linear-gradient(135deg, #020617 0%, #0f172a 100%) !important; }
    body.dark-mode .diag-card { background: #111827 !important; border-color: #1e293b !important; }
    body.dark-mode .diag-card-num { color: #f1f5f9 !important; }
    body.dark-mode .date-filter-card { background: #111827 !important; border-color: #1e293b !important; }
    body.dark-mode .date-filter-label { color: #f1f5f9 !important; }
    body.dark-mode .restore-card { background: #111827 !important; border-color: #1e293b !important; }
    body.dark-mode .restore-card-title { color: #f1f5f9 !important; }
    body.dark-mode .module-item { background: #1e293b !important; border-color: #334155 !important; }
    body.dark-mode .module-item.active { background: #1e1b4b !important; border-color: #6366f1 !important; }
    body.dark-mode .module-title { color: #f1f5f9 !important; }
    body.dark-mode .setting-select, body.dark-mode .setting-input { background: #0f172a !important; color: #f1f5f9 !important; border-color: #334155 !important; }
    body.dark-mode .restore-submit-box { background: #0f172a !important; border-color: #1e293b !important; }
</style>
@endsection

@section('content')
<div class="restore-page-wrap">

    {{-- Breadcrumb & Navigation --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ route('superadmin.schools.edit', $school->id) }}" class="btn-sa-cancel" style="padding: 6px 14px; font-size: 12px; border-radius: 8px;">
            <i class="fas fa-arrow-left mr-1"></i> Back to School Profile
        </a>
        <span class="badge badge-secondary" style="font-size: 12px; padding: 6px 12px; border-radius: 8px;">
            School ID: #{{ $school->id }} &bull; Code: {{ $school->code }}
        </span>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px; font-weight: 600;">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px; font-weight: 600;">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Hero Section --}}
    <div class="restore-hero">
        <div class="restore-hero-left">
            <div class="restore-hero-icon">
                <i class="fas fa-magic"></i>
            </div>
            <div class="restore-hero-text">
                <h1>Data Recovery & Quick Restore Tool</h1>
                <p>Instantly regenerate missing attendance records, recover lost morning data, and heal database sync for <strong>{{ $school->name }}</strong>.</p>
            </div>
        </div>
    </div>

    {{-- Date Filter Bar --}}
    <div class="date-filter-card">
        <div class="date-filter-left">
            <i class="far fa-calendar-alt" style="font-size: 20px; color: #4f46e5;"></i>
            <div>
                <div class="date-filter-label">Target Recovery Date:</div>
                <div style="font-size: 12px; color: #64748b;">Records will be checked & restored for this specific calendar day.</div>
            </div>
        </div>
        <form method="GET" action="{{ route('superadmin.schools.restore-data', $school->id) }}" id="dateFilterForm" class="d-flex align-items-center gap-2">
            <input type="date" name="date" class="setting-input" style="font-size: 13px; font-weight: 700; padding: 8px 14px;" value="{{ $targetDate }}" onchange="document.getElementById('dateFilterForm').submit()">
            <button type="submit" class="btn btn-sm btn-outline-primary" style="border-radius: 8px; font-weight: 700; padding: 7px 12px;">
                <i class="fas fa-sync-alt"></i> Refresh Stats
            </button>
        </form>
    </div>

    {{-- Diagnostic Stats Grid --}}
    <div class="diag-grid">
        {{-- Student Attendance Card --}}
        <div class="diag-card">
            <div class="diag-card-hdr">
                <span class="diag-card-title">Student Attendance</span>
                <div class="diag-card-icon" style="background: #e0e7ff; color: #4f46e5;">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
            <div class="diag-card-num">
                {{ $studentsMarkedAttendance }} / {{ $totalActiveStudents }}
            </div>
            <div class="diag-card-sub">
                @if($missingStudentAttendance > 0)
                    <span class="diag-badge-warning"><i class="fas fa-exclamation-triangle mr-1"></i> {{ $missingStudentAttendance }} Missing Today</span>
                @else
                    <span class="diag-badge-success"><i class="fas fa-check mr-1"></i> 100% Complete</span>
                @endif
            </div>
        </div>

        {{-- Staff Attendance Card --}}
        <div class="diag-card">
            <div class="diag-card-hdr">
                <span class="diag-card-title">Staff Attendance</span>
                <div class="diag-card-icon" style="background: #fef3c7; color: #d97706;">
                    <i class="fas fa-user-tie"></i>
                </div>
            </div>
            <div class="diag-card-num">
                {{ $staffMarkedAttendance }} / {{ $totalActiveStaff }}
            </div>
            <div class="diag-card-sub">
                @if($missingStaffAttendance > 0)
                    <span class="diag-badge-warning"><i class="fas fa-exclamation-triangle mr-1"></i> {{ $missingStaffAttendance }} Missing Today</span>
                @else
                    <span class="diag-badge-success"><i class="fas fa-check mr-1"></i> 100% Complete</span>
                @endif
            </div>
        </div>

        {{-- Fee Schedules Card --}}
        <div class="diag-card">
            <div class="diag-card-hdr">
                <span class="diag-card-title">Fee Mappings</span>
                <div class="diag-card-icon" style="background: #dcfce7; color: #16a34a;">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
            </div>
            <div class="diag-card-num">
                {{ $totalActiveStudents - $studentsWithoutFeeSchedule }} / {{ $totalActiveStudents }}
            </div>
            <div class="diag-card-sub">
                @if($studentsWithoutFeeSchedule > 0)
                    <span class="diag-badge-warning"><i class="fas fa-info-circle mr-1"></i> {{ $studentsWithoutFeeSchedule }} Unmapped</span>
                @else
                    <span class="diag-badge-success"><i class="fas fa-check mr-1"></i> All Linked</span>
                @endif
            </div>
        </div>

        {{-- Academic Session Card --}}
        <div class="diag-card">
            <div class="diag-card-hdr">
                <span class="diag-card-title">Active Session</span>
                <div class="diag-card-icon" style="background: #f3e8ff; color: #9333ea;">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
            <div class="diag-card-num" style="font-size: 1.25rem;">
                {{ $currentSession ? $currentSession->name : 'None' }}
            </div>
            <div class="diag-card-sub">
                <span>{{ $totalActiveStudents }} Students Active</span>
            </div>
        </div>
    </div>

    {{-- Restore Options Form --}}
    <form action="{{ route('superadmin.schools.restore-data.execute', $school->id) }}" method="POST" id="restoreDataForm">
        @csrf
        <input type="hidden" name="date" value="{{ $targetDate }}">

        <div class="restore-card">
            <div class="restore-card-title">
                <i class="fas fa-tasks text-indigo" style="color: #4f46e5;"></i> Select Modules to Restore
            </div>
            <div class="restore-card-subtitle">
                Choose the specific items you want to recover. Safe mode is enabled by default to prevent duplicate records.
            </div>

            {{-- 1. Student Attendance --}}
            <div class="module-item active" id="item_student_attendance">
                <div class="module-hdr" onclick="toggleModule('student_attendance')">
                    <div class="module-hdr-left">
                        <input type="checkbox" name="restore_modules[]" value="student_attendance" id="chk_student_attendance" class="module-checkbox" checked onclick="event.stopPropagation()">
                        <div>
                            <div class="module-title">🎓 Student Attendance Recovery</div>
                            <div class="module-desc">Regenerate attendance records for all active students for {{ \Carbon\Carbon::parse($targetDate)->format('M d, Y') }}.</div>
                        </div>
                    </div>
                    <div>
                        <span class="badge {{ $missingStudentAttendance > 0 ? 'badge-danger' : 'badge-success' }}" style="font-size: 11px; padding: 4px 10px; border-radius: 12px;">
                            {{ $missingStudentAttendance }} Missing
                        </span>
                    </div>
                </div>
                <div class="module-settings">
                    <div class="setting-group">
                        <span class="setting-label">Default Status:</span>
                        <select name="student_attendance_status" class="setting-select">
                            <option value="present" selected>Present (Default)</option>
                            <option value="late">Late</option>
                            <option value="duty_leave">Duty Leave</option>
                            <option value="half_day">Half Day</option>
                        </select>
                    </div>
                    <div class="setting-group">
                        <span class="setting-label">Attendance Type:</span>
                        <select name="student_attendance_type" class="setting-select">
                            <option value="manual" selected>Manual</option>
                            <option value="biometric">Biometric</option>
                            <option value="qr">QR Code</option>
                        </select>
                    </div>
                    <div class="setting-group">
                        <span class="setting-label">Execution Mode:</span>
                        <select name="student_attendance_mode" class="setting-select">
                            <option value="missing_only" selected>Fill Missing Students Only (Recommended)</option>
                            <option value="overwrite_all">Overwrite Entire Day for All Active Students</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- 2. Staff Attendance --}}
            <div class="module-item active" id="item_staff_attendance">
                <div class="module-hdr" onclick="toggleModule('staff_attendance')">
                    <div class="module-hdr-left">
                        <input type="checkbox" name="restore_modules[]" value="staff_attendance" id="chk_staff_attendance" class="module-checkbox" checked onclick="event.stopPropagation()">
                        <div>
                            <div class="module-title">👔 Staff / Teacher Attendance Recovery</div>
                            <div class="module-desc">Regenerate attendance records for teachers and non-teaching staff for {{ \Carbon\Carbon::parse($targetDate)->format('M d, Y') }}.</div>
                        </div>
                    </div>
                    <div>
                        <span class="badge {{ $missingStaffAttendance > 0 ? 'badge-danger' : 'badge-success' }}" style="font-size: 11px; padding: 4px 10px; border-radius: 12px;">
                            {{ $missingStaffAttendance }} Missing
                        </span>
                    </div>
                </div>
                <div class="module-settings">
                    <div class="setting-group">
                        <span class="setting-label">Default Status:</span>
                        <select name="staff_attendance_status" class="setting-select">
                            <option value="present" selected>Present (Clock-in: 08:30 AM)</option>
                            <option value="late">Late</option>
                            <option value="half_day">Half Day</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- 3. Fee Schedules Healing --}}
            <div class="module-item" id="item_fee_schedules">
                <div class="module-hdr" onclick="toggleModule('fee_schedules')">
                    <div class="module-hdr-left">
                        <input type="checkbox" name="restore_modules[]" value="fee_schedules" id="chk_fee_schedules" class="module-checkbox" onclick="event.stopPropagation()">
                        <div>
                            <div class="module-title">💰 Heal Missing Fee Schedules & Mappings</div>
                            <div class="module-desc">Re-link students who lost their Fee Schedule mappings back to their active Class fee structures.</div>
                        </div>
                    </div>
                    <div>
                        <span class="badge badge-secondary" style="font-size: 11px; padding: 4px 10px; border-radius: 12px;">
                            {{ $studentsWithoutFeeSchedule }} Unmapped
                        </span>
                    </div>
                </div>
            </div>

            {{-- 4. Academic Session Sync --}}
            <div class="module-item" id="item_session_sync">
                <div class="module-hdr" onclick="toggleModule('session_sync')">
                    <div class="module-hdr-left">
                        <input type="checkbox" name="restore_modules[]" value="session_sync" id="chk_session_sync" class="module-checkbox" onclick="event.stopPropagation()">
                        <div>
                            <div class="module-title">🏫 Sync Students with Current Academic Session</div>
                            <div class="module-desc">Ensure all active students have a valid student session entry for current session: <strong>{{ $currentSession?->name }}</strong>.</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 5. Restore Deleted Students --}}
            @if($softDeletedTodayStudents > 0)
            <div class="module-item" id="item_restore_deleted_students">
                <div class="module-hdr" onclick="toggleModule('restore_deleted_students')">
                    <div class="module-hdr-left">
                        <input type="checkbox" name="restore_modules[]" value="restore_deleted_students" id="chk_restore_deleted_students" class="module-checkbox" onclick="event.stopPropagation()">
                        <div>
                            <div class="module-title">👥 Un-delete Students Soft-Deleted on {{ \Carbon\Carbon::parse($targetDate)->format('M d, Y') }}</div>
                            <div class="module-desc">Restore {{ $softDeletedTodayStudents }} students who were deleted on this date.</div>
                        </div>
                    </div>
                    <div>
                        <span class="badge badge-warning" style="font-size: 11px; padding: 4px 10px; border-radius: 12px;">
                            {{ $softDeletedTodayStudents }} Deleted
                        </span>
                    </div>
                </div>
            </div>
            @endif

            {{-- Submit Box --}}
            <div class="restore-submit-box">
                <div style="font-size: 13.5px; color: #475569; margin-bottom: 16px;">
                    <i class="fas fa-shield-alt text-success mr-1"></i> Transaction safe. All actions will be executed inside a single database transaction.
                </div>
                <button type="button" class="btn-restore-exec" onclick="confirmAndSubmit()">
                    <i class="fas fa-bolt"></i> Execute 1-Click Data Recovery
                </button>
            </div>

        </div>
    </form>

</div>

<script>
function toggleModule(id) {
    const chk = document.getElementById('chk_' + id);
    const box = document.getElementById('item_' + id);
    if (chk) {
        chk.checked = !chk.checked;
        if (chk.checked) {
            box.classList.add('active');
        } else {
            box.classList.remove('active');
        }
    }
}

// Update class on direct checkbox clicks
document.querySelectorAll('.module-checkbox').forEach(chk => {
    chk.addEventListener('change', function() {
        const box = this.closest('.module-item');
        if (this.checked) {
            box.classList.add('active');
        } else {
            box.classList.remove('active');
        }
    });
});

function confirmAndSubmit() {
    const checked = document.querySelectorAll('.module-checkbox:checked');
    if (checked.length === 0) {
        alert('Please select at least one module checkbox to restore.');
        return;
    }

    let msg = "Are you sure you want to execute Data Recovery for date: {{ $targetDate }}?\n\n";
    checked.forEach(c => {
        const title = c.closest('.module-item').querySelector('.module-title').innerText.trim();
        msg += "• " + title + "\n";
    });
    msg += "\nThis will restore and populate database records for {{ $school->name }}.";

    if (confirm(msg)) {
        document.getElementById('restoreDataForm').submit();
    }
}
</script>
@endsection
