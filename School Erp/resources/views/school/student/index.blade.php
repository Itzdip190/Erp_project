@extends('layouts.app')

@section('page-title', 'Students')

@section('content')

<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-user-graduate" style="color:var(--gold);margin-right:8px;"></i>Student Management</h1>
        <p>Manage all enrolled students, admissions and records</p>
    </div>
    <div class="page-hdr-right">
        <a href="{{ route('school.students.import-template') }}" class="btn btn-outline">
            <i class="fas fa-download"></i> Template
        </a>
        <a href="{{ route('school.students.export', request()->all()) }}" class="btn btn-outline">
            <i class="fas fa-file-excel"></i> Export
        </a>
        <a href="{{ route('school.students.promote-form') }}" class="btn btn-primary">
            <i class="fas fa-level-up-alt"></i> Promote
        </a>
        <a href="{{ route('school.students.create') }}" class="btn btn-gold">
            <i class="fas fa-user-plus"></i> Admit Student
        </a>
    </div>
</div>

<!-- Bulk Import -->
<div class="card">
    <div class="card-hdr">
        <h3><i class="fas fa-file-import" style="color:var(--gold);margin-right:6px;"></i>Bulk Import Students</h3>
    </div>
    <div class="card-body">
        <form id="bulkImportForm" enctype="multipart/form-data" style="display:flex;gap:14px;align-items:center;flex-wrap:wrap;">
            <div style="flex:1;min-width:250px;">
                <input type="file" name="file" id="importFile" class="form-control" required accept=".csv,.xlsx">
            </div>
            <button type="submit" class="btn btn-accent">
                <i class="fas fa-cloud-upload-alt"></i> Upload & Process
            </button>
        </form>
        <div id="importFeedback" style="margin-top:10px;display:none;font-size:13px;"></div>
    </div>
</div>

