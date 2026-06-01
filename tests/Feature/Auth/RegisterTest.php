<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('registers a user successfully', function () {

    $response = $this->postJson('/api/v1/register', [
        'name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'password' => 'prova123',
        'password_confirmation' => 'prova123',
    ]);

    $response->assertStatus(201);

    $response->assertJsonStructure([
        'user' => [
            'id',
            'name',
            'email',
        ],
    ]);

    $this->assertDatabaseHas('users', [
        'email' => 'john@example.com',
    ]);

    $user = User::where('email', 'john@example.com')->first();

    expect($user)->not->toBeNull();
});