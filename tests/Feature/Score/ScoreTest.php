<?php

use App\Models\User;
use App\Models\Program;
use App\Models\Orchestra;
use App\Models\Membership;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('private');
});

function fakePdf(string $name = 'score.pdf', int $kilobytes = 1): UploadedFile
{
    $content = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj "
        . "2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj "
        . "3 0 obj<</Type/Page/MediaBox[0 0 612 792]>>endobj\n"
        . "xref\n0 4\n0000000000 65535 f\ntrailer<</Size 4/Root 1 0 R>>"
        . "startxref\n9\n%%EOF";

    $content = str_pad($content, $kilobytes * 1024, ' ');

    return UploadedFile::fake()->createWithContent($name, $content);
}

it('requires authentication to upload a score', function () {
    $program = Program::factory()->create();

    $file = fakePdf();

    $response = $this->postJson("/api/v1/programs/{$program->id}/scores", [
        'file' => $file,
        'original_name' => 'score.pdf',
    ]);

    $response->assertUnauthorized();
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

    $file = fakePdf();

    $response = $this
        ->actingAs($user, 'api')
        ->postJson("/api/v1/programs/{$program->id}/scores", [
            'file' => $file,
            'original_name' => 'score.pdf',
        ]);

    $response->assertForbidden();
});

it('allows admin users of the orchestra to upload a score', function () {
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

    $file = fakePdf();

    $response = $this
        ->actingAs($user, 'api')
        ->postJson("/api/v1/programs/{$program->id}/scores", [
            'file' => $file,
            'original_name' => 'score.pdf',
        ]);

    $response->assertCreated();
});

it('requires file', function () {
    $user = User::factory()->create();
    $program = Program::factory()->create();

    $response = $this
        ->actingAs($user, 'api')
        ->postJson("/api/v1/programs/{$program->id}/scores", []);

    $response->assertJsonValidationErrors(['file']);
});

it('only accepts pdf files', function () {
    $user = User::factory()->create();
    $program = Program::factory()->create();

    $file = UploadedFile::fake()->create('score.txt', 100, 'text/plain');

    $response = $this
        ->actingAs($user, 'api')
        ->postJson("/api/v1/programs/{$program->id}/scores", [
            'file' => $file,
        ]);

    $response->assertJsonValidationErrors(['file']);
});

it('rejects files over 10MB', function () {
    $user = User::factory()->create();
    $program = Program::factory()->create();

    $file = UploadedFile::fake()->create('score.pdf', 11000, 'application/pdf');

    $response = $this
        ->actingAs($user, 'api')
        ->postJson("/api/v1/programs/{$program->id}/scores", [
            'file' => $file,
        ]);

    $response->assertJsonValidationErrors(['file']);
});

it('rejects invalid MIME content', function () {
    $user = User::factory()->create();
    $orchestra = Orchestra::factory()->create();
    $program = Program::factory()->create(['orchestra_id' => $orchestra->id]);
    Membership::factory()->create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    $file = UploadedFile::fake()->createWithContent('score.pdf', '<?php echo "not a pdf";');

    $response = $this
        ->actingAs($user, 'api')
        ->postJson("/api/v1/programs/{$program->id}/scores", ['file' => $file]);

    $response->assertStatus(422);
});

it('rejects filenames starting with dot', function () {
    $user = User::factory()->create();
    $program = Program::factory()->create();

    $file = fakePdf();

    $file->name = '.env.pdf';

    $response = $this
        ->actingAs($user, 'api')
        ->postJson("/api/v1/programs/{$program->id}/scores", [
            'file' => $file,
            'original_name' => '.env.pdf',
        ]);

    $response->assertStatus(400);
});

it('rejects filenames with more than one dot', function () {
    $user = User::factory()->create();
    $program = Program::factory()->create();

    $file = fakePdf();

    $response = $this
        ->actingAs($user, 'api')
        ->postJson("/api/v1/programs/{$program->id}/scores", [
            'file' => $file,
            'original_name' => 'evil.file.pdf.exe',
        ]);

    $response->assertStatus(400);
});

