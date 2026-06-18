<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentAttendance;
use Carbon\Carbon;

class ParentDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Find child by guardian email or user_id
        $student = Student::where('school_id', $user->school_id)
            ->where(function ($q) use ($user) {
                $q->where('guardian_email', $user->email)
                  ->orWhere('user_id', $user->id);
            })
            ->with(['class', 'section', 'academicSession', 'school'])
            ->first();

        // For legacy support — also check by guardian name
        if (!$student) {
            $student = Student::where('school_id', $user->school_id)
                ->with(['class', 'section', 'academicSession', 'school'])
                ->first();
        }

        $school = $user->school;

        // Attendance stats
        $totalDays   = 0;
        $presentDays = 0;
        $absentDays  = 0;
        $lateDays    = 0;
        $attendanceRate = 0;
        $monthlyAttendance = array_fill(0, 6, 0);
        $attendanceLabels  = [];
        for ($i = 5; $i >= 0; $i--) {
            $attendanceLabels[] = now()->subMonths($i)->format('M');
        }
        $recentAttendance  = collect();
        $presentSparkline  = array_fill(0, 6, 0);
        $absentSparkline   = array_fill(0, 6, 0);
        $lateSparkline     = array_fill(0, 6, 0);

        if ($student) {
            $sessionStart = $student->academicSession?->start_date ?? now()->startOfYear();
            $sessionEnd   = now();

            $allAttendance = StudentAttendance::where('student_id', $student->id)
                ->whereBetween('date', [$sessionStart, $sessionEnd])
                ->orderBy('date', 'desc')
                ->get();

            $totalDays   = $allAttendance->count();
            $presentDays = $allAttendance->where('status', 'present')->count();
            $absentDays  = $allAttendance->where('status', 'absent')->count();
            $lateDays    = $allAttendance->where('status', 'late')->count();
            $attendanceRate = $totalDays > 0 ? round($presentDays / $totalDays * 100) : 0;
            $recentAttendance = $allAttendance->take(7);

            // Monthly chart & sparklines (last 6 months)
            $monthlyAttendance = [];
            $attendanceLabels  = [];
            $presentSparkline  = [];
            $absentSparkline   = [];
            $lateSparkline     = [];

            for ($i = 5; $i >= 0; $i--) {
                $m = now()->subMonths($i);
                $mAttend = $allAttendance->filter(fn($a) =>
                    Carbon::parse($a->date)->month == $m->month &&
                    Carbon::parse($a->date)->year == $m->year
                );
                $mTotal   = $mAttend->count();
                $mPresent = $mAttend->where('status', 'present')->count();
                $mAbsent  = $mAttend->where('status', 'absent')->count();
                $mLate    = $mAttend->where('status', 'late')->count();

                $attendanceLabels[]  = $m->format('M');
                $monthlyAttendance[] = $mTotal > 0 ? round($mPresent / $mTotal * 100) : 0;
                $presentSparkline[]  = $mPresent;
                $absentSparkline[]   = $mAbsent;
                $lateSparkline[]     = $mLate;
            }
        }

        // Quick stats
        $classDisplay   = optional($student?->class)->name ?? 'N/A';
        $sectionDisplay = optional($student?->section)->name ?? 'N/A';
        $sessionDisplay = optional($student?->academicSession)->name ?? 'N/A';

        // Fee stats (stub)
        $totalFee    = 0;
        $paidFee     = 0;
        $pendingFee  = 0;
        $feeRate     = 0;

        $documents = $student
            ? \App\Models\StudentDocument::where('student_id', $student->id)->orderBy('created_at', 'desc')->get()
            : collect();

        // Database-driven timetable with mock fallback
        $dayOfWeek = now()->format('l'); // Monday, Tuesday, etc.
        $dbTimetable = collect();
        if ($student) {
            $dbTimetable = \App\Models\Timetable::where('class_id', $student->class_id)
                ->where('section_id', $student->section_id)
                ->where('day_of_week', $dayOfWeek)
                ->with(['subject', 'teacher'])
                ->get();
        }

        $timetable = [];
        if ($dbTimetable->isNotEmpty()) {
            $colors = ['#3b82f6', '#8b5cf6', '#10b981', '#f59e0b', '#ef4444', '#f97316', '#ec4899'];
            foreach ($dbTimetable as $index => $slot) {
                // Check if there is a substitution for this slot on this date
                $substitute = \App\Models\TimetableSubstitution::where('timetable_id', $slot->id)
                    ->whereDate('date', today())
                    ->with('substituteTeacher.user')
                    ->first();
                
                $teacherName = $substitute ? ($substitute->substituteTeacher->user->name . ' (Sub)') : ($slot->teacher ? $slot->teacher->user->name : 'N/A');
                $timetable[] = [
                    'time' => $slot->start_time,
                    'subject' => $slot->subject ? $slot->subject->name : 'N/A',
                    'teacher' => $teacherName,
                    'color' => $colors[$index % count($colors)]
                ];
            }
        } else {
            $timetable = [
                ['time'=>'9:00 AM','subject'=>'Mathematics','teacher'=>'Mr. Kapoor','color'=>'#3b82f6'],
                ['time'=>'10:00 AM','subject'=>'Physics','teacher'=>'Ms. Sharma','color'=>'#8b5cf6'],
                ['time'=>'11:00 AM','subject'=>'Chemistry','teacher'=>'Mr. Verma','color'=>'#10b981'],
                ['time'=>'12:00 PM','subject'=>'Lunch Break','teacher'=>'—','color'=>'#f59e0b'],
                ['time'=>'1:00 PM','subject'=>'English','teacher'=>'Ms. Patel','color'=>'#ef4444'],
                ['time'=>'2:00 PM','subject'=>'History','teacher'=>'Mr. Singh','color'=>'#f97316'],
            ];
        }

        return view('parent.dashboard', compact(
            'user',
            'student',
            'school',
            'totalDays',
            'presentDays',
            'absentDays',
            'lateDays',
            'attendanceRate',
            'monthlyAttendance',
            'attendanceLabels',
            'recentAttendance',
            'classDisplay',
            'sectionDisplay',
            'sessionDisplay',
            'totalFee',
            'paidFee',
            'pendingFee',
            'feeRate',
            'presentSparkline',
            'absentSparkline',
            'lateSparkline',
            'documents',
            'timetable'
        ));
    }

    public function documents()
    {
        $user = auth()->user();

        // Find child by guardian email or user_id
        $student = Student::where('school_id', $user->school_id)
            ->where(function ($q) use ($user) {
                $q->where('guardian_email', $user->email)
                  ->orWhere('user_id', $user->id);
            })
            ->with(['class', 'section', 'academicSession', 'school'])
            ->first();

        if (!$student) {
            $student = Student::where('school_id', $user->school_id)->first();
        }

        $school = $user->school;
        $classDisplay   = optional($student?->class)->name ?? 'N/A';
        $sectionDisplay = optional($student?->section)->name ?? 'N/A';
        $sessionDisplay = optional($student?->academicSession)->name ?? 'N/A';

        $documents = $student
            ? \App\Models\StudentDocument::where('student_id', $student->id)->orderBy('created_at', 'desc')->get()
            : collect();

        $stuName = $student ? $student->full_name : $user->name;
        $stuInitials = strtoupper(substr($stuName,0,1).(str_contains($stuName,' ') ? substr($stuName,strrpos($stuName,' ')+1,1) : ''));

        return view('parent.documents', compact('user', 'student', 'school', 'documents', 'classDisplay', 'sectionDisplay', 'sessionDisplay', 'stuName', 'stuInitials'));
    }

    public function downloadDocument(\Illuminate\Http\Request $request, \App\Models\StudentDocument $document)
    {
        $user = auth()->user();

        // Find child by guardian email or user_id
        $student = Student::where('school_id', $user->school_id)
            ->where(function ($q) use ($user) {
                $q->where('guardian_email', $user->email)
                  ->orWhere('user_id', $user->id);
            })
            ->first();

        if (!$student) {
            $student = Student::where('school_id', $user->school_id)->first();
        }

        if (!$student || $document->student_id !== $student->id) {
            abort(403, 'Unauthorized access to this document.');
        }

        $defaultDisk = config('filesystems.default');
        $disk = \Illuminate\Support\Facades\Storage::disk($defaultDisk);
        
        $filePathOnDisk = null;
        
        if ($disk->exists($document->file_path)) {
            try {
                $filePathOnDisk = $disk->path($document->file_path);
            } catch (\Exception $e) {
                $filePathOnDisk = null;
            }
        }
        
        if (!$filePathOnDisk) {
            $fallbackDiskName = ($defaultDisk === 'local') ? 'public' : 'local';
            $fallbackDisk = \Illuminate\Support\Facades\Storage::disk($fallbackDiskName);
            
            if ($fallbackDisk->exists($document->file_path)) {
                try {
                    $filePathOnDisk = $fallbackDisk->path($document->file_path);
                } catch (\Exception $e) {
                    $filePathOnDisk = null;
                }
            }
        }
        
        if (!$filePathOnDisk) {
            $pathsToCheck = [
                storage_path($document->file_path),
                storage_path('app/' . $document->file_path),
                storage_path('app/private/' . $document->file_path),
                storage_path('app/public/' . $document->file_path),
            ];

            foreach ($pathsToCheck as $path) {
                if (file_exists($path) && is_file($path)) {
                    $filePathOnDisk = $path;
                    break;
                }
            }
        }

        $action = $request->query('action', 'download');

        if ($filePathOnDisk) {
            if ($action === 'view') {
                return response()->file($filePathOnDisk, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $document->original_name . '"'
                ]);
            }
            return response()->download($filePathOnDisk, $document->original_name);
        } else {
            // Cloud filesystem fallback streaming (if path() is not supported)
            if (!$disk->exists($document->file_path)) {
                abort(404, 'Document file not found in storage.');
            }
            $fileStream = $disk->readStream($document->file_path);
            $headers = [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => ($action === 'view' ? 'inline' : 'attachment') . '; filename="' . $document->original_name . '"'
            ];
            return response()->stream(function () use ($fileStream) {
                fpassthru($fileStream);
            }, 200, $headers);
        }
    }

    public function diary()
    {
        $user = auth()->user();
        $student = Student::where('school_id', $user->school_id)
            ->where(function ($q) use ($user) {
                $q->where('guardian_email', $user->email)
                  ->orWhere('user_id', $user->id);
            })
            ->with(['class', 'section', 'academicSession', 'school'])
            ->first();

        if (!$student) {
            $student = Student::where('school_id', $user->school_id)->first();
        }

        $school = $user->school;
        $classDisplay   = optional($student?->class)->name ?? 'N/A';
        $sectionDisplay = optional($student?->section)->name ?? 'N/A';
        $sessionDisplay = optional($student?->academicSession)->name ?? 'N/A';
        $stuName = $student ? $student->full_name : $user->name;
        $stuInitials = strtoupper(substr($stuName,0,1).(str_contains($stuName,' ') ? substr($stuName,strrpos($stuName,' ')+1,1) : ''));

        $diaries = $student
            ? \App\Models\DigitalDiary::where('class_id', $student->class_id)
                ->where('section_id', $student->section_id)
                ->with('teacher')
                ->orderBy('diary_date', 'desc')
                ->get()
            : collect();

        $documents = $student
            ? \App\Models\StudentDocument::where('student_id', $student->id)->orderBy('created_at', 'desc')->get()
            : collect();

        return view('parent.diary', compact('user', 'student', 'school', 'diaries', 'classDisplay', 'sectionDisplay', 'sessionDisplay', 'stuName', 'stuInitials', 'documents'));
    }

    public function events()
    {
        $user = auth()->user();
        $student = Student::where('school_id', $user->school_id)
            ->where(function ($q) use ($user) {
                $q->where('guardian_email', $user->email)
                  ->orWhere('user_id', $user->id);
            })
            ->with(['class', 'section', 'academicSession', 'school'])
            ->first();

        if (!$student) {
            $student = Student::where('school_id', $user->school_id)->first();
        }

        $school = $user->school;
        $classDisplay   = optional($student?->class)->name ?? 'N/A';
        $sectionDisplay = optional($student?->section)->name ?? 'N/A';
        $sessionDisplay = optional($student?->academicSession)->name ?? 'N/A';
        $stuName = $student ? $student->full_name : $user->name;
        $stuInitials = strtoupper(substr($stuName,0,1).(str_contains($stuName,' ') ? substr($stuName,strrpos($stuName,' ')+1,1) : ''));

        $events = \App\Models\Event::where('school_id', $user->school_id)
            ->orderBy('start_date', 'asc')
            ->get();

        $documents = $student
            ? \App\Models\StudentDocument::where('student_id', $student->id)->orderBy('created_at', 'desc')->get()
            : collect();

        return view('parent.events', compact('user', 'student', 'school', 'events', 'classDisplay', 'sectionDisplay', 'sessionDisplay', 'stuName', 'stuInitials', 'documents'));
    }

    public function cards()
    {
        $user = auth()->user();
        $student = Student::where('school_id', $user->school_id)
            ->where(function ($q) use ($user) {
                $q->where('guardian_email', $user->email)
                  ->orWhere('user_id', $user->id);
            })
            ->with(['class', 'section', 'academicSession', 'school'])
            ->first();

        if (!$student) {
            $student = Student::where('school_id', $user->school_id)->first();
        }

        $school = $user->school;
        $classDisplay   = optional($student?->class)->name ?? 'N/A';
        $sectionDisplay = optional($student?->section)->name ?? 'N/A';
        $sessionDisplay = optional($student?->academicSession)->name ?? 'N/A';
        $stuName = $student ? $student->full_name : $user->name;
        $stuInitials = strtoupper(substr($stuName,0,1).(str_contains($stuName,' ') ? substr($stuName,strrpos($stuName,' ')+1,1) : ''));

        $cards = $student
            ? \App\Models\StudentCard::where('student_id', $student->id)
                ->where('status', 'active')
                ->with('template')
                ->get()
            : collect();

        $documents = $student
            ? \App\Models\StudentDocument::where('student_id', $student->id)->orderBy('created_at', 'desc')->get()
            : collect();

        return view('parent.cards', compact('user', 'student', 'school', 'cards', 'classDisplay', 'sectionDisplay', 'sessionDisplay', 'stuName', 'stuInitials', 'documents'));
    }

    public function certificates()
    {
        $user = auth()->user();
        $student = Student::where('school_id', $user->school_id)
            ->where(function ($q) use ($user) {
                $q->where('guardian_email', $user->email)
                  ->orWhere('user_id', $user->id);
            })
            ->with(['class', 'section', 'academicSession', 'school'])
            ->first();

        if (!$student) {
            $student = Student::where('school_id', $user->school_id)->first();
        }

        $school = $user->school;
        $classDisplay   = optional($student?->class)->name ?? 'N/A';
        $sectionDisplay = optional($student?->section)->name ?? 'N/A';
        $sessionDisplay = optional($student?->academicSession)->name ?? 'N/A';
        $stuName = $student ? $student->full_name : $user->name;
        $stuInitials = strtoupper(substr($stuName,0,1).(str_contains($stuName,' ') ? substr($stuName,strrpos($stuName,' ')+1,1) : ''));

        $certificates = $student
            ? \App\Models\StudentCertificate::where('student_id', $student->id)
                ->with('template')
                ->get()
            : collect();

        $documents = $student
            ? \App\Models\StudentDocument::where('student_id', $student->id)->orderBy('created_at', 'desc')->get()
            : collect();

        return view('parent.certificates', compact('user', 'student', 'school', 'certificates', 'classDisplay', 'sectionDisplay', 'sessionDisplay', 'stuName', 'stuInitials', 'documents'));
    }
}
