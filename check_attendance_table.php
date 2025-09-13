<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking attendances table structure:\n";

try {
    $columns = DB::select('DESCRIBE attendances');
    foreach($columns as $col) {
        echo $col->Field . ' - ' . $col->Type . "\n";
    }
    
    echo "\nChecking other tables for faculty_id vs professor_id:\n";
    
    $tables = ['payslips', 'clearance_requests', 'schedule_assignments', 'subject_load_trackers', 'teaching_histories', 'evaluations'];
    
    foreach($tables as $table) {
        try {
            $columns = DB::select("DESCRIBE $table");
            $hasColumn = false;
            foreach($columns as $col) {
                if(strpos($col->Field, 'faculty_id') !== false || strpos($col->Field, 'professor_id') !== false) {
                    echo "$table: " . $col->Field . "\n";
                    $hasColumn = true;
                }
            }
            if(!$hasColumn) {
                echo "$table: No faculty/professor ID column found\n";
            }
        } catch (Exception $e) {
            echo "$table: Error - " . $e->getMessage() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
