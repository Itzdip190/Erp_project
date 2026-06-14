@extends('layouts.app')

@section('title', 'Student Management')

@section('content')
@if(session('success'))
    <div class="glass-card" style="background-color: rgba(16, 185, 129, 0.15); border-color: var(--success); color: var(--success); padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
        <i class="fa fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

<!-- Action Buttons Header -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; gap: 1rem; flex-wrap: wrap;">
    <div style="display: flex; gap: 1rem;">
        <a href="{{ route('school.students.create') }}" class="btn-accent">
            <i class="fa fa-user-plus"></i> Admit New Student
        </a>
        <a href="{{ route('school.students.promote-form') }}" class="btn-accent" style="background-color: var(--warning);">
            <i class="fa fa-level-up-alt"></i> Promote Students
        </a>
    </div>
    
    <div style="display: flex; gap: 1rem;">
        <a href="{{ route('school.students.export', request()->all()) }}" class="btn-accent" style="background-color: #4B5563;">
            <i class="fa fa-file-excel"></i> Export Selected
        </a>
        <a href="{{ route('school.students.import-template') }}" class="btn-accent" style="background-color: #10B981;">
            <i class="fa fa-download"></i> Import Template
        </a>
    </div>
</div>

<!-- Bulk Import Section -->
<div class="glass-card" style="margin-bottom: 2rem;">
    <h3 style="font-family: 'Syne', sans-serif; margin-bottom: 1rem;">Bulk Import Students</h3>
    <form id="bulkImportForm" enctype="multipart/form-data" style="display: flex; gap: 1.5rem; align-items: center; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 250px;">
            <input type="file" name="file" id="importFile" class="form-input" required accept=".csv,.xlsx">
        </div>
        <button type="submit" class="btn-accent" style="background-color: #8B5CF6;">
            <i class="fa fa-cloud-upload-alt"></i> Upload & Process
        </button>
    </form>
    <div id="importFeedback" style="margin-top: 1rem; display: none; font-size: 0.9rem;"></div>
</div>

