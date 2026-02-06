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
        
        // If no valid token and it's a file route, allow request to proceed
        // The serveFile method will handle session authentication check
        if ($isFileRoute) {
            return $next($request);
        }
        
        // No valid token and not a file route
        Log::warning('ApiUserAuth: unauthorized API request blocked', [
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

