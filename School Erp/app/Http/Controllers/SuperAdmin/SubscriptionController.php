<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Display all school subscriptions.
     */
    public function index(Request $request): View
    {
        $schools = School::with(['subscriptions.plan'])
            ->orderBy('name', 'asc')
            ->get();
            
        $plans = Plan::orderBy('price', 'asc')->get();

        return view('superadmin.subscriptions.index', compact('schools', 'plans'));
    }

    /**
     * Extend a school's active subscription by a set number of days.
     */
    public function extend(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'days' => 'required|integer|min:1',
        ]);

        $school = School::findOrFail($validated['school_id']);
        
        // Find or create subscription
        $subscription = Subscription::where('school_id', $school->id)->latest()->first();

        if (!$subscription) {
            // If they have no subscription, default to first plan for 30 days
            $plan = Plan::first();
            if (!$plan) {
                return redirect()->route('superadmin.subscriptions.index')
                    ->with('error', "No plans are defined. Please create a subscription plan first.");
            }
            $subscription = Subscription::create([
                'school_id' => $school->id,
                'plan_id' => $plan->id,
                'subscription_ends_at' => Carbon::now()->addDays($validated['days']),
                'status' => 'active',
            ]);
        } else {
            // If subscription exists, add days
            $currentEnd = Carbon::parse($subscription->subscription_ends_at);
            
            // If expired, start from now
            if ($currentEnd->isPast()) {
                $subscription->subscription_ends_at = Carbon::now()->addDays($validated['days']);
            } else {
                $subscription->subscription_ends_at = $currentEnd->addDays($validated['days']);
            }
            
            $subscription->status = 'active';
            $subscription->save();
        }

        return redirect()->route('superadmin.subscriptions.index')
            ->with('success', "Extended subscription for \"{$school->name}\" by {$validated['days']} days.");
    }

    /**
     * Change a school's subscription plan.
     */
    public function changePlan(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'plan_id' => 'required|exists:plans,id',
        ]);

        $school = School::findOrFail($validated['school_id']);
        $plan = Plan::findOrFail($validated['plan_id']);
        
        $subscription = Subscription::where('school_id', $school->id)->latest()->first();

        if (!$subscription) {
            Subscription::create([
                'school_id' => $school->id,
                'plan_id' => $plan->id,
                'subscription_ends_at' => Carbon::now()->addDays($plan->duration_days),
                'status' => 'active',
            ]);
        } else {
            $subscription->plan_id = $plan->id;
            // Also reset expiry date based on the new plan duration if selected, or preserve?
            // Usually, changing a plan resets duration to the plan's base duration
            $subscription->subscription_ends_at = Carbon::now()->addDays($plan->duration_days);
            $subscription->status = 'active';
            $subscription->save();
        }

        return redirect()->route('superadmin.subscriptions.index')
            ->with('success', "Updated plan for \"{$school->name}\" to \"{$plan->name}\".");
    }

    /**
     * Cancel/Suspend a school subscription.
     */
    public function cancel(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
        ]);

        $school = School::findOrFail($validated['school_id']);
        $subscription = Subscription::where('school_id', $school->id)->latest()->first();

        if ($subscription) {
            $subscription->status = 'suspended';
            $subscription->subscription_ends_at = Carbon::now();
            $subscription->save();
        }

        return redirect()->route('superadmin.subscriptions.index')
            ->with('success', "Suspended subscription for \"{$school->name}\".");
    }
}
