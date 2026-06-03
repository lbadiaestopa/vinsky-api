<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('registers a user successfully', function () {

    $response = $this->postJson('/api/v1/register', [
        'name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(201);

    $response->assertJsonStructure([
        'user' => [
            'id',
            'name',
            'email',
        ],
        'token',
    ]);

    $this->assertDatabaseHas('users', [
        'email' => 'john@example.com',
    ]);

    $user = User::where('email', 'john@example.com')->first();

    expect($user)->not->toBeNull();

    $response->assertJson([
        'user' => [
            'email' => 'john@example.com',
        ],
    ]);

    $this->assertNotEmpty($response->json('token'));
});

it('fails when email already exists', function () {

    User::factory()->create([
        'email' => 'test@example.com'
    ]);

    $response = $this->postJson('/api/v1/register', [
        'name' => 'John',
        'last_name' => 'Doe',
        'email' => 'test@example.com',
        'password' => 'prova123',
        'password_confirmation' => 'prova123',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email']);
});

it('fails when password confirmation does not match', function () {

    $response = $this->postJson('/api/v1/register', [
        'name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'password' => 'correct-password',
        'password_confirmation' => 'incorrect-password',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['password']);
});

it('fails when required fields are missing', function () {

    $response = $this->postJson('/api/v1/register', [
        'name' => 'John',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([
        'email',
        'last_name',
        'password',
    ]);
});

it('fails when email format is invalid', function () {

    $response = $this->postJson('/api/v1/register', [
        'name' => 'John',
        'last_name' => 'Doe',
        'email' => 'invalid-email-format',
        'password' => 'prova123',
        'password_confirmation' => 'prova123',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email']);
});
