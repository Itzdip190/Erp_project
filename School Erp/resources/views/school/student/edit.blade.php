@extends('layouts.app')

@section('page-title', 'Edit Student Record')

@section('content')

<style>
    .accordion-item {
        border: 1px solid var(--border);
        border-radius: 8px;
        margin-bottom: 16px;
        background: #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        overflow: hidden;
    }
    .accordion-header {
        padding: 16px 20px;
        background: var(--page);
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid transparent;
        transition: background 0.2s, border-color 0.2s;
        user-select: none;
    }
    .accordion-header:hover {
        background: rgba(212, 175, 55, 0.05);
    }
    .accordion-header h3 {
        margin: 0;
        font-size: 14.5px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: var(--navy);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .accordion-header h3 span.required-badge {
        color: var(--danger, #dc3545);
        margin-left: 2px;
    }
    .accordion-header .accordion-icon {
        transition: transform 0.3s;
        font-size: 13.5px;
        color: var(--t2);
    }
    .accordion-item.active .accordion-header {
        border-bottom-color: var(--border);
        background: rgba(212, 175, 55, 0.03);
    }
    .accordion-item.active .accordion-header .accordion-icon {
        transform: rotate(180deg);
    }
    .accordion-body {
        display: none;
        padding: 24px 20px;
    }
    .accordion-item.active .accordion-body {
        display: block;
    }
    
    /* Yes/No questions checklist styling */
    .checklist-question {
        background: var(--page);
        padding: 15px;
        border-radius: 6px;
        border: 1px solid var(--border);
        margin-bottom: 15px;
    }
    .checklist-question-label {
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 10px;
        font-size: 13px;
    }
    .radio-group {
        display: flex;
        gap: 15px;
        margin-bottom: 10px;
    }
    .radio-option {
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        font-size: 12.5px;
    }
    .radio-option input {
        accent-color: var(--gold);
    }
    .conditional-reason-box {
        display: none;
        margin-top: 10px;
    }
    
    /* Grid spacing tweaks */
    .form-group label span {
        color: var(--danger, #dc3545);
    }
</style>

<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-user-edit" style="color:var(--gold);margin-right:8px;"></i>Modify Student Profile</h1>
        <p>Edit student general details, class/section mapping, family background, previous education history, bank and health details</p>
    </div>
    <div class="page-hdr-right">
        <a href="{{ route('school.students.index') }}" class="btn btn-outline">
            <i class="fa fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<div class="card">
    <div class="card-hdr">
        <h3><i class="fas fa-user-graduate" style="color:var(--gold);margin-right:6px;"></i>Student profile details</h3>
    </div>
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <div>
                    <strong><i class="fa fa-exclamation-triangle"></i> Correct the following errors:</strong>
                    <ul style="list-style-position: inside; margin-top: 6px; font-size: 12.5px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('school.students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- 1. PERSONAL DETAILS -->
            <div class="accordion-item active">
                <div class="accordion-header">
                    <h3><i class="fas fa-user" style="color:var(--gold);"></i> Personal Details <span class="required-badge">*</span></h3>
                    <i class="fas fa-chevron-down accordion-icon"></i>
                </div>
                <div class="accordion-body">
                    <div style="display: flex; gap: 2rem; flex-wrap: wrap; align-items: flex-start;">
                        <!-- Avatar / Photo upload -->
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 12px; min-width: 150px; margin-top: 10px;">
                            <div id="avatarPreview" style="width: 120px; height: 120px; border-radius: 50%; border: 2px dashed var(--border); display: flex; align-items: center; justify-content: center; background-position: center; background-size: cover; overflow: hidden; color: var(--t3); background-color: var(--page); background-image: url('{{ $student->photo_url }}');">
                                @if(!$student->photo)
                                    <i class="fa fa-user" style="font-size: 3rem; color: var(--t3);"></i>
                                @endif
                            </div>
                            <label class="btn btn-outline" style="font-size: 11px; padding: 6px 12px; cursor: pointer;">
                                <i class="fa fa-camera"></i> Change Photo
                                <input type="file" name="photo" id="photoInput" style="display: none;" accept="image/*">
                            </label>
                        </div>

                        <!-- Main Personal fields -->
                        <div style="flex: 1;">
                            <div class="grid-3">
                                <div class="form-group">
                                    <label class="form-label">Admission ID (Read-only)</label>
                                    <input type="text" class="form-control" value="{{ $student->admission_number }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">First Name <span>*</span></label>
                                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $student->first_name) }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Last Name <span>*</span></label>
                                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $student->last_name) }}" required>
                                </div>
                            </div>
                            <div class="grid-3">
                                <div class="form-group">
                                    <label class="form-label">First Name Local</label>
                                    <input type="text" name="first_name_local" class="form-control" value="{{ old('first_name_local', $student->first_name_local) }}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Last Name Local</label>
                                    <input type="text" name="last_name_local" class="form-control" value="{{ old('last_name_local', $student->last_name_local) }}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Date of Birth <span>*</span></label>
                                    <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : '') }}" required>
                                </div>
                            </div>
                            <div class="grid-3">
                                <div class="form-group">
                                    <label class="form-label">Age as of today</label>
                                    <input type="text" name="age" class="form-control" placeholder="Calculated from Date of Birth" readonly>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Gender <span>*</span></label>
                                    <select name="gender" class="form-control" required>
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender', $student->gender) === 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $student->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender', $student->gender) === 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Student Phone / Mobile No</label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $student->phone) }}">
                                </div>
                            </div>
                            <div class="grid-3">
                                <div class="form-group">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $student->email) }}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Place of Birth</label>
                                    <input type="text" name="place_of_birth" class="form-control" value="{{ old('place_of_birth', $student->place_of_birth) }}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Birth Certificate No</label>
                                    <input type="text" name="birth_certificate_no" class="form-control" value="{{ old('birth_certificate_no', $student->birth_certificate_no) }}">
                                </div>
                            </div>
                            <div class="grid-3">
                                <div class="form-group">
                                    <label class="form-label">USN/SRN Number</label>
                                    <input type="text" name="usn_srn_number" class="form-control" value="{{ old('usn_srn_number', $student->usn_srn_number) }}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Aadhaar Number (National ID)</label>
                                    <input type="text" name="national_id" class="form-control" value="{{ old('national_id', $student->national_id) }}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Blood Group</label>
                                    <input type="text" name="blood_group" class="form-control" value="{{ old('blood_group', $student->blood_group) }}" placeholder="e.g. O+">
                                </div>
                            </div>
                            <div class="grid-3">
                                <div class="form-group">
                                    <label class="form-label">Student House</label>
                                    <select name="house_id" class="form-control">
                                        <option value="">Select House</option>
                                        @foreach($houses as $h)
                                            <option value="{{ $h->id }}" {{ old('house_id', $student->house_id) == $h->id ? 'selected' : '' }}>{{ $h->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">House Role</label>
                                    <input type="text" name="house_role" class="form-control" value="{{ old('house_role', $student->house_role) }}" placeholder="e.g. Captain">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Category</label>
                                    <select name="category_id" class="form-control">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ old('category_id', $student->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. ACADEMIC & FEE SCHEDULE DETAILS -->
            <div class="accordion-item active">
                <div class="accordion-header">
                    <h3><i class="fas fa-graduation-cap" style="color:var(--gold);"></i> Academic & Fee Schedule Details <span class="required-badge">*</span></h3>
                    <i class="fas fa-chevron-down accordion-icon"></i>
                </div>
                <div class="accordion-body">
                    <div class="grid-3">
                        <div class="form-group">
                            <label class="form-label">Class <span>*</span></label>
                            <select name="class_id" class="form-control" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $cls)
                                    <option value="{{ $cls->id }}" {{ old('class_id', $student->class_id) == $cls->id ? 'selected' : '' }}>{{ $cls->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Section <span>*</span></label>
                            <select name="section_id" class="form-control" required>
                                <option value="">Select Section</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Academic Year <span>*</span></label>
                            <select name="academic_session_id" class="form-control" required>
                                <option value="">Select Session</option>
                                @foreach($academicSessions as $ses)
                                    <option value="{{ $ses->id }}" {{ old('academic_session_id', $student->academic_session_id) == $ses->id ? 'selected' : '' }}>{{ $ses->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid-3">
                        <div class="form-group">
                            <label class="form-label">Date of Admission <span>*</span></label>
                            <input type="date" name="admission_date" class="form-control" value="{{ old('admission_date', $student->admission_date ? $student->admission_date->format('Y-m-d') : '') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Roll Number</label>
                            <input type="text" name="roll_number" class="form-control" value="{{ old('roll_number', $student->roll_number) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Biometric ID</label>
                            <input type="text" name="biometric_id" class="form-control" value="{{ old('biometric_id', $student->biometric_id) }}">
                        </div>
                    </div>
                    <div class="grid-3">
                        <div class="form-group">
                            <label class="form-label">PEN Number</label>
                            <input type="text" name="pen_number" class="form-control" value="{{ old('pen_number', $student->pen_number) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Apaar ID</label>
                            <input type="text" name="apaar_id" class="form-control" value="{{ old('apaar_id', $student->apaar_id) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Samagra ID</label>
                            <input type="text" name="samagra_id" class="form-control" value="{{ old('samagra_id', $student->samagra_id) }}">
                        </div>
                    </div>
                    <div class="grid-4">
                        <div class="form-group">
                            <label class="form-label">Class at time of Admission</label>
                            <input type="text" name="class_at_admission" class="form-control" value="{{ old('class_at_admission', $student->class_at_admission) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Enrollment Number</label>
                            <input type="text" name="enrollment_number" class="form-control" value="{{ old('enrollment_number', $student->enrollment_number) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">TC Number</label>
                            <input type="text" name="tc_number" class="form-control" value="{{ old('tc_number', $student->tc_number) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Opening Due Balance</label>
                            <input type="number" step="0.01" name="opening_due_balance" class="form-control" value="{{ old('opening_due_balance', $student->opening_due_balance) }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. TRANSPORT DETAILS -->
            <div class="accordion-item">
                <div class="accordion-header">
                    <h3><i class="fas fa-bus" style="color:var(--gold);"></i> Transport Details</h3>
                    <i class="fas fa-chevron-down accordion-icon"></i>
                </div>
                <div class="accordion-body">
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label class="form-label">Select Month</label>
                        <input type="text" name="transport_month" class="form-control" value="{{ old('transport_month', $student->transport_month) }}" placeholder="e.g. June 2026">
                    </div>
                    
                    <div style="display:flex; gap:30px; margin-bottom:20px;">
                        <label class="radio-option">
                            <input type="checkbox" id="pickup_enabled" {{ $student->transport_route || $student->transport_vehicle_code || $student->transport_stop ? 'checked' : '' }}> Pick Up
                        </label>
                        <label class="radio-option">
                            <input type="checkbox" id="drop_enabled" {{ $student->transport_drop_vehicle_code ? 'checked' : '' }}> Drop
                        </label>
                    </div>

                    <div id="pickup_fields" style="display:{{ $student->transport_route || $student->transport_vehicle_code || $student->transport_stop ? 'block' : 'none' }}; border-top:1px solid var(--border); padding-top:20px; margin-bottom:20px;">
                        <div class="grid-3">
                            <div class="form-group">
                                <label class="form-label">Select Route</label>
                                <input type="text" name="transport_route" class="form-control" value="{{ old('transport_route', $student->transport_route) }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Select Vehicle Code</label>
                                <input type="text" name="transport_vehicle_code" class="form-control" value="{{ old('transport_vehicle_code', $student->transport_vehicle_code) }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Select Stop</label>
                                <input type="text" name="transport_stop" class="form-control" value="{{ old('transport_stop', $student->transport_stop) }}">
                            </div>
                        </div>
                    </div>

                    <div id="drop_fields" style="display:{{ $student->transport_drop_vehicle_code ? 'block' : 'none' }}; border-top:1px solid var(--border); padding-top:20px;">
                        <div class="grid-3">
                            <div class="form-group">
                                <label class="form-label">Select Vehicle Code (Drop)</label>
                                <input type="text" name="transport_drop_vehicle_code" class="form-control" value="{{ old('transport_drop_vehicle_code', $student->transport_drop_vehicle_code) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. PREVIOUS SCHOOL/COLLEGE DETAILS -->
            <div class="accordion-item">
                <div class="accordion-header">
                    <h3><i class="fas fa-university" style="color:var(--gold);"></i> Previous School / College Details</h3>
                    <i class="fas fa-chevron-down accordion-icon"></i>
                </div>
                <div class="accordion-body">
                    <div class="grid-3">
                        <div class="form-group">
                            <label class="form-label">Previous School Name</label>
                            <input type="text" name="prev_school" class="form-control" value="{{ old('prev_school', $student->prev_school) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">City / Country</label>
                            <input type="text" name="prev_city_country" class="form-control" value="{{ old('prev_city_country', $student->prev_city_country) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Year Attended</label>
                            <input type="text" name="prev_year_attended" class="form-control" value="{{ old('prev_year_attended', $student->prev_year_attended) }}" placeholder="YYYY">
                        </div>
                    </div>
                    <div class="grid-3">
                        <div class="form-group">
                            <label class="form-label">Board</label>
                            <input type="text" name="prev_board" class="form-control" value="{{ old('prev_board', $student->prev_board) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Previous Registration Number</label>
                            <input type="text" name="prev_reg_no" class="form-control" value="{{ old('prev_reg_no', $student->prev_reg_no) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">PCM Marks</label>
                            <input type="text" name="prev_pcm_marks" class="form-control" value="{{ old('prev_pcm_marks', $student->prev_pcm_marks) }}">
                        </div>
                    </div>
                    <div class="grid-3">
                        <div class="form-group">
                            <label class="form-label">PCM %</label>
                            <input type="text" name="prev_pcm_percentage" class="form-control" value="{{ old('prev_pcm_percentage', $student->prev_pcm_percentage) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Total Marks</label>
                            <input type="text" name="prev_total_marks" class="form-control" value="{{ old('prev_total_marks', $student->prev_total_marks) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Average</label>
                            <input type="text" name="prev_average" class="form-control" value="{{ old('prev_average', $student->prev_average) }}">
                        </div>
                    </div>
                    <div class="grid-3" style="border-bottom:1px solid var(--border); padding-bottom:20px; margin-bottom:20px;">
                        <div class="form-group">
                            <label class="form-label">Entrance Exam Name</label>
                            <input type="text" name="entrance_exam_name" class="form-control" value="{{ old('entrance_exam_name', $student->entrance_exam_name) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Entrance Exam Rank</label>
                            <input type="text" name="entrance_exam_rank" class="form-control" value="{{ old('entrance_exam_rank', $student->entrance_exam_rank) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Entrance Exam Remarks</label>
                            <input type="text" name="entrance_exam_remarks" class="form-control" value="{{ old('entrance_exam_remarks', $student->entrance_exam_remarks) }}">
                        </div>
                    </div>

                    <!-- Questionnaire items -->
                    <div class="checklist-question">
                        <div class="checklist-question-label">Has the student ever been involved in any serious disciplinary action?</div>
                        <div class="radio-group">
                            <label class="radio-option"><input type="radio" name="disciplinary_action" value="1" {{ old('disciplinary_action', $student->disciplinary_action ? '1' : '0') == '1' ? 'checked' : '' }}> Yes</label>
                            <label class="radio-option"><input type="radio" name="disciplinary_action" value="0" {{ old('disciplinary_action', $student->disciplinary_action ? '1' : '0') == '0' ? 'checked' : '' }}> No</label>
                        </div>
                        <div class="form-group conditional-reason-box" id="disciplinary_action_box">
                            <label class="form-label">If yes, add the reason</label>
                            <input type="text" name="disciplinary_action_reason" class="form-control" value="{{ old('disciplinary_action_reason', $student->disciplinary_action_reason) }}">
                        </div>
                    </div>

                    <div class="checklist-question">
                        <div class="checklist-question-label">Has the student ever been asked to leave school?</div>
                        <div class="radio-group">
                            <label class="radio-option"><input type="radio" name="asked_to_leave" value="1" {{ old('asked_to_leave', $student->asked_to_leave ? '1' : '0') == '1' ? 'checked' : '' }}> Yes</label>
                            <label class="radio-option"><input type="radio" name="asked_to_leave" value="0" {{ old('asked_to_leave', $student->asked_to_leave ? '1' : '0') == '0' ? 'checked' : '' }}> No</label>
                        </div>
                        <div class="form-group conditional-reason-box" id="asked_to_leave_box">
                            <label class="form-label">If yes, add the reason</label>
                            <input type="text" name="asked_to_leave_reason" class="form-control" value="{{ old('asked_to_leave_reason', $student->asked_to_leave_reason) }}">
                        </div>
                    </div>

                    <div class="checklist-question">
                        <div class="checklist-question-label">Does the student have any special educational needs?</div>
                        <div class="radio-group">
                            <label class="radio-option"><input type="radio" name="special_needs" value="1" {{ old('special_needs', $student->special_needs ? '1' : '0') == '1' ? 'checked' : '' }}> Yes</label>
                            <label class="radio-option"><input type="radio" name="special_needs" value="0" {{ old('special_needs', $student->special_needs ? '1' : '0') == '0' ? 'checked' : '' }}> No</label>
                        </div>
                        <div class="form-group conditional-reason-box" id="special_needs_box">
                            <label class="form-label">If yes, add the reason</label>
                            <input type="text" name="special_needs_reason" class="form-control" value="{{ old('special_needs_reason', $student->special_needs_reason) }}">
                        </div>
                    </div>

                    <div class="checklist-question">
                        <div class="checklist-question-label">Does the student have any interests or talents?</div>
                        <div class="radio-group">
                            <label class="radio-option"><input type="radio" name="interests_talents" value="1" {{ old('interests_talents', $student->interests_talents ? '1' : '0') == '1' ? 'checked' : '' }}> Yes</label>
                            <label class="radio-option"><input type="radio" name="interests_talents" value="0" {{ old('interests_talents', $student->interests_talents ? '1' : '0') == '0' ? 'checked' : '' }}> No</label>
                        </div>
                        <div class="form-group conditional-reason-box" id="interests_talents_box">
                            <label class="form-label">If yes, add the reason</label>
                            <input type="text" name="interests_talents_reason" class="form-control" value="{{ old('interests_talents_reason', $student->interests_talents_reason) }}">
                        </div>
                    </div>

                    <div class="checklist-question">
                        <div class="checklist-question-label">Has the student represented his/her school in sports or any other events?</div>
                        <div class="radio-group">
                            <label class="radio-option"><input type="radio" name="represented_school" value="1" {{ old('represented_school', $student->represented_school ? '1' : '0') == '1' ? 'checked' : '' }}> Yes</label>
                            <label class="radio-option"><input type="radio" name="represented_school" value="0" {{ old('represented_school', $student->represented_school ? '1' : '0') == '0' ? 'checked' : '' }}> No</label>
                        </div>
                        <div class="form-group conditional-reason-box" id="represented_school_box">
                            <label class="form-label">If yes, add the reason</label>
                            <input type="text" name="represented_school_reason" class="form-control" value="{{ old('represented_school_reason', $student->represented_school_reason) }}">
                        </div>
                    </div>

                    <div class="checklist-question">
                        <div class="checklist-question-label">Other relevant information?</div>
                        <div class="radio-group">
                            <label class="radio-option"><input type="radio" name="other_info" value="1" {{ old('other_info', $student->other_info ? '1' : '0') == '1' ? 'checked' : '' }}> Yes</label>
                            <label class="radio-option"><input type="radio" name="other_info" value="0" {{ old('other_info', $student->other_info ? '1' : '0') == '0' ? 'checked' : '' }}> No</label>
                        </div>
                        <div class="form-group conditional-reason-box" id="other_info_box">
                            <label class="form-label">If yes, add the reason</label>
                            <input type="text" name="other_info_reason" class="form-control" value="{{ old('other_info_reason', $student->other_info_reason) }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. FAMILY DETAILS -->
            <div class="accordion-item active">
                <div class="accordion-header">
                    <h3><i class="fas fa-users" style="color:var(--gold);"></i> Family Details <span class="required-badge">*</span></h3>
                    <i class="fas fa-chevron-down accordion-icon"></i>
                </div>
                <div class="accordion-body">
                    <!-- Father Details -->
                    <div style="font-size:13px; font-weight:700; color:var(--navy); margin-bottom:15px; border-bottom:1px solid var(--border); padding-bottom:5px;">Father's Information</div>
                    <div class="grid-4" style="margin-bottom:20px;">
                        <div class="form-group">
                            <label class="form-label">Father Name</label>
                            <input type="text" name="father_name" class="form-control" value="{{ old('father_name', $student->father_name) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Father Mobile Number</label>
                            <input type="text" name="father_phone" class="form-control" value="{{ old('father_phone', $student->father_phone) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Father Alternate Number</label>
                            <input type="text" name="father_alternate_phone" class="form-control" value="{{ old('father_alternate_phone', $student->father_alternate_phone) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Father Email ID</label>
                            <input type="email" name="father_email" class="form-control" value="{{ old('father_email', $student->father_email) }}">
                        </div>
                    </div>
                    <div class="grid-4" style="margin-bottom:20px;">
                        <div class="form-group">
                            <label class="form-label">Father Occupation</label>
                            <input type="text" name="father_occupation" class="form-control" value="{{ old('father_occupation', $student->father_occupation) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Father's ID</label>
                            <input type="text" name="father_id" class="form-control" value="{{ old('father_id', $student->father_id) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Father's Aadhaar Number</label>
                            <input type="text" name="father_aadhar" class="form-control" value="{{ old('father_aadhar', $student->father_aadhar) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Father's Income</label>
                            <input type="text" name="father_income" class="form-control" value="{{ old('father_income', $student->father_income) }}">
                        </div>
                    </div>
                    <div class="grid-3" style="margin-bottom:25px;">
                        <div class="form-group">
                            <label class="form-label">Father Qualification</label>
                            <input type="text" name="father_qualification" class="form-control" value="{{ old('father_qualification', $student->father_qualification) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Father Passport Number</label>
                            <input type="text" name="father_passport" class="form-control" value="{{ old('father_passport', $student->father_passport) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Father Photo</label>
                            @if($student->father_photo)
                                <div style="margin-bottom:6px;"><a href="{{ Storage::url($student->father_photo) }}" target="_blank" class="btn btn-outline" style="padding:4px 8px;font-size:11px;"><i class="fa fa-eye"></i> View Current</a></div>
                            @endif
                            <input type="file" name="father_photo" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom:30px;">
                        <label class="form-label">Father's Home Address</label>
                        <input type="text" name="father_address" class="form-control" value="{{ old('father_address', $student->father_address) }}">
                    </div>

                    <!-- Mother Details -->
                    <div style="font-size:13px; font-weight:700; color:var(--navy); margin-bottom:15px; border-bottom:1px solid var(--border); padding-bottom:5px;">Mother's Information</div>
                    <div class="grid-4" style="margin-bottom:20px;">
                        <div class="form-group">
                            <label class="form-label">Mother Name</label>
                            <input type="text" name="mother_name" class="form-control" value="{{ old('mother_name', $student->mother_name) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Mother Mobile Number</label>
                            <input type="text" name="mother_phone" class="form-control" value="{{ old('mother_phone', $student->mother_phone) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Mother Alternate Number</label>
                            <input type="text" name="mother_alternate_phone" class="form-control" value="{{ old('mother_alternate_phone', $student->mother_alternate_phone) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Mother Email ID</label>
                            <input type="email" name="mother_email" class="form-control" value="{{ old('mother_email', $student->mother_email) }}">
                        </div>
                    </div>
                    <div class="grid-4" style="margin-bottom:20px;">
                        <div class="form-group">
                            <label class="form-label">Mother Occupation</label>
                            <input type="text" name="mother_occupation" class="form-control" value="{{ old('mother_occupation', $student->mother_occupation) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Mother's ID</label>
                            <input type="text" name="mother_id" class="form-control" value="{{ old('mother_id', $student->mother_id) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Mother's Aadhaar Number</label>
                            <input type="text" name="mother_aadhar" class="form-control" value="{{ old('mother_aadhar', $student->mother_aadhar) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Mother's Income</label>
                            <input type="text" name="mother_income" class="form-control" value="{{ old('mother_income', $student->mother_income) }}">
                        </div>
                    </div>
                    <div class="grid-3" style="margin-bottom:25px;">
                        <div class="form-group">
                            <label class="form-label">Mother Qualification</label>
                            <input type="text" name="mother_qualification" class="form-control" value="{{ old('mother_qualification', $student->mother_qualification) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Mother Passport Number</label>
                            <input type="text" name="mother_passport" class="form-control" value="{{ old('mother_passport', $student->mother_passport) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Mother Photo</label>
                            @if($student->mother_photo)
                                <div style="margin-bottom:6px;"><a href="{{ Storage::url($student->mother_photo) }}" target="_blank" class="btn btn-outline" style="padding:4px 8px;font-size:11px;"><i class="fa fa-eye"></i> View Current</a></div>
                            @endif
                            <input type="file" name="mother_photo" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div class="grid-2" style="margin-bottom:30px;">
                        <div class="form-group">
                            <label class="form-label">Mother's Home Address</label>
                            <input type="text" name="mother_address" class="form-control" value="{{ old('mother_address', $student->mother_address) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Mother's Office Address</label>
                            <input type="text" name="mother_office_address" class="form-control" value="{{ old('mother_office_address', $student->mother_office_address) }}">
                        </div>
                    </div>

                    <!-- Additional Family metadata -->
                    <div style="font-size:13px; font-weight:700; color:var(--navy); margin-bottom:15px; border-bottom:1px solid var(--border); padding-bottom:5px;">Other Family Metadata</div>
                    <div class="grid-4">
                        <div class="form-group">
                            <label class="form-label">Preferred WhatsApp Number</label>
                            <input type="text" name="whatsapp_number" class="form-control" value="{{ old('whatsapp_number', $student->whatsapp_number) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Religion</label>
                            <input type="text" name="religion" class="form-control" value="{{ old('religion', $student->religion) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Caste</label>
                            <input type="text" name="caste" class="form-control" value="{{ old('caste', $student->caste) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Sub Caste</label>
                            <input type="text" name="sub_caste" class="form-control" value="{{ old('sub_caste', $student->sub_caste) }}">
                        </div>
                    </div>
                    <div class="grid-4">
                        <div class="form-group">
                            <label class="form-label">Family ID</label>
                            <input type="text" name="family_id" class="form-control" value="{{ old('family_id', $student->family_id) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Student Group</label>
                            <select name="group" class="form-control">
                                <option value="">Select Group</option>
                                <option value="Science" {{ old('group', $student->group) === 'Science' ? 'selected' : '' }}>Science</option>
                                <option value="Commerce" {{ old('group', $student->group) === 'Commerce' ? 'selected' : '' }}>Commerce</option>
                                <option value="Arts" {{ old('group', $student->group) === 'Arts' ? 'selected' : '' }}>Arts</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 6. GUARDIAN DETAILS -->
            <div class="accordion-item active">
                <div class="accordion-header">
                    <h3><i class="fas fa-user-shield" style="color:var(--gold);"></i> Guardian Details <span class="required-badge">*</span></h3>
                    <i class="fas fa-chevron-down accordion-icon"></i>
                </div>
                <div class="accordion-body">
                    <div class="grid-3">
                        <div class="form-group">
                            <label class="form-label">Guardian Name <span>*</span></label>
                            <input type="text" name="guardian_name" class="form-control" value="{{ old('guardian_name', $student->guardian_name) }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Guardian Name (Local)</label>
                            <input type="text" name="guardian_name_local" class="form-control" value="{{ old('guardian_name_local', $student->guardian_name_local) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Guardian Mobile Number <span>*</span></label>
                            <input type="text" name="guardian_phone" class="form-control" value="{{ old('guardian_phone', $student->guardian_phone) }}" required>
                        </div>
                    </div>
                    <div class="grid-3">
                        <div class="form-group">
                            <label class="form-label">Relationship <span>*</span></label>
                            <select name="guardian_relationship" class="form-control" required>
                                <option value="">Select Relation</option>
                                <option value="father" {{ old('guardian_relationship', $student->guardian_relationship) === 'father' ? 'selected' : '' }}>Father</option>
                                <option value="mother" {{ old('guardian_relationship', $student->guardian_relationship) === 'mother' ? 'selected' : '' }}>Mother</option>
                                <option value="guardian" {{ old('guardian_relationship', $student->guardian_relationship) === 'guardian' ? 'selected' : '' }}>Other Guardian</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Guardian Email</label>
                            <input type="email" name="guardian_email" class="form-control" value="{{ old('guardian_email', $student->guardian_email) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Guardian Occupation</label>
                            <input type="text" name="guardian_occupation" class="form-control" value="{{ old('guardian_occupation', $student->guardian_occupation) }}">
                        </div>
                    </div>
                    <div class="grid-3">
                        <div class="form-group">
                            <label class="form-label">Guardian Passport Number</label>
                            <input type="text" name="guardian_passport" class="form-control" value="{{ old('guardian_passport', $student->guardian_passport) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Guardian Photo</label>
                            @if($student->guardian_photo)
                                <div style="margin-bottom:6px;"><a href="{{ Storage::url($student->guardian_photo) }}" target="_blank" class="btn btn-outline" style="padding:4px 8px;font-size:11px;"><i class="fa fa-eye"></i> View Current</a></div>
                            @endif
                            <input type="file" name="guardian_photo" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div class="form-group" style="margin-top:15px;">
                        <label class="form-label">Guardian Address</label>
                        <input type="text" name="guardian_address" class="form-control" value="{{ old('guardian_address', $student->guardian_address) }}">
                    </div>
                </div>
            </div>

            <!-- 7. EMERGENCY CONTACT DETAILS -->
            <div class="accordion-item">
                <div class="accordion-header">
                    <h3><i class="fas fa-ambulance" style="color:var(--gold);"></i> Emergency Contact Details</h3>
                    <i class="fas fa-chevron-down accordion-icon"></i>
                </div>
                <div class="accordion-body">
                    <div class="form-group">
                        <label class="form-label">Emergency Address</label>
                        <input type="text" name="emergency_address" class="form-control" value="{{ old('emergency_address', $student->emergency_address) }}">
                    </div>
                </div>
            </div>

            <!-- 8. COMMUNICATION WITH SCHOOL -->
            <div class="accordion-item">
                <div class="accordion-header">
                    <h3><i class="fas fa-comment-alt" style="color:var(--gold);"></i> Communication With School</h3>
                    <i class="fas fa-chevron-down accordion-icon"></i>
                </div>
                <div class="accordion-body">
                    <div class="form-group">
                        <label class="form-label" style="font-weight:600; margin-bottom:10px;">Priority to contact for school matters</label>
                        <div class="radio-group">
                            <label class="radio-option"><input type="radio" name="contact_priority" value="father" {{ old('contact_priority', $student->contact_priority) === 'father' ? 'checked' : '' }}> Father</label>
                            <label class="radio-option"><input type="radio" name="contact_priority" value="mother" {{ old('contact_priority', $student->contact_priority) === 'mother' ? 'checked' : '' }}> Mother</label>
                            <label class="radio-option"><input type="radio" name="contact_priority" value="guardian" {{ old('contact_priority', $student->contact_priority ?? 'guardian') === 'guardian' ? 'checked' : '' }}> Guardian</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 9. ADDRESS DETAILS -->
            <div class="accordion-item active">
                <div class="accordion-header">
                    <h3><i class="fas fa-home" style="color:var(--gold);"></i> Address Details <span class="required-badge">*</span></h3>
                    <i class="fas fa-chevron-down accordion-icon"></i>
                </div>
                <div class="accordion-body">
                    <!-- Current Address -->
                    <div style="font-size:13px; font-weight:700; color:var(--navy); margin-bottom:15px; border-bottom:1px solid var(--border); padding-bottom:5px;">Current Address</div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Address Line 1 <span>*</span></label>
                            <input type="text" name="address" id="current_address" class="form-control" value="{{ old('address', $student->address) }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Address Line 2</label>
                            <input type="text" name="address_line_2" id="current_address_line_2" class="form-control" value="{{ old('address_line_2', $student->address_line_2) }}">
                        </div>
                    </div>
                    <div class="grid-4">
                        <div class="form-group">
                            <label class="form-label">City <span>*</span></label>
                            <input type="text" name="city" id="current_city" class="form-control" value="{{ old('city', $student->city) }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">State <span>*</span></label>
                            <input type="text" name="state" id="current_state" class="form-control" value="{{ old('state', $student->state) }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Country</label>
                            <input type="text" name="country" id="current_country" class="form-control" value="{{ old('country', $student->country ?? 'India') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Pin Code <span>*</span></label>
                            <input type="text" name="pincode" id="current_pincode" class="form-control" value="{{ old('pincode', $student->pincode) }}" required>
                        </div>
                    </div>
                    <div class="grid-3" style="margin-bottom:30px;">
                        <div class="form-group">
                            <label class="form-label">Region</label>
                            <input type="text" name="region" id="current_region" class="form-control" value="{{ old('region', $student->region) }}">
                        </div>
                    </div>

                    <!-- Permanent Address Toggle -->
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:15px; border-bottom:1px solid var(--border); padding-bottom:5px;">
                        <span style="font-size:13px; font-weight:700; color:var(--navy);">Permanent Address</span>
                        <label style="font-size:12px; display:inline-flex; align-items:center; gap:4px; cursor:pointer; color:var(--t2);">
                            <input type="checkbox" id="same_address_chk" onclick="copyAddress()" style="accent-color:var(--gold);"> Same as Current Address
                        </label>
                    </div>

                    <!-- Permanent Address -->
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Address Line 1</label>
                            <input type="text" name="permanent_address" id="perm_address" class="form-control" value="{{ old('permanent_address', $student->permanent_address) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Address Line 2</label>
                            <input type="text" name="permanent_address_line_2" id="perm_address_line_2" class="form-control" value="{{ old('permanent_address_line_2', $student->permanent_address_line_2) }}">
                        </div>
                    </div>
                    <div class="grid-4">
                        <div class="form-group">
                            <label class="form-label">City</label>
                            <input type="text" name="permanent_city" id="perm_city" class="form-control" value="{{ old('permanent_city', $student->permanent_city) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">State</label>
                            <input type="text" name="permanent_state" id="perm_state" class="form-control" value="{{ old('permanent_state', $student->permanent_state) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Country</label>
                            <input type="text" name="permanent_country" id="perm_country" class="form-control" value="{{ old('permanent_country', $student->permanent_country) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Pin Code</label>
                            <input type="text" name="permanent_pincode" id="perm_pincode" class="form-control" value="{{ old('permanent_pincode', $student->permanent_pincode) }}">
                        </div>
                    </div>
                    <div class="grid-3">
                        <div class="form-group">
                            <label class="form-label">Region</label>
                            <input type="text" name="permanent_region" id="perm_region" class="form-control" value="{{ old('permanent_region', $student->permanent_region) }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 10. BANK DETAILS -->
            <div class="accordion-item">
                <div class="accordion-header">
                    <h3><i class="fas fa-university" style="color:var(--gold);"></i> Bank Details</h3>
                    <i class="fas fa-chevron-down accordion-icon"></i>
                </div>
                <div class="accordion-body">
                    <div class="grid-3">
                        <div class="form-group">
                            <label class="form-label">Bank Account No</label>
                            <input type="text" name="bank_account_no" class="form-control" value="{{ old('bank_account_no', $student->bank_account_no) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Bank Account Holder</label>
                            <input type="text" name="bank_account_holder" class="form-control" value="{{ old('bank_account_holder', $student->bank_account_holder) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $student->bank_name) }}">
                        </div>
                    </div>
                    <div class="grid-3">
                        <div class="form-group">
                            <label class="form-label">Bank Branch</label>
                            <input type="text" name="bank_branch" class="form-control" value="{{ old('bank_branch', $student->bank_branch) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">IFSC Code</label>
                            <input type="text" name="ifsc_code" class="form-control" value="{{ old('ifsc_code', $student->ifsc_code) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Bank MICR</label>
                            <input type="text" name="bank_micr" class="form-control" value="{{ old('bank_micr', $student->bank_micr) }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Additional Note / Instruction</label>
                        <textarea name="note" class="form-control" rows="3">{{ old('note', $student->note) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- 11. MEDICAL HEALTH RECORD -->
            <div class="accordion-item">
                <div class="accordion-header">
                    <h3><i class="fas fa-heartbeat" style="color:var(--gold);"></i> Medical Health Record</h3>
                    <i class="fas fa-chevron-down accordion-icon"></i>
                </div>
                <div class="accordion-body">
                    <div class="grid-4">
                        <div class="form-group">
                            <label class="form-label">Height (cm)</label>
                            <input type="text" name="medical_height" class="form-control" value="{{ old('medical_height', $student->medical_height) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Weight (kg)</label>
                            <input type="text" name="medical_weight" class="form-control" value="{{ old('medical_weight', $student->medical_weight) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Vision (Left)</label>
                            <input type="text" name="medical_vision_left" class="form-control" value="{{ old('medical_vision_left', $student->medical_vision_left) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Vision (Right)</label>
                            <input type="text" name="medical_vision_right" class="form-control" value="{{ old('medical_vision_right', $student->medical_vision_right) }}">
                        </div>
                    </div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Dental Condition</label>
                            <input type="text" name="medical_dental" class="form-control" value="{{ old('medical_dental', $student->medical_dental) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Chronic Illness (if any)</label>
                            <input type="text" name="medical_illness" class="form-control" value="{{ old('medical_illness', $student->medical_illness) }}">
                        </div>
                    </div>
                    <div class="grid-3">
                        <div class="form-group">
                            <label class="form-label">Medical History</label>
                            <textarea name="medical_history" class="form-control" rows="2">{{ old('medical_history', $student->medical_history) }}</textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Allergies</label>
                            <textarea name="medical_allergies" class="form-control" rows="2">{{ old('medical_allergies', $student->medical_allergies) }}</textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Disabilities</label>
                            <textarea name="medical_disabilities" class="form-control" rows="2">{{ old('medical_disabilities', $student->medical_disabilities) }}</textarea>
                        </div>
                    </div>
                    
                    <div style="font-size:13px; font-weight:700; color:var(--navy); margin-top:20px; margin-bottom:15px; border-bottom:1px solid var(--border); padding-bottom:5px;">Family Doctor Details</div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Doctor's Name</label>
                            <input type="text" name="medical_doctor_name" class="form-control" value="{{ old('medical_doctor_name', $student->medical_doctor_name) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Doctor's Phone Number</label>
                            <input type="text" name="medical_doctor_phone" class="form-control" value="{{ old('medical_doctor_phone', $student->medical_doctor_phone) }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Doctor's Clinic Address</label>
                        <input type="text" name="medical_doctor_address" class="form-control" value="{{ old('medical_doctor_address', $student->medical_doctor_address) }}">
                    </div>
                </div>
            </div>

            <!-- SAVE CHANGES BUTTON -->
            <div style="margin-top:30px;">
                <button type="submit" class="btn btn-gold" style="width: 100%; justify-content: center; padding: 12px; font-size: 13.5px;">
                    <i class="fa fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Accordion Toggle
    $('.accordion-header').on('click', function() {
        let item = $(this).closest('.accordion-item');
        item.toggleClass('active');
        item.find('.accordion-body').slideToggle(200);
    });

    // Preview avatar image
    $('#photoInput').on('change', function(e) {
        let reader = new FileReader();
        reader.onload = function(e) {
            $('#avatarPreview').html('').css('background-image', 'url(' + e.target.result + ')');
        }
        reader.readAsDataURL(this.files[0]);
    });

    // Address copy logic
    function copyAddress() {
        if ($('#same_address_chk').is(':checked')) {
            $('#perm_address').val($('#current_address').val());
            $('#perm_address_line_2').val($('#current_address_line_2').val());
            $('#perm_city').val($('#current_city').val());
            $('#perm_state').val($('#current_state').val());
            $('#perm_country').val($('#current_country').val());
            $('#perm_pincode').val($('#current_pincode').val());
            $('#perm_region').val($('#current_region').val());
        } else {
            $('#perm_address').val('');
            $('#perm_address_line_2').val('');
            $('#perm_city').val('');
            $('#perm_state').val('');
            $('#perm_country').val('');
            $('#perm_pincode').val('');
            $('#perm_region').val('');
        }
    }

    // Transport toggles
    $('#pickup_enabled').on('change', function() {
        if ($(this).is(':checked')) {
            $('#pickup_fields').slideDown(200);
        } else {
            $('#pickup_fields').slideUp(200);
        }
    });
    $('#drop_enabled').on('change', function() {
        if ($(this).is(':checked')) {
            $('#drop_fields').slideDown(200);
        } else {
            $('#drop_fields').slideUp(200);
        }
    });

    // Age calculation
    $('input[name="date_of_birth"]').on('change', function() {
        let dob = $(this).val();
        if (dob) {
            let birthDate = new Date(dob);
            let today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            let m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            $('input[name="age"]').val(age >= 0 ? age : 0);
        } else {
            $('input[name="age"]').val('');
        }
    });

    // Trigger age calculation initially
    let dobVal = $('input[name="date_of_birth"]').val();
    if (dobVal) {
        $('input[name="date_of_birth"]').trigger('change');
    }

    // Questionnaire conditional boxes
    function setupQuestionnaireToggle(radioName, boxId) {
        $(`input[name="${radioName}"]`).on('change', function() {
            if ($(this).val() == '1') {
                $(`#${boxId}`).slideDown(200);
            } else {
                $(`#${boxId}`).slideUp(200);
            }
        });
        // Initial state
        if ($(`input[name="${radioName}"]:checked`).val() == '1') {
            $(`#${boxId}`).show();
        } else {
            $(`#${boxId}`).hide();
        }
    }

    setupQuestionnaireToggle('disciplinary_action', 'disciplinary_action_box');
    setupQuestionnaireToggle('asked_to_leave', 'asked_to_leave_box');
    setupQuestionnaireToggle('special_needs', 'special_needs_box');
    setupQuestionnaireToggle('interests_talents', 'interests_talents_box');
    setupQuestionnaireToggle('represented_school', 'represented_school_box');
    setupQuestionnaireToggle('other_info', 'other_info_box');

    // Section class filter logic
    const allSections = @json($sections);
    const initialClassId = "{{ old('class_id', $student->class_id) }}";
    const initialSectionId = "{{ old('section_id', $student->section_id) }}";

    function filterSections(classId, selectedSectionId = null) {
        let sectionSelect = $('select[name="section_id"]');
        sectionSelect.empty().append('<option value="">Select Section</option>');

        if (classId) {
            let filtered = allSections.filter(s => s.class_id == classId);
            filtered.forEach(function(sec) {
                let isSelected = selectedSectionId == sec.id ? 'selected' : '';
                sectionSelect.append('<option value="' + sec.id + '" ' + isSelected + '>' + sec.name + '</option>');
            });
        }
    }

    $('select[name="class_id"]').on('change', function() {
        filterSections($(this).val());
    });

    // Run section mapping on page load
    if (initialClassId) {
        filterSections(initialClassId, initialSectionId);
    }
</script>
@endsection
