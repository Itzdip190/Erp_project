<?php
// routes/superadmin.php

use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\AiController;
use App\Http\Controllers\SuperAdmin\SchoolController;
use App\Http\Controllers\SuperAdmin\ProfileController;
use App\Http\Controllers\SuperAdmin\SettingsController;
use App\Http\Controllers\SuperAdmin\SchoolRequestController;
use Illuminate\Support\Facades\Route;

// SuperAdmin Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('superadmin.dashboard');
Route::post('/dashboard/optimize-db', [DashboardController::class, 'optimizeDb'])->name('superadmin.dashboard.optimize-db');
Route::post('/dashboard/quick-extend', [DashboardController::class, 'quickExtend'])->name('superadmin.dashboard.quick-extend');
Route::get('/dashboard/export-report', [DashboardController::class, 'exportReport'])->name('superadmin.dashboard.export-report');

// Profile
Route::get('/profile', [ProfileController::class, 'index'])->name('superadmin.profile.index');
Route::post('/profile/update', [ProfileController::class, 'update'])->name('superadmin.profile.update');
Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('superadmin.profile.password');

// Settings
Route::get('/settings', [SettingsController::class, 'index'])->name('superadmin.settings');
Route::post('/settings/update', [SettingsController::class, 'update'])->name('superadmin.settings.update');

// ─── Schools ──────────────────────────────────────────────────
Route::get('/school-requests',         [SchoolRequestController::class, 'index'])->name('superadmin.school-requests.index');
Route::post('/school-requests/{schoolRequest}/approve', [SchoolRequestController::class, 'approve'])->name('superadmin.school-requests.approve');
Route::post('/school-requests/{schoolRequest}/reject',  [SchoolRequestController::class, 'reject'])->name('superadmin.school-requests.reject');

Route::get('/schools',         [SchoolController::class, 'index'])->name('superadmin.schools.index');
Route::get('/schools/create',  [SchoolController::class, 'create'])->name('superadmin.schools.create');
Route::post('/schools',        [SchoolController::class, 'store'])->name('superadmin.schools.store');
Route::get('/schools/{school}/edit', [SchoolController::class, 'edit'])->name('superadmin.schools.edit');
Route::put('/schools/{school}', [SchoolController::class, 'update'])->name('superadmin.schools.update');
Route::delete('/schools/{school}', [SchoolController::class, 'destroy'])->name('superadmin.schools.destroy');
Route::post('/schools/{school}/toggle-status', [SchoolController::class, 'toggleStatus'])->name('superadmin.schools.toggle-status');
Route::post('/schools/{school}/impersonate', [SchoolController::class, 'impersonate'])->name('superadmin.schools.impersonate');

// Inactive Student Management
Route::get('/schools/{school}/inactive-students', [SchoolController::class, 'inactiveStudents'])->name('superadmin.schools.inactive-students');
Route::post('/schools/{school}/inactive-students/restore', [SchoolController::class, 'restoreStudents'])->name('superadmin.schools.restore-students');
Route::post('/schools/{school}/inactive-students/delete', [SchoolController::class, 'deleteStudentsPermanently'])->name('superadmin.schools.delete-students');

// ─── School Data Reset ─────────────────────────────────────────
Route::get('/schools/{school}/reset-data',   [SchoolController::class, 'resetDataPage'])->name('superadmin.schools.reset-data');
Route::post('/schools/{school}/reset-data',  [SchoolController::class, 'resetData'])->name('superadmin.schools.reset-data.execute');

// ─── AI Intelligence Module ───────────────────────────────────
Route::get('/ai',              [AiController::class, 'index'])->name('superadmin.ai.index');
Route::post('/ai/toggle',      [AiController::class, 'toggleSchool'])->name('superadmin.ai.toggle');
Route::get('/ai/chat',         [AiController::class, 'chat'])->name('superadmin.ai.chat');
Route::post('/ai/chat/send',   [AiController::class, 'sendMessage'])->name('superadmin.ai.chat.send');

// Stubs for other quick actions
if (class_exists(\App\Http\Controllers\SuperAdmin\PlanController::class)) {
    Route::resource('plans', \App\Http\Controllers\SuperAdmin\PlanController::class)->names('superadmin.plans');
} else {
    Route::get('/plans/create', function () {
        return response('Create Subscription Plan Page - Coming Soon', 200);
    })->name('superadmin.plans.create');
}

