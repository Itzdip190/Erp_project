<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Support\FeatureVisibilityHelper;
use App\Support\ModuleRegistry;
use App\Support\StaffAccessHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobileAppConfigController extends Controller
{
    /**
     * Get mobile application configuration, themes, and feature visibility flags.
     */
    public function config(Request $request): JsonResponse
    {
        $user = Auth::user();
        $schoolId = $user ? $user->school_id : null;
        $scope = $request->query('scope', 'mobile');

        $registeredFeatures = FeatureVisibilityHelper::registeredFeatures();
        $featureFlags = [];

        foreach ($registeredFeatures as $key => $meta) {
            $featureFlags[$key] = FeatureVisibilityHelper::isVisible($key, $scope, $schoolId);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'app_name' => 'SchoolCloud ERP Mobile',
                'version' => '1.0.0',
                'channel_scope' => $scope,
                'theme' => [
                    'primary' => '#1e293b',
                    'accent' => '#3b82f6',
                    'background' => '#f8fafc',
                    'card_bg' => '#ffffff',
                    'text_color' => '#0f172a',
                ],
                'features' => $featureFlags,
                'transport_settings' => [
                    'show_school_name_invoice' => \App\Services\SettingService::get('show_school_name_transport_invoice', '1') == '1',
                    'quarterly_payment_only' => \App\Services\SettingService::get('quarterly_transport_payment', '0') == '1',
                    'show_school_logo' => \App\Services\SettingService::get('show_school_logo_transport_invoice', '1') == '1',
                    'show_route_details' => \App\Services\SettingService::get('show_route_vehicle_on_transport_invoice', '1') == '1',
                    'auto_absent_deduction' => \App\Services\SettingService::get('auto_transport_absent_deduction', '1') == '1',
                    'allow_advance_payment' => \App\Services\SettingService::get('allow_advance_transport_payment', '1') == '1',
                    'lock_partial_payment' => \App\Services\SettingService::get('lock_partial_transport_payment', '0') == '1',
                    'show_driver_contact' => \App\Services\SettingService::get('show_driver_contact_to_parents', '1') == '1',
                    'receipt_prefix' => \App\Services\SettingService::get('transport_receipt_prefix', 'TRN-'),
                ],
            ]
        ]);
    }

    /**
     * Get dynamic mobile menu and navigation items based on active permissions and mobile feature visibility.
     */
    public function navigation(Request $request): JsonResponse
    {
        $user = Auth::user();
        $schoolId = $user ? $user->school_id : null;
        $scope = $request->query('scope', 'mobile');

        $modules = ModuleRegistry::getModules();
        $mobileNavigation = [];

        foreach ($modules as $modKey => $mod) {
            $features = [];
            foreach ($mod['features'] as $fKey => $fLabel) {
                if (FeatureVisibilityHelper::isVisible($fKey, $scope, $schoolId)) {
                    if (!$user || StaffAccessHelper::hasAccess($modKey, $fKey, 'view')) {
                        $features[] = [
                            'key' => $fKey,
                            'label' => $fLabel,
                            'route' => "/mobile/{$modKey}/{$fKey}",
                        ];
                    }
                }
            }

            if (!empty($features)) {
                $mobileNavigation[] = [
                    'key' => $modKey,
                    'label' => $mod['label'],
                    'icon' => $mod['icon'],
                    'features' => $features,
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'bottom_nav' => [
                    ['key' => 'dashboard', 'label' => 'Home', 'icon' => 'home'],
                    ['key' => 'attendance', 'label' => 'Attendance', 'icon' => 'calendar-check'],
                    ['key' => 'messages', 'label' => 'Notice', 'icon' => 'bell'],
                    ['key' => 'profile', 'label' => 'Profile', 'icon' => 'user'],
                ],
                'drawer_modules' => $mobileNavigation,
            ]
        ]);
    }

    /**
     * Get mobile dashboard grid cards and quick actions.
     */
    public function dashboard(Request $request): JsonResponse
    {
        $user = Auth::user();
        $schoolId = $user ? $user->school_id : null;
        $scope = $request->query('scope', 'mobile');

        $quickActions = [];

        if (FeatureVisibilityHelper::isVisible('student_attendance', $scope, $schoolId)) {
            $quickActions[] = ['key' => 'attendance', 'title' => 'Mark Attendance', 'icon' => 'user-check', 'color' => '#10b981'];
        }
        if (FeatureVisibilityHelper::isVisible('collect_fees', $scope, $schoolId)) {
            $quickActions[] = ['key' => 'fees', 'title' => 'Fee Collection', 'icon' => 'credit-card', 'color' => '#6366f1'];
        }
        if (FeatureVisibilityHelper::isVisible('notice_circular', $scope, $schoolId)) {
            $quickActions[] = ['key' => 'notices', 'title' => 'Notices', 'icon' => 'bullhorn', 'color' => '#f59e0b'];
        }
        if (FeatureVisibilityHelper::isVisible('digital_diary', $scope, $schoolId)) {
            $quickActions[] = ['key' => 'diary', 'title' => 'Class Diary', 'icon' => 'book-open', 'color' => '#8b5cf6'];
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->roles->first()?->name ?? 'User',
                ] : null,
                'quick_actions' => $quickActions,
            ]
        ]);
    }

    /**
     * Get feature visibility status across all scopes.
     */
    public function features(Request $request): JsonResponse
    {
        $user = Auth::user();
        $schoolId = $user ? $user->school_id : null;
        $registeredFeatures = FeatureVisibilityHelper::registeredFeatures();
        $scopes = ['web', 'mobile', 'student', 'teacher', 'parent', 'staff', 'admin'];

        $matrix = [];
        foreach ($registeredFeatures as $key => $meta) {
            $scopeStatus = [];
            foreach ($scopes as $scope) {
                $scopeStatus[$scope] = FeatureVisibilityHelper::isVisible($key, $scope, $schoolId);
            }
            $matrix[$key] = [
                'label' => $meta['label'],
                'category' => $meta['category'],
                'scopes' => $scopeStatus,
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $matrix,
        ]);
    }
}
