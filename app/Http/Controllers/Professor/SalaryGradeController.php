<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Ensure this line is present only once
use PDF; // Add this import for PDF generation

class SalaryGradeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $professor = Auth::guard('faculty')->user();
        $salaryGrades = $professor->salaryGrades()
            ->orderBy('faculty_salary_grade.effective_date', 'desc')
            ->get();

        // Get current salary grade for attendance calculations
        $currentSalaryGrade = $professor->getCurrentSalaryGrade();

        // Initialize attendance data
        $currentMonthAttendance = null;
        $currentMonthSalaryCalculation = null;
        $totalHoursCurrentMonth = 0;

        if ($currentSalaryGrade) {
            // Get current month attendance summary
            $currentMonthAttendance = $currentSalaryGrade->getCurrentMonthAttendanceSummary($professor->id);

            // Get current month salary calculation with attendance adjustments
            $currentMonthSalaryCalculation = $currentSalaryGrade->getCurrentMonthAdjustedSalary($professor->id);

            // Get total hours for current month
            $totalHoursCurrentMonth = $currentSalaryGrade->getCurrentMonthTotalHours($professor->id);
        }

        Log::info('Fetched Salary Grades:', $salaryGrades->toArray());
        Log::info('Current Month Attendance:', $currentMonthAttendance ?? []);
        Log::info('Current Month Salary Calculation:', $currentMonthSalaryCalculation ?? []);

        return view('professor.salary_grades.index', compact(
            'salaryGrades',
            'professor',
            'currentSalaryGrade',
            'currentMonthAttendance',
            'currentMonthSalaryCalculation',
            'totalHoursCurrentMonth'
        ));
    }

    /**
     * Generate and download payslip PDF for the current salary period.
     */
    public function downloadPayslip()
    {
        $professor = Auth::guard('faculty')->user();
        $currentSalaryGrade = $professor->getCurrentSalaryGrade();

        if (!$currentSalaryGrade) {
            return redirect()->route('professor.salary_grades.index')->with('error', 'No current salary grade found.');
        }

        // Generate payslip data
        $attendanceSummary = $currentSalaryGrade->getCurrentMonthAttendanceSummary($professor->id);
        $salaryCalculation = $currentSalaryGrade->getCurrentMonthAdjustedSalary($professor->id);

        // Use a PDF generation library like Dompdf (barryvdh/laravel-dompdf)
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('professor.salary_grades.payslip', [
            'professor' => $professor,
            'salaryGrade' => $currentSalaryGrade,
            'attendanceSummary' => $attendanceSummary,
            'salaryCalculation' => $salaryCalculation,
        ]);

        // Store the PDF for faster access (optional)
        $pdfPath = storage_path('app/payslips/' . $professor->id . '-' . now()->format('Y-m') . '.pdf');
        $pdf->save($pdfPath);

        // Return the PDF as a download response
        return $pdf->download('payslip-' . now()->format('Y-m') . '.pdf');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('professor.salary_grades.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'grade' => 'required|integer|min:1|max:99',
            'step' => 'required|integer|min:1|max:99',
            'base_salary' => 'required|numeric|min:0|max:9999999.99',
            'allowance' => 'nullable|numeric|min:0|max:9999999.99',
            'notes' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        $professor = Auth::guard('faculty')->user();

        $salaryGrade = \App\Models\SalaryGrade::create([
            'grade' => $request->grade,
            'step' => $request->step,
            'base_salary' => $request->base_salary,
            'allowance' => $request->allowance,
            'notes' => $request->notes,
            'is_active' => $request->has('is_active') ? $request->is_active : false,
        ]);

        $professor->salaryGrades()->attach($salaryGrade->id, [
            'effective_date' => now(),
            'notes' => $request->notes,
            'is_current' => true,
        ]);

        return redirect()->route('professor.salary_grades.index')->with('success', 'Salary grade created and assigned successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $professor = Auth::guard('faculty')->user();
        $salaryGrade = $professor->salaryGrades()
            ->where('salary_grades.id', $id)
            ->firstOrFail();

        return view('professor.salary_grades.show', compact('salaryGrade', 'professor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // This might not be needed for professors as salary grades are typically assigned by admin
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // This might not be needed for professors as salary grades are typically assigned by admin
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // This might not be needed for professors as salary grades are typically assigned by admin
        abort(404);
    }
}
