<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Event;
use Carbon\Carbon;

class ParentDashboardController extends Controller
{
    public function resolveStudent($user = null)
    {
        $user = $user ?? auth()->user();
        if (!$user) return null;

        // 0. Manual Child Switch via query parameter
        if (request()->has('switch_student_id') || request()->has('student_id')) {
            $switchId = (int) (request()->get('switch_student_id') ?: request()->get('student_id'));
            $validStudent = Student::where('school_id', $user->school_id)
                ->where('id', $switchId)
                ->with(['class', 'section', 'academicSession', 'school', 'studentSessions.schoolClass', 'studentSessions.section'])
                ->first();
            if ($validStudent) {
                session(['active_student_id' => $validStudent->id]);
                return $validStudent;
            }
        }

        // Get deduplicated children for this parent/student account (each distinct child at their latest class)
        $children = $this->getParentChildren($user);

        // If session already has an active_student_id, verify if it belongs to one of the parent's children
        if (session()->has('active_student_id')) {
            $sessId = (int) session('active_student_id');
            $matchedChild = $children->firstWhere('id', $sessId);
            if ($matchedChild) {
                return $matchedChild;
            }

            // If session has an older record of a child (e.g. Nursery), find that child in $children (which has Class 1)
            $sessStudent = Student::where('school_id', $user->school_id)->where('id', $sessId)->first();
            if ($sessStudent) {
                $sessFirstName = strtolower(trim((string)($sessStudent->first_name ?: explode(' ', $sessStudent->full_name ?? '')[0])));
                $upgradeChild = $children->first(function ($c) use ($sessFirstName, $sessStudent) {
                    $cFirstName = strtolower(trim((string)($c->first_name ?: explode(' ', $c->full_name ?? '')[0])));
                    return ($cFirstName !== '' && $cFirstName === $sessFirstName) 
                        || (!empty($sessStudent->admission_number) && $c->admission_number === $sessStudent->admission_number)
                        || (!empty($sessStudent->user_id) && $c->user_id === $sessStudent->user_id);
                });

                if ($upgradeChild) {
                    session(['active_student_id' => $upgradeChild->id]);
                    return $upgradeChild;
                }
            }
        }

        // Default to the first child in $children (which is the latest active record with highest ID)
        $activeChild = $children->first();
        if ($activeChild) {
            session(['active_student_id' => $activeChild->id]);
            return $activeChild;
        }

        return null;
    }

    /**
     * Get unique children of parent, resolving each child to their latest academic session/class record.
     */
    public function getParentChildren($user)
    {
        if (!$user) return collect();

        $cleanPhone = preg_replace('/[^0-9]/', '', (string)$user->phone);
        $userEmail = strtolower(trim((string)$user->email));

        $rawChildren = Student::withoutGlobalScopes()
            ->where('school_id', $user->school_id)
            ->where(function ($q) use ($user, $cleanPhone, $userEmail) {
                $q->where('user_id', $user->id);

                if (!empty($userEmail)) {
                    $q->orWhere('email', $userEmail)
                      ->orWhere('guardian_email', $userEmail)
                      ->orWhere('father_email', $userEmail)
                      ->orWhere('mother_email', $userEmail);
                }

                if (!empty($user->phone)) {
                    $q->orWhere('phone', $user->phone)
                      ->orWhere('guardian_phone', $user->phone)
                      ->orWhere('father_phone', $user->phone)
                      ->orWhere('mother_phone', $user->phone);
                }

                if (!empty($cleanPhone)) {
                    $q->orWhere('phone', $cleanPhone)
                      ->orWhere('guardian_phone', $cleanPhone)
                      ->orWhere('father_phone', $cleanPhone)
                      ->orWhere('mother_phone', $cleanPhone);
                }
            })
            ->with(['class', 'section', 'academicSession', 'studentSessions.schoolClass', 'studentSessions.section'])
            ->orderByDesc('id')
            ->get();

        // If no records found by user_id/phone/email, check if user has a direct linked student record
        if ($rawChildren->isEmpty()) {
            $linked = Student::withoutGlobalScopes()
                ->where('school_id', $user->school_id)
                ->where('user_id', $user->id)
                ->orderByDesc('id')
                ->with(['class', 'section', 'academicSession', 'studentSessions.schoolClass', 'studentSessions.section'])
                ->get();
            $rawChildren = $linked;
        }

        // Deduplicate: Group by normalized first name (each distinct child under parent account)
        // Since $rawChildren is sorted by id DESC, the first item in each name group is the latest class record!
        $children = $rawChildren->unique(function ($child) {
            $firstName = strtolower(trim((string)($child->first_name ?: explode(' ', $child->full_name ?? '')[0])));
            return $firstName !== '' ? $firstName : ('stu_' . $child->id);
        })->values();

        return $children;
    }

