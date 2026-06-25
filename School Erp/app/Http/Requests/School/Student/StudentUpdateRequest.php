<?php

namespace App\Http\Requests\School\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $schoolId = auth()->user()->school_id;
        return [
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'first_name_local' => 'nullable|string|max:100',
            'last_name_local'  => 'nullable|string|max:100',
            'email'          => 'nullable|email|max:150',
            'phone'          => 'nullable|string|max:20',
            'date_of_birth'  => 'required|date|before:today',
            'gender'         => 'required|in:male,female,other',
            'place_of_birth' => 'nullable|string|max:150',
            'birth_certificate_no' => 'nullable|string|max:100',
            'usn_srn_number' => 'nullable|string|max:100',
            'blood_group'    => 'nullable|string|max:10',
            'religion'       => 'nullable|string|max:100',
            'caste'          => 'nullable|string|max:100',
            'sub_caste'      => 'nullable|string|max:100',
            'family_id'      => 'nullable|string|max:100',
            
            'biometric_id'   => 'nullable|string|max:100',
            'pen_number'     => 'nullable|string|max:100',
            'apaar_id'       => 'nullable|string|max:100',
            'samagra_id'     => 'nullable|string|max:100',
            'class_at_admission' => 'nullable|string|max:100',
            'enrollment_number' => 'nullable|string|max:100',
            'tc_number'      => 'nullable|string|max:100',

            'transport_month' => 'nullable|string|max:100',
            'transport_route' => 'nullable|string|max:150',
            'transport_vehicle_code' => 'nullable|string|max:100',
            'transport_stop'  => 'nullable|string|max:150',
            'transport_drop_vehicle_code' => 'nullable|string|max:100',

            'prev_school' => 'nullable|string|max:200',
            'prev_city_country' => 'nullable|string|max:150',
            'prev_year_attended' => 'nullable|string|max:50',
            'prev_board' => 'nullable|string|max:150',
            'prev_reg_no' => 'nullable|string|max:100',
            'prev_pcm_marks' => 'nullable|string|max:50',
            'prev_pcm_percentage' => 'nullable|string|max:50',
            'prev_total_marks' => 'nullable|string|max:50',
            'prev_average' => 'nullable|string|max:50',
            'entrance_exam_name' => 'nullable|string|max:150',
            'entrance_exam_rank' => 'nullable|string|max:50',
            'entrance_exam_remarks' => 'nullable|string',
            'disciplinary_action' => 'nullable|boolean',
            'disciplinary_action_reason' => 'nullable|string',
            'asked_to_leave' => 'nullable|boolean',
            'asked_to_leave_reason' => 'nullable|string',
            'special_needs' => 'nullable|boolean',
            'special_needs_reason' => 'nullable|string',
            'interests_talents' => 'nullable|boolean',
            'interests_talents_reason' => 'nullable|string',
            'represented_school' => 'nullable|boolean',
            'represented_school_reason' => 'nullable|string',
            'other_info' => 'nullable|boolean',
            'other_info_reason' => 'nullable|string',

            'father_name'    => 'nullable|string|max:150',
            'father_phone'   => 'nullable|string|max:20',
            'father_alternate_phone' => 'nullable|string|max:20',
            'father_email'   => 'nullable|email|max:150',
            'father_occupation' => 'nullable|string|max:150',
            'father_id'      => 'nullable|string|max:100',
            'father_aadhar'  => 'nullable|string|max:50',
            'father_income'  => 'nullable|string|max:100',
            'father_qualification' => 'nullable|string|max:150',
            'father_passport' => 'nullable|string|max:100',
            'father_address' => 'nullable|string',
            'father_photo'   => 'nullable|image|max:2048',
            
            'mother_name'    => 'nullable|string|max:150',
            'mother_phone'   => 'nullable|string|max:20',
            'mother_alternate_phone' => 'nullable|string|max:20',
            'mother_email'   => 'nullable|email|max:150',
            'mother_occupation' => 'nullable|string|max:150',
            'mother_id'      => 'nullable|string|max:100',
            'mother_aadhar'  => 'nullable|string|max:50',
            'mother_income'  => 'nullable|string|max:100',
            'mother_qualification' => 'nullable|string|max:150',
            'mother_passport' => 'nullable|string|max:100',
            'mother_address' => 'nullable|string',
            'mother_office_address' => 'nullable|string',
            'mother_photo'   => 'nullable|image|max:2048',
            
            'guardian_name'  => 'required|string|max:150',
            'guardian_phone' => 'required|digits:10',
            'guardian_email' => 'nullable|email',
            'guardian_relationship' => 'required|in:father,mother,guardian',
            'guardian_occupation' => 'nullable|string|max:150',
            'guardian_photo' => 'nullable|image|max:2048',
            'guardian_passport' => 'nullable|string|max:100',
            'guardian_name_local' => 'nullable|string|max:150',
            'guardian_address' => 'nullable|string',
            
            'whatsapp_number' => 'nullable|string|max:20',

            'address'        => 'required|string',
            'address_line_2' => 'nullable|string|max:200',
            'city'           => 'required|string|max:100',
            'state'          => 'required|string|max:100',
            'country'        => 'nullable|string|max:100',
            'pincode'        => 'required|string|max:20',
            'region'         => 'nullable|string|max:100',

            'permanent_address' => 'nullable|string',
            'permanent_address_line_2' => 'nullable|string|max:200',
            'permanent_city' => 'nullable|string|max:100',
            'permanent_state' => 'nullable|string|max:100',
            'permanent_country' => 'nullable|string|max:100',
            'permanent_pincode' => 'nullable|string|max:20',
            'permanent_region'  => 'nullable|string|max:100',

            'admission_date' => 'required|date',
            'photo'          => 'nullable|image|max:2048',
            'roll_number'    => 'nullable|string|max:50',
            'opening_due_balance' => 'nullable|numeric|min:0',
            'national_id'    => 'nullable|string|max:50',
            'local_id'       => 'nullable|string|max:50',
            'bank_account_no' => 'nullable|string|max:50',
            'bank_account_holder' => 'nullable|string|max:150',
            'bank_name'      => 'nullable|string|max:150',
            'bank_branch'    => 'nullable|string|max:150',
            'ifsc_code'      => 'nullable|string|max:20',
            'bank_micr'      => 'nullable|string|max:50',
            'note'           => 'nullable|string',
            
            'emergency_address' => 'nullable|string',
            'contact_priority'  => 'nullable|string|max:50',

            'medical_height'       => 'nullable|string|max:50',
            'medical_weight'       => 'nullable|string|max:50',
            'medical_vision_left'  => 'nullable|string|max:50',
            'medical_vision_right' => 'nullable|string|max:50',
            'medical_dental'       => 'nullable|string|max:100',
            'medical_illness'      => 'nullable|string|max:150',
            'medical_history'      => 'nullable|string',
            'medical_allergies'    => 'nullable|string',
            'medical_disabilities' => 'nullable|string',
            'medical_doctor_name'  => 'nullable|string|max:150',
            'medical_doctor_phone' => 'nullable|string|max:50',
            'medical_doctor_address' => 'nullable|string',

            'class_id' => ['required', Rule::exists('school_classes', 'id')
                ->where('school_id', $schoolId)],
            'section_id' => ['required', Rule::exists('sections', 'id')
                ->where('school_id', $schoolId)],
            'academic_session_id' => ['required', Rule::exists('academic_sessions', 'id')
                ->where('school_id', $schoolId)],
            'category_id' => ['nullable', Rule::exists('student_categories', 'id')
                ->where('school_id', $schoolId)],
            'house_id' => ['nullable', Rule::exists('student_houses', 'id')
                ->where('school_id', $schoolId)],
            'house_role' => 'nullable|string|max:100',
            'group' => 'nullable|string|max:100',
        ];
    }
}
