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

it('allows an admin to view all memberships', function () {

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

    $response = $this->getJson('/api/v1/memberships');

    $response->assertOk()
        ->assertJsonCount(2, 'data');
});

it('allows a member to view all memberships', function () {

    $admin = User::factory()->create();
    $member = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    Membership::create([
        'user_id' => $admin->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    Membership::create([
        'user_id' => $member->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'member',
    ]);

    Passport::actingAs($member);

    $response = $this->getJson('/api/v1/memberships');

    $response->assertOk()
        ->assertJsonCount(2, 'data');
});

it('forbids unauthenticated users from viewing memberships', function () {

    $response = $this->getJson('/api/v1/memberships');

    $response->assertUnauthorized();
});

it('allows an admin to view a membership', function () {

    $admin = User::factory()->create();
    $member = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    Membership::create([
        'user_id' => $admin->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    $membership = Membership::create([
        'user_id' => $member->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'member',
    ]);

    Passport::actingAs($admin);

    $response = $this->getJson("/api/v1/memberships/{$membership->id}");

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'id',
                'role',
                'member_type',
                'instrument',
                'section',
                'user' => [
                    'id',
                    'email',
                ],
                'orchestra' => [
                    'id',
                    'name',
                ],
            ],
        ]);
});

it('allows a member of the orchestra to view a membership', function () {

    $admin = User::factory()->create();
    $member = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    Membership::create([
        'user_id' => $admin->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    $membership = Membership::create([
        'user_id' => $member->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'member',
    ]);

    Passport::actingAs($member);

    $response = $this->getJson("/api/v1/memberships/{$membership->id}");

    $response->assertOk();
});

it('forbids a user without membership from viewing a membership', function () {

    $admin = User::factory()->create();
    $outsider = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    $membership = Membership::create([
        'user_id' => $admin->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    Passport::actingAs($outsider);

    $response = $this->getJson("/api/v1/memberships/{$membership->id}");

    $response->assertForbidden();
});

it('returns 404 when membership does not exist', function () {

    $user = User::factory()->create();
    Passport::actingAs($user);

    $response = $this->getJson("/api/v1/memberships/999999");

    $response->assertNotFound();
});

it('allows an admin to update a membership', function () {

    $admin = User::factory()->create();
    $member = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    Membership::create([
        'user_id' => $admin->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    $membership = Membership::create([
        'user_id' => $member->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'member',
    ]);

    Passport::actingAs($admin);

    $response = $this->putJson("/api/v1/memberships/{$membership->id}", [
        'role' => 'admin',
        'member_type' => 'core',
        'instrument' => 'Violin',
        'section' => 'violin_1',
    ]);

    $response->assertOk();

    $this->assertDatabaseHas('memberships', [
        'id' => $membership->id,
        'role' => 'admin',
        'member_type' => 'core',
        'instrument' => 'Violin',
        'section' => 'violin_1',
    ]);
});

it('forbids a non admin from updating a membership', function () {

    $user = User::factory()->create();
    $target = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    Membership::create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'member',
    ]);

    $membership = Membership::create([
        'user_id' => $target->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'member',
    ]);

    Passport::actingAs($user);

    $response = $this->putJson("/api/v1/memberships/{$membership->id}", [
        'role' => 'admin',
    ]);

    $response->assertForbidden();
});

it('forbids users without membership from updating memberships', function () {

    $user = User::factory()->create();
    $target = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    $membership = Membership::create([
        'user_id' => $target->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'member',
    ]);

    Passport::actingAs($user);

    $response = $this->putJson("/api/v1/memberships/{$membership->id}", [
        'role' => 'admin',
    ]);

    $response->assertForbidden();
});

it('validates membership update data', function () {

    $admin = User::factory()->create();
    $user = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    Membership::create([
        'user_id' => $admin->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    $membership = Membership::create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'member',
    ]);

    Passport::actingAs($admin);

    $response = $this->putJson("/api/v1/memberships/{$membership->id}", [
        'role' => 'super-admin',
    ]);

    $response->assertUnprocessable();
});