    public function index()
    {
        $user = auth()->user();
        $student = $this->resolveStudent($user);
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

        // Resolve School Active Academic Session & Student Class in this Session
        $school = $user->school;
        $currentSession = \App\Models\AcademicSession::resolveCurrentSessionForUser($user, $user->school_id)
            ?? ($student?->academicSession ?? \App\Models\AcademicSession::where('school_id', $user->school_id)->first());

        $activeStudentSession = null;
        if ($student && $currentSession) {
            $activeStudentSession = \App\Models\StudentSession::where('student_id', $student->id)
                ->where('academic_session_id', $currentSession->id)
                ->with(['schoolClass', 'section'])
                ->first();
        }

        $activeClass = $activeStudentSession?->schoolClass
            ?? ($activeStudentSession?->class_id ? \App\Models\SchoolClass::find($activeStudentSession->class_id) : null)
            ?? $student?->class
            ?? $student?->schoolClass
            ?? ($student?->class_id ? \App\Models\SchoolClass::find($student->class_id) : null);

        $activeSection = $activeStudentSession?->section
            ?? ($activeStudentSession?->section_id ? \App\Models\Section::find($activeStudentSession->section_id) : null)
            ?? $student?->section
            ?? ($student?->section_id ? \App\Models\Section::find($student->section_id) : null);

        $activeClassId = $activeClass?->id ?? ($activeStudentSession?->class_id ?? $student?->class_id);
        $activeSectionId = $activeSection?->id ?? ($activeStudentSession?->section_id ?? $student?->section_id);

        // Quick stats
        $classDisplay   = $activeClass?->name ?? ($student?->class?->name ?? 'Class');
        $sectionDisplay = $activeSection?->name ?? ($student?->section?->name ?? 'A');
        $sessionDisplay = $currentSession?->name ?? ($student?->academicSession?->name ?? '2026 – 2027');
        $studentIdDisplay = $student?->admission_number ?? ($student?->admission_id ?? ('STU' . ($student?->id ?? '')));

        // All children of this parent (for child switching if parent has multiple children)
        $children = $this->getParentChildren($user);

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

        // Database-driven timetable for today
        $dayOfWeek = now()->format('l'); // Monday, Tuesday, etc.
        $dbTimetable = collect();
        if ($student && $activeClassId && $activeSectionId) {
            $dbTimetable = \App\Models\ClassTimetableCell::where('class_id', $activeClassId)
                ->where('section_id', $activeSectionId)
                ->whereRaw('LOWER(day_of_week) = ?', [strtolower($dayOfWeek)])
                ->with(['subject', 'secondarySubject', 'teacher.user', 'secondaryTeacher.user', 'period'])
                ->get();
        }

        $timetable = [];
        if ($dbTimetable->isNotEmpty()) {
            $colors = ['#3b82f6', '#8b5cf6', '#10b981', '#f59e0b', '#ef4444', '#f97316', '#ec4899'];
            $sortedSlots = $dbTimetable->sortBy(function($slot) {
                return strtotime($slot->period ? $slot->period->start_time : '00:00:00');
            })->values();

            foreach ($sortedSlots as $index => $slot) {
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
                } elseif ($slot->teacher) {
                    $teacherName = $slot->teacher->full_name;
                }

                if ($slot->secondaryTeacher) {
                    $secTeacherName = $slot->secondaryTeacher->user ? $slot->secondaryTeacher->user->name : $slot->secondaryTeacher->full_name;
                    $teacherName .= ' / ' . $secTeacherName;
                }

                $timeStr = $slot->start_time ?? ($slot->period ? date('g:i A', strtotime($slot->period->start_time)) : 'N/A');
                if ($slot->period && $slot->period->end_time) {
                    $timeStr .= ' - ' . date('g:i A', strtotime($slot->period->end_time));
                }
                
                $subjectName = $slot->subject ? $slot->subject->name : 'N/A';
                if ($slot->secondarySubject) {
                    $subjectName .= ' & ' . $slot->secondarySubject->name;
                }

                $timetable[] = [
                    'time' => $timeStr,
                    'subject' => $subjectName,
                    'teacher' => $teacherName,
                    'color' => $colors[$index % count($colors)]
                ];
            }
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
            'studentIdDisplay',
            'children',
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
        $data = $this->getStudentData(auth()->user());
        $student = $data['student'];

        $publishedReportCards = $student
            ? \App\Models\ReportCardHistoryStudent::where('student_id', $student->id)
                ->where('is_published', true)
                ->with(['history.class', 'history.section', 'history.template'])
                ->orderBy('created_at', 'desc')
                ->get()
            : collect();

        return view('parent.documents', array_merge($data, compact('publishedReportCards')));
    }