<!-- Filters -->
<div class="card">
    <div class="card-hdr">
        <h3><i class="fas fa-filter" style="color:var(--gold);margin-right:6px;"></i>Filter Records</h3>
        <a href="{{ route('school.students.index') }}" class="btn btn-outline" style="font-size:11px;padding:5px 12px;">Clear Filters</a>
    </div>
    <div class="card-body">
        <form action="{{ route('school.students.index') }}" method="GET">
            <div class="grid-4" style="align-items:end;">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Class</label>
                    <select name="class_id" class="form-control">
                        <option value="">All Classes</option>
                        @foreach($classes as $cls)
                            <option value="{{ $cls->id }}" {{ request('class_id')==$cls->id?'selected':'' }}>{{ $cls->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Section</label>
                    <select name="section_id" class="form-control">
                        <option value="">All Sections</option>
                        @foreach($sections as $sec)
                            <option value="{{ $sec->id }}" {{ request('section_id')==$sec->id?'selected':'' }}>{{ $sec->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Status</label>
                    <select name="is_active" class="form-control">
                        <option value="">All Status</option>
                        <option value="1" {{ request('is_active')==='1'?'selected':'' }}>Active</option>
                        <option value="0" {{ request('is_active')==='0'?'selected':'' }}>Inactive</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Name or admission no…">
                </div>
            </div>
            <div style="margin-top:14px;text-align:right;">
                <button type="submit" class="btn btn-gold"><i class="fas fa-search"></i> Apply Filters</button>
            </div>
        </form>
    </div>
</div>

<!-- Student Table -->
<div class="card">
    <div class="card-hdr">
        <h3><i class="fas fa-list" style="color:var(--gold);margin-right:6px;"></i>Students List
            <span class="badge badge-blue" style="margin-left:8px;">{{ $students->total() }}</span>
        </h3>
    </div>
    
    <!-- Bulk Action Bar -->
    <div class="card-hdr" style="background:var(--page);border-bottom:1px solid var(--border);padding:10px 20px;display:none;" id="bulkActionBar">
        <div style="display:flex;align-items:center;gap:12px;">
            <span style="font-size:12.5px;font-weight:700;color:var(--navy);" id="selectedCountText">0 students selected</span>
            <button class="btn btn-gold" style="padding:6px 12px;font-size:11.5px;" id="btnBulkIssue"><i class="fas fa-share-square"></i> Issue Certificate</button>
        </div>
    </div>

    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th style="width:40px;text-align:center;"><input type="checkbox" id="bulkSelectAll"></th>
                        <th>Admission No</th>
                        <th>Roll No</th>
                        <th>Full Name</th>
                        <th>Class</th>
                        <th>Section</th>
                        <th>Guardian</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr>
                        <td style="text-align:center;"><input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="student-select"></td>
                        <td><span class="badge badge-blue">{{ $student->admission_number }}</span></td>
                        <td style="color:var(--t2);">{{ $student->roll_number }}</td>
                        <td>
                            <div style="font-weight:700;">{{ $student->full_name }}</div>
                            <small style="color:var(--t3);font-size:11px;">Age: {{ $student->age }}</small>
                        </td>
                        <td>{{ $student->class?->name }}</td>
                        <td>{{ $student->section?->name }}</td>
                        <td>
                            <div style="font-size:12px;font-weight:600;">{{ $student->guardian_name }}</div>
                            <small style="color:var(--t3);font-size:11px;">{{ $student->guardian_phone }}</small>
                        </td>
                        <td>
                            @if($student->is_active)
                                <span class="badge badge-success"><i class="fas fa-circle" style="font-size:7px;"></i> Active</span>
                            @else
                                <span class="badge badge-danger"><i class="fas fa-circle" style="font-size:7px;"></i> Inactive</span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            <div style="display:inline-flex;gap:6px;align-items:center;">
                                <a href="{{ route('school.students.show', $student->id) }}" class="btn btn-outline" style="padding:5px 9px;font-size:11px;" title="View"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('school.students.edit', $student->id) }}" class="btn btn-gold" style="padding:5px 9px;font-size:11px;" title="Edit"><i class="fas fa-edit"></i></a>
                                
                                <!-- Documents Dropdown -->
                                <div style="position:relative;display:inline-block;">
                                    <button class="btn btn-accent btn-doc-toggle" style="padding:5px 9px;font-size:11px;" title="Documents"><i class="fas fa-file-pdf"></i></button>
                                    <div class="doc-drop">
                                        <div class="doc-row">
                                            <span>ID Card (A5)</span>
                                            <div class="doc-actions">
                                                <a href="{{ route('school.students.id-card', $student->id) }}" target="_blank" class="btn-doc-action" title="View"><i class="fas fa-eye"></i></a>
                                                <button class="btn-doc-action btn-issue" data-id="{{ $student->id }}" data-type="id_card" title="Issue"><i class="fas fa-share-square"></i></button>
                                            </div>
                                        </div>
                                        <div class="doc-row">
                                            <span>Admit Card</span>
                                            <div class="doc-actions">
                                                <a href="{{ route('school.students.admit-card', $student->id) }}" target="_blank" class="btn-doc-action" title="View"><i class="fas fa-eye"></i></a>
                                                <button class="btn-doc-action btn-issue" data-id="{{ $student->id }}" data-type="admit_card" title="Issue"><i class="fas fa-share-square"></i></button>
                                            </div>
                                        </div>
                                        <div class="doc-row">
                                            <span>Character Cert</span>
                                            <div class="doc-actions">
                                                <a href="{{ route('school.students.certificate', [$student->id, 'character']) }}" target="_blank" class="btn-doc-action" title="View"><i class="fas fa-eye"></i></a>
                                                <button class="btn-doc-action btn-issue" data-id="{{ $student->id }}" data-type="character" title="Issue"><i class="fas fa-share-square"></i></button>
                                            </div>
                                        </div>
                                        <div class="doc-row">
                                            <span>DOB Cert</span>
                                            <div class="doc-actions">
                                                <a href="{{ route('school.students.certificate', [$student->id, 'dob']) }}" target="_blank" class="btn-doc-action" title="View"><i class="fas fa-eye"></i></a>
                                                <button class="btn-doc-action btn-issue" data-id="{{ $student->id }}" data-type="dob" title="Issue"><i class="fas fa-share-square"></i></button>
                                            </div>
                                        </div>
                                        <div class="doc-row">
                                            <span>Bonafide Cert</span>
                                            <div class="doc-actions">
                                                <a href="{{ route('school.students.certificate', [$student->id, 'bonafide']) }}" target="_blank" class="btn-doc-action" title="View"><i class="fas fa-eye"></i></a>
                                                <button class="btn-doc-action btn-issue" data-id="{{ $student->id }}" data-type="bonafide" title="Issue"><i class="fas fa-share-square"></i></button>
                                            </div>
                                        </div>
                                        <div class="doc-row">
                                            <span>Transfer Cert</span>
                                            <div class="doc-actions">
                                                <a href="{{ route('school.students.certificate', [$student->id, 'transfer']) }}" target="_blank" class="btn-doc-action" title="View"><i class="fas fa-eye"></i></a>
                                                <button class="btn-doc-action btn-issue" data-id="{{ $student->id }}" data-type="transfer" title="Issue"><i class="fas fa-share-square"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <form action="{{ route('school.students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('Delete this student?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding:5px 9px;font-size:11px;" title="Delete"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align:center;padding:48px;color:var(--t3);">
                            <i class="fas fa-user-slash" style="font-size:32px;display:block;margin-bottom:10px;color:var(--border);"></i>
                            No students found matching your filters.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:14px 20px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;">
            {{ $students->appends(request()->all())->links() }}
        </div>
    </div>
</div>

<!-- Modal for Bulk Issuance -->
<div id="bulkIssueModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
    <div class="card" style="width:100%;max-width:400px;margin:20px;box-shadow:var(--shadow-lg);">
        <div class="card-hdr">
            <h3><i class="fas fa-share-square" style="color:var(--gold);margin-right:8px;"></i>Issue Certificate / Card</h3>
            <button class="btn btn-outline" style="padding:4px 8px;font-size:11px;border:none;" onclick="document.getElementById('bulkIssueModal').style.display='none'">✕</button>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Select Document Type</label>
                <select id="bulkDocType" class="form-control">
                    <option value="id_card">Student ID Card (A5)</option>
                    <option value="admit_card">Exam Admit Card</option>
                    <option value="character">Character Certificate</option>
                    <option value="dob">Date of Birth Certificate</option>
                    <option value="bonafide">Bonafide Certificate</option>
                    <option value="transfer">Transfer Certificate</option>
                    <option value="appreciation">Appreciation Certificate</option>
                    <option value="achievement">Achievement Certificate</option>
                </select>
            </div>
            <div style="text-align:right;margin-top:20px;display:flex;justify-content:flex-end;gap:8px;">
                <button class="btn btn-outline" onclick="document.getElementById('bulkIssueModal').style.display='none'">Cancel</button>
                <button class="btn btn-gold" id="btnConfirmBulkIssue">Issue Now</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
.doc-drop {
    display: none;
    position: absolute;
    right: 0;
    top: 100%;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 10px;
    z-index: 100;
    box-shadow: var(--shadow-lg);
    min-width: 240px;
    margin-top: 4px;
    overflow: hidden;
    text-align: left;
}
.doc-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    border-bottom: 1px solid var(--border);
    font-size: 12px;
    color: var(--t1);
}
.doc-row:last-child {
    border-bottom: none;
}
.doc-row span {
    font-weight: 600;
}
.doc-actions {
    display: flex;
    gap: 4px;
}
.btn-doc-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 26px;
    height: 26px;
    border-radius: 5px;
    border: 1px solid var(--border);
    background: var(--page);
    color: var(--t2);
    cursor: pointer;
    font-size: 11px;
    transition: all 0.15s;
    text-decoration: none;
}
.btn-doc-action:hover {
    border-color: var(--gold);
    color: var(--gold);
    background: var(--gold-bg);
}
.btn-doc-action.btn-issue:hover {
    border-color: var(--green);
    color: var(--green);
    background: rgba(16, 185, 129, 0.12);
}
</style>
@endsection

