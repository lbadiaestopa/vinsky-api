<?php

use App\Models\User;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Hash;

it('updates the authenticated user profile', function () {

    $user = User::factory()->create([
        'name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
    ]);

    Passport::actingAs($user);

    $response = $this->putJson('/api/v1/me', [
        'name' => 'Updated John',
        'last_name' => 'Updated Doe',
        'email' => 'updated@example.com',
    ]);

    $response->assertStatus(200);

    $response->assertJsonFragment([
        'name' => 'Updated John',
        'last_name' => 'Updated Doe',
        'email' => 'updated@example.com',
    ]);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated John',
        'last_name' => 'Updated Doe',
        'email' => 'updated@example.com',
    ]);
});

it('does not allow unauthenticated users to update profile', function () {

    $response = $this->putJson('/api/v1/me', [
        'name' => 'Updated John',
        'last_name' => 'Updated Doe',
        'email' => 'updated@example.com',
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'message' => 'Unauthenticated.',
        ]);
});

it('does not allow updating profile with duplicate email', function () {

    $user = User::factory()->create([
        'name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
    ]);

    $otherUser = User::factory()->create([
        'email' => 'other@example.com',
    ]);

    Passport::actingAs($user);

    $response = $this->putJson('/api/v1/me', [
        'name' => 'John Updated',
        'last_name' => 'Doe Updated',
        'email' => 'other@example.com',
    ]);

    $response->assertStatus(422);

    $response->assertJsonValidationErrors(['email']);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'email' => 'john@example.com',
    ]);
});

it('allows updating profile without changing email', function () {

    $user = User::factory()->create([
        'name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
    ]);

    Passport::actingAs($user);

    $response = $this->putJson('/api/v1/me', [
        'name' => 'John Updated',
        'last_name' => 'Doe Updated',
        'email' => 'john@example.com',
    ]);

    $response->assertStatus(200);

    $response->assertJsonFragment([
        'name' => 'John Updated',
        'last_name' => 'Doe Updated',
        'email' => 'john@example.com',
    ]);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'John Updated',
        'last_name' => 'Doe Updated',
        'email' => 'john@example.com',
    ]);
});

it('does not allow updating password from profile endpoint', function () {

    $user = User::factory()->create([
        'name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'password' => Hash::make('old-password'),
    ]);

    Passport::actingAs($user);

    $response = $this->putJson('/api/v1/me', [
        'name' => 'John Updated',
        'last_name' => 'Doe Updated',
        'email' => 'john@example.com',
        'password' => 'new-password',
    ]);

    $response->assertStatus(200);

    $response->assertJsonFragment([
        'name' => 'John Updated',
    ]);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'John Updated',
        'last_name' => 'Doe Updated',
    ]);

    $this->assertTrue(
        Hash::check('old-password', $user->fresh()->password)
    );
});