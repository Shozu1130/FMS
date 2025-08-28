<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeachingHistory extends Model
{
    use HasFactory;

    protected $table = 'teaching_histories';

    protected $fillable = [
        'faculty_id',
        'course_code',
        'course_title',
        'semester',
        'academic_year',
        'units',
        'schedule',
        'start_time',
        'end_time',
        'room',
        'number_of_students',
        'rating',
        'remarks',
        'is_active',
    ];

    protected $casts = [
        'academic_year' => 'integer',
        'units' => 'integer',
        'number_of_students' => 'integer',
        'rating' => 'decimal:2',
        'is_active' => 'boolean',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    protected $appends = [
        'formatted_schedule',
        'time_slot',
        'student_load',
    ];

    /**
     * Get the faculty that owns the teaching history.
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Get the evaluations for the teaching history.
     */
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    /**
     * Scope a query to only include active teaching assignments.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query by academic year and semester.
     */
    public function scopeByPeriod($query, $academicYear, $semester)
    {
        return $query->where('academic_year', $academicYear)
                    ->where('semester', $semester);
    }

    /**
     * Scope a query to include only current semester assignments.
     */
    public function scopeCurrent($query)
    {
        $currentYear = date('Y');
        $currentSemester = $this->getCurrentSemester();
        
        return $query->where('academic_year', $currentYear)
                    ->where('semester', $currentSemester)
                    ->where('is_active', true);
    }

    /**
     * Get formatted schedule with time.
     */
    public function getFormattedScheduleAttribute()
    {
        if (!$this->schedule || !$this->start_time || !$this->end_time) {
            return $this->schedule;
        }

        return $this->schedule . ' ' . $this->start_time->format('h:i A') . ' - ' . $this->end_time->format('h:i A');
    }

    /**
     * Get time slot.
     */
    public function getTimeSlotAttribute()
    {
        if (!$this->start_time || !$this->end_time) {
            return null;
        }

        return $this->start_time->format('h:i A') . ' - ' . $this->end_time->format('h:i A');
    }

    /**
     * Get student load (units * number of students).
     */
    public function getStudentLoadAttribute()
    {
        return $this->units * $this->number_of_students;
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

    /**
     * Validation rules for creating/updating teaching history.
     */
    public static function rules($id = null)
    {
        return [
            'faculty_id' => 'required|exists:faculty,id',
            'course_code' => 'required|string|max:20',
            'course_title' => 'required|string|max:200',
            'semester' => 'required|in:1st Semester,2nd Semester,Summer',
            'academic_year' => 'required|integer|min:2000|max:2100',
            'units' => 'required|integer|min:1|max:10',
            'schedule' => 'nullable|in:MWF,TTH,MW,TTHS,F,S',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:50',
            'number_of_students' => 'required|integer|min:0|max:500',
            'rating' => 'nullable|numeric|min:1|max:5',
            'remarks' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ];
    }
}
