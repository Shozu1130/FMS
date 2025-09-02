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
            'faculty_id' => $request->get('faculty_id'),
            'academic_year' => $currentYear,
            'semester' => $currentSemester,
            'status' => $request->get('status'),
            'search' => $request->get('search')
        ];

        // Assignments
        $assignments = $this->scheduleService->getCombinedScheduleData($filters);
        $paginatedAssignments = $this->scheduleService->getPaginatedScheduleData($filters, 15);

        // Filter options
        $faculties = Faculty::orderBy('name')->get();
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
        $faculties = Faculty::orderBy('name')->get();
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
            $request->faculty_id, $request->subject_code, $request->section, $request->academic_year, $request->semester
        )) {
            return back()->withErrors(['duplicate' => 'This faculty member is already assigned to this subject and section for the selected academic period.'])->withInput();
        }

        // Schedule conflict check
        $conflict = ScheduleAssignment::hasScheduleConflict(
            $request->faculty_id, $request->schedule_day, $request->start_time, $request->end_time,
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
            $scheduleAssignment->faculty_id, $scheduleAssignment->academic_year, $scheduleAssignment->semester
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

        $loadSummary = ScheduleAssignment::getFacultyLoadSummary(
            $scheduleAssignment->faculty_id, $scheduleAssignment->academic_year, $scheduleAssignment->semester
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
            $request->faculty_id, $request->subject_code, $request->section,
            $request->academic_year, $request->semester, $scheduleAssignment->id
        )) {
            return back()->withErrors(['duplicate' => 'This faculty member is already assigned to this subject and section for the selected academic period.'])->withInput();
        }

        // Schedule conflict check
        $conflict = ScheduleAssignment::hasScheduleConflict(
            $request->faculty_id, $request->schedule_day, $request->start_time, $request->end_time,
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
