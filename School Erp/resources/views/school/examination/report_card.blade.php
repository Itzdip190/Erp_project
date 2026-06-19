@extends('layouts.app')

@section('page-title', 'Student Report Card')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-file-contract" style="color:var(--gold);margin-right:8px;"></i>Student Report Card Generator</h1>
        <p>Review and generate official term assessment progress sheets for students</p>
    </div>
</div>

<div class="card">
    <div class="card-hdr">
        <h3>Select Student</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('school.examination.report-card') }}" style="display:flex; gap:12px; align-items:end;">
            <div class="form-group" style="margin:0; flex:1;">
                <label class="form-label">Student</label>
                <select name="student_id" class="form-control" required>
                    <option value="">-- Select Student --</option>
                    @foreach($students as $s)
                        <option value="{{ $s->id }}" {{ $selectedStudentId == $s->id ? 'selected' : '' }}>
                            {{ $s->full_name }} ({{ $s->admission_number }} — {{ $s->class?->name ?? 'N/A' }} {{ $s->section?->name ?? '' }})
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-file-pdf"></i> Generate Report Card</button>
        </form>
    </div>
</div>

@if($student)
<div class="card" style="max-width: 800px; margin:0 auto; padding:30px; border:2px solid #ddd; background:#fff;" id="printableCard">
    <div style="text-align:center; border-bottom:3px double var(--navy); padding-bottom:15px; margin-bottom:20px;">
        <h2 style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--navy); font-weight:800; font-size:20px;">SCHOOLCLOUD GLOBAL ACADEMY</h2>
        <p style="font-size:12px; color:var(--t2); text-transform:uppercase; letter-spacing:1px; margin-top:4px;">Official Student Progress Report Card</p>
        <span style="font-size:11px; background:var(--gold-bg); color:var(--gold); font-weight:700; padding:2px 8px; border-radius:10px; margin-top:8px; display:inline-block;">Session: {{ now()->year }}-{{ now()->year+1 }}</span>
    </div>

    <!-- Student details row -->
    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px; margin-bottom:20px; font-size:12.5px; color:var(--t1);">
        <div><strong>Student Name:</strong> {{ $student->full_name }}</div>
        <div><strong>Admission No:</strong> {{ $student->admission_number }}</div>
        <div><strong>Class / Section:</strong> {{ $student->class?->name ?? 'N/A' }} – {{ $student->section?->name ?? 'N/A' }}</div>
        <div><strong>Roll Number:</strong> {{ $student->roll_number ?? 'N/A' }}</div>
    </div>

    <!-- Marks Table -->
    <div class="table-wrap" style="margin-bottom:30px;">
        <table class="tbl" style="border:1px solid var(--border);">
            <thead>
                <tr style="background:var(--navy); color:#fff;">
                    <th style="color:#fff; background:var(--navy);">Subject</th>
                    <th style="color:#fff; background:var(--navy);">Exam</th>
                    <th style="color:#fff; background:var(--navy);">Obtained</th>
                    <th style="color:#fff; background:var(--navy);">Max Marks</th>
                    <th style="color:#fff; background:var(--navy);">Grade</th>
                    <th style="color:#fff; background:var(--navy);">Remarks</th>
                </tr>
            </thead>
            <tbody>
                @forelse($marks as $m)
                <tr>
                    <td><strong>{{ $m->subject?->name ?? 'N/A' }}</strong></td>
                    <td>{{ $m->exam_name }}</td>
                    <td>{{ $m->marks_obtained }}</td>
                    <td>{{ $m->max_marks }}</td>
                    <td><strong style="color:var(--gold);">{{ $m->grade ?? 'A' }}</strong></td>
                    <td><span style="font-size:11.5px; color:var(--t2);">{{ $m->remarks ?? 'Good' }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:20px; color:var(--t3);">No marks records found for this student.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Signatures -->
    <div style="display:flex; justify-content:space-between; margin-top:60px; border-top:1px solid #ddd; padding-top:20px; font-size:12px; color:var(--t2);">
        <div style="text-align:center;">
            <div style="width:150px; border-bottom:1px solid var(--t3); margin-bottom:5px;"></div>
            <span>Class Teacher Signature</span>
        </div>
        <div style="text-align:center;">
            <div style="width:150px; border-bottom:1px solid var(--t3); margin-bottom:5px;"></div>
            <span>Principal & Exam Controller</span>
        </div>
    </div>
</div>

<div style="text-align:center; margin-top:20px;">
    <button onclick="window.print()" class="btn btn-gold"><i class="fas fa-print"></i> Print Report Card</button>
</div>
@endif
@endsection
