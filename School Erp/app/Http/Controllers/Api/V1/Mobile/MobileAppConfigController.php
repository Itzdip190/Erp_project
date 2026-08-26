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

        $feeConfig = $schoolId ? \App\Models\FeeConfiguration::where('school_id', $schoolId)->first() : null;

        // Dynamic theme & mobile settings configured from Admin UI
        $appName = \App\Services\SettingService::get('mobile_app_name', 'SchoolCloud ERP Mobile', $schoolId);
        $primaryColor = \App\Services\SettingService::get('mobile_primary_color', '#1d4ed8', $schoolId);
        $accentColor = \App\Services\SettingService::get('mobile_accent_color', '#3b82f6', $schoolId);
        $appLogo = \App\Services\SettingService::get('mobile_app_logo', '', $schoolId);
        $maintenance = \App\Services\SettingService::get('mobile_app_maintenance', '0', $schoolId) == '1';
        $minVersion = \App\Services\SettingService::get('mobile_min_version', '1.0.0', $schoolId);
        $forceUpdate = \App\Services\SettingService::get('mobile_force_update', '0', $schoolId) == '1';

        return response()->json([
            'status' => 'success',
            'data' => [
                'app_name' => $appName,
                'app_logo' => $appLogo,
                'version' => \App\Services\SettingService::get('mobile_app_version', '2.4.0', $schoolId),
                'min_version' => $minVersion,
                'force_update_required' => $forceUpdate,
                'maintenance_mode' => $maintenance,
                'channel_scope' => $scope,
                'theme' => [
                    'primary' => $primaryColor,
                    'accent' => $accentColor,
                    'background' => '#f8fafc',
                    'card_bg' => '#ffffff',
                    'text_color' => '#0f172a',
                ],
                'features' => $featureFlags,
                'fee_config' => $feeConfig,
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
     * Get active mobile banners and slider promos for the authenticated user role.
     */
    public function banners(Request $request): JsonResponse
    {
        $user = Auth::user();
        $schoolId = $user ? $user->school_id : null;
        $role = $request->query('role', ($user ? ($user->roles->first()?->name ?? 'all') : 'all'));

        $banners = [];
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('mobile_app_banners')) {
                $query = \App\Models\MobileAppBanner::query();
                if ($schoolId) {
                    $query->where('school_id', $schoolId);
                }
                $banners = $query->active()
                    ->forRole($role)
                    ->get();
            }
        } catch (\Throwable $e) {}

        $sliderConfig = [
            'interval' => (int) \App\Services\SettingService::get('mobile_slider_interval', '4000', $schoolId),
            'autoplay' => \App\Services\SettingService::get('mobile_slider_autoplay', '1', $schoolId) == '1',
            'loop' => \App\Services\SettingService::get('mobile_slider_loop', '1', $schoolId) == '1',
            'aspect_ratio' => \App\Services\SettingService::get('mobile_slider_ratio', '16_9', $schoolId),
            'show_dots' => \App\Services\SettingService::get('mobile_slider_dots', '1', $schoolId) == '1',
        ];

        return response()->json([
            'status' => 'success',
            'data' => [
                'slider_config' => $sliderConfig,
                'banners' => $banners,
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
