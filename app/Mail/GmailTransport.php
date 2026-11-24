<?php

namespace App\Mail;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\Exception\TransportException;

class GmailTransport extends AbstractTransport
{
    protected function doSend(SentMessage $message): void
    {
        $accessToken = $this->getAccessToken();

        $raw = rtrim(strtr(
            base64_encode($message->getMessage()->toString()),
            '+/',
            '-_'
        ), '=');

        $response = Http::withToken($accessToken)
            ->timeout(15)
            ->post('https://gmail.googleapis.com/gmail/v1/users/me/messages/send', [
                'raw' => $raw,
            ]);

        if ($response->failed()) {
            throw new TransportException(
                'Gmail API failed to send the email. Status: ' . $response->status()
            );
        }
    }

    private function getAccessToken(): string
    {
        $response = Http::asForm()
            ->timeout(15)
            ->post('https://oauth2.googleapis.com/token', [
                'client_id' => config('services.gmail.client_id'),
                'client_secret' => config('services.gmail.client_secret'),
                'refresh_token' => config('services.gmail.refresh_token'),
                'grant_type' => 'refresh_token',
            ]);

        if ($response->failed() || ! $response->json('access_token')) {
            throw new TransportException(
                'Failed to obtain Gmail access token. Status: ' . $response->status()
            );
        }

        return $response->json('access_token');
    }

    public function __toString(): string
    {
        return 'gmail';
    }
}