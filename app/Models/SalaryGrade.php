<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SalaryGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'grade',
        'full_time_base_salary',
        'part_time_base_salary',
        'department',
    ];

    protected $casts = [
        'full_time_base_salary' => 'decimal:2',
        'part_time_base_salary' => 'decimal:2',
    ];

    protected $appends = [
        'formatted_full_time_base_salary',
        'formatted_part_time_base_salary',
    ];

    /**
     * The faculties that belong to this salary grade.
     */
    public function faculties(): BelongsToMany
    {
        return $this->belongsToMany(Faculty::class, 'faculty_salary_grade')
                    ->withPivot(['effective_date', 'end_date', 'notes'])
                    ->withTimestamps();
    }

    /**
     * Scope a query to find by grade.
     */
    public function scopeByGrade($query, $grade)
    {
        return $query->where('grade', $grade);
    }

    /**
     * Scope a query to filter by department.
     */
    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Get formatted full-time base salary.
     */
    public function getFormattedFullTimeBaseSalaryAttribute()
    {
        return $this->full_time_base_salary ? '₱' . number_format($this->full_time_base_salary, 2) : 'Not set';
    }

    /**
     * Get formatted part-time base salary.
     */
    public function getFormattedPartTimeBaseSalaryAttribute()
    {
        return $this->part_time_base_salary ? '₱' . number_format($this->part_time_base_salary, 2) : 'Not set';
    }

    /**
     * Get base salary for employment type.
     */
    public function getBaseSalaryForEmploymentType($employmentType)
    {
        return $employmentType === 'Full-Time' ? $this->full_time_base_salary : $this->part_time_base_salary;
    }

    /**
     * Check if this salary grade is currently assigned to any faculty.
     */
    public function isAssignedToFaculty()
    {
        return $this->faculties()->where(function($query) {
            $query->whereNull('faculty_salary_grade.end_date')
                  ->orWhere('faculty_salary_grade.end_date', '>', now());
        })->exists();
    }

    /**
     * Get the number of faculty members currently assigned to this salary grade.
     */
    public function getActiveFacultyCountAttribute()
    {
        return $this->faculties()->where(function($query) {
            $query->whereNull('faculty_salary_grade.end_date')
                  ->orWhere('faculty_salary_grade.end_date', '>', now());
        })->count();
    }

    /**
     * Validation rules for creating/updating salary grades.
     */
    public static function rules($id = null, $department = null)
    {
        $gradeRule = 'required|integer|min:1|max:99';
        if ($department) {
            $gradeRule .= '|unique:salary_grades,grade,' . $id . ',id,department,' . $department;
        } else {
            $gradeRule .= '|unique:salary_grades,grade,' . $id;
        }

        return [
            'grade' => $gradeRule,
            'full_time_base_salary' => 'required|numeric|min:0|max:9999999.99',
            'part_time_base_salary' => 'required|numeric|min:0|max:9999999.99',
            'department' => 'required|string|max:255',
        ];
    }

    /**
     * Get salary grade by grade.
     */
    public static function getByGrade($grade)
    {
        return self::byGrade($grade)->first();
    }

    /**
     * Get salary grades with faculty count.
     */
    public static function getWithFacultyCount()
    {
        return self::withCount(['faculties' => function($query) {
            $query->where(function($q) {
                $q->whereNull('faculty_salary_grade.end_date')
                  ->orWhere('faculty_salary_grade.end_date', '>', now());
            });
        }])->get();
    }

    /**
     * Check if a grade-step combination already exists.
     */
    public static function gradeStepExists($grade, $step, $excludeId = null)
    {
        $query = self::where('grade', $grade)->where('step', $step);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    /**
     * Get salary grades for dropdown selection.
     */
    public static function getForDropdown($department = null)
    {
        $query = self::orderBy('grade');
        
        if ($department) {
            $query->byDepartment($department);
        }
        
        return $query->get()
            ->mapWithKeys(function ($grade) {
                return [$grade->id => "Grade {$grade->grade} - FT:₱" . number_format($grade->full_time_base_salary, 2) . "/hr, PT:₱" . number_format($grade->part_time_base_salary, 2) . "/hr"];
            });
    }

    /**
     * Calculate salary based on attendance for a specific month.
     */
    public function calculateSalaryWithAttendance($facultyId, $year, $month)
    {
        $attendance = \App\Models\Attendance::where('faculty_id', $facultyId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        $totalHours = $attendance->sum('total_hours');
        $workingDays = $attendance->where('status', '!=', 'absent')->count();
        $lateDays = $attendance->where('status', 'late')->count();
        $earlyDepartureDays = $attendance->where('status', 'early_departure')->count();
        $halfDays = $attendance->where('status', 'half_day')->count();

        // Calculate deductions for late arrivals and early departures
        $lateDeduction = $lateDays * 0.1; // 10% deduction per late day
        $earlyDeduction = $earlyDepartureDays * 0.1; // 10% deduction per early departure
        $halfDayDeduction = $halfDays * 0.5; // 50% deduction per half day

        $totalDeduction = $lateDeduction + $earlyDeduction + $halfDayDeduction;

        // Calculate final salary
        $baseSalary = $this->base_salary;
        $allowance = $this->allowance;
        $totalSalary = $baseSalary + $allowance;

        // Apply deductions
        $finalSalary = $totalSalary * (1 - $totalDeduction);

        return [
            'base_salary' => $baseSalary,
            'allowance' => $allowance,
            'total_salary' => $totalSalary,
            'total_hours' => $totalHours,
            'working_days' => $workingDays,
            'late_days' => $lateDays,
            'early_departure_days' => $earlyDepartureDays,
            'half_days' => $halfDays,
            'deductions' => $totalDeduction,
            'final_salary' => $finalSalary,
            'formatted_final_salary' => '₱' . number_format($finalSalary, 2)
        ];
    }

    /**
     * Get total hours worked for current month.
     */
    public function getCurrentMonthTotalHours($facultyId)
    {
        return \App\Models\Attendance::where('faculty_id', $facultyId)
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->sum('total_hours');
    }

    /**
     * Get total hours worked for a specific period.
     */
    public function getTotalHoursForPeriod($facultyId, $year, $month)
    {
        return \App\Models\Attendance::where('faculty_id', $facultyId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->sum('total_hours');
    }

    /**
     * Get attendance summary for current month.
     */
    public function getCurrentMonthAttendanceSummary($facultyId)
    {
        $attendance = \App\Models\Attendance::where('faculty_id', $facultyId)
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->get();

        return [
            'total_records' => $attendance->count(),
            'present_days' => $attendance->where('status', 'present')->count(),
            'late_days' => $attendance->where('status', 'late')->count(),
            'early_departure_days' => $attendance->where('status', 'early_departure')->count(),
            'half_days' => $attendance->where('status', 'half_day')->count(),
            'absent_days' => $attendance->where('status', 'absent')->count(),
            'total_hours' => $attendance->sum('total_hours'),
            'average_hours_per_day' => $attendance->count() > 0 ? $attendance->sum('total_hours') / $attendance->count() : 0
        ];
    }

    /**
     * Get attendance summary for a specific period.
     */
    public function getAttendanceSummaryForPeriod($facultyId, $year, $month)
    {
        $attendance = \App\Models\Attendance::where('faculty_id', $facultyId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        return [
            'total_records' => $attendance->count(),
            'present_days' => $attendance->where('status', 'present')->count(),
            'late_days' => $attendance->where('status', 'late')->count(),
            'early_departure_days' => $attendance->where('status', 'early_departure')->count(),
            'half_days' => $attendance->where('status', 'half_day')->count(),
            'absent_days' => $attendance->where('status', 'absent')->count(),
            'total_hours' => $attendance->sum('total_hours'),
            'average_hours_per_day' => $attendance->count() > 0 ? $attendance->sum('total_hours') / $attendance->count() : 0
        ];
    }

    /**
     * Calculate adjusted salary for current month based on attendance.
     */
    public function getCurrentMonthAdjustedSalary($facultyId)
    {
        return $this->calculateSalaryWithAttendance($facultyId, now()->year, now()->month);
    }

    /**
     * Calculate adjusted salary for a specific period based on attendance.
     */
    public function getAdjustedSalaryForPeriod($facultyId, $year, $month)
    {
        return $this->calculateSalaryWithAttendance($facultyId, $year, $month);
    }
}
