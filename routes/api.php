<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Import all controllers
use App\Http\Controllers\WilayaController;
use App\Http\Controllers\CommuneController;
use App\Http\Controllers\DairaController;
use App\Http\Controllers\AntenneController;
use App\Http\Controllers\DirectionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EtablissementController;
use App\Http\Controllers\TuteurController;
use App\Http\Controllers\EleveController;
use App\Http\Controllers\MotherController;
use App\Http\Controllers\FatherController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| All routes here are prefixed with /api automatically.
| Example: http://localhost:8000/api/wilayas
|--------------------------------------------------------------------------
*/

// 🔐 Get current authenticated user (for both tuteur and user)
Route::middleware(['api.user'])->get('/user/current', [UserController::class, 'getCurrentUser']);

/*
|--------------------------------------------------------------------------
| 👤 User Data API (host-2 only data actions)
|--------------------------------------------------------------------------
| These mirror /user/* JSON actions from web routes so frontend can call
| MINHATI_API_URL for all DB reads/writes.
*/
Route::middleware(['api.user'])->prefix('user')->group(function () {
    Route::get('/dashboard-stats', [UserController::class, 'apiDashboardStats']);
    Route::get('/schools', [UserController::class, 'apiUserSchools']);
    Route::get('/tuteurs', [UserController::class, 'getTuteurs']);
    Route::post('/tuteurs', [UserController::class, 'storeTuteurForCommune']);
    Route::get('/tuteurs/{nin}', [UserController::class, 'viewTuteur']);
    Route::post('/tuteurs/{nin}/approve-all', [UserController::class, 'approveAllElevesForTuteur']);
    Route::post('/tuteurs/{nin}/decline-all', [UserController::class, 'declineAllElevesForTuteur']);
    Route::patch('/tuteurs/{nin}/dossier-depose-bulk', [UserController::class, 'updateTuteurDossierDeposeBulk']);
    Route::delete('/tuteurs/{nin}', [UserController::class, 'deleteTuteur']);

    Route::get('/eleves', [UserController::class, 'getEleves']);
    Route::get('/eleves/pending', [UserController::class, 'getPendingEleves']);
    Route::get('/eleves/approved', [UserController::class, 'getApprovedEleves']);
    Route::get('/eleves/{num_scolaire}', [UserController::class, 'viewEleve']);
    Route::post('/eleves/{num_scolaire}/approve', [UserController::class, 'approveEleve']);
    Route::post('/eleves/{num_scolaire}/decline', [UserController::class, 'declineEleve']);
    Route::patch('/eleves/{num_scolaire}/dossier-depose', [UserController::class, 'updateDossierDepose']);
    Route::delete('/eleves/{num_scolaire}', [UserController::class, 'deleteEleve']);
    Route::post('/eleves/{num_scolaire}/comments', [UserController::class, 'storeComment']);
    Route::get('/eleves/{num_scolaire}/comments', [UserController::class, 'getComments']);
    Route::post('/eleves/{num_scolaire}/istimara/generate', [EleveController::class, 'generateIstimaraForUser']);

    // Appeal management (admin-side)
    Route::get('/eleves/{num_scolaire}/appeal', [UserController::class, 'getAppealDetails']);
    Route::post('/eleves/{num_scolaire}/appeal/accept', [UserController::class, 'acceptAppeal']);
    Route::post('/eleves/{num_scolaire}/appeal/refuse', [UserController::class, 'refuseAppeal']);
});

/*
|--------------------------------------------------------------------------
| 📊 DAS (Direction de l'Action Sociale) Routes - wilaya-scoped, dossier_depose=oui
|--------------------------------------------------------------------------
| For users with role "das". Eleves: communes under user's code_wilaya + dossier_depose=oui.
| Tuteurs: those who have at least one such eleve.
*/
Route::middleware(['api.user'])->prefix('das')->group(function () {
    Route::get('/eleves', [UserController::class, 'apiDasEleves']);
    Route::get('/tuteurs', [UserController::class, 'apiDasTuteurs']);
    
    // DAS Accept/Decline actions
    Route::post('/eleves/{num_scolaire}/accept', [UserController::class, 'dasAcceptEleve']);
    Route::post('/eleves/{num_scolaire}/decline', [UserController::class, 'dasDeclineEleve']);
    Route::post('/tuteurs/{nin}/accept', [UserController::class, 'dasAcceptTuteur']);
    Route::post('/tuteurs/{nin}/decline', [UserController::class, 'dasDeclineTuteur']);
});

