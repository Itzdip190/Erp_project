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
        $sessionId = $request->academic_session_id;

        $students = Student::where('section_id', $sectionId)
            ->where('is_active', true)
            ->orderBy('roll_number')
            ->get();

        $attendances = StudentAttendance::where('section_id', $sectionId)
            ->whereDate('date', $date)
            ->get()
            ->keyBy('student_id');

        $attendanceStats = StudentAttendance::where('academic_session_id', $sessionId)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->groupBy('student_id');

        foreach ($students as $student) {
            $studentAtts = $attendanceStats->get($student->id) ?? collect();
            $totalMarked = $studentAtts->count();
            $presentCount = $studentAtts->whereIn('status', ['present', 'late', 'duty_leave'])->count() 
                + ($studentAtts->where('status', 'half_day')->count() * 0.5);
            $student->attendance_percentage = $totalMarked > 0 ? round(($presentCount / $totalMarked) * 100) : null;
        }

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
                    if ($status === 'present' || $status === 'late' || $status === 'duty_leave') {
                        $presentCount++;
                    } elseif ($status === 'absent') {
                        $absentCount++;
                    } elseif ($status === 'half_day') {
                        $presentCount += 0.5;
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
            
            $present = StudentAttendance::where('section_id', $section->id)->whereDate('date', $date)->whereIn('status', ['present', 'late', 'duty_leave'])->count();
            $absent = StudentAttendance::where('section_id', $section->id)->whereDate('date', $date)->where('status', 'absent')->count();
            $leave = StudentAttendance::where('section_id', $section->id)->whereDate('date', $date)->whereIn('status', ['leave', 'holiday'])->count();

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

    public function markingReport(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $academicSessions = AcademicSession::where('school_id', $schoolId)->get();
        $currentSession = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first()
            ?? AcademicSession::where('school_id', $schoolId)->first();

        $sessionId = $request->get('academic_session_id', $currentSession ? $currentSession->id : null);
        $selectedSession = $sessionId ? AcademicSession::find($sessionId) : null;

        // Parse date range
        $defaultFrom = now()->subDays(6)->toDateString();
        $defaultTo = now()->toDateString();
        $fromDateStr = $request->get('from_date', $defaultFrom);
        $toDateStr = $request->get('to_date', $defaultTo);

        // Convert standard format d/m/Y (e.g. 15/06/2026) to Y-m-d
        if (strpos($fromDateStr, '/') !== false) {
            try {
                $fromDateStr = \Carbon\Carbon::createFromFormat('d/m/Y', $fromDateStr)->toDateString();
            } catch (\Exception $e) {
                // fallback
            }
        }
        if (strpos($toDateStr, '/') !== false) {
            try {
                $toDateStr = \Carbon\Carbon::createFromFormat('d/m/Y', $toDateStr)->toDateString();
            } catch (\Exception $e) {
                // fallback
            }
        }

        $from = \Carbon\Carbon::parse($fromDateStr);
        $to = \Carbon\Carbon::parse($toDateStr);

        // Max 90 days constraint
        if ($from->diffInDays($to) > 90) {
            $from = $to->copy()->subDays(90);
            session()->flash('warning', 'Date range restricted to maximum of 90 days.');
        }

        // Generate all dates in the range
        $dates = [];
        $temp = $from->copy();
        while ($temp->lte($to)) {
            $dates[] = $temp->copy();
            $temp->addDay();
        }
        $totalWorkingDays = count($dates);

        // Query active staff for filters
        $teachers = \App\Models\Staff::where('school_id', $schoolId)->where('is_active', true)->get();

        // Query active sections
        $sectionsQuery = Section::with(['schoolClass', 'classTeacher'])
            ->where('school_id', $schoolId);

        // Filter by staff if provided
        $staffId = $request->get('staff_id');
        if ($staffId) {
            $sectionsQuery->where('class_teacher_id', $staffId);
        }

        $sections = $sectionsQuery->get();

        // Query active student count per section
        $studentCounts = Student::where('school_id', $schoolId)
            ->where('is_active', true)
            ->select('section_id', DB::raw('count(*) as total_students'))
            ->groupBy('section_id')
            ->pluck('total_students', 'section_id')
            ->toArray();

        // Fetch student attendance records in date range
        $attendanceRecords = StudentAttendance::where('school_id', $schoolId)
            ->whereBetween('date', [$from->toDateString() . ' 00:00:00', $to->toDateString() . ' 23:59:59'])
            ->select('section_id', 'date', DB::raw('count(distinct student_id) as marked_count'))
            ->groupBy('section_id', 'date')
            ->get()
            ->groupBy('section_id');

        // Build report grid data
        $reportData = [];
        $defaulterPct = (int) $request->get('defaulter_pct', 70);
        $showDayWise = $request->has('show_day_wise') ? filter_var($request->get('show_day_wise'), FILTER_VALIDATE_BOOLEAN) : true;

        foreach ($sections as $section) {
            $totalStudents = $studentCounts[$section->id] ?? 0;
            
            $markedDaysCount = 0;
            $dayWiseAttendance = [];
            
            $sectionRecords = isset($attendanceRecords[$section->id]) 
                ? $attendanceRecords[$section->id]->keyBy(function($r) {
                    $d = $r->date;
                    if ($d instanceof \Carbon\Carbon) {
                        return $d->toDateString();
                    }
                    return \Carbon\Carbon::parse($d)->toDateString();
                })
                : collect();

            foreach ($dates as $date) {
                $dateStr = $date->toDateString();
                $record = $sectionRecords->get($dateStr);
                $markedCount = $record ? $record->marked_count : 0;
                
                $isMarked = $markedCount > 0;
                if ($isMarked) {
                    $markedDaysCount++;
                }
                
                $dayWiseAttendance[$dateStr] = [
                    'percentage' => $isMarked ? 100 : 0,
                    'is_marked' => $isMarked,
                ];
            }
            
            $overallPct = $totalWorkingDays > 0 ? round(($markedDaysCount / $totalWorkingDays) * 100) : 0;
            
            $reportData[] = [
                'section' => $section,
                'class_name' => $section->schoolClass ? $section->schoolClass->name : 'N/A',
                'section_name' => $section->name,
                'teacher_name' => $section->classTeacher ? $section->classTeacher->full_name : 'Not Assigned',
                'total_working_days' => $totalWorkingDays,
                'marked_days' => $markedDaysCount,
                'overall_percentage' => $overallPct,
                'day_wise' => $dayWiseAttendance,
                'is_defaulter' => $overallPct < $defaulterPct,
            ];
        }

        return view('school.attendance.students.marking_report', compact(
            'academicSessions', 'currentSession', 'sessionId', 'selectedSession',
            'fromDateStr', 'toDateStr', 'from', 'to', 'dates', 'totalWorkingDays',
            'teachers', 'staffId', 'sections', 'reportData', 'defaulterPct', 'showDayWise'
        ));
    }
}
