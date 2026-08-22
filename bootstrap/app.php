<?php

use App\Http\Middleware\EnsureCanWrite;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'can-write' => EnsureCanWrite::class,
        ]);

        // No server-rendered login page exists (login lives in the SPA), so guests
        // must never be redirected to a "login" named route — Laravel registers that
        // as a default and it would crash with RouteNotFoundException since one
        // doesn't exist here.
        $middleware->redirectGuestsTo(fn () => null);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // This app has no server-rendered login page, so unauthenticated api/*
        // requests must never fall back to Laravel's default redirect-to-"login"-route
        // behavior (which would crash with a RouteNotFoundException).
        $exceptions->shouldRenderJsonWhen(function ($request, $throwable) {
            return $request->is('api/*') || $request->expectsJson();
        });
    })->create();
