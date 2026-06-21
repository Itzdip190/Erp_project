<?php

use App\Http\Controllers\Parent\ParentDashboardController;
use App\Http\Controllers\Parent\ParentChildrenController;
use App\Http\Controllers\Parent\ParentAttendanceController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard',          [ParentDashboardController::class, 'index'])->name('parent.dashboard');
Route::get('/children/{student}', [ParentChildrenController::class,  'show'])->name('parent.children.show');
Route::get('/attendance',         [ParentAttendanceController::class,'index'])->name('parent.attendance.index');
Route::get('/fees',               [ParentDashboardController::class, 'fees'])->name('parent.fees.index');
Route::get('/timetable',          [ParentDashboardController::class, 'timetable'])->name('parent.timetable.index');
Route::get('/documents',          [ParentDashboardController::class, 'documents'])->name('parent.documents.index');
Route::get('/documents/{document}/download', [ParentDashboardController::class, 'downloadDocument'])->name('parent.documents.download');

Route::get('/diary',              [ParentDashboardController::class, 'diary'])->name('parent.diary.index');
Route::get('/events',             [ParentDashboardController::class, 'events'])->name('parent.events.index');
Route::get('/cards',              [ParentDashboardController::class, 'cards'])->name('parent.cards.index');
Route::get('/certificates',       [ParentDashboardController::class, 'certificates'])->name('parent.certificates.index');

// Leaves
Route::get('/leaves',             [ParentDashboardController::class, 'leaves'])->name('parent.leaves.index');
Route::post('/leaves/store',      [ParentDashboardController::class, 'storeLeave'])->name('parent.leaves.store');

// Exams
Route::get('/exams',              [ParentDashboardController::class, 'exams'])->name('parent.exams.index');

// Notices
Route::get('/notices',            [ParentDashboardController::class, 'notices'])->name('parent.notices.index');

// Surveys
Route::get('/surveys',            [ParentDashboardController::class, 'surveys'])->name('parent.surveys.index');
Route::post('/surveys/{survey}/vote', [ParentDashboardController::class, 'voteSurvey'])->name('parent.surveys.vote');

// Chat
Route::get('/chat',               [ParentDashboardController::class, 'chat'])->name('parent.chat.index');
Route::post('/chat/send',         [ParentDashboardController::class, 'sendChatMessage'])->name('parent.chat.send');

