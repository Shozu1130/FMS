<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use App\Models\Clearance;
use App\Models\TeachingHistory;
use App\Models\Evaluation;
use App\Models\SalaryGrade;

class Faculty extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'faculty';
    
    protected $fillable = [
        'professor_id',
        'name',
        'email', 
        'password',
        'status',
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
        
        // Get the highest existing ID for the current year
        $lastProfessor = self::where('professor_id', 'like', 'PROF-' . $currentYear . '-%')
                            ->orderBy('professor_id', 'desc')
                            ->first();

        if ($lastProfessor && !empty($lastProfessor->professor_id)) {
            // Extract the number part safely
            $parts = explode('-', $lastProfessor->professor_id);
            
            // Check if we have enough parts and the last part is numeric
            if (count($parts) >= 3 && is_numeric(end($parts))) {
                $lastNumber = (int) end($parts);
                $newNumber = $lastNumber + 1;
            } else {
                // Fallback: start from 1 if format is wrong
                $newNumber = 1;
            }
        } else {
            // First professor of the year
            $newNumber = 1;
        }

        return 'PROF-' . $currentYear . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the teaching histories for the faculty.
     */
    public function teachingHistories()
    {
        return $this->hasMany(TeachingHistory::class);
    }

    /**
     * Get the clearances for the faculty.
     */
    public function clearances()
    {
        return $this->hasMany(Clearance::class);
    }

    /**
     * Get the evaluations for the faculty.
     */
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    /**
     * Get the salary grades for the faculty.
     */
    public function salaryGrades()
    {
        return $this->belongsToMany(SalaryGrade::class, 'faculty_salary_grade')
                    ->withPivot(['effective_date', 'end_date', 'notes', 'is_current'])
                    ->withTimestamps();
    }

    /**
     * Get the current salary grade relationship for the faculty.
     */
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

    /**
     * Get the current salary grade model instance for the faculty.
     */
    public function getCurrentSalaryGrade()
    {
        Log::info('Current Salary Grade requested.');

        return $this->currentSalaryGrade()->first();
    }

    /**
     * Get active teaching assignments for current semester.
     */
    public function currentTeachingAssignments()
    {
        $currentYear = date('Y');
        $currentSemester = $this->getCurrentSemester();
        
        return $this->teachingHistories()
                    ->where('academic_year', $currentYear)
                    ->where('semester', $currentSemester)
                    ->where('is_active', true)
                    ->get();
    }

    /**
     * Get valid clearances (cleared and not expired).
     */
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

    /**
     * Get overall evaluation rating average.
     */
    public function getOverallRatingAverage()
    {
        return $this->evaluations()
                    ->where('is_published', true)
                    ->avg('overall_rating');
    }

    /**
     * Get recent evaluations (last 2 years).
     */
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

    /**
     * Determine current semester based on current date.
     */
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
}