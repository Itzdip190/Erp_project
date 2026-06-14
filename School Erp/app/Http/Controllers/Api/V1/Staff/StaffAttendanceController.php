<?php

namespace App\Http\Controllers\Api\V1\Staff;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\SectionSubjectStaff;
use App\Models\Staff;
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'section_id' => 'required|exists:sections,id',
            'date' => 'required|date',
        ]);

        $user = auth()->user();
        $sectionId = $request->section_id;
        $date = $request->date;

        if ($user->hasRole('teacher')) {
            $staff = Staff::where('user_id', $user->id)->first();
            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff profile not found.',
                ], 403);
            }

            // Verify teacher assignment to section
            $isAssigned = SectionSubjectStaff::where('staff_id', $staff->id)
                ->where('section_id', $sectionId)
                ->exists();

            if (!$isAssigned) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. You are not assigned to this section.',
                ], 403);
            }
        }

        $students = Student::where('section_id', $sectionId)
            ->where('is_active', true)
            ->orderBy('roll_number')
            ->get();

        $records = StudentAttendance::where('section_id', $sectionId)
            ->where('date', $date)
            ->get()
            ->keyBy('student_id');

        $data = $students->map(function ($student) use ($records) {
            $record = $records->get($student->id);
            return [
                'student_id' => $student->id,
                'roll_number' => $student->roll_number,
                'full_name' => $student->full_name,
                'status' => $record ? $record->status : 'none',
                'remark' => $record?->remark,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $schoolId = $user->school_id;

        $request->validate([
            'section_id' => 'required|exists:sections,id',
            'date' => 'required|date|before_or_equal:today',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'attendance' => 'required|array|min:1',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status' => 'required|in:present,absent,late,half_day,holiday,leave',
            'attendance.*.remark' => 'nullable|string|max:200',
        ]);

        if ($user->hasRole('teacher')) {
            $staff = Staff::where('user_id', $user->id)->first();
            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff profile not found.',
                ], 403);
            }

            $isAssigned = SectionSubjectStaff::where('staff_id', $staff->id)
                ->where('section_id', $request->section_id)
                ->exists();

            if (!$isAssigned) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. You are not assigned to this section.',
                ], 403);
            }
        }

        $section = Section::findOrFail($request->section_id);

        DB::transaction(function () use ($schoolId, $request, $section) {
            foreach ($request->attendance as $item) {
                StudentAttendance::updateOrCreate(
                    [
                        'school_id' => $schoolId,
                        'student_id' => $item['student_id'],
                        'date' => $request->date,
                    ],
                    [
                        'section_id' => $request->section_id,
                        'class_id' => $section->class_id,
                        'academic_session_id' => $request->academic_session_id,
                        'status' => $item['status'],
                        'remark' => $item['remark'] ?? null,
                        'marked_by' => auth()->id(),
                        'attendance_type' => 'manual',
                    ]
                );
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Attendance stored successfully.',
        ]);
    }
}
