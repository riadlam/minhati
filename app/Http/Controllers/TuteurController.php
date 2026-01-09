<?php

namespace App\Http\Controllers;

use App\Models\Tuteur;
use App\Models\Mother;
use App\Models\Father;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        $tuteur = Tuteur::with([
            'communeResidence', 
            'communeNaissance',
            'father',  // Load father relationship if father_id exists
            'mother'   // Load mother relationship if mother_id exists
        ])->find($nin);
        return $tuteur
            ? response()->json($tuteur)
            : response()->json(['message' => 'Tuteur non trouvé'], 404);
    }

    // ✅ Insert new tuteur
   public function store(Request $request)
    {
        Log::info('🔵 SIGNUP: Starting signup process', [
            'request_data' => $request->except(['password']),
            'has_mothers' => $request->has('mothers'),
            'has_father' => $request->has('father'),
            'has_mother' => $request->has('mother'),
        ]);

        try {
            Log::info('🔵 SIGNUP: Step 1 - Starting basic validation');
            // Basic validation
            $validated = $request->validate([
                'nin' => 'required|string|max:18|unique:tuteures,nin',
                'num_cpt' => 'required|string|max:12|unique:tuteures,num_cpt', // ✅ enforce unique CCP
                'cle_cpt' => 'required|string|max:2',
                'nom_ar' => 'nullable|string|max:50|regex:/^[\p{Arabic}\s\-]+$/u',
                'prenom_ar' => 'nullable|string|max:50|regex:/^[\p{Arabic}\s\-]+$/u',
                'nom_fr' => 'nullable|string|max:50|regex:/^[a-zA-Z\s\-]+$/',
                'prenom_fr' => 'nullable|string|max:50|regex:/^[a-zA-Z\s\-]+$/',
                'date_naiss' => 'nullable|date',
                'presume' => 'nullable|string|max:1',
                'commune_naiss' => 'nullable|string|exists:commune,code_comm',
                'sexe' => 'nullable|string|max:4',
                'nss' => 'nullable|string|size:12|unique:tuteures,nss',
                'adresse' => 'nullable|string|max:80',
                'cats' => 'nullable|string|max:80',
                'montant_s' => 'nullable|numeric',
                'autr_info' => 'nullable|string|max:80',
                'num_cni' => 'nullable|string|max:10|unique:tuteures,num_cni',
                'date_cni' => 'nullable|date',
                'lieu_cni' => 'nullable|string|max:5',
                'tel' => 'nullable|string|max:10',
                'nbr_enfants_scolarise' => 'nullable|integer',
                'code_commune' => 'nullable|string|exists:commune,code_comm',
                'date_insertion' => 'nullable|date',
                'email' => 'nullable|email|max:255',
                'password' => 'nullable|string|min:8',
                'relation_tuteur' => 'nullable|in:1,2,3',
            ], [
                'nin.required' => 'رقم التعريف الوطني (NIN) مطلوب',
                'nin.unique' => 'هذا الرقم الوطني موجود بالفعل',
                'num_cpt.unique' => 'رقم CCP موجود بالفعل لشخص آخر',
                'nss.unique' => 'رقم الضمان الاجتماعي موجود بالفعل',
                'num_cni.unique' => 'رقم بطاقة التعريف الوطنية موجود بالفعل',
                'nom_ar.regex' => 'اللقب بالعربية يجب أن يحتوي على أحرف عربية فقط',
                'prenom_ar.regex' => 'الاسم بالعربية يجب أن يحتوي على أحرف عربية فقط',
                'nom_fr.regex' => 'اللقب باللاتينية يجب أن يحتوي على أحرف لاتينية فقط',
                'prenom_fr.regex' => 'الاسم باللاتينية يجب أن يحتوي على أحرف لاتينية فقط',
                'email.email' => 'البريد الإلكتروني غير صالح',
                'password.min' => 'كلمة المرور يجب أن تحتوي على 8 أحرف على الأقل',
                'commune_naiss.exists' => 'رمز بلدية الميلاد غير موجود في قاعدة البيانات',
                'code_commune.exists' => 'رمز بلدية الإقامة غير موجود في قاعدة البيانات',
            ]);

            Log::info('✅ SIGNUP: Step 1 - Basic validation passed', [
                'validated_fields' => array_keys($validated),
                'nin' => $validated['nin'],
                'email' => $validated['email'] ?? 'N/A',
            ]);

            Log::info('🔵 SIGNUP: Step 2 - Checking global NIN uniqueness');
            // ✅ Check NIN globally across all tables
            if (\App\Models\Mother::where('nin', $validated['nin'])->exists() || 
                \App\Models\Father::where('nin', $validated['nin'])->exists()) {
                Log::warning('❌ SIGNUP: NIN already exists in mothers/fathers', ['nin' => $validated['nin']]);
                return response()->json([
                    'message' => 'فشل في التحقق من البيانات',
                    'errors' => ['nin' => 'الرقم الوطني موجود بالفعل']
                ], 422);
            }
            
            // ✅ Check NSS globally if provided
            if (!empty($validated['nss'])) {
                if (\App\Models\Mother::where('nss', $validated['nss'])->exists() || 
                    \App\Models\Father::where('nss', $validated['nss'])->exists()) {
                    Log::warning('❌ SIGNUP: NSS already exists in mothers/fathers', ['nss' => $validated['nss']]);
                    return response()->json([
                        'message' => 'فشل في التحقق من البيانات',
                        'errors' => ['nss' => 'رقم الضمان الاجتماعي موجود بالفعل']
                    ], 422);
                }
            }
            
            // Note: CNI (num_cni) is only checked in tuteures table via Laravel unique validation rule above
            // Mothers and Fathers tables don't have num_cni column, so no global check needed

            Log::info('✅ SIGNUP: Step 2 - Global uniqueness checks passed');

            Log::info('🔵 SIGNUP: Step 3 - Validating CCP + CLE', [
                'num_cpt' => $validated['num_cpt'],
                'cle_cpt' => $validated['cle_cpt'],
            ]);
            // ✅ Validate CCP + CLE
            if (!self::verifierRIP($validated['num_cpt'], $validated['cle_cpt'])) {
                Log::warning('❌ SIGNUP: CCP validation failed', [
                    'num_cpt' => $validated['num_cpt'],
                    'cle_cpt' => $validated['cle_cpt'],
                ]);
                return response()->json([
                    'message' => 'خطأ في CCP: الرقم أو المفتاح غير صحيح.'
                ], 422);
            }

            Log::info('✅ SIGNUP: Step 3 - CCP validation passed');

            // ✅ Hash password only if provided
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
                Log::info('✅ SIGNUP: Password hashed');
            }

            // ✅ Get relation_tuteur (nullable - may not be provided during signup)
            $relationTuteur = $validated['relation_tuteur'] ?? null;
            Log::info('🔵 SIGNUP: Step 4 - Processing parent data', [
                'relation_tuteur' => $relationTuteur,
            ]);

            // ✅ Handle mothers data (for Father role - multiple wives)
            $mothersData = [];
            if ($relationTuteur === '1' && $request->has('mothers')) {
                $mothersJson = $request->input('mothers');
                Log::info('🔵 SIGNUP: Parsing mothers data', ['raw' => $mothersJson]);
                if (is_string($mothersJson)) {
                    $mothersData = json_decode($mothersJson, true) ?? [];
                } else {
                    $mothersData = $mothersJson;
                }
                Log::info('✅ SIGNUP: Mothers data parsed', ['count' => count($mothersData)]);
            }

            // ✅ Handle father data (for Mother and Guardian roles)
            $fatherData = null;
            if (in_array($relationTuteur, ['2', '3']) && $request->has('father')) {
                $fatherJson = $request->input('father');
                Log::info('🔵 SIGNUP: Parsing father data', ['raw' => $fatherJson]);
                if (is_string($fatherJson)) {
                    $fatherData = json_decode($fatherJson, true);
                } else {
                    $fatherData = $fatherJson;
                }
                Log::info('✅ SIGNUP: Father data parsed', ['has_data' => !empty($fatherData)]);
            }

            // ✅ Handle mother data (for Guardian role only)
            $motherData = null;
            if ($relationTuteur === '3' && $request->has('mother')) {
                $motherJson = $request->input('mother');
                Log::info('🔵 SIGNUP: Parsing mother data', ['raw' => $motherJson]);
                if (is_string($motherJson)) {
                    $motherData = json_decode($motherJson, true);
                } else {
                    $motherData = $motherJson;
                }
                Log::info('✅ SIGNUP: Mother data parsed', ['has_data' => !empty($motherData)]);
            }

            // Validate mothers data manually (for Father role)
            if (!empty($mothersData)) {
                foreach ($mothersData as $index => $mother) {
                    if (empty($mother['nin']) || empty($mother['nss']) || empty($mother['nom_ar']) || empty($mother['prenom_ar']) || empty($mother['categorie_sociale'])) {
                        return response()->json([
                            'message' => 'فشل في التحقق من البيانات',
                            'errors' => ["mothers.{$index}" => 'جميع حقول الأم مطلوبة']
                        ], 422);
                    }
                    
                    // Validate NIN length (must be exactly 18 digits)
                    $nin = strval($mother['nin']);
                    if (strlen($nin) !== 18 || !ctype_digit($nin)) {
                        return response()->json([
                            'message' => 'فشل في التحقق من البيانات',
                            'errors' => ["mothers.{$index}.nin" => 'الرقم الوطني للأم يجب أن يحتوي على 18 رقمًا بالضبط']
                        ], 422);
                    }
                    
                    // Validate NSS length (must be exactly 12 digits)
                    $nss = strval($mother['nss']);
                    if (strlen($nss) !== 12 || !ctype_digit($nss)) {
                        return response()->json([
                            'message' => 'فشل في التحقق من البيانات',
                            'errors' => ["mothers.{$index}.nss" => 'رقم الضمان الاجتماعي للأم يجب أن يحتوي على 12 رقمًا بالضبط']
                        ], 422);
                    }
                    
                    // Check if mother NIN already exists
                    if (Mother::where('nin', $mother['nin'])->exists()) {
                        return response()->json([
                            'message' => 'فشل في التحقق من البيانات',
                            'errors' => ["mothers.{$index}.nin" => 'الرقم الوطني للأم موجود بالفعل']
                        ], 422);
                    }
                    
                    // Validate categorie_sociale
                    if (!in_array($mother['categorie_sociale'], ['عديم الدخل', 'الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون'])) {
                        return response()->json([
                            'message' => 'فشل في التحقق من البيانات',
                            'errors' => ["mothers.{$index}.categorie_sociale" => 'الفئة الاجتماعية غير صحيحة']
                        ], 422);
                    }
                    
                    // If second category, montant_s is required
                    if ($mother['categorie_sociale'] === 'الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون' && empty($mother['montant_s'])) {
                        return response()->json([
                            'message' => 'فشل في التحقق من البيانات',
                            'errors' => ["mothers.{$index}.montant_s" => 'مبلغ الدخل الشهري مطلوب عند اختيار الفئة الاجتماعية الثانية']
                        ], 422);
                    }
                }
            }

            // Validate father data (for Mother and Guardian roles)
            if ($fatherData) {
                if (empty($fatherData['nin']) || empty($fatherData['nss']) || empty($fatherData['nom_ar']) || empty($fatherData['prenom_ar'])) {
                    return response()->json([
                        'message' => 'فشل في التحقق من البيانات',
                        'errors' => ['father' => 'جميع حقول الأب مطلوبة']
                    ], 422);
                }
                
                $fatherNin = strval($fatherData['nin']);
                if (strlen($fatherNin) !== 18 || !ctype_digit($fatherNin)) {
                    return response()->json([
                        'message' => 'فشل في التحقق من البيانات',
                        'errors' => ['father.nin' => 'الرقم الوطني للأب يجب أن يحتوي على 18 رقمًا بالضبط']
                    ], 422);
                }
                
                $fatherNss = strval($fatherData['nss']);
                if (strlen($fatherNss) !== 12 || !ctype_digit($fatherNss)) {
                    return response()->json([
                        'message' => 'فشل في التحقق من البيانات',
                        'errors' => ['father.nss' => 'رقم الضمان الاجتماعي للأب يجب أن يحتوي على 12 رقمًا بالضبط']
                    ], 422);
                }
                
                // Check if father NIN already exists
                if (Father::where('nin', $fatherData['nin'])->exists()) {
                    return response()->json([
                        'message' => 'فشل في التحقق من البيانات',
                        'errors' => ['father.nin' => 'الرقم الوطني للأب موجود بالفعل']
                    ], 422);
                }
            }

            // Validate mother data (for Guardian role only)
            if ($motherData) {
                if (empty($motherData['nin']) || empty($motherData['nss']) || empty($motherData['nom_ar']) || empty($motherData['prenom_ar'])) {
                    return response()->json([
                        'message' => 'فشل في التحقق من البيانات',
                        'errors' => ['mother' => 'جميع حقول الأم مطلوبة']
                    ], 422);
                }
                
                $motherNin = strval($motherData['nin']);
                if (strlen($motherNin) !== 18 || !ctype_digit($motherNin)) {
                    return response()->json([
                        'message' => 'فشل في التحقق من البيانات',
                        'errors' => ['mother.nin' => 'الرقم الوطني للأم يجب أن يحتوي على 18 رقمًا بالضبط']
                    ], 422);
                }
                
                $motherNss = strval($motherData['nss']);
                if (strlen($motherNss) !== 12 || !ctype_digit($motherNss)) {
                    return response()->json([
                        'message' => 'فشل في التحقق من البيانات',
                        'errors' => ['mother.nss' => 'رقم الضمان الاجتماعي للأم يجب أن يحتوي على 12 رقمًا بالضبط']
                    ], 422);
                }
                
                // Check if mother NIN already exists
                if (Mother::where('nin', $motherData['nin'])->exists()) {
                    return response()->json([
                        'message' => 'فشل في التحقق من البيانات',
                        'errors' => ['mother.nin' => 'الرقم الوطني للأم موجود بالفعل']
                    ], 422);
                }
            }

            Log::info('🔵 SIGNUP: Step 5 - Starting database transaction');
            DB::beginTransaction();
            try {
                Log::info('🔵 SIGNUP: Creating tuteur record', ['nin' => $validated['nin']]);
                $tuteur = Tuteur::create($validated);
                Log::info('✅ SIGNUP: Tuteur created successfully', ['id' => $tuteur->nin]);

                // ✅ Create mothers (for Father role - multiple wives)
                $firstMotherId = null;
                if (!empty($mothersData)) {
                    Log::info('🔵 SIGNUP: Creating mothers', ['count' => count($mothersData)]);
                    foreach ($mothersData as $index => $motherData) {
                        Log::info('🔵 SIGNUP: Processing mother', ['index' => $index, 'nin' => $motherData['nin'] ?? 'N/A']);
                        // Ensure NIN and NSS are exactly the right length (trim and validate)
                        $nin = substr(strval($motherData['nin']), 0, 18);
                        $nss = substr(strval($motherData['nss']), 0, 12);
                        
                        // Double-check lengths before creating
                        if (strlen($nin) !== 18 || strlen($nss) !== 12) {
                            Log::error('❌ SIGNUP: Invalid mother data', [
                                'index' => $index,
                                'nin_length' => strlen($nin),
                                'nss_length' => strlen($nss)
                            ]);
                            throw new \Exception("Invalid mother data: NIN must be 18 digits, NSS must be 12 digits");
                        }
                        
                        $mother = Mother::create([
                            'nin' => $nin,
                            'nss' => $nss,
                            'nom_ar' => $motherData['nom_ar'],
                            'prenom_ar' => $motherData['prenom_ar'],
                            'nom_fr' => $motherData['nom_fr'] ?? null,
                            'prenom_fr' => $motherData['prenom_fr'] ?? null,
                            'categorie_sociale' => $motherData['categorie_sociale'],
                            'montant_s' => $motherData['montant_s'] ?? null,
                            'tuteur_nin' => $tuteur->nin,
                            'date_insertion' => now(),
                        ]);
                        Log::info('✅ SIGNUP: Mother created', ['id' => $mother->id, 'nin' => $mother->nin]);
                        
                        // Set first mother as primary
                        if ($firstMotherId === null) {
                            $firstMotherId = $mother->id;
                        }
                    }
                    
                    // Set first mother as primary mother_id for tuteur
                    if ($firstMotherId) {
                        Log::info('🔵 SIGNUP: Setting primary mother', ['mother_id' => $firstMotherId]);
                        $tuteur->update(['mother_id' => $firstMotherId]);
                        Log::info('✅ SIGNUP: Primary mother set');
                    }
                }

                // ✅ Create father (for Mother and Guardian roles)
                $fatherId = null;
                if ($fatherData) {
                    Log::info('🔵 SIGNUP: Creating father', ['nin' => $fatherData['nin'] ?? 'N/A']);
                    $fatherNin = substr(strval($fatherData['nin']), 0, 18);
                    $fatherNss = substr(strval($fatherData['nss']), 0, 12);
                    
                    if (strlen($fatherNin) === 18 && strlen($fatherNss) === 12) {
                        $father = Father::create([
                            'nin' => $fatherNin,
                            'nss' => $fatherNss,
                            'nom_ar' => $fatherData['nom_ar'],
                            'prenom_ar' => $fatherData['prenom_ar'],
                            'nom_fr' => $fatherData['nom_fr'] ?? null,
                            'prenom_fr' => $fatherData['prenom_fr'] ?? null,
                            'categorie_sociale' => $fatherData['categorie_sociale'] ?? null,
                            'montant_s' => $fatherData['montant_s'] ?? null,
                            'tuteur_nin' => $tuteur->nin,
                            'date_insertion' => now(),
                        ]);
                        $fatherId = $father->id;
                        Log::info('✅ SIGNUP: Father created', ['id' => $father->id, 'nin' => $father->nin]);
                    } else {
                        Log::error('❌ SIGNUP: Invalid father data lengths', [
                            'nin_length' => strlen($fatherNin),
                            'nss_length' => strlen($fatherNss)
                        ]);
                    }
                }

                // ✅ Create mother (for Guardian role only)
                $motherIdForGuardian = null;
                if ($motherData) {
                    Log::info('🔵 SIGNUP: Creating mother (guardian)', ['nin' => $motherData['nin'] ?? 'N/A']);
                    $motherNin = substr(strval($motherData['nin']), 0, 18);
                    $motherNss = substr(strval($motherData['nss']), 0, 12);
                    
                    if (strlen($motherNin) === 18 && strlen($motherNss) === 12) {
                        $mother = Mother::create([
                            'nin' => $motherNin,
                            'nss' => $motherNss,
                            'nom_ar' => $motherData['nom_ar'],
                            'prenom_ar' => $motherData['prenom_ar'],
                            'nom_fr' => $motherData['nom_fr'] ?? null,
                            'prenom_fr' => $motherData['prenom_fr'] ?? null,
                            'categorie_sociale' => $motherData['categorie_sociale'] ?? null,
                            'montant_s' => $motherData['montant_s'] ?? null,
                            'tuteur_nin' => $tuteur->nin,
                            'date_insertion' => now(),
                        ]);
                        $motherIdForGuardian = $mother->id;
                        Log::info('✅ SIGNUP: Mother (guardian) created', ['id' => $mother->id, 'nin' => $mother->nin]);
                    } else {
                        Log::error('❌ SIGNUP: Invalid mother (guardian) data lengths', [
                            'nin_length' => strlen($motherNin),
                            'nss_length' => strlen($motherNss)
                        ]);
                    }
                }

                // ✅ Update tuteur with father_id and/or mother_id based on role
                $updateData = [];
                if ($fatherId) {
                    $updateData['father_id'] = $fatherId;
                }
                if ($motherIdForGuardian) {
                    $updateData['mother_id'] = $motherIdForGuardian;
                }
                if (!empty($updateData)) {
                    Log::info('🔵 SIGNUP: Updating tuteur with parent IDs', $updateData);
                    $tuteur->update($updateData);
                    Log::info('✅ SIGNUP: Tuteur updated with parent IDs');
                }

                Log::info('🔵 SIGNUP: Committing transaction');
                DB::commit();
                Log::info('✅ SIGNUP: Transaction committed successfully');

                Log::info('🎉 SIGNUP: Signup completed successfully', ['nin' => $tuteur->nin]);
                return response()->json([
                    'message' => 'تمت إضافة الولي/الوصي بنجاح',
                    'data' => $tuteur->load('mothers')
                ], 201);
            } catch (\Exception $e) {
                Log::error('❌ SIGNUP: Database transaction failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                DB::rollBack();
                throw $e;
            }

        } catch (ValidationException $e) {
            Log::error('❌ SIGNUP: Validation failed', [
                'errors' => $e->errors(),
            ]);
            return response()->json([
                'message' => 'فشل في التحقق من البيانات',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('❌ SIGNUP: Unexpected error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
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

    // ✅ Update existing tuteur (profile update)
    public function update(Request $request, $nin)
    {
        // Get authenticated tuteur from request
        $authTuteur = $request->user();
        
        if (!$authTuteur || !($authTuteur instanceof Tuteur)) {
            return response()->json(['message' => 'غير مصرح'], 401);
        }
        
        // Ensure tuteur can only update their own profile
        if ($authTuteur->nin !== $nin) {
            return response()->json(['message' => 'غير مصرح بتعديل هذا الملف'], 403);
        }

        $tuteur = Tuteur::find($nin);
        if (!$tuteur) {
            return response()->json(['message' => 'الولي غير موجود'], 404);
        }

        try {
            $validated = $request->validate(
                [
                    'nom_ar' => 'nullable|string|max:50|regex:/^[\p{Arabic}\s\-]+$/u',
                    'prenom_ar' => 'nullable|string|max:50|regex:/^[\p{Arabic}\s\-]+$/u',
                    'nom_fr' => 'nullable|string|max:50|regex:/^[a-zA-Z\s\-]+$/',
                    'prenom_fr' => 'nullable|string|max:50|regex:/^[a-zA-Z\s\-]+$/',
                    'date_naiss' => 'nullable|date',
                    'adresse' => 'nullable|string|max:80',
                    'tel' => 'nullable|string|max:10|regex:/^[0-9]{10}$/',
                    'email' => 'nullable|email|max:255|unique:tuteures,email,' . $nin . ',nin',
                    'num_cni' => 'nullable|string|max:10|unique:tuteures,num_cni,' . $nin . ',nin',
                    'date_cni' => 'nullable|date',
                    'nss' => 'nullable|string|size:12|regex:/^[0-9]{12}$/|unique:tuteures,nss,' . $nin . ',nin',
                    'num_cpt' => 'nullable|string|max:12|unique:tuteures,num_cpt,' . $nin . ',nin',
                    'cle_cpt' => 'nullable|string|max:2',
                    'password' => 'nullable|string|min:8|confirmed',
                ],
                [
                    'nom_ar.max' => 'اللقب بالعربية يجب ألا يتجاوز 50 حرفًا',
                    'nom_ar.regex' => 'اللقب بالعربية يجب أن يحتوي على أحرف عربية فقط',
                    'prenom_ar.max' => 'الاسم بالعربية يجب ألا يتجاوز 50 حرفًا',
                    'prenom_ar.regex' => 'الاسم بالعربية يجب أن يحتوي على أحرف عربية فقط',
                    'nom_fr.max' => 'اللقب باللاتينية يجب ألا يتجاوز 50 حرفًا',
                    'nom_fr.regex' => 'اللقب باللاتينية يجب أن يحتوي على أحرف لاتينية فقط',
                    'prenom_fr.max' => 'الاسم باللاتينية يجب ألا يتجاوز 50 حرفًا',
                    'prenom_fr.regex' => 'الاسم باللاتينية يجب أن يحتوي على أحرف لاتينية فقط',
                    'date_naiss.date' => 'تاريخ الميلاد غير صالح',
                    'adresse.max' => 'العنوان يجب ألا يتجاوز 80 حرفًا',
                    'tel.max' => 'رقم الهاتف يجب ألا يتجاوز 10 أرقام',
                    'tel.regex' => 'رقم الهاتف يجب أن يحتوي على 10 أرقام بالضبط',
                    'email.email' => 'البريد الإلكتروني غير صالح',
                    'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
                    'num_cni.max' => 'رقم بطاقة التعريف يجب ألا يتجاوز 10 أحرف',
                    'date_cni.date' => 'تاريخ إصدار البطاقة غير صالح',
                    'nss.size' => 'رقم الضمان الاجتماعي يجب أن يحتوي على 12 رقمًا بالضبط',
                    'nss.regex' => 'رقم الضمان الاجتماعي يجب أن يحتوي على أرقام فقط',
                    'nss.unique' => 'رقم الضمان الاجتماعي موجود بالفعل',
                    'num_cni.unique' => 'رقم بطاقة التعريف الوطنية موجود بالفعل',
                    'num_cpt.size' => 'رقم الحساب البريدي يجب أن يحتوي على 12 رقمًا بالضبط',
                    'num_cpt.regex' => 'رقم الحساب البريدي يجب أن يحتوي على أرقام فقط',
                    'num_cpt.unique' => 'رقم الحساب البريدي مستخدم بالفعل',
                    'cle_cpt.size' => 'مفتاح الحساب البريدي يجب أن يحتوي على رقمين بالضبط',
                    'cle_cpt.regex' => 'مفتاح الحساب البريدي يجب أن يحتوي على أرقام فقط',
                    'password.min' => 'كلمة المرور يجب أن تحتوي على 8 أحرف على الأقل',
                    'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
                ]
            );

            // ✅ Check NIN globally if changed
            if ($request->has('nin') && !empty(trim($request->nin)) && $request->nin != $tuteur->nin) {
                if (\App\Models\Mother::where('nin', trim($request->nin))->exists() || 
                    \App\Models\Father::where('nin', trim($request->nin))->exists()) {
                    return response()->json([
                        'message' => 'فشل في التحقق من البيانات',
                        'errors' => ['nin' => 'الرقم الوطني موجود بالفعل']
                    ], 422);
                }
            }
            
            // ✅ Check NSS globally if changed
            if ($request->has('nss') && !empty(trim($request->nss)) && $request->nss != $tuteur->nss) {
                if (\App\Models\Mother::where('nss', trim($request->nss))->exists() || 
                    \App\Models\Father::where('nss', trim($request->nss))->exists()) {
                    return response()->json([
                        'message' => 'فشل في التحقق من البيانات',
                        'errors' => ['nss' => 'رقم الضمان الاجتماعي موجود بالفعل']
                    ], 422);
                }
            }
            
            // Note: CNI (num_cni) is only checked in tuteures table via Laravel unique validation rule above
            // Mothers and Fathers tables don't have num_cni column, so no global check needed
            
            // ✅ Validate CCP + CLE together
            // Only validate if CCP or CLE was actually changed from current values
            $ccpChanged = $request->has('num_cpt') && $request->num_cpt != $tuteur->num_cpt;
            $cleChanged = $request->has('cle_cpt') && $request->cle_cpt != $tuteur->cle_cpt;
            
            $hasCcp = !empty($request->num_cpt) && trim($request->num_cpt) !== '';
            $hasCle = !empty($request->cle_cpt) && trim($request->cle_cpt) !== '';
            
            // Only validate if values were changed, or if trying to set new values
            if ($ccpChanged || $cleChanged) {
                // Both must be provided together
                if (!$hasCcp || !$hasCle) {
                    return response()->json([
                        'message' => 'فشل في التحقق من البيانات',
                        'errors' => [
                            'num_cpt' => !$hasCcp ? 'رقم الحساب البريدي مطلوب عند إدخال المفتاح' : null,
                            'cle_cpt' => !$hasCle ? 'مفتاح الحساب البريدي مطلوب عند إدخال الرقم' : null
                        ]
                    ], 422);
                }
                
                $ccp = trim($request->num_cpt);
                $cle = trim($request->cle_cpt);
                
                // Check if they contain only digits
                if (!ctype_digit($ccp)) {
                    return response()->json([
                        'message' => 'فشل في التحقق من البيانات',
                        'errors' => ['num_cpt' => 'رقم الحساب البريدي يجب أن يحتوي على أرقام فقط']
                    ], 422);
                }
                
                if (!ctype_digit($cle)) {
                    return response()->json([
                        'message' => 'فشل في التحقق من البيانات',
                        'errors' => ['cle_cpt' => 'مفتاح الحساب البريدي يجب أن يحتوي على أرقام فقط']
                    ], 422);
                }
                
                // Check exact lengths
                if (strlen($ccp) !== 12) {
                    return response()->json([
                        'message' => 'فشل في التحقق من البيانات',
                        'errors' => ['num_cpt' => 'رقم الحساب البريدي يجب أن يحتوي على 12 رقمًا بالضبط']
                    ], 422);
                }
                
                if (strlen($cle) !== 2) {
                    return response()->json([
                        'message' => 'فشل في التحقق من البيانات',
                        'errors' => ['cle_cpt' => 'مفتاح الحساب البريدي يجب أن يحتوي على رقمين بالضبط']
                    ], 422);
                }
                
                // Validate using RIP algorithm
                if (!self::verifierRIP($ccp, $cle)) {
                    return response()->json([
                        'message' => 'فشل في التحقق من البيانات',
                        'errors' => ['cle_cpt' => 'مفتاح الحساب البريدي غير صحيح. يرجى التحقق من الرقم والمفتاح']
                    ], 422);
                }
            }

            // ✅ Hash password if new one is provided
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                // Remove password from validated data if not provided
                unset($validated['password']);
            }

            // Update only the validated fields
            $tuteur->update($validated);

            return response()->json([
                'message' => 'تم تحديث بيانات الولي بنجاح',
                'data' => $tuteur
            ]);
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

    // ✅ Get mothers for the authenticated tuteur
    public function getMothers(Request $request)
    {
        // Try multiple ways to get the authenticated tuteur
        $tuteur = $request->user();
        
        // If user() returns null, try auth() helper
        if (!$tuteur) {
            $tuteur = auth()->user();
        }
        
        // If still null, try to get from Sanctum token
        if (!$tuteur) {
            $token = $request->bearerToken();
            if ($token) {
                $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
                if ($accessToken && $accessToken->tokenable instanceof Tuteur) {
                    $tuteur = $accessToken->tokenable;
                }
            }
        }
        
        if (!$tuteur || !($tuteur instanceof Tuteur)) {
            return response()->json(['message' => 'غير مصرح'], 401);
        }

        // Get all mothers associated with this tuteur
        $mothers = $tuteur->mothers()->get();
        
        // Return empty array if no mothers found (not an error)
        return response()->json($mothers->isEmpty() ? [] : $mothers);
    }
    
    // ✅ Check if mother NIN exists
    public function checkMotherNIN(Request $request)
    {
        $nin = $request->input('nin');
        if (!$nin || strlen($nin) !== 18) {
            return response()->json(['exists' => false, 'valid' => false]);
        }
        
        $exists = Mother::where('nin', $nin)->exists();
        return response()->json(['exists' => $exists, 'valid' => true]);
    }
    
    // ✅ Check if mother NSS exists
    public function checkMotherNSS(Request $request)
    {
        $nss = $request->input('nss');
        if (!$nss || strlen($nss) !== 12) {
            return response()->json(['exists' => false, 'valid' => false]);
        }
        
        $exists = Mother::where('nss', $nss)->whereNotNull('nss')->exists();
        return response()->json(['exists' => $exists, 'valid' => true]);
    }

    // ✅ Get a single father by ID
    public function getFather($id)
    {
        $father = Father::find($id);
        return $father
            ? response()->json($father)
            : response()->json(['message' => 'Father not found'], 404);
    }

    // ✅ Get a single mother by ID
    public function getMother($id)
    {
        $mother = Mother::find($id);
        return $mother
            ? response()->json($mother)
            : response()->json(['message' => 'Mother not found'], 404);
    }
}
