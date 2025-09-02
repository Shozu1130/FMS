<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
         $middleware->alias([
            'admin' => App\Http\Middleware\EnsureIsAdmin::class, //
            'professor' => App\Http\Middleware\EnsureIsProfessor::class, //
            'attendance_user' => App\Http\Middleware\EnsureAttendanceUser::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

    
