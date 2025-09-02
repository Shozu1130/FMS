<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SalaryGrade;

class SalaryGradeHourlyRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update existing salary grades with hourly rates
        $salaryGrades = [
            // Grade 1 - Entry Level
            ['grade' => 1, 'step' => 1, 'full_time_hourly_rate' => 250.00, 'part_time_hourly_rate' => 200.00],
            ['grade' => 1, 'step' => 2, 'full_time_hourly_rate' => 275.00, 'part_time_hourly_rate' => 220.00],
            ['grade' => 1, 'step' => 3, 'full_time_hourly_rate' => 300.00, 'part_time_hourly_rate' => 240.00],
            
            // Grade 2 - Junior Level
            ['grade' => 2, 'step' => 1, 'full_time_hourly_rate' => 325.00, 'part_time_hourly_rate' => 260.00],
            ['grade' => 2, 'step' => 2, 'full_time_hourly_rate' => 350.00, 'part_time_hourly_rate' => 280.00],
            ['grade' => 2, 'step' => 3, 'full_time_hourly_rate' => 375.00, 'part_time_hourly_rate' => 300.00],
            
            // Grade 3 - Mid Level
            ['grade' => 3, 'step' => 1, 'full_time_hourly_rate' => 400.00, 'part_time_hourly_rate' => 320.00],
            ['grade' => 3, 'step' => 2, 'full_time_hourly_rate' => 425.00, 'part_time_hourly_rate' => 340.00],
            ['grade' => 3, 'step' => 3, 'full_time_hourly_rate' => 450.00, 'part_time_hourly_rate' => 360.00],
            
            // Grade 4 - Senior Level
            ['grade' => 4, 'step' => 1, 'full_time_hourly_rate' => 475.00, 'part_time_hourly_rate' => 380.00],
            ['grade' => 4, 'step' => 2, 'full_time_hourly_rate' => 500.00, 'part_time_hourly_rate' => 400.00],
            ['grade' => 4, 'step' => 3, 'full_time_hourly_rate' => 525.00, 'part_time_hourly_rate' => 420.00],
            
            // Grade 5 - Expert Level
            ['grade' => 5, 'step' => 1, 'full_time_hourly_rate' => 550.00, 'part_time_hourly_rate' => 440.00],
            ['grade' => 5, 'step' => 2, 'full_time_hourly_rate' => 575.00, 'part_time_hourly_rate' => 460.00],
            ['grade' => 5, 'step' => 3, 'full_time_hourly_rate' => 600.00, 'part_time_hourly_rate' => 480.00],
        ];

        foreach ($salaryGrades as $gradeData) {
            $salaryGrade = SalaryGrade::where('grade', $gradeData['grade'])
                ->where('step', $gradeData['step'])
                ->first();

            if ($salaryGrade) {
                // Update existing salary grade with hourly rates
                $salaryGrade->update([
                    'full_time_hourly_rate' => $gradeData['full_time_hourly_rate'],
                    'part_time_hourly_rate' => $gradeData['part_time_hourly_rate'],
                    'standard_hours_per_month' => 160, // 8 hours/day × 20 working days
                    'overtime_multiplier' => 1.25, // 25% overtime premium
                ]);
            } else {
                // Create new salary grade if it doesn't exist
                SalaryGrade::create([
                    'grade' => $gradeData['grade'],
                    'step' => $gradeData['step'],
                    'base_salary' => $gradeData['full_time_hourly_rate'] * 160, // Calculate monthly base
                    'allowance' => 5000.00, // Standard allowance
                    'full_time_hourly_rate' => $gradeData['full_time_hourly_rate'],
                    'part_time_hourly_rate' => $gradeData['part_time_hourly_rate'],
                    'standard_hours_per_month' => 160,
                    'overtime_multiplier' => 1.25,
                    'notes' => 'Seeded salary grade with hourly rates',
                    'is_active' => true,
                ]);
            }
        }

        $this->command->info('Salary grades updated with hourly rates successfully!');
    }
}
