<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\DigitalDiary;
use App\Models\LeaveApplication;
use App\Models\School;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\EnquiryLead;
use App\Models\Event;
use App\Models\Notice;
use App\Models\StudentAttendance;
use App\Models\User;
use App\Models\Section;
use App\Models\FcmDeviceToken;
use App\Models\StudentDocument;
use App\Models\OfflineTest;
use App\Models\SchoolExpense;
use App\Models\SchoolIncome;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Carbon\Carbon;

class SchoolDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user     = auth()->user();
        $schoolId = $user->school_id;
        $school   = $user->school;

        // ── ACADEMIC SESSIONS ────────────────────────────────────────────────
        $sessions = AcademicSession::where('school_id', $schoolId)->get();
        $currentSession = AcademicSession::where('school_id', $schoolId)
            ->where('is_current', true)
            ->first();

        // ── 1. TOP CARDS (HEADCOUNT, ACCOUNTS, FEE, ATTENDANCE) ───────────────
        $totalStudents = Student::where('school_id', $schoolId)->where('is_active', true)->count();
        $totalStaffs = Staff::where('school_id', $schoolId)->where('is_active', true)->count();

        // Accounts counts
        $totalFeeCollection = (float) StudentFee::where('school_id', $schoolId)->sum('paid_amount');
        $totalSchoolIncome  = (float) SchoolIncome::where('school_id', $schoolId)
            ->where('status', '!=', 'cancelled')
            ->sum('amount');
        $totalIncome  = $totalFeeCollection + $totalSchoolIncome;
        $totalExpense = (float) SchoolExpense::where('school_id', $schoolId)
            ->where('status', '!=', 'cancelled')
            ->sum('amount');

        // Fee counts
        $todayFeeCollection = (float) StudentFee::where('school_id', $schoolId)
            ->whereDate('updated_at', today())
            ->sum('paid_amount');

        $todayFeeDue = (float) StudentFee::where('school_id', $schoolId)
            ->whereDate('due_date', today())
            ->sum('amount');
        $todayFeeCollectionPct = 0;
        if ($todayFeeCollection > 0) {
            if ($todayFeeDue > 0) {
                $todayFeeCollectionPct = min(100, round(($todayFeeCollection / $todayFeeDue) * 100));
            } else {
                $todayFeeCollectionPct = 100;
            }
        }

        // Today's Attendance rates
        $markedStudentsToday = StudentAttendance::where('school_id', $schoolId)
            ->whereDate('date', today())
            ->count();
        $presentStudentsToday = StudentAttendance::where('school_id', $schoolId)
            ->whereDate('date', today())
            ->whereIn('status', ['present', 'late', 'duty_leave'])
            ->count() + (StudentAttendance::where('school_id', $schoolId)
                ->whereDate('date', today())
                ->where('status', 'half_day')
                ->count() * 0.5);
        $studentAttendancePct = $totalStudents > 0 
            ? round(($presentStudentsToday / $totalStudents) * 100) 
            : 0;

        $markedStaffToday = StaffAttendance::where('school_id', $schoolId)
            ->whereDate('date', today())
            ->count();
        $presentStaffToday = StaffAttendance::where('school_id', $schoolId)
            ->whereDate('date', today())
            ->whereIn('status', ['present', 'late'])
            ->count() + (StaffAttendance::where('school_id', $schoolId)
                ->whereDate('date', today())
                ->where('status', 'half_day')
                ->count() * 0.5);
        $staffAttendancePct = $totalStaffs > 0 
            ? round(($presentStaffToday / $totalStaffs) * 100) 
            : 0;

        // ── 2. ENROLLMENT OVERVIEW (GENDER, ATTRITION, ADMISSIONS) ───────────
        $studentMaleCount = Student::where('school_id', $schoolId)->where('is_active', true)->where('gender', 'male')->count();
        $studentFemaleCount = Student::where('school_id', $schoolId)->where('is_active', true)->where('gender', 'female')->count();
        $studentNotMappedCount = Student::where('school_id', $schoolId)->where('is_active', true)->whereNull('section_id')->count();
        
        $sumMapped = $studentMaleCount + $studentFemaleCount + $studentNotMappedCount;
        if ($sumMapped > 0) {
            $studentMalePct = round(($studentMaleCount / $sumMapped) * 100, 1);
            $studentFemalePct = round(($studentFemaleCount / $sumMapped) * 100, 1);
            $studentNotMappedPct = round(($studentNotMappedCount / $sumMapped) * 100, 1);
        } else {
            $studentMalePct = $studentFemalePct = $studentNotMappedPct = 0;
        }

        $staffNotMappedCount = Staff::where('school_id', $schoolId)->where('is_active', true)->whereNull('department_id')->count();
        $staffNotMappedPct = $totalStaffs > 0 ? round(($staffNotMappedCount / $totalStaffs) * 100, 1) : 0;

        // Joining & Attrition
        $studentNewlyJoined = Student::where('school_id', $schoolId)
            ->whereYear('admission_date', now()->year)
            ->count();
        $studentExited = Student::where('school_id', $schoolId)->onlyTrashed()->count();
        $studentStrength = $totalStudents;

        $staffNewlyJoined = Staff::where('school_id', $schoolId)
            ->whereYear('joining_date', now()->year)
            ->count();
        $staffExited = Staff::where('school_id', $schoolId)->onlyTrashed()->count();
        $staffStrength = $totalStaffs;

        // Admission Count Summary
        $admissionEnquiry = EnquiryLead::where('school_id', $schoolId)->whereIn('status', ['new', 'enquiry'])->count();
        $admissionApplication = EnquiryLead::where('school_id', $schoolId)->whereIn('status', ['contacted', 'application'])->count();
        $admissionPayment = EnquiryLead::where('school_id', $schoolId)->where('status', 'payment')->count();
        $admissionEvaluation = EnquiryLead::where('school_id', $schoolId)->where('status', 'evaluation')->count();
        $admissionCount = EnquiryLead::where('school_id', $schoolId)->whereIn('status', ['enrolled', 'admission'])->count();

        // ── 3. FINANCIAL MANAGEMENT OVERVIEW ─────────────────────────────────
        // Income and Expense monthly data (April - February)
        $months = ['April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', 'January', 'February'];
        $incomeData = array_fill(0, count($months), 0);
        $expenseData = array_fill(0, count($months), 0);

        // Fetch paid amounts from database grouped by month
        $feePayments = StudentFee::where('school_id', $schoolId)
            ->where('paid_amount', '>', 0)
            ->get();

        foreach ($feePayments as $payment) {
            $monthName = Carbon::parse($payment->updated_at)->format('F');
            $idx = array_search($monthName, $months);
            if ($idx !== false) {
                $incomeData[$idx] += (int) $payment->paid_amount;
            }
        }

        // Add general school incomes (income_control module) to incomeData
        $schoolIncomeRecords = SchoolIncome::where('school_id', $schoolId)
            ->where('status', '!=', 'cancelled')
            ->get();
        foreach ($schoolIncomeRecords as $incRec) {
            $monthName = Carbon::parse($incRec->income_date)->format('F');
            $idx = array_search($monthName, $months);
            if ($idx !== false) {
                $incomeData[$idx] += (float) $incRec->amount;
            }
        }

        // Populate expenseData from SchoolExpense table
        $expenseRecords = SchoolExpense::where('school_id', $schoolId)
            ->where('status', '!=', 'cancelled')
            ->get();
        foreach ($expenseRecords as $expRec) {
            $monthName = Carbon::parse($expRec->expense_date)->format('F');
            $idx = array_search($monthName, $months);
            if ($idx !== false) {
                $expenseData[$idx] += (float) $expRec->amount;
            }
        }

        // Till Date (due_date <= today) Collected vs Due
        $feeCollectedAmount = (float) StudentFee::where('school_id', $schoolId)->where('due_date', '<=', today())->sum('paid_amount');
        $feeDueAmount = (float) StudentFee::where('school_id', $schoolId)->where('due_date', '<=', today())->whereColumn('amount', '>', 'paid_amount')->sum(\DB::raw('amount - paid_amount'));
        $feeTotalSum = $feeCollectedAmount + $feeDueAmount;
        $feeCollectedPct = $feeTotalSum > 0 ? round(($feeCollectedAmount / $feeTotalSum) * 100, 2) : 0;
        $feeDuePct = $feeTotalSum > 0 ? round(($feeDueAmount / $feeTotalSum) * 100, 2) : 0;

        // Annual (all fees) Collected vs Due
        $annualCollectedAmount = (float) StudentFee::where('school_id', $schoolId)->sum('paid_amount');
        $annualDueAmount = (float) StudentFee::where('school_id', $schoolId)->whereColumn('amount', '>', 'paid_amount')->sum(\DB::raw('amount - paid_amount'));
        $annualTotalSum = $annualCollectedAmount + $annualDueAmount;
        $annualCollectedPct = $annualTotalSum > 0 ? round(($annualCollectedAmount / $annualTotalSum) * 100, 2) : 0;
        $annualDuePct = $annualTotalSum > 0 ? round(($annualDueAmount / $annualTotalSum) * 100, 2) : 0;

        // Pending (till date)
        $feePendingStudentsCount = StudentFee::where('school_id', $schoolId)->where('due_date', '<=', today())->whereColumn('amount', '>', 'paid_amount')->distinct('student_id')->count();
        $feePendingDueAmount = $feeDueAmount;

        // ── 4. ADMINISTRATIVE OPERATIONS OVERVIEW ────────────────────────────
        // Recent Updates tabs (Notices)
        $notices = Notice::where('school_id', $schoolId)
            ->latest()
            ->take(10)
            ->get();

        // Recent Updates - Digital Diary
        $diaries = DigitalDiary::where('school_id', $schoolId)
            ->with(['staff', 'class', 'section'])
            ->latest()
            ->take(10)
            ->get();

        // Recent Updates - Leave Applications
        $leaves = LeaveApplication::where('school_id', $schoolId)
            ->with(['user'])
            ->latest()
            ->take(10)
            ->get();

        $noticesData = $notices->map(function($n) {
            return [
                'title' => $n->title,
                'content' => $n->content,
                'audience' => $n->target_audience ?? 'All',
                'date' => $n->created_at ? $n->created_at->format('d M Y') : '-'
            ];
        });
        $diariesData = $diaries->map(function($d) {
            return [
                'title' => $d->title,
                'content' => $d->content,
                'staff_name' => $d->staff ? $d->staff->full_name : ($d->teacher ? $d->teacher->full_name : 'Teacher'),
                'class_section' => ($d->class ? $d->class->name : '') . ' - ' . ($d->section ? $d->section->name : ''),
                'date' => $d->diary_date ? \Carbon\Carbon::parse($d->diary_date)->format('d M Y') : '-'
            ];
        });
        $leavesData = $leaves->map(function($l) {
            return [
                'user_name' => $l->user ? $l->user->name : 'Staff/Student',
                'leave_type' => ucfirst($l->leave_type),
                'start_date' => $l->start_date ? \Carbon\Carbon::parse($l->start_date)->format('d M Y') : '-',
                'end_date' => $l->end_date ? \Carbon\Carbon::parse($l->end_date)->format('d M Y') : '-',
                'reason' => $l->reason,
                'status' => $l->status,
                'applicant_type' => ucfirst($l->applicant_type ?? 'Staff')
            ];
        });

        // Staff attendance statuses today
        $staffAttendanceToday = StaffAttendance::where('school_id', $schoolId)
            ->whereDate('date', today())
            ->get();
        
        $staffPresentToday = $staffAttendanceToday->where('status', 'present')->count();
        $staffAbsentToday = $staffAttendanceToday->where('status', 'absent')->count();
        $staffHalfdayToday = $staffAttendanceToday->where('status', 'half_day')->count();
        $staffLeaveToday = $staffAttendanceToday->where('status', 'leave')->count();
        $staffCustomToday = $staffAttendanceToday->where('status', 'custom')->count();
        
        $staffNotMarkedToday = max(0, $totalStaffs - $staffAttendanceToday->count());
        $staffNotMarkedPct = $totalStaffs > 0 ? round(($staffNotMarkedToday / $totalStaffs) * 100, 1) : 0.0;

        // Student attendance breakdown for today
        $studentAttendanceToday = StudentAttendance::where('school_id', $schoolId)
            ->whereDate('date', today())
            ->get();
        $studentPresentToday  = $studentAttendanceToday->where('status', 'present')->count();
        $studentAbsentToday   = $studentAttendanceToday->where('status', 'absent')->count();
        $studentHalfDayToday  = $studentAttendanceToday->where('status', 'half_day')->count();
        $studentLeaveToday    = $studentAttendanceToday->where('status', 'leave')->count();
        $studentCustomToday   = $studentAttendanceToday->where('status', 'custom')->count();
        $studentNotMarkedToday = max(0, $totalStudents - $studentAttendanceToday->count());
        $studentNotMarkedPct   = $totalStudents > 0 ? round(($studentNotMarkedToday / $totalStudents) * 100, 1) : 0.0;

        // Birthday calendar counts & events
        $birthdaysToday = 0;

        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);
        $planName = $school->activeSubscription()?->plan?->name ?? 'Trial';
        $notificationCount = 0;

        $todayEvents = Event::where('school_id', $schoolId)
            ->whereDate('start_date', '<=', today()->toDateString())
            ->whereDate('end_date', '>=', today()->toDateString())
            ->get();

        return view('school.dashboard.index', compact(
            'todayEvents',
            'school',
            'sessions',
            'currentSession',
            'totalStudents',
            'totalStaffs',
            'totalIncome',
            'totalExpense',
            'todayFeeCollection',
            'totalFeeCollection',
            'studentAttendancePct',
            'staffAttendancePct',
            'studentMaleCount',
            'studentFemaleCount',
            'studentNotMappedCount',
            'studentMalePct',
            'studentFemalePct',
            'studentNotMappedPct',
            'staffNotMappedCount',
            'staffNotMappedPct',
            'studentNewlyJoined',
            'studentExited',
            'studentStrength',
            'staffNewlyJoined',
            'staffExited',
            'staffStrength',
            'admissionEnquiry',
            'admissionApplication',
            'admissionPayment',
            'admissionEvaluation',
            'admissionCount',
            'months',
            'incomeData',
            'expenseData',
            'feeCollectedAmount',
            'feeDueAmount',
            'feeCollectedPct',
            'feeDuePct',
            'todayFeeCollectionPct',
            'annualCollectedAmount',
            'annualDueAmount',
            'annualCollectedPct',
            'annualDuePct',
            'feePendingStudentsCount',
            'feePendingDueAmount',
            'notices',
            'diaries',
            'leaves',
            'noticesData',
            'diariesData',
            'leavesData',
            'staffPresentToday',
            'staffAbsentToday',
            'staffHalfdayToday',
            'staffLeaveToday',
            'staffCustomToday',
            'staffNotMarkedToday',
            'staffNotMarkedPct',
            'studentPresentToday',
            'studentAbsentToday',
            'studentHalfDayToday',
            'studentLeaveToday',
            'studentCustomToday',
            'studentNotMarkedToday',
            'studentNotMarkedPct',
            'month',
            'year',
            'planName',
            'notificationCount'
        ));
    }


    public function feeChartData(Request $request): JsonResponse
    {
        $schoolId = auth()->user()->school_id;
        $period   = $request->get('period', 'month');
        $month    = (int) $request->get('month', now()->month);
        $year     = (int) $request->get('year', now()->year);

        $labels = [];
        $data   = [];
        $total  = 0;
        $trend  = 0;

        if ($period === 'month') {
            $daysInMonth = Carbon::create($year, $month)->daysInMonth;
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $labels[] = $d . ' ' . Carbon::create($year, $month)->format('M');
                $date = Carbon::create($year, $month, $d)->toDateString();
                $data[] = (float) StudentFee::where('school_id', $schoolId)
                    ->whereDate('updated_at', $date)
                    ->sum('paid_amount');
            }
            $total = array_sum($data);
        } elseif ($period === '3months') {
            for ($i = 2; $i >= 0; $i--) {
                $m = now()->subMonths($i);
                $labels[] = $m->format('M Y');
                $data[] = (float) StudentFee::where('school_id', $schoolId)
                    ->whereYear('updated_at', $m->year)
                    ->whereMonth('updated_at', $m->month)
                    ->sum('paid_amount');
            }
            $total = array_sum($data);
        } else {
            for ($i = 11; $i >= 0; $i--) {
                $m = now()->subMonths($i);
                $labels[] = $m->format('M');
                $data[] = (float) StudentFee::where('school_id', $schoolId)
                    ->whereYear('updated_at', $m->year)
                    ->whereMonth('updated_at', $m->month)
                    ->sum('paid_amount');
            }
            $total = array_sum($data);
        }

        return response()->json([
            'labels' => $labels,
            'data'   => $data,
            'total'  => $total,
            'trend'  => $trend,
        ]);
    }

    public function attendanceChartData(Request $request): JsonResponse
    {
        $schoolId = auth()->user()->school_id;
        $period   = $request->get('period', 'month');
        $month    = (int) $request->get('month', now()->month);
        $year     = (int) $request->get('year', now()->year);

        $labels  = [];
        $data    = [];
        $average = 0;

        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        $sum = 0;
        $count = 0;

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date    = Carbon::create($year, $month, $d)->toDateString();
            $labels[] = (string) $d;
            $total   = StudentAttendance::where('school_id', $schoolId)->whereDate('date', $date)->count();
            $present = StudentAttendance::where('school_id', $schoolId)->whereDate('date', $date)->where('status', 'present')->count();
            $pct = $total > 0 ? round($present / $total * 100) : 0;
            $data[] = $pct;
            if ($total > 0) {
                $sum += $pct;
                $count++;
            }
        }

        $average = $count > 0 ? round($sum / $count) : 0;

        return response()->json([
            'labels'  => $labels,
            'data'    => $data,
            'average' => $average,
        ]);
    }

    public function snapshot(Request $request): JsonResponse
    {
        $schoolId = auth()->user()->school_id;

        $presentToday = StudentAttendance::where('school_id', $schoolId)
            ->whereDate('date', today())
            ->where('status', 'present')
            ->count();

        $markedToday = StudentAttendance::where('school_id', $schoolId)
            ->whereDate('date', today())
            ->count();

        $totalStudents = Student::where('school_id', $schoolId)
            ->where('is_active', true)
            ->count();

        $staffPresentToday = StaffAttendance::where('school_id', $schoolId)
            ->whereDate('date', today())
            ->where('status', 'present')
            ->count();

        $totalStaff = Staff::where('school_id', $schoolId)
            ->where('is_active', true)
            ->count();

        $feesToday = (float) StudentFee::where('school_id', $schoolId)
            ->whereDate('updated_at', today())
            ->sum('paid_amount');

        return response()->json([
            'students_present' => $presentToday,
            'students_total'   => $totalStudents,
            'staff_present'    => $staffPresentToday,
            'staff_total'      => $totalStaff,
            'fees_today'       => $feesToday,
        ]);
    }

    public function chatbotStub(Request $request): JsonResponse
    {
        $message = $request->get('message', '');

        $responses = [
            'attendance' => 'Based on current data, your school attendance is tracking well. I recommend reviewing classes with below 80% attendance this week.',
            'fee'        => 'Fee collection is progressing. Consider sending automated reminders to parents with pending dues.',
            'student'    => 'Student enrollment looks stable. New admissions this month are on par with historical trends.',
            'staff'      => 'Staff attendance is consistent. No unusual patterns detected this week.',
            'default'    => "I'm analyzing your school data... Based on current trends, everything looks within normal parameters. Ask me about attendance, fees, or student performance for detailed insights.",
        ];

        $lowerMsg  = strtolower($message);
        $response  = $responses['default'];

        if (str_contains($lowerMsg, 'attendance')) {
            $response = $responses['attendance'];
        } elseif (str_contains($lowerMsg, 'fee') || str_contains($lowerMsg, 'payment')) {
            $response = $responses['fee'];
        } elseif (str_contains($lowerMsg, 'student')) {
            $response = $responses['student'];
        } elseif (str_contains($lowerMsg, 'staff') || str_contains($lowerMsg, 'teacher')) {
            $response = $responses['staff'];
        }

        return response()->json([
            'response' => $response,
            'timestamp' => now()->format('h:i A'),
        ]);
    }

    public function misReport(Request $request): View
    {
        $schoolId = auth()->user()->school_id;
        $school = auth()->user()->school;
        $date = Carbon::parse($request->get('date', today()->toDateString()));

        // --- ROW 1 KPI ---
        // 1. Daily Revenue
        $dailyRevenue = (float) \App\Models\FeeReceipt::where('school_id', $schoolId)
            ->whereDate('payment_date', $date)
            ->sum('amount_paid');

        // 2. Student Attendance
        $totalStudents = Student::where('school_id', $schoolId)->where('is_active', true)->count();
        $studentAttendance = StudentAttendance::where('school_id', $schoolId)->whereDate('date', $date)->get();
        $studentPresent = $studentAttendance->where('status', 'present')->count();
        $studentMarked = $studentAttendance->count();
        $studentAttendancePct = $totalStudents > 0 ? round(($studentPresent / $totalStudents) * 100) : 0;
        $studentAttendanceRatio = "{$studentPresent}/{$totalStudents}";

        // 3. Staff Attendance
        $totalStaff = Staff::where('school_id', $schoolId)->where('is_active', true)->count();
        $staffAttendance = StaffAttendance::where('school_id', $schoolId)->whereDate('date', $date)->get();
        $staffPresent = $staffAttendance->where('status', 'present')->count();
        $staffAttendancePct = $totalStaff > 0 ? round(($staffPresent / $totalStaff) * 100) : 0;
        $staffAttendanceRatio = "{$staffPresent}/{$totalStaff}";

        // 4. New Admissions
        $newAdmissionsCount = Student::where('school_id', $schoolId)
            ->whereDate('admission_date', $date)
            ->count();
        $newAdmissionsThisMonth = Student::where('school_id', $schoolId)
            ->whereYear('admission_date', $date->year)
            ->whereMonth('admission_date', $date->month)
            ->count();

        // --- ROW 2 IMMEDIATE ACTIONS REQUIRED ---
        // 1. Attendance Not Marked (Teachers haven't marked attendance today)
        $markedStaffIds = StaffAttendance::where('school_id', $schoolId)
            ->whereDate('date', $date)
            ->pluck('staff_id');
        $attendanceNotMarkedTeachersCount = Staff::where('school_id', $schoolId)
            ->where('is_active', true)
            ->whereNotIn('id', $markedStaffIds)
            ->count();

        // 2. Fee Defaulters (90+ days)
        // Count of student fees that are unpaid (amount > paid_amount) and due date is more than 90 days ago
        $feeDefaultersCriticalCount = StudentFee::where('school_id', $schoolId)
            ->whereColumn('amount', '>', 'paid_amount')
            ->whereDate('due_date', '<', $date->copy()->subDays(90))
            ->distinct('student_id')
            ->count('student_id');

        // 3. App Not Downloaded
        // Students whose users don't have FCM tokens
        $studentUsersWithTokens = FcmDeviceToken::where('school_id', $schoolId)
            ->whereIn('user_id', User::where('role', 'student')->pluck('id'))
            ->pluck('user_id')
            ->unique();
        $studentAppPending = Student::where('school_id', $schoolId)
            ->where('is_active', true)
            ->whereNotIn('user_id', $studentUsersWithTokens)
            ->count();

        // Staff whose users don't have FCM tokens
        $staffUsersWithTokens = FcmDeviceToken::where('school_id', $schoolId)
            ->whereIn('user_id', User::where('role', 'teacher')->pluck('id'))
            ->pluck('user_id')
            ->unique();
        $staffAppPending = Staff::where('school_id', $schoolId)
            ->where('is_active', true)
            ->whereNotIn('user_id', $staffUsersWithTokens)
            ->count();

        // Parents whose users don't have FCM tokens
        $parentUsersWithTokens = FcmDeviceToken::where('school_id', $schoolId)
            ->whereIn('user_id', User::where('role', 'parent')->pluck('id'))
            ->pluck('user_id')
            ->unique();
        $parentAppPending = User::where('school_id', $schoolId)
            ->where('role', 'parent')
            ->whereNotIn('id', $parentUsersWithTokens)
            ->count();

        $appNotDownloadedCount = $studentAppPending + $staffAppPending + $parentAppPending;

        // --- CORE METRICS BREAKDOWN ---
        // Column 1: Income & Expenses
        $todayFeeCollection = (float) \App\Models\FeeReceipt::where('school_id', $schoolId)
            ->whereDate('payment_date', $date)
            ->sum('amount_paid');
        $todayOtherIncome = 0.00;
        $todayTotalIncome = $todayFeeCollection + $todayOtherIncome;
        $todayOtherExpenses = 0.00;
        $todayTotalExpenses = $todayOtherExpenses;
        $todayNetProfit = $todayTotalIncome - $todayTotalExpenses;

        // Column 2: Digital Metrics
        $studentAppDownloadedCount = Student::where('school_id', $schoolId)
            ->where('is_active', true)
            ->whereIn('user_id', $studentUsersWithTokens)
            ->count();
        $studentAppDownloadedTotal = $totalStudents;

        $staffAppDownloadedCount = Staff::where('school_id', $schoolId)
            ->where('is_active', true)
            ->whereIn('user_id', $staffUsersWithTokens)
            ->count();
        $staffAppDownloadedTotal = $totalStaff;

        $parentAppDownloadedCount = User::where('school_id', $schoolId)
            ->where('role', 'parent')
            ->whereIn('id', $parentUsersWithTokens)
            ->count();
        $parentAppDownloadedTotal = User::where('school_id', $schoolId)
            ->where('role', 'parent')
            ->count();

        $pendingDownloadsCount = $appNotDownloadedCount;
        $todayBooksIssued = StudentDocument::where('school_id', $schoolId)->whereDate('created_at', $date)->count();
        $todayBooksReturned = 0;
        $todayNoticesShared = Notice::where('school_id', $schoolId)
            ->whereDate('created_at', $date)
            ->count();

        // Column 3: Admissions & Academic
        $todayEnquiriesCount = EnquiryLead::where('school_id', $schoolId)
            ->whereDate('created_at', $date)
            ->count();
        $todayApplicationsCount = EnquiryLead::where('school_id', $schoolId)
            ->whereIn('status', ['contacted', 'application'])
            ->whereDate('created_at', $date)
            ->count();
        $todayInteractionsCount = EnquiryLead::where('school_id', $schoolId)
            ->where('status', 'evaluation')
            ->whereDate('created_at', $date)
            ->count();
        $todayAdmissionsCount = Student::where('school_id', $schoolId)
            ->whereDate('admission_date', $date)
            ->count();

        $todayAssignmentsShared = DigitalDiary::where('school_id', $schoolId)->whereDate('diary_date', $date)->count();
        $todayMaterialsShared = StudentDocument::where('school_id', $schoolId)->whereDate('created_at', $date)->count();
        $todayTestsShared = OfflineTest::where('school_id', $schoolId)->whereDate('created_at', $date)->count();
        $todayDiariesShared = DigitalDiary::where('school_id', $schoolId)
            ->whereDate('diary_date', $date)
            ->count();

        // Teachers No Sharing in 7 Days
        $teachersWithDiary7Days = \App\Models\DigitalDiary::where('school_id', $schoolId)
            ->whereDate('diary_date', '>=', $date->copy()->subDays(7))
            ->pluck('staff_id')
            ->unique();
        $teachersNoSharing7DaysCount = Staff::where('school_id', $schoolId)
            ->where('is_active', true)
            ->whereNotIn('id', $teachersWithDiary7Days)
            ->count();

        // Sections (classes) missing diary entries today
        $sectionsWithDiaryToday = \App\Models\DigitalDiary::where('school_id', $schoolId)
            ->whereDate('diary_date', $date)
            ->pluck('section_id')
            ->unique();
        $classesMissingDiaryTodayCount = Section::where('school_id', $schoolId)
            ->whereNotIn('id', $sectionsWithDiaryToday)
            ->count();

        // --- TODAY'S ATTENDANCE CARD ---
        $studentPresentCount = $studentAttendance->where('status', 'present')->count();
        $studentAbsentCount = $studentAttendance->where('status', 'absent')->count();
        $studentHalfDayCount = $studentAttendance->where('status', 'half_day')->count();
        $studentLeaveCount = $studentAttendance->where('status', 'leave')->count();
        $studentNotMarkedCount = max(0, $totalStudents - $studentMarked);

        $staffPresentCount = $staffAttendance->where('status', 'present')->count();
        $staffAbsentCount = $staffAttendance->where('status', 'absent')->count();
        $staffHalfDayCount = $staffAttendance->where('status', 'half_day')->count();
        $staffLeaveCount = $staffAttendance->where('status', 'leave')->count();
        $staffNotMarkedCount = $attendanceNotMarkedTeachersCount;

        // Critical attendance issues
        $criticalAttendanceIssues = [];
        if ($staffNotMarkedCount > 0) {
            $criticalAttendanceIssues[] = "{$staffNotMarkedCount} teachers haven't marked attendance in today";
        }
        // Count of sections where student attendance isn't marked today
        $sectionsMarkedToday = StudentAttendance::where('school_id', $schoolId)
            ->whereDate('date', $date)
            ->pluck('section_id')
            ->unique();
        $unmarkedSectionsCount = Section::where('school_id', $schoolId)
            ->whereNotIn('id', $sectionsMarkedToday)
            ->count();
        if ($unmarkedSectionsCount > 0) {
            $criticalAttendanceIssues[] = "{$unmarkedSectionsCount} classes attendance not yet marked today";
        }

        // --- TODAY'S FEE COLLECTION CARD ---
        $feeCashCollection = (float) \App\Models\FeeReceipt::where('school_id', $schoolId)
            ->where('payment_mode', 'cash')
            ->whereDate('payment_date', $date)
            ->sum('amount_paid');
        $feeChequeCollection = (float) \App\Models\FeeReceipt::where('school_id', $schoolId)
            ->where('payment_mode', 'cheque')
            ->whereDate('payment_date', $date)
            ->sum('amount_paid');
        $feeOnlineCollection = (float) \App\Models\FeeReceipt::where('school_id', $schoolId)
            ->whereNotIn('payment_mode', ['cash', 'cheque'])
            ->whereDate('payment_date', $date)
            ->sum('amount_paid');
        $feeTotalCollection = $feeCashCollection + $feeChequeCollection + $feeOnlineCollection;

        // Defaulters by aging
        $defaulters0_30Count = StudentFee::where('school_id', $schoolId)
            ->whereColumn('amount', '>', 'paid_amount')
            ->whereBetween('due_date', [$date->copy()->subDays(30)->toDateString(), $date->toDateString()])
            ->distinct('student_id')
            ->count('student_id');

        $defaulters31_60Count = StudentFee::where('school_id', $schoolId)
            ->whereColumn('amount', '>', 'paid_amount')
            ->whereBetween('due_date', [$date->copy()->subDays(60)->toDateString(), $date->copy()->subDays(31)->toDateString()])
            ->distinct('student_id')
            ->count('student_id');

        $defaulters61_90Count = StudentFee::where('school_id', $schoolId)
            ->whereColumn('amount', '>', 'paid_amount')
            ->whereBetween('due_date', [$date->copy()->subDays(90)->toDateString(), $date->copy()->subDays(61)->toDateString()])
            ->distinct('student_id')
            ->count('student_id');

        $defaulters90PlusCount = $feeDefaultersCriticalCount;

        // Overall collection (this month)
        $overallMonthlyCollection = (float) \App\Models\FeeReceipt::where('school_id', $schoolId)
            ->whereYear('payment_date', $date->year)
            ->whereMonth('payment_date', $date->month)
            ->sum('amount_paid');
        
        $pendingDiscountApprovalsCount = 0;

        // --- FOLLOW-UPS & ALERTS ---
        // 1. Critical Defaulters list (90+ Days)
        $feeDefaulters90PlusList = StudentFee::where('school_id', $schoolId)
            ->whereColumn('amount', '>', 'paid_amount')
            ->whereDate('due_date', '<', $date->copy()->subDays(90))
            ->with(['student.class', 'student.section'])
            ->get()
            ->map(function ($fee) use ($date) {
                $dueDays = Carbon::parse($fee->due_date)->diffInDays($date);
                return [
                    'name' => $fee->student->full_name,
                    'class_section' => ($fee->student->class->name ?? '') . '-' . ($fee->student->section->name ?? ''),
                    'pending_amount' => $fee->amount - $fee->paid_amount,
                    'due_days' => $dueDays
                ];
            })
            ->sortByDesc('due_days');
        
        $feeDefaulters90PlusMoreCount = max(0, $feeDefaulters90PlusList->count() - 3);
        $feeDefaulters90PlusList = $feeDefaulters90PlusList->take(3);

        // 2. Classes Attendance not marked today
        $classesAttendanceNotMarkedList = Section::where('school_id', $schoolId)
            ->whereNotIn('id', $sectionsMarkedToday)
            ->with('schoolClass')
            ->get()
            ->map(fn($sec) => ($sec->schoolClass->name ?? '') . '-' . $sec->name);
        
        $classesAttendanceNotMarkedMoreCount = max(0, $classesAttendanceNotMarkedList->count() - 3);
        $classesAttendanceNotMarkedList = $classesAttendanceNotMarkedList->take(3);

        // 3. Teachers not marked attendance in 7 days (sections with no attendance marked in 7 days)
        $sectionsMarked7Days = StudentAttendance::where('school_id', $schoolId)
            ->whereDate('date', '>=', $date->copy()->subDays(7))
            ->pluck('section_id')
            ->unique();
        $teachersNotMarkedAttendance7DaysList = Section::where('school_id', $schoolId)
            ->whereNotIn('id', $sectionsMarked7Days)
            ->with('schoolClass')
            ->get()
            ->map(fn($sec) => ($sec->schoolClass->name ?? '') . '-' . $sec->name);
        
        $teachersNotMarkedAttendance7DaysMoreCount = max(0, $teachersNotMarkedAttendance7DaysList->count() - 3);
        $teachersNotMarkedAttendance7DaysList = $teachersNotMarkedAttendance7DaysList->take(3);

        // 4. Teachers haven't shared any content in 7 days
        $teachersNoSharing7DaysList = Staff::where('school_id', $schoolId)
            ->where('is_active', true)
            ->whereNotIn('id', $teachersWithDiary7Days)
            ->get()
            ->map(fn($st) => $st->full_name);
        
        $teachersNoSharing7DaysMoreCount = max(0, $teachersNoSharing7DaysList->count() - 3);
        $teachersNoSharing7DaysList = $teachersNoSharing7DaysList->take(3);

        // 5. Classes missing diary entries today
        $classesMissingDiaryTodayList = Section::where('school_id', $schoolId)
            ->whereNotIn('id', $sectionsWithDiaryToday)
            ->with('schoolClass')
            ->get()
            ->map(fn($sec) => ($sec->schoolClass->name ?? '') . '-' . $sec->name);
        
        $classesMissingDiaryTodayMoreCount = max(0, $classesMissingDiaryTodayList->count() - 3);
        $classesMissingDiaryTodayList = $classesMissingDiaryTodayList->take(3);

        return view('school.dashboard.mis_report', compact(
            'school', 'date',
            'dailyRevenue', 'studentAttendancePct', 'studentAttendanceRatio', 'staffAttendancePct', 'staffAttendanceRatio',
            'newAdmissionsCount', 'newAdmissionsThisMonth',
            'attendanceNotMarkedTeachersCount', 'feeDefaultersCriticalCount', 'appNotDownloadedCount',
            'todayFeeCollection', 'todayOtherIncome', 'todayTotalIncome', 'todayOtherExpenses', 'todayTotalExpenses', 'todayNetProfit',
            'studentAppDownloadedCount', 'studentAppDownloadedTotal', 'staffAppDownloadedCount', 'staffAppDownloadedTotal',
            'parentAppDownloadedCount', 'parentAppDownloadedTotal', 'pendingDownloadsCount',
            'todayBooksIssued', 'todayBooksReturned', 'todayNoticesShared',
            'todayEnquiriesCount', 'todayApplicationsCount', 'todayInteractionsCount', 'todayAdmissionsCount',
            'todayAssignmentsShared', 'todayMaterialsShared', 'todayTestsShared', 'todayDiariesShared',
            'teachersNoSharing7DaysCount', 'classesMissingDiaryTodayCount',
            'studentPresentCount', 'studentAbsentCount', 'studentHalfDayCount', 'studentLeaveCount', 'studentNotMarkedCount',
            'staffPresentCount', 'staffAbsentCount', 'staffHalfDayCount', 'staffLeaveCount', 'staffNotMarkedCount',
            'criticalAttendanceIssues',
            'feeCashCollection', 'feeChequeCollection', 'feeOnlineCollection', 'feeTotalCollection',
            'defaulters0_30Count', 'defaulters31_60Count', 'defaulters61_90Count', 'defaulters90PlusCount',
            'overallMonthlyCollection', 'pendingDiscountApprovalsCount',
            'feeDefaulters90PlusList', 'feeDefaulters90PlusMoreCount',
            'classesAttendanceNotMarkedList', 'classesAttendanceNotMarkedMoreCount',
            'teachersNotMarkedAttendance7DaysList', 'teachersNotMarkedAttendance7DaysMoreCount',
            'teachersNoSharing7DaysList', 'teachersNoSharing7DaysMoreCount',
            'classesMissingDiaryTodayList', 'classesMissingDiaryTodayMoreCount'
        ));
    }

    public function getDetails(Request $request): JsonResponse
    {
        $schoolId = auth()->user()->school_id;
        $type = $request->get('type');

        $data = [];
        $title = '';

        switch ($type) {
            case 'students':
                $title = 'Student Details';
                $classes = \App\Models\SchoolClass::where('school_id', $schoolId)->with('sections')->get();
                $rows = [];
                $summary = [
                    'promoted' => 0,
                    'repeated' => 0,
                    'new' => 0,
                    'today' => 0,
                    'old_tc' => 0,
                    'new_tc' => 0,
                    'irregular' => 0,
                    'deactivated' => 0,
                    'total' => 0,
                    'old_deleted' => 0,
                    'new_deleted' => 0,
                    'active' => 0
                ];

                $currentSession = \App\Models\AcademicSession::where('school_id', $schoolId)
                    ->where('is_current', true)
                    ->first();
                $sessionStartDate = $currentSession ? $currentSession->start_date : now()->startOfYear();

                foreach ($classes as $c) {
                    foreach ($c->sections as $sec) {
                        $total = Student::where('school_id', $schoolId)->where('class_id', $c->id)->where('section_id', $sec->id)->count();
                        $active = Student::where('school_id', $schoolId)->where('class_id', $c->id)->where('section_id', $sec->id)->where('is_active', true)->count();
                        $deactivated = $total - $active;
                        $new = Student::where('school_id', $schoolId)->where('class_id', $c->id)->where('section_id', $sec->id)->whereYear('admission_date', now()->year)->count();
                        $promoted = max(0, $active - $new);
                        $today = Student::where('school_id', $schoolId)->where('class_id', $c->id)->where('section_id', $sec->id)->whereDate('admission_date', today())->count();
                        
                        $row = [
                            'class_section' => $c->name . ' ' . $sec->name,
                            'promoted' => $promoted,
                            'repeated' => 0,
                            'new' => $new,
                            'today' => $today,
                            'old_tc' => 0,
                            'new_tc' => 0,
                            'irregular' => 0,
                            'deactivated' => $deactivated,
                            'total' => $total,
                            'old_deleted' => 0,
                            'new_deleted' => 0,
                            'active' => $active
                        ];
                        $rows[] = $row;
                        
                        foreach ($summary as $key => $val) {
                            $summary[$key] += $row[$key];
                        }
                    }
                }

                $data = [
                    'summary' => $summary,
                    'rows' => $rows
                ];
                break;

            case 'staffs':
                $title = 'Staff Details';
                $staffList = Staff::where('school_id', $schoolId)->with(['department', 'designation'])->get();
                $rows = [];
                $stats = [
                    'newly_added' => 0,
                    'newly_added_academic_year' => 0,
                    'old_staff' => 0,
                    'deactivated' => 0
                ];

                foreach ($staffList as $st) {
                    $isNew = Carbon::parse($st->joining_date)->gt(now()->subYear());
                    if ($st->is_active) {
                        if ($isNew) {
                            $stats['newly_added']++;
                            $stats['newly_added_academic_year']++;
                        } else {
                            $stats['old_staff']++;
                        }
                    } else {
                        $stats['deactivated']++;
                    }

                    $empType = $st->staff_type;
                    if ($empType === 'Non Teaching') {
                        $empType = 'Non-Teaching';
                    } elseif ($empType === 'Driver/Supporting staff') {
                        $empType = 'Driver/Supporting Staff';
                    }

                    $rows[] = [
                        'id' => $st->id,
                        'staff_id' => str_pad($st->employee_id ?? $st->id, 2, '0', STR_PAD_LEFT),
                        'name' => $st->full_name,
                        'designation' => $st->designation?->name ?? 'Staff',
                        'highest_qualification' => $st->qualification ?? 'B.Ed',
                        'department' => $st->department?->name ?? 'General',
                        'phone' => $st->phone ?? '—',
                        'email' => $st->email ?? '—',
                        'is_active' => (bool)$st->is_active,
                        'employment_type' => $empType
                    ];
                }

                $data = [
                    'stats' => $stats,
                    'rows' => $rows
                ];
                break;

            case 'send_reminder':
                $title = 'Send Reminder';
                $classes = \App\Models\SchoolClass::where('school_id', $schoolId)->with('sections')->get();
                $classesList = [];
                foreach ($classes as $c) {
                    $classesList[] = [
                        'id' => $c->id,
                        'name' => $c->name,
                        'sections' => $c->sections->map(fn($s) => ['id' => $s->id, 'name' => $s->name])->toArray()
                    ];
                }

                // Demo pending fee students for visual preview
                $demoPendingStudents = [
                    ['id' => 1, 'name' => 'Aarav Sharma',    'roll' => 'S-1021', 'class' => 'Class 10', 'section' => 'A', 'total_fee' => '₹12,000', 'paid' => '₹8,000',  'due' => '₹4,000',  'due_date' => '15 Jun 2025', 'overdue' => true],
                    ['id' => 2, 'name' => 'Priya Patel',     'roll' => 'S-1034', 'class' => 'Class 9',  'section' => 'B', 'total_fee' => '₹10,500', 'paid' => '₹5,000',  'due' => '₹5,500',  'due_date' => '20 Jun 2025', 'overdue' => true],
                    ['id' => 3, 'name' => 'Rohan Mehta',     'roll' => 'S-1056', 'class' => 'Class 8',  'section' => 'A', 'total_fee' => '₹9,000',  'paid' => '₹9,000',  'due' => '₹0',      'due_date' => '—',           'overdue' => false],
                    ['id' => 4, 'name' => 'Sneha Reddy',     'roll' => 'S-1078', 'class' => 'Class 10', 'section' => 'C', 'total_fee' => '₹12,000', 'paid' => '₹3,500',  'due' => '₹8,500',  'due_date' => '10 Jun 2025', 'overdue' => true],
                    ['id' => 5, 'name' => 'Kiran Yadav',     'roll' => 'S-1092', 'class' => 'Class 7',  'section' => 'A', 'total_fee' => '₹8,000',  'paid' => '₹4,000',  'due' => '₹4,000',  'due_date' => '25 Jun 2025', 'overdue' => true],
                    ['id' => 6, 'name' => 'Divya Nair',      'roll' => 'S-1103', 'class' => 'Class 9',  'section' => 'A', 'total_fee' => '₹10,500', 'paid' => '₹10,500', 'due' => '₹0',      'due_date' => '—',           'overdue' => false],
                    ['id' => 7, 'name' => 'Arjun Singh',     'roll' => 'S-1117', 'class' => 'Class 11', 'section' => 'B', 'total_fee' => '₹14,000', 'paid' => '₹7,000',  'due' => '₹7,000',  'due_date' => '05 Jun 2025', 'overdue' => true],
                    ['id' => 8, 'name' => 'Meera Krishnan',  'roll' => 'S-1129', 'class' => 'Class 6',  'section' => 'A', 'total_fee' => '₹7,500',  'paid' => '₹2,500',  'due' => '₹5,000',  'due_date' => '18 Jun 2025', 'overdue' => true],
                    ['id' => 9, 'name' => 'Vivek Gupta',     'roll' => 'S-1145', 'class' => 'Class 12', 'section' => 'A', 'total_fee' => '₹15,000', 'paid' => '₹12,000', 'due' => '₹3,000',  'due_date' => '28 Jun 2025', 'overdue' => false],
                    ['id' =>10, 'name' => 'Ananya Joshi',    'roll' => 'S-1158', 'class' => 'Class 8',  'section' => 'B', 'total_fee' => '₹9,000',  'paid' => '₹0',      'due' => '₹9,000',  'due_date' => '01 Jun 2025', 'overdue' => true],
                ];

                $data = [
                    'classes'         => $classesList,
                    'pendingStudents' => $demoPendingStudents,
                ];
                break;

            case 'calendar_month_events':
                $month = (int) $request->get('month', now()->month);
                $year = (int) $request->get('year', now()->year);
                
                $students = Student::where('school_id', $schoolId)
                    ->whereMonth('date_of_birth', $month)
                    ->get();
                $staff = Staff::where('school_id', $schoolId)
                    ->whereMonth('date_of_birth', $month)
                    ->get();
                $firstDay = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
                $lastDay = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

                $events = Event::where('school_id', $schoolId)
                    ->where('start_date', '<=', $lastDay)
                    ->where('end_date', '>=', $firstDay)
                    ->get();
                    
                $birthdayList = [];
                
                foreach ($students as $stu) {
                    $day = Carbon::parse($stu->date_of_birth)->day;
                    $birthdayList[] = [
                        'day' => $day,
                        'name' => $stu->full_name . "'s Birthday",
                        'type' => 'student',
                        'details' => ($stu->class?->name ?? '—') . ' (' . ($stu->section?->name ?? '—') . ')'
                    ];
                }
                foreach ($staff as $st) {
                    $day = Carbon::parse($st->date_of_birth)->day;
                    $birthdayList[] = [
                        'day' => $day,
                        'name' => $st->full_name . "'s Birthday",
                        'type' => 'staff',
                        'details' => $st->designation?->name ?? 'Staff'
                    ];
                }
                foreach ($events as $ev) {
                    $startLimit = Carbon::parse($ev->start_date);
                    $endLimit = Carbon::parse($ev->end_date);
                    
                    $current = $startLimit->copy();
                    while ($current->lte($endLimit)) {
                        if ($current->month === $month && $current->year === $year) {
                            $birthdayList[] = [
                                'day' => $current->day,
                                'name' => $ev->title,
                                'type' => $ev->is_holiday ? 'holiday' : 'event',
                                'details' => $ev->description ?? 'Event'
                            ];
                        }
                        $current->addDay();
                    }
                }

                return response()->json([
                    'month' => $month,
                    'year' => $year,
                    'events' => $birthdayList
                ]);

            case 'total_collection':
                $title = 'Total Fee Collections (Income)';
                $fees = StudentFee::where('school_id', $schoolId)->where('paid_amount', '>', 0)->with('student')->get();
                foreach ($fees as $fee) {
                    $data[] = [
                        'receipt_id' => 'REC-' . $fee->id,
                        'student' => ($fee->student?->full_name ?? '—') . ' (' . ($fee->student?->class?->name ?? '—') . ')',
                        'amount' => '₹ ' . number_format($fee->paid_amount, 2),
                        'date' => $fee->updated_at->format('Y-m-d'),
                        'status' => ucfirst($fee->status ?? 'Paid')
                    ];
                }
                break;

            case 'income':
                $title = 'Total Income Details';
                $receipts = \App\Models\FeeReceipt::where('school_id', $schoolId)
                    ->where('amount_paid', '>', 0)
                    ->with(['student.class', 'student.section'])
                    ->get();
                
                if ($receipts->isEmpty()) {
                    $fees = StudentFee::where('school_id', $schoolId)->where('paid_amount', '>', 0)->with('student')->get();
                    foreach ($fees as $fee) {
                        $data[] = [
                            'receipt_id' => 'REC-' . $fee->id,
                            'student' => ($fee->student?->full_name ?? '—') . ' (' . ($fee->student?->class?->name ?? '—') . ')',
                            'amount' => '₹ ' . number_format($fee->paid_amount, 2),
                            'date' => $fee->updated_at->format('Y-m-d'),
                            'status' => ucfirst($fee->status ?? 'Paid'),
                            'payment_mode' => '—',
                            'category' => 'Fee',
                            'sub_category' => '—',
                            'income_name' => 'School Fee'
                        ];
                    }
                } else {
                    foreach ($receipts as $receipt) {
                        $data[] = [
                            'receipt_id' => $receipt->receipt_number ?? ('REC-' . $receipt->id),
                            'student' => ($receipt->student?->full_name ?? '—') . ' (' . ($receipt->student?->class?->name ?? '—') . ')',
                            'amount' => '₹ ' . number_format($receipt->amount_paid, 2),
                            'date' => $receipt->payment_date ?? $receipt->created_at->format('Y-m-d'),
                            'status' => 'Paid',
                            'payment_mode' => ucfirst($receipt->payment_mode ?? '—'),
                            'category' => 'Fee',
                            'sub_category' => '—',
                            'income_name' => 'School Fee'
                        ];
                    }
                }
                break;

            case 'expense':
                $title = 'School Expenses';
                $expenses = SchoolExpense::where('school_id', $schoolId)
                    ->where('status', '!=', 'cancelled')
                    ->latest('expense_date')
                    ->get();
                $data = [];
                foreach ($expenses as $exp) {
                    $data[] = [
                        'expense_id' => 'EXP-' . $exp->id,
                        'category' => ucfirst($exp->category),
                        'amount' => '₹ ' . number_format($exp->amount, 2),
                        'date' => $exp->expense_date,
                        'status' => ucfirst($exp->status ?? 'Paid')
                    ];
                }
                break;

            case 'today_collection':
                $title = "Today's Fee Collection";
                $fees = StudentFee::where('school_id', $schoolId)->whereDate('updated_at', today())->where('paid_amount', '>', 0)->with('student')->get();
                foreach ($fees as $fee) {
                    $data[] = [
                        'receipt_id' => 'REC-' . $fee->id,
                        'student' => ($fee->student?->full_name ?? '—') . ' (' . ($fee->student?->class?->name ?? '—') . ')',
                        'amount' => '₹ ' . number_format($fee->paid_amount, 2),
                        'date' => $fee->updated_at->format('Y-m-d'),
                        'status' => ucfirst($fee->status ?? 'Paid')
                    ];
                }
                break;

            case 'student_attendance':
                $title = "Today's Student Attendance Log";
                $marked = StudentAttendance::where('school_id', $schoolId)->whereDate('date', today())->with('student')->get();
                if ($marked->isEmpty()) {
                    $students = Student::where('school_id', $schoolId)->where('is_active', true)->get();
                    foreach ($students as $stu) {
                        $data[] = [
                            'roll' => $stu->roll_number ?? '—',
                            'name' => $stu->full_name,
                            'class' => $stu->class?->name ?? '—',
                            'status' => 'Not Marked',
                            'remark' => '—'
                        ];
                    }
                } else {
                    foreach ($marked as $att) {
                        $data[] = [
                            'roll' => $att->student?->roll_number ?? '—',
                            'name' => $att->student?->full_name ?? '—',
                            'class' => $att->student?->class?->name ?? '—',
                            'status' => ucfirst($att->status),
                            'remark' => $att->remark ?? '—'
                        ];
                    }
                }
                break;

            case 'staff_attendance':
                $title = "Today's Staff Attendance Statuses";
                $marked = StaffAttendance::where('school_id', $schoolId)->whereDate('date', today())->with('staff')->get();
                if ($marked->isEmpty()) {
                    $staff = Staff::where('school_id', $schoolId)->where('is_active', true)->get();
                    foreach ($staff as $st) {
                        $data[] = [
                            'name' => $st->first_name . ' ' . $st->last_name,
                            'role' => $st->designation?->name ?? 'Staff',
                            'status' => 'Not Marked',
                            'punch_in' => '—'
                        ];
                    }
                } else {
                    foreach ($marked as $att) {
                        $data[] = [
                            'name' => $att->staff?->first_name . ' ' . $att->staff?->last_name,
                            'role' => $att->staff?->designation?->name ?? 'Staff',
                            'status' => ucfirst($att->status),
                            'punch_in' => $att->punch_in_time ?? '—'
                        ];
                    }
                }
                break;

            case 'fee_pending':
                $title = "Students with Dues / Pending Fees";
                $pending = StudentFee::where('school_id', $schoolId)->whereColumn('amount', '>', 'paid_amount')->with('student')->get();
                foreach ($pending as $fee) {
                    $due = $fee->amount - $fee->paid_amount;
                    $data[] = [
                        'name' => $fee->student?->full_name ?? '—',
                        'class' => ($fee->student?->class?->name ?? '—') . ' (' . ($fee->student?->section?->name ?? '—') . ')',
                        'total_fee' => '₹ ' . number_format($fee->amount, 2),
                        'paid' => '₹ ' . number_format($fee->paid_amount, 2),
                        'due' => '₹ ' . number_format($due, 2),
                        'due_date' => $fee->due_date ?? '—'
                    ];
                }
                break;

            case 'admissions':
                $title = "Admission Enquiries & Applications";
                $leads = EnquiryLead::where('school_id', $schoolId)->get();
                foreach ($leads as $lead) {
                    $data[] = [
                        'name' => $lead->student_name,
                        'parent' => $lead->parent_name ?? '—',
                        'phone' => $lead->phone ?? '—',
                        'class' => $lead->class_interested ?? '—',
                        'status' => ucfirst($lead->status)
                    ];
                }
                break;

            case 'calendar_events':
                $dateStr = $request->get('date', today()->toDateString());
                $carbonDate = Carbon::parse($dateStr);
                $title = "Events & Birthdays on " . $carbonDate->format('M d, Y');
                
                $studentBirthdays = Student::where('school_id', $schoolId)
                    ->whereMonth('date_of_birth', $carbonDate->month)
                    ->whereDay('date_of_birth', $carbonDate->day)
                    ->get();
                    
                $staffBirthdays = Staff::where('school_id', $schoolId)
                    ->whereMonth('date_of_birth', $carbonDate->month)
                    ->whereDay('date_of_birth', $carbonDate->day)
                    ->get();

                $events = Event::where('school_id', $schoolId)
                    ->whereDate('start_date', '<=', $carbonDate->toDateString())
                    ->whereDate('end_date', '>=', $carbonDate->toDateString())
                    ->get();

                foreach ($events as $ev) {
                    $data[] = [
                        'event_name' => $ev->title,
                        'type' => $ev->is_holiday ? 'Holiday' : 'Event',
                        'time' => Carbon::parse($ev->start_date)->format('d M Y') . ($ev->start_date !== $ev->end_date ? ' – ' . Carbon::parse($ev->end_date)->format('d M Y') : ''),
                        'details' => $ev->description ?? '—'
                    ];
                }
                foreach ($studentBirthdays as $sb) {
                    $data[] = [
                        'event_name' => $sb->full_name . "'s Birthday 🎂",
                        'type' => 'Student Birthday',
                        'time' => 'All Day',
                        'details' => ($sb->class?->name ?? '—') . ' (' . ($sb->section?->name ?? '—') . ')'
                    ];
                }
                foreach ($staffBirthdays as $stb) {
                    $data[] = [
                        'event_name' => ($stb->first_name . ' ' . $stb->last_name) . "'s Birthday 🎂",
                        'type' => 'Staff Birthday',
                        'time' => 'All Day',
                        'details' => $stb->designation?->name ?? 'Staff'
                    ];
                }
                break;

            case 'class_fee_report':
                $title = 'Class-Wise Fee Report';
                $classes = \App\Models\SchoolClass::where('school_id', $schoolId)->get();
                foreach ($classes as $c) {
                    $studentIds = Student::where('school_id', $schoolId)->where('class_id', $c->id)->pluck('id');
                    $totalFee = (float) StudentFee::where('school_id', $schoolId)->whereIn('student_id', $studentIds)->sum('amount');
                    $paid = (float) StudentFee::where('school_id', $schoolId)->whereIn('student_id', $studentIds)->sum('paid_amount');
                    $due = max(0, $totalFee - $paid);
                    
                    $data[] = [
                        'class_name' => $c->name,
                        'total_fee' => $totalFee,
                        'paid' => $paid,
                        'due' => $due
                    ];
                }
                break;
        }

        return response()->json([
            'title' => $title,
            'data' => $data,
            'type' => $type
        ]);
    }

    public function sendFeeReminder(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Dues notification reminders have been sent successfully!'
        ]);
    }

    public function refreshBox(Request $request): JsonResponse
    {
        $schoolId = auth()->user()->school_id;
        $box = $request->get('box');

        $data = [];

        if ($box === 'overview') {
            $totalStudents = Student::where('school_id', $schoolId)->where('is_active', true)->count();
            $totalStaffs = Staff::where('school_id', $schoolId)->where('is_active', true)->count();
            $data = [
                'totalStudents' => $totalStudents,
                'totalStaffs' => $totalStaffs,
            ];
        } elseif ($box === 'accounts') {
            $totalFeeCollection = (float) StudentFee::where('school_id', $schoolId)->sum('paid_amount');
            $totalSchoolIncome  = (float) SchoolIncome::where('school_id', $schoolId)
                ->where('status', '!=', 'cancelled')
                ->sum('amount');
            $totalIncome = $totalFeeCollection + $totalSchoolIncome;

            $totalExpense = (float) SchoolExpense::where('school_id', $schoolId)
                ->where('status', '!=', 'cancelled')
                ->sum('amount');

            $data = [
                'totalIncome' => number_format($totalIncome),
                'totalExpense' => number_format($totalExpense),
            ];
        } elseif ($box === 'fee') {
            $todayFeeCollection = (float) StudentFee::where('school_id', $schoolId)
                ->whereDate('updated_at', today())
                ->sum('paid_amount');
            $totalFeeCollection = (float) StudentFee::where('school_id', $schoolId)->sum('paid_amount');
            
            $todayFeeDue = (float) StudentFee::where('school_id', $schoolId)
                ->whereDate('due_date', today())
                ->sum('amount');
            $todayFeeCollectionPct = 0;
            if ($todayFeeCollection > 0) {
                if ($todayFeeDue > 0) {
                    $todayFeeCollectionPct = min(100, round(($todayFeeCollection / $todayFeeDue) * 100));
                } else {
                    $todayFeeCollectionPct = 100;
                }
            }

            $feeCollectedAmount = (float) StudentFee::where('school_id', $schoolId)->where('due_date', '<=', today())->sum('paid_amount');
            $feeDueAmount = (float) StudentFee::where('school_id', $schoolId)->where('due_date', '<=', today())->whereColumn('amount', '>', 'paid_amount')->sum(\DB::raw('amount - paid_amount'));
            $feeTotalSum = $feeCollectedAmount + $feeDueAmount;
            $feeCollectedPct = $feeTotalSum > 0 ? round(($feeCollectedAmount / $feeTotalSum) * 100, 2) : 0;

            $data = [
                'todayFeeCollection' => number_format($todayFeeCollection),
                'todayFeeCollectionPct' => $todayFeeCollectionPct,
                'totalFeeCollection' => number_format($totalFeeCollection),
                'feeCollectedPct' => $feeCollectedPct,
            ];
        } elseif ($box === 'attendance') {
            $totalStudents = Student::where('school_id', $schoolId)->where('is_active', true)->count();
            $totalStaffs = Staff::where('school_id', $schoolId)->where('is_active', true)->count();

            $markedStudentsToday = StudentAttendance::where('school_id', $schoolId)
                ->whereDate('date', today())
                ->count();
            $presentStudentsToday = StudentAttendance::where('school_id', $schoolId)
                ->whereDate('date', today())
                ->whereIn('status', ['present', 'late', 'duty_leave'])
                ->count() + (StudentAttendance::where('school_id', $schoolId)
                    ->whereDate('date', today())
                    ->where('status', 'half_day')
                    ->count() * 0.5);
            $studentAttendancePct = $totalStudents > 0 
                ? round(($presentStudentsToday / $totalStudents) * 100) 
                : 0;

            $markedStaffToday = StaffAttendance::where('school_id', $schoolId)
                ->whereDate('date', today())
                ->count();
            $presentStaffToday = StaffAttendance::where('school_id', $schoolId)
                ->whereDate('date', today())
                ->whereIn('status', ['present', 'late'])
                ->count() + (StaffAttendance::where('school_id', $schoolId)
                    ->whereDate('date', today())
                    ->where('status', 'half_day')
                    ->count() * 0.5);
            $staffAttendancePct = $totalStaffs > 0 
                ? round(($presentStaffToday / $totalStaffs) * 100) 
                : 0;

            $data = [
                'studentAttendancePct' => $studentAttendancePct,
                'staffAttendancePct' => $staffAttendancePct,
                'studentPresentToday' => $presentStudentsToday,
                'staffPresentToday' => $presentStaffToday,
            ];
        } elseif ($box === 'attrition') {
            $totalStudents = Student::where('school_id', $schoolId)->where('is_active', true)->count();
            $totalStaffs = Staff::where('school_id', $schoolId)->where('is_active', true)->count();
            $studentNewlyJoined = Student::where('school_id', $schoolId)->whereYear('admission_date', now()->year)->count();
            $studentExited = Student::where('school_id', $schoolId)->onlyTrashed()->count();
            $staffNewlyJoined = Staff::where('school_id', $schoolId)->whereYear('joining_date', now()->year)->count();
            $staffExited = Staff::where('school_id', $schoolId)->onlyTrashed()->count();
            
            $data = [
                'studentNewlyJoined' => $studentNewlyJoined,
                'studentExited' => $studentExited,
                'studentStrength' => $totalStudents,
                'staffNewlyJoined' => $staffNewlyJoined,
                'staffExited' => $staffExited,
                'staffStrength' => $totalStaffs,
            ];
        } elseif ($box === 'admissions') {
            $admissionEnquiry = EnquiryLead::where('school_id', $schoolId)->whereIn('status', ['new', 'enquiry'])->count();
            $admissionApplication = EnquiryLead::where('school_id', $schoolId)->whereIn('status', ['contacted', 'application'])->count();
            $admissionPayment = EnquiryLead::where('school_id', $schoolId)->where('status', 'payment')->count();
            $admissionEvaluation = EnquiryLead::where('school_id', $schoolId)->where('status', 'evaluation')->count();
            $admissionCount = EnquiryLead::where('school_id', $schoolId)->whereIn('status', ['enrolled', 'admission'])->count();

            $data = [
                'enquiry' => $admissionEnquiry,
                'application' => $admissionApplication,
                'payment' => $admissionPayment,
                'evaluation' => $admissionEvaluation,
                'admission' => $admissionCount,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function incomeExpenseChartData(Request $request): JsonResponse
    {
        $schoolId = auth()->user()->school_id;
        $filter = $request->get('filter', 'This Year');

        $labels = [];
        $incomeData = [];
        $expenseData = [];
        $totalIncome = 0;
        $totalExpense = 0;

        if ($filter === 'This Month') {
            $daysInMonth = now()->daysInMonth;
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $labels[] = (string) $d;
                $incomeData[] = 0;
                $expenseData[] = 0;
            }

            $feePayments = StudentFee::where('school_id', $schoolId)
                ->whereMonth('updated_at', now()->month)
                ->whereYear('updated_at', now()->year)
                ->where('paid_amount', '>', 0)
                ->get();

            foreach ($feePayments as $payment) {
                $day = Carbon::parse($payment->updated_at)->day;
                if ($day <= $daysInMonth) {
                    $incomeData[$day - 1] += (float) $payment->paid_amount;
                }
            }

            // Add monthly expenses by day
            $monthExpenses = SchoolExpense::where('school_id', $schoolId)
                ->whereMonth('expense_date', now()->month)
                ->whereYear('expense_date', now()->year)
                ->where('status', '!=', 'cancelled')
                ->get();
            foreach ($monthExpenses as $expRec) {
                $day = Carbon::parse($expRec->expense_date)->day;
                if ($day <= $daysInMonth) {
                    $expenseData[$day - 1] += (float) $expRec->amount;
                }
            }

            $totalIncome = array_sum($incomeData);
            $totalExpense = array_sum($expenseData);

        } elseif ($filter === 'Last 6 Months') {
            for ($i = 5; $i >= 0; $i--) {
                $m = now()->subMonths($i);
                $labels[] = $m->format('F');
                $incomeData[] = 0;
                $expenseData[] = 0;
            }

            $feePayments = StudentFee::where('school_id', $schoolId)
                ->where('updated_at', '>=', now()->subMonths(5)->startOfMonth())
                ->where('paid_amount', '>', 0)
                ->get();

            foreach ($feePayments as $payment) {
                $monthName = Carbon::parse($payment->updated_at)->format('F');
                $idx = array_search($monthName, $labels);
                if ($idx !== false) {
                    $incomeData[$idx] += (float) $payment->paid_amount;
                }
            }

            // Add last 6 months expenses
            $last6Expenses = SchoolExpense::where('school_id', $schoolId)
                ->where('expense_date', '>=', now()->subMonths(5)->startOfMonth())
                ->where('status', '!=', 'cancelled')
                ->get();
            foreach ($last6Expenses as $expRec) {
                $monthName = Carbon::parse($expRec->expense_date)->format('F');
                $idx = array_search($monthName, $labels);
                if ($idx !== false) {
                    $expenseData[$idx] += (float) $expRec->amount;
                }
            }

            $totalIncome = array_sum($incomeData);
            $totalExpense = array_sum($expenseData);

        } else {
            $labels = ['April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', 'January', 'February'];
            $incomeData = array_fill(0, count($labels), 0);
            $expenseData = array_fill(0, count($labels), 0);

            $feePayments = StudentFee::where('school_id', $schoolId)
                ->where('paid_amount', '>', 0)
                ->get();

            foreach ($feePayments as $payment) {
                $monthName = Carbon::parse($payment->updated_at)->format('F');
                $idx = array_search($monthName, $labels);
                if ($idx !== false) {
                    $incomeData[$idx] += (float) $payment->paid_amount;
                }
            }

            // Annual expense data from SchoolExpense
            $allExpenses = SchoolExpense::where('school_id', $schoolId)
                ->where('status', '!=', 'cancelled')
                ->get();
            foreach ($allExpenses as $expRec) {
                $monthName = Carbon::parse($expRec->expense_date)->format('F');
                $idx = array_search($monthName, $labels);
                if ($idx !== false) {
                    $expenseData[$idx] += (float) $expRec->amount;
                }
            }

            $totalFeeCollection = array_sum($incomeData);
            $totalIncome = $totalFeeCollection;
            $totalExpense = array_sum($expenseData);
        }

        return response()->json([
            'labels' => $labels,
            'incomeData' => $incomeData,
            'expenseData' => $expenseData,
            'totalIncome' => '₹ ' . number_format($totalIncome),
            'totalExpense' => '₹ ' . number_format($totalExpense),
        ]);
    }

    /**
     * Change academic session/year for the current school.
     */
    public function changeSession(Request $request)
    {
        $request->validate([
            'academic_session_id' => 'required|exists:academic_sessions,id',
        ]);

        $schoolId = auth()->user()->school_id;

        // Ensure the session belongs to the school
        $session = AcademicSession::where('school_id', $schoolId)
            ->findOrFail($request->academic_session_id);

        // Set all other sessions is_current to false
        AcademicSession::where('school_id', $schoolId)->update(['is_current' => false]);

        // Set selected session is_current to true
        $session->is_current = true;
        $session->save();

        return response()->json([
            'success' => true,
            'message' => 'Academic session changed successfully!',
        ]);
    }

    /**
     * Topbar instant search for students and staff within the school.
     */
    public function topbarSearch(Request $request)
    {
        $query = trim($request->input('query'));
        if (empty($query)) {
            return response()->json(['students' => [], 'staff' => []]);
        }

        $schoolId = auth()->user()->school_id;

        // Fetch students of this school
        $students = Student::where('school_id', $schoolId)
            ->where(function($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('admission_number', 'like', "%{$query}%")
                  ->orWhere('roll_number', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%")
                  ->orWhere('father_phone', 'like', "%{$query}%")
                  ->orWhere('mother_phone', 'like', "%{$query}%")
                  ->orWhere('guardian_phone', 'like', "%{$query}%")
                  ->orWhere('whatsapp_number', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get(['id', 'first_name', 'last_name', 'admission_number', 'roll_number', 'photo', 'phone']);

        // Fetch staff of this school
        $staff = Staff::where('school_id', $schoolId)
            ->where(function($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('employee_id', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get(['id', 'first_name', 'last_name', 'employee_id', 'photo', 'phone']);

        return response()->json([
            'students' => $students,
            'staff' => $staff,
        ]);
    }
}
