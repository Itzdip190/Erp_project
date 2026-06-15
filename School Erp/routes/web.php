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
        
        // Clear all cached configurations, routes, and compiled views to apply changes immediately on Hostinger
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        
        $output = \Illuminate\Support\Facades\Artisan::output();
        return response("<h3>Database Migration, Seeding & Cache Clearing Successful!</h3><pre>{$output}</pre>", 200);
    } catch (\Exception $e) {
        return response("<h3>Database Migration Failed!</h3><p>{$e->getMessage()}</p>", 500);
    }
});

// Storage Debug Route
Route::get('/debug-storage', function (\Illuminate\Http\Request $request) {
    $expectedKey = env('DB_MIGRATE_KEY');
    if (!$expectedKey || $request->query('key') !== $expectedKey) {
        abort(403, 'Unauthorized.');
    }

    $defaultDisk = config('filesystems.default');
    $disk = \Illuminate\Support\Facades\Storage::disk($defaultDisk);
    $docs = \App\Models\StudentDocument::orderBy('created_at', 'desc')->take(10)->get();

    $output = "<h1>Storage Diagnostic Tool</h1>";
    $output .= "<p><strong>Default Disk:</strong> {$defaultDisk}</p>";
    $output .= "<p><strong>Storage App Private Path:</strong> " . storage_path('app/private') . " (Exists: " . (is_dir(storage_path('app/private')) ? 'YES' : 'NO') . ", Writable: " . (is_writable(storage_path('app/private')) ? 'YES' : 'NO') . ")</p>";
    $output .= "<p><strong>Storage App Public Path:</strong> " . storage_path('app/public') . " (Exists: " . (is_dir(storage_path('app/public')) ? 'YES' : 'NO') . ", Writable: " . (is_writable(storage_path('app/public')) ? 'YES' : 'NO') . ")</p>";
    $output .= "<h2>Last 10 Student Documents in DB:</h2>";

    if ($docs->isEmpty()) {
        $output .= "<p>No documents found in database.</p>";
    } else {
        $output .= "<table border='1' cellpadding='8' cellspacing='0'>";
        $output .= "<tr><th>ID</th><th>Student ID</th><th>Type</th><th>File Path</th><th>Original Name</th><th>Exists on Disk?</th><th>Full Path Checked</th></tr>";
        foreach ($docs as $doc) {
            $exists = $disk->exists($doc->file_path) ? 'YES' : 'NO';
            $fullPath = '';
            try {
                $fullPath = $disk->path($doc->file_path);
            } catch (\Exception $e) {
                $fullPath = 'Error: ' . $e->getMessage();
            }
            $output .= "<tr>";
            $output .= "<td>{$doc->id}</td>";
            $output .= "<td>{$doc->student_id}</td>";
            $output .= "<td>{$doc->document_type}</td>";
            $output .= "<td>{$doc->file_path}</td>";
            $output .= "<td>{$doc->original_name}</td>";
            $output .= "<td style='color:" . ($exists === 'YES' ? 'green' : 'red') . "; font-weight:bold;'>{$exists}</td>";
            $output .= "<td>{$fullPath}</td>";
            $output .= "</tr>";
        }
        $output .= "</table>";
    }

    // Read and append Laravel Logs for debugging
    $logPath = storage_path('logs/laravel.log');
    if (file_exists($logPath)) {
        $output .= "<h2>Last 3000 Lines of laravel.log:</h2>";
        $fileLines = file($logPath);
        $count = count($fileLines);
        $start = max(0, $count - 3000);
        $output .= "<pre style='background:#f4f4f4; padding:10px; border:1px solid #ccc; max-height: 800px; overflow-y: auto; font-family: monospace; font-size: 11px;'>";
        for ($i = $start; $i < $count; $i++) {
            $output .= htmlspecialchars($fileLines[$i]);
        }
        $output .= "</pre>";
    } else {
        $output .= "<h2>laravel.log not found at {$logPath}.</h2>";
    }

    return response($output);
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
