<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Faculty;
use App\Models\Payslip;
use App\Models\Attendance;
use Carbon\Carbon;

class TestPayrollSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:payroll-system';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the payroll system functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Payroll System...');
        
        // Get a faculty member
        $faculty = Faculty::whereNotNull('employment_type')->first();
        
        if (!$faculty) {
            $this->error('No faculty with employment type found. Please ensure faculty have employment_type set.');
            return 1;
        }
        
        $this->info("Testing with faculty: {$faculty->name} ({$faculty->employment_type})");
        
        // Check if faculty has salary grade
        $salaryGrade = $faculty->getCurrentSalaryGrade();
        if (!$salaryGrade) {
            $this->error('Faculty does not have a current salary grade assigned.');
            return 1;
        }
        
        $this->info("Salary Grade: {$salaryGrade->grade}-{$salaryGrade->step}");
        $this->info("Full-Time Rate: ₱{$salaryGrade->formatted_full_time_hourly_rate}");
        $this->info("Part-Time Rate: ₱{$salaryGrade->formatted_part_time_hourly_rate}");
        
        // Check attendance records
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $attendanceCount = $faculty->attendances()
            ->whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonth)
            ->count();
            
        $this->info("Attendance records for current month: {$attendanceCount}");
        
        if ($attendanceCount === 0) {
            $this->warn('No attendance records found for current month. Creating sample data...');
            $this->createSampleAttendance($faculty);
        }
        
        // Test payslip generation
        try {
            $this->info('Generating payslip...');
            $payslip = Payslip::generateForFaculty($faculty->id, $currentYear, $currentMonth);
            
            $this->info('✅ Payslip generated successfully!');
            $this->info("Period: {$payslip->period_name}");
            $this->info("Total Hours: {$payslip->total_hours}");
            $this->info("Regular Hours: {$payslip->regular_hours}");
            $this->info("Overtime Hours: {$payslip->overtime_hours}");
            $this->info("Gross Salary: ₱{$payslip->gross_salary}");
            $this->info("Total Deductions: ₱{$payslip->total_deductions}");
            $this->info("Net Salary: ₱{$payslip->net_salary}");
            
        } catch (\Exception $e) {
            $this->error("Failed to generate payslip: {$e->getMessage()}");
            return 1;
        }
        
        $this->info('✅ Payroll system test completed successfully!');
        $this->info('');
        $this->info('Next steps:');
        $this->info('1. Access admin portal: /admin/payslips');
        $this->info('2. Access professor portal: /professor/payslips');
        $this->info('3. Ensure faculty have employment_type and salary grades assigned');
        
        return 0;
    }
    
    private function createSampleAttendance($faculty)
    {
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();
        
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            // Skip weekends
            if ($date->isWeekend()) {
                continue;
            }
            
            // Create attendance record
            $timeIn = $date->copy()->setTime(8, rand(0, 30), 0); // 8:00-8:30 AM
            $timeOut = $date->copy()->setTime(17, rand(0, 30), 0); // 5:00-5:30 PM
            
            $totalHours = $timeIn->diffInMinutes($timeOut) / 60;
            
            // Determine status
            $status = 'present';
            if ($timeIn->hour >= 8 && $timeIn->minute > 15) {
                $status = 'late';
            }
            
            Attendance::create([
                'professor_id' => $faculty->id,
                'date' => $date->toDateString(),
                'time_in' => $timeIn,
                'time_out' => $timeOut,
                'total_hours' => $totalHours,
                'status' => $status,
            ]);
        }
        
        $this->info('Sample attendance data created for current month.');
    }
}