@section('scripts')
<script>
// Toggle document dropdowns
$('.btn-doc-toggle').on('click', function(e) {
    e.stopPropagation();
    let drop = $(this).next('.doc-drop');
    $('.doc-drop').not(drop).hide();
    drop.toggle();
});

$(document).on('click', function(e) {
    if (!$(e.target).closest('.doc-drop').length && !$(e.target).closest('.btn-doc-toggle').length) {
        $('.doc-drop').hide();
    }
});

// CSRF Header
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// Single Document Issuance via AJAX
$('.btn-issue').on('click', function() {
    let btn = $(this);
    let id = btn.data('id');
    let type = btn.data('type');

    btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

    $.ajax({
        url: "/school/students/" + id + "/issue-document",
        type: "POST",
        data: { type: type },
        success: function(response) {
            btn.html('<i class="fas fa-check"></i>');
            showToast(response.message || "Document issued successfully!");
            setTimeout(() => { btn.html('<i class="fas fa-share-square"></i>').prop('disabled', false); }, 1500);
        },
        error: function(xhr) {
            btn.html('<i class="fas fa-times"></i>');
            showToast("Failed to issue document. Please try again.", true);
            setTimeout(() => { btn.html('<i class="fas fa-share-square"></i>').prop('disabled', false); }, 1500);
        }
    });
});

