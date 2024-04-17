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
                <h3 style="margin-bottom: 20px">
                    <strong>Hello {{ $user->first_name }},</strong>
                </h3>
                <p style="margin-bottom: 30px">
                    To complete updating your email address, please click
                    the button below to verify your email address:
                </p>
                <table border="0" cellpadding="0" cellspacing="0" role="presentation">
                    <tr>
                        <td align="center">
                            <a href="{{ route('verification.verify', ['id' => $user->id, 'hash' => sha1($user->email)]) }}"
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
                                Verify Email Address
                            </a>
                        </td>
                    </tr>
                </table>
                <p style="margin-top: 30px; margin-bottom: 5px">
                    If you did not request this email, don't worry. You
                    don't need to do anything else, you can safely ignore
                    it.
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
