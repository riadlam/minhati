<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Tuteur;
use App\Models\Eleve;
use App\Models\Comment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use App\Models\Wilaya;
use App\Models\Commune;
use App\Models\Antenne;

class UserController extends Controller
{
    /**
     * Map Etablissement niveau_enseignement to display levels (ابتدائي, متوسط, ثانوي).
     * Returns array of one or more of ['ابتدائي','متوسط','ثانوي'] for optgroup grouping.
     */
    private function mapSchoolLevelsFromNiveau(?string $niveau): array
    {
        if (empty($niveau)) {
            return ['أخرى'];
        }
        $n = mb_strtolower(trim($niveau));
        $levels = [];
        if (mb_strpos($n, 'ابتدائي') !== false) {
            $levels[] = 'ابتدائي';
        }
        if (mb_strpos($n, 'متوسط') !== false) {
            $levels[] = 'متوسط';
        }
        if (mb_strpos($n, 'ثانوي') !== false) {
            $levels[] = 'ثانوي';
        }
        return $levels ?: ['أخرى'];
    }

    /**
     * Resolve authenticated user context from API token first, then session fallback.
     */
    /**
     * Get all commune codes for an ATR (antr) user based on their regional branch.
     * Uses the Wilaya → Antenne relationship: finds user's wilaya, gets its code_ar,
     * then finds ALL wilayas with the same code_ar, and returns all commune codes.
     */
    private function getAntrCommuneCodes(?string $userWilaya): array
    {
        if (empty($userWilaya)) return [];
        $wilaya = \App\Models\Wilaya::where('code_wil', $userWilaya)->first();
        if (!$wilaya || empty($wilaya->code_ar)) return [];
        $regionWilayas = \App\Models\Wilaya::where('code_ar', $wilaya->code_ar)->pluck('code_wil')->toArray();
        if (empty($regionWilayas)) return [];
        return \App\Models\Commune::whereIn('code_wilaya', $regionWilayas)->pluck('code_comm')->toArray();
    }

    /**
     * Get all wilaya codes in the same regional branch as the given wilaya.
     */
    private function getAntrWilayaCodes(?string $userWilaya): array
    {
        if (empty($userWilaya)) return [];
        $wilaya = \App\Models\Wilaya::where('code_wil', $userWilaya)->first();
        if (!$wilaya || empty($wilaya->code_ar)) return [];
        return \App\Models\Wilaya::where('code_ar', $wilaya->code_ar)->pluck('code_wil')->toArray();
    }

    /**
     * Get the antenne name for an ATR user.
     */
    private function getAntenneName(?string $userWilaya): ?string
    {
        if (empty($userWilaya)) return null;
        $wilaya = \App\Models\Wilaya::where('code_wil', $userWilaya)->first();
        if (!$wilaya || empty($wilaya->code_ar)) return null;
        $antenne = \App\Models\Antenne::where('code_ar', $wilaya->code_ar)->first();
        return $antenne ? $antenne->lib_ar_ar : null;
    }

    private function resolveAgentContext(Request $request): ?array
    {
        $tokenUser = $request->user();
        if ($tokenUser) {
            return [
                'role' => $tokenUser->role,
                'code' => $tokenUser->code_user,
                'commune' => $tokenUser->code_comm,
                'wilaya' => $tokenUser->code_wilaya,
                'logged' => true,
            ];
        }

        if (session('user_logged')) {
            return [
                'role' => session('user_role'),
                'code' => session('user_code'),
                'commune' => session('user_commune_code'),
                'wilaya' => session('user_wilaya'),
                'logged' => true,
            ];
        }

        return null;
    }

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
        $userCode = session('user_code');

        // ts_commune, comune_ts, das, comite_wilaya, antr, or admin can access this dashboard
        if (!in_array($userRole, ['ts_commune', 'comune_ts', 'das', 'comite_wilaya', 'antr', 'admin'])) {
            return redirect()->route('user.login')->with('error', 'Unauthorized access');
        }

        // Generate API token if missing (for existing sessions)
        if (empty(session('api_token')) && !empty($userCode)) {
            $user = User::where('code_user', $userCode)->first();
            if ($user) {
                $token = $user->createToken('user-api-token', ['*'], now()->addDays(30))->plainTextToken;
                session(['api_token' => $token]);
                session()->save();
            }
        }

        $wilayaName = null;
        $antenneName = null;
        if (in_array($userRole, ['das', 'comite_wilaya', 'antr'])) {
            $codeWilaya = session('user_wilaya');
            if (empty($codeWilaya) && session('user_code')) {
                $codeWilaya = User::where('code_user', session('user_code'))->value('code_wilaya');
                if ($codeWilaya !== null) {
                    session(['user_wilaya' => $codeWilaya]);
                }
            }
            if (!empty($codeWilaya)) {
                $wilayaName = \App\Models\Wilaya::where('code_wil', $codeWilaya)->value('lib_wil_ar') ?? $codeWilaya;
            }
            if ($userRole === 'antr') {
                $antenneName = $this->getAntenneName($codeWilaya);
            }
        }

