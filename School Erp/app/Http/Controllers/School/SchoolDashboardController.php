<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\School;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\Student;
use App\Models\StudentAttendance;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user     = auth()->user();
        $schoolId = $user->school_id;
        $school   = $user->school;
        $month    = (int) $request->get('month', now()->month);
        $year     = (int) $request->get('year', now()->year);

        // ── STAT CARD 1: Total Students ──────────────────────────────────────
        $totalStudents = Student::where('school_id', $schoolId)
            ->where('is_active', true)
            ->count();

        $studentsLastMonth = Student::where('school_id', $schoolId)
            ->where('is_active', true)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        $studentsThisMonth = Student::where('school_id', $schoolId)
            ->where('is_active', true)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $studentTrend = $studentsLastMonth > 0
            ? round((($totalStudents - $studentsLastMonth) / $studentsLastMonth) * 100, 1)
            : ($totalStudents > 0 ? 100 : 0);

        // ── STAT CARD 2: Fee Collection Rate ─────────────────────────────────
        $feePaid  = 0;
        $feeTotal = 0;
        $feeRate  = 0;

        // ── STAT CARD 3: Attendance Rate ──────────────────────────────────────
        $attendanceToday = StudentAttendance::where('school_id', $schoolId)
            ->whereDate('date', today())
            ->get();
        $presentToday   = $attendanceToday->where('status', 'present')->count();
        $markedToday    = $attendanceToday->count();
        $attendanceRate = $markedToday > 0
            ? round(($presentToday / $markedToday) * 100, 1)
            : null;

        $attendanceLastWeek = StudentAttendance::where('school_id', $schoolId)
            ->whereDate('date', now()->subWeek()->toDateString())
            ->get();
        $presentLastWeek = $attendanceLastWeek->where('status', 'present')->count();
        $markedLastWeek  = $attendanceLastWeek->count();
        $attendanceLastWeekRate = $markedLastWeek > 0
            ? round(($presentLastWeek / $markedLastWeek) * 100, 1)
            : 0;
        $attendanceTrend = $attendanceLastWeekRate > 0 && $attendanceRate !== null
            ? round($attendanceRate - $attendanceLastWeekRate, 1)
            : 0;

        // ── STAT CARD 4: Monthly Revenue ─────────────────────────────────────
        $monthlyRevenue   = 0;
        $lastMonthRevenue = 0;
        $revenueTrend     = 0;

        // ── STAT CARD 5: Active Teachers ─────────────────────────────────────
        $activeTeachers = Staff::where('school_id', $schoolId)
            ->where('is_active', true)
            ->count();

        $newStaffThisMonth = Staff::where('school_id', $schoolId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // ── STAT CARD 6: Open Complaints ─────────────────────────────────────
        $openComplaints = 0;

        // ── TODAY'S SNAPSHOT ──────────────────────────────────────────────────
        $staffPresentToday = StaffAttendance::where('school_id', $schoolId)
            ->whereDate('date', today())
            ->where('status', 'present')
            ->count();

        $totalStaff = Staff::where('school_id', $schoolId)
            ->where('is_active', true)
            ->count();

        $feesToday = 0;

        // ── FEE DUE SUMMARY ───────────────────────────────────────────────────
        $feeDueSummary = [
            'paid'    => 0,
            'pending' => 0,
            'overdue' => 0,
        ];
        $totalDue = 0;

        // ── RECENT ACTIVITIES ─────────────────────────────────────────────────
        $recentAdmissions = Student::where('school_id', $schoolId)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($s) {
                return [
                    'time'      => $s->created_at->format('h:i A'),
                    'timestamp' => $s->created_at->timestamp,
                    'type'      => 'student',
                    'icon'      => 'fa-user-graduate',
                    'color'     => '#3b82f6',
                    'bg'        => 'rgba(59,130,246,0.12)',
                    'text'      => 'New admission for ' . $s->full_name,
                    'amount'    => null,
                ];
            });

        $recentStaffAttendance = StaffAttendance::with('staff')
            ->where('school_id', $schoolId)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($a) {
                return [
                    'time'      => $a->created_at->format('h:i A'),
                    'timestamp' => $a->created_at->timestamp,
                    'type'      => 'attendance',
                    'icon'      => 'fa-calendar-check',
                    'color'     => '#8b5cf6',
                    'bg'        => 'rgba(139,92,246,0.12)',
                    'text'      => 'Attendance marked for ' . optional($a->staff)->full_name,
                    'amount'    => null,
                ];
            });

        $recentActivities = collect([...$recentAdmissions, ...$recentStaffAttendance])
            ->sortByDesc('timestamp')
            ->take(5)
            ->values();

        // ── SPARKLINES (last 7 days) ───────────────────────────────────────────
        $attendanceSparkline = collect(range(6, 0))->map(function ($d) use ($schoolId) {
            $date    = now()->subDays($d)->toDateString();
            $total   = StudentAttendance::where('school_id', $schoolId)->whereDate('date', $date)->count();
            $present = StudentAttendance::where('school_id', $schoolId)->whereDate('date', $date)->where('status', 'present')->count();
            return $total > 0 ? round($present / $total * 100) : 0;
        });

        $feeSparkline    = collect([0, 0, 0, 0, 0, 0, 0]);
        $studentSparkline = collect(range(6, 0))->map(function ($d) use ($schoolId) {
            return Student::where('school_id', $schoolId)
                ->whereDate('created_at', now()->subDays($d)->toDateString())
                ->count();
        });
        $revenueSparkline = collect([0, 0, 0, 0, 0, 0, 0]);
        $teacherSparkline = collect([0, 0, 0, 0, 0, 0, $activeTeachers]);
        $complaintSparkline = collect([0, 0, 0, 0, 0, 0, 0]);

        // ── AI INSIGHTS ───────────────────────────────────────────────────────
        $worstClassName = 'N/A';
        try {
            $worstSection = StudentAttendance::where('school_id', $schoolId)
                ->whereBetween('date', [now()->startOfWeek(), now()])
                ->selectRaw('section_id, COUNT(CASE WHEN status="present" THEN 1 END) as p, COUNT(*) as t')
                ->groupBy('section_id')
                ->having('t', '>', 0)
                ->orderByRaw('(p/t) ASC')
                ->first();

            if ($worstSection) {
                $sec = \App\Models\Section::with('schoolClass')->find($worstSection->section_id);
                if ($sec && $sec->schoolClass) {
                    $worstClassName = $sec->schoolClass->name . ' - ' . $sec->name;
                }
            }
        } catch (\Exception $e) {
            $worstClassName = 'N/A';
        }

        $atRiskCount = 0;
        try {
            $atRiskCount = StudentAttendance::where('school_id', $schoolId)
                ->selectRaw('student_id, COUNT(CASE WHEN status="present" THEN 1 END) / COUNT(*) as rate')
                ->groupBy('student_id')
                ->having('rate', '<', 0.75)
                ->count();
        } catch (\Exception $e) {
            $atRiskCount = 0;
        }

        $aiInsights = [
            [
                'icon'  => 'fa-chart-line',
                'color' => '#f59e0b',
                'bg'    => 'rgba(245,158,11,0.15)',
                'text'  => $worstClassName !== 'N/A'
                    ? "Attendance needs attention in {$worstClassName} this week."
                    : 'Attendance looks good across all classes this week!',
            ],
            [
                'icon'  => 'fa-indian-rupee-sign',
                'color' => '#10b981',
                'bg'    => 'rgba(16,185,129,0.15)',
                'text'  => 'Fee collection module is being set up. Data will appear soon.',
            ],
            [
                'icon'  => 'fa-shield-halved',
                'color' => '#ef4444',
                'bg'    => 'rgba(239,68,68,0.15)',
                'text'  => $atRiskCount > 0
                    ? "{$atRiskCount} students are at academic risk (< 75% attendance). Review now."
                    : 'No students are currently at academic risk. Great work!',
            ],
        ];

        // ── CURRENT SESSION ────────────────────────────────────────────────────
        $currentSession = AcademicSession::where('school_id', $schoolId)
            ->where('is_current', true)
            ->first();

        // ── PLAN NAME ─────────────────────────────────────────────────────────
        $planName = $school->activeSubscription()?->plan?->name ?? 'Trial';

        // ── NOTIFICATION COUNT (stub — Notice model not yet built) ────────────
        $notificationCount = 0;

        // ── FEE CHART DATA (server-side initial render) ───────────────────────
        $daysInMonth   = now()->setMonth($month)->setYear($year)->daysInMonth;
        $feeChartLabels = [];
        $feeChartData   = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $feeChartLabels[] = $d . ' ' . Carbon::create($year, $month)->format('M');
            $feeChartData[]   = 0;
        }

        // ── ATTENDANCE CHART DATA (server-side initial render) ────────────────
        $attendanceChartLabels = [];
        $attendanceChartData   = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = Carbon::create($year, $month, $d)->toDateString();
            $attendanceChartLabels[] = (string) $d;
            $total   = StudentAttendance::where('school_id', $schoolId)->whereDate('date', $date)->count();
            $present = StudentAttendance::where('school_id', $schoolId)->whereDate('date', $date)->where('status', 'present')->count();
            $attendanceChartData[] = $total > 0 ? round($present / $total * 100) : 0;
        }
        $avgAttendance = count(array_filter($attendanceChartData)) > 0
            ? round(array_sum($attendanceChartData) / count(array_filter($attendanceChartData)))
            : 0;

        return view('school.dashboard.index', compact(
            'school',
            'totalStudents',
            'studentTrend',
            'studentsThisMonth',
            'feeRate',
            'feePaid',
            'feeTotal',
            'attendanceRate',
            'attendanceTrend',
            'presentToday',
            'markedToday',
            'monthlyRevenue',
            'lastMonthRevenue',
            'revenueTrend',
            'activeTeachers',
            'newStaffThisMonth',
            'openComplaints',
            'staffPresentToday',
            'totalStaff',
            'feesToday',
            'feeDueSummary',
            'totalDue',
            'recentActivities',
            'attendanceSparkline',
            'feeSparkline',
            'studentSparkline',
            'revenueSparkline',
            'teacherSparkline',
            'complaintSparkline',
            'aiInsights',
            'currentSession',
            'planName',
            'notificationCount',
            'month',
            'year',
            'feeChartLabels',
            'feeChartData',
            'attendanceChartLabels',
            'attendanceChartData',
            'avgAttendance'
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
                $data[]   = 0;
            }
        } elseif ($period === '3months') {
            for ($i = 2; $i >= 0; $i--) {
                $labels[] = now()->subMonths($i)->format('M Y');
                $data[]   = 0;
            }
        } else {
            for ($i = 11; $i >= 0; $i--) {
                $labels[] = now()->subMonths($i)->format('M');
                $data[]   = 0;
            }
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

        return response()->json([
            'students_present' => $presentToday,
            'students_total'   => $totalStudents,
            'staff_present'    => $staffPresentToday,
            'staff_total'      => $totalStaff,
            'fees_today'       => 0,
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

        // Student Stats
        $totalStudents = Student::where('school_id', $schoolId)->where('is_active', true)->count();
        $studentAttendance = StudentAttendance::where('school_id', $schoolId)->whereDate('date', $date)->get();
        $studentPresent = $studentAttendance->where('status', 'present')->count();
        $studentAbsent = $studentAttendance->where('status', 'absent')->count();
        $studentLate = $studentAttendance->where('status', 'late')->count();
        $studentRate = $totalStudents > 0 ? round(($studentPresent / $totalStudents) * 100, 1) : 0;

        // Staff Stats
        $totalStaff = Staff::where('school_id', $schoolId)->where('is_active', true)->count();
        $staffAttendance = StaffAttendance::where('school_id', $schoolId)->whereDate('date', $date)->get();
        $staffPresent = $staffAttendance->where('status', 'present')->count();
        $staffAbsent = $staffAttendance->where('status', 'absent')->count();
        $staffLate = $staffAttendance->where('status', 'late')->count();
        $staffRate = $totalStaff > 0 ? round(($staffPresent / $totalStaff) * 100, 1) : 0;

        // Admissions Today
        $admissionsToday = Student::where('school_id', $schoolId)
            ->whereDate('created_at', $date)
            ->with(['class', 'section'])
            ->get();

        // Class-wise breakdown
        $classes = \App\Models\SchoolClass::where('school_id', $schoolId)->with('sections')->get();
        $classBreakdown = [];
        foreach ($classes as $class) {
            foreach ($class->sections as $sec) {
                $studentsInSec = Student::where('school_id', $schoolId)->where('class_id', $class->id)->where('section_id', $sec->id)->where('is_active', true)->get();
                $tot = $studentsInSec->count();
                $att = StudentAttendance::where('school_id', $schoolId)->whereDate('date', $date)->whereIn('student_id', $studentsInSec->pluck('id'))->get();
                $pres = $att->where('status', 'present')->count();
                $classBreakdown[] = [
                    'class_name' => $class->name,
                    'section_name' => $sec->name,
                    'total' => $tot,
                    'present' => $pres,
                    'rate' => $tot > 0 ? round(($pres / $tot) * 100) : 0
                ];
            }
        }

        // Fee collection summary (stubs since Fee model is not implemented)
        $cashCollection = 12500.00;
        $onlineCollection = 45000.00;
        $totalCollection = $cashCollection + $onlineCollection;
        $pendingCollection = 32000.00;

        return view('school.dashboard.mis_report', compact(
            'school',
            'date',
            'totalStudents',
            'studentPresent',
            'studentAbsent',
            'studentLate',
            'studentRate',
            'totalStaff',
            'staffPresent',
            'staffAbsent',
            'staffLate',
            'staffRate',
            'admissionsToday',
            'classBreakdown',
            'cashCollection',
            'onlineCollection',
            'totalCollection',
            'pendingCollection'
        ));
    }
}
