@extends('layouts.app')

@section('page-title', 'Student Optional Subject Allocation')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-book-open" style="color:var(--gold);margin-right:8px;"></i>Optional Subject Allocation</h1>
        <p>Assign optional subjects (languages, electives) to students in bulk</p>
    </div>
</div>

<div class="card">
    <div class="card-hdr">
        <h3>Filter Class & Section</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('school.student-mgmt.optional-subject') }}">
            <div class="grid-3" style="align-items:end;">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Class</label>
                    <select name="class_id" class="form-control" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Section</label>
                    <select name="section_id" class="form-control" required>
                        <option value="">Select Section</option>
                        @foreach($sections as $s)
                            <option value="{{ $s->id }}" {{ $sectionId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <button type="submit" class="btn btn-gold" style="width:100%;"><i class="fas fa-search"></i> Find Students</button>
                </div>
            </div>
        </form>
    </div>
</div>

@if($classId && $sectionId)
<div class="card">
    <div class="card-hdr">
        <h3>Students List & Elective Mapping</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <form method="POST" action="#">
            @csrf
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Admission ID</th>
                        <th>Student Name</th>
                        <th>Roll No</th>
                        <th>Assign Optional Subject</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $st)
                        <tr>
                            <td><span class="badge badge-blue">{{ $st->admission_number }}</span></td>
                            <td style="font-weight:700;">{{ $st->full_name }}</td>
                            <td>{{ $st->roll_number }}</td>
                            <td>
                                <select name="optional_subject[{{ $st->id }}]" class="form-control" style="max-width:240px;">
                                    <option value="">None / Not Applicable</option>
                                    @foreach($subjects as $sub)
                                        <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center; padding:30px; color:var(--t3);">No students registered in this section.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($students->isNotEmpty())
                <div style="padding:20px; text-align:right; border-top:1px solid var(--border);">
                    <button type="submit" class="btn btn-gold"><i class="fas fa-save"></i> Save Subject Allocations</button>
                </div>
            @endif
        </form>
    </div>
</div>
@endif
@endsection
