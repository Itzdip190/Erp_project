<?php

namespace App\Http\Middleware;

use App\Support\StaffAccessHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleAccess
{
    public function handle(Request $request, Closure $next, string $module, ?string $feature = null, string $type = 'view'): Response
    {
        if (!StaffAccessHelper::hasAccess($module, $feature, $type)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized access to this module.'], 403);
            }
            return redirect()->route('teacher.dashboard')->with('error', 'You do not have permission to access that module.');
        }

        return $next($request);
    }
}