<!-- Filters Bar -->
<div class="glass-card">
    <h3 style="font-family: 'Syne', sans-serif; margin-bottom: 1.5rem;">Filter Records</h3>
    <form action="{{ route('school.students.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem; align-items: end;">
        <!-- Class Filter -->
        <div>
            <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Class</label>
            <select name="class_id" class="form-input">
                <option value="">All Classes</option>
                @foreach($classes as $cls)
                    <option value="{{ $cls->id }}" {{ request('class_id') == $cls->id ? 'selected' : '' }}>{{ $cls->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Section Filter -->
        <div>
            <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Section</label>
            <select name="section_id" class="form-input">
                <option value="">All Sections</option>
                @foreach($sections as $sec)
                    <option value="{{ $sec->id }}" {{ request('section_id') == $sec->id ? 'selected' : '' }}>{{ $sec->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Session Filter -->
        <div>
            <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Academic Session</label>
            <select name="academic_session_id" class="form-input">
                <option value="">All Sessions</option>
                @foreach($academicSessions as $ses)
                    <option value="{{ $ses->id }}" {{ request('academic_session_id') == $ses->id ? 'selected' : '' }}>{{ $ses->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Status Filter -->
        <div>
            <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Status</label>
            <select name="is_active" class="form-input">
                <option value="">All Status</option>
                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <!-- Search Input -->
        <div>
            <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Search</label>
            <input type="text" name="search" class="form-input" value="{{ request('search') }}" placeholder="Name or admission number...">
        </div>

        <!-- Action Button -->
        <div>
            <button type="submit" class="btn-accent" style="width: 100%; justify-content: center;">
                <i class="fa fa-filter"></i> Apply Filters
            </button>
        </div>
    </form>
</div>

<!-- Student List Table -->
<div class="glass-card" style="margin-top: 2rem; padding: 1rem;">
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Admission No</th>
                    <th>Roll No</th>
                    <th>Full Name</th>
                    <th>Class</th>
                    <th>Section</th>
                    <th>Guardian Name</th>
                    <th>Guardian Phone</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td style="font-weight: 700;">{{ $student->admission_number }}</td>
                        <td>{{ $student->roll_number }}</td>
                        <td>
                            <div style="font-weight: 600;">{{ $student->full_name }}</div>
                            <small style="color: var(--text-muted); font-size: 0.75rem;">Age: {{ $student->age }}</small>
                        </td>
                        <td>{{ $student->class?->name }}</td>
                        <td>{{ $student->section?->name }}</td>
                        <td>{{ $student->guardian_name }}</td>
                        <td>{{ $student->guardian_phone }}</td>
                        <td>
                            @if($student->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: 0.5rem; align-items: center;">
                                <a href="{{ route('school.students.show', $student->id) }}" class="btn-accent" style="padding: 0.4rem 0.6rem; font-size: 0.8rem;" title="View Profile">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="{{ route('school.students.edit', $student->id) }}" class="btn-accent" style="padding: 0.4rem 0.6rem; font-size: 0.8rem; background-color: var(--warning);" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </a>
                                
                                <!-- Dropdown for Documents -->
                                <div style="position: relative; display: inline-block;">
                                    <button class="btn-accent" style="padding: 0.4rem 0.6rem; font-size: 0.8rem; background-color: #8B5CF6;" onclick="$(this).next().toggle()" title="Documents">
                                        <i class="fa fa-file-pdf"></i>
                                    </button>
                                    <div style="display: none; position: absolute; right: 0; top: 100%; background: #1F2937; border: 1px solid var(--border); border-radius: 8px; z-index: 100; box-shadow: 0 10px 15px rgba(0,0,0,0.5); min-width: 180px; text-align: left; margin-top: 4px;">
                                        <a href="{{ route('school.students.id-card', $student->id) }}" target="_blank" style="display: block; padding: 0.6rem 1rem; color: #FFF; text-decoration: none; font-size: 0.85rem;" class="doc-link">ID Card (A5)</a>
                                        <a href="{{ route('school.students.admit-card', $student->id) }}" target="_blank" style="display: block; padding: 0.6rem 1rem; color: #FFF; text-decoration: none; font-size: 0.85rem;" class="doc-link">Admit Card</a>
                                        <div style="border-top: 1px solid var(--border);"></div>
                                        <a href="{{ route('school.students.certificate', [$student->id, 'character']) }}" target="_blank" style="display: block; padding: 0.6rem 1rem; color: #FFF; text-decoration: none; font-size: 0.85rem;" class="doc-link">Character Cert</a>
                                        <a href="{{ route('school.students.certificate', [$student->id, 'dob']) }}" target="_blank" style="display: block; padding: 0.6rem 1rem; color: #FFF; text-decoration: none; font-size: 0.85rem;" class="doc-link">DOB Cert</a>
                                        <a href="{{ route('school.students.certificate', [$student->id, 'bonafide']) }}" target="_blank" style="display: block; padding: 0.6rem 1rem; color: #FFF; text-decoration: none; font-size: 0.85rem;" class="doc-link">Bonafide Cert</a>
                                        <a href="{{ route('school.students.certificate', [$student->id, 'transfer']) }}" target="_blank" style="display: block; padding: 0.6rem 1rem; color: #FFF; text-decoration: none; font-size: 0.85rem;" class="doc-link">Transfer Cert</a>
                                    </div>
                                </div>

                                <form action="{{ route('school.students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this student?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-accent" style="padding: 0.4rem 0.6rem; font-size: 0.8rem; background-color: var(--danger);" title="Soft Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; color: var(--text-muted); padding: 3rem;">No students found matching your filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination links -->
    <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
        {{ $students->appends(request()->all())->links() }}
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Close dropdowns when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('div').find('.btn-accent').length) {
            $('.btn-accent').next().hide();
        }
    });

    // Handle AJAX bulk import submission
    $('#bulkImportForm').on('submit', function(e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        let feedback = $('#importFeedback');
        
        feedback.show().html('<span style="color: var(--warning);"><i class="fa fa-spinner fa-spin"></i> Processing spreadsheet...</span>');

        $.ajax({
            url: "{{ route('school.students.import') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    feedback.html('<span style="color: var(--success);"><i class="fa fa-check-circle"></i> Import started. Job ID: ' + response.import_log_id + '. Processing row imports in background. Please refresh page shortly.</span>');
                } else {
                    feedback.html('<span style="color: var(--danger);"><i class="fa fa-exclamation-circle"></i> ' + response.message + '</span>');
                }
            },
            error: function(xhr) {
                feedback.html('<span style="color: var(--danger);"><i class="fa fa-exclamation-circle"></i> Error parsing sheet or invalid request fields.</span>');
            }
        });
    });
</script>
<style>
    .doc-link:hover {
        background-color: var(--accent) !important;
    }
</style>
@endsection
