<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Vinsky')</title>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body style="margin: 0; padding: 0; background-color: #f5f5f5; font-family: 'Geist', Arial, Helvetica, sans-serif; color: #171717;">

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 40px 16px;">

                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 600px; background-color: #ffffff; border: 1px solid #e5e5e5; border-radius: 12px;">

                    <tr>
                        <td align="center" style="padding: 32px; border-bottom: 1px solid #e5e5e5;">
                            <a href="{{ config('app.frontend_url') }}" style="font-size: 32px; font-weight: 700; letter-spacing: -1.5px; color: #171717; text-decoration: none;">
                                Vinsky
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 40px 32px;">
                            @yield('content')
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding: 24px 32px; border-top: 1px solid #e5e5e5;">

                            <p style="margin: 0 0 12px; font-size: 14px; font-weight: 500; color: #171717;">
                                Your Orchestra, Always in Sync.
                            </p>

                            <p style="margin: 0 0 12px; font-size: 12px; color: #737373;">
                                &copy; Vinsky {{ date('Y') }} &middot; Barcelona
                            </p>

                            <a href="{{ rtrim(config('app.frontend_url'), '/') }}/privacy" style="font-size: 12px; color: #737373; text-decoration: underline;">
                                Privacy Policy
                            </a>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>