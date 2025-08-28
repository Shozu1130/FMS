<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SalaryGrade;

class SalaryGradeSeeder extends Seeder
{
    public function run()
    {
        SalaryGrade::create([
            'grade' => 1,
            'step' => 1,
            'base_salary' => 25000.00,
            'allowance' => 5000.00,
            'is_active' => true,
            'notes' => 'Entry-level salary grade',
        ]);

        SalaryGrade::create([
            'grade' => 1,
            'step' => 2,
            'base_salary' => 26000.00,
            'allowance' => 5000.00,
            'is_active' => true,
            'notes' => 'Entry-level salary grade, step 2',
        ]);

        SalaryGrade::create([
            'grade' => 2,
            'step' => 1,
            'base_salary' => 28000.00,
            'allowance' => 6000.00,
            'is_active' => true,
            'notes' => 'Mid-level salary grade',
        ]);
    }
}
