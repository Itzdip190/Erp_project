@extends('layouts.app')

@section('title', 'Child Profile Details')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2 style="font-family: 'Syne', sans-serif;">{{ $student->full_name }}'s Academic Profile</h2>
    <a href="{{ route('parent.dashboard') }}" class="btn-accent" style="background-color: #4B5563;">
        <i class="fa fa-arrow-left"></i> Back to Dashboard
    </a>
</div>

<div style="display: flex; gap: 2rem; align-items: flex-start; flex-wrap: wrap;">
    <!-- Profile Card Left -->
    <div class="glass-card" style="flex: 0.8; min-width: 280px; text-align: center;">
        <div style="width: 130px; height: 130px; border-radius: 50%; background-image: url('{{ $student->photo_url }}'); background-size: cover; background-position: center; margin: 0 auto 1.5rem; border: 3px solid var(--accent); box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
            @if(!$student->photo)
                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background-color: var(--primary-light); color: var(--text-muted); border-radius: 50%;">
                    <i class="fa fa-user" style="font-size: 3.5rem;"></i>
                </div>
            @endif
        </div>
        
        <h3 style="font-family: 'Syne', sans-serif; font-size: 1.3rem; margin-bottom: 0.25rem;">{{ $student->full_name }}</h3>
        <span class="badge badge-success" style="margin-bottom: 1.5rem;">{{ $student->admission_number }}</span>
        
        <div style="border-top: 1px solid var(--border); padding-top: 1.5rem; text-align: left; display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.9rem;">
            <div><strong style="color: var(--text-muted);">Class / Section:</strong> {{ $student->class?->name }} - {{ $student->section?->name }}</div>
            <div><strong style="color: var(--text-muted);">Roll Number:</strong> {{ $student->roll_number ?? 'N/A' }}</div>
            <div><strong style="color: var(--text-muted);">Session:</strong> {{ $student->academicSession?->name }}</div>
            <div><strong style="color: var(--text-muted);">Gender:</strong> {{ ucfirst($student->gender) }}</div>
            <div><strong style="color: var(--text-muted);">Age:</strong> {{ $student->age }} yrs</div>
        </div>
    </div>

    <!-- Details Grid Right -->
    <div class="glass-card" style="flex: 2; min-width: 320px;">
        <h3 style="font-family: 'Syne', sans-serif; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; color: var(--accent);">Student Information</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
            <div><span style="display:block; color: var(--text-muted); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Date of Birth</span> {{ $student->date_of_birth->format('d M Y') }}</div>
            <div><span style="display:block; color: var(--text-muted); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Blood Group</span> {{ $student->blood_group ?? 'N/A' }}</div>
            <div><span style="display:block; color: var(--text-muted); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Religion</span> {{ $student->religion ?? 'N/A' }}</div>
            <div><span style="display:block; color: var(--text-muted); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Caste</span> {{ $student->caste ?? 'N/A' }}</div>
            <div><span style="display:block; color: var(--text-muted); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Admission Date</span> {{ $student->admission_date->format('d M Y') }}</div>
        </div>

        <h3 style="font-family: 'Syne', sans-serif; margin-bottom: 1.5rem; border-top: 1px solid var(--border); padding-top: 1.5rem; color: var(--accent);">Guardian & Location</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div><span style="display:block; color: var(--text-muted); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Primary Guardian</span> {{ $student->guardian_name }}</div>
            <div><span style="display:block; color: var(--text-muted); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Relationship</span> {{ ucfirst($student->guardian_relationship) }}</div>
            <div><span style="display:block; color: var(--text-muted); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Phone Number</span> {{ $student->guardian_phone }}</div>
            <div><span style="display:block; color: var(--text-muted); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Email Address</span> {{ $student->guardian_email ?? 'N/A' }}</div>
        </div>
        
        <div style="margin-top: 1.5rem;">
            <span style="display:block; color: var(--text-muted); font-size: 0.8rem; font-weight: 700; text-transform: uppercase; margin-bottom: 0.5rem;">Residential Address</span>
            <p style="line-height: 1.6;">
                {{ $student->address }}<br>
                {{ $student->city }}, {{ $student->state }} - {{ $student->pincode }}
            </p>
        </div>
    </div>
</div>
@endsection
