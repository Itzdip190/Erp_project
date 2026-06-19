@extends('layouts.app')

@section('page-title', 'Student Report Card v2')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-file-contract" style="color:var(--gold);margin-right:8px;"></i>Student Report Card Generator v2</h1>
        <p>Premium modern glassmorphism styled report sheet templates</p>
    </div>
</div>

<div class="card">
    <div class="card-hdr">
        <h3>Select Student</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('school.examination.report-card-v2') }}" style="display:flex; gap:12px; align-items:end;">
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
            <button type="submit" class="btn btn-primary"><i class="fas fa-eye"></i> View Premium Layout</button>
        </form>
    </div>
</div>

@if($student)
<div class="card" style="max-width: 800px; margin:0 auto; padding:40px; border-radius: 20px; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color:#fff; box-shadow: 0 15px 35px rgba(0,0,0,0.3);" id="printableCard">
    <div style="text-align:center; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:20px; margin-bottom:25px;">
        <h2 style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--gold); font-weight:800; font-size:22px; letter-spacing:1px;">SCHOOLCLOUD PREPARATORY</h2>
        <p style="font-size:11px; color:#94a3b8; text-transform:uppercase; letter-spacing:2px; margin-top:6px;">Performance Transcript of Academic Record</p>
    </div>

    <!-- Student Details Cards -->
    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:30px; background: rgba(255,255,255,0.05); padding:20px; border-radius:12px; border: 1px solid rgba(255,255,255,0.08);">
        <div><span style="color:#94a3b8; font-size:11px; display:block; text-transform:uppercase; font-weight:600;">Student Name</span> <strong style="font-size:14px;">{{ $student->full_name }}</strong></div>
        <div><span style="color:#94a3b8; font-size:11px; display:block; text-transform:uppercase; font-weight:600;">Admission ID</span> <strong style="font-size:14px;">{{ $student->admission_number }}</strong></div>
        <div><span style="color:#94a3b8; font-size:11px; display:block; text-transform:uppercase; font-weight:600;">Class Grade</span> <strong style="font-size:14px;">{{ $student->class?->name ?? 'N/A' }} – {{ $student->section?->name ?? 'N/A' }}</strong></div>
        <div><span style="color:#94a3b8; font-size:11px; display:block; text-transform:uppercase; font-weight:600;">Academic Term</span> <strong style="font-size:14px;">Annual Evaluation</strong></div>
    </div>

    <!-- Marks Grid -->
    <div style="display:flex; flex-direction:column; gap:12px; margin-bottom:40px;">
        @forelse($marks as $m)
        <div style="display:flex; justify-content:space-between; align-items:center; padding:15px 20px; background: rgba(255,255,255,0.03); border-radius:10px; border: 1px solid rgba(255,255,255,0.05);">
            <div>
                <strong style="font-size:13.5px; display:block;">{{ $m->subject?->name ?? 'N/A' }}</strong>
                <span style="font-size:11px; color:#94a3b8;">Exam: {{ $m->exam_name }}</span>
            </div>
            <div style="text-align:right;">
                <span style="font-size:15px; font-weight:800; color:var(--gold);">{{ $m->marks_obtained }} / {{ $m->max_marks }}</span>
                <span style="font-size:11px; display:block; color:#10b981; font-weight:700;">Grade: {{ $m->grade ?? 'A' }}</span>
            </div>
        </div>
        @empty
        <p style="text-align:center; color:#94a3b8; padding:20px;">No marks recorded.</p>
        @endforelse
    </div>

    <!-- Signatures -->
    <div style="display:flex; justify-content:space-between; margin-top:50px; border-top:1px solid rgba(255,255,255,0.1); padding-top:25px; font-size:11.5px; color:#94a3b8;">
        <div style="text-align:center;">
            <div style="width:140px; border-bottom:1px solid rgba(255,255,255,0.2); margin-bottom:8px;"></div>
            <span>Academic Advisor</span>
        </div>
        <div style="text-align:center;">
            <div style="width:140px; border-bottom:1px solid rgba(255,255,255,0.2); margin-bottom:8px;"></div>
            <span>Dean of School</span>
        </div>
    </div>
</div>

<div style="text-align:center; margin-top:20px;">
    <button onclick="window.print()" class="btn btn-gold"><i class="fas fa-print"></i> Print Transcript</button>
</div>
@endif
@endsection
