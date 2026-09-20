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

            // Verify teacher is Class Teacher or Assistant Class Teacher for section
            $isAssigned = Section::where('id', $sectionId)
                ->where('school_id', $user->school_id)
                ->where(function($q) use ($staff) {
                    $q->where('class_teacher_id', $staff->id)
                      ->orWhere('assistant_class_teacher_id', $staff->id);
                })
                ->exists();

            if (!$isAssigned) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Only class teachers can mark attendance for this section.',
                ], 403);
            }
        }

        $schoolId = $user->school_id ?? 1;
        $sessionId = $request->get('academic_session_id');
        if (!$sessionId) {
            $currentSession = \App\Models\AcademicSession::resolveCurrentSessionForUser($user, $schoolId);
            $sessionId = $currentSession?->id;
        }

        $students = Student::where('school_id', $schoolId)
            ->activeStudents()
            ->inAcademicSession($sessionId, null, $sectionId)
            ->with(['studentSessions' => fn($q) => $q->where('academic_session_id', $sessionId)])
            ->get();

        $students = $students->sortBy(function($st) {
            $sess = $st->studentSessions->first();
            $roll = $sess?->roll_number ?? $st->roll_number ?? '999999';
            return is_numeric($roll) ? (int)$roll : $roll;
        })->values();

        $records = StudentAttendance::where('section_id', $sectionId)
            ->where('date', $date)
            ->get()
            ->keyBy('student_id');

        $data = $students->map(function ($student) use ($records) {
            $sess = $student->studentSessions->first();
            $roll = $sess?->roll_number ?? $student->roll_number;
            $name = $sess?->full_name ?: ($student->full_name ?: trim($student->first_name . ' ' . $student->last_name));
            $record = $records->get($student->id);
            return [
                'student_id' => $student->id,
                'roll_number' => $roll,
                'full_name' => $name,
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

            $isAssigned = Section::where('id', $request->section_id)
                ->where('school_id', $schoolId)
                ->where(function($q) use ($staff) {
                    $q->where('class_teacher_id', $staff->id)
                      ->orWhere('assistant_class_teacher_id', $staff->id);
                })
                ->exists();

            if (!$isAssigned) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Only class teachers can mark attendance for this section.',
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

        $secName = $section->name ?? 'Section';
        $clsName = $section->schoolClass?->name ?? $section->class?->name ?? 'Class';
        if (class_exists(\App\Services\NotificationService::class)) {
            try {
                \App\Services\NotificationService::send([
                    'school_id'      => $schoolId,
                    'recipient_role' => 'school_admin',
                    'title'          => 'Student Attendance Submitted',
                    'message'        => "Attendance submitted for {$clsName} - {$secName} on {$request->date}.",
                    'module'         => 'attendance',
                    'type'           => 'attendance_submitted',
                    'icon'           => 'fa-user-clock',
                    'color'          => '#d97706',
                    'action_url'     => route('school.attendance.students.index', [
                        'class_id'            => $section->class_id,
                        'section_id'          => $section->id,
                        'date'                => $request->date,
                        'academic_session_id' => $request->academic_session_id,
                    ]),
                ]);
            } catch (\Throwable $e) {}
        }

        return response()->json([
            'success' => true,
            'message' => 'Attendance stored successfully.',
        ]);
    }
}
