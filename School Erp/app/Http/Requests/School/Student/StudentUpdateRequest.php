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
            'date_of_birth'  => 'required|date|before:today',
            'gender'         => 'required|in:male,female,other',
            'guardian_name'  => 'required|string|max:150',
            'guardian_phone' => 'required|digits:10',
            'guardian_email' => 'nullable|email',
            'guardian_relationship' => 'required|in:father,mother,guardian',
            'address'        => 'required|string',
            'city'           => 'required|string|max:100',
            'state'          => 'required|string|max:100',
            'pincode'        => 'required|string|max:20',
            'admission_date' => 'required|date',
            'photo'          => 'nullable|image|max:2048',
            'roll_number'    => 'nullable|string|max:50',
            'opening_due_balance' => 'nullable|numeric|min:0',

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
        ];
    }
}
