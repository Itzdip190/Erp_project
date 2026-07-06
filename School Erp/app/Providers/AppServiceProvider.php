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

        // Share dynamic SuperAdmin notifications across navbar and layout views
        try {
            view()->composer(['superadmin.layouts.master', 'superadmin.partials.navbar'], function ($view) {
                if (auth()->check()) {
                    $notifications = [];
                    
                    // 1. Expiring Subscriptions (<= 7 days)
                    $expiringCount = \App\Models\Subscription::where('status', 'active')
                        ->whereBetween('subscription_ends_at', [now(), now()->addDays(7)])
                        ->count();
                    if ($expiringCount > 0) {
                        $notifications[] = [
                            'icon' => 'fas fa-exclamation-triangle text-warning',
                            'text' => "$expiringCount " . ($expiringCount === 1 ? 'School' : 'Schools') . " expiring soon",
                            'time' => 'Action required',
                            'url' => \Illuminate\Support\Facades\Route::has('superadmin.subscriptions.index') ? route('superadmin.subscriptions.index') : '#'
                        ];
                    }
                    
                    // 2. New School Registrations (Created in the last 48 hours)
                    $newSchoolsCount = \App\Models\School::where('created_at', '>=', now()->subDays(2))->count();
                    if ($newSchoolsCount > 0) {
                        $notifications[] = [
                            'icon' => 'fas fa-school text-success',
                            'text' => "$newSchoolsCount new " . ($newSchoolsCount === 1 ? 'school' : 'schools') . " registered",
                            'time' => 'Recent',
                            'url' => route('superadmin.schools.index')
                        ];
                    }

                    // 3. Pending Subscription Orders
                    $pendingOrders = \App\Models\SubscriptionOrder::where('status', 'pending')->count();
                    if ($pendingOrders > 0) {
                        $notifications[] = [
                            'icon' => 'fas fa-receipt text-info',
                            'text' => "$pendingOrders " . ($pendingOrders === 1 ? 'order' : 'orders') . " pending approval",
                            'time' => 'Billing check',
                            'url' => \Illuminate\Support\Facades\Route::has('superadmin.orders.index') ? route('superadmin.orders.index') : '#'
                        ];
                    }

                    // Default system health indicator notification if list is empty
                    if (empty($notifications)) {
                        $notifications[] = [
                            'icon' => 'fas fa-check-circle text-success',
                            'text' => 'All systems running smoothly',
                            'time' => 'Just now',
                            'url' => '#'
                        ];
                    }

                    $view->with('superadminNotifications', $notifications);
                }
            });
        } catch (\Exception $e) {
            // Silence if database not ready
        }
    }
}