    public function downloadDocument(\Illuminate\Http\Request $request, \App\Models\StudentDocument $document)
    {
        $user = auth()->user();
        $student = $this->resolveStudent($user);

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
        $data = $this->getStudentData(auth()->user());
        $student = $data['student'];
        $activeClassId = $data['activeClassId'] ?? $student?->class_id;
        $activeSectionId = $data['activeSectionId'] ?? $student?->section_id;

        $diaries = ($student && $activeClassId && $activeSectionId)
            ? \App\Models\DigitalDiary::where('class_id', $activeClassId)
                ->where('section_id', $activeSectionId)
                ->with('teacher')
                ->orderBy('diary_date', 'desc')
                ->get()
            : collect();

        return view('parent.diary', array_merge($data, compact('diaries')));
    }

    public function events()
    {
        $data = $this->getStudentData(auth()->user());
        $user = $data['user'];

        $events = \App\Models\Event::where('school_id', $user->school_id)
            ->orderBy('start_date', 'asc')
            ->get();

        return view('parent.events', array_merge($data, compact('events')));
    }

    public function cards()
    {
        $data = $this->getStudentData(auth()->user());
        $student = $data['student'];

        $cards = $student
            ? \App\Models\StudentCard::where('student_id', $student->id)
                ->where('status', 'active')
                ->with('template')
                ->get()
            : collect();

        return view('parent.cards', array_merge($data, compact('cards')));
    }

    public function certificates()
    {
        $data = $this->getStudentData(auth()->user());
        $student = $data['student'];

        $certificates = $student
            ? \App\Models\StudentCertificate::where('student_id', $student->id)
                ->with('template')
                ->get()
            : collect();

        return view('parent.certificates', array_merge($data, compact('certificates')));
    }

