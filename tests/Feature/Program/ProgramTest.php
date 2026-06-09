<?php

use App\Models\User;
use App\Models\Orchestra;
use App\Models\Membership;
use App\Models\Program;
use Laravel\Passport\Passport;

it('allows an admin to create a program', function () {

    $admin = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    Membership::create([
        'user_id' => $admin->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    Passport::actingAs($admin);

    $response = $this->postJson("/api/v1/orchestras/{$orchestra->id}/programs", [
        'name' => 'Season 2026',
        'start_date' => '2026-01-01',
        'end_date' => '2026-06-30',
        'orchestra_id' => $orchestra->id,
    ]);

    $response->assertCreated();

    $this->assertDatabaseHas('programs', [
        'name' => 'Season 2026',
        'orchestra_id' => $orchestra->id,
    ]);
});

it('forbids a member from creating a program', function () {

    $member = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    Membership::create([
        'user_id' => $member->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'member',
    ]);

    Passport::actingAs($member);

    $response = $this->postJson("/api/v1/orchestras/{$orchestra->id}/programs", [
        'name' => 'Season 2026',
        'start_date' => '2026-01-01',
        'end_date' => '2026-06-30',
        'orchestra_id' => $orchestra->id,
    ]);

    $response->assertForbidden();
});

it('forbids users without membership from creating a program', function () {

    $user = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    Passport::actingAs($user);

    $response = $this->postJson("/api/v1/orchestras/{$orchestra->id}/programs", [
        'name' => 'Season 2026',
        'start_date' => '2026-01-01',
        'end_date' => '2026-06-30',
        'orchestra_id' => $orchestra->id,
    ]);

    $response->assertForbidden();
});

it('validates required fields when creating a program', function () {

    $admin = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    Membership::create([
        'user_id' => $admin->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    Passport::actingAs($admin);

    $response = $this->postJson("/api/v1/orchestras/{$orchestra->id}/programs", []);

    $response->assertUnprocessable();

    $response->assertJsonValidationErrors([
        'name',
        'start_date',
        'end_date',
    ]);
});

it('returns 404 when orchestra does not exist', function () {

    $admin = User::factory()->create();

    Passport::actingAs($admin);

    $invalidId = 999999;

    $response = $this->postJson("/api/v1/orchestras/{$invalidId}/programs", [
        'name' => 'Season 2026',
        'start_date' => '2026-01-01',
        'end_date' => '2026-06-30',
    ]);

    $response->assertNotFound();
});

it('validates that end date is after start date', function () {

    $admin = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    Membership::create([
        'user_id' => $admin->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    Passport::actingAs($admin);

    $response = $this->postJson("/api/v1/orchestras/{$orchestra->id}/programs", [
        'name' => 'Season 2026',
        'start_date' => '2026-06-30',
        'end_date' => '2026-01-01',
        'orchestra_id' => $orchestra->id,
    ]);

    $response->assertUnprocessable();

    $response->assertJsonValidationErrors([
        'end_date',
    ]);
});

it('forbids unauthenticated users from creating programs', function () {

    $orchestra = Orchestra::factory()->create();

    $response = $this->postJson("/api/v1/orchestras/{$orchestra->id}/programs", [
        'name' => 'Season 2026',
        'start_date' => '2026-01-01',
        'end_date' => '2026-06-30',
        'orchestra_id' => $orchestra->id,
    ]);

    $response->assertUnauthorized();
});

it('allows a member to list orchestra programs', function () {

    $user = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    Membership::create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'member',
    ]);

    Program::factory()->count(3)->create([
        'orchestra_id' => $orchestra->id,
    ]);

    Passport::actingAs($user);

    $response = $this->getJson("/api/v1/orchestras/{$orchestra->id}/programs");

    $response->assertOk();

    $response->assertJsonCount(3, 'data');
});

it('allows an admin to list orchestra programs', function () {

    $user = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    Membership::create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    Program::factory()->count(2)->create([
        'orchestra_id' => $orchestra->id,
    ]);

    Passport::actingAs($user);

    $response = $this->getJson("/api/v1/orchestras/{$orchestra->id}/programs");

    $response->assertOk();

    $response->assertJsonCount(2, 'data');
});

it('forbids users without membership from listing programs', function () {

    $user = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    Program::factory()->count(2)->create([
        'orchestra_id' => $orchestra->id,
    ]);

    Passport::actingAs($user);

    $response = $this->getJson("/api/v1/orchestras/{$orchestra->id}/programs");

    $response->assertForbidden();
});

it('returns 404 when trying to list programs of a non existing orchestra', function () {

    $user = User::factory()->create();

    Passport::actingAs($user);

    $response = $this->getJson("/api/v1/orchestras/999999/programs");

    $response->assertNotFound();
});
