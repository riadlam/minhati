<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Import all controllers
use App\Http\Controllers\WilayaController;
use App\Http\Controllers\CommuneController;
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
Route::get('/tuteur/{nin}/eleves', [EleveController::class, 'byTuteur']);
Route::get('/children/check-matricule/{matricule}', [EleveController::class, 'checkMatricule']);

// Protected routes - require token authentication
// Note: api.tuteur middleware already checks for Sanctum tokens via $request->user()
Route::middleware(['api.tuteur'])->group(function () {
    Route::post('/eleves', [EleveController::class, 'store']);
    Route::put('/eleves/{num_scolaire}', [EleveController::class, 'update']);
    Route::delete('/eleves/{num_scolaire}', [EleveController::class, 'destroy']);
    Route::post('/eleves/{num_scolaire}/istimara/generate', [EleveController::class, 'generateIstimara']);
});

// Admin route for creating students (without tuteur auth, uses tuteur_nin parameter)
Route::post('/admin/eleves', [EleveController::class, 'store']);

// Protected logout routes - require token
// Using custom middleware that checks for Sanctum tokens
Route::middleware(['api.tuteur'])->group(function () {
    Route::post('/auth/tuteur/logout', [App\Http\Controllers\AuthController::class, 'apiLogout']);
});
Route::middleware(['api.user'])->group(function () {
    Route::post('/auth/user/logout', [UserController::class, 'apiLogout']);
});
