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

it('allows a member of the orchestra to view program events', function () {
    $user = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    $program = Program::factory()->create([
        'orchestra_id' => $orchestra->id,
    ]);

    Membership::factory()->create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
    ]);

    Event::factory()->count(3)->create([
        'program_id' => $program->id,
    ]);

    $response = $this
        ->actingAs($user, 'api')
        ->getJson("/api/v1/programs/{$program->id}/events");

    $response->assertOk();
    $response->assertJsonCount(3, 'data');
});

it('returns events ordered by start_date', function () {
    $user = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    $program = Program::factory()->create([
        'orchestra_id' => $orchestra->id,
    ]);

    Membership::factory()->create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
    ]);

    $event1 = Event::factory()->create([
        'program_id' => $program->id,
        'start_date' => now()->addDays(3),
    ]);

    $event2 = Event::factory()->create([
        'program_id' => $program->id,
        'start_date' => now()->addDay(),
    ]);

    $response = $this
        ->actingAs($user, 'api')
        ->getJson("/api/v1/programs/{$program->id}/events");

    $response->assertOk();

    $ids = collect($response->json('data'))->pluck('id')->toArray();

    expect($ids)->toBe([$event2->id, $event1->id]);
});

it('returns correct event structure', function () {
    $user = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    $program = Program::factory()->create([
        'orchestra_id' => $orchestra->id,
    ]);

    Membership::factory()->create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
    ]);

    $event = Event::factory()->create([
        'program_id' => $program->id,
        'repertoire' => 'Beethoven 5',
        'type' => 'concert',
        'location' => 'Auditorium',
    ]);

    $response = $this
        ->actingAs($user, 'api')
        ->getJson("/api/v1/programs/{$program->id}/events");

    $response->assertOk();

    $response->assertJsonFragment([
        'id' => $event->id,
        'repertoire' => 'Beethoven 5',
        'type' => 'concert',
        'location' => 'Auditorium',
    ]);
});

it('returns 403 if user is not member of orchestra', function () {
    $user = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    $program = Program::factory()->create([
        'orchestra_id' => $orchestra->id,
    ]);

    Event::factory()->create([
        'program_id' => $program->id,
    ]);

    $response = $this
        ->actingAs($user, 'api')
        ->getJson("/api/v1/programs/{$program->id}/events");

    $response->assertForbidden();
});

it('returns 404 when program does not exist while showing all events', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user, 'api')
        ->getJson("/api/v1/programs/999999/events");

    $response->assertNotFound();
});

it('requires authentication while showing all events', function () {
    $program = Program::factory()->create();

    $response = $this
        ->getJson("/api/v1/programs/{$program->id}/events");

    $response->assertUnauthorized();
});

it('allows a member of the orchestra to view a program event', function () {
    $user = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    $program = Program::factory()->create([
        'orchestra_id' => $orchestra->id,
    ]);

    Membership::factory()->create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
    ]);

    $event = Event::factory()->create([
        'program_id' => $program->id,
        'repertoire' => 'Beethoven 5',
    ]);

    $response = $this
        ->actingAs($user, 'api')
        ->getJson("/api/v1/programs/{$program->id}/events/{$event->id}");

    $response->assertOk();

    $response->assertJsonFragment([
        'id' => $event->id,
        'repertoire' => 'Beethoven 5',
    ]);
});

it('returns 403 if user is not member of orchestra while showing event', function () {
    $user = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    $program = Program::factory()->create([
        'orchestra_id' => $orchestra->id,
    ]);

    $event = Event::factory()->create([
        'program_id' => $program->id,
    ]);

    $response = $this
        ->actingAs($user, 'api')
        ->getJson("/api/v1/programs/{$program->id}/events/{$event->id}");

    $response->assertForbidden();
});

it('returns 404 when program does not exist while showing event', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user, 'api')
        ->getJson("/api/v1/programs/999999/events/1");

    $response->assertNotFound();
});

