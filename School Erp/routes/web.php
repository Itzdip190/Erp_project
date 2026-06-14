<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function () {
    return view('welcome');
});

// Database Migration & Seeding Helper Route for Hosting (Secured with Key)
Route::get('/migrate-db', function (\Illuminate\Http\Request $request) {
    $expectedKey = env('DB_MIGRATE_KEY');
    
    if (!$expectedKey || $request->query('key') !== $expectedKey) {
        abort(403, 'Unauthorized. Please provide a valid migration key.');
    }

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
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout.post');

// Subscription Expiry Fallback
Route::get('/subscription-expired', function () {
    return view('errors.subscription-expired');
})->name('subscription.expired');