    private function getNotifications($user, $student)
    {
        $notifications = collect();

        // 0. Centralized Database Notifications (Fee & Portal Alerts)
            $dbNotifs = \App\Services\NotificationService::getNotifications($user, 20);
            foreach ($dbNotifs as $dn) {
                $iconClass = $dn->icon ?: \App\Services\NotificationService::getDefaultIcon($dn->module);
                if (!str_starts_with($iconClass, 'fa')) {
                    $iconClass = 'fas ' . $iconClass;
                }
                $notifications->push((object)[
                    'id'       => $dn->id,
                    'type'     => $dn->type ?: $dn->module,
                    'title'    => $dn->title,
                    'text'     => $dn->message,
                    'message'  => $dn->message,
                    'time'     => $dn->created_at,
                    'url'      => $dn->action_url ?: route('parent.fees.index'),
                    'icon'     => $iconClass,
                    'color'    => $dn->color ?: '#059669',
                    'color_bg' => ($dn->color ?: '#059669') . '20',
                    'is_read'  => (bool) $dn->is_read,
                ]);
            }

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
                ->where(function($q) {
                    $q->whereNull('publish_at')->orWhere('publish_at', '<=', now());
                })
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

    private function getStudentData($user = null)
    {
        $user = $user ?? auth()->user();
        $student = $this->resolveStudent($user);
        $school = $user->school;

        $currentSession = \App\Models\AcademicSession::resolveCurrentSessionForUser($user, $user->school_id)
            ?? ($student?->academicSession ?? \App\Models\AcademicSession::where('school_id', $user->school_id)->first());

        $activeStudentSession = null;
        if ($student && $currentSession) {
            $activeStudentSession = \App\Models\StudentSession::where('student_id', $student->id)
                ->where('academic_session_id', $currentSession->id)
                ->with(['schoolClass', 'section'])
                ->first();
        }

        $activeClass = $activeStudentSession?->schoolClass
            ?? ($activeStudentSession?->class_id ? \App\Models\SchoolClass::find($activeStudentSession->class_id) : null)
            ?? $student?->class
            ?? $student?->schoolClass
            ?? ($student?->class_id ? \App\Models\SchoolClass::find($student->class_id) : null);

        $activeSection = $activeStudentSession?->section
            ?? ($activeStudentSession?->section_id ? \App\Models\Section::find($activeStudentSession->section_id) : null)
            ?? $student?->section
            ?? ($student?->section_id ? \App\Models\Section::find($student->section_id) : null);

        $activeClassId = $activeClass?->id ?? ($activeStudentSession?->class_id ?? $student?->class_id);
        $activeSectionId = $activeSection?->id ?? ($activeStudentSession?->section_id ?? $student?->section_id);

        $classDisplay   = $activeClass?->name ?? ($student?->class?->name ?? 'Class');
        $sectionDisplay = $activeSection?->name ?? ($student?->section?->name ?? 'A');
        $sessionDisplay = $currentSession?->name ?? ($student?->academicSession?->name ?? '2026 – 2027');
        $studentIdDisplay = $student?->admission_number ?? ($student?->admission_id ?? ('STU' . ($student?->id ?? '')));
        $stuName = $student ? $student->full_name : $user->name;
        $stuInitials = strtoupper(substr($stuName,0,1).(str_contains($stuName,' ') ? substr($stuName,strrpos($stuName,' ')+1,1) : ''));

        // All children of this parent (for child switching if parent has multiple children)
        $children = $this->getParentChildren($user);

        $documents = $student
            ? \App\Models\StudentDocument::where('student_id', $student->id)->orderBy('created_at', 'desc')->get()
            : collect();

        $notifications = $this->getNotifications($user, $student);

        return compact('user', 'student', 'school', 'children', 'classDisplay', 'sectionDisplay', 'sessionDisplay', 'studentIdDisplay', 'stuName', 'stuInitials', 'documents', 'notifications', 'currentSession', 'activeStudentSession', 'activeClassId', 'activeSectionId');
    }


    public function leaves()
    {
        $data = $this->getStudentData(auth()->user());
        $student = $data['student'];
        
        $leaves = collect();
        $declarations = collect();

        if ($student) {
            $schoolId = $student->school_id;
            
            if (\Illuminate\Support\Facades\Schema::hasTable('student_leave_applications')) {
                $leaves = \App\Models\StudentLeaveApplication::with(['declaration'])
                    ->where('school_id', $schoolId)
                    ->where('student_id', $student->id)
                    ->orderByDesc('id')
                    ->get();
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('student_leave_declarations')) {
                $declarations = \App\Models\StudentLeaveDeclaration::where('school_id', $schoolId)
                    ->where('is_enabled', true)
                    ->orderBy('id', 'asc')
                    ->get();
            }
        }

        return view('parent.leaves', array_merge($data, compact('leaves', 'declarations')));
    }

    public function storeLeave(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();
        $data = $this->getStudentData($user);
        $student = $data['student'];

        if (!$student) {
            return back()->with('error', 'No active student record found.');
        }

        $request->validate([
            'leave_type'   => 'required|in:Leave,Sick Leave',
            'title'        => 'required|string|max:255',
            'from_date'    => 'required|date',
            'to_date'      => 'required|date|after_or_equal:from_date',
            'reason'       => 'nullable|string',
            'attachment'   => 'nullable|file|mimes:jpeg,jpg,png,pdf,doc,docx|max:5120',
            'declaration_id' => 'nullable|exists:student_leave_declarations,id',
        ]);

        $schoolId = $user->school_id ?: $student->school_id;

        // Check if declaration exists and is required
        $enabledDeclarationsCount = \App\Models\StudentLeaveDeclaration::where('school_id', $schoolId)
            ->where('is_enabled', true)
            ->count();

        if ($enabledDeclarationsCount > 0) {
            $request->validate([
                'declaration_id'       => 'required|exists:student_leave_declarations,id',
                'declaration_accepted' => 'required|accepted',
            ], [
                'declaration_accepted.required' => 'You must acknowledge and accept the declaration to submit a leave request.',
                'declaration_accepted.accepted' => 'You must acknowledge and accept the declaration to submit a leave request.',
            ]);
        }

        // Calculate total days
        $startDate = \Carbon\Carbon::parse($request->from_date);
        $endDate   = \Carbon\Carbon::parse($request->to_date);
        $totalDays = $startDate->diffInDays($endDate) + 1;

        // Handle attachment upload
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('student_leave_attachments', 'public');
        }

        // Academic session
        $currentSession = \App\Models\AcademicSession::resolveCurrentSessionForUser($user, $schoolId);
        $academicYear = $currentSession ? $currentSession->name : date('Y') . '-' . (date('Y') + 1);

        $leaveApp = \App\Models\StudentLeaveApplication::create([
            'school_id'            => $schoolId,
            'academic_year'        => $academicYear,
            'student_id'           => $student->id,
            'user_id'              => $user->id,
            'class_id'             => $student->class_id,
            'section_id'           => $student->section_id,
            'leave_type'           => $request->leave_type,
            'title'                => trim($request->title),
            'reason'               => $request->reason,
            'from_date'            => $request->from_date,
            'to_date'              => $request->to_date,
            'total_days'           => $totalDays,
            'attachment_path'      => $attachmentPath,
            'declaration_id'       => $request->declaration_id,
            'declaration_accepted' => $request->has('declaration_accepted'),
            'status'               => 'pending',
        ]);

        // Audit Log
        $stuName = trim($student->first_name . ' ' . ($student->last_name ?? ''));
        \App\Models\ImplementationTracker\ImplActivityLog::create([
            'school_id'     => $schoolId,
            'tab_name'      => 'Student Leave',
            'row_reference' => 'Leave App #' . $leaveApp->id,
            'field_changed' => 'Leave Created',
            'old_value'     => null,
            'new_value'     => "{$request->leave_type} - {$request->title} ({$totalDays} days)",
            'changed_by'    => $user->name ?? $stuName,
            'changed_at'    => now(),
        ]);

        return redirect()->back()->with('success', 'Student Leave application submitted successfully.');
    }