it('returns 404 when event does not exist in program', function () {
    $user = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    $program = Program::factory()->create([
        'orchestra_id' => $orchestra->id,
    ]);

    Membership::factory()->create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
    ]);

    $response = $this
        ->actingAs($user, 'api')
        ->getJson("/api/v1/programs/{$program->id}/events/999999");

    $response->assertNotFound();
});

it('requires authentication while showing event', function () {
    $orchestra = Orchestra::factory()->create();

    $program = Program::factory()->create([
        'orchestra_id' => $orchestra->id,
    ]);

    $event = Event::factory()->create([
        'program_id' => $program->id,
    ]);

    $response = $this
        ->getJson("/api/v1/programs/{$program->id}/events/{$event->id}");

    $response->assertUnauthorized();
});

it('allows an admin to update an event', function () {
    $user = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    Membership::factory()->create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    $program = Program::factory()->create([
        'orchestra_id' => $orchestra->id,
    ]);

    $event = Event::factory()->create([
        'program_id' => $program->id,
    ]);

    $response = $this
        ->actingAs($user, 'api')
        ->putJson("/api/v1/programs/{$program->id}/events/{$event->id}", [
            'repertoire' => 'New repertoire',
            'type' => 'concert',
            'location' => 'New Auditorium',
            'start_date' => $program->start_date,
            'end_date' => $program->end_date,
        ]);

    $response->assertOk();

    $this->assertDatabaseHas('events', [
        'id' => $event->id,
        'repertoire' => 'New repertoire',
        'type' => 'concert',
        'location' => 'New Auditorium',
    ]);
});

it('requires authentication while updating event', function () {
    $program = Program::factory()->create();
    $event = Event::factory()->create([
        'program_id' => $program->id,
    ]);

    $response = $this->putJson(
        "/api/v1/programs/{$program->id}/events/{$event->id}",
        []
    );

    $response->assertUnauthorized();
});

it('forbids a non admin from updating an event', function () {
    $user = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    Membership::factory()->create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'member',
    ]);

    $program = Program::factory()->create([
        'orchestra_id' => $orchestra->id,
    ]);

    $event = Event::factory()->create([
        'program_id' => $program->id,
    ]);

    $response = $this
        ->actingAs($user, 'api')
        ->putJson("/api/v1/programs/{$program->id}/events/{$event->id}", [
            'repertoire' => 'Updated',
            'type' => 'concert',
            'location' => 'Updated',
            'start_date' => $program->start_date,
            'end_date' => $program->end_date,
        ]);

    $response->assertForbidden();
});

it('returns 404 when program does not exist while updating event', function () {
    $user = User::factory()->create();

    $event = Event::factory()->create();

    $response = $this
        ->actingAs($user, 'api')
        ->putJson("/api/v1/programs/999999/events/{$event->id}", []);

    $response->assertNotFound();
});

it('returns 404 when event does not exist while updating event', function () {
    $user = User::factory()->create();

    $program = Program::factory()->create();

    $response = $this
        ->actingAs($user, 'api')
        ->putJson("/api/v1/programs/{$program->id}/events/999999", []);

    $response->assertNotFound();
});

it('returns 404 when event does not belong to program', function () {
    $user = User::factory()->create();

    $programA = Program::factory()->create();
    $programB = Program::factory()->create();

    $event = Event::factory()->create([
        'program_id' => $programB->id,
    ]);

    $response = $this
        ->actingAs($user, 'api')
        ->putJson("/api/v1/programs/{$programA->id}/events/{$event->id}", []);

    $response->assertNotFound();
});

