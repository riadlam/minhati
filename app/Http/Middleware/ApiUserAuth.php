<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

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
            $accessToken = PersonalAccessToken::findToken($token);
            
            if ($accessToken) {
                $user = $accessToken->tokenable;
                
                if ($user && ($user instanceof User)) {
                    // Set the authenticated user for the request
                    $request->setUserResolver(function () use ($user) {
                        return $user;
                    });
                    return $next($request);
                }
            }
        }
        
        // If no valid token and it's a file route, allow request to proceed
        // The serveFile method will handle session authentication check
        if ($isFileRoute) {
            return $next($request);
        }
        
        // No valid token and not a file route
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized. Token required.',
            'error' => 'Authentication required'
        ], 401);
    }
}

