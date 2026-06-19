@extends('layouts.app')

@section('page-title', 'Admission Configurations')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-gears" style="color:var(--gold);margin-right:8px;"></i>Admission Configuration settings</h1>
        <p>Pre-configure pricing policies, online application forms limits, and active intake date sheets</p>
    </div>
</div>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-hdr">
        <h3>General Configuration Rules</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('school.admissions.settings') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Online Application Registration Fee (INR)</label>
                <input type="number" class="form-control" name="app_fee" value="1000" required>
            </div>
            <div class="form-group">
                <label class="form-label">Maximum Intake Limit (Total Seats)</label>
                <input type="number" class="form-control" name="max_seats" value="250" required>
            </div>
            <div class="form-group">
                <label class="form-label">Active Admission Session</label>
                <select class="form-control" name="session">
                    <option value="2026_27">2026 - 27 (Current Incoming Intake)</option>
                    <option value="2027_28">2027 - 28 (Future Admissions)</option>
                </select>
            </div>
            <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center;">
                <i class="fas fa-save"></i> Save Configurations
            </button>
        </form>
    </div>
</div>
@endsection
