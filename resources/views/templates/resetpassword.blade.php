<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

<body
    style="
            margin: 5%;
            padding: 5%;
            font-family: Arial, sans-serif;
            background-color: #f6f8fc;
        ">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
        <tr>
            <td
                style="
                        padding: 40px;
                        background-color: #ffffff;
                        border-radius: 10px;
                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                    ">
                <h3 style="margin-bottom: 20px;"><strong>Hello {{ $user->first_name }},</strong></h3>
                <p style="margin-bottom: 30px">
                    We're sending you this email because someone asked to
                    change your password. It's important to keep your
                    account safe, so we're checking with you before making
                    any changes. To reset your password, just click the
                    button below. Your quick action helps keep your account
                    secure. Thanks for your cooperation!
                </p>
                <table border="0" cellpadding="0" cellspacing="0" role="presentation">
                    <tr>
                        <td align="center">
                            <a href="{{ $resetUrl }}"
                                style="
                                        display: inline-block;
                                        padding: 10px 20px;
                                        background-color: #1a1a1a;
                                        color: #ffffff;
                                        text-decoration: none;
                                        border-radius: 10px;
                                        font-size: 16px;
                                        font-weight: bold;
                                    "
                                target="_blank" rel="noopener noreferrer">
                                Reset Password
                            </a>
                        </td>
                    </tr>
                </table>
                <p style="margin-top: 30px; margin-bottom: 5px">
                    If you didn't ask to change your password, don't worry.
                    You don't need to do anything else. We'll keep your
                    account safe.
                </p>
                <p style="margin-bottom: 10px">
                    Thank you for your attention.
                </p>

                <p>
                    If you have any questions or concerns, feel free to
                    reach out to us. We're here to help!
                </p>
            </td>
        </tr>
    </table>
</body>

</html>
