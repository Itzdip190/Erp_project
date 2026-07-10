<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Event;
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

        // For legacy support — removed insecure fallback

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

        // Fee stats
        $totalFee    = 0;
        $paidFee     = 0;
        $pendingFee  = 0;
        $feeRate     = 0;

        if ($student) {
            $studentFees = \App\Models\StudentFee::where('student_id', $student->id)->get();
            $totalFee = $studentFees->sum('amount');
            $paidFee = $studentFees->sum('paid_amount');
            $pendingFee = max(0, $totalFee - $paidFee);
            $feeRate = $totalFee > 0 ? round(($paidFee / $totalFee) * 100) : 0;
        }

        $documents = $student
            ? \App\Models\StudentDocument::where('student_id', $student->id)->orderBy('created_at', 'desc')->get()
            : collect();

        $notifications = $this->getNotifications($user, $student);

        // Database-driven timetable with mock fallback
        $dayOfWeek = now()->format('l'); // Monday, Tuesday, etc.
        $dbTimetable = collect();
        if ($student) {
            $dbTimetable = \App\Models\ClassTimetableCell::where('class_id', $student->class_id)
                ->where('section_id', $student->section_id)
                ->where('day_of_week', $dayOfWeek)
                ->with(['subject', 'teacher', 'period'])
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
                
                $teacherName = 'N/A';
                if ($substitute && $substitute->substituteTeacher && $substitute->substituteTeacher->user) {
                    $teacherName = $substitute->substituteTeacher->user->name . ' (Sub)';
                } elseif ($slot->teacher && $slot->teacher->user) {
                    $teacherName = $slot->teacher->user->name;
                }
                
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

        $schoolId = $user->school_id;
        $todayEvents = Event::where('school_id', $schoolId)
            ->whereDate('start_date', '<=', today()->toDateString())
            ->whereDate('end_date', '>=', today()->toDateString())
            ->get();

        return view('parent.dashboard', compact(
            'todayEvents',
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
            'timetable',
            'notifications'
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

        // Removed insecure fallback

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

        // Removed insecure fallback

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

        // Removed insecure fallback

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

        // Removed insecure fallback

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

        // Removed insecure fallback

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

        // Removed insecure fallback

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

    private function getNotifications($user, $student)
    {
        $notifications = collect();
        if ($student) {
            // 1. Documents
            $docs = \App\Models\StudentDocument::where('student_id', $student->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
            foreach ($docs as $doc) {
                $notifications->push((object)[
                    'type' => 'document',
                    'title' => 'New Document Issued',
                    'text' => $doc->original_name,
                    'time' => $doc->created_at,
                    'url' => route('parent.documents.download', ['document' => $doc->id, 'action' => 'view']),
                    'icon' => 'fas fa-file-pdf',
                    'color' => 'var(--gold)',
                    'color_bg' => 'var(--gold-bg)',
                ]);
            }

            // 2. ID Cards
            $cards = \App\Models\StudentCard::where('student_id', $student->id)
                ->with('template')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
            foreach ($cards as $card) {
                $notifications->push((object)[
                    'type' => 'card',
                    'title' => 'ID Card / Pass Issued',
                    'text' => ($card->template ? $card->template->name : 'Student ID Card') . ' (' . $card->card_number . ')',
                    'time' => $card->created_at,
                    'url' => route('parent.cards.index'),
                    'icon' => 'fas fa-id-card',
                    'color' => 'var(--blue)',
                    'color_bg' => 'rgba(59,130,246,0.15)',
                ]);
            }

            // 3. Payment Links
            $paylinks = \App\Models\PaymentLink::where('student_id', $student->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
            foreach ($paylinks as $pl) {
                $notifications->push((object)[
                    'type' => 'paylink',
                    'title' => 'New Payment Due',
                    'text' => $pl->purpose . ' - ₹' . number_format($pl->amount, 2),
                    'time' => $pl->created_at,
                    'url' => $pl->link_url,
                    'icon' => 'fas fa-indian-rupee-sign',
                    'color' => 'var(--red)',
                    'color_bg' => 'rgba(239,68,68,0.15)',
                ]);
            }

            // 4. Notices
            $notices = \App\Models\Notice::where('school_id', $user->school_id)
                ->whereIn('target_audience', ['all', 'students'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
            foreach ($notices as $notice) {
                $notifications->push((object)[
                    'type' => 'notice',
                    'title' => 'Notice: ' . $notice->title,
                    'text' => \Illuminate\Support\Str::limit($notice->content, 60),
                    'time' => $notice->created_at,
                    'url' => route('parent.notices.index'),
                    'icon' => 'fas fa-bullhorn',
                    'color' => 'var(--purple)',
                    'color_bg' => 'rgba(139,92,246,0.15)',
                ]);
            }

            // 5. Offline Tests
            if (\Illuminate\Support\Facades\Schema::hasTable('offline_tests')) {
                $offlineTests = \App\Models\OfflineTest::where('school_id', $user->school_id)
                    ->where(function($q) use ($student) {
                        $q->whereNull('class_id')->orWhere('class_id', $student->class_id);
                    })
                    ->with('subject')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
                foreach ($offlineTests as $ot) {
                    $notifications->push((object)[
                        'type' => 'offline_test',
                        'title' => 'Offline Test: ' . $ot->title,
                        'text' => ($ot->subject ? $ot->subject->name : 'General Subject') . ($ot->start_date_time ? ' | ' . date('d M, h:i A', strtotime($ot->start_date_time)) : ''),
                        'time' => $ot->created_at,
                        'url' => route('parent.exams.index'),
                        'icon' => 'fas fa-file-signature',
                        'color' => 'var(--gold)',
                        'color_bg' => 'var(--gold-bg)',
                    ]);
                }
            }

            // 6. General Exams
            if (\Illuminate\Support\Facades\Schema::hasTable('exams')) {
                $exams = \App\Models\Exam::where('school_id', $user->school_id)
                    ->where(function($q) use ($student) {
                        $q->whereNull('class_id')->orWhere('class_id', $student->class_id);
                    })
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
                foreach ($exams as $ex) {
                    $notifications->push((object)[
                        'type' => 'exam',
                        'title' => 'Exam Scheduled: ' . $ex->name,
                        'text' => 'Term/Session: ' . $ex->academic_year . ($ex->start_date ? ' | Date: ' . date('d M Y', strtotime($ex->start_date)) : ''),
                        'time' => $ex->created_at,
                        'url' => route('parent.exams.index'),
                        'icon' => 'fas fa-pen-ruler',
                        'color' => 'var(--blue)',
                        'color_bg' => 'rgba(59,130,246,0.15)',
                    ]);
                }
            }
        }
        return $notifications->sortByDesc('time')->values()->take(10);
    }

    private function getStudentData($user)
    {
        $student = Student::where('school_id', $user->school_id)
            ->where(function ($q) use ($user) {
                $q->where('guardian_email', $user->email)
                  ->orWhere('user_id', $user->id);
            })
            ->with(['class', 'section', 'academicSession', 'school'])
            ->first();

        // Removed insecure fallback

        $school = $user->school;
        $classDisplay   = optional($student?->class)->name ?? 'N/A';
        $sectionDisplay = optional($student?->section)->name ?? 'N/A';
        $sessionDisplay = optional($student?->academicSession)->name ?? 'N/A';
        $stuName = $student ? $student->full_name : $user->name;
        $stuInitials = strtoupper(substr($stuName,0,1).(str_contains($stuName,' ') ? substr($stuName,strrpos($stuName,' ')+1,1) : ''));

        $documents = $student
            ? \App\Models\StudentDocument::where('student_id', $student->id)->orderBy('created_at', 'desc')->get()
            : collect();

        $notifications = $this->getNotifications($user, $student);

        return compact('user', 'student', 'school', 'classDisplay', 'sectionDisplay', 'sessionDisplay', 'stuName', 'stuInitials', 'documents', 'notifications');
    }


    public function leaves()
    {
        $data = $this->getStudentData(auth()->user());
        if ($data['student']) {
            $leaves = \App\Models\LeaveApplication::where('student_id', $data['student']->id)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $leaves = collect();
        }
        return view('parent.leaves', array_merge($data, compact('leaves')));
    }

    public function storeLeave(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();
        $data = $this->getStudentData($user);
        if (!$data['student']) {
            return back()->with('error', 'No active student record found.');
        }

        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'reason' => 'required|string',
        ]);

        \App\Models\LeaveApplication::create([
            'school_id' => $user->school_id,
            'student_id' => $data['student']->id,
            'applicant_type' => 'student',
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Leave application submitted successfully.');
    }

    public function fees()
    {
        $data = $this->getStudentData(auth()->user());
        if ($data['student']) {
            $fees = \App\Models\StudentFee::where('student_id', $data['student']->id)
                ->with('category')
                ->orderBy('due_date', 'asc')
                ->get();
        } else {
            $fees = collect();
        }
        $config = \App\Models\FeeConfiguration::where('school_id', auth()->user()->school_id)->first();
        return view('parent.fees', array_merge($data, compact('fees', 'config')));
    }


    public function timetable()
    {
        $data = $this->getStudentData(auth()->user());
        $timetableGrouped = [];

        if ($data['student']) {
            $slots = \App\Models\ClassTimetableCell::where('class_id', $data['student']->class_id)
                ->where('section_id', $data['student']->section_id)
                ->with(['subject', 'teacher.user', 'period'])
                ->get();

            $daysOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            foreach ($daysOrder as $day) {
                $daySlots = $slots->filter(fn($s) => strtolower($s->day_of_week) === strtolower($day))
                    ->sortBy(function($s) {
                        return strtotime($s->period ? $s->period->start_time : '00:00:00');
                    });
                if ($daySlots->isNotEmpty()) {
                    $timetableGrouped[$day] = $daySlots;
                }
            }
        }

        return view('parent.timetable', array_merge($data, compact('timetableGrouped')));
    }

    public function exams()
    {
        $data = $this->getStudentData(auth()->user());
        if ($data['student']) {
            $student = $data['student'];
            $marks = \App\Models\StudentMark::where('student_id', $student->id)
                ->with('subject')
                ->orderBy('created_at', 'desc')
                ->get();

            $offlineTests = \Illuminate\Support\Facades\Schema::hasTable('offline_tests')
                ? \App\Models\OfflineTest::where('school_id', auth()->user()->school_id)
                    ->where(function($q) use ($student) {
                        $q->whereNull('class_id')->orWhere('class_id', $student->class_id);
                    })
                    ->with(['subject', 'teacher'])
                    ->orderBy('start_date_time', 'asc')
                    ->get()
                : collect();

            $scheduledExams = \Illuminate\Support\Facades\Schema::hasTable('exams')
                ? \App\Models\Exam::where('school_id', auth()->user()->school_id)
                    ->where(function($q) use ($student) {
                        $q->whereNull('class_id')->orWhere('class_id', $student->class_id);
                    })
                    ->with('examSubjects.subject')
                    ->orderBy('created_at', 'desc')
                    ->get()
                : collect();
        } else {
            $marks = collect();
            $offlineTests = collect();
            $scheduledExams = collect();
        }
        return view('parent.exams', array_merge($data, compact('marks', 'offlineTests', 'scheduledExams')));
    }

    public function notices()
    {
        $user = auth()->user();
        $data = $this->getStudentData($user);
        $notices = \App\Models\Notice::where('school_id', $user->school_id)
            ->whereIn('target_audience', ['all', 'students'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('parent.notices', array_merge($data, compact('notices')));
    }

    public function surveys()
    {
        $user = auth()->user();
        $data = $this->getStudentData($user);
        $surveys = \App\Models\Survey::where('school_id', $user->school_id)
            ->where('is_active', true)
            ->with('options')
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($surveys as $survey) {
            $survey->has_voted = \App\Models\SurveyResponse::where('survey_id', $survey->id)
                ->where('user_id', $user->id)
                ->exists();
        }

        return view('parent.surveys', array_merge($data, compact('surveys')));
    }


    public function voteSurvey(\Illuminate\Http\Request $request, \App\Models\Survey $survey)
    {
        $user = auth()->user();
        $request->validate([
            'option_id' => 'required|exists:survey_options,id',
        ]);

        $alreadyVoted = \App\Models\SurveyResponse::where('survey_id', $survey->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyVoted) {
            return back()->with('error', 'You have already voted on this survey.');
        }

        \App\Models\SurveyResponse::create([
            'survey_id' => $survey->id,
            'user_id' => $user->id,
            'option_id' => $request->option_id,
        ]);

        \App\Models\SurveyOption::where('id', $request->option_id)->increment('votes');

        return back()->with('success', 'Vote registered successfully.');
    }

    public function chat()
    {
        $user = auth()->user();
        $data = $this->getStudentData($user);
        
        $messages = \App\Models\ChatMessage::where('school_id', $user->school_id)
            ->where(function ($q) use ($user) {
                $q->where('sender_id', $user->id)->orWhere('receiver_id', $user->id);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        $teachers = \App\Models\Staff::where('school_id', $user->school_id)
            ->with('user')
            ->get();

        return view('parent.chat', array_merge($data, compact('messages', 'teachers')));
    }

    public function sendChatMessage(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        \App\Models\ChatMessage::create([
            'school_id' => $user->school_id,
            'sender_id' => $user->id,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return back()->with('success', 'Message sent.');
    }

    public function assignments()
    {
        $data = $this->getStudentData();
        $student = $data['student'];

        $assignments = collect();
        if ($student) {
            $assignments = \App\Models\TeacherAssignment::where('school_id', $student->school_id)
                ->where('class_id', $student->class_id)
                ->where('section_id', $student->section_id)
                ->with(['subject', 'teacher'])
                ->latest()
                ->get();
        }

        return view('parent.assignments', array_merge($data, compact('assignments')));
    }

    public function studyMaterials()
    {
        $data = $this->getStudentData();
        $student = $data['student'];

        $materials = collect();
        if ($student) {
            $materials = \App\Models\StudyMaterial::where('school_id', $student->school_id)
                ->where('class_id', $student->class_id)
                ->where('section_id', $student->section_id)
                ->with(['subject', 'teacher'])
                ->latest()
                ->get();
        }

        return view('parent.study_materials', array_merge($data, compact('materials')));
    }
}

