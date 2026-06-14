@extends('layouts.app')

@section('title', 'Promote Students')

@section('content')
<div class="glass-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2 style="font-family: 'Syne', sans-serif;">Student Promotion Panel</h2>
        <a href="{{ route('school.students.index') }}" class="btn-accent" style="background-color: #4B5563;">
            <i class="fa fa-arrow-left"></i> Back to List
        </a>
    </div>

    <!-- Step 1: Select source class -->
    <div class="glass-card" style="background-color: rgba(15, 23, 42, 0.4);">
        <h3 style="font-family: 'Syne', sans-serif; margin-bottom: 1.5rem; color: var(--accent);">1. Choose Source Class</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; align-items: end;">
            <div>
                <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">From Session</label>
                <select id="from_session_id" class="form-input">
                    <option value="">Select Session</option>
                    @foreach($academicSessions as $ses)
                        <option value="{{ $ses->id }}">{{ $ses->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">From Class</label>
                <select id="from_class_id" class="form-input">
                    <option value="">Select Class</option>
                    @foreach($classes as $cls)
                        <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">From Section</label>
                <select id="from_section_id" class="form-input">
                    <option value="">Select Section</option>
                </select>
            </div>
            <div>
                <button type="button" class="btn-accent" id="btnSearchStudents" style="width: 100%; justify-content: center;">
                    <i class="fa fa-search"></i> Load Student List
                </button>
            </div>
        </div>
    </div>

    <!-- Step 2 & 3: Selection and Target class form -->
    <form action="{{ route('school.students.promote') }}" method="POST" id="promotionForm" style="display: none; margin-top: 2rem;">
        @csrf
        <input type="hidden" name="from_session_id" id="form_from_session_id">
        <input type="hidden" name="from_class_id" id="form_from_class_id">

        <div style="display: flex; gap: 2rem; align-items: flex-start; flex-wrap: wrap;">
            <!-- Student Selection List -->
            <div class="glass-card" style="flex: 1.5; min-width: 300px; max-height: 500px; overflow-y: auto;">
                <h3 style="font-family: 'Syne', sans-serif; margin-bottom: 1rem;">2. Select Students to Promote</h3>
                <div style="margin-bottom: 1rem;">
                    <label style="cursor: pointer; display: flex; align-items: center; gap: 0.5rem; font-weight: 700;">
                        <input type="checkbox" id="checkAll"> Select All / Deselect All
                    </label>
                </div>
                <div id="studentListContainer" style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <!-- Checked students dynamically populated here -->
                </div>
            </div>

            <!-- Promotion Target mapping -->
            <div class="glass-card" style="flex: 1; min-width: 250px;">
                <h3 style="font-family: 'Syne', sans-serif; margin-bottom: 1.5rem; color: var(--success);">3. Destination Target</h3>
                
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <div>
                        <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">To Session</label>
                        <select name="to_session_id" class="form-input" required>
                            <option value="">Select Session</option>
                            @foreach($academicSessions as $ses)
                                <option value="{{ $ses->id }}">{{ $ses->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">To Class</label>
                        <select name="to_class_id" class="form-input" required>
                            <option value="">Select Class</option>
                            @foreach($classes as $cls)
                                <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">To Section</label>
                        <select name="to_section_id" class="form-input" required>
                            <option value="">Select Section</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-accent" style="background-color: var(--success); width: 100%; justify-content: center; padding: 1rem;">
                        <i class="fa fa-level-up-alt"></i> Execute Promotion
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    const allSections = @json($sections);

    // Section filtering helper
    function filterSelectSections(classSelect, sectionSelect) {
        let classId = $(classSelect).val();
        let targetSelect = $(sectionSelect);
        targetSelect.empty().append('<option value="">Select Section</option>');

        if (classId) {
            let filtered = allSections.filter(s => s.class_id == classId);
            filtered.forEach(function(sec) {
                targetSelect.append('<option value="' + sec.id + '">' + sec.name + '</option>');
            });
        }
    }

    $('#from_class_id').on('change', function() {
        filterSelectSections(this, '#from_section_id');
    });

    $('select[name="to_class_id"]').on('change', function() {
        filterSelectSections(this, 'select[name="to_section_id"]');
    });

    // Check all functionality
    $('#checkAll').on('change', function() {
        $('.student-checkbox').prop('checked', this.checked);
    });

    // Search students via AJAX
    $('#btnSearchStudents').on('click', function() {
        let sessionId = $('#from_session_id').val();
        let classId = $('#from_class_id').val();
        let sectionId = $('#from_section_id').val();

        if (!sessionId || !classId || !sectionId) {
            alert('Please select Session, Class, and Section first.');
            return;
        }

        // Fetch student list dynamically
        let container = $('#studentListContainer');
        container.html('<span style="color: var(--warning);"><i class="fa fa-spinner fa-spin"></i> Fetching student list...</span>');
        
        // Populate inputs to main form
        $('#form_from_session_id').val(sessionId);
        $('#form_from_class_id').val(classId);

        // Fetch students in this section using the existing Student List API or a custom controller action.
        // We will call the Load Section endpoint from the StudentAttendanceController which returns the HTML, 
        // or query the database. Since we want an array, let's execute an AJAX request to /school/students
        // but fetch JSON. We can use jquery to fetch from students index with json expectations.
        $.ajax({
            url: "{{ route('school.students.index') }}",
            type: "GET",
            data: {
                class_id: classId,
                section_id: sectionId,
                academic_session_id: sessionId,
                is_active: 1
            },
            dataType: 'html', // we can scrape it, or since we want json, we can request with Header Accept application/json!
            headers: {
                'Accept': 'application/json'
            },
            success: function(response) {
                // If it returns JSON list
                container.empty();
                let students = response.data || [];
                if (students.length === 0) {
                    container.html('<span style="color: var(--text-muted);">No active students found in this section.</span>');
                    $('#promotionForm').hide();
                    return;
                }

                students.forEach(function(student) {
                    container.append(
                        '<label style="display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem; border-bottom: 1px solid var(--border); cursor: pointer;">' +
                        '<input type="checkbox" name="student_ids[]" value="' + student.id + '" class="student-checkbox" checked> ' +
                        '<span><strong>' + student.admission_number + '</strong> - ' + student.first_name + ' ' + student.last_name + ' (Roll: ' + (student.roll_number || 'N/A') + ')</span>' +
                        '</label>'
                    );
                });

                $('#promotionForm').show();
            },
            error: function() {
                container.html('<span style="color: var(--danger);">Failed to load students. Make sure route and permissions are correct.</span>');
            }
        });
    });
</script>
@endsection
