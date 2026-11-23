<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'CheckRole' => \App\Http\Middleware\CheckRole::class,
            'CheckPermission' => \App\Http\Middleware\CheckPermission::class,
            'NoCache' => \App\Http\Middleware\NoCache::class,
            'force.password.change' => \App\Http\Middleware\ForcePasswordChange::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\ForcePasswordChange::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('contracts:check-alerts')->daily();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
