<?php
require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test login credentials for professor
$email = 'professor@bestlink.edu.ph';
$password = 'professor123';

echo "Testing login credentials:\n";
echo "Email: $email\n";
echo "Password: $password\n\n";

// Check if user exists
$user = App\Models\User::where('email', $email)->first();

if ($user) {
    echo "✓ User found in database\n";
    echo "  Name: " . $user->name . "\n";
    echo "  Role: " . $user->role . "\n";
    echo "  Department: " . ($user->department ?? 'null') . "\n";
    
    // Test password verification
    if (Hash::check($password, $user->password)) {
        echo "✓ Password verification successful\n";
        
        // Test Auth::attempt simulation
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $authenticatedUser = Auth::user();
            echo "✓ Auth::attempt successful\n";
            echo "  Authenticated user role: " . $authenticatedUser->role . "\n";
            
            // Check if role qualifies for admin dashboard
            if ($authenticatedUser->role === 'admin' || $authenticatedUser->role === 'master_admin') {
                echo "✓ User qualifies for admin dashboard access\n";
            } else {
                echo "✗ User does NOT qualify for admin dashboard access\n";
            }
            
            Auth::logout();
        } else {
            echo "✗ Auth::attempt failed\n";
        }
    } else {
        echo "✗ Password verification failed\n";
    }
} else {
    echo "✗ User not found in database\n";
}
?>
