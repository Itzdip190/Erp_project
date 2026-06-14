<?php

namespace App\Http\Requests\School\Attendance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttendanceMarkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $schoolId = auth()->user()->school_id;
        return [
            'section_id' => ['required', Rule::exists('sections', 'id')
                ->where('school_id', $schoolId)],
            'date'               => 'required|date|before_or_equal:today',
            'academic_session_id' => ['required', Rule::exists('academic_sessions', 'id')
                ->where('school_id', $schoolId)],
            'attendance'              => 'required|array|min:1',
            'attendance.*.student_id' => ['required', Rule::exists('students', 'id')
                ->where('school_id', $schoolId)],
            'attendance.*.status' => 'required|in:present,absent,late,half_day,holiday,leave',
            'attendance.*.remark' => 'nullable|string|max:200',
        ];
    }
}
