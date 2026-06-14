<?php

use App\Http\Controllers\School\Student\StudentController;
use App\Http\Controllers\School\Student\StudentIdCardController;
use App\Http\Controllers\School\Student\AdmitCardController;
use App\Http\Controllers\School\Student\CertificateController;
use App\Http\Controllers\School\Attendance\StudentAttendanceController;
use App\Http\Controllers\School\Attendance\StaffAttendanceController;
use App\Http\Controllers\School\SchoolDashboardController;
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
