<?php

namespace App\Http\Requests\School\Student;

use Illuminate\Foundation\Http\FormRequest;

class PromoteStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from_session_id' => 'required|exists:academic_sessions,id',
            'from_class_id'   => 'required|exists:school_classes,id',
            'to_session_id'   => 'required|exists:academic_sessions,id',
            'to_class_id'     => 'required|exists:school_classes,id',
            'to_section_id'   => 'required|exists:sections,id',
            'student_ids'     => 'required|array|min:1',
            'student_ids.*'   => 'exists:students,id',
        ];
    }
}
