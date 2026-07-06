<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionOrder;
use App\Models\School;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Display a listing of orders/payments.
     */
    public function index(Request $request): View
    {
        $query = SubscriptionOrder::with(['school', 'plan'])->latest();

        // Filter by Gateway
        if ($request->filled('gateway')) {
            $query->where('gateway', $request->gateway);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by School Name (via Join or Relation search)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('school', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('superadmin.orders.index', compact('orders'));
    }

    /**
     * Update the status of an order (e.g. manual Bank Transfer approval).
     */
    public function updateStatus(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:completed,pending,failed',
        ]);

        $order = SubscriptionOrder::findOrFail($id);
        $oldStatus = $order->status;
        
        $order->status = $request->status;
        $order->save();

        // If transitioning to completed, activate/extend subscription
        if ($order->status == 'completed' && $oldStatus != 'completed') {
            $school = School::find($order->school_id);
            $plan = Plan::find($order->plan_id);
            
            if ($school && $plan) {
                $sub = Subscription::where('school_id', $school->id)->latest()->first();
                if (!$sub) {
                    Subscription::create([
                        'school_id' => $school->id,
                        'plan_id' => $plan->id,
                        'subscription_ends_at' => Carbon::now()->addDays($plan->duration_days),
                        'status' => 'active',
                    ]);
                } else {
                    $sub->plan_id = $plan->id;
                    $currentEnd = Carbon::parse($sub->subscription_ends_at);
                    if ($currentEnd->isPast()) {
                        $sub->subscription_ends_at = Carbon::now()->addDays($plan->duration_days);
                    } else {
                        $sub->subscription_ends_at = $currentEnd->addDays($plan->duration_days);
                    }
                    $sub->status = 'active';
                    $sub->save();
                }
            }
        }

        return redirect()->route('superadmin.orders.index')
            ->with('success', "Order status updated to \"{$request->status}\" successfully.");
    }
}
