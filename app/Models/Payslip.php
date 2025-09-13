<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Payslip extends Model
{
    use HasFactory;

    protected $fillable = [
        'professor_id',
        'year',
        'month',
        'employment_type',
        'base_salary',
        'total_hours',
        'total_deductions',
        'net_salary',
        'present_days',
        'absent_days',
        'late_days',
        'attendance_summary',
        'status',
        'generated_at',
        'finalized_at',
        'paid_at'
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'total_hours' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'attendance_summary' => 'array',
        'generated_at' => 'datetime',
        'finalized_at' => 'datetime',
        'paid_at' => 'datetime'
    ];

    protected $appends = [
        'formatted_net_salary',
        'formatted_base_salary',
        'period_name',
        'status_badge'
    ];

    /**
     * Get the faculty that owns the payslip.
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class, 'professor_id');
    }

    /**
     * Get formatted net salary.
     */
    public function getFormattedNetSalaryAttribute()
    {
        return '₱' . number_format($this->net_salary, 2);
    }

    /**
     * Get formatted base salary.
     */
    public function getFormattedBaseSalaryAttribute()
    {
        return '₱' . number_format($this->base_salary, 2);
    }

    /**
     * Get period name (e.g., "January 2024").
     */
    public function getPeriodNameAttribute()
    {
        return Carbon::createFromDate($this->year, $this->month, 1)->format('F Y');
    }

    /**
     * Get status badge with color.
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => '<span class="badge bg-secondary">Draft</span>',
            'finalized' => '<span class="badge bg-primary">Finalized</span>',
            'paid' => '<span class="badge bg-success">Paid</span>'
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    /**
     * Generate payslip for a faculty member for a specific month.
     */
    public static function generateForFaculty($professorId, $year, $month)
    {
        $faculty = Faculty::findOrFail($professorId);
        $salaryGrade = $faculty->getCurrentSalaryGrade();
        
        if (!$salaryGrade) {
            throw new \Exception('Faculty member does not have a current salary grade assigned.');
        }

        // Get attendance records for the month
        $attendances = Attendance::where('professor_id', $professorId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        // Calculate attendance summary
        $totalHours = $attendances->sum('total_hours');
        $presentDays = $attendances->where('status', 'present')->count();
        $lateDays = $attendances->where('status', 'late')->count();
        $absentDays = $attendances->where('status', 'absent')->count();

        // Get base salary from faculty employment type
        $employmentType = $faculty->employment_type;
        $baseSalary = $salaryGrade->getBaseSalaryForEmploymentType($employmentType);

        if (!$baseSalary) {
            throw new \Exception("Base salary not set for {$employmentType} employment type in salary grade.");
        }

        // Calculate deductions based on attendance
        $lateDeduction = $lateDays * 500; // ₱500 deduction per late day
        $absenceDeduction = $absentDays * 1000; // ₱1000 deduction per absent day
        $totalDeductions = $lateDeduction + $absenceDeduction;

        $netSalary = $baseSalary - $totalDeductions;

        // Prepare attendance summary
        $attendanceSummary = [
            'total_records' => $attendances->count(),
            'present_days' => $presentDays,
            'late_days' => $lateDays,
            'absent_days' => $absentDays,
            'total_hours' => $totalHours,
            'average_hours_per_day' => $attendances->count() > 0 ? $totalHours / $attendances->count() : 0
        ];

        // Create or update payslip
        return self::updateOrCreate(
            [
                'professor_id' => $professorId,
                'year' => $year,
                'month' => $month
            ],
            [
                'employment_type' => $employmentType,
                'base_salary' => $baseSalary,
                'total_hours' => $totalHours,
                'total_deductions' => $totalDeductions,
                'net_salary' => $netSalary,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'late_days' => $lateDays,
                'attendance_summary' => $attendanceSummary,
                'generated_at' => now()
            ]
        );
    }

    /**
     * Finalize the payslip.
     */
    public function finalize()
    {
        $this->update([
            'status' => 'finalized',
            'finalized_at' => now()
        ]);
    }

    /**
     * Mark payslip as paid.
     */
    public function markAsPaid()
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now()
        ]);
    }

    /**
     * Scope for current month payslips.
     */
    public function scopeCurrentMonth($query)
    {
        return $query->where('year', now()->year)
                    ->where('month', now()->month);
    }

    /**
     * Scope for specific period.
     */
    public function scopeForPeriod($query, $year, $month)
    {
        return $query->where('year', $year)->where('month', $month);
    }

    /**
     * Scope for specific faculty.
     */
    public function scopeForFaculty($query, $professorId)
    {
        return $query->where('professor_id', $professorId);
    }

    /**
     * Get all payslips for a specific month with faculty details.
     */
    public static function getMonthlyPayslipsWithFaculty($year, $month)
    {
        return self::with('faculty')
            ->forPeriod($year, $month)
            ->orderBy('net_salary', 'desc')
            ->get();
    }
}
