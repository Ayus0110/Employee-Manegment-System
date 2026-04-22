<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify OTP</title>
</head>
<body>
    <h2>Verify OTP</h2>

    @if(session('error'))
        <p style="color:red;">{{ session('error') }}</p>
    @endif

    @if(session('success'))
        <p style="color:green;">{{ session('success') }}</p>
    @endif

    @if(session('otp_success'))
        <p style="color:green;">{{ session('otp_success') }}</p>
    @endif

    <form action="{{ route('password.verifyOtp') }}" method="POST">
        @csrf
        <label>Enter OTP</label>
        <input type="text" name="otp" maxlength="6" required>
        <button type="submit">Verify OTP</button>
    </form>

    <hr>

    <form action="{{ route('password.updateForgot') }}" method="POST">
        @csrf
        <label>New Password</label>
        <input type="password" name="password" required>

        <label>Confirm Password</label>
        <input type="password" name="password_confirmation" required>

        <button type="submit">Update Password</button>
    </form>
</body>
</html>