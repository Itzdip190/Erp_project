<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordIsChanged
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->must_change_password) {
                // Allowed routes during mandatory password reset
                $allowedRoutes = [
                    'password.change',
                    'password.change.update',
                    'logout',
                ];

                $routeName = $request->route()?->getName();
                $isAllowed = in_array($routeName, $allowedRoutes, true) || $request->is('logout') || $request->is('change-password*');

                if (!$isAllowed) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'message' => 'Temporary password detected. Password change required.',
                            'redirect_url' => route('password.change'),
                        ], 403);
                    }

                    return redirect()->route('password.change')
                        ->with('warning', 'Your password was reset by an administrator. Please create a new password to continue.');
                }
            }
        }

        return $next($request);
    }
}
