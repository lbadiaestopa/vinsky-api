<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmail
{
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verify your email address | Vinsky')
            ->view('emails.verify-email', [
                'name' => $notifiable->name,
                'verificationUrl' => $verificationUrl,
                'expiration' => config('auth.verification.expire', 60),
            ]);
    }
}