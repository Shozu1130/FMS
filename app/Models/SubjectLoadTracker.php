<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class SubjectLoadTracker extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'faculty_id',
        'subject_code',
        'subject_name',
        'section',
        'year_level',
        'units',
        'hours_per_week',
        'schedule_day',
        'start_time',
        'end_time',
        'room',
        'academic_year',
        'semester',
        'status',

        'source',

        'notes'
    ];

    protected $casts = [

        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',

        'start_time' => 'string',
        'end_time' => 'string',

        'units' => 'integer',
        'hours_per_week' => 'integer',
        'academic_year' => 'integer',
    ];

    protected $appends = [
        'schedule_display',
        'time_range',
        'total_load_display'
    ];

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_COMPLETED = 'completed';


    // Source constants
    const SOURCE_DIRECT = 'direct';
    const SOURCE_SUBJECT_LOAD_TRACKER = 'subject_load_tracker';


    // Day constants
    const DAYS = [
        'monday' => 'Monday',
        'tuesday' => 'Tuesday', 
        'wednesday' => 'Wednesday',
        'thursday' => 'Thursday',
        'friday' => 'Friday',
        'saturday' => 'Saturday',
        'sunday' => 'Sunday'
    ];

    // Semester constants
    const SEMESTERS = [
        '1st Semester' => '1st Semester',
        '2nd Semester' => '2nd Semester',
        'Summer' => 'Summer'
    ];

    // Year Level constants
    const YEAR_LEVELS = [
        '1st Year' => '1st Year',
        '2nd Year' => '2nd Year',
        '3rd Year' => '3rd Year',
        '4th Year' => '4th Year',
        '5th Year' => '5th Year'
    ];

    /**
     * Get the faculty that owns the subject load.
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Get schedule display format.
     */
    public function getScheduleDisplayAttribute()
    {
        return ucfirst($this->schedule_day) . ' ' . $this->time_range;
    }

    /**
     * Get time range display.
     */
    public function getTimeRangeAttribute()
    {
        if ($this->start_time && $this->end_time) {

            return Carbon::parse($this->start_time)->format('g:i A') . ' - ' . 
                   Carbon::parse($this->end_time)->format('g:i A');

            try {
                $startTime = Carbon::createFromFormat('H:i', $this->start_time);
                $endTime = Carbon::createFromFormat('H:i', $this->end_time);
                return $startTime->format('g:i A') . ' - ' . $endTime->format('g:i A');
            } catch (\Exception $e) {
                return 'Invalid time format';
            }

        }
        return 'No schedule';
    }

    /**
     * Get total load display.
     */
    public function getTotalLoadDisplayAttribute()
    {
        return $this->units . ' units (' . $this->hours_per_week . ' hrs/week)';
    }

    /**
     * Check for duplicate subject assignment.
     */
    public static function hasDuplicateAssignment($facultyId, $subjectCode, $section, $academicYear, $semester, $excludeId = null)
    {
        $query = self::where('faculty_id', $facultyId)
                    ->where('subject_code', $subjectCode)
                    ->where('section', $section)
                    ->where('academic_year', $academicYear)
                    ->where('semester', $semester)
                    ->where('status', self::STATUS_ACTIVE);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Check for schedule conflicts.
     */
    public static function hasScheduleConflict($facultyId, $scheduleDay, $startTime, $endTime, $academicYear, $semester, $excludeId = null)
    {
        $query = self::where('faculty_id', $facultyId)
                    ->where('schedule_day', $scheduleDay)
                    ->where('academic_year', $academicYear)
                    ->where('semester', $semester)
                    ->where('status', self::STATUS_ACTIVE)
                    ->where(function($q) use ($startTime, $endTime) {
                        $q->where(function($subQ) use ($startTime, $endTime) {
                            // New schedule starts during existing schedule
                            $subQ->where('start_time', '<=', $startTime)
                                 ->where('end_time', '>', $startTime);
                        })->orWhere(function($subQ) use ($startTime, $endTime) {
                            // New schedule ends during existing schedule
                            $subQ->where('start_time', '<', $endTime)
                                 ->where('end_time', '>=', $endTime);
                        })->orWhere(function($subQ) use ($startTime, $endTime) {
                            // New schedule encompasses existing schedule
                            $subQ->where('start_time', '>=', $startTime)
                                 ->where('end_time', '<=', $endTime);
                        });
                    });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->first();
    }

    /**
     * Get faculty total units for a period.
     */
    public static function getFacultyTotalUnits($facultyId, $academicYear, $semester)
    {
        return self::where('faculty_id', $facultyId)
                  ->where('academic_year', $academicYear)
                  ->where('semester', $semester)
                  ->where('status', self::STATUS_ACTIVE)
                  ->sum('units');
    }

    /**
     * Get faculty total hours for a period.
     */
    public static function getFacultyTotalHours($facultyId, $academicYear, $semester)
    {
        return self::where('faculty_id', $facultyId)
                  ->where('academic_year', $academicYear)
                  ->where('semester', $semester)
                  ->where('status', self::STATUS_ACTIVE)
                  ->sum('hours_per_week');
    }

    /**
     * Scope for active loads.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for specific academic period.
     */
    public function scopeForPeriod($query, $academicYear, $semester)
    {
        return $query->where('academic_year', $academicYear)
                    ->where('semester', $semester);
    }

    /**
     * Scope for specific faculty.
     */
    public function scopeForFaculty($query, $facultyId)
    {
        return $query->where('faculty_id', $facultyId);
    }

    /**
     * Get all available days.
     */
    public static function getDays()
    {
        return self::DAYS;
    }

    /**
     * Get all available semesters.
     */
    public static function getSemesters()
    {
        return self::SEMESTERS;
    }

    /**
     * Get all available year levels.
     */
    public static function getYearLevels()
    {
        return self::YEAR_LEVELS;
    }

    /**
     * Get status options.
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_COMPLETED => 'Completed'
        ];
    }

    /**
     * Validation rules.
     */
    public static function rules($id = null)
    {
        return [
            'faculty_id' => 'required|exists:faculties,id',
            'subject_code' => 'required|string|max:20',
            'subject_name' => 'required|string|max:255',
            'section' => 'required|string|max:10',
            'year_level' => 'required|in:' . implode(',', array_keys(self::YEAR_LEVELS)),
            'units' => 'required|integer|min:1|max:6',
            'hours_per_week' => 'required|integer|min:1|max:40',
            'schedule_day' => 'required|in:' . implode(',', array_keys(self::DAYS)),
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:50',
            'academic_year' => 'required|integer|min:2000|max:2100',
            'semester' => 'required|in:' . implode(',', array_keys(self::SEMESTERS)),
            'status' => 'required|in:' . implode(',', [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_COMPLETED]),


            'source' => 'required|in:' . implode(',', [self::SOURCE_DIRECT, self::SOURCE_SUBJECT_LOAD_TRACKER]),

            'notes' => 'nullable|string|max:1000'
        ];
    }

    /**
     * Get validation messages.
     */
    public static function validationMessages()
    {
        return [
            'faculty_id.required' => 'Please select a faculty member.',
            'faculty_id.exists' => 'Selected faculty member does not exist.',
            'subject_code.required' => 'Subject code is required.',
            'subject_name.required' => 'Subject name is required.',
            'section.required' => 'Section is required.',
            'year_level.required' => 'Year level is required.',
            'year_level.in' => 'Please select a valid year level.',
            'units.required' => 'Units are required.',
            'units.min' => 'Units must be at least 1.',
            'units.max' => 'Units cannot exceed 6.',
            'hours_per_week.required' => 'Hours per week is required.',
            'schedule_day.required' => 'Schedule day is required.',
            'start_time.required' => 'Start time is required.',
            'end_time.required' => 'End time is required.',
            'end_time.after' => 'End time must be after start time.',
            'academic_year.required' => 'Academic year is required.',
            'semester.required' => 'Semester is required.'
        ];
    }
}