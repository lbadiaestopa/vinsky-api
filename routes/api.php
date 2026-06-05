<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\OrchestraController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::post('/v1/register', [RegisterController::class, 'store']);

Route::post('/v1/login', [LoginController::class, 'store']);

Route::middleware('auth:api')->group(function () {

    Route::post('/v1/logout', [LogoutController::class, 'store']);

    Route::get('/v1/me', [ProfileController::class, 'show']);

    Route::put('/v1/me', [ProfileController::class, 'update']);

    Route::put('v1/me/password', [ProfileController::class, 'updatePassword']);

    Route::delete('/v1/me', [ProfileController::class, 'destroy']);

    Route::post('/v1/orchestras', [OrchestraController::class, 'store']);
});
