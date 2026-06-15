<?php

use App\Http\Controllers\School\Student\StudentController;
use App\Http\Controllers\School\Student\StudentIdCardController;
use App\Http\Controllers\School\Student\AdmitCardController;
use App\Http\Controllers\School\Student\CertificateController;
use App\Http\Controllers\School\Attendance\StudentAttendanceController;
use App\Http\Controllers\School\Attendance\StaffAttendanceController;
use App\Http\Controllers\School\SchoolDashboardController;
use App\Http\Controllers\School\SettingsController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/dashboard', [SchoolDashboardController::class, 'index'])->name('school.dashboard');
Route::get('/dashboard/chart/fee',    [SchoolDashboardController::class, 'feeChartData'])->name('school.dashboard.chart.fee');
Route::get('/dashboard/chart/attend', [SchoolDashboardController::class, 'attendanceChartData'])->name('school.dashboard.chart.attend');
Route::get('/dashboard/snapshot',     [SchoolDashboardController::class, 'snapshot'])->name('school.dashboard.snapshot');
Route::post('/chatbot/send',          [SchoolDashboardController::class, 'chatbotStub'])->name('school.chatbot.send');

// Students Module
Route::middleware(['check.module:students'])->group(function () {
    Route::get('/students/import-template', [StudentController::class, 'downloadTemplate'])->name('school.students.import-template');
    Route::post('/students/import', [StudentController::class, 'bulkImport'])->name('school.students.import');
    Route::get('/students/export', [StudentController::class, 'export'])->name('school.students.export');
    Route::get('/students/promote', [StudentController::class, 'promoteForm'])->name('school.students.promote-form');
    Route::post('/students/promote', [StudentController::class, 'promote'])->name('school.students.promote');

    Route::resource('students', StudentController::class)->names([
        'index' => 'school.students.index',
        'create' => 'school.students.create',
        'store' => 'school.students.store',
        'show' => 'school.students.show',
        'edit' => 'school.students.edit',
        'update' => 'school.students.update',
        'destroy' => 'school.students.destroy',
    ]);

    Route::get('/students/{student}/id-card', [StudentIdCardController::class, 'generate'])->name('school.students.id-card');
    Route::get('/students/{student}/admit-card', [AdmitCardController::class, 'generate'])->name('school.students.admit-card');
    Route::get('/students/{student}/certificate/{type}', [CertificateController::class, 'generate'])->name('school.students.certificate');
    Route::post('/students/{student}/issue-document', [StudentController::class, 'issueDocument'])->name('school.students.issue-document');
    Route::post('/students/bulk-issue-document', [StudentController::class, 'bulkIssueDocuments'])->name('school.students.bulk-issue-document');
});

// Attendance Module
Route::middleware(['check.module:attendance'])->group(function () {
    // Student Attendance
    Route::get('/attendance/students', [StudentAttendanceController::class, 'index'])->name('school.attendance.students.index');
    Route::post('/attendance/students/load', [StudentAttendanceController::class, 'loadSection'])->name('school.attendance.students.load');
    Route::post('/attendance/students', [StudentAttendanceController::class, 'store'])->name('school.attendance.students.store');
    Route::get('/attendance/students/report', [StudentAttendanceController::class, 'report'])->name('school.attendance.students.report');
    Route::get('/attendance/students/daily', [StudentAttendanceController::class, 'dailyReport'])->name('school.attendance.students.daily');
    Route::get('/attendance/students/stats', [StudentAttendanceController::class, 'stats'])->name('school.attendance.students.stats');

    // Staff Attendance
    Route::get('/attendance/staff', [StaffAttendanceController::class, 'index'])->name('school.attendance.staff.index');
    Route::post('/attendance/staff', [StaffAttendanceController::class, 'store'])->name('school.attendance.staff.store');
    Route::get('/attendance/staff/report', [StaffAttendanceController::class, 'report'])->name('school.attendance.staff.report');
});

// Settings & Profile
Route::get('/settings', [SettingsController::class, 'index'])->name('school.settings.index');
Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('school.settings.profile');
Route::put('/settings/password', [SettingsController::class, 'changePassword'])->name('school.settings.password');

// Overview Features
Route::get('/dashboard/mis-report', [SchoolDashboardController::class, 'misReport'])->name('school.dashboard.mis-report');

// Settings & Institute Info
Route::get('/settings/institute-info', [SettingsController::class, 'instituteInfo'])->name('school.settings.institute-info');
Route::put('/settings/institute-info', [SettingsController::class, 'updateInstituteInfo'])->name('school.settings.institute-info.update');
Route::get('/settings/implementation', [SettingsController::class, 'implementationProcess'])->name('school.settings.implementation');
Route::get('/settings/udise', [SettingsController::class, 'udise'])->name('school.settings.udise');
Route::put('/settings/udise', [SettingsController::class, 'updateUdise'])->name('school.settings.udise.update');
Route::get('/settings/reset-password', [SettingsController::class, 'resetPasswordPage'])->name('school.settings.reset-password');
Route::post('/settings/reset-password', [SettingsController::class, 'resetPassword'])->name('school.settings.reset-password.post');

// Role Management Features
Route::get('/role-management/roles', [\App\Http\Controllers\School\RoleController::class, 'index'])->name('school.roles.index');
Route::get('/role-management/staff-access', [\App\Http\Controllers\School\RoleController::class, 'staffAccess'])->name('school.roles.staff-access');
Route::put('/role-management/staff-access/{user}', [\App\Http\Controllers\School\RoleController::class, 'updateStaffAccess'])->name('school.roles.staff-access.update');

// Staff Management CRUD & Additional Features
Route::get('/staff/import', [\App\Http\Controllers\School\StaffController::class, 'importForm'])->name('school.staff.import');
Route::post('/staff/import', [\App\Http\Controllers\School\StaffController::class, 'bulkImport'])->name('school.staff.import.post');
Route::get('/staff/bulk-photo', [\App\Http\Controllers\School\StaffController::class, 'bulkPhotoForm'])->name('school.staff.bulk-photo');
Route::post('/staff/bulk-photo', [\App\Http\Controllers\School\StaffController::class, 'bulkPhotoUpload'])->name('school.staff.bulk-photo.post');
Route::get('/staff/bulk-attendance', [\App\Http\Controllers\School\StaffController::class, 'bulkAttendance'])->name('school.staff.bulk-attendance');
Route::post('/staff/bulk-attendance', [\App\Http\Controllers\School\StaffController::class, 'saveBulkAttendance'])->name('school.staff.bulk-attendance.post');

Route::resource('staff', \App\Http\Controllers\School\StaffController::class)->names([
    'index' => 'school.staff.index',
    'create' => 'school.staff.create',
    'store' => 'school.staff.store',
    'show' => 'school.staff.show',
    'edit' => 'school.staff.edit',
    'update' => 'school.staff.update',
    'destroy' => 'school.staff.destroy',
]);
