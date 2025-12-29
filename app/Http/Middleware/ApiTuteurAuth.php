<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tuteur;

class ApiTuteurAuth
{
    /**
     * Handle an incoming request.
     * Token-only authentication (no session fallback)
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if authenticated via Sanctum token only
        if ($request->user() && $request->user() instanceof Tuteur) {
            return $next($request);
        }

        // Not authenticated - token required
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized. Token required.',
            'error' => 'Authentication required'
        ], 401);
    }
}

