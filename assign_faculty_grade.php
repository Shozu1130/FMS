<?php

require_once 'vendor/autoload.php';

use App\Models\Faculty;
use App\Models\SalaryGrade;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $faculty = Faculty::first();
    $salaryGrade = SalaryGrade::first();
    
    if ($faculty && $salaryGrade) {
        $faculty->salaryGrades()->attach($salaryGrade->id, [
            'effective_date' => now(),
            'is_current' => true
        ]);
        echo "Faculty {$faculty->name} assigned to Grade {$salaryGrade->grade}\n";
    } else {
        echo "Faculty or SalaryGrade not found\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
