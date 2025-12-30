<?php

namespace App\Http\Controllers;

use App\Models\Tuteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        Log::info('=== WEB LOGIN ATTEMPT ===', [
            'nin' => $request->input('nin'),
            'has_password' => !empty($request->input('password')),
            'password_length' => strlen($request->input('password') ?? ''),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        try {
            // ✅ Validate input
            $validated = $request->validate([
                'nin' => 'required|string|size:18',
                'password' => 'required|string|min:8',
            ]);

            Log::info('Validation passed', ['nin' => $validated['nin']]);

            // ✅ Find the Tuteur by NIN
            $tuteur = Tuteur::where('nin', $validated['nin'])->first();

            if (!$tuteur) {
                Log::warning('Tuteur not found', ['nin' => $validated['nin']]);
                return back()
                    ->withErrors(['nin' => 'رقم التعريف الوطني غير موجود'])
                    ->withInput();
            }

            Log::info('Tuteur found', [
                'nin' => $tuteur->nin,
                'has_password' => !empty($tuteur->password),
                'password_hash_length' => strlen($tuteur->password ?? ''),
            ]);

            // ✅ Check password
            $passwordCheck = Hash::check($validated['password'], $tuteur->password);
            
            Log::info('Password check result', [
                'password_match' => $passwordCheck,
                'provided_password' => substr($validated['password'], 0, 2) . '***',
            ]);

            if (!$passwordCheck) {
                Log::warning('Password mismatch', ['nin' => $validated['nin']]);
                return back()
                    ->withErrors(['password' => 'كلمة المرور غير صحيحة'])
                    ->withInput();
            }

            // ✅ If success — store session data
            $sessionData = [
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
                ]
            ];

            session($sessionData);

            Log::info('Session stored', [
                'session_id' => session()->getId(),
                'session_data_keys' => array_keys(session()->all()),
                'tuteur_nin_in_session' => session('tuteur.nin'),
            ]);

            // ✅ Redirect to dashboard or home
            Log::info('Redirecting to dashboard', ['route' => 'dashboard']);
            return redirect()->route('dashboard')->with('success', 'تم تسجيل الدخول بنجاح');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', [
                'errors' => $e->errors(),
                'nin' => $request->input('nin'),
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Login exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'nin' => $request->input('nin'),
            ]);
            return back()
                ->withErrors(['general' => 'حدث خطأ أثناء تسجيل الدخول'])
                ->withInput();
        }
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('login.form')->with('success', 'تم تسجيل الخروج بنجاح');
    }

    /**
     * API Login for Tuteur - returns JSON response
     */
    public function apiLogin(Request $request)
    {
        Log::info('=== API LOGIN ATTEMPT ===', [
            'nin' => $request->input('nin'),
            'has_password' => !empty($request->input('password')),
            'password_length' => strlen($request->input('password') ?? ''),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        try {
            $validated = $request->validate([
                'nin' => 'required|string|size:18',
                'password' => 'required|string|min:8',
            ]);

            Log::info('API Validation passed', ['nin' => $validated['nin']]);

            $tuteur = Tuteur::where('nin', $validated['nin'])->first();

            if (!$tuteur) {
                Log::warning('API: Tuteur not found', ['nin' => $validated['nin']]);
                return response()->json([
                    'success' => false,
                    'message' => 'رقم التعريف الوطني غير موجود',
                    'errors' => ['nin' => ['رقم التعريف الوطني غير موجود']]
                ], 401);
            }

            Log::info('API: Tuteur found', [
                'nin' => $tuteur->nin,
                'has_password' => !empty($tuteur->password),
            ]);

            $passwordCheck = Hash::check($validated['password'], $tuteur->password);
            
            Log::info('API: Password check result', [
                'password_match' => $passwordCheck,
            ]);

            if (!$passwordCheck) {
                Log::warning('API: Password mismatch', ['nin' => $validated['nin']]);
                return response()->json([
                    'success' => false,
                    'message' => 'كلمة المرور غير صحيحة',
                    'errors' => ['password' => ['كلمة المرور غير صحيحة']]
                ], 401);
            }

            // Revoke all existing tokens for this tuteur
            $tuteur->tokens()->delete();

            // Create new token (token-only, no session)
            $token = $tuteur->createToken('tuteur-api-token', ['*'], now()->addDays(30))->plainTextToken;

            // Also create session for web routes compatibility
            $sessionData = [
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
                ]
            ];

            session($sessionData);
            
            // Force save the session to ensure it persists
            session()->save();

            Log::info('API: Token and session created successfully', [
                'nin' => $tuteur->nin,
                'token_preview' => substr($token, 0, 20) . '...',
                'session_id' => session()->getId(),
                'session_has_tuteur' => session()->has('tuteur'),
                'session_name' => config('session.cookie'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل الدخول بنجاح',
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => 2592000, // 30 days in seconds
                'data' => [
                    'nin' => $tuteur->nin,
                    'nom_ar' => $tuteur->nom_ar,
                    'prenom_ar' => $tuteur->prenom_ar,
                    'nom_fr' => $tuteur->nom_fr,
                    'prenom_fr' => $tuteur->prenom_fr,
                    'email' => $tuteur->email,
                ]
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('API: Validation failed', [
                'errors' => $e->errors(),
                'nin' => $request->input('nin'),
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('API: Login exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'nin' => $request->input('nin'),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تسجيل الدخول',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API Logout for Tuteur - returns JSON response
     */
    public function apiLogout(Request $request)
    {
        // Revoke current token (token-only)
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الخروج بنجاح'
        ], 200);
    }
}
