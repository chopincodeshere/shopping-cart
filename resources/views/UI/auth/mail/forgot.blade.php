<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
</head>
<body>
    <p>Hello, your password is reseted!</p>
    
    <p>Your new password is : <b>{{ $password }}</b></p>
    
    <p>© {{ now()->format('Y') }} {{ config('app.name') }}. All rights reserved.</p>
</body>
</html>