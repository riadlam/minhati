<?php

namespace App\Http\Controllers;

use App\Models\Father;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class FatherController extends Controller
{
    /**
     * Display a listing of fathers for the authenticated tuteur.
     */
    public function index(Request $request)
    {
        // Try both $request->user() and auth()->user() for compatibility
        $tuteur = $request->user() ?? auth()->user();
        
        // For admin use, allow tuteur_nin from query parameter
        $tuteurNin = null;
        if ($request->has('tuteur_nin') && !empty($request->tuteur_nin)) {
            $tuteurNin = $request->tuteur_nin;
        } else if ($tuteur) {
            $tuteurNin = $tuteur->nin;
        } else {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $query = Father::where('tuteur_nin', $tuteurNin);
        
        // If NIN is provided in query, filter by it
        if ($request->has('nin') && !empty($request->nin)) {
            $query->where('nin', $request->nin);
        }
        
        $fathers = $query->get();
        
        return response()->json($fathers);
    }

    /**
     * Store a newly created father.
     */
    public function store(Request $request)
    {
        // Try both $request->user() and auth()->user() for compatibility
        $tuteur = $request->user() ?? auth()->user();
        
        // For admin use, allow tuteur_nin from request body
        $tuteurNin = null;
        if ($request->has('tuteur_nin') && !empty($request->tuteur_nin)) {
            $tuteurNin = $request->tuteur_nin;
            // Verify tuteur exists
            $tuteurExists = \App\Models\Tuteur::where('nin', $tuteurNin)->exists();
            if (!$tuteurExists) {
                return response()->json(['message' => 'الولي المحدد غير موجود'], 404);
            }
        } else if ($tuteur) {
            $tuteurNin = $tuteur->nin;
        } else {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'nin' => 'required|string|size:18|unique:fathers,nin',
            'nss' => 'nullable|string|size:12',
            'nom_ar' => 'required|string|max:50|regex:/^[\p{Arabic}\s\-]+$/u',
            'prenom_ar' => 'required|string|max:50|regex:/^[\p{Arabic}\s\-]+$/u',
            'nom_fr' => 'nullable|string|max:50|regex:/^[a-zA-Z\s\-]+$/',
            'prenom_fr' => 'nullable|string|max:50|regex:/^[a-zA-Z\s\-]+$/',
            'categorie_sociale' => 'nullable|string|max:80',
            'montant_s' => 'nullable|numeric|min:0|max:99999999.99',
            'biometric_id' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'biometric_id_back' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'Certificate_of_none_income' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'Certificate_of_non_affiliation_to_social_security' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'crossed_ccp' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'salary_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'nin.required' => 'الرقم الوطني للأب مطلوب',
            'nin.size' => 'الرقم الوطني للأب يجب أن يحتوي على 18 رقمًا بالضبط',
            'nin.unique' => 'الرقم الوطني موجود بالفعل',
            'nss.size' => 'رقم الضمان الاجتماعي للأب يجب أن يحتوي على 12 رقمًا بالضبط',
            'nom_ar.required' => 'اللقب بالعربية مطلوب',
            'nom_ar.max' => 'اللقب بالعربية يجب ألا يتجاوز 50 حرفًا',
            'nom_ar.regex' => 'اللقب بالعربية يجب أن يحتوي على أحرف عربية فقط',
            'prenom_ar.required' => 'الاسم بالعربية مطلوب',
            'prenom_ar.max' => 'الاسم بالعربية يجب ألا يتجاوز 50 حرفًا',
            'prenom_ar.regex' => 'الاسم بالعربية يجب أن يحتوي على أحرف عربية فقط',
            'nom_fr.max' => 'اللقب باللاتينية يجب ألا يتجاوز 50 حرفًا',
            'nom_fr.regex' => 'اللقب باللاتينية يجب أن يحتوي على أحرف لاتينية فقط',
            'prenom_fr.max' => 'الاسم باللاتينية يجب ألا يتجاوز 50 حرفًا',
            'prenom_fr.regex' => 'الاسم باللاتينية يجب أن يحتوي على أحرف لاتينية فقط',
            'categorie_sociale.max' => 'الفئة الاجتماعية يجب ألا تتجاوز 80 حرفًا',
            'montant_s.numeric' => 'مبلغ الدخل الشهري يجب أن يكون رقمًا',
            'montant_s.min' => 'مبلغ الدخل الشهري يجب أن يكون أكبر من أو يساوي 0',
            'montant_s.max' => 'مبلغ الدخل الشهري يجب ألا يتجاوز 99,999,999.99',
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
        if ($request->has('nss') && $request->nss !== null && trim($request->nss) !== '') {
            $nss = trim(strval($request->nss));
            if (strlen($nss) !== 12 || !ctype_digit($nss)) {
                return response()->json([
                    'message' => 'فشل في التحقق من البيانات',
                    'errors' => ['nss' => 'رقم الضمان الاجتماعي للأب يجب أن يحتوي على 12 رقمًا بالضبط']
                ], 422);
            }
            
            // Validate NSS check digit using SiNSScle algorithm
            if (!self::validateNSS($nss)) {
                return response()->json([
                    'message' => 'فشل في التحقق من البيانات',
                    'errors' => ['nss' => 'رقم الضمان الاجتماعي للأب غير صحيح. يرجى التحقق من الرقم']
                ], 422);
            }
            
            // Check if NSS already exists GLOBALLY (NSS is optional for fathers)
            if (Father::where('nss', $nss)->exists() || 
                \App\Models\Mother::where('nss', $nss)->exists() || 
                \App\Models\Tuteur::where('nss', $nss)->exists()) {
                return response()->json([
                    'message' => 'فشل في التحقق من البيانات',
                    'errors' => ['nss' => 'رقم الضمان الاجتماعي موجود بالفعل']
                ], 422);
            }
        }

        // Check if NIN already exists GLOBALLY
        if (Father::where('nin', $nin)->exists() || 
            \App\Models\Mother::where('nin', $nin)->exists() || 
            \App\Models\Tuteur::where('nin', $nin)->exists()) {
            return response()->json([
                'message' => 'فشل في التحقق من البيانات',
                'errors' => ['nin' => 'الرقم الوطني موجود بالفعل']
            ], 422);
        }

        // Validate conditional file uploads based on social category
        $cats = $request->categorie_sociale ?? null;
        if ($cats === 'عديم الدخل') {
            if (!$request->hasFile('Certificate_of_none_income')) {
                return response()->json([
                    'message' => 'فشل في التحقق من البيانات',
                    'errors' => ['Certificate_of_none_income' => 'شهادة عدم الدخل مطلوبة عند اختيار "عديم الدخل"']
                ], 422);
            }
            if (!$request->hasFile('Certificate_of_non_affiliation_to_social_security')) {
                return response()->json([
                    'message' => 'فشل في التحقق من البيانات',
                    'errors' => ['Certificate_of_non_affiliation_to_social_security' => 'شهادة عدم الانتساب للضمان الاجتماعي مطلوبة عند اختيار "عديم الدخل"']
                ], 422);
            }
        } elseif ($cats === 'الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون') {
            if (!$request->hasFile('crossed_ccp')) {
                return response()->json([
                    'message' => 'فشل في التحقق من البيانات',
                    'errors' => ['crossed_ccp' => 'صك بريدي مشطوب مطلوب عند اختيار "الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون"']
                ], 422);
            }
        }

        // Handle file uploads securely
        $fileFields = [
            'biometric_id',
            'biometric_id_back',
            'Certificate_of_none_income',
            'Certificate_of_non_affiliation_to_social_security',
            'crossed_ccp',
            'salary_certificate'
        ];
        
        $fileData = [];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                
                // Validate MIME type
                $allowedMimes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
                if (!in_array($file->getMimeType(), $allowedMimes)) {
                    return response()->json([
                        'message' => 'فشل في التحقق من البيانات',
                        'errors' => [$field => 'نوع الملف غير مسموح. يجب أن يكون PDF, JPG, JPEG, أو PNG']
                    ], 422);
                }
                
                // Generate secure filename
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $sanitizedName = preg_replace('/[^a-zA-Z0-9_\-\x{0600}-\x{06FF}]/u', '_', pathinfo($originalName, PATHINFO_FILENAME));
                $timestamp = time();
                $randomHash = bin2hex(random_bytes(8));
                $secureFilename = "{$timestamp}_{$randomHash}_{$sanitizedName}.{$extension}";
                
                // Store file in private storage
                $path = $file->storeAs("father_docs/{$field}", $secureFilename, 'local');
                
                // Add path to file data
                $fileData[$field] = $path;
            }
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
            'tuteur_nin' => $tuteurNin,
            'date_insertion' => now(),
        ] + $fileData);

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
        // Try both $request->user() and auth()->user() for compatibility
        $tuteur = $request->user() ?? auth()->user();
        
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
        // Try both $request->user() and auth()->user() for compatibility
        $tuteur = $request->user() ?? auth()->user();
        
        // For admin use, allow tuteur_nin from request body
        $tuteurNin = null;
        if ($request->has('tuteur_nin') && !empty($request->tuteur_nin)) {
            $tuteurNin = $request->tuteur_nin;
            // Verify tuteur exists
            $tuteurExists = \App\Models\Tuteur::where('nin', $tuteurNin)->exists();
            if (!$tuteurExists) {
                return response()->json(['message' => 'الولي المحدد غير موجود'], 404);
            }
        } else if ($tuteur) {
            $tuteurNin = $tuteur->nin;
        } else {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $father = Father::where('id', $id)
            ->where('tuteur_nin', $tuteurNin)
            ->first();

        if (!$father) {
            return response()->json(['message' => 'الأب غير موجود'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nin' => 'sometimes|required|string|size:18|unique:fathers,nin,' . $id,
            'nss' => 'nullable|string|size:12',
            'nom_ar' => 'sometimes|required|string|max:50|regex:/^[\p{Arabic}\s\-]+$/u',
            'prenom_ar' => 'sometimes|required|string|max:50|regex:/^[\p{Arabic}\s\-]+$/u',
            'nom_fr' => 'nullable|string|max:50|regex:/^[a-zA-Z\s\-]+$/',
            'prenom_fr' => 'nullable|string|max:50|regex:/^[a-zA-Z\s\-]+$/',
            'categorie_sociale' => 'nullable|string|max:80',
            'montant_s' => 'nullable|numeric|min:0|max:99999999.99',
            'biometric_id' => 'sometimes|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'biometric_id_back' => 'sometimes|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'Certificate_of_none_income' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'Certificate_of_non_affiliation_to_social_security' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'crossed_ccp' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'salary_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'nin.required' => 'الرقم الوطني للأب مطلوب',
            'nin.size' => 'الرقم الوطني للأب يجب أن يحتوي على 18 رقمًا بالضبط',
            'nin.unique' => 'الرقم الوطني موجود بالفعل',
            'nss.size' => 'رقم الضمان الاجتماعي للأب يجب أن يحتوي على 12 رقمًا بالضبط',
            'nom_ar.required' => 'اللقب بالعربية مطلوب',
            'nom_ar.max' => 'اللقب بالعربية يجب ألا يتجاوز 50 حرفًا',
            'nom_ar.regex' => 'اللقب بالعربية يجب أن يحتوي على أحرف عربية فقط',
            'prenom_ar.required' => 'الاسم بالعربية مطلوب',
            'prenom_ar.max' => 'الاسم بالعربية يجب ألا يتجاوز 50 حرفًا',
            'prenom_ar.regex' => 'الاسم بالعربية يجب أن يحتوي على أحرف عربية فقط',
            'nom_fr.max' => 'اللقب باللاتينية يجب ألا يتجاوز 50 حرفًا',
            'nom_fr.regex' => 'اللقب باللاتينية يجب أن يحتوي على أحرف لاتينية فقط',
            'prenom_fr.max' => 'الاسم باللاتينية يجب ألا يتجاوز 50 حرفًا',
            'prenom_fr.regex' => 'الاسم باللاتينية يجب أن يحتوي على أحرف لاتينية فقط',
            'categorie_sociale.max' => 'الفئة الاجتماعية يجب ألا تتجاوز 80 حرفًا',
            'montant_s.numeric' => 'مبلغ الدخل الشهري يجب أن يكون رقمًا',
            'montant_s.min' => 'مبلغ الدخل الشهري يجب أن يكون أكبر من أو يساوي 0',
            'montant_s.max' => 'مبلغ الدخل الشهري يجب ألا يتجاوز 99,999,999.99',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'فشل في التحقق من البيانات',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validate NIN is exactly 18 digits if provided
        if ($request->has('nin') && trim($request->nin) !== '') {
            $nin = trim(strval($request->nin));
            if (strlen($nin) !== 18 || !ctype_digit($nin)) {
                return response()->json([
                    'message' => 'فشل في التحقق من البيانات',
                    'errors' => ['nin' => 'الرقم الوطني للأب يجب أن يحتوي على 18 رقمًا بالضبط']
                ], 422);
            }

            // Check if NIN already exists GLOBALLY (excluding current record)
            if (Father::where('nin', $nin)->where('id', '!=', $id)->exists() || 
                \App\Models\Mother::where('nin', $nin)->exists() || 
                \App\Models\Tuteur::where('nin', $nin)->exists()) {
                return response()->json([
                    'message' => 'فشل في التحقق من البيانات',
                    'errors' => ['nin' => 'الرقم الوطني موجود بالفعل']
                ], 422);
            }
        }

        // Validate NSS is exactly 12 digits if provided
        $nssValue = $father->nss; // Keep existing value by default
        if ($request->has('nss') && $request->nss !== null && trim($request->nss) !== '') {
            $nss = trim(strval($request->nss));
            if (strlen($nss) !== 12 || !ctype_digit($nss)) {
                return response()->json([
                    'message' => 'فشل في التحقق من البيانات',
                    'errors' => ['nss' => 'رقم الضمان الاجتماعي للأب يجب أن يحتوي على 12 رقمًا بالضبط']
                ], 422);
            }
            
            // Validate NSS check digit using SiNSScle algorithm
            if (!self::validateNSS($nss)) {
                return response()->json([
                    'message' => 'فشل في التحقق من البيانات',
                    'errors' => ['nss' => 'رقم الضمان الاجتماعي للأب غير صحيح. يرجى التحقق من الرقم']
                ], 422);
            }
            
            // Check if NSS already exists GLOBALLY (excluding current record, NSS is optional for fathers)
            if (Father::where('nss', $nss)->where('id', '!=', $id)->exists() || 
                \App\Models\Mother::where('nss', $nss)->exists() || 
                \App\Models\Tuteur::where('nss', $nss)->exists()) {
                return response()->json([
                    'message' => 'فشل في التحقق من البيانات',
                    'errors' => ['nss' => 'رقم الضمان الاجتماعي موجود بالفعل']
                ], 422);
            }
            
            // Store the validated NSS value
            $nssValue = $nss;
        } else if ($request->has('nss') && ($request->nss === null || trim($request->nss) === '')) {
            // Explicitly set to null if empty string is sent
            $nssValue = null;
        }

        // Handle file uploads securely
        $fileFields = [
            'biometric_id',
            'biometric_id_back',
            'Certificate_of_none_income',
            'Certificate_of_non_affiliation_to_social_security',
            'crossed_ccp',
            'salary_certificate'
        ];
        
        $fileData = [];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                
                // Validate MIME type
                $allowedMimes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
                if (!in_array($file->getMimeType(), $allowedMimes)) {
                    return response()->json([
                        'message' => 'فشل في التحقق من البيانات',
                        'errors' => [$field => 'نوع الملف غير مسموح. يجب أن يكون PDF, JPG, JPEG, أو PNG']
                    ], 422);
                }
                
                // Delete old file if exists
                if ($father->$field && Storage::disk('local')->exists($father->$field)) {
                    Storage::disk('local')->delete($father->$field);
                }
                
                // Generate secure filename
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $sanitizedName = preg_replace('/[^a-zA-Z0-9_\-\x{0600}-\x{06FF}]/u', '_', pathinfo($originalName, PATHINFO_FILENAME));
                $timestamp = time();
                $randomHash = bin2hex(random_bytes(8));
                $secureFilename = "{$timestamp}_{$randomHash}_{$sanitizedName}.{$extension}";
                
                // Store file in private storage
                $path = $file->storeAs("father_docs/{$field}", $secureFilename, 'local');
                
                // Add path to file data
                $fileData[$field] = $path;
            }
        }

        // Update fields - FormData sends all form fields, so we update all that are present
        $updateData = [];
        
        // Always update these fields if they're in the request (FormData sends all fields)
        if ($request->has('nin') && $request->nin) {
            $updateData['nin'] = strval($request->nin);
        }
        if ($request->has('nss')) {
            $updateData['nss'] = $nssValue;
        }
        // Required fields - always update if present (they should always be present from form)
        if ($request->has('nom_ar')) {
            $updateData['nom_ar'] = $request->nom_ar;
        }
        if ($request->has('prenom_ar')) {
            $updateData['prenom_ar'] = $request->prenom_ar;
        }
        // Optional fields
        if ($request->has('nom_fr')) {
            $updateData['nom_fr'] = $request->nom_fr ?: null;
        }
        if ($request->has('prenom_fr')) {
            $updateData['prenom_fr'] = $request->prenom_fr ?: null;
        }
        if ($request->has('categorie_sociale')) {
            $updateData['categorie_sociale'] = $request->categorie_sociale ?: null;
        }
        if ($request->has('montant_s')) {
            $updateData['montant_s'] = $request->montant_s ?: null;
        }
        
        \Log::info('Father update data', ['update_data' => $updateData, 'file_data_keys' => array_keys($fileData), 'request_all' => $request->all()]);

        // Merge file data with update data
        $father->update($updateData + $fileData);
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
        // Try both $request->user() and auth()->user() for compatibility
        $tuteur = $request->user() ?? auth()->user();
        
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

    // 🔹 NSS validation function (SiNSScle algorithm)
    private static function validateNSS(string $nss): bool
    {
        $nss = trim($nss);
        
        // Must be exactly 12 digits
        if (strlen($nss) !== 12 || !ctype_digit($nss)) {
            return false;
        }
        
        // Convert string to array of integers (0-indexed)
        $digits = array_map('intval', str_split($nss));
        
        // Calculate sum: (positions 0,2,4,6,8) * 2 + (positions 1,3,5,7,9)
        // Note: Pascal uses 1-indexed, PHP uses 0-indexed
        $sum = ($digits[0] + $digits[2] + $digits[4] + $digits[6] + $digits[8]) * 2 +
               ($digits[1] + $digits[3] + $digits[5] + $digits[7] + $digits[9]);
        
        // Calculate check digit: 99 - sum
        $cleN = 99 - $sum;
        
        // Format as 2-digit string with leading zero if needed
        $formattedCle = str_pad($cleN, 2, "0", STR_PAD_LEFT);
        
        // Check if last 2 digits (positions 10-11, 0-indexed) match calculated check digit
        $lastTwoDigits = substr($nss, 10, 2);
        
        return $lastTwoDigits === $formattedCle;
    }
}
