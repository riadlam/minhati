<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTuteurIsAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if a tuteur session exists
        if (!session()->has('tuteur')) {
            // Redirect to login if not authenticated
            return redirect()->route('login.form')->with('error', 'يرجى تسجيل الدخول أولاً');
        }

        return $next($request);
    }
}
