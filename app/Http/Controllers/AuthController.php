<?php

namespace App\Http\Controllers;

use App\Models\Tuteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
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
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('login.form')->with('success', 'تم تسجيل الخروج بنجاح');
    }
}
