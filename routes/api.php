<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\OrchestraController;

Route::prefix('v1')->group(function () {

    Route::post('/register', [RegisterController::class, 'store']);
    Route::post('/login', [LoginController::class, 'store']);

    Route::middleware('auth:api')->group(function () {

        Route::post('/logout', [LogoutController::class, 'store']);

        Route::get('/me', [ProfileController::class, 'show']);
        Route::put('/me', [ProfileController::class, 'update']);
        Route::put('/me/password', [ProfileController::class, 'updatePassword']);
        Route::delete('/me', [ProfileController::class, 'destroy']);

        Route::post('/orchestras', [OrchestraController::class, 'store']);
        Route::get('/orchestras', [OrchestraController::class, 'index']);
        Route::get('/orchestras/{orchestra}', [OrchestraController::class, 'show']);
        Route::put('/orchestras/{orchestra}', [OrchestraController::class, 'update']);
    });
});
