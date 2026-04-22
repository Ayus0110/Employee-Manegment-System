<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - EMS</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>

<body>
    <div class="auth-container">
        <div class="auth-card" style="width:min(100%, 430px);">
            <h2>Forgot Password</h2>
            <p>Enter your registered email and we will send you a secure OTP to reset your password.</p>

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

            <form action="{{ route('send-otp') }}" method="POST">
                @csrf

                <label for="forgotEmail">Email</label>
                <input type="email" name="email" id="forgotEmail" value="{{ old('email') }}" placeholder="Enter your email address" required>
                @error('email')
                    <small style="display:block;margin-top:6px;color:#dc2626;">{{ $message }}</small>
                @enderror

                <button type="submit" style="margin-top:16px;">Send OTP</button>
            </form>

            <p class="switch">
                Back to Login?
                <a href="{{ route('login') }}">Sign In</a>
            </p>
        </div>
    </div>
</body>

</html>
