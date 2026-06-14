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

Route::prefix('api/v1')->group(function () {

    // Public — no auth
    Route::post('/login',        [AuthController::class,        'login']);
    Route::post('/parent/login', [ParentAuthController::class,  'login']);
    Route::post('/driver/login', [DriverAuthController::class,  'login']);
    Route::post('/otp/send',     [OtpLoginController::class,    'send']);
    Route::post('/otp/verify',   [OtpLoginController::class,    'verify']);

    // face-login: 5 req/min — prevents CPU exhaustion + enumeration
    Route::middleware(['throttle:5,1'])
        ->post('/face-login', [FaceAuthController::class, 'login']);

    // Protected — auth:sanctum
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/logout',           [AuthController::class,      'logout']);
        Route::get('/me',                [AuthController::class,      'me']);
        Route::post('/face-login/enroll',[FaceAuthController::class,  'enroll']);

        // Parent
        Route::get('/parent/children',                    [ChildrenController::class,    'index']);
        Route::get('/parent/children/{id}/profile',       [ChildrenController::class,    'profile']);
        Route::get('/parent/children/{id}/documents',     [ChildrenController::class,    'documents']);
        Route::get('/parent/attendance',                  [ParentAttendanceController::class, 'index']);

        // Staff
        Route::get('/staff/students',                     [StaffStudentController::class,'index']);
        Route::post('/staff/attendance',                  [StaffAttendanceController::class,'store']);
        Route::get('/staff/attendance',                   [StaffAttendanceController::class,'index']);
        Route::post('/staff/self-attendance/punch',       [SelfAttendanceController::class,'punch']);
    });
});
