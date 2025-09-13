<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\SubjectLoadTracker;
use App\Models\ScheduleAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ScheduleController extends Controller
{
    /**
     * Display the professor's combined schedule from both Subject Load Tracker and Schedule Assignment.
     */
    public function index(Request $request)
    {
        $faculty = Auth::guard('faculty')->user();
        
        // Get current academic period
        $currentYear = now()->year;
        $currentSemester = $this->getCurrentSemester();
        
        // Use request parameters or default to current period
        $academicYear = $request->get('academic_year', $currentYear);
        $semester = $request->get('semester', $currentSemester);
        $yearLevel = $request->get('year_level');
        
        // Get schedules from Subject Load Tracker
        $subjectLoads = SubjectLoadTracker::where('professor_id', $faculty->id)
            ->where('academic_year', $academicYear)
            ->where('semester', $semester)
            ->where('status', 'active')
            ->when($yearLevel, function($query) use ($yearLevel) {
                return $query->where('year_level', $yearLevel);
            })
            ->orderBy('schedule_day')
            ->orderBy('start_time')
            ->get();
        
        // Get schedules from Schedule Assignment
        $scheduleAssignments = ScheduleAssignment::where('professor_id', $faculty->id)
            ->where('academic_year', $academicYear)
            ->where('semester', $semester)
            ->where('status', 'active')
            ->when($yearLevel, function($query) use ($yearLevel) {
                return $query->where('year_level', $yearLevel);
            })
            ->orderBy('schedule_day')
            ->orderBy('start_time')
            ->get();
        
        // Combine both collections and add source information
        $subjectLoads->each(function ($item) {
            $item->source_name = 'Subject Load Tracker';
            $item->source_color = 'success';
        });
        
        $scheduleAssignments->each(function ($item) {
            $item->source_name = 'Schedule Assignment';
            $item->source_color = 'primary';
        });
        
        $allSchedules = $subjectLoads->concat($scheduleAssignments);
        
        // Group by day for calendar view
        $schedule = [];
        $days = SubjectLoadTracker::getDays();
        
        foreach ($days as $dayKey => $dayName) {
            $schedule[$dayKey] = $allSchedules->where('schedule_day', $dayKey)
                ->sortBy('start_time')
                ->values();
        }
        
        // Calculate summary statistics
        $summary = [
            'total_subjects' => $allSchedules->count(),
            'total_units' => $allSchedules->sum('units'),
            'total_hours' => $allSchedules->sum('hours_per_week'),
            'academic_year' => $academicYear,
            'semester' => $semester,
            'subject_load_count' => $subjectLoads->count(),
            'schedule_assignment_count' => $scheduleAssignments->count()
        ];
        
        // Get workload status
        $workloadStatus = ScheduleAssignment::getWorkloadStatus($summary['total_hours']);
        $summary['workload_status'] = $workloadStatus;
        
        // Check for schedule conflicts
        $conflicts = $this->checkScheduleConflicts($allSchedules);
        $summary['conflicts'] = $conflicts;
        
        // Get available academic years for filter
        $subjectLoadYears = SubjectLoadTracker::where('professor_id', $faculty->id)
            ->distinct()
            ->pluck('academic_year');
            
        $scheduleAssignmentYears = ScheduleAssignment::where('professor_id', $faculty->id)
            ->distinct()
            ->pluck('academic_year');
            
        $academicYears = $subjectLoadYears->merge($scheduleAssignmentYears)
            ->unique()
            ->sort()
            ->reverse();
        
        $semesters = SubjectLoadTracker::getSemesters();
        
        // Get available year levels for filter
        $subjectLoadYearLevels = SubjectLoadTracker::where('professor_id', $faculty->id)
            ->distinct()
            ->pluck('year_level');
            
        $scheduleAssignmentYearLevels = ScheduleAssignment::where('professor_id', $faculty->id)
            ->distinct()
            ->pluck('year_level');
            
        $availableYearLevels = $subjectLoadYearLevels->merge($scheduleAssignmentYearLevels)
            ->unique()
            ->filter()
            ->sort();
        
        $yearLevels = SubjectLoadTracker::getYearLevels();
        
        return view('professor.schedule.index', compact(
            'schedule',
            'summary',
            'academicYears',
            'semesters',
            'days',
            'allSchedules',
            'yearLevels',
            'availableYearLevels'
        ));
    }
    
    /**
     * Get calendar view of the professor's schedule.
     */
    public function calendar(Request $request)
    {
        $faculty = Auth::guard('faculty')->user();
        
        $academicYear = $request->get('academic_year', now()->year);
        $semester = $request->get('semester', $this->getCurrentSemester());
        $yearLevel = $request->get('year_level');
        
        // Get calendar data using the ScheduleAssignment model method
        $calendarData = ScheduleAssignment::getCalendarData($academicYear, $semester, $faculty->id, $yearLevel);
        
        // Get summary for the period
        $loadSummary = ScheduleAssignment::getFacultyLoadSummary($faculty->id, $academicYear, $semester);
        
        // Get available filters
        $subjectLoadYears = SubjectLoadTracker::where('professor_id', $faculty->id)
            ->distinct()
            ->pluck('academic_year');
            
        $scheduleAssignmentYears = ScheduleAssignment::where('professor_id', $faculty->id)
            ->distinct()
            ->pluck('academic_year');
            
        $academicYears = $subjectLoadYears->merge($scheduleAssignmentYears)
            ->unique()
            ->sort()
            ->reverse();
        
        $semesters = SubjectLoadTracker::getSemesters();
        $days = SubjectLoadTracker::getDays();
        
        // Get available year levels for filter
        $subjectLoadYearLevels = SubjectLoadTracker::where('professor_id', $faculty->id)
            ->distinct()
            ->pluck('year_level');
            
        $scheduleAssignmentYearLevels = ScheduleAssignment::where('professor_id', $faculty->id)
            ->distinct()
            ->pluck('year_level');
            
        $availableYearLevels = $subjectLoadYearLevels->merge($scheduleAssignmentYearLevels)
            ->unique()
            ->filter()
            ->sort();
        
        $yearLevels = SubjectLoadTracker::getYearLevels();
        
        return view('professor.schedule.calendar', compact(
            'calendarData',
            'loadSummary',
            'academicYears',
            'semesters',
            'days',
            'academicYear',
            'semester',
            'yearLevels',
            'availableYearLevels'
        ));
    }
    
    /**
     * Get detailed view of a specific schedule item.
     */
    public function show(Request $request, $type, $id)
    {
        $faculty = Auth::guard('faculty')->user();
        
        if ($type === 'subject-load') {
            $schedule = SubjectLoadTracker::findOrFail($id);
            $viewPath = 'professor.schedule.subject_load_detail';
        } elseif ($type === 'schedule-assignment') {
            $schedule = ScheduleAssignment::findOrFail($id);
            $viewPath = 'professor.schedule.schedule_assignment_detail';
        } else {
            abort(404, 'Invalid schedule type');
        }
        
        // Ensure the schedule belongs to the authenticated professor
        if ($schedule->professor_id !== $faculty->id) {
            abort(403, 'Unauthorized access to schedule.');
        }
        
        // Get other schedules for the same period
        $otherSchedules = collect();
        
        // Get from Subject Load Tracker
        $subjectLoads = SubjectLoadTracker::where('professor_id', $faculty->id)
            ->where('academic_year', $schedule->academic_year)
            ->where('semester', $schedule->semester)
            ->where('id', '!=', $type === 'subject-load' ? $id : null)
            ->get();
        
        // Get from Schedule Assignment
        $scheduleAssignments = ScheduleAssignment::where('professor_id', $faculty->id)
            ->where('academic_year', $schedule->academic_year)
            ->where('semester', $schedule->semester)
            ->where('id', '!=', $type === 'schedule-assignment' ? $id : null)
            ->get();
        
        $otherSchedules = $subjectLoads->concat($scheduleAssignments)
            ->sortBy(['schedule_day', 'start_time']);
        
        $periodSummary = [
            'total_subjects' => $otherSchedules->count() + 1,
            'total_units' => $otherSchedules->sum('units') + $schedule->units,
            'total_hours' => $otherSchedules->sum('hours_per_week') + $schedule->hours_per_week
        ];
        
        return view($viewPath, compact(
            'schedule',
            'otherSchedules',
            'periodSummary'
        ));
    }
    
    /**
     * Export schedule to PDF.
     */
    public function exportPdf(Request $request)
    {
        $faculty = Auth::guard('faculty')->user();
        
        $academicYear = $request->get('academic_year', now()->year);
        $semester = $request->get('semester', $this->getCurrentSemester());
        $yearLevel = $request->get('year_level');
        
        // Get schedules from both systems
        $subjectLoads = SubjectLoadTracker::where('professor_id', $faculty->id)
            ->where('academic_year', $academicYear)
            ->where('semester', $semester)
            ->where('status', 'active')
            ->when($yearLevel, function($query) use ($yearLevel) {
                return $query->where('year_level', $yearLevel);
            })
            ->orderBy('schedule_day')
            ->orderBy('start_time')
            ->get();
        
        $scheduleAssignments = ScheduleAssignment::where('professor_id', $faculty->id)
            ->where('academic_year', $academicYear)
            ->where('semester', $semester)
            ->where('status', 'active')
            ->when($yearLevel, function($query) use ($yearLevel) {
                return $query->where('year_level', $yearLevel);
            })
            ->orderBy('schedule_day')
            ->orderBy('start_time')
            ->get();
        
        // Add source information
        $subjectLoads->each(function ($item) {
            $item->source_name = 'Subject Load Tracker';
        });
        
        $scheduleAssignments->each(function ($item) {
            $item->source_name = 'Schedule Assignment';
        });
        
        $allSchedules = $subjectLoads->concat($scheduleAssignments);
        
        // Group by day
        $schedule = [];
        $days = SubjectLoadTracker::getDays();
        
        foreach ($days as $dayKey => $dayName) {
            $schedule[$dayKey] = $allSchedules->where('schedule_day', $dayKey)
                ->sortBy('start_time')
                ->values();
        }
        
        // Calculate summary
        $summary = [
            'total_subjects' => $allSchedules->count(),
            'total_units' => $allSchedules->sum('units'),
            'total_hours' => $allSchedules->sum('hours_per_week'),
            'academic_year' => $academicYear,
            'semester' => $semester,
            'subject_load_count' => $subjectLoads->count(),
            'schedule_assignment_count' => $scheduleAssignments->count()
        ];
        
        $workloadStatus = ScheduleAssignment::getWorkloadStatus($summary['total_hours']);
        $summary['workload_status'] = $workloadStatus;
        
        $pdf = Pdf::loadView('professor.schedule.pdf', compact(
            'faculty',
            'schedule',
            'summary',
            'days',
            'allSchedules'
        ));
        
        $filename = 'schedule-' . $faculty->name . '-' . $semester . '-' . $academicYear . '.pdf';
        
        return $pdf->download($filename);
    }
    
    /**
     * Export schedule to CSV.
     */
    public function exportCsv(Request $request)
    {
        $faculty = Auth::guard('faculty')->user();
        
        $academicYear = $request->get('academic_year', now()->year);
        $semester = $request->get('semester', $this->getCurrentSemester());
        $yearLevel = $request->get('year_level');
        
        // Get schedules from both systems
        $subjectLoads = SubjectLoadTracker::where('professor_id', $faculty->id)
            ->where('academic_year', $academicYear)
            ->where('semester', $semester)
            ->where('status', 'active')
            ->when($yearLevel, function($query) use ($yearLevel) {
                return $query->where('year_level', $yearLevel);
            })
            ->orderBy('schedule_day')
            ->orderBy('start_time')
            ->get();
        
        $scheduleAssignments = ScheduleAssignment::where('professor_id', $faculty->id)
            ->where('academic_year', $academicYear)
            ->where('semester', $semester)
            ->where('status', 'active')
            ->when($yearLevel, function($query) use ($yearLevel) {
                return $query->where('year_level', $yearLevel);
            })
            ->orderBy('schedule_day')
            ->orderBy('start_time')
            ->get();
        
        // Add source information and combine
        $subjectLoads->each(function ($item) {
            $item->source_name = 'Subject Load Tracker';
        });
        
        $scheduleAssignments->each(function ($item) {
            $item->source_name = 'Schedule Assignment';
        });
        
        $allSchedules = $subjectLoads->concat($scheduleAssignments)
            ->sortBy(['schedule_day', 'start_time']);
        
        // Generate CSV content
        $csvData = [];
        $csvData[] = [
            'Subject Code',
            'Subject Name',
            'Section',
            'Year Level',
            'Units',
            'Hours/Week',
            'Day',
            'Start Time',
            'End Time',
            'Room',
            'Source',
            'Academic Year',
            'Semester',
            'Status'
        ];
        
        foreach ($allSchedules as $schedule) {
            $csvData[] = [
                $schedule->subject_code,
                $schedule->subject_name,
                $schedule->section,
                $schedule->year_level,
                $schedule->units,
                $schedule->hours_per_week,
                ucfirst($schedule->schedule_day),
                $schedule->start_time,
                $schedule->end_time,
                $schedule->room ?: 'N/A',
                $schedule->source_name,
                $schedule->academic_year . '-' . ($schedule->academic_year + 1),
                $schedule->semester,
                ucfirst($schedule->status)
            ];
        }
        
        $filename = 'schedule-' . str_replace(' ', '-', strtolower($faculty->name)) . '-' . 
                   str_replace(' ', '-', strtolower($semester)) . '-' . $academicYear . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Check for schedule conflicts within the professor's schedule.
     */
    private function checkScheduleConflicts($schedules)
    {
        $conflicts = [];
        $schedulesByDay = $schedules->groupBy('schedule_day');
        
        foreach ($schedulesByDay as $day => $daySchedules) {
            $sortedSchedules = $daySchedules->sortBy('start_time');
            
            for ($i = 0; $i < $sortedSchedules->count() - 1; $i++) {
                for ($j = $i + 1; $j < $sortedSchedules->count(); $j++) {
                    $schedule1 = $sortedSchedules->values()[$i];
                    $schedule2 = $sortedSchedules->values()[$j];
                    
                    if ($this->timesOverlap($schedule1->start_time, $schedule1->end_time, 
                                          $schedule2->start_time, $schedule2->end_time)) {
                        $conflicts[] = [
                            'day' => ucfirst($day),
                            'schedule1' => $schedule1,
                            'schedule2' => $schedule2,
                            'type' => 'time_overlap'
                        ];
                    }
                }
            }
        }
        
        return $conflicts;
    }
    
    /**
     * Check if two time ranges overlap.
     */
    private function timesOverlap($start1, $end1, $start2, $end2)
    {
        try {
            $start1 = \Carbon\Carbon::createFromFormat('H:i', $start1);
            $end1 = \Carbon\Carbon::createFromFormat('H:i', $end1);
            $start2 = \Carbon\Carbon::createFromFormat('H:i', $start2);
            $end2 = \Carbon\Carbon::createFromFormat('H:i', $end2);
            
            return $start1->lt($end2) && $start2->lt($end1);
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Get current semester based on current date.
     */
    private function getCurrentSemester()
    {
        $month = now()->month;
        
        if ($month >= 1 && $month <= 5) {
            return '2nd Semester';
        } elseif ($month >= 6 && $month <= 10) {
            return '1st Semester';
        } else {
            return 'Summer';
        }
    }
}
