<?php

namespace App\Http\Controllers;

use App\Models\Tuteur;
use App\Models\Mother;
use App\Models\Father;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
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
                'relation_tuteur' => 'required|in:1,2,3',
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

            // ✅ Get relation_tuteur (already validated, so it's in $validated array)
            $relationTuteur = $validated['relation_tuteur'];

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
