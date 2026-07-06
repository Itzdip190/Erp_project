<?php

namespace App\Support;

use App\Models\Staff;
use App\Models\StaffModuleAccess;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class StaffAccessHelper
{
    /**
     * Cache for user staff permissions during a request.
     * Format: [ user_id => [ 'module_key.feature_key.view' => true, ... ] ]
     */
    protected static array $permissionCache = [];

    /**
     * Check if the logged-in user has access to a given module and feature.
     */
    public static function hasAccess(string $moduleKey, ?string $featureKey = null, string $accessType = 'view'): bool
    {
        // Check if school has disabled this module (wrapped in try-catch for migration safety)
        try {
            $school = app()->bound('currentSchool') ? app('currentSchool') : null;
            if (!$school && Auth::check()) {
                $user = Auth::user();
                if ($user->school_id) {
                    $school = $user->school;
                }
            }
            if ($school && is_array($school->disabled_modules) && in_array($moduleKey, $school->disabled_modules)) {
                return false;
            }
        } catch (\Throwable $e) {
            // Safe fallback if database is not fully migrated
        }

        $user = Auth::user();
        if (!$user) {
            return false;
        }

        // SuperAdmin and SchoolAdmin have unrestricted access to all modules
        if ($user->hasRole('superadmin') || $user->hasRole('school_admin') || $user->role === 'superadmin' || $user->role === 'school_admin') {
            return true;
        }

        $userId = (int) $user->id;
        if (!isset(self::$permissionCache[$userId])) {
            self::loadUserPermissions($user);
        }

        // Module Key Alias Map to bridge route middleware naming with ModuleRegistry naming
        $aliasMap = [
            'students'   => 'student_management',
            'staff'      => 'staff_management',
        ];
        if (isset($aliasMap[$moduleKey])) {
            $moduleKey = $aliasMap[$moduleKey];
        }

        if ($moduleKey === 'attendance') {
            if (!$featureKey) {
                foreach (self::$permissionCache[$userId] as $key => $granted) {
                    if ($granted) {
                        if (str_starts_with($key, "attendance.") && str_ends_with($key, ".{$accessType}")) {
                            return true;
                        }
                        if ($key === "student_management.student_attendance.{$accessType}" ||
                            $key === "student_management.bulk_attendance.{$accessType}" ||
                            $key === "staff_management.staff_attendance.{$accessType}" ||
                            $key === "staff_management.bulk_attendance.{$accessType}" ||
                            $key === "staff_management.student_att_report.{$accessType}") {
                            return true;
                        }
                    }
                }
                return false;
            }

            if (!empty(self::$permissionCache[$userId]["attendance.{$featureKey}.{$accessType}"])) {
                return true;
            }

            if ($featureKey === 'student_attendance') {
                return !empty(self::$permissionCache[$userId]["student_management.student_attendance.{$accessType}"]);
            }
            if ($featureKey === 'student_bulk_attendance') {
                return !empty(self::$permissionCache[$userId]["student_management.bulk_attendance.{$accessType}"]);
            }
            if ($featureKey === 'staff_attendance') {
                return !empty(self::$permissionCache[$userId]["staff_management.staff_attendance.{$accessType}"]);
            }
            if ($featureKey === 'staff_bulk_attendance') {
                return !empty(self::$permissionCache[$userId]["staff_management.bulk_attendance.{$accessType}"]);
            }
            if ($featureKey === 'student_att_report') {
                return !empty(self::$permissionCache[$userId]["staff_management.student_att_report.{$accessType}"]);
            }
        }

        if ($moduleKey === 'staff_management') {
            if ($featureKey === 'staff_attendance') {
                if (!empty(self::$permissionCache[$userId]["attendance.staff_attendance.{$accessType}"])) {
                    return true;
                }
            }
            if ($featureKey === 'bulk_attendance') {
                if (!empty(self::$permissionCache[$userId]["attendance.staff_bulk_attendance.{$accessType}"])) {
                    return true;
                }
            }
            if ($featureKey === 'student_att_report') {
                if (!empty(self::$permissionCache[$userId]["attendance.student_att_report.{$accessType}"])) {
                    return true;
                }
            }
        }

        if ($moduleKey === 'student_management') {
            if ($featureKey === 'student_attendance') {
                if (!empty(self::$permissionCache[$userId]["attendance.student_attendance.{$accessType}"])) {
                    return true;
                }
            }
            if ($featureKey === 'bulk_attendance') {
                if (!empty(self::$permissionCache[$userId]["attendance.student_bulk_attendance.{$accessType}"])) {
                    return true;
                }
            }
        }

        if ($featureKey) {
            return !empty(self::$permissionCache[$userId]["{$moduleKey}.{$featureKey}.{$accessType}"]);
        }

        // If checking module level without a specific feature, check if ANY feature under that module has access
        foreach (self::$permissionCache[$userId] as $key => $granted) {
            if (str_starts_with($key, "{$moduleKey}.") && str_ends_with($key, ".{$accessType}") && $granted) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the user has edit access specifically.
     */
    public static function canEdit(string $moduleKey, ?string $featureKey = null): bool
    {
        return self::hasAccess($moduleKey, $featureKey, 'edit');
    }

    /**
     * Load permissions into static cache for fast repeated checks in sidebar & layout.
     */
    protected static function loadUserPermissions($user): void
    {
        $userId = (int) $user->id;
        self::$permissionCache[$userId] = [];

        if (!Schema::hasTable('staff_module_access')) {
            self::setDefaultTeacherPermissions($userId);
            return;
        }

        $staff = null;
        if (Schema::hasTable('staff')) {
            $staff = Staff::where('user_id', $userId)->first();
        }
        $staffId = $staff?->id;

        // Query by user_id or staff_id for robust matching
        $records = StaffModuleAccess::where(function($q) use ($userId, $staffId) {
            $q->where('user_id', $userId);
            if ($staffId) {
                $q->orWhere('user_id', $staffId);
                if (Schema::hasColumn('staff_module_access', 'staff_id')) {
                    $q->orWhere('staff_id', $staffId);
                }
            }
        })->get();

        foreach ($records as $rec) {
            $v = $rec->view_access ?? $rec->can_view ?? false;
            $e = $rec->edit_access ?? $rec->can_edit ?? false;

            $viewVal = ($v === true || $v === 1 || $v === '1' || $v === 'true');
            $editVal = ($e === true || $e === 1 || $e === '1' || $e === 'true');

            if ($viewVal) {
                self::$permissionCache[$userId]["{$rec->module_key}.{$rec->feature_key}.view"] = true;
            }
            if ($editVal) {
                self::$permissionCache[$userId]["{$rec->module_key}.{$rec->feature_key}.edit"] = true;
            }
        }

        // Fallback: If no custom records exist yet for this user, populate default teacher modules
        if (empty(self::$permissionCache[$userId])) {
            self::setDefaultTeacherPermissions($userId);
        }
    }

    /**
     * Default granted modules for teaching staff so they always have core access.
     */
    protected static function setDefaultTeacherPermissions(int $userId): void
    {
        $defaults = [
            'timetable.class_timetable.view' => true,
            'timetable.teacher_timetable.view' => true,
            'timetable.teacher_substitution.view' => true,
            'student_management.student_attendance.view' => true,
            'attendance.student_attendance.view' => true,
            'student_management.student_directory.view' => true,
            'examination.marks_entry.view' => true,
            'digital_diary.create_diary.view' => true,
            'digital_diary.diary_report.view' => true,
            'communication.notice_circular.view' => true,
            'communication.chat.view' => true,
            'leave_management.staff_leave.view' => true,
        ];

        foreach ($defaults as $key => $val) {
            self::$permissionCache[$userId][$key] = $val;
        }
    }
}
