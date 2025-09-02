<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing attendance creation:\n";

try {
    // Test creating an attendance record directly
    $attendance = new \App\Models\Attendance();
    $attendance->faculty_id = 2;
    $attendance->date = '2025-09-02';
    $attendance->status = 'late';
    $attendance->time_in = '2025-09-02 19:53:12';
    $attendance->time_in_photo = 'test_photo.jpg';
    $attendance->time_in_location = '14.626300, 121.039900';
    $attendance->notes = 'Test note';
    
    echo "Attempting to save attendance record...\n";
    $result = $attendance->save();
    
    if ($result) {
        echo "SUCCESS: Attendance record saved with ID: " . $attendance->id . "\n";
    } else {
        echo "FAILED: Could not save attendance record\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Full error: " . $e->getTraceAsString() . "\n";
}
