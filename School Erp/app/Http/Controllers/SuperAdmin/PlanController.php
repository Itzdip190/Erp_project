<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;

class PlanController extends Controller
{
    /**
     * Display a listing of the plans.
     */
    public function index(): View
    {
        $plans = Plan::withCount('subscriptions')->orderBy('price', 'asc')->get();
        return view('superadmin.plans.index', compact('plans'));
    }

    /**
     * Store a newly created plan in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'features' => 'nullable|array',
            'features.*' => 'nullable|string|max:255',
        ]);

        // Filter out empty features
        if (isset($validated['features'])) {
            $validated['features'] = array_values(array_filter($validated['features']));
        } else {
            $validated['features'] = [];
        }

        Plan::create($validated);

        return redirect()->route('superadmin.plans.index')
            ->with('success', "Subscription plan \"{$request->name}\" created successfully!");
    }

    /**
     * Update the specified plan in storage.
     */
    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'features' => 'nullable|array',
            'features.*' => 'nullable|string|max:255',
        ]);

        // Filter out empty features
        if (isset($validated['features'])) {
            $validated['features'] = array_values(array_filter($validated['features']));
        } else {
            $validated['features'] = [];
        }

        $plan->update($validated);

        return redirect()->route('superadmin.plans.index')
            ->with('success', "Subscription plan \"{$plan->name}\" updated successfully!");
    }

    /**
     * Remove the specified plan from storage.
     */
    public function destroy(Plan $plan): RedirectResponse
    {
        if ($plan->subscriptions()->count() > 0) {
            return redirect()->route('superadmin.plans.index')
                ->with('error', "Cannot delete plan \"{$plan->name}\" because it has active subscriptions associated with it.");
        }

        $plan->delete();

        return redirect()->route('superadmin.plans.index')
            ->with('success', "Subscription plan \"{$plan->name}\" deleted successfully!");
    }
}
