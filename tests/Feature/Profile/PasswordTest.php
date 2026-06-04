<?php

use App\Models\User;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Hash;

it('allows authenticated user to change password', function () {

    $user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);

    Passport::actingAs($user);

    $response = $this->putJson('/api/v1/me/password', [
        'current_password' => 'old-password',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ]);

    $response->assertStatus(200);

    $this->assertTrue(
        Hash::check('new-password', $user->fresh()->password)
    );
});

it('fails when current password is incorrect', function () {

    $user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);

    Passport::actingAs($user);

    $response = $this->putJson('/api/v1/me/password', [
        'current_password' => 'wrong-password',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ]);

    $response->assertStatus(422);

    $this->assertTrue(
        Hash::check('old-password', $user->fresh()->password)
    );
});

it('fails when password confirmation does not match', function () {

    $user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);

    Passport::actingAs($user);

    $response = $this->putJson('/api/v1/me/password', [
        'current_password' => 'old-password',
        'password' => 'new-password',
        'password_confirmation' => 'different-password',
    ]);

    $response->assertStatus(422);

    $this->assertTrue(
        Hash::check('old-password', $user->fresh()->password)
    );
});