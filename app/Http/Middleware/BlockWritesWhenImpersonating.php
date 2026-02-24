<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BlockWritesWhenImpersonating
{
    /**
     * When admin is "logged in as" ts_commune (read-only), block all write methods.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!session('impersonate_read_only')) {
            return $next($request);
        }
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'العرض فقط — التعديل غير مسموح في وضع "تم الدخول باسم"',
                ], 403);
            }
            return back()->with('error', 'العرض فقط — التعديل غير مسموح في وضع "تم الدخول باسم"');
        }
        return $next($request);
    }
}
