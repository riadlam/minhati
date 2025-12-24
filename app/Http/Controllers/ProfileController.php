<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tuteur;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        // 🧭 Récupération du tuteur connecté
        $tuteurData = session('tuteur'); // selon votre gestion de session
        if (!$tuteurData) {
            return redirect()->route('login')->with('error', 'يرجى تسجيل الدخول أولاً');
        }

        // 🔍 Récupération complète depuis la base
        $tuteur = Tuteur::where('nin', $tuteurData['nin'])->first();

        if (!$tuteur) {
            return redirect()->route('tuteur.dashboard')->with('error', 'تعذر العثور على معلوماتك.');
        }

        return view('tuteur_profile', compact('tuteur'));
    }
}