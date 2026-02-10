<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureTuteurIsAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        $hasTuteur = session()->has('tuteur');
        $path = $request->path();
        $sessionId = session()->getId();

        if (!$hasTuteur) {
            Log::channel('single')->info('Tuteur auth failed (middleware)', [
                'path' => $path,
                'host' => $request->getHost(),
                'full_url' => $request->fullUrl(),
                'referer' => $request->headers->get('referer'),
                'session_id' => $sessionId,
                'session_has_tuteur' => false,
                'session_keys' => array_keys(session()->all()),
                'cookie_present' => $request->hasCookie(config('session.cookie')),
                'session_cookie_name' => config('session.cookie'),
            ]);
            return redirect()->route('login.form')->with('error', 'يرجى تسجيل الدخول أولاً');
        }

        Log::channel('single')->debug('Tuteur auth OK', ['path' => $path, 'session_id' => $sessionId]);
        return $next($request);
    }
}
