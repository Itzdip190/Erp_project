@extends('layouts.app')

@section('page-title', 'Marks Entry')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-edit" style="color:var(--gold);margin-right:8px;"></i>Marks Entry Panel</h1>
        <p>Record exam grades and scores for students in specific classes and subjects</p>
    </div>
</div>

<div class="card">
    <div class="card-hdr">
        <h3>Filter & Select Subject</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('school.examination.marks-entry') }}" style="display:grid; grid-template-columns: repeat(4, 1fr) auto; gap:12px; align-items:end;">
            <div class="form-group" style="margin:0;">
                <label class="form-label">Class</label>
                <select name="class_id" class="form-control" required>
                    <option value="">-- Select Class --</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin:0;">
                <label class="form-label">Section</label>
                <select name="section_id" class="form-control" required>
                    <option value="">-- Select Section --</option>
                    @foreach($sections as $s)
                        <option value="{{ $s->id }}" {{ $sectionId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin:0;">
                <label class="form-label">Subject</label>
                <select name="subject_id" class="form-control" required>
                    <option value="">-- Select Subject --</option>
                    @foreach($subjects as $sub)
                        <option value="{{ $sub->id }}" {{ $subjectId == $sub->id ? 'selected' : '' }}>{{ $sub->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin:0;">
                <label class="form-label">Exam Name</label>
                <select name="exam_name" class="form-control" required>
                    <option value="Unit Test 1" {{ $examName === 'Unit Test 1' ? 'selected' : '' }}>Unit Test 1</option>
                    <option value="Unit Test 2" {{ $examName === 'Unit Test 2' ? 'selected' : '' }}>Unit Test 2</option>
                    <option value="Term 1" {{ $examName === 'Term 1' ? 'selected' : '' }}>Term 1 (Half Yearly)</option>
                    <option value="Term 2" {{ $examName === 'Term 2' ? 'selected' : '' }}>Term 2 (Finals)</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Load Students</button>
        </form>
    </div>
</div>

@if($students->isNotEmpty())
<div class="card">
    <div class="card-hdr">
        <h3>Entering Scores for {{ $examName }}</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('school.examination.marks-entry') }}">
            @csrf
            <input type="hidden" name="subject_id" value="{{ $subjectId }}">
            <input type="hidden" name="exam_name" value="{{ $examName }}">

            <div class="table-wrap" style="margin-bottom:20px;">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Roll No.</th>
                            <th>Student Name</th>
                            <th>Marks Obtained</th>
                            <th>Max Marks</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $index => $stu)
                            @php $m = $marks->get($stu->id); @endphp
                            <tr>
                                <td>{{ $stu->roll_number ?? '—' }}</td>
                                <td>
                                    <strong>{{ $stu->full_name }}</strong>
                                    <input type="hidden" name="marks[{{ $index }}][student_id]" value="{{ $stu->id }}">
                                </td>
                                <td>
                                    <input type="number" step="0.1" class="form-control" name="marks[{{ $index }}][marks_obtained]" 
                                           value="{{ $m ? $m->marks_obtained : '' }}" required style="max-width:120px;">
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="marks[{{ $index }}][max_marks]" 
                                           value="{{ $m ? $m->max_marks : 100 }}" required style="max-width:120px;">
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="marks[{{ $index }}][remarks]" 
                                           value="{{ $m ? $m->remarks : '' }}" placeholder="remarks...">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center; padding:12px;">
                <i class="fas fa-check-circle"></i> Save Marks Entry Sheet
            </button>
        </form>
    </div>
</div>
@endif
@endsection
