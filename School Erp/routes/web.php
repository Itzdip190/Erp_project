<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/migrate-db', function (\Illuminate\Http\Request $request) {
    $expectedKey = env('DB_MIGRATE_KEY');
    
    if (!$expectedKey || $request->query('key') !== $expectedKey) {
        abort(403, 'Unauthorized. Please provide a valid migration key.');
    }

    // Prevent PHP timeout on shared hosting (Hostinger has short default limits)
    @set_time_limit(0);
    @ini_set('memory_limit', '512M');
    @ini_set('max_execution_time', 0);

    $allOutput = '';

    try {
        if ($request->query('fresh') === 'true') {
            // DESTRUCTIVE: wipes all data
            \Illuminate\Support\Facades\Artisan::call('migrate:fresh', [
                '--force' => true,
                '--seed' => true
            ]);
            $allOutput .= \Illuminate\Support\Facades\Artisan::output();
            $actionText = "Database Fresh Migration & Seeding (Destructive)";
        } else {
            // NON-DESTRUCTIVE: keeps existing data
            \Illuminate\Support\Facades\Artisan::call('migrate', [
                '--force' => true
            ]);
            $allOutput .= \Illuminate\Support\Facades\Artisan::output();
            if ($request->query('seed') === 'true') {
                \Illuminate\Support\Facades\Artisan::call('db:seed', [
                    '--force' => true
                ]);
                $allOutput .= \Illuminate\Support\Facades\Artisan::output();
                $actionText = "Safe Database Migration & Seeding (Non-Destructive)";
            } else {
                $actionText = "Safe Database Migration (Non-Destructive)";
            }
        }

        // Ensure public/uploads directory exists and is writable
        $uploadsDir = public_path('uploads');
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0755, true);
            $allOutput .= "\n[INFO] Created uploads directory at: {$uploadsDir}";
        } else {
            $allOutput .= "\n[INFO] Uploads directory already exists at: {$uploadsDir}";
        }
        
        // Clear all caches
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        $allOutput .= \Illuminate\Support\Facades\Artisan::output();
        
        return response("<h3 style='color:green'>✅ {$actionText} Successful!</h3><pre>{$allOutput}</pre>", 200);
    } catch (\Throwable $e) {
        $errorMsg  = "<h3 style='color:red'>❌ Database Migration Failed!</h3>";
        $errorMsg .= "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        $errorMsg .= "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " (Line: " . $e->getLine() . ")</p>";
        $errorMsg .= "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        if ($allOutput) {
            $errorMsg .= "<h4>Output before error:</h4><pre>" . htmlspecialchars($allOutput) . "</pre>";
        }
        return response($errorMsg, 500);
    }
});

Route::get('/deploy-git', function (\Illuminate\Http\Request $request) {
    $expectedKey = env('DB_MIGRATE_KEY');
    if (!$expectedKey || $request->query('key') !== $expectedKey) {
        abort(403, 'Unauthorized.');
    }

    $output = [];
    $returnVar = 0;
    exec('git pull origin main 2>&1', $output, $returnVar);

    if ($returnVar === 0) {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $output[] = "[INFO] Laravel cache cleared and migrations run.";
        $output[] = \Illuminate\Support\Facades\Artisan::output();
    }

    return response()->json([
        'output' => $output,
        'return_var' => $returnVar
    ]);
});

