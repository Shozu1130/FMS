<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\Payslip;
use App\Models\SalaryGrade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PayslipController extends Controller
{
    /**
     * Display a listing of payslips.
     */
    public function index(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        
        $payslips = Payslip::with('faculty')
            ->forPeriod($year, $month)
            ->orderBy('net_salary', 'desc')
            ->paginate(15);

        $totalPayroll = Payslip::forPeriod($year, $month)->sum('net_salary');
        $totalFaculty = Payslip::forPeriod($year, $month)->count();
        
        $fullTimeCount = Payslip::forPeriod($year, $month)
            ->where('employment_type', 'Full-Time')->count();
        $partTimeCount = Payslip::forPeriod($year, $month)
            ->where('employment_type', 'Part-Time')->count();

        return view('admin.payslips.index', compact(
            'payslips', 'year', 'month', 'totalPayroll', 
            'totalFaculty', 'fullTimeCount', 'partTimeCount'
        ));
    }

    /**
     * Show salary calculation details for a specific faculty.
     */
    public function show($id)
    {
        $payslip = Payslip::with('faculty')->findOrFail($id);
        $faculty = $payslip->faculty;
        $salaryGrade = $faculty->getCurrentSalaryGrade();
        
        // Get attendance details for the month
        $attendances = $faculty->attendances()
            ->whereYear('date', $payslip->year)
            ->whereMonth('date', $payslip->month)
            ->orderBy('date')
            ->get();

        return view('admin.payslips.show', compact('payslip', 'faculty', 'salaryGrade', 'attendances'));
    }

    /**
     * Generate payslips for all faculty for a specific month.
     */
    public function generateAll(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:2030',
            'month' => 'required|integer|min:1|max:12'
        ]);

        $year = $request->year;
        $month = $request->month;
        
        $faculties = Faculty::whereNotNull('employment_type')->get();
        $generated = 0;
        $errors = [];

        foreach ($faculties as $faculty) {
            try {
                Payslip::generateForFaculty($faculty->id, $year, $month);
                $generated++;
            } catch (\Exception $e) {
                $errors[] = "Error generating payslip for {$faculty->name}: " . $e->getMessage();
            }
        }

        if ($generated > 0) {
            $message = "Successfully generated {$generated} payslips for " . Carbon::createFromDate($year, $month)->format('F Y');
            if (!empty($errors)) {
                $message .= " with " . count($errors) . " errors.";
            }
            return redirect()->route('admin.payslips.index', ['year' => $year, 'month' => $month])
                ->with('success', $message)
                ->with('errors', $errors);
        }

        return redirect()->back()->with('error', 'No payslips were generated. Please check faculty employment types and salary grades.');
    }

    /**
     * Generate payslip for a specific faculty.
     */
    public function generateSingle(Request $request)
    {
        $request->validate([
            'faculty_id' => 'required|exists:faculty,id',
            'year' => 'required|integer|min:2020|max:2030',
            'month' => 'required|integer|min:1|max:12'
        ]);

        try {
            $payslip = Payslip::generateForFaculty($request->faculty_id, $request->year, $request->month);
            $faculty = Faculty::find($request->faculty_id);
            
            return redirect()->route('admin.payslips.show', $payslip->id)
                ->with('success', "Payslip generated successfully for {$faculty->name}");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error generating payslip: ' . $e->getMessage());
        }
    }

    /**
     * Show salary calculation breakdown for all faculty.
     */
    public function calculations(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        
        $faculties = Faculty::with(['attendances' => function($query) use ($year, $month) {
                $query->whereYear('date', $year)->whereMonth('date', $month);
            }])
            ->whereNotNull('employment_type')
            ->get();

        $calculationData = [];
        
        foreach ($faculties as $faculty) {
            $salaryGrade = $faculty->getCurrentSalaryGrade();
            if (!$salaryGrade) continue;

            $attendances = $faculty->attendances;
            $totalHours = $attendances->sum('total_hours');
            $presentDays = $attendances->where('status', 'present')->count();
            $lateDays = $attendances->where('status', 'late')->count();
            $absentDays = $attendances->where('status', 'absent')->count();

            $hourlyRate = $faculty->employment_type === 'Full-Time' 
                ? $salaryGrade->full_time_hourly_rate 
                : $salaryGrade->part_time_hourly_rate;

            $standardHours = $salaryGrade->standard_hours_per_month ?? 160;
            $regularHours = min($totalHours, $standardHours);
            $overtimeHours = max(0, $totalHours - $standardHours);

            $baseSalary = $regularHours * ($hourlyRate ?? 0);
            $overtimePay = $overtimeHours * ($hourlyRate ?? 0) * ($salaryGrade->overtime_multiplier ?? 1.25);
            $grossSalary = $baseSalary + $overtimePay + ($salaryGrade->allowance ?? 0);

            $lateDeductions = $lateDays * (($hourlyRate ?? 0) * 0.5);
            $absenceDeductions = $absentDays * (($hourlyRate ?? 0) * 8);
            $totalDeductions = $lateDeductions + $absenceDeductions;
            $netSalary = $grossSalary - $totalDeductions;

            $calculationData[] = [
                'faculty' => $faculty,
                'salary_grade' => $salaryGrade,
                'hourly_rate' => $hourlyRate,
                'total_hours' => $totalHours,
                'regular_hours' => $regularHours,
                'overtime_hours' => $overtimeHours,
                'base_salary' => $baseSalary,
                'overtime_pay' => $overtimePay,
                'gross_salary' => $grossSalary,
                'late_deductions' => $lateDeductions,
                'absence_deductions' => $absenceDeductions,
                'total_deductions' => $totalDeductions,
                'net_salary' => $netSalary,
                'present_days' => $presentDays,
                'late_days' => $lateDays,
                'absent_days' => $absentDays
            ];
        }

        // Sort by net salary descending
        usort($calculationData, function($a, $b) {
            return $b['net_salary'] <=> $a['net_salary'];
        });

        return view('admin.payslips.calculations', compact('calculationData', 'year', 'month'));
    }

    /**
     * Finalize payslip.
     */
    public function finalize($id)
    {
        $payslip = Payslip::findOrFail($id);
        $payslip->finalize();

        return redirect()->back()->with('success', 'Payslip finalized successfully.');
    }

    /**
     * Mark payslip as paid.
     */
    public function markPaid($id)
    {
        $payslip = Payslip::findOrFail($id);
        $payslip->markAsPaid();

        return redirect()->back()->with('success', 'Payslip marked as paid.');
    }

    /**
     * Bulk finalize payslips for a month.
     */
    public function bulkFinalize(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer'
        ]);

        $updated = Payslip::forPeriod($request->year, $request->month)
            ->where('status', 'draft')
            ->update([
                'status' => 'finalized',
                'finalized_at' => now()
            ]);

        return redirect()->back()->with('success', "Finalized {$updated} payslips.");
    }
}
