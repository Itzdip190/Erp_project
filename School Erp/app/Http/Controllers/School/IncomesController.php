<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolIncome;
use App\Models\IncomeHead;
use App\Models\FeeReceipt;
use App\Models\SchoolExpense;
use App\Models\AccountTransfer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class IncomesController extends Controller
{
    /**
     * Display the daily income page.
     */
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        // Filters
        $month        = $request->get('month', now()->format('Y-m'));
        $incomeHeadId = $request->get('income_head_id', '');
        $status       = $request->get('status', '');

        [$year, $mon] = explode('-', $month . '-' . now()->month);

        $query = SchoolIncome::where('school_id', $schoolId)
            ->whereYear('income_date', $year)
            ->whereMonth('income_date', $mon);

        if ($incomeHeadId) {
            $query->where('income_head_id', $incomeHeadId);
        }
        if ($status) {
            $query->where('status', $status);
        }

        $incomes = $query->with(['incomeHead', 'voucher'])->orderByDesc('income_date')->get();

        // Summary stats (excluding cancelled ones)
        $totalThisMonth = SchoolIncome::where('school_id', $schoolId)
            ->whereYear('income_date', $year)
            ->whereMonth('income_date', $mon)
            ->where('status', '!=', 'cancelled')
            ->sum('amount');

        $totalAllTime = SchoolIncome::where('school_id', $schoolId)
            ->where('status', '!=', 'cancelled')
            ->sum('amount');

        $vouchersDue = \App\Models\IncomeVoucher::where('school_id', $schoolId)
            ->where('approval_status', 'Approved')
            ->get()
            ->sum(function($v) {
                return $v->total_due;
            });

        $pendingIncomes = SchoolIncome::where('school_id', $schoolId)
            ->where('status', 'pending')
            ->sum('amount');

        $pendingAmount = $vouchersDue + $pendingIncomes;

        $incomeCount = SchoolIncome::where('school_id', $schoolId)
            ->whereYear('income_date', $year)
            ->whereMonth('income_date', $mon)
            ->count();

        // Category breakdown for chart
        $categoryBreakdown = SchoolIncome::where('school_id', $schoolId)
            ->whereYear('income_date', $year)
            ->whereMonth('income_date', $mon)
            ->where('status', '!=', 'cancelled')
            ->whereNotNull('income_head_id')
            ->selectRaw('income_head_id, SUM(amount) as total')
            ->groupBy('income_head_id')
            ->get()
            ->keyBy('income_head_id')
            ->map(fn($r) => (float) $r->total);

        // Monthly trend (last 6 months)
        $trendMonths = [];
        $trendData   = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $trendMonths[] = $m->format('M');
            $trendData[]   = (float) SchoolIncome::where('school_id', $schoolId)
                ->whereYear('income_date', $m->year)
                ->whereMonth('income_date', $m->month)
                ->where('status', '!=', 'cancelled')
                ->sum('amount');
        }

        $categories   = SchoolIncome::categories();
        $paymentModes = SchoolIncome::paymentModes();
        $incomeHeads  = IncomeHead::where('school_id', $schoolId)->orderBy('name')->get();

        return view('school.income.index', compact(
            'incomes',
            'totalThisMonth',
            'totalAllTime',
            'pendingAmount',
            'incomeCount',
            'categoryBreakdown',
            'trendMonths',
            'trendData',
            'categories',
            'paymentModes',
            'incomeHeads',
            'month',
            'incomeHeadId',
            'status'
        ));
    }

    /**
     * Store a new income.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'income_head_id' => 'required|exists:income_heads,id',
            'amount'         => 'required|numeric|min:0.01',
            'income_date'    => 'required|date',
            'payment_mode'   => 'required|string',
            'status'         => 'required|in:paid,pending,cancelled',
            'bank_name'        => 'required_if:payment_mode,cheque|nullable|string|max:255',
            'check_issue_date' => 'required_if:payment_mode,cheque|nullable|date',
            'branch'           => 'required_if:payment_mode,cheque|nullable|string|max:255',
        ]);

        $schoolId = auth()->user()->school_id;

        $income = SchoolIncome::create([
            'school_id'       => $schoolId,
            'income_head_id'  => $request->income_head_id,
            'title'           => $request->title,
            'category'        => 'other',
            'amount'          => $request->amount,
            'income_date'     => $request->income_date,
            'payment_mode'    => $request->payment_mode,
            'bank_name'       => $request->payment_mode === 'cheque' ? $request->bank_name : null,
            'check_issue_date'=> $request->payment_mode === 'cheque' ? $request->check_issue_date : null,
            'branch'          => $request->payment_mode === 'cheque' ? $request->branch : null,
            'description'     => $request->description,
            'reference_no'    => $request->reference_no,
            'receipt_no'      => $request->receipt_no,
            'received_from'   => $request->received_from,
            'status'          => $request->status,
            'created_by'      => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Income added successfully.',
            'income'  => $income,
            'invoice_url' => route('school.income.invoice', $income->id),
        ]);
    }

    /**
     * Update an income.
     */
    public function update(Request $request, SchoolIncome $income): JsonResponse
    {
        if ($income->school_id !== auth()->user()->school_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'title'          => 'required|string|max:255',
            'income_head_id' => 'required|exists:income_heads,id',
            'amount'         => 'required|numeric|min:0.01',
            'income_date'    => 'required|date',
            'payment_mode'   => 'required|string',
            'status'         => 'required|in:paid,pending,cancelled',
            'bank_name'        => 'required_if:payment_mode,cheque|nullable|string|max:255',
            'check_issue_date' => 'required_if:payment_mode,cheque|nullable|date',
            'branch'           => 'required_if:payment_mode,cheque|nullable|string|max:255',
        ]);

        $income->update([
            'title'           => $request->title,
            'income_head_id'  => $request->income_head_id,
            'category'        => 'other',
            'amount'          => $request->amount,
            'income_date'     => $request->income_date,
            'payment_mode'    => $request->payment_mode,
            'bank_name'       => $request->payment_mode === 'cheque' ? $request->bank_name : null,
            'check_issue_date'=> $request->payment_mode === 'cheque' ? $request->check_issue_date : null,
            'branch'          => $request->payment_mode === 'cheque' ? $request->branch : null,
            'description'     => $request->description,
            'reference_no'    => $request->reference_no,
            'receipt_no'      => $request->receipt_no,
            'received_from'   => $request->received_from,
            'status'          => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Income updated successfully.',
            'income'  => $income->fresh(),
            'invoice_url' => route('school.income.invoice', $income->id),
        ]);
    }

    /**
     * Delete an income.
     */
    public function destroy(SchoolIncome $income): JsonResponse
    {
        if ($income->school_id !== auth()->user()->school_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $income->delete();

        return response()->json([
            'success' => true,
            'message' => 'Income deleted.',
        ]);
    }

    /**
     * Get summary data for dashboard integration (AJAX).
     */
    public function dashboardSummary(): JsonResponse
    {
        $schoolId = auth()->user()->school_id;

        $totalIncome = (float) SchoolIncome::where('school_id', $schoolId)
            ->where('status', '!=', 'cancelled')
            ->sum('amount');

        $thisMonthIncome = (float) SchoolIncome::where('school_id', $schoolId)
            ->whereYear('income_date', now()->year)
            ->whereMonth('income_date', now()->month)
            ->where('status', '!=', 'cancelled')
            ->sum('amount');

        // Monthly data for chart
        $months = ['April','May','June','July','August','September','October','November','December','January','February'];
        $incomeData = array_fill(0, count($months), 0);

        $incomes = SchoolIncome::where('school_id', $schoolId)
            ->where('status', '!=', 'cancelled')
            ->get();

        foreach ($incomes as $i) {
            $monthName = Carbon::parse($i->income_date)->format('F');
            $idx = array_search($monthName, $months);
            if ($idx !== false) {
                $incomeData[$idx] += (float) $i->amount;
            }
        }

        return response()->json([
            'totalIncome'     => $totalIncome,
            'thisMonthIncome' => $thisMonthIncome,
            'incomeData'      => $incomeData,
            'months'          => $months,
        ]);
    }

    /**
     * Display the income and reports page.
     */
    public function reports(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $preset = $request->get('preset', 'this_month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($preset === 'this_month') {
            $startDate = now()->startOfMonth()->toDateString();
            $endDate = now()->endOfMonth()->toDateString();
        } elseif ($preset === 'last_month') {
            $startDate = now()->subMonth()->startOfMonth()->toDateString();
            $endDate = now()->subMonth()->endOfMonth()->toDateString();
        } elseif ($preset === 'this_year') {
            $startDate = now()->startOfYear()->toDateString();
            $endDate = now()->endOfYear()->toDateString();
        } elseif ($preset === 'academic_year') {
            if (now()->month >= 4) {
                $startDate = now()->setDate(now()->year, 4, 1)->toDateString();
                $endDate = now()->setDate(now()->year + 1, 3, 31)->toDateString();
            } else {
                $startDate = now()->setDate(now()->year - 1, 4, 1)->toDateString();
                $endDate = now()->setDate(now()->year, 3, 31)->toDateString();
            }
        } elseif ($preset === 'custom') {
            if (!$startDate) $startDate = now()->startOfMonth()->toDateString();
            if (!$endDate) $endDate = now()->endOfMonth()->toDateString();
        } else {
            $preset = 'this_month';
            $startDate = now()->startOfMonth()->toDateString();
            $endDate = now()->endOfMonth()->toDateString();
        }

        // Fetch School Incomes
        $schoolIncomes = SchoolIncome::where('school_id', $schoolId)
            ->whereBetween('income_date', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->get();

        // Fetch Fee collections (since they are also a primary source of income)
        $feeIncome = FeeReceipt::where('school_id', $schoolId)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->with('student')
            ->get();

        $transactions = collect();

        foreach ($schoolIncomes as $inc) {
            $transactions->push((object)[
                'id' => 'si_' . $inc->id,
                'type' => 'income',
                'category' => $inc->category_label,
                'title' => $inc->title,
                'amount' => (float)$inc->amount,
                'date' => $inc->income_date ? $inc->income_date->format('Y-m-d') : '-',
                'payment_mode' => $inc->payment_mode ? (SchoolIncome::paymentModes()[$inc->payment_mode] ?? ucfirst($inc->payment_mode)) : '-',
                'ref' => $inc->reference_no ?: ($inc->receipt_no ?: '-'),
                'payer' => $inc->received_from ?: '-',
            ]);
        }

        foreach ($feeIncome as $income) {
            $transactions->push((object)[
                'id' => 'fi_' . $income->id,
                'type' => 'fee_income',
                'category' => 'Student Fees',
                'title' => $income->student ? "Fee Collection - {$income->student->full_name} (Receipt: {$income->receipt_number})" : "Fee Collection (Receipt: {$income->receipt_number})",
                'amount' => (float)$income->amount_paid,
                'date' => $income->payment_date ? Carbon::parse($income->payment_date)->format('Y-m-d') : '-',
                'payment_mode' => ucfirst($income->payment_mode ?? 'Other'),
                'ref' => $income->transaction_id ?: '-',
                'payer' => $income->student ? $income->student->full_name : '-',
            ]);
        }

        $transactions = $transactions->sortByDesc('date');

        // Summaries
        $totalSchoolIncome = (float) $schoolIncomes->sum('amount');
        $totalFeeIncome = (float) $feeIncome->sum('amount_paid');
        $totalIncome = $totalSchoolIncome + $totalFeeIncome;

        // Chart 1: Income breakdown by category
        $incomeCategoryBreakdown = [];
        foreach ($schoolIncomes as $inc) {
            $lbl = $inc->category_label;
            $incomeCategoryBreakdown[$lbl] = ($incomeCategoryBreakdown[$lbl] ?? 0) + (float)$inc->amount;
        }
        $incomeCategoryBreakdown['Student Fees'] = ($incomeCategoryBreakdown['Student Fees'] ?? 0) + $totalFeeIncome;

        // Chart 2: Income breakdown by payment mode
        $incomeModeBreakdown = [];
        foreach ($schoolIncomes as $inc) {
            $lbl = ucfirst($inc->payment_mode ?: 'Other');
            $incomeModeBreakdown[$lbl] = ($incomeModeBreakdown[$lbl] ?? 0) + (float)$inc->amount;
        }
        foreach ($feeIncome as $inc) {
            $lbl = ucfirst($inc->payment_mode ?: 'Other');
            $incomeModeBreakdown[$lbl] = ($incomeModeBreakdown[$lbl] ?? 0) + (float)$inc->amount_paid;
        }

        // Chart 3: Monthly Trend (last 6 months)
        $trendMonths = [];
        $trendIncome = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $trendMonths[] = $m->format('M Y');

            $mFeeIncome = (float) FeeReceipt::where('school_id', $schoolId)
                ->whereYear('payment_date', $m->year)
                ->whereMonth('payment_date', $m->month)
                ->sum('amount_paid');

            $mSchoolIncome = (float) SchoolIncome::where('school_id', $schoolId)
                ->whereYear('income_date', $m->year)
                ->whereMonth('income_date', $m->month)
                ->where('status', '!=', 'cancelled')
                ->sum('amount');

            $trendIncome[] = $mFeeIncome + $mSchoolIncome;
        }

        return view('school.income.reports', compact(
            'transactions',
            'startDate',
            'endDate',
            'preset',
            'totalSchoolIncome',
            'totalFeeIncome',
            'totalIncome',
            'incomeCategoryBreakdown',
            'incomeModeBreakdown',
            'trendMonths',
            'trendIncome'
        ));
    }

    /**
     * Search student and staff payers for autocomplete selection.
     */
    public function searchPayer(Request $request): JsonResponse
    {
        $query = trim($request->get('query'));
        if (empty($query)) {
            return response()->json([]);
        }

        $schoolId = auth()->user()->school_id;

        // Fetch students
        $students = \App\Models\Student::where('school_id', $schoolId)
            ->where(function($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('admission_number', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get();

        // Fetch staff
        $staff = \App\Models\Staff::where('school_id', $schoolId)
            ->where(function($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('employee_id', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get();

        $results = [];

        foreach ($students as $student) {
            $results[] = [
                'id' => $student->id,
                'name' => $student->first_name . ' ' . $student->last_name . ' (Student - Reg: ' . $student->admission_number . ')',
                'raw_name' => $student->first_name . ' ' . $student->last_name,
                'type' => 'student',
                'ref' => $student->admission_number,
            ];
        }

        foreach ($staff as $s) {
            $results[] = [
                'id' => $s->id,
                'name' => $s->first_name . ' ' . $s->last_name . ' (Staff - ID: ' . $s->employee_id . ')',
                'raw_name' => $s->first_name . ' ' . $s->last_name,
                'type' => 'staff',
                'ref' => $s->employee_id,
            ];
        }

        return response()->json($results);
    }

    /**
     * Show a print-friendly invoice layout.
     */
    public function invoice($id)
    {
        $schoolId = auth()->user()->school_id;
        $school = auth()->user()->school;
        
        $income = SchoolIncome::where('school_id', $schoolId)->findOrFail($id);
        
        $amountInWords = $this->convertNumberToWords($income->amount);
        
        return view('school.income.invoice', compact('income', 'school', 'amountInWords'));
    }

    /**
     * Display the Cash Drawer dashboard, reports, and reconciliation calculator.
     */
    public function cashDrawer(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        // Date filter
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        // Inflows
        $incomes = SchoolIncome::where('school_id', $schoolId)
            ->where('payment_mode', 'cash')
            ->where('status', '!=', 'cancelled')
            ->whereBetween('income_date', [$startDate, $endDate])
            ->with('incomeHead')
            ->get();

        $feeReceipts = FeeReceipt::where('school_id', $schoolId)
            ->where('payment_mode', 'cash')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->with('student')
            ->get();

        // Outflows
        $expenses = SchoolExpense::where('school_id', $schoolId)
            ->where('payment_mode', 'cash')
            ->where('status', '!=', 'cancelled')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->with('expenseHead')
            ->get();

        // Transfers affecting cash
        $transfers = AccountTransfer::where('school_id', $schoolId)
            ->whereBetween('transfer_date', [$startDate, $endDate])
            ->get();

        // Combine into a chronological ledger
        $ledger = collect();

        foreach ($incomes as $inc) {
            $ledger->push((object)[
                'date' => $inc->income_date->format('Y-m-d'),
                'type' => 'Cash In',
                'source' => 'Income: ' . $inc->title,
                'head' => $inc->incomeHead->name ?? 'Other',
                'amount' => (float)$inc->amount,
                'ref' => $inc->reference_no ?: ($inc->receipt_no ?: '-'),
                'is_inflow' => true,
            ]);
        }

        foreach ($feeReceipts as $rec) {
            $ledger->push((object)[
                'date' => Carbon::parse($rec->payment_date)->format('Y-m-d'),
                'type' => 'Cash In',
                'source' => 'Fee Collection - ' . ($rec->student->full_name ?? 'Student'),
                'head' => 'Academic Fees',
                'amount' => (float)$rec->amount_paid,
                'ref' => $rec->receipt_number ?: ($rec->transaction_id ?: '-'),
                'is_inflow' => true,
            ]);
        }

        foreach ($expenses as $exp) {
            $ledger->push((object)[
                'date' => $exp->expense_date->format('Y-m-d'),
                'type' => 'Cash Out',
                'source' => 'Expense: ' . $exp->title,
                'head' => $exp->expenseHead->name ?? 'Other',
                'amount' => (float)$exp->amount,
                'ref' => $exp->reference_no ?: ($exp->receipt_no ?: '-'),
                'is_inflow' => false,
            ]);
        }

        foreach ($transfers as $tr) {
            $isToCash = (stripos($tr->to_account, 'cash') !== false);
            $isFromCash = (stripos($tr->from_account, 'cash') !== false);

            if ($isToCash && $isFromCash) {
                // Cash-to-cash transfer
                $ledger->push((object)[
                    'date' => $tr->transfer_date->format('Y-m-d'),
                    'type' => 'Cash Transfer',
                    'source' => 'Transfer: ' . $tr->from_account . ' ➔ ' . $tr->to_account,
                    'head' => 'Internal Transfer',
                    'amount' => (float)$tr->amount,
                    'ref' => 'TR-' . $tr->id,
                    'is_inflow' => null, // neutral
                ]);
            } elseif ($isToCash) {
                $ledger->push((object)[
                    'date' => $tr->transfer_date->format('Y-m-d'),
                    'type' => 'Cash In',
                    'source' => 'Transfer: ' . $tr->from_account . ' ➔ ' . $tr->to_account,
                    'head' => 'Internal Transfer',
                    'amount' => (float)$tr->amount,
                    'ref' => 'TR-' . $tr->id,
                    'is_inflow' => true,
                ]);
            } elseif ($isFromCash) {
                $ledger->push((object)[
                    'date' => $tr->transfer_date->format('Y-m-d'),
                    'type' => 'Cash Out',
                    'source' => 'Transfer: ' . $tr->from_account . ' ➔ ' . $tr->to_account,
                    'head' => 'Internal Transfer',
                    'amount' => (float)$tr->amount,
                    'ref' => 'TR-' . $tr->id,
                    'is_inflow' => false,
                ]);
            }
        }

        $ledger = $ledger->sortByDesc('date');

        // Total cash-in and cash-out (all time to get current balance)
        $allIncomesCash = (float) SchoolIncome::where('school_id', $schoolId)
            ->where('payment_mode', 'cash')
            ->where('status', '!=', 'cancelled')
            ->sum('amount');

        $allFeesCash = (float) FeeReceipt::where('school_id', $schoolId)
            ->where('payment_mode', 'cash')
            ->sum('amount_paid');

        $allExpensesCash = (float) SchoolExpense::where('school_id', $schoolId)
            ->where('payment_mode', 'cash')
            ->where('status', '!=', 'cancelled')
            ->sum('amount');

        $allTransfers = AccountTransfer::where('school_id', $schoolId)->get();
        $allTransfersIn = 0;
        $allTransfersOut = 0;
        foreach ($allTransfers as $tr) {
            $isToCash = (stripos($tr->to_account, 'cash') !== false);
            $isFromCash = (stripos($tr->from_account, 'cash') !== false);
            if ($isToCash && !$isFromCash) {
                $allTransfersIn += (float)$tr->amount;
            } elseif ($isFromCash && !$isToCash) {
                $allTransfersOut += (float)$tr->amount;
            }
        }

        $totalCashIn = $allIncomesCash + $allFeesCash + $allTransfersIn;
        $totalCashOut = $allExpensesCash + $allTransfersOut;
        $cashOnHand = $totalCashIn - $totalCashOut;

        return view('school.income.cash_drawer', compact(
            'ledger',
            'startDate',
            'endDate',
            'totalCashIn',
            'totalCashOut',
            'cashOnHand'
        ));
    }

    /**
     * Redirect to the invoice print for the latest receipt under a voucher.
     */
    public function voucherInvoice($id)
    {
        $schoolId = auth()->user()->school_id;
        $income = SchoolIncome::where('school_id', $schoolId)
            ->where('income_voucher_id', $id)
            ->orderBy('id', 'desc')
            ->first();

        if (!$income) {
            $voucher = \App\Models\IncomeVoucher::where('school_id', $schoolId)->findOrFail($id);
            $income = new SchoolIncome([
                'school_id' => $schoolId,
                'title' => 'Voucher - ' . ($voucher->reason ?: 'Approved Voucher'),
                'amount' => $voucher->amount,
                'income_date' => $voucher->income_date,
                'payment_mode' => 'N/A (Voucher)',
                'description' => $voucher->remarks ?: 'Approved Income Voucher',
                'reference_no' => $voucher->voucher_no,
                'receipt_no' => $voucher->voucher_no,
                'received_from' => 'Voucher Payee',
                'status' => 'pending',
            ]);
            // Mock relationship
            $income->setRelation('incomeHead', $voucher->incomeHead);
        }

        $amountInWords = $this->convertNumberToWords($income->amount);
        $school = auth()->user()->school;

        return view('school.income.invoice', compact('income', 'school', 'amountInWords'));
    }

    /**
     * Print all selected income invoices.
     */
    public function printAll(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $ids = explode(',', $request->query('ids', ''));
        $perPage = (int) $request->query('per_page', 2);
        $type = $request->query('type', 'income');
        
        $incomes = [];
        if ($type === 'voucher') {
            $vouchers = \App\Models\IncomeVoucher::where('school_id', $schoolId)
                ->whereIn('id', $ids)
                ->get();
                
            foreach ($vouchers as $v) {
                $income = SchoolIncome::where('school_id', $schoolId)
                    ->where('income_voucher_id', $v->id)
                    ->orderBy('id', 'desc')
                    ->first();
                if (!$income) {
                    $income = new SchoolIncome([
                        'school_id' => $schoolId,
                        'title' => 'Voucher - ' . ($v->reason ?: 'Approved Voucher'),
                        'amount' => $v->amount,
                        'income_date' => $v->income_date,
                        'payment_mode' => 'N/A (Voucher)',
                        'description' => $v->remarks ?: 'Approved Income Voucher',
                        'reference_no' => $v->voucher_no,
                        'receipt_no' => $v->voucher_no,
                        'received_from' => 'Voucher Payee',
                        'status' => 'pending',
                    ]);
                    $income->setRelation('incomeHead', $v->incomeHead);
                }
                $income->amount_in_words = $this->convertNumberToWords($income->amount);
                $incomes[] = $income;
            }
        } else {
            $incomesList = SchoolIncome::where('school_id', $schoolId)
                ->whereIn('id', $ids)
                ->get();
            foreach ($incomesList as $inc) {
                $inc->amount_in_words = $this->convertNumberToWords($inc->amount);
                $incomes[] = $inc;
            }
        }
        
        $school = auth()->user()->school;
        
        return view('school.income.print_all', compact('incomes', 'school', 'perPage'));
    }

    /**
     * Private helper to convert numbers into words for printing invoices.
     */
    private function convertNumberToWords($number)
    {
        $decimal = round($number - ($no = floor($number)), 2) * 100;
        $hundred = null;
        $digits_length = strlen($no);
        $i = 0;
        $str = array();
        $words = array(
            0 => '', 1 => 'One', 2 => 'Two',
            3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
            7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
            10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
            13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
            16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
            19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
            40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
            70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
        );
        $digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
        while( $i < $digits_length ) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += $divider == 10 ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter].$plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
            } else $str[] = null;
        }
        $Rupees = implode('', array_reverse($str));
        $paise = ($decimal > 0) ? " and " . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
        return ($Rupees ? $Rupees . ' Rupees ' : '') . $paise . ' Only';
    }
}
