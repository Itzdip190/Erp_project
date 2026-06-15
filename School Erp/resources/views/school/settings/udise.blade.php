@extends('layouts.app')

@section('title', 'UDISE')
@section('page-title', 'UDISE Info')

@section('content')
<div class="card" style="max-width:900px; margin:0 auto;">
    <div class="card-hdr" style="display:flex; justify-content:space-between; align-items:center;">
        <h3>UDISE (Unified District Information System for Education) Data Form</h3>
        <span style="font-size:11px; color:var(--t2);">Academic Session: <strong>{{ now()->format('Y') }}</strong></span>
    </div>
    <div class="card-body">
        <p style="font-size:13px; color:var(--t2); margin-bottom:20px;">
            Fill in the standard UDISE+ details for official government data reporting.
        </p>

        <form method="POST" action="{{ route('school.settings.udise.update') }}">
            @csrf
            @method('PUT')

            <!-- Section 1: School Profile -->
            <h4 style="font-size:13px; font-weight:700; text-transform:uppercase; color:var(--navy); border-bottom:1px solid var(--border); padding-bottom:6px; margin-bottom:16px;">
                1. School Profile & Registration
            </h4>
            <div class="grid-3">
                <div class="form-group">
                    <label class="form-label">UDISE School Code</label>
                    <input type="text" name="udise_code" class="form-control" value="{{ $udise['udise_code'] ?? '' }}" placeholder="11-digit code" maxlength="11">
                </div>
                <div class="form-group">
                    <label class="form-label">School Category</label>
                    <select name="school_category" class="form-control">
                        <option value="">Select Category</option>
                        <option value="primary" {{ ($udise['school_category'] ?? '') === 'primary' ? 'selected' : '' }}>Primary Only (1-5)</option>
                        <option value="upper_primary" {{ ($udise['school_category'] ?? '') === 'upper_primary' ? 'selected' : '' }}>Upper Primary (1-8)</option>
                        <option value="secondary" {{ ($udise['school_category'] ?? '') === 'secondary' ? 'selected' : '' }}>Secondary (1-10)</option>
                        <option value="higher_secondary" {{ ($udise['school_category'] ?? '') === 'higher_secondary' ? 'selected' : '' }}>Higher Secondary (1-12)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">School Management Type</label>
                    <select name="management_type" class="form-control">
                        <option value="">Select Management</option>
                        <option value="govt" {{ ($udise['management_type'] ?? '') === 'govt' ? 'selected' : '' }}>Department of Education (Government)</option>
                        <option value="aided" {{ ($udise['management_type'] ?? '') === 'aided' ? 'selected' : '' }}>Govt. Aided</option>
                        <option value="private" {{ ($udise['management_type'] ?? '') === 'private' ? 'selected' : '' }}>Private Unaided</option>
                    </select>
                </div>
            </div>

            <!-- Section 2: Infrastructure Details -->
            <h4 style="font-size:13px; font-weight:700; text-transform:uppercase; color:var(--navy); border-bottom:1px solid var(--border); padding-bottom:6px; margin-top:20px; margin-bottom:16px;">
                2. Infrastructure & Facilities
            </h4>
            <div class="grid-3">
                <div class="form-group">
                    <label class="form-label">Total Classrooms</label>
                    <input type="number" name="classrooms_count" class="form-control" value="{{ $udise['classrooms_count'] ?? '' }}" min="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Internet Connection?</label>
                    <select name="internet_available" class="form-control">
                        <option value="no" {{ ($udise['internet_available'] ?? '') === 'no' ? 'selected' : '' }}>No</option>
                        <option value="yes" {{ ($udise['internet_available'] ?? '') === 'yes' ? 'selected' : '' }}>Yes</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Electricity Connection?</label>
                    <select name="electricity_available" class="form-control">
                        <option value="no" {{ ($udise['electricity_available'] ?? '') === 'no' ? 'selected' : '' }}>No</option>
                        <option value="yes" {{ ($udise['electricity_available'] ?? '') === 'yes' ? 'selected' : '' }}>Yes</option>
                    </select>
                </div>
            </div>

            <!-- Section 3: Summary Statistics -->
            <h4 style="font-size:13px; font-weight:700; text-transform:uppercase; color:var(--navy); border-bottom:1px solid var(--border); padding-bottom:6px; margin-top:20px; margin-bottom:16px;">
                3. Summary Demographics (Current Auto-calculated)
            </h4>
            <div class="grid-2" style="background:#f8fafc; border:1px solid var(--border); border-radius:8px; padding:16px; margin-bottom:20px;">
                <div>
                    <span style="font-size:12px; color:var(--t2);">Total Admitted Students:</span>
                    <strong style="font-size:16px; color:var(--t1); display:block; margin-top:2px;">{{ $totalStudents }}</strong>
                </div>
                <div>
                    <span style="font-size:12px; color:var(--t2);">Total Active Staff:</span>
                    <strong style="font-size:16px; color:var(--t1); display:block; margin-top:2px;">{{ $totalStaff }}</strong>
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:8px; border-top:1px solid var(--border); padding-top:16px;">
                <a href="{{ route('school.dashboard') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">Save UDISE Details</button>
            </div>
        </form>
    </div>
</div>
@endsection
