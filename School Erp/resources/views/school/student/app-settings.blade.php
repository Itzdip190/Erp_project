@extends('layouts.app')

@section('page-title', 'Student Info. Update Settings')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-toggle-on" style="color:var(--gold);margin-right:8px;"></i>Student Info. Update Settings</h1>
        <p>Configure which profile fields students and parents are allowed to edit from the mobile application</p>
    </div>
</div>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-hdr">
        <h3>App Update Authorization Policies</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('school.student-mgmt.app-settings') }}">
            @csrf
            
            <p style="color:var(--t2); font-size:12.5px; margin-bottom:20px; line-height:1.6;">
                Unchecking a field will lock it on the student and parent mobile apps. Any changes to locked fields must be made by school administrators.
            </p>

            <div style="display:flex; flex-direction:column; gap:16px; margin-bottom:30px;">
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-weight:700; color:var(--navy);">
                    <input type="checkbox" name="allow_photo" checked style="width:16px; height:16px;">
                    Allow Students to change Profile Photo
                </label>
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-weight:700; color:var(--navy);">
                    <input type="checkbox" name="allow_phone" checked style="width:16px; height:16px;">
                    Allow parents to edit Guardian Phone Number
                </label>
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-weight:700; color:var(--navy);">
                    <input type="checkbox" name="allow_email" checked style="width:16px; height:16px;">
                    Allow parents to edit Guardian Email Address
                </label>
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-weight:700; color:var(--navy);">
                    <input type="checkbox" name="allow_address" style="width:16px; height:16px;">
                    Allow editing Residential Address (Requires Admin approval)
                </label>
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-weight:700; color:var(--navy);">
                    <input type="checkbox" name="allow_blood" style="width:16px; height:16px;">
                    Allow updating Blood Group & Allergies
                </label>
            </div>

            <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center; padding:12px;">
                <i class="fas fa-save"></i> Save Configurations
            </button>
        </form>
    </div>
</div>
@endsection
