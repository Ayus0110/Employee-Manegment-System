<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow p-4 mx-auto" style="max-width: 500px;">
        <h3 class="mb-4 text-center">Change Password</h3>

        <form method="POST" action="{{ route('password.change.update') }}">
            @csrf

            <input type="password" name="password" class="form-control mb-3" placeholder="New Password" required>

            <input type="password" name="password_confirmation" class="form-control mb-3" placeholder="Confirm Password" required>

            <button type="submit" class="btn btn-primary w-100">Update Password</button>
        </form>
    </div>
</div>

</body>
</html>