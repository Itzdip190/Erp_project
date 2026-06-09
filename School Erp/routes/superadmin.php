<?php
// routes/superadmin.php

use App\Http\Controllers\SuperAdmin\DashboardController;
use Illuminate\Support\Facades\Route;

// SuperAdmin Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('superadmin.dashboard');

// Stubs for Quick Actions
Route::get('/schools/create', function () {
    return response('Add New School Page - Stub', 200);
})->name('superadmin.schools.create');

Route::get('/plans/create', function () {
    return response('Create Subscription Plan Page - Stub', 200);
})->name('superadmin.plans.create');

Route::get('/broadcast', function () {
    return response('Send Broadcast Notification Page - Stub', 200);
})->name('superadmin.broadcast');

Route::get('/server-status', function () {
    return response('Server Status Page - Stub', 200);
})->name('superadmin.server-status');
