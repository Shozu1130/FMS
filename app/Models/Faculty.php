<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use App\Models\Clearance;
use App\Models\TeachingHistory;
use App\Models\Evaluation;
use App\Models\SalaryGrade;
use App\Models\ScheduleAssignment;

class Faculty extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'faculties';
    
    protected $fillable = [
        'professor_id',
        'name',
        'email',
        'password',
        'status',
        'employment_type',
        'department',
        'picture',
        'skills',
        'experiences'
        //'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    protected $rememberTokenName = ['remember_token'];
    
    /**
     * Generate automatic Professor ID (PROF-YYYY-0001)
     */
    public static function generateProfessorId()
    {
        $currentYear = date('Y');
        
        $lastProfessor = self::withTrashed()
                            ->where('professor_id', 'like', 'PROF-' . $currentYear . '-%')
                            ->orderBy('professor_id', 'desc')
                            ->first();

        if ($lastProfessor && !empty($lastProfessor->professor_id)) {
            $parts = explode('-', $lastProfessor->professor_id);
            if (count($parts) >= 3 && is_numeric(end($parts))) {
                $lastNumber = (int) end($parts);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
        } else {
            $newNumber = 1;
        }

        $proposedId = 'PROF-' . $currentYear . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        
        while (self::withTrashed()->where('professor_id', $proposedId)->exists()) {
            $newNumber++;
            $proposedId = 'PROF-' . $currentYear . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        }

        return $proposedId;
    }

    public function teachingHistories()
    {
        return $this->hasMany(TeachingHistory::class, 'professor_id');
    }

    public function clearances()
    {
        return $this->hasMany(Clearance::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'professor_id');
    }

    public function salaryGrades()
    {
        return $this->belongsToMany(SalaryGrade::class, 'faculty_salary_grade', 'professor_id', 'salary_grade_id')
                    ->withPivot(['effective_date', 'end_date', 'notes', 'is_current'])
                    ->withTimestamps();
    }

    public function currentSalaryGrade()
    {
        return $this->salaryGrades()
                    ->wherePivot('is_current', true)
                    ->orWhere(function($query) {
                        $query->whereNull('faculty_salary_grade.end_date')
                              ->orWhere('faculty_salary_grade.end_date', '>', now());
                    })
                    ->orderBy('faculty_salary_grade.effective_date', 'desc');
    }

    public function getCurrentSalaryGrade()
    {
        Log::info('Current Salary Grade requested.');

        $currentGrade = $this->currentSalaryGrade()->first();
        return ($currentGrade && is_object($currentGrade)) ? $currentGrade : null;
    }

    public function currentTeachingAssignments()
    {
        $currentYear = date('Y');
        $currentSemester = TeachingHistory::getCurrentSemesterStatic();

        return $this->teachingHistories()
                    ->where('academic_year', $currentYear)
                    ->where('semester', $currentSemester)
                    ->where('is_active', true)
                    ->get();
    }

    public function validClearances()
    {
        return $this->clearances()
                    ->where('is_cleared', true)
                    ->where(function($query) {
                        $query->whereNull('expiration_date')
                              ->orWhere('expiration_date', '>', now());
                    })
                    ->get();
    }

    public function getOverallRatingAverage()
    {
        return $this->evaluations()
                    ->where('is_published', true)
                    ->avg('overall_rating');
    }

    public function recentEvaluations()
    {
        $twoYearsAgo = date('Y') - 2;
        return $this->evaluations()
                    ->where('academic_year', '>=', $twoYearsAgo)
                    ->where('is_published', true)
                    ->orderBy('academic_year', 'desc')
                    ->orderBy('semester', 'desc')
                    ->get();
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'professor_id');
    }

    public function payslips()
    {
        return $this->hasMany(Payslip::class, 'professor_id');
    }

    public function user()
    {
        return $this;
    }

    public function clearanceRequests()
    {
        return $this->hasMany(ClearanceRequest::class, 'professor_id');
    }

    public function subjectLoads()
    {
        return $this->hasMany(SubjectLoadTracker::class, 'professor_id');
    }

    public function scheduleAssignments()
    {
        return $this->hasMany(ScheduleAssignment::class, 'professor_id');
    }

    public function qualifications()
    {
        return $this->hasMany(ProfessorQualification::class, 'professor_id');
    }

    public function getCurrentMonthAttendance()
    {
        return $this->attendances()
                    ->whereYear('date', now()->year)
                    ->whereMonth('date', now()->month)
                    ->get();
    }

    public function getCurrentMonthHours()
    {
        return $this->attendances()
                    ->whereYear('date', now()->year)
                    ->whereMonth('date', now()->month)
                    ->sum('total_hours');
    }

    private function getCurrentSemester()
    {
        $month = date('n');
        
        if ($month >= 1 && $month <= 5) {
            return '2nd Semester';
        } elseif ($month >= 6 && $month <= 10) {
            return '1st Semester';
        } else {
            return 'Summer';
        }
    }

    /**
     * Get department enum
     */
    public function getDepartmentEnum(): ?\App\Enums\Department
    {
        return $this->department ? \App\Enums\Department::from($this->department) : null;
    }
}