/*
|--------------------------------------------------------------------------
| Comité Wilaya Routes - wilaya-scoped, only eleves with etat_das in (accepte, refuse)
|--------------------------------------------------------------------------
*/
Route::middleware(['api.user'])->prefix('comite_wilaya')->group(function () {
    Route::post('/eleves/{num_scolaire}/accept', [UserController::class, 'comiteAcceptEleve']);
    Route::post('/eleves/{num_scolaire}/decline', [UserController::class, 'comiteDeclineEleve']);
    Route::patch('/eleves/{num_scolaire}/refuse-details', [UserController::class, 'comiteUpdateEleveRefuseDetails']);
    Route::post('/tuteurs/{nin}/accept', [UserController::class, 'comiteAcceptTuteur']);
    Route::post('/tuteurs/{nin}/decline', [UserController::class, 'comiteDeclineTuteur']);
    Route::patch('/tuteurs/{nin}/refuse-details', [UserController::class, 'comiteUpdateTuteurRefuseDetails']);
});

/*
|--------------------------------------------------------------------------
| 🏛️ ATR (Antenne Régionale) Routes - region-scoped, only eleves with etat_das=accepte AND etat_comite_wilaya=accepte
|--------------------------------------------------------------------------
*/
Route::middleware(['api.user'])->prefix('antr')->group(function () {
    Route::post('/eleves/{num_scolaire}/accept', [UserController::class, 'antrAcceptEleve']);
    Route::post('/eleves/{num_scolaire}/decline', [UserController::class, 'antrDeclineEleve']);
    Route::post('/tuteurs/{nin}/accept', [UserController::class, 'antrAcceptTuteur']);
    Route::post('/tuteurs/{nin}/decline', [UserController::class, 'antrDeclineTuteur']);
});

/*
|--------------------------------------------------------------------------
| 📍 Wilaya Routes
|--------------------------------------------------------------------------
*/
Route::get('/wilayas', [WilayaController::class, 'index']);
Route::get('/wilayas/{id}', [WilayaController::class, 'show']);
Route::post('/wilayas', [WilayaController::class, 'store']);
Route::put('/wilayas/{id}', [WilayaController::class, 'update']);
Route::delete('/wilayas/{id}', [WilayaController::class, 'destroy']);

/*
|--------------------------------------------------------------------------
| 🏘️ Commune Routes
|--------------------------------------------------------------------------
*/
Route::get('/communes', [CommuneController::class, 'index']);
Route::get('/communes/{id}', [CommuneController::class, 'show']);
Route::post('/communes', [CommuneController::class, 'store']);
Route::put('/communes/{id}', [CommuneController::class, 'update']);
Route::delete('/communes/{id}', [CommuneController::class, 'destroy']);
Route::get('/communes/by-wilaya/{wilayaId}', [CommuneController::class, 'getByWilaya']);
Route::get('/communes/by-wilaya-daira/{wilayaId}/{dairaName}', [CommuneController::class, 'getByWilayaAndDaira']);

/*
||--------------------------------------------------------------------------
|| 🏛️ Daira Routes
||--------------------------------------------------------------------------
*/
Route::get('/dairas/by-wilaya/{wilayaId}', [DairaController::class, 'getByWilaya']);
Route::get('/dairas/by-commune/{communeCode}', [DairaController::class, 'getByCommune']);

/*
|--------------------------------------------------------------------------
| 🏢 Antenne Routes
|--------------------------------------------------------------------------
*/
Route::get('/antennes', [AntenneController::class, 'index']);
Route::get('/antennes/{id}', [AntenneController::class, 'show']);
Route::post('/antennes', [AntenneController::class, 'store']);
Route::put('/antennes/{id}', [AntenneController::class, 'update']);
Route::delete('/antennes/{id}', [AntenneController::class, 'destroy']);

/*
|--------------------------------------------------------------------------
| 🧭 Direction Routes
|--------------------------------------------------------------------------
*/
Route::get('/directions', [DirectionController::class, 'index']);
Route::get('/directions/{id}', [DirectionController::class, 'show']);
Route::post('/directions', [DirectionController::class, 'store']);
Route::put('/directions/{id}', [DirectionController::class, 'update']);
Route::delete('/directions/{id}', [DirectionController::class, 'destroy']);

/*
|--------------------------------------------------------------------------
| 👤 User Routes
|--------------------------------------------------------------------------
*/
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::post('/users', [UserController::class, 'store']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);
// Admin create-user endpoint: support both bearer token and web session auth.
Route::middleware(['web', 'api.user'])->post('/admin/users', [UserController::class, 'storeByAdmin']);
Route::middleware(['web', 'api.user'])->get('/admin/users', [UserController::class, 'apiAdminUsers']);
Route::middleware(['web', 'api.user'])->get('/admin/users/{code_user}', [UserController::class, 'apiAdminShowUser']);
Route::middleware(['web', 'api.user'])->put('/admin/users/{code_user}', [UserController::class, 'apiAdminUpdateUser']);

/*
|--------------------------------------------------------------------------
| 🏫 Etablissement Routes
|--------------------------------------------------------------------------
*/
// Filtered route (handles both filtered and unfiltered requests)
Route::get('/etablissements', [EtablissementController::class, 'getByFilters']);
Route::get('/etablissements/{id}', [EtablissementController::class, 'show']);
Route::post('/etablissements', [EtablissementController::class, 'store']);
Route::put('/etablissements/{id}', [EtablissementController::class, 'update']);
Route::delete('/etablissements/{id}', [EtablissementController::class, 'destroy']);

