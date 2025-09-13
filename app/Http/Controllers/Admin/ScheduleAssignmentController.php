<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScheduleAssignment;
use App\Models\SubjectLoadTracker;
use App\Models\Faculty;
use App\Services\ScheduleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ScheduleAssignmentController extends Controller
{
    protected $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    /**
     * Display the schedule assignment dashboard.
     */
    public function index(Request $request)
    {
        $currentYear = $request->get('academic_year', date('Y'));
        $currentSemester = $request->get('semester', $this->getCurrentSemester());

        // Dashboard stats
        $stats = $this->scheduleService->getDashboardStats($currentYear, $currentSemester);

        // Filters
        $filters = [
            'professor_id' => $request->get('professor_id'),
            'academic_year' => $currentYear,
            'semester' => $currentSemester,
            'status' => $request->get('status'),
            'search' => $request->get('search')
        ];

        // Assignments
        $assignments = $this->scheduleService->getCombinedScheduleData($filters);
        $paginatedAssignments = $this->scheduleService->getPaginatedScheduleData($filters, 15);

        // Filter options - filter faculties by department
        $facultiesQuery = Faculty::orderBy('name');
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $facultiesQuery->where('department', auth()->user()->department);
        }
        $faculties = $facultiesQuery->get();
        $academicYears = range(date('Y') - 2, date('Y') + 2);
        $semesters = ['1st Semester' => '1st Semester', '2nd Semester' => '2nd Semester', 'Summer' => 'Summer'];

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

        $stats = $this->scheduleService->getDashboardStats($currentYear, $currentSemester);

        $recentAssignments = $this->scheduleService->getCombinedScheduleData([
            'academic_year' => $currentYear,
            'semester' => $currentSemester,
            'status' => 'active'
        ])->take(10);

        $workloadDistribution = $this->scheduleService->getFacultyWorkloadDistribution($currentYear, $currentSemester);

        $academicYears = range(date('Y') - 2, date('Y') + 2);
        $semesters = ['1st Semester' => '1st Semester', '2nd Semester' => '2nd Semester', 'Summer' => 'Summer'];

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
        // Filter faculties by department
        $facultiesQuery = Faculty::orderBy('name');
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $facultiesQuery->where('department', auth()->user()->department);
        }
        $faculties = $facultiesQuery->get();
        $days = ScheduleAssignment::getDays();
        $semesters = ScheduleAssignment::getSemesters();
        $yearLevels = ScheduleAssignment::getYearLevels();
        $statusOptions = ScheduleAssignment::getStatusOptions();
        $sourceOptions = ScheduleAssignment::getSourceOptions();
        $academicYears = range(date('Y') - 2, date('Y') + 2);
        $currentYear = date('Y');

        return view('admin.schedule_assignment.create', compact(
            'faculties', 'days', 'semesters', 'yearLevels',
            'statusOptions', 'sourceOptions', 'academicYears', 'currentYear'
        ));
    }

    /**
     * Store a newly created schedule assignment.
     */
    public function store(Request $request)
    {
        $request->validate(ScheduleAssignment::rules(), ScheduleAssignment::validationMessages());

        // Duplicate check
        if (ScheduleAssignment::hasDuplicateAssignment(
            $request->professor_id, $request->subject_code, $request->section, $request->academic_year, $request->semester
        )) {
            return back()->withErrors(['duplicate' => 'This faculty member is already assigned to this subject and section for the selected academic period.'])->withInput();
        }

        // Schedule conflict check
        $conflict = ScheduleAssignment::hasScheduleConflict(
            $request->professor_id, $request->schedule_day, $request->start_time, $request->end_time,
            $request->academic_year, $request->semester
        );

        if ($conflict) {
            $conflictDetails = "Schedule Conflict: Professor already assigned to {$conflict->subject_code} ({$conflict->subject_name}) at {$conflict->schedule_display}";
            return back()->withErrors(['conflict' => $conflictDetails])->withInput();
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

        $loadSummary = ScheduleAssignment::getFacultyLoadSummary(
            $scheduleAssignment->professor_id, $scheduleAssignment->academic_year, $scheduleAssignment->semester
        );

        return view('admin.schedule_assignment.show', compact('scheduleAssignment', 'loadSummary'));
    }

    /**
     * Show the form for editing the specified schedule assignment.
     */
    public function edit(ScheduleAssignment $scheduleAssignment)
    {
        // Filter faculties by department
        $facultiesQuery = Faculty::orderBy('name');
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $facultiesQuery->where('department', auth()->user()->department);
        }
        $faculties = $facultiesQuery->get();
        $days = ScheduleAssignment::getDays();
        $semesters = ScheduleAssignment::getSemesters();
        $yearLevels = ScheduleAssignment::getYearLevels();
        $statusOptions = ScheduleAssignment::getStatusOptions();
        $sourceOptions = ScheduleAssignment::getSourceOptions();

        $loadSummary = ScheduleAssignment::getFacultyLoadSummary(
            $scheduleAssignment->professor_id, $scheduleAssignment->academic_year, $scheduleAssignment->semester
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

        // Duplicate check
        if (ScheduleAssignment::hasDuplicateAssignment(
            $request->professor_id, $request->subject_code, $request->section,
            $request->academic_year, $request->semester, $scheduleAssignment->id
        )) {
            return back()->withErrors(['duplicate' => 'This faculty member is already assigned to this subject and section for the selected academic period.'])->withInput();
        }

        // Schedule conflict check
        $conflict = ScheduleAssignment::hasScheduleConflict(
            $request->professor_id, $request->schedule_day, $request->start_time, $request->end_time,
            $request->academic_year, $request->semester, $scheduleAssignment->id
        );

        if ($conflict) {
            $conflictDetails = "Schedule Conflict: Professor already assigned to {$conflict->subject_code} ({$conflict->subject_name}) at {$conflict->schedule_display}";
            return back()->withErrors(['conflict' => $conflictDetails])->withInput();
        }

        $scheduleAssignment->update($request->all());

        return redirect()->route('admin.schedule-assignment.index')
                         ->with('success', 'Schedule assignment updated successfully.');
    }

    /**
     * Display calendar view of schedule assignments.
     */
    public function calendar(Request $request)
    {
        $currentYear = $request->get('academic_year', date('Y'));
        $currentSemester = $request->get('semester', $this->getCurrentSemester());
        $professorId = $request->get('professor_id');

        // Get all assignments for the calendar view
        $filters = [
            'academic_year' => $currentYear,
            'semester' => $currentSemester,
            'status' => 'active'
        ];

        // Add faculty filter if specified
        if ($professorId) {
            $filters['professor_id'] = $professorId;
        }

        $assignments = $this->scheduleService->getCombinedScheduleData($filters);

        // Group assignments by day and time for calendar display
        $calendarData = [];
        $timeSlots = [];

        foreach ($assignments as $assignment) {
            $day = $assignment->schedule_day;
            $startTime = $assignment->start_time;
            $endTime = $assignment->end_time;
            
            if (!isset($calendarData[$day])) {
                $calendarData[$day] = [];
            }
            
            $calendarData[$day][] = $assignment;
            
            // Collect unique time slots
            $timeSlots[] = $startTime;
            $timeSlots[] = $endTime;
        }

        // Sort and deduplicate time slots
        $timeSlots = array_unique($timeSlots);
        sort($timeSlots);

        // Filter faculties by department
        $facultiesQuery = Faculty::orderBy('name');
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $facultiesQuery->where('department', auth()->user()->department);
        }
        $faculties = $facultiesQuery->get();
        $academicYears = range(date('Y') - 2, date('Y') + 2);
        $semesters = ['1st Semester' => '1st Semester', '2nd Semester' => '2nd Semester', 'Summer' => 'Summer'];
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        return view('admin.schedule_assignment.calendar', compact(
            'calendarData', 'timeSlots', 'days', 'faculties', 'academicYears', 
            'semesters', 'currentYear', 'currentSemester', 'professorId'
        ));
    }

    /**
     * Display reports and analytics for schedule assignments.
     */
    public function reports(Request $request)
    {
        $currentYear = $request->get('academic_year', date('Y'));
        $currentSemester = $request->get('semester', $this->getCurrentSemester());

        // Get faculty workload distribution
        $workloadDistribution = $this->scheduleService->getFacultyWorkloadDistribution($currentYear, $currentSemester);

        // Get dashboard stats for the reports
        $stats = $this->scheduleService->getDashboardStats($currentYear, $currentSemester);

        // Get all assignments for detailed analysis
        $filters = [
            'academic_year' => $currentYear,
            'semester' => $currentSemester
        ];
        $assignments = $this->scheduleService->getCombinedScheduleData($filters);

        // Group assignments by faculty for detailed reporting
        $facultyReports = [];
        foreach ($assignments as $assignment) {
            $professorId = $assignment->professor_id;
            if (!isset($facultyReports[$professorId])) {
                $facultyReports[$professorId] = [
                    'faculty' => $assignment->faculty,
                    'assignments' => [],
                    'total_units' => 0,
                    'total_hours' => 0
                ];
            }
            $facultyReports[$professorId]['assignments'][] = $assignment;
            $facultyReports[$professorId]['total_units'] += $assignment->units ?? 0;
            $facultyReports[$professorId]['total_hours'] += $assignment->hours ?? 0;
        }

        // Filter faculties by department
        $facultiesQuery = Faculty::orderBy('name');
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $facultiesQuery->where('department', auth()->user()->department);
        }
        $faculties = $facultiesQuery->get();
        $academicYears = range(date('Y') - 2, date('Y') + 2);
        $semesters = ['1st Semester' => '1st Semester', '2nd Semester' => '2nd Semester', 'Summer' => 'Summer'];

        return view('admin.schedule_assignment.reports', compact(
            'workloadDistribution', 'stats', 'facultyReports', 'faculties',
            'academicYears', 'semesters', 'currentYear', 'currentSemester'
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
            'academic_year' => $currentYear,
            'semester' => $currentSemester
        ];

        if ($request->get('professor_id')) {
            $filters['professor_id'] = $request->get('professor_id');
        }

        $assignments = $this->scheduleService->getCombinedScheduleData($filters);

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
                'Subject Code',
                'Subject Name',
                'Section',
                'Units',
                'Hours',
                'Schedule Day',
                'Start Time',
                'End Time',
                'Room',
                'Year Level',
                'Academic Year',
                'Semester',
                'Status',
                'Source'
            ]);

            // CSV Data
            foreach ($assignments as $assignment) {
                fputcsv($file, [
                    $assignment->faculty->name ?? 'N/A',
                    $assignment->subject_code,
                    $assignment->subject_name,
                    $assignment->section,
                    $assignment->units ?? 0,
                    $assignment->hours ?? 0,
                    $assignment->schedule_day,
                    $assignment->start_time,
                    $assignment->end_time,
                    $assignment->room ?? 'N/A',
                    $assignment->year_level ?? 'N/A',
                    $assignment->academic_year,
                    $assignment->semester,
                    $assignment->status,
                    $assignment->source ?? 'Direct Assignment'
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
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
     * Get current semester based on current date.
     */
    private function getCurrentSemester()
    {
        $month = date('n');

        if ($month >= 1 && $month <= 5) return '2nd Semester';
        if ($month >= 6 && $month <= 10) return '1st Semester';
        return 'Summer';
    }
}
