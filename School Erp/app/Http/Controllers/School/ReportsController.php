<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\FeeReceipt;
use App\Models\StudentFee;
use App\Models\SchoolExpense;
use App\Models\SchoolIncome;
use App\Models\ExpenseHead;
use App\Models\IncomeHead;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    private function schoolId(): int
    {
        return auth()->user()->school_id;
    }

    // ─── Main Reports Hub ────────────────────────────────────────────────
    public function index(Request $request)
    {
        $schoolId = $this->schoolId();

        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo   = $request->get('date_to', now()->format('Y-m-d'));

        // Quick stats for the hub
        $totalStudents  = Student::where('school_id', $schoolId)->where('is_active', 1)->count();
        $totalFeesDue   = StudentFee::where('school_id', $schoolId)->sum(DB::raw('amount - paid_amount - COALESCE(instant_discount_amount, 0)'));
        
        $totalIncome    = SchoolIncome::where('school_id', $schoolId)
            ->where('status', '!=', 'cancelled')
            ->whereBetween('income_date', [$dateFrom, $dateTo])
            ->sum('amount');
            
        $totalExpense   = SchoolExpense::where('school_id', $schoolId)
            ->where('status', '!=', 'cancelled')
            ->whereBetween('expense_date', [$dateFrom, $dateTo])
            ->sum('amount');

        return view('school.reports.index', compact('totalStudents', 'totalFeesDue', 'totalIncome', 'totalExpense', 'dateFrom', 'dateTo'));
    }

    // ─── Student Report ──────────────────────────────────────────────────
    public function studentReport(Request $request)
    {
        $schoolId  = $this->schoolId();
        $classId   = $request->get('class_id', '');
        $sectionId = $request->get('section_id', '');
        $gender    = $request->get('gender', '');
        $status    = $request->get('status', 'active');
        $dateFrom  = $request->get('date_from', now()->startOfYear()->format('Y-m-d'));
        $dateTo    = $request->get('date_to', now()->format('Y-m-d'));

        $classes  = SchoolClass::where('school_id', $schoolId)->orderBy('name')->get();
        $sections = $classId
            ? Section::where('school_id', $schoolId)->where('class_id', $classId)->orderBy('name')->get()
            : collect();

        $query = Student::where('school_id', $schoolId)
            ->with(['class', 'section'])
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);

        if ($classId)   $query->where('class_id', $classId);
        if ($sectionId) $query->where('section_id', $sectionId);
        if ($gender)    $query->where('gender', $gender);
        if ($status)    $query->where('is_active', $status === 'active' ? 1 : 0);

        $students = $query->orderBy('first_name')->get();

        // Gender breakdown for pie chart
        $genderBreakdown = Student::where('school_id', $schoolId)
            ->where('is_active', 1)
            ->selectRaw("COALESCE(gender,'Unknown') as gender, COUNT(*) as total")
            ->groupBy('gender')
            ->pluck('total', 'gender');

        // Class-wise distribution
        $classWise = Student::where('students.school_id', $schoolId)
            ->where('is_active', 1)
            ->join('school_classes', 'students.class_id', '=', 'school_classes.id')
            ->selectRaw('school_classes.name as class_name, COUNT(students.id) as total')
            ->groupBy('school_classes.name')
            ->orderBy('school_classes.name')
            ->pluck('total', 'class_name');

        // Monthly admissions (last 12 months)
        $monthlyAdmissions = [];
        $monthlyLabels = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $monthlyLabels[] = $m->format('M Y');
            $monthlyAdmissions[] = Student::where('school_id', $schoolId)
                ->whereYear('created_at', $m->year)
                ->whereMonth('created_at', $m->month)
                ->count();
        }

        $totalActive   = Student::where('school_id', $schoolId)->where('is_active', 1)->count();
        $totalInactive = Student::where('school_id', $schoolId)->where('is_active', 0)->count();

        return view('school.reports.student', compact(
            'students', 'classes', 'sections',
            'genderBreakdown', 'classWise',
            'monthlyAdmissions', 'monthlyLabels',
            'totalActive', 'totalInactive',
            'classId', 'sectionId', 'gender', 'status', 'dateFrom', 'dateTo'
        ));
    }

    // ─── Attendance Report ───────────────────────────────────────────────
    public function attendanceReport(Request $request)
    {
        $schoolId  = $this->schoolId();
        $classId   = $request->get('class_id', '');
        $sectionId = $request->get('section_id', '');
        $month     = $request->get('month', now()->format('Y-m'));
        $dateFrom  = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo    = $request->get('date_to', now()->format('Y-m-d'));

        [$year, $mon] = explode('-', $month . '-' . now()->month);

        $classes  = SchoolClass::where('school_id', $schoolId)->orderBy('name')->get();
        $sections = $classId
            ? Section::where('school_id', $schoolId)->where('class_id', $classId)->orderBy('name')->get()
            : collect();

        // Attendance summary by status
        $baseQuery = StudentAttendance::where('school_id', $schoolId)
            ->whereBetween('date', [$dateFrom, $dateTo]);
        if ($classId)   $baseQuery->where('class_id', $classId);
        if ($sectionId) $baseQuery->where('section_id', $sectionId);

        $present = (clone $baseQuery)->where('status', 'present')->count();
        $absent  = (clone $baseQuery)->where('status', 'absent')->count();
        $late    = (clone $baseQuery)->where('status', 'late')->count();
        $leave   = (clone $baseQuery)->where('status', 'leave')->count();
        $total   = $present + $absent + $late + $leave;

        // Daily attendance trend (last 30 days)
        $trendDays  = [];
        $trendPres  = [];
        $trendAbs   = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = now()->subDays($i);
            $trendDays[]  = $d->format('d M');
            $dayQ = StudentAttendance::where('school_id', $schoolId)->whereDate('date', $d->format('Y-m-d'));
            if ($classId)   $dayQ->where('class_id', $classId);
            if ($sectionId) $dayQ->where('section_id', $sectionId);
            $trendPres[] = $dayQ->where('status', 'present')->count();
            $trendAbs[]  = (clone $dayQ)->where('status', 'absent')->count();
        }

        // Class-wise attendance rate
        $classAttendance = SchoolClass::where('school_id', $schoolId)->orderBy('name')->get()->map(function ($cls) use ($schoolId, $dateFrom, $dateTo) {
            $total   = StudentAttendance::where('school_id', $schoolId)->where('class_id', $cls->id)->whereBetween('date', [$dateFrom, $dateTo])->count();
            $present = StudentAttendance::where('school_id', $schoolId)->where('class_id', $cls->id)->whereBetween('date', [$dateFrom, $dateTo])->where('status', 'present')->count();
            return [
                'class'   => $cls->name,
                'total'   => $total,
                'present' => $present,
                'rate'    => $total > 0 ? round(($present / $total) * 100, 1) : 0,
            ];
        });

        return view('school.reports.attendance', compact(
            'classes', 'sections',
            'present', 'absent', 'late', 'leave', 'total',
            'trendDays', 'trendPres', 'trendAbs',
            'classAttendance',
            'classId', 'sectionId', 'month', 'dateFrom', 'dateTo'
        ));
    }

    // ─── Fee Report ──────────────────────────────────────────────────────
    public function feeReport(Request $request)
    {
        $schoolId  = $this->schoolId();
        $classId   = $request->get('class_id', '');
        $sectionId = $request->get('section_id', '');
        $dateFrom  = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo    = $request->get('date_to', now()->format('Y-m-d'));
        $status    = $request->get('status', '');

        $classes  = SchoolClass::where('school_id', $schoolId)->orderBy('name')->get();
        $sections = $classId
            ? Section::where('school_id', $schoolId)->where('class_id', $classId)->orderBy('name')->get()
            : collect();

        // Fee Receipts in date range
        $receiptQuery = FeeReceipt::where('school_id', $schoolId)
            ->with(['student.class', 'student.section'])
            ->whereBetween('payment_date', [$dateFrom, $dateTo]);

        $receipts = $receiptQuery->orderByDesc('payment_date')->get();

        $totalCollected = $receipts->sum('amount_paid');
        $totalPending   = StudentFee::where('school_id', $schoolId)
            ->whereRaw('amount - paid_amount - COALESCE(instant_discount_amount, 0) > 0')
            ->sum(DB::raw('amount - paid_amount - COALESCE(instant_discount_amount, 0)'));
        $totalRefunded  = 0;
        $receiptCount   = $receipts->count();

        // Payment mode breakdown for pie
        $paymentModes = $receipts
            ->groupBy('payment_mode')
            ->map(fn($g) => $g->sum('amount_paid'));

        // Monthly collection trend (last 6 months)
        $trendMonths = [];
        $trendFees   = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $trendMonths[] = $m->format('M Y');
            $trendFees[]   = (float) FeeReceipt::where('school_id', $schoolId)
                ->whereYear('payment_date', $m->year)
                ->whereMonth('payment_date', $m->month)
                ->sum('amount_paid');
        }

        // Class-wise fee summary
        $classFees = SchoolClass::where('school_id', $schoolId)->orderBy('name')->get()->map(function ($cls) use ($schoolId) {
            $paid    = FeeReceipt::where('school_id', $schoolId)
                ->whereHas('student', fn($q) => $q->where('class_id', $cls->id))
                ->sum('amount_paid');
            $pending = StudentFee::where('school_id', $schoolId)
                ->whereRaw('amount - paid_amount - COALESCE(instant_discount_amount, 0) > 0')
                ->whereHas('student', fn($q) => $q->where('class_id', $cls->id))
                ->sum(DB::raw('amount - paid_amount - COALESCE(instant_discount_amount, 0)'));
            return [
                'class'   => $cls->name,
                'paid'    => $paid,
                'pending' => $pending,
            ];
        });

        return view('school.reports.fees', compact(
            'classes', 'sections', 'receipts',
            'totalCollected', 'totalPending', 'totalRefunded', 'receiptCount',
            'paymentModes', 'trendMonths', 'trendFees', 'classFees',
            'classId', 'sectionId', 'dateFrom', 'dateTo', 'status'
        ));
    }

    // ─── Sibling Report ──────────────────────────────────────────────────
    public function siblingReport(Request $request)
    {
        $schoolId = $this->schoolId();
        $classId  = $request->get('class_id', '');
        $dateFrom = $request->get('date_from', now()->startOfYear()->format('Y-m-d'));
        $dateTo   = $request->get('date_to', now()->format('Y-m-d'));

        $classes = SchoolClass::where('school_id', $schoolId)->orderBy('name')->get();

        // Group students by guardian_phone (siblings share same phone)
        $query = Student::where('school_id', $schoolId)
            ->where('is_active', 1)
            ->whereNotNull('guardian_phone')
            ->with(['class', 'section']);

        if ($classId) $query->where('class_id', $classId);

        $students = $query->get();

        $siblingGroups = $students->groupBy('guardian_phone')
            ->filter(fn($group) => $group->count() > 1)
            ->values();

        // Distribution for chart
        $sizeDistribution = $siblingGroups->groupBy(fn($g) => $g->count() . ' siblings')
            ->map(fn($g) => $g->count());

        $totalSiblingStudents = $siblingGroups->sum(fn($g) => $g->count());
        $totalFamilies = $siblingGroups->count();

        return view('school.reports.siblings', compact(
            'siblingGroups', 'classes', 'sizeDistribution',
            'totalSiblingStudents', 'totalFamilies',
            'classId', 'dateFrom', 'dateTo'
        ));
    }

    // ─── Income Report ───────────────────────────────────────────────────
    public function incomeReport(Request $request)
    {
        $schoolId = $this->schoolId();
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo   = $request->get('date_to', now()->format('Y-m-d'));
        $headId   = $request->get('income_head_id', '');
        $status   = $request->get('status', '');

        $incomeHeads = IncomeHead::where('school_id', $schoolId)->orderBy('name')->get();

        $query = SchoolIncome::where('school_id', $schoolId)
            ->with('incomeHead')
            ->whereBetween('income_date', [$dateFrom, $dateTo]);
        if ($headId) $query->where('income_head_id', $headId);
        if ($status) $query->where('status', $status);

        $incomes = $query->orderByDesc('income_date')->get();

        $totalIncome  = $incomes->where('status', '!=', 'cancelled')->sum('amount');
        $totalPending = $incomes->where('status', 'pending')->sum('amount');
        $totalPaid    = $incomes->where('status', 'paid')->sum('amount');

        // Head-wise breakdown for pie
        $headBreakdown = $incomes->where('status', '!=', 'cancelled')
            ->groupBy(fn($i) => optional($i->incomeHead)->name ?? 'Other')
            ->map(fn($g) => $g->sum('amount'));

        // Monthly trend
        $trendMonths = [];
        $trendIncome = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $trendMonths[] = $m->format('M Y');
            $trendIncome[] = (float) SchoolIncome::where('school_id', $schoolId)
                ->where('status', '!=', 'cancelled')
                ->whereYear('income_date', $m->year)
                ->whereMonth('income_date', $m->month)
                ->sum('amount');
        }

        // Payment mode breakdown
        $paymentModes = $incomes->where('status', '!=', 'cancelled')
            ->groupBy('payment_mode')
            ->map(fn($g) => $g->sum('amount'));

        return view('school.reports.income', compact(
            'incomes', 'incomeHeads',
            'totalIncome', 'totalPending', 'totalPaid',
            'headBreakdown', 'trendMonths', 'trendIncome', 'paymentModes',
            'headId', 'status', 'dateFrom', 'dateTo'
        ));
    }

    // ─── Expense Report ──────────────────────────────────────────────────
    public function expenseReport(Request $request)
    {
        $schoolId = $this->schoolId();
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo   = $request->get('date_to', now()->format('Y-m-d'));
        $headId   = $request->get('expense_head_id', '');
        $status   = $request->get('status', '');

        $expenseHeads = ExpenseHead::where('school_id', $schoolId)->orderBy('name')->get();

        $query = SchoolExpense::where('school_id', $schoolId)
            ->with('expenseHead')
            ->whereBetween('expense_date', [$dateFrom, $dateTo]);
        if ($headId) $query->where('expense_head_id', $headId);
        if ($status) $query->where('status', $status);

        $expenses = $query->orderByDesc('expense_date')->get();

        $totalExpense = $expenses->where('status', '!=', 'cancelled')->sum('amount');
        $totalPending = $expenses->where('status', 'pending')->sum('amount');
        $totalPaid    = $expenses->where('status', 'paid')->sum('amount');

        // Head-wise breakdown for pie
        $headBreakdown = $expenses->where('status', '!=', 'cancelled')
            ->groupBy(fn($e) => optional($e->expenseHead)->name ?? 'Other')
            ->map(fn($g) => $g->sum('amount'));

        // Monthly trend
        $trendMonths   = [];
        $trendExpense  = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $trendMonths[]  = $m->format('M Y');
            $trendExpense[] = (float) SchoolExpense::where('school_id', $schoolId)
                ->where('status', '!=', 'cancelled')
                ->whereYear('expense_date', $m->year)
                ->whereMonth('expense_date', $m->month)
                ->sum('amount');
        }

        // Payment mode breakdown
        $paymentModes = $expenses->where('status', '!=', 'cancelled')
            ->groupBy('payment_mode')
            ->map(fn($g) => $g->sum('amount'));

        return view('school.reports.expenses', compact(
            'expenses', 'expenseHeads',
            'totalExpense', 'totalPending', 'totalPaid',
            'headBreakdown', 'trendMonths', 'trendExpense', 'paymentModes',
            'headId', 'status', 'dateFrom', 'dateTo'
        ));
    }

    // ─── AJAX: Sections for a Class ──────────────────────────────────────
    public function getSections(Request $request)
    {
        $classId  = $request->get('class_id');
        $schoolId = $this->schoolId();
        $sections = Section::where('school_id', $schoolId)->where('class_id', $classId)->orderBy('name')->get(['id', 'name']);
        return response()->json($sections);
    }

    // ─── Dynamic Detailed Report Viewer ──────────────────────────────────
    public function detailReport(string $type, Request $request)
    {
        $schoolId = $this->schoolId();
        
        $dateFrom = $request->get('date_from', now()->startOfYear()->format('Y-m-d'));
        $dateTo   = $request->get('date_to', now()->format('Y-m-d'));
        $sessionVal = $request->get('session', '');
        $dateType = $request->get('date_type', 'payment_date');

        $sessions = \App\Models\AcademicSession::where('school_id', $schoolId)->orderByDesc('is_current')->orderByDesc('name')->get();
        $currentSession = $sessions->where('is_current', 1)->first() ?? $sessions->first();
        if (!$sessionVal && $currentSession) {
            $sessionVal = $currentSession->name;
        }

        $classes = SchoolClass::where('school_id', $schoolId)->orderBy('name')->get();
        $classId = $request->get('class_id', '');
        $paymentMode = $request->get('payment_mode', '');
        $routeFilter = $request->get('route', '');

        // Friendly title map
        $titleMap = [
            'daily_collection'                => 'Daily Collection Report',
            'datewise_collection'             => 'Datewise Collection Report',
            'datewise_collection_detailed'    => 'Datewise Collection (Detailed)',
            'headwise_collection'             => 'Headwise Collection Report',
            'transport_fee_report'            => 'Transport Fee Report',
            'hostel_fee_report'               => 'Hostel Fee Report',
            'inventory_fee_report'            => 'Inventory Fee Report',
            'prospectus_fee_report'           => 'Prospectus Fee Report',
            'cancelled_payments'              => 'Cancelled Payments Report',
            'student_wise'                    => 'Student Wise Fee Report',
            'pending_fees_report'             => 'Pending Fees Report',
            'classes_wise_report'             => 'Classes Wise Report',
            'transport_wise_report'           => 'Transport Wise Report',
            'hostel_wise_report'              => 'Hostel Wise Report',
            'inventory_wise_report'           => 'Inventory Wise Report',
            'fine_report'                     => 'Fine Report',
            'discount_report'                 => 'Discount Report',
            'installment_edit_history_report' => 'Installment Edit History Report',
            'deleted_fine_report'             => 'Deleted Fine Report',
            'deleted_concession_report'       => 'Deleted Concession Report',
            // New reports
            'route_wise_transport'            => 'Route Wise Transport Report',
            'concession_fine_report'          => 'Concession & Fine Report',
            'discount_report_detailed'        => 'Discount Report (Detailed)',
            'dues_report'                     => 'Dues Report',
            'paid_report'                     => 'Paid Fees Report',
            'refund_report'                   => 'Refund Report',
            'studentwise_refund'              => 'Student-wise Refund Report',
            'estimated_fees'                  => 'Estimated Fees Report',
            'consolidated_fees'               => 'Consolidated Fees Report',
        ];
        $title = $titleMap[$type] ?? ucwords(str_replace('_', ' ', $type));

        $columns = [];
        $records = collect();
        $summary = [];

        switch ($type) {
            case 'daily_collection':
            case 'datewise_collection_detailed':
                $columns = [
                    'receipt_number' => 'Receipt No.',
                    'student_name' => 'Student Name',
                    'class_name' => 'Class & Section',
                    'payment_date' => 'Payment Date',
                    'payment_mode' => 'Payment Mode',
                    'transaction_id' => 'Transaction ID',
                    'amount_paid' => 'Amount Paid'
                ];
                $query = FeeReceipt::where('fee_receipts.school_id', $schoolId)
                    ->join('students', 'fee_receipts.student_id', '=', 'students.id')
                    ->join('school_classes', 'students.class_id', '=', 'school_classes.id')
                    ->leftJoin('sections', 'students.section_id', '=', 'sections.id')
                    ->selectRaw("fee_receipts.receipt_number, students.first_name, students.last_name, school_classes.name as class_name, sections.name as section_name, fee_receipts.payment_date, fee_receipts.payment_mode, fee_receipts.transaction_id, fee_receipts.amount_paid");
                
                if ($dateFrom && $dateTo) {
                    $query->whereBetween('fee_receipts.payment_date', [$dateFrom, $dateTo]);
                }
                
                $records = $query->orderBy('fee_receipts.payment_date')->get()->map(function($r) {
                    return [
                        'receipt_number' => $r->receipt_number,
                        'student_name' => trim($r->first_name . ' ' . $r->last_name),
                        'class_name' => $r->class_name . ($r->section_name ? ' - ' . $r->section_name : ''),
                        'payment_date' => $r->payment_date,
                        'payment_mode' => strtoupper($r->payment_mode),
                        'transaction_id' => $r->transaction_id ?? '—',
                        'amount_paid' => '₹ ' . number_format($r->amount_paid, 2)
                    ];
                });
                $summary = ['Total Collected' => '₹ ' . number_format($query->sum('amount_paid'), 2)];
                break;

            case 'datewise_collection':
                $columns = [
                    'payment_date' => 'Date',
                    'receipt_count' => 'Receipt Count',
                    'amount_paid' => 'Total Collected'
                ];
                $query = FeeReceipt::where('school_id', $schoolId);
                if ($dateFrom && $dateTo) {
                    $query->whereBetween('payment_date', [$dateFrom, $dateTo]);
                }
                $records = $query->selectRaw('payment_date, COUNT(*) as receipt_count, SUM(amount_paid) as amount_paid')
                    ->groupBy('payment_date')
                    ->orderBy('payment_date')
                    ->get()
                    ->map(function($r) {
                        return [
                            'payment_date' => $r->payment_date,
                            'receipt_count' => $r->receipt_count,
                            'amount_paid' => '₹ ' . number_format($r->amount_paid, 2)
                        ];
                    });
                $summary = [
                    'Total Collected' => '₹ ' . number_format($query->sum('amount_paid'), 2),
                    'Total Receipts' => $query->count()
                ];
                break;

            case 'headwise_collection':
                $columns = [
                    'category_name' => 'Fee Head / Category',
                    'total_collected' => 'Total Collected'
                ];
                $query = StudentFee::where('student_fees.school_id', $schoolId)
                    ->join('fee_categories', 'student_fees.fee_category_id', '=', 'fee_categories.id')
                    ->where('student_fees.paid_amount', '>', 0)
                    ->selectRaw('fee_categories.name as category_name, SUM(student_fees.paid_amount) as total_collected')
                    ->groupBy('fee_categories.name');
                
                $records = $query->get()->map(function($r) {
                    return [
                        'category_name' => $r->category_name,
                        'total_collected' => '₹ ' . number_format($r->total_collected, 2)
                    ];
                });
                $summary = ['Total Collected' => '₹ ' . number_format($query->get()->sum('total_collected'), 2)];
                break;

            case 'transport_fee_report':
            case 'hostel_fee_report':
            case 'inventory_fee_report':
            case 'prospectus_fee_report':
                $categoryKeyword = str_replace('_fee_report', '', $type);
                $columns = [
                    'student_name' => 'Student Name',
                    'class_name' => 'Class & Section',
                    'fee_head' => 'Fee Head',
                    'amount' => 'Assigned Amount',
                    'paid_amount' => 'Collected Amount',
                    'balance' => 'Balance Due'
                ];
                $query = StudentFee::where('student_fees.school_id', $schoolId)
                    ->join('fee_categories', 'student_fees.fee_category_id', '=', 'fee_categories.id')
                    ->where('fee_categories.name', 'like', '%' . $categoryKeyword . '%')
                    ->join('students', 'student_fees.student_id', '=', 'students.id')
                    ->join('school_classes', 'students.class_id', '=', 'school_classes.id')
                    ->leftJoin('sections', 'students.section_id', '=', 'sections.id')
                    ->selectRaw("students.first_name, students.last_name, school_classes.name as class_name, sections.name as section_name, fee_categories.name as category_name, student_fees.amount, student_fees.paid_amount");

                $records = $query->get()->map(function($r) {
                    return [
                        'student_name' => trim($r->first_name . ' ' . $r->last_name),
                        'class_name' => $r->class_name . ($r->section_name ? ' - ' . $r->section_name : ''),
                        'fee_head' => $r->category_name,
                        'amount' => '₹ ' . number_format($r->amount, 2),
                        'paid_amount' => '₹ ' . number_format($r->paid_amount, 2),
                        'balance' => '₹ ' . number_format($r->amount - $r->paid_amount, 2)
                    ];
                });
                $summary = [
                    'Total Assigned' => '₹ ' . number_format($query->sum('amount'), 2),
                    'Total Collected' => '₹ ' . number_format($query->sum('paid_amount'), 2),
                    'Total Balance' => '₹ ' . number_format($query->sum('amount') - $query->sum('paid_amount'), 2)
                ];
                break;

            case 'cancelled_payments':
                $columns = [
                    'receipt_number' => 'Receipt No.',
                    'student_name' => 'Student Name',
                    'payment_date' => 'Payment Date',
                    'amount' => 'Amount',
                    'reason' => 'Reason'
                ];
                $query = DB::table('cancelled_payments')
                    ->where('school_id', $schoolId)
                    ->whereBetween('payment_date', [$dateFrom, $dateTo])
                    ->orderByDesc('payment_date');
                
                $records = $query->get()->map(function($r) {
                    return [
                        'receipt_number' => $r->receipt_number,
                        'student_name' => $r->student_name,
                        'payment_date' => $r->payment_date,
                        'amount' => '₹ ' . number_format($r->amount, 2),
                        'reason' => $r->reason
                    ];
                });
                $summary = ['Total Cancelled' => '₹ ' . number_format($query->sum('amount'), 2)];
                break;

            case 'student_wise':
                $columns = [
                    'student_name' => 'Student Name',
                    'class_name' => 'Class & Section',
                    'total_fees' => 'Total Assigned',
                    'total_paid' => 'Total Paid',
                    'total_dues' => 'Total Dues'
                ];
                $query = Student::where('students.school_id', $schoolId)
                    ->join('school_classes', 'students.class_id', '=', 'school_classes.id')
                    ->leftJoin('sections', 'students.section_id', '=', 'sections.id')
                    ->leftJoin('student_fees', 'students.id', '=', 'student_fees.student_id')
                    ->selectRaw("students.first_name, students.last_name, school_classes.name as class_name, sections.name as section_name, SUM(student_fees.amount) as total_fees, SUM(student_fees.paid_amount) as total_paid")
                    ->groupBy('students.id', 'students.first_name', 'students.last_name', 'school_classes.name', 'sections.name');

                $records = $query->get()->map(function($r) {
                    return [
                        'student_name' => trim($r->first_name . ' ' . $r->last_name),
                        'class_name' => $r->class_name . ($r->section_name ? ' - ' . $r->section_name : ''),
                        'total_fees' => '₹ ' . number_format($r->total_fees ?? 0, 2),
                        'total_paid' => '₹ ' . number_format($r->total_paid ?? 0, 2),
                        'total_dues' => '₹ ' . number_format(($r->total_fees ?? 0) - ($r->total_paid ?? 0), 2)
                    ];
                });
                $summary = [
                    'Total Assigned' => '₹ ' . number_format($query->get()->sum('total_fees'), 2),
                    'Total Collected' => '₹ ' . number_format($query->get()->sum('total_paid'), 2)
                ];
                break;

            case 'pending_fees_report':
                $columns = [
                    'student_name' => 'Student Name',
                    'class_name' => 'Class & Section',
                    'fee_head' => 'Fee Head',
                    'amount_due' => 'Amount Due',
                    'due_date' => 'Due Date'
                ];
                $query = StudentFee::where('student_fees.school_id', $schoolId)
                    ->whereRaw('student_fees.amount - student_fees.paid_amount > 0')
                    ->join('students', 'student_fees.student_id', '=', 'students.id')
                    ->join('school_classes', 'students.class_id', '=', 'school_classes.id')
                    ->leftJoin('sections', 'students.section_id', '=', 'sections.id')
                    ->join('fee_categories', 'student_fees.fee_category_id', '=', 'fee_categories.id')
                    ->selectRaw("students.first_name, students.last_name, school_classes.name as class_name, sections.name as section_name, fee_categories.name as category_name, (student_fees.amount - student_fees.paid_amount) as amount_due, student_fees.due_date");

                $results = $query->get();
                $records = $results->map(function($r) {
                    return [
                        'student_name' => trim($r->first_name . ' ' . $r->last_name),
                        'class_name' => $r->class_name . ($r->section_name ? ' - ' . $r->section_name : ''),
                        'fee_head' => $r->category_name,
                        'amount_due' => '₹ ' . number_format($r->amount_due, 2),
                        'due_date' => $r->due_date ? Carbon::parse($r->due_date)->format('d M Y') : '—'
                    ];
                });
                $summary = ['Total Pending' => '₹ ' . number_format($results->sum('amount_due'), 2)];
                break;

            case 'classes_wise_report':
                $columns = [
                    'class_name' => 'Class Name',
                    'student_count' => 'Total Students',
                    'total_collected' => 'Total Collected',
                    'total_pending' => 'Total Pending'
                ];
                $query = SchoolClass::where('school_classes.school_id', $schoolId)
                    ->leftJoin('students', 'school_classes.id', '=', 'students.class_id')
                    ->leftJoin('student_fees', 'students.id', '=', 'student_fees.student_id')
                    ->selectRaw("school_classes.name as class_name, COUNT(DISTINCT students.id) as student_count, SUM(student_fees.paid_amount) as total_collected, SUM(student_fees.amount - student_fees.paid_amount) as total_pending")
                    ->groupBy('school_classes.id', 'school_classes.name');

                $records = $query->get()->map(function($r) {
                    return [
                        'class_name' => $r->class_name,
                        'student_count' => $r->student_count,
                        'total_collected' => '₹ ' . number_format($r->total_collected ?? 0, 2),
                        'total_pending' => '₹ ' . number_format($r->total_pending ?? 0, 2)
                    ];
                });
                $summary = [
                    'Total Collected' => '₹ ' . number_format($query->get()->sum('total_collected'), 2),
                    'Total Pending' => '₹ ' . number_format($query->get()->sum('total_pending'), 2)
                ];
                break;

            case 'transport_wise_report':
            case 'hostel_wise_report':
            case 'inventory_wise_report':
                $catKeyword = str_replace('_wise_report', '', $type);
                $columns = [
                    'student_name' => 'Student Name',
                    'class_name' => 'Class & Section',
                    'fee_head' => 'Fee Head',
                    'paid_amount' => 'Collected',
                    'pending_amount' => 'Pending'
                ];
                $query = StudentFee::where('student_fees.school_id', $schoolId)
                    ->join('fee_categories', 'student_fees.fee_category_id', '=', 'fee_categories.id')
                    ->where('fee_categories.name', 'like', '%' . $catKeyword . '%')
                    ->join('students', 'student_fees.student_id', '=', 'students.id')
                    ->join('school_classes', 'students.class_id', '=', 'school_classes.id')
                    ->leftJoin('sections', 'students.section_id', '=', 'sections.id')
                    ->selectRaw("students.first_name, students.last_name, school_classes.name as class_name, sections.name as section_name, MIN(fee_categories.name) as category_name, SUM(student_fees.paid_amount) as paid_amount, SUM(student_fees.amount - student_fees.paid_amount) as pending_amount")
                    ->groupBy('students.id', 'students.first_name', 'students.last_name', 'school_classes.name', 'sections.name');

                $results = $query->get();
                $records = $results->map(function($r) {
                    return [
                        'student_name' => trim($r->first_name . ' ' . $r->last_name),
                        'class_name' => $r->class_name . ($r->section_name ? ' - ' . $r->section_name : ''),
                        'fee_head' => $r->category_name,
                        'paid_amount' => '₹ ' . number_format($r->paid_amount, 2),
                        'pending_amount' => '₹ ' . number_format($r->pending_amount, 2)
                    ];
                });
                $summary = [
                    'Total Collected' => '₹ ' . number_format($results->sum('paid_amount'), 2),
                    'Total Pending' => '₹ ' . number_format($results->sum('pending_amount'), 2)
                ];
                break;

            case 'fine_report':
                $columns = [
                    'name' => 'Fine Name',
                    'fine_type' => 'Fine Type',
                    'fine_amount' => 'Fine Amount',
                    'status' => 'Status'
                ];
                $records = DB::table('fee_fines')->where('school_id', $schoolId)->get()->map(function($r) {
                    return [
                        'name' => $r->name,
                        'fine_type' => $r->fine_type,
                        'fine_amount' => '₹ ' . number_format($r->fine_amount, 2),
                        'status' => $r->status ? 'Active' : 'Inactive'
                    ];
                });
                $summary = ['Total Active Fines' => $records->where('status', 'Active')->count()];
                break;

            case 'discount_report':
                $columns = [
                    'name' => 'Discount Name',
                    'classes_installments' => 'Installment Mapping',
                    'amount' => 'Discount Amount'
                ];
                $records = DB::table('fee_discounts')->where('school_id', $schoolId)->get()->map(function($r) {
                    return [
                        'name' => $r->name,
                        'classes_installments' => $r->classes_installments ? implode(', ', json_decode($r->classes_installments, true) ?: []) : 'All Classes',
                        'amount' => '₹ ' . number_format($r->amount, 2)
                    ];
                });
                $summary = ['Total Discount Schemes' => $records->count()];
                break;

            case 'installment_edit_history_report':
                $columns = [
                    'student_name' => 'Student Name',
                    'field' => 'Field Edited',
                    'old_value' => 'Old Value',
                    'new_value' => 'New Value',
                    'updated_at' => 'Date & Time'
                ];
                $query = DB::table('installment_edit_histories')
                    ->where('school_id', $schoolId)
                    ->orderByDesc('created_at');
                
                $records = $query->get()->map(function($r) {
                    return [
                        'student_name' => $r->student_name,
                        'field' => $r->field,
                        'old_value' => $r->old_value,
                        'new_value' => $r->new_value,
                        'updated_at' => Carbon::parse($r->created_at)->format('d M Y h:i A')
                    ];
                });
                $summary = ['Total Updates Logs' => $records->count()];
                break;

            case 'deleted_fine_report':
                $columns = [
                    'fine_name' => 'Fine Name',
                    'deleted_by' => 'Deleted By',
                    'date' => 'Deletion Date'
                ];
                $query = DB::table('deleted_fines')
                    ->where('school_id', $schoolId)
                    ->whereBetween('date', [$dateFrom, $dateTo])
                    ->orderByDesc('date');
                
                $records = $query->get()->map(function($r) {
                    return [
                        'fine_name' => $r->fine_name,
                        'deleted_by' => $r->deleted_by,
                        'date' => Carbon::parse($r->date)->format('d M Y')
                    ];
                });
                $summary = ['Total Deleted Fines' => $records->count()];
                break;

            case 'deleted_concession_report':
                $columns = [
                    'concession_name' => 'Concession Name',
                    'deleted_by' => 'Deleted By',
                    'date' => 'Deletion Date'
                ];
                $query = DB::table('deleted_concessions')
                    ->where('school_id', $schoolId)
                    ->whereBetween('date', [$dateFrom, $dateTo])
                    ->orderByDesc('date');
                
                $records = $query->get()->map(function($r) {
                    return [
                        'concession_name' => $r->concession_name,
                        'deleted_by' => $r->deleted_by,
                        'date' => Carbon::parse($r->date)->format('d M Y')
                    ];
                });
                $summary = ['Total Deleted Concessions' => $records->count()];
                break;

            // ─── NEW ADDITIONAL REPORTS ────────────────────────────────────

            case 'route_wise_transport':
                $columns = [
                    'route_name'     => 'Route Name',
                    'student_name'   => 'Student Name',
                    'class_name'     => 'Class & Section',
                    'stop_name'      => 'Boarding Stop',
                    'vehicle_code'   => 'Vehicle No.',
                    'transport_month'=> 'Transport Month',
                ];
                $records = Student::where('students.school_id', $schoolId)
                    ->whereNotNull('students.transport_route')
                    ->join('school_classes', 'students.class_id', '=', 'school_classes.id')
                    ->leftJoin('sections', 'students.section_id', '=', 'sections.id')
                    ->selectRaw("students.first_name, students.last_name, school_classes.name as class_name, sections.name as section_name, students.transport_route, students.transport_stop, students.transport_vehicle_code, students.transport_month")
                    ->orderBy('students.transport_route')
                    ->orderBy('students.first_name')
                    ->get()->map(function($r) {
                        return [
                            'route_name'      => $r->transport_route ?? '—',
                            'student_name'    => trim($r->first_name . ' ' . $r->last_name),
                            'class_name'      => $r->class_name . ($r->section_name ? ' - ' . $r->section_name : ''),
                            'stop_name'       => $r->transport_stop ?? '—',
                            'vehicle_code'    => $r->transport_vehicle_code ?? '—',
                            'transport_month' => $r->transport_month ?? '—',
                        ];
                    });
                $summary = ['Total Transport Students' => $records->count()];
                break;

            case 'concession_fine_report':
                $columns = [
                    'type'         => 'Type',
                    'name'         => 'Name',
                    'fine_type'    => 'Sub-Type / Amount Type',
                    'amount'       => 'Amount / Value',
                    'status'       => 'Status',
                ];
                $fines = DB::table('fee_fines')->where('school_id', $schoolId)->get()->map(function($r) {
                    return [
                        'type'      => 'Fine',
                        'name'      => $r->name,
                        'fine_type' => $r->fine_type,
                        'amount'    => '₹ ' . number_format($r->fine_amount, 2),
                        'status'    => $r->status ? 'Active' : 'Inactive',
                    ];
                });
                $discounts = DB::table('fee_discounts')->where('school_id', $schoolId)->get()->map(function($r) {
                    $targetInst = $r->installment_no ? ' (Installment ' . $r->installment_no . ')' : '';
                    return [
                        'type'      => 'Concession / Discount',
                        'name'      => $r->name . $targetInst,
                        'fine_type' => isset($r->type) && $r->type === 'percentage' ? 'Percentage' : 'Fixed Amount',
                        'amount'    => isset($r->type) && $r->type === 'percentage' ? number_format($r->amount, 0) . '%' : '₹ ' . number_format($r->amount, 2),
                        'status'    => 'Active',
                    ];
                });
                $records = $fines->merge($discounts);
                $summary = [
                    'Total Fines'     => $fines->count(),
                    'Total Discounts' => $discounts->count(),
                ];
                break;

            case 'discount_report_detailed':
                $columns = [
                    'discount_name'  => 'Discount Name',
                    'student_name'   => 'Student Name',
                    'class_name'     => 'Class & Section',
                    'amount'         => 'Discount Amount',
                    'remarks'        => 'Remarks',
                ];
                $discountRows = DB::table('fee_discounts')
                    ->where('fee_discounts.school_id', $schoolId)
                    ->get();
                $rows = collect();
                foreach ($discountRows as $disc) {
                    $targetInst = $disc->installment_no ? 'Applies to: Installment ' . $disc->installment_no : 'Applies to: All Installments';
                    $formattedRemarks = ($disc->remarks ? $disc->remarks . ' · ' : '') . $targetInst;
                    $formattedAmount = isset($disc->type) && $disc->type === 'percentage' ? number_format($disc->amount, 0) . '%' : '₹ ' . number_format($disc->amount, 2);

                    $studentIds = $disc->student_ids ? json_decode($disc->student_ids, true) : [];
                    if (!empty($studentIds)) {
                        $students = Student::whereIn('students.id', $studentIds)
                            ->join('school_classes', 'students.class_id', '=', 'school_classes.id')
                            ->leftJoin('sections', 'students.section_id', '=', 'sections.id')
                            ->selectRaw("students.first_name, students.last_name, school_classes.name as class_name, sections.name as section_name")
                            ->get();
                        foreach ($students as $stu) {
                            $rows->push([
                                'discount_name' => $disc->name,
                                'student_name'  => trim($stu->first_name . ' ' . $stu->last_name),
                                'class_name'    => $stu->class_name . ($stu->section_name ? ' - ' . $stu->section_name : ''),
                                'amount'        => $formattedAmount,
                                'remarks'       => $formattedRemarks,
                            ]);
                        }
                    } else {
                        $rows->push([
                            'discount_name' => $disc->name,
                            'student_name'  => 'All Students',
                            'class_name'    => $disc->classes_installments ?? 'All Classes',
                            'amount'        => $formattedAmount,
                            'remarks'       => $formattedRemarks,
                        ]);
                    }
                }
                $records = $rows;
                $summary = ['Total Discount Entries' => $records->count()];
                break;

            case 'dues_report':
                $columns = [
                    'student_name'  => 'Student Name',
                    'admission_no'  => 'Admission No.',
                    'class_name'    => 'Class & Section',
                    'fee_head'      => 'Fee Head',
                    'total_amount'  => 'Total Amount',
                    'paid_amount'   => 'Amount Paid',
                    'dues_amount'   => 'Dues Amount',
                    'due_date'      => 'Due Date',
                    'status'        => 'Status',
                ];
                $query = StudentFee::where('student_fees.school_id', $schoolId)
                    ->whereRaw('student_fees.amount - student_fees.paid_amount > 0')
                    ->join('students', 'student_fees.student_id', '=', 'students.id')
                    ->join('school_classes', 'students.class_id', '=', 'school_classes.id')
                    ->leftJoin('sections', 'students.section_id', '=', 'sections.id')
                    ->join('fee_categories', 'student_fees.fee_category_id', '=', 'fee_categories.id')
                    ->selectRaw("students.admission_number, students.first_name, students.last_name, school_classes.name as class_name, sections.name as section_name, MIN(fee_categories.name) as category_name, SUM(student_fees.amount) as amount, SUM(student_fees.paid_amount) as paid_amount, SUM(student_fees.amount - student_fees.paid_amount) as dues_amount, MIN(student_fees.due_date) as due_date, MAX(student_fees.status) as status")
                    ->groupBy('students.id', 'students.admission_number', 'students.first_name', 'students.last_name', 'school_classes.name', 'sections.name')
                    ->orderBy('students.first_name');
                if ($classId) $query->where('students.class_id', $classId);

                $results = $query->get();
                $records = $results->map(function($r) {
                    return [
                        'student_name'  => trim($r->first_name . ' ' . $r->last_name),
                        'admission_no'  => $r->admission_number ?? '—',
                        'class_name'    => $r->class_name . ($r->section_name ? ' - ' . $r->section_name : ''),
                        'fee_head'      => 'Pending Fees',
                        'total_amount'  => '₹ ' . number_format($r->amount, 2),
                        'paid_amount'   => '₹ ' . number_format($r->paid_amount, 2),
                        'dues_amount'   => '₹ ' . number_format($r->dues_amount, 2),
                        'due_date'      => $r->due_date ? Carbon::parse($r->due_date)->format('d M Y') : '—',
                        'status'        => 'Pending',
                    ];
                });
                $summary = [
                    'Total Records with Dues' => $records->count(),
                    'Total Dues Amount'        => '₹ ' . number_format($results->sum('dues_amount'), 2),
                ];
                break;

            case 'paid_report':
                $columns = [
                    'receipt_number' => 'Receipt No.',
                    'student_name'   => 'Student Name',
                    'admission_no'   => 'Admission No.',
                    'class_name'     => 'Class & Section',
                    'payment_mode'   => 'Payment Mode',
                    'transaction_id' => 'Transaction / Cheque ID',
                    'payment_date'   => 'Payment Date',
                    'amount_paid'    => 'Amount Paid',
                ];
                $query = FeeReceipt::where('fee_receipts.school_id', $schoolId)
                    ->join('students', 'fee_receipts.student_id', '=', 'students.id')
                    ->join('school_classes', 'students.class_id', '=', 'school_classes.id')
                    ->leftJoin('sections', 'students.section_id', '=', 'sections.id')
                    ->selectRaw("fee_receipts.receipt_number, students.admission_number, students.first_name, students.last_name, school_classes.name as class_name, sections.name as section_name, fee_receipts.payment_mode, fee_receipts.transaction_id, fee_receipts.payment_date, fee_receipts.amount_paid")
                    ->whereBetween('fee_receipts.payment_date', [$dateFrom, $dateTo])
                    ->orderByDesc('fee_receipts.payment_date');
                if ($classId) $query->where('students.class_id', $classId);
                if ($paymentMode) $query->where('fee_receipts.payment_mode', $paymentMode);

                $records = $query->get()->map(function($r) {
                    return [
                        'receipt_number' => $r->receipt_number,
                        'student_name'   => trim($r->first_name . ' ' . $r->last_name),
                        'admission_no'   => $r->admission_number ?? '—',
                        'class_name'     => $r->class_name . ($r->section_name ? ' - ' . $r->section_name : ''),
                        'payment_mode'   => strtoupper($r->payment_mode),
                        'transaction_id' => $r->transaction_id ?? '—',
                        'payment_date'   => $r->payment_date,
                        'amount_paid'    => '₹ ' . number_format($r->amount_paid, 2),
                    ];
                });
                $summary = [
                    'Total Receipts'   => $records->count(),
                    'Total Collected'  => '₹ ' . number_format($query->sum('fee_receipts.amount_paid'), 2),
                ];
                break;

            case 'refund_report':
                $columns = [
                    'student_name'  => 'Student Name',
                    'class_name'    => 'Class & Section',
                    'amount'        => 'Refund Amount',
                    'refund_date'   => 'Refund Date',
                    'reason'        => 'Reason',
                ];
                $query = DB::table('fee_refunds')
                    ->where('fee_refunds.school_id', $schoolId)
                    ->join('students', 'fee_refunds.student_id', '=', 'students.id')
                    ->join('school_classes', 'students.class_id', '=', 'school_classes.id')
                    ->leftJoin('sections', 'students.section_id', '=', 'sections.id')
                    ->selectRaw("students.first_name, students.last_name, school_classes.name as class_name, sections.name as section_name, fee_refunds.amount, fee_refunds.refund_date, fee_refunds.reason")
                    ->whereBetween('fee_refunds.refund_date', [$dateFrom, $dateTo])
                    ->orderByDesc('fee_refunds.refund_date');
                if ($classId) $query->where('students.class_id', $classId);

                $records = $query->get()->map(function($r) {
                    return [
                        'student_name'  => trim($r->first_name . ' ' . $r->last_name),
                        'class_name'    => $r->class_name . ($r->section_name ? ' - ' . $r->section_name : ''),
                        'amount'        => '₹ ' . number_format($r->amount, 2),
                        'refund_date'   => Carbon::parse($r->refund_date)->format('d M Y'),
                        'reason'        => $r->reason ?? '—',
                    ];
                });
                $summary = [
                    'Total Refunds'       => $records->count(),
                    'Total Refund Amount' => '₹ ' . number_format($query->sum('fee_refunds.amount'), 2),
                ];
                break;

            case 'studentwise_refund':
                $columns = [
                    'student_name'  => 'Student Name',
                    'admission_no'  => 'Admission No.',
                    'class_name'    => 'Class & Section',
                    'refund_count'  => 'Refunds Count',
                    'total_refund'  => 'Total Refunded Amount',
                ];
                $query = DB::table('fee_refunds')
                    ->where('fee_refunds.school_id', $schoolId)
                    ->join('students', 'fee_refunds.student_id', '=', 'students.id')
                    ->join('school_classes', 'students.class_id', '=', 'school_classes.id')
                    ->leftJoin('sections', 'students.section_id', '=', 'sections.id')
                    ->selectRaw("students.first_name, students.last_name, students.admission_number, school_classes.name as class_name, sections.name as section_name, COUNT(fee_refunds.id) as refund_count, SUM(fee_refunds.amount) as total_refund")
                    ->whereBetween('fee_refunds.refund_date', [$dateFrom, $dateTo])
                    ->groupBy('students.id', 'students.first_name', 'students.last_name', 'students.admission_number', 'school_classes.name', 'sections.name')
                    ->orderBy('students.first_name');
                if ($classId) $query->where('students.class_id', $classId);

                $records = $query->get()->map(function($r) {
                    return [
                        'student_name'  => trim($r->first_name . ' ' . $r->last_name),
                        'admission_no'  => $r->admission_number ?? '—',
                        'class_name'    => $r->class_name . ($r->section_name ? ' - ' . $r->section_name : ''),
                        'refund_count'  => $r->refund_count,
                        'total_refund'  => '₹ ' . number_format($r->total_refund, 2),
                    ];
                });
                
                $summary = [
                    'Total Refunded Students' => $records->count(),
                    'Total Refunded Amount'   => '₹ ' . number_format($records->sum(function($r) {
                        return (float) str_replace(['₹ ', ','], '', $r['total_refund']);
                    }), 2),
                ];
                break;

            case 'estimated_fees':
                $columns = [
                    'class_name'       => 'Class Name',
                    'student_count'    => 'Total Students',
                    'fee_head'         => 'Fee Head',
                    'per_student_fee'  => 'Fee Per Student',
                    'estimated_total'  => 'Estimated Total',
                ];
                $query = DB::table('fee_structures')
                    ->where('fee_structures.school_id', $schoolId)
                    ->join('school_classes', 'fee_structures.class_id', '=', 'school_classes.id')
                    ->join('fee_categories', 'fee_structures.fee_category_id', '=', 'fee_categories.id')
                    ->selectRaw("school_classes.name as class_name, fee_categories.name as category_name, fee_structures.amount");

                $rows = collect();
                foreach ($query->get() as $fs) {
                    $classObj = SchoolClass::where('school_id', $schoolId)->where('name', $fs->class_name)->first();
                    $studentCount = $classObj ? Student::where('class_id', $classObj->id)->where('school_id', $schoolId)->count() : 0;
                    $rows->push([
                        'class_name'      => $fs->class_name,
                        'student_count'   => $studentCount,
                        'fee_head'        => $fs->category_name,
                        'per_student_fee' => '₹ ' . number_format($fs->amount, 2),
                        'estimated_total' => '₹ ' . number_format($fs->amount * $studentCount, 2),
                    ]);
                }
                $records = $rows;
                $grandEstimate = $rows->sum(function($r) {
                    return (float) str_replace(['₹ ', ','], '', $r['estimated_total']);
                });
                $summary = [
                    'Total Fee Heads'      => $records->count(),
                    'Grand Estimated Total' => '₹ ' . number_format($grandEstimate, 2),
                ];
                break;

            case 'consolidated_fees':
                $columns = [
                    'student_name'   => 'Student Name',
                    'admission_no'   => 'Admission No.',
                    'class_name'     => 'Class & Section',
                    'total_assigned' => 'Total Fees Assigned',
                    'total_paid'     => 'Total Paid',
                    'total_dues'     => 'Total Dues',
                    'total_refund'   => 'Total Refunded',
                    'net_balance'    => 'Net Balance',
                ];
                $studentsData = Student::where('students.school_id', $schoolId)
                    ->join('school_classes', 'students.class_id', '=', 'school_classes.id')
                    ->leftJoin('sections', 'students.section_id', '=', 'sections.id')
                    ->leftJoin('student_fees', 'students.id', '=', 'student_fees.student_id')
                    ->selectRaw("students.id as student_id, students.admission_number, students.first_name, students.last_name, school_classes.name as class_name, sections.name as section_name, SUM(student_fees.amount) as total_assigned, SUM(student_fees.paid_amount) as total_paid")
                    ->groupBy('students.id', 'students.admission_number', 'students.first_name', 'students.last_name', 'school_classes.name', 'sections.name')
                    ->orderBy('students.first_name')
                    ->get();

                $records = $studentsData->map(function($s) {
                    $totalRefund = DB::table('fee_refunds')->where('student_id', $s->student_id)->sum('amount');
                    $totalAssigned = (float)($s->total_assigned ?? 0);
                    $totalPaid = (float)($s->total_paid ?? 0);
                    $totalDues = max(0, $totalAssigned - $totalPaid);
                    $netBalance = $totalDues - (float)$totalRefund;
                    return [
                        'student_name'   => trim($s->first_name . ' ' . $s->last_name),
                        'admission_no'   => $s->admission_number ?? '—',
                        'class_name'     => $s->class_name . ($s->section_name ? ' - ' . $s->section_name : ''),
                        'total_assigned' => '₹ ' . number_format($totalAssigned, 2),
                        'total_paid'     => '₹ ' . number_format($totalPaid, 2),
                        'total_dues'     => '₹ ' . number_format($totalDues, 2),
                        'total_refund'   => '₹ ' . number_format($totalRefund, 2),
                        'net_balance'    => '₹ ' . number_format($netBalance, 2),
                    ];
                });
                $totalAssignedAll = $studentsData->sum(fn($s) => (float)($s->total_assigned ?? 0));
                $totalPaidAll     = $studentsData->sum(fn($s) => (float)($s->total_paid ?? 0));
                $summary = [
                    'Total Students'   => $records->count(),
                    'Total Assigned'   => '₹ ' . number_format($totalAssignedAll, 2),
                    'Total Collected'  => '₹ ' . number_format($totalPaidAll, 2),
                    'Total Dues'       => '₹ ' . number_format(max(0, $totalAssignedAll - $totalPaidAll), 2),
                ];
                break;
        }

        $school = \App\Models\School::find($schoolId);

        return view('school.reports.detail', compact(
            'type', 'title', 'columns', 'records', 'summary',
            'dateFrom', 'dateTo', 'sessionVal', 'dateType', 'sessions', 'classes', 'school'
        ));
    }
}