/*
|--------------------------------------------------------------------------
| 🔐 Authentication Routes (Public)
|--------------------------------------------------------------------------
*/
// Enable web middleware for session support in login routes
Route::middleware('web')->group(function () {
    Route::post('/auth/tuteur/login', [App\Http\Controllers\AuthController::class, 'apiLogin']);
    Route::post('/auth/user/login', [UserController::class, 'apiLogin']);
});

/*
|--------------------------------------------------------------------------
| 👨‍👧 Tuteur Routes
|--------------------------------------------------------------------------
*/
Route::get('/tuteurs', [TuteurController::class, 'index']);
// Get mothers for a tuteur - MUST be before /tuteurs/{id} to avoid route conflict
Route::middleware(['api.tuteur'])->get('/tuteurs/mothers', [TuteurController::class, 'getMothers']);
Route::get('/tuteurs/{id}', [TuteurController::class, 'show']);
Route::post('/tuteurs', [TuteurController::class, 'store']);
Route::middleware(['api.tuteur'])->put('/tuteurs/{id}', [TuteurController::class, 'update']);
Route::delete('/tuteurs/{id}', [TuteurController::class, 'destroy']);
Route::post('/check/mother/nin', [TuteurController::class, 'checkMotherNIN']);
Route::post('/check/mother/nss', [TuteurController::class, 'checkMotherNSS']);
Route::post('/check/father/nin', [TuteurController::class, 'checkFatherNIN']);
Route::post('/check/tuteur/exists', [TuteurController::class, 'checkTuteurExists']);

/*
|--------------------------------------------------------------------------
| 👩 Mothers CRUD Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['api.tuteur'])->group(function () {
    Route::get('/mothers', [MotherController::class, 'index']);
    Route::post('/mothers', [MotherController::class, 'store']);
    Route::get('/mothers/{id}', [MotherController::class, 'show']);
    Route::put('/mothers/{id}', [MotherController::class, 'update']);
    Route::delete('/mothers/{id}', [MotherController::class, 'destroy']);
});

// Admin routes for mothers (without tuteur auth, uses tuteur_nin parameter)
Route::get('/admin/mothers', [MotherController::class, 'index']);
Route::post('/admin/mothers', [MotherController::class, 'store']);

/*
|--------------------------------------------------------------------------
| 👨 Fathers CRUD Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['api.tuteur'])->group(function () {
    Route::get('/fathers', [FatherController::class, 'index']);
    Route::post('/fathers', [FatherController::class, 'store']);
    Route::get('/fathers/{id}', [FatherController::class, 'show']);
    Route::put('/fathers/{id}', [FatherController::class, 'update']);
    Route::delete('/fathers/{id}', [FatherController::class, 'destroy']);
});

// Admin routes for fathers (without tuteur auth, uses tuteur_nin parameter)
Route::get('/admin/fathers', [FatherController::class, 'index']);
Route::post('/admin/fathers', [FatherController::class, 'store']);

/*
|--------------------------------------------------------------------------
| 🧒 Élève Routes
|--------------------------------------------------------------------------
*/
Route::get('/eleves', [EleveController::class, 'index']);
Route::get('/eleves/{id}', [EleveController::class, 'show']);
Route::get('/children/check-matricule/{matricule}', [EleveController::class, 'checkMatricule']);

// Protected routes - require token authentication
// Note: api.tuteur middleware already checks for Sanctum tokens via $request->user()
Route::middleware(['api.tuteur'])->group(function () {
    Route::get('/tuteur/{nin}/eleves', [EleveController::class, 'byTuteur']);
    Route::post('/eleves', [EleveController::class, 'store']);
    Route::put('/eleves/{num_scolaire}', [EleveController::class, 'update']);
    Route::delete('/eleves/{num_scolaire}', [EleveController::class, 'destroy']);
    Route::post('/eleves/{num_scolaire}/istimara/generate', [EleveController::class, 'generateIstimara']);
    Route::post('/eleves/{num_scolaire}/appeal', [EleveController::class, 'submitAppeal']);
});

// Admin route for creating students (without tuteur auth, uses tuteur_nin parameter)
Route::post('/admin/eleves', [EleveController::class, 'store']);

// Serve private files securely (for admin users)
// Allow session-based auth for file serving (middleware will allow pass-through, controller handles auth)
Route::middleware(['web', 'api.user'])->get('/user/files/{path}', [UserController::class, 'serveFile'])->where('path', '.*');

// Protected logout routes - require token
// Using custom middleware that checks for Sanctum tokens
Route::middleware(['api.tuteur'])->group(function () {
    Route::post('/auth/tuteur/logout', [App\Http\Controllers\AuthController::class, 'apiLogout']);
});
Route::middleware(['api.user'])->group(function () {
    Route::post('/auth/user/logout', [UserController::class, 'apiLogout']);
});
