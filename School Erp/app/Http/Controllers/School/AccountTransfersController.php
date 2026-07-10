<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AccountTransfer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class AccountTransfersController extends Controller
{
    /**
     * Display a listing of transfers.
     */
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        // Default date range: April 1st of current year/session to today
        $defaultStartDate = Carbon::now()->month >= 4 
            ? Carbon::now()->setDate(Carbon::now()->year, 4, 1)->format('Y-m-d')
            : Carbon::now()->setDate(Carbon::now()->year - 1, 4, 1)->format('Y-m-d');
            
        $startDate = $request->get('start_date', $defaultStartDate);
        $endDate   = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        // Normalize dates
        if ($startDate) {
            try {
                $startDate = Carbon::parse($startDate)->format('Y-m-d');
            } catch (\Exception $e) {}
        }
        if ($endDate) {
            try {
                $endDate = Carbon::parse($endDate)->format('Y-m-d');
            } catch (\Exception $e) {}
        }

        $transfers = AccountTransfer::where('school_id', $schoolId)
            ->whereBetween('transfer_date', [$startDate, $endDate])
            ->with('creator')
            ->orderBy('transfer_date', 'desc')
            ->get();

        // Eager seed / load school bank names
        $banks = \App\Models\SchoolBank::where('school_id', $schoolId)->get();
        if ($banks->isEmpty()) {
            $defaultBanks = ['CASH', 'SBI Bank', 'HDFC Bank', 'Petty Cash'];
            foreach ($defaultBanks as $name) {
                \App\Models\SchoolBank::create([
                    'school_id' => $schoolId,
                    'bank_name' => $name,
                ]);
            }
            $banks = \App\Models\SchoolBank::where('school_id', $schoolId)->get();
        }

        return view('school.expenses.transfers', compact('transfers', 'startDate', 'endDate', 'banks'));
    }

    /**
     * Store a newly created transfer in database.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'from_account'  => 'required|string|max:255',
            'to_account'    => 'required|string|max:255',
            'amount'        => 'required|numeric|min:0.01',
            'transfer_date' => 'required|date',
            'remarks'       => 'nullable|string',
        ]);

        $schoolId = auth()->user()->school_id;

        $transfer = AccountTransfer::create([
            'school_id'     => $schoolId,
            'from_account'  => $request->from_account,
            'to_account'    => $request->to_account,
            'amount'        => $request->amount,
            'transfer_date' => $request->transfer_date,
            'remarks'       => $request->remarks,
            'created_by'    => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Account transfer recorded successfully.',
            'transfer' => $transfer->load('creator'),
        ]);
    }

    /**
     * Store a new bank name.
     */
    public function storeBank(Request $request): JsonResponse
    {
        $request->validate([
            'bank_name'  => 'required|string|max:255',
            'account_no' => 'nullable|string|max:255',
            'branch'     => 'nullable|string|max:255',
        ]);

        $schoolId = auth()->user()->school_id;

        // Check if bank name already exists for this school
        $exists = \App\Models\SchoolBank::where('school_id', $schoolId)
            ->where('bank_name', $request->bank_name)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This bank/account name already exists.',
            ], 422);
        }

        $bank = \App\Models\SchoolBank::create([
            'school_id'  => $schoolId,
            'bank_name'  => $request->bank_name,
            'account_no' => $request->account_no,
            'branch'     => $request->branch,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bank/Account created successfully.',
            'bank'    => $bank,
        ]);
    }
}
