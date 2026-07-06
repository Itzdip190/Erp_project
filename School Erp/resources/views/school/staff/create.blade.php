@extends('layouts.app')

@section('title', 'Add Staff')
@section('page-title', 'Add Staff')

@section('styles')
<style>
:root {
    --st-blue: #1d4ed8;
    --st-blue-hover: #1e40af;
    --st-blue-light: #eff6ff;
    --st-border: #bfdbfe;
    --st-text: #1e3a8a;
    --st-text-muted: #64748b;
    --st-shadow: 0 4px 6px -1px rgba(29, 78, 216, 0.05), 0 2px 4px -1px rgba(29, 78, 216, 0.03);
}

.st-card {
    background: #ffffff;
    border: 1px solid var(--st-border);
    border-radius: 16px;
    box-shadow: var(--st-shadow);
    margin-bottom: 30px;
    overflow: hidden;
}

.st-card-hdr {
    background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%);
    padding: 24px 30px;
    color: #ffffff;
}

.st-card-hdr h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
}

.st-card-body {
    padding: 30px;
}

.st-section {
    border: 1px solid var(--st-border);
    border-radius: 12px;
    margin-bottom: 24px;
    background: #ffffff;
    overflow: hidden;
}

.st-section-hdr {
    background: var(--st-blue-light);
    padding: 14px 20px;
    font-weight: 700;
    color: var(--st-text);
    font-size: 14px;
    border-bottom: 1px solid var(--st-border);
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    user-select: none;
}

.st-section-body {
    padding: 20px;
}

.st-grid-3 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.st-grid-4 {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

.st-grid-2 {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.st-form-group {
    margin-bottom: 16px;
}

.st-label {
    display: block;
    font-size: 12.5px;
    font-weight: 600;
    color: var(--st-text);
    margin-bottom: 6px;
}

.st-label span {
    color: #ef4444;
}

.st-control {
    width: 100%;
    height: 40px;
    padding: 0 12px;
    border: 1px solid var(--st-border);
    border-radius: 8px;
    font-size: 13.5px;
    color: #1f2937;
    outline: none;
    transition: all 0.2s;
}

.st-control:focus {
    border-color: var(--st-blue);
    box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.15);
}

textarea.st-control {
    height: auto;
    padding: 10px 12px;
}

.st-btn-submit {
    background: var(--st-blue);
    color: #ffffff;
    border: none;
    padding: 10px 24px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.2s;
}

.st-btn-submit:hover {
    background: var(--st-blue-hover);
}

.st-btn-cancel {
    background: #ffffff;
    color: var(--st-text-muted);
    border: 1px solid var(--st-border);
    padding: 10px 24px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    text-align: center;
}

.st-btn-cancel:hover {
    background: #f8fafc;
}

.st-radio-group {
    display: flex;
    gap: 15px;
    align-items: center;
    height: 40px;
}

.st-radio-label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13.5px;
    cursor: pointer;
}

@media (max-width: 768px) {
    .st-grid-3, .st-grid-4, .st-grid-2 {
        grid-template-columns: 1fr;
        gap: 10px;
    }
}

