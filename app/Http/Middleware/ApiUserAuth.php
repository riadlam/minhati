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
        // Get token from Authorization header
        $token = $request->bearerToken();

        // For file serving routes, allow session fallback
        $isFileRoute = $request->is('api/user/files/*');
        
        if ($token) {
            // Token authentication
            Log::info('ApiUserAuth: bearer token received', [
                'path' => $request->path(),
                'ip' => $request->ip(),
                'token_prefix' => substr($token, 0, 12),
            ]);

            $accessToken = PersonalAccessToken::findToken($token);
            
            if ($accessToken) {
                $user = $accessToken->tokenable;
                
                if ($user && ($user instanceof User)) {
                    // Set the authenticated user for the request
                    $request->setUserResolver(function () use ($user) {
                        return $user;
                    });
                    Log::info('ApiUserAuth: token authenticated', [
                        'path' => $request->path(),
                        'user_id' => $user->code_user,
                        'role' => $user->role,
                    ]);
                    return $next($request);
                }
            }

            Log::warning('ApiUserAuth: invalid or non-user token', [
                'path' => $request->path(),
                'ip' => $request->ip(),
            ]);
        }
        else {
            Log::warning('ApiUserAuth: missing bearer token', [
                'path' => $request->path(),
                'ip' => $request->ip(),
            ]);
        }

        /**
         * 🔁 SESSION FALLBACK (for web-originated API calls)
         *
         * Many of your API calls (DAS / comité actions) are made from Blade pages
         * in the same web session. If the Sanctum token is invalid for any reason,
         * we can safely fall back to the logged-in web user in session.
         */
        $sessionUserCode = session('user_code');
        if ($sessionUserCode) {
            $sessionUser = User::where('code_user', $sessionUserCode)->first();
            if ($sessionUser) {
                Log::info('ApiUserAuth: authenticated via SESSION fallback', [
                    'path' => $request->path(),
                    'ip' => $request->ip(),
                    'user_id' => $sessionUser->code_user,
                    'role' => $sessionUser->role,
                ]);
                $request->setUserResolver(function () use ($sessionUser) {
                    return $sessionUser;
                });
                return $next($request);
            }

            Log::warning('ApiUserAuth: session user_code not found in DB', [
                'path' => $request->path(),
                'ip' => $request->ip(),
                'user_code' => $sessionUserCode,
            ]);
        }

        // If no valid token and no valid session user (and not a file route), block
        if ($isFileRoute) {
            return $next($request);
        }

        Log::warning('ApiUserAuth: unauthorized API request blocked (no valid token or session)', [
            'path' => $request->path(),
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized. Token required.',
            'error' => 'Authentication required'
        ], 401);
    }
}

