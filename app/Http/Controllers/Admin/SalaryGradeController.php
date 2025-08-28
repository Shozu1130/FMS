<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalaryGrade;
use Illuminate\Http\Request;

class SalaryGradeController extends Controller
{
    public function index()
    {
        $grades = SalaryGrade::orderBy('grade')->orderBy('step')->get();
        return view('admin.salary_grades.index', compact('grades'));
    }

    public function create()
    {
        return view('admin.salary_grades.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'grade' => 'required|integer|min:1',
            'step' => 'nullable|integer|min:1',
            'base_salary' => 'required|numeric|min:0',
            'allowance' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
        $validated['step'] = $validated['step'] ?? 1;
        $validated['allowance'] = $validated['allowance'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active');

        SalaryGrade::create($validated);
        return redirect()->route('admin.salary_grades.index')->with('success', 'Salary grade created.');
    }

    public function edit(SalaryGrade $salary_grade)
    {
        return view('admin.salary_grades.edit', ['grade' => $salary_grade]);
    }

    public function update(Request $request, SalaryGrade $salary_grade)
    {
        $validated = $request->validate([
            'grade' => 'required|integer|min:1',
            'step' => 'nullable|integer|min:1',
            'base_salary' => 'required|numeric|min:0',
            'allowance' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
        $validated['step'] = $validated['step'] ?? 1;
        $validated['allowance'] = $validated['allowance'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active');

        $salary_grade->update($validated);
        return redirect()->route('admin.salary_grades.index')->with('success', 'Salary grade updated.');
    }

    public function destroy(SalaryGrade $salary_grade)
    {
        $salary_grade->delete();
        return redirect()->route('admin.salary_grades.index')->with('success', 'Salary grade deleted.');
    }
}



