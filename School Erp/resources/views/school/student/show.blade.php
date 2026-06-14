@extends('layouts.app')

@section('title', 'Student Profile')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2 style="font-family: 'Syne', sans-serif;">Profile 360°</h2>
    <div style="display: flex; gap: 1rem;">
        <a href="{{ route('school.students.edit', $student->id) }}" class="btn-accent" style="background-color: var(--warning);">
            <i class="fa fa-edit"></i> Edit Record
        </a>
        <a href="{{ route('school.students.index') }}" class="btn-accent" style="background-color: #4B5563;">
            <i class="fa fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<div style="display: flex; gap: 2rem; align-items: flex-start; flex-wrap: wrap;">
    <!-- Profile Card -->
    <div class="glass-card" style="flex: 0.8; min-width: 280px; text-align: center;">
        <div style="width: 140px; height: 140px; border-radius: 50%; background-image: url('{{ $student->photo_url }}'); background-size: cover; background-position: center; margin: 0 auto 1.5rem; border: 3px solid var(--accent); box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
            @if(!$student->photo)
                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background-color: var(--primary-light); color: var(--text-muted); border-radius: 50%;">
                    <i class="fa fa-user" style="font-size: 4rem;"></i>
                </div>
            @endif
        </div>
        
        <h3 style="font-family: 'Syne', sans-serif; font-size: 1.4rem; margin-bottom: 0.25rem;">{{ $student->full_name }}</h3>
        <span class="badge badge-success" style="margin-bottom: 1.5rem;">{{ $student->admission_number }}</span>
        
        <div style="border-top: 1px solid var(--border); padding-top: 1.5rem; text-align: left; display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.9rem;">
            <div><strong style="color: var(--text-muted);">Class / Sec:</strong> {{ $student->class?->name }} - {{ $student->section?->name }}</div>
            <div><strong style="color: var(--text-muted);">Roll Number:</strong> {{ $student->roll_number ?? 'N/A' }}</div>
            <div><strong style="color: var(--text-muted);">Gender:</strong> {{ ucfirst($student->gender) }}</div>
            <div><strong style="color: var(--text-muted);">Age:</strong> {{ $student->age }} yrs</div>
        </div>
    </div>

    <!-- Tabbed details wrapper -->
    <div style="flex: 2.2; min-width: 320px; width: 100%;">
        <!-- Tabs selection bar -->
        <div style="display: flex; gap: 1rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; margin-bottom: 1.5rem;">
            <button class="tab-trigger active" onclick="switchTab('general')" style="background: transparent; border: none; font-size: 1rem; font-weight: 700; color: var(--accent); border-bottom: 3px solid var(--accent); padding: 0.5rem 1rem; cursor: pointer;">General Info</button>
            <button class="tab-trigger" onclick="switchTab('guardian')" style="background: transparent; border: none; font-size: 1rem; font-weight: 700; color: var(--text-muted); padding: 0.5rem 1rem; cursor: pointer;">Guardian</button>
            @if(config('modules.fees_enabled'))
                <button class="tab-trigger" onclick="switchTab('fees')" style="background: transparent; border: none; font-size: 1rem; font-weight: 700; color: var(--text-muted); padding: 0.5rem 1rem; cursor: pointer;">Fees Mapping</button>
            @endif
        </div>

        <!-- TAB CONTENT: General -->
        <div id="tab-general" class="tab-content glass-card">
            <h3 style="font-family: 'Syne', sans-serif; margin-bottom: 1.5rem; color: var(--accent);">Student Personal Profile</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div><span style="display:block; color: var(--text-muted); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Date of Birth</span> {{ $student->date_of_birth->format('d M Y') }}</div>
                <div><span style="display:block; color: var(--text-muted); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Blood Group</span> {{ $student->blood_group ?? 'N/A' }}</div>
                <div><span style="display:block; color: var(--text-muted); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Religion</span> {{ $student->religion ?? 'N/A' }}</div>
                <div><span style="display:block; color: var(--text-muted); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Caste</span> {{ $student->caste ?? 'N/A' }}</div>
                <div><span style="display:block; color: var(--text-muted); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Admission Date</span> {{ $student->admission_date->format('d M Y') }}</div>
                <div><span style="display:block; color: var(--text-muted); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Status</span> {{ $student->is_active ? 'Active Enrolled' : 'Suspended' }}</div>
            </div>
        </div>

        <!-- TAB CONTENT: Guardian -->
        <div id="tab-guardian" class="tab-content glass-card" style="display: none;">
            <h3 style="font-family: 'Syne', sans-serif; margin-bottom: 1.5rem; color: var(--accent);">Guardian Information</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                <div><span style="display:block; color: var(--text-muted); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Guardian Name</span> {{ $student->guardian_name }}</div>
                <div><span style="display:block; color: var(--text-muted); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Relationship</span> {{ ucfirst($student->guardian_relationship) }}</div>
                <div><span style="display:block; color: var(--text-muted); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Phone Number</span> {{ $student->guardian_phone }}</div>
                <div><span style="display:block; color: var(--text-muted); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Email Address</span> {{ $student->guardian_email ?? 'N/A' }}</div>
            </div>
            
            <h4 style="font-family: 'Syne', sans-serif; margin-bottom: 1rem; color: var(--text-muted); border-top: 1px solid var(--border); padding-top: 1.5rem;">Residential Address</h4>
            <p style="line-height: 1.6;">
                {{ $student->address }}<br>
                {{ $student->city }}, {{ $student->state }} - {{ $student->pincode }}
            </p>
        </div>

        <!-- TAB CONTENT: Fees -->
        @if(config('modules.fees_enabled'))
            <div id="tab-fees" class="tab-content glass-card" style="display: none;">
                <h3 style="font-family: 'Syne', sans-serif; margin-bottom: 1.5rem; color: var(--accent);">Financial Transactions</h3>
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <span style="display:block; color: var(--text-muted); font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Opening Balance Due</span>
                        <span style="font-size: 1.5rem; font-weight: 800; color: var(--danger);">${{ number_format($student->opening_due_balance, 2) }}</span>
                    </div>
                </div>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Fee collections and transaction details are locked until the Fees Management module (Phase 2) is enabled.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    function switchTab(tabId) {
        $('.tab-content').hide();
        $('#tab-' + tabId).show();

        $('.tab-trigger').css({
            'color': 'var(--text-muted)',
            'border-bottom': 'none'
        });

        // Set active styling
        let activeBtn = event.currentTarget;
        $(activeBtn).css({
            'color': 'var(--accent)',
            'border-bottom': '3px solid var(--accent)'
        });
    }
</script>
@endsection
