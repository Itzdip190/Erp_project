@extends('layouts.app')

@section('title', 'Class Overview')
@section('page-title', 'Class Overview')

@section('content')
<div class="card" style="margin-bottom:20px;">
    <div class="card-hdr" style="display:flex; justify-content:space-between; align-items:center;">
        <h3>Class Overview & Directory</h3>
        <div style="display:flex; gap:8px;">
            <a href="{{ route('school.assignments.classes') }}" class="btn btn-outline"><i class="fas fa-plus"></i> Add Class/Section</a>
            <a href="{{ route('school.assignments.subjects') }}" class="btn btn-outline"><i class="fas fa-book"></i> Manage Subjects</a>
            <a href="{{ route('school.assignments.teachers') }}" class="btn btn-primary"><i class="fas fa-chalkboard-teacher"></i> Assign Teachers</a>
        </div>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Class Name</th>
                        <th>Sections & Class Teachers</th>
                        <th>Subjects Offered</th>
                        <th>Total Sections</th>
                        <th>Total Subjects</th>
                        <th>Total Students</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $class)
                    <tr>
                        <td><strong style="color:var(--navy); font-size:14px;">{{ $class->name }}</strong></td>
                        <td>
                            <div style="display:flex; flex-direction:column; gap:4px;">
                                @forelse($class->sections as $sec)
                                    <div style="font-size:12px;">
                                        <span class="badge badge-blue">{{ $sec->name }}</span>
                                        <span style="color:var(--t2); margin-left:4px;">
                                            Teacher: <strong>{{ optional($sec->classTeacher)->full_name ?? 'Not Assigned' }}</strong>
                                        </span>
                                    </div>
                                @empty
                                    <span style="font-size:11px; color:var(--t3);">No sections created</span>
                                @endforelse
                            </div>
                        </td>
                        <td>
                            <div style="display:flex; flex-wrap:wrap; gap:4px;">
                                @forelse($class->subjects as $sub)
                                    <span class="badge badge-warning" style="font-size:10px;">{{ $sub->name }} ({{ $sub->code ?? 'N/A' }})</span>
                                @empty
                                    <span style="font-size:11px; color:var(--t3);">No subjects created</span>
                                @endforelse
                            </div>
                        </td>
                        <td><span style="font-weight:600;">{{ $class->sections->count() }}</span></td>
                        <td><span style="font-weight:600;">{{ $class->subjects->count() }}</span></td>
                        <td>
                            <span class="badge badge-success" style="font-weight:700;">
                                {{ $class->sections->sum(function($sec) { return $sec->students->count(); }) }} Students
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding:24px; color:var(--t2);">No classes or academic structures defined in this school.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
