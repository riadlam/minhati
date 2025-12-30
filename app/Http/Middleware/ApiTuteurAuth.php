<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tuteur;
use Laravel\Sanctum\PersonalAccessToken;

class ApiTuteurAuth
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
        // PersonalAccessToken::findToken() expects the full token string (with ID prefix)
        $accessToken = PersonalAccessToken::findToken($token);
        
        if (!$accessToken) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token.',
                'error' => 'Authentication required'
            ], 401);
        }

        // Check if token is expired
        if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Token has expired.',
                'error' => 'Authentication required'
            ], 401);
        }

        // Get the tokenable (user/tuteur) model
        $tuteur = $accessToken->tokenable;
        
        if (!$tuteur) {
            return response()->json([
                'success' => false,
                'message' => 'Token user not found.',
                'error' => 'Authentication required'
            ], 401);
        }

        if (!($tuteur instanceof Tuteur)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token type.',
                'error' => 'Authentication required'
            ], 401);
        }

        // Set the authenticated user for the request
        // This makes $request->user() work in controllers
        $request->setUserResolver(function () use ($tuteur) {
            return $tuteur;
        });

        // Also set it using auth() helper for compatibility
        auth()->setUser($tuteur);

        return $next($request);
    }
}

