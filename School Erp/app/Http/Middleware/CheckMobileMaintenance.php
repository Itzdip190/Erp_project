<?php

namespace App\Http\Middleware;

use App\Services\SettingService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckMobileMaintenance
{
    /**
     * Handle an incoming request and enforce Mobile App Maintenance Mode per school.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $schoolId = $user ? $user->school_id : (app()->bound('currentSchool') ? app('currentSchool')?->id : null);

        if (!$schoolId && $request->filled('school_id')) {
            $schoolId = (int) $request->input('school_id');
        }

        if (!$schoolId) {
            return $next($request);
        }

        $isMaintActive = SettingService::get('mobile_app_maintenance', '0', $schoolId) == '1';

        if (!$isMaintActive) {
            return $next($request);
        }

        $allowStaffBypass = SettingService::get('mobile_bypass_staff_maintenance', '1', $schoolId) == '1';
        $maintTitle = SettingService::get('mobile_maintenance_title', 'Under Scheduled Maintenance', $schoolId) ?: 'Under Scheduled Maintenance';
        $maintMsg = SettingService::get('mobile_maintenance_msg', 'We are performing regular server optimizations. The mobile app will be back online shortly.', $schoolId) ?: 'We are performing regular server optimizations. The mobile app will be back online shortly.';

        // Check if user is staff with bypass privilege
        if ($user) {
            $isStaff = $user->hasRole('school_admin') || $user->hasRole('superadmin') || $user->hasRole('teacher') || $user->hasRole('staff') || $user->role === 'school_admin' || $user->role === 'teacher';
            if ($isStaff && $allowStaffBypass) {
                return $next($request);
            }
        }

        // Return JSON 503 for API or AJAX requests
        if ($request->expectsJson() || $request->is('api*')) {
            return response()->json([
                'success' => false,
                'status' => 'maintenance',
                'code' => 'MAINTENANCE_MODE',
                'title' => $maintTitle,
                'message' => $maintMsg,
            ], 503);
        }

        // Return standalone responsive Maintenance View for WebView/Web
        return response()->view('errors.mobile_maintenance', [
            'maintTitle' => $maintTitle,
            'maintMsg' => $maintMsg,
            'schoolId' => $schoolId,
        ], 503);
    }
}
