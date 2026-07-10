<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolExpense;
use App\Models\VehicleExpense;
use App\Models\FeeReceipt;
use App\Models\ExpenseVoucher;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class ExpensesController extends Controller
{
    /**
     * Display the expenses page.
     */
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        // Filters
        $month         = $request->get('month', now()->format('Y-m'));
        $expenseHeadId = $request->get('expense_head_id', '');
        $status        = $request->get('status', '');

        [$year, $mon] = explode('-', $month . '-' . now()->month);

        $query = SchoolExpense::where('school_id', $schoolId)
            ->whereYear('expense_date', $year)
            ->whereMonth('expense_date', $mon);

        if ($expenseHeadId) {
            $query->where('expense_head_id', $expenseHeadId);
        }
        if ($status) {
            $query->where('status', $status);
        }

        $expenses = $query->with(['expenseHead', 'voucher'])->orderByDesc('expense_date')->get();

        // Summary stats
        $totalThisMonth   = SchoolExpense::where('school_id', $schoolId)
            ->whereYear('expense_date', $year)
            ->whereMonth('expense_date', $mon)
            ->where('status', '!=', 'cancelled')
            ->sum('amount');

        $totalAllTime = SchoolExpense::where('school_id', $schoolId)
            ->where('status', '!=', 'cancelled')
            ->sum('amount');

        $vouchersDue = ExpenseVoucher::where('school_id', $schoolId)
            ->where('approval_status', 'Approved')
            ->get()
            ->sum(function($v) {
                return $v->total_due;
            });

        $pendingExpenses = SchoolExpense::where('school_id', $schoolId)
            ->where('status', 'pending')
            ->sum('amount');

        $pendingAmount = $vouchersDue + $pendingExpenses;

        $expenseCount = SchoolExpense::where('school_id', $schoolId)
            ->whereYear('expense_date', $year)
            ->whereMonth('expense_date', $mon)
            ->count();

        // Category breakdown for chart
        $categoryBreakdown = SchoolExpense::where('school_id', $schoolId)
            ->whereYear('expense_date', $year)
            ->whereMonth('expense_date', $mon)
            ->where('status', '!=', 'cancelled')
            ->whereNotNull('expense_head_id')
            ->selectRaw('expense_head_id, SUM(amount) as total')
            ->groupBy('expense_head_id')
            ->get()
            ->keyBy('expense_head_id')
            ->map(fn($r) => (float) $r->total);

        // Monthly trend (last 6 months)
        $trendMonths = [];
        $trendData   = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $trendMonths[] = $m->format('M');
            $trendData[]   = (float) SchoolExpense::where('school_id', $schoolId)
                ->whereYear('expense_date', $m->year)
                ->whereMonth('expense_date', $m->month)
                ->where('status', '!=', 'cancelled')
                ->sum('amount');
        }

        $categories  = SchoolExpense::categories();
        $paymentModes = SchoolExpense::paymentModes();
        $expenseHeads = \App\Models\ExpenseHead::where('school_id', $schoolId)->orderBy('name')->get();

        return view('school.expenses.index', compact(
            'expenses',
            'totalThisMonth',
            'totalAllTime',
            'pendingAmount',
            'expenseCount',
            'categoryBreakdown',
            'trendMonths',
            'trendData',
            'categories',
            'paymentModes',
            'expenseHeads',
            'month',
            'expenseHeadId',
            'status'
        ));
    }

    /**
     * Store a new expense.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title'           => 'required|string|max:255',
            'expense_head_id' => 'required|exists:expense_heads,id',
            'amount'          => 'required|numeric|min:0.01',
            'expense_date'    => 'required|date',
            'payment_mode'    => 'required|string',
            'status'          => 'required|in:paid,pending,cancelled',
            'bank_name'        => 'required_if:payment_mode,cheque|nullable|string|max:255',
            'check_issue_date'  => 'required_if:payment_mode,cheque|nullable|date',
            'branch'           => 'required_if:payment_mode,cheque|nullable|string|max:255',
        ]);

        $schoolId = auth()->user()->school_id;

        $expense = SchoolExpense::create([
            'school_id'       => $schoolId,
            'expense_head_id' => $request->expense_head_id,
            'title'           => $request->title,
            'category'        => 'other',
            'amount'          => $request->amount,
            'expense_date'    => $request->expense_date,
            'payment_mode'    => $request->payment_mode,
            'bank_name'       => $request->payment_mode === 'cheque' ? $request->bank_name : null,
            'check_issue_date'=> $request->payment_mode === 'cheque' ? $request->check_issue_date : null,
            'branch'          => $request->payment_mode === 'cheque' ? $request->branch : null,
            'description'     => $request->description,
            'reference_no'    => $request->reference_no,
            'receipt_no'      => $request->receipt_no,
            'paid_to'         => $request->paid_to,
            'status'          => $request->status,
            'created_by'      => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Expense added successfully.',
            'expense' => $expense,
        ]);
    }

    /**
     * Update an expense.
     */
    public function update(Request $request, SchoolExpense $expense): JsonResponse
    {
        // Ensure the expense belongs to this school
        if ($expense->school_id !== auth()->user()->school_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'title'           => 'required|string|max:255',
            'expense_head_id' => 'required|exists:expense_heads,id',
            'amount'          => 'required|numeric|min:0.01',
            'expense_date'    => 'required|date',
            'payment_mode'    => 'required|string',
            'status'          => 'required|in:paid,pending,cancelled',
            'bank_name'        => 'required_if:payment_mode,cheque|nullable|string|max:255',
            'check_issue_date'  => 'required_if:payment_mode,cheque|nullable|date',
            'branch'           => 'required_if:payment_mode,cheque|nullable|string|max:255',
        ]);

        $expense->update([
            'title'           => $request->title,
            'expense_head_id' => $request->expense_head_id,
            'category'        => 'other',
            'amount'          => $request->amount,
            'expense_date'    => $request->expense_date,
            'payment_mode'    => $request->payment_mode,
            'bank_name'       => $request->payment_mode === 'cheque' ? $request->bank_name : null,
            'check_issue_date'=> $request->payment_mode === 'cheque' ? $request->check_issue_date : null,
            'branch'          => $request->payment_mode === 'cheque' ? $request->branch : null,
            'description'     => $request->description,
            'reference_no'    => $request->reference_no,
            'receipt_no'      => $request->receipt_no,
            'paid_to'         => $request->paid_to,
            'status'          => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Expense updated successfully.',
            'expense' => $expense->fresh(),
        ]);
    }

    /**
     * Delete an expense.
     */
    public function destroy(SchoolExpense $expense): JsonResponse
    {
        if ($expense->school_id !== auth()->user()->school_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Expense deleted.',
        ]);
    }

    /**
     * Get summary data for dashboard integration (AJAX).
     */
    public function dashboardSummary(): JsonResponse
    {
        $schoolId = auth()->user()->school_id;

        $totalExpense = (float) SchoolExpense::where('school_id', $schoolId)
            ->where('status', '!=', 'cancelled')
            ->sum('amount');

        $thisMonthExpense = (float) SchoolExpense::where('school_id', $schoolId)
            ->whereYear('expense_date', now()->year)
            ->whereMonth('expense_date', now()->month)
            ->where('status', '!=', 'cancelled')
            ->sum('amount');

        // Monthly data for income-expense chart
        $months = ['April','May','June','July','August','September','October','November','December','January','February'];
        $expenseData = array_fill(0, count($months), 0);

        $expenses = SchoolExpense::where('school_id', $schoolId)
            ->where('status', '!=', 'cancelled')
            ->get();

        foreach ($expenses as $e) {
            $monthName = Carbon::parse($e->expense_date)->format('F');
            $idx = array_search($monthName, $months);
            if ($idx !== false) {
                $expenseData[$idx] += (float) $e->amount;
            }
        }

        return response()->json([
            'totalExpense'     => $totalExpense,
            'thisMonthExpense' => $thisMonthExpense,
            'expenseData'      => $expenseData,
            'months'           => $months,
        ]);
    }

    /**
     * Display the expenses and income reports page.
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

        // Fetch School Expenses
        $schoolExpenses = SchoolExpense::where('school_id', $schoolId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->get();

        // Fetch Vehicle Expenses
        $vehicleExpenses = VehicleExpense::where('school_id', $schoolId)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('vehicle')
            ->get();

        // Fetch Income (Fee Collections)
        $feeIncome = FeeReceipt::where('school_id', $schoolId)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->with('student')
            ->get();

        $transactions = collect();

        foreach ($schoolExpenses as $exp) {
            $transactions->push((object)[
                'id' => 'se_' . $exp->id,
                'type' => 'expense',
                'category' => $exp->category_label,
                'title' => $exp->title,
                'amount' => (float)$exp->amount,
                'date' => $exp->expense_date ? $exp->expense_date->format('Y-m-d') : '-',
                'payment_mode' => $exp->payment_mode ? (SchoolExpense::paymentModes()[$exp->payment_mode] ?? ucfirst($exp->payment_mode)) : '-',
                'ref' => $exp->reference_no ?: ($exp->receipt_no ?: '-'),
                'payee' => $exp->paid_to ?: '-',
            ]);
        }

        foreach ($vehicleExpenses as $exp) {
            $transactions->push((object)[
                'id' => 've_' . $exp->id,
                'type' => 'expense',
                'category' => 'Vehicle (' . ucfirst(str_replace('_', ' ', $exp->expense_type)) . ')',
                'title' => $exp->vehicle ? "Vehicle: {$exp->vehicle->vehicle_number} ({$exp->description})" : "Vehicle Expense ({$exp->description})",
                'amount' => (float)$exp->amount,
                'date' => $exp->date ? $exp->date->format('Y-m-d') : '-',
                'payment_mode' => 'N/A',
                'ref' => '-',
                'payee' => '-',
            ]);
        }

        foreach ($feeIncome as $income) {
            $transactions->push((object)[
                'id' => 'fi_' . $income->id,
                'type' => 'income',
                'category' => 'Student Fees',
                'title' => $income->student ? "Fee Collection - {$income->student->full_name} (Receipt: {$income->receipt_number})" : "Fee Collection (Receipt: {$income->receipt_number})",
                'amount' => (float)$income->amount_paid,
                'date' => $income->payment_date ? Carbon::parse($income->payment_date)->format('Y-m-d') : '-',
                'payment_mode' => ucfirst($income->payment_mode ?? 'Other'),
                'ref' => $income->transaction_id ?: '-',
                'payee' => $income->student ? $income->student->full_name : '-',
            ]);
        }

        $transactions = $transactions->sortByDesc('date');

        // Summaries
        $totalIncome = (float) $feeIncome->sum('amount_paid');
        $totalSchoolExpense = (float) $schoolExpenses->sum('amount');
        $totalVehicleExpense = (float) $vehicleExpenses->sum('amount');
        $totalExpense = $totalSchoolExpense + $totalVehicleExpense;
        $netProfit = $totalIncome - $totalExpense;

        $expenseRatio = $totalIncome > 0 ? ($totalExpense / $totalIncome) * 100 : 0;

        // Chart 1: Expense breakdown by category
        $expenseCategoryBreakdown = [];
        foreach ($schoolExpenses as $exp) {
            $lbl = $exp->category_label;
            $expenseCategoryBreakdown[$lbl] = ($expenseCategoryBreakdown[$lbl] ?? 0) + (float)$exp->amount;
        }
        foreach ($vehicleExpenses as $exp) {
            $lbl = 'Vehicle (' . ucfirst(str_replace('_', ' ', $exp->expense_type)) . ')';
            $expenseCategoryBreakdown[$lbl] = ($expenseCategoryBreakdown[$lbl] ?? 0) + (float)$exp->amount;
        }

        // Chart 2: Income breakdown by payment mode
        $incomeModeBreakdown = [];
        foreach ($feeIncome as $inc) {
            $lbl = ucfirst($inc->payment_mode ?: 'Other');
            $incomeModeBreakdown[$lbl] = ($incomeModeBreakdown[$lbl] ?? 0) + (float)$inc->amount_paid;
        }

        // Chart 3: Monthly Trend (last 6 months)
        $trendMonths = [];
        $trendIncome = [];
        $trendExpense = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $trendMonths[] = $m->format('M Y');

            $mIncome = (float) FeeReceipt::where('school_id', $schoolId)
                ->whereYear('payment_date', $m->year)
                ->whereMonth('payment_date', $m->month)
                ->sum('amount_paid');

            $mSchoolExp = (float) SchoolExpense::where('school_id', $schoolId)
                ->whereYear('expense_date', $m->year)
                ->whereMonth('expense_date', $m->month)
                ->where('status', '!=', 'cancelled')
                ->sum('amount');

            $mVehicleExp = (float) VehicleExpense::where('school_id', $schoolId)
                ->whereYear('date', $m->year)
                ->whereMonth('date', $m->month)
                ->sum('amount');

            $trendIncome[] = $mIncome;
            $trendExpense[] = $mSchoolExp + $mVehicleExp;
        }

        return view('school.expenses.reports', compact(
            'transactions',
            'startDate',
            'endDate',
            'preset',
            'totalIncome',
            'totalExpense',
            'netProfit',
            'expenseRatio',
            'expenseCategoryBreakdown',
            'incomeModeBreakdown',
            'trendMonths',
            'trendIncome',
            'trendExpense'
        ));
    }

    /**
     * Print all selected expense invoices.
     */
    public function printAll(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $ids = explode(',', $request->query('ids', ''));
        $perPage = (int) $request->query('per_page', 2);
        $type = $request->query('type', 'expense');
        
        $expenses = [];
        if ($type === 'voucher') {
            $vouchers = \App\Models\ExpenseVoucher::where('school_id', $schoolId)
                ->whereIn('id', $ids)
                ->get();
                
            foreach ($vouchers as $v) {
                $exp = SchoolExpense::where('school_id', $schoolId)
                    ->where('expense_voucher_id', $v->id)
                    ->orderBy('id', 'desc')
                    ->first();
                if (!$exp) {
                    $exp = new SchoolExpense([
                        'school_id' => $schoolId,
                        'title' => 'Voucher - ' . ($v->reason ?: 'Approved Voucher'),
                        'amount' => $v->amount,
                        'expense_date' => $v->expense_date,
                        'payment_mode' => 'N/A (Voucher)',
                        'description' => $v->remarks ?: 'Approved Expense Voucher',
                        'reference_no' => $v->voucher_no,
                        'receipt_no' => $v->voucher_no,
                        'paid_to' => $v->reason,
                        'status' => 'pending',
                    ]);
                    $exp->setRelation('expenseHead', $v->expenseHead);
                }
                $exp->amount_in_words = $this->convertNumberToWords($exp->amount);
                $expenses[] = $exp;
            }
        } elseif ($type === 'transfer') {
            $transfers = \App\Models\AccountTransfer::where('school_id', $schoolId)
                ->whereIn('id', $ids)
                ->get();
                
            foreach ($transfers as $t) {
                $exp = new SchoolExpense([
                    'school_id' => $schoolId,
                    'title' => 'Account Transfer',
                    'amount' => $t->amount,
                    'expense_date' => $t->transfer_date,
                    'payment_mode' => 'Internal Transfer',
                    'description' => 'Transfer from Account: ' . ($t->fromAccount->name ?? 'N/A') . ' to Account: ' . ($t->toAccount->name ?? 'N/A') . ($t->remarks ? ' (' . $t->remarks . ')' : ''),
                    'reference_no' => 'TRF-' . str_pad($t->id, 5, '0', STR_PAD_LEFT),
                    'receipt_no' => 'TRF-' . str_pad($t->id, 5, '0', STR_PAD_LEFT),
                    'paid_to' => $t->toAccount->name ?? 'Recipient Account',
                    'status' => 'paid',
                ]);
                $exp->amount_in_words = $this->convertNumberToWords($exp->amount);
                $expenses[] = $exp;
            }
        } else {
            $expensesList = SchoolExpense::where('school_id', $schoolId)
                ->whereIn('id', $ids)
                ->get();
            foreach ($expensesList as $e) {
                $e->amount_in_words = $this->convertNumberToWords($e->amount);
                $expenses[] = $e;
            }
        }
        
        $school = auth()->user()->school;
        
        return view('school.expenses.print_all', compact('expenses', 'school', 'perPage'));
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
