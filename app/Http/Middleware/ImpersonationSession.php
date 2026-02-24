<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * Impersonation window is identified by a flag cookie (set only in apply response).
 * When both flag and impersonation_session cookie exist, we use the impersonation session.
 * Admin tab never gets the flag cookie, so it always uses the main session.
 */
class ImpersonationSession
{
    public const IMPERSONATE_COOKIE = 'laravel_impersonate_session';
    public const IMPERSONATION_FLAG_COOKIE = 'impersonation_window';

    public function handle(Request $request, Closure $next): Response
    {
        $mainCookie = config('session.cookie');
        $impersonateId = $request->cookies->get(self::IMPERSONATE_COOKIE);
        $isImpersonationWindow = (bool) $request->cookies->get(self::IMPERSONATION_FLAG_COOKIE);

        // Use impersonation session when this request is from the impersonation window (has flag + session id).
        // Admin tab never has the flag cookie, so it always keeps using the main session.
        if ($impersonateId && $isImpersonationWindow) {
            $request->cookies->set($mainCookie, $impersonateId);
            $request->attributes->set('_used_impersonation_session', true);
        }

        $response = $next($request);

        $usedImpersonation = $request->attributes->get('_used_impersonation_session');
        $isImpersonationApply = $request->attributes->get('impersonation_apply_response');
        if (($usedImpersonation || $isImpersonationApply) && $response instanceof Response) {
            // Do not send main session cookie so admin tab is never overwritten and impersonation window keeps working.
            $cookies = $response->headers->getCookies();
            foreach ($cookies as $cookie) {
                if ($cookie->getName() === $mainCookie) {
                    $response->headers->removeCookie($cookie->getName(), $cookie->getPath(), $cookie->getDomain());
                }
            }
            if ($usedImpersonation) {
                $sessionId = $request->session()->getId();
                $lifetime = (int) config('session.lifetime', 120) * 60;
                $response->headers->setCookie(new Cookie(
                    self::IMPERSONATE_COOKIE,
                    $sessionId,
                    time() + $lifetime,
                    '/',
                    null,
                    $request->secure(),
                    true,
                    false,
                    'lax'
                ));
            }
        }

        return $response;
    }
}
