<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.tuteur' => \App\Http\Middleware\EnsureTuteurIsAuthenticated::class,
            'user.auth' => \App\Http\Middleware\UserAuth::class,
            'api.tuteur' => \App\Http\Middleware\ApiTuteurAuth::class,
            'api.user' => \App\Http\Middleware\ApiUserAuth::class,
        ]);
        
        // Replace default CSRF middleware with our custom one that excludes API login routes
        $middleware->validateCsrfTokens(except: [
            'api/auth/tuteur/login',
            'api/auth/user/login',
            'tuteur/session-restore',
        ]);
        
        // Enable cookies + sessions on API routes (needed for web UI on same host)
        // and add response time middleware.
        $middleware->api(
            prepend: [
                \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
                \Illuminate\Session\Middleware\StartSession::class,
            ],
            append: [
                \App\Http\Middleware\ApiResponseTime::class,
            ],
        );
    })

    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
