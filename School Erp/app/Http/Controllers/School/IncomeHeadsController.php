<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\IncomeHead;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class IncomeHeadsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schoolId = auth()->user()->school_id;
        $schoolName = auth()->user()->school->name ?? 'VEDANT PUBLIC SCHOOL';
        $heads = IncomeHead::where('school_id', $schoolId)->orderBy('name')->get();

        return view('school.income.heads', compact('heads', 'schoolName'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'budget_target' => 'nullable|numeric|min:0',
        ]);

        $schoolId = auth()->user()->school_id;

        // Check if an income head with the same name already exists for this school
        $exists = IncomeHead::where('school_id', $schoolId)
            ->where('name', $request->name)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'An income head with this name already exists.',
            ], 422);
        }

        $head = IncomeHead::create([
            'school_id'     => $schoolId,
            'name'          => $request->name,
            'budget_target' => $request->get('budget_target', 0.00) ?: 0.00,
            'created_by'    => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Income head created successfully.',
            'head'    => $head,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'budget_target' => 'nullable|numeric|min:0',
        ]);

        $schoolId = auth()->user()->school_id;
        $head = IncomeHead::where('school_id', $schoolId)->findOrFail($id);

        // Check for duplicate name
        $exists = IncomeHead::where('school_id', $schoolId)
            ->where('name', $request->name)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'An income head with this name already exists.',
            ], 422);
        }

        $head->update([
            'name'          => $request->name,
            'budget_target' => $request->get('budget_target', 0.00) ?: 0.00,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Income head updated successfully.',
            'head'    => $head,
        ]);
    }

    /**
     * Update the budget target for the income head via AJAX.
     */
    public function updateBudget(Request $request, $id): JsonResponse
    {
        $request->validate([
            'budget_target' => 'required|numeric|min:0',
        ]);

        $schoolId = auth()->user()->school_id;
        $head = IncomeHead::where('school_id', $schoolId)->findOrFail($id);

        $head->update([
            'budget_target' => $request->budget_target,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Budget target updated successfully.',
            'head'    => $head,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $schoolId = auth()->user()->school_id;
        $head = IncomeHead::where('school_id', $schoolId)->findOrFail($id);

        // Check: are there vouchers under this head?
        if ($head->vouchers()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete this income head as it has active vouchers associated with it.',
            ], 422);
        }

        $head->delete();

        return response()->json([
            'success' => true,
            'message' => 'Income head deleted successfully.',
        ]);
    }
}
