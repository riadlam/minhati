<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Tuteur;
use App\Models\Eleve;
use App\Models\Comment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::with(['commune', 'wilaya'])->get());
    }

    // 🔹 Show login form
    public function showLoginForm()
    {
        return view('users.login');
    }

    // 🔹 Show dashboard (simple)
    public function dashboard()
    {
        // Ensure user is logged in
        if (!session('user_logged')) {
            return redirect()->route('user.login');
        }

        $userRole = session('user_role');
        $userCommune = session('user_commune_code');

        // Only ts_commune role can access this dashboard (also check for comune_ts as alternative)
        if ($userRole !== 'ts_commune' && $userRole !== 'comune_ts') {
            return redirect()->route('user.login')->with('error', 'Unauthorized access');
        }

        return view('users.dashboard');
    }

    // 🔹 Show tuteurs list page
    public function showTuteursList()
    {
        // Ensure user is logged in
        if (!session('user_logged')) {
            return redirect()->route('user.login');
        }

        $userRole = session('user_role');
        $userCommune = session('user_commune_code');

        // Only ts_commune role can access this page
        if ($userRole !== 'ts_commune' && $userRole !== 'comune_ts') {
            return redirect()->route('user.login')->with('error', 'Unauthorized access');
        }

        // Get schools for the filter dropdown
        $schools = collect([]);
        if (!empty($userCommune)) {
            $schools = \App\Models\Etablissement::where('code_commune', $userCommune)
                ->orderBy('nom_etabliss')
                ->get(['code_etabliss', 'nom_etabliss']);
        }

        return view('users.tuteurs_list', compact('schools'));
    }

    // 🔹 Show students list page
    public function showStudentsList()
    {
        // Ensure user is logged in
        if (!session('user_logged')) {
            return redirect()->route('user.login');
        }

        $userRole = session('user_role');
        $userCommune = session('user_commune_code');

        // Only ts_commune role can access this page
        if ($userRole !== 'ts_commune' && $userRole !== 'comune_ts') {
            return redirect()->route('user.login')->with('error', 'Unauthorized access');
        }

        // Get schools for the filter dropdown
        $schools = collect([]);
        if (!empty($userCommune)) {
            $schools = \App\Models\Etablissement::where('code_commune', $userCommune)
                ->orderBy('nom_etabliss')
                ->get(['code_etabliss', 'nom_etabliss']);
        }

        return view('users.students_list', compact('schools'));
    }

    // 🔹 Get paginated tuteurs (AJAX)
    public function getTuteurs(Request $request)
    {
        $userRole = session('user_role');
        if (!session('user_logged') || ($userRole !== 'ts_commune' && $userRole !== 'comune_ts')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $userCommune = session('user_commune_code');
        $page = $request->input('page', 1);
        $perPage = 20;
        $code_etabliss = $request->input('code_etabliss');
        $nin_search = $request->input('nin_search');

        if (empty($userCommune)) {
            return response()->json([
                'success' => true,
                'data' => [],
                'total' => 0,
                'current_page' => 1,
                'last_page' => 1
            ]);
        }

        // Build query - show tuteurs that have at least one eleve with code_commune matching user's commune
        $query = Tuteur::with(['eleves' => function($q) use ($userCommune, $code_etabliss) {
                // Only load eleves that match the user's commune
                $q->where('code_commune', $userCommune);
                if ($code_etabliss) {
                    $q->where('code_etabliss', $code_etabliss);
                }
            }])
            ->whereHas('eleves', function($q) use ($userCommune) {
                // Show tuteur if ANY of their eleves have code_commune matching user's commune
                $q->where('code_commune', $userCommune);
            });

        // Filter by NIN search if provided
        if ($nin_search) {
            $query->where('nin', 'like', '%' . $nin_search . '%');
        }

        // Filter by school if provided
        if ($code_etabliss) {
            $query->whereHas('eleves', function($q) use ($userCommune, $code_etabliss) {
                // Filter by school within the user's commune
                $q->where('code_commune', $userCommune)
                  ->where('code_etabliss', $code_etabliss);
            });
        }

        $total = $query->count();
        $tuteurs = $query->orderBy('date_insertion', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
                    ->get();

        // Format data
        $data = $tuteurs->map(function($tuteur) {
            $approvedCount = $tuteur->eleves->where('dossier_depose', 'oui')->count();
            $totalCount = $tuteur->eleves->count();
            $allApproved = $totalCount > 0 && $approvedCount === $totalCount;
            $someApproved = $approvedCount > 0 && $approvedCount < $totalCount;

            return [
                'nin' => $tuteur->nin,
                'nom' => ($tuteur->nom_ar ?? $tuteur->nom_fr ?? ''),
                'prenom' => ($tuteur->prenom_ar ?? $tuteur->prenom_fr ?? ''),
                'cats' => $tuteur->cats ?? 'غير محدد',
                'total_count' => $totalCount,
                'approved_count' => $approvedCount,
                'all_approved' => $allApproved,
                'some_approved' => $someApproved,
                'eleves' => $tuteur->eleves
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $total,
            'current_page' => (int)$page,
            'last_page' => (int)ceil($total / $perPage),
            'per_page' => $perPage
        ]);
    }


    public function show($id)
    {
        $user = User::with(['commune', 'wilaya'])->find($id);
        return $user ? response()->json($user) : response()->json(['message' => 'Not found'], 404);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code_user' => 'required|digits:18|unique:users,code_user',
            'nom_user' => 'nullable|string|max:50',
            'prenom_user' => 'nullable|string|max:50',
            'pass' => 'required|string|min:6',
            'fonction' => 'nullable|string|max:50',
            'organisme' => 'nullable|string|max:50',
            'statut' => 'nullable|string|max:1',
            'code_comm' => 'nullable|string|exists:commune,code_comm',
            'code_wilaya' => 'nullable|string|exists:wilaya,code_wil',
            'role' => 'required|string',
        ]);

        $validated['pass'] = Hash::make($validated['pass']);

        $user = User::create($validated);
        return response()->json($user, 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'Not found'], 404);

        $data = $request->all();
        if (isset($data['pass'])) {
            $data['pass'] = Hash::make($data['pass']);
        }

        $user->update($data);
        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'Not found'], 404);

        $user->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    // 🟢 User login (agents de saisie)
    public function login(Request $request)
    {
        $request->validate([
            'code_user' => 'required|digits:18',
            'password' => 'required|string',
        ]);

        // Explicitly eager load commune
        $user = User::where('code_user', $request->code_user)
                    ->with(['commune' => function($q) {
                        $q->select('code_comm', 'lib_comm_ar');
                    }])->first();

        if (!$user || !\Illuminate\Support\Facades\Hash::check($request->password, $user->pass)) {
            return back()->withErrors(['login' => 'رمز المستخدم أو كلمة المرور غير صحيحة'])->withInput();
        }

        // 🧠 Store session with commune name
        session([
            'user_logged' => true,
            'user_code' => $user->code_user,
            'user_name' => $user->nom_user . ' ' . $user->prenom_user,
            'user_role' => $user->role,
            'user_commune' => $user->commune?->lib_comm_ar ?? 'غير محددة',
            'user_commune_code' => $user->code_comm,
            'user_wilaya' => $user->code_wilaya,
        ]);

        return redirect()->route('user.dashboard');
    }


    // 🟡 Logout
    public function logout()
    {
        session()->forget(['user_logged', 'user_code', 'user_name', 'user_role', 'user_commune', 'user_commune_code', 'user_wilaya']);
        return redirect()->route('user.login')->with('success', 'تم تسجيل الخروج بنجاح');
    }

    /**
     * API Login for User (agents de saisie) - returns JSON response
     */
    public function apiLogin(Request $request)
    {
        $request->validate([
            'code_user' => 'required|digits:18',
            'password' => 'required|string',
        ]);

        $user = User::where('code_user', $request->code_user)
                    ->with(['commune' => function($q) {
                        $q->select('code_comm', 'lib_comm_ar');
                    }])->first();

        if (!$user || !\Illuminate\Support\Facades\Hash::check($request->password, $user->pass)) {
            return response()->json([
                'success' => false,
                'message' => 'رمز المستخدم أو كلمة المرور غير صحيحة',
                'errors' => ['login' => ['رمز المستخدم أو كلمة المرور غير صحيحة']]
            ], 401);
        }

        // Revoke all existing tokens for this user
        $user->tokens()->delete();

        // Create new token
        $token = $user->createToken('user-api-token', ['*'], now()->addDays(30))->plainTextToken;

        // Also create session for web routes compatibility
        session([
            'user_logged' => true,
            'user_code' => $user->code_user,
            'user_name' => $user->nom_user . ' ' . $user->prenom_user,
            'user_role' => $user->role,
            'user_commune' => $user->commune?->lib_comm_ar ?? 'غير محددة',
            'user_commune_code' => $user->code_comm,
            'user_wilaya_code' => $user->code_wilaya,
            'user_nom' => $user->nom_user,
            'user_prenom' => $user->prenom_user,
        ]);
        
        // Force save the session to ensure it persists
        session()->save();

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح',
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 2592000, // 30 days in seconds
            'data' => [
                'code_user' => $user->code_user,
                'nom_user' => $user->nom_user,
                'prenom_user' => $user->prenom_user,
                'role' => $user->role,
                'commune' => $user->commune?->lib_comm_ar ?? 'غير محددة',
                'commune_code' => $user->code_comm,
                'wilaya' => $user->code_wilaya,
            ]
        ], 200);
    }

    /**
     * Get current authenticated user data
     */
    public function getCurrentUser(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        
        // Load commune relationship
        $user->load('commune');
        
        return response()->json([
            'success' => true,
            'data' => [
                'code_user' => $user->code_user,
                'nom_user' => $user->nom_user,
                'prenom_user' => $user->prenom_user,
                'user_name' => $user->nom_user . ' ' . $user->prenom_user,
                'role' => $user->role,
                'commune' => $user->commune?->lib_comm_ar ?? 'غير محددة',
                'commune_code' => $user->code_comm,
                'wilaya' => $user->code_wilaya,
            ]
        ], 200);
    }

    /**
     * API Logout for User - returns JSON response
     */
    public function apiLogout(Request $request)
    {
        // Revoke current token (token-only)
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الخروج بنجاح'
        ], 200);
    }

    // 🔹 View tuteur details (return JSON for modal)
    public function viewTuteur($nin)
    {
        $userRole = session('user_role');
        if (!session('user_logged') || ($userRole !== 'ts_commune' && $userRole !== 'comune_ts')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $userCommune = session('user_commune_code');
        
        // Load tuteur with filtered eleves (for display) and all eleves (for count)
        $tuteur = Tuteur::with([
            'eleves' => function($query) use ($userCommune) {
                $query->where('code_commune', $userCommune)->with(['etablissement', 'mother']);
            },
            'eleves.etablissement.commune',
            'communeResidence',
            'communeNaissance'
        ])->where('nin', $nin)->first();

        // Check if tuteur has any eleves with matching code_commune
        if (!$tuteur || !$tuteur->eleves()->where('code_commune', $userCommune)->exists()) {
            return response()->json(['success' => false, 'message' => 'Tuteur not found or no students in your commune'], 404);
        }

        // Get total count of ALL eleves for this tuteur (not filtered by commune)
        $totalElevesCount = \App\Models\Eleve::where('code_tuteur', $nin)->count();
        \Log::info('Total eleves count for tuteur ' . $nin . ': ' . $totalElevesCount);
        
        // Log tuteur code_commune
        \Log::info('Tuteur code_commune: ' . $tuteur->code_commune);
        
        // Manually load commune if not loaded
        if (!$tuteur->relationLoaded('communeResidence') || !$tuteur->communeResidence) {
            \Log::info('Commune relationship not loaded, loading manually...');
            $commune = \App\Models\Commune::where('code_comm', $tuteur->code_commune)->first();
            if ($commune) {
                \Log::info('Commune found: ' . $commune->lib_comm_ar . ' (code: ' . $commune->code_comm . ')');
                $tuteur->setRelation('communeResidence', $commune);
            } else {
                \Log::warning('Commune not found for code: ' . $tuteur->code_commune);
            }
        } else {
            \Log::info('Commune relationship loaded: ' . ($tuteur->communeResidence->lib_comm_ar ?? 'null'));
        }
        
        // Log the final tuteur data structure
        \Log::info('Tuteur communeResidence: ' . json_encode($tuteur->communeResidence));
        
        // Convert to array to ensure relationships are included
        $tuteurArray = $tuteur->toArray();
        // Add total eleves count to the array
        $tuteurArray['total_eleves_count'] = $totalElevesCount;
        \Log::info('Tuteur array keys: ' . implode(', ', array_keys($tuteurArray)));
        \Log::info('Has communeResidence in array: ' . (isset($tuteurArray['commune_residence']) ? 'yes (snake_case)' : 'no'));
        \Log::info('Has communeResidence (camelCase) in array: ' . (isset($tuteurArray['communeResidence']) ? 'yes' : 'no'));

        return response()->json([
            'success' => true,
            'tuteur' => $tuteurArray
        ]);
    }

    // 🔹 Delete tuteur
    public function deleteTuteur($nin)
    {
        $userRole = session('user_role');
        if (!session('user_logged') || ($userRole !== 'ts_commune' && $userRole !== 'comune_ts')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $userCommune = session('user_commune_code');
        $tuteur = Tuteur::where('nin', $nin)->first();

        // Check if tuteur has any eleves with matching code_commune
        if (!$tuteur || !$tuteur->eleves()->where('code_commune', $userCommune)->exists()) {
            return response()->json(['success' => false, 'message' => 'Tuteur not found or no students in your commune'], 404);
        }

        // Delete associated eleves first
        $tuteur->eleves()->delete();
        $tuteur->delete();

        return response()->json(['success' => true, 'message' => 'Tuteur deleted successfully']);
    }

    // 🔹 View eleve details (return JSON for modal)
    public function viewEleve($num_scolaire)
    {
        $userRole = session('user_role');
        if (!session('user_logged') || ($userRole !== 'ts_commune' && $userRole !== 'comune_ts')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $userCommune = session('user_commune_code');
        $eleve = Eleve::with([
            'tuteur.communeResidence',
            'tuteur.communeNaissance',
            'etablissement.commune',
            'communeResidence',
            'communeNaissance'
        ])->where('num_scolaire', $num_scolaire)->first();

        if (!$eleve) {
            return response()->json(['success' => false, 'message' => 'Eleve not found'], 404);
        }

        // Check if eleve has code_commune matching user's commune
        if ($eleve->code_commune !== $userCommune) {
            return response()->json(['success' => false, 'message' => 'Eleve not in your commune'], 403);
        }

        return response()->json([
            'success' => true,
            'eleve' => $eleve->toArray()
        ]);
    }

    // 🔹 Approve eleve (set dossier_depose to 'oui')
    public function approveEleve($num_scolaire)
    {
        $userRole = session('user_role');
        if (!session('user_logged') || ($userRole !== 'ts_commune' && $userRole !== 'comune_ts')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $userCommune = session('user_commune_code');
        $eleve = Eleve::with('tuteur')->where('num_scolaire', $num_scolaire)->first();

        if (!$eleve || $eleve->code_commune !== $userCommune) {
            return response()->json(['success' => false, 'message' => 'Eleve not found or not in your commune'], 404);
        }

        // Set dossier_depose to 'oui' (approved) and store who approved it
        $eleve->dossier_depose = 'oui';
        $eleve->approved_by = session('user_code');
        $eleve->save();

        return response()->json(['success' => true, 'message' => 'Eleve approved successfully']);
    }

    /**
     * Export eleves of the current commune (ts_commune) as CSV
     */
    public function exportEleves(Request $request)
    {
        $userRole = session('user_role');
        if (!session('user_logged') || ($userRole !== 'ts_commune' && $userRole !== 'comune_ts')) {
            return redirect()->route('user.login');
        }

        $userCommune = session('user_commune_code');
        if (!$userCommune) {
            return back()->with('error', 'لا توجد بلدية مرتبطة بالمستخدم.');
        }

        $eleves = Eleve::with(['etablissement'])
            ->where('code_commune', $userCommune)
            ->orderBy('date_insertion', 'desc')
            ->get([
                'num_scolaire',
                'nom',
                'prenom',
                'date_naiss',
                'niv_scol',
                'classe_scol',
                'code_etabliss',
                'dossier_depose',
                'relation_tuteur'
            ]);

        $lines = [];
        $lines[] = [
            'num_scolaire',
            'nom',
            'prenom',
            'date_naiss',
            'niveau',
            'classe',
            'code_etabliss',
            'etat_dossier',
            'relation_tuteur'
        ];

        foreach ($eleves as $e) {
            $lines[] = [
                $e->num_scolaire,
                $e->nom,
                $e->prenom,
                $e->date_naiss,
                $e->niv_scol,
                $e->classe_scol,
                $e->code_etabliss,
                $e->dossier_depose,
                $e->relation_tuteur
            ];
        }

        $csv = '';
        foreach ($lines as $row) {
            $csv .= implode(',', array_map(function ($v) {
                $escaped = str_replace('"', '""', $v ?? '');
                return '"' . $escaped . '"';
            }, $row)) . "\n";
        }

        $filename = 'eleves_' . $userCommune . '_' . now()->format('Ymd_His') . '.csv';
        return response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    /**
     * Allow ts_commune to create a new tuteur in their commune
     */
    public function storeTuteurForCommune(Request $request)
    {
        $userRole = session('user_role');
        if (!session('user_logged') || ($userRole !== 'ts_commune' && $userRole !== 'comune_ts')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $userCommune = session('user_commune_code');
        if (!$userCommune) {
            return response()->json(['success' => false, 'message' => 'No commune bound to user'], 400);
        }

        $validated = $request->validate([
            'nin' => 'required|string|max:18|unique:tuteures,nin',
            'nom_ar' => 'required|string|max:50|regex:/^[\p{Arabic}\s\-]+$/u',
            'prenom_ar' => 'required|string|max:50|regex:/^[\p{Arabic}\s\-]+$/u',
            'sexe' => 'required|string|in:ذكر,أنثى',
            'adresse' => 'nullable|string|max:80',
            'num_cpt' => 'required|string|max:12|unique:tuteures,num_cpt',
            'cle_cpt' => 'required|string|max:2',
            'nss' => 'nullable|string|max:12',
            'num_cni' => 'nullable|string|max:10',
            'date_cni' => 'nullable|date',
            'lieu_cni' => 'nullable|string|max:5',
            'tel' => 'nullable|string|max:10',
            'email' => 'nullable|email|max:255',
        ]);

        $password = Str::random(12);

        $tuteur = Tuteur::create([
            'nin' => $validated['nin'],
            'nom_ar' => $validated['nom_ar'],
            'prenom_ar' => $validated['prenom_ar'],
            'sexe' => $validated['sexe'],
            'adresse' => $validated['adresse'] ?? null,
            'num_cpt' => $validated['num_cpt'],
            'cle_cpt' => $validated['cle_cpt'],
            'nss' => $validated['nss'] ?? null,
            'num_cni' => $validated['num_cni'] ?? null,
            'date_cni' => $validated['date_cni'] ?? null,
            'lieu_cni' => $validated['lieu_cni'] ?? null,
            'tel' => $validated['tel'] ?? null,
            'email' => $validated['email'] ?? null,
            'code_commune' => $userCommune,
            'password' => Hash::make($password),
            'date_insertion' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tuteur created successfully',
            'tuteur' => $tuteur,
            'temporary_password' => $password
        ], 201);
    }

    // 🔹 Delete eleve
    public function deleteEleve($num_scolaire)
    {
        $userRole = session('user_role');
        if (!session('user_logged') || ($userRole !== 'ts_commune' && $userRole !== 'comune_ts')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $userCommune = session('user_commune_code');
        $eleve = Eleve::with('tuteur')->where('num_scolaire', $num_scolaire)->first();

        if (!$eleve || $eleve->code_commune !== $userCommune) {
            return response()->json(['success' => false, 'message' => 'Eleve not found or not in your commune'], 404);
        }

        $eleve->delete();

        return response()->json(['success' => true, 'message' => 'Eleve deleted successfully']);
    }

    // 🔹 Store comment for eleve
    public function storeComment(Request $request, $num_scolaire)
    {
        $userRole = session('user_role');
        if (!session('user_logged') || ($userRole !== 'ts_commune' && $userRole !== 'comune_ts')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $userCommune = session('user_commune_code');
        $eleve = Eleve::with('tuteur')->where('num_scolaire', $num_scolaire)->first();

        if (!$eleve || $eleve->code_commune !== $userCommune) {
            return response()->json(['success' => false, 'message' => 'Eleve not found or not in your commune'], 404);
        }

        $validated = $request->validate([
            'text' => 'required|string|max:1000'
        ]);

        $comment = Comment::create([
            'user_id' => session('user_code'),
            'eleve_id' => $num_scolaire,
            'text' => $validated['text']
        ]);

        $comment->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully',
            'comment' => $comment
        ]);
    }

    // 🔹 Get comments for eleve
    public function getComments($num_scolaire)
    {
        $userRole = session('user_role');
        if (!session('user_logged') || ($userRole !== 'ts_commune' && $userRole !== 'comune_ts')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $userCommune = session('user_commune_code');
        $eleve = Eleve::with('tuteur')->where('num_scolaire', $num_scolaire)->first();

        if (!$eleve || $eleve->code_commune !== $userCommune) {
            return response()->json(['success' => false, 'message' => 'Eleve not found or not in your commune'], 404);
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

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8',
            'new_password_confirmation' => 'required|string|same:new_password',
        ]);

        // 🔹 Récupération du tuteur connecté depuis la session
        $sessionTuteur = session('tuteur');

        if (!$sessionTuteur || !isset($sessionTuteur['nin'])) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على المستخدم في الجلسة.'
            ], 401);
        }

        // 🔹 Chercher le tuteur dans la base de données
        $tuteur = Tuteur::where('nin', $sessionTuteur['nin'])->first();

        if (!$tuteur) {
            return response()->json([
                'success' => false,
                'message' => 'المستخدم غير موجود.'
            ], 404);
        }

        // 🔹 Vérifier le mot de passe actuel
        if (!Hash::check($validated['current_password'], $tuteur->password)) {
            return response()->json([
                'success' => false,
                'message' => 'كلمة المرور الحالية غير صحيحة.'
            ], 400);
        }

        // 🔹 Mettre à jour le mot de passe
        $tuteur->password = Hash::make($validated['new_password']);
        $tuteur->save();

        // 🔹 Optionnel : mettre à jour la session pour éviter toute incohérence
        session(['tuteur' => $tuteur->only(['nin', 'nom_fr', 'prenom_fr', 'email'])]);

        return response()->json([
            'success' => true,
            'message' => 'تم تغيير كلمة المرور بنجاح.'
        ]);
    }
    

}