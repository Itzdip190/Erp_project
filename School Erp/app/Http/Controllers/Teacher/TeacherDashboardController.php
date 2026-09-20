<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\ClassSubjectTeacher;
use App\Models\ClassTimetableCell;
use App\Models\Event;
use App\Models\Notice;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\SectionSubjectStaff;
use App\Models\Staff;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentMark;
use App\Models\StudyMaterial;
use App\Models\TeacherAssignment;
use App\Models\TimetableSubstitution;
use App\Support\ModuleRegistry;
use App\Support\StaffAccessHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class TeacherDashboardController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login');
            }

            $school = null;
            if ($user->school_id && Schema::hasTable('schools')) {
                $school = School::find($user->school_id);
            }
            if (!$school && Schema::hasTable('schools')) {
                $school = app('currentSchool') ?? School::first();
            }

            $schoolId = $school?->id ?? $user->school_id ?? 1;

            $todayEvents = Event::where('school_id', $schoolId)
                ->whereDate('start_date', '<=', today()->toDateString())
                ->whereDate('end_date', '>=', today()->toDateString())
                ->get();

            $currentSession = null;
            if (Schema::hasTable('academic_sessions')) {
                $currentSession = AcademicSession::resolveCurrentSessionForUser($user, $schoolId);
            }

            // Get Staff profile details
            $staff = null;
            if (Schema::hasTable('staff')) {
                $staff = Staff::where('school_id', $schoolId)->where('user_id', $user->id)->first();
                if (!$staff) {
                    $staff = Staff::where('user_id', $user->id)->first();
                }
                if (!$staff && !empty($user->email)) {
                    $staff = Staff::where('school_id', $schoolId)->where('email', $user->email)->first()
                        ?? Staff::where('email', $user->email)->first();
                    if ($staff && !$staff->user_id) {
                        $staff->update(['user_id' => $user->id]);
                    }
                }
                if (!$staff && !empty($user->name)) {
                    $firstName = explode(' ', trim($user->name))[0];
                    $staff = Staff::where('school_id', $schoolId)
                        ->where(function($q) use ($firstName) {
                            $q->where('first_name', 'LIKE', "%{$firstName}%")
                              ->orWhere('last_name', 'LIKE', "%{$firstName}%");
                        })
                        ->first();
                }
            }

            // Teacher Avatar URL
            $teacherAvatarUrl = $user->photo_url ?? $staff?->photo_url ?? null;

            // 1. Assigned Classes & Subjects for THIS teacher
            $assignedClassSections = collect();
            $timetableSectionIds = [];
            $timetableClassIds = [];
            if ($staff && Schema::hasTable('class_timetable_cells')) {
                $assignedClassSections = ClassTimetableCell::where('school_id', $schoolId)
                    ->where('teacher_id', $staff->id)
                    ->select('class_id', 'section_id', 'subject_id')
                    ->distinct()
                    ->with(['schoolClass', 'section', 'subject'])
                    ->get();
                $timetableSectionIds = $assignedClassSections->pluck('section_id')->toArray();
                $timetableClassIds   = $assignedClassSections->pluck('class_id')->toArray();
            }

            // Include section IDs from SectionSubjectStaff
            $sssSectionIds = [];
            if ($staff && Schema::hasTable('section_subject_staff')) {
                $sssSectionIds = SectionSubjectStaff::where('school_id', $schoolId)
                    ->where('staff_id', $staff->id)
                    ->pluck('section_id')->toArray();
            }

            // Include section & class IDs from ClassSubjectTeacher
            $cstSectionIds = [];
            $cstClassIds = [];
            if ($staff && Schema::hasTable('class_subject_teacher')) {
                $cstRecords = ClassSubjectTeacher::where('school_id', $schoolId)
                    ->where('teacher_id', $staff->id)
                    ->select('class_id', 'section_id')
                    ->get();
                $cstSectionIds = $cstRecords->pluck('section_id')->toArray();
                $cstClassIds   = $cstRecords->pluck('class_id')->toArray();
            }

            // Include section & class IDs where staff is Primary Class Teacher or Assistant
            $classTeacherSectionIds = [];
            $classTeacherClassIds = [];
            if ($staff && Schema::hasTable('sections')) {
                $ctSections = Section::where('school_id', $schoolId)
                    ->where(function($q) use ($staff) {
                        $q->where('class_teacher_id', $staff->id)
                          ->orWhere('assistant_class_teacher_id', $staff->id);
                    })
                    ->select('id', 'class_id')
                    ->get();
                $classTeacherSectionIds = $ctSections->pluck('id')->toArray();
                $classTeacherClassIds   = $ctSections->pluck('class_id')->toArray();
            }

            // Include section & class IDs from Teacher Assignments table
            $assignmentSectionIds = [];
            $assignmentClassIds = [];
            if ($staff && Schema::hasTable('teacher_assignments')) {
                $taRecords = TeacherAssignment::where('school_id', $schoolId)
                    ->where('staff_id', $staff->id)
                    ->select('class_id', 'section_id')
                    ->get();
                $assignmentSectionIds = $taRecords->pluck('section_id')->toArray();
                $assignmentClassIds   = $taRecords->pluck('class_id')->toArray();
            }

            $uniqueSectionIds = array_values(array_unique(array_filter(array_merge(
                $timetableSectionIds,
                $sssSectionIds,
                $cstSectionIds,
                $classTeacherSectionIds,
                $assignmentSectionIds
            ))));

            $directClassIds = array_values(array_unique(array_filter(array_merge(
                $timetableClassIds,
                $cstClassIds,
                $classTeacherClassIds,
                $assignmentClassIds
            ))));

            $mappedClassIds = [];
            if (count($uniqueSectionIds) > 0 && Schema::hasTable('sections')) {
                $mappedClassIds = Section::where('school_id', $schoolId)
                    ->whereIn('id', $uniqueSectionIds)
                    ->pluck('class_id')->filter()->toArray();
            }
            $uniqueClassIds = array_values(array_unique(array_filter(array_merge($directClassIds, $mappedClassIds))));

            $classesAssignedCount = count($uniqueClassIds);

            // 2. Total Students in Assigned Classes / Sections
            $totalStudents = 0;
            if (Schema::hasTable('students') && (count($uniqueSectionIds) > 0 || count($uniqueClassIds) > 0)) {
                $sessId = $currentSession?->id;
                $studentQuery = Student::where('school_id', $schoolId)->activeStudents();

                $studentQuery->where(function($q) use ($uniqueSectionIds, $uniqueClassIds, $sessId) {
                    if (count($uniqueSectionIds) > 0) {
                        $q->where(function($sq) use ($uniqueSectionIds, $sessId) {
                            $sq->inAcademicSession($sessId, null, $uniqueSectionIds);
                        });
                    }
                    if (count($uniqueClassIds) > 0) {
                        $q->orWhere(function($sq) use ($uniqueClassIds, $sessId) {
                            $sq->inAcademicSession($sessId, $uniqueClassIds, null);
                        });
                    }
                });
                $totalStudents = $studentQuery->count();
            }

            // 3. Attendance Calculation (Strictly for Assigned Sections of this Teacher Today)
            $todayStr = date('Y-m-d');
            $totalAttCount = 0;
            $presentAttCount = 0;
            $absentAttCount = 0;
            $leaveAttCount = 0;
            $attendanceTodayPct = 0;

            if (Schema::hasTable('student_attendances') && count($uniqueSectionIds) > 0) {
                $todayAttQuery = StudentAttendance::where('school_id', $schoolId)
                    ->whereDate('date', $todayStr)
                    ->whereIn('section_id', $uniqueSectionIds);

                $totalAttCount = (clone $todayAttQuery)->count();
                if ($totalAttCount > 0) {
                    $presentAttCount = (clone $todayAttQuery)->whereIn('status', ['present', 'Present', 'p', 'P'])->count();
                    $absentAttCount  = (clone $todayAttQuery)->whereIn('status', ['absent', 'Absent', 'a', 'A'])->count();
                    $leaveAttCount   = (clone $todayAttQuery)->whereIn('status', ['leave', 'Leave', 'late', 'Late', 'l', 'L'])->count();

                    $attendanceTodayPct = round(($presentAttCount / $totalAttCount) * 100);
                }
            }

            // Monthly Attendance Overview (for Donut Chart)
            $monthTotalAtt = 0;
            $monthPresentAtt = 0;
            $monthAbsentAtt = 0;
            $monthLeaveAtt = 0;
            $monthAvgPct = 0;

            if (Schema::hasTable('student_attendances') && count($uniqueSectionIds) > 0) {
                $monthAttQuery = StudentAttendance::where('school_id', $schoolId)
                    ->whereYear('date', date('Y'))
                    ->whereMonth('date', date('m'))
                    ->whereIn('section_id', $uniqueSectionIds);

                $monthTotalAtt = (clone $monthAttQuery)->count();
                if ($monthTotalAtt > 0) {
                    $monthPresentAtt = (clone $monthAttQuery)->whereIn('status', ['present', 'Present', 'p', 'P'])->count();
                    $monthAbsentAtt  = (clone $monthAttQuery)->whereIn('status', ['absent', 'Absent', 'a', 'A'])->count();
                    $monthLeaveAtt   = (clone $monthAttQuery)->whereIn('status', ['leave', 'Leave', 'late', 'Late', 'l', 'L'])->count();

                    $monthAvgPct = round(($monthPresentAtt / $monthTotalAtt) * 100);
                }
            }

            // 4. Pending / Active Assignments for THIS staff
            $pendingAssignmentsCount = 0;
            if ($staff && Schema::hasTable('teacher_assignments')) {
                $pendingAssignmentsCount = TeacherAssignment::where('school_id', $schoolId)
                    ->where('staff_id', $staff->id)
                    ->count();
            }

            // 5. Average Score Calculation for Assigned Classes
            $avgScore = 0;
            if (Schema::hasTable('student_marks') && count($uniqueClassIds) > 0) {
                $calcAvg = StudentMark::where('school_id', $schoolId)
                    ->whereIn('class_id', $uniqueClassIds)
                    ->avg('marks_obtained');
                if ($calcAvg) {
                    $avgScore = round($calcAvg);
                }
            }

            // 6. Class Performance Bar Chart Data (Only Teacher's Assigned Classes)
            $classPerformance = [];
            if (Schema::hasTable('school_classes') && count($uniqueClassIds) > 0) {
                $schoolClasses = SchoolClass::where('school_id', $schoolId)
                    ->whereIn('id', $uniqueClassIds)
                    ->take(5)
                    ->get();
                foreach ($schoolClasses as $sc) {
                    $score = 0;
                    if (Schema::hasTable('student_marks')) {
                        $cAvg = StudentMark::where('school_id', $schoolId)->where('class_id', $sc->id)->avg('marks_obtained');
                        if ($cAvg) { $score = round($cAvg); }
                    }
                    $classPerformance[] = ['class' => $sc->name, 'score' => $score];
                }
            }

            // 7. Today's Schedule Timeline (Only Teacher's Scheduled Classes)
            $todayDay = date('l'); // e.g. Monday
            $todaysSchedule = collect();
            if ($staff && Schema::hasTable('class_timetable_cells')) {
                $todaysSchedule = ClassTimetableCell::where('school_id', $schoolId)
                    ->where(function($q) use ($staff) {
                        $q->where('teacher_id', $staff->id);
                        if (Schema::hasColumn('class_timetable_cells', 'secondary_teacher_id')) {
                            $q->orWhere('secondary_teacher_id', $staff->id);
                        }
                    })
                    ->where('day_of_week', $todayDay)
                    ->with(['schoolClass', 'section', 'subject', 'secondarySubject', 'period'])
                    ->get();
            }

            // 8. Recent Assignments (Only Teacher's Assignments)
            $recentAssignments = collect();
            if ($staff && Schema::hasTable('teacher_assignments')) {
                $recentAssignments = TeacherAssignment::where('school_id', $schoolId)
                    ->where('staff_id', $staff->id)
                    ->with(['schoolClass', 'section', 'submissions'])
                    ->latest()
                    ->take(4)
                    ->get();
            }

            // 9. Student Spotlight (From Assigned Classes)
            $spotlightStudent = null;
            if (Schema::hasTable('students') && (count($uniqueSectionIds) > 0 || count($uniqueClassIds) > 0)) {
                $sq = Student::where('school_id', $schoolId)->activeStudents();
                if (count($uniqueSectionIds) > 0) {
                    $sq->whereIn('section_id', $uniqueSectionIds);
                } elseif (count($uniqueClassIds) > 0) {
                    $sq->whereIn('class_id', $uniqueClassIds);
                }
                $spotlightStudent = $sq->first();
            }

            // 10. Granted Modules Permission Check
            $featureRouteMap = [
                // Overview
                'mis_report'            => 'school.dashboard.mis-report',
                'admin_dashboard'       => 'school.dashboard',
                'role_category'         => 'school.roles.index',
                'staff_access'          => 'school.roles.staff-access',
                'reset_password'        => 'school.settings.reset-password',
                
                // Institute Info
                'basic_info'            => 'school.settings.institute-info',
                'udise'                 => 'school.settings.udise',
                
                // Admin Role Management & Passwords
                'roles'                 => 'school.roles.index',
                'permissions'           => 'school.roles.permissions',
                'password_reset'        => 'school.passwords.reset',

                // Staff Management
                'staff_directory'       => 'school.staff.index',
                'add_staff'             => 'school.staff.create',
                'bulk_import'           => 'school.staff.import',
                'bulk_photo'            => 'school.staff.bulk-photo',
                
                // Leave Management
                'leave_basics'         => 'school.leave.basics',
                'staff_leave'          => 'teacher.leave.apply',
                'student_leave'        => 'school.leave.student',

                // Class, Subject & Teacher Allocation
                'class_overview'        => 'school.assignments.class-overview',
                'add_class'             => 'school.assignments.classes',
                'add_subject'           => 'school.assignments.subjects',
                'assign_teacher'        => 'school.assignments.teachers',

                // Timetable
                'class_timetable'       => 'school.timetable.class',
                'group_timetable'       => 'school.timetable.group',
                'teacher_timetable'     => 'school.timetable.teacher',
                'teacher_substitution'  => 'school.timetable.substitution',

                // Student Information System
                'add_student'           => 'school.students.create',
                'bulk_student_import'   => 'school.student-mgmt.import',
                'bulk_photo_doc'        => 'school.student-mgmt.bulk-photo',
                'optional_subject'      => 'school.student-mgmt.optional-subject',
                'student_directory'     => 'school.students.index',
                'admission_report'      => 'school.student-mgmt.admission-report',
                'siblings'              => 'school.student-mgmt.siblings',
                'student_attendance'    => 'school.attendance.students.index',
                'student_report'        => 'school.student-mgmt.report',

                // Manage Attendance
                'student_bulk_attendance'=> 'school.student-mgmt.bulk-attendance',
                'staff_attendance'      => 'school.attendance.staff.index',
                'staff_bulk_attendance' => 'school.staff.bulk-attendance',
                'student_att_report'    => 'school.attendance.students.marking-report',

                // Download Statistics
                'student_download'      => 'school.downloads.student-status',
                'staff_download'        => 'school.downloads.staff-status',
                'parent_download'       => 'school.downloads.parent-status',
                'student_activity'      => 'school.downloads.student-activity',
                'staff_activity'        => 'school.downloads.staff-activity',
                'parent_activity'       => 'school.downloads.parent-activity',

                // Fee Management
                'fee_configuration'     => 'school.fees.configuration',
                'fee_basics'            => 'school.fees.basics',
                'class_wise_fee'        => 'school.fees.class-wise',
                'student_wise_fee'      => 'school.fees.student-wise',
                'schedule_mapper'       => 'school.fees.schedule-mapper',
                'fee_receipts'          => 'school.fees.receipts',
                'pending_cheques'       => 'school.fees.pending-cheques',
                'fee_reports'           => 'school.fees.reports',
                'fee_invoice'           => 'school.fees.invoice',
                'fee_invoice1'          => 'school.fees.invoice1',

                // I-Card & Bus Pass
                'template_creator'      => 'school.cards.template-creator',
                'generate_card'         => 'school.cards.generate-card',

                // E-Diary & Learning Journal
                'create_diary'          => 'school.diary.create',
                'diary_report'          => 'school.diary.report',

                // Event & Holiday
                'event_holiday'         => 'school.events.index',

                // Certificate Management
                'manage_certs'          => 'school.certificates.manage',
                'class_wise_cert'       => 'school.certificates.class-wise',
                'cert_report'           => 'school.certificates.report',

                // Communication
                'notification_settings' => 'school.communication.settings',
                'notice_circular'       => 'teacher.notices.index',
                'survey'                => 'school.communication.survey',
                'sms'                   => 'school.communication.sms',
                'sms_template'          => 'school.communication.sms-template',
                'whatsapp'              => 'school.communication.whatsapp',
                'email'                 => 'school.communication.email',
                'chat'                  => 'school.communication.chat',

                // Examination Center
                'grade_scale'          => 'school.examination.grade-scale',
                'marks_entry'           => 'school.examination.marks-entry',
                'offline_tests'         => 'school.examination.offline-tests',
                'report_card_template'  => 'school.examination.report-card-template',
                'report_card'           => 'school.examination.report-card',

                // Admissions
                'admission_process'      => 'school.admissions.process',
                'admission_settings'     => 'school.admissions.settings',
                'enquiry_leads'          => 'school.admissions.enquiry-leads',
                'application_payment'    => 'school.admissions.application-payment',
                'pending_documents'      => 'school.admissions.pending-documents',
                'interaction_evaluation' => 'school.admissions.interaction-evaluation',
                'admission'              => 'school.admissions.admission',
                'new_admission_report'   => 'school.admissions.new-admission-report',
                'daily_planner'          => 'school.admissions.daily-planner',
                'admission_dashboard'    => 'school.admissions.dashboard',

                // Gallery & Others
                'post_event'             => 'school.gallery.events',
            ];

            $allModules = ModuleRegistry::all();
            $accessibleModules = [];

            foreach ($allModules as $modKey => $modInfo) {
                $hasAnyFeature = false;
                $grantedFeatures = [];

                foreach ($modInfo['features'] as $featKey => $featLabel) {
                    if (StaffAccessHelper::hasAccess($modKey, $featKey, 'view')) {
                        $hasAnyFeature = true;
                        $routeName = $featureRouteMap[$featKey] ?? null;
                        $url = '#';
                        if ($routeName && Route::has($routeName)) {
                            $url = route($routeName);
                        }
                        $grantedFeatures[$featKey] = [
                            'label' => $featLabel,
                            'url'   => $url,
                        ];
                    }
                }

                if ($hasAnyFeature) {
                    $accessibleModules[$modKey] = [
                        'label' => $modInfo['label'],
                        'icon' => $modInfo['icon'],
                        'features' => $grantedFeatures,
                    ];
                }
            }

            return view('teacher.dashboard', compact(
                'todayEvents',
                'user',
                'school',
                'currentSession',
                'staff',
                'teacherAvatarUrl',
                'totalStudents',
                'classesAssignedCount',
                'attendanceTodayPct',
                'totalAttCount',
                'presentAttCount',
                'absentAttCount',
                'leaveAttCount',
                'monthTotalAtt',
                'monthPresentAtt',
                'monthAbsentAtt',
                'monthLeaveAtt',
                'monthAvgPct',
                'avgScore',
                'pendingAssignmentsCount',
                'classPerformance',
                'todaysSchedule',
                'recentAssignments',
                'spotlightStudent',
                'accessibleModules'
            ));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("TeacherDashboardController error: " . $e->getMessage());
            
            $user = Auth::user();
            $todayEvents = collect();
            $school = app('currentSchool') ?? School::first();
            $currentSession = null;
            $staff = null;
            $teacherAvatarUrl = $user?->photo_url ?? null;
            $totalStudents = 0;
            $classesAssignedCount = 0;
            $attendanceTodayPct = 0;
            $totalAttCount = 0;
            $presentAttCount = 0;
            $absentAttCount = 0;
            $leaveAttCount = 0;
            $monthTotalAtt = 0;
            $monthPresentAtt = 0;
            $monthAbsentAtt = 0;
            $monthLeaveAtt = 0;
            $monthAvgPct = 0;
            $avgScore = 0;
            $pendingAssignmentsCount = 0;
            $classPerformance = [];
            $todaysSchedule = collect();
            $recentAssignments = collect();
            $spotlightStudent = null;
            $accessibleModules = [];
            try {
                $allModules = ModuleRegistry::all();
                foreach ($allModules as $modKey => $modInfo) {
                    $hasAnyFeature = false;
                    $grantedFeatures = [];
                    foreach ($modInfo['features'] as $featKey => $featLabel) {
                        if (StaffAccessHelper::hasAccess($modKey, $featKey, 'view')) {
                            $hasAnyFeature = true;
                            $routeName = $featureRouteMap[$featKey] ?? null;
                            $url = ($routeName && Route::has($routeName)) ? route($routeName) : '#';
                            $grantedFeatures[$featKey] = ['label' => $featLabel, 'url' => $url];
                        }
                    }
                    if ($hasAnyFeature) {
                        $accessibleModules[$modKey] = [
                            'label' => $modInfo['label'],
                            'icon' => $modInfo['icon'],
                            'features' => $grantedFeatures,
                        ];
                    }
                }
            } catch (\Throwable $ex) {}

            return view('teacher.dashboard', compact(
                'todayEvents',
                'user',
                'school',
                'currentSession',
                'staff',
                'teacherAvatarUrl',
                'totalStudents',
                'classesAssignedCount',
                'attendanceTodayPct',
                'totalAttCount',
                'presentAttCount',
                'absentAttCount',
                'leaveAttCount',
                'monthTotalAtt',
                'monthPresentAtt',
                'monthAbsentAtt',
                'monthLeaveAtt',
                'monthAvgPct',
                'avgScore',
                'pendingAssignmentsCount',
                'classPerformance',
                'todaysSchedule',
                'recentAssignments',
                'spotlightStudent',
                'accessibleModules'
            ));
        }
    }

    public function notices()
    {
        $user = auth()->user();
        $school = null;
        if (\Schema::hasTable('schools')) {
            $school = app('currentSchool') ?? \App\Models\School::first();
        }
        $schoolId = $school?->id ?? $user->school_id ?? 1;

        $staff = null;
        if (\Schema::hasTable('staff')) {
            $staff = \App\Models\Staff::where('user_id', $user->id)->first();
        }

        $notices = Notice::where('school_id', $schoolId)
            ->whereIn('target_audience', ['all', 'staff'])
            ->where(function($q) {
                $q->whereNull('publish_at')->orWhere('publish_at', '<=', now());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Accessible modules logic for sidebar
        $accessibleModules = [];
        try {
            $allModules = ModuleRegistry::all();
            $featureRouteMap = [
                // Overview
                'mis_report'            => 'school.dashboard.mis-report',
                'admin_dashboard'       => 'school.dashboard',
                
                // Institute Info
                'basic_info'            => 'school.settings.institute-info',
                'udise'                 => 'school.settings.udise',
                
                // Admin Role Management & Passwords
                'roles'                 => 'school.roles.index',
                'permissions'           => 'school.roles.permissions',
                'password_reset'        => 'school.passwords.reset',

                // Staff Management
                'staff_directory'       => 'school.staff.index',
                'add_staff'             => 'school.staff.create',
                'bulk_import'           => 'school.staff.import',
                'bulk_photo'            => 'school.staff.bulk-photo',
                
                // Leave Management
                'leave_basics'         => 'school.leave.basics',
                'staff_leave'          => 'school.leave.staff',
                'student_leave'        => 'school.leave.student',

                // Class, Subject & Teacher Allocation
                'class_overview'        => 'school.assignments.class-overview',
                'add_class'             => 'school.assignments.classes',
                'add_subject'           => 'school.assignments.subjects',
                'assign_teacher'        => 'school.assignments.teachers',

                // Timetable
                'class_timetable'       => 'school.timetable.class',
                'group_timetable'       => 'school.timetable.group',
                'teacher_timetable'     => 'school.timetable.teacher',
                'teacher_substitution'  => 'school.timetable.substitution',

                // Student Information System
                'add_student'           => 'school.students.create',
                'bulk_student_import'   => 'school.student-mgmt.import',
                'bulk_photo_doc'        => 'school.student-mgmt.bulk-photo',
                'optional_subject'      => 'school.student-mgmt.optional-subject',
                'student_directory'     => 'school.students.index',
                'admission_report'      => 'school.student-mgmt.admission-report',
                'siblings'              => 'school.student-mgmt.siblings',
                'student_attendance'    => 'school.attendance.students.index',
                'student_report'        => 'school.student-mgmt.report',

                // Manage Attendance
                'student_bulk_attendance'=> 'school.student-mgmt.bulk-attendance',
                'staff_attendance'      => 'school.attendance.staff.index',
                'staff_bulk_attendance' => 'school.staff.bulk-attendance',
                'student_att_report'    => 'school.attendance.students.marking-report',

                // Download Statistics
                'student_download'      => 'school.downloads.student-status',
                'staff_download'        => 'school.downloads.staff-status',
                'parent_download'       => 'school.downloads.parent-status',
                'student_activity'      => 'school.downloads.student-activity',
                'staff_activity'        => 'school.downloads.staff-activity',
                'parent_activity'       => 'school.downloads.parent-activity',

                // Fee Management
                'fee_configuration'     => 'school.fees.configuration',
                'fee_basics'            => 'school.fees.basics',
                'class_wise_fee'        => 'school.fees.class-wise',
                'student_wise_fee'      => 'school.fees.student-wise',
                'schedule_mapper'       => 'school.fees.schedule-mapper',
                'fee_receipts'          => 'school.fees.receipts',
                'pending_cheques'       => 'school.fees.pending-cheques',
                'fee_reports'           => 'school.fees.reports',
                'fee_invoice'           => 'school.fees.invoice',
                'fee_invoice1'          => 'school.fees.invoice1',

                // I-Card & Bus Pass
                'template_creator'      => 'school.cards.template-creator',
                'generate_card'         => 'school.cards.generate-card',

                // E-Diary & Learning Journal
                'create_diary'          => 'school.diary.create',
                'diary_report'          => 'school.diary.report',

                // Event & Holiday
                'event_holiday'         => 'school.events.index',

                // Certificate Management
                'manage_certs'          => 'school.certificates.manage',
                'class_wise_cert'       => 'school.certificates.class-wise',
                'cert_report'           => 'school.certificates.report',

                // Communication
                'notification_settings' => 'school.communication.settings',
                'notice_circular'       => 'teacher.notices.index',
                'survey'                => 'school.communication.survey',
                'sms'                   => 'school.communication.sms',
                'sms_template'          => 'school.communication.sms-template',
                'whatsapp'              => 'school.communication.whatsapp',
                'email'                 => 'school.communication.email',
                'chat'                  => 'school.communication.chat',

                // Examination Center
                'grade_scale'          => 'school.examination.grade-scale',
                'marks_entry'           => 'school.examination.marks-entry',
                'offline_tests'         => 'school.examination.offline-tests',
                'report_card_template'  => 'school.examination.report-card-template',
                'report_card'           => 'school.examination.report-card',

                // Admissions
                'admission_process'      => 'school.admissions.process',
                'admission_settings'     => 'school.admissions.settings',
                'enquiry_leads'          => 'school.admissions.enquiry-leads',
                'application_payment'    => 'school.admissions.application-payment',
                'pending_documents'      => 'school.admissions.pending-documents',
                'interaction_evaluation' => 'school.admissions.interaction-evaluation',
                'admission'              => 'school.admissions.admission',
                'new_admission_report'   => 'school.admissions.new-admission-report',
                'daily_planner'          => 'school.admissions.daily-planner',
                'admission_dashboard'    => 'school.admissions.dashboard',

                // Gallery & Others
                'post_event'             => 'school.gallery.events',
            ];

            foreach ($allModules as $modKey => $modInfo) {
                $hasAnyFeature = false;
                $grantedFeatures = [];
                foreach ($modInfo['features'] as $featKey => $featLabel) {
                    if (\App\Helpers\StaffAccessHelper::hasAccess($modKey, $featKey, 'view')) {
                        $hasAnyFeature = true;
                        $routeName = $featureRouteMap[$featKey] ?? null;
                        $url = '#';
                        if ($routeName && \Route::has($routeName)) {
                            $url = route($routeName);
                        }
                        $grantedFeatures[$featKey] = [
                            'label' => $featLabel,
                            'url'   => $url,
                        ];
                    }
                }
                if ($hasAnyFeature) {
                    $accessibleModules[$modKey] = [
                        'label' => $modInfo['label'],
                        'icon' => $modInfo['icon'],
                        'features' => $grantedFeatures,
                    ];
                }
            }
        } catch (\Throwable $ex) {}

        $teacherAvatarUrl = $user->photo_url ?? $staff?->photo_url ?? null;
        return view('teacher.notices', compact('user', 'school', 'staff', 'teacherAvatarUrl', 'notices', 'accessibleModules'));
    }

    /**
     * Update Teacher Profile Picture
     */
    public function updateProfilePicture(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }

            $request->validate([
                'photo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            ]);

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = 'teacher_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                
                $destinationPath = public_path('uploads/profile_pictures');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                
                $file->move($destinationPath, $filename);
                $relativeUrl = 'uploads/profile_pictures/' . $filename;

                // Save to User
                $user->photo = $relativeUrl;
                $user->save();

                // Save to Staff if available
                $staff = Staff::where('user_id', $user->id)->first();
                if ($staff) {
                    $staff->photo = $relativeUrl;
                    $staff->save();
                }

                $fullUrl = asset($relativeUrl);

                return response()->json([
                    'success' => true,
                    'message' => 'Profile picture updated successfully!',
                    'photo_url' => $fullUrl
                ]);
            }

            return response()->json(['success' => false, 'message' => 'No image file uploaded.'], 422);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update Teacher Password
     */
    public function updatePassword(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }

            $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:6|confirmed',
            ]);

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(['success' => false, 'message' => 'Current password does not match our records.'], 422);
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = collect($e->errors())->flatten()->first();
            return response()->json(['success' => false, 'message' => $errors ?? 'Validation error.'], 422);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update Dashboard Settings
     */
    public function updateSettings(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }

            $settings = $request->input('settings', []);

            $extra = $user->additional_fields ?? [];
            if (is_string($extra)) {
                $extra = json_decode($extra, true) ?? [];
            }
            $extra['dashboard_settings'] = $settings;

            if (Schema::hasColumn('users', 'additional_fields')) {
                $user->additional_fields = $extra;
                $user->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Dashboard settings saved successfully!',
                'settings' => $settings
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
