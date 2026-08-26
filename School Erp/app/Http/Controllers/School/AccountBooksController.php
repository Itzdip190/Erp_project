<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\SchoolIncome;
use App\Models\IncomeHead;
use App\Models\SchoolExpense;
use App\Models\ExpenseHead;
use App\Models\VehicleExpense;
use App\Models\FeeReceipt;
use App\Models\AccountTransfer;
use App\Models\SchoolBank;
use App\Models\InventorySale;
use App\Models\StaffPayrollPayment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AccountBooksController extends Controller
{
    /**
     * Display Trial Balance page.
     */
    public function trialBalance(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        // Fetch academic sessions
        $academicSessions = AcademicSession::where('school_id', $schoolId)
            ->orderBy('name', 'desc')
            ->get();

        // Determine current/selected session
        $currentSession = AcademicSession::where('school_id', $schoolId)
            ->where('is_current', true)
            ->first()
            ?? $academicSessions->first();

        $selectedSessionId = $request->get('session_id', $currentSession?->id);
        $selectedSession = $academicSessions->firstWhere('id', $selectedSessionId) ?? $currentSession;

        // Determine date range (defaults to session date range or 1 month span)
        $defaultStartDate = $selectedSession?->start_date 
            ? Carbon::parse($selectedSession->start_date)->format('Y-m-d')
            : Carbon::now()->subMonth()->format('Y-m-d');
            
        $defaultEndDate = $selectedSession?->end_date && Carbon::parse($selectedSession->end_date)->isPast()
            ? Carbon::parse($selectedSession->end_date)->format('Y-m-d')
            : Carbon::now()->format('Y-m-d');

        $rawFromDate = $request->get('from_date');
        $rawToDate   = $request->get('to_date');

        $fromDate = $rawFromDate ? $this->parseDate($rawFromDate, $defaultStartDate) : $defaultStartDate;
        $toDate   = $rawToDate ? $this->parseDate($rawToDate, $defaultEndDate) : $defaultEndDate;

        // Calculate Trial Balance Data
        $trialBalanceData = $this->calculateTrialBalanceData($schoolId, $fromDate, $toDate);

        return view('school.account_books.trial_balance', [
            'academicSessions' => $academicSessions,
            'selectedSession'  => $selectedSession,
            'fromDate'         => $fromDate,
            'toDate'           => $toDate,
            'rows'             => $trialBalanceData['rows'],
            'totalDebit'       => $trialBalanceData['totalDebit'],
            'totalCredit'      => $trialBalanceData['totalCredit'],
            'netBalance'       => $trialBalanceData['netBalance'],
        ]);
    }

    /**
     * Export Trial Balance to Excel / CSV spreadsheet.
     */
    public function exportTrialBalanceExcel(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $schoolName = auth()->user()->school->name ?? 'School';

        // Session & Date Range
        $academicSessions = AcademicSession::where('school_id', $schoolId)->get();
        $currentSession = $academicSessions->where('is_current', true)->first() ?? $academicSessions->first();
        $selectedSessionId = $request->get('session_id', $currentSession?->id);
        $selectedSession = $academicSessions->firstWhere('id', $selectedSessionId) ?? $currentSession;

        $defaultStartDate = $selectedSession?->start_date 
            ? Carbon::parse($selectedSession->start_date)->format('Y-m-d')
            : Carbon::now()->subMonth()->format('Y-m-d');
        $defaultEndDate = Carbon::now()->format('Y-m-d');

        $fromDate = $request->get('from_date') ? $this->parseDate($request->get('from_date'), $defaultStartDate) : $defaultStartDate;
        $toDate   = $request->get('to_date') ? $this->parseDate($request->get('to_date'), $defaultEndDate) : $defaultEndDate;

        $data = $this->calculateTrialBalanceData($schoolId, $fromDate, $toDate);

        $fileName = 'Trial_Balance_' . date('Ymd_His') . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($schoolName, $selectedSession, $fromDate, $toDate, $data) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel compatibility
            fputs($file, "\xEF\xBB\xBF");

            // Header information
            fputcsv($file, [$schoolName]);
            fputcsv($file, ['TRIAL BALANCE REPORT']);
            fputcsv($file, ['Academic Session', $selectedSession?->name ?? 'All Sessions', 'Date Range', Carbon::parse($fromDate)->format('d-m-Y') . ' to ' . Carbon::parse($toDate)->format('d-m-Y')]);
            fputcsv($file, ['Generated On', Carbon::now()->format('d-m-Y H:i:s')]);
            fputcsv($file, []);

            // Columns
            fputcsv($file, ['S/N', 'Account Head', 'Account Type', 'Debit', 'Credit', 'Balance']);

            // Data rows
            foreach ($data['rows'] as $row) {
                fputcsv($file, [
                    $row['sn'],
                    $row['head_name'],
                    $row['account_type'],
                    number_format($row['debit'], 2, '.', ''),
                    number_format($row['credit'], 2, '.', ''),
                    number_format($row['balance'], 2, '.', '')
                ]);
            }

            // Totals row
            fputcsv($file, []);
            fputcsv($file, [
                '',
                'Total',
                '',
                number_format($data['totalDebit'], 2, '.', ''),
                number_format($data['totalCredit'], 2, '.', ''),
                number_format($data['netBalance'], 2, '.', '')
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Core calculation logic for Trial Balance.
     */
    protected function calculateTrialBalanceData($schoolId, $startDate, $endDate): array
    {
        // 1. Payment Asset Accounts (Cash, Bank, UPI, Cheque)
        $paymentAccounts = [
            'cash' => [
                'name'    => 'Cash Account',
                'type'    => 'Asset',
                'debits'  => 0.0,
                'credits' => 0.0,
            ],
            'bank' => [
                'name'    => 'Bank Account',
                'type'    => 'Asset',
                'debits'  => 0.0,
                'credits' => 0.0,
            ],
            'upi' => [
                'name'    => 'UPI / Online Account',
                'type'    => 'Asset',
                'debits'  => 0.0,
                'credits' => 0.0,
            ],
            'cheque' => [
                'name'    => 'Cheque / Clearing Account',
                'type'    => 'Asset',
                'debits'  => 0.0,
                'credits' => 0.0,
            ],
        ];

        // 2. Fetch all School Incomes in date range
        $incomes = SchoolIncome::where('school_id', $schoolId)
            ->whereBetween('income_date', [Carbon::parse($startDate)->toDateString(), Carbon::parse($endDate)->toDateString()])
            ->where('status', '!=', 'cancelled')
            ->with('incomeHead')
            ->get();

        // Dynamic Income Heads container
        $incomeHeadsData = [];
        $allIncomeHeads = IncomeHead::where('school_id', $schoolId)->orderBy('name')->get();
        foreach ($allIncomeHeads as $ih) {
            $incomeHeadsData['head_' . $ih->id] = [
                'name'    => $ih->name,
                'type'    => 'Income',
                'debits'  => 0.0,
                'credits' => 0.0,
            ];
        }

        foreach ($incomes as $inc) {
            $amt = (float) $inc->amount;
            $mode = strtolower($inc->payment_mode ?? 'cash');

            // Credit Income Head
            if ($inc->income_head_id && isset($incomeHeadsData['head_' . $inc->income_head_id])) {
                $incomeHeadsData['head_' . $inc->income_head_id]['credits'] += $amt;
            } else {
                $categoryName = $inc->category_label ?? 'Other Income';
                $catKey = 'cat_inc_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $categoryName));
                if (!isset($incomeHeadsData[$catKey])) {
                    $incomeHeadsData[$catKey] = [
                        'name'    => $categoryName,
                        'type'    => 'Income',
                        'debits'  => 0.0,
                        'credits' => 0.0,
                    ];
                }
                $incomeHeadsData[$catKey]['credits'] += $amt;
            }

            // Debit Payment Asset Account
            if (str_contains($mode, 'bank') || str_contains($mode, 'transfer') || str_contains($mode, 'card')) {
                $paymentAccounts['bank']['debits'] += $amt;
            } elseif (str_contains($mode, 'upi') || str_contains($mode, 'qr') || str_contains($mode, 'online')) {
                $paymentAccounts['upi']['debits'] += $amt;
            } elseif (str_contains($mode, 'cheque') || str_contains($mode, 'check')) {
                $paymentAccounts['cheque']['debits'] += $amt;
            } else {
                $paymentAccounts['cash']['debits'] += $amt;
            }
        }

        // 3. Fetch Fee Collections (School Fees)
        $feeReceipts = FeeReceipt::where('school_id', $schoolId)
            ->whereBetween('payment_date', [Carbon::parse($startDate)->toDateString(), Carbon::parse($endDate)->toDateString()])
            ->where(function($q) {
                $q->where('status', '!=', 'cancelled')
                  ->orWhereNull('status');
            })
            ->get();

        $totalFeeIncome = 0.0;
        foreach ($feeReceipts as $fee) {
            $amt = (float) $fee->amount_paid;
            $totalFeeIncome += $amt;
            $mode = strtolower($fee->payment_mode ?? 'cash');

            // Debit Asset Account for Fees Collected
            if (str_contains($mode, 'bank') || str_contains($mode, 'transfer') || str_contains($mode, 'card') || str_contains($mode, 'netbanking')) {
                $paymentAccounts['bank']['debits'] += $amt;
            } elseif (str_contains($mode, 'upi') || str_contains($mode, 'qr') || str_contains($mode, 'gpay') || str_contains($mode, 'phonepe') || str_contains($mode, 'paytm')) {
                $paymentAccounts['upi']['debits'] += $amt;
            } elseif (str_contains($mode, 'cheque') || str_contains($mode, 'check')) {
                $paymentAccounts['cheque']['debits'] += $amt;
            } else {
                $paymentAccounts['cash']['debits'] += $amt;
            }
        }

        // 4. Fetch Inventory Sales (Product Sales Income)
        $inventorySales = InventorySale::where('school_id', $schoolId)
            ->whereBetween('sale_date', [Carbon::parse($startDate)->toDateString(), Carbon::parse($endDate)->toDateString()])
            ->where('status', '!=', 'cancelled')
            ->get();

        $totalProductSales = 0.0;
        foreach ($inventorySales as $sale) {
            $amt = (float) ($sale->paid_amount ?? ($sale->grand_total ?? 0));
            $totalProductSales += $amt;
            $mode = strtolower($sale->payment_mode ?? 'cash');

            if (str_contains($mode, 'bank')) {
                $paymentAccounts['bank']['debits'] += $amt;
            } elseif (str_contains($mode, 'upi')) {
                $paymentAccounts['upi']['debits'] += $amt;
            } elseif (str_contains($mode, 'cheque')) {
                $paymentAccounts['cheque']['debits'] += $amt;
            } else {
                $paymentAccounts['cash']['debits'] += $amt;
            }
        }

        // 5. Fetch School Expenses in date range
        $expenses = SchoolExpense::where('school_id', $schoolId)
            ->whereBetween('expense_date', [Carbon::parse($startDate)->toDateString(), Carbon::parse($endDate)->toDateString()])
            ->where('status', '!=', 'cancelled')
            ->with('expenseHead')
            ->get();

        // Dynamic Expense Heads container
        $expenseHeadsData = [];
        $allExpenseHeads = ExpenseHead::where('school_id', $schoolId)->orderBy('name')->get();
        foreach ($allExpenseHeads as $eh) {
            $expenseHeadsData['head_' . $eh->id] = [
                'name'    => $eh->name,
                'type'    => 'Expense',
                'debits'  => 0.0,
                'credits' => 0.0,
            ];
        }

        foreach ($expenses as $exp) {
            $amt = (float) $exp->amount;
            $mode = strtolower($exp->payment_mode ?? 'cash');

            // Debit Expense Head
            if ($exp->expense_head_id && isset($expenseHeadsData['head_' . $exp->expense_head_id])) {
                $expenseHeadsData['head_' . $exp->expense_head_id]['debits'] += $amt;
            } else {
                $categoryName = $exp->category_label ?? 'Maintenance Expense';
                $catKey = 'cat_exp_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $categoryName));
                if (!isset($expenseHeadsData[$catKey])) {
                    $expenseHeadsData[$catKey] = [
                        'name'    => $categoryName,
                        'type'    => 'Expense',
                        'debits'  => 0.0,
                        'credits' => 0.0,
                    ];
                }
                $expenseHeadsData[$catKey]['debits'] += $amt;
            }

            // Credit Payment Asset Account
            if (str_contains($mode, 'bank') || str_contains($mode, 'transfer') || str_contains($mode, 'card')) {
                $paymentAccounts['bank']['credits'] += $amt;
            } elseif (str_contains($mode, 'upi') || str_contains($mode, 'qr')) {
                $paymentAccounts['upi']['credits'] += $amt;
            } elseif (str_contains($mode, 'cheque') || str_contains($mode, 'check')) {
                $paymentAccounts['cheque']['credits'] += $amt;
            } else {
                $paymentAccounts['cash']['credits'] += $amt;
            }
        }

        // 6. Fetch Vehicle Expenses
        $vehicleExpenses = VehicleExpense::where('school_id', $schoolId)
            ->whereBetween('date', [Carbon::parse($startDate)->toDateString(), Carbon::parse($endDate)->toDateString()])
            ->get();

        if ($vehicleExpenses->isNotEmpty()) {
            $vehicleExpenseTotal = (float) $vehicleExpenses->sum('amount');
            $expenseHeadsData['cat_exp_vehicle'] = [
                'name'    => 'Vehicle & Transport Expense',
                'type'    => 'Expense',
                'debits'  => $vehicleExpenseTotal,
                'credits' => 0.0,
            ];
            $paymentAccounts['cash']['credits'] += $vehicleExpenseTotal;
        }

        // 7. Fetch Account Transfers (between Cash & Bank)
        $transfers = AccountTransfer::where('school_id', $schoolId)
            ->whereBetween('transfer_date', [Carbon::parse($startDate)->toDateString(), Carbon::parse($endDate)->toDateString()])
            ->get();

        foreach ($transfers as $tr) {
            $amt = (float) $tr->amount;
            $from = strtolower($tr->from_account ?? '');
            $to   = strtolower($tr->to_account ?? '');

            // Credit from_account
            if (str_contains($from, 'cash')) {
                $paymentAccounts['cash']['credits'] += $amt;
            } else {
                $paymentAccounts['bank']['credits'] += $amt;
            }

            // Debit to_account
            if (str_contains($to, 'cash')) {
                $paymentAccounts['cash']['debits'] += $amt;
            } else {
                $paymentAccounts['bank']['debits'] += $amt;
            }
        }

        // 8. Compile All Rows
        $rows = [];
        $sn = 1;

        // A. Asset Accounts (Bank, Cash, UPI, Cheque)
        // Bank Account
        $bankDebit  = $paymentAccounts['bank']['debits'];
        $bankCredit = $paymentAccounts['bank']['credits'];
        $rows[] = [
            'sn'           => $sn++,
            'head_name'    => 'Bank Account',
            'account_type' => 'Asset',
            'debit'        => $bankDebit,
            'credit'       => $bankCredit,
            'balance'      => $bankDebit - $bankCredit,
        ];

        // Cash Account
        $cashDebit  = $paymentAccounts['cash']['debits'];
        $cashCredit = $paymentAccounts['cash']['credits'];
        $rows[] = [
            'sn'           => $sn++,
            'head_name'    => 'Cash Account',
            'account_type' => 'Asset',
            'debit'        => $cashDebit,
            'credit'       => $cashCredit,
            'balance'      => $cashDebit - $cashCredit,
        ];

        // UPI Account (if has activity)
        if ($paymentAccounts['upi']['debits'] > 0 || $paymentAccounts['upi']['credits'] > 0) {
            $upiDebit  = $paymentAccounts['upi']['debits'];
            $upiCredit = $paymentAccounts['upi']['credits'];
            $rows[] = [
                'sn'           => $sn++,
                'head_name'    => 'UPI / Online Account',
                'account_type' => 'Asset',
                'debit'        => $upiDebit,
                'credit'       => $upiCredit,
                'balance'      => $upiDebit - $upiCredit,
            ];
        }

        // Cheque Account (if has activity)
        if ($paymentAccounts['cheque']['debits'] > 0 || $paymentAccounts['cheque']['credits'] > 0) {
            $cqDebit  = $paymentAccounts['cheque']['debits'];
            $cqCredit = $paymentAccounts['cheque']['credits'];
            $rows[] = [
                'sn'           => $sn++,
                'head_name'    => 'Cheque / Clearing Account',
                'account_type' => 'Asset',
                'debit'        => $cqDebit,
                'credit'       => $cqCredit,
                'balance'      => $cqDebit - $cqCredit,
            ];
        }

        // B. Income Heads
        foreach ($incomeHeadsData as $ih) {
            $debit  = $ih['debits'];
            $credit = $ih['credits'];
            $rows[] = [
                'sn'           => $sn++,
                'head_name'    => $ih['name'],
                'account_type' => 'Income',
                'debit'        => $debit,
                'credit'       => $credit,
                'balance'      => $debit - $credit,
            ];
        }

        // Product Sales (if any)
        if ($totalProductSales > 0 && !isset($incomeHeadsData['cat_inc_productsales'])) {
            $rows[] = [
                'sn'           => $sn++,
                'head_name'    => 'Product Sales',
                'account_type' => 'Income',
                'debit'        => 0.0,
                'credit'       => $totalProductSales,
                'balance'      => -$totalProductSales,
            ];
        }

        // School Fee Income
        $rows[] = [
            'sn'           => $sn++,
            'head_name'    => 'School Fee Income',
            'account_type' => 'Income',
            'debit'        => 0.0,
            'credit'       => $totalFeeIncome,
            'balance'      => -$totalFeeIncome,
        ];

        // C. Expense Heads
        foreach ($expenseHeadsData as $eh) {
            $debit  = $eh['debits'];
            $credit = $eh['credits'];
            $rows[] = [
                'sn'           => $sn++,
                'head_name'    => $eh['name'],
                'account_type' => 'Expense',
                'debit'        => $debit,
                'credit'       => $credit,
                'balance'      => $debit - $credit,
            ];
        }

        // Totals
        $totalDebit  = array_sum(array_column($rows, 'debit'));
        $totalCredit = array_sum(array_column($rows, 'credit'));
        $netBalance  = $totalDebit - $totalCredit;

        return [
            'rows'        => $rows,
            'totalDebit'  => $totalDebit,
            'totalCredit' => $totalCredit,
            'netBalance'  => $netBalance,
        ];
    }

    /**
     * Helper to parse flexible date formats.
     */
    protected function parseDate(?string $dateStr, string $fallback): string
    {
        if (!$dateStr) return $fallback;
        try {
            return Carbon::parse($dateStr)->format('Y-m-d');
        } catch (\Exception $e) {
            return $fallback;
        }
    }

    /**
     * Display Profit & Loss page.
     */
    public function profitLoss(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        // Fetch academic sessions
        $academicSessions = AcademicSession::where('school_id', $schoolId)
            ->orderBy('name', 'desc')
            ->get();

        // Determine current/selected session
        $currentSession = AcademicSession::where('school_id', $schoolId)
            ->where('is_current', true)
            ->first()
            ?? $academicSessions->first();

        $selectedSessionId = $request->get('session_id', $currentSession?->id);
        $selectedSession = $academicSessions->firstWhere('id', $selectedSessionId) ?? $currentSession;

        // Determine date range
        $defaultStartDate = $selectedSession?->start_date 
            ? Carbon::parse($selectedSession->start_date)->format('Y-m-d')
            : Carbon::now()->subMonth()->format('Y-m-d');
            
        $defaultEndDate = $selectedSession?->end_date && Carbon::parse($selectedSession->end_date)->isPast()
            ? Carbon::parse($selectedSession->end_date)->format('Y-m-d')
            : Carbon::now()->format('Y-m-d');

        $rawFromDate = $request->get('from_date');
        $rawToDate   = $request->get('to_date');

        $fromDate = $rawFromDate ? $this->parseDate($rawFromDate, $defaultStartDate) : $defaultStartDate;
        $toDate   = $rawToDate ? $this->parseDate($rawToDate, $defaultEndDate) : $defaultEndDate;

        // Calculate Profit & Loss Data
        $plData = $this->calculateProfitLossData($schoolId, $fromDate, $toDate);

        return view('school.account_books.profit_loss', [
            'academicSessions' => $academicSessions,
            'selectedSession'  => $selectedSession,
            'fromDate'         => $fromDate,
            'toDate'           => $toDate,
            'rows'             => $plData['rows'],
            'totalIncome'      => $plData['totalIncome'],
            'totalExpense'     => $plData['totalExpense'],
            'netProfit'        => $plData['netProfit'],
            'isProfit'         => $plData['isProfit'],
        ]);
    }

    /**
     * Export Profit & Loss to Excel / CSV spreadsheet.
     */
    public function exportProfitLossExcel(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $schoolName = auth()->user()->school->name ?? 'School';

        // Session & Date Range
        $academicSessions = AcademicSession::where('school_id', $schoolId)->get();
        $currentSession = $academicSessions->where('is_current', true)->first() ?? $academicSessions->first();
        $selectedSessionId = $request->get('session_id', $currentSession?->id);
        $selectedSession = $academicSessions->firstWhere('id', $selectedSessionId) ?? $currentSession;

        $defaultStartDate = $selectedSession?->start_date 
            ? Carbon::parse($selectedSession->start_date)->format('Y-m-d')
            : Carbon::now()->subMonth()->format('Y-m-d');
        $defaultEndDate = Carbon::now()->format('Y-m-d');

        $fromDate = $request->get('from_date') ? $this->parseDate($request->get('from_date'), $defaultStartDate) : $defaultStartDate;
        $toDate   = $request->get('to_date') ? $this->parseDate($request->get('to_date'), $defaultEndDate) : $defaultEndDate;

        $data = $this->calculateProfitLossData($schoolId, $fromDate, $toDate);

        $fileName = 'Profit_Loss_' . date('Ymd_His') . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($schoolName, $selectedSession, $fromDate, $toDate, $data) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel compatibility
            fputs($file, "\xEF\xBB\xBF");

            // Header information
            fputcsv($file, [$schoolName]);
            fputcsv($file, ['PROFIT & LOSS STATEMENT']);
            fputcsv($file, ['Academic Session', $selectedSession?->name ?? 'All Sessions', 'Date Range', Carbon::parse($fromDate)->format('d-m-Y') . ' to ' . Carbon::parse($toDate)->format('d-m-Y')]);
            fputcsv($file, ['Generated On', Carbon::now()->format('d-m-Y H:i:s')]);
            fputcsv($file, []);

            // Columns
            fputcsv($file, ['S/N', 'Particular', 'Group', 'Amount']);

            // Data rows
            foreach ($data['rows'] as $row) {
                fputcsv($file, [
                    $row['sn'],
                    $row['particular'],
                    $row['group'],
                    number_format($row['amount'], 2, '.', '')
                ]);
            }

            // Summary Totals
            fputcsv($file, []);
            fputcsv($file, ['Total Income', number_format($data['totalIncome'], 2, '.', '')]);
            fputcsv($file, ['Total Expense', number_format($data['totalExpense'], 2, '.', '')]);
            fputcsv($file, [$data['isProfit'] ? 'Net Profit' : 'Net Loss', number_format(abs($data['netProfit']), 2, '.', '')]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Core calculation logic for Profit & Loss.
     */
    protected function calculateProfitLossData($schoolId, $startDate, $endDate): array
    {
        // 1. Incomes
        $incomes = SchoolIncome::where('school_id', $schoolId)
            ->whereBetween('income_date', [Carbon::parse($startDate)->toDateString(), Carbon::parse($endDate)->toDateString()])
            ->where('status', '!=', 'cancelled')
            ->with('incomeHead')
            ->get();

        $incomeHeadsData = [];
        $allIncomeHeads = IncomeHead::where('school_id', $schoolId)->orderBy('name')->get();
        foreach ($allIncomeHeads as $ih) {
            $incomeHeadsData['head_' . $ih->id] = [
                'name'   => $ih->name,
                'group'  => 'Income',
                'amount' => 0.0,
            ];
        }

        foreach ($incomes as $inc) {
            $amt = (float) $inc->amount;
            if ($inc->income_head_id && isset($incomeHeadsData['head_' . $inc->income_head_id])) {
                $incomeHeadsData['head_' . $inc->income_head_id]['amount'] += $amt;
            } else {
                $categoryName = $inc->category_label ?? 'Other Income';
                $catKey = 'cat_inc_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $categoryName));
                if (!isset($incomeHeadsData[$catKey])) {
                    $incomeHeadsData[$catKey] = [
                        'name'   => $categoryName,
                        'group'  => 'Income',
                        'amount' => 0.0,
                    ];
                }
                $incomeHeadsData[$catKey]['amount'] += $amt;
            }
        }

        // 2. Fee Collections (School Fee Income)
        $totalFeeIncome = (float) FeeReceipt::where('school_id', $schoolId)
            ->whereBetween('payment_date', [Carbon::parse($startDate)->toDateString(), Carbon::parse($endDate)->toDateString()])
            ->where(function($q) {
                $q->where('status', '!=', 'cancelled')
                  ->orWhereNull('status');
            })
            ->sum('amount_paid');

        // 3. Product Sales (Inventory Sales)
        $totalProductSales = (float) InventorySale::where('school_id', $schoolId)
            ->whereBetween('sale_date', [Carbon::parse($startDate)->toDateString(), Carbon::parse($endDate)->toDateString()])
            ->where('status', '!=', 'cancelled')
            ->sum('paid_amount');

        // 4. Expenses
        $expenses = SchoolExpense::where('school_id', $schoolId)
            ->whereBetween('expense_date', [Carbon::parse($startDate)->toDateString(), Carbon::parse($endDate)->toDateString()])
            ->where('status', '!=', 'cancelled')
            ->with('expenseHead')
            ->get();

        $expenseHeadsData = [];
        $allExpenseHeads = ExpenseHead::where('school_id', $schoolId)->orderBy('name')->get();
        foreach ($allExpenseHeads as $eh) {
            $expenseHeadsData['head_' . $eh->id] = [
                'name'   => $eh->name,
                'group'  => 'Expense',
                'amount' => 0.0,
            ];
        }

        foreach ($expenses as $exp) {
            $amt = (float) $exp->amount;
            if ($exp->expense_head_id && isset($expenseHeadsData['head_' . $exp->expense_head_id])) {
                $expenseHeadsData['head_' . $exp->expense_head_id]['amount'] += $amt;
            } else {
                $categoryName = $exp->category_label ?? 'Maintenance Expense';
                $catKey = 'cat_exp_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $categoryName));
                if (!isset($expenseHeadsData[$catKey])) {
                    $expenseHeadsData[$catKey] = [
                        'name'   => $categoryName,
                        'group'  => 'Expense',
                        'amount' => 0.0,
                    ];
                }
                $expenseHeadsData[$catKey]['amount'] += $amt;
            }
        }

        // 5. Vehicle Expenses
        $totalVehicleExpense = (float) VehicleExpense::where('school_id', $schoolId)
            ->whereBetween('date', [Carbon::parse($startDate)->toDateString(), Carbon::parse($endDate)->toDateString()])
            ->sum('amount');

        if ($totalVehicleExpense > 0) {
            $expenseHeadsData['cat_exp_vehicle'] = [
                'name'   => 'Vehicle & Transport Expense',
                'group'  => 'Expense',
                'amount' => $totalVehicleExpense,
            ];
        }

        // Compile rows (Incomes first, then Expenses)
        $rows = [];
        $sn = 1;

        // Income Rows
        foreach ($incomeHeadsData as $ih) {
            $rows[] = [
                'sn'         => $sn++,
                'particular' => $ih['name'],
                'group'      => 'Income',
                'amount'     => $ih['amount'],
            ];
        }

        if ($totalProductSales > 0 && !isset($incomeHeadsData['cat_inc_productsales'])) {
            $rows[] = [
                'sn'         => $sn++,
                'particular' => 'Product Sales',
                'group'      => 'Income',
                'amount'     => $totalProductSales,
            ];
        }

        $rows[] = [
            'sn'         => $sn++,
            'particular' => 'School Fee Income',
            'group'      => 'Income',
            'amount'     => $totalFeeIncome,
        ];

        // Expense Rows
        foreach ($expenseHeadsData as $eh) {
            $rows[] = [
                'sn'         => $sn++,
                'particular' => $eh['name'],
                'group'      => 'Expense',
                'amount'     => $eh['amount'],
            ];
        }

        $totalIncome = 0.0;
        $totalExpense = 0.0;

        foreach ($rows as $r) {
            if ($r['group'] === 'Income') {
                $totalIncome += $r['amount'];
            } else {
                $totalExpense += $r['amount'];
            }
        }

        $netProfit = $totalIncome - $totalExpense;
        $isProfit  = $netProfit >= 0;

        return [
            'rows'         => $rows,
            'totalIncome'  => $totalIncome,
            'totalExpense' => $totalExpense,
            'netProfit'    => $netProfit,
            'isProfit'     => $isProfit,
        ];
    }

    /**
     * Display Balance Sheet page.
     */
    public function balanceSheet(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        // Fetch academic sessions
        $academicSessions = AcademicSession::where('school_id', $schoolId)
            ->orderBy('name', 'desc')
            ->get();

        // Determine current/selected session
        $currentSession = AcademicSession::where('school_id', $schoolId)
            ->where('is_current', true)
            ->first()
            ?? $academicSessions->first();

        $selectedSessionId = $request->get('session_id', $currentSession?->id);
        $selectedSession = $academicSessions->firstWhere('id', $selectedSessionId) ?? $currentSession;

        // Date range (Balance Sheet is as of To Date)
        $defaultStartDate = $selectedSession?->start_date 
            ? Carbon::parse($selectedSession->start_date)->format('Y-m-d')
            : Carbon::now()->startOfYear()->format('Y-m-d');
            
        $defaultEndDate = $selectedSession?->end_date && Carbon::parse($selectedSession->end_date)->isPast()
            ? Carbon::parse($selectedSession->end_date)->format('Y-m-d')
            : Carbon::now()->format('Y-m-d');

        $rawToDate = $request->get('to_date');
        $toDate    = $rawToDate ? $this->parseDate($rawToDate, $defaultEndDate) : $defaultEndDate;
        $fromDate  = $defaultStartDate;

        // Calculate Balance Sheet Data
        $bsData = $this->calculateBalanceSheetData($schoolId, $fromDate, $toDate);

        return view('school.account_books.balance_sheet', [
            'academicSessions'   => $academicSessions,
            'selectedSession'    => $selectedSession,
            'toDate'             => $toDate,
            'rows'               => $bsData['rows'],
            'totalAssets'        => $bsData['totalAssets'],
            'totalLiabilities'   => $bsData['totalLiabilities'],
            'totalEquity'        => $bsData['totalEquity'],
            'totalLiabAndEquity' => $bsData['totalLiabAndEquity'],
        ]);
    }

    /**
     * Export Balance Sheet to Excel / CSV spreadsheet.
     */
    public function exportBalanceSheetExcel(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $schoolName = auth()->user()->school->name ?? 'School';

        // Session & Date Range
        $academicSessions = AcademicSession::where('school_id', $schoolId)->get();
        $currentSession = $academicSessions->where('is_current', true)->first() ?? $academicSessions->first();
        $selectedSessionId = $request->get('session_id', $currentSession?->id);
        $selectedSession = $academicSessions->firstWhere('id', $selectedSessionId) ?? $currentSession;

        $defaultStartDate = $selectedSession?->start_date 
            ? Carbon::parse($selectedSession->start_date)->format('Y-m-d')
            : Carbon::now()->startOfYear()->format('Y-m-d');
        $defaultEndDate = Carbon::now()->format('Y-m-d');

        $toDate   = $request->get('to_date') ? $this->parseDate($request->get('to_date'), $defaultEndDate) : $defaultEndDate;
        $fromDate = $defaultStartDate;

        $data = $this->calculateBalanceSheetData($schoolId, $fromDate, $toDate);

        $fileName = 'Balance_Sheet_' . date('Ymd_His') . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($schoolName, $selectedSession, $toDate, $data) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel compatibility
            fputs($file, "\xEF\xBB\xBF");

            // Header information
            fputcsv($file, [$schoolName]);
            fputcsv($file, ['BALANCE SHEET STATEMENT']);
            fputcsv($file, ['Academic Session', $selectedSession?->name ?? 'All Sessions', 'As of Date', Carbon::parse($toDate)->format('d-m-Y')]);
            fputcsv($file, ['Generated On', Carbon::now()->format('d-m-Y H:i:s')]);
            fputcsv($file, []);

            // Main Columns
            fputcsv($file, ['S/N', 'Particular', 'Group', 'Amount']);

            foreach ($data['rows'] as $row) {
                fputcsv($file, [
                    $row['sn'],
                    $row['particular'],
                    $row['group'],
                    number_format($row['amount'], 2, '.', '')
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['SUMMARY']);
            fputcsv($file, ['Total Assets', number_format($data['totalAssets'], 2, '.', '')]);
            fputcsv($file, ['Total Liabilities', number_format($data['totalLiabilities'], 2, '.', '')]);
            fputcsv($file, ['Total Equity', number_format($data['totalEquity'], 2, '.', '')]);
            fputcsv($file, ['Total Liabilities & Equity', number_format($data['totalLiabAndEquity'], 2, '.', '')]);

            // Proper Structured 2-Column Corporate Balance Sheet
            fputcsv($file, []);
            fputcsv($file, ['STRUCTURED BALANCE SHEET STATEMENT']);
            fputcsv($file, ['LIABILITIES & EQUITY', 'AMOUNT (INR)', 'ASSETS', 'AMOUNT (INR)']);
            
            $assetRows = array_values(array_filter($data['rows'], fn($r) => strtoupper($r['group']) === 'ASSET'));
            $liabRows  = array_values(array_filter($data['rows'], fn($r) => strtoupper($r['group']) === 'LIABILITY' || strtoupper($r['group']) === 'EQUITY'));
            $maxLen    = max(count($assetRows), count($liabRows));

            for ($i = 0; $i < $maxLen; $i++) {
                $lParticular = $liabRows[$i]['particular'] ?? '';
                $lAmount     = isset($liabRows[$i]) ? number_format($liabRows[$i]['amount'], 2, '.', '') : '';
                $aParticular = $assetRows[$i]['particular'] ?? '';
                $aAmount     = isset($assetRows[$i]) ? number_format($assetRows[$i]['amount'], 2, '.', '') : '';

                fputcsv($file, [$lParticular, $lAmount, $aParticular, $aAmount]);
            }

            fputcsv($file, ['TOTAL LIABILITIES & EQUITY', number_format($data['totalLiabAndEquity'], 2, '.', ''), 'TOTAL ASSETS', number_format($data['totalAssets'], 2, '.', '')]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Core calculation logic for Balance Sheet.
     */
    protected function calculateBalanceSheetData($schoolId, $startDate, $endDate): array
    {
        // 1. Assets Calculation (Trial balance asset balances up to to_date)
        $tbData = $this->calculateTrialBalanceData($schoolId, $startDate, $endDate);
        
        $assetRows = [];
        $liabRows = [];
        $sn = 1;

        // Extract Asset accounts
        foreach ($tbData['rows'] as $r) {
            if (strtolower($r['account_type']) === 'asset') {
                $amt = max(0, (float) $r['balance']);
                $assetRows[] = [
                    'sn'         => $sn++,
                    'particular' => $r['head_name'],
                    'group'      => 'ASSET',
                    'amount'     => $amt,
                ];
            }
        }

        // 2. Statutory Liabilities (from Staff Salary Structures & payrolls)
        $pfTotal  = (float) \App\Models\StaffSalaryStructure::where('school_id', $schoolId)->where('is_active', true)->sum('pf');
        $esiTotal = (float) \App\Models\StaffSalaryStructure::where('school_id', $schoolId)->where('is_active', true)->sum('esi');
        $tdsTotal = (float) \App\Models\StaffSalaryStructure::where('school_id', $schoolId)->where('is_active', true)->sum('tds');
        $ptTotal  = (float) \App\Models\StaffSalaryStructure::where('school_id', $schoolId)->where('is_active', true)->sum('prof_tax');

        // Check GST from inventory sales
        $gstTotal = (float) \App\Models\InventorySale::where('school_id', $schoolId)->where('status', '!=', 'cancelled')->sum('total_tax');

        $liabilities = [
            ['name' => 'ESI Payable',              'amount' => $esiTotal],
            ['name' => 'GST Payable',              'amount' => $gstTotal],
            ['name' => 'PF Payable',               'amount' => $pfTotal],
            ['name' => 'Professional Tax Payable', 'amount' => $ptTotal],
            ['name' => 'TDS Payable',              'amount' => $tdsTotal],
        ];

        foreach ($liabilities as $l) {
            $liabRows[] = [
                'sn'         => $sn++,
                'particular' => $l['name'],
                'group'      => 'LIABILITY',
                'amount'     => $l['amount'],
            ];
        }

        // Combine all rows: Assets first, then Liabilities
        $allRows = array_merge($assetRows, $liabRows);

        $totalAssets = 0.0;
        $totalLiabilities = 0.0;

        foreach ($allRows as $r) {
            if ($r['group'] === 'ASSET') {
                $totalAssets += $r['amount'];
            } elseif ($r['group'] === 'LIABILITY') {
                $totalLiabilities += $r['amount'];
            }
        }

        $totalEquity = 0.0;
        $totalLiabAndEquity = $totalLiabilities + $totalEquity;

        return [
            'rows'               => $allRows,
            'totalAssets'        => $totalAssets,
            'totalLiabilities'   => $totalLiabilities,
            'totalEquity'        => $totalEquity,
            'totalLiabAndEquity' => $totalLiabAndEquity,
        ];
    }

    /**
     * Display Ledger Report page.
     */
    public function ledgerReport(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        // Fetch academic sessions
        $academicSessions = AcademicSession::where('school_id', $schoolId)
            ->orderBy('name', 'desc')
            ->get();

        // Determine current/selected session
        $currentSession = AcademicSession::where('school_id', $schoolId)
            ->where('is_current', true)
            ->first()
            ?? $academicSessions->first();

        $selectedSessionId = $request->get('session_id', $currentSession?->id);
        $selectedSession = $academicSessions->firstWhere('id', $selectedSessionId) ?? $currentSession;

        // Date range
        $defaultStartDate = $selectedSession?->start_date 
            ? Carbon::parse($selectedSession->start_date)->format('Y-m-d')
            : Carbon::now()->subMonth()->format('Y-m-d');
            
        $defaultEndDate = $selectedSession?->end_date && Carbon::parse($selectedSession->end_date)->isPast()
            ? Carbon::parse($selectedSession->end_date)->format('Y-m-d')
            : Carbon::now()->format('Y-m-d');

        $rawFromDate = $request->get('from_date');
        $rawToDate   = $request->get('to_date');

        $fromDate = $rawFromDate ? $this->parseDate($rawFromDate, $defaultStartDate) : $defaultStartDate;
        $toDate   = $rawToDate ? $this->parseDate($rawToDate, $defaultEndDate) : $defaultEndDate;

        // Dropdown lists
        $incomeHeads = IncomeHead::where('school_id', $schoolId)->orderBy('name')->get();
        $expenseHeads = ExpenseHead::where('school_id', $schoolId)->orderBy('name')->get();

        // Filters
        $filters = [
            'voucher_type' => $request->get('voucher_type'),
            'payment_mode' => $request->get('payment_mode'),
            'income_head'  => $request->get('income_head'),
            'expense_head' => $request->get('expense_head'),
            'voucher_no'   => $request->get('voucher_no'),
        ];

        // Calculate Ledger Report Data
        $ledgerData = $this->calculateLedgerReportData($schoolId, $fromDate, $toDate, $filters);

        return view('school.account_books.ledger_report', [
            'academicSessions' => $academicSessions,
            'selectedSession'  => $selectedSession,
            'fromDate'         => $fromDate,
            'toDate'           => $toDate,
            'incomeHeads'      => $incomeHeads,
            'expenseHeads'     => $expenseHeads,
            'filters'          => $filters,
            'rows'             => $ledgerData['rows'],
            'totalDebit'       => $ledgerData['totalDebit'],
            'totalCredit'      => $ledgerData['totalCredit'],
        ]);
    }

    /**
     * Export Ledger Report to Excel / CSV spreadsheet.
     */
    public function exportLedgerReportExcel(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $schoolName = auth()->user()->school->name ?? 'School';

        // Session & Date Range
        $academicSessions = AcademicSession::where('school_id', $schoolId)->get();
        $currentSession = $academicSessions->where('is_current', true)->first() ?? $academicSessions->first();
        $selectedSessionId = $request->get('session_id', $currentSession?->id);
        $selectedSession = $academicSessions->firstWhere('id', $selectedSessionId) ?? $currentSession;

        $defaultStartDate = $selectedSession?->start_date 
            ? Carbon::parse($selectedSession->start_date)->format('Y-m-d')
            : Carbon::now()->subMonth()->format('Y-m-d');
        $defaultEndDate = Carbon::now()->format('Y-m-d');

        $fromDate = $request->get('from_date') ? $this->parseDate($request->get('from_date'), $defaultStartDate) : $defaultStartDate;
        $toDate   = $request->get('to_date') ? $this->parseDate($request->get('to_date'), $defaultEndDate) : $defaultEndDate;

        $filters = [
            'voucher_type' => $request->get('voucher_type'),
            'payment_mode' => $request->get('payment_mode'),
            'income_head'  => $request->get('income_head'),
            'expense_head' => $request->get('expense_head'),
            'voucher_no'   => $request->get('voucher_no'),
        ];

        $data = $this->calculateLedgerReportData($schoolId, $fromDate, $toDate, $filters);

        $fileName = 'Ledger_Report_' . date('Ymd_His') . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($schoolName, $selectedSession, $fromDate, $toDate, $filters, $data) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel compatibility
            fputs($file, "\xEF\xBB\xBF");

            // Header information
            fputcsv($file, [$schoolName]);
            fputcsv($file, ['LEDGER REPORT']);
            fputcsv($file, ['Academic Session', $selectedSession?->name ?? 'All Sessions', 'Date Range', Carbon::parse($fromDate)->format('d-m-Y') . ' to ' . Carbon::parse($toDate)->format('d-m-Y')]);
            if (!empty($filters['income_head'])) {
                fputcsv($file, ['Income Head Filter', $filters['income_head']]);
            }
            if (!empty($filters['expense_head'])) {
                fputcsv($file, ['Expense Head Filter', $filters['expense_head']]);
            }
            if (!empty($filters['voucher_type'])) {
                fputcsv($file, ['Voucher Type Filter', $filters['voucher_type']]);
            }
            if (!empty($filters['payment_mode'])) {
                fputcsv($file, ['Payment Mode Filter', $filters['payment_mode']]);
            }
            fputcsv($file, ['Generated On', Carbon::now()->format('d-m-Y H:i:s')]);
            fputcsv($file, []);

            // Table Header Columns
            fputcsv($file, ['S/N', 'Voucher No', 'Date', 'Voucher Type', 'Account Head', 'Payment Mode', 'Narration', 'Status', 'Debit', 'Credit']);

            foreach ($data['rows'] as $row) {
                fputcsv($file, [
                    $row['sn'],
                    $row['voucher_no'],
                    $row['date'],
                    $row['voucher_type'],
                    $row['account_head'],
                    $row['payment_mode'],
                    $row['narration'],
                    $row['status'],
                    number_format($row['debit'], 2, '.', ''),
                    number_format($row['credit'], 2, '.', '')
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, [
                '',
                'Total',
                '',
                '',
                '',
                '',
                '',
                '',
                number_format($data['totalDebit'], 2, '.', ''),
                number_format($data['totalCredit'], 2, '.', '')
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Core calculation logic for Ledger Report mapping all transactions into double entries.
     */
    protected function calculateLedgerReportData($schoolId, $startDate, $endDate, array $filters = []): array
    {
        $allEntries = [];
        $startStr = Carbon::parse($startDate)->toDateString();
        $endStr   = Carbon::parse($endDate)->toDateString();

        // 1. School Incomes (RECEIPT)
        $incomes = SchoolIncome::where('school_id', $schoolId)
            ->whereBetween('income_date', [$startStr, $endStr])
            ->where('status', '!=', 'cancelled')
            ->with('incomeHead')
            ->get();

        foreach ($incomes as $inc) {
            $amt = (float) $inc->amount;
            $voucherNo = $inc->receipt_no ?? ($inc->reference_no ?? ('REC/INC/' . str_pad($inc->id, 5, '0', STR_PAD_LEFT)));
            $date = Carbon::parse($inc->income_date)->format('d-M-Y');
            $rawDate = Carbon::parse($inc->income_date)->format('Y-m-d');
            $rawMode = strtolower($inc->payment_mode ?? 'cash');
            $modeLabel = $this->formatPaymentModeLabel($rawMode);
            $assetAccount = $this->getAssetAccountName($rawMode);
            $incomeHeadName = $inc->incomeHead?->name ?? ($inc->category_label ?? 'Other Income');
            $narration = $inc->title ?? ($inc->description ?? ('Income from ' . ($inc->received_from ?? $incomeHeadName)));

            // Credit Income Head
            $allEntries[] = [
                'raw_date'     => $rawDate,
                'voucher_no'   => $voucherNo,
                'date'         => $date,
                'voucher_type' => 'RECEIPT',
                'account_head' => $incomeHeadName,
                'payment_mode' => $modeLabel,
                'narration'    => $narration,
                'status'       => 'Confirmed',
                'debit'        => 0.0,
                'credit'       => $amt,
            ];

            // Debit Payment Asset Account
            $allEntries[] = [
                'raw_date'     => $rawDate,
                'voucher_no'   => $voucherNo,
                'date'         => $date,
                'voucher_type' => 'RECEIPT',
                'account_head' => $assetAccount,
                'payment_mode' => $modeLabel,
                'narration'    => 'Receipt deposited to ' . $assetAccount,
                'status'       => 'Confirmed',
                'debit'        => $amt,
                'credit'       => 0.0,
            ];
        }

        // 2. School Expenses (PAYMENT)
        $expenses = SchoolExpense::where('school_id', $schoolId)
            ->whereBetween('expense_date', [$startStr, $endStr])
            ->where('status', '!=', 'cancelled')
            ->with('expenseHead')
            ->get();

        foreach ($expenses as $exp) {
            $amt = (float) $exp->amount;
            $voucherNo = $exp->receipt_no ?? ($exp->reference_no ?? ('PAY/EXP/' . str_pad($exp->id, 5, '0', STR_PAD_LEFT)));
            $date = Carbon::parse($exp->expense_date)->format('d-M-Y');
            $rawDate = Carbon::parse($exp->expense_date)->format('Y-m-d');
            $rawMode = strtolower($exp->payment_mode ?? 'cash');
            $modeLabel = $this->formatPaymentModeLabel($rawMode);
            $assetAccount = $this->getAssetAccountName($rawMode);
            $expenseHeadName = $exp->expenseHead?->name ?? ($exp->category_label ?? 'Maintenance Expense');
            $narration = $exp->title ?? ($exp->description ?? ('Payment for ' . $expenseHeadName));

            // Debit Expense Head
            $allEntries[] = [
                'raw_date'     => $rawDate,
                'voucher_no'   => $voucherNo,
                'date'         => $date,
                'voucher_type' => 'PAYMENT',
                'account_head' => $expenseHeadName,
                'payment_mode' => $modeLabel,
                'narration'    => $narration,
                'status'       => 'Confirmed',
                'debit'        => $amt,
                'credit'       => 0.0,
            ];

            // Credit Payment Asset Account
            $allEntries[] = [
                'raw_date'     => $rawDate,
                'voucher_no'   => $voucherNo,
                'date'         => $date,
                'voucher_type' => 'PAYMENT',
                'account_head' => $assetAccount,
                'payment_mode' => $modeLabel,
                'narration'    => 'Payment disbursed from ' . $assetAccount,
                'status'       => 'Confirmed',
                'debit'        => 0.0,
                'credit'       => $amt,
            ];
        }

        // 3. Fee Receipts (RECEIPT)
        $feeReceipts = FeeReceipt::where('school_id', $schoolId)
            ->whereBetween('payment_date', [$startStr, $endStr])
            ->where(function($q) {
                $q->where('status', '!=', 'cancelled')
                  ->orWhereNull('status');
            })
            ->with('student')
            ->get();

        foreach ($feeReceipts as $fee) {
            $amt = (float) $fee->amount_paid;
            $voucherNo = $fee->receipt_number ?? ('REC/FEE/' . str_pad($fee->id, 5, '0', STR_PAD_LEFT));
            $date = Carbon::parse($fee->payment_date)->format('d-M-Y');
            $rawDate = Carbon::parse($fee->payment_date)->format('Y-m-d');
            $rawMode = strtolower($fee->payment_mode ?? 'cash');
            $modeLabel = $this->formatPaymentModeLabel($rawMode);
            $assetAccount = $this->getAssetAccountName($rawMode);
            $studentName = $fee->student?->full_name ?? ($fee->student_name ?? 'Student');
            $admNo = $fee->student?->admission_no ? " (Adm: {$fee->student->admission_no})" : '';

            // Credit School Fee Income
            $allEntries[] = [
                'raw_date'     => $rawDate,
                'voucher_no'   => $voucherNo,
                'date'         => $date,
                'voucher_type' => 'RECEIPT',
                'account_head' => 'School Fee Income',
                'payment_mode' => $modeLabel,
                'narration'    => "Fee Collection from {$studentName}{$admNo}",
                'status'       => 'Confirmed',
                'debit'        => 0.0,
                'credit'       => $amt,
            ];

            // Debit Payment Asset Account
            $allEntries[] = [
                'raw_date'     => $rawDate,
                'voucher_no'   => $voucherNo,
                'date'         => $date,
                'voucher_type' => 'RECEIPT',
                'account_head' => $assetAccount,
                'payment_mode' => $modeLabel,
                'narration'    => "Fee Deposit into {$assetAccount}",
                'status'       => 'Confirmed',
                'debit'        => $amt,
                'credit'       => 0.0,
            ];
        }

        // 4. Inventory Sales (RECEIPT)
        $inventorySales = InventorySale::where('school_id', $schoolId)
            ->whereBetween('sale_date', [$startStr, $endStr])
            ->where('status', '!=', 'cancelled')
            ->get();

        foreach ($inventorySales as $sale) {
            $totalAmt = (float) ($sale->paid_amount ?? ($sale->grand_total ?? 0));
            $taxAmt = (float) ($sale->total_tax ?? 0);
            $salesIncomeAmt = max(0, $totalAmt - $taxAmt);
            $voucherNo = $sale->receipt_number ?? ($sale->invoice_number ?? ('REC/INV/' . str_pad($sale->id, 5, '0', STR_PAD_LEFT)));
            $date = Carbon::parse($sale->sale_date)->format('d-M-Y');
            $rawDate = Carbon::parse($sale->sale_date)->format('Y-m-d');
            $rawMode = strtolower($sale->payment_mode ?? 'cash');
            $modeLabel = $sale->payment_mode_label ?? $this->formatPaymentModeLabel($rawMode);
            $assetAccount = $this->getAssetAccountName($rawMode);
            $custName = $sale->customer_name ?? 'Customer';

            // Credit Product Sales
            $allEntries[] = [
                'raw_date'     => $rawDate,
                'voucher_no'   => $voucherNo,
                'date'         => $date,
                'voucher_type' => 'RECEIPT',
                'account_head' => 'Product Sales',
                'payment_mode' => $modeLabel,
                'narration'    => "Sales Income ({$custName})",
                'status'       => 'Confirmed',
                'debit'        => 0.0,
                'credit'       => $salesIncomeAmt > 0 ? $salesIncomeAmt : $totalAmt,
            ];

            // Credit GST Payable if tax exists
            if ($taxAmt > 0) {
                $allEntries[] = [
                    'raw_date'     => $rawDate,
                    'voucher_no'   => $voucherNo,
                    'date'         => $date,
                    'voucher_type' => 'RECEIPT',
                    'account_head' => 'GST Payable',
                    'payment_mode' => $modeLabel,
                    'narration'    => 'GST Collected on Sale',
                    'status'       => 'Confirmed',
                    'debit'        => 0.0,
                    'credit'       => $taxAmt,
                ];
            }

            // Debit Payment Asset Account
            $allEntries[] = [
                'raw_date'     => $rawDate,
                'voucher_no'   => $voucherNo,
                'date'         => $date,
                'voucher_type' => 'RECEIPT',
                'account_head' => $assetAccount,
                'payment_mode' => $modeLabel,
                'narration'    => 'Product Sale Receipt',
                'status'       => 'Confirmed',
                'debit'        => $totalAmt,
                'credit'       => 0.0,
            ];
        }

        // 5. Vehicle Expenses (PAYMENT)
        $vehicleExpenses = VehicleExpense::where('school_id', $schoolId)
            ->whereBetween('date', [$startStr, $endStr])
            ->with('vehicle')
            ->get();

        foreach ($vehicleExpenses as $ve) {
            $amt = (float) $ve->amount;
            $voucherNo = 'PAY/VEH/' . str_pad($ve->id, 5, '0', STR_PAD_LEFT);
            $date = Carbon::parse($ve->date)->format('d-M-Y');
            $rawDate = Carbon::parse($ve->date)->format('Y-m-d');
            $vehNo = $ve->vehicle?->vehicle_number ?? 'Vehicle';

            // Debit Vehicle & Transport Expense
            $allEntries[] = [
                'raw_date'     => $rawDate,
                'voucher_no'   => $voucherNo,
                'date'         => $date,
                'voucher_type' => 'PAYMENT',
                'account_head' => 'Vehicle & Transport Expense',
                'payment_mode' => 'Cash',
                'narration'    => "Vehicle Expense ({$vehNo} - " . ($ve->expense_type ?? 'Expense') . ")",
                'status'       => 'Confirmed',
                'debit'        => $amt,
                'credit'       => 0.0,
            ];

            // Credit Cash Account
            $allEntries[] = [
                'raw_date'     => $rawDate,
                'voucher_no'   => $voucherNo,
                'date'         => $date,
                'voucher_type' => 'PAYMENT',
                'account_head' => 'Cash Account',
                'payment_mode' => 'Cash',
                'narration'    => 'Disbursed for Vehicle Expense',
                'status'       => 'Confirmed',
                'debit'        => 0.0,
                'credit'       => $amt,
            ];
        }

        // 6. Account Transfers (TRANSFER)
        $transfers = AccountTransfer::where('school_id', $schoolId)
            ->whereBetween('transfer_date', [$startStr, $endStr])
            ->get();

        foreach ($transfers as $trf) {
            $amt = (float) $trf->amount;
            $voucherNo = 'TRF/' . str_pad($trf->id, 5, '0', STR_PAD_LEFT);
            $date = Carbon::parse($trf->transfer_date)->format('d-M-Y');
            $rawDate = Carbon::parse($trf->transfer_date)->format('Y-m-d');
            $toAcc = $this->getAssetAccountName($trf->to_account ?? 'bank');
            $fromAcc = $this->getAssetAccountName($trf->from_account ?? 'cash');

            // Debit Destination
            $allEntries[] = [
                'raw_date'     => $rawDate,
                'voucher_no'   => $voucherNo,
                'date'         => $date,
                'voucher_type' => 'TRANSFER',
                'account_head' => $toAcc,
                'payment_mode' => ucfirst($trf->to_account ?? 'Bank'),
                'narration'    => "Transfer from {$fromAcc}" . ($trf->remarks ? " ({$trf->remarks})" : ''),
                'status'       => 'Confirmed',
                'debit'        => $amt,
                'credit'       => 0.0,
            ];

            // Credit Source
            $allEntries[] = [
                'raw_date'     => $rawDate,
                'voucher_no'   => $voucherNo,
                'date'         => $date,
                'voucher_type' => 'TRANSFER',
                'account_head' => $fromAcc,
                'payment_mode' => ucfirst($trf->from_account ?? 'Cash'),
                'narration'    => "Transfer to {$toAcc}" . ($trf->remarks ? " ({$trf->remarks})" : ''),
                'status'       => 'Confirmed',
                'debit'        => 0.0,
                'credit'       => $amt,
            ];
        }

        // 7. Staff Payroll Payments (PAYMENT)
        $payrollPayments = StaffPayrollPayment::where('school_id', $schoolId)
            ->whereBetween('payment_date', [$startStr, $endStr])
            ->with(['staff', 'staffPayroll'])
            ->get();

        foreach ($payrollPayments as $pp) {
            $amt = (float) $pp->amount;
            $voucherNo = 'PAY/PAYROLL/' . str_pad($pp->id, 5, '0', STR_PAD_LEFT);
            $date = Carbon::parse($pp->payment_date)->format('d-M-Y');
            $rawDate = Carbon::parse($pp->payment_date)->format('Y-m-d');
            $rawMode = strtolower($pp->payment_method ?? 'bank');
            $modeLabel = $this->formatPaymentModeLabel($rawMode);
            $assetAccount = $this->getAssetAccountName($rawMode);
            $staffName = $pp->staff?->full_name ?? 'Staff';

            // Debit Staff Salary Expense
            $allEntries[] = [
                'raw_date'     => $rawDate,
                'voucher_no'   => $voucherNo,
                'date'         => $date,
                'voucher_type' => 'PAYMENT',
                'account_head' => 'Staff Salary Expense',
                'payment_mode' => $modeLabel,
                'narration'    => "Salary Payment to {$staffName}" . ($pp->notes ? " ({$pp->notes})" : ''),
                'status'       => 'Confirmed',
                'debit'        => $amt,
                'credit'       => 0.0,
            ];

            // Credit Asset Account
            $allEntries[] = [
                'raw_date'     => $rawDate,
                'voucher_no'   => $voucherNo,
                'date'         => $date,
                'voucher_type' => 'PAYMENT',
                'account_head' => $assetAccount,
                'payment_mode' => $modeLabel,
                'narration'    => "Salary Disbursement to {$staffName}",
                'status'       => 'Confirmed',
                'debit'        => 0.0,
                'credit'       => $amt,
            ];
        }

        // Sort entries by date descending, then voucher_no
        usort($allEntries, function($a, $b) {
            $cmp = strcmp($b['raw_date'], $a['raw_date']);
            if ($cmp === 0) {
                return strcmp($b['voucher_no'], $a['voucher_no']);
            }
            return $cmp;
        });

        // Apply filters
        $filteredRows = [];
        $vTypeFilter = !empty($filters['voucher_type']) && strtolower($filters['voucher_type']) !== 'all' ? strtolower(trim($filters['voucher_type'])) : null;
        $pModeFilter = !empty($filters['payment_mode']) && strtolower($filters['payment_mode']) !== 'all' ? strtolower(trim($filters['payment_mode'])) : null;
        $iHeadFilter = !empty($filters['income_head']) && strtolower($filters['income_head']) !== 'all' ? strtolower(trim($filters['income_head'])) : null;
        $eHeadFilter = !empty($filters['expense_head']) && strtolower($filters['expense_head']) !== 'all' ? strtolower(trim($filters['expense_head'])) : null;
        $vNoFilter   = !empty($filters['voucher_no']) ? strtolower(trim($filters['voucher_no'])) : null;

        $sn = 1;
        $totalDebit = 0.0;
        $totalCredit = 0.0;

        foreach ($allEntries as $entry) {
            if ($vTypeFilter && strtolower($entry['voucher_type']) !== $vTypeFilter) {
                continue;
            }
            if ($pModeFilter && !str_contains(strtolower($entry['payment_mode']), $pModeFilter)) {
                continue;
            }
            if ($iHeadFilter && strtolower($entry['account_head']) !== $iHeadFilter) {
                continue;
            }
            if ($eHeadFilter && strtolower($entry['account_head']) !== $eHeadFilter) {
                continue;
            }
            if ($vNoFilter && !str_contains(strtolower($entry['voucher_no']), $vNoFilter)) {
                continue;
            }

            $entry['sn'] = $sn++;
            $filteredRows[] = $entry;
            $totalDebit += $entry['debit'];
            $totalCredit += $entry['credit'];
        }

        return [
            'rows'        => $filteredRows,
            'totalDebit'  => $totalDebit,
            'totalCredit' => $totalCredit,
        ];
    }

    /**
     * Format Payment Mode label
     */
    protected function formatPaymentModeLabel(string $mode): string
    {
        if (str_contains($mode, 'bank') || str_contains($mode, 'transfer') || str_contains($mode, 'card')) {
            return 'Bank';
        }
        if (str_contains($mode, 'upi') || str_contains($mode, 'qr') || str_contains($mode, 'online')) {
            return 'Online';
        }
        if (str_contains($mode, 'cheque') || str_contains($mode, 'check')) {
            return 'Cheque';
        }
        return 'Cash';
    }

    /**
     * Get Asset Account Name based on payment mode string
     */
    protected function getAssetAccountName(string $mode): string
    {
        if (str_contains($mode, 'bank') || str_contains($mode, 'transfer') || str_contains($mode, 'card')) {
            return 'Bank Account';
        }
        if (str_contains($mode, 'upi') || str_contains($mode, 'qr') || str_contains($mode, 'online')) {
            return 'UPI / Online Account';
        }
        if (str_contains($mode, 'cheque') || str_contains($mode, 'check')) {
            return 'Cheque Clearing Account';
        }
        return 'Cash Account';
    }
}
