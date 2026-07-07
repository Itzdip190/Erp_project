<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\ExpenseHead;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ExpenseHeadsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schoolId = auth()->user()->school_id;
        $schoolName = auth()->user()->school->name ?? 'VEDANT PUBLIC SCHOOL';
        $heads = ExpenseHead::where('school_id', $schoolId)->orderBy('name')->get();

        return view('school.expenses.heads', compact('heads', 'schoolName'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $schoolId = auth()->user()->school_id;

        // Check if an expense head with the same name already exists for this school
        $exists = ExpenseHead::where('school_id', $schoolId)
            ->where('name', $request->name)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'An expense head with this name already exists.',
            ], 422);
        }

        $head = ExpenseHead::create([
            'school_id'  => $schoolId,
            'name'       => $request->name,
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Expense head created successfully.',
            'head'    => $head,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $schoolId = auth()->user()->school_id;
        $head = ExpenseHead::where('school_id', $schoolId)->findOrFail($id);

        // Check for duplicate name
        $exists = ExpenseHead::where('school_id', $schoolId)
            ->where('name', $request->name)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'An expense head with this name already exists.',
            ], 422);
        }

        $head->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Expense head updated successfully.',
            'head'    => $head,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $schoolId = auth()->user()->school_id;
        $head = ExpenseHead::where('school_id', $schoolId)->findOrFail($id);

        // Optional check: are there vouchers under this head?
        if ($head->vouchers()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete this expense head as it has active vouchers associated with it.',
            ], 422);
        }

        $head->delete();

        return response()->json([
            'success' => true,
            'message' => 'Expense head deleted successfully.',
        ]);
    }
}
