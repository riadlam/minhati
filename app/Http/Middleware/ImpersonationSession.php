<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * When the request has the impersonation session cookie and no (or empty) main session cookie,
 * use the impersonation session so the admin's session is never overwritten.
 * Only one "logged in as" window is active; the impersonation cookie is set only in that window's response.
 */
class ImpersonationSession
{
    public const IMPERSONATE_COOKIE = 'laravel_impersonate_session';

    public function handle(Request $request, Closure $next): Response
    {
        $mainCookie = config('session.cookie');
        $mainId = $request->cookies->get($mainCookie);
        $impersonateId = $request->cookies->get(self::IMPERSONATE_COOKIE);

        // Use impersonation session only when it's the only session cookie (impersonation window).
        // When both exist, keep using main session (admin tab).
        if ($impersonateId && (empty($mainId) || $mainId === '')) {
            $request->cookies->set($mainCookie, $impersonateId);
            $request->attributes->set('_used_impersonation_session', true);
        }

        $response = $next($request);

        // When we used impersonation session, don't send main session cookie (so admin tab is unchanged).
        // Send impersonation cookie instead so the impersonation window keeps working.
        $usedImpersonation = $request->attributes->get('_used_impersonation_session');
        $isImpersonationApply = $request->attributes->get('impersonation_apply_response');
        if (($usedImpersonation || $isImpersonationApply) && $response instanceof Response) {
            $cookies = $response->headers->getCookies();
            foreach ($cookies as $cookie) {
                if ($cookie->getName() === $mainCookie) {
                    $response->headers->removeCookie($cookie->getName(), $cookie->getPath(), $cookie->getDomain());
                }
            }
            if ($usedImpersonation) {
                // Refresh impersonation cookie with current session id.
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
