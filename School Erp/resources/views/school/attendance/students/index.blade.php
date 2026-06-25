@extends('layouts.app')

@section('page-title', 'Student Attendance')

@section('content')
<style>
    /* Styling for the Filter Section and Cards */
    .filter-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 12px;
        box-shadow: var(--shadow);
        padding: 20px;
        margin-bottom: 24px;
    }
    
    .filter-grid-2 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        align-items: flex-end;
    }
    
    /* Stats Cards Styles */
    .stats-container {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 12px;
        margin-bottom: 24px;
    }
    
    @media (max-width: 1200px) {
        .stats-container {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    
    @media (max-width: 768px) {
        .stats-container {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    .stat-card {
        display: flex;
        border-radius: 8px;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    .stat-card-left {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 30%;
        min-width: 44px;
        font-size: 20px;
    }
    
    .stat-card-right {
        display: flex;
        flex-direction: column;
        justify-content: center;
        width: 70%;
        padding: 12px 16px;
    }
    
    .stat-card-count {
        font-size: 24px;
        font-weight: 800;
        line-height: 1;
    }
    
    .stat-card-label {
        font-size: 11px;
        font-weight: 700;
        opacity: 0.9;
        text-transform: uppercase;
        margin-top: 4px;
        letter-spacing: 0.5px;
    }
    
    /* Stats Colors matching the design */
    .stat-present { background-color: #10b981; }
    .stat-present .stat-card-left { background-color: #059669; }
    
    .stat-absent { background-color: #ef4444; }
    .stat-absent .stat-card-left { background-color: #b91c1c; }
    
    .stat-halfday { background-color: #eab308; }
    .stat-halfday .stat-card-left { background-color: #a16207; }
    
    .stat-leave { background-color: #d97706; }
    .stat-leave .stat-card-left { background-color: #9a3412; }
    
    .stat-duty-leave { background-color: #ec4899; }
    .stat-duty-leave .stat-card-left { background-color: #be185d; }
    
    .stat-not-marked { background-color: #9ca3af; }
    .stat-not-marked .stat-card-left { background-color: #4b5563; }
    
    /* Edit mode toggle rules */
    .in-edit-mode .view-only-block { display: none !important; }
    .in-edit-mode .view-only-inline { display: none !important; }
    .in-edit-mode .edit-only-block { display: block !important; }
    .in-edit-mode .edit-only-inline { display: inline !important; }
    .in-edit-mode .edit-only-flex { display: flex !important; }
    
    /* Styling elements */
    .btn-outline-gold {
        border: 1px solid #b45309;
        color: #b45309;
        background: transparent;
        font-weight: 700;
        transition: all 0.2s;
    }
    .btn-outline-gold:hover {
        background: rgba(180, 83, 9, 0.05);
    }
</style>

<div class="page-hdr" style="margin-bottom: 20px;">
    <div class="page-hdr-left">
        <h1 style="font-size: 24px; font-weight: 800; color: var(--navy); display: flex; align-items: center; gap: 8px;">
            Student Attendance 
            <span style="display: inline-flex; align-items: center; justify-content: center; width: 20px; height: 20px; border-radius: 50%; background: #f97316; color: #fff; font-size: 10px; cursor: pointer;">
                <i class="fas fa-chevron-down"></i>
            </span>
        </h1>
        <p style="font-size: 13px; color: var(--t3); margin: 4px 0 0 0;">Student Management</p>
    </div>
</div>

<!-- Filter Section -->
<div class="filter-card">
    <!-- Row 1: Academic Session, Date, Export Actions -->
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 18px; flex-wrap: wrap; gap: 16px;">
        <div style="display: flex; gap: 16px; flex-wrap: wrap;">
            <div class="form-group" style="margin-bottom: 0; min-width: 220px;">
                <label class="form-label" style="font-weight: 700; font-size: 12px; color: var(--t2); margin-bottom: 6px;">Academic Year *</label>
                <div style="position: relative;">
                    <i class="far fa-calendar-alt" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--t3);"></i>
                    <select id="academic_session_id" class="form-control" style="padding-left: 36px; height: 42px; border-radius: 8px; font-size: 13.5px; color: var(--t1); border: 1px solid #cbd5e1;" required>
                        @foreach($academicSessions as $ses)
                            <option value="{{ $ses->id }}" {{ $ses->is_current ? 'selected' : '' }}>{{ $ses->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 0; min-width: 220px;">
                <label class="form-label" style="font-weight: 700; font-size: 12px; color: var(--t2); margin-bottom: 6px;">Pick Date</label>
                <div style="position: relative;">
                    <i class="far fa-calendar" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--t3); pointer-events: none;"></i>
                    <input type="date" id="attendance_date" class="form-control" value="{{ date('Y-m-d') }}" style="padding-left: 36px; height: 42px; border-radius: 8px; font-size: 13.5px; color: var(--t1); border: 1px solid #cbd5e1;" required>
                </div>
            </div>
        </div>
        
        <div style="display: flex; gap: 8px; align-items: center;">
            <button type="button" class="btn btn-outline-gold" style="height: 42px; border-radius: 8px; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; font-size: 12px; padding: 0 16px;">
                <i class="far fa-file-excel"></i> VIEW EXCEL
            </button>
            <button type="button" class="btn btn-outline-gold" style="height: 42px; border-radius: 8px; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; font-size: 12px; padding: 0 16px;">
                <i class="fas fa-download"></i> DOWNLOAD
            </button>
            <button type="button" class="btn-icon" style="height: 42px; width: 42px; border-radius: 8px; border: 1px solid #cbd5e1; display: inline-flex; align-items: center; justify-content: center; background: #fff; color: #64748b; font-size: 14px;">
                <i class="far fa-comment-alt"></i>
            </button>
            <button type="button" class="btn-icon" style="height: 42px; width: 42px; border-radius: 8px; border: 1px solid #cbd5e1; display: inline-flex; align-items: center; justify-content: center; background: #fff; color: #64748b; font-size: 14px;">
                <i class="fas fa-cog"></i>
            </button>
        </div>
    </div>
    
    <!-- Row 2: Select Class, Section, Search, Status filter -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; align-items: end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" style="font-weight: 700; font-size: 12px; color: var(--t2); margin-bottom: 6px;">Select Class</label>
            <div style="position: relative;">
                <i class="fas fa-book" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--t3);"></i>
                <select id="class_id" class="form-control" style="padding-left: 34px; height: 42px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13.5px;" required>
                    <option value="">Select Class</option>
                    @foreach($classes as $cls)
                        <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" style="font-weight: 700; font-size: 12px; color: var(--t2); margin-bottom: 6px;">Select Section</label>
            <div style="position: relative;">
                <i class="fas fa-book" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--t3);"></i>
                <select id="section_id" class="form-control" style="padding-left: 34px; height: 42px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13.5px;" required>
                    <option value="">Select Section</option>
                </select>
            </div>
        </div>
        
        <div class="form-group" style="margin-bottom: 0; grid-column: span 2;">
            <label class="form-label" style="font-weight: 700; font-size: 12px; color: var(--t2); margin-bottom: 6px;">Search</label>
            <div style="position: relative;">
                <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--t3);"></i>
                <input type="text" id="search_student" class="form-control" placeholder="Student Name" style="padding-left: 34px; height: 42px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13.5px;">
            </div>
        </div>
        
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" style="font-weight: 700; font-size: 12px; color: var(--t2); margin-bottom: 6px;">Select Status</label>
            <div style="position: relative;">
                <i class="fas fa-folder" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--t3);"></i>
                <select id="status_filter" class="form-control" style="padding-left: 34px; height: 42px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13.5px;">
                    <option value="">All Statuses</option>
                    <option value="present">Present</option>
                    <option value="half_day">Half Day</option>
                    <option value="absent">Absent</option>
                    <option value="leave">Leave</option>
                    <option value="duty_leave">Duty Leave</option>
                    <option value="not_marked">Not Marked</option>
                </select>
            </div>
        </div>
        
        <div>
            <button type="button" id="show_logs_btn" class="btn" style="height: 42px; border-radius: 8px; border: 1px solid #b45309; color: #b45309; background: transparent; font-weight: 700; font-size: 12px; width: 100%;">
                SHOW LOGS
            </button>
        </div>
    </div>
</div>

<!-- Stats Counter Badges -->
<div class="stats-container">
    <!-- Present -->
    <div class="stat-card stat-present">
        <div class="stat-card-left"><i class="fas fa-check" style="font-size: 16px;"></i></div>
        <div class="stat-card-right">
            <div class="stat-card-count" id="count-present">0</div>
            <div class="stat-card-label">Present</div>
        </div>
    </div>
    
    <!-- Absent -->
    <div class="stat-card stat-absent">
        <div class="stat-card-left"><i class="fas fa-times" style="font-size: 16px;"></i></div>
        <div class="stat-card-right">
            <div class="stat-card-count" id="count-absent">0</div>
            <div class="stat-card-label">Absent</div>
        </div>
    </div>
    
    <!-- Half Day -->
    <div class="stat-card stat-halfday">
        <div class="stat-card-left"><i class="fas fa-times" style="font-size: 16px; transform: rotate(45deg);"></i></div>
        <div class="stat-card-right">
            <div class="stat-card-count" id="count-halfday">0</div>
            <div class="stat-card-label">HalfDay</div>
        </div>
    </div>
    
    <!-- Leave -->
    <div class="stat-card stat-leave">
        <div class="stat-card-left"><i class="fas fa-check" style="font-size: 16px;"></i></div>
        <div class="stat-card-right">
            <div class="stat-card-count" id="count-leave">0</div>
            <div class="stat-card-label">Leave</div>
        </div>
    </div>
    
    <!-- Duty Leave -->
    <div class="stat-card stat-duty-leave">
        <div class="stat-card-left"><i class="fas fa-check" style="font-size: 16px;"></i></div>
        <div class="stat-card-right">
            <div class="stat-card-count" id="count-duty-leave">0</div>
            <div class="stat-card-label">Duty Leave</div>
        </div>
    </div>
    
    <!-- Not Marked -->
    <div class="stat-card stat-not-marked">
        <div class="stat-card-left"><i class="fas fa-ban" style="font-size: 16px;"></i></div>
        <div class="stat-card-right">
            <div class="stat-card-count" id="count-not-marked">0</div>
            <div class="stat-card-label">NOT MARKED</div>
        </div>
    </div>
</div>

<!-- Marking Form -->
<form action="{{ route('school.attendance.students.store') }}" method="POST" id="attendanceSaveForm" style="display: none;">
    @csrf
    <input type="hidden" name="section_id" id="form_section_id">
    <input type="hidden" name="date" id="form_date">
    <input type="hidden" name="academic_session_id" id="form_academic_session_id">

    <div class="card" style="border-radius: 12px; overflow: hidden; border: 1px solid var(--border); box-shadow: var(--shadow);">
        <div class="card-body" style="padding: 0;">
            <div id="attendanceTableContainer" style="padding: 20px; color: var(--t3); text-align: center; background: #fff;">
                <div style="padding: 24px; color: var(--t2); font-weight: 500;">
                    Select Class and Section above to view student registers.
                </div>
            </div>
            
            <!-- Bottom Buttons Container -->
            <div id="form-buttons-container" style="padding: 16px 20px; border-top: 1px solid var(--border); background: #f8fafc; display: flex; justify-content: flex-end; align-items: center;">
                <!-- View Mode Button -->
                <button type="button" class="btn" id="btn-mark-attendance" onclick="enterEditMode()" style="background-color: #b45309; border-color: #b45309; color: #fff; padding: 10px 24px; font-weight: 700; border-radius: 6px;">
                    MARK ATTENDANCE
                </button>
                
                <!-- Edit Mode Buttons -->
                <button type="button" class="btn btn-outline" id="btn-cancel-edit" onclick="exitEditMode()" style="display: none; padding: 10px 24px; font-weight: 700; border-radius: 6px; margin-right: 12px; border: 1px solid #cbd5e1; background: #fff; color: #475569;">
                    CANCEL
                </button>
                <button type="submit" class="btn" id="btn-save-attendance" style="display: none; background-color: #b45309; border-color: #b45309; color: #fff; padding: 10px 24px; font-weight: 700; border-radius: 6px;">
                    SAVE
                </button>
            </div>
        </div>
    </div>
</form>

@endsection

@section('scripts')
<script>
const allSections = @json($sections);
const sessions = @json($academicSessions);

// Handle class change to filter sections
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

// Auto-retrieve when parameters are selected
$('#class_id, #section_id, #attendance_date, #academic_session_id').on('change', function() {
    if (this.id === 'academic_session_id') {
        updateDateInputBoundaries();
    }
    triggerRetrieveStudents();
});

// Update min/max attributes on pick date field based on selected academic session dates
function updateDateInputBoundaries() {
    let sessionId = $('#academic_session_id').val();
    let dateInput = $('#attendance_date');
    if (!sessionId) return;
    
    let session = sessions.find(s => s.id == sessionId);
    if (session) {
        // Set calendar boundaries dynamically
        dateInput.attr('min', session.start_date);
        dateInput.attr('max', session.end_date);
        
        // Correct date if currently out of boundaries
        let val = dateInput.val();
        if (val) {
            let currentDate = new Date(val);
            currentDate.setHours(0,0,0,0);
            let startDate = new Date(session.start_date);
            startDate.setHours(0,0,0,0);
            let endDate = new Date(session.end_date);
            endDate.setHours(0,0,0,0);
            
            if (currentDate < startDate) {
                dateInput.val(session.start_date);
            } else if (currentDate > endDate) {
                dateInput.val(session.end_date);
            }
        }
    }
}

// Initial boundaries setup
updateDateInputBoundaries();

function triggerRetrieveStudents() {
    let sectionId = $('#section_id').val();
    let date      = $('#attendance_date').val();
    let sessionId = $('#academic_session_id').val();
    
    if (!sectionId || !date || !sessionId) {
        $('#attendanceSaveForm').hide();
        return;
    }

    $('#form_section_id').val(sectionId);
    $('#form_date').val(date);
    $('#form_academic_session_id').val(sessionId);

    // Switch back to view mode on reload
    exitEditModeStyles();

    let container = $('#attendanceTableContainer');
    container.html('<div style="padding:24px;text-align:center;color:var(--t2);"><i class="fas fa-spinner fa-spin" style="font-size:20px;color:#d97706;display:block;margin-bottom:8px;"></i>Loading student registers...</div>');
    $('#attendanceSaveForm').show();

    $.ajax({
        url: "{{ route('school.attendance.students.load') }}",
        type: "POST",
        data: { 
            _token: "{{ csrf_token() }}",
            section_id: sectionId, 
            date: date, 
            academic_session_id: sessionId 
        },
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
}

// Enter Edit/Marking Mode
function enterEditMode() {
    $('#attendanceSaveForm').addClass('in-edit-mode');
    $('#btn-mark-attendance').hide();
    $('#btn-cancel-edit').show();
    $('#btn-save-attendance').show();
    updateCounts();
}

// Reset Styles back to View Mode
function exitEditModeStyles() {
    $('#attendanceSaveForm').removeClass('in-edit-mode');
    $('#btn-mark-attendance').show();
    $('#btn-cancel-edit').hide();
    $('#btn-save-attendance').hide();
}

// Exit Edit Mode and reload original values
function exitEditMode() {
    exitEditModeStyles();
    triggerRetrieveStudents();
}

// Set All status in header
function setAllStatus(status) {
    $(`.status-btn.btn-${status === 'half_day' ? 'hd' : (status === 'duty_leave' ? 'dl' : status.charAt(0))} input`).prop('checked', true).trigger('change');
}

// Clear all selected radios
function clearAllAttendance() {
    $('.status-radio').prop('checked', false).trigger('change');
}

// Dynamic counter stats updater
function updateCounts() {
    let total = $('#attendanceTableBody tr[data-student-id]').length;
    let present = 0;
    let absent = 0;
    let halfday = 0;
    let leave = 0;
    let duty_leave = 0;
    let not_marked = 0;

    let isEditMode = $('#attendanceSaveForm').hasClass('in-edit-mode');

    $('#attendanceTableBody tr[data-student-id]').each(function() {
        let row = $(this);
        let status = 'not_marked';
        
        if (isEditMode) {
            let checkedRadio = row.find('input[type="radio"]:checked');
            if (checkedRadio.length > 0) {
                status = checkedRadio.val();
            }
        } else {
            status = row.attr('data-status') || 'not_marked';
        }

        if (status === 'present') present++;
        else if (status === 'absent') absent++;
        else if (status === 'half_day') halfday++;
        else if (status === 'leave') leave++;
        else if (status === 'duty_leave') duty_leave++;
        else not_marked++;
    });

    // Update stats counters
    $('#count-present').text(present);
    $('#count-absent').text(absent);
    $('#count-halfday').text(halfday);
    $('#count-leave').text(leave);
    $('#count-duty-leave').text(duty_leave);
    $('#count-not-marked').text(not_marked);
}

// Event listeners for radio button updates
$(document).on('change', '.status-radio', function() {
    updateCounts();
    $(this).closest('tr').attr('data-status', $(this).val());
    filterTable();
});

// Event listener for search and status filters
$(document).on('input', '#search_student', function() {
    filterTable();
});

$(document).on('change', '#status_filter', function() {
    filterTable();
});

function filterTable() {
    let searchText = $('#search_student').val().toLowerCase();
    let statusFilter = $('#status_filter').val();
    
    $('#attendanceTableBody tr[data-student-id]').each(function() {
        let row = $(this);
        let name = row.find('.student-name').text().toLowerCase();
        let roll = row.find('.student-roll').text().toLowerCase();
        let status = row.attr('data-status') || 'not_marked';
        
        let matchesSearch = name.includes(searchText) || roll.includes(searchText);
        let matchesStatus = !statusFilter || (statusFilter === 'not_marked' && status === 'not_marked') || (status === statusFilter);
        
        if (matchesSearch && matchesStatus) {
            row.show();
        } else {
            row.hide();
        }
    });
}

// File Attachment icon update
function updateAttachmentIcon(input) {
    let label = $(input).closest('.attachment-btn');
    if (input.files && input.files.length > 0) {
        label.css('color', '#10b981');
        label.attr('title', 'File Selected: ' + input.files[0].name);
    } else {
        label.css('color', '#d97706');
        label.attr('title', 'Upload Attachment');
    }
}
</script>
@endsection
