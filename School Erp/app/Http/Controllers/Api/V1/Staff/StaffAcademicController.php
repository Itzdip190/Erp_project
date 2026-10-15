<?php

namespace App\Http\Controllers\Api\V1\Staff;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\LibraryBook;
use App\Models\LibraryTransaction;
use App\Models\Notice;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\SectionSubjectStaff;
use App\Models\Staff;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffAcademicController extends Controller
{
    /**
     * Get Teacher Assigned Classes & Sections Overview
     */
    public function classOverview(Request $request): JsonResponse
    {
        $user = auth()->user();
        $schoolId = $user->school_id ?? 1;

        $staff = Staff::where('user_id', $user->id)->first()
            ?? Staff::where('school_id', $schoolId)->where(function($q) use ($user) {
                $q->where('email', $user->email)->orWhere('phone', $user->phone);
            })->first();

        if (!$staff && !empty($user->name)) {
            $firstName = explode(' ', trim($user->name))[0];
            $staff = Staff::where('school_id', $schoolId)
                ->where(function($q) use ($firstName) {
                    $q->where('first_name', 'LIKE', "%{$firstName}%")
                      ->orWhere('last_name', 'LIKE', "%{$firstName}%");
                })
                ->first();
        }

        $staffIds = array_values(array_unique(array_filter([$staff?->id, $user->id])));

        // Current session strictly anchored to today's current calendar date (matching Web ERP TeacherDashboard)
        $today = today()->toDateString();
        $currentSession = null;
        if (class_exists(AcademicSession::class)) {
            $currentSession = AcademicSession::where('school_id', $schoolId)
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->first();

            if (!$currentSession) {
                $currentSession = AcademicSession::resolveCurrentSessionForUser($user, $schoolId);
            }
            if (!$currentSession) {
                $currentSession = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first()
                    ?? AcademicSession::where('school_id', $schoolId)->latest('id')->first();
            }
        }
        $sessionId = $currentSession?->id;

        // Class Teacher sections
        $classTeacherSections = [];
        $recordedSectionIds = [];

        if (!empty($staffIds)) {
            $ctSections = Section::where('school_id', $schoolId)
                ->where(function ($q) use ($staffIds) {
                    $q->whereIn('class_teacher_id', $staffIds)
                      ->orWhereIn('assistant_class_teacher_id', $staffIds);
                })
                ->with('schoolClass')
                ->get();

            foreach ($ctSections as $sec) {
                if (in_array($sec->id, $recordedSectionIds, true)) {
                    continue;
                }
                $recordedSectionIds[] = $sec->id;

                // Query students enrolled strictly in current academic session
                $studentCount = 0;
                if ($sessionId && method_exists(Student::class, 'scopeInAcademicSession')) {
                    $studentCount = Student::where('school_id', $schoolId)
                        ->activeStudents()
                        ->inAcademicSession($sessionId, null, $sec->id)
                        ->count();
                }
                if ($studentCount === 0) {
                    // Fallback to active students in this section if session not explicitly linked
                    $studentCount = Student::where('school_id', $schoolId)
                        ->activeStudents()
                        ->where('section_id', $sec->id)
                        ->when($sessionId, function($q) use ($sessionId) {
                            $q->where(function($sq) use ($sessionId) {
                                $sq->where('academic_session_id', $sessionId)
                                   ->orWhereNull('academic_session_id');
                            });
                        })
                        ->count();
                }

                $classTeacherSections[] = [
                    'id' => $sec->id,
                    'class_id' => $sec->class_id,
                    'class_name' => $sec->schoolClass?->name ?? ('Class ' . $sec->class_id),
                    'section_name' => $sec->name,
                    'display_name' => ($sec->schoolClass?->name ?? 'Class') . ' - ' . $sec->name,
                    'is_class_teacher' => true,
                    'subject_name' => 'All Subjects (Class Teacher)',
                    'total_students' => $studentCount,
                    'room_number' => $sec->room_no ?? ('Room ' . $sec->id),
                ];
            }
        }

        // Subject Teacher sections (scoped to current academic session)
        $subjectTeacherSections = [];
        if (!empty($staffIds)) {
            // A. SectionSubjectStaff filtered by active session
            $sssQuery = SectionSubjectStaff::where('school_id', $schoolId)
                ->whereIn('staff_id', $staffIds);
            if ($sessionId && \Illuminate\Support\Facades\Schema::hasColumn('section_subject_staff', 'academic_session_id')) {
                $sssQuery->where('academic_session_id', $sessionId);
            }
            $sssRecords = $sssQuery->with(['section.schoolClass', 'subject'])->get();

            foreach ($sssRecords as $record) {
                $sec = $record->section;
                if (!$sec || in_array($sec->id, $recordedSectionIds, true)) {
                    continue;
                }
                $recordedSectionIds[] = $sec->id;

                $studentCount = 0;
                if ($sessionId && method_exists(Student::class, 'scopeInAcademicSession')) {
                    $studentCount = Student::where('school_id', $schoolId)
                        ->activeStudents()
                        ->inAcademicSession($sessionId, null, $sec->id)
                        ->count();
                }
                if ($studentCount === 0) {
                    $studentCount = Student::where('school_id', $schoolId)
                        ->activeStudents()
                        ->where('section_id', $sec->id)
                        ->when($sessionId, function($q) use ($sessionId) {
                            $q->where(function($sq) use ($sessionId) {
                                $sq->where('academic_session_id', $sessionId)
                                   ->orWhereNull('academic_session_id');
                            });
                        })
                        ->count();
                }

                $subjectTeacherSections[] = [
                    'id' => $sec->id,
                    'class_id' => $sec->class_id,
                    'class_name' => $sec->schoolClass?->name ?? ('Class ' . $sec->class_id),
                    'section_name' => $sec->name,
                    'display_name' => ($sec->schoolClass?->name ?? 'Class') . ' - ' . $sec->name,
                    'is_class_teacher' => false,
                    'subject_name' => $record->subject?->name ?? 'Subject Teacher',
                    'total_students' => $studentCount,
                    'room_number' => $sec->room_no ?? ('Room ' . $sec->id),
                ];
            }

            // B. ClassTimetableCell
            if (\Illuminate\Support\Facades\Schema::hasTable('class_timetable_cells')) {
                $timetableCells = \App\Models\ClassTimetableCell::where('school_id', $schoolId)
                    ->whereIn('teacher_id', $staffIds)
                    ->with(['section.schoolClass', 'subject'])
                    ->get();

                foreach ($timetableCells as $cell) {
                    $sec = $cell->section;
                    if (!$sec || in_array($sec->id, $recordedSectionIds, true)) {
                        continue;
                    }
                    $recordedSectionIds[] = $sec->id;

                    $studentCount = 0;
                    if ($sessionId && method_exists(Student::class, 'scopeInAcademicSession')) {
                        $studentCount = Student::where('school_id', $schoolId)
                            ->activeStudents()
                            ->inAcademicSession($sessionId, null, $sec->id)
                            ->count();
                    }
                    if ($studentCount === 0) {
                        $studentCount = Student::where('school_id', $schoolId)
                            ->activeStudents()
                            ->where('section_id', $sec->id)
                            ->when($sessionId, function($q) use ($sessionId) {
                                $q->where(function($sq) use ($sessionId) {
                                    $sq->where('academic_session_id', $sessionId)
                                       ->orWhereNull('academic_session_id');
                                });
                            })
                            ->count();
                    }

                    $subjectTeacherSections[] = [
                        'id' => $sec->id,
                        'class_id' => $sec->class_id,
                        'class_name' => $sec->schoolClass?->name ?? ('Class ' . $sec->class_id),
                        'section_name' => $sec->name,
                        'display_name' => ($sec->schoolClass?->name ?? 'Class') . ' - ' . $sec->name,
                        'is_class_teacher' => false,
                        'subject_name' => $cell->subject?->name ?? 'Subject Teacher',
                        'total_students' => $studentCount,
                        'room_number' => $sec->room_no ?? ('Room ' . $sec->id),
                    ];
                }
            }

            // C. ClassSubjectTeacher
            if (\Illuminate\Support\Facades\Schema::hasTable('class_subject_teacher')) {
                $cstRecords = \App\Models\ClassSubjectTeacher::where('school_id', $schoolId)
                    ->whereIn('teacher_id', $staffIds)
                    ->with(['section.schoolClass', 'subject'])
                    ->get();

                foreach ($cstRecords as $cst) {
                    $sec = $cst->section;
                    if (!$sec || in_array($sec->id, $recordedSectionIds, true)) {
                        continue;
                    }
                    $recordedSectionIds[] = $sec->id;

                    $studentCount = 0;
                    if ($sessionId && method_exists(Student::class, 'scopeInAcademicSession')) {
                        $studentCount = Student::where('school_id', $schoolId)
                            ->activeStudents()
                            ->inAcademicSession($sessionId, null, $sec->id)
                            ->count();
                    }
                    if ($studentCount === 0) {
                        $studentCount = Student::where('school_id', $schoolId)
                            ->activeStudents()
                            ->where('section_id', $sec->id)
                            ->when($sessionId, function($q) use ($sessionId) {
                                $q->where(function($sq) use ($sessionId) {
                                    $sq->where('academic_session_id', $sessionId)
                                       ->orWhereNull('academic_session_id');
                                });
                            })
                            ->count();
                    }

                    $subjectTeacherSections[] = [
                        'id' => $sec->id,
                        'class_id' => $sec->class_id,
                        'class_name' => $sec->schoolClass?->name ?? ('Class ' . $sec->class_id),
                        'section_name' => $sec->name,
                        'display_name' => ($sec->schoolClass?->name ?? 'Class') . ' - ' . $sec->name,
                        'is_class_teacher' => false,
                        'subject_name' => $cst->subject?->name ?? 'Subject Teacher',
                        'total_students' => $studentCount,
                        'room_number' => $sec->room_no ?? ('Room ' . $sec->id),
                    ];
                }
            }
        }

        // If no sections assigned specifically, provide school sections with real counts
        if (empty($classTeacherSections) && empty($subjectTeacherSections)) {
            $fallbackSections = Section::where('school_id', $schoolId)
                ->with('schoolClass')
                ->take(5)
                ->get();

            foreach ($fallbackSections as $idx => $sec) {
                $studentCount = 0;
                if ($sessionId && method_exists(Student::class, 'scopeInAcademicSession')) {
                    $studentCount = Student::where('school_id', $schoolId)
                        ->activeStudents()
                        ->inAcademicSession($sessionId, null, $sec->id)
                        ->count();
                }
                if ($studentCount === 0) {
                    $studentCount = Student::where('school_id', $schoolId)
                        ->activeStudents()
                        ->where('section_id', $sec->id)
                        ->when($sessionId, function($q) use ($sessionId) {
                            $q->where(function($sq) use ($sessionId) {
                                $sq->where('academic_session_id', $sessionId)
                                   ->orWhereNull('academic_session_id');
                            });
                        })
                        ->count();
                }

                $subjectTeacherSections[] = [
                    'id' => $sec->id,
                    'class_id' => $sec->class_id,
                    'class_name' => $sec->schoolClass?->name ?? ('Class ' . $sec->class_id),
                    'section_name' => $sec->name,
                    'display_name' => ($sec->schoolClass?->name ?? 'Class') . ' - ' . $sec->name,
                    'is_class_teacher' => false,
                    'subject_name' => 'General Teacher',
                    'total_students' => $studentCount,
                    'room_number' => $sec->room_no ?? ('Room ' . ($idx + 101)),
                ];
            }
        }

        $allSections = array_merge($classTeacherSections, $subjectTeacherSections);
        $totalStudents = array_sum(array_column($allSections, 'total_students'));

        return response()->json([
            'success' => true,
            'data' => [
                'class_teacher_sections' => $classTeacherSections,
                'subject_teacher_sections' => $subjectTeacherSections,
                'total_assigned_classes' => count($allSections),
                'total_assigned_students' => $totalStudents,
                'academic_session' => [
                    'id' => $currentSession?->id,
                    'name' => $currentSession?->name,
                    'is_current' => (bool)$currentSession?->is_current,
                ],
            ],
        ]);
    }

    /**
     * Get School Notices & Circulars (Staff / Teacher Scoped)
     */
    public function notices(Request $request): JsonResponse
    {
        $user = auth()->user();
        $schoolId = $user->school_id ?? 1;

        $query = Notice::where('school_id', $schoolId);

        // Published notices only
        $query->where(function ($q) {
            $q->whereNull('publish_at')
              ->orWhere('publish_at', '<=', now());
        });

        // Target audience: all, teachers, teacher, staff
        $allowedAudiences = ['all', 'teachers', 'teacher', 'staff', 'employees'];
        $query->where(function ($q) use ($allowedAudiences) {
            $q->whereIn('target_audience', $allowedAudiences)
              ->orWhereNull('target_audience');
        });

        $notices = $query->orderByDesc('publish_at')
            ->orderByDesc('created_at')
            ->take(30)
            ->get();

        $data = $notices->map(function ($n) {
            $publishDate = $n->publish_at ? Carbon::parse($n->publish_at) : ($n->created_at ? Carbon::parse($n->created_at) : now());
            return [
                'id' => $n->id,
                'title' => $n->title,
                'message' => $n->content ?? $n->description ?? '',
                'notice_date' => $publishDate->toDateString(),
                'publish_on' => $publishDate->toDateString(),
                'time_ago' => $publishDate->diffForHumans(),
                'created_by' => $n->created_by_name ?? 'School Administration',
                'category' => ucfirst($n->target_audience ?? 'Notice'),
                'priority' => $n->priority ?? 'normal',
                'attachment_url' => $n->attachment ? asset('storage/' . $n->attachment) : null,
                'is_read' => false,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get School Library Dashboard Data & Catalog
     */
    public function library(Request $request): JsonResponse
    {
        $user = auth()->user();
        $schoolId = $user->school_id ?? 1;

        $totalBooks = LibraryBook::where('school_id', $schoolId)->sum('total_copies') ?: 0;
        $totalTitles = LibraryBook::where('school_id', $schoolId)->count() ?: 0;
        
        $circulatedCount = LibraryTransaction::where('school_id', $schoolId)
            ->where('status', 'issued')
            ->count() ?: 0;

        $finePending = LibraryTransaction::where('school_id', $schoolId)
            ->where('status', '!=', 'returned')
            ->sum('total_fine') ?: 0;

        $fineCollected = LibraryTransaction::where('school_id', $schoolId)
            ->where('status', 'returned')
            ->sum('total_fine') ?: 0;

        $books = LibraryBook::where('school_id', $schoolId)
            ->orderBy('title')
            ->take(50)
            ->get()
            ->map(function ($b) {
                return [
                    'id' => $b->id,
                    'title' => $b->title,
                    'author' => $b->author,
                    'isbn' => $b->isbn ?? $b->accession_no ?? ('BK-' . $b->id),
                    'rack_location' => $b->rack_location ?? 'Shelf A',
                    'total_copies' => (int) $b->total_copies,
                    'available_copies' => (int) $b->available_copies,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_books' => (int)$totalBooks,
                    'total_titles' => (int)$totalTitles,
                    'circulated_books' => (int)$circulatedCount,
                    'discarded_books' => 0,
                    'fine_pending' => (float)$finePending,
                    'fine_collected' => (float)$fineCollected,
                ],
                'books' => $books,
            ],
        ]);
    }

    /**
     * Get Chat Recipients Categorized:
     * 1. Parents (Scoped only to students of assigned classes/sections)
     * 2. Teachers (All school faculty)
     * 3. School Admin (Principal, Management, Office)
     */
    public function recipients(Request $request): JsonResponse
    {
        $user = auth()->user();
        $schoolId = $user->school_id ?? 1;

        $staff = Staff::where('user_id', $user->id)->first();
        if (!$staff) {
            $isTeacherRole = ($user->role === 'teacher') || (method_exists($user, 'hasRole') && rescue(fn() => $user->hasRole('teacher'), false));
            if ($isTeacherRole) {
                $staff = Staff::where('school_id', $schoolId)->where(function($q) use ($user) {
                    $q->where('email', $user->email)->orWhere('phone', $user->phone);
                })->first();
            }
        }

        // 1. Get assigned sections for this teacher (Class Teacher, Subject Teacher, and Timetable)
        $assignedSectionIds = [];
        if ($staff) {
            $secIdsFromCt = Section::where('school_id', $schoolId)
                ->where(function ($q) use ($staff) {
                    $q->where('class_teacher_id', $staff->id)
                      ->orWhere('assistant_class_teacher_id', $staff->id);
                })
                ->pluck('id')->toArray();
            $secIdsFromSss = SectionSubjectStaff::where('staff_id', $staff->id)->pluck('section_id')->toArray();
            $secIdsFromTimetable = \App\Models\ClassTimetableCell::where('school_id', $schoolId)
                ->where('teacher_id', $staff->id)
                ->pluck('section_id')->toArray();
            $assignedSectionIds = array_values(array_unique(array_filter(array_merge($secIdsFromCt, $secIdsFromSss, $secIdsFromTimetable))));
        }

        // If no sections assigned or teacher wants broader reach, include sections
        if (empty($assignedSectionIds)) {
            $assignedSectionIds = Section::where('school_id', $schoolId)->pluck('id')->toArray();
        }

        // Current session strictly anchored to today's date
        $today = today()->toDateString();
        $currentSession = AcademicSession::where('school_id', $schoolId)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->first()
            ?? AcademicSession::resolveCurrentSessionForUser($user, $schoolId);
        $sessionId = $currentSession?->id;

        // Fetch students in assigned sections in current academic session
        $studentsQuery = Student::where('school_id', $schoolId)
            ->activeStudents()
            ->with(['class', 'section']);

        if (!empty($assignedSectionIds)) {
            if ($sessionId && method_exists(Student::class, 'scopeInAcademicSession')) {
                $studentsQuery->inAcademicSession($sessionId, null, $assignedSectionIds);
            } else {
                $studentsQuery->whereIn('section_id', $assignedSectionIds);
            }
        }

        $students = $studentsQuery->orderBy('section_id')
            ->orderBy('roll_number')
            ->get();

        if ($students->isEmpty() && !empty($assignedSectionIds)) {
            $students = Student::where('school_id', $schoolId)
                ->activeStudents()
                ->whereIn('section_id', $assignedSectionIds)
                ->with(['class', 'section'])
                ->orderBy('section_id')
                ->orderBy('roll_number')
                ->get();
        }

        $parentsList = [];
        foreach ($students as $st) {
            $parentName = $st->father_name ?: ($st->mother_name ?: ($st->guardian_name ?: ('Parent of ' . $st->first_name)));
            $relation = $st->father_name ? 'Father' : ($st->mother_name ? 'Mother' : ($st->guardian_relationship ?: 'Guardian'));
            $phone = $st->father_phone ?: ($st->mother_phone ?: ($st->guardian_phone ?: ($st->phone ?: '—')));
            $className = ($st->class?->name ?? 'Class') . ' - ' . ($st->section?->name ?? '');

            $avatarUrl = null;
            if ($st->photo) {
                $avatarUrl = str_starts_with($st->photo, 'http') ? $st->photo : asset('storage/' . ltrim($st->photo, '/'));
            }

            $parentsList[] = [
                'id' => 'parent_' . $st->id,
                'user_id' => $st->user_id ?? $st->id,
                'student_id' => $st->id,
                'student_name' => trim($st->first_name . ' ' . $st->last_name),
                'parent_name' => $parentName,
                'relation' => $relation,
                'display_name' => $parentName . ' (' . $relation . ' of ' . $st->first_name . ')',
                'class_name' => $className,
                'section_id' => $st->section_id,
                'roll_number' => $st->roll_number ?? '—',
                'admission_number' => $st->admission_number ?? ('ADM/' . $st->id),
                'phone' => $phone,
                'avatar' => $avatarUrl,
            ];
        }

        // 2. All Teachers & Faculty
        $teachersQuery = Staff::where('school_id', $schoolId)
            ->where(function ($q) {
                $q->where('is_active', 1)
                  ->orWhere('is_active', true)
                  ->orWhereNull('is_active');
            });

        if ($staff) {
            $teachersQuery->where('id', '!=', $staff->id);
        }

        $teachers = $teachersQuery->with(['designation', 'department'])
            ->orderBy('first_name')
            ->get()
            ->map(function ($t) {
                $avatar = null;
                if ($t->photo) {
                    $avatar = str_starts_with($t->photo, 'http') ? $t->photo : asset('storage/' . ltrim($t->photo, '/'));
                }
                return [
                    'id' => 'teacher_' . $t->id,
                    'user_id' => $t->user_id ?? $t->id,
                    'staff_id' => $t->id,
                    'name' => trim($t->first_name . ' ' . $t->last_name),
                    'designation' => $t->designation?->name ?? 'Faculty Member',
                    'department' => $t->department?->name ?? 'Academic',
                    'email' => $t->email,
                    'phone' => $t->phone,
                    'avatar' => $avatar,
                ];
            });

        // 3. School Admin & Management
        $adminsMap = [];

        // Admin users from Users table (Principal, Admin, Management)
        $adminUsers = User::where('school_id', $schoolId)
            ->where(function ($q) {
                $q->whereIn('role', ['admin', 'school_admin', 'superadmin', 'principal', 'management', 'coordinator'])
                  ->orWhere('email', 'like', '%admin%')
                  ->orWhere('name', 'like', '%admin%')
                  ->orWhere('name', 'like', '%principal%');
            })
            ->get();

        foreach ($adminUsers as $au) {
            $key = strtolower(trim($au->email ?: ('user_' . $au->id)));
            $roleLabel = ucfirst(str_replace('_', ' ', $au->role ?: 'Administrator'));
            $adminsMap[$key] = [
                'id' => 'admin_user_' . $au->id,
                'user_id' => $au->id,
                'staff_id' => $au->id,
                'name' => $au->name ?: 'School Administrator',
                'designation' => $roleLabel,
                'department' => 'School Leadership & Administration',
                'email' => $au->email,
                'phone' => $au->phone,
                'avatar' => $au->photo ? (str_starts_with($au->photo, 'http') ? $au->photo : asset('storage/' . ltrim($au->photo, '/'))) : null,
            ];
        }

        // Administrative staff
        $adminStaff = Staff::where('school_id', $schoolId)
            ->where(function ($q) {
                $q->whereHas('designation', function ($d) {
                    $d->where('name', 'like', '%principal%')
                      ->orWhere('name', 'like', '%director%')
                      ->orWhere('name', 'like', '%admin%')
                      ->orWhere('name', 'like', '%manager%')
                      ->orWhere('name', 'like', '%coordinator%');
                });
            })
            ->with(['designation', 'department'])
            ->get();

        foreach ($adminStaff as $as) {
            $key = strtolower(trim($as->email ?: ('staff_' . $as->id)));
            if (!isset($adminsMap[$key])) {
                $adminsMap[$key] = [
                    'id' => 'admin_staff_' . $as->id,
                    'user_id' => $as->user_id ?? $as->id,
                    'staff_id' => $as->id,
                    'name' => trim($as->first_name . ' ' . $as->last_name) ?: 'School Administrator',
                    'designation' => $as->designation?->name ?? 'Administration',
                    'department' => $as->department?->name ?? 'Administration & Management',
                    'email' => $as->email,
                    'phone' => $as->phone,
                    'avatar' => $as->photo ? (str_starts_with($as->photo, 'http') ? $as->photo : asset('storage/' . ltrim($as->photo, '/'))) : null,
                ];
            }
        }

        // Fallback default admin entries if none found in DB
        if (empty($adminsMap)) {
            $school = School::find($schoolId);
            $adminsMap['principal'] = [
                'id' => 'admin_1',
                'staff_id' => 1,
                'name' => 'Principal Office',
                'designation' => 'Principal',
                'department' => 'School Leadership',
                'email' => $school?->email ?? 'principal@school.edu',
                'phone' => $school?->phone ?? '+91 98765 00000',
                'avatar' => null,
            ];
            $adminsMap['office'] = [
                'id' => 'admin_2',
                'staff_id' => 2,
                'name' => 'Administrative Office',
                'designation' => 'School Admin',
                'department' => 'Main Office',
                'email' => 'admin@school.edu',
                'phone' => $school?->phone ?? '+91 98765 00001',
                'avatar' => null,
            ];
        }

        $admins = array_values($adminsMap);

        return response()->json([
            'success' => true,
            'data' => [
                'parents' => $parentsList,
                'teachers' => $teachers,
                'admins' => $admins,
            ],
        ]);
    }

    /**
     * Get Teacher / Staff Payroll Summary & Payslips
     */
    public function payroll(Request $request): JsonResponse
    {
        $user = auth()->user();
        $schoolId = $user->school_id ?? 1;

        $staff = Staff::where('user_id', $user->id)->first()
            ?? Staff::where('school_id', $schoolId)->where(function($q) use ($user) {
                $q->where('email', $user->email)->orWhere('phone', $user->phone);
            })->first();

        $payslips = [];
        $basicSalary = (float)($staff?->basic_salary ?? 0);

        if ($staff) {
            $records = \App\Models\StaffPayroll::where('staff_id', $staff->id)
                ->orderByDesc('id')
                ->take(12)
                ->get();

            foreach ($records as $p) {
                $monthName = $p->salary_month ? ($p->salary_month . ' ' . $p->salary_year) : ($p->payroll_month ?? 'Month');
                $payslips[] = [
                    'id' => $p->id,
                    'month' => $monthName,
                    'gross' => (float)$p->gross_salary,
                    'deductions' => (float)$p->deductions,
                    'net' => (float)$p->net_payable,
                    'status' => ucfirst($p->payment_status ?: ($p->status ?: 'Paid')),
                    'date' => $p->finalised_at ? Carbon::parse($p->finalised_at)->format('d M Y') : ($p->created_at ? $p->created_at->format('d M Y') : '—'),
                ];
            }
        }

        // Structure breakdown from staff profile if configured
        $hra = $basicSalary > 0 ? round($basicSalary * 0.4, 2) : 0;
        $conveyance = $basicSalary > 0 ? 1600 : 0;
        $special = $basicSalary > 0 ? round($basicSalary * 0.1, 2) : 0;
        $gross = $basicSalary + $hra + $conveyance + $special;
        $pf = $basicSalary > 0 ? round($basicSalary * 0.12, 2) : 0;
        $tds = $gross > 50000 ? round($gross * 0.05, 2) : 0;
        $pt = $gross > 15000 ? 200 : 0;
        $totalDeductions = $pf + $tds + $pt;
        $net = $gross - $totalDeductions;

        return response()->json([
            'success' => true,
            'data' => [
                'structure' => [
                    'basic_pay' => $basicSalary,
                    'hra' => $hra,
                    'conveyance_allowance' => $conveyance,
                    'special_allowance' => $special,
                    'gross_earnings' => $gross,
                    'pf_deduction' => $pf,
                    'tax_deducted_source' => $tds,
                    'professional_tax' => $pt,
                    'other_deductions' => 0,
                    'total_deductions' => $totalDeductions,
                    'net_salary' => $net,
                ],
                'payslips' => $payslips,
            ],
        ]);
    }
}
