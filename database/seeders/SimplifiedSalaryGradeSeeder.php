<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SalaryGrade;

class SimplifiedSalaryGradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $salaryGrades = [
            [
                'grade' => 1,
                'full_time_base_salary' => 25000.00,
                'part_time_base_salary' => 20000.00,
            ],
            [
                'grade' => 2,
                'full_time_base_salary' => 30000.00,
                'part_time_base_salary' => 24000.00,
            ],
            [
                'grade' => 3,
                'full_time_base_salary' => 35000.00,
                'part_time_base_salary' => 28000.00,
            ],
            [
                'grade' => 4,
                'full_time_base_salary' => 40000.00,
                'part_time_base_salary' => 32000.00,
            ],
            [
                'grade' => 5,
                'full_time_base_salary' => 45000.00,
                'part_time_base_salary' => 36000.00,
            ],
        ];

        foreach ($salaryGrades as $grade) {
            SalaryGrade::updateOrCreate(
                ['grade' => $grade['grade']],
                $grade
            );
        }
    }
}
