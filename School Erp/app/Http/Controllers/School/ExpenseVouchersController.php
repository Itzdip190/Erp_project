<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\ExpenseHead;
use App\Models\ExpenseVoucher;
use App\Models\VoucherPayment;
use App\Models\SchoolExpense;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class ExpenseVouchersController extends Controller
{
    /**
     * Display datewise vouchers.
     */
    public function datewise(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        // Default filters
        $startDateInput = $request->get('start_date');
        $endDateInput   = $request->get('end_date');

        if ($startDateInput) {
            try {
                $startDate = Carbon::parse($startDateInput)->format('Y-m-d');
            } catch (\Exception $e) {
                $startDate = Carbon::now()->subMonth()->format('Y-m-d');
            }
        } else {
            $startDate = Carbon::now()->subMonth()->format('Y-m-d');
        }

        if ($endDateInput) {
            try {
                $endDate = Carbon::parse($endDateInput)->format('Y-m-d');
            } catch (\Exception $e) {
                $endDate = Carbon::now()->format('Y-m-d');
            }
        } else {
            $endDate = Carbon::now()->format('Y-m-d');
        }

        $paymentStatus = $request->get('payment_status', 'All');
        $approvalStatus = $request->get('approval_status', 'All');
        $showDeleted = $request->has('show_deleted');
        $sortBy = $request->get('sort_by', 'date_desc');
        $expenseHeadId = $request->get('expense_head_id', 'All');

        $query = ExpenseVoucher::where('school_id', $schoolId)
            ->whereBetween('expense_date', [$startDate, $endDate]);

        if ($showDeleted) {
            $query->withTrashed();
        }

        if ($paymentStatus !== 'All') {
            $query->where('payment_status', $paymentStatus);
        }

        if ($approvalStatus !== 'All') {
            $query->where('approval_status', $approvalStatus);
        }

        if ($expenseHeadId !== 'All') {
            $query->where('expense_head_id', $expenseHeadId);
        }

        // Sorting
        if ($sortBy === 'amount_asc') {
            $query->orderBy('amount', 'asc');
        } elseif ($sortBy === 'amount_desc') {
            $query->orderBy('amount', 'desc');
        } elseif ($sortBy === 'date_asc') {
            $query->orderBy('expense_date', 'asc');
        } else {
            $query->orderBy('expense_date', 'desc');
        }

        $vouchers = $query->with(['expenseHead', 'creator', 'payments'])->get();

        // Calculate KPI values (Total, Paid, Due) based on filtered query results
        // Note: For deleted vouchers, we only include them in totals if they are shown
        $totalAmount = 0;
        $totalPaid = 0;
        $totalDue = 0;

        foreach ($vouchers as $v) {
            if (!$v->deleted_at) { // typically KPIs only show active vouchers
                $totalAmount += (float) $v->amount;
                $totalPaid += (float) $v->total_paid;
                $totalDue += (float) $v->total_due;
            }
        }

        $expenseHeads = ExpenseHead::where('school_id', $schoolId)->orderBy('name')->get();

        return view('school.expenses.vouchers.datewise', compact(
            'vouchers',
            'expenseHeads',
            'startDate',
            'endDate',
            'paymentStatus',
            'approvalStatus',
            'showDeleted',
            'sortBy',
            'expenseHeadId',
            'totalAmount',
            'totalPaid',
            'totalDue'
        ));
    }

    /**
     * Display accountwise vouchers summary.
     */
    public function accountwise(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $expenseHeads = ExpenseHead::where('school_id', $schoolId)->orderBy('name')->get();

        $expenseHeadId = $request->get('expense_head_id');
        $vouchers = null;
        $totalAmount = 0;
        $totalPaid = 0;
        $totalDue = 0;

        $paymentStatus = $request->get('payment_status', 'All');
        $approvalStatus = $request->get('approval_status', 'All');
        $showDeleted = $request->has('show_deleted');
        $sortBy = $request->get('sort_by', 'date_desc');

        if ($expenseHeadId && $expenseHeadId !== 'Select Expense Head') {
            $query = ExpenseVoucher::where('school_id', $schoolId)
                ->where('expense_head_id', $expenseHeadId);

            if ($showDeleted) {
                $query->withTrashed();
            }

            if ($paymentStatus !== 'All') {
                $query->where('payment_status', $paymentStatus);
            }

            if ($approvalStatus !== 'All') {
                $query->where('approval_status', $approvalStatus);
            }

            // Sorting
            if ($sortBy === 'amount_asc') {
                $query->orderBy('amount', 'asc');
            } elseif ($sortBy === 'amount_desc') {
                $query->orderBy('amount', 'desc');
            } elseif ($sortBy === 'date_asc') {
                $query->orderBy('expense_date', 'asc');
            } else {
                $query->orderBy('expense_date', 'desc');
            }

            $vouchers = $query->with(['expenseHead', 'creator', 'payments'])->get();

            foreach ($vouchers as $v) {
                if (!$v->deleted_at) {
                    $totalAmount += (float) $v->amount;
                    $totalPaid += (float) $v->total_paid;
                    $totalDue += (float) $v->total_due;
                }
            }
        }

        return view('school.expenses.vouchers.accountwise', compact(
            'expenseHeads',
            'expenseHeadId',
            'vouchers',
            'paymentStatus',
            'approvalStatus',
            'showDeleted',
            'sortBy',
            'totalAmount',
            'totalPaid',
            'totalDue'
        ));
    }

    /**
     * Store a new voucher.
     */
    public function storeVoucher(Request $request): JsonResponse
    {
        $request->validate([
            'expense_head_id' => 'required|exists:expense_heads,id',
            'amount'          => 'required|numeric|min:0.01',
            'expense_date'    => 'required|date',
            'reason'          => 'required|string|max:255',
            'remarks'         => 'nullable|string',
            'document'        => 'nullable|file|max:5120', // max 5MB
        ]);

        $schoolId = auth()->user()->school_id;

        $documentPath = null;
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '', $file->getClientOriginalName());
            
            // Ensure uploads/expenses directory exists
            $destinationPath = public_path('uploads/expenses');
            if (!is_dir($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            $file->move($destinationPath, $filename);
            $documentPath = 'uploads/expenses/' . $filename;
        }

        $voucher = ExpenseVoucher::create([
            'school_id'       => $schoolId,
            'expense_head_id' => $request->expense_head_id,
            'amount'          => $request->amount,
            'expense_date'    => $request->expense_date,
            'reason'          => $request->reason,
            'remarks'         => $request->remarks,
            'document_path'   => $documentPath,
            'approval_status' => 'Approved', // defaults to approved as in mockup
            'payment_status'  => 'Pending',
            'created_by'      => auth()->id(),
        ]);

        // Auto generate Voucher No like Voc8
        $voucher->voucher_no = 'Voc' . $voucher->id;
        $voucher->save();

        return response()->json([
            'success' => true,
            'message' => 'Voucher created successfully.',
            'voucher' => $voucher,
        ]);
    }

    /**
     * Store payment against a voucher.
     */
    public function storePayment(Request $request, $id): JsonResponse
    {
        $schoolId = auth()->user()->school_id;
        $voucher = ExpenseVoucher::where('school_id', $schoolId)->findOrFail($id);

        $request->validate([
            'payment_date'     => 'required|date',
            'invoice_no'       => 'required|string|max:255',
            'payment_mode'     => 'required|string|in:cash,bank_transfer,cheque,upi',
            'remarks'          => 'nullable|string',
            'amount'           => 'required|numeric|min:0.01',
            'bank_name'        => 'required_if:payment_mode,cheque|nullable|string|max:255',
            'check_issue_date' => 'required_if:payment_mode,cheque|nullable|date',
            'branch'           => 'required_if:payment_mode,cheque|nullable|string|max:255',
        ]);

        $remainingDue = $voucher->total_due;

        if ((float) $request->amount > $remainingDue) {
            return response()->json([
                'success' => false,
                'message' => 'Payment amount cannot exceed the remaining due amount of ₹' . number_format($remainingDue, 2),
            ], 422);
        }

        // Create Voucher Payment
        $payment = VoucherPayment::create([
            'school_id'          => $schoolId,
            'expense_voucher_id' => $voucher->id,
            'payment_date'       => $request->payment_date,
            'invoice_no'         => $request->invoice_no,
            'payment_mode'       => $request->payment_mode,
            'bank_name'          => $request->payment_mode === 'cheque' ? $request->bank_name : null,
            'check_issue_date'   => $request->payment_mode === 'cheque' ? $request->check_issue_date : null,
            'branch'             => $request->payment_mode === 'cheque' ? $request->branch : null,
            'remarks'            => $request->remarks,
            'amount'             => $request->amount,
            'created_by'         => auth()->id(),
        ]);

        // Update voucher status
        $totalPaid = $voucher->total_paid;
        if ($totalPaid >= (float) $voucher->amount) {
            $voucher->payment_status = 'Paid';
        } elseif ($totalPaid > 0) {
            $voucher->payment_status = 'Partial';
        } else {
            $voucher->payment_status = 'Pending';
        }
        $voucher->save();

        // Sync into school_expenses so it tracks in dashboard / central reports
        SchoolExpense::create([
            'school_id'          => $schoolId,
            'expense_voucher_id' => $voucher->id,
            'voucher_payment_id' => $payment->id,
            'expense_head_id'    => $voucher->expense_head_id,
            'title'              => $voucher->expenseHead->name . ' - Payment',
            'category'           => 'other',
            'amount'             => $payment->amount,
            'expense_date'       => $payment->payment_date,
            'payment_mode'       => $payment->payment_mode,
            'bank_name'          => $payment->bank_name,
            'check_issue_date'   => $payment->check_issue_date,
            'branch'             => $payment->branch,
            'description'        => 'Payment for Voucher: ' . $voucher->voucher_no . ($payment->remarks ? ' (' . $payment->remarks . ')' : ''),
            'reference_no'       => $payment->invoice_no,
            'receipt_no'         => $voucher->voucher_no,
            'paid_to'            => $voucher->reason,
            'status'             => 'paid',
            'created_by'         => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment registered successfully.',
            'payment' => $payment->load('voucher.expenseHead', 'voucher.creator', 'voucher.payments'),
        ]);
    }

    /**
     * Reject / Cancel a voucher.
     */
    public function rejectVoucher($id): JsonResponse
    {
        $schoolId = auth()->user()->school_id;
        $voucher = ExpenseVoucher::where('school_id', $schoolId)->findOrFail($id);

        $voucher->update([
            'approval_status' => 'Rejected',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Voucher status updated to Rejected.',
        ]);
    }

    /**
     * Destroy a voucher (soft-delete).
     */
    public function destroyVoucher($id): JsonResponse
    {
        $schoolId = auth()->user()->school_id;
        $voucher = ExpenseVoucher::where('school_id', $schoolId)->findOrFail($id);

        // Delete associated payments and their school_expenses sync records
        $payments = $voucher->payments;
        foreach ($payments as $payment) {
            SchoolExpense::where('voucher_payment_id', $payment->id)->delete();
            $payment->delete();
        }

        $voucher->delete();

        return response()->json([
            'success' => true,
            'message' => 'Voucher deleted successfully.',
        ]);
    }

    /**
     * Export vouchers to CSV (Excel).
     */
    public function export(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $startDate = $request->get('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
        $endDate   = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $paymentStatus = $request->get('payment_status', 'All');
        $approvalStatus = $request->get('approval_status', 'All');
        $showDeleted = $request->has('show_deleted');
        $expenseHeadId = $request->get('expense_head_id', 'All');

        $query = ExpenseVoucher::where('school_id', $schoolId)
            ->whereBetween('expense_date', [$startDate, $endDate]);

        if ($showDeleted) {
            $query->withTrashed();
        }
        if ($paymentStatus !== 'All') {
            $query->where('payment_status', $paymentStatus);
        }
        if ($approvalStatus !== 'All') {
            $query->where('approval_status', $approvalStatus);
        }
        if ($expenseHeadId !== 'All') {
            $query->where('expense_head_id', $expenseHeadId);
        }

        $vouchers = $query->with(['expenseHead', 'creator'])->orderBy('expense_date', 'desc')->get();

        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=vouchers_export_' . now()->format('YmdHis') . '.csv',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($vouchers) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel encoding
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV Headers
            fputcsv($file, [
                'Voucher No',
                'Amount (INR)',
                'Paid Amount (INR)',
                'Due Amount (INR)',
                'Expense Date',
                'Expense Account',
                'Created By',
                'Reason',
                'Remark',
                'Approval Status',
                'Payment Status',
                'Status'
            ]);

            foreach ($vouchers as $v) {
                fputcsv($file, [
                    $v->voucher_no,
                    $v->amount,
                    $v->total_paid,
                    $v->total_due,
                    $v->expense_date ? $v->expense_date->format('Y-m-d') : '',
                    $v->expenseHead->name ?? 'N/A',
                    $v->creator->name ?? 'N/A',
                    $v->reason,
                    $v->remarks ?? '',
                    $v->approval_status,
                    $v->payment_status,
                    $v->deleted_at ? 'Deleted' : 'Active'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