/* ── STAFF REGISTRATION DARK MODE OVERRIDES ── */
body.dark-mode {
    --st-blue: #818cf8 !important;
    --st-blue-hover: #6366f1 !important;
    --st-blue-light: #1f2937 !important;
    --st-border: #1e293b !important;
    --st-text: #f8fafc !important;
    --st-text-muted: #cbd5e1 !important;
}
body.dark-mode .st-card {
    background: #111827 !important;
    border-color: #1e293b !important;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3) !important;
    color: #f8fafc !important;
}
body.dark-mode .st-card-hdr {
    background: linear-gradient(135deg, #111827 0%, #1e1b4b 60%, #312e81 100%) !important;
    border-bottom: 1px solid #1e293b !important;
}
body.dark-mode .st-section {
    background: #111827 !important;
    border-color: #1e293b !important;
}
body.dark-mode .st-section-hdr {
    background: #1f2937 !important;
    border-bottom-color: #374151 !important;
    color: #818cf8 !important;
}
body.dark-mode .st-label {
    color: #cbd5e1 !important;
}
body.dark-mode .st-control {
    background-color: #1f2937 !important;
    border-color: #374151 !important;
    color: #f8fafc !important;
}
body.dark-mode select.st-control option {
    background-color: #1f2937 !important;
    color: #f8fafc !important;
}
body.dark-mode .st-btn-cancel {
    background: #1f2937 !important;
    color: #cbd5e1 !important;
    border-color: #374151 !important;
}
body.dark-mode .st-btn-cancel:hover {
    background: #374151 !important;
    color: #ffffff !important;
}
</style>
@endsection

@section('content')
<div class="st-card" style="max-width: 1100px; margin: 0 auto;">
    <div class="st-card-hdr">
        <h3>Register New Staff Member</h3>
    </div>
    <div class="st-card-body">
        <form method="POST" action="{{ route('school.staff.store') }}" enctype="multipart/form-data">
            @csrf

            @if ($errors->any())
                <div style="background:#fee2e2; border-left:4px solid #ef4444; color:#991b1b; padding:12px; border-radius:8px; margin-bottom:20px; font-size:13.5px;">
                    <strong style="display:block; margin-bottom:4px;">Please correct the errors below:</strong>
                    <ul style="margin-left:20px; padding-left:0;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- 1. PERSONAL DETAILS -->
            <div class="st-section">
                <div class="st-section-hdr">
                    <i class="fas fa-user"></i> 1. Personal Details
                </div>
                <div class="st-section-body">
                    <div class="st-grid-3">
                        <div class="st-form-group">
                            <label class="st-label">Employee ID <span>*</span></label>
                            <input type="text" name="employee_id" class="st-control" value="{{ old('employee_id') }}" required placeholder="e.g. EMP101">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">First Name <span>*</span></label>
                            <input type="text" name="first_name" class="st-control" value="{{ old('first_name') }}" required placeholder="First name">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Last Name <span>*</span></label>
                            <input type="text" name="last_name" class="st-control" value="{{ old('last_name') }}" required placeholder="Last name">
                        </div>
                    </div>

                    <div class="st-grid-3">
                        <div class="st-form-group">
                            <label class="st-label">Email (Login Username) <span>*</span></label>
                            <input type="email" name="email" class="st-control" value="{{ old('email') }}" required placeholder="staff@school.com">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Password</label>
                            <input type="password" name="password" class="st-control" placeholder="Default: Welcome@2026!">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Mobile Number <span>*</span></label>
                            <input type="text" name="phone" class="st-control" value="{{ old('phone') }}" required placeholder="e.g. 9876543210">
                        </div>
                    </div>

                    <div class="st-grid-4">
                        <div class="st-form-group">
                            <label class="st-label">Alternate Mobile</label>
                            <input type="text" name="additional_fields[alternate_phone]" class="st-control" value="{{ old('additional_fields.alternate_phone') }}" placeholder="Alternate mobile number">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="st-control" value="{{ old('date_of_birth') }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Age</label>
                            <input type="text" name="age" class="st-control" placeholder="Calculated from Date of Birth" readonly>
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Gender</label>
                            <select name="gender" class="st-control">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="st-grid-4">
                        <div class="st-form-group">
                            <label class="st-label">Marital Status</label>
                            <select name="additional_fields[marital_status]" class="st-control">
                                <option value="single">Single</option>
                                <option value="married">Married</option>
                                <option value="divorced">Divorced</option>
                                <option value="widowed">Widowed</option>
                            </select>
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Category</label>
                            <input type="text" name="additional_fields[category]" class="st-control" placeholder="e.g. General, OBC, SC, ST">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Blood Group</label>
                            <input type="text" name="blood_group" class="st-control" placeholder="e.g. O+, A+">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Religion</label>
                            <select name="additional_fields[religion]" class="st-control">
                                <option value="">Select Religion</option>
                                <option value="Hinduism" {{ old('additional_fields.religion') === 'Hinduism' ? 'selected' : '' }}>Hinduism</option>
                                <option value="Islam" {{ old('additional_fields.religion') === 'Islam' ? 'selected' : '' }}>Islam</option>
                                <option value="Christianity" {{ old('additional_fields.religion') === 'Christianity' ? 'selected' : '' }}>Christianity</option>
                                <option value="Sikhism" {{ old('additional_fields.religion') === 'Sikhism' ? 'selected' : '' }}>Sikhism</option>
                                <option value="Buddhism" {{ old('additional_fields.religion') === 'Buddhism' ? 'selected' : '' }}>Buddhism</option>
                                <option value="Jainism" {{ old('additional_fields.religion') === 'Jainism' ? 'selected' : '' }}>Jainism</option>
                                <option value="Zoroastrianism" {{ old('additional_fields.religion') === 'Zoroastrianism' ? 'selected' : '' }}>Zoroastrianism (Parsi)</option>
                                <option value="Judaism" {{ old('additional_fields.religion') === 'Judaism' ? 'selected' : '' }}>Judaism</option>
                                <option value="Others" {{ old('additional_fields.religion') === 'Others' ? 'selected' : '' }}>Others</option>
                            </select>
                        </div>
                    </div>

                    <div class="st-grid-4">
                        <div class="st-form-group">
                            <label class="st-label">Mother Tongue</label>
                            <input type="text" name="additional_fields[mother_tongue]" class="st-control" placeholder="e.g. English, Hindi">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">PAN Number</label>
                            <input type="text" name="pan_number" class="st-control" value="{{ old('pan_number') }}" placeholder="e.g. ABCDE1234F">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Aadhar Number</label>
                            <input type="text" name="additional_fields[aadhar_number]" class="st-control" value="{{ old('additional_fields.aadhar_number') }}" placeholder="12-digit Aadhar number">
                        </div>
                        <div class="st-form-group" style="grid-column: span 2; display: flex; align-items: center; gap: 15px; margin-bottom: 0;">
                            <div id="avatarPreview" style="width: 80px; height: 80px; border-radius: 50%; border: 2px dashed var(--st-border); display: flex; align-items: center; justify-content: center; background-position: center; background-size: cover; overflow: hidden; background-color: #f8fafc; flex-shrink: 0; border-color: var(--border);">
                                <i class="fa fa-user" style="font-size: 2rem; color: var(--t3);"></i>
                            </div>
                            <div style="flex: 1;">
                                <label class="st-label">Staff Photo</label>
                                <input type="hidden" name="captured_photo" id="captured_photo" value="{{ old('captured_photo') }}">
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <label class="st-btn-submit" style="font-size: 11px; padding: 6px 12px; cursor: pointer; background: #4b5563; margin: 0; display: inline-block;">
                                        <i class="fa fa-image"></i> Choose Photo
                                        <input type="file" name="photo" id="photoInput" style="display: none;" accept="image/*">
                                    </label>
                                    <button type="button" class="btn btn-outline" id="cameraTriggerBtn" style="font-size: 11px; padding: 6px 12px;">
                                        <i class="fa fa-camera"></i> Camera
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Camera Modal Overlay -->
                        <div id="cameraModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); z-index: 10000; align-items: center; justify-content: center; backdrop-filter: blur(8px); padding: 20px;">
                            <div class="glass-card" style="max-width: 480px; width: 100%; border-radius: 20px; overflow: hidden; padding: 24px; text-align: center; display: flex; flex-direction: column; gap: 1.5rem; background: var(--card); border: 1px solid var(--border);">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <h3 style="font-family: 'Syne', sans-serif; font-weight: 800; margin: 0; font-size: 1.25rem; color: var(--navy);">Camera Capture</h3>
                                    <button type="button" id="closeCameraBtn" style="background: transparent; border: none; font-size: 1.2rem; cursor: pointer; color: var(--t2);"><i class="fa fa-times"></i></button>
                                </div>
                                <div style="position: relative; width: 100%; aspect-ratio: 4/3; background: #000; border-radius: 12px; overflow: hidden; border: 2px solid var(--border);">
                                    <video id="cameraVideo" autoplay playsinline style="width: 100%; height: 100%; object-fit: cover;"></video>
                                    <canvas id="cameraCanvas" style="display: none;"></canvas>
                                </div>
                                <div style="display: flex; justify-content: center; gap: 1rem;">
                                    <button type="button" id="takeSnapshotBtn" class="btn btn-gold" style="padding: 10px 24px; border-radius: 10px; font-weight: 700; background: var(--gold); border: none; color: #fff; cursor: pointer;"><i class="fa fa-circle"></i> Capture</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. QUALIFICATIONS AND EPF/ESI DETAILS -->
            <div class="st-section">
                <div class="st-section-hdr">
                    <i class="fas fa-graduation-cap"></i> 2. Qualifications & Platform Specific Details
                </div>
                <div class="st-section-body">
                    <div class="st-grid-3">
                        <div class="st-form-group">
                            <label class="st-label">Department <span>*</span></label>
                            <select name="department_id" class="st-control" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Designation <span>*</span></label>
                            <select name="designation_id" class="st-control" required>
                                <option value="">Select Designation</option>
                                @foreach($designations as $desg)
                                    <option value="{{ $desg->id }}">{{ $desg->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Employment Type <span>*</span></label>
                            <select name="employment_type" class="st-control" required>
                                <option value="permanent">Permanent</option>
                                <option value="contract">Contract</option>
                                <option value="part_time">Part Time</option>
                            </select>
                        </div>
                    </div>

                    <div class="st-grid-4">
                        <div class="st-form-group">
                            <label class="st-label">Date of Joining <span>*</span></label>
                            <input type="date" name="joining_date" class="st-control" value="{{ today()->toDateString() }}" required>
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Basic Salary <span>*</span></label>
                            <input type="number" name="basic_salary" class="st-control" value="0.00" step="0.01" required>
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Qualification</label>
                            <input type="text" name="qualification" class="st-control" placeholder="e.g. B.Ed, M.Sc">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Work Experience (Years)</label>
                            <input type="number" name="experience_years" class="st-control" value="0">
                        </div>
                    </div>

                    <div class="st-grid-4">
                        <div class="st-form-group">
                            <label class="st-label">EPF Account Number</label>
                            <input type="text" name="additional_fields[epf_account]" class="st-control" placeholder="EPF Account Number">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">ESI Account Number</label>
                            <input type="text" name="additional_fields[esi_account]" class="st-control" placeholder="ESI Account Number">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">EPF/ESI UAN</label>
                            <input type="text" name="additional_fields[epf_uan]" class="st-control" placeholder="12-digit UAN">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Status <span>*</span></label>
                            <select name="is_active" class="st-control" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="st-grid-4">
                        <div class="st-form-group">
                            <label class="st-label">Date of EPF Joining</label>
                            <input type="date" name="additional_fields[epf_joining_date]" class="st-control">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Date of EPF Exit</label>
                            <input type="date" name="additional_fields[epf_exit_date]" class="st-control">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Date of ESI Joining</label>
                            <input type="date" name="additional_fields[esi_joining_date]" class="st-control">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Date of ESI Exit</label>
                            <input type="date" name="additional_fields[esi_exit_date]" class="st-control">
                        </div>
                    </div>

                    <div class="st-form-group">
                        <label class="st-label">Remarks</label>
                        <textarea name="additional_fields[remarks]" class="st-control" rows="2" placeholder="Any remarks..."></textarea>
                    </div>
                </div>
            </div>

            <!-- 3. PROFESSIONAL HISTORY -->
            <div class="st-section">
                <div class="st-section-hdr">
                    <i class="fas fa-briefcase"></i> 3. Professional History
                </div>
                <div class="st-section-body">
                    <div class="st-grid-3">
                        <div class="st-form-group">
                            <label class="st-label">Previous Employer</label>
                            <input type="text" name="additional_fields[previous_employer]" class="st-control" placeholder="Company/School Name">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Start Date</label>
                            <input type="date" name="additional_fields[prev_start]" class="st-control">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">End Date</label>
                            <input type="date" name="additional_fields[prev_end]" class="st-control">
                        </div>
                    </div>
                    <div class="st-grid-3">
                        <div class="st-form-group">
                            <label class="st-label">Designation</label>
                            <input type="text" name="additional_fields[prev_designation]" class="st-control" placeholder="e.g. Teacher, Clerk">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Reason for Leaving</label>
                            <input type="text" name="additional_fields[prev_reason]" class="st-control" placeholder="Reason for leaving">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. FAMILY DETAILS -->
            <div class="st-section">
                <div class="st-section-hdr">
                    <i class="fas fa-users"></i> 4. Family Details
                </div>
                <div class="st-section-body">
                    <div class="st-grid-3">
                        <div class="st-form-group">
                            <label class="st-label">Father Name</label>
                            <input type="text" name="additional_fields[father_name]" class="st-control" placeholder="Father's name">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Father Phone</label>
                            <input type="text" name="additional_fields[father_phone]" class="st-control" placeholder="Father's phone">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Mother Name</label>
                            <input type="text" name="additional_fields[mother_name]" class="st-control" placeholder="Mother's name">
                        </div>
                    </div>
                    <div class="st-grid-3">
                        <div class="st-form-group">
                            <label class="st-label">Mother Phone</label>
                            <input type="text" name="additional_fields[mother_phone]" class="st-control" placeholder="Mother's phone">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Spouse Name</label>
                            <input type="text" name="additional_fields[spouse_name]" class="st-control" placeholder="Spouse name">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Spouse Phone</label>
                            <input type="text" name="additional_fields[spouse_phone]" class="st-control" placeholder="Spouse's phone">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. IDENTIFICATION & ADDRESS -->
            <div class="st-section">
                <div class="st-section-hdr">
                    <i class="fas fa-id-card"></i> 5. Identification & Address Details
                </div>
                <div class="st-section-body">
                    <div class="st-grid-2">
                        <div class="st-form-group">
                            <label class="st-label">Passport Number</label>
                            <input type="text" name="additional_fields[passport_number]" class="st-control" placeholder="Passport Number">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Visa / Work Permit Details</label>
                            <input type="text" name="additional_fields[visa_details]" class="st-control" placeholder="Visa details">
                        </div>
                    </div>

                    <h5 style="margin: 10px 0; color: var(--st-text); font-weight: 700; font-size:13px;">Permanent Address</h5>
                    <div class="st-grid-4">
                        <div class="st-form-group" style="grid-column: span 2;">
                            <label class="st-label">Address Line</label>
                            <input type="text" name="address" class="st-control" placeholder="Permanent Address">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">City</label>
                            <input type="text" name="city" class="st-control" placeholder="City">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">State</label>
                            <input type="text" name="state" class="st-control" placeholder="State">
                        </div>
                    </div>
                    <div class="st-grid-4">
                        <div class="st-form-group">
                            <label class="st-label">Pincode</label>
                            <input type="text" name="pincode" class="st-control" placeholder="Pincode">
                        </div>
                    </div>

                    <h5 style="margin: 15px 0 10px 0; color: var(--st-text); font-weight: 700; font-size:13px;">Correspondence Address</h5>
                    <div class="st-grid-4">
                        <div class="st-form-group" style="grid-column: span 2;">
                            <label class="st-label">Address Line</label>
                            <input type="text" name="additional_fields[correspondence_address]" class="st-control" placeholder="Correspondence Address">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">City</label>
                            <input type="text" name="additional_fields[correspondence_city]" class="st-control" placeholder="City">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">State</label>
                            <input type="text" name="additional_fields[correspondence_state]" class="st-control" placeholder="State">
                        </div>
                    </div>
                    <div class="st-grid-4">
                        <div class="st-form-group">
                            <label class="st-label">Pincode</label>
                            <input type="text" name="additional_fields[correspondence_pincode]" class="st-control" placeholder="Pincode">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 6. BANK ACCOUNT & EMERGENCY & DRIVING DETAILS -->
            <div class="st-section">
                <div class="st-section-hdr">
                    <i class="fas fa-university"></i> 6. Bank, Emergency & Driving Details
                </div>
                <div class="st-section-body">
                    <h5 style="margin-bottom: 10px; color: var(--st-text); font-weight: 700; font-size:13px;">Bank Details</h5>
                    <div class="st-grid-4">
                        <div class="st-form-group">
                            <label class="st-label">Bank Name</label>
                            <input type="text" name="bank_name" class="st-control" placeholder="e.g. State Bank of India">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Account Number</label>
                            <input type="text" name="bank_account_number" class="st-control" placeholder="Account Number">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">IFSC Code</label>
                            <input type="text" name="ifsc_code" class="st-control" placeholder="IFSC Code">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Branch Name</label>
                            <input type="text" name="additional_fields[branch_name]" class="st-control" placeholder="Branch Name">
                        </div>
                    </div>

                    <h5 style="margin: 15px 0 10px 0; color: var(--st-text); font-weight: 700; font-size:13px;">Emergency Details</h5>
                    <div class="st-grid-4">
                        <div class="st-form-group">
                            <label class="st-label">Contact Person Name</label>
                            <input type="text" name="additional_fields[emergency_contact_name]" class="st-control" placeholder="Emergency contact name">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Relationship</label>
                            <input type="text" name="additional_fields[emergency_relationship]" class="st-control" placeholder="e.g. Brother, Wife">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Contact Phone</label>
                            <input type="text" name="additional_fields[emergency_contact_phone]" class="st-control" placeholder="Contact number">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Alternate Contact Phone</label>
                            <input type="text" name="additional_fields[emergency_alt_phone]" class="st-control" placeholder="Alternate phone">
                        </div>
                    </div>

                    <h5 style="margin: 15px 0 10px 0; color: var(--st-text); font-weight: 700; font-size:13px;">Driving License Details</h5>
                    <div class="st-grid-2">
                        <div class="st-form-group">
                            <label class="st-label">DL Number</label>
                            <input type="text" name="additional_fields[dl_number]" class="st-control" placeholder="DL Number">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">DL Expiry</label>
                            <input type="date" name="additional_fields[dl_expiry]" class="st-control">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 7. SALARY DETAILS & SOCIAL ACCOUNTS -->
            <div class="st-section">
                <div class="st-section-hdr">
                    <i class="fas fa-indian-rupee-sign"></i> 7. Salary Details & Social Accounts
                </div>
                <div class="st-section-body">
                    <div class="st-grid-3">
                        <div class="st-form-group">
                            <label class="st-label">Gross Salary</label>
                            <input type="number" name="additional_fields[gross_salary]" class="st-control" value="0.00" step="0.01">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Net Salary</label>
                            <input type="number" name="additional_fields[net_salary]" class="st-control" value="0.00" step="0.01">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Deductions</label>
                            <input type="number" name="additional_fields[deductions]" class="st-control" value="0.00" step="0.01">
                        </div>
                    </div>

                    <h5 style="margin: 15px 0 10px 0; color: var(--st-text); font-weight: 700; font-size:13px;">Social Links</h5>
                    <div class="st-grid-3">
                        <div class="st-form-group">
                            <label class="st-label">LinkedIn Profile URL</label>
                            <input type="url" name="additional_fields[linkedin_url]" class="st-control" placeholder="https://linkedin.com/in/username">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Facebook Profile URL</label>
                            <input type="url" name="additional_fields[facebook_url]" class="st-control" placeholder="https://facebook.com/username">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Twitter / X Profile URL</label>
                            <input type="url" name="additional_fields[twitter_url]" class="st-control" placeholder="https://twitter.com/username">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACTION BUTTONS -->
            <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid var(--st-border); padding-top: 20px;">
                <a href="{{ route('school.staff.index') }}" class="st-btn-cancel">Cancel</a>
                <button type="submit" class="st-btn-submit">Register Staff Member</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Camera capture and preview logic
    document.addEventListener('DOMContentLoaded', function() {
        const cameraTriggerBtn = document.getElementById('cameraTriggerBtn');
        const cameraModal = document.getElementById('cameraModal');
        const closeCameraBtn = document.getElementById('closeCameraBtn');
        const cameraVideo = document.getElementById('cameraVideo');
        const cameraCanvas = document.getElementById('cameraCanvas');
        const takeSnapshotBtn = document.getElementById('takeSnapshotBtn');
        const capturedPhotoInput = document.getElementById('captured_photo');
        const avatarPreview = document.getElementById('avatarPreview');
        const photoInput = document.getElementById('photoInput');
        
        let stream = null;

        if (cameraTriggerBtn) {
            cameraTriggerBtn.addEventListener('click', async function() {
                cameraModal.style.display = 'flex';
                try {
                    stream = await navigator.mediaDevices.getUserMedia({ 
                        video: { width: 640, height: 480, facingMode: 'user' }, 
                        audio: false 
                    });
                    cameraVideo.srcObject = stream;
                } catch (err) {
                    console.error("Camera access error:", err);
                    alert("Could not access camera. Please verify permissions.");
                    cameraModal.style.display = 'none';
                }
            });
        }

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
            cameraVideo.srcObject = null;
            cameraModal.style.display = 'none';
        }

        if (closeCameraBtn) {
            closeCameraBtn.addEventListener('click', stopCamera);
        }

        if (takeSnapshotBtn) {
            takeSnapshotBtn.addEventListener('click', function() {
                if (!stream) return;
                
                const context = cameraCanvas.getContext('2d');
                cameraCanvas.width = cameraVideo.videoWidth || 640;
                cameraCanvas.height = cameraVideo.videoHeight || 480;
                
                context.drawImage(cameraVideo, 0, 0, cameraCanvas.width, cameraCanvas.height);
                
                const dataUrl = cameraCanvas.toDataURL('image/jpeg');
                capturedPhotoInput.value = dataUrl;
                
                // Clear standard file input so it doesn't conflict
                if (photoInput) {
                    photoInput.value = '';
                }
                
                // Show preview
                if (avatarPreview) {
                    avatarPreview.style.backgroundImage = `url(${dataUrl})`;
                    const icon = avatarPreview.querySelector('.fa-user');
                    if (icon) icon.style.display = 'none';
                }
                
                stopCamera();
            });
        }

        // Convert standard selected file to base64 so validation failures don't lose it!
        if (photoInput) {
            photoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(evt) {
                        capturedPhotoInput.value = evt.target.result;
                        if (avatarPreview) {
                            avatarPreview.style.backgroundImage = `url(${evt.target.result})`;
                            const icon = avatarPreview.querySelector('.fa-user');
                            if (icon) icon.style.display = 'none';
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Check on page load if captured_photo has an old value (validation redirect)
        if (capturedPhotoInput && capturedPhotoInput.value) {
            if (avatarPreview) {
                avatarPreview.style.backgroundImage = `url(${capturedPhotoInput.value})`;
                const icon = avatarPreview.querySelector('.fa-user');
                if (icon) icon.style.display = 'none';
            }
        }

        // Detailed Age calculation
        const dobInput = document.querySelector('input[name="date_of_birth"]');
        const ageInput = document.querySelector('input[name="age"]');
        
        if (dobInput && ageInput) {
            const calculateAge = function() {
                const dob = dobInput.value;
                if (dob) {
                    const birthDate = new Date(dob);
                    const today = new Date();
                    
                    let years = today.getFullYear() - birthDate.getFullYear();
                    let months = today.getMonth() - birthDate.getMonth();
                    let days = today.getDate() - birthDate.getDate();
                    
                    if (days < 0) {
                        months--;
                        const prevMonth = new Date(today.getFullYear(), today.getMonth(), 0);
                        days += prevMonth.getDate();
                    }
                    
                    if (months < 0) {
                        years--;
                        months += 12;
                    }
                    
                    if (years < 0) {
                        ageInput.value = '0 years, 0 months, 0 days';
                    } else {
                        ageInput.value = `${years} years, ${months} months, ${days} days`;
                    }
                } else {
                    ageInput.value = '';
                }
            };
            
            dobInput.addEventListener('change', calculateAge);
            if (dobInput.value) {
                calculateAge();
            }
        }

        // Restrict phone numbers to 10 digits and only numbers
        const phoneFields = ['phone', 'additional_fields[alternate_phone]', 'additional_fields[father_phone]'];
        phoneFields.forEach(name => {
            const input = document.querySelector(`input[name="${name}"]`);
            if (input) {
                input.setAttribute('maxlength', '10');
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            }
        });
    });
</script>