// Fix Tables Route — directly creates staff_module_access if it doesn't exist (bypasses migration tracking)
Route::get('/fix-tables', function (\Illuminate\Http\Request $request) {
    $expectedKey = env('DB_MIGRATE_KEY');
    if (!$expectedKey || $request->query('key') !== $expectedKey) {
        abort(403, 'Unauthorized.');
    }

    $output = '';
    $created = [];
    $skipped = [];

    try {
        if (!\Illuminate\Support\Facades\Schema::hasTable('module_permissions')) {
            \Illuminate\Support\Facades\Schema::create('module_permissions', function ($table) {
                $table->id();
                $table->unsignedBigInteger('school_id');
                $table->string('module_key');
                $table->string('feature_key');
                $table->boolean('view_access')->default(false);
                $table->boolean('edit_access')->default(false);
                $table->timestamps();
                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                $table->unique(['school_id', 'module_key', 'feature_key']);
                $table->index(['school_id', 'module_key']);
            });
            $created[] = 'module_permissions';
        } else {
            $skipped[] = 'module_permissions (already exists)';
        }

        if (!\Illuminate\Support\Facades\Schema::hasTable('staff_module_access')) {
            \Illuminate\Support\Facades\Schema::create('staff_module_access', function ($table) {
                $table->id();
                $table->unsignedBigInteger('school_id');
                $table->unsignedBigInteger('user_id');
                $table->string('module_key');
                $table->string('feature_key');
                $table->boolean('view_access')->default(false);
                $table->boolean('edit_access')->default(false);
                $table->timestamps();
                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->unique(['school_id', 'user_id', 'module_key', 'feature_key'], 'sma_school_user_mod_feat_unique');
                $table->index(['school_id', 'module_key', 'feature_key']);
            });
            $created[] = 'staff_module_access';
        } else {
            $skipped[] = 'staff_module_access (already exists)';
        }

        // Run pending migrations too
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $migrateOutput = \Illuminate\Support\Facades\Artisan::output();

        // Clear all caches
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');

        $output = '<h2 style="color:green">✅ Tables Fixed Successfully!</h2>';
        $output .= '<p><strong>Created:</strong> ' . (empty($created) ? 'None' : implode(', ', $created)) . '</p>';
        $output .= '<p><strong>Skipped:</strong> ' . (empty($skipped) ? 'None' : implode(', ', $skipped)) . '</p>';
        $output .= '<pre>' . $migrateOutput . '</pre>';
        return response($output, 200);
    } catch (\Exception $e) {
        return response('<h2 style="color:red">❌ Fix Failed</h2><pre>' . $e->getMessage() . "\n\n" . $e->getTraceAsString() . '</pre>', 500);
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
    $output .= "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
    $output .= "<p><strong>Default Disk:</strong> {$defaultDisk}</p>";
    $output .= "<p><strong>Base Path:</strong> " . base_path() . " (Exists: " . (is_dir(base_path()) ? 'YES' : 'NO') . ")</p>";
    $output .= "<p><strong>Public Path:</strong> " . public_path() . " (Exists: " . (is_dir(public_path()) ? 'YES' : 'NO') . ")</p>";
    $output .= "<p><strong>base_path('public') exists:</strong> " . (is_dir(base_path('public')) ? 'YES' : 'NO') . "</p>";
    $output .= "<p><strong>base_path('uploads') exists:</strong> " . (is_dir(base_path('uploads')) ? 'YES' : 'NO') . "</p>";
    $output .= "<p><strong>public_path('uploads') exists:</strong> " . (is_dir(public_path('uploads')) ? 'YES' : 'NO') . ", Writable: " . (is_writable(public_path('uploads')) ? 'YES' : 'NO') . "</p>";
    $output .= "<p><strong>public disk root (config):</strong> " . config('filesystems.disks.public.root') . "</p>";
    $output .= "<p><strong>public disk url (config):</strong> " . config('filesystems.disks.public.url') . "</p>";
    $output .= "<p><strong>Storage::disk('public') root:</strong> " . \Illuminate\Support\Facades\Storage::disk('public')->path('') . "</p>";
    $output .= "<p><strong>Sample photo_url would be:</strong> " . \Illuminate\Support\Facades\Storage::disk('public')->url('staff/sample.jpg') . "</p>";
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

Route::get('/db-status', function () {
    $dbConnection = config('database.default');
    $dbConfig = config("database.connections.{$dbConnection}");
    
    try {
        $classes = \App\Models\SchoolClass::all();
        $classCount = $classes->count();
        $classList = $classes->pluck('name')->toArray();
        $error = null;
    } catch (\Exception $e) {
        $classCount = 0;
        $classList = [];
        $error = $e->getMessage();
    }
    
    return [
        'active_connection' => $dbConnection,
        'database_details' => $dbConfig,
        'classes_in_database_count' => $classCount,
        'classes_list' => $classList,
        'error' => $error,
    ];
});
