<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('logs in a user successfully', function () {

    User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make('test-password')
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
