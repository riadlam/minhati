<?php

namespace App\Http\Controllers;

use App\Models\Tuteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class TuteurController extends Controller
{
    // ✅ Get all tuteurs
    public function index()
    {
        return response()->json(Tuteur::with(['communeResidence', 'communeNaissance'])->get());
    }

    // ✅ Get a single tuteur
    public function show($nin)
    {
        $tuteur = Tuteur::with(['communeResidence', 'communeNaissance'])->find($nin);
        return $tuteur
            ? response()->json($tuteur)
            : response()->json(['message' => 'Tuteur non trouvé'], 404);
    }

    // ✅ Insert new tuteur
   public function store(Request $request)
    {
        try {
            // Basic validation
            $validated = $request->validate([
                'nin' => 'required|string|max:18|unique:tuteures,nin',
                'num_cpt' => 'required|string|max:12|unique:tuteures,num_cpt', // ✅ enforce unique CCP
                'cle_cpt' => 'required|string|max:2',
                'nom_ar' => 'nullable|string|max:50',
                'prenom_ar' => 'nullable|string|max:50',
                'nom_fr' => 'nullable|string|max:50',
                'prenom_fr' => 'nullable|string|max:50',
                'date_naiss' => 'nullable|date',
                'presume' => 'nullable|string|max:1',
                'commune_naiss' => 'nullable|string|exists:commune,code_comm',
                'sexe' => 'nullable|string|max:4',
                'nss' => 'nullable|string|max:12',
                'adresse' => 'nullable|string|max:80',
                'cats' => 'nullable|string|max:80',
                'montant_s' => 'nullable|numeric',
                'autr_info' => 'nullable|string|max:80',
                'num_cni' => 'nullable|string|max:10',
                'date_cni' => 'nullable|date',
                'lieu_cni' => 'nullable|string|max:5',
                'tel' => 'nullable|string|max:10',
                'nbr_enfants_scolarise' => 'nullable|integer',
                'code_commune' => 'nullable|string|exists:commune,code_comm',
                'date_insertion' => 'nullable|date',
                'email' => 'nullable|email|max:255',
                'password' => 'nullable|string|min:8',
            ], [
                'nin.required' => 'رقم التعريف الوطني (NIN) مطلوب',
                'nin.unique' => 'هذا الرقم الوطني موجود بالفعل',
                'num_cpt.unique' => 'رقم CCP موجود بالفعل لشخص آخر', // 🔹 custom message
                'email.email' => 'البريد الإلكتروني غير صالح',
                'password.min' => 'كلمة المرور يجب أن تحتوي على 8 أحرف على الأقل',
                'commune_naiss.exists' => 'رمز بلدية الميلاد غير موجود في قاعدة البيانات',
                'code_commune.exists' => 'رمز بلدية الإقامة غير موجود في قاعدة البيانات',
            ]);

            // ✅ Validate CCP + CLE
            if (!self::verifierRIP($validated['num_cpt'], $validated['cle_cpt'])) {
                return response()->json([
                    'message' => 'خطأ في CCP: الرقم أو المفتاح غير صحيح.'
                ], 422);
            }

            // ✅ Hash password only if provided
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }

            $tuteur = Tuteur::create($validated);

            return response()->json([
                'message' => 'تمت إضافة الولي/الوصي بنجاح',
                'data' => $tuteur
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'فشل في التحقق من البيانات',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ غير متوقع',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 🔹 CCP + CLE validation function
    private static function verifierRIP(string $ccp, string $cle): bool
    {
        $ccp = trim($ccp);
        $cle = trim($cle);

        if (!ctype_digit($ccp) || !ctype_digit($cle)) return false;

        $R1 = intval($ccp) * 100;
        $R2 = $R1 % 97;
        $R3 = ($R2 + 85 > 97) ? ($R2 + 85 - 97) : ($R2 + 85);
        $clerr = str_pad(97 - $R3, 2, "0", STR_PAD_LEFT);

        return $cle === $clerr;
    }

    // ✅ Update existing tuteur
    public function update(Request $request, $nin)
    {
        $tuteur = Tuteur::find($nin);
        if (!$tuteur) {
            return response()->json(['message' => 'الولي غير موجود'], 404);
        }

        try {
            $validated = $request->validate(
                [
                    'email' => 'nullable|email|max:255',
                    'password' => 'nullable|string|min:8',
                ],
                [
                    'email.email' => 'البريد الإلكتروني غير صالح',
                    'password.min' => 'كلمة المرور يجب أن تحتوي على 8 أحرف على الأقل',
                ]
            );

            // ✅ Hash password if new one is provided
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }

            $tuteur->update(array_merge($request->all(), $validated));

            return response()->json([
                'message' => 'تم تحديث بيانات الولي بنجاح',
                'data' => $tuteur
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'فشل في التحقق من البيانات',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // ✅ Delete tuteur
    public function destroy($nin)
    {
        $tuteur = Tuteur::find($nin);
        if (!$tuteur) {
            return response()->json(['message' => 'الولي غير موجود'], 404);
        }

        $tuteur->delete();
        return response()->json(['message' => 'تم حذف الولي بنجاح']);
    }
}
