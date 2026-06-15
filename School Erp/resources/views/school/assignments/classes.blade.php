@extends('layouts.app')

@section('title', 'Manage Classes & Sections')
@section('page-title', 'Manage Classes & Sections')

@section('content')
<div class="grid-2" style="margin-bottom:20px; align-items: flex-start;">
    <!-- Add Class Form -->
    <div class="card">
        <div class="card-hdr">
            <h3>Add New Class</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('school.assignments.classes.store') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Class Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g., Class 10, Grade A" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Numeric Name (Optional)</label>
                    <input type="number" name="numeric_name" class="form-control" placeholder="e.g., 10">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;"><i class="fas fa-plus"></i> Create Class</button>
            </form>
        </div>
    </div>

    <!-- Add Section Form -->
    <div class="card">
        <div class="card-hdr">
            <h3>Add New Section</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('school.assignments.sections.store') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Section Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g., A, B, Rose" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Select Class</label>
                    <select name="class_id" class="form-control" required>
                        <option value="">Choose Class...</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Class Teacher (Optional)</label>
                    <select name="class_teacher_id" class="form-control">
                        <option value="">No Teacher</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->full_name }} ({{ $teacher->employee_id }})</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-gold" style="width:100%;"><i class="fas fa-plus"></i> Create Section</button>
            </form>
        </div>
    </div>
</div>

<!-- List of Classes and Sections -->
<div class="card">
    <div class="card-hdr">
        <h3>Current Classes & Sections</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Class Name</th>
                        <th>Numeric Code</th>
                        <th>Sections</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $class)
                    <tr>
                        <td><strong>{{ $class->name }}</strong></td>
                        <td>{{ $class->numeric_name ?? '—' }}</td>
                        <td>
                            <div style="display:flex; flex-wrap:wrap; gap:8px;">
                                @forelse($class->sections as $sec)
                                    <span class="badge badge-blue" style="display:inline-flex; align-items:center; gap:6px; padding:6px 10px;">
                                        {{ $sec->name }}
                                        <form method="POST" action="{{ route('school.assignments.sections.destroy', $sec->id) }}" onsubmit="return confirm('Delete section {{ $sec->name }}?')" style="display:inline; margin-left:4px;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background:none; border:none; color:var(--red); cursor:pointer; font-size:10px; padding:0 2px;"><i class="fas fa-times"></i></button>
                                        </form>
                                    </span>
                                @empty
                                    <span style="font-size:11px; color:var(--t3);">No sections defined.</span>
                                @endforelse
                            </div>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('school.assignments.classes.destroy', $class->id) }}" onsubmit="return confirm('Warning: Deleting this class will delete all its sections and subjects! Proceed?')" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" style="padding:4px 8px; font-size:11px;">
                                    <i class="fas fa-trash"></i> Delete Class
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center; padding:24px; color:var(--t2);">No classes or sections configured.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
