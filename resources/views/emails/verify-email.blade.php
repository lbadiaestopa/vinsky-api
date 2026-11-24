@extends('emails.layout')

@section('title', 'Verify your email address | Vinsky')

@section('content')

    <h1 style="margin: 0 0 24px; font-size: 28px; font-weight: 600; line-height: 1.3; letter-spacing: -0.8px; color: #171717;">
        Verify your email address.
    </h1>

    <p style="margin: 0 0 16px; font-size: 16px; line-height: 1.6; color: #404040;">
        Hi {{ $name }},
    </p>

    <p style="margin: 0 0 16px; font-size: 16px; line-height: 1.6; color: #404040;">
        Welcome to Vinsky! You're one step away from keeping your orchestra in sync.
    </p>

    <p style="margin: 0 0 32px; font-size: 16px; line-height: 1.6; color: #404040;">
        Please verify your email address to activate your account and get started.
    </p>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center" bgcolor="#171717" style="border-radius: 8px;">
                <a href="{{ $verificationUrl }}" style="display: block; padding: 16px 24px; font-size: 15px; font-weight: 600; color: #ffffff; text-decoration: none; border-radius: 8px;">
                    Verify email address
                </a>
            </td>
        </tr>
    </table>

    <p style="margin: 24px 0 16px; font-size: 13px; line-height: 1.6; color: #737373;">
        This verification link will expire in {{ $expiration }} minutes.
    </p>

    <p style="margin: 0; font-size: 13px; line-height: 1.6; color: #737373;">
        If you didn't create a Vinsky account, you can safely ignore this email.
    </p>

    <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid #e5e5e5;">

        <p style="margin: 0 0 8px; font-size: 12px; line-height: 1.6; color: #737373;">
            If you're having trouble clicking the button, copy and paste the following URL into your browser:
        </p>

        <a href="{{ $verificationUrl }}" style="font-size: 12px; line-height: 1.6; color: #737373; word-break: break-all;">
            {{ $verificationUrl }}
        </a>

    </div>

@endsection