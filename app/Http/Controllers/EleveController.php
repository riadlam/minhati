<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Mpdf\Mpdf;
use Carbon\Carbon;

class EleveController extends Controller
{
    public function index()
    {
        return response()->json(
            Eleve::with(['tuteur', 'etablissement', 'commune', 'mother', 'father'])->get()
        );
    }

    public function show($num_scolaire)
    {
        $eleve = Eleve::where('num_scolaire', $num_scolaire)
            ->with(['tuteur', 'etablissement', 'communeResidence', 'communeNaissance', 'mother', 'father'])
            ->first();
        
        if (!$eleve) {
            return response()->json(['message' => 'Not found'], 404);
        }
        
        return response()->json($eleve);
    }

    public function edit($num_scolaire)
    {
        $eleve = Eleve::where('num_scolaire', $num_scolaire)
            ->with(['tuteur', 'etablissement', 'communeResidence', 'communeNaissance', 'mother', 'father'])
            ->first();
        
        if (!$eleve) {
            abort(404, 'Student not found');
        }
        
        return response()->json($eleve);
    }

    public function store(Request $request)
    {
        // Get tuteur from token (set by ApiTuteurAuth middleware)
        $tuteur = $request->user();
        
        // Fallback to auth() helper if $request->user() doesn't work
        if (!$tuteur) {
            $tuteur = auth()->user();
        }

        if (!$tuteur || !($tuteur instanceof \App\Models\Tuteur)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Token required.',
                'error' => 'Authentication required'
            ], 401);
        }
        
        $tuteurNin = $tuteur->nin;
        
        // Get selected relation_tuteur from form (1=Father, 2=Mother, 3=Guardian)
        $selectedRelation = (int)($request->input('relation_tuteur') ?? 0);
        if (!in_array($selectedRelation, [1, 2, 3])) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في التحقق من البيانات',
                'errors' => [
                    'relation_tuteur' => ['صفة طالب المنحة مطلوبة ويجب أن تكون 1 (الولي - الأب)، 2 (الولي - الأم)، أو 3 (وصي)']
                ]
            ], 422);
        }

        // 🔹 Step 1: Validate incoming form data with Arabic error messages
        $rules = [
            'num_scolaire'   => 'required|string|size:16|unique:eleves,num_scolaire|regex:/^\d+$/',
            'nom'            => 'required|string|max:50|regex:/^[ء-ي\s]+$/',
            'prenom'         => 'required|string|max:50|regex:/^[ء-ي\s]+$/',
            'nom_pere'       => 'required|string|max:50|regex:/^[ء-ي\s]+$/',
            'prenom_pere'    => 'required|string|max:50|regex:/^[ء-ي\s]+$/',
            'date_naiss'     => 'required|date|before:today',
            'presume'        => 'nullable|string|in:0,1',
            'commune_naiss'  => 'nullable|string|size:5',
            'num_act'        => 'nullable|string|max:5',
            'bis'            => 'nullable|string|max:1',
            'ecole'          => 'required|string|max:30',
            'niveau'         => 'nullable|string|max:30',
            'classe_scol'    => 'nullable|string|max:30',
            'sexe'           => 'required|string|in:ذكر,أنثى',
            'handicap'       => 'required|string|in:0,1',
            'handicap_nature'=> 'nullable|string|max:150|required_if:handicap,1',
            'handicap_percentage' => 'nullable|numeric|min:0|max:100|required_if:handicap,1',
            'relation_tuteur'=> 'required|integer|in:1,2,3',
            'commune_id'     => 'required|string|size:5',
        ];

        // Conditional validation based on relation_tuteur
        if ($selectedRelation === 1) {
            // الولي (الأب): mother_id is required
            $rules['mother_id'] = 'required|exists:mothers,id';
            $rules['father_id'] = 'nullable|exists:fathers,id';
        } elseif ($selectedRelation === 2) {
            // الولي (الأم): father_id is required
            $rules['father_id'] = 'required|exists:fathers,id';
            $rules['mother_id'] = 'nullable|exists:mothers,id';
        } elseif ($selectedRelation === 3) {
            // وصي: both mother_id and father_id are required
            $rules['mother_id'] = 'required|exists:mothers,id';
            $rules['father_id'] = 'required|exists:fathers,id';
        }

        $messages = [
            // num_scolaire
            'num_scolaire.required' => 'الرقم التعريفي المدرسي مطلوب',
            'num_scolaire.size' => 'الرقم التعريفي المدرسي يجب أن يكون 16 رقمًا بالضبط',
            'num_scolaire.unique' => 'الرقم التعريفي المدرسي موجود مسبقًا',
            'num_scolaire.regex' => 'الرقم التعريفي المدرسي يجب أن يحتوي على أرقام فقط',
            
            // nom
            'nom.required' => 'لقب التلميذ بالعربية مطلوب',
            'nom.max' => 'لقب التلميذ بالعربية يجب ألا يتجاوز 50 حرفًا',
            'nom.regex' => 'لقب التلميذ بالعربية يجب أن يحتوي على أحرف عربية فقط',
            
            // prenom
            'prenom.required' => 'اسم التلميذ بالعربية مطلوب',
            'prenom.max' => 'اسم التلميذ بالعربية يجب ألا يتجاوز 50 حرفًا',
            'prenom.regex' => 'اسم التلميذ بالعربية يجب أن يحتوي على أحرف عربية فقط',
            
            // nom_pere
            'nom_pere.required' => 'لقب الأب/الأم/الوصي بالعربية مطلوب',
            'nom_pere.max' => 'لقب الأب/الأم/الوصي بالعربية يجب ألا يتجاوز 50 حرفًا',
            'nom_pere.regex' => 'لقب الأب/الأم/الوصي بالعربية يجب أن يحتوي على أحرف عربية فقط',
            
            // prenom_pere
            'prenom_pere.required' => 'اسم الأب/الأم/الوصي بالعربية مطلوب',
            'prenom_pere.max' => 'اسم الأب/الأم/الوصي بالعربية يجب ألا يتجاوز 50 حرفًا',
            'prenom_pere.regex' => 'اسم الأب/الأم/الوصي بالعربية يجب أن يحتوي على أحرف عربية فقط',
            
            // date_naiss
            'date_naiss.required' => 'تاريخ الميلاد مطلوب',
            'date_naiss.date' => 'تاريخ الميلاد يجب أن يكون تاريخًا صحيحًا',
            'date_naiss.before' => 'تاريخ الميلاد يجب أن يكون في الماضي',
            
            // ecole
            'ecole.required' => 'المؤسسة التعليمية مطلوبة',
            'ecole.max' => 'المؤسسة التعليمية يجب ألا تتجاوز 30 حرفًا',
            
            // sexe
            'sexe.required' => 'الجنس مطلوب',
            'sexe.in' => 'الجنس يجب أن يكون ذكر أو أنثى',
            
            // handicap
            'handicap.required' => 'حقل الإعاقة مطلوب',
            'handicap.in' => 'حقل الإعاقة يجب أن يكون نعم أو لا',
            
            // handicap_nature
            'handicap_nature.required_if' => 'طبيعة الإعاقة مطلوبة عند اختيار وجود إعاقة',
            'handicap_nature.max' => 'طبيعة الإعاقة يجب ألا تتجاوز 150 حرفًا',
            
            // handicap_percentage
            'handicap_percentage.required_if' => 'نسبة الإعاقة مطلوبة عند اختيار وجود إعاقة',
            'handicap_percentage.numeric' => 'نسبة الإعاقة يجب أن تكون رقماً',
            'handicap_percentage.min' => 'نسبة الإعاقة يجب أن تكون 0 أو أكثر',
            'handicap_percentage.max' => 'نسبة الإعاقة يجب ألا تتجاوز 100',
            
            // relation_tuteur
            'relation_tuteur.required' => 'صفة طالب المنحة مطلوبة',
            'relation_tuteur.in' => 'صفة طالب المنحة يجب أن تكون 1 (الولي - الأب)، 2 (الولي - الأم)، أو 3 (وصي)',
            
            // commune_id
            'commune_id.required' => 'البلدية مطلوبة',
            'commune_id.size' => 'رمز البلدية يجب أن يكون 5 أحرف',
            
            // mother_id
            'mother_id.required' => 'الأم مطلوبة',
            'mother_id.exists' => 'الأم المحددة غير موجودة',
            
            // father_id
            'father_id.required' => 'الأب مطلوب',
            'father_id.exists' => 'الأب المحدد غير موجود',
        ];

        try {
            $validated = $request->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في التحقق من البيانات',
                'errors' => $e->errors()
            ], 422);
        }

        // Additional age validation (must be >= 4 years)
        if (isset($validated['date_naiss']) && $validated['date_naiss']) {
            try {
                $birthDate = Carbon::parse($validated['date_naiss']);
                $age = $birthDate->diffInYears(Carbon::now());
                if ($age < 4) {
                    return response()->json([
                        'success' => false,
                        'message' => 'خطأ في التحقق من البيانات',
                        'errors' => [
                            'date_naiss' => ['عمر التلميذ يجب أن يكون 4 سنوات على الأقل']
                        ]
                    ], 422);
                }
            } catch (\Exception $e) {
                // Invalid date format (should be caught by validation, but just in case)
                return response()->json([
                    'success' => false,
                    'message' => 'خطأ في التحقق من البيانات',
                    'errors' => [
                        'date_naiss' => ['تاريخ الميلاد غير صحيح']
                    ]
                ], 422);
            }
        }

        // 🔹 Step 2: Map form field names → DB column names
        $data = [
            'num_scolaire'   => $validated['num_scolaire'],
            'nom'            => $validated['nom'],
            'prenom'         => $validated['prenom'],
            'date_naiss'     => $validated['date_naiss'] ?? null,
            'presume'        => $validated['presume'] ?? '0',
            'commune_naiss'  => $validated['commune_naiss'] ?? null,
            'num_act'        => $validated['num_act'] ?? null,
            'bis'            => $validated['bis'] ?? '0',
            'code_etabliss'  => $validated['ecole'] ?? null,
            'niv_scol'       => $validated['niveau'] ?? null,
            'classe_scol'    => $validated['classe_scol'] ?? null,
            'sexe'           => $validated['sexe'] ?? null,
            'handicap'       => $validated['handicap'] ?? '0',
            'handicap_nature'=> $validated['handicap_nature'] ?? null,
            'handicap_percentage' => $validated['handicap_percentage'] ?? null,
            'relation_tuteur'=> $selectedRelation, // Use selected relation from form
            'code_commune'   => $validated['commune_id'] ?? null, // Use commune from form (where school is located)
            'mother_id'      => $validated['mother_id'] ?? null,
            'father_id'      => $validated['father_id'] ?? null,
            'etat_das'       => 'en_cours',
            'etat_final'     => 'en_cours',
            'dossier_depose' => 'non',
            'code_tuteur'    => $tuteurNin,
        ];

        // 🔹 Step 3: Insert student
        $eleve = Eleve::create($data);

        return response()->json($eleve, 201);
    }



    public function update(Request $request, $num_scolaire)
    {
        // Get tuteur from token only (no session fallback)
        $tuteur = $request->user();
        
        if (!$tuteur || !($tuteur instanceof \App\Models\Tuteur)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Token required.'
            ], 401);
        }
        
        $tuteurNin = $tuteur->nin;

        $eleve = Eleve::where('num_scolaire', $num_scolaire)
            ->where('code_tuteur', $tuteurNin)
            ->first();
        
        if (!$eleve) {
            return response()->json(['message' => 'Not found'], 404);
        }

        // Validate incoming form data
        $validated = $request->validate([
            'nom'            => 'required|string|max:50',
            'prenom'         => 'required|string|max:50',
            'date_naiss'     => 'nullable|date',
            'presume'        => 'nullable|string|in:0,1',
            'commune_naiss'  => 'nullable|string|max:5',
            'num_act'        => 'nullable|string|max:5',
            'bis'            => 'nullable|string|max:1',
            'ecole'          => 'nullable|string|max:30',
            'niveau'         => 'nullable|string|max:30',
            'classe_scol'    => 'nullable|string|max:30',
            'sexe'           => 'nullable|string|max:4',
            'handicap'       => 'nullable|string|in:0,1',
            'handicap_nature'=> 'nullable|string|max:150|required_if:handicap,1',
            'handicap_percentage' => 'nullable|numeric|min:0|max:100|required_if:handicap,1',
            'relation_tuteur'=> 'nullable|integer|in:1,2,3',
            'mother_id'      => 'nullable|exists:mothers,id',
            'father_id'      => 'nullable|exists:fathers,id',
            'commune_id'     => 'nullable|string|max:5', // Commune is optional for updates (already set on creation)
        ]);

        // Map form field names → DB column names
        $data = [
            'nom'            => $validated['nom'],
            'prenom'         => $validated['prenom'],
            'date_naiss'     => $validated['date_naiss'] ?? null,
            'presume'        => $validated['presume'] ?? '0',
            'commune_naiss'  => $validated['commune_naiss'] ?? null,
            'num_act'        => $validated['num_act'] ?? null,
            'bis'            => $validated['bis'] ?? '0',
            'code_etabliss'  => $validated['ecole'] ?? null,
            'niv_scol'       => $validated['niveau'] ?? null,
            'classe_scol'    => $validated['classe_scol'] ?? null,
            'sexe'           => $validated['sexe'] ?? null,
            'handicap'       => $validated['handicap'] ?? '0',
            'handicap_nature'=> $validated['handicap_nature'] ?? null,
            'handicap_percentage' => $validated['handicap_percentage'] ?? null,
            'relation_tuteur'=> isset($validated['relation_tuteur']) ? (int)$validated['relation_tuteur'] : null,
            'mother_id'      => $validated['mother_id'] ?? null,
            'father_id'      => $validated['father_id'] ?? null,
            'code_commune'   => $validated['commune_id'] ?? $eleve->code_commune, // Use commune from form or keep existing
        ];

        $eleve->update($data);
        
        // Reload relationships for response
        $eleve->load(['mother', 'father', 'tuteur', 'etablissement', 'communeResidence', 'communeNaissance']);
        
        return response()->json([
            'success' => true,
            'message' => 'تم تحديث معلومات التلميذ بنجاح',
            'data' => $eleve
        ]);
    }

    public function destroy(Request $request, $num_scolaire)
    {
        // Get tuteur from token only (no session fallback)
        $tuteur = $request->user();
        
        if (!$tuteur || !($tuteur instanceof \App\Models\Tuteur)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Token required.'
            ], 401);
        }
        
        $tuteurNin = $tuteur->nin;

        $eleve = Eleve::where('num_scolaire', $num_scolaire)
            ->where('code_tuteur', $tuteurNin)
            ->first();
        
        if (!$eleve) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $eleve->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    public function byTuteur($nin)
    {
        $eleves = Eleve::where('code_tuteur', $nin)
            ->with(['etablissement', 'communeResidence', 'communeNaissance', 'mother', 'father'])
            ->get();

        // Return empty array instead of 404 if no eleves found
        return response()->json($eleves);
    }

    public function checkMatricule($matricule)
    {
        $exists = Eleve::where('num_scolaire', $matricule)->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Matricule déjà utilisé' : 'Matricule disponible'
        ]);
    }

    /**
     * Generate and save PDF to storage (helper method)
     */
    private function generateAndSaveIstimara($num_scolaire, Request $request)
    {
        \Log::info('generateAndSaveIstimara: Starting for num_scolaire: ' . $num_scolaire);
        
        // Get tuteur from token only (no session fallback)
        $tuteur = $request->user();
        
        if (!$tuteur || !($tuteur instanceof \App\Models\Tuteur)) {
            \Log::error('generateAndSaveIstimara: Unauthorized - Token required');
            throw new \Exception('Unauthorized: Token required');
        }
        
        $tuteurNin = $tuteur->nin;

        \Log::info('generateAndSaveIstimara: Tuteur NIN: ' . $tuteurNin);

        $eleve = Eleve::with([
            'tuteur.communeResidence.wilaya',
            'tuteur.communeNaissance.wilaya',
            'tuteur.communeCni.wilaya',
            'etablissement.commune.wilaya',
            'communeResidence.wilaya',
            'communeNaissance.wilaya',
            'mother',
            'father'
        ])
        ->where('num_scolaire', $num_scolaire)
        ->where('code_tuteur', $tuteurNin)
        ->first();
        
        // Log relation_tuteur for debugging
        if ($eleve) {
            \Log::info('generateAndSaveIstimara: relation_tuteur value: ' . ($eleve->relation_tuteur ?? 'NULL'));
        }

        if (!$eleve) {
            \Log::error('generateAndSaveIstimara: Student not found');
            throw new \Exception('Student not found');
        }

        \Log::info('generateAndSaveIstimara: Student found, rendering HTML...');

        $html = view('pdf.istimara', compact('eleve'))->render();
        \Log::info('generateAndSaveIstimara: HTML rendered, length: ' . strlen($html));

        $tempDir = storage_path('app/temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
            \Log::info('generateAndSaveIstimara: Created temp directory: ' . $tempDir);
        }

        \Log::info('generateAndSaveIstimara: Creating mPDF instance...');
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'default_font' => 'dejavusans',
            'tempDir' => $tempDir,
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 16,
            'margin_bottom' => 16,
            'margin_header' => 9,
            'margin_footer' => 9,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'useSubstitutions' => true,
            'simpleTables' => true,
            'shrink_tables_to_fit' => 1
        ]);

        $mpdf->SetDirectionality('rtl');
        \Log::info('generateAndSaveIstimara: Writing HTML to PDF...');
        $mpdf->WriteHTML($html, 0);
        \Log::info('generateAndSaveIstimara: Generating PDF content...');
        $pdfContent = $mpdf->Output('', 'S');
        \Log::info('generateAndSaveIstimara: PDF content generated, size: ' . strlen($pdfContent) . ' bytes');

        // Verify it's a valid PDF
        if (substr($pdfContent, 0, 4) !== '%PDF') {
            \Log::error('generateAndSaveIstimara: Invalid PDF generated! First 50 chars: ' . substr($pdfContent, 0, 50));
            throw new \Exception('Failed to generate valid PDF');
        }

        // Store PDF in storage/app/public/istimara directory
        $storagePath = storage_path('app/public/istimara');
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
            \Log::info('generateAndSaveIstimara: Created storage directory: ' . $storagePath);
        }

        $filename = "istimara_{$num_scolaire}.pdf";
        $filePath = $storagePath . '/' . $filename;
        \Log::info('generateAndSaveIstimara: Saving PDF to: ' . $filePath);
        file_put_contents($filePath, $pdfContent);
        \Log::info('generateAndSaveIstimara: PDF saved, file size: ' . filesize($filePath) . ' bytes');

        // Update eleve record with PDF URL
        $pdfUrl = "/storage/istimara/" . $filename;
        $eleve->istimara = $pdfUrl;
        $eleve->save();
        \Log::info('generateAndSaveIstimara: Eleve record updated with PDF URL: ' . $pdfUrl);

        return $filePath;
    }

    public function generateIstimara(Request $request, $num_scolaire)
    {
        \Log::info('Generate Istimara PDF called for: ' . $num_scolaire);
        try {
            $filePath = $this->generateAndSaveIstimara($num_scolaire, $request);
            $filename = basename($filePath);
            
            \Log::info('PDF generated and saved: ' . $filePath);
            
            // Clear output buffers
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            
            // Return PDF directly as download
            return response()->download(
                $filePath,
                $filename,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"'
                ]
            );
        } catch (\Exception $e) {
            \Log::error('Generate Istimara Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            abort(500, 'Error generating PDF: ' . $e->getMessage());
        }
    }

    public function viewIstimara($num_scolaire)
    {
        // No authentication checks - public access
        try {
            // PDF filename
            $filename = "istimara_{$num_scolaire}.pdf";
            $pdfPath = storage_path('app/public/istimara/' . $filename);
            
            // Check if regenerate parameter is set or if PDF doesn't exist
            $forceRegenerate = request()->has('regenerate');
            
            if ($forceRegenerate && file_exists($pdfPath)) {
                \Log::info('viewIstimara: Force regenerating PDF for: ' . $num_scolaire);
                @unlink($pdfPath); // Delete old file
            }
            
            // Check if PDF exists, if not generate it
            if (!file_exists($pdfPath)) {
                \Log::info('viewIstimara: PDF not found, generating new one for: ' . $num_scolaire);
                try {
                    $this->generateAndSaveIstimaraForUser($num_scolaire);
                    // Reload path after generation
                    $pdfPath = storage_path('app/public/istimara/' . $filename);
                } catch (\Exception $genError) {
                    \Log::error('viewIstimara: Error generating PDF: ' . $genError->getMessage());
                    abort(500, 'Error generating PDF: ' . $genError->getMessage());
                }
            }

            // Verify it's a valid PDF file
            $firstBytes = file_get_contents($pdfPath, false, null, 0, 4);
            if ($firstBytes !== '%PDF') {
                \Log::error('viewIstimara: Invalid PDF file, regenerating...');
                // Try to regenerate
                try {
                    @unlink($pdfPath); // Delete invalid file
                    $this->generateAndSaveIstimaraForUser($num_scolaire);
                    $pdfPath = storage_path('app/public/istimara/' . $filename);
                } catch (\Exception $regError) {
                    abort(500, 'Error regenerating PDF: ' . $regError->getMessage());
                }
            }
            
            // Serve PDF file - no authentication required
            return response()->file($pdfPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]);
        } catch (\Exception $e) {
            \Log::error('viewIstimara: Error: ' . $e->getMessage());
            abort(500, 'Error viewing PDF: ' . $e->getMessage());
        }
    }

    public function downloadIstimara($num_scolaire)
    {
        $eleve = Eleve::with([
            'tuteur.communeResidence.wilaya',
            'tuteur.communeNaissance.wilaya',
            'etablissement.commune.wilaya',
            'communeResidence.wilaya',
            'communeNaissance.wilaya',
            'mother',
            'father'
        ])
        ->where('num_scolaire', $num_scolaire)
        ->firstOrFail();

        $html = view('pdf.istimara', compact('eleve'))->render();

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'Amiri',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'default_direction' => 'rtl'
        ]);

        $mpdf->WriteHTML($html);
        $pdfContent = $mpdf->Output('', 'S'); // Get PDF as string

        return response($pdfContent, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="istimara_' . $num_scolaire . '.pdf"');
    }

    /**
     * Generate and save istimara PDF for normal users (without tuteur session check)
     */
    private function generateAndSaveIstimaraForUser($num_scolaire)
    {
        \Log::info('generateAndSaveIstimaraForUser: Starting for num_scolaire: ' . $num_scolaire);

        $eleve = Eleve::with([
            'tuteur.communeResidence.wilaya',
            'tuteur.communeNaissance.wilaya',
            'tuteur.communeCni.wilaya',
            'etablissement.commune.wilaya',
            'communeResidence.wilaya',
            'communeNaissance.wilaya',
            'mother',
            'father'
        ])
        ->where('num_scolaire', $num_scolaire)
        ->first();

        if (!$eleve) {
            \Log::error('generateAndSaveIstimaraForUser: Student not found');
            throw new \Exception('Student not found');
        }

        \Log::info('generateAndSaveIstimaraForUser: Student found, rendering HTML...');

        $html = view('pdf.istimara', compact('eleve'))->render();
        \Log::info('generateAndSaveIstimaraForUser: HTML rendered, length: ' . strlen($html));

        $tempDir = storage_path('app/temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
            \Log::info('generateAndSaveIstimaraForUser: Created temp directory: ' . $tempDir);
        }

        \Log::info('generateAndSaveIstimaraForUser: Creating mPDF instance...');
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'default_font' => 'dejavusans',
            'tempDir' => $tempDir,
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 16,
            'margin_bottom' => 16,
            'margin_header' => 9,
            'margin_footer' => 9,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'useSubstitutions' => true,
            'simpleTables' => true,
            'shrink_tables_to_fit' => 1
        ]);

        $mpdf->SetDirectionality('rtl');
        \Log::info('generateAndSaveIstimaraForUser: Writing HTML to PDF...');
        $mpdf->WriteHTML($html, 0);
        \Log::info('generateAndSaveIstimaraForUser: Generating PDF content...');
        $pdfContent = $mpdf->Output('', 'S');
        \Log::info('generateAndSaveIstimaraForUser: PDF content generated, size: ' . strlen($pdfContent) . ' bytes');

        // Verify it's a valid PDF
        if (substr($pdfContent, 0, 4) !== '%PDF') {
            \Log::error('generateAndSaveIstimaraForUser: Invalid PDF generated! First 50 chars: ' . substr($pdfContent, 0, 50));
            throw new \Exception('Failed to generate valid PDF');
        }

        // Store PDF in storage/app/public/istimara directory
        $storagePath = storage_path('app/public/istimara');
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
            \Log::info('generateAndSaveIstimaraForUser: Created storage directory: ' . $storagePath);
        }

        $filename = "istimara_{$num_scolaire}.pdf";
        $filePath = $storagePath . '/' . $filename;
        \Log::info('generateAndSaveIstimaraForUser: Saving PDF to: ' . $filePath);
        file_put_contents($filePath, $pdfContent);
        \Log::info('generateAndSaveIstimaraForUser: PDF saved, file size: ' . filesize($filePath) . ' bytes');

        // Update eleve record with PDF URL
        $pdfUrl = "/storage/istimara/" . $filename;
        $eleve->istimara = $pdfUrl;
        $eleve->save();
        \Log::info('generateAndSaveIstimaraForUser: Eleve record updated with PDF URL: ' . $pdfUrl);

        return $filePath;
    }

    /**
     * Generate istimara PDF for normal users
     */
    public function generateIstimaraForUser($num_scolaire)
    {
        \Log::info('Generate Istimara PDF for User called for: ' . $num_scolaire);
        try {
            $filePath = $this->generateAndSaveIstimaraForUser($num_scolaire);
            $filename = basename($filePath);
            
            \Log::info('PDF generated and saved: ' . $filePath);
            
            // If AJAX request, return JSON response
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'PDF generated successfully',
                    'url' => "/eleves/{$num_scolaire}/istimara"
                ]);
            }
            
            // Clear output buffers
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            
            // Return PDF directly as download for non-AJAX requests
            return response()->download(
                $filePath,
                $filename,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"'
                ]
            );
        } catch (\Exception $e) {
            \Log::error('Generate Istimara For User Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // If AJAX request, return JSON error
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error generating PDF: ' . $e->getMessage()
                ], 500);
            }
            
            abort(500, 'Error generating PDF: ' . $e->getMessage());
        }
    }

    // 🔹 Get comments for eleve (for tuteur dashboard)
    public function getComments($num_scolaire)
    {
        $tuteur = session('tuteur');
        if (!$tuteur || !isset($tuteur['nin'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $eleve = Eleve::where('num_scolaire', $num_scolaire)
            ->where('code_tuteur', $tuteur['nin'])
            ->first();

        if (!$eleve) {
            return response()->json(['success' => false, 'message' => 'Eleve not found'], 404);
        }

        $comments = Comment::with('user')
            ->where('eleve_id', $num_scolaire)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'comments' => $comments
        ]);
    }

}