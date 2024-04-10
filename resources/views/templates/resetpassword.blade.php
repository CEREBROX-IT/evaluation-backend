<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
</head>

<body>
    <p>Hello {{ $user->first_name }},</p>
    <p>You are receiving this email because we received a password reset request for your account.</p>
    <p>Please click the button below to reset your password:</p>
    <table class="action" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
                    <tr>
                        <td align="center">
                            <a href="{{ $resetUrl }}" class="button button-primary" target="_blank"
                                rel="noopener noreferrer">Reset Password</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <p>If you did not request a password reset, no further action is required.</p>
    <p>Thank you.</p>
</body>

</html>
