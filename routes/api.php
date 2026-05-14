<?php

use App\Http\Controllers\Api\UserGroupApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\NewsApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Auth API
    |--------------------------------------------------------------------------
    */

    Route::post('/login', [AuthApiController::class, 'login']);

    /*
    |--------------------------------------------------------------------------
    | Public News / Categories API
    |--------------------------------------------------------------------------
    | Không cần token vẫn xem được.
    */

    Route::get('/categories', [CategoryApiController::class, 'index']);
    Route::get('/categories/{category}', [CategoryApiController::class, 'show']);

    Route::get('/news', [NewsApiController::class, 'index']);
    Route::get('/news/{news}', [NewsApiController::class, 'show']);

    /*
    |--------------------------------------------------------------------------
    | Protected API - cần token Sanctum
    |--------------------------------------------------------------------------
    | Những API thêm / sửa / xoá phải đăng nhập bằng token.
    */

    Route::middleware('auth:sanctum')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Auth Protected
        |--------------------------------------------------------------------------
        */

        Route::get('/user', [AuthApiController::class, 'me']);
        Route::post('/logout', [AuthApiController::class, 'logout']);

        /*
        |--------------------------------------------------------------------------
        | Categories CRUD API
        |--------------------------------------------------------------------------
        */

        Route::post('/categories', [CategoryApiController::class, 'store']);
        Route::put('/categories/{category}', [CategoryApiController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryApiController::class, 'destroy']);

        Route::post('/news', [NewsApiController::class, 'store']);
        Route::put('/news/{news}', [NewsApiController::class, 'update']);
        Route::delete('/news/{news}', [NewsApiController::class, 'destroy']);

        Route::get('/users', [UserApiController::class, 'index']);
        Route::get('/users/{user}', [UserApiController::class, 'show']);
        Route::post('/users', [UserApiController::class, 'store']);
        Route::put('/users/{user}', [UserApiController::class, 'update']);
        Route::delete('/users/{user}', [UserApiController::class, 'destroy']);

        Route::get('/user-groups', [UserGroupApiController::class, 'index']);
        Route::get('/user-groups/{userGroup}', [UserGroupApiController::class, 'show']);
        Route::post('/user-groups', [UserGroupApiController::class, 'store']);
        Route::put('/user-groups/{userGroup}', [UserGroupApiController::class, 'update']);
        Route::delete('/user-groups/{userGroup}', [UserGroupApiController::class, 'destroy']);

        Route::get('/user-groups/{userGroup}/permissions', [UserGroupApiController::class, 'permissions']);
        Route::post('/user-groups/{userGroup}/permissions', [UserGroupApiController::class, 'updatePermissions']);

    });

});