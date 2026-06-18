<?php

use App\Http\Controllers\Parent\ParentDashboardController;
use App\Http\Controllers\Parent\ParentChildrenController;
use App\Http\Controllers\Parent\ParentAttendanceController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard',          [ParentDashboardController::class, 'index'])->name('parent.dashboard');
Route::get('/children/{student}', [ParentChildrenController::class,  'show'])->name('parent.children.show');
Route::get('/attendance',         [ParentAttendanceController::class,'index'])->name('parent.attendance.index');
Route::get('/documents',          [ParentDashboardController::class, 'documents'])->name('parent.documents.index');
Route::get('/documents/{document}/download', [ParentDashboardController::class, 'downloadDocument'])->name('parent.documents.download');

Route::get('/diary',              [ParentDashboardController::class, 'diary'])->name('parent.diary.index');
Route::get('/events',             [ParentDashboardController::class, 'events'])->name('parent.events.index');
Route::get('/cards',              [ParentDashboardController::class, 'cards'])->name('parent.cards.index');
Route::get('/certificates',       [ParentDashboardController::class, 'certificates'])->name('parent.certificates.index');
