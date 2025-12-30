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
                // Tuteur uses 'nin' as primary key (string), so use where() instead of find()
                $tuteur = Tuteur::where('nin', $accessToken->tokenable_id)->first();
                
                // If not found and tokenable_id is less than 18 digits, try padding with leading zero
                if (!$tuteur && strlen($accessToken->tokenable_id) < 18) {
                    $paddedNin = str_pad($accessToken->tokenable_id, 18, '0', STR_PAD_LEFT);
                    \Log::info('ApiTuteurAuth: Trying padded NIN', [
                        'original' => $accessToken->tokenable_id,
                        'padded' => $paddedNin,
                    ]);
                    $tuteur = Tuteur::where('nin', $paddedNin)->first();
                }
                
                // If still not found, try without leading zero (in case DB stores without leading zero)
                if (!$tuteur && strlen($accessToken->tokenable_id) === 18 && substr($accessToken->tokenable_id, 0, 1) === '0') {
                    $unpaddedNin = ltrim($accessToken->tokenable_id, '0');
                    \Log::info('ApiTuteurAuth: Trying unpadded NIN', [
                        'original' => $accessToken->tokenable_id,
                        'unpadded' => $unpaddedNin,
                    ]);
                    $tuteur = Tuteur::where('nin', $unpaddedNin)->first();
                }
                
                if (!$tuteur) {
                    \Log::error('ApiTuteurAuth: Tuteur not found with NIN', [
                        'tokenable_id' => $accessToken->tokenable_id,
                        'nin_length' => strlen($accessToken->tokenable_id),
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Token user not found.',
                        'error' => 'Authentication required'
                    ], 401);
                }
                
                \Log::info('ApiTuteurAuth: Tuteur loaded manually', [
                    'tuteur_nin' => $tuteur->nin,
                    'tokenable_id' => $accessToken->tokenable_id,
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