    public function fees()
    {
        $data = $this->getStudentData(auth()->user());
        $config = \App\Models\FeeConfiguration::where('school_id', auth()->user()->school_id)->first();
        if ($data['student']) {
            $query = \App\Models\StudentFee::where('student_id', $data['student']->id)
                ->with(['category', 'component'])
                ->orderBy('installment_no', 'asc')
                ->orderBy('due_date', 'asc');

            if ($config?->parent_show_only_current_installment) {
                $firstUnpaid = (clone $query)->where('status', '!=', 'paid')->first();
                if ($firstUnpaid) {
                    $query->where('installment_no', $firstUnpaid->installment_no);
                }
            }

            $fees = $query->get();
        } else {
            $fees = collect();
        }
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

            $publishedReportCards = \App\Models\ReportCardHistoryStudent::where('student_id', $student->id)
                ->where('is_published', true)
                ->with(['history.class', 'history.section', 'history.template'])
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
            $publishedReportCards = collect();
            $offlineTests = collect();
            $scheduledExams = collect();
        }
        return view('parent.exams', array_merge($data, compact('marks', 'publishedReportCards', 'offlineTests', 'scheduledExams')));
    }

    public function downloadReportCard(\Illuminate\Http\Request $request, \App\Models\ReportCardHistoryStudent $record)
    {
        $user = auth()->user();
        $student = $this->resolveStudent($user);

        if (!$student || $record->student_id !== $student->id || !$record->is_published) {
            abort(403, 'Unauthorized access to this report card.');
        }

        $examController = new \App\Http\Controllers\School\ExaminationController();
        $html = $examController->renderStudentReportCardHtml($student->school_id, $record->history?->academic_session_id, $student, $record->exam_name, $record->template_id);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
        $pdf->setPaper('a4', 'portrait');
        $filename = "ReportCard_" . str_replace(' ', '_', $student->full_name) . ".pdf";

        if ($request->query('action') === 'view') {
            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"'
            ]);
        }

        return $pdf->download($filename);
    }

    public function notices()
    {
        $user = auth()->user();
        $data = $this->getStudentData($user);
        $notices = \App\Models\Notice::where('school_id', $user->school_id)
            ->whereIn('target_audience', ['all', 'students'])
            ->where(function($q) {
                $q->whereNull('publish_at')->orWhere('publish_at', '<=', now());
            })
            ->orderBy('created_at', 'desc')
            ->get();
        return view('parent.notices', array_merge($data, compact('notices')));
    }

    public function surveys()
    {
        $user = auth()->user();
        $data = $this->getStudentData($user);

        // Auto check scheduled surveys
        \App\Models\Survey::checkAndPublishScheduled($user->school_id);

        $studentClassId = $data['student']->class_id ?? null;
        $studentSectionId = $data['student']->section_id ?? null;

        $surveys = \App\Models\Survey::where('school_id', $user->school_id)
            ->where('is_active', true)
            ->where(function($q) use ($studentClassId, $studentSectionId) {
                $q->whereIn('target_audience', ['all', 'parents', 'students_parents']);
                if ($studentClassId) {
                    $q->orWhere('class_id', $studentClassId);
                }
            })
            ->with(['questions.options', 'questions.responses', 'options', 'responses'])
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

        $alreadyVoted = \App\Models\SurveyResponse::where('survey_id', $survey->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyVoted) {
            return back()->with('error', 'You have already completed this survey.');
        }

        // Support multi-question answers array AND single option_id fallback
        if ($request->has('answers') && is_array($request->answers)) {
            $request->validate([
                'answers' => 'required|array|min:1',
                'answers.*' => 'required|exists:survey_options,id',
            ]);

            foreach ($request->answers as $questionId => $optionId) {
                \App\Models\SurveyResponse::create([
                    'survey_id'          => $survey->id,
                    'survey_question_id' => $questionId,
                    'survey_option_id'   => $optionId,
                    'user_id'            => $user->id,
                ]);

                \App\Models\SurveyOption::where('id', $optionId)->increment('votes');
            }
        } elseif ($request->has('option_id')) {
            $request->validate([
                'option_id' => 'required|exists:survey_options,id',
            ]);

            $option = \App\Models\SurveyOption::find($request->option_id);

            \App\Models\SurveyResponse::create([
                'survey_id'          => $survey->id,
                'survey_question_id' => $option->survey_question_id ?? null,
                'survey_option_id'   => $request->option_id,
                'user_id'            => $user->id,
            ]);

            $option->increment('votes');
        } else {
            return back()->with('error', 'Please select an option for each question.');
        }

        return back()->with('success', 'Your survey responses have been submitted successfully!');
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

        $chatMsg = \App\Models\ChatMessage::create([
            'school_id' => $user->school_id,
            'sender_id' => $user->id,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        \App\Services\NotificationService::send([
            'school_id'    => $user->school_id,
            'user_id'      => $request->receiver_id,
            'title'        => 'New Message from ' . $user->name,
            'message'      => \Illuminate\Support\Str::limit($request->message, 80),
            'module'       => 'communication',
            'type'         => 'chat',
            'related_id'   => $chatMsg->id,
            'icon'         => 'fa-comments',
            'color'        => '#2563eb',
            'action_url'   => route('school.communication.chat', ['user_id' => $user->id]),
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

    public function profile()
    {
        $data = $this->getStudentData();
        $student = $data['student'];
        if ($student) {
            return view('parent.profile', array_merge($data, compact('student')));
        }
        return redirect()->route('parent.dashboard');
    }

    public function settings()
    {
        $data = $this->getStudentData();
        return view('parent.settings', $data);
    }
}

