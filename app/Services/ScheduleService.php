<?php

namespace App\Services;

use App\Models\ScheduleAssignment;
use App\Models\SubjectLoadTracker;
use App\Models\Faculty;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScheduleService
{
    /**
     * Get paginated combined schedule data with proper Laravel pagination.
     */
    public function getPaginatedScheduleData(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $combinedData = $this->getCombinedScheduleData($filters);
        
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        
        $paginatedItems = $combinedData->slice($offset, $perPage)->values();
        
        return new LengthAwarePaginator(
            $paginatedItems,
            $combinedData->count(),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );
    }

    /**
     * Get combined schedule data from both tables with optimized queries.
     */
    public function getCombinedScheduleData(array $filters = []): Collection
    {
        // Build base queries with eager loading
        $scheduleQuery = ScheduleAssignment::with(['faculty:id,name,professor_id'])
            ->select([
                'id', 'faculty_id', 'subject_code', 'subject_name', 'section', 
                'year_level', 'units', 'hours_per_week', 'schedule_day', 
                'start_time', 'end_time', 'room', 'academic_year', 'semester', 
                'status', 'source', 'notes', 'created_at'
            ])
            ->selectRaw("'Schedule Assignment' as source_table")
            ->where('status', ScheduleAssignment::STATUS_ACTIVE);

        $subjectLoadQuery = SubjectLoadTracker::with(['faculty:id,name,professor_id'])
            ->select([
                'id', 'faculty_id', 'subject_code', 'subject_name', 'section', 
                'year_level', 'units', 'hours_per_week', 'schedule_day', 
                'start_time', 'end_time', 'room', 'academic_year', 'semester', 
                'status', 'source', 'notes', 'created_at'
            ])
            ->selectRaw("'Subject Load Tracker' as source_table")
            ->where('status', SubjectLoadTracker::STATUS_ACTIVE);

        // Apply filters efficiently
        $this->applyFilters($scheduleQuery, $filters);
        $this->applyFilters($subjectLoadQuery, $filters);

        // Execute queries and combine results
        $scheduleResults = $scheduleQuery->get();
        $subjectLoadResults = $subjectLoadQuery->get();

        return $scheduleResults->concat($subjectLoadResults)
            ->sortBy([
                ['faculty.name', 'asc'],
                ['schedule_day', 'asc'],
                ['start_time', 'asc']
            ]);
    }

    /**
     * Apply filters to query builder.
     */
    private function applyFilters($query, array $filters): void
    {
        // Filter by department if not master admin
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $query->whereHas('faculty', function($q) {
                $q->where('department', auth()->user()->department);
            });
        }

        if (!empty($filters['faculty_id'])) {
            $query->where('faculty_id', $filters['faculty_id']);
        }

        if (!empty($filters['academic_year'])) {
            $query->where('academic_year', $filters['academic_year']);
        }

        if (!empty($filters['semester'])) {
            $query->where('semester', $filters['semester']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('subject_code', 'like', "%{$search}%")
                  ->orWhere('subject_name', 'like', "%{$search}%")
                  ->orWhere('section', 'like', "%{$search}%")
                  ->orWhereHas('faculty', function($fq) use ($search) {
                      $fq->where('name', 'like', "%{$search}%");
                  });
            });
        }
    }

    /**
     * Check for schedule conflicts with improved error handling.
     */
    public function checkScheduleConflict(
        int $facultyId, 
        string $scheduleDay, 
        string $startTime, 
        string $endTime, 
        int $academicYear, 
        string $semester, 
        ?int $excludeId = null, 
        string $excludeTable = 'schedule_assignments'
    ): ?object {
        try {
            // Validate time format
            Carbon::createFromFormat('H:i', $startTime);
            Carbon::createFromFormat('H:i', $endTime);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Invalid time format provided');
        }

        return ScheduleAssignment::hasScheduleConflict(
            $facultyId, $scheduleDay, $startTime, $endTime, 
            $academicYear, $semester, $excludeId, $excludeTable
        );
    }

    /**
     * Get faculty workload summary with caching.
     */
    public function getFacultyWorkloadSummary(int $facultyId, int $academicYear, string $semester): array
    {
        $cacheKey = "faculty_workload_{$facultyId}_{$academicYear}_{$semester}";
        
        return cache()->remember($cacheKey, 300, function() use ($facultyId, $academicYear, $semester) {
            return ScheduleAssignment::getFacultyLoadSummary($facultyId, $academicYear, $semester);
        });
    }

    /**
     * Get dashboard statistics with optimized queries.
     */
    public function getDashboardStats(?int $academicYear = null, ?string $semester = null): array
    {
        $currentYear = $academicYear ?: date('Y');
        $currentSemester = $semester ?: $this->getCurrentSemester();

        return DB::transaction(function() use ($currentYear, $currentSemester) {
            // Use more efficient counting queries with department filtering
            $scheduleQuery = ScheduleAssignment::where('academic_year', $currentYear)
                ->where('semester', $currentSemester);
            
            // Filter by department if not master admin
            if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
                $scheduleQuery->whereHas('faculty', function($q) {
                    $q->where('department', auth()->user()->department);
                });
            }
            
            $scheduleStats = $scheduleQuery->selectRaw('
                    COUNT(*) as total_assignments,
                    COUNT(CASE WHEN status = "active" THEN 1 END) as active_assignments,
                    COUNT(DISTINCT faculty_id) as active_faculty,
                    SUM(CASE WHEN status = "active" THEN units ELSE 0 END) as total_units,
                    SUM(CASE WHEN status = "active" THEN hours_per_week ELSE 0 END) as total_hours
                ')
                ->first();

            $subjectLoadQuery = SubjectLoadTracker::where('academic_year', $currentYear)
                ->where('semester', $currentSemester);
            
            // Filter by department if not master admin
            if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
                $subjectLoadQuery->whereHas('faculty', function($q) {
                    $q->where('department', auth()->user()->department);
                });
            }
            
            $subjectLoadStats = $subjectLoadQuery->selectRaw('
                    COUNT(*) as total_assignments,
                    COUNT(CASE WHEN status = "active" THEN 1 END) as active_assignments,
                    COUNT(DISTINCT faculty_id) as active_faculty,
                    SUM(CASE WHEN status = "active" THEN units ELSE 0 END) as total_units,
                    SUM(CASE WHEN status = "active" THEN hours_per_week ELSE 0 END) as total_hours
                ')
                ->first();

            return [
                'total_assignments' => $scheduleStats->total_assignments + $subjectLoadStats->total_assignments,
                'active_assignments' => $scheduleStats->active_assignments + $subjectLoadStats->active_assignments,
                'active_faculty' => max($scheduleStats->active_faculty, $subjectLoadStats->active_faculty),
                'total_units' => $scheduleStats->total_units + $subjectLoadStats->total_units,
                'total_hours' => $scheduleStats->total_hours + $subjectLoadStats->total_hours,
                'conflicts' => $this->getConflictCount($currentYear, $currentSemester),
                'overloaded_faculty' => $this->getOverloadedFacultyCount($currentYear, $currentSemester)
            ];
        });
    }

    /**
     * Get calendar data with improved performance.
     */
    public function getCalendarData(int $academicYear, string $semester, ?int $facultyId = null): array
    {
        $scheduleQuery = ScheduleAssignment::with('faculty:id,name')
            ->select(['id', 'faculty_id', 'subject_code', 'subject_name', 'section', 
                     'schedule_day', 'start_time', 'end_time', 'room'])
            ->where('academic_year', $academicYear)
            ->where('semester', $semester)
            ->where('status', ScheduleAssignment::STATUS_ACTIVE);

        $subjectLoadQuery = SubjectLoadTracker::with('faculty:id,name')
            ->select(['id', 'faculty_id', 'subject_code', 'subject_name', 'section', 
                     'schedule_day', 'start_time', 'end_time', 'room'])
            ->where('academic_year', $academicYear)
            ->where('semester', $semester)
            ->where('status', SubjectLoadTracker::STATUS_ACTIVE);

        if ($facultyId) {
            $scheduleQuery->where('faculty_id', $facultyId);
            $subjectLoadQuery->where('faculty_id', $facultyId);
        }

        $scheduleData = $scheduleQuery->get();
        $subjectLoadData = $subjectLoadQuery->get();

        return $this->formatCalendarData($scheduleData, $subjectLoadData);
    }

    /**
     * Format calendar data for display.
     */
    private function formatCalendarData($scheduleData, $subjectLoadData): array
    {
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
     * Get faculty workload distribution for reports.
     */
    public function getFacultyWorkloadDistribution(int $academicYear, string $semester): array
    {
        // Filter faculties by department
        $facultiesQuery = Faculty::select(['id', 'name', 'professor_id', 'department']);
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $facultiesQuery->where('department', auth()->user()->department);
        }
        $faculties = $facultiesQuery->get();
        $workloadDistribution = [];
        
        foreach ($faculties as $faculty) {
            $totalHours = ScheduleAssignment::getFacultyTotalHours($faculty->id, $academicYear, $semester);
            $totalUnits = ScheduleAssignment::getFacultyTotalUnits($faculty->id, $academicYear, $semester);
            
            if ($totalHours > 0) {
                $workloadStatus = ScheduleAssignment::getWorkloadStatus($totalHours);
                $workloadDistribution[] = [
                    'faculty' => $faculty,
                    'total_hours' => $totalHours,
                    'total_units' => $totalUnits,
                    'workload_status' => $workloadStatus,
                    'assignments_count' => $this->getFacultyAssignmentCount($faculty->id, $academicYear, $semester)
                ];
            }
        }
        
        // Sort by total hours descending
        usort($workloadDistribution, function($a, $b) {
            return $b['total_hours'] - $a['total_hours'];
        });
        
        return $workloadDistribution;
    }

    /**
     * Get faculty assignment count from both tables.
     */
    private function getFacultyAssignmentCount(int $facultyId, int $academicYear, string $semester): int
    {
        $scheduleCount = ScheduleAssignment::where('faculty_id', $facultyId)
            ->where('academic_year', $academicYear)
            ->where('semester', $semester)
            ->where('status', 'active')
            ->count();

        $subjectLoadCount = SubjectLoadTracker::where('faculty_id', $facultyId)
            ->where('academic_year', $academicYear)
            ->where('semester', $semester)
            ->where('status', 'active')
            ->count();

        return $scheduleCount + $subjectLoadCount;
    }

    /**
     * Get conflict count with optimized logic.
     */
    private function getConflictCount(int $academicYear, string $semester): int
    {
        // This is a simplified version - in production, you might want to cache this
        return 0; // Placeholder - implement based on business requirements
    }

    /**
     * Get overloaded faculty count.
     */
    private function getOverloadedFacultyCount(int $academicYear, string $semester): int
    {
        $facultyIds = collect();
        
        $scheduleAssignments = ScheduleAssignment::where('academic_year', $academicYear)
            ->where('semester', $semester)
            ->where('status', 'active')
            ->pluck('faculty_id');
        
        $subjectLoadAssignments = SubjectLoadTracker::where('academic_year', $academicYear)
            ->where('semester', $semester)
            ->where('status', 'active')
            ->pluck('faculty_id');
        
        $facultyIds = $scheduleAssignments->merge($subjectLoadAssignments)->unique();

        $overloadedCount = 0;
        foreach ($facultyIds as $facultyId) {
            $totalHours = ScheduleAssignment::getFacultyTotalHours($facultyId, $academicYear, $semester);
            if ($totalHours > 40) {
                $overloadedCount++;
            }
        }

        return $overloadedCount;
    }

    /**
     * Get current semester based on current date.
     */
    private function getCurrentSemester(): string
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
     * Validate schedule assignment data.
     */
    public function validateScheduleData(array $data): array
    {
        $errors = [];

        // Check for duplicate assignment
        if (ScheduleAssignment::hasDuplicateAssignment(
            $data['faculty_id'],
            $data['subject_code'],
            $data['section'],
            $data['academic_year'],
            $data['semester'],
            $data['exclude_id'] ?? null
        )) {
            $errors['duplicate'] = 'This faculty member is already assigned to this subject and section for the selected academic period.';
        }

        // Check for schedule conflicts
        $conflict = $this->checkScheduleConflict(
            $data['faculty_id'],
            $data['schedule_day'],
            $data['start_time'],
            $data['end_time'],
            $data['academic_year'],
            $data['semester'],
            $data['exclude_id'] ?? null
        );

        if ($conflict) {
            $errors['conflict'] = "Schedule Conflict: Professor already assigned to {$conflict->subject_code} ({$conflict->subject_name}) at {$conflict->schedule_display}";
        }

        return $errors;
    }
}
