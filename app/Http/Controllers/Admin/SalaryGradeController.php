<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalaryGrade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SalaryGradeController extends Controller
{
    public function index()
    {
        $salaryGrades = SalaryGrade::withCount(['faculties' => function($query) {
            $query->where(function($q) {
                $q->whereNull('faculty_salary_grade.end_date')
                  ->orWhere('faculty_salary_grade.end_date', '>', now());
            });
        }])->orderBy('grade')->get();
        
        return view('admin.salary-grades.index', compact('salaryGrades'));
    }

    public function create()
    {
        return view('admin.salary-grades.create');
    }

    public function store(Request $request)
    {
        try {
            Log::info('Salary Grade Store Request', [
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
                'headers' => $request->headers->all()
            ]);

            $validated = $request->validate(SalaryGrade::rules());

            Log::info('Validated Data', $validated);

            $salaryGrade = SalaryGrade::create($validated);

            Log::info('Salary Grade Created', ['id' => $salaryGrade->id]);

            return redirect()->route('admin.salary-grades.index')->with('success', 'Salary grade created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation Error', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Salary Grade Creation Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create salary grade: ' . $e->getMessage());
        }
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
        return redirect()->route('admin.salary-grades.index')->with('success', 'Salary grade updated.');
    }

    public function destroy(SalaryGrade $salary_grade)
    {
        $salary_grade->delete();
        return redirect()->route('admin.salary-grades.index')->with('success', 'Salary grade deleted.');
    }
}