it('validates event type while updating event', function () {
    $user = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    Membership::factory()->create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    $program = Program::factory()->create([
        'orchestra_id' => $orchestra->id,
    ]);

    $event = Event::factory()->create([
        'program_id' => $program->id,
    ]);

    $response = $this
        ->actingAs($user, 'api')
        ->putJson("/api/v1/programs/{$program->id}/events/{$event->id}", [
            'repertoire' => 'Updated',
            'type' => 'invalid',
            'location' => 'Updated',
            'start_date' => $program->start_date,
            'end_date' => $program->end_date,
        ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('type');
});

it('validates that end_date is after start_date while updating event', function () {
    $user = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    Membership::factory()->create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    $program = Program::factory()->create([
        'orchestra_id' => $orchestra->id,
    ]);

    $event = Event::factory()->create([
        'program_id' => $program->id,
    ]);

    $response = $this
        ->actingAs($user, 'api')
        ->putJson("/api/v1/programs/{$program->id}/events/{$event->id}", [
            'repertoire' => 'Updated',
            'type' => 'concert',
            'location' => 'Updated',
            'start_date' => now(),
            'end_date' => now()->subDay(),
        ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('end_date');
});

it('does not allow events before the program period while updating', function () {
    $admin = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    Membership::factory()->create([
        'user_id' => $admin->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    $program = Program::factory()->create([
        'orchestra_id' => $orchestra->id,
        'start_date' => now(),
        'end_date' => now()->addMonth(),
    ]);

    $event = Event::factory()->create([
        'program_id' => $program->id,
    ]);

    $response = $this
        ->actingAs($admin, 'api')
        ->putJson("/api/v1/programs/{$program->id}/events/{$event->id}", [
            'repertoire' => 'Test',
            'type' => 'concert',
            'location' => 'Auditorium',
            'start_date' => now()->subDay()->toDateTimeString(),
            'end_date' => now()->addDay()->toDateTimeString(),
        ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('start_date');
});

it('does not allow events after the program period while updating', function () {
    $admin = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    Membership::factory()->create([
        'user_id' => $admin->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    $program = Program::factory()->create([
        'orchestra_id' => $orchestra->id,
        'start_date' => now(),
        'end_date' => now()->addMonth(),
    ]);

    $event = Event::factory()->create([
        'program_id' => $program->id,
    ]);

    $response = $this
        ->actingAs($admin, 'api')
        ->putJson("/api/v1/programs/{$program->id}/events/{$event->id}", [
            'repertoire' => 'Test',
            'type' => 'concert',
            'location' => 'Auditorium',
            'start_date' => now()->addDays(10)->toDateTimeString(),
            'end_date' => now()->addMonths(2)->toDateTimeString(),
        ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('end_date');
});

it('requires the entire event to be inside the program period while updating', function () {
    $admin = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    Membership::factory()->create([
        'user_id' => $admin->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    $program = Program::factory()->create([
        'orchestra_id' => $orchestra->id,
        'start_date' => now(),
        'end_date' => now()->addMonth(),
    ]);

    $event = Event::factory()->create([
        'program_id' => $program->id,
    ]);

    $response = $this
        ->actingAs($admin, 'api')
        ->putJson("/api/v1/programs/{$program->id}/events/{$event->id}", [
            'repertoire' => 'Test',
            'type' => 'concert',
            'location' => 'Auditorium',
            'start_date' => now()->subDay()->toDateTimeString(),
            'end_date' => now()->addMonths(2)->toDateTimeString(),
        ]);

    $response->assertUnprocessable();

    $response->assertJsonValidationErrors([
        'start_date',
        'end_date',
    ]);
});

it('returns updated event resource', function () {
    $user = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    Membership::factory()->create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    $program = Program::factory()->create([
        'orchestra_id' => $orchestra->id,
    ]);

    $event = Event::factory()->create([
        'program_id' => $program->id,
    ]);

    $response = $this
        ->actingAs($user, 'api')
        ->putJson("/api/v1/programs/{$program->id}/events/{$event->id}", [
            'repertoire' => 'New repertoire',
            'type' => 'concert',
            'location' => 'New Auditorium',
            'start_date' => $program->start_date,
            'end_date' => $program->end_date,
        ]);

    $response->assertOk();

    $response->assertJsonFragment([
        'id' => $event->id,
        'repertoire' => 'New repertoire',
        'type' => 'concert',
        'location' => 'New Auditorium',
    ]);
});