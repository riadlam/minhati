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
        $accessToken = PersonalAccessToken::findToken($token);
        
        if (!$accessToken) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token.',
                'error' => 'Authentication required'
            ], 401);
        }

        // Get the tokenable (user/tuteur) model
        $tuteur = $accessToken->tokenable;
        
        if (!$tuteur || !($tuteur instanceof Tuteur)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid token.',
                'error' => 'Authentication required'
            ], 401);
        }

        // Set the authenticated user for the request
        $request->setUserResolver(function () use ($tuteur) {
            return $tuteur;
        });

        return $next($request);
    }
}

