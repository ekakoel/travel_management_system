<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Partner Account Verified</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">

    <h2>Your Bali Kami Partner Account Has Been Verified</h2>

    <p>
        Dear {{ $agent->contact_name ?? $agent->name }},
    </p>

    <p>
        We are pleased to inform you that your Bali Kami Partner application
        has been successfully verified.
    </p>

    <p>
        You can now log in to your Bali Kami Partner account using the
        credentials below:
    </p>

    <table cellpadding="8" cellspacing="0" border="0">
        <tr>
            <td><strong>Username</strong></td>
            <td>{{ $email }}</td>
        </tr>
        <tr>
            <td><strong>Password</strong></td>
            <td>{{ $password }}</td>
        </tr>
    </table>

    <p>
        Please keep your login credentials secure and do not share them
        with unauthorized persons.
    </p>

    <p>
        You can access your account through the Bali Kami Partner portal.
    </p>

    <p>
        Best regards,<br>
        <strong>Bali Kami Tourism</strong>
    </p>

</body>
</html>