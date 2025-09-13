<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalaryGrade;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalaryGradeController extends Controller
{
    public function index()
    {
        $salaryGradesQuery = SalaryGrade::withCount(['faculties' => function($query) {
            $query->where(function($q) {
                $q->whereNull('faculty_salary_grade.end_date')
                  ->orWhere('faculty_salary_grade.end_date', '>', now());
            });
            // Filter by department if not master admin
            if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
                $query->where('department', auth()->user()->department);
            }
        }]);
        
        // Filter salary grades by department for non-master admins
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $salaryGradesQuery->byDepartment(auth()->user()->department);
        }
        
        $salaryGrades = $salaryGradesQuery->orderBy('grade')->get();
        
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

            // Create validation rules without requiring department field
            $rules = [
                'grade' => 'required|integer|min:1|max:99|unique:salary_grades,grade,NULL,id,department,' . auth()->user()->department,
                'full_time_base_salary' => 'required|numeric|min:0|max:9999999.99',
                'part_time_base_salary' => 'required|numeric|min:0|max:9999999.99',
            ];

            $validated = $request->validate($rules);
            
            // Automatically set department from logged-in admin
            $validated['department'] = auth()->user()->department;

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
        $rules = [
            'grade' => 'required|integer|min:1|max:99|unique:salary_grades,grade,' . $salary_grade->id . ',id,department,' . auth()->user()->department,
            'full_time_base_salary' => 'required|numeric|min:0|max:9999999.99',
            'part_time_base_salary' => 'required|numeric|min:0|max:9999999.99',
        ];

        $validated = $request->validate($rules);
        
        // Keep the existing department (don't allow changing it)
        $validated['department'] = $salary_grade->department;

        $salary_grade->update($validated);
        return redirect()->route('admin.salary-grades.index')->with('success', 'Salary grade updated successfully.');
    }

    public function show(SalaryGrade $salary_grade)
    {
        $facultyQuery = $salary_grade->faculties()->where(function($query) {
            $query->whereNull('faculty_salary_grade.end_date')
                  ->orWhere('faculty_salary_grade.end_date', '>', now());
        });
        
        // Filter by department
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $facultyQuery->where('department', auth()->user()->department);
        }
        
        $facultyCount = $facultyQuery->count();
        $facultyMembers = $facultyQuery->with('faculty')->get();

        return view('admin.salary-grades.show', compact('salary_grade', 'facultyCount', 'facultyMembers'))->with('salaryGrade', $salary_grade);
    }

    public function faculty(SalaryGrade $salary_grade)
    {
        $facultyQuery = $salary_grade->faculties()->where(function($query) {
            $query->whereNull('faculty_salary_grade.end_date')
                  ->orWhere('faculty_salary_grade.end_date', '>', now());
        });
        
        // Filter by department
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $facultyQuery->where('department', auth()->user()->department);
        }
        
        $facultyMembers = $facultyQuery->with('faculty')->get();

        $facultyCount = $facultyMembers->count();
        $fullTimeCount = $facultyMembers->where('faculty.employment_type', 'Full-Time')->count();
        $partTimeCount = $facultyMembers->where('faculty.employment_type', 'Part-Time')->count();
        
        $averageMonthlyPay = 0;
        if ($facultyCount > 0) {
            $totalPay = $facultyMembers->sum(function($assignment) use ($salary_grade) {
                return $assignment->faculty->employment_type === 'Full-Time' 
                    ? $salary_grade->full_time_base_salary * 160
                    : $salary_grade->part_time_base_salary * 160;
            });
            $averageMonthlyPay = $totalPay / $facultyCount;
        }

        return view('admin.salary-grades.faculty', compact('salary_grade', 'facultyMembers', 'facultyCount', 'fullTimeCount', 'partTimeCount', 'averageMonthlyPay'))->with('salaryGrade', $salary_grade);
    }

    public function assign()
    {
        // Filter faculties by department
        $facultiesQuery = Faculty::orderBy('name');
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $facultiesQuery->where('department', auth()->user()->department);
        }
        $faculties = $facultiesQuery->get();
        
        // Filter salary grades by department for non-master admins
        $salaryGradesQuery = SalaryGrade::orderBy('grade');
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $salaryGradesQuery->byDepartment(auth()->user()->department);
        }
        $salaryGrades = $salaryGradesQuery->get();
        
        $currentAssignmentsQuery = DB::table('faculty_salary_grade')
            ->join('faculties', 'faculty_salary_grade.professor_id', '=', 'faculties.id')
            ->join('salary_grades', 'faculty_salary_grade.salary_grade_id', '=', 'salary_grades.id')
            ->where(function($query) {
                $query->whereNull('faculty_salary_grade.end_date')
                      ->orWhere('faculty_salary_grade.end_date', '>', now());
            });
            
        // Filter by department
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $currentAssignmentsQuery->where('faculties.department', auth()->user()->department);
        }
        
        $currentAssignments = $currentAssignmentsQuery
            ->select('faculty_salary_grade.*', 'faculties.name as faculty_name', 'faculties.professor_id', 'faculties.employment_type', 'salary_grades.grade')
            ->orderBy('salary_grades.grade')
            ->get()
            ->map(function($assignment) {
                return (object)[
                    'id' => $assignment->id,
                    'effective_date' => \Carbon\Carbon::parse($assignment->effective_date),
                    'faculty' => (object)[
                        'name' => $assignment->faculty_name,
                        'professor_id' => $assignment->professor_id,
                        'employment_type' => $assignment->employment_type
                    ],
                    'salaryGrade' => (object)[
                        'grade' => $assignment->grade,
                        'full_time_base_salary' => SalaryGrade::find($assignment->salary_grade_id)->full_time_base_salary,
                        'part_time_base_salary' => SalaryGrade::find($assignment->salary_grade_id)->part_time_base_salary,
                    ]
                ];
            });

        return view('admin.salary-grades.assign', compact('faculties', 'salaryGrades', 'currentAssignments'));
    }

    public function assignStore(Request $request)
    {
        $request->validate([
            'professor_id' => 'required|exists:faculties,id',
            'salary_grade_id' => 'required|exists:salary_grades,id',
            'effective_date' => 'required|date',
            'notes' => 'nullable|string|max:500'
        ]);

        // Check if faculty already has an active assignment
        $existingAssignment = DB::table('faculty_salary_grade')
            ->where('professor_id', $request->professor_id)
            ->where(function($query) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>', now());
            })
            ->first();

        if ($existingAssignment) {
            // End the existing assignment
            DB::table('faculty_salary_grade')
                ->where('id', $existingAssignment->id)
                ->update([
                    'end_date' => now(),
                    'is_current' => false,
                    'updated_at' => now()
                ]);
        }

        // Create new assignment
        DB::table('faculty_salary_grade')->insert([
            'professor_id' => $request->professor_id,
            'salary_grade_id' => $request->salary_grade_id,
            'effective_date' => $request->effective_date,
            'notes' => $request->notes,
            'is_current' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $faculty = Faculty::find($request->professor_id);
        $salaryGrade = SalaryGrade::find($request->salary_grade_id);

        return redirect()->route('admin.salary-grades.assign')
            ->with('success', "Successfully assigned {$faculty->name} to Grade {$salaryGrade->grade}.");
    }

    public function assignRemove($assignmentId)
    {
        $assignment = DB::table('faculty_salary_grade')->where('id', $assignmentId)->first();
        
        if (!$assignment) {
            return redirect()->back()->with('error', 'Assignment not found.');
        }

        DB::table('faculty_salary_grade')
            ->where('id', $assignmentId)
            ->update([
                'end_date' => now(),
                'is_current' => false,
                'updated_at' => now()
            ]);

        return redirect()->back()->with('success', 'Faculty assignment removed successfully.');
    }

    public function destroy(SalaryGrade $salary_grade)
    {
        $salary_grade->delete();
        return redirect()->route('admin.salary-grades.index')->with('success', 'Salary grade deleted.');
    }
}
