<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ScheduleAssignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'professor_id',
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
        'notes',
        'source' // To identify if this came from SubjectLoadTracker or direct assignment
    ];

    protected $casts = [
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
     * Get the faculty that owns the schedule assignment.
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class, 'professor_id');
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
     * Check for schedule conflicts across both ScheduleAssignment and SubjectLoadTracker.
     */
    public static function hasScheduleConflict($professorId, $scheduleDay, $startTime, $endTime, $academicYear, $semester, $excludeId = null, $excludeTable = 'schedule_assignments')
    {
        // Check conflicts in ScheduleAssignment table
        $scheduleConflict = null;
        if ($excludeTable !== 'schedule_assignments') {
            $query = self::where('professor_id', $professorId)
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

            if ($excludeId && $excludeTable === 'schedule_assignments') {
                $query->where('id', '!=', $excludeId);
            }

            $scheduleConflict = $query->first();
        }

        // Check conflicts in SubjectLoadTracker table
        $subjectLoadConflict = null;
        if ($excludeTable !== 'subject_load_trackers') {
            $subjectLoadConflict = SubjectLoadTracker::hasScheduleConflict(
                $professorId, 
                $scheduleDay, 
                $startTime, 
                $endTime, 
                $academicYear, 
                $semester, 
                $excludeTable === 'subject_load_trackers' ? $excludeId : null
            );
        }

        // Return the first conflict found
        return $scheduleConflict ?: $subjectLoadConflict;
    }

    /**
     * Check for duplicate subject assignment across both tables.
     */
    public static function hasDuplicateAssignment($professorId, $subjectCode, $section, $academicYear, $semester, $excludeId = null, $excludeTable = 'schedule_assignments')
    {
        // Check duplicates in ScheduleAssignment table
        $scheduleDuplicate = false;
        if ($excludeTable !== 'schedule_assignments') {
            $query = self::where('professor_id', $professorId)
                        ->where('subject_code', $subjectCode)
                        ->where('section', $section)
                        ->where('academic_year', $academicYear)
                        ->where('semester', $semester)
                        ->where('status', self::STATUS_ACTIVE);

            if ($excludeId && $excludeTable === 'schedule_assignments') {
                $query->where('id', '!=', $excludeId);
            }

            $scheduleDuplicate = $query->exists();
        }

        // Check duplicates in SubjectLoadTracker table
        $subjectLoadDuplicate = false;
        if ($excludeTable !== 'subject_load_trackers') {
            $subjectLoadDuplicate = SubjectLoadTracker::hasDuplicateAssignment(
                $professorId,
                $subjectCode,
                $section,
                $academicYear,
                $semester,
                $excludeTable === 'subject_load_trackers' ? $excludeId : null
            );
        }

        return $scheduleDuplicate || $subjectLoadDuplicate;
    }

    /**
     * Get faculty total units for a period from both tables.
     */
    public static function getFacultyTotalUnits($professorId, $academicYear, $semester)
    {
        $scheduleUnits = self::where('professor_id', $professorId)
                            ->where('academic_year', $academicYear)
                            ->where('semester', $semester)
                            ->where('status', self::STATUS_ACTIVE)
                            ->sum('units');

        $subjectLoadUnits = SubjectLoadTracker::getFacultyTotalUnits($professorId, $academicYear, $semester);

        return $scheduleUnits + $subjectLoadUnits;
    }

    /**
     * Get faculty total hours for a period from both tables.
     */
    public static function getFacultyTotalHours($professorId, $academicYear, $semester)
    {
        $scheduleHours = self::where('professor_id', $professorId)
                            ->where('academic_year', $academicYear)
                            ->where('semester', $semester)
                            ->where('status', self::STATUS_ACTIVE)
                            ->sum('hours_per_week');

        $subjectLoadHours = SubjectLoadTracker::getFacultyTotalHours($professorId, $academicYear, $semester);

        return $scheduleHours + $subjectLoadHours;
    }

    /**
     * Get combined schedule data from both tables.
     */
    public static function getCombinedScheduleData($filters = [])
    {
        // Get data from ScheduleAssignment
        $scheduleQuery = self::with('faculty')
                            ->select('*', \DB::raw("'Schedule Assignment' as source_table"))
                            ->where('status', self::STATUS_ACTIVE);

        // Get data from SubjectLoadTracker
        $subjectLoadQuery = SubjectLoadTracker::with('faculty')
                                             ->select('*', \DB::raw("'Subject Load Tracker' as source_table"))
                                             ->where('status', SubjectLoadTracker::STATUS_ACTIVE);

        // Apply filters to both queries
        if (!empty($filters['professor_id'])) {
            $scheduleQuery->where('professor_id', $filters['professor_id']);
            $subjectLoadQuery->where('professor_id', $filters['professor_id']);
        }

        if (!empty($filters['academic_year'])) {
            $scheduleQuery->where('academic_year', $filters['academic_year']);
            $subjectLoadQuery->where('academic_year', $filters['academic_year']);
        }

        if (!empty($filters['semester'])) {
            $scheduleQuery->where('semester', $filters['semester']);
            $subjectLoadQuery->where('semester', $filters['semester']);
        }

        if (!empty($filters['status'])) {
            $scheduleQuery->where('status', $filters['status']);
            $subjectLoadQuery->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $scheduleQuery->where(function($q) use ($search) {
                $q->where('subject_code', 'like', "%{$search}%")
                  ->orWhere('subject_name', 'like', "%{$search}%")
                  ->orWhere('section', 'like', "%{$search}%")
                  ->orWhereHas('faculty', function($fq) use ($search) {
                      $fq->where('name', 'like', "%{$search}%");
                  });
            });
            
            $subjectLoadQuery->where(function($q) use ($search) {
                $q->where('subject_code', 'like', "%{$search}%")
                  ->orWhere('subject_name', 'like', "%{$search}%")
                  ->orWhere('section', 'like', "%{$search}%")
                  ->orWhereHas('faculty', function($fq) use ($search) {
                      $fq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Get results from both tables
        $scheduleResults = $scheduleQuery->get();
        $subjectLoadResults = $subjectLoadQuery->get();

        // Combine and sort by faculty name, then by schedule
        $combined = $scheduleResults->concat($subjectLoadResults);
        
        return $combined->sortBy([
            ['faculty.name', 'asc'],
            ['schedule_day', 'asc'],
            ['start_time', 'asc']
        ]);
    }

    /**
     * Scope for active assignments.
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
    public function scopeForFaculty($query, $professorId)
    {
        return $query->where('professor_id', $professorId);
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
     * Get source options.
     */
    public static function getSourceOptions()
    {
        return [
            self::SOURCE_DIRECT => 'Direct Assignment',
            self::SOURCE_SUBJECT_LOAD_TRACKER => 'Subject Load Tracker'
        ];
    }

    /**
     * Validation rules.
     */
    public static function rules($id = null)
    {
        return [
            'professor_id' => 'required|exists:faculties,id',
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
            'professor_id.required' => 'Please select a faculty member.',
            'professor_id.exists' => 'Selected faculty member does not exist.',
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
            'semester.required' => 'Semester is required.',
            'source.required' => 'Source is required.'
        ];
    }

    /**
     * Get workload status based on total hours.
     */
    public static function getWorkloadStatus($totalHours)
    {
        if ($totalHours > 40) {
            return ['status' => 'overloaded', 'label' => 'Overloaded', 'class' => 'danger'];
        } elseif ($totalHours >= 30) {
            return ['status' => 'full_load', 'label' => 'Full Load', 'class' => 'success'];
        } else {
            return ['status' => 'partial_load', 'label' => 'Partial Load', 'class' => 'warning'];
        }
    }

    /**
     * Get faculty load summary including both tables.
     */
    public static function getFacultyLoadSummary($professorId, $academicYear, $semester)
    {
        $totalUnits = self::getFacultyTotalUnits($professorId, $academicYear, $semester);
        $totalHours = self::getFacultyTotalHours($professorId, $academicYear, $semester);
        $workloadStatus = self::getWorkloadStatus($totalHours);

        // Get assignments from both tables
        $scheduleAssignments = self::where('professor_id', $professorId)
                                  ->where('academic_year', $academicYear)
                                  ->where('semester', $semester)
                                  ->where('status', self::STATUS_ACTIVE)
                                  ->get();

        $subjectLoadAssignments = SubjectLoadTracker::where('professor_id', $professorId)
                                                   ->where('academic_year', $academicYear)
                                                   ->where('semester', $semester)
                                                   ->where('status', SubjectLoadTracker::STATUS_ACTIVE)
                                                   ->get();

        return [
            'total_units' => $totalUnits,
            'total_hours' => $totalHours,
            'workload_status' => $workloadStatus,
            'schedule_assignments' => $scheduleAssignments,
            'subject_load_assignments' => $subjectLoadAssignments,
            'total_assignments' => $scheduleAssignments->count() + $subjectLoadAssignments->count()
        ];
    }

    /**
     * Get calendar data for a specific period.
     */
    public static function getCalendarData($academicYear, $semester, $professorId = null, $yearLevel = null)
    {
        $scheduleQuery = self::with('faculty')
                            ->where('academic_year', $academicYear)
                            ->where('semester', $semester)
                            ->where('status', self::STATUS_ACTIVE);

        $subjectLoadQuery = SubjectLoadTracker::with('faculty')
                                             ->where('academic_year', $academicYear)
                                             ->where('semester', $semester)
                                             ->where('status', SubjectLoadTracker::STATUS_ACTIVE);

        if ($professorId) {
            $scheduleQuery->where('professor_id', $professorId);
            $subjectLoadQuery->where('professor_id', $professorId);
        }

        if ($yearLevel) {
            $scheduleQuery->where('year_level', $yearLevel);
            $subjectLoadQuery->where('year_level', $yearLevel);
        }

        $scheduleData = $scheduleQuery->get();
        $subjectLoadData = $subjectLoadQuery->get();

        $calendar = [];
        
        // Process ScheduleAssignment data
        foreach ($scheduleData as $assignment) {
            $day = $assignment->schedule_day;
            
            if (!isset($calendar[$day])) {
                $calendar[$day] = [];
            }
            
            $calendar[$day][] = [
                'id' => $assignment->id,
                'subject_code' => $assignment->subject_code,
                'subject_name' => $assignment->subject_name,
                'section' => $assignment->section,
                'faculty_name' => $assignment->faculty->name ?? 'Unknown Faculty',
                'time_range' => $assignment->time_range,
                'room' => $assignment->room,
                'source' => 'Schedule Assignment',
                'color' => 'primary'
            ];
        }

        // Process SubjectLoadTracker data
        foreach ($subjectLoadData as $load) {
            $day = $load->schedule_day;
            
            if (!isset($calendar[$day])) {
                $calendar[$day] = [];
            }
            
            $calendar[$day][] = [
                'id' => $load->id,
                'subject_code' => $load->subject_code,
                'subject_name' => $load->subject_name,
                'section' => $load->section,
                'faculty_name' => $load->faculty->name ?? 'Unknown Faculty',
                'time_range' => $load->time_range,
                'room' => $load->room,
                'source' => 'Subject Load Tracker',
                'color' => 'success'
            ];
        }

        // Sort each day's assignments by start time
        foreach ($calendar as $day => $assignments) {
            usort($calendar[$day], function($a, $b) {
                $timeA = explode(' - ', $a['time_range'])[0];
                $timeB = explode(' - ', $b['time_range'])[0];
                return strtotime($timeA) - strtotime($timeB);
            });
        }

        return $calendar;
    }

    /**
     * Get dashboard statistics.
     */
    public static function getDashboardStats($academicYear = null, $semester = null)
    {
        $currentYear = $academicYear ?: date('Y');
        $currentSemester = $semester ?: self::getCurrentSemester();

        // Combined statistics from both tables
        $scheduleAssignments = self::where('academic_year', $currentYear)
                                  ->where('semester', $currentSemester);

        $subjectLoadAssignments = SubjectLoadTracker::where('academic_year', $currentYear)
                                                   ->where('semester', $currentSemester);

        $totalAssignments = $scheduleAssignments->count() + $subjectLoadAssignments->count();
        $activeAssignments = $scheduleAssignments->where('status', 'active')->count() + 
                           $subjectLoadAssignments->where('status', 'active')->count();

        // Get faculty with assignments
        $facultyWithSchedules = self::where('academic_year', $currentYear)
                                   ->where('semester', $currentSemester)
                                   ->where('status', 'active')
                                   ->distinct('professor_id')
                                   ->count('professor_id');

        $facultyWithSubjectLoads = SubjectLoadTracker::where('academic_year', $currentYear)
                                                    ->where('semester', $currentSemester)
                                                    ->where('status', 'active')
                                                    ->distinct('professor_id')
                                                    ->count('professor_id');

        $activeFaculty = max($facultyWithSchedules, $facultyWithSubjectLoads);

        // Calculate total units and hours
        $totalUnits = $scheduleAssignments->where('status', 'active')->sum('units') + 
                     $subjectLoadAssignments->where('status', 'active')->sum('units');

        $totalHours = $scheduleAssignments->where('status', 'active')->sum('hours_per_week') + 
                     $subjectLoadAssignments->where('status', 'active')->sum('hours_per_week');

        // Check for conflicts and overloaded faculty
        $conflicts = self::getConflictCount($currentYear, $currentSemester);
        $overloadedFaculty = self::getOverloadedFacultyCount($currentYear, $currentSemester);

        return [
            'total_assignments' => $totalAssignments,
            'active_assignments' => $activeAssignments,
            'active_faculty' => $activeFaculty,
            'total_units' => $totalUnits,
            'total_hours' => $totalHours,
            'conflicts' => $conflicts,
            'overloaded_faculty' => $overloadedFaculty
        ];
    }

    /**
     * Get conflict count for the period.
     */
    private static function getConflictCount($academicYear, $semester)
    {
        $conflicts = 0;
        
        // Get all active faculty for the period
        $facultyIds = collect();
        
        $scheduleAssignments = self::where('academic_year', $academicYear)
                                  ->where('semester', $semester)
                                  ->where('status', 'active')
                                  ->pluck('professor_id');
        
        $subjectLoadAssignments = SubjectLoadTracker::where('academic_year', $academicYear)
                                                   ->where('semester', $semester)
                                                   ->where('status', 'active')
                                                   ->pluck('professor_id');
        
        $facultyIds = $scheduleAssignments->merge($subjectLoadAssignments)->unique();

        foreach ($facultyIds as $professorId) {
            $facultySchedules = self::getCombinedScheduleData([
                'professor_id' => $professorId,
                'academic_year' => $academicYear,
                'semester' => $semester,
                'status' => 'active'
            ]);

            // Check for time conflicts within this faculty's schedule
            $schedulesByDay = $facultySchedules->groupBy('schedule_day');
            
            foreach ($schedulesByDay as $day => $daySchedules) {
                for ($i = 0; $i < $daySchedules->count() - 1; $i++) {
                    for ($j = $i + 1; $j < $daySchedules->count(); $j++) {
                        $schedule1 = $daySchedules[$i];
                        $schedule2 = $daySchedules[$j];
                        
                        if (self::timesOverlap($schedule1->start_time, $schedule1->end_time, 
                                             $schedule2->start_time, $schedule2->end_time)) {
                            $conflicts++;
                        }
                    }
                }
            }
        }

        return $conflicts;
    }

    /**
     * Get overloaded faculty count.
     */
    private static function getOverloadedFacultyCount($academicYear, $semester)
    {
        $overloadedCount = 0;
        
        $facultyIds = collect();
        
        $scheduleAssignments = self::where('academic_year', $academicYear)
                                  ->where('semester', $semester)
                                  ->where('status', 'active')
                                  ->pluck('professor_id');
        
        $subjectLoadAssignments = SubjectLoadTracker::where('academic_year', $academicYear)
                                                   ->where('semester', $semester)
                                                   ->where('status', 'active')
                                                   ->pluck('professor_id');
        
        $facultyIds = $scheduleAssignments->merge($subjectLoadAssignments)->unique();

        foreach ($facultyIds as $professorId) {
            $totalHours = self::getFacultyTotalHours($professorId, $academicYear, $semester);
            if ($totalHours > 40) {
                $overloadedCount++;
            }
        }

        return $overloadedCount;
    }

    /**
     * Check if two time ranges overlap.
     */
    private static function timesOverlap($start1, $end1, $start2, $end2)
    {
        $start1 = Carbon::parse($start1);
        $end1 = Carbon::parse($end1);
        $start2 = Carbon::parse($start2);
        $end2 = Carbon::parse($end2);

        return $start1->lt($end2) && $start2->lt($end1);
    }

    /**
     * Get current semester based on current date.
     */
    private static function getCurrentSemester()
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