        $impersonating = (bool) session('impersonate_read_only');
        $loggedInAsName = session('logged_in_as_name', '');
        return view('users.dashboard', compact('wilayaName', 'antenneName', 'impersonating', 'loggedInAsName'));
    }

    // 🔹 Show users list page (admin only)
    public function showUsersList()
    {
        if (!session('user_logged')) {
            return redirect()->route('user.login');
        }

        $userRole = session('user_role');
        if ($userRole !== 'admin') {
            return redirect()->route('user.dashboard')->with('error', 'غير مصرح لك بالوصول لهذه الصفحة');
        }

        return view('users.users_list');
    }

    // 🔹 Admin API: list users with filters
    public function apiAdminUsers(Request $request)
    {
        $authUser = $request->user();
        if (!$authUser || $authUser->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $query = User::with(['commune', 'wilaya']);

        if ($request->filled('search')) {
            $search = trim((string)$request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('code_user', 'like', "%{$search}%")
                    ->orWhere('nom_user', 'like', "%{$search}%")
                    ->orWhere('prenom_user', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }
        if ($request->filled('code_wilaya')) {
            $query->where('code_wilaya', $request->input('code_wilaya'));
        }
        if ($request->filled('code_comm')) {
            $query->where('code_comm', $request->input('code_comm'));
        }

        $perPage = (int)$request->input('per_page', 15);
        if ($perPage < 1) {
            $perPage = 15;
        }
        if ($perPage > 100) {
            $perPage = 100;
        }

        $users = $query->orderByDesc('date_insertion')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $users->items(),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ]
        ]);
    }

    // 🔹 Admin API: show one user
    public function apiAdminShowUser(Request $request, $code_user)
    {
        $authUser = $request->user();
        if (!$authUser || $authUser->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $user = User::with(['commune', 'wilaya'])->where('code_user', $code_user)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $user]);
    }

    // 🔹 Admin API: update user
    public function apiAdminUpdateUser(Request $request, $code_user)
    {
        $authUser = $request->user();
        if (!$authUser || $authUser->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $user = User::where('code_user', $code_user)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        $validated = $request->validate([
            'nom_user' => 'nullable|string|max:50',
            'prenom_user' => 'nullable|string|max:50',
            'pass' => 'nullable|string|min:6',
            'role' => 'required|in:admin,ts_commune,das,comite_wilaya,antr',
            'code_comm' => 'nullable|string|exists:commune,code_comm',
            'code_wilaya' => 'nullable|string|exists:wilaya,code_wil',
            'statut' => 'nullable|string|max:1',
        ]);

        // Role-based normalization for location.
        if ($validated['role'] === 'ts_commune') {
            if (empty($validated['code_wilaya']) || empty($validated['code_comm'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'يرجى اختيار الولاية والبلدية لرتبة تقني البلدية',
                ], 422);
            }
        } elseif (in_array($validated['role'], ['das', 'comite_wilaya', 'antr'], true)) {
            if (empty($validated['code_wilaya'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'يرجى اختيار الولاية لهذه الرتبة',
                ], 422);
            }
            $validated['code_comm'] = null;
        } else {
            $validated['code_comm'] = null;
            $validated['code_wilaya'] = null;
        }

        if (!empty($validated['pass'])) {
            $validated['pass'] = Hash::make($validated['pass']);
        } else {
            unset($validated['pass']);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المستخدم بنجاح',
            'data' => $user->fresh(['commune', 'wilaya']),
        ]);
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
        $userWilaya = session('user_wilaya');
        $userCode = session('user_code');

        // ts_commune, comune_ts, das, comite_wilaya, or antr can access this page
        if (!in_array($userRole, ['ts_commune', 'comune_ts', 'das', 'comite_wilaya', 'antr'])) {
            return redirect()->route('user.login')->with('error', 'Unauthorized access');
        }
        $impersonating = (bool) session('impersonate_read_only');
        $loggedInAsName = session('logged_in_as_name', '');
        return view('users.tuteurs_list', ['schools' => collect([]), 'impersonating' => $impersonating, 'loggedInAsName' => $loggedInAsName]);
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
        $userWilaya = session('user_wilaya');
        $userCode = session('user_code');

        // ts_commune, comune_ts, das, comite_wilaya, or antr can access this page
        if (!in_array($userRole, ['ts_commune', 'comune_ts', 'das', 'comite_wilaya', 'antr'])) {
            return redirect()->route('user.login')->with('error', 'Unauthorized access');
        }

        if (empty(session('api_token')) && !empty($userCode)) {
            $user = User::where('code_user', $userCode)->first();
            if ($user) {
                $token = $user->createToken('user-api-token', ['*'], now()->addDays(30))->plainTextToken;
                session(['api_token' => $token]);
                session()->save();
            }
        }

        $impersonating = (bool) session('impersonate_read_only');
        $loggedInAsName = session('logged_in_as_name', '');
        return view('users.students_list', ['schools' => collect([]), 'impersonating' => $impersonating, 'loggedInAsName' => $loggedInAsName]);
    }

    // 🔹 Show pending requests page (ts_commune: by commune; das/comite_wilaya: by wilaya)
    public function showPendingRequests()
    {
        if (!session('user_logged')) {
            return redirect()->route('user.login');
        }

        $userRole = session('user_role');
        $userCommune = session('user_commune_code');
        $userWilaya = session('user_wilaya');
        $userCode = session('user_code');

        if (!in_array($userRole, ['ts_commune', 'comune_ts', 'das', 'comite_wilaya', 'antr'])) {
            return redirect()->route('user.login')->with('error', 'Unauthorized access');
        }

        if (empty(session('api_token')) && !empty($userCode)) {
            $user = User::where('code_user', $userCode)->first();
            if ($user) {
                $token = $user->createToken('user-api-token', ['*'], now()->addDays(30))->plainTextToken;
                session(['api_token' => $token]);
                session()->save();
            }
        }

        $impersonating = (bool) session('impersonate_read_only');
        $loggedInAsName = session('logged_in_as_name', '');
        return view('users.pending_requests', ['schools' => collect([]), 'impersonating' => $impersonating, 'loggedInAsName' => $loggedInAsName]);
    }

    // 🔹 Show approved requests page (ts_commune: by commune; das/comite_wilaya: by wilaya)
    public function showApprovedRequests()
    {
        if (!session('user_logged')) {
            return redirect()->route('user.login');
        }

        $userRole = session('user_role');
        $userCommune = session('user_commune_code');
        $userWilaya = session('user_wilaya');
        $userCode = session('user_code');

        if (!in_array($userRole, ['ts_commune', 'comune_ts', 'das', 'comite_wilaya', 'antr'])) {
            return redirect()->route('user.login')->with('error', 'Unauthorized access');
        }

        if (empty(session('api_token')) && !empty($userCode)) {
            $user = User::where('code_user', $userCode)->first();
            if ($user) {
                $token = $user->createToken('user-api-token', ['*'], now()->addDays(30))->plainTextToken;
                session(['api_token' => $token]);
                session()->save();
            }
        }

        $impersonating = (bool) session('impersonate_read_only');
        $loggedInAsName = session('logged_in_as_name', '');
        return view('users.approved_requests', ['schools' => collect([]), 'impersonating' => $impersonating, 'loggedInAsName' => $loggedInAsName]);
    }

    /**
     * API: list accessible schools for logged-in user (ts_commune/comune_ts/das/comite_wilaya).
     */
    public function apiUserSchools(Request $request)
    {
        $ctx = $this->resolveAgentContext($request);
        $userRole = $ctx['role'] ?? null;
        if (!$ctx || !in_array($userRole, ['ts_commune', 'comune_ts', 'das', 'comite_wilaya', 'antr'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $userCommune = $ctx['commune'] ?? null;
        $userWilaya = $ctx['wilaya'] ?? null;

        $schools = collect([]);
        if (in_array($userRole, ['ts_commune', 'comune_ts']) && !empty($userCommune)) {
            $schools = \App\Models\Etablissement::where('code_commune', $userCommune)
                ->orderBy('nom_etabliss')
                ->get(['code_etabliss', 'nom_etabliss', 'niveau_enseignement']);
        } elseif ($userRole === 'antr' && !empty($userWilaya)) {
            $communeCodes = $this->getAntrCommuneCodes($userWilaya);
            if (!empty($communeCodes)) {
                $schools = \App\Models\Etablissement::whereIn('code_commune', $communeCodes)
                    ->orderBy('nom_etabliss')
                    ->get(['code_etabliss', 'nom_etabliss', 'niveau_enseignement']);
            }
        } elseif (in_array($userRole, ['das', 'comite_wilaya']) && !empty($userWilaya)) {
            $communeCodes = \App\Models\Commune::where('code_wilaya', $userWilaya)->pluck('code_comm')->toArray();
            if (!empty($communeCodes)) {
                $schools = \App\Models\Etablissement::whereIn('code_commune', $communeCodes)
                    ->orderBy('nom_etabliss')
                    ->get(['code_etabliss', 'nom_etabliss', 'niveau_enseignement']);
            }
        }

        $data = $schools->map(function ($school) {
            return [
                'code_etabliss' => $school->code_etabliss,
                'nom_etabliss' => $school->nom_etabliss,
                'levels' => $this->mapSchoolLevelsFromNiveau($school->niveau_enseignement ?? ''),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    // 🔹 Get paginated students (AJAX) - ts_commune: by commune; das: by wilaya + dossier_depose=oui
    public function getEleves(Request $request)
    {
        $ctx = $this->resolveAgentContext($request);
        $userRole = $ctx['role'] ?? null;
        if (!$ctx || !in_array($userRole, ['ts_commune', 'comune_ts', 'das', 'comite_wilaya', 'antr'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $userCommune = $ctx['commune'] ?? null;
        $userWilaya = $ctx['wilaya'] ?? null;
        $page = $request->input('page', 1);
        $perPage = 20;
        $code_etabliss = $request->input('code_etabliss');
        $num_scolaire_search = $request->input('num_scolaire_search');
        $statusFilter = $request->input('status_filter');

        $query = Eleve::with(['tuteur', 'etablissement']);

        if ($userRole === 'antr') {
            if (empty($userWilaya)) {
                return response()->json(['success' => true, 'data' => [], 'total' => 0, 'current_page' => 1, 'last_page' => 1, 'per_page' => $perPage]);
            }
            $communeCodes = $this->getAntrCommuneCodes($userWilaya);
            if (empty($communeCodes)) {
                return response()->json(['success' => true, 'data' => [], 'total' => 0, 'current_page' => 1, 'last_page' => 1, 'per_page' => $perPage]);
            }
            $query->whereIn('code_commune', $communeCodes)
                  ->where('etat_das', 'accepte')
                  ->where('etat_comite_wilaya', 'accepte');
        } elseif ($userRole === 'das') {
            if (empty($userWilaya)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'total' => 0,
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $perPage
                ]);
            }
            $communeCodes = \App\Models\Commune::where('code_wilaya', $userWilaya)->pluck('code_comm')->toArray();
            if (empty($communeCodes)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'total' => 0,
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $perPage
                ]);
            }
            $query->whereIn('code_commune', $communeCodes)->where('dossier_depose', 'oui');
        } elseif ($userRole === 'comite_wilaya') {
            if (empty($userWilaya)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'total' => 0,
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $perPage
                ]);
            }
            $communeCodes = \App\Models\Commune::where('code_wilaya', $userWilaya)->pluck('code_comm')->toArray();
            if (empty($communeCodes)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'total' => 0,
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $perPage
                ]);
            }
            $query->whereIn('code_commune', $communeCodes)->whereIn('etat_das', ['accepte', 'refuse']);
        } else {
            if (empty($userCommune)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'total' => 0,
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $perPage
                ]);
            }
            $query->where('code_commune', $userCommune);
        }

        // Status filter for DAS, comite_wilaya, and antr (accepte | refuse | pending)
        if (in_array($userRole, ['das', 'comite_wilaya', 'antr']) && $statusFilter) {
            if ($userRole === 'das') {
                if ($statusFilter === 'accepte') {
                    $query->where('etat_das', 'accepte');
                } elseif ($statusFilter === 'refuse') {
                    $query->where('etat_das', 'refuse');
                } elseif ($statusFilter === 'pending') {
                    $query->where(function ($q) {
                        $q->whereNotIn('etat_das', ['accepte', 'refuse'])->orWhereNull('etat_das');
                    });
                }
            } elseif ($userRole === 'antr') {
                if ($statusFilter === 'accepte') {
                    $query->where('etat_final', 'accepte');
                } elseif ($statusFilter === 'refuse') {
                    $query->where('etat_final', 'refuse');
                } elseif ($statusFilter === 'pending') {
                    $query->where(function ($q) {
                        $q->whereNotIn('etat_final', ['accepte', 'refuse'])->orWhereNull('etat_final');
                    });
                }
            } else {
                if ($statusFilter === 'accepte') {
                    $query->where('etat_comite_wilaya', 'accepte');
                } elseif ($statusFilter === 'refuse') {
                    $query->where('etat_comite_wilaya', 'refuse');
                } elseif ($statusFilter === 'pending') {
                    $query->where(function ($q) {
                        $q->whereNotIn('etat_comite_wilaya', ['accepte', 'refuse'])->orWhereNull('etat_comite_wilaya');
                    });
                }
            }
        }

        if ($code_etabliss) {
            $query->where('code_etabliss', $code_etabliss);
        }
        if ($num_scolaire_search) {
            $query->where('num_scolaire', 'like', '%' . $num_scolaire_search . '%');
        }

        $total = $query->count();
        $eleves = $query->orderBy('date_insertion', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        $data = $eleves->map(function ($eleve) {
            $tuteur = $eleve->tuteur;
            return [
                'num_scolaire' => $eleve->num_scolaire,
                'nom' => $eleve->nom,
                'prenom' => $eleve->prenom,
                'date_naiss' => $eleve->date_naiss,
                'niv_scol' => $eleve->niv_scol,
                'classe_scol' => $eleve->classe_scol,
                'sexe' => $eleve->sexe,
                'code_etabliss' => $eleve->code_etabliss,
                'etablissement_nom' => $eleve->etablissement->nom_etabliss ?? '—',
                'dossier_depose' => $eleve->dossier_depose,
                'etat_das' => $eleve->etat_das,
                'etat_comite_wilaya' => $eleve->etat_comite_wilaya ?? null,
                'etat_final' => $eleve->etat_final ?? null,
                'motif' => $eleve->motif ?? '',
                'cnas_refuse' => (int) ($eleve->cnas_refuse ?? 0),
                'casnos_refuse' => (int) ($eleve->casnos_refuse ?? 0),
                'relation_tuteur' => $eleve->relation_tuteur,
                'relation_tuteur_text' => $eleve->relation_tuteur_text,
                'tuteur_nin' => $tuteur->nin ?? null,
                'tuteur_nom' => ($tuteur->nom_ar ?? $tuteur->nom_fr ?? '—') ?? '—',
                'tuteur_prenom' => ($tuteur->prenom_ar ?? $tuteur->prenom_fr ?? '—') ?? '—',
                'appeal_status' => $eleve->appeal_status,
                'appeal_text' => $eleve->appeal_text,
                'appeal_document' => $eleve->appeal_document,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $total,
            'current_page' => (int) $page,
            'last_page' => (int) ceil($total / $perPage),
            'per_page' => $perPage
        ]);
    }

    // 🔹 Get paginated pending students (AJAX) - ts_commune: by commune; das/comite_wilaya: by wilaya
    public function getPendingEleves(Request $request)
    {
        $ctx = $this->resolveAgentContext($request);
        $userRole = $ctx['role'] ?? null;
        if (!$ctx || !in_array($userRole, ['ts_commune', 'comune_ts', 'das', 'comite_wilaya', 'antr'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $userCommune = $ctx['commune'] ?? null;
        $userWilaya = $ctx['wilaya'] ?? null;
        $page = $request->input('page', 1);
        $perPage = 20;
        $code_etabliss = $request->input('code_etabliss');
        $num_scolaire_search = $request->input('num_scolaire_search');

        $query = Eleve::with(['tuteur', 'etablissement'])
            ->where(function($q) {
                $q->where('dossier_depose', '!=', 'oui')->orWhereNull('dossier_depose');
            });

        if ($userRole === 'antr') {
            if (empty($userWilaya)) {
                return response()->json(['success' => true, 'data' => [], 'total' => 0, 'current_page' => 1, 'last_page' => 1]);
            }
            $communeCodes = $this->getAntrCommuneCodes($userWilaya);
            if (empty($communeCodes)) {
                return response()->json(['success' => true, 'data' => [], 'total' => 0, 'current_page' => 1, 'last_page' => 1]);
            }
            $query->whereIn('code_commune', $communeCodes)
                  ->where('etat_das', 'accepte')
                  ->where('etat_comite_wilaya', 'accepte');
        } elseif (in_array($userRole, ['das', 'comite_wilaya'])) {
            if (empty($userWilaya)) {
                return response()->json(['success' => true, 'data' => [], 'total' => 0, 'current_page' => 1, 'last_page' => 1]);
            }
            $communeCodes = \App\Models\Commune::where('code_wilaya', $userWilaya)->pluck('code_comm')->toArray();
            if (empty($communeCodes)) {
                return response()->json(['success' => true, 'data' => [], 'total' => 0, 'current_page' => 1, 'last_page' => 1]);
            }
            $query->whereIn('code_commune', $communeCodes);
        } else {
            if (empty($userCommune)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'total' => 0,
                    'current_page' => 1,
                    'last_page' => 1
                ]);
            }
            $query->where('code_commune', $userCommune);
        }

        // Filter by school if provided
        if ($code_etabliss) {
            $query->where('code_etabliss', $code_etabliss);
        }

        // Filter by student ID (num_scolaire) if provided
        if ($num_scolaire_search) {
            $query->where('num_scolaire', 'like', '%' . $num_scolaire_search . '%');
        }

        $total = $query->count();
        $eleves = $query->orderBy('date_insertion', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        // Format data
        $data = $eleves->map(function($eleve) {
            $tuteur = $eleve->tuteur;
            return [
                'num_scolaire' => $eleve->num_scolaire,
                'nom' => $eleve->nom,
                'prenom' => $eleve->prenom,
                'date_naiss' => $eleve->date_naiss,
                'niv_scol' => $eleve->niv_scol,
                'classe_scol' => $eleve->classe_scol,
                'sexe' => $eleve->sexe,
                'code_etabliss' => $eleve->code_etabliss,
                'etablissement_nom' => $eleve->etablissement->nom_etabliss ?? '—',
                'dossier_depose' => $eleve->dossier_depose,
                'relation_tuteur' => $eleve->relation_tuteur,
                'relation_tuteur_text' => $eleve->relation_tuteur_text,
                'tuteur_nin' => $tuteur->nin ?? null,
                'tuteur_nom' => ($tuteur->nom_ar ?? $tuteur->nom_fr ?? '—') ?? '—',
                'tuteur_prenom' => ($tuteur->prenom_ar ?? $tuteur->prenom_fr ?? '—') ?? '—',
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

    // 🔹 Get paginated approved students (AJAX) - ts_commune: by commune; das/comite_wilaya: by wilaya
    public function getApprovedEleves(Request $request)
    {
        $ctx = $this->resolveAgentContext($request);
        $userRole = $ctx['role'] ?? null;
        if (!$ctx || !in_array($userRole, ['ts_commune', 'comune_ts', 'das', 'comite_wilaya', 'antr'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $userCommune = $ctx['commune'] ?? null;
        $userWilaya = $ctx['wilaya'] ?? null;
        $page = $request->input('page', 1);
        $perPage = 20;
        $code_etabliss = $request->input('code_etabliss');
        $num_scolaire_search = $request->input('num_scolaire_search');

        $query = Eleve::with(['tuteur', 'etablissement'])->where('dossier_depose', 'oui');

        if ($userRole === 'antr') {
            if (empty($userWilaya)) {
                return response()->json(['success' => true, 'data' => [], 'total' => 0, 'current_page' => 1, 'last_page' => 1]);
            }
            $communeCodes = $this->getAntrCommuneCodes($userWilaya);
            if (empty($communeCodes)) {
                return response()->json(['success' => true, 'data' => [], 'total' => 0, 'current_page' => 1, 'last_page' => 1]);
            }
            $query->whereIn('code_commune', $communeCodes)
                  ->where('etat_das', 'accepte')
                  ->where('etat_comite_wilaya', 'accepte');
        } elseif (in_array($userRole, ['das', 'comite_wilaya'])) {
            if (empty($userWilaya)) {
                return response()->json(['success' => true, 'data' => [], 'total' => 0, 'current_page' => 1, 'last_page' => 1]);
            }
            $communeCodes = \App\Models\Commune::where('code_wilaya', $userWilaya)->pluck('code_comm')->toArray();
            if (empty($communeCodes)) {
                return response()->json(['success' => true, 'data' => [], 'total' => 0, 'current_page' => 1, 'last_page' => 1]);
            }
            $query->whereIn('code_commune', $communeCodes);
        } else {
            if (empty($userCommune)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'total' => 0,
                    'current_page' => 1,
                    'last_page' => 1
                ]);
            }
            $query->where('code_commune', $userCommune);
        }

        // Filter by school if provided
        if ($code_etabliss) {
            $query->where('code_etabliss', $code_etabliss);
        }

        // Filter by student ID (num_scolaire) if provided
        if ($num_scolaire_search) {
            $query->where('num_scolaire', 'like', '%' . $num_scolaire_search . '%');
        }

        $total = $query->count();
        $eleves = $query->orderBy('date_insertion', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        // Format data
        $data = $eleves->map(function($eleve) {
            $tuteur = $eleve->tuteur;
            return [
                'num_scolaire' => $eleve->num_scolaire,
                'nom' => $eleve->nom,
                'prenom' => $eleve->prenom,
                'date_naiss' => $eleve->date_naiss,
                'niv_scol' => $eleve->niv_scol,
                'classe_scol' => $eleve->classe_scol,
                'sexe' => $eleve->sexe,
                'code_etabliss' => $eleve->code_etabliss,
                'etablissement_nom' => $eleve->etablissement->nom_etabliss ?? '—',
                'dossier_depose' => $eleve->dossier_depose,
                'relation_tuteur' => $eleve->relation_tuteur,
                'relation_tuteur_text' => $eleve->relation_tuteur_text,
                'tuteur_nin' => $tuteur->nin ?? null,
                'tuteur_nom' => ($tuteur->nom_ar ?? $tuteur->nom_fr ?? '—') ?? '—',
                'tuteur_prenom' => ($tuteur->prenom_ar ?? $tuteur->prenom_fr ?? '—') ?? '—',
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

    // 🔹 Show add student page for admin (ts_commune only; DAS cannot add students)
    public function showAddStudent()
    {
        // Ensure user is logged in
        if (!session('user_logged')) {
            return redirect()->route('user.login');
        }

        $userRole = session('user_role');

        // Only ts_commune / comune_ts can access this page; DAS is not allowed
        if ($userRole !== 'ts_commune' && $userRole !== 'comune_ts') {
            return redirect()->route('user.dashboard')->with('error', 'غير مصرح لك بهذه الصفحة');
        }

        // Get wilayas for dropdowns
        $wilayas = \App\Models\Wilaya::orderBy('lib_wil_ar')->get(['code_wil', 'lib_wil_ar']);
        $impersonating = (bool) session('impersonate_read_only');
        $loggedInAsName = session('logged_in_as_name', '');
        return view('users.add_student', compact('wilayas', 'impersonating', 'loggedInAsName'));
    }

    // 🔹 Get paginated tuteurs (AJAX) - ts_commune: by commune; das: by wilaya + eleves with dossier_depose=oui
    public function getTuteurs(Request $request)
    {
        $ctx = $this->resolveAgentContext($request);
        $userRole = $ctx['role'] ?? null;
        if (!$ctx || !in_array($userRole, ['ts_commune', 'comune_ts', 'das', 'comite_wilaya', 'antr'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $userCommune = $ctx['commune'] ?? null;
        $userWilaya = $ctx['wilaya'] ?? null;
        $page = $request->input('page', 1);
        $perPage = 20;
        $code_etabliss = $request->input('code_etabliss');
        $nin_search = $request->input('nin_search');
        $statusFilter = $request->input('status_filter');

        if ($userRole === 'antr') {
            if (empty($userWilaya)) {
                return response()->json(['success' => true, 'data' => [], 'total' => 0, 'current_page' => 1, 'last_page' => 1, 'per_page' => $perPage]);
            }
            $communeCodes = $this->getAntrCommuneCodes($userWilaya);
            if (empty($communeCodes)) {
                return response()->json(['success' => true, 'data' => [], 'total' => 0, 'current_page' => 1, 'last_page' => 1, 'per_page' => $perPage]);
            }
            $query = Tuteur::with(['eleves' => function ($q) use ($communeCodes, $code_etabliss) {
                $q->whereIn('code_commune', $communeCodes)->where('etat_das', 'accepte')->where('etat_comite_wilaya', 'accepte');
                if ($code_etabliss) { $q->where('code_etabliss', $code_etabliss); }
            }])
                ->whereHas('eleves', function ($q) use ($communeCodes, $code_etabliss) {
                    $q->whereIn('code_commune', $communeCodes)->where('etat_das', 'accepte')->where('etat_comite_wilaya', 'accepte');
                    if ($code_etabliss) { $q->where('code_etabliss', $code_etabliss); }
                });
        } elseif ($userRole === 'das') {
            if (empty($userWilaya)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'total' => 0,
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $perPage
                ]);
            }
            $communeCodes = \App\Models\Commune::where('code_wilaya', $userWilaya)->pluck('code_comm')->toArray();
            if (empty($communeCodes)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'total' => 0,
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $perPage
                ]);
            }
            $query = Tuteur::with(['eleves' => function ($q) use ($communeCodes, $code_etabliss) {
                $q->whereIn('code_commune', $communeCodes)->where('dossier_depose', 'oui');
                if ($code_etabliss) {
                    $q->where('code_etabliss', $code_etabliss);
                }
            }])
                ->whereHas('eleves', function ($q) use ($communeCodes, $code_etabliss) {
                    $q->whereIn('code_commune', $communeCodes)->where('dossier_depose', 'oui');
                    if ($code_etabliss) {
                        $q->where('code_etabliss', $code_etabliss);
                    }
                });
        } elseif ($userRole === 'comite_wilaya') {
            if (empty($userWilaya)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'total' => 0,
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $perPage
                ]);
            }
            $communeCodes = \App\Models\Commune::where('code_wilaya', $userWilaya)->pluck('code_comm')->toArray();
            if (empty($communeCodes)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'total' => 0,
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $perPage
                ]);
            }
            $query = Tuteur::with(['eleves' => function ($q) use ($communeCodes, $code_etabliss) {
                $q->whereIn('code_commune', $communeCodes)->whereIn('etat_das', ['accepte', 'refuse']);
                if ($code_etabliss) {
                    $q->where('code_etabliss', $code_etabliss);
                }
            }])
                ->whereHas('eleves', function ($q) use ($communeCodes, $code_etabliss) {
                    $q->whereIn('code_commune', $communeCodes)->whereIn('etat_das', ['accepte', 'refuse']);
                    if ($code_etabliss) {
                        $q->where('code_etabliss', $code_etabliss);
                    }
                });
        } else {
            if (empty($userCommune)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'total' => 0,
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $perPage
                ]);
            }
            $query = Tuteur::with(['eleves' => function ($q) use ($userCommune, $code_etabliss) {
                $q->where('code_commune', $userCommune);
                if ($code_etabliss) {
                    $q->where('code_etabliss', $code_etabliss);
                }
            }])
                ->whereHas('eleves', function ($q) use ($userCommune) {
                    $q->where('code_commune', $userCommune);
                });
            if ($code_etabliss) {
                $query->whereHas('eleves', function ($q) use ($userCommune, $code_etabliss) {
                    $q->where('code_commune', $userCommune)->where('code_etabliss', $code_etabliss);
                });
            }
        }

        // Status filter for DAS, comite_wilaya, and antr (accepte | refuse | pending) on tuteurs
        if (in_array($userRole, ['das', 'comite_wilaya', 'antr']) && $statusFilter) {
            if ($userRole === 'antr') {
                $scopeElevesAntr = function ($q) use ($communeCodes, $code_etabliss) {
                    $q->whereIn('code_commune', $communeCodes)
                      ->where('etat_das', 'accepte')
                      ->where('etat_comite_wilaya', 'accepte');
                    if ($code_etabliss) {
                        $q->where('code_etabliss', $code_etabliss);
                    }
                };
                if ($statusFilter === 'accepte') {
                    $query->whereDoesntHave('eleves', function ($q) use ($scopeElevesAntr) {
                        $scopeElevesAntr($q);
                        $q->where(function ($q2) {
                            $q2->where('etat_final', '!=', 'accepte')->orWhereNull('etat_final');
                        });
                    });
                } elseif ($statusFilter === 'refuse') {
                    $query->whereDoesntHave('eleves', function ($q) use ($scopeElevesAntr) {
                        $scopeElevesAntr($q);
                        $q->where('etat_final', '!=', 'refuse');
                    });
                } elseif ($statusFilter === 'pending') {
                    $query->whereHas('eleves', function ($q) use ($scopeElevesAntr) {
                        $scopeElevesAntr($q);
                        $q->where(function ($q2) {
                            $q2->whereNotIn('etat_final', ['accepte', 'refuse'])->orWhereNull('etat_final');
                        });
                    });
                }
            } else {
                $scopeEleves = function ($q) use ($communeCodes, $code_etabliss, $userRole) {
                    $q->whereIn('code_commune', $communeCodes);
                    if ($userRole === 'das') {
                        $q->where('dossier_depose', 'oui');
                    } else {
                        $q->whereIn('etat_das', ['accepte', 'refuse']);
                    }
                    if ($code_etabliss) {
                        $q->where('code_etabliss', $code_etabliss);
                    }
                };
                if ($statusFilter === 'accepte') {
                    $col = $userRole === 'das' ? 'etat_das' : 'etat_comite_wilaya';
                    $query->whereDoesntHave('eleves', function ($q) use ($scopeEleves, $col) {
                        $scopeEleves($q);
                        $q->where(function ($q2) use ($col) {
                            $q2->where($col, '!=', 'accepte')->orWhereNull($col);
                        });
                    });
                } elseif ($statusFilter === 'refuse') {
                    $col = $userRole === 'das' ? 'etat_das' : 'etat_comite_wilaya';
                    $query->whereDoesntHave('eleves', function ($q) use ($scopeEleves, $col) {
                        $scopeEleves($q);
                        $q->where($col, '!=', 'refuse');
                    });
                } elseif ($statusFilter === 'pending') {
                    $col = $userRole === 'das' ? 'etat_das' : 'etat_comite_wilaya';
                    $query->whereHas('eleves', function ($q) use ($scopeEleves, $col) {
                        $scopeEleves($q);
                        $q->where(function ($q2) use ($col) {
                            $q2->whereNotIn($col, ['accepte', 'refuse'])->orWhereNull($col);
                        });
                    });
                }
            }
        }

        if ($nin_search) {
            $query->where('nin', 'like', '%' . $nin_search . '%');
        }

        $total = $query->count();
        $tuteurs = $query->orderBy('date_insertion', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        $data = $tuteurs->map(function ($tuteur) use ($userRole) {
            $approvedCount = $userRole === 'das' ? $tuteur->eleves->count() : ($userRole === 'comite_wilaya' ? $tuteur->eleves->count() : $tuteur->eleves->where('dossier_depose', 'oui')->count());
            $totalCount = $tuteur->eleves->count();
            $allApproved = $totalCount > 0 && $approvedCount === $totalCount;
            $someApproved = $approvedCount > 0 && $approvedCount < $totalCount;
            
            $dasAcceptedCount = 0;
            $dasRefusedCount = 0;
            $comiteAcceptedCount = 0;
            $comiteRefusedCount = 0;
            $finalAcceptedCount = 0;
            $finalRefusedCount = 0;
            $refuseMotif = null;
            $refuseCnasRefuse = 0;
            $refuseCasnosRefuse = 0;

            if ($userRole === 'das') {
                $dasAcceptedCount = $tuteur->eleves->where('etat_das', 'accepte')->count();
                $dasRefusedCount = $tuteur->eleves->where('etat_das', 'refuse')->count();
                $refusedEleve = $tuteur->eleves->where('etat_das', 'refuse')->first();
                if ($refusedEleve) {
                    $refuseMotif = $refusedEleve->motif ?? '';
                    $refuseCnasRefuse = (int) ($refusedEleve->cnas_refuse ?? 0);
                    $refuseCasnosRefuse = (int) ($refusedEleve->casnos_refuse ?? 0);
                }
            } elseif ($userRole === 'comite_wilaya') {
                $dasAcceptedCount = $tuteur->eleves->where('etat_das', 'accepte')->count();
                $dasRefusedCount = $tuteur->eleves->where('etat_das', 'refuse')->count();
                $comiteAcceptedCount = $tuteur->eleves->where('etat_comite_wilaya', 'accepte')->count();
                $comiteRefusedCount = $tuteur->eleves->where('etat_comite_wilaya', 'refuse')->count();
                $refusedEleve = $tuteur->eleves->first(function ($e) {
                    return $e->etat_das === 'refuse' || $e->etat_comite_wilaya === 'refuse';
                });
                if ($refusedEleve) {
                    $refuseMotif = $refusedEleve->motif ?? '';
                    $refuseCnasRefuse = (int) ($refusedEleve->cnas_refuse ?? 0);
                    $refuseCasnosRefuse = (int) ($refusedEleve->casnos_refuse ?? 0);
                }
            } elseif ($userRole === 'antr') {
                $dasAcceptedCount = $totalCount; // all are accepted by DAS for antr
                $comiteAcceptedCount = $totalCount; // all are accepted by comite for antr
                $finalAcceptedCount = $tuteur->eleves->where('etat_final', 'accepte')->count();
                $finalRefusedCount = $tuteur->eleves->where('etat_final', 'refuse')->count();
            }

            return [
                'nin' => $tuteur->nin,
                'nom' => ($tuteur->nom_ar ?? $tuteur->nom_fr ?? ''),
                'prenom' => ($tuteur->prenom_ar ?? $tuteur->prenom_fr ?? ''),
                'cats' => $tuteur->cats ?? 'غير محدد',
                'situation_familiale' => $tuteur->situation_familiale ?? null,
                'total_count' => $totalCount,
                'approved_count' => $approvedCount,
                'all_approved' => $allApproved,
                'some_approved' => $someApproved,
                'das_accepted_count' => $dasAcceptedCount,
                'das_refused_count' => $dasRefusedCount,
                'comite_accepted_count' => $comiteAcceptedCount,
                'comite_refused_count' => $comiteRefusedCount,
                'final_accepted_count' => $finalAcceptedCount,
                'final_refused_count' => $finalRefusedCount,
                'refuse_motif' => $refuseMotif,
                'refuse_cnas_refuse' => $refuseCnasRefuse,
                'refuse_casnos_refuse' => $refuseCasnosRefuse,
                'pending_appeals_count' => $tuteur->eleves->where('appeal_status', 'pending')->count(),
                'first_pending_appeal_num_scolaire' => \App\Models\Eleve::where('code_tuteur', $tuteur->nin)->where('appeal_status', 'pending')->value('num_scolaire'),
                'eleves' => $tuteur->eleves
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $total,
            'current_page' => (int) $page,
            'last_page' => (int) ceil($total / $perPage),
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

    /**
     * Admin-only API endpoint to create users from dashboard.
     */
    public function storeByAdmin(Request $request)
    {
        $authUser = $request->user();
        if (!$authUser || $authUser->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

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
            'role' => 'required|in:admin,ts_commune,das,comite_wilaya,antr',
            'date_insertion' => 'nullable|date',
        ]);

        // Keep these server-controlled for consistency and security.
        $validated['statut'] = '1';
        $validated['pass'] = Hash::make($validated['pass']);
        $validated['date_insertion'] = now();

        // Normalize location fields based on selected role.
        // ts_commune: wilaya + commune, das/comite_wilaya/antr: wilaya only, admin: none.
        if ($validated['role'] === 'ts_commune') {
            if (empty($validated['code_wilaya']) || empty($validated['code_comm'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'يرجى اختيار الولاية والبلدية لرتبة تقني البلدية',
                ], 422);
            }
        } elseif (in_array($validated['role'], ['das', 'comite_wilaya', 'antr'], true)) {
            if (empty($validated['code_wilaya'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'يرجى اختيار الولاية لهذه الرتبة',
                ], 422);
            }
            $validated['code_comm'] = null;
        } else {
            $validated['code_comm'] = null;
            $validated['code_wilaya'] = null;
        }

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء المستخدم بنجاح',
            'data' => $user
        ], 201);
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

        // Delete old tokens
        $user->tokens()->delete();

        // Create new API token for future API calls
        $token = $user->createToken('user-api-token', ['*'], now()->addDays(30))->plainTextToken;

        // 🧠 Store session with commune name and API token
        session([
            'user_logged' => true,
            'user_code' => $user->code_user,
            'user_name' => $user->nom_user . ' ' . $user->prenom_user,
            'user_role' => $user->role,
            'user_commune' => $user->commune?->lib_comm_ar ?? 'غير محددة',
            'user_commune_code' => $user->code_comm,
            'user_wilaya' => $user->code_wilaya,
            'api_token' => $token, // Store token in session for API calls
        ]);

        return redirect()->route('user.dashboard');
    }


    // 🟡 Logout
    public function logout()
    {
        session()->forget([
            'user_logged', 'user_code', 'user_name', 'user_role', 'user_commune', 'user_commune_code', 'user_wilaya', 'api_token',
            'impersonate_read_only', 'logged_in_as_name',
        ]);
        return redirect()->route('user.login')->with('success', 'تم تسجيل الخروج بنجاح');
    }

    /**
     * Admin: ts_commune management page — wilaya grid, then commune grid; open "as" ts_commune in new window.
     */
    public function showTsCommuneManagement()
    {
        if (!session('user_logged') || session('user_role') !== 'admin') {
            return redirect()->route('user.login')->with('error', 'غير مصرح');
        }
        return view('users.ts_commune_management');
    }

    /**
     * Admin: DAS management page — wilaya grid, then DAS users grid; open as DAS in new window.
     */
    public function showDasManagement()
    {
        if (!session('user_logged') || session('user_role') !== 'admin') {
            return redirect()->route('user.login')->with('error', 'غير مصرح');
        }
        return view('users.das_management');
    }

    /**
     * Admin: comite_wilaya management page — wilaya grid, then users grid; open as comite_wilaya.
     */
    public function showComiteWilayaManagement()
    {
        if (!session('user_logged') || session('user_role') !== 'admin') {
            return redirect()->route('user.login')->with('error', 'غير مصرح');
        }
        return view('users.comite_wilaya_management');
    }

    /**
     * Admin: ATR (antr) management page — wilaya grid, then users grid; open as ATR.
     */
    public function showAntrManagement()
    {
        if (!session('user_logged') || session('user_role') !== 'admin') {
            return redirect()->route('user.login')->with('error', 'غير مصرح');
        }
        return view('users.antr_management');
    }

    /**
     * Admin: list wilayas for ts_commune management (JSON).
     */
    public function getWilayasForAdmin(Request $request)
    {
        if (!session('user_logged') || session('user_role') !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $wilayas = Wilaya::orderBy('lib_wil_ar')->get(['code_wil', 'lib_wil_ar', 'lib_wil_fr']);
        return response()->json(['success' => true, 'wilayas' => $wilayas]);
    }

    /**
     * Admin: list communes (baladias) by wilaya for ts_commune management (JSON).
     */
    public function getCommunesByWilayaForAdmin(Request $request)
    {
        if (!session('user_logged') || session('user_role') !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $codeWilaya = $request->query('code_wilaya');
        if (empty($codeWilaya)) {
            return response()->json(['success' => false, 'message' => 'code_wilaya required'], 400);
        }
        $communes = Commune::where('code_wilaya', $codeWilaya)
            ->orderBy('lib_comm_ar')
            ->get(['code_comm', 'lib_comm_ar', 'lib_comm_fr', 'code_wilaya']);
        return response()->json(['success' => true, 'communes' => $communes]);
    }

    /**
     * Admin: list ts_commune users for a given commune (for "logged in as" user picker).
     */
    public function getUsersByCommuneForAdmin(Request $request)
    {
        if (!session('user_logged') || session('user_role') !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $codeComm = $request->query('code_comm');
        $codeWilaya = $request->query('code_wilaya');
        if (empty($codeComm) || empty($codeWilaya)) {
            return response()->json(['success' => false, 'message' => 'code_comm and code_wilaya required'], 400);
        }
        $users = User::where('code_comm', $codeComm)
            ->where('code_wilaya', $codeWilaya)
            ->whereIn('role', ['ts_commune', 'comune_ts'])
            ->orderBy('nom_user')
            ->get(['code_user', 'nom_user', 'prenom_user']);
        return response()->json(['success' => true, 'users' => $users]);
    }

    /**
     * Admin: create impersonation token for a ts_commune user.
     * If code_user is provided, use that user (must belong to the commune); else first user for commune.
     * Returns JSON with { url } to open in new window.
     */
    public function impersonateTsCommune(Request $request)
    {
        if (!session('user_logged') || session('user_role') !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $codeComm = $request->query('code_comm');
        $codeWilaya = $request->query('code_wilaya');
        $codeUser = $request->query('code_user');
        if (empty($codeComm) || empty($codeWilaya)) {
            return response()->json(['success' => false, 'message' => 'code_comm and code_wilaya required'], 400);
        }
        if (!empty($codeUser)) {
            $user = User::where('code_user', $codeUser)
                ->where('code_comm', $codeComm)
                ->where('code_wilaya', $codeWilaya)
                ->whereIn('role', ['ts_commune', 'comune_ts'])
                ->first();
        } else {
            $user = User::where('code_comm', $codeComm)
                ->where('code_wilaya', $codeWilaya)
                ->whereIn('role', ['ts_commune', 'comune_ts'])
                ->orderBy('code_user')
                ->first();
        }
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'No ts_commune user for this commune'], 404);
        }
        $payload = [
            'code_user' => $user->code_user,
            'exp' => now()->addHours(2)->timestamp,
        ];
        $encrypted = Crypt::encryptString(json_encode($payload));
        $token = str_replace(['+', '/', '='], ['-', '_', ''], $encrypted);
        $url = route('user.impersonate.apply', ['token' => $token]);
        return response()->json(['success' => true, 'url' => $url]);
    }

    /**
     * Admin: list DAS users for a given wilaya (for "logged in as" picker).
     */
    public function getDasUsersByWilayaForAdmin(Request $request)
    {
        if (!session('user_logged') || session('user_role') !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $codeWilaya = $request->query('code_wilaya');
        if (empty($codeWilaya)) {
            return response()->json(['success' => false, 'message' => 'code_wilaya required'], 400);
        }
        $users = User::where('code_wilaya', $codeWilaya)
            ->where('role', 'das')
            ->orderBy('nom_user')
            ->get(['code_user', 'nom_user', 'prenom_user']);
        return response()->json(['success' => true, 'users' => $users]);
    }

    /**
     * Admin: create impersonation token for a DAS user of the given wilaya.
     * code_user is required; user must be DAS for that wilaya.
     */
    public function impersonateDas(Request $request)
    {
        if (!session('user_logged') || session('user_role') !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $codeWilaya = $request->query('code_wilaya');
        $codeUser = $request->query('code_user');
        if (empty($codeWilaya) || empty($codeUser)) {
            return response()->json(['success' => false, 'message' => 'code_wilaya and code_user required'], 400);
        }
        $user = User::where('code_user', $codeUser)
            ->where('code_wilaya', $codeWilaya)
            ->where('role', 'das')
            ->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'No DAS user found for this wilaya'], 404);
        }
        $payload = [
            'code_user' => $user->code_user,
            'exp' => now()->addHours(2)->timestamp,
        ];
        $encrypted = Crypt::encryptString(json_encode($payload));
        $token = str_replace(['+', '/', '='], ['-', '_', ''], $encrypted);
        $url = route('user.impersonate.apply', ['token' => $token]);
        return response()->json(['success' => true, 'url' => $url]);
    }

    /**
     * Admin: list comite_wilaya users for a given wilaya (for "logged in as" picker).
     */
    public function getComiteWilayaUsersByWilayaForAdmin(Request $request)
    {
        if (!session('user_logged') || session('user_role') !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $codeWilaya = $request->query('code_wilaya');
        if (empty($codeWilaya)) {
            return response()->json(['success' => false, 'message' => 'code_wilaya required'], 400);
        }
        $users = User::where('code_wilaya', $codeWilaya)
            ->where('role', 'comite_wilaya')
            ->orderBy('nom_user')
            ->get(['code_user', 'nom_user', 'prenom_user']);
        return response()->json(['success' => true, 'users' => $users]);
    }

    /**
     * Admin: create impersonation token for a comite_wilaya user of the given wilaya.
     */
    public function impersonateComiteWilaya(Request $request)
    {
        if (!session('user_logged') || session('user_role') !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $codeWilaya = $request->query('code_wilaya');
        $codeUser = $request->query('code_user');
        if (empty($codeWilaya) || empty($codeUser)) {
            return response()->json(['success' => false, 'message' => 'code_wilaya and code_user required'], 400);
        }
        $user = User::where('code_user', $codeUser)
            ->where('code_wilaya', $codeWilaya)
            ->where('role', 'comite_wilaya')
            ->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'No comite_wilaya user found for this wilaya'], 404);
        }
        $payload = [
            'code_user' => $user->code_user,
            'exp' => now()->addHours(2)->timestamp,
        ];
        $encrypted = Crypt::encryptString(json_encode($payload));
        $token = str_replace(['+', '/', '='], ['-', '_', ''], $encrypted);
        $url = route('user.impersonate.apply', ['token' => $token]);
        return response()->json(['success' => true, 'url' => $url]);
    }

    /**
     * Admin: list regions (antennes) for ATR management (JSON).
     */
    public function getAntennesForAdmin(Request $request)
    {
        if (!session('user_logged') || session('user_role') !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Exclude "الأجنبي" (99) and "المديرية العامة" (0) — they don't exist for ATR management
        $antennes = Antenne::whereNotIn('code_ar', ['0', '99'])
            ->orderBy('lib_ar_ar')
            ->get(['code_ar', 'lib_ar_ar', 'lib_ar_fr']);
        return response()->json(['success' => true, 'antennes' => $antennes]);
    }

    /**
     * Admin: list ATR (antr) users for a given region (code_ar) or wilaya (code_wilaya) for "logged in as" picker.
     * When code_ar is provided, returns all ATR users whose wilaya belongs to that region.
     */
    public function getAntrUsersByWilayaForAdmin(Request $request)
    {
        if (!session('user_logged') || session('user_role') !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $codeAr = $request->query('code_ar');
        $codeWilaya = $request->query('code_wilaya');
        if (!empty($codeAr)) {
            $wilayaCodes = Wilaya::where('code_ar', $codeAr)->pluck('code_wil')->toArray();
            if (empty($wilayaCodes)) {
                return response()->json(['success' => true, 'users' => []]);
            }
            $users = User::whereIn('code_wilaya', $wilayaCodes)
                ->where('role', 'antr')
                ->orderBy('nom_user')
                ->get(['code_user', 'nom_user', 'prenom_user', 'code_wilaya']);
        } elseif (!empty($codeWilaya)) {
            $users = User::where('code_wilaya', $codeWilaya)
                ->where('role', 'antr')
                ->orderBy('nom_user')
                ->get(['code_user', 'nom_user', 'prenom_user', 'code_wilaya']);
        } else {
            return response()->json(['success' => false, 'message' => 'code_ar or code_wilaya required'], 400);
        }
        return response()->json(['success' => true, 'users' => $users]);
    }

    /**
     * Admin: create impersonation token for an ATR (antr) user of the given wilaya.
     */
    public function impersonateAntr(Request $request)
    {
        if (!session('user_logged') || session('user_role') !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $codeWilaya = $request->query('code_wilaya');
        $codeUser = $request->query('code_user');
        if (empty($codeWilaya) || empty($codeUser)) {
            return response()->json(['success' => false, 'message' => 'code_wilaya and code_user required'], 400);
        }
        $user = User::where('code_user', $codeUser)
            ->where('code_wilaya', $codeWilaya)
            ->where('role', 'antr')
            ->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'No ATR user found for this wilaya'], 404);
        }
        $payload = [
            'code_user' => $user->code_user,
            'exp' => now()->addHours(2)->timestamp,
        ];
        $encrypted = Crypt::encryptString(json_encode($payload));
        $token = str_replace(['+', '/', '='], ['-', '_', ''], $encrypted);
        $url = route('user.impersonate.apply', ['token' => $token]);
        return response()->json(['success' => true, 'url' => $url]);
    }

    /**
     * Apply impersonation token (no auth): set session as that user (ts_commune, DAS, comite_wilaya, or ATR), read-only, redirect to dashboard.
     */
    public function applyImpersonation(Request $request, string $token)
    {
        try {
            $restored = str_replace(['-', '_'], ['+', '/'], $token);
            $padding = strlen($restored) % 4;
            if ($padding) {
                $restored .= str_repeat('=', 4 - $padding);
            }
            $payload = json_decode(Crypt::decryptString($restored), true);
        } catch (\Throwable $e) {
            return redirect()->route('user.login')->with('error', 'رابط منتهي أو غير صالح');
        }
        if (empty($payload['code_user']) || empty($payload['exp']) || $payload['exp'] < time()) {
            return redirect()->route('user.login')->with('error', 'رابط منتهي الصلاحية');
        }
        $user = User::with('commune')->where('code_user', $payload['code_user'])->first();
        if (!$user || !in_array($user->role, ['ts_commune', 'comune_ts', 'das', 'comite_wilaya', 'antr'])) {
            return redirect()->route('user.login')->with('error', 'مستخدم غير موجود');
        }
        $user->tokens()->delete();
        $apiToken = $user->createToken('user-api-token', ['*'], now()->addHours(2))->plainTextToken;
        session([
            'user_logged' => true,
            'user_code' => $user->code_user,
            'user_name' => $user->nom_user . ' ' . $user->prenom_user,
            'user_role' => $user->role,
            'user_commune' => $user->commune?->lib_comm_ar ?? 'غير محددة',
            'user_commune_code' => $user->code_comm,
            'user_wilaya' => $user->code_wilaya,
            'api_token' => $apiToken,
            'impersonate_read_only' => true,
            'logged_in_as_name' => $user->nom_user . ' ' . $user->prenom_user,
        ]);
        session()->save();
        return redirect()->route('user.dashboard')->with('success', 'تم الدخول للعرض فقط باسم: ' . $user->nom_user . ' ' . $user->prenom_user);
    }

    /**
     * End impersonation: clear session and redirect to login.
     */
    public function endImpersonation()
    {
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('user.login')->with('success', 'تم إنهاء وضع العرض فقط');
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

        // Also create session for web routes compatibility (user_wilaya + api_token for API calls from blade)
        session([
            'user_logged' => true,
            'user_code' => $user->code_user,
            'user_name' => $user->nom_user . ' ' . $user->prenom_user,
            'user_role' => $user->role,
            'user_commune' => $user->commune?->lib_comm_ar ?? 'غير محددة',
            'user_commune_code' => $user->code_comm,
            'user_wilaya' => $user->code_wilaya,
            'user_wilaya_code' => $user->code_wilaya,
            'user_nom' => $user->nom_user,
            'user_prenom' => $user->prenom_user,
            'api_token' => $token,
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
     * DAS API: Paginated eleves for logged-in DAS user.
     * Only eleves whose code_commune belongs to the user's wilaya and dossier_depose = 'oui'.
     */
    public function apiDasEleves(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->role !== 'das') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $codeWilaya = $user->code_wilaya;
        if (empty($codeWilaya)) {
            return response()->json([
                'success' => true,
                'data' => [],
                'total' => 0,
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 20
            ]);
        }

        $communeCodes = \App\Models\Commune::where('code_wilaya', $codeWilaya)->pluck('code_comm')->toArray();
        if (empty($communeCodes)) {
            return response()->json([
                'success' => true,
                'data' => [],
                'total' => 0,
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 20
            ]);
        }

        $page = $request->input('page', 1);
        $perPage = 20;
        $code_etabliss = $request->input('code_etabliss');
        $num_scolaire_search = $request->input('num_scolaire_search');

        $query = Eleve::with(['tuteur', 'etablissement'])
            ->whereIn('code_commune', $communeCodes)
            ->where('dossier_depose', 'oui');

        if ($code_etabliss) {
            $query->where('code_etabliss', $code_etabliss);
        }
        if ($num_scolaire_search) {
            $query->where('num_scolaire', 'like', '%' . $num_scolaire_search . '%');
        }

        $total = $query->count();
        $eleves = $query->orderBy('date_insertion', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        $data = $eleves->map(function ($eleve) {
            $tuteur = $eleve->tuteur;
            return [
                'num_scolaire' => $eleve->num_scolaire,
                'nom' => $eleve->nom,
                'prenom' => $eleve->prenom,
                'date_naiss' => $eleve->date_naiss,
                'niv_scol' => $eleve->niv_scol,
                'classe_scol' => $eleve->classe_scol,
                'sexe' => $eleve->sexe,
                'code_etabliss' => $eleve->code_etabliss,
                'etablissement_nom' => $eleve->etablissement->nom_etabliss ?? '—',
                'dossier_depose' => $eleve->dossier_depose,
                'relation_tuteur' => $eleve->relation_tuteur,
                'relation_tuteur_text' => $eleve->relation_tuteur_text,
                'tuteur_nin' => $tuteur->nin ?? null,
                'tuteur_nom' => ($tuteur->nom_ar ?? $tuteur->nom_fr ?? '—') ?? '—',
                'tuteur_prenom' => ($tuteur->prenom_ar ?? $tuteur->prenom_fr ?? '—') ?? '—',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $total,
            'current_page' => (int) $page,
            'last_page' => (int) ceil($total / $perPage),
            'per_page' => $perPage
        ]);
    }

    /**
     * DAS API: Paginated tuteurs for logged-in DAS user.
     * Only tuteurs who have at least one eleve in the user's wilaya with dossier_depose = 'oui'.
     */
    public function apiDasTuteurs(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->role !== 'das') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $codeWilaya = $user->code_wilaya;
        if (empty($codeWilaya)) {
            return response()->json([
                'success' => true,
                'data' => [],
                'total' => 0,
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 20
            ]);
        }

        $communeCodes = \App\Models\Commune::where('code_wilaya', $codeWilaya)->pluck('code_comm')->toArray();
        if (empty($communeCodes)) {
            return response()->json([
                'success' => true,
                'data' => [],
                'total' => 0,
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 20
            ]);
        }

        $page = $request->input('page', 1);
        $perPage = 20;
        $code_etabliss = $request->input('code_etabliss');
        $nin_search = $request->input('nin_search');

        $query = Tuteur::with(['eleves' => function ($q) use ($communeCodes, $code_etabliss) {
            $q->whereIn('code_commune', $communeCodes)->where('dossier_depose', 'oui');
            if ($code_etabliss) {
                $q->where('code_etabliss', $code_etabliss);
            }
        }])
            ->whereHas('eleves', function ($q) use ($communeCodes, $code_etabliss) {
                $q->whereIn('code_commune', $communeCodes)->where('dossier_depose', 'oui');
                if ($code_etabliss) {
                    $q->where('code_etabliss', $code_etabliss);
                }
            });

        if ($nin_search) {
            $query->where('nin', 'like', '%' . $nin_search . '%');
        }

        $total = $query->count();
        $tuteurs = $query->orderBy('date_insertion', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        $data = $tuteurs->map(function ($tuteur) {
            $approvedCount = $tuteur->eleves->count();
            $totalCount = $tuteur->eleves->count();
            return [
                'nin' => $tuteur->nin,
                'nom' => ($tuteur->nom_ar ?? $tuteur->nom_fr ?? ''),
                'prenom' => ($tuteur->prenom_ar ?? $tuteur->prenom_fr ?? ''),
                'cats' => $tuteur->cats ?? 'غير محدد',
                'situation_familiale' => $tuteur->situation_familiale ?? null,
                'total_count' => $totalCount,
                'approved_count' => $approvedCount,
                'all_approved' => $totalCount > 0,
                'some_approved' => false,
                'eleves' => $tuteur->eleves
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $total,
            'current_page' => (int) $page,
            'last_page' => (int) ceil($total / $perPage),
            'per_page' => $perPage
        ]);
    }

    /**
     * DAS Accept Eleve - set etat_das to 'accepte'
     */
    public function dasAcceptEleve(Request $request, $num_scolaire)
    {
        $user = $request->user();
        if (!$user || $user->role !== 'das') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $codeWilaya = $user->code_wilaya;
        if (empty($codeWilaya)) {
            return response()->json(['success' => false, 'message' => 'No wilaya bound to user'], 403);
        }

        $communeCodes = \App\Models\Commune::where('code_wilaya', $codeWilaya)->pluck('code_comm')->toArray();
        if (empty($communeCodes)) {
            return response()->json(['success' => false, 'message' => 'No communes in your wilaya'], 404);
        }

        $eleve = Eleve::where('num_scolaire', $num_scolaire)
            ->whereIn('code_commune', $communeCodes)
            ->where('dossier_depose', 'oui')
            ->first();

        if (!$eleve) {
            return response()->json(['success' => false, 'message' => 'Eleve not found or not in your wilaya'], 404);
        }

        $eleve->etat_das = 'accepte';
        $eleve->motif = null;
        $eleve->cnas_refuse = 0;
        $eleve->casnos_refuse = 0;
        $eleve->save();

        return response()->json([
            'success' => true,
            'message' => 'Eleve accepted successfully'
        ]);
    }

    /**
     * DAS Decline Eleve - set etat_das to 'refuse'
     */
    public function dasDeclineEleve(Request $request, $num_scolaire)
    {
        $user = $request->user();
        if (!$user || $user->role !== 'das') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $codeWilaya = $user->code_wilaya;
        if (empty($codeWilaya)) {
            return response()->json(['success' => false, 'message' => 'No wilaya bound to user'], 403);
        }

        $communeCodes = \App\Models\Commune::where('code_wilaya', $codeWilaya)->pluck('code_comm')->toArray();
        if (empty($communeCodes)) {
            return response()->json(['success' => false, 'message' => 'No communes in your wilaya'], 404);
        }

        $eleve = Eleve::where('num_scolaire', $num_scolaire)
            ->whereIn('code_commune', $communeCodes)
            ->where('dossier_depose', 'oui')
            ->first();

        if (!$eleve) {
            return response()->json(['success' => false, 'message' => 'Eleve not found or not in your wilaya'], 404);
        }

        $motif = $request->input('motif', '');
        $cnasRefuse = (int) $request->input('cnas_refuse', 0);
        $casnosRefuse = (int) $request->input('casnos_refuse', 0);

        $eleve->etat_das = 'refuse';
        $eleve->motif = $motif;
        $eleve->cnas_refuse = $cnasRefuse ? 1 : 0;
        $eleve->casnos_refuse = $casnosRefuse ? 1 : 0;
        $eleve->save();

        return response()->json([
            'success' => true,
            'message' => 'Eleve declined successfully'
        ]);
    }

    /**
     * DAS Accept Tuteur - set etat_das to 'accepte' for all their eleves in the wilaya
     */
    public function dasAcceptTuteur(Request $request, $nin)
    {
        $user = $request->user();
        if (!$user || $user->role !== 'das') {
            Log::warning('dasAcceptTuteur unauthorized access attempt', [
                'nin' => $nin,
                'user_id' => $user->code_user ?? null,
                'role' => $user->role ?? null,
            ]);
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $codeWilaya = $user->code_wilaya;
        if (empty($codeWilaya)) {
            Log::warning('dasAcceptTuteur called without bound wilaya', [
                'nin' => $nin,
                'user_id' => $user->code_user,
            ]);
            return response()->json(['success' => false, 'message' => 'No wilaya bound to user'], 403);
        }

        $communeCodes = \App\Models\Commune::where('code_wilaya', $codeWilaya)->pluck('code_comm')->toArray();
        if (empty($communeCodes)) {
            Log::warning('dasAcceptTuteur no communes found for wilaya', [
                'nin' => $nin,
                'user_id' => $user->code_user,
                'code_wilaya' => $codeWilaya,
            ]);
            return response()->json(['success' => false, 'message' => 'No communes in your wilaya'], 404);
        }

        Log::info('dasAcceptTuteur bulk accept started', [
            'nin' => $nin,
            'user_id' => $user->code_user,
            'code_wilaya' => $codeWilaya,
            'communes_count' => count($communeCodes),
        ]);

        $count = Eleve::where('code_tuteur', $nin)
            ->whereIn('code_commune', $communeCodes)
            ->where('dossier_depose', 'oui')
            ->update([
                'etat_das' => 'accepte',
                'motif' => null,
                'cnas_refuse' => 0,
                'casnos_refuse' => 0,
            ]);

        Log::info('dasAcceptTuteur bulk accept finished', [
            'nin' => $nin,
            'user_id' => $user->code_user,
            'accepted_count' => $count,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tuteur eleves accepted successfully',
            'count' => $count
        ]);
    }

    /**
     * DAS Decline Tuteur - set etat_das to 'refuse' for all their eleves in the wilaya
     */
    public function dasDeclineTuteur(Request $request, $nin)
    {
        $user = $request->user();
        if (!$user || $user->role !== 'das') {
            Log::warning('dasDeclineTuteur unauthorized access attempt', [
                'nin' => $nin,
                'user_id' => $user->code_user ?? null,
                'role' => $user->role ?? null,
            ]);
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $codeWilaya = $user->code_wilaya;
        if (empty($codeWilaya)) {
            Log::warning('dasDeclineTuteur called without bound wilaya', [
                'nin' => $nin,
                'user_id' => $user->code_user,
            ]);
            return response()->json(['success' => false, 'message' => 'No wilaya bound to user'], 403);
        }

        $communeCodes = \App\Models\Commune::where('code_wilaya', $codeWilaya)->pluck('code_comm')->toArray();
        if (empty($communeCodes)) {
            Log::warning('dasDeclineTuteur no communes found for wilaya', [
                'nin' => $nin,
                'user_id' => $user->code_user,
                'code_wilaya' => $codeWilaya,
            ]);
            return response()->json(['success' => false, 'message' => 'No communes in your wilaya'], 404);
        }

        $motif = $request->input('motif', '');
        $cnasRefuse = (int) $request->input('cnas_refuse', 0);
        $casnosRefuse = (int) $request->input('casnos_refuse', 0);

        Log::info('dasDeclineTuteur bulk decline started', [
            'nin' => $nin,
            'user_id' => $user->code_user,
            'code_wilaya' => $codeWilaya,
            'communes_count' => count($communeCodes),
            'motif' => $motif,
            'cnas_refuse' => $cnasRefuse,
            'casnos_refuse' => $casnosRefuse,
        ]);

        $count = Eleve::where('code_tuteur', $nin)
            ->whereIn('code_commune', $communeCodes)
            ->where('dossier_depose', 'oui')
            ->update([
                'etat_das' => 'refuse',
                'motif' => $motif,
                'cnas_refuse' => $cnasRefuse ? 1 : 0,
                'casnos_refuse' => $casnosRefuse ? 1 : 0
            ]);

        Log::info('dasDeclineTuteur bulk decline finished', [
            'nin' => $nin,
            'user_id' => $user->code_user,
            'declined_count' => $count,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tuteur eleves declined successfully',
            'count' => $count
        ]);
    }

    /**
     * Comité Wilaya - same as DAS but only eleves with etat_das in (accepte, refuse); sets etat_comite_wilaya
     */
    public function comiteAcceptEleve(Request $request, $num_scolaire)
    {
        $user = $request->user();
        if (!$user || $user->role !== 'comite_wilaya') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $codeWilaya = $user->code_wilaya;
        if (empty($codeWilaya)) {
            return response()->json(['success' => false, 'message' => 'No wilaya bound to user'], 403);
        }
        $communeCodes = \App\Models\Commune::where('code_wilaya', $codeWilaya)->pluck('code_comm')->toArray();
        if (empty($communeCodes)) {
            return response()->json(['success' => false, 'message' => 'No communes in your wilaya'], 404);
        }
        $eleve = Eleve::where('num_scolaire', $num_scolaire)
            ->whereIn('code_commune', $communeCodes)
            ->whereIn('etat_das', ['accepte', 'refuse'])
            ->first();
        if (!$eleve) {
            return response()->json(['success' => false, 'message' => 'Eleve not found or not in your wilaya'], 404);
        }
        $eleve->etat_comite_wilaya = 'accepte';
        $eleve->motif = null;
        $eleve->cnas_refuse = 0;
        $eleve->casnos_refuse = 0;
        $eleve->save();
        return response()->json(['success' => true, 'message' => 'Eleve accepted successfully']);
    }

    public function comiteDeclineEleve(Request $request, $num_scolaire)
    {
        $user = $request->user();
        if (!$user || $user->role !== 'comite_wilaya') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $codeWilaya = $user->code_wilaya;
        if (empty($codeWilaya)) {
            return response()->json(['success' => false, 'message' => 'No wilaya bound to user'], 403);
        }
        $communeCodes = \App\Models\Commune::where('code_wilaya', $codeWilaya)->pluck('code_comm')->toArray();
        if (empty($communeCodes)) {
            return response()->json(['success' => false, 'message' => 'No communes in your wilaya'], 404);
        }
        $eleve = Eleve::where('num_scolaire', $num_scolaire)
            ->whereIn('code_commune', $communeCodes)
            ->whereIn('etat_das', ['accepte', 'refuse'])
            ->first();
        if (!$eleve) {
            return response()->json(['success' => false, 'message' => 'Eleve not found or not in your wilaya'], 404);
        }
        $request->validate([
            'motif' => 'required|string|min:10',
        ], [
            'motif.required' => 'يرجى إدخال سبب الرفض.',
            'motif.min' => 'سبب الرفض يجب أن يكون 10 أحرف على الأقل.',
        ]);
        $motif = $request->input('motif', '');
        $cnasRefuse = (int) $request->input('cnas_refuse', 0);
        $casnosRefuse = (int) $request->input('casnos_refuse', 0);
        $eleve->etat_comite_wilaya = 'refuse';
        $eleve->motif = $motif;
        $eleve->cnas_refuse = $cnasRefuse ? 1 : 0;
        $eleve->casnos_refuse = $casnosRefuse ? 1 : 0;
        $eleve->save();
        return response()->json(['success' => true, 'message' => 'Eleve declined successfully']);
    }

    public function comiteAcceptTuteur(Request $request, $nin)
    {
        $user = $request->user();
        if (!$user || $user->role !== 'comite_wilaya') {
            Log::warning('comiteAcceptTuteur unauthorized access attempt', [
                'nin' => $nin,
                'user_id' => $user->code_user ?? null,
                'role' => $user->role ?? null,
            ]);
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $codeWilaya = $user->code_wilaya;
        if (empty($codeWilaya)) {
            Log::warning('comiteAcceptTuteur called without bound wilaya', [
                'nin' => $nin,
                'user_id' => $user->code_user,
            ]);
            return response()->json(['success' => false, 'message' => 'No wilaya bound to user'], 403);
        }
        $communeCodes = \App\Models\Commune::where('code_wilaya', $codeWilaya)->pluck('code_comm')->toArray();
        if (empty($communeCodes)) {
            Log::warning('comiteAcceptTuteur no communes found for wilaya', [
                'nin' => $nin,
                'user_id' => $user->code_user,
                'code_wilaya' => $codeWilaya,
            ]);
            return response()->json(['success' => false, 'message' => 'No communes in your wilaya'], 404);
        }
        Log::info('comiteAcceptTuteur bulk accept started', [
            'nin' => $nin,
            'user_id' => $user->code_user,
            'code_wilaya' => $codeWilaya,
            'communes_count' => count($communeCodes),
        ]);
        $count = Eleve::where('code_tuteur', $nin)
            ->whereIn('code_commune', $communeCodes)
            ->whereIn('etat_das', ['accepte', 'refuse'])
            ->update([
                'etat_comite_wilaya' => 'accepte',
                'motif' => null,
                'cnas_refuse' => 0,
                'casnos_refuse' => 0,
            ]);
        Log::info('comiteAcceptTuteur bulk accept finished', [
            'nin' => $nin,
            'user_id' => $user->code_user,
            'accepted_count' => $count,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Tuteur eleves accepted successfully',
            'count' => $count
        ]);
    }

    public function comiteDeclineTuteur(Request $request, $nin)
    {
        $user = $request->user();
        if (!$user || $user->role !== 'comite_wilaya') {
            Log::warning('comiteDeclineTuteur unauthorized access attempt', [
                'nin' => $nin,
                'user_id' => $user->code_user ?? null,
                'role' => $user->role ?? null,
            ]);
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $codeWilaya = $user->code_wilaya;
        if (empty($codeWilaya)) {
            Log::warning('comiteDeclineTuteur called without bound wilaya', [
                'nin' => $nin,
                'user_id' => $user->code_user,
            ]);
            return response()->json(['success' => false, 'message' => 'No wilaya bound to user'], 403);
        }
        $communeCodes = \App\Models\Commune::where('code_wilaya', $codeWilaya)->pluck('code_comm')->toArray();
        if (empty($communeCodes)) {
            Log::warning('comiteDeclineTuteur no communes found for wilaya', [
                'nin' => $nin,
                'user_id' => $user->code_user,
                'code_wilaya' => $codeWilaya,
            ]);
            return response()->json(['success' => false, 'message' => 'No communes in your wilaya'], 404);
        }
        $request->validate([
            'motif' => 'required|string|min:10',
        ], [
            'motif.required' => 'يرجى إدخال سبب الرفض.',
            'motif.min' => 'سبب الرفض يجب أن يكون 10 أحرف على الأقل.',
        ]);
        $motif = $request->input('motif', '');
        $cnasRefuse = (int) $request->input('cnas_refuse', 0);
        $casnosRefuse = (int) $request->input('casnos_refuse', 0);
        Log::info('comiteDeclineTuteur bulk decline started', [
            'nin' => $nin,
            'user_id' => $user->code_user,
            'code_wilaya' => $codeWilaya,
            'communes_count' => count($communeCodes),
            'motif' => $motif,
            'cnas_refuse' => $cnasRefuse,
            'casnos_refuse' => $casnosRefuse,
        ]);
        $count = Eleve::where('code_tuteur', $nin)
            ->whereIn('code_commune', $communeCodes)
            ->whereIn('etat_das', ['accepte', 'refuse'])
            ->update([
                'etat_comite_wilaya' => 'refuse',
                'motif' => $motif,
                'cnas_refuse' => $cnasRefuse ? 1 : 0,
                'casnos_refuse' => $casnosRefuse ? 1 : 0
            ]);
        Log::info('comiteDeclineTuteur bulk decline finished', [
            'nin' => $nin,
            'user_id' => $user->code_user,
            'declined_count' => $count,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Tuteur eleves declined successfully',
            'count' => $count
        ]);
    }

    public function comiteUpdateEleveRefuseDetails(Request $request, $num_scolaire)
    {
        $user = $request->user();
        if (!$user || $user->role !== 'comite_wilaya') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $codeWilaya = $user->code_wilaya;
        if (empty($codeWilaya)) {
            return response()->json(['success' => false, 'message' => 'No wilaya bound to user'], 403);
        }
        $communeCodes = \App\Models\Commune::where('code_wilaya', $codeWilaya)->pluck('code_comm')->toArray();
        $eleve = Eleve::where('num_scolaire', $num_scolaire)
            ->whereIn('code_commune', $communeCodes)
            ->whereIn('etat_das', ['accepte', 'refuse'])
            ->first();
        if (!$eleve) {
            return response()->json(['success' => false, 'message' => 'Eleve not found'], 404);
        }
        $request->validate([
            'motif' => 'required|string|min:10',
        ], [
            'motif.required' => 'يرجى إدخال سبب الرفض.',
            'motif.min' => 'سبب الرفض يجب أن يكون 10 أحرف على الأقل.',
        ]);
        $eleve->motif = $request->input('motif', $eleve->motif);
        $eleve->cnas_refuse = (int) $request->input('cnas_refuse', $eleve->cnas_refuse) ? 1 : 0;
        $eleve->casnos_refuse = (int) $request->input('casnos_refuse', $eleve->casnos_refuse) ? 1 : 0;
        $eleve->save();
        return response()->json(['success' => true, 'message' => 'Refuse details updated']);
    }

    public function comiteUpdateTuteurRefuseDetails(Request $request, $nin)
    {
        $user = $request->user();
        if (!$user || $user->role !== 'comite_wilaya') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $codeWilaya = $user->code_wilaya;
        if (empty($codeWilaya)) {
            return response()->json(['success' => false, 'message' => 'No wilaya bound to user'], 403);
        }
        $communeCodes = \App\Models\Commune::where('code_wilaya', $codeWilaya)->pluck('code_comm')->toArray();
        $request->validate([
            'motif' => 'required|string|min:10',
        ], [
            'motif.required' => 'يرجى إدخال سبب الرفض.',
            'motif.min' => 'سبب الرفض يجب أن يكون 10 أحرف على الأقل.',
        ]);
        $motif = $request->input('motif', '');
        $cnasRefuse = (int) $request->input('cnas_refuse', 0) ? 1 : 0;
        $casnosRefuse = (int) $request->input('casnos_refuse', 0) ? 1 : 0;
        $count = Eleve::where('code_tuteur', $nin)
            ->whereIn('code_commune', $communeCodes)
            ->where('etat_comite_wilaya', 'refuse')
            ->update([
                'motif' => $motif,
                'cnas_refuse' => $cnasRefuse,
                'casnos_refuse' => $casnosRefuse
            ]);
        return response()->json(['success' => true, 'message' => 'Refuse details updated', 'count' => $count]);
    }

    // ========================= ATR (Antenne Régionale) Accept/Refuse =========================

    public function antrAcceptEleve(Request $request, $num_scolaire)
    {
        $user = $request->user();
        if (!$user || $user->role !== 'antr') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $communeCodes = $this->getAntrCommuneCodes($user->code_wilaya);
        if (empty($communeCodes)) {
            return response()->json(['success' => false, 'message' => 'No communes in your region'], 404);
        }
        $eleve = Eleve::where('num_scolaire', $num_scolaire)
            ->whereIn('code_commune', $communeCodes)
            ->where('etat_das', 'accepte')
            ->where('etat_comite_wilaya', 'accepte')
            ->first();
        if (!$eleve) {
            return response()->json(['success' => false, 'message' => 'Eleve not found or not eligible'], 404);
        }
        $eleve->etat_final = 'accepte';
        $eleve->save();
        return response()->json(['success' => true, 'message' => 'Eleve accepted (final)']);
    }

    public function antrDeclineEleve(Request $request, $num_scolaire)
    {
        $user = $request->user();
        if (!$user || $user->role !== 'antr') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $communeCodes = $this->getAntrCommuneCodes($user->code_wilaya);
        if (empty($communeCodes)) {
            return response()->json(['success' => false, 'message' => 'No communes in your region'], 404);
        }
        $eleve = Eleve::where('num_scolaire', $num_scolaire)
            ->whereIn('code_commune', $communeCodes)
            ->where('etat_das', 'accepte')
            ->where('etat_comite_wilaya', 'accepte')
            ->first();
        if (!$eleve) {
            return response()->json(['success' => false, 'message' => 'Eleve not found or not eligible'], 404);
        }
        $motif = $request->input('motif', '');
        $eleve->etat_final = 'refuse';
        $eleve->motif = $motif;
        $eleve->save();
        return response()->json(['success' => true, 'message' => 'Eleve declined (final)']);
    }

    public function antrAcceptTuteur(Request $request, $nin)
    {
        $user = $request->user();
        if (!$user || $user->role !== 'antr') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $communeCodes = $this->getAntrCommuneCodes($user->code_wilaya);
        if (empty($communeCodes)) {
            return response()->json(['success' => false, 'message' => 'No communes in your region'], 404);
        }
        $count = Eleve::where('code_tuteur', $nin)
            ->whereIn('code_commune', $communeCodes)
            ->where('etat_das', 'accepte')
            ->where('etat_comite_wilaya', 'accepte')
            ->update(['etat_final' => 'accepte']);
        return response()->json(['success' => true, 'message' => 'Tuteur eleves accepted (final)', 'count' => $count]);
    }

    public function antrDeclineTuteur(Request $request, $nin)
    {
        $user = $request->user();
        if (!$user || $user->role !== 'antr') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $communeCodes = $this->getAntrCommuneCodes($user->code_wilaya);
        if (empty($communeCodes)) {
            return response()->json(['success' => false, 'message' => 'No communes in your region'], 404);
        }
        $motif = $request->input('motif', '');
        $count = Eleve::where('code_tuteur', $nin)
            ->whereIn('code_commune', $communeCodes)
            ->where('etat_das', 'accepte')
            ->where('etat_comite_wilaya', 'accepte')
            ->update([
                'etat_final' => 'refuse',
                'motif' => $motif,
            ]);
        return response()->json(['success' => true, 'message' => 'Tuteur eleves declined (final)', 'count' => $count]);
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

    // 🔹 Get appeal details for an eleve (admin)
    public function getAppealDetails(Request $request, $num_scolaire)
    {
        $ctx = $this->resolveAgentContext($request);
        $userRole = $ctx['role'] ?? null;
        if (!$ctx || !in_array($userRole, ['ts_commune', 'comune_ts', 'das', 'comite_wilaya', 'antr'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $eleve = Eleve::where('num_scolaire', $num_scolaire)->first();
        if (!$eleve) {
            return response()->json(['success' => false, 'message' => 'Eleve not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'num_scolaire' => $eleve->num_scolaire,
                'nom' => $eleve->nom,
                'prenom' => $eleve->prenom,
                'appeal_text' => $eleve->appeal_text,
                'appeal_document' => $eleve->appeal_document,
                'appeal_status' => $eleve->appeal_status,
                'appeal_accepted_by' => $eleve->appeal_accepted_by,
                'motif' => $eleve->motif,
                'cnas_refuse' => $eleve->cnas_refuse,
                'casnos_refuse' => $eleve->casnos_refuse,
                'etat_das' => $eleve->etat_das,
                'etat_comite_wilaya' => $eleve->etat_comite_wilaya,
            ]
        ]);
    }

    // 🔹 Accept appeal - resets etat_das and etat_comite_wilaya for ALL eleves of the tuteur
    public function acceptAppeal(Request $request, $num_scolaire)
    {
        $ctx = $this->resolveAgentContext($request);
        $userRole = $ctx['role'] ?? null;
        if (!$ctx || !in_array($userRole, ['ts_commune', 'comune_ts', 'das', 'comite_wilaya'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $eleve = Eleve::where('num_scolaire', $num_scolaire)->first();
        if (!$eleve) {
            return response()->json(['success' => false, 'message' => 'Eleve not found'], 404);
        }
        if ($eleve->appeal_status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'No pending appeal for this student'], 422);
        }

        $userCode = $ctx['code'] ?? session('user_code');
        $tuteurNin = $eleve->code_tuteur;
        if (empty($tuteurNin)) {
            return response()->json(['success' => false, 'message' => 'التلميذ غير مرتبط بولي أمر'], 422);
        }

        // Accept appeal for ALL students of this parent (same tuteur) — not just the one whose appeal was opened.
        // Also set dossier_depose = oui so "تم الاستلام" shows for all of them.
        $updated = Eleve::where('code_tuteur', $tuteurNin)->update([
            'etat_das' => 'accepte',
            'etat_comite_wilaya' => 'accepte',
            'motif' => null,
            'cnas_refuse' => 0,
            'casnos_refuse' => 0,
            'appeal_status' => 'accepte',
            'appeal_accepted_by' => $userCode,
            'dossier_depose' => 'oui',
        ]);

        return response()->json([
            'success' => true,
            'message' => $updated > 1
                ? "تم قبول الطعن وإعادة قبول جميع التلاميذ ({$updated}) لهذا ولي الأمر بنجاح"
                : "تم قبول الطعن وإعادة قبول التلميذ بنجاح",
            'count' => $updated,
        ]);
    }

    // 🔹 Refuse appeal
    public function refuseAppeal(Request $request, $num_scolaire)
    {
        $ctx = $this->resolveAgentContext($request);
        $userRole = $ctx['role'] ?? null;
        if (!$ctx || !in_array($userRole, ['ts_commune', 'comune_ts', 'das', 'comite_wilaya'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $eleve = Eleve::where('num_scolaire', $num_scolaire)->first();
        if (!$eleve) {
            return response()->json(['success' => false, 'message' => 'Eleve not found'], 404);
        }
        if ($eleve->appeal_status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'No pending appeal for this student'], 422);
        }

        $userCode = $ctx['code'] ?? session('user_code');

        $eleve->appeal_status = 'refuse';
        $eleve->appeal_accepted_by = $userCode;
        $eleve->save();

        return response()->json([
            'success' => true,
            'message' => 'تم رفض الطعن',
        ]);
    }

    // 🔹 View tuteur details (return JSON for modal) - ts_commune: by commune; das: by wilaya + dossier_depose=oui
    public function viewTuteur(Request $request, $nin)
    {
        $ctx = $this->resolveAgentContext($request);
        $userRole = $ctx['role'] ?? null;
        if (!$ctx || !in_array($userRole, ['ts_commune', 'comune_ts', 'das', 'comite_wilaya', 'antr'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $userCommune = $ctx['commune'] ?? null;
        $userWilaya = $ctx['wilaya'] ?? null;

        if ($userRole === 'das') {
            if (empty($userWilaya)) {
                return response()->json(['success' => false, 'message' => 'No wilaya bound to user'], 403);
            }
            $communeCodes = \App\Models\Commune::where('code_wilaya', $userWilaya)->pluck('code_comm')->toArray();
            if (empty($communeCodes)) {
                return response()->json(['success' => false, 'message' => 'No communes in your wilaya'], 404);
            }
            $tuteur = Tuteur::with([
                'eleves' => function ($query) use ($communeCodes) {
                    $query->whereIn('code_commune', $communeCodes)->where('dossier_depose', 'oui')
                        ->with(['etablissement', 'mother']);
                },
                'eleves.etablissement.commune',
                'communeResidence',
                'communeNaissance',
                'communeCni'
            ])->where('nin', $nin)->first();

            if (!$tuteur || !$tuteur->eleves()->whereIn('code_commune', $communeCodes)->where('dossier_depose', 'oui')->exists()) {
                return response()->json(['success' => false, 'message' => 'Tuteur not found or no approved students in your wilaya'], 404);
            }
        } elseif ($userRole === 'comite_wilaya') {
            if (empty($userWilaya)) {
                return response()->json(['success' => false, 'message' => 'No wilaya bound to user'], 403);
            }
            $communeCodes = \App\Models\Commune::where('code_wilaya', $userWilaya)->pluck('code_comm')->toArray();
            if (empty($communeCodes)) {
                return response()->json(['success' => false, 'message' => 'No communes in your wilaya'], 404);
            }
            $tuteur = Tuteur::with([
                'eleves' => function ($query) use ($communeCodes) {
                    $query->whereIn('code_commune', $communeCodes)->whereIn('etat_das', ['accepte', 'refuse'])
                        ->with(['etablissement', 'mother']);
                },
                'eleves.etablissement.commune',
                'communeResidence',
                'communeNaissance',
                'communeCni'
            ])->where('nin', $nin)->first();

            if (!$tuteur || !$tuteur->eleves()->whereIn('code_commune', $communeCodes)->whereIn('etat_das', ['accepte', 'refuse'])->exists()) {
                return response()->json(['success' => false, 'message' => 'Tuteur not found or no students in your wilaya'], 404);
            }
        } elseif ($userRole === 'antr') {
            if (empty($userWilaya)) {
                return response()->json(['success' => false, 'message' => 'No wilaya bound to user'], 403);
            }
            $communeCodes = $this->getAntrCommuneCodes($userWilaya);
            if (empty($communeCodes)) {
                return response()->json(['success' => false, 'message' => 'No communes in your region'], 404);
            }
            $tuteur = Tuteur::with([
                'eleves' => function ($query) use ($communeCodes) {
                    $query->whereIn('code_commune', $communeCodes)
                        ->where('etat_das', 'accepte')
                        ->where('etat_comite_wilaya', 'accepte')
                        ->with(['etablissement', 'mother']);
                },
                'eleves.etablissement.commune',
                'communeResidence',
                'communeNaissance',
                'communeCni'
            ])->where('nin', $nin)->first();

            if (!$tuteur || !$tuteur->eleves()->whereIn('code_commune', $communeCodes)->where('etat_das', 'accepte')->where('etat_comite_wilaya', 'accepte')->exists()) {
                return response()->json(['success' => false, 'message' => 'Tuteur not found or no eligible students in your region'], 404);
            }
        } else {
            $tuteur = Tuteur::with([
                'eleves' => function ($query) use ($userCommune) {
                    $query->where('code_commune', $userCommune)->with(['etablissement', 'mother']);
                },
                'eleves.etablissement.commune',
                'communeResidence',
                'communeNaissance',
                'communeCni'
            ])->where('nin', $nin)->first();

            if (!$tuteur || !$tuteur->eleves()->where('code_commune', $userCommune)->exists()) {
                return response()->json(['success' => false, 'message' => 'Tuteur not found or no students in your commune'], 404);
            }
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
        
        // Explicitly ensure document fields are included (Laravel might not include null fields)
        $documentFields = [
            'biometric_id',
            'biometric_id_back',
            'Certificate_of_none_income',
            'salary_certificate',
            'Certificate_of_non_affiliation_to_social_security',
            'crossed_ccp'
        ];
        
        foreach ($documentFields as $field) {
            if (!isset($tuteurArray[$field])) {
                $tuteurArray[$field] = $tuteur->$field ?? null;
            }
        }
        
        // Add total eleves count to the array
        $tuteurArray['total_eleves_count'] = $totalElevesCount;

        return response()->json([
            'success' => true,
            'tuteur' => $tuteurArray
        ]);
    }

    /**
     * Export tuteurs to Excel - same structure as students export
     * Supports ts_commune, comune_ts (by commune), das (wilaya + dossier_depose=oui), comite_wilaya (wilaya + etat_das in accepte/refuse).
     * Falls back to CSV if PhpSpreadsheet is not installed on the server.
     */
    public function exportTuteursToExcel(Request $request)
    {
        $userRole = session('user_role');
        if (!session('user_logged') || !in_array($userRole, ['ts_commune', 'comune_ts', 'das', 'comite_wilaya', 'antr'])) {
            return redirect()->route('user.login');
        }

        $userCommune = session('user_commune_code');
        $userWilaya = session('user_wilaya');
        if ($userRole === 'antr') {
            if (empty($userWilaya)) {
                return back()->with('error', 'لا توجد ولاية مرتبطة بالمستخدم.');
            }
            $communeCodes = $this->getAntrCommuneCodes($userWilaya);
            if (empty($communeCodes)) {
                return back()->with('error', 'لا توجد بلديات في المنطقة الجهوية.');
            }
        } elseif (in_array($userRole, ['das', 'comite_wilaya'])) {
            if (empty($userWilaya)) {
                return back()->with('error', 'لا توجد ولاية مرتبطة بالمستخدم.');
            }
            $communeCodes = \App\Models\Commune::where('code_wilaya', $userWilaya)->pluck('code_comm')->toArray();
            if (empty($communeCodes)) {
                return back()->with('error', 'لا توجد بلديات في الولاية.');
            }
        } else {
            if (!$userCommune) {
                return back()->with('error', 'لا توجد بلدية مرتبطة بالمستخدم.');
            }
        }

        if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet', false)) {
            // Try autoloading once safely
            try {
                if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
                    return $this->exportTuteursToCsv($request);
                }
            } catch (\Throwable $e) {
                \Log::warning('PhpSpreadsheet not available, falling back to CSV: ' . $e->getMessage());
                return $this->exportTuteursToCsv($request);
            }
        }

        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set LTR direction (left to right)
            $sheet->setRightToLeft(false);

            $query = Eleve::with([
                'etablissement',
                'tuteur.communeResidence',
                'tuteur.communeCni',
                'mother',
                'father',
                'communeNaissance',
                'comments' => function($query) {
                    $query->orderBy('created_at', 'desc')->limit(1);
                }
            ])->orderBy('date_insertion', 'desc');

            if ($userRole === 'antr') {
                $query->whereIn('code_commune', $communeCodes)->where('etat_das', 'accepte')->where('etat_comite_wilaya', 'accepte');
            } elseif ($userRole === 'das') {
                $query->whereIn('code_commune', $communeCodes)->where('dossier_depose', 'oui');
            } elseif ($userRole === 'comite_wilaya') {
                $query->whereIn('code_commune', $communeCodes)->whereIn('etat_das', ['accepte', 'refuse']);
            } else {
                $query->where('code_commune', $userCommune);
            }

            $eleves = $query->get();

            // Headers - matching the exact structure provided (same as students export)
            $headers = [
                'NUM_SCOLAIRE_ELEVE',
                'NOM_ELEVE',
                'PRENOM_ELEVE',
                'DATE_NAISS_ELEVE',
                'PRESUME_ELEVE',
                'COMMUNE_NAISS_ELEVE',
                'SEXE_ELEVE',
                'ETABLISSEMENT_NAME',
                'ETABLISSEMENT_ADRESSE',
                'NIV_SCOL_ELEVE',
                'NOM_PERE_ELEVE',
                'PRENOM_PERE_ELEVE',
                'NIN_PERE_ELEVE',
                'NSS_PERE_ELEVE',
                'SAL_PERE_ELEVE',
                'NOM_MERE_ELEVE',
                'PRENOM_MERE_ELEVE',
                'NIN_MERE_ELEVE',
                'NSS_MERE_ELEVE',
                'SAL_MERE_ELEVE',
                'NOM_TUTEUR',
                'PRENOM_TUTEUR',
                'NIN_TUTEUR',
                'NSS_TUTEUR',
                'SAL_TUTEUR',
                'ADRESSE_TUTEUR',
                'TEL_TUTEUR',
                'SITU_FAM_TUTEUR',
                'PROF_TUTEUR',
                'N_ENF_TUTEUR',
                'N_ENF_SCOL_TUTEUR',
                'N_ENF_HAND_TUTEUR',
                'NUM_CPT_TUTEUR',
                'CLE_CPT_TUTEUR',
                'CATS_TUTEUR',
                'AUTR_INFO_TUTEUR',
                'NUM_CNI_TUTEUR',
                'DATE_CNI_TUTEUR',
                'LIEU_CNI_TUTEUR',
                'CODE_WIL_TUTEUR',
                'CODE_AR_TUTEUR',
                'CODE_COMMUNE_TUTEUR',
                'NATURE_DOC_ELEVE',
                'TOTAL_SAL',
                'ETAT_ELEVE',
                'MOTIF_RAD_ELEVE'
            ];

            // Set headers with styling
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $style = $sheet->getStyle($col . '1');
                $style->getFont()->setBold(true);
                $style->getFont()->setSize(11);
                $style->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('D9E1F2');
                $style->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getRowDimension(1)->setRowHeight(20);
                $col++;
            }

            // Fill data (same logic as students export)
            $row = 2;
            foreach ($eleves as $eleve) {
                $tuteur = $eleve->tuteur;
                $father = $eleve->father;
                $mother = $eleve->mother;
                
                // Get tuteur's total enrolled children count
                $nEnfScolTuteur = 0;
                $nEnfHandTuteur = 0;
                if ($tuteur) {
                    $nEnfScolTuteur = \App\Models\Eleve::where('code_tuteur', $tuteur->nin)->count();
                    $nEnfHandTuteur = \App\Models\Eleve::where('code_tuteur', $tuteur->nin)
                        ->where('handicap', '1')->count();
                }
                
                // Calculate total salary
                $totalSal = 0;
                if ($father && $father->montant_s) {
                    $totalSal += floatval($father->montant_s);
                }
                if ($mother && $mother->montant_s) {
                    $totalSal += floatval($mother->montant_s);
                }
                if ($tuteur && $tuteur->montant_s) {
                    $totalSal += floatval($tuteur->montant_s);
                }
                
                // Get commune names
                $communeNaissName = '-';
                if ($eleve->communeNaissance) {
                    $communeNaissName = $eleve->communeNaissance->lib_comm_ar;
                }
                
                $lieuCniName = '-';
                if ($tuteur && $tuteur->communeCni) {
                    $lieuCniName = $tuteur->communeCni->lib_comm_ar;
                } elseif ($tuteur && $tuteur->lieu_cni) {
                    $lieuCniName = $tuteur->lieu_cni;
                }
                
                // Get wilaya code from commune
                $codeWilTuteur = '-';
                if ($tuteur && $tuteur->communeResidence) {
                    $codeWilTuteur = $tuteur->communeResidence->code_wilaya ?? '-';
                }
                
                // Presume text
                $presumeText = '-';
                if ($eleve->presume == '1' || $eleve->presume == 1) {
                    $presumeText = 'Oui';
                } elseif ($eleve->presume == '0' || $eleve->presume == 0) {
                    $presumeText = 'Non';
                }
                
                // Nature doc (handicap nature or guardian doc indicator)
                $natureDoc = '-';
                if ($eleve->handicap == '1' || $eleve->handicap == 1) {
                    $natureDoc = $eleve->handicap_nature ?? '-';
                } elseif ($eleve->relation_tuteur == 3 && $eleve->guardian_doc) {
                    $natureDoc = 'وثيقة إسناد الوصاية';
                }
                
                // Etat eleve
                $etatEleve = $eleve->dossier_depose == 'oui' ? 'موافق عليه' : 'قيد المراجعة';
                
                // Get rejection reason (latest comment if not approved)
                $motifRad = '-';
                if ($eleve->dossier_depose != 'oui' && $eleve->comments && $eleve->comments->count() > 0) {
                    $motifRad = $eleve->comments->first()->text ?? '-';
                }
                
                // Fill row data (null-safe: tuteur, father, mother, etablissement can be null)
                $sheet->setCellValue('A' . $row, $eleve->num_scolaire ?? '-');
                $sheet->setCellValue('B' . $row, $eleve->nom ?? '-');
                $sheet->setCellValue('C' . $row, $eleve->prenom ?? '-');
                $sheet->setCellValue('D' . $row, $eleve->date_naiss ?? '-');
                $sheet->setCellValue('E' . $row, $presumeText);
                $sheet->setCellValue('F' . $row, $communeNaissName);
                $sheet->setCellValue('G' . $row, $eleve->sexe ?? '-');
                $sheet->setCellValue('H' . $row, $eleve->etablissement?->nom_etabliss ?? '-');
                $sheet->setCellValue('I' . $row, $eleve->etablissement?->adresse ?? '-');
                $sheet->setCellValue('J' . $row, $eleve->niv_scol ?? '-');
                $sheet->setCellValue('K' . $row, $father?->nom_ar ?? '-');
                $sheet->setCellValue('L' . $row, $father?->prenom_ar ?? '-');
                $sheet->setCellValue('M' . $row, $father?->nin ?? '-');
                $sheet->setCellValue('N' . $row, $father?->nss ?? '-');
                $sheet->setCellValue('O' . $row, $father?->montant_s ?? '-');
                $sheet->setCellValue('P' . $row, $mother?->nom_ar ?? '-');
                $sheet->setCellValue('Q' . $row, $mother?->prenom_ar ?? '-');
                $sheet->setCellValue('R' . $row, $mother?->nin ?? '-');
                $sheet->setCellValue('S' . $row, $mother?->nss ?? '-');
                $sheet->setCellValue('T' . $row, $mother?->montant_s ?? '-');
                $sheet->setCellValue('U' . $row, $tuteur?->nom_ar ?? $tuteur?->nom_fr ?? '-');
                $sheet->setCellValue('V' . $row, $tuteur?->prenom_ar ?? $tuteur?->prenom_fr ?? '-');
                $sheet->setCellValue('W' . $row, $tuteur?->nin ?? '-');
                $sheet->setCellValue('X' . $row, $tuteur?->nss ?? '-');
                $sheet->setCellValue('Y' . $row, $tuteur?->montant_s ?? '-');
                $sheet->setCellValue('Z' . $row, $tuteur?->adresse ?? '-');
                $sheet->setCellValue('AA' . $row, $tuteur?->tel ?? '-');
                $sheet->setCellValue('AB' . $row, '-'); // SITU_FAM_TUTEUR - not in database
                $sheet->setCellValue('AC' . $row, '-'); // PROF_TUTEUR - not in database
                $sheet->setCellValue('AD' . $row, $tuteur?->nbr_enfants_scolarise ?? '0');
                $sheet->setCellValue('AE' . $row, $nEnfScolTuteur);
                $sheet->setCellValue('AF' . $row, $nEnfHandTuteur);
                $sheet->setCellValue('AG' . $row, $tuteur?->num_cpt ?? '-');
                $sheet->setCellValue('AH' . $row, $tuteur?->cle_cpt ?? '-');
                $sheet->setCellValue('AI' . $row, $tuteur?->cats ?? '-');
                $sheet->setCellValue('AJ' . $row, $tuteur?->autr_info ?? '-');
                $sheet->setCellValue('AK' . $row, $tuteur?->num_cni ?? '-');
                $sheet->setCellValue('AL' . $row, $tuteur?->date_cni ?? '-');
                $sheet->setCellValue('AM' . $row, $lieuCniName);
                $sheet->setCellValue('AN' . $row, $codeWilTuteur);
                $sheet->setCellValue('AO' . $row, '-'); // CODE_AR_TUTEUR - not in database
                $sheet->setCellValue('AP' . $row, $tuteur?->code_commune ?? '-');
                $sheet->setCellValue('AQ' . $row, $natureDoc);
                $sheet->setCellValue('AR' . $row, $totalSal > 0 ? $totalSal : '-');
                $sheet->setCellValue('AS' . $row, $etatEleve);
                $sheet->setCellValue('AT' . $row, $motifRad);

                // Apply borders to data rows
                $cols = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT'];
                foreach ($cols as $col) {
                    $sheet->getStyle($col . $row)->getBorders()->getAllBorders()
                        ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                }

                $row++;
            }

            // Auto-size columns
            $cols = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT'];
            foreach ($cols as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Apply borders to header row
            foreach ($cols as $col) {
                $sheet->getStyle($col . '1')->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            }

            // Create writer and stream download (avoids "headers already sent" and works with Laravel response)
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $filePrefix = in_array($userRole, ['das', 'comite_wilaya']) ? ('tuteurs_wilaya' . $userWilaya) : ('tuteurs_' . $userCommune);
            $filename = $filePrefix . '_' . now()->format('Ymd_His') . '.xlsx';

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'max-age=0',
            ]);
        } catch (\Throwable $e) {
            \Log::error('Export tuteurs to Excel error: ' . $e->getMessage(), ['exception' => $e]);
            // If PhpSpreadsheet fails at runtime, fall back to CSV
            if (str_contains($e->getMessage(), 'PhpSpreadsheet') || str_contains($e->getMessage(), 'phpspreadsheet') || str_contains($e->getMessage(), 'Failed to open stream')) {
                \Log::warning('Falling back to CSV export for tuteurs');
                return $this->exportTuteursToCsv($request);
            }
            return back()->with('error', 'حدث خطأ أثناء تصدير البيانات: ' . $e->getMessage());
        }
    }

    /**
     * Export students to Excel
     * Supports ts_commune, comune_ts (by commune), das (wilaya + dossier_depose=oui), comite_wilaya (wilaya + etat_das in accepte/refuse).
     * Falls back to CSV if PhpSpreadsheet is not installed on the server.
     */
    public function exportStudentsToExcel(Request $request)
    {
        $userRole = session('user_role');
        if (!session('user_logged') || !in_array($userRole, ['ts_commune', 'comune_ts', 'das', 'comite_wilaya', 'antr'])) {
            return redirect()->route('user.login');
        }

        $userCommune = session('user_commune_code');
        $userWilaya = session('user_wilaya');
        if ($userRole === 'antr') {
            if (empty($userWilaya)) {
                return back()->with('error', 'لا توجد ولاية مرتبطة بالمستخدم.');
            }
            $communeCodes = $this->getAntrCommuneCodes($userWilaya);
            if (empty($communeCodes)) {
                return back()->with('error', 'لا توجد بلديات في المنطقة الجهوية.');
            }
        } elseif (in_array($userRole, ['das', 'comite_wilaya'])) {
            if (empty($userWilaya)) {
                return back()->with('error', 'لا توجد ولاية مرتبطة بالمستخدم.');
            }
            $communeCodes = \App\Models\Commune::where('code_wilaya', $userWilaya)->pluck('code_comm')->toArray();
            if (empty($communeCodes)) {
                return back()->with('error', 'لا توجد بلديات في الولاية.');
            }
        } else {
            if (!$userCommune) {
                return back()->with('error', 'لا توجد بلدية مرتبطة بالمستخدم.');
            }
        }

        if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet', false)) {
            // Try autoloading once safely
            try {
                if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
                    return $this->exportStudentsToCsv($request);
                }
            } catch (\Throwable $e) {
                \Log::warning('PhpSpreadsheet not available, falling back to CSV: ' . $e->getMessage());
                return $this->exportStudentsToCsv($request);
            }
        }

        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set LTR direction (left to right)
            $sheet->setRightToLeft(false);

            $query = Eleve::with([
                'etablissement',
                'tuteur.communeResidence',
                'tuteur.communeCni',
                'mother',
                'father',
                'communeNaissance',
                'comments' => function($query) {
                    $query->orderBy('created_at', 'desc')->limit(1);
                }
            ])->orderBy('date_insertion', 'desc');

            if ($userRole === 'antr') {
                $query->whereIn('code_commune', $communeCodes)->where('etat_das', 'accepte')->where('etat_comite_wilaya', 'accepte');
            } elseif ($userRole === 'das') {
                $query->whereIn('code_commune', $communeCodes)->where('dossier_depose', 'oui');
            } elseif ($userRole === 'comite_wilaya') {
                $query->whereIn('code_commune', $communeCodes)->whereIn('etat_das', ['accepte', 'refuse']);
            } else {
                $query->where('code_commune', $userCommune);
            }

            $eleves = $query->get();

            // Headers - matching the exact structure provided
            $headers = [
                'NUM_SCOLAIRE_ELEVE',
                'NOM_ELEVE',
                'PRENOM_ELEVE',
                'DATE_NAISS_ELEVE',
                'PRESUME_ELEVE',
                'COMMUNE_NAISS_ELEVE',
                'SEXE_ELEVE',
                'ETABLISSEMENT_NAME',
                'ETABLISSEMENT_ADRESSE',
                'NIV_SCOL_ELEVE',
                'NOM_PERE_ELEVE',
                'PRENOM_PERE_ELEVE',
                'NIN_PERE_ELEVE',
                'NSS_PERE_ELEVE',
                'SAL_PERE_ELEVE',
                'NOM_MERE_ELEVE',
                'PRENOM_MERE_ELEVE',
                'NIN_MERE_ELEVE',
                'NSS_MERE_ELEVE',
                'SAL_MERE_ELEVE',
                'NOM_TUTEUR',
                'PRENOM_TUTEUR',
                'NIN_TUTEUR',
                'NSS_TUTEUR',
                'SAL_TUTEUR',
                'ADRESSE_TUTEUR',
                'TEL_TUTEUR',
                'SITU_FAM_TUTEUR',
                'PROF_TUTEUR',
                'N_ENF_TUTEUR',
                'N_ENF_SCOL_TUTEUR',
                'N_ENF_HAND_TUTEUR',
                'NUM_CPT_TUTEUR',
                'CLE_CPT_TUTEUR',
                'CATS_TUTEUR',
                'AUTR_INFO_TUTEUR',
                'NUM_CNI_TUTEUR',
                'DATE_CNI_TUTEUR',
                'LIEU_CNI_TUTEUR',
                'CODE_WIL_TUTEUR',
                'CODE_AR_TUTEUR',
                'CODE_COMMUNE_TUTEUR',
                'NATURE_DOC_ELEVE',
                'TOTAL_SAL',
                'ETAT_ELEVE',
                'MOTIF_RAD_ELEVE'
            ];

            // Set headers with styling
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $style = $sheet->getStyle($col . '1');
                $style->getFont()->setBold(true);
                $style->getFont()->setSize(11);
                $style->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('D9E1F2');
                $style->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getRowDimension(1)->setRowHeight(20);
                $col++;
            }

                // Fill data (null-safe: tuteur, father, mother, etablissement can be null)
            $row = 2;
            foreach ($eleves as $eleve) {
                $tuteur = $eleve->tuteur;
                $father = $eleve->father;
                $mother = $eleve->mother;
                
                // Get tuteur's total enrolled children count
                $nEnfScolTuteur = 0;
                $nEnfHandTuteur = 0;
                if ($tuteur) {
                    $nEnfScolTuteur = \App\Models\Eleve::where('code_tuteur', $tuteur->nin)->count();
                    $nEnfHandTuteur = \App\Models\Eleve::where('code_tuteur', $tuteur->nin)
                        ->where('handicap', '1')->count();
                }
                
                // Calculate total salary
                $totalSal = 0;
                if ($father && $father->montant_s) {
                    $totalSal += floatval($father->montant_s);
                }
                if ($mother && $mother->montant_s) {
                    $totalSal += floatval($mother->montant_s);
                }
                if ($tuteur && $tuteur->montant_s) {
                    $totalSal += floatval($tuteur->montant_s);
                }
                
                // Get commune names
                $communeNaissName = $eleve->communeNaissance?->lib_comm_ar ?? '-';
                
                $lieuCniName = '-';
                if ($tuteur && $tuteur->communeCni) {
                    $lieuCniName = $tuteur->communeCni->lib_comm_ar;
                } elseif ($tuteur && $tuteur->lieu_cni) {
                    $lieuCniName = $tuteur->lieu_cni;
                }
                
                // Get wilaya code from commune
                $codeWilTuteur = $tuteur?->communeResidence?->code_wilaya ?? '-';
                
                // Presume text
                $presumeText = '-';
                if ($eleve->presume == '1' || $eleve->presume == 1) {
                    $presumeText = 'Oui';
                } elseif ($eleve->presume == '0' || $eleve->presume == 0) {
                    $presumeText = 'Non';
                }
                
                // Nature doc (handicap nature or guardian doc indicator)
                $natureDoc = '-';
                if ($eleve->handicap == '1' || $eleve->handicap == 1) {
                    $natureDoc = $eleve->handicap_nature ?? '-';
                } elseif ($eleve->relation_tuteur == 3 && $eleve->guardian_doc) {
                    $natureDoc = 'وثيقة إسناد الوصاية';
                }
                
                // Etat eleve
                $etatEleve = $eleve->dossier_depose == 'oui' ? 'موافق عليه' : 'قيد المراجعة';
                
                // Get rejection reason (latest comment if not approved)
                $motifRad = '-';
                if ($eleve->dossier_depose != 'oui' && $eleve->comments && $eleve->comments->count() > 0) {
                    $motifRad = $eleve->comments->first()->text ?? '-';
                }
                
                // Fill row data
                $sheet->setCellValue('A' . $row, $eleve->num_scolaire ?? '-');
                $sheet->setCellValue('B' . $row, $eleve->nom ?? '-');
                $sheet->setCellValue('C' . $row, $eleve->prenom ?? '-');
                $sheet->setCellValue('D' . $row, $eleve->date_naiss ?? '-');
                $sheet->setCellValue('E' . $row, $presumeText);
                $sheet->setCellValue('F' . $row, $communeNaissName);
                $sheet->setCellValue('G' . $row, $eleve->sexe ?? '-');
                $sheet->setCellValue('H' . $row, $eleve->etablissement?->nom_etabliss ?? '-');
                $sheet->setCellValue('I' . $row, $eleve->etablissement?->adresse ?? '-');
                $sheet->setCellValue('J' . $row, $eleve->niv_scol ?? '-');
                $sheet->setCellValue('K' . $row, $father?->nom_ar ?? '-');
                $sheet->setCellValue('L' . $row, $father?->prenom_ar ?? '-');
                $sheet->setCellValue('M' . $row, $father?->nin ?? '-');
                $sheet->setCellValue('N' . $row, $father?->nss ?? '-');
                $sheet->setCellValue('O' . $row, $father?->montant_s ?? '-');
                $sheet->setCellValue('P' . $row, $mother?->nom_ar ?? '-');
                $sheet->setCellValue('Q' . $row, $mother?->prenom_ar ?? '-');
                $sheet->setCellValue('R' . $row, $mother?->nin ?? '-');
                $sheet->setCellValue('S' . $row, $mother?->nss ?? '-');
                $sheet->setCellValue('T' . $row, $mother?->montant_s ?? '-');
                $sheet->setCellValue('U' . $row, $tuteur?->nom_ar ?? $tuteur?->nom_fr ?? '-');
                $sheet->setCellValue('V' . $row, $tuteur?->prenom_ar ?? $tuteur?->prenom_fr ?? '-');
                $sheet->setCellValue('W' . $row, $tuteur?->nin ?? '-');
                $sheet->setCellValue('X' . $row, $tuteur?->nss ?? '-');
                $sheet->setCellValue('Y' . $row, $tuteur?->montant_s ?? '-');
                $sheet->setCellValue('Z' . $row, $tuteur?->adresse ?? '-');
                $sheet->setCellValue('AA' . $row, $tuteur?->tel ?? '-');
                $sheet->setCellValue('AB' . $row, '-'); // SITU_FAM_TUTEUR - not in database
                $sheet->setCellValue('AC' . $row, '-'); // PROF_TUTEUR - not in database
                $sheet->setCellValue('AD' . $row, $tuteur?->nbr_enfants_scolarise ?? '0');
                $sheet->setCellValue('AE' . $row, $nEnfScolTuteur);
                $sheet->setCellValue('AF' . $row, $nEnfHandTuteur);
                $sheet->setCellValue('AG' . $row, $tuteur?->num_cpt ?? '-');
                $sheet->setCellValue('AH' . $row, $tuteur?->cle_cpt ?? '-');
                $sheet->setCellValue('AI' . $row, $tuteur?->cats ?? '-');
                $sheet->setCellValue('AJ' . $row, $tuteur?->autr_info ?? '-');
                $sheet->setCellValue('AK' . $row, $tuteur?->num_cni ?? '-');
                $sheet->setCellValue('AL' . $row, $tuteur?->date_cni ?? '-');
                $sheet->setCellValue('AM' . $row, $lieuCniName);
                $sheet->setCellValue('AN' . $row, $codeWilTuteur);
                $sheet->setCellValue('AO' . $row, '-'); // CODE_AR_TUTEUR - not in database
                $sheet->setCellValue('AP' . $row, $tuteur?->code_commune ?? '-');
                $sheet->setCellValue('AQ' . $row, $natureDoc);
                $sheet->setCellValue('AR' . $row, $totalSal > 0 ? $totalSal : '-');
                $sheet->setCellValue('AS' . $row, $etatEleve);
                $sheet->setCellValue('AT' . $row, $motifRad);

                // Apply borders to data rows
                $cols = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT'];
                foreach ($cols as $col) {
                    $sheet->getStyle($col . $row)->getBorders()->getAllBorders()
                        ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                }

                $row++;
            }

            // Auto-size columns
            $cols = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT'];
            foreach ($cols as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Apply borders to header row
            foreach ($cols as $col) {
                $sheet->getStyle($col . '1')->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            }

            // Create writer and stream download (avoids "headers already sent" and works with Laravel response)
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $filePrefix = in_array($userRole, ['das', 'comite_wilaya']) ? ('eleves_wilaya' . $userWilaya) : ('eleves_' . $userCommune);
            $filename = $filePrefix . '_' . now()->format('Ymd_His') . '.xlsx';

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'max-age=0',
            ]);
        } catch (\Throwable $e) {
            \Log::error('Export students to Excel error: ' . $e->getMessage(), ['exception' => $e]);
            // If PhpSpreadsheet fails at runtime, fall back to CSV
            if (str_contains($e->getMessage(), 'PhpSpreadsheet') || str_contains($e->getMessage(), 'phpspreadsheet') || str_contains($e->getMessage(), 'Failed to open stream')) {
                \Log::warning('Falling back to CSV export for students');
                return $this->exportStudentsToCsv($request);
            }
            return back()->with('error', 'حدث خطأ أثناء تصدير البيانات: ' . $e->getMessage());
        }
    }

    /**
     * CSV fallback when PhpSpreadsheet is not installed (same data as Excel tuteurs export).
     */
    private function exportTuteursToCsv(Request $request)
    {
        $userRole = session('user_role');
        $userCommune = session('user_commune_code');
        $userWilaya = session('user_wilaya');
        $communeCodes = [];
        if ($userRole === 'antr') {
            $communeCodes = $this->getAntrCommuneCodes($userWilaya);
        } elseif (in_array($userRole, ['das', 'comite_wilaya'])) {
            $communeCodes = \App\Models\Commune::where('code_wilaya', $userWilaya)->pluck('code_comm')->toArray();
        }

        $query = Eleve::with(['etablissement', 'tuteur.communeResidence', 'tuteur.communeCni', 'mother', 'father', 'communeNaissance'])
            ->orderBy('date_insertion', 'desc');
        if ($userRole === 'antr') {
            $query->whereIn('code_commune', $communeCodes)->where('etat_das', 'accepte')->where('etat_comite_wilaya', 'accepte');
        } elseif ($userRole === 'das') {
            $query->whereIn('code_commune', $communeCodes)->where('dossier_depose', 'oui');
        } elseif ($userRole === 'comite_wilaya') {
            $query->whereIn('code_commune', $communeCodes)->whereIn('etat_das', ['accepte', 'refuse']);
        } else {
            $query->where('code_commune', $userCommune);
        }
        $eleves = $query->get();

        $headers = ['NUM_SCOLAIRE_ELEVE','NOM_ELEVE','PRENOM_ELEVE','DATE_NAISS_ELEVE','PRESUME_ELEVE','COMMUNE_NAISS_ELEVE','SEXE_ELEVE','ETABLISSEMENT_NAME','ETABLISSEMENT_ADRESSE','NIV_SCOL_ELEVE','NOM_PERE_ELEVE','PRENOM_PERE_ELEVE','NIN_PERE_ELEVE','NSS_PERE_ELEVE','SAL_PERE_ELEVE','NOM_MERE_ELEVE','PRENOM_MERE_ELEVE','NIN_MERE_ELEVE','NSS_MERE_ELEVE','SAL_MERE_ELEVE','NOM_TUTEUR','PRENOM_TUTEUR','NIN_TUTEUR','NSS_TUTEUR','SAL_TUTEUR','ADRESSE_TUTEUR','TEL_TUTEUR','SITU_FAM_TUTEUR','PROF_TUTEUR','N_ENF_TUTEUR','N_ENF_SCOL_TUTEUR','N_ENF_HAND_TUTEUR','NUM_CPT_TUTEUR','CLE_CPT_TUTEUR','CATS_TUTEUR','AUTR_INFO_TUTEUR','NUM_CNI_TUTEUR','DATE_CNI_TUTEUR','LIEU_CNI_TUTEUR','CODE_WIL_TUTEUR','CODE_AR_TUTEUR','CODE_COMMUNE_TUTEUR','NATURE_DOC_ELEVE','TOTAL_SAL','ETAT_ELEVE','MOTIF_RAD_ELEVE'];

        $filePrefix = in_array($userRole, ['das', 'comite_wilaya']) ? ('tuteurs_wilaya' . $userWilaya) : ('tuteurs_' . $userCommune);
        $filename = $filePrefix . '_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($eleves, $headers) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM for Excel
            fputcsv($out, $headers);
            foreach ($eleves as $eleve) {
                $tuteur = $eleve->tuteur;
                $father = $eleve->father;
                $mother = $eleve->mother;
                $communeNaissName = $eleve->communeNaissance?->lib_comm_ar ?? '-';
                $lieuCniName = $tuteur?->communeCni?->lib_comm_ar ?? $tuteur?->lieu_cni ?? '-';
                $codeWilTuteur = $tuteur?->communeResidence?->code_wilaya ?? '-';
                $presumeText = ($eleve->presume == '1' || $eleve->presume == 1) ? 'Oui' : (($eleve->presume == '0' || $eleve->presume == 0) ? 'Non' : '-');
                $natureDoc = ($eleve->handicap == '1' || $eleve->handicap == 1) ? ($eleve->handicap_nature ?? '-') : (($eleve->relation_tuteur == 3 && $eleve->guardian_doc) ? 'وثيقة إسناد الوصاية' : '-');
                $etatEleve = $eleve->dossier_depose == 'oui' ? 'موافق عليه' : 'قيد المراجعة';
                $nEnfScol = $tuteur ? Eleve::where('code_tuteur', $tuteur->nin)->count() : 0;
                $nEnfHand = $tuteur ? Eleve::where('code_tuteur', $tuteur->nin)->where('handicap', '1')->count() : 0;
                $totalSal = (float)($father?->montant_s ?? 0) + (float)($mother?->montant_s ?? 0) + (float)($tuteur?->montant_s ?? 0);
                $row = [
                    $eleve->num_scolaire ?? '-', $eleve->nom ?? '-', $eleve->prenom ?? '-', $eleve->date_naiss ?? '-', $presumeText, $communeNaissName, $eleve->sexe ?? '-',
                    $eleve->etablissement?->nom_etabliss ?? '-', $eleve->etablissement?->adresse ?? '-', $eleve->niv_scol ?? '-',
                    $father?->nom_ar ?? '-', $father?->prenom_ar ?? '-', $father?->nin ?? '-', $father?->nss ?? '-', $father?->montant_s ?? '-',
                    $mother?->nom_ar ?? '-', $mother?->prenom_ar ?? '-', $mother?->nin ?? '-', $mother?->nss ?? '-', $mother?->montant_s ?? '-',
                    $tuteur?->nom_ar ?? $tuteur?->nom_fr ?? '-', $tuteur?->prenom_ar ?? $tuteur?->prenom_fr ?? '-', $tuteur?->nin ?? '-', $tuteur?->nss ?? '-', $tuteur?->montant_s ?? '-',
                    $tuteur?->adresse ?? '-', $tuteur?->tel ?? '-', '-', '-', $tuteur?->nbr_enfants_scolarise ?? '0', $nEnfScol, $nEnfHand,
                    $tuteur?->num_cpt ?? '-', $tuteur?->cle_cpt ?? '-', $tuteur?->cats ?? '-', $tuteur?->autr_info ?? '-', $tuteur?->num_cni ?? '-', $tuteur?->date_cni ?? '-', $lieuCniName, $codeWilTuteur, '-', $tuteur?->code_commune ?? '-',
                    $natureDoc, $totalSal > 0 ? $totalSal : '-', $etatEleve, '-'
                ];
                fputcsv($out, $row);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8', 'Cache-Control' => 'max-age=0']);
    }

    /**
     * CSV fallback when PhpSpreadsheet is not installed (same data as Excel students export).
     */
    private function exportStudentsToCsv(Request $request)
    {
        $userRole = session('user_role');
        $userCommune = session('user_commune_code');
        $userWilaya = session('user_wilaya');
        $communeCodes = [];
        if ($userRole === 'antr') {
            $communeCodes = $this->getAntrCommuneCodes($userWilaya);
        } elseif (in_array($userRole, ['das', 'comite_wilaya'])) {
            $communeCodes = \App\Models\Commune::where('code_wilaya', $userWilaya)->pluck('code_comm')->toArray();
        }

        $query = Eleve::with(['etablissement', 'tuteur.communeResidence', 'tuteur.communeCni', 'mother', 'father', 'communeNaissance'])
            ->orderBy('date_insertion', 'desc');
        if ($userRole === 'antr') {
            $query->whereIn('code_commune', $communeCodes)->where('etat_das', 'accepte')->where('etat_comite_wilaya', 'accepte');
        } elseif ($userRole === 'das') {
            $query->whereIn('code_commune', $communeCodes)->where('dossier_depose', 'oui');
        } elseif ($userRole === 'comite_wilaya') {
            $query->whereIn('code_commune', $communeCodes)->whereIn('etat_das', ['accepte', 'refuse']);
        } else {
            $query->where('code_commune', $userCommune);
        }
        $eleves = $query->get();

        $headers = ['NUM_SCOLAIRE_ELEVE','NOM_ELEVE','PRENOM_ELEVE','DATE_NAISS_ELEVE','PRESUME_ELEVE','COMMUNE_NAISS_ELEVE','SEXE_ELEVE','ETABLISSEMENT_NAME','ETABLISSEMENT_ADRESSE','NIV_SCOL_ELEVE','NOM_PERE_ELEVE','PRENOM_PERE_ELEVE','NIN_PERE_ELEVE','NSS_PERE_ELEVE','SAL_PERE_ELEVE','NOM_MERE_ELEVE','PRENOM_MERE_ELEVE','NIN_MERE_ELEVE','NSS_MERE_ELEVE','SAL_MERE_ELEVE','NOM_TUTEUR','PRENOM_TUTEUR','NIN_TUTEUR','NSS_TUTEUR','SAL_TUTEUR','ADRESSE_TUTEUR','TEL_TUTEUR','SITU_FAM_TUTEUR','PROF_TUTEUR','N_ENF_TUTEUR','N_ENF_SCOL_TUTEUR','N_ENF_HAND_TUTEUR','NUM_CPT_TUTEUR','CLE_CPT_TUTEUR','CATS_TUTEUR','AUTR_INFO_TUTEUR','NUM_CNI_TUTEUR','DATE_CNI_TUTEUR','LIEU_CNI_TUTEUR','CODE_WIL_TUTEUR','CODE_AR_TUTEUR','CODE_COMMUNE_TUTEUR','NATURE_DOC_ELEVE','TOTAL_SAL','ETAT_ELEVE','MOTIF_RAD_ELEVE'];

        $filePrefix = in_array($userRole, ['das', 'comite_wilaya']) ? ('eleves_wilaya' . $userWilaya) : ('eleves_' . $userCommune);
        $filename = $filePrefix . '_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($eleves, $headers) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM for Excel
            fputcsv($out, $headers);
            foreach ($eleves as $eleve) {
                $tuteur = $eleve->tuteur;
                $father = $eleve->father;
                $mother = $eleve->mother;
                $communeNaissName = $eleve->communeNaissance?->lib_comm_ar ?? '-';
                $lieuCniName = $tuteur?->communeCni?->lib_comm_ar ?? $tuteur?->lieu_cni ?? '-';
                $codeWilTuteur = $tuteur?->communeResidence?->code_wilaya ?? '-';
                $presumeText = ($eleve->presume == '1' || $eleve->presume == 1) ? 'Oui' : (($eleve->presume == '0' || $eleve->presume == 0) ? 'Non' : '-');
                $natureDoc = ($eleve->handicap == '1' || $eleve->handicap == 1) ? ($eleve->handicap_nature ?? '-') : (($eleve->relation_tuteur == 3 && $eleve->guardian_doc) ? 'وثيقة إسناد الوصاية' : '-');
                $etatEleve = $eleve->dossier_depose == 'oui' ? 'موافق عليه' : 'قيد المراجعة';
                $nEnfScol = $tuteur ? Eleve::where('code_tuteur', $tuteur->nin)->count() : 0;
                $nEnfHand = $tuteur ? Eleve::where('code_tuteur', $tuteur->nin)->where('handicap', '1')->count() : 0;
                $totalSal = (float)($father?->montant_s ?? 0) + (float)($mother?->montant_s ?? 0) + (float)($tuteur?->montant_s ?? 0);
                $row = [
                    $eleve->num_scolaire ?? '-', $eleve->nom ?? '-', $eleve->prenom ?? '-', $eleve->date_naiss ?? '-', $presumeText, $communeNaissName, $eleve->sexe ?? '-',
                    $eleve->etablissement?->nom_etabliss ?? '-', $eleve->etablissement?->adresse ?? '-', $eleve->niv_scol ?? '-',
                    $father?->nom_ar ?? '-', $father?->prenom_ar ?? '-', $father?->nin ?? '-', $father?->nss ?? '-', $father?->montant_s ?? '-',
                    $mother?->nom_ar ?? '-', $mother?->prenom_ar ?? '-', $mother?->nin ?? '-', $mother?->nss ?? '-', $mother?->montant_s ?? '-',
                    $tuteur?->nom_ar ?? $tuteur?->nom_fr ?? '-', $tuteur?->prenom_ar ?? $tuteur?->prenom_fr ?? '-', $tuteur?->nin ?? '-', $tuteur?->nss ?? '-', $tuteur?->montant_s ?? '-',
                    $tuteur?->adresse ?? '-', $tuteur?->tel ?? '-', '-', '-', $tuteur?->nbr_enfants_scolarise ?? '0', $nEnfScol, $nEnfHand,
                    $tuteur?->num_cpt ?? '-', $tuteur?->cle_cpt ?? '-', $tuteur?->cats ?? '-', $tuteur?->autr_info ?? '-', $tuteur?->num_cni ?? '-', $tuteur?->date_cni ?? '-', $lieuCniName, $codeWilTuteur, '-', $tuteur?->code_commune ?? '-',
                    $natureDoc, $totalSal > 0 ? $totalSal : '-', $etatEleve, '-'
                ];
                fputcsv($out, $row);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8', 'Cache-Control' => 'max-age=0']);
    }

    // 🔹 Delete tuteur
    public function deleteTuteur(Request $request, $nin)
    {
        $ctx = $this->resolveAgentContext($request);
        $userRole = $ctx['role'] ?? null;
        if (!$ctx || ($userRole !== 'ts_commune' && $userRole !== 'comune_ts')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $userCommune = $ctx['commune'] ?? null;
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

    // 🔹 View eleve details (return JSON for modal) - ts_commune: by commune; das: by wilaya + dossier_depose=oui
    public function viewEleve(Request $request, $num_scolaire)
    {
        $ctx = $this->resolveAgentContext($request);
        $userRole = $ctx['role'] ?? null;
        if (!$ctx || !in_array($userRole, ['ts_commune', 'comune_ts', 'das', 'comite_wilaya', 'antr'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $userCommune = $ctx['commune'] ?? null;
        $userWilaya = $ctx['wilaya'] ?? null;
        $eleve = Eleve::with([
            'tuteur.communeResidence',
            'tuteur.communeNaissance',
            'etablissement.commune',
            'communeResidence',
            'communeNaissance',
            'mother',
            'father'
        ])->where('num_scolaire', $num_scolaire)->first();

        if (!$eleve) {
            return response()->json(['success' => false, 'message' => 'Eleve not found'], 404);
        }

        if ($userRole === 'antr') {
            if (empty($userWilaya)) {
                return response()->json(['success' => false, 'message' => 'No wilaya bound to user'], 403);
            }
            $communeCodes = $this->getAntrCommuneCodes($userWilaya);
            if (!in_array($eleve->code_commune, $communeCodes)) {
                return response()->json(['success' => false, 'message' => 'Eleve not in your region'], 403);
            }
        } elseif ($userRole === 'das') {
            if (empty($userWilaya)) {
                return response()->json(['success' => false, 'message' => 'No wilaya bound to user'], 403);
            }
            $communeCodes = \App\Models\Commune::where('code_wilaya', $userWilaya)->pluck('code_comm')->toArray();
            if (!in_array($eleve->code_commune, $communeCodes) || $eleve->dossier_depose !== 'oui') {
                return response()->json(['success' => false, 'message' => 'Eleve not in your wilaya or not approved'], 403);
            }
        } elseif ($userRole === 'comite_wilaya') {
            if (empty($userWilaya)) {
                return response()->json(['success' => false, 'message' => 'No wilaya bound to user'], 403);
            }
            $communeCodes = \App\Models\Commune::where('code_wilaya', $userWilaya)->pluck('code_comm')->toArray();
            if (!in_array($eleve->code_commune, $communeCodes)) {
                return response()->json(['success' => false, 'message' => 'Eleve not in your wilaya'], 403);
            }
        } else {
            if ($eleve->code_commune !== $userCommune) {
                return response()->json(['success' => false, 'message' => 'Eleve not in your commune'], 403);
            }
        }

        return response()->json([
            'success' => true,
            'eleve' => $eleve->toArray()
        ]);
    }

    // 🔹 Approve eleve (set dossier_depose to 'oui')
    public function approveEleve(Request $request, $num_scolaire)
    {
        $ctx = $this->resolveAgentContext($request);
        $userRole = $ctx['role'] ?? null;
        if (!$ctx || ($userRole !== 'ts_commune' && $userRole !== 'comune_ts')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $userCommune = $ctx['commune'] ?? null;
        $eleve = Eleve::with('tuteur')->where('num_scolaire', $num_scolaire)->first();

        if (!$eleve || $eleve->code_commune !== $userCommune) {
            return response()->json(['success' => false, 'message' => 'Eleve not found or not in your commune'], 404);
        }

        // Set dossier_depose to 'oui' (approved) and store who approved it
        $eleve->dossier_depose = 'oui';
        $eleve->approved_by = $ctx['code'] ?? null;
        $eleve->save();

        return response()->json(['success' => true, 'message' => 'Eleve approved successfully']);
    }

    /**
     * Decline eleve (set dossier_depose to 'refuse') - ts_commune only.
     * Declined eleves do not show on DAS (DAS only sees dossier_depose = 'oui').
     */
    public function declineEleve(Request $request, $num_scolaire)
    {
        $ctx = $this->resolveAgentContext($request);
        $userRole = $ctx['role'] ?? null;
        if (!$ctx || ($userRole !== 'ts_commune' && $userRole !== 'comune_ts')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $userCommune = $ctx['commune'] ?? null;
        $eleve = Eleve::where('num_scolaire', $num_scolaire)->first();

        if (!$eleve || $eleve->code_commune !== $userCommune) {
            return response()->json(['success' => false, 'message' => 'Eleve not found or not in your commune'], 404);
        }

        $eleve->dossier_depose = 'refuse';
        $eleve->approved_by = $ctx['code'] ?? null; // user who refused (approved_by stores who did the last action)
        $eleve->save();

        return response()->json(['success' => true, 'message' => 'تم رفض الملف من البلدية']);
    }

    /**
     * Approve all eleves of a tuteur (set dossier_depose to 'oui' for all in the user's commune).
     * Only allowed for ts_commune / comune_ts.
     */
    public function approveAllElevesForTuteur(Request $request, $nin)
    {
        $ctx = $this->resolveAgentContext($request);
        $userRole = $ctx['role'] ?? null;
        if (!$ctx || ($userRole !== 'ts_commune' && $userRole !== 'comune_ts')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $userCommune = $ctx['commune'] ?? null;
        if (!$userCommune) {
            return response()->json(['success' => false, 'message' => 'No commune bound to user'], 403);
        }

        $count = Eleve::where('code_tuteur', $nin)
            ->where('code_commune', $userCommune)
            ->update([
                'dossier_depose' => 'oui',
                'approved_by' => $ctx['code'] ?? null,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'تمت الموافقة على جميع تلاميذ هذا الولي/الوصي بنجاح',
            'count' => $count,
        ]);
    }

    /**
     * Decline all eleves of a tuteur (set dossier_depose to 'refuse') - ts_commune only.
     */
    public function declineAllElevesForTuteur(Request $request, $nin)
    {
        $ctx = $this->resolveAgentContext($request);
        $userRole = $ctx['role'] ?? null;
        if (!$ctx || ($userRole !== 'ts_commune' && $userRole !== 'comune_ts')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $userCommune = $ctx['commune'] ?? null;
        if (!$userCommune) {
            return response()->json(['success' => false, 'message' => 'No commune bound to user'], 403);
        }

        $count = Eleve::where('code_tuteur', $nin)
            ->where('code_commune', $userCommune)
            ->update([
                'dossier_depose' => 'refuse',
                'approved_by' => $ctx['code'] ?? null, // user who refused
            ]);

        return response()->json([
            'success' => true,
            'message' => 'تم رفض ملفات جميع تلاميذ هذا الولي/الوصي من البلدية',
            'count' => $count,
        ]);
    }

    /**
     * Update dossier_depose (oui/non) for an eleve. Only allowed for ts_commune/comune_ts when etat_das is en_cours.
     */
    public function updateDossierDepose(Request $request, $num_scolaire)
    {
        $ctx = $this->resolveAgentContext($request);
        $userRole = $ctx['role'] ?? null;
        if (!$ctx || ($userRole !== 'ts_commune' && $userRole !== 'comune_ts')) {
            return response()->json(['success' => false, 'message' => 'غير مصرح'], 403);
        }

        $request->validate([
            'dossier_depose' => 'required|string|in:oui,non,refuse',
        ], [
            'dossier_depose.in' => 'حالة الإيداع يجب أن تكون: نعم، لا، أو مرفوض',
        ]);

        $userCommune = $ctx['commune'] ?? null;
        $eleve = Eleve::where('num_scolaire', $num_scolaire)->first();

        if (!$eleve || $eleve->code_commune !== $userCommune) {
            return response()->json(['success' => false, 'message' => 'التلميذ غير موجود أو لا يتبع بلديتكم'], 404);
        }

        $etatDas = strtolower(trim($eleve->etat_das ?? ''));
        if ($etatDas !== 'en_cours') {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن تعديل حالة إيداع الملف إلا عندما يكون وضع الملف عند الدائرة قيد المعالجة (قيد المراجعة).',
            ], 422);
        }

        $eleve->dossier_depose = $request->input('dossier_depose');
        // approved_by = user who did the action (approve or refuse)
        $eleve->approved_by = $ctx['code'] ?? null;
        $eleve->save();

        $label = $eleve->dossier_depose === 'oui' ? 'موافق عليه (تم الإيداع)' : ($eleve->dossier_depose === 'refuse' ? 'مرفوض من البلدية' : 'قيد المراجعة (لم يُودع بعد)');
        return response()->json(['success' => true, 'message' => 'تم تحديث حالة إيداع الملف بنجاح.', 'dossier_depose' => $eleve->dossier_depose, 'label' => $label]);
    }

    /**
     * Update dossier_depose for all en_cours eleves of a tuteur at once (ts_commune only).
     */
    public function updateTuteurDossierDeposeBulk(Request $request, $nin)
    {
        $ctx = $this->resolveAgentContext($request);
        $userRole = $ctx['role'] ?? null;
        if (!$ctx || ($userRole !== 'ts_commune' && $userRole !== 'comune_ts')) {
            return response()->json(['success' => false, 'message' => 'غير مصرح'], 403);
        }

        $request->validate([
            'dossier_depose' => 'required|string|in:oui,refuse',
        ], [
            'dossier_depose.in' => 'حالة الإيداع يجب أن تكون: نعم أو مرفوض',
        ]);

        $userCommune = $ctx['commune'] ?? null;
        if (!$userCommune) {
            return response()->json(['success' => false, 'message' => 'لا توجد بلدية مرتبطة بالمستخدم'], 403);
        }

        $updateData = [
            'dossier_depose' => $request->input('dossier_depose'),
            'approved_by' => $ctx['code'] ?? null, // user who set the status (approve or refuse)
        ];

        $count = Eleve::where('code_tuteur', $nin)
            ->where('code_commune', $userCommune)
            ->whereRaw('LOWER(TRIM(COALESCE(etat_das, ?))) = ?', ['', 'en_cours'])
            ->update($updateData);

        $label = $updateData['dossier_depose'] === 'oui' ? 'موافق عليه (تم الإيداع)' : 'مرفوض من البلدية';
        return response()->json([
            'success' => true,
            'message' => 'تم تطبيق الحالة على جميع التلاميذ المعنيين (قيد المعالجة).',
            'count' => $count,
            'label' => $label,
        ]);
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
        $ctx = $this->resolveAgentContext($request);
        $userRole = $ctx['role'] ?? null;
        if (!$ctx || ($userRole !== 'ts_commune' && $userRole !== 'comune_ts')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $userCommune = $ctx['commune'] ?? null;
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
    public function deleteEleve(Request $request, $num_scolaire)
    {
        $ctx = $this->resolveAgentContext($request);
        $userRole = $ctx['role'] ?? null;
        if (!$ctx || ($userRole !== 'ts_commune' && $userRole !== 'comune_ts')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $userCommune = $ctx['commune'] ?? null;
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
        $ctx = $this->resolveAgentContext($request);
        $userRole = $ctx['role'] ?? null;
        if (!$ctx || ($userRole !== 'ts_commune' && $userRole !== 'comune_ts')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $userCommune = $ctx['commune'] ?? null;
        $eleve = Eleve::with('tuteur')->where('num_scolaire', $num_scolaire)->first();

        if (!$eleve || $eleve->code_commune !== $userCommune) {
            return response()->json(['success' => false, 'message' => 'Eleve not found or not in your commune'], 404);
        }

        $validated = $request->validate([
            'text' => 'required|string|max:1000'
        ]);

        $comment = Comment::create([
            'user_id' => $ctx['code'] ?? null,
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
    public function getComments(Request $request, $num_scolaire)
    {
        $ctx = $this->resolveAgentContext($request);
        $userRole = $ctx['role'] ?? null;
        if (!$ctx || ($userRole !== 'ts_commune' && $userRole !== 'comune_ts')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $userCommune = $ctx['commune'] ?? null;
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
    
    // Serve private files securely via API
    public function serveFile(Request $request, $path)
    {
        // Check for API token authentication first (fastest path)
        $token = $request->bearerToken();
        
        if ($token) {
            // API token authentication
            $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
            
            if ($accessToken) {
                $user = $accessToken->tokenable;
                
                if ($user && ($user instanceof \App\Models\User)) {
                    $allowedRoles = ['ts_commune', 'comune_ts', 'das', 'comite_wilaya', 'antr'];
                    if (in_array($user->role, $allowedRoles)) {
                        $decodedPath = urldecode($path);
                        
                        if (!Storage::disk('local')->exists($decodedPath)) {
                            return response()->json([
                                'success' => false,
                                'message' => 'File not found.'
                            ], 404);
                        }
                        
                        return Storage::disk('local')->response($decodedPath);
                    }
                }
            }
        }
        
        // Fallback to session authentication (web middleware ensures session is available)
        $userRole = session('user_role', null);
        $allowedFileRoles = ['ts_commune', 'comune_ts', 'das', 'comite_wilaya', 'antr'];
        if (!session('user_logged', false) || !in_array($userRole, $allowedFileRoles)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Token or session required.',
                'error' => 'Authentication required'
            ], 401);
        }
        
        // Authenticated via session - serve file
        $decodedPath = urldecode($path);
        
        if (!Storage::disk('local')->exists($decodedPath)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found.'
            ], 404);
        }
        
        return Storage::disk('local')->response($decodedPath);
    }

    /**
     * API: Dashboard statistics for DAS (and comite_wilaya).
     * Returns counts, breakdowns by status, gender, level, commune, and recent activity.
     */
    public function apiDashboardStats(Request $request)
    {
        $ctx = $this->resolveAgentContext($request);
        $userRole = $ctx['role'] ?? null;
        if (!$ctx || !in_array($userRole, ['das', 'comite_wilaya', 'ts_commune', 'comune_ts', 'antr'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $userCommune = $ctx['commune'] ?? null;
        $userWilaya = $ctx['wilaya'] ?? null;

        // Build the base scope depending on role
        if ($userRole === 'antr') {
            if (empty($userWilaya)) {
                return response()->json(['success' => true, 'data' => $this->emptyStats()]);
            }
            $communeCodes = $this->getAntrCommuneCodes($userWilaya);
            if (empty($communeCodes)) {
                return response()->json(['success' => true, 'data' => $this->emptyStats()]);
            }
            // ATR sees only eleves accepted by both DAS and Comité Wilaya
            $eleveScope = fn($q) => $q->whereIn('code_commune', $communeCodes)
                ->where('etat_das', 'accepte')
                ->where('etat_comite_wilaya', 'accepte');
        } elseif ($userRole === 'das') {
            if (empty($userWilaya)) {
                return response()->json(['success' => true, 'data' => $this->emptyStats()]);
            }
            $communeCodes = \App\Models\Commune::where('code_wilaya', $userWilaya)->pluck('code_comm')->toArray();
            if (empty($communeCodes)) {
                return response()->json(['success' => true, 'data' => $this->emptyStats()]);
            }
            $eleveScope = fn($q) => $q->whereIn('code_commune', $communeCodes)->where('dossier_depose', 'oui');
        } elseif ($userRole === 'comite_wilaya') {
            if (empty($userWilaya)) {
                return response()->json(['success' => true, 'data' => $this->emptyStats()]);
            }
            $communeCodes = \App\Models\Commune::where('code_wilaya', $userWilaya)->pluck('code_comm')->toArray();
            if (empty($communeCodes)) {
                return response()->json(['success' => true, 'data' => $this->emptyStats()]);
            }
            $eleveScope = fn($q) => $q->whereIn('code_commune', $communeCodes)->whereIn('etat_das', ['accepte', 'refuse']);
        } else {
            if (empty($userCommune)) {
                return response()->json(['success' => true, 'data' => $this->emptyStats()]);
            }
            $communeCodes = [$userCommune];
            $eleveScope = fn($q) => $q->where('code_commune', $userCommune);
        }

        // -- 1. Overall counts --
        $totalEleves = Eleve::where(function($q) use ($eleveScope) { $eleveScope($q); })->count();
        $totalTuteurs = Tuteur::whereHas('eleves', function($q) use ($eleveScope) { $eleveScope($q); })->count();
        $totalSchools = \App\Models\Etablissement::whereIn('code_commune', $communeCodes)->count();
        $totalCommunes = count($communeCodes);

        // -- 2. DAS status breakdown --
        $dasAccepted = Eleve::where(function($q) use ($eleveScope) { $eleveScope($q); })->where('etat_das', 'accepte')->count();
        $dasRefused = Eleve::where(function($q) use ($eleveScope) { $eleveScope($q); })->where('etat_das', 'refuse')->count();
        $dasPending = $totalEleves - $dasAccepted - $dasRefused;

        // -- 3. Comité wilaya status breakdown --
        $comiteAccepted = Eleve::where(function($q) use ($eleveScope) { $eleveScope($q); })->where('etat_comite_wilaya', 'accepte')->count();
        $comiteRefused = Eleve::where(function($q) use ($eleveScope) { $eleveScope($q); })->where('etat_comite_wilaya', 'refuse')->count();
        $comitePending = $totalEleves - $comiteAccepted - $comiteRefused;

        // -- 3b. ATR (etat_final) status breakdown --
        $finalAccepted = Eleve::where(function($q) use ($eleveScope) { $eleveScope($q); })->where('etat_final', 'accepte')->count();
        $finalRefused = Eleve::where(function($q) use ($eleveScope) { $eleveScope($q); })->where('etat_final', 'refuse')->count();
        $finalPending = $totalEleves - $finalAccepted - $finalRefused;

        // -- 4. Gender breakdown --
        $genderMale = Eleve::where(function($q) use ($eleveScope) { $eleveScope($q); })->where('sexe', 'ذكر')->count();
        $genderFemale = Eleve::where(function($q) use ($eleveScope) { $eleveScope($q); })->where('sexe', 'أنثى')->count();

        // -- 5. Education level breakdown --
        $nivScol = Eleve::where(function($q) use ($eleveScope) { $eleveScope($q); })
            ->selectRaw("CASE
                WHEN niv_scol LIKE '%ابتدائي%' OR niv_scol IN ('1AP','2AP','3AP','4AP','5AP') THEN 'ابتدائي'
                WHEN niv_scol LIKE '%متوسط%' OR niv_scol IN ('1AM','2AM','3AM','4AM') THEN 'متوسط'
                WHEN niv_scol LIKE '%ثانوي%' OR niv_scol IN ('1AS','2AS','3AS') THEN 'ثانوي'
                ELSE 'أخرى' END as level_group, count(*) as cnt")
            ->groupBy('level_group')
            ->pluck('cnt', 'level_group')
            ->toArray();

        // -- 6. Top communes by student count --
        $communeStats = Eleve::where(function($q) use ($eleveScope) { $eleveScope($q); })
            ->join('commune', 'eleves.code_commune', '=', 'commune.code_comm')
            ->selectRaw('commune.lib_comm_ar as commune_name, commune.code_comm, count(*) as cnt')
            ->groupBy('commune.code_comm', 'commune.lib_comm_ar')
            ->orderByDesc('cnt')
            ->limit(15)
            ->get()
            ->toArray();

        // -- 6b. For ATR: students by wilaya (across the region) --
        $wilayaStats = [];
        if ($userRole === 'antr') {
            $wilayaStats = Eleve::where(function($q) use ($eleveScope) { $eleveScope($q); })
                ->join('commune', 'eleves.code_commune', '=', 'commune.code_comm')
                ->join('wilaya', 'commune.code_wilaya', '=', 'wilaya.code_wil')
                ->selectRaw('wilaya.lib_wil_ar as wilaya_name, wilaya.code_wil, count(*) as cnt')
                ->groupBy('wilaya.code_wil', 'wilaya.lib_wil_ar')
                ->orderByDesc('cnt')
                ->get()
                ->toArray();
        }

        // -- 7. Recent eleves (last 10 added) --
        $recentEleves = Eleve::where(function($q) use ($eleveScope) { $eleveScope($q); })
            ->with(['tuteur', 'etablissement'])
            ->orderByDesc('date_insertion')
            ->limit(10)
            ->get()
            ->map(fn($e) => [
                'num_scolaire' => $e->num_scolaire,
                'nom' => $e->nom,
                'prenom' => $e->prenom,
                'sexe' => $e->sexe,
                'niv_scol' => $e->niv_scol,
                'etablissement' => $e->etablissement->nom_etabliss ?? '—',
                'etat_das' => $e->etat_das,
                'etat_comite_wilaya' => $e->etat_comite_wilaya,
                'etat_final' => $e->etat_final,
                'date_insertion' => $e->date_insertion,
                'tuteur_nom' => ($e->tuteur->nom_ar ?? $e->tuteur->nom_fr ?? '') . ' ' . ($e->tuteur->prenom_ar ?? $e->tuteur->prenom_fr ?? ''),
            ]);

        // -- 8. Relation tuteur breakdown (ولي vs وصي) --
        $relationWali = Eleve::where(function($q) use ($eleveScope) { $eleveScope($q); })->where('relation_tuteur', 1)->count();
        $relationWasi = Eleve::where(function($q) use ($eleveScope) { $eleveScope($q); })->where('relation_tuteur', 3)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'role' => $userRole,
                'totals' => [
                    'eleves' => $totalEleves,
                    'tuteurs' => $totalTuteurs,
                    'schools' => $totalSchools,
                    'communes' => $totalCommunes,
                ],
                'das_status' => [
                    'accepte' => $dasAccepted,
                    'refuse' => $dasRefused,
                    'pending' => $dasPending,
                ],
                'comite_status' => [
                    'accepte' => $comiteAccepted,
                    'refuse' => $comiteRefused,
                    'pending' => $comitePending,
                ],
                'final_status' => [
                    'accepte' => $finalAccepted,
                    'refuse' => $finalRefused,
                    'pending' => $finalPending,
                ],
                'gender' => [
                    'male' => $genderMale,
                    'female' => $genderFemale,
                ],
                'education_levels' => $nivScol,
                'communes' => $communeStats,
                'wilayas' => $wilayaStats,
                'recent_eleves' => $recentEleves,
                'relation_tuteur' => [
                    'wali' => $relationWali,
                    'wasi' => $relationWasi,
                ],
            ],
        ]);
    }

    private function emptyStats(): array
    {
        return [
            'totals' => ['eleves' => 0, 'tuteurs' => 0, 'schools' => 0, 'communes' => 0],
            'das_status' => ['accepte' => 0, 'refuse' => 0, 'pending' => 0],
            'comite_status' => ['accepte' => 0, 'refuse' => 0, 'pending' => 0],
            'gender' => ['male' => 0, 'female' => 0],
            'education_levels' => [],
            'communes' => [],
            'recent_eleves' => [],
            'relation_tuteur' => ['wali' => 0, 'wasi' => 0],
        ];
    }

}