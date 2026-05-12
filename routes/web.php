<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserGroupController;
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
})
    ->middleware(['auth', 'verified', 'permission:DashboardController,index'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Custom Pages
|--------------------------------------------------------------------------
*/

Route::view('/form-advanced', 'form-advanced')
    ->middleware(['auth', 'permission:PageController,form_advanced'])
    ->name('form.advanced');

Route::view('/ecommerce-customers', 'ecommerce-customers')
    ->middleware(['auth', 'permission:PageController,ecommerce_customers'])
    ->name('ecommerce.customers');

Route::view('/ecommerce-checkout', 'ecommerce-checkout')
    ->middleware(['auth', 'permission:PageController,ecommerce_checkout'])
    ->name('ecommerce.checkout');

Route::view('/email-template-basic', 'email-template-basic')
    ->middleware(['auth', 'permission:PageController,email_template_basic'])
    ->name('email.basic');

Route::view('/email-template-billing', 'email-template-billing')
    ->middleware(['auth', 'permission:PageController,email_template_billing'])
    ->name('email.billing');

/*
|--------------------------------------------------------------------------
| News Categories Management
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/categories', [CategoryController::class, 'index'])
        ->middleware('permission:CategoryController,index')
        ->name('categories.index');

    Route::get('/categories/create', [CategoryController::class, 'create'])
        ->middleware('permission:CategoryController,create')
        ->name('categories.create');

    Route::post('/categories', [CategoryController::class, 'store'])
        ->middleware('permission:CategoryController,store')
        ->name('categories.store');

    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])
        ->middleware('permission:CategoryController,edit')
        ->name('categories.edit');

    Route::put('/categories/{category}', [CategoryController::class, 'update'])
        ->middleware('permission:CategoryController,update')
        ->name('categories.update');

    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])
        ->middleware('permission:CategoryController,destroy')
        ->name('categories.destroy');

});

/*
|--------------------------------------------------------------------------
| News Management
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/news', [NewsController::class, 'index'])
        ->middleware('permission:NewsController,index')
        ->name('news.index');

    Route::get('/news/create', [NewsController::class, 'create'])
        ->middleware('permission:NewsController,create')
        ->name('news.create');

    Route::post('/news', [NewsController::class, 'store'])
        ->middleware('permission:NewsController,store')
        ->name('news.store');

    Route::get('/news/{news}/edit', [NewsController::class, 'edit'])
        ->middleware('permission:NewsController,edit')
        ->name('news.edit');

    Route::put('/news/{news}', [NewsController::class, 'update'])
        ->middleware('permission:NewsController,update')
        ->name('news.update');

    Route::delete('/news/{news}', [NewsController::class, 'destroy'])
        ->middleware('permission:NewsController,destroy')
        ->name('news.destroy');

});

/*
|--------------------------------------------------------------------------
| User Groups Management
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/user-groups', [UserGroupController::class, 'index'])
        ->middleware('permission:UserGroupController,index')
        ->name('user-groups.index');

    Route::get('/user-groups/create', [UserGroupController::class, 'create'])
        ->middleware('permission:UserGroupController,create')
        ->name('user-groups.create');

    Route::post('/user-groups', [UserGroupController::class, 'store'])
        ->middleware('permission:UserGroupController,store')
        ->name('user-groups.store');

    Route::get('/user-groups/{userGroup}/edit', [UserGroupController::class, 'edit'])
        ->middleware('permission:UserGroupController,edit')
        ->name('user-groups.edit');

    Route::put('/user-groups/{userGroup}', [UserGroupController::class, 'update'])
        ->middleware('permission:UserGroupController,update')
        ->name('user-groups.update');

    Route::delete('/user-groups/{userGroup}', [UserGroupController::class, 'destroy'])
        ->middleware('permission:UserGroupController,destroy')
        ->name('user-groups.destroy');

    Route::get('/user-groups/{userGroup}/permissions', [UserGroupController::class, 'permissions'])
        ->middleware('permission:UserGroupController,permissions')
        ->name('user-groups.permissions');

    Route::post('/user-groups/{userGroup}/permissions', [UserGroupController::class, 'updatePermissions'])
        ->middleware('permission:UserGroupController,updatePermissions')
        ->name('user-groups.permissions.update');

});

/*
|--------------------------------------------------------------------------
| Users Management
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/users', [UserController::class, 'index'])
        ->middleware('permission:UserController,index')
        ->name('users.index');

    Route::get('/users/create', [UserController::class, 'create'])
        ->middleware('permission:UserController,create')
        ->name('users.create');

    Route::post('/users', [UserController::class, 'store'])
        ->middleware('permission:UserController,store')
        ->name('users.store');

    Route::get('/users/{user}/edit', [UserController::class, 'edit'])
        ->middleware('permission:UserController,edit')
        ->name('users.edit');

    Route::put('/users/{user}', [UserController::class, 'update'])
        ->middleware('permission:UserController,update')
        ->name('users.update');

    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->middleware('permission:UserController,destroy')
        ->name('users.destroy');

});

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->middleware('permission:ProfileController,edit')
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->middleware('permission:ProfileController,update')
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->middleware('permission:ProfileController,destroy')
        ->name('profile.destroy');

});

require __DIR__.'/auth.php';