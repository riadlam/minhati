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
        
        \Log::info('ApiTuteurAuth: Token check', [
            'has_token' => !empty($token),
            'token_preview' => $token ? substr($token, 0, 20) . '...' : null,
            'url' => $request->url(),
            'method' => $request->method(),
        ]);
        
        if (!$token) {
            \Log::warning('ApiTuteurAuth: No token provided');
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Token required.',
                'error' => 'Authentication required'
            ], 401);
        }

        // Find the token in database
        // PersonalAccessToken::findToken() expects the full token string (with ID prefix)
        $accessToken = PersonalAccessToken::findToken($token);
        
        \Log::info('ApiTuteurAuth: Token lookup', [
            'token_found' => !is_null($accessToken),
            'token_id' => $accessToken?->id,
        ]);
        
        if (!$accessToken) {
            \Log::warning('ApiTuteurAuth: Token not found in database');
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token.',
                'error' => 'Authentication required'
            ], 401);
        }

        // Check if token is expired
        if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
            \Log::warning('ApiTuteurAuth: Token expired', [
                'expires_at' => $accessToken->expires_at,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Token has expired.',
                'error' => 'Authentication required'
            ], 401);
        }

        // Get the tokenable (user/tuteur) model
        // Log token details for debugging
        \Log::info('ApiTuteurAuth: Token details', [
            'token_id' => $accessToken->id,
            'tokenable_type' => $accessToken->tokenable_type,
            'tokenable_id' => $accessToken->tokenable_id,
        ]);
        
        $tuteur = $accessToken->tokenable;
        
        \Log::info('ApiTuteurAuth: Tokenable check', [
            'has_tokenable' => !is_null($tuteur),
            'tokenable_type' => $tuteur ? get_class($tuteur) : null,
            'is_tuteur' => $tuteur instanceof Tuteur,
        ]);
        
        // If tokenable is null, try to manually load the tuteur
        if (!$tuteur) {
            \Log::warning('ApiTuteurAuth: Tokenable relationship returned null, trying manual load', [
                'tokenable_type' => $accessToken->tokenable_type,
                'tokenable_id' => $accessToken->tokenable_id,
            ]);
            
            // Check if tokenable_type is correct
            if ($accessToken->tokenable_type === Tuteur::class || $accessToken->tokenable_type === 'App\\Models\\Tuteur') {
                // Try to manually load the tuteur
                $tuteur = Tuteur::find($accessToken->tokenable_id);
                
                if (!$tuteur) {
                    \Log::error('ApiTuteurAuth: Tuteur not found with ID', [
                        'tokenable_id' => $accessToken->tokenable_id,
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Token user not found.',
                        'error' => 'Authentication required'
                    ], 401);
                }
                
                \Log::info('ApiTuteurAuth: Tuteur loaded manually', [
                    'tuteur_nin' => $tuteur->nin,
                ]);
            } else {
                \Log::error('ApiTuteurAuth: Invalid tokenable_type', [
                    'expected' => Tuteur::class,
                    'actual' => $accessToken->tokenable_type,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token type.',
                    'error' => 'Authentication required'
                ], 401);
            }
        }

        if (!($tuteur instanceof Tuteur)) {
            \Log::warning('ApiTuteurAuth: Tokenable is not Tuteur', [
                'tokenable_type' => get_class($tuteur),
            ]);
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

        \Log::info('ApiTuteurAuth: Authentication successful', [
            'tuteur_nin' => $tuteur->nin,
        ]);

        return $next($request);
    }
}

