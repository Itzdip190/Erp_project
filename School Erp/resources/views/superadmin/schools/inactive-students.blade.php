@extends('superadmin.layouts.master')

@section('styles')
<style>
    .sa-inactive-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.03);
        overflow: hidden;
        margin-bottom: 30px;
    }

    .sa-inactive-hdr {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }
    
    .sa-inactive-hdr-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .sa-inactive-hdr-icon {
        width: 38px; height: 38px;
        border-radius: 10px;
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        display: flex; align-items: center; justify-content: center;
        font-size: 15px;
    }
    .sa-inactive-hdr h3 { font-size: 15px; font-weight: 800; color: #1e1b4b; margin: 0; }
    .sa-inactive-hdr p { font-size: 11px; color: #64748b; margin: 2px 0 0; }

    .table-custom th {
        font-size: 0.72rem;
        font-weight: 800;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.8px;
        border-top: none !important;
        border-bottom: 2px solid #f3f4f6 !important;
        padding: 14px 16px !important;
    }

    .table-custom td {
        font-size: 0.88rem;
        color: #1e1b4b;
        vertical-align: middle !important;
        padding: 14px 16px !important;
        border-bottom: 1px solid #f3f4f6 !important;
        border-top: none !important;
    }

    .student-name-cell {
        display: flex;
        flex-direction: column;
    }
    .student-name {
        font-weight: 700;
        color: #1e1b4b;
    }
    .student-admission {
        font-size: 0.75rem;
        color: #6366f1;
        font-weight: 700;
        margin-top: 2px;
    }

    .btn-sa-action {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8rem;
        font-weight: 700;
        padding: 8px 16px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none !important;
    }

    .btn-restore {
        background-color: #ecfdf5;
        color: #10b981;
        border: 1px solid #a7f3d0;
    }

    .btn-restore:hover {
        background-color: #10b981;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);
    }

    .btn-delete-perm {
        background-color: #fee2e2;
        color: #ef4444;
        border: 1px solid #fca5a5;
    }

    .btn-delete-perm:hover {
        background-color: #ef4444;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2);
    }

    .btn-sa-cancel {
        padding: 8px 16px;
        border-radius: 8px;
        border: 1.5px solid #e2e8f0;
        background: #fff;
        color: #4b5563;
        font-size: 13px; font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex; align-items: center; justify-content: center;
        transition: all .2s;
    }
    .btn-sa-cancel:hover { background: #f8fafc; border-color: #cbd5e1; }

    /* Dark mode overrides */
    body.dark-mode .sa-inactive-card { background: #111827 !important; border-color: #1e293b !important; }
    body.dark-mode .sa-inactive-hdr { border-bottom-color: #1e293b !important; }
    body.dark-mode .sa-inactive-hdr h3 { color: #f1f5f9 !important; }
    body.dark-mode .sa-inactive-hdr p { color: #64748b !important; }
    body.dark-mode .table-custom th { border-bottom-color: #1e293b !important; color: #94a3b8 !important; }
    body.dark-mode .table-custom td { border-bottom-color: #1e293b !important; color: #cbd5e1 !important; }
    body.dark-mode .student-name { color: #f1f5f9 !important; }
    body.dark-mode .btn-sa-cancel { background: #1f2937 !important; border-color: #374151 !important; color: #cbd5e1 !important; }
    body.dark-mode .btn-sa-cancel:hover { background: #111827 !important; }
</style>
@endsection

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible show mb-4" role="alert" style="border-radius: 12px;">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible show mb-4" role="alert" style="border-radius: 12px;">
        <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="sa-inactive-card">
    <div class="sa-inactive-hdr">
        <div class="sa-inactive-hdr-left">
            <div class="sa-inactive-hdr-icon">
                <i class="fas fa-user-slash"></i>
            </div>
            <div>
                <h3>Inactive Students - {{ $school->name }}</h3>
                <p>Review deactivated students. Authorize permanent deletion or restore active status.</p>
            </div>
        </div>
        <div>
            <a href="{{ route('superadmin.schools.index') }}" class="btn-sa-cancel">
                <i class="fas fa-arrow-left mr-1"></i> Back to Schools
            </a>
        </div>
    </div>

    <!-- Bulk Action Toolbar -->
    <div class="card-hdr" style="background:#faf8f5; border-bottom:1px solid #e2e8f0; padding:12px 24px; display:flex; align-items:center; gap:16px; flex-wrap:wrap; justify-content:space-between;">
        <div style="font-size:13px; font-weight:700; color:#1e1b4b;">
            <span id="selectedCountText">0 student(s) selected</span>
        </div>
        <div style="display:flex; align-items:center; gap:12px;">
            <button type="button" class="btn-sa-action btn-restore" id="btnBulkRestore">
                <i class="fas fa-undo"></i> Restore Selected
            </button>
            <button type="button" class="btn-sa-action btn-delete-perm" id="btnBulkDeletePerm">
                <i class="fas fa-trash-alt"></i> Delete Permanently
            </button>
        </div>
    </div>

    <div class="p-0">
        <form id="inactiveStudentsForm" method="POST" style="margin:0;">
            @csrf
            <div class="table-responsive">
                <table class="table table-custom m-0">
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;"><input type="checkbox" id="selectAll"></th>
                            <th>Student Profile</th>
                            <th>Class & Section</th>
                            <th>Guardian Details</th>
                            <th>Deactivated At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inactiveStudents as $student)
                            <tr>
                                <td style="text-align: center;">
                                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="student-select">
                                </td>
                                <td>
                                    <div class="student-name-cell">
                                        <span class="student-name">{{ $student->full_name }}</span>
                                        <span class="student-admission"><i class="fas fa-id-card mr-1"></i> Admission No: {{ $student->admission_number }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight:600;">{{ $student->class?->name ?? 'N/A' }}</div>
                                    <div class="text-muted text-xs" style="font-size:0.78rem;">Section: {{ $student->section?->name ?? 'N/A' }}</div>
                                </td>
                                <td>
                                    <div style="font-weight:600;">{{ $student->guardian_name ?? 'N/A' }}</div>
                                    <div class="text-muted text-xs" style="font-size:0.78rem;">{{ $student->guardian_phone }}</div>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $student->updated_at ? $student->updated_at->format('M d, Y H:i') : 'N/A' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <div style="font-size:32px; margin-bottom:10px;"><i class="fas fa-users-slash"></i></div>
                                    No inactive student data found for this school.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    </div>
    
    @if($inactiveStudents->hasPages())
        <div style="padding:14px 24px; border-top:1px solid #f1f5f9; display:flex; justify-content:flex-end;">
            {{ $inactiveStudents->links() }}
        </div>
    @endif
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Checkbox selector logic
    $('#selectAll').on('change', function() {
        $('.student-select').prop('checked', this.checked).trigger('change');
    });

    $(document).on('change', '.student-select', function() {
        let count = $('.student-select:checked').length;
        $('#selectedCountText').text(count + " student(s) selected");
    });

    // Bulk Restore Submit Action
    $('#btnBulkRestore').on('click', function() {
        let form = $('#inactiveStudentsForm');
        let selectedCount = $('.student-select:checked').length;

        if (selectedCount === 0) {
            alert('Please select at least one student to restore.');
            return;
        }

        if (confirm('Are you sure you want to restore the ' + selectedCount + ' selected student(s) back to Active status?')) {
            form.attr('action', "{{ route('superadmin.schools.restore-students', $school->id) }}");
            form.submit();
        }
    });

    // Bulk Delete Permanently Submit Action
    $('#btnBulkDeletePerm').on('click', function() {
        let form = $('#inactiveStudentsForm');
        let selectedCount = $('.student-select:checked').length;

        if (selectedCount === 0) {
            alert('Please select at least one student to delete permanently.');
            return;
        }

        let confirmInput = prompt('WARNING: You are about to permanently delete ' + selectedCount + ' student records from the database. This action is irreversible and all associated student data will be lost!\n\nPlease type "DELETE PERMANENTLY" to authorize this action:');

        if (confirmInput === 'DELETE PERMANENTLY') {
            form.attr('action', "{{ route('superadmin.schools.delete-students', $school->id) }}");
            form.submit();
        } else {
            alert('Authorization failed. Action cancelled.');
        }
    });
});
</script>
@endsection
