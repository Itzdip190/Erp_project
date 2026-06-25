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
        // Local + API dev bypass — stateless APIs use header
        if (app()->environment('local', 'testing')) {
            $code = $request->header('X-School-Code')
                  ?? session('school_code')
                  ?? env('DEV_SCHOOL_CODE', 'YIS2024');

            $school = School::where('code', $code)->first();
            if ($school) {
                app()->instance('currentSchool', $school);
                $request->attributes->set('school', $school);
                return $next($request);
            }
        }

        // Production: resolve by custom_domain
        $school = School::where('custom_domain', $request->getHost())->first();
        if (!$school && !app()->environment('testing')) {
            $school = School::first();
        }
        if (!$school) abort(404, 'School not found');

        app()->instance('currentSchool', $school);
        $request->attributes->set('school', $school);
        return $next($request);
    }
}
