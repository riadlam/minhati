<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Tuteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureTuteurIsAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        $hasTuteur = session()->has('tuteur');
        $path = $request->path();
        $sessionId = session()->getId();

        if (!$hasTuteur) {
            // Fallback: rehydrate tuteur session from signed query params.
            // This protects navigation when some clients/proxies drop session cookie intermittently.
            $nin = (string) $request->query('nin', '');
            $ts = (int) $request->query('ts', 0);
            $sig = (string) $request->query('sig', '');
            $maxSkewSeconds = 900; // 15 minutes
            $isFresh = $ts > 0 && abs(time() - $ts) <= $maxSkewSeconds;
            $expectedSig = hash_hmac('sha256', $nin.'|'.$ts, (string) config('app.key'));

            if ($nin !== '' && $sig !== '' && $isFresh && hash_equals($expectedSig, $sig)) {
                $tuteur = Tuteur::where('nin', $nin)->first();
                if ($tuteur) {
                    session([
                        'tuteur' => [
                            'nin' => $tuteur->nin,
                            'nss' => $tuteur->nss,
                            'sexe' => $tuteur->sexe,
                            'nom_ar' => $tuteur->nom_ar,
                            'prenom_ar' => $tuteur->prenom_ar,
                            'nom_fr' => $tuteur->nom_fr,
                            'prenom_fr' => $tuteur->prenom_fr,
                            'tel' => $tuteur->tel,
                            'email' => $tuteur->email,
                            'adresse' => $tuteur->adresse,
                            'nbr_enfants_scolarise' => $tuteur->nbr_enfants_scolarise,
                            'code_commune' => $tuteur->code_commune,
                        ],
                    ]);
                    session()->save();

                    Log::channel('single')->info('Tuteur auth recovered via signed params', [
                        'path' => $path,
                        'nin' => $nin,
                        'session_id' => session()->getId(),
                    ]);

                    return $next($request);
                }
            }

            Log::channel('single')->info('Tuteur auth failed (middleware)', [
                'path' => $path,
                'host' => $request->getHost(),
                'full_url' => $request->fullUrl(),
                'referer' => $request->headers->get('referer'),
                'session_id' => $sessionId,
                'session_has_tuteur' => false,
                'session_keys' => array_keys(session()->all()),
                'cookie_present' => $request->hasCookie(config('session.cookie')),
                'session_cookie_name' => config('session.cookie'),
            ]);
            return redirect()->route('login.form')->with('error', 'يرجى تسجيل الدخول أولاً');
        }

        Log::channel('single')->debug('Tuteur auth OK', ['path' => $path, 'session_id' => $sessionId]);
        return $next($request);
    }
}
