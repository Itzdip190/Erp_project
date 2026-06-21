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

                    <div class="st-grid-3">
                        <div class="st-form-group">
                            <label class="st-label">Alternate Mobile</label>
                            <input type="text" name="additional_fields[alternate_phone]" class="st-control" value="{{ old('additional_fields.alternate_phone') }}" placeholder="Alternate mobile number">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="st-control" value="{{ old('date_of_birth') }}">
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
                            <input type="text" name="additional_fields[religion]" class="st-control" placeholder="e.g. Hinduism, Christianity">
                        </div>
                    </div>

                    <div class="st-grid-4">
                        <div class="st-form-group">
                            <label class="st-label">Mother Tongue</label>
                            <input type="text" name="additional_fields[mother_tongue]" class="st-control" placeholder="e.g. English, Hindi">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">PAN Number</label>
                            <input type="text" name="pan_number" class="st-control" placeholder="e.g. ABCDE1234F">
                        </div>
                        <div class="st-form-group">
                            <label class="st-label">Aadhar Number</label>
                            <input type="text" name="additional_fields[aadhar_number]" class="st-control" placeholder="12-digit Aadhar number">
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
