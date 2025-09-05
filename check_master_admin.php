<?php
require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check master admin account
$user = App\Models\User::where('email', 'master@bestlink.edu.ph')->first();

if ($user) {
    echo "Master Admin found:\n";
    echo "Name: " . $user->name . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Role: " . $user->role . "\n";
    echo "Department: " . ($user->department ?? 'null') . "\n";
    echo "Created: " . $user->created_at . "\n";
    
    // Test password verification
    if (Hash::check('master123', $user->password)) {
        echo "Password 'master123' is correct!\n";
    } else {
        echo "Password 'master123' is incorrect!\n";
    }
} else {
    echo "Master Admin not found!\n";
}
?>
