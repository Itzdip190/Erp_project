<?php

namespace App\Support;

use App\Services\SettingService;
use Illuminate\Support\Facades\Auth;

class FeatureVisibilityHelper
{
    /**
     * Cache array for feature visibility during a request cycle.
     */
    protected static array $localCache = [];

    /**
     * List of all configurable features in the ERP for Feature Visibility Control.
     * Keyed by feature_key => [ 'label' => '...', 'category' => '...' ]
     */
    public static function registeredFeatures(): array
    {
        return [
            // Student Management Features
            'add_student' => ['label' => 'Add Student', 'category' => 'student_management', 'default_web' => 1, 'default_mobile' => 1],
            'bulk_student_import' => ['label' => 'Bulk Student Import', 'category' => 'student_management', 'default_web' => 1, 'default_mobile' => 1],
            'bulk_photo_doc' => ['label' => 'Bulk Student Photo & Document Upload', 'category' => 'student_management', 'default_web' => 1, 'default_mobile' => 1],
            'optional_subject' => ['label' => 'Student Optional Subject Allocation', 'category' => 'student_management', 'default_web' => 1, 'default_mobile' => 1],
            'student_directory' => ['label' => 'Student Directory', 'category' => 'student_management', 'default_web' => 1, 'default_mobile' => 1],
            'admission_report' => ['label' => 'New Admission Report', 'category' => 'student_management', 'default_web' => 1, 'default_mobile' => 1],
            'siblings' => ['label' => 'Siblings List Tracking', 'category' => 'student_management', 'default_web' => 1, 'default_mobile' => 1],
            'student_attendance' => ['label' => 'Mark Student Attendance', 'category' => 'student_management', 'default_web' => 1, 'default_mobile' => 1],
            'bulk_attendance' => ['label' => 'Bulk Student Attendance Import', 'category' => 'student_management', 'default_web' => 1, 'default_mobile' => 0],
            'student_bulk_attendance' => ['label' => 'Student Mark Bulk Attendance', 'category' => 'student_management', 'default_web' => 1, 'default_mobile' => 0],
            'student_report' => ['label' => 'Student Reports & Analytics', 'category' => 'student_management', 'default_web' => 1, 'default_mobile' => 1],

            // Staff Management Features
            'staff_directory' => ['label' => 'Staff Directory', 'category' => 'staff_management', 'default_web' => 1, 'default_mobile' => 1],
            'add_staff' => ['label' => 'Add Staff Member', 'category' => 'staff_management', 'default_web' => 1, 'default_mobile' => 1],
            'bulk_staff_import' => ['label' => 'Bulk Staff Import', 'category' => 'staff_management', 'default_web' => 1, 'default_mobile' => 1],
            'bulk_staff_photo' => ['label' => 'Bulk Staff Photo Upload', 'category' => 'staff_management', 'default_web' => 1, 'default_mobile' => 1],
            'staff_attendance' => ['label' => 'Mark Staff Attendance', 'category' => 'staff_management', 'default_web' => 1, 'default_mobile' => 1],
            'staff_bulk_attendance' => ['label' => 'Staff Bulk Attendance', 'category' => 'staff_management', 'default_web' => 1, 'default_mobile' => 0],

            // Timetable Features
            'class_timetable' => ['label' => 'Class Timetable', 'category' => 'timetable', 'default_web' => 1, 'default_mobile' => 1],
            'group_timetable' => ['label' => 'Group Timetable', 'category' => 'timetable', 'default_web' => 1, 'default_mobile' => 1],
            'teacher_timetable' => ['label' => 'Teacher Timetable', 'category' => 'timetable', 'default_web' => 1, 'default_mobile' => 1],
            'teacher_substitution' => ['label' => 'Teacher Substitution', 'category' => 'timetable', 'default_web' => 1, 'default_mobile' => 1],

            // Leave Management Features
            'leave_basics' => ['label' => 'Leave Types & Rules', 'category' => 'leave', 'default_web' => 1, 'default_mobile' => 1],
            'staff_leave' => ['label' => 'Staff Leave Applications', 'category' => 'leave', 'default_web' => 1, 'default_mobile' => 1],
            'student_leave' => ['label' => 'Student Leave Applications', 'category' => 'leave', 'default_web' => 1, 'default_mobile' => 1],

            // Examinations & Marks
            'exam_setup' => ['label' => 'Examination Setup', 'category' => 'exam', 'default_web' => 1, 'default_mobile' => 1],
            'marks_entry' => ['label' => 'Teacher Marks Entry', 'category' => 'exam', 'default_web' => 1, 'default_mobile' => 1],
            'report_cards' => ['label' => 'Student Report Cards / Marksheets', 'category' => 'exam', 'default_web' => 1, 'default_mobile' => 1],

            // Fee Management
            'collect_fees' => ['label' => 'Fee Collection & Invoicing', 'category' => 'fee', 'default_web' => 1, 'default_mobile' => 1],
            'fee_structure' => ['label' => 'Fee Categories & Structure', 'category' => 'fee', 'default_web' => 1, 'default_mobile' => 1],
            'concessions' => ['label' => 'Fee Concessions & Discounts', 'category' => 'fee', 'default_web' => 1, 'default_mobile' => 1],
            'fee_reports' => ['label' => 'Fee Collection & Due Reports', 'category' => 'fee', 'default_web' => 1, 'default_mobile' => 1],
            'due_fees' => ['label' => 'Due Fee Defaulter Lists', 'category' => 'fee', 'default_web' => 1, 'default_mobile' => 1],

            // Transport
            'routes' => ['label' => 'Route Management', 'category' => 'transport', 'default_web' => 1, 'default_mobile' => 1],
            'vehicles' => ['label' => 'Vehicle & Driver Assigning', 'category' => 'transport', 'default_web' => 1, 'default_mobile' => 1],

            // Hostel
            'hostels' => ['label' => 'Hostel & Room Management', 'category' => 'hostel', 'default_web' => 1, 'default_mobile' => 1],

            // Library
            'library_books' => ['label' => 'Book Catalog & Issue/Return', 'category' => 'library', 'default_web' => 1, 'default_mobile' => 1],

            // Inventory
            'inventory_items' => ['label' => 'Inventory Items & Stock Issue', 'category' => 'inventory', 'default_web' => 1, 'default_mobile' => 1],

            // Homework & Assignments
            'homework' => ['label' => 'Homework Module', 'category' => 'homework', 'default_web' => 1, 'default_mobile' => 1],
            'assignments' => ['label' => 'Class Assignments', 'category' => 'homework', 'default_web' => 1, 'default_mobile' => 1],

            // Communication & Digital Diary
            'notice_circular' => ['label' => 'Notices & Circulars', 'category' => 'communication', 'default_web' => 1, 'default_mobile' => 1],
            'digital_diary' => ['label' => 'Digital Class Diary', 'category' => 'communication', 'default_web' => 1, 'default_mobile' => 1],
            'chat' => ['label' => 'Portal Chat / Messages', 'category' => 'communication', 'default_web' => 1, 'default_mobile' => 1],

            // Cards & Certificates
            'id_cards' => ['label' => 'ID Card Template & Generation', 'category' => 'certificates', 'default_web' => 1, 'default_mobile' => 1],
            'certificates' => ['label' => 'Certificate Creator & Printing', 'category' => 'certificates', 'default_web' => 1, 'default_mobile' => 1],

            // AI & Utilities
            'ai_assistant_widget' => ['label' => 'AI Assistant Chat Widget', 'category' => 'ai_assistant', 'default_web' => 1, 'default_mobile' => 1],
            'download_center' => ['label' => 'Download Center & Documents', 'category' => 'system', 'default_web' => 1, 'default_mobile' => 1],
        ];
    }

