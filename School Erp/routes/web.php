<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function () {
    return view('welcome');
});

// Database Migration & Seeding Helper Route for Hosting
Route::get('/migrate-db', function () {
    try {
        // Run migrations and seed database in force mode for production environments
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh', [
            '--force' => true,
            '--seed' => true
        ]);
        $output = \Illuminate\Support\Facades\Artisan::output();
        return response("<h3>Database Migration & Seeding Successful!</h3><pre>{$output}</pre>", 200);
    } catch (\Exception $e) {
        return response("<h3>Database Migration Failed!</h3><p>{$e->getMessage()}</p>", 500);
    }
});

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout.post');

// Roles dashboard landing stubs
Route::get('/school/dashboard', function () {
    return response('<h3>School Dashboard (School Admin / Teacher)</h3><p>Redirected successfully!</p><p><a href="/logout">Logout</a></p>', 200);
})->middleware('auth')->name('school.dashboard');

Route::get('/parent/dashboard', function () {
    return response('<h3>Parent Dashboard</h3><p>Redirected successfully!</p><p><a href="/logout">Logout</a></p>', 200);
})->middleware('auth')->name('parent.dashboard');

Route::get('/student/dashboard', function () {
    return response('<h3>Student Dashboard</h3><p>Redirected successfully!</p><p><a href="/logout">Logout</a></p>', 200);
})->middleware('auth')->name('student.dashboard');

Route::get('/school/login', function () {
    return response('<h3>School Admin Login</h3><p>Subdomain routing logic will go here.</p><p><a href="/login">Back to Root Login</a></p>', 200);
})->name('school.login');
