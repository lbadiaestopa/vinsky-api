<?php

use App\Mail\GmailTransport;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mime\Email;

beforeEach(function () {
    config([
        'services.gmail.client_id' => 'test-client-id',
        'services.gmail.client_secret' => 'test-client-secret',
        'services.gmail.refresh_token' => 'test-refresh-token',
    ]);

    Http::preventStrayRequests();

    $this->transport = new GmailTransport();

    $this->email = (new Email())
        ->from('vinsky.app@gmail.com')
        ->to('john@example.com')
        ->subject('Test email')
        ->text('This is a test email.');
});

it('obtains an access token successfully', function () {
    Http::fake([
        'oauth2.googleapis.com/token' => Http::response([
            'access_token' => 'test-access-token',
        ], 200),
        'gmail.googleapis.com/*' => Http::response([
            'id' => 'test-message-id',
        ], 200),
    ]);

    $this->transport->send($this->email);

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://oauth2.googleapis.com/token'
            && $request->method() === 'POST'
            && $request['client_id'] === 'test-client-id'
            && $request['client_secret'] === 'test-client-secret'
            && $request['refresh_token'] === 'test-refresh-token'
            && $request['grant_type'] === 'refresh_token';
    });
});

it('sends an email successfully through Gmail API', function () {
    Http::fake([
        'oauth2.googleapis.com/token' => Http::response([
            'access_token' => 'test-access-token',
        ], 200),
        'gmail.googleapis.com/*' => Http::response([
            'id' => 'test-message-id',
        ], 200),
    ]);

    $this->transport->send($this->email);

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://gmail.googleapis.com/gmail/v1/users/me/messages/send'
            && $request->method() === 'POST'
            && $request->hasHeader('Authorization', 'Bearer test-access-token')
            && ! empty($request['raw']);
    });

    Http::assertSentCount(2);
});

it('encodes the email correctly using Base64URL', function () {
    Http::fake([
        'oauth2.googleapis.com/token' => Http::response([
            'access_token' => 'test-access-token',
        ], 200),
        'gmail.googleapis.com/*' => Http::response([
            'id' => 'test-message-id',
        ], 200),
    ]);

    $this->transport->send($this->email);

    Http::assertSent(function (Request $request) {
        if ($request->url() !== 'https://gmail.googleapis.com/gmail/v1/users/me/messages/send') {
            return false;
        }

        $raw = $request['raw'];

        $decoded = base64_decode(strtr($raw, '-_', '+/'), true);

        expect($raw)->toMatch('/^[A-Za-z0-9_-]+$/');

        expect($decoded)
            ->toBeString()
            ->toContain('From: vinsky.app@gmail.com')
            ->toContain('To: john@example.com')
            ->toContain('Subject: Test email')
            ->toContain('This is a test email.');

        return true;
    });
});

it('throws an exception when obtaining an access token fails', function () {
    Http::fake([
        'oauth2.googleapis.com/token' => Http::response([
            'error' => 'invalid_grant',
        ], 400),
    ]);

    expect(fn () => $this->transport->send($this->email))
        ->toThrow(
            TransportException::class,
            'Failed to obtain Gmail access token. Status: 400'
        );

    Http::assertSentCount(1);
});

it('throws an exception when the access token is missing', function () {
    Http::fake([
        'oauth2.googleapis.com/token' => Http::response([], 200),
    ]);

    expect(fn () => $this->transport->send($this->email))
        ->toThrow(
            TransportException::class,
            'Failed to obtain Gmail access token. Status: 200'
        );

    Http::assertSentCount(1);
});

it('throws an exception when Gmail API fails to send an email', function () {
    Http::fake([
        'oauth2.googleapis.com/token' => Http::response([
            'access_token' => 'test-access-token',
        ], 200),
        'gmail.googleapis.com/*' => Http::response([
            'error' => [
                'message' => 'Forbidden',
            ],
        ], 403),
    ]);

    expect(fn () => $this->transport->send($this->email))
        ->toThrow(
            TransportException::class,
            'Gmail API failed to send the email. Status: 403'
        );

    Http::assertSentCount(2);
});