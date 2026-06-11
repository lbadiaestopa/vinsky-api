<?php

use App\Models\User;
use App\Models\Program;
use App\Models\Orchestra;
use App\Models\Membership;
use App\Models\Score;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('allows an admin to upload a score to a program', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    $program = Program::factory()->create([
        'orchestra_id' => $orchestra->id,
    ]);

    Membership::factory()->create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    $file = UploadedFile::fake()->create('score.pdf', 100, 'application/pdf');

    $response = $this
        ->actingAs($user, 'api')
        ->postJson("/api/v1/programs/{$program->id}/scores", [
            'title' => 'Beethoven Symphony 5',
            'file' => $file,
        ]);

    $response->assertCreated();

    $this->assertDatabaseHas('scores', [
        'program_id' => $program->id,
        'title' => 'Beethoven Symphony 5',
        'mime_type' => 'application/pdf',
    ]);

    Storage::disk('public')->assertExists(
        $response->json('data.file_path')
    );
});

it('forbids non-admin users from uploading scores', function () {
    $user = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    $program = Program::factory()->create([
        'orchestra_id' => $orchestra->id,
    ]);

    Membership::factory()->create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'member',
    ]);

    $file = UploadedFile::fake()->create('score.pdf', 100, 'application/pdf');

    $response = $this
        ->actingAs($user, 'api')
        ->postJson("/api/v1/programs/{$program->id}/scores", [
            'title' => 'Score',
            'file' => $file,
        ]);

    $response->assertForbidden();
});

it('requires authentication to upload a score', function () {
    $program = Program::factory()->create();

    $file = UploadedFile::fake()->create('score.pdf', 100, 'application/pdf');

    $response = $this->postJson("/api/v1/programs/{$program->id}/scores", [
        'title' => 'Score',
        'file' => $file,
    ]);

    $response->assertUnauthorized();
});

it('validates required fields when uploading a score', function () {
    $user = User::factory()->create();
    $program = Program::factory()->create();

    $response = $this
        ->actingAs($user, 'api')
        ->postJson("/api/v1/programs/{$program->id}/scores", []);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['title', 'file']);
});

it('only accepts pdf files for scores', function () {
    $user = User::factory()->create();
    $program = Program::factory()->create();

    $file = UploadedFile::fake()->create('score.txt', 100, 'text/plain');

    $response = $this
        ->actingAs($user, 'api')
        ->postJson("/api/v1/programs/{$program->id}/scores", [
            'title' => 'Invalid file',
            'file' => $file,
        ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['file']);
});

it('stores the uploaded file and saves correct metadata', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $orchestra = Orchestra::factory()->create();

    $program = Program::factory()->create([
        'orchestra_id' => $orchestra->id,
    ]);

    Membership::factory()->create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    $file = UploadedFile::fake()->create('myscore.pdf', 120, 'application/pdf');

    $response = $this
        ->actingAs($user, 'api')
        ->postJson("/api/v1/programs/{$program->id}/scores", [
            'title' => 'Metadata test',
            'file' => $file,
        ]);

    $response->assertCreated();

    $score = Score::first();

    expect($score)->not->toBeNull();

    Storage::disk('public')->assertExists($score->file_path);

    expect($score->title)->toBe('Metadata test');
    expect($score->mime_type)->toBe('application/pdf');
    expect($score->original_name)->toBe('myscore.pdf');
});