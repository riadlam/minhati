<?php

namespace App\Http\Controllers;

use App\Models\Tuteur;
use App\Models\Mother;
use App\Models\Father;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
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
        try {
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
                'situation_familiale' => 'nullable|string|in:متزوج,أرمل,مطلق',
                'nss' => 'nullable|string|size:12|unique:tuteures,nss',
                'adresse' => 'nullable|string|max:80',
                'cats' => 'nullable|string|max:80',
                'montant_s' => 'nullable|numeric',
                'autr_info' => 'nullable|string|max:80|regex:/^[\p{Arabic}\s\-]+$/u',
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
                'biometric_id' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'biometric_id_back' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'Certificate_of_none_income' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'Certificate_of_non_affiliation_to_social_security' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'crossed_ccp' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'salary_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
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
                'autr_info.regex' => 'معلومات أخرى متعلقة بالحالة الاجتماعية يجب أن تحتوي على أحرف عربية فقط',
                'email.email' => 'البريد الإلكتروني غير صالح',
                'password.min' => 'كلمة المرور يجب أن تحتوي على 8 أحرف على الأقل',
                'commune_naiss.exists' => 'رمز بلدية الميلاد غير موجود في قاعدة البيانات',
                'situation_familiale.in' => 'الحالة العائلية يجب أن تكون: متزوج، أرمل، أو مطلق',
                'code_commune.exists' => 'رمز بلدية الإقامة غير موجود في قاعدة البيانات',
                'biometric_id.required' => 'بطاقة الهوية البيومترية (الوجه الأمامي) مطلوبة',
                'biometric_id.file' => 'بطاقة الهوية البيومترية (الوجه الأمامي) يجب أن تكون ملف',
                'biometric_id.mimes' => 'بطاقة الهوية البيومترية (الوجه الأمامي) يجب أن تكون بصيغة PDF, JPG, JPEG, أو PNG',
                'biometric_id.max' => 'حجم بطاقة الهوية البيومترية (الوجه الأمامي) يجب ألا يتجاوز 5 ميجابايت',
                'biometric_id_back.required' => 'بطاقة الهوية البيومترية (الوجه الخلفي) مطلوبة',
                'biometric_id_back.file' => 'بطاقة الهوية البيومترية (الوجه الخلفي) يجب أن تكون ملف',
                'biometric_id_back.mimes' => 'بطاقة الهوية البيومترية (الوجه الخلفي) يجب أن تكون بصيغة PDF, JPG, JPEG, أو PNG',
                'biometric_id_back.max' => 'حجم بطاقة الهوية البيومترية (الوجه الخلفي) يجب ألا يتجاوز 5 ميجابايت',
                'Certificate_of_none_income.file' => 'شهادة عدم الدخل يجب أن تكون ملف',
                'Certificate_of_none_income.mimes' => 'شهادة عدم الدخل يجب أن تكون بصيغة PDF, JPG, JPEG, أو PNG',
                'Certificate_of_none_income.max' => 'حجم شهادة عدم الدخل يجب ألا يتجاوز 5 ميجابايت',
                'Certificate_of_non_affiliation_to_social_security.file' => 'شهادة عدم الانتساب للضمان الاجتماعي يجب أن تكون ملف',
                'Certificate_of_non_affiliation_to_social_security.mimes' => 'شهادة عدم الانتساب للضمان الاجتماعي يجب أن تكون بصيغة PDF, JPG, JPEG, أو PNG',
                'Certificate_of_non_affiliation_to_social_security.max' => 'حجم شهادة عدم الانتساب للضمان الاجتماعي يجب ألا يتجاوز 5 ميجابايت',
                'crossed_ccp.file' => 'صك بريدي مشطوب يجب أن يكون ملف',
                'crossed_ccp.mimes' => 'صك بريدي مشطوب يجب أن يكون بصيغة PDF, JPG, JPEG, أو PNG',
                'crossed_ccp.max' => 'حجم صك بريدي مشطوب يجب ألا يتجاوز 5 ميجابايت',
                'salary_certificate.file' => 'شهادة الراتب يجب أن تكون ملف',
                'salary_certificate.mimes' => 'شهادة الراتب يجب أن تكون بصيغة PDF, JPG, JPEG, أو PNG',
                'salary_certificate.max' => 'حجم شهادة الراتب يجب ألا يتجاوز 5 ميجابايت',
            ]);
            
            // Validate conditional file uploads based on social category
            $cats = $validated['cats'] ?? null;
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
                if (!$request->hasFile('salary_certificate')) {
                    return response()->json([
                        'message' => 'فشل في التحقق من البيانات',
                        'errors' => ['salary_certificate' => 'شهادة الراتب مطلوبة عند اختيار "الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون"']
                    ], 422);
                }
            }

            // ✅ Check NIN globally across all tables
            if (\App\Models\Mother::where('nin', $validated['nin'])->exists() || 
                \App\Models\Father::where('nin', $validated['nin'])->exists()) {
                return response()->json([
                    'message' => 'فشل في التحقق من البيانات',
                    'errors' => ['nin' => 'الرقم الوطني موجود بالفعل']
                ], 422);
            }
            
            // ✅ Check NSS globally if provided
            if (!empty($validated['nss'])) {
                // Validate NSS check digit using SiNSScle algorithm
                if (!self::validateNSS($validated['nss'])) {
                    return response()->json([
                        'message' => 'فشل في التحقق من البيانات',
                        'errors' => ['nss' => 'رقم الضمان الاجتماعي غير صحيح. يرجى التحقق من الرقم']
                    ], 422);
                }
                
                if (\App\Models\Mother::where('nss', $validated['nss'])->exists() || 
                    \App\Models\Father::where('nss', $validated['nss'])->exists()) {
                    return response()->json([
                        'message' => 'فشل في التحقق من البيانات',
                        'errors' => ['nss' => 'رقم الضمان الاجتماعي موجود بالفعل']
                    ], 422);
                }
            }
            
            // Note: CNI (num_cni) is only checked in tuteures table via Laravel unique validation rule above
            // Mothers and Fathers tables don't have num_cni column, so no global check needed

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

            // ✅ Handle file uploads securely
            $fileFields = [
                'biometric_id',
                'biometric_id_back',
                'Certificate_of_none_income',
                'Certificate_of_non_affiliation_to_social_security',
                'crossed_ccp',
                'salary_certificate'
            ];
            
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
                    $path = $file->storeAs("tuteur_docs/{$field}", $secureFilename, 'local');
                    
                    // Add path to validated data
                    $validated[$field] = $path;
                }
            }

            // ✅ Get relation_tuteur (nullable - may not be provided during signup)
            $relationTuteur = $validated['relation_tuteur'] ?? null;

            // ✅ Handle mothers data (for Father role - multiple wives)
            $mothersData = [];
            if ($relationTuteur === '1' && $request->has('mothers')) {
                $mothersJson = $request->input('mothers');
                if (is_string($mothersJson)) {
                    $mothersData = json_decode($mothersJson, true) ?? [];
                } else {
                    $mothersData = $mothersJson;
                }
            }

            // ✅ Handle father data (for Mother and Guardian roles)
            $fatherData = null;
            if (in_array($relationTuteur, ['2', '3']) && $request->has('father')) {
                $fatherJson = $request->input('father');
                if (is_string($fatherJson)) {
                    $fatherData = json_decode($fatherJson, true);
                } else {
                    $fatherData = $fatherJson;
                }
            }

            // ✅ Handle mother data (for Guardian role only)
            $motherData = null;
            if ($relationTuteur === '3' && $request->has('mother')) {
                $motherJson = $request->input('mother');
                if (is_string($motherJson)) {
                    $motherData = json_decode($motherJson, true);
                } else {
                    $motherData = $motherJson;
                }
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
                    
                    // Validate NSS check digit using SiNSScle algorithm
                    if (!self::validateNSS($nss)) {
                        return response()->json([
                            'message' => 'فشل في التحقق من البيانات',
                            'errors' => ["mothers.{$index}.nss" => 'رقم الضمان الاجتماعي للأم غير صحيح. يرجى التحقق من الرقم']
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
                
                // Validate NSS check digit using SiNSScle algorithm
                if (!self::validateNSS($fatherNss)) {
                    return response()->json([
                        'message' => 'فشل في التحقق من البيانات',
                        'errors' => ['father.nss' => 'رقم الضمان الاجتماعي للأب غير صحيح. يرجى التحقق من الرقم']
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
                
                // Validate NSS check digit using SiNSScle algorithm
                if (!self::validateNSS($motherNss)) {
                    return response()->json([
                        'message' => 'فشل في التحقق من البيانات',
                        'errors' => ['mother.nss' => 'رقم الضمان الاجتماعي للأم غير صحيح. يرجى التحقق من الرقم']
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

            DB::beginTransaction();
            try {
            $tuteur = Tuteur::create($validated);

                // ✅ Create mothers (for Father role - multiple wives)
                $firstMotherId = null;
                if (!empty($mothersData)) {
                    foreach ($mothersData as $index => $motherData) {
                        // Ensure NIN and NSS are exactly the right length (trim and validate)
                        $nin = substr(strval($motherData['nin']), 0, 18);
                        $nss = substr(strval($motherData['nss']), 0, 12);
                        
                        // Double-check lengths before creating
                        if (strlen($nin) !== 18 || strlen($nss) !== 12) {
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
                        
                        // Set first mother as primary
                        if ($firstMotherId === null) {
                            $firstMotherId = $mother->id;
                        }
                    }
                    
                    // Set first mother as primary mother_id for tuteur
                    if ($firstMotherId) {
                        $tuteur->update(['mother_id' => $firstMotherId]);
                    }
                }

                // ✅ Create father (for Mother and Guardian roles)
                $fatherId = null;
                if ($fatherData) {
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
                    }
                }

                // ✅ Create mother (for Guardian role only)
                $motherIdForGuardian = null;
                if ($motherData) {
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
                    $tuteur->update($updateData);
                }

                DB::commit();

            return response()->json([
                'message' => 'تمت إضافة الولي/الوصي بنجاح',
                    'data' => $tuteur->load('mothers')
            ], 201);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

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

        // Only validate email unique when it is actually being changed (avoid "already taken" for own email)
        $emailRules = ['nullable', 'email', 'max:255'];
        $requestEmail = $request->input('email');
        $currentEmail = $tuteur->email ?? '';
        if ($requestEmail !== null && $requestEmail !== '' && trim((string)$requestEmail) !== trim((string)$currentEmail)) {
            $emailRules[] = Rule::unique('tuteures', 'email')->ignore($tuteur->nin, 'nin');
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
                    'autr_info' => 'nullable|string|max:80|regex:/^[\p{Arabic}\s\-]+$/u',
                    'tel' => 'nullable|string|max:10|regex:/^[0-9]{10}$/',
                    'email' => $emailRules,
                    'num_cni' => 'nullable|string|max:10|unique:tuteures,num_cni,' . $nin . ',nin',
                    'date_cni' => 'nullable|date',
                    'nss' => 'nullable|string|size:12|regex:/^[0-9]{12}$/|unique:tuteures,nss,' . $nin . ',nin',
                    'num_cpt' => 'nullable|string|max:12|unique:tuteures,num_cpt,' . $nin . ',nin',
                    'cle_cpt' => 'nullable|string|max:2',
                    'situation_familiale' => 'nullable|string|in:متزوج,أرمل,مطلق',
                    'nbr_enfants_scolarise' => 'nullable|integer|min:0',
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
                    'autr_info.regex' => 'معلومات أخرى متعلقة بالحالة الاجتماعية يجب أن تحتوي على أحرف عربية فقط',
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
                    'situation_familiale.in' => 'الحالة العائلية يجب أن تكون: متزوج، أرمل، أو مطلق',
                    'nbr_enfants_scolarise.min' => 'عدد الأطفال المتمدرسين يجب أن يكون 0 أو أكثر',
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
                $nss = trim($request->nss);
                
                // Validate NSS check digit using SiNSScle algorithm
                if (!self::validateNSS($nss)) {
                    return response()->json([
                        'message' => 'فشل في التحقق من البيانات',
                        'errors' => ['nss' => 'رقم الضمان الاجتماعي غير صحيح. يرجى التحقق من الرقم']
                    ], 422);
                }
                
                if (\App\Models\Mother::where('nss', $nss)->exists() || 
                    \App\Models\Father::where('nss', $nss)->exists()) {
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
            return response()->json(['exists' => false, 'valid' => false, 'mother' => null]);
        }
        
        $mother = Mother::where('nin', $nin)->first();
        return response()->json([
            'exists' => $mother !== null,
            'valid' => true,
            'mother' => $mother ? [
                'id' => $mother->id,
                'nin' => $mother->nin,
                'nss' => $mother->nss,
                'nom_ar' => $mother->nom_ar,
                'prenom_ar' => $mother->prenom_ar,
                'nom_fr' => $mother->nom_fr,
                'prenom_fr' => $mother->prenom_fr,
                'tuteur_nin' => $mother->tuteur_nin,
            ] : null
        ]);
    }
    
    // ✅ Check if tuteur exists by NIN (for admin add student page)
    public function checkTuteurExists(Request $request)
    {
        $nin = $request->input('nin');
        if (!$nin || strlen($nin) !== 18) {
            return response()->json(['exists' => false, 'valid' => false, 'tuteur' => null]);
        }
        
        $tuteur = Tuteur::where('nin', $nin)->first();
        return response()->json([
            'exists' => $tuteur !== null,
            'valid' => true,
            'tuteur' => $tuteur ? [
                'nin' => $tuteur->nin,
                'nom_ar' => $tuteur->nom_ar,
                'prenom_ar' => $tuteur->prenom_ar,
                'nom_fr' => $tuteur->nom_fr,
                'prenom_fr' => $tuteur->prenom_fr,
                'sexe' => $tuteur->sexe,
            ] : null
        ]);
    }
    
    // ✅ Check if father NIN exists
    public function checkFatherNIN(Request $request)
    {
        $nin = $request->input('nin');
        if (!$nin || strlen($nin) !== 18) {
            return response()->json(['exists' => false, 'valid' => false, 'father' => null]);
        }
        
        $father = Father::where('nin', $nin)->first();
        return response()->json([
            'exists' => $father !== null,
            'valid' => true,
            'father' => $father ? [
                'id' => $father->id,
                'nin' => $father->nin,
                'nss' => $father->nss,
                'nom_ar' => $father->nom_ar,
                'prenom_ar' => $father->prenom_ar,
                'nom_fr' => $father->nom_fr,
                'prenom_fr' => $father->prenom_fr,
                'tuteur_nin' => $father->tuteur_nin,
            ] : null
        ]);
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
