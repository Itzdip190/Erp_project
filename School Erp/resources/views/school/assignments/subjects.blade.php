@extends('layouts.app')

@section('title', 'Manage Subjects')
@section('page-title', 'Manage Subjects')

@section('content')
<div class="card" style="margin-bottom:20px;">
    <div class="card-hdr">
        <h3>Create New Subject</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('school.assignments.subjects.store') }}" class="grid-3" style="align-items: flex-end;">
            @csrf
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Subject Name</label>
                <input type="text" name="name" class="form-control" placeholder="e.g., Mathematics, English Literature" required>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Subject Code</label>
                <input type="text" name="code" class="form-control" placeholder="e.g., MATH-101">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Type</label>
                <select name="type" class="form-control" required>
                    <option value="Theory">Theory</option>
                    <option value="Practical">Practical</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Class</label>
                <select name="class_id" class="form-control" required>
                    <option value="">Choose Class...</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Max Marks</label>
                <input type="number" name="max_marks" class="form-control" placeholder="e.g., 100" value="100" required>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Pass Marks</label>
                <input type="number" name="pass_marks" class="form-control" placeholder="e.g., 33" value="33" required>
            </div>
            <div class="form-group" style="margin-bottom:0; grid-column:span 3; text-align:right;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Create Subject</button>
            </div>
        </form>
    </div>
</div>

<!-- List of Subjects -->
<div class="card">
    <div class="card-hdr">
        <h3>Subjects Catalog</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Subject Name</th>
                        <th>Code</th>
                        <th>Type</th>
                        <th>Class Name</th>
                        <th>Max / Pass Marks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $sub)
                    <tr>
                        <td><strong>{{ $sub->name }}</strong></td>
                        <td><code style="color:var(--gold); font-weight:700;">{{ $sub->code ?? '—' }}</code></td>
                        <td>
                            <span class="badge {{ $sub->type == 'Theory' ? 'badge-blue' : 'badge-purple' }}">
                                {{ $sub->type }}
                            </span>
                        </td>
                        <td>{{ optional($sub->schoolClass)->name ?? 'N/A' }}</td>
                        <td>
                            <span style="font-weight:600;">{{ $sub->max_marks }}</span> / <span style="color:var(--t2);">{{ $sub->pass_marks }}</span>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('school.assignments.subjects.destroy', $sub->id) }}" onsubmit="return confirm('Are you sure you want to delete this subject?')" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" style="padding:4px 8px; font-size:11px;">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding:24px; color:var(--t2);">No subjects configured yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
