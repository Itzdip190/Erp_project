<?php

use App\Http\Controllers\Teacher\TeacherDashboardController;
use App\Http\Controllers\Teacher\TeacherAssignmentController;
use App\Http\Controllers\Teacher\TeacherStudyMaterialController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('teacher.dashboard');

// Assignments
Route::resource('assignments', TeacherAssignmentController::class)->names([
    'index'   => 'teacher.assignments.index',
    'create'  => 'teacher.assignments.create',
    'store'   => 'teacher.assignments.store',
    'destroy' => 'teacher.assignments.destroy',
]);

// Study Materials
Route::resource('study-materials', TeacherStudyMaterialController::class)->names([
    'index'   => 'teacher.study-materials.index',
    'create'  => 'teacher.study-materials.create',
    'store'   => 'teacher.study-materials.store',
    'destroy' => 'teacher.study-materials.destroy',
]);

// Notices
Route::get('/notices', [TeacherDashboardController::class, 'notices'])->name('teacher.notices.index');
