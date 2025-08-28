<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Ensure this line is present only once

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

        Log::info('Fetched Salary Grades:', $salaryGrades->toArray());

        return view('professor.salary_grades.index', compact('salaryGrades', 'professor'));
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
