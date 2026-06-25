@extends('layouts.app')

@section('title', 'Edit Staff')
@section('page-title', 'Edit Staff')

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

@media (max-width: 768px) {
    .st-grid-3, .st-grid-4, .st-grid-2 {
        grid-template-columns: 1fr;
        gap: 10px;
    }
}
</style>
@endsection

@section('content')
<div class="st-card" style="max-width: 1100px; margin: 0 auto;">
    <div class="st-card-hdr">
        <h3>Edit Staff Profile: {{ $staff->full_name }}</h3>
    </div>
    <div class="st-card-body">
        <form method="POST" action="{{ route('school.staff.update', $staff->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

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
                            <input type="text" name="employee_id" class="st-control" value="{{ old('employee_id', $staff->employee_id) }}" required>
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">First Name <span>*</span></label>
                            <input type="text" name="first_name" class="st-control" value="{{ old('first_name', $staff->first_name) }}" required>
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Last Name <span>*</span></label>
                            <input type="text" name="last_name" class="st-control" value="{{ old('last_name', $staff->last_name) }}" required>
                        </div>
                    </div>

                    <div class="st-grid-3">
                        <div class="st-form-group">
                            <label class="st-label">Email <span>*</span></label>
                            <input type="email" name="email" class="st-control" value="{{ old('email', $staff->email) }}" required>
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Password</label>
                            <input type="password" name="password" class="st-control" placeholder="Leave blank to keep current">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Mobile Number <span>*</span></label>
                            <input type="text" name="phone" class="st-control" value="{{ old('phone', $staff->phone) }}" required>
                        </div>
                    </div>

                    <div class="st-grid-3">
                        <div class="st-form-group">
                            <label class="st-label">Alternate Mobile</label>
                            <input type="text" name="additional_fields[alternate_phone]" class="st-control" value="{{ old('additional_fields.alternate_phone', $staff->additional_fields['alternate_phone'] ?? '') }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="st-control" value="{{ old('date_of_birth', $staff->date_of_birth?->toDateString()) }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Gender</label>
                            <select name="gender" class="st-control">
                                <option value="male" {{ $staff->gender === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ $staff->gender === 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ $staff->gender === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="st-grid-4">
                        <div class="st-form-group">
                            <label class="st-label">Marital Status</label>
                            <select name="additional_fields[marital_status]" class="st-control">
                                <option value="single" {{ ($staff->additional_fields['marital_status'] ?? '') === 'single' ? 'selected' : '' }}>Single</option>
                                <option value="married" {{ ($staff->additional_fields['marital_status'] ?? '') === 'married' ? 'selected' : '' }}>Married</option>
                                <option value="divorced" {{ ($staff->additional_fields['marital_status'] ?? '') === 'divorced' ? 'selected' : '' }}>Divorced</option>
                                <option value="widowed" {{ ($staff->additional_fields['marital_status'] ?? '') === 'widowed' ? 'selected' : '' }}>Widowed</option>
                            </select>
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Category</label>
                            <input type="text" name="additional_fields[category]" class="st-control" value="{{ old('additional_fields.category', $staff->additional_fields['category'] ?? '') }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Blood Group</label>
                            <input type="text" name="blood_group" class="st-control" value="{{ old('blood_group', $staff->blood_group) }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Religion</label>
                            <input type="text" name="additional_fields[religion]" class="st-control" value="{{ old('additional_fields.religion', $staff->additional_fields['religion'] ?? '') }}">
                        </div>
                    </div>

                    <div class="st-grid-4">
                        <div class="st-form-group">
                            <label class="st-label">Mother Tongue</label>
                            <input type="text" name="additional_fields[mother_tongue]" class="st-control" value="{{ old('additional_fields.mother_tongue', $staff->additional_fields['mother_tongue'] ?? '') }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">PAN Number <span>*</span></label>
                            <input type="text" name="pan_number" class="st-control" value="{{ old('pan_number', $staff->pan_number) }}" required>
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Aadhar Number <span>*</span></label>
                            <input type="text" name="additional_fields[aadhar_number]" class="st-control" value="{{ old('additional_fields.aadhar_number', $staff->additional_fields['aadhar_number'] ?? '') }}" required>
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Staff Photo</label>
                            <input type="file" name="photo" class="st-control" accept="image/*">
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
                                    <option value="{{ $dept->id }}" {{ $staff->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Designation <span>*</span></label>
                            <select name="designation_id" class="st-control" required>
                                <option value="">Select Designation</option>
                                @foreach($designations as $desg)
                                    <option value="{{ $desg->id }}" {{ $staff->designation_id == $desg->id ? 'selected' : '' }}>{{ $desg->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Employment Type <span>*</span></label>
                            <select name="employment_type" class="st-control" required>
                                <option value="permanent" {{ $staff->employment_type === 'permanent' ? 'selected' : '' }}>Permanent</option>
                                <option value="contract" {{ $staff->employment_type === 'contract' ? 'selected' : '' }}>Contract</option>
                                <option value="part_time" {{ $staff->employment_type === 'part_time' ? 'selected' : '' }}>Part Time</option>
                            </select>
                        </div>
                    </div>

                    <div class="st-grid-4">
                        <div class="st-form-group">
                            <label class="st-label">Date of Joining <span>*</span></label>
                            <input type="date" name="joining_date" class="st-control" value="{{ old('joining_date', $staff->joining_date?->toDateString()) }}" required>
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Basic Salary <span>*</span></label>
                            <input type="number" name="basic_salary" class="st-control" value="{{ old('basic_salary', $staff->basic_salary) }}" step="0.01" required>
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Qualification</label>
                            <input type="text" name="qualification" class="st-control" value="{{ old('qualification', $staff->qualification) }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Work Experience (Years)</label>
                            <input type="number" name="experience_years" class="st-control" value="{{ old('experience_years', $staff->experience_years) }}">
                        </div>
                    </div>

                    <div class="st-grid-4">
                        <div class="st-form-group">
                            <label class="st-label">EPF Account Number</label>
                            <input type="text" name="additional_fields[epf_account]" class="st-control" value="{{ old('additional_fields.epf_account', $staff->additional_fields['epf_account'] ?? '') }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">ESI Account Number</label>
                            <input type="text" name="additional_fields[esi_account]" class="st-control" value="{{ old('additional_fields.esi_account', $staff->additional_fields['esi_account'] ?? '') }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">EPF/ESI UAN</label>
                            <input type="text" name="additional_fields[epf_uan]" class="st-control" value="{{ old('additional_fields.epf_uan', $staff->additional_fields['epf_uan'] ?? '') }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Status <span>*</span></label>
                            <select name="is_active" class="st-control" required>
                                <option value="1" {{ $staff->is_active ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ !$staff->is_active ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="st-grid-4">
                        <div class="st-form-group">
                            <label class="st-label">Date of EPF Joining</label>
                            <input type="date" name="additional_fields[epf_joining_date]" class="st-control" value="{{ old('additional_fields.epf_joining_date', $staff->additional_fields['epf_joining_date'] ?? '') }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Date of EPF Exit</label>
                            <input type="date" name="additional_fields[epf_exit_date]" class="st-control" value="{{ old('additional_fields.epf_exit_date', $staff->additional_fields['epf_exit_date'] ?? '') }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Date of ESI Joining</label>
                            <input type="date" name="additional_fields[esi_joining_date]" class="st-control" value="{{ old('additional_fields.esi_joining_date', $staff->additional_fields['esi_joining_date'] ?? '') }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Date of ESI Exit</label>
                            <input type="date" name="additional_fields[esi_exit_date]" class="st-control" value="{{ old('additional_fields.esi_exit_date', $staff->additional_fields['esi_exit_date'] ?? '') }}">
                        </div>
                    </div>

                    <div class="st-form-group">
                        <label class="st-label">Remarks</label>
                        <textarea name="additional_fields[remarks]" class="st-control" rows="2">{{ old('additional_fields.remarks', $staff->additional_fields['remarks'] ?? '') }}</textarea>
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
                            <input type="text" name="additional_fields[previous_employer]" class="st-control" value="{{ old('additional_fields.previous_employer', $staff->additional_fields['previous_employer'] ?? '') }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Start Date</label>
                            <input type="date" name="additional_fields[prev_start]" class="st-control" value="{{ old('additional_fields.prev_start', $staff->additional_fields['prev_start'] ?? '') }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">End Date</label>
                            <input type="date" name="additional_fields[prev_end]" class="st-control" value="{{ old('additional_fields.prev_end', $staff->additional_fields['prev_end'] ?? '') }}">
                        </div>
                    </div>
                    <div class="st-grid-3">
                        <div class="st-form-group">
                            <label class="st-label">Designation</label>
                            <input type="text" name="additional_fields[prev_designation]" class="st-control" value="{{ old('additional_fields.prev_designation', $staff->additional_fields['prev_designation'] ?? '') }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Reason for Leaving</label>
                            <input type="text" name="additional_fields[prev_reason]" class="st-control" value="{{ old('additional_fields.prev_reason', $staff->additional_fields['prev_reason'] ?? '') }}">
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
                            <input type="text" name="additional_fields[father_name]" class="st-control" value="{{ old('additional_fields.father_name', $staff->additional_fields['father_name'] ?? '') }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Father Phone</label>
                            <input type="text" name="additional_fields[father_phone]" class="st-control" value="{{ old('additional_fields.father_phone', $staff->additional_fields['father_phone'] ?? '') }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Mother Name</label>
                            <input type="text" name="additional_fields[mother_name]" class="st-control" value="{{ old('additional_fields.mother_name', $staff->additional_fields['mother_name'] ?? '') }}">
                        </div>
                    </div>
                    <div class="st-grid-3">
                        <div class="st-form-group">
                            <label class="st-label">Mother Phone</label>
                            <input type="text" name="additional_fields[mother_phone]" class="st-control" value="{{ old('additional_fields.mother_phone', $staff->additional_fields['mother_phone'] ?? '') }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Spouse Name</label>
                            <input type="text" name="additional_fields[spouse_name]" class="st-control" value="{{ old('additional_fields.spouse_name', $staff->additional_fields['spouse_name'] ?? '') }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Spouse Phone</label>
                            <input type="text" name="additional_fields[spouse_phone]" class="st-control" value="{{ old('additional_fields.spouse_phone', $staff->additional_fields['spouse_phone'] ?? '') }}">
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
                            <input type="text" name="additional_fields[passport_number]" class="st-control" value="{{ old('additional_fields.passport_number', $staff->additional_fields['passport_number'] ?? '') }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Visa / Work Permit Details</label>
                            <input type="text" name="additional_fields[visa_details]" class="st-control" value="{{ old('additional_fields.visa_details', $staff->additional_fields['visa_details'] ?? '') }}">
                        </div>
                    </div>

                    <h5 style="margin: 10px 0; color: var(--st-text); font-weight: 700; font-size:13px;">Permanent Address</h5>
                    <div class="st-grid-4">
                        <div class="st-form-group" style="grid-column: span 2;">
                            <label class="st-label">Address Line</label>
                            <input type="text" name="address" class="st-control" value="{{ old('address', $staff->address) }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">City</label>
                            <input type="text" name="city" class="st-control" value="{{ old('city', $staff->city) }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">State</label>
                            <input type="text" name="state" class="st-control" value="{{ old('state', $staff->state) }}">
                        </div>
                    </div>
                    <div class="st-grid-4">
                        <div class="st-form-group">
                            <label class="st-label">Pincode</label>
                            <input type="text" name="pincode" class="st-control" value="{{ old('pincode', $staff->pincode) }}">
                        </div>
                    </div>

                    <h5 style="margin: 15px 0 10px 0; color: var(--st-text); font-weight: 700; font-size:13px;">Correspondence Address</h5>
                    <div class="st-grid-4">
                        <div class="st-form-group" style="grid-column: span 2;">
                            <label class="st-label">Address Line</label>
                            <input type="text" name="additional_fields[correspondence_address]" class="st-control" value="{{ old('additional_fields.correspondence_address', $staff->additional_fields['correspondence_address'] ?? '') }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">City</label>
                            <input type="text" name="additional_fields[correspondence_city]" class="st-control" value="{{ old('additional_fields.correspondence_city', $staff->additional_fields['correspondence_city'] ?? '') }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">State</label>
                            <input type="text" name="additional_fields[correspondence_state]" class="st-control" value="{{ old('additional_fields.correspondence_state', $staff->additional_fields['correspondence_state'] ?? '') }}">
                        </div>
                    </div>
                    <div class="st-grid-4">
                        <div class="st-form-group">
                            <label class="st-label">Pincode</label>
                            <input type="text" name="additional_fields[correspondence_pincode]" class="st-control" value="{{ old('additional_fields.correspondence_pincode', $staff->additional_fields['correspondence_pincode'] ?? '') }}">
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
                            <input type="text" name="bank_name" class="st-control" value="{{ old('bank_name', $staff->bank_name) }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Account Number</label>
                            <input type="text" name="bank_account_number" class="st-control" value="{{ old('bank_account_number', $staff->bank_account_number) }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">IFSC Code</label>
                            <input type="text" name="ifsc_code" class="st-control" value="{{ old('ifsc_code', $staff->ifsc_code) }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Branch Name</label>
                            <input type="text" name="additional_fields[branch_name]" class="st-control" value="{{ old('additional_fields.branch_name', $staff->additional_fields['branch_name'] ?? '') }}">
                        </div>
                    </div>

                    <h5 style="margin: 15px 0 10px 0; color: var(--st-text); font-weight: 700; font-size:13px;">Emergency Details</h5>
                    <div class="st-grid-4">
                        <div class="st-form-group">
                            <label class="st-label">Contact Person Name</label>
                            <input type="text" name="additional_fields[emergency_contact_name]" class="st-control" value="{{ old('additional_fields.emergency_contact_name', $staff->additional_fields['emergency_contact_name'] ?? '') }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Relationship</label>
                            <input type="text" name="additional_fields[emergency_relationship]" class="st-control" value="{{ old('additional_fields.emergency_relationship', $staff->additional_fields['emergency_relationship'] ?? '') }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Contact Phone</label>
                            <input type="text" name="additional_fields[emergency_contact_phone]" class="st-control" value="{{ old('additional_fields.emergency_contact_phone', $staff->additional_fields['emergency_contact_phone'] ?? '') }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Alternate Contact Phone</label>
                            <input type="text" name="additional_fields[emergency_alt_phone]" class="st-control" value="{{ old('additional_fields.emergency_alt_phone', $staff->additional_fields['emergency_alt_phone'] ?? '') }}">
                        </div>
                    </div>

                    <h5 style="margin: 15px 0 10px 0; color: var(--st-text); font-weight: 700; font-size:13px;">Driving License Details</h5>
                    <div class="st-grid-2">
                        <div class="st-form-group">
                            <label class="st-label">DL Number</label>
                            <input type="text" name="additional_fields[dl_number]" class="st-control" value="{{ old('additional_fields.dl_number', $staff->additional_fields['dl_number'] ?? '') }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">DL Expiry</label>
                            <input type="date" name="additional_fields[dl_expiry]" class="st-control" value="{{ old('additional_fields.dl_expiry', $staff->additional_fields['dl_expiry'] ?? '') }}">
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
                            <input type="number" name="additional_fields[gross_salary]" class="st-control" value="{{ old('additional_fields.gross_salary', $staff->additional_fields['gross_salary'] ?? '0.00') }}" step="0.01">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Net Salary</label>
                            <input type="number" name="additional_fields[net_salary]" class="st-control" value="{{ old('additional_fields.net_salary', $staff->additional_fields['net_salary'] ?? '0.00') }}" step="0.01">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Deductions</label>
                            <input type="number" name="additional_fields[deductions]" class="st-control" value="{{ old('additional_fields.deductions', $staff->additional_fields['deductions'] ?? '0.00') }}" step="0.01">
                        </div>
                    </div>

                    <h5 style="margin: 15px 0 10px 0; color: var(--st-text); font-weight: 700; font-size:13px;">Social Links</h5>
                    <div class="st-grid-3">
                        <div class="st-form-group">
                            <label class="st-label">LinkedIn Profile URL</label>
                            <input type="url" name="additional_fields[linkedin_url]" class="st-control" value="{{ old('additional_fields.linkedin_url', $staff->additional_fields['linkedin_url'] ?? '') }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Facebook Profile URL</label>
                            <input type="url" name="additional_fields[facebook_url]" class="st-control" value="{{ old('additional_fields.facebook_url', $staff->additional_fields['facebook_url'] ?? '') }}">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Twitter / X Profile URL</label>
                            <input type="url" name="additional_fields[twitter_url]" class="st-control" value="{{ old('additional_fields.twitter_url', $staff->additional_fields['twitter_url'] ?? '') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACTION BUTTONS -->
            <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid var(--st-border); padding-top: 20px;">
                <a href="{{ route('school.staff.index') }}" class="st-btn-cancel">Cancel</a>
                <button type="submit" class="st-btn-submit">Update Staff Profile</button>
            </div>
        </form>
    </div>
</div>
@endsection
