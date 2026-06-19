@extends('layouts.app')

@section('page-title', 'Enroll Students')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-check-double" style="color:var(--gold);margin-right:8px;"></i>Final Seat Booking & Admission</h1>
        <p>Convert approved enquiry leads into active class student entries</p>
    </div>
</div>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-hdr">
        <h3>Seat Allocation & Registration</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('school.admissions.admission') }}">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Select Prospect Lead</label>
                <select name="lead_id" class="form-control" required>
                    <option value="">-- Select Prospect Candidate --</option>
                    @foreach($leads as $lead)
                        <option value="{{ $lead->id }}">{{ $lead->student_name }} (Parent: {{ $lead->parent_name }} | Contact: {{ $lead->phone }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Assign Class</label>
                <select name="class_id" class="form-control" required>
                    <option value="">-- Select Class --</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; padding:12px;">
                <i class="fas fa-user-plus"></i> Complete Admission & Enroll
            </button>
        </form>
    </div>
</div>
@endsection
