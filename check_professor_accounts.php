<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking professor accounts in users table:\n";

try {
    $users = DB::table('users')->get();
    echo "Total users: " . $users->count() . "\n\n";
    
    foreach($users as $user) {
        echo "ID: {$user->id}\n";
        echo "Name: {$user->name}\n";
        echo "Email: {$user->email}\n";
        echo "Role: {$user->role}\n";
        echo "Department: " . ($user->department ?? 'null') . "\n";
        echo "---\n";
    }
    
    echo "\nChecking faculties table:\n";
    $faculties = DB::table('faculties')->get();
    echo "Total faculties: " . $faculties->count() . "\n\n";
    
    foreach($faculties as $faculty) {
        echo "ID: {$faculty->id}\n";
        echo "Name: {$faculty->name}\n";
        echo "Email: {$faculty->email}\n";
        echo "Role: " . ($faculty->role ?? 'null') . "\n";
        echo "Department: " . ($faculty->department ?? 'null') . "\n";
        echo "---\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
