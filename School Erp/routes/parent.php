<?php

use App\Http\Controllers\Parent\ParentDashboardController;
use App\Http\Controllers\Parent\ParentChildrenController;
use App\Http\Controllers\Parent\ParentAttendanceController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard',          [ParentDashboardController::class, 'index'])->name('parent.dashboard');
Route::get('/children/{student}', [ParentChildrenController::class,  'show'])->name('parent.children.show');
Route::get('/attendance',         [ParentAttendanceController::class,'index'])->name('parent.attendance.index');
