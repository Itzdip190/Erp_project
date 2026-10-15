<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\DigitalDiary;
use App\Models\EnquiryLead;
use App\Models\Event;
use App\Models\FeeInvoice;
use App\Models\FeeReceipt;
use App\Models\Notice;
use App\Models\PendingCheque;
use App\Models\School;
use App\Models\SchoolExpense;
use App\Models\SchoolIncome;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\StaffLeaveApplication;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Visitor;
use App\Services\FeeHelper;
use App\Support\FeatureVisibilityHelper;
use App\Support\ModuleRegistry;
use App\Support\StaffAccessHelper;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MobileAppConfigController extends Controller
{
    /**
     * Get mobile application configuration, themes, and feature visibility flags.
     */
    public function config(Request $request): JsonResponse
    {
        $user = Auth::user();
        $schoolId = $user ? $user->school_id : null;
        $scope = $request->query('scope', 'mobile');

        $registeredFeatures = FeatureVisibilityHelper::registeredFeatures();
        $featureFlags = [];

        foreach ($registeredFeatures as $key => $meta) {
            $featureFlags[$key] = FeatureVisibilityHelper::isVisible($key, $scope, $schoolId);
        }

        $feeConfig = $schoolId ? \App\Models\FeeConfiguration::where('school_id', $schoolId)->first() : null;

        // Dynamic theme & mobile settings configured from Admin UI
        $appName = \App\Services\SettingService::get('mobile_app_name', 'SchoolCloud ERP Mobile', $schoolId);
        $primaryColor = \App\Services\SettingService::get('mobile_primary_color', '#1d4ed8', $schoolId);
        $accentColor = \App\Services\SettingService::get('mobile_accent_color', '#3b82f6', $schoolId);
        $appLogo = \App\Services\SettingService::get('mobile_app_logo', '', $schoolId);
        if (empty($appLogo) && $schoolId) {
            $school = \App\Models\School::find($schoolId);
            $appLogo = $school?->logo_url ?: ($school?->logo ? (filter_var($school->logo, FILTER_VALIDATE_URL) ? $school->logo : asset('storage/' . ltrim(preg_replace('/^(public\/|storage\/)/', '', $school->logo), '/'))) : '');
        }
        $maintenance = \App\Services\SettingService::get('mobile_app_maintenance', '0', $schoolId) == '1';
        $minVersion = \App\Services\SettingService::get('mobile_min_version', '1.0.0', $schoolId);
        $forceUpdate = \App\Services\SettingService::get('mobile_force_update', '0', $schoolId) == '1';

        return response()->json([
            'status' => 'success',
            'data' => [
                'app_name' => $appName,
                'app_logo' => $appLogo,
                'version' => \App\Services\SettingService::get('mobile_app_version', '2.4.0', $schoolId),
                'min_version' => $minVersion,
                'force_update_required' => $forceUpdate,
                'maintenance_mode' => $maintenance,
                'channel_scope' => $scope,
                'theme' => [
                    'primary' => $primaryColor,
                    'accent' => $accentColor,
                    'background' => '#f8fafc',
                    'card_bg' => '#ffffff',
                    'text_color' => '#0f172a',
                ],
                'features' => $featureFlags,
                'fee_config' => $feeConfig,
                'transport_settings' => [
                    'show_school_name_invoice' => \App\Services\SettingService::get('show_school_name_transport_invoice', '1') == '1',
                    'quarterly_payment_only' => \App\Services\SettingService::get('quarterly_transport_payment', '0') == '1',
                    'show_school_logo' => \App\Services\SettingService::get('show_school_logo_transport_invoice', '1') == '1',
                    'show_route_details' => \App\Services\SettingService::get('show_route_vehicle_on_transport_invoice', '1') == '1',
                    'auto_absent_deduction' => \App\Services\SettingService::get('auto_transport_absent_deduction', '1') == '1',
                    'allow_advance_payment' => \App\Services\SettingService::get('allow_advance_transport_payment', '1') == '1',
                    'lock_partial_payment' => \App\Services\SettingService::get('lock_partial_transport_payment', '0') == '1',
                    'show_driver_contact' => \App\Services\SettingService::get('show_driver_contact_to_parents', '1') == '1',
                    'receipt_prefix' => \App\Services\SettingService::get('transport_receipt_prefix', 'TRN-'),
                ],
            ]
        ]);
    }

    /**
     * Get active mobile banners and slider promos for the authenticated user role.
     */
    public function banners(Request $request): JsonResponse
    {
        $user = Auth::user();
        $schoolId = $user ? $user->school_id : null;
        $role = $request->query('role', ($user ? ($user->roles->first()?->name ?? 'all') : 'all'));

        $banners = [];
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('mobile_app_banners')) {
                $query = \App\Models\MobileAppBanner::query();
                if ($schoolId) {
                    $query->where('school_id', $schoolId);
                }
                $banners = $query->active()
                    ->forRole($role)
                    ->get();
            }
        } catch (\Throwable $e) {}

        $sliderConfig = [
            'interval' => (int) \App\Services\SettingService::get('mobile_slider_interval', '4000', $schoolId),
            'autoplay' => \App\Services\SettingService::get('mobile_slider_autoplay', '1', $schoolId) == '1',
            'loop' => \App\Services\SettingService::get('mobile_slider_loop', '1', $schoolId) == '1',
            'aspect_ratio' => \App\Services\SettingService::get('mobile_slider_ratio', '16_9', $schoolId),
            'show_dots' => \App\Services\SettingService::get('mobile_slider_dots', '1', $schoolId) == '1',
        ];

        return response()->json([
            'status' => 'success',
            'data' => [
                'slider_config' => $sliderConfig,
                'banners' => $banners,
            ]
        ]);
    }

    /**
     * Get dynamic mobile menu and navigation items based on active permissions and mobile feature visibility.
     */
    public function navigation(Request $request): JsonResponse
    {
        $user = Auth::user();
        $schoolId = $user ? $user->school_id : null;
        $scope = $request->query('scope', 'mobile');

        $modules = ModuleRegistry::getModules();
        $mobileNavigation = [];

        foreach ($modules as $modKey => $mod) {
            $features = [];
            foreach ($mod['features'] as $fKey => $fLabel) {
                if (FeatureVisibilityHelper::isVisible($fKey, $scope, $schoolId)) {
                    if (!$user || StaffAccessHelper::hasAccess($modKey, $fKey, 'view')) {
                        $features[] = [
                            'key' => $fKey,
                            'label' => $fLabel,
                            'route' => "/mobile/{$modKey}/{$fKey}",
                        ];
                    }
                }
            }

            if (!empty($features)) {
                $mobileNavigation[] = [
                    'key' => $modKey,
                    'label' => $mod['label'],
                    'icon' => $mod['icon'],
                    'features' => $features,
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'bottom_nav' => [
                    ['key' => 'dashboard', 'label' => 'Home', 'icon' => 'home'],
                    ['key' => 'attendance', 'label' => 'Attendance', 'icon' => 'calendar-check'],
                    ['key' => 'messages', 'label' => 'Notice', 'icon' => 'bell'],
                    ['key' => 'profile', 'label' => 'Profile', 'icon' => 'user'],
                ],
                'drawer_modules' => $mobileNavigation,
            ]
        ]);
    }

    /**
     * Get mobile dashboard grid cards and complete ERP operational metrics.
     */
    public function dashboard(Request $request): JsonResponse
    {
        $user = Auth::user();
        $code = $request->header('X-School-Code') ?: $request->query('school_code');
        $schoolId = null;

        if ($code) {
            $foundSchool = School::where('code', $code)
                ->orWhereRaw('LOWER(code) = ?', [strtolower($code)])
                ->first();
            if ($foundSchool) {
                if (!$user || $user->hasRole('superadmin') || $user->role === 'superadmin' || !$user->school_id) {
                    $schoolId = $foundSchool->id;
                }
            }
        }
        if (!$schoolId && $user && $user->school_id) {
            $schoolId = $user->school_id;
        }
        if (!$schoolId && $code) {
            $foundSchool = School::where('code', $code)
                ->orWhereRaw('LOWER(code) = ?', [strtolower($code)])
                ->first();
            $schoolId = $foundSchool?->id;
        }
        if (!$schoolId) {
            $schoolId = School::first()?->id;
        }

        $school = $schoolId ? School::find($schoolId) : null;
        
        // ── ACADEMIC SESSIONS ────────────────────────────────────────────────
        $sessions = $schoolId ? AcademicSession::where('school_id', $schoolId)->get() : collect();
        $requestedSessionId = $request->query('session_id');
        $currentSession = null;
        if ($requestedSessionId && $schoolId) {
            $currentSession = AcademicSession::where('school_id', $schoolId)->where('id', $requestedSessionId)->first();
        }
        if (!$currentSession && $schoolId) {
            $currentSession = ($user ? AcademicSession::resolveCurrentSessionForUser($user, $schoolId) : null)
                ?? AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first()
                ?? AcademicSession::where('school_id', $schoolId)->latest('id')->first();
        }

        $sessId = $currentSession?->id;

        // ── 1. HEADCOUNT & DEMOGRAPHIC RATIO ──────────────────────────────────
        $totalStudentsQuery = Student::where('school_id', $schoolId)->where(function($q) {
            $q->where('is_active', true)->orWhereNull('is_active');
        });
        if ($currentSession) {
            $totalStudentsQuery->where(function($q) use ($sessId) {
                $q->where('academic_session_id', $sessId)
                  ->orWhereHas('studentSessions', fn($sq) => $sq->where('academic_session_id', $sessId));
            });
        }
        $totalInSession = $schoolId ? (clone $totalStudentsQuery)->count() : 0;
        if ($totalInSession === 0 && $schoolId && !$requestedSessionId) {
            $totalStudentsQuery = Student::where('school_id', $schoolId)->where(function($q) {
                $q->where('is_active', true)->orWhereNull('is_active');
            });
            $totalStudents = $totalStudentsQuery->count();
        } else {
            $totalStudents = $totalInSession;
        }

        $totalStaff = $schoolId ? Staff::where('school_id', $schoolId)->where(function($q) {
            $q->where('is_active', true)->orWhereNull('is_active');
        })->count() : 0;

        // Student gender breakdown
        $studentMaleCount = $schoolId ? (clone $totalStudentsQuery)->where('gender', 'male')->count() : 0;
        $studentFemaleCount = $schoolId ? (clone $totalStudentsQuery)->where('gender', 'female')->count() : 0;
        $studentNotMappedCount = $schoolId ? (clone $totalStudentsQuery)->whereNull('section_id')->count() : 0;
        $sumMapped = $studentMaleCount + $studentFemaleCount + $studentNotMappedCount;
        $studentMalePct = $sumMapped > 0 ? round(($studentMaleCount / $sumMapped) * 100, 1) : 0;
        $studentFemalePct = $sumMapped > 0 ? round(($studentFemaleCount / $sumMapped) * 100, 1) : 0;
        $studentNotMappedPct = $sumMapped > 0 ? round(($studentNotMappedCount / $sumMapped) * 100, 1) : 0;

        // Staff gender breakdown
        $staffMaleCount = $schoolId ? Staff::where('school_id', $schoolId)->where(function($q) {
            $q->where('is_active', true)->orWhereNull('is_active');
        })->whereIn('gender', ['male', 'Male'])->count() : 0;
        $staffFemaleCount = $schoolId ? Staff::where('school_id', $schoolId)->where(function($q) {
            $q->where('is_active', true)->orWhereNull('is_active');
        })->whereIn('gender', ['female', 'Female'])->count() : 0;
        $staffNotMappedCount = $schoolId ? Staff::where('school_id', $schoolId)->where(function($q) {
            $q->where('is_active', true)->orWhereNull('is_active');
        })->where(function($q) {
            $q->whereNull('gender')->orWhereNotIn('gender', ['male', 'Male', 'female', 'Female']);
        })->count() : 0;
        $sumStaffMapped = $staffMaleCount + $staffFemaleCount + $staffNotMappedCount;
        $staffMalePct = $sumStaffMapped > 0 ? round(($staffMaleCount / $sumStaffMapped) * 100, 1) : 0;
        $staffFemalePct = $sumStaffMapped > 0 ? round(($staffFemaleCount / $sumStaffMapped) * 100, 1) : 0;
        $staffNotMappedPct = $sumStaffMapped > 0 ? round(($staffNotMappedCount / $sumStaffMapped) * 100, 1) : 0;

        // Joining & Attrition Summary
        $studentNewlyJoined = $schoolId ? Student::where('school_id', $schoolId)->whereYear('admission_date', now()->year)->count() : 0;
        $studentExited = $schoolId ? Student::where('school_id', $schoolId)->onlyTrashed()->count() : 0;
        $studentStrength = $totalStudents;

        $staffNewlyJoined = $schoolId ? Staff::where('school_id', $schoolId)->whereYear('joining_date', now()->year)->count() : 0;
        $staffExited = $schoolId ? Staff::where('school_id', $schoolId)->onlyTrashed()->count() : 0;
        $staffStrength = $totalStaff;

        // Admission Pipeline Summary
        $admissionEnquiry = $schoolId ? EnquiryLead::where('school_id', $schoolId)->whereIn('status', ['new', 'enquiry'])->count() : 0;
        $admissionApplication = $schoolId ? EnquiryLead::where('school_id', $schoolId)->whereIn('status', ['contacted', 'application'])->count() : 0;
        $admissionEvaluation = $schoolId ? EnquiryLead::where('school_id', $schoolId)->where('status', 'evaluation')->count() : 0;
        $admissionPayment = $schoolId ? EnquiryLead::where('school_id', $schoolId)->where('status', 'payment')->count() : 0;
        $admissionCount = $schoolId ? EnquiryLead::where('school_id', $schoolId)->whereIn('status', ['enrolled', 'admission'])->count() : 0;
        $totalPipeline = $admissionEnquiry + $admissionApplication + $admissionEvaluation + $admissionPayment + $admissionCount;

        // ── 2. FINANCIAL MANAGEMENT OVERVIEW ─────────────────────────────────
        $financialFilter = $request->query('financial_filter', 'This Year');
        $financialOverview = $schoolId ? $this->calculateMonthlyFinancialOverview($schoolId, $currentSession, $financialFilter) : [
            'labels' => ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'],
            'incomeData' => array_fill(0, 12, 0.0),
            'expenseData' => array_fill(0, 12, 0.0),
        ];
        $months = $financialOverview['labels'];
        $incomeData = $financialOverview['incomeData'];
        $expenseData = $financialOverview['expenseData'];
        $totalIncome = (float) array_sum($incomeData);
        $totalExpense = (float) array_sum($expenseData);
        $netBalance = $totalIncome - $totalExpense;

        // ── 3. FEE METRICS (TILL DATE & ANNUAL) ───────────────────────────────
        $feeMetrics = [
            'tillDateCollected' => 0.0,
            'tillDateDue' => 0.0,
            'tillDateTotal' => 0.0,
            'tillDateCollectedPct' => 0.0,
            'tillDateDuePct' => 0.0,
            'tillDatePendingStudentsCount' => 0,
            'tillDateAssignedStudentsCount' => 0,
            'annualCollected' => 0.0,
            'annualDue' => 0.0,
            'annualTotal' => 0.0,
            'annualCollectedPct' => 0.0,
            'annualDuePct' => 0.0,
            'annualPendingStudentsCount' => 0,
            'annualAssignedStudentsCount' => $totalStudents,
            'todayFeeCollection' => 0.0,
            'todayFeeDue' => 0.0,
            'todayFeeCollectionPct' => 0,
            'schoolPendingChequesTotal' => 0.0,
        ];

        if ($schoolId && class_exists(FeeHelper::class)) {
            try {
                $calc = FeeHelper::calculateSessionFeeMetrics($schoolId, $currentSession);
                $feeMetrics = array_merge($feeMetrics, $calc);
            } catch (\Throwable $e) {}
        }

        // ── 4. ATTENDANCE OVERVIEW (DETAILED BREAKDOWN TODAY) ─────────────────
        $studentAttendanceToday = $schoolId ? StudentAttendance::where('school_id', $schoolId)
            ->whereDate('date', today())
            ->when($sessId, function($q) use ($sessId) {
                $q->where(function($sub) use ($sessId) {
                    $sub->where('academic_session_id', $sessId)
                        ->orWhere(function($sq) use ($sessId) {
                            $sq->whereNull('academic_session_id')
                               ->whereHas('student', function($stq) use ($sessId) {
                                   $stq->where('academic_session_id', $sessId)
                                       ->orWhereHas('studentSessions', fn($ssq) => $ssq->where('academic_session_id', $sessId));
                               });
                        });
                });
            })
            ->get() : collect();

        $studentPresentToday  = $studentAttendanceToday->where('status', 'present')->count();
        $studentAbsentToday   = $studentAttendanceToday->where('status', 'absent')->count();
        $studentHalfDayToday  = $studentAttendanceToday->where('status', 'half_day')->count();
        $studentLeaveToday    = $studentAttendanceToday->where('status', 'leave')->count();
        $studentCustomToday   = $studentAttendanceToday->where('status', 'custom')->count();
        $studentNotMarkedToday = max(0, $totalStudents - $studentAttendanceToday->count());
        $studentNotMarkedPct   = $totalStudents > 0 ? round(($studentNotMarkedToday / $totalStudents) * 100, 1) : 0.0;
        $studentAttendancePct  = $totalStudents > 0 
            ? round((($studentPresentToday + ($studentHalfDayToday * 0.5)) / $totalStudents) * 100) 
            : 0;

        $staffAttendanceToday = $schoolId ? StaffAttendance::where('school_id', $schoolId)
            ->whereDate('date', today())
            ->get() : collect();

        $staffPresentToday = $staffAttendanceToday->where('status', 'present')->count();
        $staffAbsentToday = $staffAttendanceToday->where('status', 'absent')->count();
        $staffHalfdayToday = $staffAttendanceToday->where('status', 'half_day')->count();
        $staffLeaveToday = $staffAttendanceToday->where('status', 'leave')->count();
        $staffCustomToday = $staffAttendanceToday->where('status', 'custom')->count();
        $staffNotMarkedToday = max(0, $totalStaff - $staffAttendanceToday->count());
        $staffNotMarkedPct = $totalStaff > 0 ? round(($staffNotMarkedToday / $totalStaff) * 100, 1) : 0.0;
        $staffAttendancePct = $totalStaff > 0 
            ? round((($staffPresentToday + ($staffHalfdayToday * 0.5)) / $totalStaff) * 100) 
            : 0;

        // ── 5. RECENT UPDATES (4 TABS) ────────────────────────────────────────
        $sessStart = ($currentSession && $currentSession->start_date) ? Carbon::parse($currentSession->start_date)->startOfDay() : null;
        $sessEnd = ($currentSession && $currentSession->end_date) ? Carbon::parse($currentSession->end_date)->endOfDay() : null;
        $sessName = $currentSession?->name;

        // 1. Notices Tab
        $noticesQuery = Notice::where('school_id', $schoolId);
        if ($sessStart && $sessEnd) {
            $noticesQuery->where(function($q) use ($sessStart, $sessEnd) {
                $q->whereBetween('created_at', [$sessStart, $sessEnd])
                  ->orWhereBetween('publish_at', [$sessStart, $sessEnd]);
            });
        }
        $notices = $schoolId ? $noticesQuery->latest()->take(10)->get()->map(function($n) {
            return [
                'id' => $n->id,
                'title' => $n->title,
                'content' => $n->content ?: $n->description ?: 'Campus notice and circular update.',
                'audience' => $n->target_audience ?? 'All',
                'date' => $n->created_at ? $n->created_at->format('d M Y') : '-',
            ];
        }) : [];

        // 2. Visitors Tab
        $visitorsQuery = Visitor::where('school_id', $schoolId);
        if ($currentSession) {
            $visitorsQuery->where(function($q) use ($currentSession, $sessStart, $sessEnd) {
                $q->where('academic_session_id', $currentSession->id);
                if ($sessStart && $sessEnd) {
                    $q->orWhereBetween('created_at', [$sessStart, $sessEnd]);
                }
            });
        }
        $visitors = $schoolId ? $visitorsQuery->latest()->take(10)->get()->map(function($v) {
            return [
                'id' => $v->id,
                'name' => $v->full_name,
                'type' => $v->visitor_type ?? 'Visitor',
                'whom_to_meet' => $v->host_name ?? $v->whom_to_meet_type ?? 'Staff',
                'purpose' => $v->visit_purpose ?? 'General Visit',
                'date' => $v->created_at ? $v->created_at->format('d M Y') : '-',
                'status' => ucfirst($v->status ?? 'Pending'),
            ];
        }) : [];

        // 3. Leaves Tab
        $leavesQuery = StaffLeaveApplication::where('school_id', $schoolId);
        if ($currentSession) {
            $leavesQuery->where(function($q) use ($sessName, $sessStart, $sessEnd) {
                if ($sessName) {
                    $q->where('academic_year', $sessName);
                }
                if ($sessStart && $sessEnd) {
                    $q->orWhere(function($sub) use ($sessStart, $sessEnd) {
                        $sub->whereBetween('start_date', [$sessStart->toDateString(), $sessEnd->toDateString()])
                            ->orWhereBetween('created_at', [$sessStart, $sessEnd]);
                    });
                }
            });
        }
        $leaves = $schoolId ? $leavesQuery->with(['staff'])->latest()->take(10)->get()->map(function($l) {
            $staffName = $l->staff ? trim($l->staff->first_name . ' ' . ($l->staff->last_name ?? '')) : 'Staff';
            return [
                'id' => $l->id,
                'user_name' => $staffName,
                'leave_type' => ucfirst($l->leave_type_name ?? $l->leave_type_code ?? 'Leave'),
                'start_date' => $l->start_date ? Carbon::parse($l->start_date)->format('d M Y') : '-',
                'end_date' => $l->end_date ? Carbon::parse($l->end_date)->format('d M Y') : '-',
                'reason' => $l->reason ?: 'Personal leave application',
                'status' => ucfirst($l->status ?? 'Pending'),
                'applicant_type' => 'Staff',
            ];
        }) : [];

        // 4. Digital Diary Tab
        $diariesQuery = DigitalDiary::where('school_id', $schoolId);
        if ($sessStart && $sessEnd) {
            $diariesQuery->where(function($q) use ($sessStart, $sessEnd) {
                $q->whereBetween('diary_date', [$sessStart->toDateString(), $sessEnd->toDateString()])
                  ->orWhere(function($sub) use ($sessStart, $sessEnd) {
                      $sub->whereNull('diary_date')->whereBetween('created_at', [$sessStart, $sessEnd]);
                  });
            });
        }
        $diaries = $schoolId ? $diariesQuery->with(['staff', 'class', 'section'])->latest()->take(10)->get()->map(function($d) {
            return [
                'id' => $d->id,
                'title' => $d->title ?: 'Class Activity',
                'content' => $d->content ?: 'Daily coursework completed in class.',
                'staff_name' => $d->staff ? $d->staff->full_name : ($d->teacher ? $d->teacher->full_name : 'Teacher'),
                'class_section' => ($d->class ? $d->class->name : '') . ($d->section ? (' - ' . $d->section->name) : ''),
                'date' => $d->diary_date ? Carbon::parse($d->diary_date)->format('d M Y') : '-',
            ];
        }) : [];

        // ── 6. EVENTS & UPCOMING BIRTHDAYS ───────────────────────────────────
        $todayEvents = $schoolId ? Event::where('school_id', $schoolId)
            ->whereDate('start_date', '<=', today()->toDateString())
            ->whereDate('end_date', '>=', today()->toDateString())
            ->get()->map(function($e) {
                return [
                    'id' => $e->id,
                    'title' => $e->title,
                    'type' => $e->type ?? 'Event',
                    'start_date' => $e->start_date ? Carbon::parse($e->start_date)->format('d M Y') : '-',
                    'end_date' => $e->end_date ? Carbon::parse($e->end_date)->format('d M Y') : '-',
                ];
            }) : [];

        $targetMonth = (int) ($request->query('month') ?: now()->month);
        $upcomingBirthdays = [];
        if ($schoolId) {
            $studentsWithDob = Student::where('school_id', $schoolId)->where('is_active', true)->whereNotNull('date_of_birth')->get(['id', 'first_name', 'last_name', 'date_of_birth']);
            foreach ($studentsWithDob as $s) {
                if ($s->date_of_birth && Carbon::parse($s->date_of_birth)->month === $targetMonth) {
                    $upcomingBirthdays[] = [
                        'id' => 'st_' . $s->id,
                        'name' => trim($s->first_name . ' ' . ($s->last_name ?? '')),
                        'role' => 'Student',
                        'type' => 'student',
                        'date' => Carbon::parse($s->date_of_birth)->format('d M'),
                        'day' => (int) Carbon::parse($s->date_of_birth)->day,
                    ];
                }
            }
            $staffWithDob = Staff::where('school_id', $schoolId)->where('is_active', true)->whereNotNull('date_of_birth')->get(['id', 'first_name', 'last_name', 'date_of_birth']);
            foreach ($staffWithDob as $st) {
                if ($st->date_of_birth && Carbon::parse($st->date_of_birth)->month === $targetMonth) {
                    $upcomingBirthdays[] = [
                        'id' => 'sf_' . $st->id,
                        'name' => trim($st->first_name . ' ' . ($st->last_name ?? '')),
                        'role' => 'Teacher / Faculty',
                        'type' => 'teacher',
                        'date' => Carbon::parse($st->date_of_birth)->format('d M'),
                        'day' => (int) Carbon::parse($st->date_of_birth)->day,
                    ];
                }
            }
            usort($upcomingBirthdays, fn($a, $b) => $a['day'] <=> $b['day']);
        }

        // ── 7. ROTATING MARQUEE ALERTS ────────────────────────────────────────
        $alerts = [
            ['id' => 1, 'type' => 'session', 'title' => 'Academic Session', 'text' => ($currentSession->name ?? '2026-2027') . ' Active'],
            ['id' => 2, 'type' => 'attendance', 'title' => "Today's Attendance", 'text' => "Students {$studentAttendancePct}% • Staff {$staffAttendancePct}% Present"],
            ['id' => 3, 'type' => 'collection', 'title' => 'Fee Collections', 'text' => "₹" . number_format($feeMetrics['todayFeeCollection'], 2) . " received today"],
        ];
        if (!empty($todayEvents)) {
            $alerts[] = ['id' => 4, 'type' => 'event', 'title' => 'Campus Event', 'text' => "Today: " . $todayEvents[0]['title']];
        }

        // ── 8. PRIMARY ACTIONS ───────────────────────────────────────────────
        $quickActions = [
            ['key' => 'student_directory', 'title' => 'Student Directory', 'icon' => 'graduation-cap', 'color' => '#0252d9', 'route' => 'StudentDirectory'],
            ['key' => 'attendance', 'title' => 'Mark Attendance', 'icon' => 'calendar-check', 'color' => '#10b981', 'route' => 'StudentAttendance'],
            ['key' => 'finance', 'title' => 'Finance & Fees', 'icon' => 'credit-card', 'color' => '#8b5cf6', 'route' => 'FinanceManagement'],
            ['key' => 'transport', 'title' => 'Fleet Transport', 'icon' => 'bus', 'color' => '#f59e0b', 'route' => 'TransportDashboard'],
            ['key' => 'ai_assistant', 'title' => 'AI Assistant', 'icon' => 'bot', 'color' => '#6366f1', 'route' => 'AiAssistant'],
            ['key' => 'mis_report', 'title' => 'Daily MIS Report', 'icon' => 'bar-chart-3', 'color' => '#0891b2', 'route' => 'MisReport'],
            ['key' => 'notices', 'title' => 'Notice Board', 'icon' => 'megaphone', 'color' => '#ec4899', 'route' => 'Notices'],
            ['key' => 'staff_directory', 'title' => 'Staff Directory', 'icon' => 'users', 'color' => '#14b8a6', 'route' => 'StaffDirectory'],
        ];

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->roles->first()?->name ?? 'Administrator',
                    'designation' => $user->designation ?? 'School Administrator',
                ] : [
                    'id' => 1,
                    'name' => 'School Administrator',
                    'email' => 'admin@school.com',
                    'role' => 'Administrator',
                    'designation' => 'Principal / School Admin',
                ],
                'school' => [
                    'id' => $school?->id,
                    'name' => $school?->name ?? 'EduCore School ERP',
                    'code' => $school?->code ?? 'ADMIN',
                    'logo_url' => $school?->logo_url ?: ($school?->logo ?: null),
                ],
                'available_sessions' => $sessions->map(fn($s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                    'is_current' => (bool) $s->is_current,
                ]),
                'current_session' => [
                    'id' => $currentSession?->id,
                    'name' => $currentSession?->name ?? '2026-2027',
                    'start_date' => $currentSession?->start_date,
                    'end_date' => $currentSession?->end_date,
                ],
                'headcount' => [
                    'total_students' => $totalStudents,
                    'total_staff' => $totalStaff,
                    'student_male_count' => $studentMaleCount,
                    'student_female_count' => $studentFemaleCount,
                    'student_not_mapped_count' => $studentNotMappedCount,
                    'student_male_pct' => $studentMalePct,
                    'student_female_pct' => $studentFemalePct,
                    'student_not_mapped_pct' => $studentNotMappedPct,
                    'staff_male_count' => $staffMaleCount,
                    'staff_female_count' => $staffFemaleCount,
                    'staff_not_mapped_count' => $staffNotMappedCount,
                    'staff_male_pct' => $staffMalePct,
                    'staff_female_pct' => $staffFemalePct,
                    'staff_not_mapped_pct' => $staffNotMappedPct,
                    'student_newly_joined' => $studentNewlyJoined,
                    'student_exited' => $studentExited,
                    'student_strength' => $studentStrength,
                    'staff_newly_joined' => $staffNewlyJoined,
                    'staff_exited' => $staffExited,
                    'staff_strength' => $staffStrength,
                    'admission_enquiry' => $admissionEnquiry,
                    'admission_application' => $admissionApplication,
                    'admission_evaluation' => $admissionEvaluation,
                    'admission_payment' => $admissionPayment,
                    'admission_count' => $admissionCount,
                    'total_pipeline' => $totalPipeline,
                ],
                'financial_overview' => [
                    'filter' => $financialFilter,
                    'labels' => $months,
                    'income_data' => $incomeData,
                    'expense_data' => $expenseData,
                    'total_income' => $totalIncome,
                    'total_expense' => $totalExpense,
                    'net_balance' => $netBalance,
                ],
                'fee_management' => [
                    'till_date_collected' => (float) $feeMetrics['tillDateCollected'],
                    'till_date_due' => (float) $feeMetrics['tillDateDue'],
                    'till_date_total' => (float) $feeMetrics['tillDateTotal'],
                    'till_date_collected_pct' => (float) $feeMetrics['tillDateCollectedPct'],
                    'till_date_due_pct' => (float) $feeMetrics['tillDateDuePct'],
                    'till_date_pending_students' => (int) $feeMetrics['tillDatePendingStudentsCount'],
                    'annual_collected' => (float) $feeMetrics['annualCollected'],
                    'annual_due' => (float) $feeMetrics['annualDue'],
                    'annual_total' => (float) $feeMetrics['annualTotal'],
                    'annual_collected_pct' => (float) $feeMetrics['annualCollectedPct'],
                    'annual_due_pct' => (float) $feeMetrics['annualDuePct'],
                    'annual_pending_students' => (int) $feeMetrics['annualPendingStudentsCount'],
                    'annual_assigned_students' => (int) $feeMetrics['annualAssignedStudentsCount'],
                    'today_collection' => (float) $feeMetrics['todayFeeCollection'],
                    'today_due' => (float) $feeMetrics['todayFeeDue'],
                    'today_collection_pct' => (int) $feeMetrics['todayFeeCollectionPct'],
                    'school_pending_cheques' => (float) $feeMetrics['schoolPendingChequesTotal'],
                ],
                'attendance_overview' => [
                    'student' => [
                        'present' => $studentPresentToday,
                        'absent' => $studentAbsentToday,
                        'half_day' => $studentHalfDayToday,
                        'leave' => $studentLeaveToday,
                        'custom' => $studentCustomToday,
                        'not_marked' => $studentNotMarkedToday,
                        'not_marked_pct' => $studentNotMarkedPct,
                        'attendance_pct' => $studentAttendancePct,
                        'total' => $totalStudents,
                    ],
                    'staff' => [
                        'present' => $staffPresentToday,
                        'absent' => $staffAbsentToday,
                        'half_day' => $staffHalfdayToday,
                        'leave' => $staffLeaveToday,
                        'custom' => $staffCustomToday,
                        'not_marked' => $staffNotMarkedToday,
                        'not_marked_pct' => $staffNotMarkedPct,
                        'attendance_pct' => $staffAttendancePct,
                        'total' => $totalStaff,
                    ],
                ],
                'recent_updates' => [
                    'notices' => $notices,
                    'visitors' => $visitors,
                    'leaves' => $leaves,
                    'diaries' => $diaries,
                ],
                'calendar' => [
                    'today_events' => $todayEvents,
                    'upcoming_birthdays' => $upcomingBirthdays,
                ],
                'alerts' => $alerts,
                'quick_actions' => $quickActions,
            ]
        ]);
    }

    /**
     * Reusable monthly financial calculation for mobile dashboard (Parity with SchoolDashboardController).
     */
    private function calculateMonthlyFinancialOverview(int $schoolId, ?AcademicSession $currentSession, string $filter = 'This Year'): array
    {
        $isSqlite = DB::getDriverName() === 'sqlite';

        // 1. Session Date Range
        $sessionStart = null;
        $sessionEnd   = null;

        if ($currentSession && $currentSession->start_date && $currentSession->end_date) {
            $sessionStart = Carbon::parse($currentSession->start_date)->startOfDay();
            $sessionEnd   = Carbon::parse($currentSession->end_date)->endOfDay();
        } elseif ($currentSession && !empty($currentSession->name)) {
            preg_match_all('/\b(20\d\d)\b/', $currentSession->name, $matches);
            if (!empty($matches[0])) {
                $years = array_map('intval', $matches[0]);
                $startYear = min($years);
                $endYear = max($years);
                if (count($years) === 1 && str_contains($currentSession->name, '-')) {
                    $endYear = $startYear + 1;
                }
                $sessionStart = Carbon::create($startYear, 4, 1)->startOfDay();
                $sessionEnd   = Carbon::create($endYear, 3, 31)->endOfDay();
            }
        }

        // 2. Default: 'This Year' (April to March)
        $labels = ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'];
        $incomeData  = array_fill(0, 12, 0.0);
        $expenseData = array_fill(0, 12, 0.0);

        $dateColExpr = "COALESCE(payment_date, created_at)";
        $mExpr = $isSqlite ? "CAST(strftime('%m', {$dateColExpr}) as integer)" : "MONTH({$dateColExpr})";

        // Query Fee Receipts
        $receiptQuery = FeeReceipt::where('school_id', $schoolId)
            ->where(function($q) {
                $q->where('status', '!=', 'cancelled')->orWhereNull('status');
            })
            ->where('amount_paid', '>', 0);

        if ($sessionStart && $sessionEnd) {
            $receiptQuery->whereBetween(DB::raw("DATE({$dateColExpr})"), [$sessionStart->toDateString(), $sessionEnd->toDateString()]);
        }
        $receiptTotals = $receiptQuery->selectRaw("{$mExpr} as m, SUM(amount_paid) as total")
            ->groupByRaw($mExpr)
            ->get();

        $hasReceipts = false;
        foreach ($receiptTotals as $row) {
            $month = (int) $row->m;
            $idx = ($month >= 4) ? ($month - 4) : ($month + 8);
            if ($idx >= 0 && $idx < 12) {
                $incomeData[$idx] += (float) $row->total;
                $hasReceipts = true;
            }
        }

        // Fallback to FeeInvoice if no receipts
        if (!$hasReceipts) {
            $invDateExpr = "COALESCE(payment_date, created_at)";
            $invMExpr = $isSqlite ? "CAST(strftime('%m', {$invDateExpr}) as integer)" : "MONTH({$invDateExpr})";
            $invQuery = FeeInvoice::where('school_id', $schoolId)
                ->where('type', 'payment')
                ->whereIn('status', ['paid', 'active', 'success'])
                ->where('amount', '>', 0);

            if ($sessionStart && $sessionEnd) {
                $invQuery->whereBetween(DB::raw("DATE({$invDateExpr})"), [$sessionStart->toDateString(), $sessionEnd->toDateString()]);
            }
            $invTotals = $invQuery->selectRaw("{$invMExpr} as m, SUM(amount) as total")
                ->groupByRaw($invMExpr)
                ->get();

            foreach ($invTotals as $row) {
                $month = (int) $row->m;
                $idx = ($month >= 4) ? ($month - 4) : ($month + 8);
                if ($idx >= 0 && $idx < 12) {
                    $incomeData[$idx] += (float) $row->total;
                }
            }
        }

        // General School Income
        $incDateExpr = "COALESCE(income_date, created_at)";
        $incMExpr = $isSqlite ? "CAST(strftime('%m', {$incDateExpr}) as integer)" : "MONTH({$incDateExpr})";
        $incQuery = SchoolIncome::where('school_id', $schoolId)
            ->where('status', '!=', 'cancelled')
            ->where('amount', '>', 0);

        if ($sessionStart && $sessionEnd) {
            $incQuery->whereBetween(DB::raw("DATE({$incDateExpr})"), [$sessionStart->toDateString(), $sessionEnd->toDateString()]);
        }
        $incTotals = $incQuery->selectRaw("{$incMExpr} as m, SUM(amount) as total")
            ->groupByRaw($incMExpr)
            ->get();

        foreach ($incTotals as $row) {
            $month = (int) $row->m;
            $idx = ($month >= 4) ? ($month - 4) : ($month + 8);
            if ($idx >= 0 && $idx < 12) {
                $incomeData[$idx] += (float) $row->total;
            }
        }

        // General School Expenses
        $expDateExpr = "COALESCE(expense_date, created_at)";
        $expMExpr = $isSqlite ? "CAST(strftime('%m', {$expDateExpr}) as integer)" : "MONTH({$expDateExpr})";
        $expQuery = SchoolExpense::where('school_id', $schoolId)
            ->where('status', '!=', 'cancelled')
            ->where('amount', '>', 0);

        if ($sessionStart && $sessionEnd) {
            $expQuery->whereBetween(DB::raw("DATE({$expDateExpr})"), [$sessionStart->toDateString(), $sessionEnd->toDateString()]);
        }
        $expTotals = $expQuery->selectRaw("{$expMExpr} as m, SUM(amount) as total")
            ->groupByRaw($expMExpr)
            ->get();

        foreach ($expTotals as $row) {
            $month = (int) $row->m;
            $idx = ($month >= 4) ? ($month - 4) : ($month + 8);
            if ($idx >= 0 && $idx < 12) {
                $expenseData[$idx] += (float) $row->total;
            }
        }

        return [
            'labels'      => $labels,
            'incomeData'  => $incomeData,
            'expenseData' => $expenseData,
        ];
    }

    /**
     * Get feature visibility status across all scopes.
     */
    public function features(Request $request): JsonResponse
    {
        $user = Auth::user();
        $schoolId = $user ? $user->school_id : null;
        $registeredFeatures = FeatureVisibilityHelper::registeredFeatures();
        $scopes = ['web', 'mobile', 'student', 'teacher', 'parent', 'staff', 'admin'];

        $matrix = [];
        foreach ($registeredFeatures as $key => $meta) {
            $scopeStatus = [];
            foreach ($scopes as $scope) {
                $scopeStatus[$scope] = FeatureVisibilityHelper::isVisible($key, $scope, $schoolId);
            }
            $matrix[$key] = [
                'label' => $meta['label'],
                'category' => $meta['category'],
                'scopes' => $scopeStatus,
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $matrix,
        ]);
    }
}
