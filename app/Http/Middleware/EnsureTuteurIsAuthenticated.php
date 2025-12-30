<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class EnsureTuteurIsAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('=== AUTH.TUTEUR MIDDLEWARE CHECK ===', [
            'route' => $request->route()->getName(),
            'url' => $request->fullUrl(),
            'session_id' => session()->getId(),
            'has_tuteur_session' => session()->has('tuteur'),
            'tuteur_nin_in_session' => session('tuteur.nin'),
            'all_session_keys' => array_keys(session()->all()),
            'has_authorization_header' => $request->hasHeader('Authorization'),
            'authorization_header_preview' => $request->hasHeader('Authorization') ? substr($request->header('Authorization'), 0, 30) . '...' : null,
        ]);

        // Check if a tuteur session exists
        if (!session()->has('tuteur')) {
            Log::warning('Middleware: No tuteur session found - redirecting to login', [
                'session_id' => session()->getId(),
                'session_data' => session()->all(),
            ]);
            // Redirect to login if not authenticated
            return redirect()->route('login.form')->with('error', 'يرجى تسجيل الدخول أولاً');
        }

        Log::info('Middleware: Tuteur session found - allowing access', [
            'tuteur_nin' => session('tuteur.nin'),
        ]);

        return $next($request);
    }
}
