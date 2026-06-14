<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware(['web', 'auth', 'role:superadmin'])
                ->prefix('superadmin')
                ->group(base_path('routes/superadmin.php'));

            Route::middleware(['web', 'auth', 'school', 'subscription'])
                ->prefix('school')
                ->group(base_path('routes/school.php'));

            Route::middleware(['web', 'auth', 'role:parent|student'])
                ->prefix('parent')
                ->group(base_path('routes/parent.php'));

            Route::group([], base_path('routes/api_v1.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'school'       => \App\Http\Middleware\IdentifySchoolByDomain::class,
            'subscription' => \App\Http\Middleware\CheckSubscriptionStatus::class,
            'check.module' => \App\Http\Middleware\CheckModuleAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

