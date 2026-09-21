<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AcademicSession;

class EnforceAcademicSessionScope
{
    /**
     * Enforce and propagate the active academic session across all requests,
     * ensuring delegated restricted admins only see data for their assigned session,
     * and Main Admin's chosen session persists smoothly across pages without changing page architecture.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user) {
            $school = app()->bound('currentSchool') ? app('currentSchool') : null;
            $schoolId = (int) ($user->school_id ?: ($school?->id ?: 0));

            if ($schoolId > 0) {
                // 1. Check if user is a restricted delegated admin
                $restrictedSessionId = method_exists($user, 'getAllowedAcademicSessionId')
                    ? $user->getAllowedAcademicSessionId()
                    : null;

                if ($restrictedSessionId) {
                    // Force session parameters in the request so all controllers receive it
                    $request->merge([
                        'academic_session_id' => $restrictedSessionId,
                        'session_id'          => $restrictedSessionId,
                    ]);

                    // Also keep browser session synchronized
                    session(['admin_selected_session_id' => $restrictedSessionId]);

                    return $next($request);
                }

                // 2. Unrestricted Admin / Superadmin: Resolve active session dynamically
                $currentSession = AcademicSession::resolveCurrentSessionForUser($user, $schoolId);
                if ($currentSession) {
                    session(['admin_selected_session_id' => $currentSession->id]);

                    if (!$request->has('academic_session_id') || $request->get('academic_session_id') === '' || $request->get('academic_session_id') === null) {
                        $request->merge([
                            'academic_session_id' => $currentSession->id,
                            'session_id'          => $currentSession->id,
                        ]);
                    }
                }
            }
        }

        return $next($request);
    }
}
