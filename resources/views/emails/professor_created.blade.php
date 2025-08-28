<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Professor Account Created</title>
</head>
<body>
    <h2>Welcome to Bestlink College Faculty System!</h2>
    
    <p>Dear {{ $professorName }},</p>
    
    <p>Your professor account has been created. Here are your login credentials:</p>
    
    <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
        <p><strong>Professor ID:</strong> {{ $professorId }}</p>
        <p><strong>Email:</strong> {{ $professorEmail }}</p>
        <p><strong>Temporary Password:</strong> {{ $tempPassword }}</p>
    </div>
    
    <p><strong>Login URL:</strong> <a href="{{ url('/login') }}">{{ url('/login') }}</a></p>
    
    <p style="color: #dc3545;">
        <strong>Important:</strong> Please change your password after first login.
    </p>
    
    <hr>
    <p>Best regards,<br>Bestlink College Administration</p>
</body>
</html>