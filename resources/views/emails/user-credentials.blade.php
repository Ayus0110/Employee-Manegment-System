<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>User Credentials</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <h2>Hello {{ $user->name }},</h2>

    <p>Your HRMS account has been created successfully.</p>

    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Password:</strong> {{ $plainPassword }}</p>

    <p>Please login with these credentials and change your password after login.</p>

    <p>Thank you.</p>
</body>
</html>