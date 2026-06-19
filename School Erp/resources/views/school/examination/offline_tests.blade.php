@extends('layouts.app')

@section('page-title', 'Offline Exam scheduling')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-calendar-check" style="color:var(--gold);margin-right:8px;"></i>Offline written tests & Date Sheets</h1>
        <p>Prepare timetable schedules and exam halls mappings for pen-and-paper assessments</p>
    </div>
</div>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-hdr">
        <h3>Create Exam Datesheet</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('school.examination.offline-tests') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Exam category</label>
                <select class="form-control" name="exam_cat">
                    <option value="half_yearly">Half Yearly Exams (Term 1)</option>
                    <option value="finals">Final Board Exams (Term 2)</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Subject</label>
                <input type="text" class="form-control" name="subject" required placeholder="e.g. Mathematics">
            </div>
            <div class="form-group">
                <label class="form-label">Exam Date</label>
                <input type="date" class="form-control" name="exam_date" required>
            </div>
            <div class="form-group">
                <label class="form-label">Exam Time Window</label>
                <input type="text" class="form-control" name="time" required placeholder="e.g. 09:30 AM - 12:30 PM">
            </div>
            <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center;">
                <i class="fas fa-calendar-plus"></i> Schedule exam slot
            </button>
        </form>
    </div>
</div>
@endsection
