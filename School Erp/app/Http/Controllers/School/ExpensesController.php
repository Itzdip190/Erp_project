<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolExpense;
use App\Models\VehicleExpense;
use App\Models\FeeReceipt;
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
        $month    = $request->get('month', now()->format('Y-m'));
        $category = $request->get('category', '');
        $status   = $request->get('status', '');

        [$year, $mon] = explode('-', $month . '-' . now()->month);

        $query = SchoolExpense::where('school_id', $schoolId)
            ->whereYear('expense_date', $year)
            ->whereMonth('expense_date', $mon);

        if ($category) {
            $query->where('category', $category);
        }
        if ($status) {
            $query->where('status', $status);
        }

        $expenses = $query->orderByDesc('expense_date')->get();

        // Summary stats
        $totalThisMonth   = SchoolExpense::where('school_id', $schoolId)
            ->whereYear('expense_date', $year)
            ->whereMonth('expense_date', $mon)
            ->where('status', '!=', 'cancelled')
            ->sum('amount');

        $totalAllTime = SchoolExpense::where('school_id', $schoolId)
            ->where('status', '!=', 'cancelled')
            ->sum('amount');

        $pendingAmount = SchoolExpense::where('school_id', $schoolId)
            ->where('status', 'pending')
            ->sum('amount');

        $expenseCount = SchoolExpense::where('school_id', $schoolId)
            ->whereYear('expense_date', $year)
            ->whereMonth('expense_date', $mon)
            ->count();

        // Category breakdown for chart
        $categoryBreakdown = SchoolExpense::where('school_id', $schoolId)
            ->whereYear('expense_date', $year)
            ->whereMonth('expense_date', $mon)
            ->where('status', '!=', 'cancelled')
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get()
            ->keyBy('category')
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
            'month',
            'category',
            'status'
        ));
    }

    /**
     * Store a new expense.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'category'     => 'required|string',
            'amount'       => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'payment_mode' => 'required|string',
            'status'       => 'required|in:paid,pending,cancelled',
        ]);

        $schoolId = auth()->user()->school_id;

        $expense = SchoolExpense::create([
            'school_id'    => $schoolId,
            'title'        => $request->title,
            'category'     => $request->category,
            'amount'       => $request->amount,
            'expense_date' => $request->expense_date,
            'payment_mode' => $request->payment_mode,
            'description'  => $request->description,
            'reference_no' => $request->reference_no,
            'receipt_no'   => $request->receipt_no,
            'paid_to'      => $request->paid_to,
            'status'       => $request->status,
            'created_by'   => auth()->id(),
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
            'title'        => 'required|string|max:255',
            'category'     => 'required|string',
            'amount'       => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'payment_mode' => 'required|string',
            'status'       => 'required|in:paid,pending,cancelled',
        ]);

        $expense->update($request->only([
            'title', 'category', 'amount', 'expense_date',
            'payment_mode', 'description', 'reference_no',
            'receipt_no', 'paid_to', 'status',
        ]));

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
}
