<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
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
    ->middleware(['auth', 'admin']);

Route::view('/ecommerce-customers', 'ecommerce-customers')
    ->middleware(['auth', 'admin']);

Route::view('/ecommerce-checkout', 'ecommerce-checkout')
    ->middleware(['auth', 'admin']);

Route::view('/email-template-basic', 'email-template-basic')
    ->middleware(['auth', 'admin']);

Route::view('/email-template-billing', 'email-template-billing')
    ->middleware(['auth', 'admin']);

/*
|--------------------------------------------------------------------------
| Users Management
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/users', [UserController::class, 'index'])
        ->name('users.index');

    Route::get('/users/create', [UserController::class, 'create'])
        ->name('users.create');

    Route::post('/users', [UserController::class, 'store'])
        ->name('users.store');

    Route::get('/users/{user}/edit', [UserController::class, 'edit'])
        ->name('users.edit');

    Route::put('/users/{user}', [UserController::class, 'update'])
        ->name('users.update');

    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->name('users.destroy');

});
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