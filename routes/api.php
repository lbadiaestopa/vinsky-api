<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\OrchestraController;
use App\Http\Controllers\Api\V1\MembershipController;
use App\Http\Controllers\Api\V1\ProgramController;
use App\Http\Controllers\Api\V1\EventController;
use App\Http\Controllers\Api\V1\ScoreController;

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
        Route::delete('/orchestras/{orchestra}', [OrchestraController::class, 'destroy']);

        Route::post('/memberships', [MembershipController::class, 'store']);
        Route::get('/memberships', [MembershipController::class, 'index']);
        Route::get('/orchestras/{orchestra}/memberships', [MembershipController::class, 'listMemberships']);
        Route::get('/memberships/{membership}', [MembershipController::class, 'show']);
        Route::put('/memberships/{membership}', [MembershipController::class, 'update']);
        Route::delete('/memberships/{membership}', [MembershipController::class, 'destroy']);

        Route::post('/orchestras/{orchestra}/programs', [ProgramController::class, 'store']);
        Route::get('/orchestras/{orchestra}/programs', [ProgramController::class, 'index']);
        Route::get('/programs/{program}', [ProgramController::class, 'show']);
        Route::put('/programs/{program}', [ProgramController::class, 'update']);
        Route::delete('/programs/{program}', [ProgramController::class, 'destroy']);

        Route::post('/programs/{program}/events', [EventController::class, 'store']);
        Route::get('/programs/{program}/events', [EventController::class, 'index']);
        Route::get('/programs/{program}/events/{event}', [EventController::class, 'show']);
        Route::put('/programs/{program}/events/{event}', [EventController::class, 'update']);
        Route::delete('/programs/{program}/events/{event}', [EventController::class, 'destroy']);

        Route::post('/programs/{program}/scores', [ScoreController::class, 'store']);
        Route::get('/programs/{program}/scores', [ScoreController::class, 'index']);
        Route::get('/programs/{program}/scores/{score}/download', [ScoreController::class, 'download'])->scopeBindings();
        Route::delete('/programs/{program}/scores/{score}', [ScoreController::class, 'destroy'])->scopeBindings();
    });
});
