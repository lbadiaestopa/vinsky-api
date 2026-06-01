<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('logs in a user successfully', function () {

    $user = User::create([
        'name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'password' => Hash::make('prova123'),
    ]);

    $response = $this->postJson('/api/v1/login', [
        'email' => 'john@example.com',
        'password' => 'prova123',
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