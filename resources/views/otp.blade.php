<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - EMS</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>

<body>
    <div class="auth-container">
        <div class="auth-card" style="width:min(100%, 430px);">
            <h2>Verify OTP</h2>
            <p>Enter the OTP sent to your email and reset your password securely.</p>

            @if(session('error'))
                <div style="margin-bottom:14px;padding:10px 12px;border-radius:10px;background:#fef2f2;color:#dc2626;border:1px solid #fecaca;">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div style="margin-bottom:14px;padding:10px 12px;border-radius:10px;background:#ecfdf5;color:#15803d;border:1px solid #bbf7d0;">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('otp_success'))
                <div style="margin-bottom:14px;padding:10px 12px;border-radius:10px;background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;">
                    {{ session('otp_success') }}
                </div>
            @endif

            <form action="{{ route('verify-otp') }}" method="POST">
                @csrf

                <label for="otpCode">Enter OTP</label>
                <input type="text" name="otp" id="otpCode" maxlength="6" placeholder="Enter 6-digit OTP" required>
                @error('otp')
                    <small style="display:block;margin-top:6px;color:#dc2626;">{{ $message }}</small>
                @enderror

                <button type="submit" style="margin-top:16px;">Verify OTP</button>
            </form>

            @if(session('otp_verified'))
                <div style="margin:22px 0 16px;border-top:1px solid #dbe5f1;"></div>

                <form action="{{ route('reset-password') }}" method="POST">
                    @csrf

                    <label for="newPassword">New Password</label>
                    <input type="password" name="password" id="newPassword" placeholder="Enter new password" required>

                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="confirmPassword" placeholder="Confirm new password" required>
                    @error('password')
                        <small style="display:block;margin-top:6px;color:#dc2626;">{{ $message }}</small>
                    @enderror

                    <button type="submit" style="margin-top:16px;">Reset Password</button>
                </form>
            @endif

            <p class="switch">
                Back to Forgot Password?
                <a href="{{ route('forgot-password') }}">Go Back</a>
            </p>
        </div>
    </div>
</body>

</html>