it('sanitizes original filename removing control characters', function () {
    $user = User::factory()->create();
    $orchestra = Orchestra::factory()->create();
    $program = Program::factory()->create(['orchestra_id' => $orchestra->id]);
    Membership::factory()->create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    $file = fakePdf();

    $response = $this
        ->actingAs($user, 'api')
        ->postJson("/api/v1/programs/{$program->id}/scores", [
            'file' => $file,
            'original_name' => "sc\0ore\n.pdf",
        ]);

    $response->assertCreated();

    $this->assertDatabaseMissing('scores', [
        'original_name' => "sc\0ore\n.pdf",
    ]);
});

it('stores file in private disk', function () {
    Storage::fake('private');

    $user = User::factory()->create();
    $orchestra = Orchestra::factory()->create();
    $program = Program::factory()->create(['orchestra_id' => $orchestra->id]);
    Membership::factory()->create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    $file = fakePdf();

    $this->actingAs($user, 'api')
        ->postJson("/api/v1/programs/{$program->id}/scores", [
            'file' => $file,
            'original_name' => 'score.pdf',
        ]);

    $this->assertDatabaseCount('scores', 1);

    $score = \App\Models\Score::first();
    Storage::disk('private')->assertExists($score->file_path);
});

it('stores file metadata correctly', function () {
    $user = User::factory()->create();
    $orchestra = Orchestra::factory()->create();
    $program = Program::factory()->create(['orchestra_id' => $orchestra->id]);
    Membership::factory()->create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    $file = fakePdf();

    $this->actingAs($user, 'api')
        ->postJson("/api/v1/programs/{$program->id}/scores", [
            'file' => $file,
            'original_name' => 'score.pdf',
        ]);

    $this->assertDatabaseHas('scores', [
        'program_id' => $program->id,
        'mime_type' => 'application/pdf',
    ]);
});

it('stores file with correct size', function () {
    $user = User::factory()->create();
    $orchestra = Orchestra::factory()->create();
    $program = Program::factory()->create(['orchestra_id' => $orchestra->id]);
    Membership::factory()->create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    $file = fakePdf();

    $this->actingAs($user, 'api')
        ->postJson("/api/v1/programs/{$program->id}/scores", [
            'file' => $file,
            'original_name' => 'score.pdf',
        ]);

    $this->assertDatabaseHas('scores', [
        'size' => $file->getSize(),
    ]);
});

it('allows an authenticated user to view all scores of a program', function () {

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

    Score::factory()->count(3)->create([
        'program_id' => $program->id,
    ]);

    $response = $this
        ->actingAs($user, 'api')
        ->getJson("/api/v1/programs/{$program->id}/scores");

    $response->assertOk();

    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'title',
                'file_path',
                'mime_type',
            ],
        ],
    ]);

    expect($response->json('data'))->toHaveCount(3);
});


it('does not return scores from other programs', function () {

    $user = User::factory()->create();

    $orchestra = Orchestra::factory()->create();

    $program = Program::factory()->create([
        'orchestra_id' => $orchestra->id,
    ]);

    $otherProgram = Program::factory()->create([
        'orchestra_id' => $orchestra->id,
    ]);

    Membership::factory()->create([
        'user_id' => $user->id,
        'orchestra_id' => $orchestra->id,
        'role' => 'admin',
    ]);

    Score::factory()->count(2)->create([
        'program_id' => $program->id,
    ]);

    Score::factory()->count(3)->create([
        'program_id' => $otherProgram->id,
    ]);

    $response = $this
        ->actingAs($user, 'api')
        ->getJson("/api/v1/programs/{$program->id}/scores");

    $response->assertOk();

    expect($response->json('data'))->toHaveCount(2);
});


it('requires authentication to view scores', function () {

    $program = Program::factory()->create();

    $response = $this
        ->getJson("/api/v1/programs/{$program->id}/scores");

    $response->assertUnauthorized();
});