Route::get('/broadcast', function () {
    return response('Send Broadcast Notification Page - Coming Soon', 200);
})->name('superadmin.broadcast');

// ─── Menu Manager & Audit Logs ──────────────────────────────────
if (class_exists(\App\Http\Controllers\SuperAdmin\MenuManagerController::class)) {
    Route::get('/menu-manager', [\App\Http\Controllers\SuperAdmin\MenuManagerController::class, 'index'])->name('superadmin.menu-manager.index');
    Route::post('/menu-manager/update', [\App\Http\Controllers\SuperAdmin\MenuManagerController::class, 'update'])->name('superadmin.menu-manager.update');
}

if (class_exists(\App\Http\Controllers\SuperAdmin\AuditLogController::class)) {
    Route::get('/audit-logs', [\App\Http\Controllers\SuperAdmin\AuditLogController::class, 'index'])->name('superadmin.audit-logs');
}

// ─── Subscriptions & Billing ──────────────────────────────────
if (class_exists(\App\Http\Controllers\SuperAdmin\SubscriptionController::class)) {
    Route::get('/subscriptions', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'index'])->name('superadmin.subscriptions.index');
    Route::post('/subscriptions/extend', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'extend'])->name('superadmin.subscriptions.extend');
    Route::post('/subscriptions/change-plan', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'changePlan'])->name('superadmin.subscriptions.change-plan');
    Route::post('/subscriptions/cancel', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'cancel'])->name('superadmin.subscriptions.cancel');
}

if (class_exists(\App\Http\Controllers\SuperAdmin\OrderController::class)) {
    Route::get('/orders', [\App\Http\Controllers\SuperAdmin\OrderController::class, 'index'])->name('superadmin.orders.index');
    Route::put('/orders/{order}/status', [\App\Http\Controllers\SuperAdmin\OrderController::class, 'updateStatus'])->name('superadmin.orders.update-status');
}

if (class_exists(\App\Http\Controllers\SuperAdmin\PaymentGatewayController::class)) {
    Route::get('/gateways', [\App\Http\Controllers\SuperAdmin\PaymentGatewayController::class, 'index'])->name('superadmin.gateways.index');
    Route::post('/gateways', [\App\Http\Controllers\SuperAdmin\PaymentGatewayController::class, 'update'])->name('superadmin.gateways.update');
}

// ─── Platform Config Modules ──────────────────────────────────
if (class_exists(\App\Http\Controllers\SuperAdmin\SmsGatewayController::class)) {
    Route::get('/sms-gateways', [\App\Http\Controllers\SuperAdmin\SmsGatewayController::class, 'index'])->name('superadmin.sms-gateways.index');
    Route::post('/sms-gateways', [\App\Http\Controllers\SuperAdmin\SmsGatewayController::class, 'update'])->name('superadmin.sms-gateways.update');
}

if (class_exists(\App\Http\Controllers\SuperAdmin\NotificationTypeController::class)) {
    Route::get('/notification-types', [\App\Http\Controllers\SuperAdmin\NotificationTypeController::class, 'index'])->name('superadmin.notification-types.index');
    Route::post('/notification-types', [\App\Http\Controllers\SuperAdmin\NotificationTypeController::class, 'update'])->name('superadmin.notification-types.update');
}

if (class_exists(\App\Http\Controllers\SuperAdmin\BlogCmsController::class)) {
    Route::resource('blog-cms', \App\Http\Controllers\SuperAdmin\BlogCmsController::class)->names('superadmin.blog-cms');
}

if (class_exists(\App\Http\Controllers\SuperAdmin\WhiteLabelController::class)) {
    Route::get('/white-label', [\App\Http\Controllers\SuperAdmin\WhiteLabelController::class, 'index'])->name('superadmin.white-label.index');
    Route::post('/white-label', [\App\Http\Controllers\SuperAdmin\WhiteLabelController::class, 'update'])->name('superadmin.white-label.update');
}

if (class_exists(\App\Http\Controllers\SuperAdmin\PlatformSettingsController::class)) {
    Route::get('/platform-settings', [\App\Http\Controllers\SuperAdmin\PlatformSettingsController::class, 'index'])->name('superadmin.platform-settings.index');
    Route::post('/platform-settings', [\App\Http\Controllers\SuperAdmin\PlatformSettingsController::class, 'update'])->name('superadmin.platform-settings.update');
}
