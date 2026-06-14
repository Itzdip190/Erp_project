<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        $school = app('currentSchool') ?? auth()->user()?->school;
        if (!$school) return $next($request); // superadmin has no school

        $allowed = $school->status === 'active'
                 || $school->activeSubscription() !== null;

        if (!$allowed) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subscription expired or suspended',
                ], 403);
            }
            return redirect()->route('subscription.expired');
        }

        return $next($request);
    }
}
