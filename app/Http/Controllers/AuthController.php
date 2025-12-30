<?php

namespace App\Http\Controllers;

use App\Models\Tuteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            // ✅ Validate input
            $validated = $request->validate([
                'nin' => 'required|string|size:18',
                'password' => 'required|string|min:8',
            ]);

            // ✅ Find the Tuteur by NIN
            $tuteur = Tuteur::where('nin', $validated['nin'])->first();

            if (!$tuteur) {
                return back()
                    ->withErrors(['nin' => 'رقم التعريف الوطني غير موجود'])
                    ->withInput();
            }

            // ✅ Check password
            if (!Hash::check($validated['password'], $tuteur->password)) {
                return back()
                    ->withErrors(['password' => 'كلمة المرور غير صحيحة'])
                    ->withInput();
            }

            // ✅ If success — store session data
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
                ]
            ]);

            // ✅ Redirect to dashboard or home
            return redirect()->route('dashboard')->with('success', 'تم تسجيل الدخول بنجاح');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
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
        try {
            $validated = $request->validate([
                'nin' => 'required|string|size:18',
                'password' => 'required|string|min:8',
            ]);

            $tuteur = Tuteur::where('nin', $validated['nin'])->first();

            if (!$tuteur) {
                return response()->json([
                    'success' => false,
                    'message' => 'رقم التعريف الوطني غير موجود',
                    'errors' => ['nin' => ['رقم التعريف الوطني غير موجود']]
                ], 401);
            }

            if (!Hash::check($validated['password'], $tuteur->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'كلمة المرور غير صحيحة',
                    'errors' => ['password' => ['كلمة المرور غير صحيحة']]
                ], 401);
            }

            // Revoke all existing tokens for this tuteur
            $tuteur->tokens()->delete();

            // Create new token
            $token = $tuteur->createToken('tuteur-api-token', ['*'], now()->addDays(30))->plainTextToken;

            // Also create session for web routes compatibility
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
                ]
            ]);
            
            // Force save the session to ensure it persists
            session()->save();

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
            throw $e;
        } catch (\Exception $e) {
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
