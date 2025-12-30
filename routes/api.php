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

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| All routes here are prefixed with /api automatically.
| Example: http://localhost:8000/api/wilayas
|--------------------------------------------------------------------------
*/

// 🔐 Optional: Route for authenticated user (keep it for later if using Sanctum)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
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
Route::get('/tuteurs/{id}', [TuteurController::class, 'show']);
Route::post('/tuteurs', [TuteurController::class, 'store']);
Route::put('/tuteurs/{id}', [TuteurController::class, 'update']);
Route::delete('/tuteurs/{id}', [TuteurController::class, 'destroy']);

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
    Route::put('/eleves/{id}', [EleveController::class, 'update']);
    Route::delete('/eleves/{id}', [EleveController::class, 'destroy']);
    Route::post('/eleves/{num_scolaire}/istimara/generate', [EleveController::class, 'generateIstimara']);
});

// Protected logout routes - require token
// Using custom middleware that checks for Sanctum tokens
Route::middleware(['api.tuteur'])->group(function () {
    Route::post('/auth/tuteur/logout', [App\Http\Controllers\AuthController::class, 'apiLogout']);
});
Route::middleware(['api.user'])->group(function () {
    Route::post('/auth/user/logout', [UserController::class, 'apiLogout']);
});
