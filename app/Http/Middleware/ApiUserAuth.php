<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Log;

class ApiUserAuth
{
    /**
     * Handle an incoming request.
     * Token authentication with session fallback for file serving
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isFileRoute = $request->is('api/user/files/*');

        // 1) SESSION FIRST: same-host web UI uses session from /api/auth/user/login
        $sessionUserCode = session('user_code');
        if ($sessionUserCode) {
            $sessionUser = User::where('code_user', $sessionUserCode)->first();
            if ($sessionUser) {
                Log::info('ApiUserAuth: authenticated via SESSION', [
                    'path' => $request->path(),
                    'user_id' => $sessionUser->code_user,
                    'role' => $sessionUser->role,
                ]);
                $request->setUserResolver(fn () => $sessionUser);
                return $next($request);
            }
        }

        // 2) BEARER TOKEN (Sanctum) for pure API / other hosts
        $token = $request->bearerToken();
        if ($token) {
            $accessToken = PersonalAccessToken::findToken($token);
            if ($accessToken && $accessToken->tokenable instanceof User) {
                $user = $accessToken->tokenable;
                Log::info('ApiUserAuth: authenticated via Bearer token', [
                    'path' => $request->path(),
                    'user_id' => $user->code_user,
                    'role' => $user->role,
                ]);
                $request->setUserResolver(fn () => $user);
                return $next($request);
            }
            Log::warning('ApiUserAuth: Bearer token invalid or expired', [
                'path' => $request->path(),
                'ip' => $request->ip(),
            ]);
        }

        if ($isFileRoute) {
            return $next($request);
        }

        Log::warning('ApiUserAuth: unauthorized', [
            'path' => $request->path(),
            'ip' => $request->ip(),
            'has_session_code' => !empty($sessionUserCode),
            'has_bearer' => !empty($token),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized. Token required.',
            'error' => 'Authentication required'
        ], 401);
    }
}

