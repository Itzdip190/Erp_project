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

        $user = \Illuminate\Support\Facades\Auth::guard('sanctum')->user() ?? auth()->user();
        $code = $request->header('X-School-Code') ?: $request->query('school_code');
        $schoolId = null;
        if ($code) {
            $s = \App\Models\School::where('code', $code)->orWhereRaw('LOWER(code) = ?', [strtolower($code)])->first();
            $schoolId = $s?->id;
        }
        if (!$schoolId && $user) {
            $schoolId = $user->school_id;
        }
        if (!$schoolId) {
            $schoolId = (int) (\App\Models\School::first()?->id ?? 21);
        }

        $sectionId = (int)$request->section_id;
        $date = $request->date;

        $staff = null;
        if ($user) {
            $staff = Staff::where('user_id', $user->id)->first()
                ?? Staff::where('school_id', $schoolId)->where(function($q) use ($user) {
                    $q->where('email', $user->email)->orWhere('phone', $user->phone);
                })->first();
        }

        // Resolve current academic session anchored strictly to today's date
        $today = today()->toDateString();
        $currentSession = \App\Models\AcademicSession::where('school_id', $schoolId)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->first();

        if (!$currentSession && $user) {
            $currentSession = \App\Models\AcademicSession::resolveCurrentSessionForUser($user, $schoolId);
        }
        if (!$currentSession) {
            $currentSession = \App\Models\AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first()
                ?? \App\Models\AcademicSession::where('school_id', $schoolId)->latest('id')->first();
        }

        $sessionId = $request->get('academic_session_id', $currentSession?->id);

        // Get students for this section in active academic session (matching Web ERP StudentAttendanceController)
        $studentsQuery = Student::where('school_id', $schoolId)
            ->activeStudents();

        if ($sessionId && method_exists(Student::class, 'scopeInAcademicSession')) {
            $studentsQuery->inAcademicSession((int)$sessionId, null, $sectionId);
        } else {
            $studentsQuery->where('section_id', $sectionId);
        }

        $studentsQuery->with(['studentSessions' => function($q) use ($sessionId) {
            if ($sessionId) {
                $q->where('academic_session_id', $sessionId);
            }
        }]);

        $students = $studentsQuery->get();

        if ($students->isEmpty()) {
            $students = Student::where('school_id', $schoolId)
                ->activeStudents()
                ->where('section_id', $sectionId)
                ->with(['studentSessions' => function($q) use ($sessionId) {
                    if ($sessionId) {
                        $q->where('academic_session_id', $sessionId);
                    }
                }])
                ->get();
        }

        $students = $students->sortBy(function($st) {
            $sess = $st->studentSessions->first();
            $roll = $sess?->roll_number ?? $st->roll_number ?? '999999';
            return is_numeric($roll) ? (int)$roll : $roll;
        })->values();

        $records = StudentAttendance::where('section_id', $sectionId)
            ->where('date', $date)
            ->get()
            ->keyBy('student_id');

        $data = $students->map(function ($student) use ($records, $sessionId) {
            $sess = $student->studentSessions->first();
            $roll = $sess?->roll_number ?? $student->roll_number ?? '—';
            $name = $sess?->full_name ?: ($student->full_name ?: trim($student->first_name . ' ' . $student->last_name));
            $record = $records->get($student->id);

            $photoUrl = $student->resolvePhotoUrl($sessionId)
                ?? ($student->photo ? (str_starts_with($student->photo, 'http') ? $student->photo : asset('storage/' . ltrim($student->photo, '/'))) : null);

            return [
                'id' => $student->id,
                'student_id' => $student->id,
                'roll_number' => $roll,
                'full_name' => $name,
                'student_name' => $name,
                'admission_number' => $student->admission_number ?? ('ADM/' . $student->id),
                'photo' => $photoUrl,
                'photo_url' => $photoUrl,
                'avatar' => $photoUrl,
                'gender' => $student->gender,
                'status' => $record ? $record->status : 'none',
                'remark' => $record?->remark,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'academic_session' => [
                'id' => $currentSession?->id,
                'name' => $currentSession?->name,
                'is_current' => (bool)$currentSession?->is_current,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $schoolId = $user->school_id ?? 1;

        if (!$request->filled('academic_session_id')) {
            $today = today()->toDateString();
            $currentSession = \App\Models\AcademicSession::where('school_id', $schoolId)
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->first()
                ?? \App\Models\AcademicSession::resolveCurrentSessionForUser($user, $schoolId);

            $request->merge(['academic_session_id' => $currentSession?->id ?? 1]);
        }

        $request->validate([
            'section_id' => 'required|exists:sections,id',
            'date' => 'required|date',
            'academic_session_id' => 'nullable',
            'attendance' => 'required|array|min:1',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status' => 'required|in:present,absent,late,half_day,holiday,leave',
            'attendance.*.remark' => 'nullable|string|max:200',
        ]);

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
