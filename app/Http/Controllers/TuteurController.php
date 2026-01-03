<?php

namespace App\Http\Controllers;

use App\Models\Tuteur;
use App\Models\Mother;
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
        $tuteur = Tuteur::with(['communeResidence', 'communeNaissance'])->find($nin);
        return $tuteur
            ? response()->json($tuteur)
            : response()->json(['message' => 'Tuteur non trouvé'], 404);
    }

    // ✅ Insert new tuteur
   public function store(Request $request)
    {
        Log::info('=== TUTEUR SIGNUP START ===', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'all_input' => $request->all(),
            'has_mothers' => $request->has('mothers'),
            'mothers_raw' => $request->input('mothers'),
        ]);
        
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
            
            Log::info('TUTEUR SIGNUP: Validation passed', [
                'validated_data' => $validated,
                'nin' => $validated['nin'] ?? null,
                'email' => $validated['email'] ?? null,
            ]);

            // ✅ Validate CCP + CLE
            if (!self::verifierRIP($validated['num_cpt'], $validated['cle_cpt'])) {
                Log::warning('TUTEUR SIGNUP: CCP validation failed', [
                    'num_cpt' => $validated['num_cpt'],
                    'cle_cpt' => $validated['cle_cpt'],
                ]);
                return response()->json([
                    'message' => 'خطأ في CCP: الرقم أو المفتاح غير صحيح.'
                ], 422);
            }
            
            Log::info('TUTEUR SIGNUP: CCP validation passed');

            // ✅ Hash password only if provided
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }

            // ✅ Handle mothers data
            $mothersData = [];
            if ($request->has('mothers')) {
                $mothersJson = $request->input('mothers');
                Log::info('TUTEUR SIGNUP: Processing mothers data', [
                    'mothers_json_type' => gettype($mothersJson),
                    'mothers_json' => $mothersJson,
                ]);
                
                if (is_string($mothersJson)) {
                    $mothersData = json_decode($mothersJson, true) ?? [];
                    Log::info('TUTEUR SIGNUP: Decoded mothers JSON', [
                        'decoded' => $mothersData,
                        'json_error' => json_last_error_msg(),
                    ]);
                } else {
                    $mothersData = $mothersJson;
                }
                
                Log::info('TUTEUR SIGNUP: Final mothers data', [
                    'count' => count($mothersData),
                    'data' => $mothersData,
                ]);
            } else {
                Log::info('TUTEUR SIGNUP: No mothers data in request');
            }

            // Validate mothers data manually
            if (!empty($mothersData)) {
                Log::info('TUTEUR SIGNUP: Validating mothers data', ['count' => count($mothersData)]);
                foreach ($mothersData as $index => $mother) {
                    Log::info("TUTEUR SIGNUP: Validating mother {$index}", ['mother_data' => $mother]);
                    
                    if (empty($mother['nin']) || empty($mother['nss']) || empty($mother['nom_ar']) || empty($mother['prenom_ar']) || empty($mother['categorie_sociale'])) {
                        Log::warning("TUTEUR SIGNUP: Mother {$index} missing required fields", ['mother' => $mother]);
                        return response()->json([
                            'message' => 'فشل في التحقق من البيانات',
                            'errors' => ["mothers.{$index}" => 'جميع حقول الأم مطلوبة']
                        ], 422);
                    }
                    
                    // Check if mother NIN already exists
                    if (Mother::where('nin', $mother['nin'])->exists()) {
                        Log::warning("TUTEUR SIGNUP: Mother {$index} NIN already exists", ['nin' => $mother['nin']]);
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

            DB::beginTransaction();
            Log::info('TUTEUR SIGNUP: Starting database transaction');
            try {
                Log::info('TUTEUR SIGNUP: Creating tuteur record', ['data' => $validated]);
                $tuteur = Tuteur::create($validated);
                Log::info('TUTEUR SIGNUP: Tuteur created successfully', ['nin' => $tuteur->nin]);

                // ✅ Create mothers
                $firstMotherId = null;
                if (!empty($mothersData)) {
                    Log::info('TUTEUR SIGNUP: Creating mothers', ['count' => count($mothersData)]);
                    foreach ($mothersData as $index => $motherData) {
                        Log::info("TUTEUR SIGNUP: Creating mother {$index}", ['data' => $motherData]);
                        $mother = Mother::create([
                            'nin' => $motherData['nin'],
                            'nss' => $motherData['nss'],
                            'nom_ar' => $motherData['nom_ar'],
                            'prenom_ar' => $motherData['prenom_ar'],
                            'categorie_sociale' => $motherData['categorie_sociale'],
                            'montant_s' => $motherData['montant_s'] ?? null,
                            'tuteur_nin' => $tuteur->nin,
                            'date_insertion' => now(),
                        ]);
                        Log::info("TUTEUR SIGNUP: Mother {$index} created", ['mother_id' => $mother->id, 'nin' => $mother->nin]);
                        
                        // Set first mother as primary
                        if ($firstMotherId === null) {
                            $firstMotherId = $mother->id;
                            Log::info('TUTEUR SIGNUP: Set first mother as primary', ['mother_id' => $firstMotherId]);
                        }
                    }
                    
                    // Set first mother as primary mother_id for tuteur
                    if ($firstMotherId) {
                        $tuteur->update(['mother_id' => $firstMotherId]);
                        Log::info('TUTEUR SIGNUP: Updated tuteur with mother_id', ['mother_id' => $firstMotherId]);
                    }
                } else {
                    Log::info('TUTEUR SIGNUP: No mothers to create');
                }

                DB::commit();
                Log::info('TUTEUR SIGNUP: Transaction committed successfully', [
                    'tuteur_nin' => $tuteur->nin,
                    'mothers_count' => $tuteur->mothers()->count(),
                ]);

                return response()->json([
                    'message' => 'تمت إضافة الولي/الوصي بنجاح',
                    'data' => $tuteur->load('mothers')
                ], 201);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('TUTEUR SIGNUP: Database transaction failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e;
            }

        } catch (ValidationException $e) {
            Log::warning('TUTEUR SIGNUP: Validation failed', [
                'errors' => $e->errors(),
            ]);
            return response()->json([
                'message' => 'فشل في التحقق من البيانات',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('TUTEUR SIGNUP: Unexpected error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'message' => 'حدث خطأ غير متوقع',
                'error' => $e->getMessage()
            ], 500);
        } finally {
            Log::info('=== TUTEUR SIGNUP END ===');
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
        $tuteur = $request->user();
        if (!$tuteur || !($tuteur instanceof Tuteur)) {
            return response()->json(['message' => 'غير مصرح'], 401);
        }

        $mothers = $tuteur->mothers()->get();
        return response()->json($mothers);
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
}
