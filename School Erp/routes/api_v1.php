<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\Parent\ParentAuthController;
use App\Http\Controllers\Api\V1\Driver\DriverAuthController;
use App\Http\Controllers\Api\V1\OtpLoginController;
use App\Http\Controllers\Api\V1\FaceAuthController;
use App\Http\Controllers\Api\V1\Parent\ChildrenController;
use App\Http\Controllers\Api\V1\Parent\ParentAttendanceController;
use App\Http\Controllers\Api\V1\Staff\StaffStudentController;
use App\Http\Controllers\Api\V1\Staff\StaffAttendanceController;
use App\Http\Controllers\Api\V1\Staff\SelfAttendanceController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1')->middleware(['check.mobile_maintenance'])->group(function () {

    // Public Authentication & School Lookup — rate-limited
    Route::middleware(['throttle:10,1'])->group(function () {
        Route::get('/school/lookup', [ParentAuthController::class,  'lookupSchool']);
        Route::post('/login',        [AuthController::class,        'login']);
        Route::post('/parent/login', [ParentAuthController::class,  'login']);
        Route::post('/student/login', [ParentAuthController::class,  'login']);
        Route::post('/driver/login', [DriverAuthController::class,  'login']);
    });

    // Public OTP — strict rate limiting to prevent SMS abuse and OTP guessing
    Route::middleware(['throttle:3,1'])->post('/otp/send',   [OtpLoginController::class, 'send']);
    Route::middleware(['throttle:5,1'])->post('/otp/verify', [OtpLoginController::class, 'verify']);

    // Public Device Token Registration — throttled
    Route::middleware(['throttle:10,1'])->group(function () {
        Route::post('/mobile/register-device',        [\App\Http\Controllers\NotificationStreamController::class, 'registerDevice']);
        Route::post('/notifications/register-device', [\App\Http\Controllers\NotificationStreamController::class, 'registerDevice']);
    });

    // face-login: 5 req/min — prevents CPU exhaustion + enumeration
    Route::middleware(['throttle:5,1'])
        ->post('/face-login', [FaceAuthController::class, 'login']);

    // Protected — auth:sanctum
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/logout',           [AuthController::class,      'logout']);
        Route::get('/me',                [AuthController::class,      'me']);
        Route::post('/face-login/enroll',[FaceAuthController::class,  'enroll']);

        // Parent
        Route::middleware(['active_student'])->group(function () {
            Route::get('/parent/children',                    [ChildrenController::class,    'index']);
            Route::get('/parent/children/{id}/profile',       [ChildrenController::class,    'profile']);
            Route::get('/parent/children/{id}/documents',     [ChildrenController::class,    'documents']);
            Route::get('/parent/attendance',                  [ParentAttendanceController::class, 'index']);
            Route::get('/parent/fees',                        [\App\Http\Controllers\Api\V1\Parent\ParentFeaturesController::class, 'fees']);
            Route::get('/parent/assignments',                 [\App\Http\Controllers\Api\V1\Parent\ParentFeaturesController::class, 'assignments']);
            Route::get('/parent/diary',                       [\App\Http\Controllers\Api\V1\Parent\ParentFeaturesController::class, 'diary']);
            Route::get('/parent/timetable',                   [\App\Http\Controllers\Api\V1\Parent\ParentFeaturesController::class, 'timetable']);
            Route::get('/parent/leaves',                      [\App\Http\Controllers\Api\V1\Parent\ParentFeaturesController::class, 'leaves']);
            Route::post('/parent/leaves/store',               [\App\Http\Controllers\Api\V1\Parent\ParentFeaturesController::class, 'storeLeave']);
            Route::get('/parent/exams',                       [\App\Http\Controllers\Api\V1\Parent\ParentFeaturesController::class, 'exams']);
            Route::get('/parent/report-cards',                [\App\Http\Controllers\Api\V1\Parent\ParentFeaturesController::class, 'reportCards']);
            Route::get('/parent/transport',                   [\App\Http\Controllers\Api\V1\Parent\ParentFeaturesController::class, 'transport']);
            Route::get('/parent/notices',                     [\App\Http\Controllers\Api\V1\Parent\ParentFeaturesController::class, 'notices']);
            Route::get('/parent/chat',                        [\App\Http\Controllers\Api\V1\Parent\ParentFeaturesController::class, 'chat']);
            Route::post('/parent/chat/send',                  [\App\Http\Controllers\Api\V1\Parent\ParentFeaturesController::class, 'sendChat']);
            Route::get('/parent/surveys',                     [\App\Http\Controllers\Api\V1\Parent\ParentFeaturesController::class, 'surveys']);
            Route::post('/parent/surveys/{id}/vote',          [\App\Http\Controllers\Api\V1\Parent\ParentFeaturesController::class, 'voteSurvey']);
            Route::get('/parent/id-card',                     [\App\Http\Controllers\Api\V1\Parent\ParentFeaturesController::class, 'idCard']);
            Route::get('/parent/cards',                       [\App\Http\Controllers\Api\V1\Parent\ParentFeaturesController::class, 'cards']);
            Route::get('/parent/gatepasses',                  [\App\Http\Controllers\Api\V1\Parent\ParentFeaturesController::class, 'gatepasses']);
            Route::get('/parent/class-overview',              [\App\Http\Controllers\Api\V1\Parent\ParentFeaturesController::class, 'classOverview']);
            Route::post('/parent/reset-password',             [\App\Http\Controllers\Api\V1\Parent\ParentFeaturesController::class, 'resetPassword']);
            Route::post('/parent/ai-chat',                    [\App\Http\Controllers\Api\V1\Parent\ParentFeaturesController::class, 'aiChat']);
            Route::get('/parent/teachers',                    [\App\Http\Controllers\Api\V1\Parent\ParentFeaturesController::class, 'teachers']);
            Route::get('/parent/events',                      [\App\Http\Controllers\Api\V1\Parent\ParentFeaturesController::class, 'events']);
            Route::get('/parent/library',                     [\App\Http\Controllers\Api\V1\Parent\ParentFeaturesController::class, 'library']);
            Route::get('/parent/gallery',                     [\App\Http\Controllers\Api\V1\Parent\ParentFeaturesController::class, 'gallery']);
            Route::get('/parent/study-materials',             [\App\Http\Controllers\Api\V1\Parent\ParentFeaturesController::class, 'studyMaterials']);
            Route::get('/parent/medical',                     [\App\Http\Controllers\Api\V1\Parent\ParentFeaturesController::class, 'medical']);
            Route::get('/parent/discipline',                  [\App\Http\Controllers\Api\V1\Parent\ParentFeaturesController::class, 'discipline']);
            Route::get('/parent/awards',                      [\App\Http\Controllers\Api\V1\Parent\ParentFeaturesController::class, 'awards']);
            Route::get('/parent/thoughts',                    [\App\Http\Controllers\Api\V1\Parent\ParentFeaturesController::class, 'thoughts']);
            Route::get('/parent/meal-menu',                   [\App\Http\Controllers\Api\V1\Parent\ParentFeaturesController::class, 'mealMenu']);
        });
    });

    // Mobile Presentation & Admin Modules APIs (Tenant-scoped: supports Sanctum Bearer token OR X-School-Code header)
    Route::group([], function () {
        Route::get('/mobile/config',                      [\App\Http\Controllers\Api\V1\Mobile\MobileAppConfigController::class, 'config']);
        Route::get('/mobile/navigation',                  [\App\Http\Controllers\Api\V1\Mobile\MobileAppConfigController::class, 'navigation']);
        Route::get('/mobile/dashboard',                   [\App\Http\Controllers\Api\V1\Mobile\MobileAppConfigController::class, 'dashboard']);
        Route::get('/mobile/features',                    [\App\Http\Controllers\Api\V1\Mobile\MobileAppConfigController::class, 'features']);
        Route::get('/mobile/banners',                     [\App\Http\Controllers\Api\V1\Mobile\MobileAppConfigController::class, 'banners']);

        // Mobile Staff & Academic APIs (Admin & Teacher presentation)
        Route::get('/staff/students',                     [StaffStudentController::class,'index']);
        Route::get('/staff/students/{id}',                [StaffStudentController::class,'show']);
        Route::put('/staff/students/{id}',                [StaffStudentController::class,'update']);
        Route::post('/staff/students/{id}/photo',          [StaffStudentController::class,'updatePhoto']);
        Route::post('/staff/attendance',                  [StaffAttendanceController::class,'store']);
        Route::get('/staff/attendance',                   [StaffAttendanceController::class,'index']);
        Route::get('/staff/self-attendance',              [SelfAttendanceController::class,'index']);
        Route::post('/staff/self-attendance/punch',       [SelfAttendanceController::class,'punch']);
        Route::get('/staff/leaves',                       [\App\Http\Controllers\Api\V1\Staff\StaffLeaveApiController::class, 'index']);
        Route::post('/staff/leaves',                      [\App\Http\Controllers\Api\V1\Staff\StaffLeaveApiController::class, 'store']);
        Route::get('/staff/transport/dashboard',          [\App\Http\Controllers\Api\V1\Staff\StaffTransportApiController::class, 'dashboard']);
        Route::get('/staff/transport/attendance',         [\App\Http\Controllers\Api\V1\Staff\StaffTransportApiController::class, 'getAttendanceRoster']);
        Route::post('/staff/transport/attendance',        [\App\Http\Controllers\Api\V1\Staff\StaffTransportApiController::class, 'saveAttendance']);
        Route::post('/staff/transport/driver-punch',       [\App\Http\Controllers\Api\V1\Staff\StaffTransportApiController::class, 'driverPunch']);
        Route::get('/staff/class-overview',               [\App\Http\Controllers\Api\V1\Staff\StaffAcademicController::class, 'classOverview']);
        Route::get('/teacher/class-overview',             [\App\Http\Controllers\Api\V1\Staff\StaffAcademicController::class, 'classOverview']);
        Route::get('/staff/notices',                      [\App\Http\Controllers\Api\V1\Staff\StaffAcademicController::class, 'notices']);
        Route::get('/teacher/notices',                    [\App\Http\Controllers\Api\V1\Staff\StaffAcademicController::class, 'notices']);
        Route::get('/staff/library',                      [\App\Http\Controllers\Api\V1\Staff\StaffAcademicController::class, 'library']);
        Route::get('/staff/chat/conversations',           [\App\Http\Controllers\Api\V1\Staff\StaffChatApiController::class, 'conversations']);
        Route::get('/staff/chat/messages',                [\App\Http\Controllers\Api\V1\Staff\StaffChatApiController::class, 'messages']);
        Route::post('/staff/chat/send',                   [\App\Http\Controllers\Api\V1\Staff\StaffChatApiController::class, 'send']);
        Route::get('/staff/chat/recipients',              [\App\Http\Controllers\Api\V1\Staff\StaffAcademicController::class, 'recipients']);
        Route::get('/staff/payroll',                      [\App\Http\Controllers\Api\V1\Staff\StaffAcademicController::class, 'payroll']);
        Route::get('/teacher/payroll',                    [\App\Http\Controllers\Api\V1\Staff\StaffAcademicController::class, 'payroll']);
        Route::get('/notifications/fetch-latest',         [\App\Http\Controllers\NotificationStreamController::class, 'fetchLatest']);
        Route::post('/notifications/{id}/read',           [\App\Http\Controllers\NotificationStreamController::class, 'markAsRead']);
        Route::post('/notifications/read-all',           [\App\Http\Controllers\NotificationStreamController::class, 'markAllRead']);

        // Mobile Admin Modules APIs (EduCore Admin Presentation)
        Route::prefix('mobile/admin')->group(function () {
            Route::get('/mis-report',           [\App\Http\Controllers\Api\V1\Mobile\AdminModulesMobileController::class, 'misReport']);
            Route::get('/finance-overview',    [\App\Http\Controllers\Api\V1\Mobile\AdminModulesMobileController::class, 'financeOverview']);
            Route::get('/staff-directory',     [\App\Http\Controllers\Api\V1\Mobile\AdminModulesMobileController::class, 'staffDirectory']);
            Route::get('/gatepass-history',    [\App\Http\Controllers\Api\V1\Mobile\AdminModulesMobileController::class, 'gatepassHistory']);
            Route::get('/access-roles',        [\App\Http\Controllers\Api\V1\Mobile\AdminModulesMobileController::class, 'accessRoles']);
            Route::get('/app-downloads',       [\App\Http\Controllers\Api\V1\Mobile\AdminModulesMobileController::class, 'appDownloads']);
            Route::get('/e-diary',             [\App\Http\Controllers\Api\V1\Mobile\AdminModulesMobileController::class, 'eDiary']);
            Route::get('/events-calendar',     [\App\Http\Controllers\Api\V1\Mobile\AdminModulesMobileController::class, 'eventsCalendar']);
            Route::get('/certificates',        [\App\Http\Controllers\Api\V1\Mobile\AdminModulesMobileController::class, 'certificates']);
            Route::get('/communication',       [\App\Http\Controllers\Api\V1\Mobile\AdminModulesMobileController::class, 'communication']);
            Route::get('/examination',         [\App\Http\Controllers\Api\V1\Mobile\AdminModulesMobileController::class, 'examination']);
            Route::get('/admissions-hub',      [\App\Http\Controllers\Api\V1\Mobile\AdminModulesMobileController::class, 'admissionsHub']);
            Route::get('/campus-gallery',      [\App\Http\Controllers\Api\V1\Mobile\AdminModulesMobileController::class, 'campusGallery']);
            Route::get('/expenses',            [\App\Http\Controllers\Api\V1\Mobile\AdminModulesMobileController::class, 'expenses']);
            Route::get('/income',              [\App\Http\Controllers\Api\V1\Mobile\AdminModulesMobileController::class, 'income']);
            Route::get('/account-books',       [\App\Http\Controllers\Api\V1\Mobile\AdminModulesMobileController::class, 'accountBooks']);
            Route::get('/reports-center',      [\App\Http\Controllers\Api\V1\Mobile\AdminModulesMobileController::class, 'reportsCenter']);
        });
    });
});
