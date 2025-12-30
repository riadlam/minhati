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
     * Token-only authentication (no session fallback)
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get token from Authorization header
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Token required.',
                'error' => 'Authentication required'
            ], 401);
        }

        // Find the token in database
        $accessToken = PersonalAccessToken::findToken($token);
        
        if (!$accessToken) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token.',
                'error' => 'Authentication required'
            ], 401);
        }

        // Get the tokenable (user) model
        $user = $accessToken->tokenable;
        
        if (!$user || !($user instanceof User)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid token.',
                'error' => 'Authentication required'
            ], 401);
        }

        // Set the authenticated user for the request
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }
}

