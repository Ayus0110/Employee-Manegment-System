<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" href="{{ asset('/assets/images/browser_icone.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EMS</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <script src="{{ asset('assets/js/script.js') }}" defer></script>
</head>

<body>

    <div class="auth-container">
        <div class="auth-card">
            <h2>Welcome Back</h2>
            <p>Sign in to your account</p>

            <form id="loginForm" action="{{ route('login.submit') }}" method="POST">
                @csrf
                 @if ($errors->has('login'))
                    <small style="display:block;margin-top:6px;color:#dc2626;font-weight:600;">
                        {{ $errors->first('login') }}
                    </small>
                @endif
                <label>Email</label>
                <input type="email" name="email" id="loginEmail" value="{{ old('email') }}" required>
               
                @error('email')
                    <small style="display:block;margin-top:6px;color:#dc2626;">{{ $message }}</small>
                @enderror

                <label>Password</label>
                <input type="password" name="password" id="loginPassword" required>
                @error('password')
                    <small style="display:block;margin-top:6px;color:#dc2626;">{{ $message }}</small>
                @enderror

                <div class="options">
                    {{-- <label for="rememberMe">
                        <input type="checkbox" name="remember" id="rememberMe" value="1" {{ old('remember') ? 'checked' : '' }}>
                        Remember me
                    </label> --}}
                    <a href="{{ route('forgot-password') }}">Forgot password?</a>
                </div>

                <button type="submit">Sign In</button>
            </form>

            <p class="switch">
                Back to Home ?
                <a href="{{ route('home') }}">Home</a>
            </p>
        </div>
    </div>


</body>

</html>
