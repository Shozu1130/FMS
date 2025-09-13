<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking database tables:\n";

try {
    $tables = DB::select("SHOW TABLES");
    echo "Tables found:\n";
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        echo "- " . $tableName . "\n";
    }
    
    echo "\nChecking faculty table specifically:\n";
    try {
        $facultyCount = DB::table('faculties')->count();
        echo "Faculty table exists with $facultyCount records\n";
    } catch (Exception $e) {
        echo "Faculty table error: " . $e->getMessage() . "\n";
    }
    
    try {
        $facultyCount2 = DB::table('faculty')->count();
        echo "Faculty table (singular) exists with $facultyCount2 records\n";
    } catch (Exception $e) {
        echo "Faculty table (singular) error: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
