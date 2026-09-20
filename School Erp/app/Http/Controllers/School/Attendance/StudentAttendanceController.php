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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StudentAttendanceController extends Controller
{
    private function getTeacherStaff()
    {
        $user = auth()->user();
        $isTeacher = $user && ($user->hasRole('teacher') || $user->role === 'teacher' || $user->hasRole('staff'));
        return $isTeacher ? $user->staff : null;
    }

    private function getTeacherAssignedSectionIds($staff, $schoolId)
    {
        $secIdsFromCt = Section::where('school_id', $schoolId)
            ->where(function($q) use ($staff) {
                $q->where('class_teacher_id', $staff->id)
                  ->orWhere('assistant_class_teacher_id', $staff->id);
            })
            ->pluck('id')->toArray();
        return array_values(array_unique(array_filter($secIdsFromCt)));
    }

    private function getMonthlyWorkingDays($year, $month): int
    {
        $startOfMonth = \Carbon\Carbon::createFromDate((int)$year, (int)$month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $daysInMonth = $startOfMonth->daysInMonth;
        $sundays = 0;
        $temp = $startOfMonth->copy();
        while ($temp->lte($endOfMonth)) {
            if ($temp->dayOfWeek === \Carbon\Carbon::SUNDAY) {
                $sundays++;
            }
            $temp->addDay();
        }
        return max(1, $daysInMonth - $sundays);
    }

    private function calculateMonthlyAttendancePercentage($students, $sessionId, $year, $month, $upToDate = null): void
    {
        $monthlyWorkingDays = $this->getMonthlyWorkingDays($year, $month);

        $query = StudentAttendance::where('academic_session_id', $sessionId)
            ->whereYear('date', (int)$year)
            ->whereMonth('date', (int)$month)
            ->whereIn('student_id', $students->pluck('id'));

        if ($upToDate) {
            $query->whereDate('date', '<=', $upToDate);
        }

        $attendanceStats = $query->get()->groupBy('student_id');

        foreach ($students as $student) {
            $studentAtts = $attendanceStats->get($student->id) ?? collect();
            $presentCount = $studentAtts->whereIn('status', ['present', 'late', 'duty_leave'])->count() 
                + ($studentAtts->where('status', 'half_day')->count() * 0.5);
            $student->attendance_percentage = $monthlyWorkingDays > 0 ? round(($presentCount / $monthlyWorkingDays) * 100) : 0;
        }
    }

    public function index()
    {
        $schoolId = auth()->user()->school_id;
        $staff = $this->getTeacherStaff();
        $isOnlySubjectTeacher = false;

        if ($staff) {
            $assignedSectionIds = $this->getTeacherAssignedSectionIds($staff, $schoolId);
            $sections = Section::where('school_id', $schoolId)->whereIn('id', $assignedSectionIds)->get();
            $classes = SchoolClass::where('school_id', $schoolId)->whereIn('id', $sections->pluck('class_id'))->get();
            if ($sections->isEmpty()) {
                $isOnlySubjectTeacher = true;
            }
        } else {
            $classes = SchoolClass::where('school_id', $schoolId)->get();
            $sections = Section::where('school_id', $schoolId)->get();
        }
        $academicSessions = AcademicSession::where('school_id', $schoolId)->get();
        $currentSession = AcademicSession::resolveCurrentSessionForUser(auth()->user(), $schoolId);

        return view('school.attendance.students.index', compact('classes', 'sections', 'academicSessions', 'currentSession', 'isOnlySubjectTeacher'));
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

        $staff = $this->getTeacherStaff();
        if ($staff) {
            $assignedSectionIds = $this->getTeacherAssignedSectionIds($staff, $schoolId);
            $section = Section::where('school_id', $schoolId)->whereIn('id', $assignedSectionIds)->find($sectionId);
            if (!$section) {
                return response()->json(['success' => false, 'message' => 'Unauthorized: Only assigned Class Teachers can mark attendance for this class/section.'], 403);
            }
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

        foreach ($students as $student) {
            $sess = $student->studentSessions->first();
            if ($sess) {
                if (!empty($sess->roll_number)) {
                    $student->roll_number = $sess->roll_number;
                }
                $sessFullName = $sess->full_name;
                if (!empty($sessFullName)) {
                    $student->full_name_session = $sessFullName;
                }
            }
        }

        $attendances = StudentAttendance::where('section_id', $sectionId)
            ->whereDate('date', $date)
            ->get()
            ->keyBy('student_id');

        $carbonDate = \Carbon\Carbon::parse($date);
        $this->calculateMonthlyAttendancePercentage($students, $sessionId, $carbonDate->year, $carbonDate->month, $carbonDate->format('Y-m-d'));

        $isSunday = ($carbonDate->dayOfWeek === 0);
        $isHolidayEvent = \App\Models\Event::where('school_id', $schoolId)
            ->where('is_holiday', true)
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->exists();
        $isHolidayDate = ($isSunday || $isHolidayEvent);

        $html = view('school.attendance.students.load-table', compact('students', 'attendances', 'isHolidayDate'))->render();

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

        $staff = $this->getTeacherStaff();
        if ($staff) {
            $assignedSectionIds = $this->getTeacherAssignedSectionIds($staff, $schoolId);
            $section = Section::where('school_id', $schoolId)->whereIn('id', $assignedSectionIds)->find($sectionId);
            if (!$section) {
                abort(403, 'Unauthorized: Only assigned Class Teachers can mark attendance for this class/section.');
            }
        } else {
            $section = Section::findOrFail($sectionId);
        }

        DB::transaction(function () use ($schoolId, $data, $date, $sectionId, $sessionId, $markedBy, $section) {
            foreach ($data['attendance'] as $item) {
                $rawStatus = $item['status'] ?? null;
                $status = (!empty($rawStatus) && $rawStatus !== 'not_marked') ? $rawStatus : 'present';

                $attendance = StudentAttendance::where('school_id', $schoolId)
                    ->where('student_id', $item['student_id'])
                    ->whereDate('date', $date)
                    ->first();

                if ($attendance) {
                    $attendance->update([
                        'section_id' => $sectionId,
                        'class_id' => $section->class_id,
                        'academic_session_id' => $sessionId,
                        'status' => $status,
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
                        'status' => $status,
                        'remark' => $item['remark'] ?? null,
                        'marked_by' => $markedBy,
                        'attendance_type' => 'manual',
                    ]);
                }
            }
        });

        $secName = $section->name ?? 'Section';
        $clsName = $section->schoolClass?->name ?? $section->class?->name ?? 'Class';
        \App\Services\NotificationService::send([
            'school_id'      => $schoolId,
            'recipient_role' => 'school_admin',
            'title'          => 'Student Attendance Submitted',
            'message'        => "Attendance submitted for {$clsName} - {$secName} on {$date}.",
            'module'         => 'attendance',
            'type'           => 'attendance_submitted',
            'icon'           => 'fa-user-clock',
            'color'          => '#d97706',
            'action_url'     => route('school.attendance.students.index', [
                'class_id'            => $section->class_id,
                'section_id'          => $sectionId,
                'date'                => $date,
                'academic_session_id' => $sessionId,
            ]),
        ]);

        return redirect()->route('school.attendance.students.index', [
            'class_id' => $section->class_id,
            'section_id' => $sectionId,
            'date' => $date,
            'academic_session_id' => $sessionId
        ])->with('success', 'Attendance marked successfully.');
    }


    public function report(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $staff = $this->getTeacherStaff();

        $classId = $request->get('class_id');
        $sectionId = $request->get('section_id');

        if ($staff) {
            $assignedSectionIds = $this->getTeacherAssignedSectionIds($staff, $schoolId);
            $sections = Section::where('school_id', $schoolId)->whereIn('id', $assignedSectionIds)->get();
            $classes = SchoolClass::where('school_id', $schoolId)->whereIn('id', $sections->pluck('class_id'))->get();
            if ($sectionId) {
                $checkSection = Section::where('school_id', $schoolId)->whereIn('id', $assignedSectionIds)->find($sectionId);
                if (!$checkSection) {
                    abort(403, 'Unauthorized.');
                }
            }
        } else {
            $classes = SchoolClass::where('school_id', $schoolId)->get();
            $sections = Section::where('school_id', $schoolId)->get();
        }
        $academicSessions = AcademicSession::where('school_id', $schoolId)->get();

        $sessionId = $request->get('academic_session_id');
        if (!$sessionId) {
            $currentSession = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first()
                ?? AcademicSession::where('school_id', $schoolId)->first();
            $sessionId = $currentSession?->id;
        }

        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        $students = [];
        $attendanceDays = [];
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        if ($classId && $sectionId) {
            $students = Student::where('school_id', $schoolId)
                ->activeStudents()
                ->inAcademicSession($sessionId, $classId, $sectionId)
                ->with(['studentSessions' => fn($q) => $q->where('academic_session_id', $sessionId)])
                ->get();

            $students = $students->sortBy(function($st) {
                $sess = $st->studentSessions->first();
                $roll = $sess?->roll_number ?? $st->roll_number ?? '999999';
                return is_numeric($roll) ? (int)$roll : $roll;
            })->values();

            foreach ($students as $student) {
                $sess = $student->studentSessions->first();
                if ($sess) {
                    if (!empty($sess->roll_number)) {
                        $student->roll_number = $sess->roll_number;
                    }
                    $sessFullName = $sess->full_name;
                    if (!empty($sessFullName)) {
                        $student->full_name_session = $sessFullName;
                    }
                }
            }

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
        $staff = $this->getTeacherStaff();

        $sessionId = $request->get('academic_session_id');
        if (!$sessionId) {
            $currentSession = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first()
                ?? AcademicSession::where('school_id', $schoolId)->first();
            $sessionId = $currentSession?->id;
        }

        // Load summaries per class/section
        if ($staff) {
            $assignedSectionIds = $this->getTeacherAssignedSectionIds($staff, $schoolId);
            $sections = Section::with(['schoolClass'])->where('school_id', $schoolId)->whereIn('id', $assignedSectionIds)->get();
        } else {
            $sections = Section::with(['schoolClass'])->where('school_id', $schoolId)->get();
        }
        $reportData = [];

        $totalPresent = 0;
        $totalAbsent = 0;
        $totalLate = 0;
        $totalLeave = 0;

        foreach ($sections as $section) {
            $studentCount = Student::where('school_id', $schoolId)
                ->activeStudents()
                ->inAcademicSession($sessionId, null, $section->id)
                ->count();
            
            $present = StudentAttendance::where('section_id', $section->id)->whereDate('date', $date)->whereIn('status', ['present', 'late', 'duty_leave'])->count();
            $absent = StudentAttendance::where('section_id', $section->id)->whereDate('date', $date)->where('status', 'absent')->count();
            $late = StudentAttendance::where('section_id', $section->id)->whereDate('date', $date)->where('status', 'late')->count();
            $leave = StudentAttendance::where('section_id', $section->id)->whereDate('date', $date)->whereIn('status', ['leave', 'holiday'])->count();

            $totalPresent += $present;
            $totalAbsent += $absent;
            $totalLate += $late;
            $totalLeave += $leave;

            $isMarked = StudentAttendance::where('section_id', $section->id)->whereDate('date', $date)->exists();

            $reportData[] = [
                'section' => $section,
                'class_name' => $section->schoolClass?->name ?? 'N/A',
                'section_name' => $section->name,
                'total_students' => $studentCount,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'leave' => $leave,
                'is_marked' => $isMarked,
                'percentage' => $studentCount > 0 ? round(($present / $studentCount) * 100) : 0,
            ];
        }

        $summary = [
            'present' => $totalPresent,
            'absent' => $totalAbsent,
            'late' => $totalLate,
            'leave' => $totalLeave,
            'total' => $totalPresent + $totalAbsent + $totalLeave,
        ];

        return view('school.attendance.students.daily', compact('reportData', 'summary', 'date'));
    }

    public function stats(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $staff = $this->getTeacherStaff();
        
        $query = StudentAttendance::select('student_id', DB::raw('count(*) as absent_count'))
            ->where('school_id', $schoolId)
            ->where('status', 'absent');

        if ($staff) {
            $assignedSectionIds = $this->getTeacherAssignedSectionIds($staff, $schoolId);
            $query->whereIn('section_id', $assignedSectionIds);
        }
        
        $topAbsentees = $query->groupBy('student_id')
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

        $staff = $this->getTeacherStaff();

        // Query active sections
        $sectionsQuery = Section::with(['schoolClass', 'classTeacher'])
            ->where('school_id', $schoolId);

        if ($staff) {
            $assignedSectionIds = $this->getTeacherAssignedSectionIds($staff, $schoolId);
            $sectionsQuery->whereIn('id', $assignedSectionIds);
        } else {
            // Filter by staff if provided
            $staffId = $request->get('staff_id');
            if ($staffId) {
                $sectionsQuery->where(function($q) use ($staffId) {
                    $q->where('class_teacher_id', $staffId)
                      ->orWhere('assistant_class_teacher_id', $staffId);
                });
            }
        }

        $sections = $sectionsQuery->get();

        // Query active student count per section in the selected academic session
        $studentCounts = [];
        foreach ($sections as $sectionItem) {
            $studentCounts[$sectionItem->id] = Student::where('school_id', $schoolId)
                ->activeStudents()
                ->inAcademicSession($sessionId, null, $sectionItem->id)
                ->count();
        }

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

    public function export(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $type = $request->get('type', 'daily'); // 'daily' or 'monthly'
        $classId = $request->get('class_id');
        $sectionId = $request->get('section_id');
        $date = $request->get('date', date('Y-m-d'));
        $sessionId = $request->get('academic_session_id');
        
        if (!$classId || !$sectionId || !$sessionId) {
            return back()->with('error', 'Please select Class, Section and Academic Year.');
        }

        $staff = $this->getTeacherStaff();
        if ($staff) {
            $assignedSectionIds = $this->getTeacherAssignedSectionIds($staff, $schoolId);
            $section = Section::where('school_id', $schoolId)->whereIn('id', $assignedSectionIds)->find($sectionId);
            if (!$section) {
                abort(403, 'Unauthorized.');
            }
        } else {
            $section = Section::findOrFail($sectionId);
        }
        
        $class = SchoolClass::findOrFail($classId);
        $session = AcademicSession::findOrFail($sessionId);
        
        
        $students = Student::where('school_id', $schoolId)
            ->activeStudents()
            ->inAcademicSession($sessionId, $classId, $sectionId)
            ->with(['studentSessions' => fn($q) => $q->where('academic_session_id', $sessionId)])
            ->get();

        $students = $students->sortBy(function($st) {
            $sess = $st->studentSessions->first();
            $roll = $sess?->roll_number ?? $st->roll_number ?? '999999';
            return is_numeric($roll) ? (int)$roll : $roll;
        })->values();

        foreach ($students as $student) {
            $sess = $student->studentSessions->first();
            if ($sess) {
                if (!empty($sess->roll_number)) {
                    $student->roll_number = $sess->roll_number;
                }
                $sessFullName = $sess->full_name;
                if (!empty($sessFullName)) {
                    $student->full_name_session = $sessFullName;
                }
            }
        }

        $titleStyle = [
            'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF01242E']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ];

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF023C4D']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FFFFFFFF']
                ]
            ]
        ];

        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FFCBD5E1']
                ]
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ];
            
        if ($type === 'daily') {
            // Daily register
            $attendances = StudentAttendance::where('section_id', $sectionId)
                ->whereDate('date', $date)
                ->get()
                ->keyBy('student_id');
                
            $carbonDate = \Carbon\Carbon::parse($date);
            $this->calculateMonthlyAttendancePercentage($students, $sessionId, $carbonDate->year, $carbonDate->month, $carbonDate->format('Y-m-d'));
            
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Daily Register');

            // Title block
            $sheet->mergeCells('A1:F1');
            $sheet->setCellValue('A1', 'Daily Attendance Register - Class: ' . $class->name . ' - ' . $section->name . ' (Date: ' . date('d-m-Y', strtotime($date)) . ')');
            $sheet->getRowDimension(1)->setRowHeight(36);
            $sheet->getStyle('A1:F1')->applyFromArray($titleStyle);
            
            // Set Headers
            $headers = ['Roll No', 'Admission No', 'Student Name', 'Status', 'Remarks', 'Attendance %'];
            $sheet->fromArray($headers, null, 'A2');
            $sheet->getRowDimension(2)->setRowHeight(22);
            $sheet->getStyle('A2:F2')->applyFromArray($headerStyle);
            
            $rowIdx = 3;
            foreach ($students as $st) {
                $att = $attendances->get($st->id);
                $status = $att ? ucfirst(str_replace('_', ' ', $att->status)) : 'Not Marked';
                $remark = $att ? $att->remark : '';
                $pct = $st->attendance_percentage !== null ? $st->attendance_percentage . '%' : '—';
                
                $sheet->fromArray([
                    $st->roll_number ?? '—',
                    $st->admission_number,
                    $st->full_name,
                    $status,
                    $remark,
                    $pct
                ], null, 'A' . $rowIdx);

                $sheet->getRowDimension($rowIdx)->setRowHeight(20);
                $sheet->getStyle('A' . $rowIdx . ':F' . $rowIdx)->applyFromArray($dataStyle);
                $rowIdx++;
            }

            // Auto-width
            foreach (range('A', 'F') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            $writer = new Xlsx($spreadsheet);
            $filename = 'Student_Attendance_' . $class->name . '_' . $section->name . '_' . $date . '.xlsx';
            
            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
            
        } else {
            // Monthly summary
            $month = $request->get('month', date('m'));
            $year = $request->get('year', date('Y'));
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            
            $records = StudentAttendance::where('section_id', $sectionId)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get()
                ->groupBy('student_id');
                
            $this->calculateMonthlyAttendancePercentage($students, $sessionId, (int)$year, (int)$month);
            
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Monthly Summary');

            $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($daysInMonth + 6);

            // Title block
            $sheet->mergeCells('A1:' . $lastCol . '1');
            $sheet->setCellValue('A1', 'Monthly Attendance Summary - Class: ' . $class->name . ' - ' . $section->name . ' (' . date('F Y', mktime(0,0,0,$month,1,$year)) . ')');
            $sheet->getRowDimension(1)->setRowHeight(36);
            $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray($titleStyle);
            
            // Set Headers
            $headers = ['Roll No', 'Admission No', 'Student Name'];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $headers[] = $d;
            }
            $headers[] = 'Present';
            $headers[] = 'Absent';
            $headers[] = 'Attendance %';
            
            $sheet->fromArray($headers, null, 'A2');
            $sheet->getRowDimension(2)->setRowHeight(22);
            $sheet->getStyle('A2:' . $lastCol . '2')->applyFromArray($headerStyle);
            
            $rowIdx = 3;
            foreach ($students as $st) {
                $studentRecords = $records->get($st->id) ?? collect();
                $presentCountVal = 0;
                $absentCountVal = 0;
                
                $row = [
                    $st->roll_number ?? '—',
                    $st->admission_number,
                    $st->full_name,
                ];
                
                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $dateString = sprintf('%s-%02s-%02s', $year, $month, $d);
                    $rec = $studentRecords->first(fn($r) => $r->date->format('Y-m-d') === $dateString);
                    
                    $status = $rec ? $rec->status : null;
                    if ($status === 'present' || $status === 'late' || $status === 'duty_leave') {
                        $presentCountVal++;
                        $statusText = 'P';
                    } elseif ($status === 'absent') {
                        $absentCountVal++;
                        $statusText = 'A';
                    } elseif ($status === 'half_day') {
                        $presentCountVal += 0.5;
                        $statusText = 'HD';
                    } elseif ($status === 'leave') {
                        $statusText = 'L';
                    } elseif ($status === 'holiday') {
                        $statusText = 'H';
                    } else {
                        $statusText = '—';
                    }
                    $row[] = $statusText;
                }
                
                $row[] = $presentCountVal;
                $row[] = $absentCountVal;
                $row[] = $st->attendance_percentage . '%';
                
                $sheet->fromArray($row, null, 'A' . $rowIdx);

                $sheet->getRowDimension($rowIdx)->setRowHeight(20);
                $sheet->getStyle('A' . $rowIdx . ':' . $lastCol . $rowIdx)->applyFromArray($dataStyle);
                $rowIdx++;
            }

            // Auto-width
            for ($i = 1; $i <= ($daysInMonth + 6); $i++) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            $writer = new Xlsx($spreadsheet);
            $filename = 'Student_Monthly_Attendance_' . $class->name . '_' . $section->name . '_' . $year . '_' . $month . '.xlsx';
            
            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }
    }

    public function preview(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $type = $request->get('type', 'daily');
        $classId = $request->get('class_id');
        $sectionId = $request->get('section_id');
        $date = $request->get('date', date('Y-m-d'));
        $sessionId = $request->get('academic_session_id');

        if (!$classId || !$sectionId || !$sessionId) {
            return response()->json(['success' => false, 'message' => 'Missing filters.']);
        }

        $staff = $this->getTeacherStaff();
        if ($staff) {
            $section = Section::where('school_id', $schoolId)
                ->where(function($q) use ($staff) {
                    $q->where('class_teacher_id', $staff->id)
                      ->orWhere('assistant_class_teacher_id', $staff->id);
                })
                ->find($sectionId);
            if (!$section) {
                return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
            }
        } else {
            $section = Section::findOrFail($sectionId);
        }

        $session = AcademicSession::findOrFail($sessionId);
        

        $students = Student::where('school_id', $schoolId)
            ->activeStudents()
            ->inAcademicSession($sessionId, $classId, $sectionId)
            ->with(['studentSessions' => fn($q) => $q->where('academic_session_id', $sessionId)])
            ->get();

        $students = $students->sortBy(function($st) {
            $sess = $st->studentSessions->first();
            $roll = $sess?->roll_number ?? $st->roll_number ?? '999999';
            return is_numeric($roll) ? (int)$roll : $roll;
        })->values();

        foreach ($students as $student) {
            $sess = $student->studentSessions->first();
            if ($sess) {
                if (!empty($sess->roll_number)) {
                    $student->roll_number = $sess->roll_number;
                }
                $sessFullName = $sess->full_name;
                if (!empty($sessFullName)) {
                    $student->full_name_session = $sessFullName;
                }
            }
        }

        if ($type === 'daily') {
            $attendances = StudentAttendance::where('section_id', $sectionId)
                ->whereDate('date', $date)
                ->get()
                ->keyBy('student_id');

            $carbonDate = \Carbon\Carbon::parse($date);
            $this->calculateMonthlyAttendancePercentage($students, $sessionId, $carbonDate->year, $carbonDate->month, $carbonDate->format('Y-m-d'));

            $columns = ['Roll No', 'Admission No', 'Student Name', 'Status', 'Remarks', 'Attendance %'];
            $rows = [];
            foreach ($students as $idx => $st) {
                $att = $attendances->get($st->id);
                $status = $att ? ucfirst(str_replace('_', ' ', $att->status)) : 'Not Marked';
                $remark = $att ? $att->remark : '';
                $pct = $st->attendance_percentage ?? 0;

                $rows[] = [
                    $st->roll_number ?? '—',
                    $st->admission_number,
                    $st->full_name,
                    $status,
                    $remark,
                    $pct . '%'
                ];
            }
        } else {
            $month = $request->get('month', date('m'));
            $year = $request->get('year', date('Y'));
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

            $records = StudentAttendance::where('section_id', $sectionId)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get()
                ->groupBy('student_id');

            $this->calculateMonthlyAttendancePercentage($students, $sessionId, (int)$year, (int)$month);

            $columns = ['Roll No', 'Admission No', 'Student Name'];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $columns[] = (string)$d;
            }
            $columns[] = 'Present';
            $columns[] = 'Absent';
            $columns[] = 'Attendance %';

            $rows = [];
            foreach ($students as $st) {
                $studentRecords = $records->get($st->id) ?? collect();
                $presentCountVal = 0;
                $absentCountVal = 0;

                $row = [
                    $st->roll_number ?? '—',
                    $st->admission_number,
                    $st->full_name,
                ];

                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $dateString = sprintf('%s-%02s-%02s', $year, $month, $d);
                    $rec = $studentRecords->first(fn($r) => $r->date->format('Y-m-d') === $dateString);

                    $status = $rec ? $rec->status : null;
                    if ($status === 'present' || $status === 'late' || $status === 'duty_leave') {
                        $presentCountVal++;
                        $statusText = 'P';
                    } elseif ($status === 'absent') {
                        $absentCountVal++;
                        $statusText = 'A';
                    } elseif ($status === 'half_day') {
                        $presentCountVal += 0.5;
                        $statusText = 'HD';
                    } elseif ($status === 'leave') {
                        $statusText = 'L';
                    } elseif ($status === 'holiday') {
                        $statusText = 'H';
                    } else {
                        $statusText = '—';
                    }
                    $row[] = $statusText;
                }

                $pct = $st->attendance_percentage ?? 0;

                $row[] = (string)$presentCountVal;
                $row[] = (string)$absentCountVal;
                $row[] = $pct . '%';

                $rows[] = $row;
            }
        }

        // Add spreadsheet letter headers A, B, C, D...
        $excelLetters = [];
        for ($i = 1; $i <= count($columns); $i++) {
            $excelLetters[] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
        }

        return response()->json([
            'success' => true,
            'headers' => $excelLetters,
            'columns' => $columns,
            'rows' => $rows
        ]);
    }
}
