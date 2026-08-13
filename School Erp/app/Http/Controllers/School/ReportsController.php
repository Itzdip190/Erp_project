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
use App\Models\AcademicSession;
use App\Models\FeeSchedule;
use App\Models\FeeComponent;
use App\Models\PendingCheque;
use App\Models\School;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Support\SearchHelper;

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
            ->with(['class', 'section', 'documents', 'category', 'house'])
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);

        if ($classId)   $query->where('class_id', $classId);
        if ($sectionId) $query->where('section_id', $sectionId);
        if ($gender)    $query->where('gender', $gender);
        if ($status)    $query->where('is_active', $status === 'active' ? 1 : 0);

        $students = $query->orderBy('first_name')->get();

        // Document upload statistics
        $studentsWithDocsCount = $students->filter(fn($s) => $s->documents->count() > 0)->count();
        $studentsWithoutDocsCount = $students->count() - $studentsWithDocsCount;
        $totalDocsUploaded = $students->sum(fn($s) => $s->documents->count());

        $docTypesBreakdown = [];
        foreach ($students as $st) {
            foreach ($st->documents as $doc) {
                $type = $doc->document_type ?: 'Other Document';
                $docTypesBreakdown[$type] = ($docTypesBreakdown[$type] ?? 0) + 1;
            }
        }
        arsort($docTypesBreakdown);

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
            'studentsWithDocsCount', 'studentsWithoutDocsCount', 'totalDocsUploaded', 'docTypesBreakdown',
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

    // ─── Fee Report Module ──────────────────────────────────────────────────────
    public function feeReport(Request $request)
    {
        $schoolId = $this->schoolId();
        $tabData  = $this->getFeeReportData($request, $schoolId);

        return view('school.reports.fees', array_merge($tabData, [
            'schoolId' => $schoolId
        ]));
    }

    public function exportFeeReportPdf(Request $request)
    {
        $schoolId = $this->schoolId();
        $tabData  = $this->getFeeReportData($request, $schoolId);

        $school = School::find($schoolId);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('school.reports.pdf.fee-report-pdf', [
            'school'        => $school,
            'reportTitle'   => $tabData['reportTitle'],
            'sessionName'   => $tabData['sessionName'],
            'dateFrom'      => $tabData['dateFrom'],
            'dateTo'        => $tabData['dateTo'],
            'filterSummary' => $tabData['filterSummary'],
            'kpis'          => $tabData['kpis'],
            'headers'       => $tabData['exportHeaders'],
            'rows'          => $tabData['exportRows'],
            'totals'        => $tabData['exportTotals'],
        ]);

        $pdf->setPaper('a4', 'landscape');
        $fileName = str_replace(' ', '_', $tabData['reportTitle']) . '_' . date('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }

    public function exportFeeReportCsv(Request $request)
    {
        $schoolId = $this->schoolId();
        $tabData  = $this->getFeeReportData($request, $schoolId);

        $fileName = str_replace(' ', '_', $tabData['reportTitle']) . '_' . date('Ymd_His') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($tabData) {
            $file = fopen('php://output', 'w');
            
            // CSV Title & Metadata Header
            fputcsv($file, [$tabData['reportTitle']]);
            fputcsv($file, ["Session", $tabData['sessionName'], "Date Range", $tabData['dateFrom'] . ' to ' . $tabData['dateTo']]);
            fputcsv($file, ["Filters", $tabData['filterSummary']]);
            fputcsv($file, []);

            // Data Table Column Headers
            $colTitles = array_map(fn($h) => $h['title'], $tabData['exportHeaders']);
            fputcsv($file, $colTitles);

            // Data Rows
            foreach ($tabData['exportRows'] as $row) {
                $line = [];
                foreach ($tabData['exportHeaders'] as $key => $h) {
                    $fieldKey = is_numeric($key) ? ($h['key'] ?? '') : $key;
                    $val = $row[$fieldKey] ?? '';
                    $line[] = strip_tags(str_replace(['&nbsp;', '₹'], ['', ''], $val));
                }
                fputcsv($file, $line);
            }

            // Totals Row
            if (!empty($tabData['exportTotals'])) {
                $totalLine = [];
                foreach ($tabData['exportHeaders'] as $key => $h) {
                    $fieldKey = is_numeric($key) ? ($h['key'] ?? '') : $key;
                    $val = $tabData['exportTotals'][$fieldKey] ?? '';
                    $totalLine[] = strip_tags(str_replace(['&nbsp;', '₹'], ['', ''], $val));
                }
                fputcsv($file, $totalLine);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getFeeReportData(Request $request, $schoolId)
    {
        $activeTab         = $request->get('tab', 'student_fee_collection');
        $selectedSessionId = $request->get('academic_session_id');
        
        $academicSessions = AcademicSession::where('school_id', $schoolId)->get();
        $currentSession   = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first()
            ?? $academicSessions->first();

        if (!$selectedSessionId) {
            $selectedSessionId = $currentSession?->id;
        }

        $sessionObj  = $academicSessions->firstWhere('id', $selectedSessionId) ?? $currentSession;
        $sessionName = $sessionObj ? $sessionObj->name : 'All Sessions';

        $classes  = SchoolClass::where('school_id', $schoolId)->orderBy('name')->get();
        $classId   = $request->get('class_id', '');
        $sectionId = $request->get('section_id', '');
        $sections = $classId
            ? Section::where('school_id', $schoolId)->where('class_id', $classId)->orderBy('name')->get()
            : Section::where('school_id', $schoolId)->orderBy('name')->get();

        $dateFrom       = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo         = $request->get('date_to', now()->format('Y-m-d'));
        $installmentNo  = $request->get('installment_no', '');
        $paymentMode    = $request->get('payment_mode', '');
        $status         = $request->get('status', '');
        $feeScheduleId  = $request->get('fee_schedule_id', '');
        $feeComponentId = $request->get('fee_component_id', '');
        $searchStudent  = $request->get('search_student', '');

        $feeSchedules  = FeeSchedule::where('school_id', $schoolId)->get();
        $feeComponents = FeeComponent::where('school_id', $schoolId)->get();
        $paymentModes  = FeeReceipt::where('school_id', $schoolId)->whereNotNull('payment_mode')->where('payment_mode', '!=', '')->distinct()->pluck('payment_mode');
        $installments  = StudentFee::where('school_id', $schoolId)->whereNotNull('installment_no')->distinct()->pluck('installment_no')->sort();

        // Student filter Closure
        $studentFilter = function($q) use ($selectedSessionId, $classId, $sectionId, $searchStudent) {
            if ($selectedSessionId) $q->where('academic_session_id', $selectedSessionId);
            if ($classId) $q->where('class_id', $classId);
            if ($sectionId) $q->where('section_id', $sectionId);
            if ($searchStudent) {
                SearchHelper::applyStudentSearch($q, $searchStudent);
            }
        };

        // Filter summary text for exports
        $filterParts = [];
        if ($classId && $cls = $classes->firstWhere('id', $classId)) $filterParts[] = 'Class: ' . $cls->name;
        if ($sectionId && $sec = $sections->firstWhere('id', $sectionId)) $filterParts[] = 'Section: ' . $sec->name;
        if ($paymentMode) $filterParts[] = 'Mode: ' . ucfirst($paymentMode);
        if ($status) $filterParts[] = 'Status: ' . ucfirst($status);
        if ($installmentNo) $filterParts[] = 'Installment: ' . $installmentNo;
        if ($searchStudent) $filterParts[] = 'Search: ' . $searchStudent;
        $filterSummary = !empty($filterParts) ? implode(' | ', $filterParts) : 'All Records';

        $reportTitle   = 'Fee Collections Report';
        $exportHeaders = [];
        $exportRows    = [];
        $exportTotals  = [];
        $kpis          = [];
        $reportData    = [];

        // ─── TAB 1: STUDENT FEE COLLECTION ───
        if ($activeTab === 'student_fee_collection') {
            $reportTitle = 'Student Fee Collection Report';

            $feeQuery = StudentFee::where('school_id', $schoolId)
                ->whereHas('student', $studentFilter)
                ->with(['student.class', 'student.section', 'feeSchedule', 'component']);

            if ($feeScheduleId) $feeQuery->where('fee_schedule_id', $feeScheduleId);
            if ($feeComponentId) $feeQuery->where('fee_component_id', $feeComponentId);
            if ($installmentNo) $feeQuery->where('installment_no', $installmentNo);
            if ($dateFrom) $feeQuery->whereDate('due_date', '>=', $dateFrom);
            if ($dateTo) $feeQuery->whereDate('due_date', '<=', $dateTo);
            if ($status) {
                if ($status === 'paid') $feeQuery->where('status', 'paid');
                elseif ($status === 'partial') $feeQuery->where('paid_amount', '>', 0)->where('status', '!=', 'paid');
                elseif ($status === 'pending') $feeQuery->where('paid_amount', 0)->where('status', '!=', 'paid');
            }

            if ($paymentMode) {
                $matchingStudentIds = FeeReceipt::where('school_id', $schoolId)->where('payment_mode', $paymentMode)->pluck('student_id');
                $feeQuery->whereIn('student_id', $matchingStudentIds);
            }

            $rawFees = $feeQuery->orderBy('due_date', 'desc')->get();

            // Fetch receipts keyed by student_id
            $receiptsMap = FeeReceipt::where('school_id', $schoolId)
                ->whereIn('student_id', $rawFees->pluck('student_id')->unique())
                ->get()
                ->groupBy('student_id');

            $totAssigned = 0; $totPaid = 0; $totDiscount = 0; $totFine = 0; $totPending = 0;

            foreach ($rawFees as $f) {
                $st = $f->student;
                if (!$st) continue;

                $assigned  = (float) $f->amount;
                $disc      = (float) $f->instant_discount_amount;
                $fine      = (float) $f->fine_amount_applied;
                $paid      = (float) $f->paid_amount;
                $due       = max(0.00, $assigned + $fine - $disc - $paid);

                $totAssigned += $assigned;
                $totDiscount += $disc;
                $totFine     += $fine;
                $totPaid     += $paid;
                $totPending  += $due;

                $stReceipts = $receiptsMap->get($f->student_id, collect());
                $latestReceipt = $stReceipts->sortByDesc('payment_date')->first();

                $statusLabel = 'Pending';
                $statusBadge = 'danger';
                if ($paid >= ($assigned + $fine - $disc) && ($assigned + $fine - $disc) > 0) {
                    $statusLabel = 'Paid';
                    $statusBadge = 'success';
                } elseif ($paid > 0) {
                    $statusLabel = 'Partial';
                    $statusBadge = 'warning';
                }

                $row = [
                    'id'               => $f->id,
                    'student_name'     => $st->full_name,
                    'admission_number' => $st->admission_number,
                    'class_section'    => ($st->class?->name ?? '—') . ($st->section ? ' - ' . $st->section->name : ''),
                    'roll_number'      => $st->roll_number ?? '—',
                    'fee_structure'    => $f->feeSchedule?->name ?? ($f->transport_fee_schedule_id ? 'Transport Fee' : 'General Fee'),
                    'installment'      => $f->installment_name,
                    'component'        => $f->component?->name ?? ($f->transport_fee_schedule_id ? 'Transport' : 'Tuition/General'),
                    'actual_amount'    => '₹' . number_format($assigned, 2),
                    'discount'         => '₹' . number_format($disc, 2),
                    'fine'             => '₹' . number_format($fine, 2),
                    'paid_amount'      => '₹' . number_format($paid, 2),
                    'balance_due'      => '₹' . number_format($due, 2),
                    'payment_date'     => $latestReceipt?->payment_date ? \Carbon\Carbon::parse($latestReceipt->payment_date)->format('d M Y') : '—',
                    'payment_mode'     => $latestReceipt?->payment_mode ? ucfirst($latestReceipt->payment_mode) : '—',
                    'receipt_number'   => $latestReceipt?->receipt_number ?? '—',
                    'status'           => $statusLabel,
                    'status_badge'     => $statusBadge,
                    'collected_by'     => 'Admin/System',
                    'academic_session' => $sessionName,
                ];

                $exportRows[] = $row;
            }

            $kpis = [
                ['label' => 'Total Assigned',  'value' => '₹' . number_format($totAssigned, 2)],
                ['label' => 'Total Collected', 'value' => '₹' . number_format($totPaid, 2)],
                ['label' => 'Total Discount',  'value' => '₹' . number_format($totDiscount, 2)],
                ['label' => 'Total Fine',      'value' => '₹' . number_format($totFine, 2)],
                ['label' => 'Total Pending',   'value' => '₹' . number_format($totPending, 2)],
            ];

            $exportHeaders = [
                ['title' => 'Student Name',     'key' => 'student_name'],
                ['title' => 'Adm No',           'key' => 'admission_number'],
                ['title' => 'Class & Sec',      'key' => 'class_section'],
                ['title' => 'Installment',      'key' => 'installment'],
                ['title' => 'Structure',        'key' => 'fee_structure'],
                ['title' => 'Assigned',         'key' => 'actual_amount', 'align' => 'right'],
                ['title' => 'Discount',         'key' => 'discount',      'align' => 'right'],
                ['title' => 'Fine',             'key' => 'fine',          'align' => 'right'],
                ['title' => 'Paid Amount',      'key' => 'paid_amount',   'align' => 'right'],
                ['title' => 'Balance Due',      'key' => 'balance_due',   'align' => 'right'],
                ['title' => 'Receipt No',       'key' => 'receipt_number'],
                ['title' => 'Mode',             'key' => 'payment_mode'],
                ['title' => 'Status',           'key' => 'status',        'type'  => 'badge'],
            ];

            $exportTotals = [
                'student_name'  => 'GRAND TOTALS (' . count($exportRows) . ' Records)',
                'actual_amount' => '₹' . number_format($totAssigned, 2),
                'discount'      => '₹' . number_format($totDiscount, 2),
                'fine'          => '₹' . number_format($totFine, 2),
                'paid_amount'   => '₹' . number_format($totPaid, 2),
                'balance_due'   => '₹' . number_format($totPending, 2),
            ];
        }

        // ─── TAB 2: DAILY FEE COLLECTION ───
        elseif ($activeTab === 'daily_fee_collection') {
            $reportTitle = 'Daily Fee Collection Report';

            $receiptQuery = FeeReceipt::where('school_id', $schoolId)
                ->whereHas('student', $studentFilter)
                ->whereBetween('payment_date', [$dateFrom, $dateTo])
                ->with(['student.class', 'student.section']);

            if ($paymentMode) $receiptQuery->where('payment_mode', $paymentMode);

            $receipts = $receiptQuery->orderBy('payment_date', 'desc')->get();

            $groupedByDate = $receipts->groupBy(fn($r) => \Carbon\Carbon::parse($r->payment_date)->format('Y-m-d'));

            $totCollections = 0; $totReceiptsCount = 0;
            $totCash = 0; $totUpi = 0; $totCard = 0; $totBank = 0; $totCheque = 0; $totDisc = 0;

            foreach ($groupedByDate as $dateStr => $dayReceipts) {
                $dayTotal   = $dayReceipts->sum('amount_paid');
                $dayCount   = $dayReceipts->count();
                $dayDisc    = $dayReceipts->sum('discount_amount');

                $cashAmt   = $dayReceipts->where('payment_mode', 'cash')->sum('amount_paid');
                $upiAmt    = $dayReceipts->whereIn('payment_mode', ['upi', 'online', 'gpay', 'phonepe'])->sum('amount_paid');
                $cardAmt   = $dayReceipts->where('payment_mode', 'card')->sum('amount_paid');
                $bankAmt   = $dayReceipts->whereIn('payment_mode', ['bank_transfer', 'neft', 'rtgs', 'net_banking'])->sum('amount_paid');
                $chequeAmt = $dayReceipts->where('payment_mode', 'cheque')->sum('amount_paid');

                $totCollections   += $dayTotal;
                $totReceiptsCount += $dayCount;
                $totCash          += $cashAmt;
                $totUpi           += $upiAmt;
                $totCard          += $cardAmt;
                $totBank          += $bankAmt;
                $totCheque        += $chequeAmt;
                $totDisc          += $dayDisc;

                $exportRows[] = [
                    'date'               => \Carbon\Carbon::parse($dateStr)->format('d M Y (D)'),
                    'total_collections'  => '₹' . number_format($dayTotal, 2),
                    'receipts_count'     => $dayCount . ' Receipts',
                    'cash'               => '₹' . number_format($cashAmt, 2),
                    'upi'                => '₹' . number_format($upiAmt, 2),
                    'card'               => '₹' . number_format($cardAmt, 2),
                    'bank'               => '₹' . number_format($bankAmt, 2),
                    'cheque'             => '₹' . number_format($chequeAmt, 2),
                    'discounts'          => '₹' . number_format($dayDisc, 2),
                    'net_collection'     => '₹' . number_format($dayTotal, 2),
                ];
            }

            $kpis = [
                ['label' => 'Total Collection',  'value' => '₹' . number_format($totCollections, 2)],
                ['label' => 'Total Transactions','value' => number_format($totReceiptsCount)],
                ['label' => 'Cash Collections',  'value' => '₹' . number_format($totCash, 2)],
                ['label' => 'UPI/Online',        'value' => '₹' . number_format($totUpi, 2)],
                ['label' => 'Cheque Collections','value' => '₹' . number_format($totCheque, 2)],
            ];

            $exportHeaders = [
                ['title' => 'Date',             'key' => 'date'],
                ['title' => 'Receipts Count',   'key' => 'receipts_count',   'align' => 'center'],
                ['title' => 'Cash',             'key' => 'cash',             'align' => 'right'],
                ['title' => 'UPI / Online',     'key' => 'upi',              'align' => 'right'],
                ['title' => 'Card',             'key' => 'card',             'align' => 'right'],
                ['title' => 'Bank Transfer',    'key' => 'bank',             'align' => 'right'],
                ['title' => 'Cheque',           'key' => 'cheque',           'align' => 'right'],
                ['title' => 'Discounts',        'key' => 'discounts',        'align' => 'right'],
                ['title' => 'Total Collection', 'key' => 'total_collections','align' => 'right'],
            ];

            $exportTotals = [
                'date'              => 'TOTALS (' . count($groupedByDate) . ' Days)',
                'receipts_count'    => $totReceiptsCount . ' Receipts',
                'cash'              => '₹' . number_format($totCash, 2),
                'upi'               => '₹' . number_format($totUpi, 2),
                'card'              => '₹' . number_format($totCard, 2),
                'bank'              => '₹' . number_format($totBank, 2),
                'cheque'            => '₹' . number_format($totCheque, 2),
                'discounts'         => '₹' . number_format($totDisc, 2),
                'total_collections' => '₹' . number_format($totCollections, 2),
            ];
        }

        // ─── TAB 3: INSTALLMENT WISE DUES ───
        elseif ($activeTab === 'installment_wise_dues') {
            $reportTitle = 'Installment Wise Dues Report';

            $feeQuery = StudentFee::where('school_id', $schoolId)
                ->whereHas('student', $studentFilter)
                ->with(['student.class', 'student.section']);

            if ($installmentNo) $feeQuery->where('installment_no', $installmentNo);
            if ($status === 'overdue') {
                $feeQuery->where('status', '!=', 'paid')->whereDate('due_date', '<', now());
            } elseif ($status === 'pending') {
                $feeQuery->where('status', '!=', 'paid');
            }

            $rawFees = $feeQuery->orderBy('installment_no')->orderBy('due_date')->get();

            $totAssigned = 0; $totPaid = 0; $totRemaining = 0; $totFine = 0; $overdueCount = 0;

            foreach ($rawFees as $f) {
                $st = $f->student;
                if (!$st) continue;

                $assigned  = (float) $f->amount;
                $disc      = (float) $f->instant_discount_amount;
                $fine      = (float) $f->fine_amount_applied;
                $paid      = (float) $f->paid_amount;
                $remaining = max(0.00, $assigned + $fine - $disc - $paid);

                $dueDate    = $f->due_date ? \Carbon\Carbon::parse($f->due_date) : null;
                $daysOverdue = ($dueDate && $dueDate->isPast() && $remaining > 0) ? now()->diffInDays($dueDate) : 0;

                if ($daysOverdue > 0) $overdueCount++;

                $totAssigned  += $assigned;
                $totPaid      += $paid;
                $totRemaining += $remaining;
                $totFine      += $fine;

                $statusLabel = 'Pending';
                $statusBadge = 'danger';
                if ($remaining <= 0) {
                    $statusLabel = 'Paid';
                    $statusBadge = 'success';
                } elseif ($daysOverdue > 0) {
                    $statusLabel = 'Overdue (' . $daysOverdue . 'd)';
                    $statusBadge = 'danger';
                } elseif ($paid > 0) {
                    $statusLabel = 'Partial';
                    $statusBadge = 'warning';
                }

                $exportRows[] = [
                    'student_name'     => $st->full_name,
                    'admission_number' => $st->admission_number,
                    'class_section'    => ($st->class?->name ?? '—') . ($st->section ? ' - ' . $st->section->name : ''),
                    'installment_name' => $f->installment_name,
                    'total_installment'=> '₹' . number_format($assigned, 2),
                    'paid_amount'      => '₹' . number_format($paid, 2),
                    'remaining_due'    => '₹' . number_format($remaining, 2),
                    'due_date'         => $dueDate ? $dueDate->format('d M Y') : '—',
                    'days_overdue'     => $daysOverdue > 0 ? $daysOverdue . ' Days' : '0',
                    'fine_applied'     => '₹' . number_format($fine, 2),
                    'status'           => $statusLabel,
                    'status_badge'     => $statusBadge,
                ];
            }

            $kpis = [
                ['label' => 'Total Installments Assigned', 'value' => '₹' . number_format($totAssigned, 2)],
                ['label' => 'Total Paid Amount',           'value' => '₹' . number_format($totPaid, 2)],
                ['label' => 'Total Remaining Dues',        'value' => '₹' . number_format($totRemaining, 2)],
                ['label' => 'Total Fine Applied',          'value' => '₹' . number_format($totFine, 2)],
                ['label' => 'Overdue Items Count',         'value' => number_format($overdueCount)],
            ];

            $exportHeaders = [
                ['title' => 'Student Name',         'key' => 'student_name'],
                ['title' => 'Adm No',               'key' => 'admission_number'],
                ['title' => 'Class & Sec',          'key' => 'class_section'],
                ['title' => 'Installment',          'key' => 'installment_name'],
                ['title' => 'Due Date',             'key' => 'due_date'],
                ['title' => 'Days Overdue',         'key' => 'days_overdue',   'align' => 'center'],
                ['title' => 'Assigned',             'key' => 'total_installment', 'align' => 'right'],
                ['title' => 'Paid',                 'key' => 'paid_amount',    'align' => 'right'],
                ['title' => 'Fine',                 'key' => 'fine_applied',   'align' => 'right'],
                ['title' => 'Remaining Due',        'key' => 'remaining_due',  'align' => 'right'],
                ['title' => 'Status',               'key' => 'status',         'type'  => 'badge'],
            ];

            $exportTotals = [
                'student_name'      => 'TOTALS (' . count($exportRows) . ' Records)',
                'total_installment' => '₹' . number_format($totAssigned, 2),
                'paid_amount'       => '₹' . number_format($totPaid, 2),
                'fine_applied'      => '₹' . number_format($totFine, 2),
                'remaining_due'     => '₹' . number_format($totRemaining, 2),
            ];
        }

        // ─── TAB 4: COMPONENT WISE REPORT ───
        elseif ($activeTab === 'component_wise_report') {
            $reportTitle = 'Component Wise Fee Report';

            $allFees = StudentFee::where('school_id', $schoolId)
                ->whereHas('student', $studentFilter)
                ->with(['component'])
                ->get();

            $componentsMap = [];
            foreach ($allFees as $fee) {
                $compName = $fee->component?->name ?? ($fee->transport_fee_schedule_id ? 'Transport Fee' : 'General / Misc Fee');
                if (!isset($componentsMap[$compName])) {
                    $componentsMap[$compName] = [
                        'assigned'  => 0,
                        'collected' => 0,
                        'discount'  => 0,
                        'fine'      => 0,
                    ];
                }
                $componentsMap[$compName]['assigned']  += (float) $fee->amount;
                $componentsMap[$compName]['collected'] += (float) $fee->paid_amount;
                $componentsMap[$compName]['discount']  += (float) $fee->instant_discount_amount;
                $componentsMap[$compName]['fine']      += (float) $fee->fine_amount_applied;
            }

            $totAssigned = 0; $totCollected = 0; $totPending = 0; $totDiscount = 0; $totFine = 0;

            foreach ($componentsMap as $name => $vals) {
                $assigned  = $vals['assigned'];
                $collected = $vals['collected'];
                $discount  = $vals['discount'];
                $fine      = $vals['fine'];
                $pending   = max(0.00, $assigned + $fine - $discount - $collected);

                $pct = ($assigned - $discount) > 0 ? round(($collected / ($assigned - $discount)) * 100, 1) : 0;

                $totAssigned  += $assigned;
                $totCollected += $collected;
                $totPending   += $pending;
                $totDiscount  += $discount;
                $totFine      += $fine;

                $exportRows[] = [
                    'component_name'   => $name,
                    'assigned_amount'  => '₹' . number_format($assigned, 2),
                    'collected_amount' => '₹' . number_format($collected, 2),
                    'pending_amount'   => '₹' . number_format($pending, 2),
                    'discount_amount'  => '₹' . number_format($discount, 2),
                    'fine_amount'      => '₹' . number_format($fine, 2),
                    'collection_pct'   => $pct . '%',
                ];
            }

            $overallPct = ($totAssigned - $totDiscount) > 0 ? round(($totCollected / ($totAssigned - $totDiscount)) * 100, 1) : 0;

            $kpis = [
                ['label' => 'Total Components',     'value' => count($exportRows)],
                ['label' => 'Total Assigned',       'value' => '₹' . number_format($totAssigned, 2)],
                ['label' => 'Total Collected',      'value' => '₹' . number_format($totCollected, 2)],
                ['label' => 'Total Pending',        'value' => '₹' . number_format($totPending, 2)],
                ['label' => 'Collection Rate',      'value' => $overallPct . '%'],
            ];

            $exportHeaders = [
                ['title' => 'Fee Component Name',  'key' => 'component_name'],
                ['title' => 'Assigned Amount',     'key' => 'assigned_amount',  'align' => 'right'],
                ['title' => 'Collected Amount',    'key' => 'collected_amount', 'align' => 'right'],
                ['title' => 'Discounts Allowed',   'key' => 'discount_amount',  'align' => 'right'],
                ['title' => 'Fine Applied',        'key' => 'fine_amount',      'align' => 'right'],
                ['title' => 'Pending Amount',      'key' => 'pending_amount',   'align' => 'right'],
                ['title' => 'Collection Rate %',   'key' => 'collection_pct',   'align' => 'center'],
            ];

            $exportTotals = [
                'component_name'   => 'TOTALS',
                'assigned_amount'  => '₹' . number_format($totAssigned, 2),
                'collected_amount' => '₹' . number_format($totCollected, 2),
                'discount_amount'  => '₹' . number_format($totDiscount, 2),
                'fine_amount'      => '₹' . number_format($totFine, 2),
                'pending_amount'   => '₹' . number_format($totPending, 2),
                'collection_pct'   => $overallPct . '%',
            ];
        }

        // ─── TAB 5: CLASS WISE FEE REPORT ───
        elseif ($activeTab === 'class_wise_fee_report') {
            $reportTitle = 'Class-Wise Fee Report';

            $targetClasses = $classId ? $classes->where('id', $classId) : $classes;

            $totStudents = 0; $totAssigned = 0; $totCollected = 0; $totPending = 0; $totDiscount = 0; $totFine = 0;

            foreach ($targetClasses as $cls) {
                $studentsQuery = Student::where('school_id', $schoolId)->where('class_id', $cls->id);
                if ($selectedSessionId) $studentsQuery->where('academic_session_id', $selectedSessionId);
                if ($sectionId) $studentsQuery->where('section_id', $sectionId);

                $stCount = $studentsQuery->count();

                $classFees = StudentFee::where('school_id', $schoolId)
                    ->whereHas('student', function($sq) use ($cls, $selectedSessionId, $sectionId) {
                        $sq->where('class_id', $cls->id);
                        if ($selectedSessionId) $sq->where('academic_session_id', $selectedSessionId);
                        if ($sectionId) $sq->where('section_id', $sectionId);
                    })
                    ->get();

                $assigned  = (float) $classFees->sum('amount');
                $collected = (float) $classFees->sum('paid_amount');
                $discount  = (float) $classFees->sum('instant_discount_amount');
                $fine      = (float) $classFees->sum('fine_amount_applied');
                $pending   = max(0.00, $assigned + $fine - $discount - $collected);
                $pct       = ($assigned - $discount) > 0 ? round(($collected / ($assigned - $discount)) * 100, 1) : 0;

                $totStudents  += $stCount;
                $totAssigned  += $assigned;
                $totCollected += $collected;
                $totPending   += $pending;
                $totDiscount  += $discount;
                $totFine      += $fine;

                $exportRows[] = [
                    'class_name'       => $cls->name,
                    'total_students'   => $stCount . ' Students',
                    'fee_assigned'     => '₹' . number_format($assigned, 2),
                    'fee_collected'    => '₹' . number_format($collected, 2),
                    'discount_amount'  => '₹' . number_format($discount, 2),
                    'fine_amount'      => '₹' . number_format($fine, 2),
                    'pending_balance'  => '₹' . number_format($pending, 2),
                    'collection_pct'   => $pct . '%',
                ];
            }

            $overallPct = ($totAssigned - $totDiscount) > 0 ? round(($totCollected / ($totAssigned - $totDiscount)) * 100, 1) : 0;

            $kpis = [
                ['label' => 'Total Classes',     'value' => count($exportRows)],
                ['label' => 'Total Students',    'value' => number_format($totStudents)],
                ['label' => 'Total Assigned',    'value' => '₹' . number_format($totAssigned, 2)],
                ['label' => 'Total Collected',   'value' => '₹' . number_format($totCollected, 2)],
                ['label' => 'Total Pending',     'value' => '₹' . number_format($totPending, 2)],
            ];

            $exportHeaders = [
                ['title' => 'Class Name',          'key' => 'class_name'],
                ['title' => 'Total Students',      'key' => 'total_students',   'align' => 'center'],
                ['title' => 'Assigned Amount',     'key' => 'fee_assigned',     'align' => 'right'],
                ['title' => 'Collected Amount',    'key' => 'fee_collected',    'align' => 'right'],
                ['title' => 'Discounts Allowed',   'key' => 'discount_amount',  'align' => 'right'],
                ['title' => 'Fine Applied',        'key' => 'fine_amount',      'align' => 'right'],
                ['title' => 'Pending Balance',     'key' => 'pending_balance',  'align' => 'right'],
                ['title' => 'Collection Rate %',   'key' => 'collection_pct',   'align' => 'center'],
            ];

            $exportTotals = [
                'class_name'       => 'TOTALS',
                'total_students'   => number_format($totStudents) . ' Students',
                'fee_assigned'     => '₹' . number_format($totAssigned, 2),
                'fee_collected'    => '₹' . number_format($totCollected, 2),
                'discount_amount'  => '₹' . number_format($totDiscount, 2),
                'fine_amount'      => '₹' . number_format($totFine, 2),
                'pending_balance'  => '₹' . number_format($totPending, 2),
                'collection_pct'   => $overallPct . '%',
            ];
        }

        // ─── TAB 6: PENDING CHEQUES ───
        elseif ($activeTab === 'pending_cheques') {
            $reportTitle = 'Pending Cheques Management Report';

            $chequeQuery = PendingCheque::where('school_id', $schoolId)
                ->whereHas('student', $studentFilter)
                ->with(['student.class', 'student.section']);

            if ($status) $chequeQuery->where('status', $status);
            if ($dateFrom) $chequeQuery->whereDate('cheque_date', '>=', $dateFrom);
            if ($dateTo) $chequeQuery->whereDate('cheque_date', '<=', $dateTo);

            $cheques = $chequeQuery->orderBy('cheque_date', 'desc')->get();

            $totAmt = 0; $pendingAmt = 0; $clearedAmt = 0; $bouncedAmt = 0;

            foreach ($cheques as $c) {
                $st = $c->student;
                if (!$st) continue;

                $amt = (float) $c->amount;
                $totAmt += $amt;

                $stLabel = ucfirst($c->status ?? 'pending');
                $stBadge = 'warning';

                if ($c->status === 'cleared') {
                    $clearedAmt += $amt;
                    $stBadge = 'success';
                } elseif ($c->status === 'bounced') {
                    $bouncedAmt += $amt;
                    $stBadge = 'danger';
                } elseif ($c->status === 'cancelled') {
                    $stBadge = 'slate';
                } else {
                    $pendingAmt += $amt;
                }

                $exportRows[] = [
                    'student_name'     => $st->full_name,
                    'admission_number' => $st->admission_number,
                    'receipt_number'   => $c->receipt_number ?? '—',
                    'cheque_number'    => $c->cheque_number,
                    'bank_name'        => $c->bank_name . ($c->branch ? ' (' . $c->branch . ')' : ''),
                    'amount'           => '₹' . number_format($amt, 2),
                    'collection_date'  => $c->cheque_date ? \Carbon\Carbon::parse($c->cheque_date)->format('d M Y') : '—',
                    'status'           => $stLabel,
                    'status_badge'     => $stBadge,
                    'cleared_date'     => $c->status_changed_at ? \Carbon\Carbon::parse($c->status_changed_at)->format('d M Y') : '—',
                    'remarks'          => $c->status_remarks ?? '—',
                ];
            }

            $kpis = [
                ['label' => 'Total Cheques Logged',  'value' => count($exportRows)],
                ['label' => 'Pending Cheques Amt',   'value' => '₹' . number_format($pendingAmt, 2)],
                ['label' => 'Cleared Cheques Amt',   'value' => '₹' . number_format($clearedAmt, 2)],
                ['label' => 'Bounced Cheques Amt',   'value' => '₹' . number_format($bouncedAmt, 2)],
            ];

            $exportHeaders = [
                ['title' => 'Student Name',     'key' => 'student_name'],
                ['title' => 'Adm No',           'key' => 'admission_number'],
                ['title' => 'Receipt No',       'key' => 'receipt_number'],
                ['title' => 'Cheque No',        'key' => 'cheque_number'],
                ['title' => 'Bank & Branch',    'key' => 'bank_name'],
                ['title' => 'Cheque Date',      'key' => 'collection_date'],
                ['title' => 'Amount',           'key' => 'amount',           'align' => 'right'],
                ['title' => 'Status',           'key' => 'status',           'type'  => 'badge'],
                ['title' => 'Cleared/Status Date','key' => 'cleared_date'],
                ['title' => 'Remarks',          'key' => 'remarks'],
            ];

            $exportTotals = [
                'student_name' => 'TOTALS (' . count($exportRows) . ' Cheques)',
                'amount'       => '₹' . number_format($totAmt, 2),
            ];
        }

        return [
            'activeTab'         => $activeTab,
            'academicSessions'  => $academicSessions,
            'selectedSessionId' => $selectedSessionId,
            'sessionName'       => $sessionName,
            'classes'           => $classes,
            'classId'           => $classId,
            'sections'          => $sections,
            'sectionId'         => $sectionId,
            'dateFrom'          => $dateFrom,
            'dateTo'            => $dateTo,
            'installmentNo'     => $installmentNo,
            'paymentMode'       => $paymentMode,
            'status'            => $status,
            'feeScheduleId'     => $feeScheduleId,
            'feeComponentId'    => $feeComponentId,
            'searchStudent'     => $searchStudent,
            'feeSchedules'      => $feeSchedules,
            'feeComponents'     => $feeComponents,
            'paymentModes'      => $paymentModes,
            'installments'      => $installments,
            'reportTitle'       => $reportTitle,
            'filterSummary'     => $filterSummary,
            'kpis'              => $kpis,
            'exportHeaders'     => $exportHeaders,
            'exportRows'        => $exportRows,
            'exportTotals'      => $exportTotals,
        ];
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

    // ─── Export Student Report PDF ──────────────────────────────────────────
    public function exportStudentReportPdf(Request $request)
    {
        @ini_set('memory_limit', '512M');
        @set_time_limit(300);

        $schoolId  = $this->schoolId();
        $classId   = $request->get('class_id', '');
        $sectionId = $request->get('section_id', '');
        $gender    = $request->get('gender', '');
        $status    = $request->get('status', 'active');
        $dateFrom  = $request->get('date_from', now()->startOfYear()->format('Y-m-d'));
        $dateTo    = $request->get('date_to', now()->format('Y-m-d'));

        $school = School::find($schoolId);
        $currentSession = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first();
        $sessionName = $currentSession ? $currentSession->name : 'Current Session';

        $query = Student::where('school_id', $schoolId)
            ->with(['class', 'section', 'category', 'house', 'academicSession', 'documents'])
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);

        if ($classId)   $query->where('class_id', $classId);
        if ($sectionId) $query->where('section_id', $sectionId);
        if ($gender)    $query->where('gender', $gender);
        if ($status)    $query->where('is_active', $status === 'active' ? 1 : 0);

        $totalFilteredCount = (clone $query)->count();

        // Performance safety cap for PDF (500 records per PDF file to ensure fast generation under 3 seconds & zero 500 errors)
        $limit = (int) $request->get('limit', 500);
        $students = $query->orderBy('first_name')->take($limit)->get();

        $studentsWithDocsCount = $students->filter(fn($s) => $s->documents->count() > 0)->count();
        $totalDocsUploaded     = $students->sum(fn($s) => $s->documents->count());

        $classes  = SchoolClass::where('school_id', $schoolId)->get();
        $sections = Section::where('school_id', $schoolId)->get();

        $filterParts = [];
        if ($classId && $cls = $classes->firstWhere('id', $classId)) $filterParts[] = 'Class: ' . $cls->name;
        if ($sectionId && $sec = $sections->firstWhere('id', $sectionId)) $filterParts[] = 'Section: ' . $sec->name;
        if ($gender) $filterParts[] = 'Gender: ' . ucfirst($gender);
        if ($status) $filterParts[] = 'Status: ' . ucfirst($status);
        if ($totalFilteredCount > $limit) {
            $filterParts[] = "Showing Top {$limit} of " . number_format($totalFilteredCount) . " Records (Use Excel/CSV for full download)";
        }
        $filterSummary = !empty($filterParts) ? implode(' | ', $filterParts) : 'All Records';

        $kpis = [
            ['label' => 'Total Records',    'value' => number_format($totalFilteredCount) . ($totalFilteredCount > $limit ? " (Pdf: {$limit})" : '')],
            ['label' => 'Active Students',  'value' => number_format($students->where('is_active', 1)->count())],
            ['label' => 'Inactive',         'value' => number_format($students->where('is_active', 0)->count())],
            ['label' => 'Classes Count',    'value' => number_format($students->pluck('class_id')->unique()->filter()->count())],
            ['label' => 'Students w/ Docs', 'value' => number_format($studentsWithDocsCount) . ' / ' . number_format($students->count())],
            ['label' => 'Total Files Uploaded', 'value' => number_format($totalDocsUploaded)],
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('school.reports.pdf.student-report-pdf', [
            'school'                => $school,
            'reportTitle'           => 'Comprehensive Student Demographics & Document Report',
            'sessionName'           => $sessionName,
            'dateFrom'              => $dateFrom,
            'dateTo'                => $dateTo,
            'filterSummary'         => $filterSummary,
            'kpis'                  => $kpis,
            'students'              => $students,
            'studentsWithDocsCount' => $studentsWithDocsCount,
            'totalDocsUploaded'     => $totalDocsUploaded,
        ]);

        $pdf->setPaper('a4', 'landscape');
        $fileName = 'Student_Report_' . date('Ymd_His') . '.pdf';
        return $pdf->download($fileName);
    }

    // ─── Export Attendance Report PDF ───────────────────────────────────────
    public function exportAttendanceReportPdf(Request $request)
    {
        $schoolId  = $this->schoolId();
        $classId   = $request->get('class_id', '');
        $sectionId = $request->get('section_id', '');
        $dateFrom  = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo    = $request->get('date_to', now()->format('Y-m-d'));

        $school = School::find($schoolId);
        $currentSession = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first();
        $sessionName = $currentSession ? $currentSession->name : 'Current Session';

        $classes  = SchoolClass::where('school_id', $schoolId)->orderBy('name')->get();
        $sections = Section::where('school_id', $schoolId)->orderBy('name')->get();

        $baseQuery = StudentAttendance::where('school_id', $schoolId)
            ->whereBetween('date', [$dateFrom, $dateTo]);
        if ($classId)   $baseQuery->where('class_id', $classId);
        if ($sectionId) $baseQuery->where('section_id', $sectionId);

        $present = (clone $baseQuery)->where('status', 'present')->count();
        $absent  = (clone $baseQuery)->where('status', 'absent')->count();
        $late    = (clone $baseQuery)->where('status', 'late')->count();
        $leave   = (clone $baseQuery)->where('status', 'leave')->count();
        $total   = $present + $absent + $late + $leave;
        $rate    = $total > 0 ? round(($present / $total) * 100, 1) : 0;

        $classAttendance = $classes->map(function ($cls) use ($schoolId, $dateFrom, $dateTo) {
            $tot   = StudentAttendance::where('school_id', $schoolId)->where('class_id', $cls->id)->whereBetween('date', [$dateFrom, $dateTo])->count();
            $pres  = StudentAttendance::where('school_id', $schoolId)->where('class_id', $cls->id)->whereBetween('date', [$dateFrom, $dateTo])->where('status', 'present')->count();
            return [
                'class'   => $cls->name,
                'total'   => $tot,
                'present' => $pres,
                'rate'    => $tot > 0 ? round(($pres / $tot) * 100, 1) : 0,
            ];
        });

        $stQuery = Student::where('school_id', $schoolId)->where('is_active', 1)->with(['class', 'section']);
        if ($classId) $stQuery->where('class_id', $classId);
        if ($sectionId) $stQuery->where('section_id', $sectionId);
        $studentsList = $stQuery->orderBy('first_name')->take(150)->get();

        $studentDetails = [];
        foreach ($studentsList as $st) {
            $stLogs = StudentAttendance::where('school_id', $schoolId)->where('student_id', $st->id)->whereBetween('date', [$dateFrom, $dateTo])->get();
            $stTot  = $stLogs->count();
            if ($stTot === 0) continue;
            $stPres = $stLogs->where('status', 'present')->count();
            $stAbs  = $stLogs->where('status', 'absent')->count();
            $stLate = $stLogs->whereIn('status', ['late', 'leave'])->count();
            $stRate = round(($stPres / $stTot) * 100, 1);

            $studentDetails[] = [
                'admission_number' => $st->admission_number,
                'name'             => $st->full_name,
                'class_section'    => ($st->class?->name ?? '—') . ($st->section ? ' - ' . $st->section->name : ''),
                'working_days'     => $stTot,
                'present'          => $stPres,
                'absent'           => $stAbs,
                'late_leave'       => $stLate,
                'rate'             => $stRate,
            ];
        }

        $filterParts = [];
        if ($classId && $cls = $classes->firstWhere('id', $classId)) $filterParts[] = 'Class: ' . $cls->name;
        if ($sectionId && $sec = $sections->firstWhere('id', $sectionId)) $filterParts[] = 'Section: ' . $sec->name;
        $filterSummary = !empty($filterParts) ? implode(' | ', $filterParts) : 'All Classes';

        $kpis = [
            ['label' => 'Total Present',  'value' => number_format($present)],
            ['label' => 'Total Absent',   'value' => number_format($absent)],
            ['label' => 'Total Late',     'value' => number_format($late)],
            ['label' => 'On Leave',       'value' => number_format($leave)],
            ['label' => 'Attendance Rate','value' => $rate . '%'],
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('school.reports.pdf.attendance-report-pdf', [
            'school'          => $school,
            'reportTitle'     => 'Attendance Summary & Analytics Report',
            'sessionName'     => $sessionName,
            'dateFrom'        => $dateFrom,
            'dateTo'          => $dateTo,
            'filterSummary'   => $filterSummary,
            'kpis'            => $kpis,
            'classAttendance' => $classAttendance,
            'studentDetails'  => $studentDetails,
        ]);

        $pdf->setPaper('a4', 'landscape');
        $fileName = 'Attendance_Report_' . date('Ymd_His') . '.pdf';
        return $pdf->download($fileName);
    }

    // ─── Export Sibling Report PDF ──────────────────────────────────────────
    public function exportSiblingReportPdf(Request $request)
    {
        $schoolId = $this->schoolId();
        $classId  = $request->get('class_id', '');
        $dateFrom = $request->get('date_from', now()->startOfYear()->format('Y-m-d'));
        $dateTo   = $request->get('date_to', now()->format('Y-m-d'));

        $school = School::find($schoolId);
        $currentSession = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first();
        $sessionName = $currentSession ? $currentSession->name : 'Current Session';

        $classes = SchoolClass::where('school_id', $schoolId)->orderBy('name')->get();

        $query = Student::where('school_id', $schoolId)
            ->where('is_active', 1)
            ->whereNotNull('guardian_phone')
            ->with(['class', 'section']);

        if ($classId) $query->where('class_id', $classId);

        $students = $query->get();

        $siblingGroups = $students->groupBy('guardian_phone')
            ->filter(fn($group) => $group->count() > 1)
            ->values();

        $totalSiblingStudents = $siblingGroups->sum(fn($g) => $g->count());
        $totalFamilies        = $siblingGroups->count();
        $twoSiblings          = $siblingGroups->filter(fn($g) => $g->count() === 2)->count();
        $threePlusSiblings    = $siblingGroups->filter(fn($g) => $g->count() >= 3)->count();

        $filterParts = [];
        if ($classId && $cls = $classes->firstWhere('id', $classId)) $filterParts[] = 'Class: ' . $cls->name;
        $filterSummary = !empty($filterParts) ? implode(' | ', $filterParts) : 'All Classes';

        $kpis = [
            ['label' => 'Sibling Students', 'value' => number_format($totalSiblingStudents)],
            ['label' => 'Sibling Families', 'value' => number_format($totalFamilies)],
            ['label' => '2-Sibling Families','value' => number_format($twoSiblings)],
            ['label' => '3+ Sibling Families','value' => number_format($threePlusSiblings)],
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('school.reports.pdf.sibling-report-pdf', [
            'school'        => $school,
            'reportTitle'   => 'Sibling Family Relationship Report',
            'sessionName'   => $sessionName,
            'dateFrom'      => $dateFrom,
            'dateTo'        => $dateTo,
            'filterSummary' => $filterSummary,
            'kpis'          => $kpis,
            'siblingGroups' => $siblingGroups,
        ]);

        $pdf->setPaper('a4', 'landscape');
        $fileName = 'Sibling_Report_' . date('Ymd_His') . '.pdf';
        return $pdf->download($fileName);
    }

    // ─── Export Income Report PDF ───────────────────────────────────────────
    public function exportIncomeReportPdf(Request $request)
    {
        $schoolId = $this->schoolId();
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo   = $request->get('date_to', now()->format('Y-m-d'));
        $headId   = $request->get('income_head_id', '');
        $status   = $request->get('status', '');

        $school = School::find($schoolId);
        $currentSession = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first();
        $sessionName = $currentSession ? $currentSession->name : 'Current Session';

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

        $filterParts = [];
        if ($headId && $head = $incomeHeads->firstWhere('id', $headId)) $filterParts[] = 'Head: ' . $head->name;
        if ($status) $filterParts[] = 'Status: ' . ucfirst($status);
        $filterSummary = !empty($filterParts) ? implode(' | ', $filterParts) : 'All Income Records';

        $kpis = [
            ['label' => 'Total Income',  'value' => '₹' . number_format($totalIncome, 2)],
            ['label' => 'Total Paid',    'value' => '₹' . number_format($totalPaid, 2)],
            ['label' => 'Total Pending', 'value' => '₹' . number_format($totalPending, 2)],
            ['label' => 'Transactions',  'value' => number_format($incomes->count())],
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('school.reports.pdf.income-report-pdf', [
            'school'        => $school,
            'reportTitle'   => 'Non-Fee Income Ledger Report',
            'sessionName'   => $sessionName,
            'dateFrom'      => $dateFrom,
            'dateTo'        => $dateTo,
            'filterSummary' => $filterSummary,
            'kpis'          => $kpis,
            'incomes'       => $incomes,
        ]);

        $pdf->setPaper('a4', 'landscape');
        $fileName = 'Income_Report_' . date('Ymd_His') . '.pdf';
        return $pdf->download($fileName);
    }

    // ─── Export Expense Report PDF ──────────────────────────────────────────
    public function exportExpenseReportPdf(Request $request)
    {
        $schoolId = $this->schoolId();
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo   = $request->get('date_to', now()->format('Y-m-d'));
        $headId   = $request->get('expense_head_id', '');
        $status   = $request->get('status', '');

        $school = School::find($schoolId);
        $currentSession = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first();
        $sessionName = $currentSession ? $currentSession->name : 'Current Session';

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

        $filterParts = [];
        if ($headId && $head = $expenseHeads->firstWhere('id', $headId)) $filterParts[] = 'Head: ' . $head->name;
        if ($status) $filterParts[] = 'Status: ' . ucfirst($status);
        $filterSummary = !empty($filterParts) ? implode(' | ', $filterParts) : 'All Expense Records';

        $kpis = [
            ['label' => 'Total Expense', 'value' => '₹' . number_format($totalExpense, 2)],
            ['label' => 'Total Paid',    'value' => '₹' . number_format($totalPaid, 2)],
            ['label' => 'Total Pending', 'value' => '₹' . number_format($totalPending, 2)],
            ['label' => 'Vouchers Count','value' => number_format($expenses->count())],
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('school.reports.pdf.expense-report-pdf', [
            'school'        => $school,
            'reportTitle'   => 'School Expense Ledger Report',
            'sessionName'   => $sessionName,
            'dateFrom'      => $dateFrom,
            'dateTo'        => $dateTo,
            'filterSummary' => $filterSummary,
            'kpis'          => $kpis,
            'expenses'      => $expenses,
        ]);

        $pdf->setPaper('a4', 'landscape');
        $fileName = 'Expense_Report_' . date('Ymd_His') . '.pdf';
        return $pdf->download($fileName);
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
                    'route_no'       => 'Route No.',
                    'stop_name'      => 'Stop Name',
                    'vehicle_code'   => 'Vehicle No.',
                    'driver_name'    => 'Driver Name',
                    'student_name'   => 'Student Name',
                    'admission_no'   => 'Adm No.',
                    'class_name'     => 'Class',
                    'section_name'   => 'Section',
                    'boarding_stop'  => 'Boarding Stop',
                    'drop_stop'      => 'Drop Stop',
                    'monthly_fee'    => 'Monthly Fee',
                    'payment_status' => 'Status',
                ];

                $vehicles = DB::table('vehicles')->where('school_id', $schoolId)->get()->keyBy('vehicle_no');
                $transportRoutes = DB::table('transport_routes')->where('school_id', $schoolId)->orderBy('name')->get();
                $transportStops  = DB::table('stops')->where('school_id', $schoolId)->orderBy('name')->get();

                $routeIdFilter  = $request->get('route_id', '');
                $stopNameFilter = $request->get('stop_name', '');

                $studentsQuery = Student::where('students.school_id', $schoolId)
                    ->where(function($q) {
                        $q->where('students.transport_opted', true)
                          ->orWhereNotNull('students.transport_route')
                          ->orWhereNotNull('students.transport_route_id');
                    })
                    ->join('school_classes', 'students.class_id', '=', 'school_classes.id')
                    ->leftJoin('sections', 'students.section_id', '=', 'sections.id')
                    ->leftJoin('transport_routes', 'students.transport_route_id', '=', 'transport_routes.id')
                    ->selectRaw("students.id as student_id, students.admission_number, students.first_name, students.last_name, school_classes.name as class_name, sections.name as section_name, students.transport_route, students.transport_route_id, students.transport_stop, students.transport_vehicle_code, students.transport_pickup_location, students.transport_drop_location, students.transport_pick_fare, students.transport_drop_fare, transport_routes.name as tr_route_name");

                if ($routeIdFilter) {
                    $studentsQuery->where(function($q) use ($routeIdFilter) {
                        $q->where('students.transport_route_id', $routeIdFilter)
                          ->orWhere('students.transport_route', $routeIdFilter)
                          ->orWhere('transport_routes.name', $routeIdFilter);
                    });
                }

                if ($stopNameFilter) {
                    $studentsQuery->where(function($q) use ($stopNameFilter) {
                        $q->where('students.transport_stop', $stopNameFilter)
                          ->orWhere('students.transport_pickup_location', $stopNameFilter)
                          ->orWhere('students.transport_drop_location', $stopNameFilter);
                    });
                }

                $students = $studentsQuery->orderBy('students.transport_route')
                    ->orderBy('students.first_name')
                    ->get();

                $totalMonthlyFeeSum = 0;
                $totalPaidTransportFeeSum = 0;

                $records = $students->map(function($r) use ($vehicles, &$totalMonthlyFeeSum, &$totalPaidTransportFeeSum, $schoolId) {
                    $rName = $r->transport_route ?? ($r->tr_route_name ?? '—');
                    $rNo   = $r->transport_route_id ? ('R-' . str_pad($r->transport_route_id, 3, '0', STR_PAD_LEFT)) : '—';
                    $vNo   = $r->transport_vehicle_code ?? '—';
                    $driver = isset($vehicles[$vNo]) ? $vehicles[$vNo]->driver_name : '—';
                    $mFee  = (float)($r->transport_pick_fare ?? 0) + (float)($r->transport_drop_fare ?? 0);
                    $totalMonthlyFeeSum += $mFee;

                    $transportFees = DB::table('student_fees')
                        ->where('school_id', $schoolId)
                        ->where('student_id', $r->student_id)
                        ->where(function($q) {
                            $q->whereNotNull('transport_fee_schedule_id')
                              ->orWhereExists(function($sq) {
                                  $sq->select(DB::raw(1))
                                     ->from('fee_categories')
                                     ->whereRaw('fee_categories.id = student_fees.fee_category_id')
                                     ->where('name', 'like', '%transport%');
                              })
                              ->orWhereExists(function($sq) {
                                  $sq->select(DB::raw(1))
                                     ->from('fee_components')
                                     ->whereRaw('fee_components.id = student_fees.fee_component_id')
                                     ->where('component_name', 'like', '%transport%');
                              });
                        })
                        ->get();

                    $feeAssigned = $transportFees->sum('amount');
                    $feePaid     = $transportFees->sum('paid_amount');
                    $totalPaidTransportFeeSum += $feePaid;

                    if ($feeAssigned > 0 && $feePaid >= $feeAssigned) {
                        $statusText = 'PAID';
                        $statusBadge = '<span class="badge badge-success" style="background:#dcfce7; color:#166534; padding:2px 6px; border-radius:4px; font-weight:700; font-size:10px;">PAID</span>';
                    } elseif ($feePaid > 0) {
                        $statusText = 'PARTIAL';
                        $statusBadge = '<span class="badge badge-warning" style="background:#fef3c7; color:#92400e; padding:2px 6px; border-radius:4px; font-weight:700; font-size:10px;">PARTIAL</span>';
                    } else {
                        $statusText = 'PENDING';
                        $statusBadge = '<span class="badge badge-danger" style="background:#fee2e2; color:#991b1b; padding:2px 6px; border-radius:4px; font-weight:700; font-size:10px;">PENDING</span>';
                    }

                    return [
                        'route_name'     => $rName,
                        'route_no'       => $rNo,
                        'stop_name'      => $r->transport_stop ?? '—',
                        'vehicle_code'   => $vNo,
                        'driver_name'    => $driver,
                        'student_name'   => trim($r->first_name . ' ' . $r->last_name),
                        'admission_no'   => $r->admission_number ?? '—',
                        'class_name'     => $r->class_name ?? '—',
                        'section_name'   => $r->section_name ?? '—',
                        'boarding_stop'  => $r->transport_pickup_location ?? $r->transport_stop ?? '—',
                        'drop_stop'      => $r->transport_drop_location ?? '—',
                        'monthly_fee'    => '₹ ' . number_format($mFee, 2),
                        'payment_status' => $statusBadge,
                        'payment_status_raw' => $statusText,
                    ];
                });

                $summary = [
                    'Total Transport Students' => $records->count(),
                    'Total Monthly Transport Fee' => '₹ ' . number_format($totalMonthlyFeeSum, 2),
                    'Total Route Collection' => '₹ ' . number_format($totalPaidTransportFeeSum, 2),
                ];

                // ─── Vehicle Report Data & Summary ─────────────────────────
                $allVehiclesList = DB::table('vehicles')->where('school_id', $schoolId)->get();
                $allDocsGrouped = DB::table('vehicle_documents')
                    ->where('school_id', $schoolId)
                    ->get()
                    ->groupBy('vehicle_id');

                $assignedCountByVehicle = DB::table('students')
                    ->where('school_id', $schoolId)
                    ->whereNotNull('transport_vehicle_code')
                    ->select('transport_vehicle_code', DB::raw('count(*) as total'))
                    ->groupBy('transport_vehicle_code')
                    ->pluck('total', 'transport_vehicle_code');

                $now = Carbon::now();
                $thirtyDaysLater = Carbon::now()->addDays(30);

                $totalVehicles = $allVehiclesList->count();
                $totalCapacity = 0;
                $totalAssigned = 0;
                $totalDocsCount = 0;
                $expiringDocsCount = 0;

                $vehicleReportRecords = $allVehiclesList->map(function($v) use ($allDocsGrouped, $assignedCountByVehicle, $now, $thirtyDaysLater, &$totalCapacity, &$totalAssigned, &$totalDocsCount, &$expiringDocsCount) {
                    $cap = (int)($v->capacity ?? 40);
                    $assigned = (int)($assignedCountByVehicle[$v->vehicle_no] ?? 0);
                    $avail = max(0, $cap - $assigned);

                    $totalCapacity += $cap;
                    $totalAssigned += $assigned;

                    $docs = $allDocsGrouped->get($v->id, collect());
                    $docCount = $docs->count();
                    $totalDocsCount += $docCount;

                    $vExpiringCount = 0;
                    $vExpiredCount = 0;

                    $docItems = $docs->map(function($d) use ($now, $thirtyDaysLater, &$vExpiringCount, &$vExpiredCount) {
                        $status = 'valid';
                        if ($d->valid_to) {
                            $vt = Carbon::parse($d->valid_to);
                            if ($vt->isPast()) {
                                $status = 'expired';
                                $vExpiredCount++;
                            } elseif ($vt->between($now, $thirtyDaysLater)) {
                                $status = 'expiring_soon';
                                $vExpiringCount++;
                            }
                        }
                        return [
                            'id'              => $d->id,
                            'document_type'   => $d->document_type,
                            'document_number' => $d->document_number ?? '—',
                            'valid_from'      => $d->valid_from ? Carbon::parse($d->valid_from)->format('d M Y') : '—',
                            'valid_to'        => $d->valid_to ? Carbon::parse($d->valid_to)->format('d M Y') : '—',
                            'original_name'   => $d->original_name,
                            'file_size'       => $d->file_size ? number_format($d->file_size / 1024, 1) . ' KB' : '—',
                            'status'          => $status,
                            'view_url'        => route('school.transport.vehicles.documents.view', $d->id),
                            'download_url'    => route('school.transport.vehicles.documents.download', $d->id),
                        ];
                    });

                    if ($vExpiringCount > 0 || $vExpiredCount > 0) {
                        $expiringDocsCount += ($vExpiringCount + $vExpiredCount);
                    }

                    return [
                        'id'                => $v->id,
                        'vehicle_no'        => $v->vehicle_no,
                        'vehicle_model'     => $v->vehicle_model ?? '—',
                        'driver_name'       => $v->driver_name ?? '—',
                        'driver_phone'      => $v->driver_phone ?? '—',
                        'capacity'          => $cap,
                        'assigned'          => $assigned,
                        'available'         => $avail,
                        'status'            => $v->status ? 'Active' : 'Inactive',
                        'documents_count'   => $docCount,
                        'expiring_count'    => $vExpiringCount,
                        'expired_count'     => $vExpiredCount,
                        'documents'         => $docItems,
                    ];
                });

                $vehicleSummary = [
                    'total_vehicles'        => $totalVehicles,
                    'total_capacity'        => $totalCapacity,
                    'total_assigned'        => $totalAssigned,
                    'total_available'       => max(0, $totalCapacity - $totalAssigned),
                    'uploaded_documents'    => $totalDocsCount,
                    'expiring_expired_docs' => $expiringDocsCount,
                ];
                break;

            case 'transport_wise_report':
                $columns = [
                    'vehicle_code'   => 'Vehicle',
                    'route_name'     => 'Route',
                    'student_name'   => 'Student Name',
                    'admission_no'   => 'Adm No.',
                    'class_name'     => 'Class & Section',
                    'transport_fee'  => 'Transport Fee',
                    'paid_amount'    => 'Paid Amount',
                    'due_amount'     => 'Due Amount',
                    'collection_date'=> 'Collection Date',
                    'receipt_number' => 'Receipt No.',
                    'payment_mode'   => 'Payment Mode',
                ];

                $students = Student::where('students.school_id', $schoolId)
                    ->where(function($q) {
                        $q->where('students.transport_opted', true)
                          ->orWhereNotNull('students.transport_route')
                          ->orWhereNotNull('students.transport_route_id');
                    })
                    ->join('school_classes', 'students.class_id', '=', 'school_classes.id')
                    ->leftJoin('sections', 'students.section_id', '=', 'sections.id')
                    ->selectRaw("students.id as student_id, students.admission_number, students.first_name, students.last_name, school_classes.name as class_name, sections.name as section_name, students.transport_route, students.transport_vehicle_code, students.transport_pick_fare, students.transport_drop_fare")
                    ->get();

                $totalCollectionSum = 0;
                $totalPendingSum = 0;
                $totalFeeSum = 0;

                $records = $students->map(function($s) use ($schoolId, &$totalCollectionSum, &$totalPendingSum, &$totalFeeSum) {
                    $transportFees = DB::table('student_fees')
                        ->where('school_id', $schoolId)
                        ->where('student_id', $s->student_id)
                        ->where(function($q) {
                            $q->whereNotNull('transport_fee_schedule_id')
                              ->orWhereExists(function($sq) {
                                  $sq->select(DB::raw(1))
                                     ->from('fee_categories')
                                     ->whereRaw('fee_categories.id = student_fees.fee_category_id')
                                     ->where('name', 'like', '%transport%');
                              })
                              ->orWhereExists(function($sq) {
                                  $sq->select(DB::raw(1))
                                     ->from('fee_components')
                                     ->whereRaw('fee_components.id = student_fees.fee_component_id')
                                     ->where('component_name', 'like', '%transport%');
                              });
                        })
                        ->get();

                    $assigned = $transportFees->sum('amount');
                    if ($assigned == 0) {
                        $assigned = ((float)($s->transport_pick_fare ?? 0) + (float)($s->transport_drop_fare ?? 0)) * 10;
                    }
                    $paid = $transportFees->sum('paid_amount');
                    $due  = max(0, $assigned - $paid);

                    $totalFeeSum += $assigned;
                    $totalCollectionSum += $paid;
                    $totalPendingSum += $due;

                    $receipt = DB::table('fee_receipts')
                        ->where('school_id', $schoolId)
                        ->where('student_id', $s->student_id)
                        ->orderByDesc('payment_date')
                        ->first();

                    return [
                        'vehicle_code'   => $s->transport_vehicle_code ?? '—',
                        'route_name'     => $s->transport_route ?? '—',
                        'student_name'   => trim($s->first_name . ' ' . $s->last_name),
                        'admission_no'   => $s->admission_number ?? '—',
                        'class_name'     => $s->class_name . ($s->section_name ? ' - ' . $s->section_name : ''),
                        'transport_fee'  => '₹ ' . number_format($assigned, 2),
                        'paid_amount'    => '₹ ' . number_format($paid, 2),
                        'due_amount'     => '₹ ' . number_format($due, 2),
                        'collection_date'=> ($receipt && $receipt->payment_date) ? Carbon::parse($receipt->payment_date)->format('d M Y') : '—',
                        'receipt_number' => $receipt ? $receipt->receipt_number : '—',
                        'payment_mode'   => $receipt ? strtoupper($receipt->payment_mode) : '—',
                    ];
                });

                $summary = [
                    'Total Collection' => '₹ ' . number_format($totalCollectionSum, 2),
                    'Total Pending Amount' => '₹ ' . number_format($totalPendingSum, 2),
                ];
                break;

            case 'concession_fine_report':
                $columns = [
                    'student_name'      => 'Student Name',
                    'admission_no'      => 'Admission No.',
                    'class_name'        => 'Class & Section',
                    'fee_head'          => 'Fee Head',
                    'concession_amount' => 'Concession Amount',
                    'fine_amount'       => 'Fine Amount',
                    'approved_by'       => 'Approved By',
                    'reason'            => 'Reason / Remarks',
                ];

                $fines = DB::table('fee_fines')->where('school_id', $schoolId)->get()->map(function($r) {
                    return [
                        'student_name'      => 'All Students',
                        'admission_no'      => '—',
                        'class_name'        => 'All Classes',
                        'fee_head'          => $r->name,
                        'concession_amount' => '₹ 0.00',
                        'fine_amount'       => '₹ ' . number_format($r->fine_amount, 2),
                        'approved_by'       => 'Admin',
                        'reason'            => 'Fine Rule: ' . $r->fine_type,
                        'raw_fine'          => (float)$r->fine_amount,
                        'raw_concession'    => 0,
                    ];
                });

                $discounts = DB::table('fee_discounts')->where('school_id', $schoolId)->get();
                $discMapped = collect();

                foreach ($discounts as $disc) {
                    $studentIds = $disc->student_ids ? json_decode($disc->student_ids, true) : [];
                    $formattedVal = isset($disc->type) && $disc->type === 'percentage' ? (float)$disc->amount . '%' : '₹ ' . number_format($disc->amount, 2);
                    $numVal = isset($disc->type) && $disc->type === 'percentage' ? 0 : (float)$disc->amount;

                    if (!empty($studentIds)) {
                        $stus = Student::whereIn('students.id', $studentIds)
                            ->join('school_classes', 'students.class_id', '=', 'school_classes.id')
                            ->leftJoin('sections', 'students.section_id', '=', 'sections.id')
                            ->selectRaw("students.admission_number, students.first_name, students.last_name, school_classes.name as class_name, sections.name as section_name")
                            ->get();
                        foreach ($stus as $stu) {
                            $discMapped->push([
                                'student_name'      => trim($stu->first_name . ' ' . $stu->last_name),
                                'admission_no'      => $stu->admission_number ?? '—',
                                'class_name'        => $stu->class_name . ($stu->section_name ? ' - ' . $stu->section_name : ''),
                                'fee_head'          => $disc->name,
                                'concession_amount' => $formattedVal,
                                'fine_amount'       => '₹ 0.00',
                                'approved_by'       => 'Admin',
                                'reason'            => $disc->remarks ?? 'Fee Concession Scheme',
                                'raw_fine'          => 0,
                                'raw_concession'    => $numVal,
                            ]);
                        }
                    } else {
                        $discMapped->push([
                            'student_name'      => 'All Students',
                            'admission_no'      => '—',
                            'class_name'        => $disc->classes_installments ?? 'All Classes',
                            'fee_head'          => $disc->name,
                            'concession_amount' => $formattedVal,
                            'fine_amount'       => '₹ 0.00',
                            'approved_by'       => 'Admin',
                            'reason'            => $disc->remarks ?? 'Fee Concession Scheme',
                            'raw_fine'          => 0,
                            'raw_concession'    => $numVal,
                        ]);
                    }
                }

                $records = $fines->merge($discMapped);

                $summary = [
                    'Total Fines Defined'     => $fines->count(),
                    'Total Concessions'       => $discMapped->count(),
                    'Total Concession Amount' => '₹ ' . number_format($discMapped->sum('raw_concession'), 2),
                    'Total Fine Amount'       => '₹ ' . number_format($fines->sum('raw_fine'), 2),
                ];
                break;

            case 'discount_report_detailed':
                $columns = [
                    'student_name'   => 'Student Name',
                    'admission_no'   => 'Admission No.',
                    'class_name'     => 'Class & Section',
                    'discount_name'  => 'Fee Head / Scheme',
                    'discount_pct'   => 'Discount %',
                    'amount'         => 'Discount Amount',
                    'reason'         => 'Reason / Remarks',
                    'approved_by'    => 'Approved By',
                    'created_date'   => 'Date',
                ];

                $discountRows = DB::table('fee_discounts')
                    ->where('fee_discounts.school_id', $schoolId)
                    ->get();
                $rows = collect();

                foreach ($discountRows as $disc) {
                    $targetInst = $disc->installment_no ? 'Applies to: Installment ' . $disc->installment_no : 'Applies to: All Installments';
                    $formattedRemarks = ($disc->remarks ? $disc->remarks . ' · ' : '') . $targetInst;
                    $formattedAmount = isset($disc->type) && $disc->type === 'percentage' ? (float)$disc->amount . '%' : '₹ ' . number_format($disc->amount, 2);
                    $discPct = isset($disc->type) && $disc->type === 'percentage' ? (float)$disc->amount . '%' : '—';
                    $dateStr = $disc->created_at ? Carbon::parse($disc->created_at)->format('d M Y') : '—';

                    $studentIds = $disc->student_ids ? json_decode($disc->student_ids, true) : [];
                    if (!empty($studentIds)) {
                        $students = Student::whereIn('students.id', $studentIds)
                            ->join('school_classes', 'students.class_id', '=', 'school_classes.id')
                            ->leftJoin('sections', 'students.section_id', '=', 'sections.id')
                            ->selectRaw("students.admission_number, students.first_name, students.last_name, school_classes.name as class_name, sections.name as section_name")
                            ->get();
                        foreach ($students as $stu) {
                            $rows->push([
                                'student_name'  => trim($stu->first_name . ' ' . $stu->last_name),
                                'admission_no'  => $stu->admission_number ?? '—',
                                'class_name'    => $stu->class_name . ($stu->section_name ? ' - ' . $stu->section_name : ''),
                                'discount_name' => $disc->name,
                                'discount_pct'  => $discPct,
                                'amount'        => $formattedAmount,
                                'reason'        => $formattedRemarks,
                                'approved_by'   => 'Admin',
                                'created_date'  => $dateStr,
                            ]);
                        }
                    } else {
                        $rows->push([
                            'student_name'  => 'All Students',
                            'admission_no'  => '—',
                            'class_name'    => $disc->classes_installments ?? 'All Classes',
                            'discount_name' => $disc->name,
                            'discount_pct'  => $discPct,
                            'amount'        => $formattedAmount,
                            'reason'        => $formattedRemarks,
                            'approved_by'   => 'Admin',
                            'created_date'  => $dateStr,
                        ]);
                    }
                }
                $records = $rows;
                $summary = ['Total Discount Schemes' => $records->count()];
                break;

            case 'dues_report':
                $columns = [
                    'student_name'  => 'Student Name',
                    'admission_no'  => 'Admission No.',
                    'class_name'    => 'Class & Section',
                    'fee_head'      => 'Fee Structure / Head',
                    'installment'   => 'Installment',
                    'total_amount'  => 'Assigned Amount',
                    'paid_amount'   => 'Paid Amount',
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
                    ->selectRaw("students.admission_number, students.first_name, students.last_name, school_classes.name as class_name, sections.name as section_name, fee_categories.name as category_name, student_fees.installment_no, student_fees.amount, student_fees.paid_amount, (student_fees.amount - student_fees.paid_amount) as dues_amount, student_fees.due_date, student_fees.status")
                    ->orderBy('students.first_name');

                if ($classId) $query->where('students.class_id', $classId);

                $results = $query->get();

                $totalAssignedSum = 0;
                $totalPaidSum = 0;
                $totalDuesSum = 0;

                $records = $results->map(function($r) use (&$totalAssignedSum, &$totalPaidSum, &$totalDuesSum) {
                    $totalAssignedSum += (float)$r->amount;
                    $totalPaidSum += (float)$r->paid_amount;
                    $totalDuesSum += (float)$r->dues_amount;

                    $statusText = $r->paid_amount > 0 ? 'PARTIAL' : 'PENDING';
                    $statusBadge = $r->paid_amount > 0 
                        ? '<span class="badge badge-warning" style="background:#fef3c7; color:#92400e; padding:2px 6px; border-radius:4px; font-weight:700; font-size:10px;">PARTIAL</span>'
                        : '<span class="badge badge-danger" style="background:#fee2e2; color:#991b1b; padding:2px 6px; border-radius:4px; font-weight:700; font-size:10px;">PENDING</span>';

                    return [
                        'student_name'  => trim($r->first_name . ' ' . $r->last_name),
                        'admission_no'  => $r->admission_number ?? '—',
                        'class_name'    => $r->class_name . ($r->section_name ? ' - ' . $r->section_name : ''),
                        'fee_head'      => $r->category_name ?? 'General Fee',
                        'installment'   => \App\Services\FeeHelper::getInstallmentName(null, $r->installment_no ?? 1),
                        'total_amount'  => '₹ ' . number_format($r->amount, 2),
                        'paid_amount'   => '₹ ' . number_format($r->paid_amount, 2),
                        'dues_amount'   => '₹ ' . number_format($r->dues_amount, 2),
                        'due_date'      => $r->due_date ? Carbon::parse($r->due_date)->format('d M Y') : '—',
                        'status'        => $statusBadge,
                        'status_raw'    => $statusText,
                    ];
                });

                $summary = [
                    'Total Records with Dues' => $records->count(),
                    'Total Dues Amount'        => '₹ ' . number_format($totalDuesSum, 2),
                ];
                break;

            case 'paid_report':
                $columns = [
                    'student_name'   => 'Student Name',
                    'admission_no'   => 'Admission No.',
                    'receipt_number' => 'Receipt No.',
                    'invoice_number' => 'Invoice No.',
                    'amount_paid'    => 'Paid Amount',
                    'payment_date'   => 'Payment Date',
                    'payment_mode'   => 'Payment Mode',
                    'transaction_id' => 'Transaction ID',
                    'collected_by'   => 'Collected By',
                    'fee_head'       => 'Fee Head',
                    'installment'    => 'Installment',
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

                $totalPaidCollectionSum = 0;

                $records = $query->get()->map(function($r) use (&$totalPaidCollectionSum) {
                    $totalPaidCollectionSum += (float)$r->amount_paid;
                    $invNo = 'INV-' . str_replace('REC-', '', $r->receipt_number);
                    return [
                        'student_name'   => trim($r->first_name . ' ' . $r->last_name),
                        'admission_no'   => $r->admission_number ?? '—',
                        'receipt_number' => $r->receipt_number,
                        'invoice_number' => $invNo,
                        'amount_paid'    => '₹ ' . number_format($r->amount_paid, 2),
                        'payment_date'   => $r->payment_date ? Carbon::parse($r->payment_date)->format('d M Y') : '—',
                        'payment_mode'   => strtoupper($r->payment_mode),
                        'transaction_id' => $r->transaction_id ?? '—',
                        'collected_by'   => 'Admin',
                        'fee_head'       => 'Tuition & General Fee',
                        'installment'    => 'Installment 1',
                    ];
                });

                $summary = [
                    'Total Receipts'   => $records->count(),
                    'Total Paid Collection' => '₹ ' . number_format($totalPaidCollectionSum, 2),
                ];
                break;

            case 'refund_report':
                $columns = [
                    'student_name'   => 'Student Name',
                    'admission_no'   => 'Admission No.',
                    'class_name'     => 'Class',
                    'section_name'   => 'Section',
                    'receipt_number' => 'Receipt No.',
                    'amount'         => 'Refund Amount',
                    'refund_date'    => 'Refund Date',
                    'reason'         => 'Refund Reason',
                    'payment_mode'   => 'Payment Mode',
                    'approved_by'    => 'Approved By',
                    'remarks'        => 'Remarks',
                    'refund_status'  => 'Refund Status',
                ];
                $query = DB::table('fee_refunds')
                    ->where('fee_refunds.school_id', $schoolId)
                    ->join('students', 'fee_refunds.student_id', '=', 'students.id')
                    ->join('school_classes', 'students.class_id', '=', 'school_classes.id')
                    ->leftJoin('sections', 'students.section_id', '=', 'sections.id')
                    ->selectRaw("
                        students.admission_number,
                        students.first_name,
                        students.last_name,
                        school_classes.name as class_name,
                        sections.name as section_name,
                        fee_refunds.slip_no,
                        fee_refunds.id as refund_id,
                        fee_refunds.amount,
                        fee_refunds.refund_date,
                        fee_refunds.reason,
                        fee_refunds.payment_mode
                    ")
                    ->whereBetween('fee_refunds.refund_date', [$dateFrom, $dateTo])
                    ->orderByDesc('fee_refunds.refund_date');
                if ($classId) $query->where('students.class_id', $classId);

                $totalAmountSum = 0;
                $records = $query->get()->map(function($r) use (&$totalAmountSum) {
                    $totalAmountSum += (float)$r->amount;
                    $receiptNo = $r->slip_no ? $r->slip_no : 'RF-REC-' . str_pad($r->refund_id, 5, '0', STR_PAD_LEFT);
                    return [
                        'student_name'   => trim($r->first_name . ' ' . $r->last_name),
                        'admission_no'   => $r->admission_number ?? '—',
                        'class_name'     => $r->class_name,
                        'section_name'   => $r->section_name ?? '—',
                        'receipt_number' => $receiptNo,
                        'amount'         => '₹ ' . number_format($r->amount, 2),
                        'refund_date'    => Carbon::parse($r->refund_date)->format('d M Y'),
                        'reason'         => $r->reason ?? 'Fee Refund',
                        'payment_mode'   => strtoupper($r->payment_mode ?? 'CASH'),
                        'approved_by'    => 'Admin',
                        'remarks'        => $r->reason ?? 'Approved by School Finance',
                        'refund_status'  => '<span class="badge badge-success" style="background:#dcfce7; color:#166534; padding:2px 6px; border-radius:4px; font-weight:700; font-size:10px;">REFUNDED</span>',
                        'refund_status_raw' => 'REFUNDED',
                    ];
                });
                $summary = [
                    'Total Refund Amount' => '₹ ' . number_format($totalAmountSum, 2),
                    'Total Refund Count'  => $records->count(),
                ];
                break;

            case 'studentwise_refund':
                $columns = [
                    'student_name'       => 'Student Name',
                    'admission_no'       => 'Admission No.',
                    'class_name'         => 'Class',
                    'section_name'       => 'Section',
                    'refund_count'       => 'Number of Refunds',
                    'total_refund'       => 'Total Refunded Amount',
                    'latest_refund_date' => 'Latest Refund Date',
                    'refund_status'      => 'Current Refund Status',
                ];
                $query = DB::table('fee_refunds')
                    ->where('fee_refunds.school_id', $schoolId)
                    ->join('students', 'fee_refunds.student_id', '=', 'students.id')
                    ->join('school_classes', 'students.class_id', '=', 'school_classes.id')
                    ->leftJoin('sections', 'students.section_id', '=', 'sections.id')
                    ->selectRaw("
                        students.first_name,
                        students.last_name,
                        students.admission_number,
                        school_classes.name as class_name,
                        sections.name as section_name,
                        COUNT(fee_refunds.id) as refund_count,
                        SUM(fee_refunds.amount) as total_refund,
                        MAX(fee_refunds.refund_date) as latest_refund_date
                    ")
                    ->whereBetween('fee_refunds.refund_date', [$dateFrom, $dateTo])
                    ->groupBy('students.id', 'students.first_name', 'students.last_name', 'students.admission_number', 'school_classes.name', 'sections.name')
                    ->orderBy('students.first_name');
                if ($classId) $query->where('students.class_id', $classId);

                $totalRefundedSum = 0;
                $records = $query->get()->map(function($r) use (&$totalRefundedSum) {
                    $totalRefundedSum += (float)$r->total_refund;
                    return [
                        'student_name'       => trim($r->first_name . ' ' . $r->last_name),
                        'admission_no'       => $r->admission_number ?? '—',
                        'class_name'         => $r->class_name,
                        'section_name'       => $r->section_name ?? '—',
                        'refund_count'       => $r->refund_count,
                        'total_refund'       => '₹ ' . number_format($r->total_refund, 2),
                        'latest_refund_date' => $r->latest_refund_date ? Carbon::parse($r->latest_refund_date)->format('d M Y') : '—',
                        'refund_status'      => '<span class="badge badge-success" style="background:#dcfce7; color:#166534; padding:2px 6px; border-radius:4px; font-weight:700; font-size:10px;">PROCESSED</span>',
                        'refund_status_raw'  => 'PROCESSED',
                    ];
                });
                
                $summary = [
                    'Total Students Refunded' => $records->count(),
                    'Total Refund Amount'     => '₹ ' . number_format($totalRefundedSum, 2),
                ];
                break;

            case 'estimated_fees':
                $columns = [
                    'class_name'          => 'Class',
                    'section_name'        => 'Section',
                    'fee_head'            => 'Fee Head',
                    'planned_collection'  => 'Planned Collection',
                    'expected_collection' => 'Expected Collection',
                    'estimated_income'    => 'Estimated Income',
                    'student_count'       => 'Number of Students',
                    'fee_structure'       => 'Fee Structure',
                    'installments'        => 'Installments',
                    'estimated_total'     => 'Estimated Total',
                ];
                $query = DB::table('fee_structures')
                    ->where('fee_structures.school_id', $schoolId)
                    ->join('school_classes', 'fee_structures.class_id', '=', 'school_classes.id')
                    ->join('fee_categories', 'fee_structures.fee_category_id', '=', 'fee_categories.id')
                    ->selectRaw("
                        school_classes.id as class_id,
                        school_classes.name as class_name,
                        fee_categories.name as category_name,
                        fee_structures.amount
                    ");
                if ($classId) $query->where('fee_structures.class_id', $classId);

                $rows = collect();
                $grandEstimatedSum = 0;
                $totalStudentsSum = 0;

                $classStructures = $query->get();
                foreach ($classStructures as $fs) {
                    $studentQuery = Student::where('class_id', $fs->class_id)
                        ->where('school_id', $schoolId)
                        ->where('is_active', 1);
                    $studentCount = $studentQuery->count();
                    $plannedVal = (float)$fs->amount;
                    $estTotalVal = $plannedVal * $studentCount;

                    $grandEstimatedSum += $estTotalVal;
                    $totalStudentsSum += $studentCount;

                    $rows->push([
                        'class_name'          => $fs->class_name,
                        'section_name'        => 'All Sections',
                        'fee_head'            => $fs->category_name,
                        'planned_collection'  => '₹ ' . number_format($plannedVal, 2),
                        'expected_collection' => '₹ ' . number_format($estTotalVal, 2),
                        'estimated_income'    => '₹ ' . number_format($estTotalVal, 2),
                        'student_count'       => $studentCount,
                        'fee_structure'       => $fs->category_name . ' Structure',
                        'installments'        => 'Annual / Regular',
                        'estimated_total'     => '₹ ' . number_format($estTotalVal, 2),
                    ]);
                }
                $records = $rows;
                $summary = [
                    'Total Estimated Revenue' => '₹ ' . number_format($grandEstimatedSum, 2),
                    'Total Fee Heads'         => $records->count(),
                    'Total Students'          => $totalStudentsSum,
                ];
                break;

            case 'consolidated_fees':
                $columns = [
                    'student_name'        => 'Student Name',
                    'admission_no'        => 'Admission No.',
                    'class_name'          => 'Class',
                    'section_name'        => 'Section',
                    'total_assigned'      => 'Total Assigned',
                    'total_paid'          => 'Total Paid',
                    'total_discount'      => 'Total Discount',
                    'total_fine'          => 'Total Fine',
                    'total_refund'        => 'Total Refund',
                    'total_dues'          => 'Total Due',
                    'outstanding_balance' => 'Outstanding Balance',
                    'status'              => 'Current Status',
                ];
                $query = Student::where('students.school_id', $schoolId)
                    ->join('school_classes', 'students.class_id', '=', 'school_classes.id')
                    ->leftJoin('sections', 'students.section_id', '=', 'sections.id')
                    ->leftJoin('student_fees', 'students.id', '=', 'student_fees.student_id')
                    ->selectRaw("
                        students.id as student_id,
                        students.admission_number,
                        students.first_name,
                        students.last_name,
                        school_classes.name as class_name,
                        sections.name as section_name,
                        SUM(student_fees.amount) as total_assigned,
                        SUM(student_fees.paid_amount) as total_paid,
                        SUM(COALESCE(student_fees.instant_discount_amount, 0)) as total_discount,
                        SUM(COALESCE(student_fees.fine_amount_applied, 0)) as total_fine
                    ")
                    ->groupBy('students.id', 'students.admission_number', 'students.first_name', 'students.last_name', 'school_classes.name', 'sections.name')
                    ->orderBy('students.first_name');

                if ($classId) $query->where('students.class_id', $classId);

                $studentsData = $query->get();

                $sumAssigned = 0;
                $sumPaid = 0;
                $sumDiscount = 0;
                $sumFine = 0;
                $sumRefund = 0;
                $sumDues = 0;
                $sumNetBalance = 0;

                $records = $studentsData->map(function($s) use (&$sumAssigned, &$sumPaid, &$sumDiscount, &$sumFine, &$sumRefund, &$sumDues, &$sumNetBalance) {
                    $totalRefund = (float) DB::table('fee_refunds')->where('student_id', $s->student_id)->sum('amount');
                    $totalAssigned = (float)($s->total_assigned ?? 0);
                    $totalPaid = (float)($s->total_paid ?? 0);
                    $totalDiscount = (float)($s->total_discount ?? 0);
                    $totalFine = (float)($s->total_fine ?? 0);
                    $totalDues = max(0, $totalAssigned + $totalFine - $totalPaid - $totalDiscount);
                    $netBalance = max(0, $totalDues - $totalRefund);

                    $sumAssigned += $totalAssigned;
                    $sumPaid += $totalPaid;
                    $sumDiscount += $totalDiscount;
                    $sumFine += $totalFine;
                    $sumRefund += $totalRefund;
                    $sumDues += $totalDues;
                    $sumNetBalance += $netBalance;

                    if ($totalDues <= 0) {
                        $statusBadge = '<span class="badge badge-success" style="background:#dcfce7; color:#166534; padding:2px 6px; border-radius:4px; font-weight:700; font-size:10px;">PAID</span>';
                        $statusText = 'PAID';
                    } elseif ($totalPaid > 0) {
                        $statusBadge = '<span class="badge badge-warning" style="background:#fef3c7; color:#92400e; padding:2px 6px; border-radius:4px; font-weight:700; font-size:10px;">PARTIAL</span>';
                        $statusText = 'PARTIAL';
                    } else {
                        $statusBadge = '<span class="badge badge-danger" style="background:#fee2e2; color:#991b1b; padding:2px 6px; border-radius:4px; font-weight:700; font-size:10px;">DUE</span>';
                        $statusText = 'DUE';
                    }

                    return [
                        'student_name'        => trim($s->first_name . ' ' . $s->last_name),
                        'admission_no'        => $s->admission_number ?? '—',
                        'class_name'          => $s->class_name,
                        'section_name'        => $s->section_name ?? '—',
                        'total_assigned'      => '₹ ' . number_format($totalAssigned, 2),
                        'total_paid'          => '₹ ' . number_format($totalPaid, 2),
                        'total_discount'      => '₹ ' . number_format($totalDiscount, 2),
                        'total_fine'          => '₹ ' . number_format($totalFine, 2),
                        'total_refund'        => '₹ ' . number_format($totalRefund, 2),
                        'total_dues'          => '₹ ' . number_format($totalDues, 2),
                        'outstanding_balance' => '₹ ' . number_format($netBalance, 2),
                        'status'              => $statusBadge,
                        'status_raw'          => $statusText,
                    ];
                });

                $summary = [
                    'Total Assigned Fees' => '₹ ' . number_format($sumAssigned, 2),
                    'Total Paid'          => '₹ ' . number_format($sumPaid, 2),
                    'Total Outstanding'   => '₹ ' . number_format($sumDues, 2),
                    'Total Refund'        => '₹ ' . number_format($sumRefund, 2),
                    'Net Balance'         => '₹ ' . number_format($sumNetBalance, 2),
                ];
                break;
        }

        $school = \App\Models\School::find($schoolId);

        $transportRoutes      = $transportRoutes ?? collect();
        $transportStops       = $transportStops ?? collect();
        $routeIdFilter        = $routeIdFilter ?? '';
        $stopNameFilter       = $stopNameFilter ?? '';
        $vehicleReportRecords = $vehicleReportRecords ?? collect();
        $vehicleSummary       = $vehicleSummary ?? [];

        return view('school.reports.detail', compact(
            'type', 'title', 'columns', 'records', 'summary',
            'dateFrom', 'dateTo', 'sessionVal', 'dateType', 'sessions', 'classes', 'school',
            'transportRoutes', 'transportStops', 'routeIdFilter', 'stopNameFilter',
            'vehicleReportRecords', 'vehicleSummary'
        ));
    }

    public function exportDetailReportPdf(string $type, Request $request)
    {
        $schoolId = $this->schoolId();
        $school   = School::find($schoolId);

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

        // Generate response using existing detailReport view data
        $view = $this->detailReport($type, $request);
        $data = $view->getData();

        $kpis = [];
        if (!empty($data['summary'])) {
            foreach ($data['summary'] as $lbl => $val) {
                $kpis[] = ['label' => $lbl, 'value' => $val];
            }
        }

        $exportHeaders = [];
        if (!empty($data['columns'])) {
            foreach ($data['columns'] as $key => $lbl) {
                $align = in_array($key, [
                    'amount', 'amount_paid', 'dues_amount', 'total_amount', 'paid_amount', 'due_amount',
                    'transport_fee', 'monthly_fee', 'concession_amount', 'fine_amount', 'total_collected',
                    'total_pending', 'total_assigned', 'net_balance', 'total_paid', 'total_discount',
                    'total_fine', 'total_refund', 'total_dues', 'outstanding_balance', 'planned_collection',
                    'expected_collection', 'estimated_income', 'per_student_fee', 'estimated_total'
                ]) ? 'right' : 'left';
                $exportHeaders[] = [
                    'key'   => $key,
                    'title' => $lbl,
                    'align' => $align,
                ];
            }
        }

        $exportRows = [];
        if (!empty($data['records'])) {
            foreach ($data['records'] as $r) {
                $rowArr = [];
                foreach ($data['columns'] as $k => $lbl) {
                    if (isset($r[$k . '_raw'])) {
                        $rowArr[$k] = $r[$k . '_raw'];
                    } else {
                        $rowArr[$k] = strip_tags($r[$k] ?? '—');
                    }
                }
                $exportRows[] = $rowArr;
            }
        }

        $filterParts = [];
        if (!empty($data['sessionVal'])) $filterParts[] = 'Session: ' . $data['sessionVal'];
        if ($classId) {
            $cls = SchoolClass::find($classId);
            if ($cls) $filterParts[] = 'Class: ' . $cls->name;
        }
        if ($paymentMode) $filterParts[] = 'Mode: ' . strtoupper($paymentMode);
        $filterSummary = !empty($filterParts) ? implode(' | ', $filterParts) : 'All Records';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('school.reports.pdf.fee-report-pdf', [
            'school'        => $school,
            'reportTitle'   => $data['title'] ?? 'Fee Report',
            'sessionName'   => $data['sessionVal'] ?: 'Current Session',
            'dateFrom'      => $data['dateFrom'] ?? $dateFrom,
            'dateTo'        => $data['dateTo'] ?? $dateTo,
            'filterSummary' => $filterSummary,
            'kpis'          => $kpis,
            'headers'       => $exportHeaders,
            'rows'          => $exportRows,
            'totals'        => $data['totals'] ?? [],
        ]);

        $pdf->setPaper('a4', 'landscape');
        $fileName = str_replace([' ', '/', '\\', '&'], '_', $data['title'] ?? 'Fee_Report') . '_' . date('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }
}

