<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use \Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

it('logs out an authenticated user', function () {

    $user = User::factory()->create();

    $token = $user->createToken('TestToken')->accessToken;

    $response = $this->withHeaders([
        'Authorization' => "Bearer $token",
    ])->postJson('/api/v1/logout');

    $response->assertStatus(200);

    Auth::guard('api')->forgetUser();

    $protectedResponse = $this->withHeaders([
        'Authorization' => "Bearer $token",
    ])->getJson('/api/v1/me');

    $protectedResponse->assertStatus(401);
});

it('does not allow unauthenticated users to logout', function () {

    $response = $this->postJson('/api/v1/logout');

    $response->assertStatus(401);

    $user = User::factory()->create();

    $token = $user->createToken('TestToken')->accessToken;
    
    $logoutResponse = $this->withHeader('Authorization', "Bearer $token")

        ->postJson('/api/v1/logout');

    $logoutResponse->assertStatus(200);

    Auth::guard('api')->forgetUser();

    $protectedResponse = $this->withHeader('Authorization', "Bearer $token")

        ->getJson('/api/v1/me');

    $protectedResponse->assertStatus(401);
});