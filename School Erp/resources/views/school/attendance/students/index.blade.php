@extends('layouts.app')

@section('page-title', 'Student Attendance')

@section('content')

<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-calendar-check" style="color:var(--gold);margin-right:8px;"></i>Mark Student Attendance</h1>
        <p>Select class, section and date to mark daily attendance</p>
    </div>
    <div class="page-hdr-right">
        <a href="{{ route('school.attendance.students.daily') }}" class="btn btn-outline">
            <i class="fas fa-chart-pie"></i> Daily Summary
        </a>
        <a href="{{ route('school.attendance.students.report') }}" class="btn btn-outline">
            <i class="fas fa-calendar-alt"></i> Monthly Report
        </a>
        <a href="{{ route('school.attendance.students.stats') }}" class="btn btn-primary">
            <i class="fas fa-chart-line"></i> Stats
        </a>
    </div>
</div>

<!-- Filter Card -->
<div class="card">
    <div class="card-hdr">
        <h3><i class="fas fa-filter" style="color:var(--gold);margin-right:6px;"></i>Select Parameters</h3>
    </div>
    <div class="card-body">
        <form id="attendanceSelectForm">
            <div class="grid-4" style="align-items:end;">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Class</label>
                    <select id="class_id" class="form-control" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $cls)
                            <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Section</label>
                    <select id="section_id" class="form-control" required>
                        <option value="">Select Section</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Academic Session</label>
                    <select id="academic_session_id" class="form-control" required>
                        <option value="">Select Session</option>
                        @foreach($academicSessions as $ses)
                            <option value="{{ $ses->id }}" {{ $ses->is_current ? 'selected' : '' }}>{{ $ses->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Date</label>
                    <input type="date" id="attendance_date" class="form-control" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
                </div>
            </div>
            <div style="margin-top:16px;text-align:right;">
                <button type="submit" class="btn btn-gold">
                    <i class="fas fa-users"></i> Retrieve Students
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Marking Form (AJAX loaded) -->
<form action="{{ route('school.attendance.students.store') }}" method="POST" id="attendanceSaveForm" style="display:none;">
    @csrf
    <input type="hidden" name="section_id" id="form_section_id">
    <input type="hidden" name="date" id="form_date">
    <input type="hidden" name="academic_session_id" id="form_academic_session_id">

    <div class="card">
        <div class="card-hdr">
            <h3><i class="fas fa-clipboard-list" style="color:var(--gold);margin-right:6px;"></i>Marking Register</h3>
            <button type="button" class="btn btn-success" onclick="markAllPresent()" style="font-size:12px;padding:6px 14px;">
                <i class="fas fa-check-double"></i> Set All Present
            </button>
        </div>
        <div class="card-body" style="padding:0;">
            <div id="attendanceTableContainer" style="padding:20px;color:var(--t3);text-align:center;">
                <!-- AJAX table loaded here -->
            </div>
            <div style="padding:16px 20px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;">
                <button type="submit" class="btn btn-primary" style="padding:10px 32px;">
                    <i class="fas fa-save"></i> Save Attendance Register
                </button>
            </div>
        </div>
    </div>
</form>

@endsection

@section('scripts')
<script>
const allSections = @json($sections);

$('#class_id').on('change', function() {
    let classId = $(this).val();
    let sectionSelect = $('#section_id');
    sectionSelect.empty().append('<option value="">Select Section</option>');
    if (classId) {
        let filtered = allSections.filter(s => s.class_id == classId);
        filtered.forEach(function(sec) {
            sectionSelect.append('<option value="' + sec.id + '">' + sec.name + '</option>');
        });
    }
});

$('#attendanceSelectForm').on('submit', function(e) {
    e.preventDefault();
    let sectionId = $('#section_id').val();
    let date      = $('#attendance_date').val();
    let sessionId = $('#academic_session_id').val();
    if (!sectionId || !date || !sessionId) return;

    $('#form_section_id').val(sectionId);
    $('#form_date').val(date);
    $('#form_academic_session_id').val(sessionId);

    let container = $('#attendanceTableContainer');
    container.html('<div style="padding:24px;text-align:center;color:var(--t2);"><i class="fas fa-spinner fa-spin" style="font-size:20px;color:var(--gold);display:block;margin-bottom:8px;"></i>Loading students...</div>');
    $('#attendanceSaveForm').show();

    $.ajax({
        url: "{{ route('school.attendance.students.load') }}",
        type: "POST",
        data: { section_id: sectionId, date: date, academic_session_id: sessionId },
        success: function(response) {
            if (response.success) {
                container.html(response.html);
            } else {
                container.html('<div style="padding:20px;color:var(--red);"><i class="fas fa-exclamation-circle"></i> Failed to load students.</div>');
            }
        },
        error: function() {
            container.html('<div style="padding:20px;color:var(--red);"><i class="fas fa-exclamation-circle"></i> Network error. Please try again.</div>');
        }
    });
});

function markAllPresent() {
    $('.status-present').prop('checked', true);
}
</script>
@endsection
