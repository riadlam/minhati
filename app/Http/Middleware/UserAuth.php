<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('user_logged')) {
            return redirect()->route('user.login');
        }

        return $next($request);
    }
}
