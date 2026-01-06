<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tuteur;
use App\Models\Father;
use App\Models\Mother;

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
            return redirect()->route('dashboard')->with('error', 'تعذر العثور على معلوماتك.');
        }

        return view('tuteur_profile', compact('tuteur'));
    }

    public function showFather(Request $request)
    {
        $tuteurData = session('tuteur');
        if (!$tuteurData) {
            return redirect()->route('login')->with('error', 'يرجى تسجيل الدخول أولاً');
        }

        $tuteur = Tuteur::where('nin', $tuteurData['nin'])->first();
        if (!$tuteur) {
            return redirect()->route('dashboard')->with('error', 'تعذر العثور على معلوماتك.');
        }

        // Get father info
        $father = null;
        if ($tuteur->father_id) {
            $father = Father::find($tuteur->father_id);
        }

        return view('tuteur_father_info', compact('tuteur', 'father'));
    }

    public function showMother(Request $request)
    {
        $tuteurData = session('tuteur');
        if (!$tuteurData) {
            return redirect()->route('login')->with('error', 'يرجى تسجيل الدخول أولاً');
        }

        $tuteur = Tuteur::where('nin', $tuteurData['nin'])->first();
        if (!$tuteur) {
            return redirect()->route('dashboard')->with('error', 'تعذر العثور على معلوماتك.');
        }

        // Get mothers list (for role 1) or single mother (for role 3)
        $mothers = [];
        $mother = null;
        
        if ($tuteur->relation_tuteur == 1) {
            // Role 1 (Father): Get all mothers (wives)
            $mothers = Mother::where('tuteur_nin', $tuteur->nin)->get();
        } elseif ($tuteur->relation_tuteur == 3) {
            // Role 3 (Guardian): Get single mother if exists
            if ($tuteur->mother_id) {
                $mother = Mother::find($tuteur->mother_id);
            }
        }

        return view('tuteur_mother_info', compact('tuteur', 'mothers', 'mother'));
    }
}