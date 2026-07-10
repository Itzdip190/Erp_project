<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\IncomeHead;
use App\Models\IncomeVoucher;
use App\Models\VoucherReceipt;
use App\Models\SchoolIncome;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class IncomeVouchersController extends Controller
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
        $incomeHeadId = $request->get('income_head_id', 'All');

        $query = IncomeVoucher::where('school_id', $schoolId)
            ->whereBetween('income_date', [$startDate, $endDate]);

        if ($showDeleted) {
            $query->withTrashed();
        }

        if ($paymentStatus !== 'All') {
            $query->where('payment_status', $paymentStatus);
        }

        if ($approvalStatus !== 'All') {
            $query->where('approval_status', $approvalStatus);
        }

        if ($incomeHeadId !== 'All') {
            $query->where('income_head_id', $incomeHeadId);
        }

        // Sorting
        if ($sortBy === 'amount_asc') {
            $query->orderBy('amount', 'asc');
        } elseif ($sortBy === 'amount_desc') {
            $query->orderBy('amount', 'desc');
        } elseif ($sortBy === 'date_asc') {
            $query->orderBy('income_date', 'asc');
        } else {
            $query->orderBy('income_date', 'desc');
        }

        $vouchers = $query->with(['incomeHead', 'creator'])->get();

        // Calculate KPI values
        $totalAmount = 0;
        $totalPaid = 0;
        $totalDue = 0;

        foreach ($vouchers as $v) {
            if (!$v->deleted_at) {
                $totalAmount += (float) $v->amount;
                $totalPaid += (float) $v->total_paid;
                $totalDue += (float) $v->total_due;
            }
        }

        $incomeHeads = IncomeHead::where('school_id', $schoolId)->orderBy('name')->get();

        return view('school.income.vouchers.datewise', compact(
            'vouchers',
            'incomeHeads',
            'startDate',
            'endDate',
            'paymentStatus',
            'approvalStatus',
            'showDeleted',
            'sortBy',
            'incomeHeadId',
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
        $incomeHeads = IncomeHead::where('school_id', $schoolId)->orderBy('name')->get();

        $incomeHeadId = $request->get('income_head_id');
        $vouchers = null;
        $totalAmount = 0;
        $totalPaid = 0;
        $totalDue = 0;

        $paymentStatus = $request->get('payment_status', 'All');
        $approvalStatus = $request->get('approval_status', 'All');
        $showDeleted = $request->has('show_deleted');
        $sortBy = $request->get('sort_by', 'date_desc');

        if ($incomeHeadId && $incomeHeadId !== 'Select Income Head') {
            $query = IncomeVoucher::where('school_id', $schoolId)
                ->where('income_head_id', $incomeHeadId);

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
                $query->orderBy('income_date', 'asc');
            } else {
                $query->orderBy('income_date', 'desc');
            }

            $vouchers = $query->with(['incomeHead', 'creator'])->get();

            foreach ($vouchers as $v) {
                if (!$v->deleted_at) {
                    $totalAmount += (float) $v->amount;
                    $totalPaid += (float) $v->total_paid;
                    $totalDue += (float) $v->total_due;
                }
            }
        }

        return view('school.income.vouchers.accountwise', compact(
            'incomeHeads',
            'incomeHeadId',
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
            'income_head_id' => 'required|exists:income_heads,id',
            'amount'          => 'required|numeric|min:0.01',
            'income_date'    => 'required|date',
            'reason'          => 'required|string|max:255',
            'remarks'         => 'nullable|string',
            'document'        => 'nullable|file|max:5120', // max 5MB
        ]);

        $schoolId = auth()->user()->school_id;

        $documentPath = null;
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '', $file->getClientOriginalName());
            
            $destinationPath = public_path('uploads/income');
            if (!is_dir($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            $file->move($destinationPath, $filename);
            $documentPath = 'uploads/income/' . $filename;
        }

        $voucher = IncomeVoucher::create([
            'school_id'       => $schoolId,
            'income_head_id' => $request->income_head_id,
            'amount'          => $request->amount,
            'income_date'    => $request->income_date,
            'reason'          => $request->reason,
            'remarks'         => $request->remarks,
            'document_path'   => $documentPath,
            'approval_status' => 'Approved', // defaults to approved as in mockup
            'payment_status'  => 'Pending',
            'created_by'      => auth()->id(),
        ]);

        $voucher->voucher_no = 'Voc' . $voucher->id;
        $voucher->save();

        return response()->json([
            'success' => true,
            'message' => 'Voucher created successfully.',
            'voucher' => $voucher,
        ]);
    }

    /**
     * Store payment (receipt) against a voucher.
     */
    public function storePayment(Request $request, $id): JsonResponse
    {
        $schoolId = auth()->user()->school_id;
        $voucher = IncomeVoucher::where('school_id', $schoolId)->findOrFail($id);

        $request->validate([
            'receipt_date'     => 'required|date',
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
                'message' => 'Receipt amount cannot exceed the remaining due amount of ₹' . number_format($remainingDue, 2),
            ], 422);
        }

        // Create Voucher Receipt
        $receipt = VoucherReceipt::create([
            'school_id'          => $schoolId,
            'income_voucher_id' => $voucher->id,
            'receipt_date'       => $request->receipt_date,
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

        // Sync into school_incomes so it tracks in dashboard / central reports
        $income = SchoolIncome::create([
            'school_id'          => $schoolId,
            'income_voucher_id'  => $voucher->id,
            'voucher_receipt_id' => $receipt->id,
            'income_head_id'     => $voucher->income_head_id,
            'title'              => $voucher->incomeHead->name . ' - Receipt',
            'category'           => 'other',
            'amount'             => $receipt->amount,
            'income_date'        => $receipt->receipt_date,
            'payment_mode'       => $receipt->payment_mode,
            'bank_name'          => $receipt->bank_name,
            'check_issue_date'   => $receipt->check_issue_date,
            'branch'             => $receipt->branch,
            'description'        => 'Receipt for Voucher: ' . $voucher->voucher_no . ($receipt->remarks ? ' (' . $receipt->remarks . ')' : ''),
            'reference_no'       => $receipt->invoice_no,
            'receipt_no'         => $voucher->voucher_no,
            'received_from'      => $voucher->reason,
            'status'             => 'paid',
            'created_by'         => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Receipt registered successfully.',
            'receipt' => $receipt,
            'invoice_url' => route('school.income.invoice', $income->id),
        ]);
    }

    /**
     * Reject / Cancel a voucher.
     */
    public function rejectVoucher($id): JsonResponse
    {
        $schoolId = auth()->user()->school_id;
        $voucher = IncomeVoucher::where('school_id', $schoolId)->findOrFail($id);

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
        $voucher = IncomeVoucher::where('school_id', $schoolId)->findOrFail($id);

        // Delete associated receipts and their school_incomes sync records
        $receipts = $voucher->receipts;
        foreach ($receipts as $receipt) {
            SchoolIncome::where('voucher_receipt_id', $receipt->id)->delete();
            $receipt->delete();
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
        $incomeHeadId = $request->get('income_head_id', 'All');

        $query = IncomeVoucher::where('school_id', $schoolId)
            ->whereBetween('income_date', [$startDate, $endDate]);

        if ($showDeleted) {
            $query->withTrashed();
        }
        if ($paymentStatus !== 'All') {
            $query->where('payment_status', $paymentStatus);
        }
        if ($approvalStatus !== 'All') {
            $query->where('approval_status', $approvalStatus);
        }
        if ($incomeHeadId !== 'All') {
            $query->where('income_head_id', $incomeHeadId);
        }

        $vouchers = $query->with(['incomeHead', 'creator'])->orderBy('income_date', 'desc')->get();

        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=income_vouchers_export_' . now()->format('YmdHis') . '.csv',
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
                'Received Amount (INR)',
                'Due Amount (INR)',
                'Income Date',
                'Income Account',
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
                    $v->income_date ? $v->income_date->format('Y-m-d') : '',
                    $v->incomeHead->name ?? 'N/A',
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