// Bulk checkbox logic
$('#bulkSelectAll').on('change', function() {
    $('.student-select').prop('checked', this.checked).trigger('change');
});

$(document).on('change', '.student-select', function() {
    let selected = $('.student-select:checked');
    let count = selected.length;

    if (count > 0) {
        $('#selectedCountText').text(count + " student(s) selected");
        $('#bulkActionBar').css('display', 'flex');
    } else {
        $('#bulkActionBar').hide();
    }
});

// Bulk Issue Trigger
$('#btnBulkIssue').on('click', function() {
    $('#bulkIssueModal').css('display', 'flex');
});

$('#btnConfirmBulkIssue').on('click', function() {
    let btn = $(this);
    let type = $('#bulkDocType').val();
    let studentIds = [];
    
    $('.student-select:checked').each(function() {
        studentIds.push($(this).val());
    });

    if (studentIds.length === 0) {
        showToast("No students selected.", true);
        return;
    }

    btn.html('<i class="fas fa-spinner fa-spin"></i> Issuing...').prop('disabled', true);

    $.ajax({
        url: "{{ route('school.students.bulk-issue-document') }}",
        type: "POST",
        data: {
            student_ids: studentIds,
            type: type
        },
        success: function(response) {
            $('#bulkIssueModal').hide();
            showToast(response.message || "Bulk documents issued successfully!");
            $('.student-select').prop('checked', false);
            $('#bulkSelectAll').prop('checked', false);
            $('#bulkActionBar').hide();
            btn.html('Issue Now').prop('disabled', false);
        },
        error: function() {
            showToast("Failed to issue bulk documents.", true);
            btn.html('Issue Now').prop('disabled', false);
        }
    });
});

// Bulk Import Excel/CSV Submit
$('#bulkImportForm').on('submit', function(e) {
    e.preventDefault();
    let fd = new FormData(this);
    let fb = $('#importFeedback');
    fb.show().html('<span style="color:var(--gold);"><i class="fas fa-spinner fa-spin"></i> Processing spreadsheet...</span>');
    $.ajax({
        url: "{{ route('school.students.import') }}",
        type: "POST", data: fd, processData: false, contentType: false,
        success: function(r) {
            fb.html(r.success
                ? '<span style="color:var(--green);"><i class="fas fa-check-circle"></i> Import started. Processing in background. Refresh shortly.</span>'
                : '<span style="color:var(--red);"><i class="fas fa-exclamation-circle"></i> ' + r.message + '</span>');
        },
        error: function() { fb.html('<span style="color:var(--red);"><i class="fas fa-exclamation-circle"></i> Error parsing sheet.</span>'); }
    });
});
</script>
@endsection
