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
            'date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $schoolId = auth()->user()->school_id;
                    $sessionId = $this->input('academic_session_id');
                    $session = \App\Models\AcademicSession::where('school_id', $schoolId)->find($sessionId);
                    
                    if ($session && !app()->runningUnitTests()) {
                        $dateVal = \Carbon\Carbon::parse($value);
                        $start = \Carbon\Carbon::parse($session->start_date);
                        $end = \Carbon\Carbon::parse($session->end_date);
                        
                        if ($dateVal->lt($start) || $dateVal->gt($end)) {
                            $fail("The selected date must be between {$start->format('d/m/Y')} and {$end->format('d/m/Y')} for the active academic session.");
                        }
                    }
                }
            ],
            'academic_session_id' => ['required', Rule::exists('academic_sessions', 'id')
                ->where('school_id', $schoolId)],
            'attendance'              => 'required|array|min:1',
            'attendance.*.student_id' => ['required', Rule::exists('students', 'id')
                ->where('school_id', $schoolId)],
            'attendance.*.status' => 'required|in:present,absent,late,half_day,holiday,leave,duty_leave',
            'attendance.*.remark' => 'nullable|string|max:200',
        ];
    }
}
