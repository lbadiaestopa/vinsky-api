<?php

use App\Models\User;
use App\Models\Membership;
use Laravel\Passport\Passport;
use App\Models\Orchestra;

it('allows an admin to create an orchestra', function () {

    $admin = User::factory()->create();

    Membership::create([
        'user_id' => $admin->id,
        'orchestra_id' => null,
        'role' => 'admin',
    ]);

    Passport::actingAs($admin);

    $response = $this->postJson('/api/v1/orchestras', [
        'name' => 'Symphony Orchestra',
        'location' => 'Barcelona',
    ]);

    $response->assertCreated();

    $this->assertDatabaseHas('orchestras', [
        'name' => 'Symphony Orchestra',
        'location' => 'Barcelona',
    ]);

    $this->assertDatabaseHas('memberships', [
        'user_id' => $admin->id,
        'orchestra_id' => null,
        'role' => 'admin',
    ]);
});

it('does not allow a member to create an orchestra', function () {

    $member = User::factory()->create();

    Membership::create([
        'user_id' => $member->id,
        'orchestra_id' => null,
        'role' => 'member',
    ]);

    Passport::actingAs($member);

    $response = $this->postJson('/api/v1/orchestras', [
        'name' => 'Symphony Orchestra',
        'location' => 'Barcelona',
    ]);

    $response->assertForbidden();

    $this->assertDatabaseMissing('orchestras', [
        'name' => 'Symphony Orchestra',
    ]);
});

it('validates required fields when creating an orchestra', function () {

    $admin = User::factory()->create();

    Membership::create([
        'user_id' => $admin->id,
        'orchestra_id' => null,
        'role' => 'admin',
    ]);

    Passport::actingAs($admin);

    $response = $this->postJson('/api/v1/orchestras', []);

    $response->assertUnprocessable();

    $response->assertJsonValidationErrors([
        'name',
        'location',
    ]);
});

it('returns only the user orchestras', function () {

    $user = User::factory()->create();

    $orchestra1 = Orchestra::factory()->create([
        'name' => 'Orchestra One',
    ]);

    $orchestra2 = Orchestra::factory()->create([
        'name' => 'Orchestra Two',
    ]);

    Membership::factory()->create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra1->id,
        'role' => 'member',
    ]);

    Membership::factory()->create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra2->id,
        'role' => 'admin',
    ]);

    Passport::actingAs($user);

    $response = $this->getJson('/api/v1/orchestras');

    $response->assertOk();

    $response->assertJsonCount(2, 'data');

    $response->assertJsonFragment([
        'id' => $orchestra1->id,
        'name' => 'Orchestra One',
    ]);

    $response->assertJsonFragment([
        'id' => $orchestra2->id,
        'name' => 'Orchestra Two',
    ]);
});

it('does not return orchestras from other users', function () {

    $user = User::factory()->create();

    $otherUser = User::factory()->create();

    $myOrchestra = Orchestra::factory()->create([
        'name' => 'My Orchestra',
    ]);

    $otherOrchestra = Orchestra::factory()->create([
        'name' => 'Other Orchestra',
    ]);

    Membership::factory()->create([
        'user_id' => $user->id,
        'orchestra_id' => $myOrchestra->id,
    ]);

    Membership::factory()->create([
        'user_id' => $otherUser->id,
        'orchestra_id' => $otherOrchestra->id,
    ]);

    Passport::actingAs($user);

    $response = $this->getJson('/api/v1/orchestras');

    $response->assertOk();

    $response->assertJsonFragment([
        'id' => $myOrchestra->id,
    ]);

    $response->assertJsonMissing([
        'id' => $otherOrchestra->id,
    ]);
});

it('returns forbidden when user has no orchestra memberships', function () {

    $user = User::factory()->create();

    Membership::factory()->adminWithoutOrchestra()->create([
        'user_id' => $user->id,
    ]);

    Passport::actingAs($user);

    $response = $this->getJson('/api/v1/orchestras');

    $response->assertForbidden();
});

it('requires authentication', function () {

    $response = $this->getJson('/api/v1/orchestras');

    $response->assertUnauthorized();
});

it('allows a user to view their orchestra', function () {

    $user = User::factory()->create();
    Passport::actingAs($user);

    $orchestra = Orchestra::factory()->create();

    Membership::factory()->create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'member',
    ]);

    $response = $this->getJson("/api/v1/orchestras/{$orchestra->id}");

    $response->assertOk();

    $response->assertJsonFragment([
        'id' => $orchestra->id,
        'name' => $orchestra->name,
        'location' => $orchestra->location,
    ]);
});

it('allows an admin to view their orchestra', function () {

    $user = User::factory()->create();
    Passport::actingAs($user);

    $orchestra = Orchestra::factory()->create();

    Membership::factory()->create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    $response = $this->getJson("/api/v1/orchestras/{$orchestra->id}");

    $response->assertOk();
});

it('forbids access to an orchestra without membership', function () {

    $user = User::factory()->create();
    Passport::actingAs($user);

    $orchestra = Orchestra::factory()->create();

    $response = $this->getJson("/api/v1/orchestras/{$orchestra->id}");

    $response->assertForbidden();
});

it('forbids access to another user orchestra', function () {

    $user = User::factory()->create();
    Passport::actingAs($user);

    $orchestra = Orchestra::factory()->create();

    $otherUser = User::factory()->create();

    Membership::factory()->create([
        'user_id' => $otherUser->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'member',
    ]);

    $response = $this->getJson("/api/v1/orchestras/{$orchestra->id}");

    $response->assertForbidden();
});

it('does not allow creator without membership in orchestra', function () {

    $user = User::factory()->create();

    Membership::factory()->create([
        'user_id' => $user->id,
        'orchestra_id' => null,
        'role' => 'admin',
    ]);

    Passport::actingAs($user);

    $orchestra = Orchestra::factory()->create();

    $response = $this->getJson("/api/v1/orchestras/{$orchestra->id}");

    $response->assertForbidden();
});