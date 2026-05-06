<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Custom Auth Pages
|--------------------------------------------------------------------------
*/

Route::view('/custom-login', 'auth-login-2')->name('custom.login');

Route::view('/custom-register', 'auth-register-2')->name('custom.register');

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard-saas');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Custom Pages
|--------------------------------------------------------------------------
*/

Route::view('/form-advanced', 'form-advanced')
    ->middleware('auth');

Route::view('/ecommerce-customers', 'ecommerce-customers')
    ->middleware('auth');

Route::view('/ecommerce-checkout', 'ecommerce-checkout')
    ->middleware('auth');

Route::view('/email-template-basic', 'email-template-basic')
    ->middleware('auth');

Route::view('/email-template-billing', 'email-template-billing')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

});

require __DIR__.'/auth.php';