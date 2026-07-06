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

            Route::middleware(['web', 'auth', 'role:parent|student', 'active_student'])
                ->prefix('parent')
                ->group(base_path('routes/parent.php'));

            Route::middleware(['web', 'auth', 'school', 'subscription'])
                ->prefix('teacher')
                ->group(base_path('routes/teacher.php'));

            Route::group([], base_path('routes/api_v1.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(prepend: [
            \App\Http\Middleware\AutoExitImpersonation::class,
        ], append: [
            \App\Http\Middleware\InjectLanguageSwitcherMiddleware::class,
        ]);
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'school'       => \App\Http\Middleware\IdentifySchoolByDomain::class,
            'subscription' => \App\Http\Middleware\CheckSubscriptionStatus::class,
            'check.module' => \App\Http\Middleware\CheckModuleAccess::class,
            'active_student' => \App\Http\Middleware\CheckActiveStudent::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Catch Spatie's role/permission unauthorized exception → redirect to login
        $exceptions->render(function (
            \Spatie\Permission\Exceptions\UnauthorizedException $e,
            \Illuminate\Http\Request $request
        ) {
            if (!$request->expectsJson()) {
                return redirect()->route('login')->withErrors([
                    'email' => 'You do not have permission to access that page. Please log in with the correct account.',
                ]);
            }
        });

        // When any other 403 HTTP exception fires, redirect to login
        $exceptions->render(function (
            \Symfony\Component\HttpKernel\Exception\HttpException $e,
            \Illuminate\Http\Request $request
        ) {
            if ($e->getStatusCode() === 403 && !$request->expectsJson()) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Access denied. Please log in with an authorised account.',
                ]);
            }
        });

        // When auth middleware fires (unauthenticated), redirect to login
        $exceptions->render(function (
            \Illuminate\Auth\AuthenticationException $e,
            \Illuminate\Http\Request $request
        ) {
            if (!$request->expectsJson()) {
                return redirect()->route('login');
            }
        });

        // Catch database connection or query execution errors in production (when config('app.debug') is false)
        $exceptions->render(function (
            \Illuminate\Database\QueryException $e,
            \Illuminate\Http\Request $request
        ) {
            if (!config('app.debug') && !$request->expectsJson()) {
                return response()->view('errors.503', [], 503);
            }
        });
    })->create();

