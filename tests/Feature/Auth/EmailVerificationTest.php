<?php

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

it('verifies a user email successfully', function () {

    Event::fake([Verified::class]);

    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        [
            'id' => $user->id,
            'hash' => sha1($user->getEmailForVerification()),
        ]
    );

    $response = $this->get($verificationUrl);

    $response->assertRedirect(config('app.frontend_url') . '/email/verified');

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();

    Event::assertDispatched(Verified::class);
});

it('rejects a verification link with an invalid hash', function () {

    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        [
            'id' => $user->id,
            'hash' => sha1('different@example.com'),
        ]
    );

    $response = $this->get($verificationUrl);

    $response->assertStatus(403);

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

it('rejects an expired verification link', function () {

    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        [
            'id' => $user->id,
            'hash' => sha1($user->getEmailForVerification()),
        ]
    );

    $this->travel(61)->minutes();

    $response = $this->get($verificationUrl);

    $response->assertStatus(403);

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

it('resends the verification email to an unverified user', function () {

    Notification::fake();

    $user = User::factory()->unverified()->create();

    $response = $this->postJson('/api/v1/email/verification-notification', [
        'email' => $user->email,
    ]);

    $response->assertStatus(200);

    $response->assertJson([
        'message' => 'If your email address is registered and not verified, a verification link has been sent.',
    ]);

    Notification::assertSentTo($user, VerifyEmail::class);
});

it('does not resend verification emails to verified or nonexistent users', function ($userExists) {

    Notification::fake();

    $email = 'john@example.com';

    if ($userExists) {
        User::factory()->create([
            'email' => $email,
            'email_verified_at' => now(),
        ]);
    }

    $response = $this->postJson('/api/v1/email/verification-notification', [
        'email' => $email,
    ]);

    $response->assertStatus(200);

    $response->assertJson([
        'message' => 'If your email address is registered and not verified, a verification link has been sent.',
    ]);

    Notification::assertNothingSent();
})->with([
    'verified user' => true,
    'nonexistent user' => false,
]);

it('rejects a verification link with an invalid signature', function () {

    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        [
            'id' => $user->id,
            'hash' => sha1($user->getEmailForVerification()),
        ]
    );

    $tamperedUrl = $verificationUrl . '&modified=true';

    $response = $this->get($tamperedUrl);

    $response->assertStatus(403);

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});