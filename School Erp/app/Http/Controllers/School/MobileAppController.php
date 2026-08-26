<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\FcmDeviceToken;
use App\Models\MobileAppBanner;
use App\Models\Notification;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\User;
use App\Services\FcmPushService;
use App\Services\SettingService;
use App\Support\FeatureVisibilityHelper;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MobileAppController extends Controller
{
    /**
     * Get active school ID.
     */
    protected function getSchoolId(): ?int
    {
        if (Auth::check() && Auth::user()->school_id) {
            return Auth::user()->school_id;
        }

        if (app()->bound('currentSchool')) {
            return app('currentSchool')?->id;
        }

        return request()->route('school')?->id;
    }

    /**
     * Ensure all necessary tables exist in the database.
     */
    protected function ensureTablesExist(): void
    {
        try {
            if (!Schema::hasTable('mobile_app_banners')) {
                Schema::create('mobile_app_banners', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
                    $table->string('title');
                    $table->string('subtitle')->nullable();
                    $table->string('badge_text')->nullable();
                    $table->text('image_url')->nullable();
                    $table->string('target_type', 40)->default('none');
                    $table->string('target_route')->nullable();
                    $table->string('target_role', 30)->default('all');
                    $table->string('bg_color', 20)->default('#1d4ed8');
                    $table->integer('display_order')->default(0);
                    $table->enum('status', ['active', 'inactive'])->default('active');
                    $table->date('start_date')->nullable();
                    $table->date('end_date')->nullable();
                    $table->timestamps();
                });
            }

            if (!Schema::hasTable('fcm_device_tokens')) {
                Schema::create('fcm_device_tokens', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('cascade');
                    $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
                    $table->text('token');
                    $table->string('device_name')->nullable();
                    $table->string('platform', 20)->default('android');
                    $table->timestamps();
                });
            }

            if (!Schema::hasTable('notifications')) {
                Schema::create('notifications', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('cascade');
                    $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
                    $table->string('recipient_role', 50)->nullable();
                    $table->string('title');
                    $table->text('message');
                    $table->string('module', 50)->default('mobile_app');
                    $table->string('type', 50)->default('push');
                    $table->unsignedBigInteger('related_id')->nullable();
                    $table->string('priority', 20)->default('normal');
                    $table->string('action_url')->nullable();
                    $table->string('icon', 50)->nullable();
                    $table->string('color', 20)->nullable();
                    $table->boolean('is_read')->default(false);
                    $table->timestamp('read_at')->nullable();
                    $table->timestamps();
                });
            }
        } catch (\Throwable $e) {
            Log::warning("Table initialization check in MobileAppController: " . $e->getMessage());
        }
    }

    // =========================================================================
    // 1. PAGE 1: MOBILE APP SETTINGS & API
    // =========================================================================

    /**
     * Display Mobile App Settings & API page.
     */
    public function settings()
    {
        $this->ensureTablesExist();
        $schoolId = $this->getSchoolId();

        $settings = [
            // App Branding & Info
            'mobile_app_name' => SettingService::get('mobile_app_name', 'SchoolCloud Mobile', $schoolId),
            'mobile_app_tagline' => SettingService::get('mobile_app_tagline', 'Smart Digital Campus Portal', $schoolId),
            'mobile_app_version' => SettingService::get('mobile_app_version', '2.4.0', $schoolId),
            'mobile_min_version' => SettingService::get('mobile_min_version', '2.0.0', $schoolId),
            'mobile_force_update' => SettingService::get('mobile_force_update', '0', $schoolId),
            'mobile_force_update_title' => SettingService::get('mobile_force_update_title', 'Update Required', $schoolId),
            'mobile_force_update_msg' => SettingService::get('mobile_force_update_msg', 'A newer and faster version of the School Mobile App is available on the Store. Please update now to continue.', $schoolId),
            'mobile_playstore_url' => SettingService::get('mobile_playstore_url', 'https://play.google.com/store/apps/details?id=com.schoolcloud.erp', $schoolId),
            'mobile_appstore_url' => SettingService::get('mobile_appstore_url', 'https://apps.apple.com/app/schoolcloud-erp/id123456789', $schoolId),
            'mobile_app_logo' => SettingService::get('mobile_app_logo', '', $schoolId),
            'mobile_support_email' => SettingService::get('mobile_support_email', 'support@schoolcloud.com', $schoolId),
            'mobile_support_phone' => SettingService::get('mobile_support_phone', '+91 98765 43210', $schoolId),

            // Theme & Colors
            'mobile_primary_color' => SettingService::get('mobile_primary_color', '#1d4ed8', $schoolId),
            'mobile_accent_color' => SettingService::get('mobile_accent_color', '#3b82f6', $schoolId),
            'mobile_header_gradient' => SettingService::get('mobile_header_gradient', '1', $schoolId),
            'mobile_splash_bg' => SettingService::get('mobile_splash_bg', '#002266', $schoolId),
            'mobile_theme_mode' => SettingService::get('mobile_theme_mode', 'light', $schoolId),

            // Operational Modes
            'enable_mobile_api' => SettingService::get('enable_mobile_api', '1', $schoolId),
            'mobile_app_maintenance' => SettingService::get('mobile_app_maintenance', '0', $schoolId),
            'mobile_maintenance_title' => SettingService::get('mobile_maintenance_title', 'Under Scheduled Maintenance', $schoolId),
            'mobile_maintenance_msg' => SettingService::get('mobile_maintenance_msg', 'We are performing regular server optimizations. The mobile app will be back online shortly.', $schoolId),
            'mobile_bypass_staff_maintenance' => SettingService::get('mobile_bypass_staff_maintenance', '1', $schoolId),

            // Security & Biometrics
            'mobile_allow_otp_login' => SettingService::get('mobile_allow_otp_login', '1', $schoolId),
            'mobile_enforce_biometrics' => SettingService::get('mobile_enforce_biometrics', '1', $schoolId),
            'mobile_allow_face_auth' => SettingService::get('mobile_allow_face_auth', '1', $schoolId),
            'mobile_session_timeout' => SettingService::get('mobile_session_timeout', '60', $schoolId),
            'mobile_single_device_login' => SettingService::get('mobile_single_device_login', '0', $schoolId),
            'mobile_remember_me_days' => SettingService::get('mobile_remember_me_days', '30', $schoolId),
            'mobile_allow_screenshot' => SettingService::get('mobile_allow_screenshot', '1', $schoolId),

            // API Configuration & Diagnostics
            'mobile_master_api_key' => SettingService::get('mobile_master_api_key', 'm_api_' . substr(md5($schoolId . '_erp_secure_key'), 0, 32), $schoolId),
            'mobile_api_rate_limit' => SettingService::get('mobile_api_rate_limit', '120', $schoolId),
            'mobile_debug_logging' => SettingService::get('mobile_debug_logging', '0', $schoolId),
            'mobile_offline_sync' => SettingService::get('mobile_offline_sync', '1', $schoolId),
        ];

        // System Diagnostic Metrics
        $registeredDevicesCount = 0;
        try {
            $registeredDevicesCount = FcmDeviceToken::where('school_id', $schoolId)->count();
        } catch (\Throwable $e) {}

        $activeBannersCount = 0;
        try {
            $activeBannersCount = MobileAppBanner::where('school_id', $schoolId)->where('status', 'active')->count();
        } catch (\Throwable $e) {}

        $apiBaseUrl = url('/api/v1');

        return view('school.mobile_app.settings', compact(
            'settings',
            'registeredDevicesCount',
            'activeBannersCount',
            'apiBaseUrl'
        ));
    }

    /**
     * Save Mobile App Settings.
     */
    public function saveSettings(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();

        $data = $request->except(['_token', 'mobile_app_logo_file']);

        // Handle logo file upload if provided
        if ($request->hasFile('mobile_app_logo_file')) {
            $file = $request->file('mobile_app_logo_file');
            $filename = 'app_logo_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/mobile_branding', $filename);
            $data['mobile_app_logo'] = Storage::url($path);
        }

        // Set checkboxes if absent
        $checkboxKeys = [
            'enable_mobile_api',
            'mobile_force_update',
            'mobile_app_maintenance',
            'mobile_bypass_staff_maintenance',
            'mobile_allow_otp_login',
            'mobile_enforce_biometrics',
            'mobile_allow_face_auth',
            'mobile_single_device_login',
            'mobile_allow_screenshot',
            'mobile_debug_logging',
            'mobile_offline_sync',
            'mobile_header_gradient',
        ];

        foreach ($checkboxKeys as $chk) {
            if (!isset($data[$chk])) {
                $data[$chk] = '0';
            }
        }

        SettingService::saveBulk($data, $schoolId);

        return response()->json([
            'status' => 'success',
            'message' => 'Mobile App settings saved successfully!',
            'settings' => $data,
        ]);
    }

    /**
     * Regenerate Mobile App Master API Key.
     */
    public function regenerateApiKey(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $newKey = 'm_api_' . Str::random(36);

        SettingService::set('mobile_master_api_key', $newKey, 'mobile_app', 'string', $schoolId);

        return response()->json([
            'status' => 'success',
            'message' => 'Mobile API Master Key regenerated successfully!',
            'api_key' => $newKey,
        ]);
    }

    // =========================================================================
    // 2. PAGE 2: PUSH NOTIFICATIONS
    // =========================================================================

    /**
     * Display Push Notifications management page.
     */
    public function pushNotifications(Request $request)
    {
        $this->ensureTablesExist();
        $schoolId = $this->getSchoolId();

        // Load notifications sent through mobile push
        $notifications = Notification::where('school_id', $schoolId)
            ->where('type', 'push')
            ->orderBy('id', 'desc')
            ->paginate(15);

        // Stats
        $totalPushes = Notification::where('school_id', $schoolId)->where('type', 'push')->count();
        $registeredDevicesCount = FcmDeviceToken::where('school_id', $schoolId)->count();
        $androidCount = FcmDeviceToken::where('school_id', $schoolId)->where('platform', 'android')->count();
        $iosCount = FcmDeviceToken::where('school_id', $schoolId)->where('platform', 'ios')->count();

        // Recent Registered Device Tokens
        $deviceTokens = FcmDeviceToken::where('school_id', $schoolId)
            ->with('user')
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();

        // Classes for targeted push dropdown
        $classes = SchoolClass::where('school_id', $schoolId)->with('sections')->get();
        $fcmServerKey = SettingService::get('fcm_server_key', '', $schoolId);

        return view('school.mobile_app.push_notifications', compact(
            'notifications',
            'totalPushes',
            'registeredDevicesCount',
            'androidCount',
            'iosCount',
            'deviceTokens',
            'classes',
            'fcmServerKey'
        ));
    }

    /**
     * Save Firebase Cloud Messaging (FCM) configuration.
     */
    public function saveFcmConfig(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $fcmKey = trim((string) $request->input('fcm_server_key'));

        SettingService::set('fcm_server_key', $fcmKey, 'mobile_app', 'string', $schoolId);

        return response()->json([
            'status' => 'success',
            'message' => 'Firebase Cloud Messaging (FCM) Server Key saved successfully!',
        ]);
    }

    /**
     * Send / Broadcast Push Notification to Mobile App users.
     */
    public function sendPushNotification(Request $request): JsonResponse
    {
        $this->ensureTablesExist();
        $schoolId = $this->getSchoolId();

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:150',
            'message' => 'required|string|max:1000',
            'recipient_role' => 'required|string|in:all,student,parent,teacher,staff,class_section,custom',
            'priority' => 'nullable|string|in:high,normal,urgent',
            'category' => 'nullable|string|max:50',
            'action_url' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:50',
            'class_id' => 'nullable|integer',
            'section_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $recipientRole = $request->input('recipient_role');
        $title = $request->input('title');
        $message = $request->input('message');
        $priority = $request->input('priority', 'high');
        $actionUrl = $request->input('action_url', '/');
        $category = $request->input('category', 'General Notice');
        $icon = $request->input('icon', 'bell');

        // Color coding based on priority/category
        $color = match ($priority) {
            'urgent' => '#ef4444',
            'high' => '#f59e0b',
            default => '#1d4ed8',
        };

        // Determine recipient users
        $targetedUserIds = [];

        if ($recipientRole === 'class_section' && $request->filled('class_id')) {
            $classId = $request->input('class_id');
            $sectionId = $request->input('section_id');

            $studentsQuery = \App\Models\Student::where('school_id', $schoolId)
                ->where('class_id', $classId);

            if ($sectionId) {
                $studentsQuery->where('section_id', $sectionId);
            }

            $userIds = $studentsQuery->pluck('user_id')->filter()->toArray();
            $parentUserIds = $studentsQuery->pluck('parent_user_id')->filter()->toArray();
            $targetedUserIds = array_values(array_unique(array_merge($userIds, $parentUserIds)));
        }

        $dispatchedCount = 0;

        if (!empty($targetedUserIds)) {
            // Create notification per targeted user
            foreach ($targetedUserIds as $uId) {
                Notification::create([
                    'school_id' => $schoolId,
                    'user_id' => $uId,
                    'recipient_role' => $recipientRole,
                    'title' => $title,
                    'message' => $message,
                    'module' => 'mobile_app',
                    'type' => 'push',
                    'priority' => $priority,
                    'action_url' => $actionUrl,
                    'icon' => $icon,
                    'color' => $color,
                    'is_read' => false,
                ]);
                $dispatchedCount++;
            }
        } else {
            // Role broadcast notification (user_id = null)
            Notification::create([
                'school_id' => $schoolId,
                'user_id' => null,
                'recipient_role' => $recipientRole,
                'title' => $title,
                'message' => $message,
                'module' => 'mobile_app',
                'type' => 'push',
                'priority' => $priority,
                'action_url' => $actionUrl,
                'icon' => $icon,
                'color' => $color,
                'is_read' => false,
            ]);

            $dispatchedCount = FcmDeviceToken::where('school_id', $schoolId)
                ->when($recipientRole !== 'all', function ($q) use ($recipientRole) {
                    $q->whereHas('user', function ($uq) use ($recipientRole) {
                        $uq->where('role', $recipientRole);
                    });
                })
                ->count();
        }

        // Fetch tokens for real hardware push with chime sound
        $tokensQuery = FcmDeviceToken::where('school_id', $schoolId);
        if (!empty($targetedUserIds)) {
            $tokensQuery->whereIn('user_id', $targetedUserIds);
        } elseif ($recipientRole !== 'all') {
            $tokensQuery->where(function ($q) use ($recipientRole) {
                $q->whereHas('user', function ($uq) use ($recipientRole) {
                    $uq->where('role', $recipientRole)
                       ->orWhereHas('roles', function ($rq) use ($recipientRole) {
                           $rq->where('name', $recipientRole);
                       });
                })->orWhereNull('user_id');
            });
        }
        $tokens = $tokensQuery->pluck('token')->toArray();

        // If no role-specific tokens found, fallback to all active school tokens so devices receive broadcast
        if (empty($tokens)) {
            $tokens = FcmDeviceToken::where('school_id', $schoolId)->pluck('token')->toArray();
        }

        // Dispatch real FCM Push to connected mobile devices
        $fcmResult = FcmPushService::sendToTokens($schoolId, $tokens, $title, $message, [
            'priority'   => $priority,
            'action_url' => $actionUrl,
            'category'   => $category,
            'sound'      => 'default',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Push Notification broadcast successfully dispatched to mobile devices! " . ($fcmResult['message'] ?? ''),
            'dispatched_count' => $dispatchedCount,
            'fcm_result' => $fcmResult,
        ]);
    }

    /**
     * Delete a sent push notification.
     */
    public function deleteNotification($id): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $notif = Notification::where('school_id', $schoolId)->where('id', $id)->first();

        if ($notif) {
            $notif->delete();
            return response()->json(['status' => 'success', 'message' => 'Push notification deleted successfully.']);
        }

        return response()->json(['status' => 'error', 'message' => 'Notification not found.'], 404);
    }

    /**
     * Send test ping to specific registered device.
     */
    public function testDevicePing(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $tokenId = $request->input('token_id');

        $token = FcmDeviceToken::where('school_id', $schoolId)->where('id', $tokenId)->first();

        if (!$token) {
            return response()->json(['status' => 'error', 'message' => 'Device token not found.'], 404);
        }

        // Create test notification
        Notification::create([
            'school_id' => $schoolId,
            'user_id' => $token->user_id,
            'recipient_role' => 'test',
            'title' => '⚡ Test Push Notification Ping',
            'message' => 'Your mobile device is successfully registered and connected with School ERP Push Gateway.',
            'module' => 'mobile_app',
            'type' => 'push',
            'priority' => 'high',
            'action_url' => '/',
            'icon' => 'mobile-screen',
            'color' => '#10b981',
            'is_read' => false,
        ]);

        // Send real FCM ping with sound
        FcmPushService::sendToTokens($schoolId, [$token->token], '⚡ Test Push Notification Ping', 'Your mobile device received the push ping chime!', [
            'priority' => 'high',
            'action_url' => '/',
            'sound' => 'default',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Test push ping sent to {$token->device_name} ({$token->platform}) with chime sound successfully!",
        ]);
    }

    // =========================================================================
    // 3. PAGE 3: APP BANNERS & SLIDERS
    // =========================================================================

    /**
     * Display App Banners & Sliders management page.
     */
    public function banners()
    {
        $this->ensureTablesExist();
        $schoolId = $this->getSchoolId();

        $banners = MobileAppBanner::where('school_id', $schoolId)
            ->orderBy('display_order', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        $sliderConfig = [
            'interval' => SettingService::get('mobile_slider_interval', '4000', $schoolId),
            'autoplay' => SettingService::get('mobile_slider_autoplay', '1', $schoolId),
            'loop' => SettingService::get('mobile_slider_loop', '1', $schoolId),
            'aspect_ratio' => SettingService::get('mobile_slider_ratio', '16_9', $schoolId),
            'show_dots' => SettingService::get('mobile_slider_dots', '1', $schoolId),
        ];

        return view('school.mobile_app.banners', compact('banners', 'sliderConfig'));
    }

    /**
     * Create or update a Mobile App Banner.
     */
    public function saveBanner(Request $request): JsonResponse
    {
        $this->ensureTablesExist();
        $schoolId = $this->getSchoolId();

        $validator = Validator::make($request->all(), [
            'id' => 'nullable|integer',
            'title' => 'required|string|max:150',
            'subtitle' => 'nullable|string|max:255',
            'badge_text' => 'nullable|string|max:50',
            'target_type' => 'required|string|in:none,screen,url,notice,fee,event,exam',
            'target_route' => 'nullable|string|max:255',
            'target_role' => 'required|string|in:all,student,parent,teacher,staff',
            'bg_color' => 'nullable|string|max:20',
            'display_order' => 'nullable|integer',
            'status' => 'required|string|in:active,inactive',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:4096',
            'image_url' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $id = $request->input('id');
        $banner = $id ? MobileAppBanner::where('school_id', $schoolId)->where('id', $id)->first() : new MobileAppBanner();

        if (!$banner) {
            return response()->json(['status' => 'error', 'message' => 'Banner not found.'], 404);
        }

        $banner->school_id = $schoolId;
        $banner->title = $request->input('title');
        $banner->subtitle = $request->input('subtitle');
        $banner->badge_text = $request->input('badge_text');
        $banner->target_type = $request->input('target_type');
        $banner->target_route = $request->input('target_route');
        $banner->target_role = $request->input('target_role');
        $banner->bg_color = $request->input('bg_color', '#1d4ed8');
        $banner->display_order = (int) $request->input('display_order', 0);
        $banner->status = $request->input('status', 'active');
        $banner->start_date = $request->input('start_date');
        $banner->end_date = $request->input('end_date');

        // Image handling: File Upload or Image URL
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = 'mob_banner_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/mobile_banners', $filename);
            $banner->image_url = Storage::url($path);
        } elseif ($request->filled('image_url')) {
            $banner->image_url = $request->input('image_url');
        }

        $banner->save();

        return response()->json([
            'status' => 'success',
            'message' => $id ? 'App Banner updated successfully!' : 'New App Banner created successfully!',
            'banner' => $banner,
        ]);
    }

    /**
     * Toggle Banner active / inactive status.
     */
    public function toggleBannerStatus($id): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $banner = MobileAppBanner::where('school_id', $schoolId)->where('id', $id)->first();

        if (!$banner) {
            return response()->json(['status' => 'error', 'message' => 'Banner not found.'], 404);
        }

        $banner->status = ($banner->status === 'active') ? 'inactive' : 'active';
        $banner->save();

        return response()->json([
            'status' => 'success',
            'message' => "Banner is now {$banner->status}.",
            'new_status' => $banner->status,
        ]);
    }

    /**
     * Delete an App Banner.
     */
    public function deleteBanner($id): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $banner = MobileAppBanner::where('school_id', $schoolId)->where('id', $id)->first();

        if (!$banner) {
            return response()->json(['status' => 'error', 'message' => 'Banner not found.'], 404);
        }

        $banner->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'App Banner deleted successfully.',
        ]);
    }

    /**
     * Save Slider Carousel Display Settings.
     */
    public function saveSliderConfig(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();

        $data = [
            'mobile_slider_interval' => $request->input('interval', '4000'),
            'mobile_slider_autoplay' => $request->input('autoplay', '1'),
            'mobile_slider_loop' => $request->input('loop', '1'),
            'mobile_slider_ratio' => $request->input('aspect_ratio', '16_9'),
            'mobile_slider_dots' => $request->input('show_dots', '1'),
        ];

        SettingService::saveBulk($data, $schoolId);

        return response()->json([
            'status' => 'success',
            'message' => 'Slider carousel configuration updated!',
        ]);
    }

    // =========================================================================
    // 4. PAGE 4: MOBILE FEATURE CONTROL
    // =========================================================================

    /**
     * Display Mobile Feature Control matrix.
     */
    public function featureControl()
    {
        $schoolId = $this->getSchoolId();

        // Curated Role-Specific Mobile Feature Maps (Student, Teacher, Staff)
        $roleFeatureMap = [
            'student' => [
                'title' => 'Student Mobile Portal',
                'subtitle' => 'Curated features visible exclusively to Students on Mobile App (WebView)',
                'icon' => 'fas fa-graduation-cap',
                'badge' => 'Student Scope',
                'features' => [
                    'student_attendance' => ['label' => 'My Attendance Calendar', 'desc' => 'View monthly attendance days, present/absent counts', 'icon' => 'fas fa-calendar-check'],
                    'class_timetable'    => ['label' => 'My Class Routine & Timetable', 'desc' => 'Daily period schedule and subject timings', 'icon' => 'fas fa-calendar-days'],
                    'homework'           => ['label' => 'Homework & Tasks', 'desc' => 'View daily class homework given by teachers', 'icon' => 'fas fa-book-open'],
                    'assignments'        => ['label' => 'Assignments & Projects', 'desc' => 'Download assignments and upload solutions', 'icon' => 'fas fa-file-lines'],
                    'digital_diary'      => ['label' => 'Class Digital Diary', 'desc' => 'Teacher notes, remarks and daily diary updates', 'icon' => 'fas fa-book-bookmark'],
                    'report_cards'       => ['label' => 'Exam Marksheets & Report Cards', 'desc' => 'View published exam results and progress cards', 'icon' => 'fas fa-award'],
                    'collect_fees'       => ['label' => 'Fee Invoices & Dues', 'desc' => 'View fee challans, due amounts, and paid receipts', 'icon' => 'fas fa-receipt'],
                    'notice_circular'    => ['label' => 'School Notices & Circulars', 'desc' => 'General school announcements, event circulars, holidays', 'icon' => 'fas fa-bullhorn'],
                    'catalogue'          => ['label' => 'Library Book Catalogue', 'desc' => 'Browse school library book titles and availability', 'icon' => 'fas fa-book'],
                    'student_leave'      => ['label' => 'Apply Student Leave', 'desc' => 'Submit leave requests to class teacher with reason', 'icon' => 'fas fa-paper-plane'],
                    'chat'               => ['label' => 'Student Chat / Messages', 'desc' => 'Direct messaging with teachers and school desk', 'icon' => 'fas fa-comments'],
                    'ai_assistant_widget'=> ['label' => 'EduBot AI Study Helper', 'desc' => 'AI chatbot assistant for student study queries', 'icon' => 'fas fa-robot'],
                    'download_center'    => ['label' => 'Study Materials & Syllabus', 'desc' => 'Download syllabus PDFs and subject study notes', 'icon' => 'fas fa-download'],
                    'id_cards'           => ['label' => 'Digital Student ID Card', 'desc' => 'Digital student identity card with barcode/QR', 'icon' => 'fas fa-id-card'],
                ]
            ],
            'teacher' => [
                'title' => 'Teacher Mobile Portal',
                'subtitle' => 'Curated features visible exclusively to Teachers on Mobile App (WebView)',
                'icon' => 'fas fa-chalkboard-user',
                'badge' => 'Teacher Scope',
                'features' => [
                    'student_attendance' => ['label' => 'Mark Student Attendance', 'desc' => 'Quick 1-tap classroom student attendance on mobile', 'icon' => 'fas fa-user-check'],
                    'marks_entry'        => ['label' => 'Teacher Marks Entry', 'desc' => 'Enter test & examination marks on mobile phone', 'icon' => 'fas fa-pen-to-square'],
                    'homework'           => ['label' => 'Post Daily Homework', 'desc' => 'Create and assign daily homework to class sections', 'icon' => 'fas fa-book-open'],
                    'digital_diary'      => ['label' => 'Write Digital Diary Remarks', 'desc' => 'Post daily class diary updates and student remarks', 'icon' => 'fas fa-book-bookmark'],
                    'teacher_timetable'  => ['label' => 'Teacher Routine Schedule', 'desc' => 'View my assigned class periods and subjects', 'icon' => 'fas fa-calendar-days'],
                    'teacher_substitution'=> ['label' => 'Teacher Substitutions', 'desc' => 'View assigned proxy / substitution periods', 'icon' => 'fas fa-repeat'],
                    'staff_leave'        => ['label' => 'Apply Teacher Leave', 'desc' => 'Submit teacher leave request to Principal/Admin', 'icon' => 'fas fa-plane-departure'],
                    'student_leave'      => ['label' => 'Review Student Leave', 'desc' => 'View leave applications submitted by class students', 'icon' => 'fas fa-clipboard-check'],
                    'student_directory'  => ['label' => 'Student Emergency Directory', 'desc' => 'Lookup student roll number, parent phone and emergency contacts', 'icon' => 'fas fa-address-book'],
                    'notice_circular'    => ['label' => 'Staff & School Notices', 'desc' => 'View staff circulars and school announcements', 'icon' => 'fas fa-bullhorn'],
                    'chat'               => ['label' => 'Chat with Students/Parents', 'desc' => 'Parent & student message queries inbox', 'icon' => 'fas fa-comments'],
                ]
            ],
            'staff' => [
                'title' => 'Staff & Operations Portal',
                'subtitle' => 'Curated features visible exclusively to Non-Teaching Staff on Mobile App',
                'icon' => 'fas fa-id-badge',
                'badge' => 'Staff Scope',
                'features' => [
                    'staff_attendance'   => ['label' => 'Punch Self Attendance', 'desc' => 'Staff self punch in / punch out mobile attendance', 'icon' => 'fas fa-fingerprint'],
                    'staff_leave'        => ['label' => 'Apply Staff Leave', 'desc' => 'Submit staff leave request and check leave balance', 'icon' => 'fas fa-plane-departure'],
                    'staff_directory'    => ['label' => 'Staff Phone Directory', 'desc' => 'Staff contact list and internal phone extensions', 'icon' => 'fas fa-address-book'],
                    'inventory_items'    => ['label' => 'Quick Inventory Issue', 'desc' => 'Issue stock items from mobile inventory', 'icon' => 'fas fa-boxes-stacked'],
                    'notice_circular'    => ['label' => 'Staff Circulars & Notices', 'desc' => 'Official administrative notices and office orders', 'icon' => 'fas fa-bullhorn'],
                    'download_center'    => ['label' => 'Office Forms & Downloads', 'desc' => 'Download staff documents and standard forms', 'icon' => 'fas fa-download'],
                ]
            ],
        ];

        // Retrieve current status for each role feature
        $featureStatus = [];
        $activeCount = 0;
        $totalRoleFeatures = 0;

        foreach ($roleFeatureMap as $role => $roleData) {
            foreach ($roleData['features'] as $fKey => $fMeta) {
                $totalRoleFeatures++;
                $isRoleActive = FeatureVisibilityHelper::isVisible($fKey, $role, $schoolId);
                $isMasterMobileActive = FeatureVisibilityHelper::isVisible($fKey, 'mobile', $schoolId);

                $status = ($isRoleActive && $isMasterMobileActive);
                $featureStatus[$role][$fKey] = $status;

                if ($status) {
                    $activeCount++;
                }
            }
        }

        return view('school.mobile_app.feature_control', compact(
            'roleFeatureMap',
            'featureStatus',
            'totalRoleFeatures',
            'activeCount'
        ));
    }

    /**
     * Save a specific feature visibility toggle for mobile scopes.
     */
    public function saveFeatureControl(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $featureKey = $request->input('feature_key');
        $scope = $request->input('scope', 'mobile'); // 'mobile', 'student', 'parent', 'teacher', 'staff'
        $value = $request->input('value') ? '1' : '0';

        if (!$featureKey) {
            return response()->json(['status' => 'error', 'message' => 'Feature key is required.'], 422);
        }

        $settingKey = "feat_{$featureKey}_{$scope}";
        SettingService::set($settingKey, $value, 'mobile_app', 'boolean', $schoolId);

        if ($value === '1') {
            SettingService::set("feat_{$featureKey}_mobile", '1', 'mobile_app', 'boolean', $schoolId);
        }

        FeatureVisibilityHelper::clearLocalCache();

        return response()->json([
            'status' => 'success',
            'message' => "Feature [{$featureKey}] updated for [{$scope}] scope.",
            'feature_key' => $featureKey,
            'scope' => $scope,
            'value' => $value,
        ]);
    }

    /**
     * Apply Bulk Preset Actions for Mobile Features.
     */
    public function applyFeaturePreset(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $preset = $request->input('preset'); // 'enable_all_mobile', 'core_only', 'recommended_mobile', 'reset_defaults'

        $registered = FeatureVisibilityHelper::registeredFeatures();
        $scopes = ['mobile', 'student', 'parent', 'teacher', 'staff'];
        $updates = [];

        foreach ($registered as $key => $meta) {
            if ($preset === 'enable_all_mobile') {
                foreach ($scopes as $s) {
                    $updates["feat_{$key}_{$s}"] = '1';
                }
            } elseif ($preset === 'disable_all_mobile') {
                foreach ($scopes as $s) {
                    $updates["feat_{$key}_{$s}"] = '0';
                }
            } elseif ($preset === 'recommended_mobile') {
                // High-performance mobile configuration
                $isHeavy = in_array($key, ['bulk_attendance', 'student_bulk_attendance', 'staff_bulk_attendance', 'bulk_student_import', 'bulk_staff_import']);
                $val = $isHeavy ? '0' : '1';
                foreach ($scopes as $s) {
                    $updates["feat_{$key}_{$s}"] = $val;
                }
            } elseif ($preset === 'reset_defaults') {
                foreach ($scopes as $s) {
                    $def = isset($meta["default_{$s}"]) ? (string)$meta["default_{$s}"] : '1';
                    $updates["feat_{$key}_{$s}"] = $def;
                }
            }
        }

        SettingService::saveBulk($updates, $schoolId);
        FeatureVisibilityHelper::clearLocalCache();

        return response()->json([
            'status' => 'success',
            'message' => "Preset '{$preset}' successfully applied to mobile application configuration!",
        ]);
    }
}
