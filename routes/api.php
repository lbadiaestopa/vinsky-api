<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Resources\UserResource;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::post('/v1/register', [RegisterController::class, 'store']);

Route::post('/v1/login', [LoginController::class, 'store']);

Route::middleware('auth:api')->post('/v1/logout', [LogoutController::class, 'store']);

Route::middleware('auth:api')->get('/v1/me', function (Request $request) {
    return new UserResource($request->user());
});
