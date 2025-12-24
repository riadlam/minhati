<?php

use Illuminate\Support\Facades\Route;
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

Route::get('/tuteur/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('tuteur.profile');


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
});



/*
|--------------------------------------------------------------------------
| 👨‍👧 Tuteur protected routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth.tuteur')->group(function () {
    Route::get('/dashboard', fn() => view('tuteur-dashboard'))->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/tuteur/{nin}/eleves', [EleveController::class, 'byTuteur'])->name('tuteur.eleves');
    Route::post('/eleves', [EleveController::class, 'store'])->name('eleves.store');
    Route::get('/eleves/{num_scolaire}/istimara', [EleveController::class, 'viewIstimara']);
    Route::get('/eleves/{num_scolaire}/download', [EleveController::class, 'downloadIstimara']);
});
