@extends('layouts.app')

@section('page-title', 'LMS Tests Integration')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-network-wired" style="color:var(--gold);margin-right:8px;"></i>LMS Linked Tests & Assignments</h1>
        <p>Integrate online mock assessments and digital quiz banks with class scoring ledgers</p>
    </div>
</div>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-hdr">
        <h3>Link LMS Quiz</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('school.examination.lms-tests') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Select LMS Course</label>
                <select class="form-control" name="lms_course">
                    <option value="math_10">Grade 10 - Algebra Chapter 1</option>
                    <option value="sci_10">Grade 10 - Physics Mechanics Quiz</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Mapping Weightage in term exam (%)</label>
                <input type="number" class="form-control" name="weightage" value="20" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
                <i class="fas fa-link"></i> Link LMS quiz grades
            </button>
        </form>
    </div>
</div>
@endsection
