<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use App\Http\Middleware\AdminMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust all proxies so Railway's load balancer forwards X-Forwarded-Proto: https
        // correctly. Without this, Laravel sees every request as http:// and generates
        // HTTP URLs for assets, routes, and form actions (Mixed Content errors).
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'admin' => AdminMiddleware::class,
        ]);

        $middleware->redirectTo(
            guests: '/login',
            users: '/'
        );
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('disaster:fetch-data')->everyFiveMinutes();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
