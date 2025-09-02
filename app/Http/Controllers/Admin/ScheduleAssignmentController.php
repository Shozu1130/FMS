<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScheduleAssignment;
use App\Models\SubjectLoadTracker;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ScheduleAssignmentController extends Controller
{
    /**
     * Display the schedule assignment dashboard.
     */
    public function index(Request $request)
    {
        $currentYear = $request->get('academic_year', date('Y'));
        $currentSemester = $request->get('semester', $this->getCurrentSemester());
        
        // Get dashboard statistics
        $stats = ScheduleAssignment::getDashboardStats($currentYear, $currentSemester);
        
        // Get combined schedule data with filters
        $filters = [
            'faculty_id' => $request->get('faculty_id'),
            'academic_year' => $currentYear,
            'semester' => $currentSemester,
            'status' => $request->get('status'),
            'search' => $request->get('search')
        ];
        
        $assignments = ScheduleAssignment::getCombinedScheduleData($filters);
        
        // Paginate the results manually
        $page = $request->get('page', 1);
        $perPage = 15;
        $offset = ($page - 1) * $perPage;
        $paginatedAssignments = $assignments->slice($offset, $perPage);
        
        // Get filter options
        $faculties = Faculty::orderBy('name')->get();
        $academicYears = range(date('Y') - 2, date('Y') + 2);
        $semesters = [
            '1st Semester' => '1st Semester',
            '2nd Semester' => '2nd Semester',
            'Summer' => 'Summer'
        ];
        
        return view('admin.schedule_assignment.index', compact(
            'assignments', 'paginatedAssignments', 'stats', 'faculties', 
            'academicYears', 'semesters', 'currentYear', 'currentSemester', 'filters'
        ));
    }

    /**
     * Display the dashboard with statistics.
     */
    public function dashboard(Request $request)
    {
        $currentYear = $request->get('academic_year', date('Y'));
        $currentSemester = $request->get('semester', $this->getCurrentSemester());
        
        $stats = ScheduleAssignment::getDashboardStats($currentYear, $currentSemester);
        
        // Get recent assignments
        $recentAssignments = ScheduleAssignment::getCombinedScheduleData([
            'academic_year' => $currentYear,
            'semester' => $currentSemester,
            'status' => 'active'
        ])->take(10);
        
        // Get faculty workload distribution
        $faculties = Faculty::with(['subjectLoads' => function($query) use ($currentYear, $currentSemester) {
            $query->where('academic_year', $currentYear)
                  ->where('semester', $currentSemester)
                  ->where('status', 'active');
        }])->get();
        
        $workloadDistribution = [];
        foreach ($faculties as $faculty) {
            $totalHours = ScheduleAssignment::getFacultyTotalHours($faculty->id, $currentYear, $currentSemester);
            if ($totalHours > 0) {
                $workloadStatus = ScheduleAssignment::getWorkloadStatus($totalHours);
                $workloadDistribution[] = [
                    'faculty' => $faculty,
                    'total_hours' => $totalHours,
                    'total_units' => ScheduleAssignment::getFacultyTotalUnits($faculty->id, $currentYear, $currentSemester),
                    'workload_status' => $workloadStatus
                ];
            }
        }
        
        // Sort by total hours descending
        usort($workloadDistribution, function($a, $b) {
            return $b['total_hours'] - $a['total_hours'];
        });
        
        $academicYears = range(date('Y') - 2, date('Y') + 2);
        $semesters = [
            '1st Semester' => '1st Semester',
            '2nd Semester' => '2nd Semester',
            'Summer' => 'Summer'
        ];
        
        return view('admin.schedule_assignment.dashboard', compact(
            'stats', 'recentAssignments', 'workloadDistribution', 
            'currentYear', 'currentSemester', 'academicYears', 'semesters'
        ));
    }

    /**
     * Show the form for creating a new schedule assignment.
     */
    public function create()
    {
        $faculties = Faculty::orderBy('name')->get();
        $days = ScheduleAssignment::getDays();
        $semesters = ScheduleAssignment::getSemesters();
        $yearLevels = ScheduleAssignment::getYearLevels();
        $statusOptions = ScheduleAssignment::getStatusOptions();
        $sourceOptions = ScheduleAssignment::getSourceOptions();
        
        return view('admin.schedule_assignment.create', compact(
            'faculties', 'days', 'semesters', 'yearLevels', 'statusOptions', 'sourceOptions'
        ));
    }

    /**
     * Store a newly created schedule assignment.
     */
    public function store(Request $request)
    {
        $request->validate(ScheduleAssignment::rules(), ScheduleAssignment::validationMessages());
        
        // Check for duplicate assignment
        if (ScheduleAssignment::hasDuplicateAssignment(
            $request->faculty_id,
            $request->subject_code,
            $request->section,
            $request->academic_year,
            $request->semester
        )) {
            return back()->withErrors([
                'duplicate' => 'This faculty member is already assigned to this subject and section for the selected academic period.'
            ])->withInput();
        }
        
        // Check for schedule conflicts
        $conflict = ScheduleAssignment::hasScheduleConflict(
            $request->faculty_id,
            $request->schedule_day,
            $request->start_time,
            $request->end_time,
            $request->academic_year,
            $request->semester
        );
        
        if ($conflict) {
            $conflictDetails = "Schedule Conflict: Professor already assigned to {$conflict->subject_code} ({$conflict->subject_name}) at {$conflict->schedule_display}";
            return back()->withErrors([
                'conflict' => $conflictDetails
            ])->withInput();
        }
        
        ScheduleAssignment::create($request->all());
        
        return redirect()->route('admin.schedule-assignment.index')
                        ->with('success', 'Schedule assignment created successfully.');
    }

    /**
     * Display the specified schedule assignment.
     */
    public function show(ScheduleAssignment $scheduleAssignment)
    {
        $scheduleAssignment->load('faculty');
        
        // Get faculty load summary for the same period
        $loadSummary = ScheduleAssignment::getFacultyLoadSummary(
            $scheduleAssignment->faculty_id,
            $scheduleAssignment->academic_year,
            $scheduleAssignment->semester
        );
        
        return view('admin.schedule_assignment.show', compact('scheduleAssignment', 'loadSummary'));
    }

    /**
     * Show the form for editing the specified schedule assignment.
     */
    public function edit(ScheduleAssignment $scheduleAssignment)
    {
        $faculties = Faculty::orderBy('name')->get();
        $days = ScheduleAssignment::getDays();
        $semesters = ScheduleAssignment::getSemesters();
        $yearLevels = ScheduleAssignment::getYearLevels();
        $statusOptions = ScheduleAssignment::getStatusOptions();
        $sourceOptions = ScheduleAssignment::getSourceOptions();
        
        // Get faculty load summary
        $loadSummary = ScheduleAssignment::getFacultyLoadSummary(
            $scheduleAssignment->faculty_id,
            $scheduleAssignment->academic_year,
            $scheduleAssignment->semester
        );
        
        return view('admin.schedule_assignment.edit', compact(
            'scheduleAssignment', 'faculties', 'days', 'semesters', 
            'yearLevels', 'statusOptions', 'sourceOptions', 'loadSummary'
        ));
    }

    /**
     * Update the specified schedule assignment.
     */
    public function update(Request $request, ScheduleAssignment $scheduleAssignment)
    {
        $request->validate(ScheduleAssignment::rules($scheduleAssignment->id), ScheduleAssignment::validationMessages());
        
        // Check for duplicate assignment
        if (ScheduleAssignment::hasDuplicateAssignment(
            $request->faculty_id,
            $request->subject_code,
            $request->section,
            $request->academic_year,
            $request->semester,
            $scheduleAssignment->id
        )) {
            return back()->withErrors([
                'duplicate' => 'This faculty member is already assigned to this subject and section for the selected academic period.'
            ])->withInput();
        }
        
        // Check for schedule conflicts
        $conflict = ScheduleAssignment::hasScheduleConflict(
            $request->faculty_id,
            $request->schedule_day,
            $request->start_time,
            $request->end_time,
            $request->academic_year,
            $request->semester,
            $scheduleAssignment->id
        );
        
        if ($conflict) {
            $conflictDetails = "Schedule Conflict: Professor already assigned to {$conflict->subject_code} ({$conflict->subject_name}) at {$conflict->schedule_display}";
            return back()->withErrors([
                'conflict' => $conflictDetails
            ])->withInput();
        }
        
        $scheduleAssignment->update($request->all());
        
        return redirect()->route('admin.schedule-assignment.index')
                        ->with('success', 'Schedule assignment updated successfully.');
    }

    /**
     * Remove the specified schedule assignment.
     */
    public function destroy(ScheduleAssignment $scheduleAssignment)
    {
        $scheduleAssignment->delete();
        
        return redirect()->route('admin.schedule-assignment.index')
                        ->with('success', 'Schedule assignment deleted successfully.');
    }

    /**
     * Display calendar view.
     */
    public function calendar(Request $request)
    {
        $currentYear = $request->get('academic_year', date('Y'));
        $currentSemester = $request->get('semester', $this->getCurrentSemester());
        $facultyId = $request->get('faculty_id');
        
        $calendarData = ScheduleAssignment::getCalendarData($currentYear, $currentSemester, $facultyId);
        
        $faculties = Faculty::orderBy('name')->get();
        $academicYears = range(date('Y') - 2, date('Y') + 2);
        $semesters = [
            '1st Semester' => '1st Semester',
            '2nd Semester' => '2nd Semester',
            'Summer' => 'Summer'
        ];
        
        return view('admin.schedule_assignment.calendar', compact(
            'calendarData', 'faculties', 'academicYears', 'semesters',
            'currentYear', 'currentSemester', 'facultyId'
        ));
    }

    /**
     * Export schedule assignments to CSV.
     */
    public function export(Request $request)
    {
        $currentYear = $request->get('academic_year', date('Y'));
        $currentSemester = $request->get('semester', $this->getCurrentSemester());
        
        $filters = [
            'faculty_id' => $request->get('faculty_id'),
            'academic_year' => $currentYear,
            'semester' => $currentSemester,
            'status' => $request->get('status'),
            'search' => $request->get('search')
        ];
        
        $assignments = ScheduleAssignment::getCombinedScheduleData($filters);
        
        $filename = "schedule_assignments_{$currentYear}_{$currentSemester}_" . date('Y-m-d') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($assignments) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Faculty Name',
                'Professor ID',
                'Subject Code',
                'Subject Name',
                'Section',
                'Year Level',
                'Units',
                'Hours/Week',
                'Schedule',
                'Room',
                'Academic Year',
                'Semester',
                'Status',
                'Source',
                'Notes'
            ]);
            
            foreach ($assignments as $assignment) {
                fputcsv($file, [
                    $assignment->faculty->name,
                    $assignment->faculty->professor_id,
                    $assignment->subject_code,
                    $assignment->subject_name,
                    $assignment->section,
                    $assignment->year_level,
                    $assignment->units,
                    $assignment->hours_per_week,
                    $assignment->schedule_display,
                    $assignment->room,
                    $assignment->academic_year,
                    $assignment->semester,
                    ucfirst($assignment->status),
                    $assignment->source_table ?? 'Schedule Assignment',
                    $assignment->notes
                ]);
            }
            
            fclose($file);
        };
        
        return Response::stream($callback, 200, $headers);
    }

    /**
     * Bulk update status for multiple assignments.
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'assignment_ids' => 'required|array',
            'assignment_ids.*' => 'integer',
            'status' => 'required|in:active,inactive,completed'
        ]);
        
        $updated = ScheduleAssignment::whereIn('id', $request->assignment_ids)
                                   ->update(['status' => $request->status]);
        
        return back()->with('success', "Updated status for {$updated} schedule assignments.");
    }

    /**
     * Get faculty load summary via AJAX.
     */
    public function getFacultyLoadSummary(Request $request)
    {
        $facultyId = $request->get('faculty_id');
        $academicYear = $request->get('academic_year');
        $semester = $request->get('semester');
        
        if (!$facultyId || !$academicYear || !$semester) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }
        
        $summary = ScheduleAssignment::getFacultyLoadSummary($facultyId, $academicYear, $semester);
        
        return response()->json($summary);
    }

    /**
     * Check for schedule conflicts via AJAX.
     */
    public function checkConflict(Request $request)
    {
        $facultyId = $request->get('faculty_id');
        $scheduleDay = $request->get('schedule_day');
        $startTime = $request->get('start_time');
        $endTime = $request->get('end_time');
        $academicYear = $request->get('academic_year');
        $semester = $request->get('semester');
        $excludeId = $request->get('exclude_id');
        
        if (!$facultyId || !$scheduleDay || !$startTime || !$endTime || !$academicYear || !$semester) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }
        
        $conflict = ScheduleAssignment::hasScheduleConflict(
            $facultyId, $scheduleDay, $startTime, $endTime, 
            $academicYear, $semester, $excludeId
        );
        
        if ($conflict) {
            return response()->json([
                'has_conflict' => true,
                'conflict_details' => [
                    'subject_code' => $conflict->subject_code,
                    'subject_name' => $conflict->subject_name,
                    'schedule_display' => $conflict->schedule_display,
                    'source' => isset($conflict->source_table) ? $conflict->source_table : 'Schedule Assignment'
                ],
                'message' => "Schedule Conflict: Professor already assigned to {$conflict->subject_code} ({$conflict->subject_name}) at {$conflict->schedule_display}"
            ]);
        }
        
        return response()->json(['has_conflict' => false]);
    }

    /**
     * Check for duplicate assignments via AJAX.
     */
    public function checkDuplicate(Request $request)
    {
        $facultyId = $request->get('faculty_id');
        $subjectCode = $request->get('subject_code');
        $section = $request->get('section');
        $academicYear = $request->get('academic_year');
        $semester = $request->get('semester');
        $excludeId = $request->get('exclude_id');
        
        if (!$facultyId || !$subjectCode || !$section || !$academicYear || !$semester) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }
        
        $hasDuplicate = ScheduleAssignment::hasDuplicateAssignment(
            $facultyId, $subjectCode, $section, $academicYear, $semester, $excludeId
        );
        
        return response()->json([
            'has_duplicate' => $hasDuplicate,
            'message' => $hasDuplicate ? 'This faculty member is already assigned to this subject and section for the selected academic period.' : ''
        ]);
    }

    /**
     * Get reports data.
     */
    public function reports(Request $request)
    {
        $currentYear = $request->get('academic_year', date('Y'));
        $currentSemester = $request->get('semester', $this->getCurrentSemester());
        
        // Get faculty workload analysis
        $faculties = Faculty::all();
        $facultyWorkloads = [];
        
        foreach ($faculties as $faculty) {
            $totalHours = ScheduleAssignment::getFacultyTotalHours($faculty->id, $currentYear, $currentSemester);
            $totalUnits = ScheduleAssignment::getFacultyTotalUnits($faculty->id, $currentYear, $currentSemester);
            
            if ($totalHours > 0) {
                $workloadStatus = ScheduleAssignment::getWorkloadStatus($totalHours);
                $facultyWorkloads[] = [
                    'faculty' => $faculty,
                    'total_hours' => $totalHours,
                    'total_units' => $totalUnits,
                    'workload_status' => $workloadStatus,
                    'assignments_count' => ScheduleAssignment::where('faculty_id', $faculty->id)
                                                            ->where('academic_year', $currentYear)
                                                            ->where('semester', $currentSemester)
                                                            ->where('status', 'active')
                                                            ->count() +
                                         SubjectLoadTracker::where('faculty_id', $faculty->id)
                                                          ->where('academic_year', $currentYear)
                                                          ->where('semester', $currentSemester)
                                                          ->where('status', 'active')
                                                          ->count()
                ];
            }
        }
        
        // Sort by total hours descending
        usort($facultyWorkloads, function($a, $b) {
            return $b['total_hours'] - $a['total_hours'];
        });
        
        $academicYears = range(date('Y') - 2, date('Y') + 2);
        $semesters = [
            '1st Semester' => '1st Semester',
            '2nd Semester' => '2nd Semester',
            'Summer' => 'Summer'
        ];
        
        return view('admin.schedule_assignment.reports', compact(
            'facultyWorkloads', 'currentYear', 'currentSemester', 'academicYears', 'semesters'
        ));
    }

    /**
     * Get current semester based on current date.
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
