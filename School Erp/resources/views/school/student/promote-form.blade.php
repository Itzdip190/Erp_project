@extends('layouts.app')

@section('page-title', 'Student Promotion')

@section('content')

<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-level-up-alt" style="color:var(--gold);margin-right:8px;"></i>Student Promotion Panel</h1>
        <p>Promote active students from one class/session to the next academic year</p>
    </div>
    <div class="page-hdr-right">
        <a href="{{ route('school.students.index') }}" class="btn btn-outline">
            <i class="fa fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<div class="card">
    <div class="card-hdr">
        <h3><i class="fas fa-filter" style="color:var(--gold);margin-right:6px;"></i>1. Choose Source Class</h3>
    </div>
    <div class="card-body">
        <div class="grid-4" style="align-items:end;">
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">From Session</label>
                <select id="from_session_id" class="form-control">
                    <option value="">Select Session</option>
                    @foreach($academicSessions as $ses)
                        <option value="{{ $ses->id }}">{{ $ses->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">From Class</label>
                <select id="from_class_id" class="form-control">
                    <option value="">Select Class</option>
                    @foreach($classes as $cls)
                        <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">From Section</label>
                <select id="from_section_id" class="form-control">
                    <option value="">Select Section</option>
                </select>
            </div>
            <div>
                <button type="button" class="btn btn-gold" id="btnSearchStudents" style="width:100%;justify-content:center;">
                    <i class="fa fa-search"></i> Load Student List
                </button>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('school.students.promote') }}" method="POST" id="promotionForm" style="display:none;margin-top:20px;">
    @csrf
    <input type="hidden" name="from_session_id" id="form_from_session_id">
    <input type="hidden" name="from_class_id" id="form_from_class_id">

    <div style="display:flex;gap:20px;align-items:flex-start;flex-wrap:wrap;">
        <!-- Student Selection List -->
        <div class="card" style="flex:1.5;min-width:300px;">
            <div class="card-hdr">
                <h3><i class="fas fa-users" style="color:var(--gold);margin-right:6px;"></i>2. Select Students to Promote</h3>
            </div>
            <div class="card-body">
                <div style="margin-bottom:12px;padding-bottom:10px;border-bottom:1px solid var(--border);">
                    <label style="cursor:pointer;display:inline-flex;align-items:center;gap:8px;font-weight:700;font-size:13px;color:var(--navy);">
                        <input type="checkbox" id="checkAll" checked> Select All / Deselect All
                    </label>
                </div>
                <div id="studentListContainer" style="display:flex;flex-direction:column;gap:8px;max-height:400px;overflow-y:auto;padding-right:8px;">
                    <!-- Checked students dynamically populated here -->
                </div>
            </div>
        </div>

        <!-- Promotion Target mapping -->
        <div class="card" style="flex:1;min-width:250px;">
            <div class="card-hdr">
                <h3><i class="fas fa-arrow-alt-circle-right" style="color:var(--green);margin-right:6px;"></i>3. Destination Target</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">To Session <span>*</span></label>
                    <select name="to_session_id" class="form-control" required>
                        <option value="">Select Session</option>
                        @foreach($academicSessions as $ses)
                            <option value="{{ $ses->id }}">{{ $ses->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">To Class <span>*</span></label>
                    <select name="to_class_id" class="form-control" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $cls)
                            <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">To Section <span>*</span></label>
                    <select name="to_section_id" class="form-control" required>
                        <option value="">Select Section</option>
                    </select>
                </div>

                <div style="margin-top:20px;">
                    <button type="submit" class="btn btn-success" style="width:100%;justify-content:center;padding:12px;">
                        <i class="fa fa-level-up-alt"></i> Execute Promotion
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

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
        container.html('<span style="color:var(--gold);font-size:12.5px;"><i class="fa fa-spinner fa-spin"></i> Fetching student list...</span>');
        
        // Populate inputs to main form
        $('#form_from_session_id').val(sessionId);
        $('#form_from_class_id').val(classId);

        // Fetch students in this section using AJAX with wantsJson header
        $.ajax({
            url: "{{ route('school.students.index') }}",
            type: "GET",
            data: {
                class_id: classId,
                section_id: sectionId,
                academic_session_id: sessionId,
                is_active: 1
            },
            dataType: 'json',
            headers: {
                'Accept': 'application/json'
            },
            success: function(response) {
                container.empty();
                let students = response.data || [];
                if (students.length === 0) {
                    container.html('<span style="color:var(--t3);font-size:12.5px;">No active students found in this section.</span>');
                    $('#promotionForm').hide();
                    return;
                }

                students.forEach(function(student) {
                    container.append(
                        '<label style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-bottom:1px solid var(--border);cursor:pointer;font-size:13px;color:var(--t1);">' +
                        '<input type="checkbox" name="student_ids[]" value="' + student.id + '" class="student-checkbox" checked> ' +
                        '<span><strong>' + student.admission_number + '</strong> - ' + student.first_name + ' ' + student.last_name + ' (Roll: ' + (student.roll_number || 'N/A') + ')</span>' +
                        '</label>'
                    );
                });

                $('#promotionForm').show();
            },
            error: function() {
                container.html('<span style="color:var(--red);font-size:12.5px;">Failed to load students. Make sure route and permissions are correct.</span>');
            }
        });
    });
</script>
@endsection
