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
            'block.writes.impersonate' => \App\Http\Middleware\BlockWritesWhenImpersonating::class,
            'api.tuteur' => \App\Http\Middleware\ApiTuteurAuth::class,
            'api.user' => \App\Http\Middleware\ApiUserAuth::class,
        ]);
        
        // So impersonation window uses its own session; admin session is never overwritten.
        $middleware->prependToGroup('web', \App\Http\Middleware\ImpersonationSession::class);
        $middleware->encryptCookies(except: [
            \App\Http\Middleware\ImpersonationSession::IMPERSONATE_COOKIE,
        ]);

        // Replace default CSRF middleware with our custom one that excludes API login routes
        $middleware->validateCsrfTokens(except: [
            'api/auth/tuteur/login',
            'api/auth/user/login',
        ]);
        
        // API: stateful when same-origin (so /api/user/* can use session); token for cross-origin.
        $middleware->api(
            prepend: [
                \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            ],
            append: [
                \App\Http\Middleware\ApiResponseTime::class,
            ],
        );
    })

    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
