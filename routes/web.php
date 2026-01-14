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

// ✅ FIXED: use 'user.auth' (the alias you registered in bootstrap/app.php)
Route::middleware(['user.auth'])->group(function () {
    Route::get('/user/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
    Route::post('/user/logout', [UserController::class, 'logout'])->name('user.logout');
    
    // Main pages for ts_commune users
    Route::get('/user/tuteurs-list', [UserController::class, 'showTuteursList'])->name('user.tuteurs.list');
    Route::get('/user/students-list', [UserController::class, 'showStudentsList'])->name('user.students.list');
    Route::get('/user/add-student', [UserController::class, 'showAddStudent'])->name('user.add.student');
    
    // Tuteur management routes for ts_commune users
    Route::get('/user/tuteurs', [UserController::class, 'getTuteurs'])->name('user.tuteurs.get');
    Route::get('/user/tuteurs/{nin}', [UserController::class, 'viewTuteur'])->name('user.tuteurs.view');
    Route::delete('/user/tuteurs/{nin}', [UserController::class, 'deleteTuteur'])->name('user.tuteurs.delete');
    
    // Students management routes for ts_commune users
    Route::get('/user/eleves', [UserController::class, 'getEleves'])->name('user.eleves.get');
    
    // Eleve management routes for ts_commune users
    Route::get('/user/eleves/{num_scolaire}', [UserController::class, 'viewEleve'])->name('user.eleves.view');
    Route::post('/user/eleves/{num_scolaire}/approve', [UserController::class, 'approveEleve'])->name('user.eleves.approve');
    Route::delete('/user/eleves/{num_scolaire}', [UserController::class, 'deleteEleve'])->name('user.eleves.delete');
    
    // Comment routes for ts_commune users
    Route::post('/user/eleves/{num_scolaire}/comments', [UserController::class, 'storeComment'])->name('user.eleves.comments.store');
    Route::get('/user/eleves/{num_scolaire}/comments', [UserController::class, 'getComments'])->name('user.eleves.comments.index');
    
    // PDF istimara generation for normal users
    Route::post('/user/eleves/{num_scolaire}/istimara/generate', [EleveController::class, 'generateIstimaraForUser'])->name('user.eleves.istimara.generate');
    
    // Tuteurs pagination route (AJAX)
    Route::get('/user/tuteurs', [UserController::class, 'getTuteurs'])->name('user.tuteurs.index');
    Route::post('/user/tuteurs', [UserController::class, 'storeTuteurForCommune'])->name('user.tuteurs.store');
    Route::get('/user/eleves/export', [UserController::class, 'exportEleves'])->name('user.eleves.export');
});



/*
|--------------------------------------------------------------------------
| 👨‍👧 Tuteur protected routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth.tuteur')->group(function () {
    Route::get('/dashboard', fn() => view('tuteur-dashboard'))->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
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
});

// PDF viewing route - outside middleware to avoid session interference
// Still secured by checking session inside the controller
Route::get('/eleves/{num_scolaire}/istimara', [EleveController::class, 'viewIstimara']);
