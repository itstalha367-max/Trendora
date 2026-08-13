<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AdminPermissionMiddleware;
use App\Http\Middleware\TwoFactorMiddleware;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\LogAdminActivity;
use App\Http\Middleware\ApiKeyMiddleware;
use App\Http\Middleware\MaintenanceMode;
use Barryvdh\DomPDF\ServiceProvider as DomPDFServiceProvider;
use Maatwebsite\Excel\ExcelServiceProvider;
use PragmaRX\Google2FALaravel\ServiceProvider as Google2FAServiceProvider;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    
    // ✅ All Service Providers - Combined
    ->withCommands([
        \App\Console\Commands\TrendoraDoctor::class,
        \App\Console\Commands\TrendoraSmokeTest::class,
        \App\Console\Commands\CreateAdmin::class,
    ])

    ->withProviders(array_values(array_filter([
        DomPDFServiceProvider::class,
        ExcelServiceProvider::class,
        Google2FAServiceProvider::class,
        class_exists(\ZipArchive::class) ? \Spatie\Backup\BackupServiceProvider::class : null,
    ])))
    
    // ✅ All Middleware - Combined
    ->withMiddleware(function (Middleware $middleware) {
        
        // 🔥 CSRF Exclude - Payment Callback
        $middleware->validateCsrfTokens(except: [
            'payment/callback',
            'webhook/*',
        ]);
        
        // 🔥 Middleware Aliases
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'admin.permission' => AdminPermissionMiddleware::class,
            'trendora.api' => ApiKeyMiddleware::class,
            '2fa' => TwoFactorMiddleware::class,
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        ]);
        
        // 🔥 Global Middleware (Append - Runs on every request)
        $middleware->append(MaintenanceMode::class);
        $middleware->append(LogAdminActivity::class);
        $middleware->append(SecurityHeaders::class);
    })
    
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();