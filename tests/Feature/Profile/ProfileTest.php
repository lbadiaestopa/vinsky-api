<?php

use App\Models\User;
use App\Models\Orchestra;
use App\Models\Membership;
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

it('deletes the authenticated user account', function () {

    $user = User::factory()->create();

    Passport::actingAs($user);

    $response = $this->deleteJson('/api/v1/me');

    $response->assertNoContent();

    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);
});

it('does not allow unauthenticated users to delete their account', function () {

    $user = User::factory()->create();

    $response = $this->deleteJson('/api/v1/me');

    $response->assertUnauthorized();

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
    ]);
});

it('invalidates user tokens after account deletion', function () {

    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => bcrypt('password'),
    ]);

    $loginResponse = $this->postJson('/api/v1/login', [
        'email' => 'john@example.com',
        'password' => 'password',
    ]);

    $token = $loginResponse->json('token');

    $this->withHeader('Authorization', "Bearer $token")
        ->deleteJson('/api/v1/me')
        ->assertNoContent();

    $this->app->get('auth')->forgetGuards();
    $this->app->get('auth')->shouldUse('api');

    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);

    $meResponse = $this->withHeader('Authorization', "Bearer $token")
        ->getJson('/api/v1/me');

    $meResponse->assertUnauthorized();
});

it('deletes the user and all their memberships if they are not an admin', function () {
    $user = User::factory()->create();

    $orchestra1 = Orchestra::factory()->create();
    $orchestra2 = Orchestra::factory()->create();

    $membership1 = Membership::create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra1->id,
        'role' => 'member',
    ]);

    $membership2 = Membership::create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra2->id,
        'role' => 'member',
    ]);

    Passport::actingAs($user);

    $response = $this->deleteJson("/api/v1/me");

    $response->assertNoContent();

    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);

    $this->assertDatabaseMissing('memberships', [
        'id' => $membership1->id,
    ]);

    $this->assertDatabaseMissing('memberships', [
        'id' => $membership2->id,
    ]);
});

it('does not allow an admin to delete their account', function () {
    $user = User::factory()->create();

    $orchestra = Orchestra::factory()->create();

    $membership = Membership::create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    Passport::actingAs($user);

    $response = $this->deleteJson("/api/v1/me");

    $response->assertStatus(409);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
    ]);

    $this->assertDatabaseHas('memberships', [
        'id' => $membership->id,
    ]);
});
