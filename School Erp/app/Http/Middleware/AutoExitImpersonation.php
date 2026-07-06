<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AutoExitImpersonation
{
    public function handle(Request $request, Closure $next): Response
    {
        // If the request path is for superadmin and we are currently impersonating
        if ($request->is('superadmin*') && session()->has('is_impersonating') && session()->has('original_user_id')) {
            $originalUserId = session('original_user_id');
            $originalUser = User::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)->find($originalUserId);

            if ($originalUser && $originalUser->hasRole('superadmin')) {
                // Clear impersonation session keys
                session()->forget(['is_impersonating', 'original_user_id', 'school_code']);

                // Log back in as the superadmin
                Auth::login($originalUser);
            }
        }

        return $next($request);
    }
}
