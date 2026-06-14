@extends('layouts.app')

@section('title', 'Mark Student Attendance')

@section('content')
@if(session('success'))
    <div class="glass-card" style="background-color: rgba(16, 185, 129, 0.15); border-color: var(--success); color: var(--success); padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
        <i class="fa fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

<!-- Quick links bar -->
<div style="display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
    <a href="{{ route('school.attendance.students.daily') }}" class="btn-accent" style="background-color: #8B5CF6;">
        <i class="fa fa-chart-pie"></i> Daily Summary
    </a>
    <a href="{{ route('school.attendance.students.report') }}" class="btn-accent" style="background-color: #4B5563;">
        <i class="fa fa-calendar-alt"></i> Monthly Register
    </a>
    <a href="{{ route('school.attendance.students.stats') }}" class="btn-accent" style="background-color: #F59E0B;">
        <i class="fa fa-chart-line"></i> Attendance Stats
    </a>
</div>

<!-- Attendance Parameters Selection -->
<div class="glass-card">
    <h3 style="font-family: 'Syne', sans-serif; margin-bottom: 1.5rem;">Select Parameters</h3>
    <form id="attendanceSelectForm" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; align-items: end;">
        <div>
            <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Class</label>
            <select id="class_id" class="form-input" required>
                <option value="">Select Class</option>
                @foreach($classes as $cls)
                    <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Section</label>
            <select id="section_id" class="form-input" required>
                <option value="">Select Section</option>
            </select>
        </div>

        <div>
            <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Academic Session</label>
            <select id="academic_session_id" class="form-input" required>
                <option value="">Select Session</option>
                @foreach($academicSessions as $ses)
                    <option value="{{ $ses->id }}" {{ $ses->is_current ? 'selected' : '' }}>{{ $ses->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Date</label>
            <input type="date" id="attendance_date" class="form-input" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
        </div>

        <div>
            <button type="submit" class="btn-accent" style="width: 100%; justify-content: center;">
                <i class="fa fa-users"></i> Retrieve Students
            </button>
        </div>
    </form>
</div>

<!-- Marking Form Container (AJAX loaded) -->
<form action="{{ route('school.attendance.students.store') }}" method="POST" id="attendanceSaveForm" style="display: none; margin-top: 2rem;">
    @csrf
    <input type="hidden" name="section_id" id="form_section_id">
    <input type="hidden" name="date" id="form_date">
    <input type="hidden" name="academic_session_id" id="form_academic_session_id">

    <div class="glass-card" style="padding: 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; border-bottom: 1px solid var(--border); margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <h3 style="font-family: 'Syne', sans-serif;">Marking Register</h3>
            <button type="button" class="btn-accent" style="background-color: var(--success); padding: 0.5rem 1rem; font-size: 0.85rem;" onclick="markAllPresent()">
                <i class="fa fa-check-double"></i> Set All Present
            </button>
        </div>

        <div id="attendanceTableContainer">
            <!-- AJAX table loaded here -->
        </div>

        <div style="margin-top: 2rem; padding: 1rem; border-top: 1px solid var(--border); display: flex; justify-content: flex-end;">
            <button type="submit" class="btn-accent" style="padding: 1rem 3rem; font-size: 1.1rem;">
                <i class="fa fa-save"></i> Save Attendance Register
            </button>
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

    // Handle retrieving student list via AJAX
    $('#attendanceSelectForm').on('submit', function(e) {
        e.preventDefault();
        
        let sectionId = $('#section_id').val();
        let date = $('#attendance_date').val();
        let sessionId = $('#academic_session_id').val();

        if (!sectionId || !date || !sessionId) return;

        // Copy parameters to main submission form
        $('#form_section_id').val(sectionId);
        $('#form_date').val(date);
        $('#form_academic_session_id').val(sessionId);

        let tableContainer = $('#attendanceTableContainer');
        tableContainer.html('<span style="color: var(--warning);"><i class="fa fa-spinner fa-spin"></i> Retrieving registers...</span>');
        $('#attendanceSaveForm').show();

        $.ajax({
            url: "{{ route('school.attendance.students.load') }}",
            type: "POST",
            data: {
                section_id: sectionId,
                date: date,
                academic_session_id: sessionId
            },
            success: function(response) {
                if (response.success) {
                    tableContainer.html(response.html);
                } else {
                    tableContainer.html('<span style="color: var(--danger);"><i class="fa fa-exclamation-circle"></i> Failed to retrieve student details.</span>');
                }
            },
            error: function() {
                tableContainer.html('<span style="color: var(--danger);"><i class="fa fa-exclamation-circle"></i> Network error loading registers.</span>');
            }
        });
    });

    // Set all present helper
    function markAllPresent() {
        $('.status-present').prop('checked', true);
    }
</script>
@endsection
