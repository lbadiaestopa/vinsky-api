<?php

use App\Models\Event;
use App\Models\User;
use App\Models\Program;
use App\Models\Orchestra;
use App\Models\Membership;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function validEventPayload(array $overrides = []): array
{
    return array_merge([
        'repertoire' => 'Beethoven Symphony No. 5',
        'type' => 'rehearsal',
        'location' => 'Main Hall',
        'start_date' => '2026-03-01 19:00:00',
        'end_date' => '2026-03-01 22:00:00',
    ], $overrides);
}

it('allows an admin to create an event', function () {
    $user = User::factory()->create();

    $orchestra = Orchestra::factory()->create();

    $program = Program::factory()->create([
        'orchestra_id' => $orchestra->id,
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
    ]);

    Membership::factory()->create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    $response = $this
        ->actingAs($user, 'api')
        ->postJson(
            "/api/v1/programs/{$program->id}/events",
            validEventPayload()
        );

    $response->assertCreated();

    $this->assertDatabaseHas('events', [
        'program_id' => $program->id,
        'repertoire' => 'Beethoven Symphony No. 5',
        'type' => 'rehearsal',
        'location' => 'Main Hall',
    ]);
});

it('requires authentication', function () {
    $program = Program::factory()->create([
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
    ]);

    $response = $this->postJson(
        "/api/v1/programs/{$program->id}/events",
        validEventPayload()
    );

    $response->assertUnauthorized();
});

it('forbids a non admin from creating an event', function () {
    $user = User::factory()->create();

    $orchestra = Orchestra::factory()->create();

    $program = Program::factory()->create([
        'orchestra_id' => $orchestra->id,
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
    ]);

    Membership::factory()->create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'member',
    ]);

    $response = $this
        ->actingAs($user, 'api')
        ->postJson(
            "/api/v1/programs/{$program->id}/events",
            validEventPayload()
        );

    $response->assertForbidden();

    $this->assertDatabaseCount('events', 0);
});

it('returns 404 when program does not exist', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user, 'api')
        ->postJson(
            '/api/v1/programs/999999/events',
            validEventPayload()
        );

    $response->assertNotFound();
});

it('validates event type', function () {
    $user = User::factory()->create();

    $orchestra = Orchestra::factory()->create();

    $program = Program::factory()->create([
        'orchestra_id' => $orchestra->id,
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
    ]);

    Membership::factory()->create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    $response = $this
        ->actingAs($user, 'api')
        ->postJson(
            "/api/v1/programs/{$program->id}/events",
            validEventPayload([
                'type' => 'pizza',
            ])
        );

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors('type');
});

it('validates that end_date is after start_date', function () {
    $user = User::factory()->create();

    $orchestra = Orchestra::factory()->create();

    $program = Program::factory()->create([
        'orchestra_id' => $orchestra->id,
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
    ]);

    Membership::factory()->create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    $response = $this
        ->actingAs($user, 'api')
        ->postJson(
            "/api/v1/programs/{$program->id}/events",
            validEventPayload([
                'start_date' => '2026-03-01 22:00:00',
                'end_date' => '2026-03-01 19:00:00',
            ])
        );

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors('end_date');
});

it('does not allow events before the program period', function () {
    $user = User::factory()->create();

    $orchestra = Orchestra::factory()->create();

    $program = Program::factory()->create([
        'orchestra_id' => $orchestra->id,
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
    ]);

    Membership::factory()->create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    $response = $this
        ->actingAs($user, 'api')
        ->postJson(
            "/api/v1/programs/{$program->id}/events",
            validEventPayload([
                'start_date' => '2025-12-20 19:00:00',
                'end_date' => '2025-12-20 22:00:00',
            ])
        );

    $response
        ->assertUnprocessable();
});

it('does not allow events after the program period', function () {
    $user = User::factory()->create();

    $orchestra = Orchestra::factory()->create();

    $program = Program::factory()->create([
        'orchestra_id' => $orchestra->id,
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
    ]);

    Membership::factory()->create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    $response = $this
        ->actingAs($user, 'api')
        ->postJson(
            "/api/v1/programs/{$program->id}/events",
            validEventPayload([
                'start_date' => '2027-01-10 19:00:00',
                'end_date' => '2027-01-10 22:00:00',
            ])
        );

    $response
        ->assertUnprocessable();
});

it('requires the entire event to be inside the program period', function () {
    $user = User::factory()->create();

    $orchestra = Orchestra::factory()->create();

    $program = Program::factory()->create([
        'orchestra_id' => $orchestra->id,
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
    ]);

    Membership::factory()->create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    $response = $this
        ->actingAs($user, 'api')
        ->postJson(
            "/api/v1/programs/{$program->id}/events",
            validEventPayload([
                'start_date' => '2026-12-15 19:00:00',
                'end_date' => '2027-01-15 22:00:00',
            ])
        );

    $response->assertUnprocessable();
});