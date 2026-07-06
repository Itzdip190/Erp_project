<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\School;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifySchoolByDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        $school = null;
        $host = $request->getHost();

        // 1. Check for explicit X-School-Code header (APIs / Testing)
        $code = $request->header('X-School-Code');
        if ($code) {
            $school = School::where('code', $code)->first();
        }

        // 2. Check for custom domain matching the host if not a localhost
        if (!$school) {
            $isLocalHost = in_array($host, ['localhost', '127.0.0.1', '::1']) 
                || !str_contains($host, '.');

            if (!$isLocalHost) {
                $school = School::where('custom_domain', $host)->first();
            }
        }

        // 3. Fallbacks when on a local host OR outside the testing environment
        if (!$school) {
            $isLocalHost = in_array($host, ['localhost', '127.0.0.1', '::1']) 
                || !str_contains($host, '.');

            if ($isLocalHost || !app()->environment('testing')) {
                // Prioritize authenticated user's school if logged in
                if (auth()->check() && auth()->user()->school_id) {
                    $school = auth()->user()->school;
                }

                // Fallback to session school code
                if (!$school && session()->has('school_code')) {
                    $school = School::where('code', session('school_code'))->first();
                }

                // Fallback to local development / testing default env code
                if (!$school && app()->environment('local', 'testing')) {
                    $code = env('DEV_SCHOOL_CODE', 'YIS2024');
                    $school = School::where('code', $code)->first();
                }

                // Fallback to first school (for main app domain)
                if (!$school && !app()->environment('testing')) {
                    $school = School::first();
                }
            }
        }

        if (!$school) {
            abort(404, 'School not found');
        }

        app()->instance('currentSchool', $school);
        $request->attributes->set('school', $school);

        // Sync school code to session for consistency
        session(['school_code' => $school->code]);

        return $next($request);
    }
}
