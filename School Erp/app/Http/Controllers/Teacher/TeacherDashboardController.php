<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\ClassTimetableCell;
use App\Models\Notice;
use App\Models\School;
use App\Models\SchoolClass;
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
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

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

            $currentSession = null;
            if (Schema::hasTable('academic_sessions')) {
                $currentSession = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first()
                    ?? AcademicSession::where('school_id', $schoolId)->first();
            }

            // Get Staff profile details
            $staff = null;
            if (Schema::hasTable('staff')) {
                $staff = Staff::where('user_id', $user->id)->first();
            }

            // 1. Assigned Classes & Subjects (Original Data)
            $assignedClassSections = collect();
            if ($staff && Schema::hasTable('class_timetable_cells')) {
                $assignedClassSections = ClassTimetableCell::where('school_id', $schoolId)
                    ->where('teacher_id', $staff->id)
                    ->select('class_id', 'section_id', 'subject_id')
                    ->distinct()
                    ->with(['schoolClass', 'section', 'subject'])
                    ->get();
            }

            $uniqueClassIds = $assignedClassSections->pluck('class_id')->unique()->filter()->toArray();
            $uniqueSectionIds = $assignedClassSections->pluck('section_id')->unique()->filter()->toArray();

            // Fallback to SectionSubjectStaff if no timetable cells assigned yet
            if (empty($uniqueClassIds) && $staff && Schema::hasTable('section_subject_staff')) {
                $sss = SectionSubjectStaff::where('school_id', $schoolId)
                    ->where('staff_id', $staff->id)
                    ->select('section_id', 'subject_id')
                    ->distinct()
                    ->with(['section.class', 'subject'])
                    ->get();
                $uniqueSectionIds = $sss->pluck('section_id')->unique()->filter()->toArray();
                $uniqueClassIds = $sss->pluck('section.class_id')->unique()->filter()->toArray();
            }

            $classesAssignedCount = count($uniqueClassIds);

            // 2. Total Students in Assigned Classes (Original Data)
            $totalStudents = 0;
            if (Schema::hasTable('students')) {
                if (count($uniqueSectionIds) > 0) {
                    $totalStudents = Student::where('school_id', $schoolId)
                        ->whereIn('section_id', $uniqueSectionIds)
                        ->where('status', 'active')
                        ->count();
                }
                if ($totalStudents === 0) {
                    $totalStudents = Student::where('school_id', $schoolId)->where('status', 'active')->count();
                }
            }

            // 3. Attendance Calculation (Original Data from Database)
            $todayStr = date('Y-m-d');
            $totalAttCount = 0; $presentAttCount = 0; $absentAttCount = 0; $leaveAttCount = 0;
            if (Schema::hasTable('student_attendances')) {
                $attDate = $todayStr;
                $hasTodayAtt = StudentAttendance::where('school_id', $schoolId)->whereDate('date', $todayStr)->exists();
                if (!$hasTodayAtt) {
                    $latestDate = StudentAttendance::where('school_id', $schoolId)->latest('date')->value('date');
                    if ($latestDate) { $attDate = $latestDate; }
                }

                $attendanceQuery = StudentAttendance::where('school_id', $schoolId)->whereDate('date', $attDate);
                if (count($uniqueSectionIds) > 0) {
                    $attendanceQuery->whereIn('section_id', $uniqueSectionIds);
                }

                $totalAttCount = $attendanceQuery->count();
                $presentAttCount = (clone $attendanceQuery)->where('status', 'present')->count();
                $absentAttCount = (clone $attendanceQuery)->where('status', 'absent')->count();
                $leaveAttCount = (clone $attendanceQuery)->whereIn('status', ['leave', 'late'])->count();
            }

            $attendanceTodayPct = $totalAttCount > 0 ? round(($presentAttCount / $totalAttCount) * 100) : 0;

            // 4. Pending / Active Assignments (Original Data)
            $pendingAssignmentsCount = 0;
            if ($staff && Schema::hasTable('teacher_assignments')) {
                $pendingAssignmentsCount = TeacherAssignment::where('school_id', $schoolId)
                    ->where('staff_id', $staff->id)
                    ->count();
            }

            // 5. Average Score Calculation (Original Data)
            $avgScore = 0;
            if (Schema::hasTable('student_marks')) {
                $marksQuery = StudentMark::where('school_id', $schoolId);
                if ($marksQuery->count() > 0) {
                    $calcAvg = $marksQuery->avg('marks_obtained');
                    if ($calcAvg) {
                        $avgScore = round($calcAvg);
                    }
                }
            }

            // 6. Class Performance Bar Chart Data (Original Data)
            $classPerformance = [];
            if (Schema::hasTable('school_classes')) {
                $schoolClasses = SchoolClass::where('school_id', $schoolId)->take(5)->get();
                foreach ($schoolClasses as $sc) {
                    $score = 0;
                    if (Schema::hasTable('student_marks')) {
                        $cAvg = StudentMark::where('school_id', $schoolId)->where('class_id', $sc->id)->avg('marks_obtained');
                        if ($cAvg) { $score = round($cAvg); }
                    }
                    $classPerformance[] = ['class' => $sc->name, 'score' => $score];
                }
            }

            // 7. Today's Schedule Timeline (Original Data)
            $todayDay = date('l'); // e.g. Monday
            $todaysSchedule = collect();
            if ($staff && Schema::hasTable('class_timetable_cells')) {
                $todaysSchedule = ClassTimetableCell::where('school_id', $schoolId)
                    ->where('teacher_id', $staff->id)
                    ->where('day_of_week', $todayDay)
                    ->with(['schoolClass', 'section', 'subject', 'period'])
                    ->get();
            }

            // 8. Recent Assignments (Original Data)
            $recentAssignments = collect();
            if ($staff && Schema::hasTable('teacher_assignments')) {
                $recentAssignments = TeacherAssignment::where('school_id', $schoolId)
                    ->where('staff_id', $staff->id)
                    ->with(['schoolClass', 'section', 'submissions'])
                    ->latest()
                    ->take(4)
                    ->get();
            }

            // 9. Student Spotlight (Original Data)
            $spotlightStudent = null;
            if (Schema::hasTable('students')) {
                $spotlightStudent = Student::where('school_id', $schoolId)->where('status', 'active')->first();
            }

            // 10. Granted Modules Permission Check
            $featureRouteMap = [
                'mis_report'            => 'school.dashboard.mis-report',
                'admin_dashboard'       => 'school.dashboard',
                'basic_info'            => 'school.settings.institute-info',
                'udise'                 => 'school.settings.udise',
                'role_category'         => 'school.roles.index',
                'staff_access'          => 'school.roles.staff-access',
                'reset_password'        => 'school.settings.reset-password',
                'staff_directory'       => 'school.staff.index',
                'add_staff'             => 'school.staff.create',
                'bulk_import'           => 'school.staff.import',
                'bulk_photo'            => 'school.staff.bulk-photo',
                'staff_attendance'      => 'school.attendance.staff.index',
                'bulk_attendance'       => 'school.staff.bulk-attendance',
                'student_att_report'    => 'school.attendance.students.marking-report',
                'class_overview'        => 'school.assignments.class-overview',
                'add_class'             => 'school.assignments.classes',
                'add_subject'           => 'school.assignments.subjects',
                'assign_teacher'        => 'school.assignments.teachers',
                'class_timetable'       => 'school.timetable.class',
                'group_timetable'       => 'school.timetable.group',
                'teacher_timetable'     => 'school.timetable.teacher',
                'teacher_substitution'  => 'school.timetable.substitution',
                'add_student'           => 'school.students.create',
                'bulk_student_import'   => 'school.student-mgmt.import',
                'bulk_photo_doc'        => 'school.student-mgmt.bulk-photo',
                'optional_subject'      => 'school.student-mgmt.optional-subject',
                'student_directory'      => 'school.students.index',
                'admission_report'      => 'school.student-mgmt.admission-report',
                'siblings'              => 'school.student-mgmt.siblings',
                'student_attendance'    => 'school.attendance.students.index',
                'student_report'        => 'school.student-mgmt.report',
                'student_download'      => 'school.downloads.student-status',
                'staff_download'        => 'school.downloads.staff-status',
                'parent_download'       => 'school.downloads.parent-status',
                'student_activity'      => 'school.downloads.student-activity',
                'staff_activity'        => 'school.downloads.staff-activity',
                'parent_activity'       => 'school.downloads.parent-activity',
                'fee_configuration'    => 'school.fees.configuration',
                'fee_basics'           => 'school.fees.basics',
                'class_wise_fee'       => 'school.fees.class-wise',
                'student_wise_fee'     => 'school.fees.student-wise',
                'schedule_mapper'      => 'school.fees.schedule-mapper',
                'fee_receipts'         => 'school.fees.receipts',
                'pending_cheques'      => 'school.fees.pending-cheques',
                'fee_reports'          => 'school.fees.reports',
                'fee_invoice'          => 'school.fees.invoice',
                'fee_invoice1'         => 'school.fees.invoice1',
                'template_creator'     => 'school.cards.template-creator',
                'generate_card'        => 'school.cards.generate-card',
                'create_diary'         => 'school.diary.create',
                'diary_report'         => 'school.diary.report',
                'event_holiday'        => 'school.events.index',
                'manage_certs'        => 'school.certificates.manage',
                'class_wise_cert'     => 'school.certificates.class-wise',
                'cert_report'         => 'school.certificates.report',
                'leave_basics'         => 'school.leave.basics',
                'staff_leave'          => 'school.leave.staff',
                'student_leave'        => 'school.leave.student',
                'notification_settings'=> 'school.communication.settings',
                'notice_circular'      => 'school.communication.notice',
                'survey'               => 'school.communication.survey',
                'sms'                  => 'school.communication.sms',
                'sms_template'         => 'school.communication.sms-template',
                'whatsapp'             => 'school.communication.whatsapp',
                'email'                => 'school.communication.email',
                'chat'                 => 'school.communication.chat',
                'grade_scale'         => 'school.examination.grade-scale',
                'marks_entry'          => 'school.examination.marks-entry',
                'offline_tests'        => 'school.examination.offline-tests',
                'report_card_template' => 'school.examination.report-card-template',
                'report_card'          => 'school.examination.report-card',
                'report_card_v2'       => 'school.examination.report-card-v2',
                'admission_process'     => 'school.admissions.process',
                'admission_settings'    => 'school.admissions.settings',
                'enquiry_leads'         => 'school.admissions.enquiry-leads',
                'application_payment'   => 'school.admissions.application-payment',
                'pending_documents'     => 'school.admissions.pending-documents',
                'interaction_evaluation'=> 'school.admissions.interaction-evaluation',
                'admission'             => 'school.admissions.admission',
                'new_admission_report'  => 'school.admissions.new-admission-report',
                'daily_planner'         => 'school.admissions.daily-planner',
                'admission_dashboard'   => 'school.admissions.dashboard',
                'post_event'            => 'school.gallery.events',
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
                'user',
                'school',
                'currentSession',
                'staff',
                'totalStudents',
                'classesAssignedCount',
                'attendanceTodayPct',
                'presentAttCount',
                'absentAttCount',
                'leaveAttCount',
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
            $school = app('currentSchool') ?? School::first();
            $currentSession = null;
            $staff = null;
            $totalStudents = 0;
            $classesAssignedCount = 0;
            $attendanceTodayPct = 0;
            $presentAttCount = 0;
            $absentAttCount = 0;
            $leaveAttCount = 0;
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
                'user',
                'school',
                'currentSession',
                'staff',
                'totalStudents',
                'classesAssignedCount',
                'attendanceTodayPct',
                'presentAttCount',
                'absentAttCount',
                'leaveAttCount',
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
}
