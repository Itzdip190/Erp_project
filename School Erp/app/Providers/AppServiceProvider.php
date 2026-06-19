<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $helperPath = app_path('Helpers/NumberHelper.php');
        if (file_exists($helperPath)) {
            require_once $helperPath;
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Auto-run migrations if any of our tables are missing
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('fee_categories') || 
                !\Illuminate\Support\Facades\Schema::hasTable('timetables') || 
                !\Illuminate\Support\Facades\Schema::hasTable('card_templates')) {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            }
        } catch (\Exception $e) {
            // Fail silently or log
        }
    }
}
