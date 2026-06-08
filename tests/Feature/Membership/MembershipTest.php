<?php

use App\Models\User;
use App\Models\Orchestra;
use App\Models\Membership;
use Laravel\Passport\Passport;

it('allows an admin to create a membership', function () {

    $admin = User::factory()->create();
    $user = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    Membership::create([
        'user_id' => $admin->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    Passport::actingAs($admin);

    $response = $this->postJson('/api/v1/memberships', [
        'email' => $user->email,
        'orchestra_name' => $orchestra->name,
        'role' => 'member',
    ]);

    $response->assertCreated();

    $this->assertDatabaseHas('memberships', [
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'member',
    ]);
});

it('forbids a non admin from creating memberships', function () {

    $user = User::factory()->create();
    $target = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    Membership::create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'member',
    ]);

    Passport::actingAs($user);

    $response = $this->postJson('/api/v1/memberships', [
        'email' => $target->email,
        'orchestra_name' => $orchestra->name,
        'role' => 'member',
    ]);

    $response->assertForbidden();
});

it('validates required fields when creating membership', function () {

    $user = User::factory()->create();

    Passport::actingAs($user);

    $response = $this->postJson('/api/v1/memberships', []);

    $response->assertUnprocessable();
});

it('prevents duplicate membership', function () {

    $admin = User::factory()->create();
    $user = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    Membership::create([
        'user_id' => $admin->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    Membership::create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'member',
    ]);

    Passport::actingAs($admin);

    $response = $this->postJson('/api/v1/memberships', [
        'email' => $user->email,
        'orchestra_name' => $orchestra->name,
        'role' => 'member',
    ]);

    $response->assertConflict();
});