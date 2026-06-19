<?php

namespace App\Http\Controllers\School\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\School\Attendance\AttendanceMarkRequest;
use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentAttendanceController extends Controller
{
    public function index()
    {
        $classes = SchoolClass::all();
        $sections = Section::all();
        $academicSessions = AcademicSession::all();

        return view('school.attendance.students.index', compact('classes', 'sections', 'academicSessions'));
    }

    public function loadSection(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        
        $request->validate([
            'section_id' => 'required|integer',
            'date' => 'required|date',
            'academic_session_id' => 'required|integer',
        ]);

        $sectionId = $request->section_id;
        $date = $request->date;

        $students = Student::where('section_id', $sectionId)
            ->where('is_active', true)
            ->orderBy('roll_number')
            ->get();

        $attendances = StudentAttendance::where('section_id', $sectionId)
            ->whereDate('date', $date)
            ->get()
            ->keyBy('student_id');

        $html = view('school.attendance.students.load-table', compact('students', 'attendances'))->render();

        return response()->json([
            'success' => true,
            'html' => $html,
        ]);
    }

    public function store(AttendanceMarkRequest $request)
    {
        $schoolId = auth()->user()->school_id;
        $data = $request->validated();
        
        $markedBy = auth()->id();
        $date = $data['date'];
        $sectionId = $data['section_id'];
        $sessionId = $data['academic_session_id'];

        $section = Section::findOrFail($sectionId);

        DB::transaction(function () use ($schoolId, $data, $date, $sectionId, $sessionId, $markedBy, $section) {
            foreach ($data['attendance'] as $item) {
                $attendance = StudentAttendance::where('school_id', $schoolId)
                    ->where('student_id', $item['student_id'])
                    ->whereDate('date', $date)
                    ->first();

                if ($attendance) {
                    $attendance->update([
                        'section_id' => $sectionId,
                        'class_id' => $section->class_id,
                        'academic_session_id' => $sessionId,
                        'status' => $item['status'],
                        'remark' => $item['remark'] ?? null,
                        'marked_by' => $markedBy,
                        'attendance_type' => 'manual',
                    ]);
                } else {
                    StudentAttendance::create([
                        'school_id' => $schoolId,
                        'student_id' => $item['student_id'],
                        'date' => $date,
                        'section_id' => $sectionId,
                        'class_id' => $section->class_id,
                        'academic_session_id' => $sessionId,
                        'status' => $item['status'],
                        'remark' => $item['remark'] ?? null,
                        'marked_by' => $markedBy,
                        'attendance_type' => 'manual',
                    ]);
                }
            }
        });

        return redirect()->route('school.attendance.students.index')->with('success', 'Attendance marked successfully.');
    }

    public function report(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $classes = SchoolClass::all();
        $sections = Section::all();
        $academicSessions = AcademicSession::all();

        $classId = $request->get('class_id');
        $sectionId = $request->get('section_id');
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        $students = [];
        $attendanceDays = [];
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        if ($classId && $sectionId) {
            $students = Student::where('section_id', $sectionId)
                ->where('is_active', true)
                ->orderBy('roll_number')
                ->get();

            $records = StudentAttendance::where('section_id', $sectionId)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get()
                ->groupBy('student_id');

            foreach ($students as $student) {
                $studentRecords = $records->get($student->id) ?? collect();
                $studentDays = [];
                
                $presentCount = 0;
                $absentCount = 0;

                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $dateString = sprintf('%s-%02s-%02s', $year, $month, $day);
                    $record = $studentRecords->first(fn($r) => $r->date->format('Y-m-d') === $dateString);
                    
                    $status = $record ? $record->status : null;
                    if ($status === 'present' || $status === 'late') {
                        $presentCount++;
                    } elseif ($status === 'absent') {
                        $absentCount++;
                    }

                    $studentDays[$day] = $status;
                }

                $totalMarked = $presentCount + $absentCount;
                $student->attendance_summary = [
                    'days' => $studentDays,
                    'present' => $presentCount,
                    'absent' => $absentCount,
                    'percentage' => $totalMarked > 0 ? round(($presentCount / $totalMarked) * 100, 1) : 0,
                ];
            }
        }

        return view('school.attendance.students.report', compact('classes', 'sections', 'academicSessions', 'students', 'daysInMonth', 'month', 'year', 'classId', 'sectionId'));
    }

    public function dailyReport(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $date = $request->get('date', date('Y-m-d'));

        // Load summaries per class/section
        $sections = Section::with(['schoolClass'])->get();
        $reportData = [];

        $totalPresent = 0;
        $totalAbsent = 0;
        $totalLate = 0;
        $totalLeave = 0;

        foreach ($sections as $section) {
            $studentCount = Student::where('section_id', $section->id)->where('is_active', true)->count();
            
            $present = StudentAttendance::where('section_id', $section->id)->whereDate('date', $date)->whereIn('status', ['present', 'late'])->count();
            $absent = StudentAttendance::where('section_id', $section->id)->whereDate('date', $date)->where('status', 'absent')->count();
            $leave = StudentAttendance::where('section_id', $section->id)->whereDate('date', $date)->where('status', 'leave')->count();

            $totalPresent += $present;
            $totalAbsent += $absent;
            $totalLeave += $leave;

            $reportData[] = [
                'class_name' => $section->schoolClass?->name ?? 'N/A',
                'section_name' => $section->name,
                'total_students' => $studentCount,
                'present' => $present,
                'absent' => $absent,
                'leave' => $leave,
                'percentage' => $studentCount > 0 ? round(($present / $studentCount) * 100, 1) : 0,
            ];
        }

        $summary = [
            'present' => $totalPresent,
            'absent' => $totalAbsent,
            'leave' => $totalLeave,
            'total' => $totalPresent + $totalAbsent + $totalLeave,
        ];

        return view('school.attendance.students.daily', compact('reportData', 'summary', 'date'));
    }

    public function stats(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        
        // Return analytical page for student attendance
        $topAbsentees = StudentAttendance::select('student_id', DB::raw('count(*) as absent_count'))
            ->where('school_id', $schoolId)
            ->where('status', 'absent')
            ->groupBy('student_id')
            ->orderBy('absent_count', 'desc')
            ->with('student')
            ->limit(10)
            ->get();

        return view('school.attendance.students.stats', compact('topAbsentees'));
    }
}
