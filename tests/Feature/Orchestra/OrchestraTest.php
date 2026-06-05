<?php

use App\Models\User;
use App\Models\Membership;
use Laravel\Passport\Passport;

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