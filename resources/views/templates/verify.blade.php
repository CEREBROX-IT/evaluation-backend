<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
</head>

<body>
    <h2>Email Verification</h2>

    <p>Dear {{ $user->first_name }},</p>

    <p>
        To complete updating your email address, please click the link below to verify your
        email address:
    </p>

    <p>
        <a href="{{ route('verification.verify', ['id' => $user->id, 'hash' => sha1($user->email)]) }}">Verify Email</a>
    </p>

    <p>
        If you did not request this email, you can safely ignore it.
    </p>

    <p>Regards,<br>
        Your Application Name</p>
</body>

</html>
