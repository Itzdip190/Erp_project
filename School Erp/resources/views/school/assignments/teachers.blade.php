@extends('layouts.app')

@section('title', 'Teacher Assignments')
@section('page-title', 'Teacher Assignments')

@section('content')
<div class="grid-2" style="margin-bottom:20px; align-items: flex-start;">
    <!-- Assign Teacher to Subject Form -->
    <div class="card">
        <div class="card-hdr">
            <h3>Assign Teacher to Subject & Section</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('school.assignments.teachers.store') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Select Section</label>
                    <select name="section_id" class="form-control" required>
                        <option value="">Choose Section...</option>
                        @foreach($sections as $sec)
                            <option value="{{ $sec->id }}">{{ optional($sec->schoolClass)->name ?? 'Class' }} — {{ $sec->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Select Subject</label>
                    <select name="subject_id" class="form-control" required>
                        <option value="">Choose Subject...</option>
                        @foreach($subjects as $sub)
                            <option value="{{ $sub->id }}">{{ optional($sub->schoolClass)->name ?? 'Class' }} — {{ $sub->name }} ({{ $sub->code ?? 'N/A' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Select Teacher (Staff)</label>
                    <select name="staff_id" class="form-control" required>
                        <option value="">Choose Teacher...</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->full_name }} ({{ $teacher->employee_id }})</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;"><i class="fas fa-link"></i> Assign Subject Teacher</button>
            </form>
        </div>
    </div>

    <!-- Class Teacher Assignment Form -->
    <div class="card">
        <div class="card-hdr">
            <h3>Set Class Teacher</h3>
        </div>
        <div class="card-body">
            <div style="font-size: 12px; color: var(--t2); margin-bottom: 12px;">
                Assign a dedicated Class Teacher who oversees general attendance, reports, and administrative tasks for each section.
            </div>
            <table class="tbl" style="margin-bottom:12px; font-size:12px;">
                <thead>
                    <tr>
                        <th>Class & Section</th>
                        <th>Class Teacher</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sections as $sec)
                    <tr>
                        <td><strong>{{ optional($sec->schoolClass)->name ?? 'Class' }} — {{ $sec->name }}</strong></td>
                        <td>
                            <form method="POST" action="{{ route('school.assignments.class-teacher.update', $sec->id) }}" id="form-ct-{{ $sec->id }}">
                                @csrf
                                <select name="class_teacher_id" class="form-control" style="padding:4px 8px; font-size:12px;" onchange="document.getElementById('form-ct-{{ $sec->id }}').submit()">
                                    <option value="">No Teacher</option>
                                    @foreach($teachers as $t)
                                        <option value="{{ $t->id }}" {{ $sec->class_teacher_id == $t->id ? 'selected' : '' }}>{{ $t->full_name }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td>
                            <span style="font-size:11px; color:var(--t3);">Auto-saves on change</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- List of Subject Teacher Assignments -->
<div class="card">
    <div class="card-hdr">
        <h3>Subject & Classroom Teacher Assignments</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Class & Section</th>
                        <th>Subject Name</th>
                        <th>Teacher Name</th>
                        <th>Employee ID</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $assign)
                    <tr>
                        <td><strong>{{ optional($assign->section?->schoolClass)->name ?? 'N/A' }} — {{ optional($assign->section)->name ?? 'N/A' }}</strong></td>
                        <td>{{ optional($assign->subject)->name ?? 'N/A' }} ({{ optional($assign->subject)->code ?? '—' }})</td>
                        <td><strong>{{ optional($assign->staff)->full_name ?? 'N/A' }}</strong></td>
                        <td><code style="color:var(--gold); font-weight:700;">{{ optional($assign->staff)->employee_id ?? '—' }}</code></td>
                        <td>
                            <form method="POST" action="{{ route('school.assignments.teachers.destroy', $assign->id) }}" onsubmit="return confirm('Remove this teacher assignment?')" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" style="padding:4px 8px; font-size:11px;">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center; padding:24px; color:var(--t2);">No subject teacher assignments configured.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