    /**
     * Check if a feature is visible in a given portal/scope.
     *
     * @param string $featureKey (e.g. 'bulk_attendance', 'add_student')
     * @param string $scope ('web', 'mobile', 'student', 'parent', 'teacher', 'staff', 'admin')
     * @param int|null $schoolId
     * @return bool
     */
    public static function isVisible(string $featureKey, string $scope = 'web', ?int $schoolId = null): bool
    {
        $schoolId = $schoolId ?: (Auth::check() ? Auth::user()->school_id : null);

        $userAgent = (function_exists('request') && request()) ? request()->header('User-Agent', '') : '';
        $isMobileRequest = (function_exists('request') && request()) && (
            request()->is('api*') ||
            request()->is('mobile*') ||
            request()->query('scope') === 'mobile' ||
            request()->header('X-Mobile-App') ||
            (bool)preg_match('/(android|iphone|ipod|mobile|opera m(ob|in)i)/i', $userAgent)
        );

        // HARD ENFORCEMENT: Bulk Attendance is 100% hidden on mobile applications
        if (in_array($featureKey, ['bulk_attendance', 'student_bulk_attendance', 'staff_bulk_attendance', 'bulk_staff_attendance'], true)) {
            if ($scope === 'mobile' || $isMobileRequest) {
                return false;
            }
        }

        // Auto-detect mobile request if scope is web
        if ($scope === 'web' && $isMobileRequest) {
            $scope = 'mobile';
        }

        $cacheKey = "{$schoolId}_{$featureKey}_{$scope}";

        if (isset(self::$localCache[$cacheKey])) {
            return self::$localCache[$cacheKey];
        }

        $registered = self::registeredFeatures();
        $meta = $registered[$featureKey] ?? [];

        // 1. Check Master Feature Enable Toggle
        $masterKey = "feat_{$featureKey}_master";
        $masterVal = SettingService::get($masterKey, '1', $schoolId);
        if ($masterVal === '0' || $masterVal === 0 || $masterVal === false) {
            self::$localCache[$cacheKey] = false;
            return false;
        }

        // 2. Check Portal-Specific Toggle
        $scopeKey = "feat_{$featureKey}_{$scope}";
        $defaultVal = isset($meta["default_{$scope}"]) ? (string)$meta["default_{$scope}"] : '1';
        $scopeVal = SettingService::get($scopeKey, $defaultVal, $schoolId);
        if ($scopeVal === '0' || $scopeVal === 0 || $scopeVal === false) {
            self::$localCache[$cacheKey] = false;
            return false;
        }

        self::$localCache[$cacheKey] = true;
        return true;
    }

    /**
     * Check if master feature is enabled.
     */
    public static function isMasterEnabled(string $featureKey, ?int $schoolId = null): bool
    {
        $schoolId = $schoolId ?: (Auth::check() ? Auth::user()->school_id : null);
        $masterKey = "feat_{$featureKey}_master";
        $val = SettingService::get($masterKey, '1', $schoolId);
        return !($val === '0' || $val === 0 || $val === false);
    }

    /**
     * Clear local static cache.
     */
    public static function clearLocalCache(): void
    {
        self::$localCache = [];
    }
}
