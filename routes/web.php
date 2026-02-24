<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\EleveController;
use App\Http\Controllers\TuteurController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserDashboardController; // ✅ if missing

/*
|--------------------------------------------------------------------------
| Public routes (no session required)
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => view('welcome'))->name('home');


// Login + signup
Route::get('/login', fn() => view('auth.login'))->name('login.form');
Route::get('/signup', fn() => view('auth.signup'))->name('signup');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Password reset
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// change password
Route::post('/change-password', [UserController::class, 'changePassword'])->name('password.change');

// Test layout route
Route::get('/test-layout', fn() => view('test'));

// Tuteur registration
Route::post('/tuteurs', [TuteurController::class, 'store'])->name('tuteurs.store');

/*
|--------------------------------------------------------------------------
| 🧍‍♂️ Agents de saisie (users)
|--------------------------------------------------------------------------
*/

Route::get('/user/login', [UserController::class, 'showLoginForm'])->name('user.login');
Route::post('/user/login', [UserController::class, 'login'])->name('user.login.submit');
Route::post('/users', [UserController::class, 'store']);

// Impersonation: apply token (no auth; token is the auth)
Route::get('/user/as/{token}', [UserController::class, 'applyImpersonation'])->name('user.impersonate.apply');

// ✅ FIXED: use 'user.auth' + block writes when in "logged in as" (read-only) mode
Route::middleware(['user.auth', 'block.writes.impersonate'])->group(function () {
    Route::get('/user/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
    Route::post('/user/logout', [UserController::class, 'logout'])->name('user.logout');
    Route::match(['get', 'post'], '/user/end-impersonation', [UserController::class, 'endImpersonation'])->name('user.impersonate.end');
    Route::get('/user/users-list', [UserController::class, 'showUsersList'])->name('user.users.list');

    // Admin: ts_commune management (wilaya → commune grids, open as ts_commune)
    Route::get('/user/admin/ts-commune-management', [UserController::class, 'showTsCommuneManagement'])->name('user.admin.ts_commune.management');
    Route::get('/user/admin/wilayas', [UserController::class, 'getWilayasForAdmin'])->name('user.admin.wilayas');
    Route::get('/user/admin/communes', [UserController::class, 'getCommunesByWilayaForAdmin'])->name('user.admin.communes');
    Route::get('/user/admin/commune-users', [UserController::class, 'getUsersByCommuneForAdmin'])->name('user.admin.commune.users');
    Route::get('/user/admin/impersonate-ts-commune', [UserController::class, 'impersonateTsCommune'])->name('user.admin.impersonate.ts_commune');
    
    // Main pages for ts_commune users
    Route::get('/user/tuteurs-list', [UserController::class, 'showTuteursList'])->name('user.tuteurs.list');
    Route::get('/user/students-list', [UserController::class, 'showStudentsList'])->name('user.students.list');
    Route::get('/user/add-student', [UserController::class, 'showAddStudent'])->name('user.add.student');
    Route::get('/user/pending-requests', [UserController::class, 'showPendingRequests'])->name('user.pending.requests');
    Route::get('/user/approved-requests', [UserController::class, 'showApprovedRequests'])->name('user.approved.requests');
    
    // Tuteur management routes for ts_commune users
    // Tuteurs routes - specific routes first, then parameterized
    Route::get('/user/tuteurs', [UserController::class, 'getTuteurs'])->name('user.tuteurs.get');
    Route::post('/user/tuteurs', [UserController::class, 'storeTuteurForCommune'])->name('user.tuteurs.store');
    // Export route must come before parameterized route
    Route::get('/user/tuteurs/export-excel', [UserController::class, 'exportTuteursToExcel'])->name('user.tuteurs.export.excel');
    // Parameterized routes come after specific routes
    Route::get('/user/tuteurs/{nin}', [UserController::class, 'viewTuteur'])->name('user.tuteurs.view');
    Route::delete('/user/tuteurs/{nin}', [UserController::class, 'deleteTuteur'])->name('user.tuteurs.delete');
    
    // Students management routes for ts_commune users
    Route::get('/user/eleves', [UserController::class, 'getEleves'])->name('user.eleves.get');
    Route::get('/user/eleves/pending', [UserController::class, 'getPendingEleves'])->name('user.eleves.pending');
    Route::get('/user/eleves/approved', [UserController::class, 'getApprovedEleves'])->name('user.eleves.approved');
    // Export routes (must come before parameterized routes)
    Route::get('/user/eleves/export', [UserController::class, 'exportEleves'])->name('user.eleves.export');
    Route::get('/user/eleves/export-excel', [UserController::class, 'exportStudentsToExcel'])->name('user.eleves.export.excel');
    
    // Eleve management routes for ts_commune users (parameterized routes come after specific routes)
    Route::get('/user/eleves/{num_scolaire}', [UserController::class, 'viewEleve'])->name('user.eleves.view');
    Route::post('/user/eleves/{num_scolaire}/approve', [UserController::class, 'approveEleve'])->name('user.eleves.approve');
    Route::delete('/user/eleves/{num_scolaire}', [UserController::class, 'deleteEleve'])->name('user.eleves.delete');
    
    // Comment routes for ts_commune users
    Route::post('/user/eleves/{num_scolaire}/comments', [UserController::class, 'storeComment'])->name('user.eleves.comments.store');
    Route::get('/user/eleves/{num_scolaire}/comments', [UserController::class, 'getComments'])->name('user.eleves.comments.index');
    
    // PDF istimara generation for normal users
    Route::post('/user/eleves/{num_scolaire}/istimara/generate', [EleveController::class, 'generateIstimaraForUser'])->name('user.eleves.istimara.generate');
    
    // DAS Accept/Decline routes
    Route::post('/user/das/eleves/{num_scolaire}/accept', [UserController::class, 'dasAcceptEleve'])->name('user.das.eleves.accept');
    Route::post('/user/das/eleves/{num_scolaire}/decline', [UserController::class, 'dasDeclineEleve'])->name('user.das.eleves.decline');
    Route::post('/user/das/tuteurs/{nin}/accept', [UserController::class, 'dasAcceptTuteur'])->name('user.das.tuteurs.accept');
    Route::post('/user/das/tuteurs/{nin}/decline', [UserController::class, 'dasDeclineTuteur'])->name('user.das.tuteurs.decline');

    // ATR (Antenne Régionale) Accept/Decline routes
    Route::post('/user/antr/eleves/{num_scolaire}/accept', [UserController::class, 'antrAcceptEleve'])->name('user.antr.eleves.accept');
    Route::post('/user/antr/eleves/{num_scolaire}/decline', [UserController::class, 'antrDeclineEleve'])->name('user.antr.eleves.decline');
    Route::post('/user/antr/tuteurs/{nin}/accept', [UserController::class, 'antrAcceptTuteur'])->name('user.antr.tuteurs.accept');
    Route::post('/user/antr/tuteurs/{nin}/decline', [UserController::class, 'antrDeclineTuteur'])->name('user.antr.tuteurs.decline');
});



/*
|--------------------------------------------------------------------------
| 👨‍👧 Tuteur protected routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth.tuteur')->group(function () {
    Route::get('/dashboard', fn() => view('tuteur-dashboard'))->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/tuteur/api-token', [AuthController::class, 'issueSessionToken'])->name('tuteur.api-token');
    Route::get('/tuteur/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('tuteur.profile');
    Route::get('/tuteur/father', [App\Http\Controllers\ProfileController::class, 'showFather'])->name('tuteur.father');
    Route::get('/tuteur/mother', [App\Http\Controllers\ProfileController::class, 'showMother'])->name('tuteur.mother');
    // Father/Mother web forms (session + CSRF)
    Route::post('/tuteur/fathers', [App\Http\Controllers\ProfileController::class, 'storeFather'])->name('tuteur.fathers.store');
    Route::put('/tuteur/fathers/{father}', [App\Http\Controllers\ProfileController::class, 'updateFather'])->name('tuteur.fathers.update');
    Route::delete('/tuteur/fathers/{father}', [App\Http\Controllers\ProfileController::class, 'destroyFather'])->name('tuteur.fathers.destroy');
    
    Route::post('/tuteur/mothers', [App\Http\Controllers\ProfileController::class, 'storeMother'])->name('tuteur.mothers.store');
    Route::put('/tuteur/mothers/{mother}', [App\Http\Controllers\ProfileController::class, 'updateMother'])->name('tuteur.mothers.update');
    Route::delete('/tuteur/mothers/{mother}', [App\Http\Controllers\ProfileController::class, 'destroyMother'])->name('tuteur.mothers.destroy');
    Route::put('/tuteur/mother', [App\Http\Controllers\ProfileController::class, 'updateSingleMother'])->name('tuteur.mother.update');
    Route::get('/tuteur/{nin}/eleves', [EleveController::class, 'byTuteur'])->name('tuteur.eleves');
    Route::post('/eleves', [EleveController::class, 'store'])->name('eleves.store');
    Route::get('/eleves/{num_scolaire}', [EleveController::class, 'show'])->name('eleves.show');
    Route::get('/eleves/{num_scolaire}/edit', [EleveController::class, 'edit'])->name('eleves.edit');
    Route::put('/eleves/{num_scolaire}', [EleveController::class, 'update'])->name('eleves.update');
    Route::delete('/eleves/{num_scolaire}', [EleveController::class, 'destroy'])->name('eleves.destroy');
    Route::post('/eleves/{num_scolaire}/istimara/generate', [EleveController::class, 'generateIstimara']);
    Route::get('/eleves/{num_scolaire}/download', [EleveController::class, 'downloadIstimara']);
    
    // Comments routes for tuteurs
    Route::get('/eleves/{num_scolaire}/comments', [EleveController::class, 'getComments'])->name('eleves.comments.index');

    // Appeal routes for tuteurs
    Route::post('/eleves/{num_scolaire}/appeal', [EleveController::class, 'submitAppeal'])->name('eleves.appeal.submit');
});

// PDF viewing route - outside middleware to avoid session interference
// Still secured by checking session inside the controller
Route::get('/eleves/{num_scolaire}/istimara', [EleveController::class, 'viewIstimara']);
