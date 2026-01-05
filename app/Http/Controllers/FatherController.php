<?php

namespace App\Http\Controllers;

use App\Models\Father;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FatherController extends Controller
{
    /**
     * Display a listing of fathers for the authenticated tuteur.
     */
    public function index(Request $request)
    {
        $tuteur = $request->user();
        
        if (!$tuteur) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $fathers = Father::where('tuteur_nin', $tuteur->nin)->get();
        
        return response()->json($fathers);
    }

    /**
     * Store a newly created father.
     */
    public function store(Request $request)
    {
        $tuteur = $request->user();
        
        if (!$tuteur) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'nin' => 'required|string|size:18|unique:fathers,nin',
            'nss' => 'nullable|string|size:12',
            'nom_ar' => 'required|string|max:50',
            'prenom_ar' => 'required|string|max:50',
            'nom_fr' => 'nullable|string|max:50',
            'prenom_fr' => 'nullable|string|max:50',
            'categorie_sociale' => 'nullable|string|max:80',
            'montant_s' => 'nullable|numeric|min:0|max:99999999.99',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'فشل في التحقق من البيانات',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validate NIN is exactly 18 digits
        $nin = strval($request->nin);
        if (strlen($nin) !== 18 || !ctype_digit($nin)) {
            return response()->json([
                'message' => 'فشل في التحقق من البيانات',
                'errors' => ['nin' => 'الرقم الوطني للأب يجب أن يحتوي على 18 رقمًا بالضبط']
            ], 422);
        }

        // Validate NSS is exactly 12 digits if provided
        if ($request->has('nss') && $request->nss !== null) {
            $nss = strval($request->nss);
            if (strlen($nss) !== 12 || !ctype_digit($nss)) {
                return response()->json([
                    'message' => 'فشل في التحقق من البيانات',
                    'errors' => ['nss' => 'رقم الضمان الاجتماعي للأب يجب أن يحتوي على 12 رقمًا بالضبط']
                ], 422);
            }
        }

        // Check if NIN already exists
        if (Father::where('nin', $nin)->exists()) {
            return response()->json([
                'message' => 'فشل في التحقق من البيانات',
                'errors' => ['nin' => 'الرقم الوطني للأب موجود بالفعل']
            ], 422);
        }

        $father = Father::create([
            'nin' => $nin,
            'nss' => $request->nss ? substr(strval($request->nss), 0, 12) : null,
            'nom_ar' => $request->nom_ar,
            'prenom_ar' => $request->prenom_ar,
            'nom_fr' => $request->nom_fr ?? null,
            'prenom_fr' => $request->prenom_fr ?? null,
            'categorie_sociale' => $request->categorie_sociale ?? null,
            'montant_s' => $request->montant_s ?? null,
            'tuteur_nin' => $tuteur->nin,
            'date_insertion' => now(),
        ]);

        return response()->json([
            'message' => 'تم إنشاء الأب بنجاح',
            'data' => $father
        ], 201);
    }

    /**
     * Display the specified father.
     */
    public function show(Request $request, string $id)
    {
        $tuteur = $request->user();
        
        if (!$tuteur) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $father = Father::where('id', $id)
            ->where('tuteur_nin', $tuteur->nin)
            ->first();

        if (!$father) {
            return response()->json(['message' => 'الأب غير موجود'], 404);
        }

        return response()->json($father);
    }

    /**
     * Update the specified father.
     */
    public function update(Request $request, string $id)
    {
        $tuteur = $request->user();
        
        if (!$tuteur) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $father = Father::where('id', $id)
            ->where('tuteur_nin', $tuteur->nin)
            ->first();

        if (!$father) {
            return response()->json(['message' => 'الأب غير موجود'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nin' => 'sometimes|required|string|size:18|unique:fathers,nin,' . $id,
            'nss' => 'nullable|string|size:12',
            'nom_ar' => 'sometimes|required|string|max:50',
            'prenom_ar' => 'sometimes|required|string|max:50',
            'nom_fr' => 'nullable|string|max:50',
            'prenom_fr' => 'nullable|string|max:50',
            'categorie_sociale' => 'nullable|string|max:80',
            'montant_s' => 'nullable|numeric|min:0|max:99999999.99',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'فشل في التحقق من البيانات',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validate NIN is exactly 18 digits if provided
        if ($request->has('nin')) {
            $nin = strval($request->nin);
            if (strlen($nin) !== 18 || !ctype_digit($nin)) {
                return response()->json([
                    'message' => 'فشل في التحقق من البيانات',
                    'errors' => ['nin' => 'الرقم الوطني للأب يجب أن يحتوي على 18 رقمًا بالضبط']
                ], 422);
            }

            // Check if NIN already exists (excluding current record)
            if (Father::where('nin', $nin)->where('id', '!=', $id)->exists()) {
                return response()->json([
                    'message' => 'فشل في التحقق من البيانات',
                    'errors' => ['nin' => 'الرقم الوطني للأب موجود بالفعل']
                ], 422);
            }
        }

        // Validate NSS is exactly 12 digits if provided
        if ($request->has('nss') && $request->nss !== null) {
            $nss = strval($request->nss);
            if (strlen($nss) !== 12 || !ctype_digit($nss)) {
                return response()->json([
                    'message' => 'فشل في التحقق من البيانات',
                    'errors' => ['nss' => 'رقم الضمان الاجتماعي للأب يجب أن يحتوي على 12 رقمًا بالضبط']
                ], 422);
            }
        }

        // Update only provided fields
        $updateData = [];
        if ($request->has('nin')) {
            $updateData['nin'] = strval($request->nin);
        }
        if ($request->has('nss')) {
            $updateData['nss'] = $request->nss ? substr(strval($request->nss), 0, 12) : null;
        }
        if ($request->has('nom_ar')) {
            $updateData['nom_ar'] = $request->nom_ar;
        }
        if ($request->has('prenom_ar')) {
            $updateData['prenom_ar'] = $request->prenom_ar;
        }
        if ($request->has('nom_fr')) {
            $updateData['nom_fr'] = $request->nom_fr;
        }
        if ($request->has('prenom_fr')) {
            $updateData['prenom_fr'] = $request->prenom_fr;
        }
        if ($request->has('categorie_sociale')) {
            $updateData['categorie_sociale'] = $request->categorie_sociale;
        }
        if ($request->has('montant_s')) {
            $updateData['montant_s'] = $request->montant_s;
        }

        $father->update($updateData);
        $father->refresh();

        return response()->json([
            'message' => 'تم تحديث الأب بنجاح',
            'data' => $father
        ]);
    }

    /**
     * Remove the specified father.
     */
    public function destroy(Request $request, string $id)
    {
        $tuteur = $request->user();
        
        if (!$tuteur) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $father = Father::where('id', $id)
            ->where('tuteur_nin', $tuteur->nin)
            ->first();

        if (!$father) {
            return response()->json(['message' => 'الأب غير موجود'], 404);
        }

        // Check if father is linked to any students
        if ($father->eleves()->count() > 0) {
            return response()->json([
                'message' => 'لا يمكن حذف الأب لأنه مرتبط بتلاميذ',
                'errors' => ['father' => 'يجب حذف التلاميذ المرتبطين أولاً']
            ], 422);
        }

        $father->delete();

        return response()->json([
            'message' => 'تم حذف الأب بنجاح'
        ]);
    }
}
