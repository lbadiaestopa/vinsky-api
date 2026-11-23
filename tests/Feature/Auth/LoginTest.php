<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('logs in a verified user successfully', function () {

    User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make('test-password'),
        'email_verified_at' => now(),
    ]);

    $response = $this->postJson('/api/v1/login', [
        'email' => 'john@example.com',
        'password' => 'test-password',
    ]);

    $response->assertStatus(200);

    $response->assertJsonStructure([
        'user' => [
            'id',
            'name',
            'last_name',
            'email',
        ],
        'token',
    ]);

    expect($response->json('token'))->not->toBeEmpty();
});

it('fails login with incorrect password', function () {

    User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make('correct-password'),
    ]);

    $response = $this->postJson('/api/v1/login', [
        'email' => 'john@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(401);

    $response->assertJson([
        'message' => 'Invalid credentials',
    ]);
});

it('fails login with non existing email', function () {

    $response = $this->postJson('/api/v1/login', [
        'email' => 'nonexistent@example.com',
        'password' => 'random-password',
    ]);

    $response->assertStatus(401);

    $response->assertJson([
        'message' => 'Invalid credentials',
    ]);
});

it('prevents unverified users from logging in', function () {

    $user = User::factory()->unverified()->create([
        'password' => Hash::make('test-password'),
    ]);

    $response = $this->postJson('/api/v1/login', [
        'email' => $user->email,
        'password' => 'test-password',
    ]);

    $response->assertStatus(403);

    $response->assertJson([
        'message' => 'Please verify your email address before logging in.',
    ]);

    $response->assertJsonMissingPath('token');
});

it('prevents unverified users with valid tokens from accessing protected routes', function () {

    $user = User::factory()->unverified()->create();

    $token = $user->createToken('Test Token')->accessToken;

    $response = $this->withToken($token)->getJson('/api/v1/me');

    $response->assertStatus(403);
});