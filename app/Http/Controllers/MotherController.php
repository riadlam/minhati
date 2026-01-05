<?php

namespace App\Http\Controllers;

use App\Models\Mother;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MotherController extends Controller
{
    /**
     * Display a listing of mothers for the authenticated tuteur.
     */
    public function index(Request $request)
    {
        // Try both $request->user() and auth()->user() for compatibility
        $tuteur = $request->user() ?? auth()->user();
        
        if (!$tuteur) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $mothers = Mother::where('tuteur_nin', $tuteur->nin)->get();
        
        return response()->json($mothers);
    }

    /**
     * Store a newly created mother.
     */
    public function store(Request $request)
    {
        // Try both $request->user() and auth()->user() for compatibility
        $tuteur = $request->user() ?? auth()->user();
        
        if (!$tuteur) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'nin' => 'required|string|size:18|unique:mothers,nin',
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
                'errors' => ['nin' => 'الرقم الوطني للأم يجب أن يحتوي على 18 رقمًا بالضبط']
            ], 422);
        }

        // Validate NSS is exactly 12 digits if provided
        if ($request->has('nss') && $request->nss !== null) {
            $nss = strval($request->nss);
            if (strlen($nss) !== 12 || !ctype_digit($nss)) {
                return response()->json([
                    'message' => 'فشل في التحقق من البيانات',
                    'errors' => ['nss' => 'رقم الضمان الاجتماعي للأم يجب أن يحتوي على 12 رقمًا بالضبط']
                ], 422);
            }
        }

        // Check if NIN already exists
        if (Mother::where('nin', $nin)->exists()) {
            return response()->json([
                'message' => 'فشل في التحقق من البيانات',
                'errors' => ['nin' => 'الرقم الوطني للأم موجود بالفعل']
            ], 422);
        }

        $mother = Mother::create([
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
            'message' => 'تم إنشاء الأم بنجاح',
            'data' => $mother
        ], 201);
    }

    /**
     * Display the specified mother.
     */
    public function show(Request $request, string $id)
    {
        // Try both $request->user() and auth()->user() for compatibility
        $tuteur = $request->user() ?? auth()->user();
        
        if (!$tuteur) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $mother = Mother::where('id', $id)
            ->where('tuteur_nin', $tuteur->nin)
            ->first();

        if (!$mother) {
            return response()->json(['message' => 'الأم غير موجودة'], 404);
        }

        return response()->json($mother);
    }

    /**
     * Update the specified mother.
     */
    public function update(Request $request, string $id)
    {
        // Try both $request->user() and auth()->user() for compatibility
        $tuteur = $request->user() ?? auth()->user();
        
        if (!$tuteur) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $mother = Mother::where('id', $id)
            ->where('tuteur_nin', $tuteur->nin)
            ->first();

        if (!$mother) {
            return response()->json(['message' => 'الأم غير موجودة'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nin' => 'sometimes|required|string|size:18|unique:mothers,nin,' . $id,
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
                    'errors' => ['nin' => 'الرقم الوطني للأم يجب أن يحتوي على 18 رقمًا بالضبط']
                ], 422);
            }

            // Check if NIN already exists (excluding current record)
            if (Mother::where('nin', $nin)->where('id', '!=', $id)->exists()) {
                return response()->json([
                    'message' => 'فشل في التحقق من البيانات',
                    'errors' => ['nin' => 'الرقم الوطني للأم موجود بالفعل']
                ], 422);
            }
        }

        // Validate NSS is exactly 12 digits if provided
        if ($request->has('nss') && $request->nss !== null) {
            $nss = strval($request->nss);
            if (strlen($nss) !== 12 || !ctype_digit($nss)) {
                return response()->json([
                    'message' => 'فشل في التحقق من البيانات',
                    'errors' => ['nss' => 'رقم الضمان الاجتماعي للأم يجب أن يحتوي على 12 رقمًا بالضبط']
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

        $mother->update($updateData);
        $mother->refresh();

        return response()->json([
            'message' => 'تم تحديث الأم بنجاح',
            'data' => $mother
        ]);
    }

    /**
     * Remove the specified mother.
     */
    public function destroy(Request $request, string $id)
    {
        // Try both $request->user() and auth()->user() for compatibility
        $tuteur = $request->user() ?? auth()->user();
        
        if (!$tuteur) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $mother = Mother::where('id', $id)
            ->where('tuteur_nin', $tuteur->nin)
            ->first();

        if (!$mother) {
            return response()->json(['message' => 'الأم غير موجودة'], 404);
        }

        // Check if mother is linked to any students
        if ($mother->eleves()->count() > 0) {
            return response()->json([
                'message' => 'لا يمكن حذف الأم لأنها مرتبطة بتلاميذ',
                'errors' => ['mother' => 'يجب حذف التلاميذ المرتبطين أولاً']
            ], 422);
        }

        $mother->delete();

        return response()->json([
            'message' => 'تم حذف الأم بنجاح'
        ]);
    }
}
