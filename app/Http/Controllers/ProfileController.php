<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tuteur;
use App\Models\Father;
use App\Models\Mother;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    private function currentTuteurOrRedirect()
    {
        $tuteurData = session('tuteur');
        if (!$tuteurData || !isset($tuteurData['nin'])) {
            return null;
        }
        return Tuteur::where('nin', $tuteurData['nin'])->first();
    }

    private function normalizeMontant(array &$data): void
    {
        // If categorie_sociale is not the "low income" option, montant_s must be null
        $lowIncome = 'الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون';
        if (($data['categorie_sociale'] ?? null) !== $lowIncome) {
            $data['montant_s'] = null;
        }
    }

    public function show(Request $request)
    {
        $tuteur = $this->currentTuteurOrRedirect();
        if (!$tuteur) {
            return redirect()->route('login')->with('error', 'يرجى تسجيل الدخول أولاً');
        }

        return view('tuteur_profile', compact('tuteur'));
    }

    public function showFather(Request $request)
    {
        $tuteur = $this->currentTuteurOrRedirect();
        if (!$tuteur) {
            return redirect()->route('login')->with('error', 'يرجى تسجيل الدخول أولاً');
        }

        // Get all fathers for the logged-in tuteur (regardless of role)
        $fathers = Father::where('tuteur_nin', $tuteur->nin)->get();

        return view('tuteur_father_info', compact('tuteur', 'fathers'));
    }

    public function showMother(Request $request)
    {
        $tuteur = $this->currentTuteurOrRedirect();
        if (!$tuteur) {
            return redirect()->route('login')->with('error', 'يرجى تسجيل الدخول أولاً');
        }

        // Get all mothers for the logged-in tuteur (regardless of role)
        $mothers = Mother::where('tuteur_nin', $tuteur->nin)->get();

        return view('tuteur_mother_info', compact('tuteur', 'mothers'));
    }

    public function storeFather(Request $request)
    {
        $tuteur = $this->currentTuteurOrRedirect();
        if (!$tuteur) return redirect()->route('login')->with('error', 'يرجى تسجيل الدخول أولاً');

        $validated = $request->validate([
            'nin' => ['required', 'regex:/^\d{18}$/'],
            'nss' => ['nullable', 'regex:/^\d{12}$/'],
            'nom_ar' => ['required', 'string', 'max:50', 'regex:/^[\p{Arabic}\s\-]+$/u'],
            'prenom_ar' => ['required', 'string', 'max:50', 'regex:/^[\p{Arabic}\s\-]+$/u'],
            'nom_fr' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-Z\s\-]+$/'],
            'prenom_fr' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-Z\s\-]+$/'],
            'categorie_sociale' => ['nullable', 'string', 'max:191'],
            'montant_s' => ['nullable', 'numeric', 'min:0'],
        ], [
            'nom_ar.required' => 'اللقب بالعربية مطلوب',
            'nom_ar.regex' => 'اللقب بالعربية يجب أن يحتوي على أحرف عربية فقط',
            'prenom_ar.required' => 'الاسم بالعربية مطلوب',
            'prenom_ar.regex' => 'الاسم بالعربية يجب أن يحتوي على أحرف عربية فقط',
            'nom_fr.regex' => 'اللقب باللاتينية يجب أن يحتوي على أحرف لاتينية فقط',
            'prenom_fr.regex' => 'الاسم باللاتينية يجب أن يحتوي على أحرف لاتينية فقط',
        ]);

        // ✅ Global NIN uniqueness check
        if (Father::where('nin', $validated['nin'])->exists() || 
            Mother::where('nin', $validated['nin'])->exists() || 
            Tuteur::where('nin', $validated['nin'])->exists()) {
            return redirect()->back()->withInput()->withErrors(['nin' => 'الرقم الوطني موجود بالفعل']);
        }

        // ✅ Global NSS uniqueness check (optional field)
        if (!empty($validated['nss']) && trim($validated['nss']) !== '') {
            if (Father::where('nss', $validated['nss'])->exists() || 
                Mother::where('nss', $validated['nss'])->exists() || 
                Tuteur::where('nss', $validated['nss'])->exists()) {
                return redirect()->back()->withInput()->withErrors(['nss' => 'رقم الضمان الاجتماعي موجود بالفعل']);
            }
        }

        $this->normalizeMontant($validated);

        Father::create([
            ...$validated,
            'tuteur_nin' => $tuteur->nin,
            'date_insertion' => now(),
        ]);

        return redirect()->route('tuteur.father')->with('success', 'تمت إضافة الأب بنجاح.');
    }

    public function updateFather(Request $request, Father $father)
    {
        $tuteur = $this->currentTuteurOrRedirect();
        if (!$tuteur) return redirect()->route('login')->with('error', 'يرجى تسجيل الدخول أولاً');

        if ($father->tuteur_nin !== $tuteur->nin) {
            return redirect()->route('tuteur.father')->with('error', 'غير مصرح.');
        }

        $validated = $request->validate([
            'nin' => ['required', 'regex:/^\d{18}$/'],
            'nss' => ['nullable', 'regex:/^\d{12}$/'],
            'nom_ar' => ['required', 'string', 'max:50', 'regex:/^[\p{Arabic}\s\-]+$/u'],
            'prenom_ar' => ['required', 'string', 'max:50', 'regex:/^[\p{Arabic}\s\-]+$/u'],
            'nom_fr' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-Z\s\-]+$/'],
            'prenom_fr' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-Z\s\-]+$/'],
            'categorie_sociale' => ['nullable', 'string', 'max:191'],
            'montant_s' => ['nullable', 'numeric', 'min:0'],
        ], [
            'nom_ar.required' => 'اللقب بالعربية مطلوب',
            'nom_ar.regex' => 'اللقب بالعربية يجب أن يحتوي على أحرف عربية فقط',
            'prenom_ar.required' => 'الاسم بالعربية مطلوب',
            'prenom_ar.regex' => 'الاسم بالعربية يجب أن يحتوي على أحرف عربية فقط',
            'nom_fr.regex' => 'اللقب باللاتينية يجب أن يحتوي على أحرف لاتينية فقط',
            'prenom_fr.regex' => 'الاسم باللاتينية يجب أن يحتوي على أحرف لاتينية فقط',
        ]);

        // ✅ Global NIN uniqueness check (excluding current father)
        if (!empty(trim($validated['nin'])) && $validated['nin'] != $father->nin) {
            if (Father::where('nin', $validated['nin'])->where('id', '!=', $father->id)->exists() || 
                Mother::where('nin', $validated['nin'])->exists() || 
                Tuteur::where('nin', $validated['nin'])->exists()) {
                return redirect()->back()->withInput()->withErrors(['nin' => 'الرقم الوطني موجود بالفعل']);
            }
        }

        // ✅ Global NSS uniqueness check (optional field, excluding current father)
        if (!empty($validated['nss']) && trim($validated['nss']) !== '' && $validated['nss'] != $father->nss) {
            if (Father::where('nss', $validated['nss'])->where('id', '!=', $father->id)->exists() || 
                Mother::where('nss', $validated['nss'])->exists() || 
                Tuteur::where('nss', $validated['nss'])->exists()) {
                return redirect()->back()->withInput()->withErrors(['nss' => 'رقم الضمان الاجتماعي موجود بالفعل']);
            }
        }

        $this->normalizeMontant($validated);

        $father->update($validated);

        return redirect()->route('tuteur.father')->with('success', 'تم تحديث معلومات الأب بنجاح.');
    }

    public function destroyFather(Request $request, Father $father)
    {
        $tuteur = $this->currentTuteurOrRedirect();
        if (!$tuteur) return redirect()->route('login')->with('error', 'يرجى تسجيل الدخول أولاً');

        if ($father->tuteur_nin !== $tuteur->nin) {
            return redirect()->route('tuteur.father')->with('error', 'غير مصرح.');
        }
        if ($father->eleves()->exists()) {
            return redirect()->route('tuteur.father')->with('error', 'لا يمكن حذف الأب لأنه مرتبط بتلاميذ.');
        }

        $father->delete();
        return redirect()->route('tuteur.father')->with('success', 'تم حذف الأب بنجاح.');
    }

    public function storeMother(Request $request)
    {
        $tuteur = $this->currentTuteurOrRedirect();
        if (!$tuteur) return redirect()->route('login')->with('error', 'يرجى تسجيل الدخول أولاً');

        $validated = $request->validate([
            'nin' => ['required', 'regex:/^\d{18}$/'],
            'nss' => ['nullable', 'regex:/^\d{12}$/'],
            'nom_ar' => ['required', 'string', 'max:50', 'regex:/^[\p{Arabic}\s\-]+$/u'],
            'prenom_ar' => ['required', 'string', 'max:50', 'regex:/^[\p{Arabic}\s\-]+$/u'],
            'nom_fr' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-Z\s\-]+$/'],
            'prenom_fr' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-Z\s\-]+$/'],
            'categorie_sociale' => ['nullable', 'string', 'max:191'],
            'montant_s' => ['nullable', 'numeric', 'min:0'],
        ], [
            'nom_ar.required' => 'اللقب بالعربية مطلوب',
            'nom_ar.regex' => 'اللقب بالعربية يجب أن يحتوي على أحرف عربية فقط',
            'prenom_ar.required' => 'الاسم بالعربية مطلوب',
            'prenom_ar.regex' => 'الاسم بالعربية يجب أن يحتوي على أحرف عربية فقط',
            'nom_fr.regex' => 'اللقب باللاتينية يجب أن يحتوي على أحرف لاتينية فقط',
            'prenom_fr.regex' => 'الاسم باللاتينية يجب أن يحتوي على أحرف لاتينية فقط',
        ]);

        // ✅ Global NIN uniqueness check
        if (Mother::where('nin', $validated['nin'])->exists() || 
            Father::where('nin', $validated['nin'])->exists() || 
            Tuteur::where('nin', $validated['nin'])->exists()) {
            return redirect()->back()->withInput()->withErrors(['nin' => 'الرقم الوطني موجود بالفعل']);
        }

        // ✅ Global NSS uniqueness check (optional field)
        if (!empty($validated['nss']) && trim($validated['nss']) !== '') {
            if (Mother::where('nss', $validated['nss'])->exists() || 
                Father::where('nss', $validated['nss'])->exists() || 
                Tuteur::where('nss', $validated['nss'])->exists()) {
                return redirect()->back()->withInput()->withErrors(['nss' => 'رقم الضمان الاجتماعي موجود بالفعل']);
            }
        }

        $this->normalizeMontant($validated);

        Mother::create([
            ...$validated,
            'tuteur_nin' => $tuteur->nin,
            'date_insertion' => now(),
        ]);

        return redirect()->route('tuteur.mother')->with('success', 'تمت إضافة الأم بنجاح.');
    }

    public function updateMother(Request $request, Mother $mother)
    {
        $tuteur = $this->currentTuteurOrRedirect();
        if (!$tuteur) return redirect()->route('login')->with('error', 'يرجى تسجيل الدخول أولاً');

        if ($mother->tuteur_nin !== $tuteur->nin) {
            return redirect()->route('tuteur.mother')->with('error', 'غير مصرح.');
        }

        $validated = $request->validate([
            'nin' => ['required', 'regex:/^\d{18}$/'],
            'nss' => ['nullable', 'regex:/^\d{12}$/'],
            'nom_ar' => ['required', 'string', 'max:50', 'regex:/^[\p{Arabic}\s\-]+$/u'],
            'prenom_ar' => ['required', 'string', 'max:50', 'regex:/^[\p{Arabic}\s\-]+$/u'],
            'nom_fr' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-Z\s\-]+$/'],
            'prenom_fr' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-Z\s\-]+$/'],
            'categorie_sociale' => ['nullable', 'string', 'max:191'],
            'montant_s' => ['nullable', 'numeric', 'min:0'],
        ], [
            'nom_ar.required' => 'اللقب بالعربية مطلوب',
            'nom_ar.regex' => 'اللقب بالعربية يجب أن يحتوي على أحرف عربية فقط',
            'prenom_ar.required' => 'الاسم بالعربية مطلوب',
            'prenom_ar.regex' => 'الاسم بالعربية يجب أن يحتوي على أحرف عربية فقط',
            'nom_fr.regex' => 'اللقب باللاتينية يجب أن يحتوي على أحرف لاتينية فقط',
            'prenom_fr.regex' => 'الاسم باللاتينية يجب أن يحتوي على أحرف لاتينية فقط',
        ]);

        // ✅ Global NIN uniqueness check (excluding current mother)
        if (!empty(trim($validated['nin'])) && $validated['nin'] != $mother->nin) {
            if (Mother::where('nin', $validated['nin'])->where('id', '!=', $mother->id)->exists() || 
                Father::where('nin', $validated['nin'])->exists() || 
                Tuteur::where('nin', $validated['nin'])->exists()) {
                return redirect()->back()->withInput()->withErrors(['nin' => 'الرقم الوطني موجود بالفعل']);
            }
        }

        // ✅ Global NSS uniqueness check (optional field, excluding current mother)
        if (!empty($validated['nss']) && trim($validated['nss']) !== '' && $validated['nss'] != $mother->nss) {
            if (Mother::where('nss', $validated['nss'])->where('id', '!=', $mother->id)->exists() || 
                Father::where('nss', $validated['nss'])->exists() || 
                Tuteur::where('nss', $validated['nss'])->exists()) {
                return redirect()->back()->withInput()->withErrors(['nss' => 'رقم الضمان الاجتماعي موجود بالفعل']);
            }
        }

        $this->normalizeMontant($validated);

        $mother->update($validated);

        return redirect()->route('tuteur.mother')->with('success', 'تم تحديث معلومات الأم بنجاح.');
    }

    public function destroyMother(Request $request, Mother $mother)
    {
        $tuteur = $this->currentTuteurOrRedirect();
        if (!$tuteur) return redirect()->route('login')->with('error', 'يرجى تسجيل الدخول أولاً');

        if ($mother->tuteur_nin !== $tuteur->nin) {
            return redirect()->route('tuteur.mother')->with('error', 'غير مصرح.');
        }
        if ($mother->eleves()->exists()) {
            return redirect()->route('tuteur.mother')->with('error', 'لا يمكن حذف الأم لأنها مرتبطة بتلاميذ.');
        }

        $mother->delete();
        return redirect()->route('tuteur.mother')->with('success', 'تم حذف الأم بنجاح.');
    }

    public function updateSingleMother(Request $request)
    {
        $tuteur = $this->currentTuteurOrRedirect();
        if (!$tuteur) return redirect()->route('login')->with('error', 'يرجى تسجيل الدخول أولاً');

        if ((int)$tuteur->relation_tuteur !== 3) {
            return redirect()->route('tuteur.mother')->with('error', 'غير مصرح.');
        }
        if (!$tuteur->mother_id) {
            return redirect()->route('tuteur.mother')->with('error', 'لا توجد معلومات أم لتعديلها.');
        }

        $mother = Mother::where('id', $tuteur->mother_id)
            ->where('tuteur_nin', $tuteur->nin)
            ->first();
        if (!$mother) {
            return redirect()->route('tuteur.mother')->with('error', 'تعذر العثور على معلومات الأم.');
        }

        $validated = $request->validate([
            'nin' => ['required', 'regex:/^\d{18}$/'],
            'nss' => ['nullable', 'regex:/^\d{12}$/'],
            'nom_ar' => ['required', 'string', 'max:50', 'regex:/^[\p{Arabic}\s\-]+$/u'],
            'prenom_ar' => ['required', 'string', 'max:50', 'regex:/^[\p{Arabic}\s\-]+$/u'],
            'nom_fr' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-Z\s\-]+$/'],
            'prenom_fr' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-Z\s\-]+$/'],
            'categorie_sociale' => ['nullable', 'string', 'max:191'],
            'montant_s' => ['nullable', 'numeric', 'min:0'],
        ], [
            'nom_ar.required' => 'اللقب بالعربية مطلوب',
            'nom_ar.regex' => 'اللقب بالعربية يجب أن يحتوي على أحرف عربية فقط',
            'prenom_ar.required' => 'الاسم بالعربية مطلوب',
            'prenom_ar.regex' => 'الاسم بالعربية يجب أن يحتوي على أحرف عربية فقط',
            'nom_fr.regex' => 'اللقب باللاتينية يجب أن يحتوي على أحرف لاتينية فقط',
            'prenom_fr.regex' => 'الاسم باللاتينية يجب أن يحتوي على أحرف لاتينية فقط',
        ]);

        // ✅ Global NIN uniqueness check (excluding current mother)
        if (!empty(trim($validated['nin'])) && $validated['nin'] != $mother->nin) {
            if (Mother::where('nin', $validated['nin'])->where('id', '!=', $mother->id)->exists() || 
                Father::where('nin', $validated['nin'])->exists() || 
                Tuteur::where('nin', $validated['nin'])->exists()) {
                return redirect()->back()->withInput()->withErrors(['nin' => 'الرقم الوطني موجود بالفعل']);
            }
        }

        // ✅ Global NSS uniqueness check (optional field, excluding current mother)
        if (!empty($validated['nss']) && trim($validated['nss']) !== '' && $validated['nss'] != $mother->nss) {
            if (Mother::where('nss', $validated['nss'])->where('id', '!=', $mother->id)->exists() || 
                Father::where('nss', $validated['nss'])->exists() || 
                Tuteur::where('nss', $validated['nss'])->exists()) {
                return redirect()->back()->withInput()->withErrors(['nss' => 'رقم الضمان الاجتماعي موجود بالفعل']);
            }
        }

        $this->normalizeMontant($validated);

        $mother->update($validated);
        return redirect()->route('tuteur.mother')->with('success', 'تم تحديث معلومات الأم بنجاح.');
    }
}