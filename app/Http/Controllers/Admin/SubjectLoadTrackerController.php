<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubjectLoadTracker;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubjectLoadTrackerController extends Controller
{
    /**
     * Display a listing of subject loads.
     */
    public function index(Request $request)
    {
        $query = SubjectLoadTracker::with('faculty');

        // Filter by department if not master admin
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $query->whereHas('faculty', function($q) {
                $q->where('department', auth()->user()->department);
            });
        }

        // Filter by faculty
        if ($request->filled('faculty_id')) {
            $query->where('faculty_id', $request->faculty_id);
        }

        // Filter by academic year
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        // Filter by semester
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject_code', 'like', "%{$search}%")
                  ->orWhere('subject_name', 'like', "%{$search}%")
                  ->orWhere('section', 'like', "%{$search}%")
                  ->orWhere('room', 'like', "%{$search}%")
                  ->orWhereHas('faculty', function($facultyQuery) use ($search) {
                      $facultyQuery->where('name', 'like', "%{$search}%")
                                  ->orWhere('professor_id', 'like', "%{$search}%");
                  });
            });
        }

        $subjectLoads = $query->orderBy('academic_year', 'desc')
                             ->orderBy('semester', 'asc')
                             ->orderBy('schedule_day', 'asc')
                             ->orderBy('start_time', 'asc')
                             ->paginate(15);

        // Filter faculties by department
        $facultiesQuery = Faculty::where('status', 'active');
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $facultiesQuery->where('department', auth()->user()->department);
        }
        $faculties = $facultiesQuery->orderBy('name')->get();
        $academicYears = SubjectLoadTracker::distinct()->pluck('academic_year')->sort()->values();
        
        return view('admin.subject_loads.index', compact('subjectLoads', 'faculties', 'academicYears'));
    }

    /**
     * Show the form for creating a new subject load.
     */
    public function create()
    {
        // Filter faculties by department
        $facultiesQuery = Faculty::where('status', 'active');
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $facultiesQuery->where('department', auth()->user()->department);
        }
        $faculties = $facultiesQuery->orderBy('name')->get();
        $days = SubjectLoadTracker::getDays();
        $semesters = SubjectLoadTracker::getSemesters();
        $yearLevels = SubjectLoadTracker::getYearLevels();
        $statusOptions = SubjectLoadTracker::getStatusOptions();
        
        return view('admin.subject_loads.create', compact('faculties', 'days', 'semesters', 'yearLevels', 'statusOptions'));
    }

    /**
     * Store a newly created subject load in storage.
     */
    public function store(Request $request)
    {
        $rules = SubjectLoadTracker::rules();
        $messages = SubjectLoadTracker::validationMessages();
        
        $request->validate($rules, $messages);

        // Check for duplicate assignment
        if (SubjectLoadTracker::hasDuplicateAssignment(
            $request->faculty_id,
            $request->subject_code,
            $request->section,
            $request->academic_year,
            $request->semester
        )) {
            throw ValidationException::withMessages([
                'subject_code' => 'This faculty member is already assigned to this subject and section for the selected period.'
            ]);
        }

        // Check for schedule conflicts
        $conflictingLoad = SubjectLoadTracker::hasScheduleConflict(
            $request->faculty_id,
            $request->schedule_day,
            $request->start_time,
            $request->end_time,
            $request->academic_year,
            $request->semester
        );

        if ($conflictingLoad) {
            throw ValidationException::withMessages([
                'start_time' => 'Schedule conflict detected with ' . $conflictingLoad->subject_code . ' (' . $conflictingLoad->time_range . ')'
            ]);
        }

        // Prepare data for creation
        $data = $request->all();
        
        // Set default source if not provided
        if (!isset($data['source'])) {
            $data['source'] = SubjectLoadTracker::SOURCE_SUBJECT_LOAD_TRACKER;
        }
        
        SubjectLoadTracker::create($data);

        return redirect()->route('admin.subject-loads.index')
                        ->with('success', 'Subject load assigned successfully.');
    }

    /**
     * Display the specified subject load.
     */
    public function show(SubjectLoadTracker $subjectLoad)
    {
        $subjectLoad->load('faculty');
        
        // Get faculty's other loads for the same period
        $otherLoads = SubjectLoadTracker::where('faculty_id', $subjectLoad->faculty_id)
                                       ->where('academic_year', $subjectLoad->academic_year)
                                       ->where('semester', $subjectLoad->semester)
                                       ->where('id', '!=', $subjectLoad->id)
                                       ->where('status', SubjectLoadTracker::STATUS_ACTIVE)
                                       ->orderBy('schedule_day')
                                       ->orderBy('start_time')
                                       ->get();

        $totalUnits = SubjectLoadTracker::getFacultyTotalUnits(
            $subjectLoad->faculty_id,
            $subjectLoad->academic_year,
            $subjectLoad->semester
        );

        $totalHours = SubjectLoadTracker::getFacultyTotalHours(
            $subjectLoad->faculty_id,
            $subjectLoad->academic_year,
            $subjectLoad->semester
        );

        return view('admin.subject_loads.show', compact('subjectLoad', 'otherLoads', 'totalUnits', 'totalHours'));
    }

    /**
     * Show the form for editing the specified subject load.
     */
    public function edit(SubjectLoadTracker $subjectLoad)
    {
        // Filter faculties by department
        $facultiesQuery = Faculty::where('status', 'active')->orderBy('name');
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $facultiesQuery->where('department', auth()->user()->department);
        }
        $faculties = $facultiesQuery->get();
        $days = SubjectLoadTracker::getDays();
        $semesters = SubjectLoadTracker::getSemesters();
        $yearLevels = SubjectLoadTracker::getYearLevels();
        $statusOptions = SubjectLoadTracker::getStatusOptions();
        
        return view('admin.subject_loads.edit', compact('subjectLoad', 'faculties', 'days', 'semesters', 'yearLevels', 'statusOptions'));
    }

    /**
     * Update the specified subject load in storage.
     */
    public function update(Request $request, SubjectLoadTracker $subjectLoad)
    {
        $rules = SubjectLoadTracker::rules($subjectLoad->id);
        $messages = SubjectLoadTracker::validationMessages();
        
        $request->validate($rules, $messages);

        // Check for duplicate assignment (excluding current record)
        if (SubjectLoadTracker::hasDuplicateAssignment(
            $request->faculty_id,
            $request->subject_code,
            $request->section,
            $request->academic_year,
            $request->semester,
            $subjectLoad->id
        )) {
            throw ValidationException::withMessages([
                'subject_code' => 'This faculty member is already assigned to this subject and section for the selected period.'
            ]);
        }

        // Check for schedule conflicts (excluding current record)
        $conflictingLoad = SubjectLoadTracker::hasScheduleConflict(
            $request->faculty_id,
            $request->schedule_day,
            $request->start_time,
            $request->end_time,
            $request->academic_year,
            $request->semester,
            $subjectLoad->id
        );

        if ($conflictingLoad) {
            throw ValidationException::withMessages([
                'start_time' => 'Schedule conflict detected with ' . $conflictingLoad->subject_code . ' (' . $conflictingLoad->time_range . ')'
            ]);
        }

        $subjectLoad->update($request->all());

        return redirect()->route('admin.subject-loads.index')
                        ->with('success', 'Subject load updated successfully.');
    }

    /**
     * Remove the specified subject load from storage.
     */
    public function destroy(SubjectLoadTracker $subjectLoad)
    {
        $subjectLoad->delete();

        return redirect()->route('admin.subject-loads.index')
                        ->with('success', 'Subject load deleted successfully.');
    }

    /**
     * Display dashboard with statistics.
     */
    public function dashboard()
    {
        $currentYear = date('Y');
        $currentSemester = '1st Semester'; // You can make this dynamic

        // Filter statistics by department
        $statsQuery = SubjectLoadTracker::active();
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $statsQuery->whereHas('faculty', function($q) {
                $q->where('department', auth()->user()->department);
            });
        }
        
        $currentPeriodQuery = SubjectLoadTracker::active()->forPeriod($currentYear, $currentSemester);
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $currentPeriodQuery->whereHas('faculty', function($q) {
                $q->where('department', auth()->user()->department);
            });
        }
        
        $stats = [
            'total_loads' => $statsQuery->count(),
            'total_faculties' => $statsQuery->distinct('faculty_id')->count(),
            'current_period_loads' => $currentPeriodQuery->count(),
            'total_units' => $statsQuery->sum('units'),
            'total_hours' => $statsQuery->sum('hours_per_week')
        ];

        // Recent assignments - filter by department
        $recentLoadsQuery = SubjectLoadTracker::with('faculty')->orderBy('created_at', 'desc');
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $recentLoadsQuery->whereHas('faculty', function($q) {
                $q->where('department', auth()->user()->department);
            });
        }
        $recentLoads = $recentLoadsQuery->limit(10)->get();

        // Faculty with highest loads - filter by department
        $facultyLoadsQuery = SubjectLoadTracker::select('faculty_id')
                                         ->selectRaw('SUM(units) as total_units')
                                         ->selectRaw('SUM(hours_per_week) as total_hours')
                                         ->with('faculty')
                                         ->active()
                                         ->forPeriod($currentYear, $currentSemester);
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $facultyLoadsQuery->whereHas('faculty', function($q) {
                $q->where('department', auth()->user()->department);
            });
        }
        $facultyLoads = $facultyLoadsQuery->groupBy('faculty_id')
                                         ->orderBy('total_units', 'desc')
                                         ->limit(10)
                                         ->get();

        return view('admin.subject_loads.dashboard', compact('stats', 'recentLoads', 'facultyLoads'));
    }

    /**
     * Generate summary report.
     */
    public function report(Request $request)
    {
        $academicYear = $request->get('academic_year', date('Y'));
        $semester = $request->get('semester', '1st Semester');

        // Filter faculties by department
        $facultyQuery = Faculty::with(['subjectLoads' => function($query) use ($academicYear, $semester) {
            $query->active()->forPeriod($academicYear, $semester);
        }])->where('status', 'active');
        
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $facultyQuery->where('department', auth()->user()->department);
        }
        
        $facultyLoads = $facultyQuery->orderBy('name')->get();

        // Filter summary statistics by department
        $summaryQuery = SubjectLoadTracker::active()->forPeriod($academicYear, $semester);
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $summaryQuery->whereHas('faculty', function($q) {
                $q->where('department', auth()->user()->department);
            });
        }
        
        $summary = [
            'total_faculties' => $facultyLoads->count(),
            'total_loads' => $summaryQuery->count(),
            'total_units' => $summaryQuery->sum('units'),
            'total_hours' => $summaryQuery->sum('hours_per_week'),
            'average_units_per_faculty' => $facultyLoads->count() > 0 ? 
                $summaryQuery->sum('units') / $facultyLoads->count() : 0,
            'academic_year' => $academicYear,
            'semester' => $semester
        ];

        return view('admin.subject_loads.report', compact('facultyLoads', 'summary'));
    }

    /**
     * Export subject loads to CSV.
     */
    public function export(Request $request)
    {
        $query = SubjectLoadTracker::with('faculty');

        // Apply same filters as index
        if ($request->filled('faculty_id')) {
            $query->where('faculty_id', $request->faculty_id);
        }
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $subjectLoads = $query->orderBy('academic_year', 'desc')
                             ->orderBy('semester')
                             ->get();

        $filename = 'subject_loads_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($subjectLoads) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Faculty Name',
                'Professor ID',
                'Subject Code',
                'Subject Name',
                'Section',
                'Units',
                'Hours/Week',
                'Schedule',
                'Room',
                'Academic Year',
                'Semester',
                'Status',
                'Created At'
            ]);

            // CSV Data
            foreach ($subjectLoads as $load) {
                fputcsv($file, [
                    $load->faculty->name,
                    $load->faculty->professor_id,
                    $load->subject_code,
                    $load->subject_name,
                    $load->section,
                    $load->units,
                    $load->hours_per_week,
                    $load->schedule_display,
                    $load->room,
                    $load->academic_year,
                    $load->semester,
                    ucfirst($load->status),
                    $load->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Check for conflicts via AJAX.
     */
    public function checkConflicts(Request $request)
    {
        $conflicts = [];

        // Check duplicate assignment
        if (SubjectLoadTracker::hasDuplicateAssignment(
            $request->faculty_id,
            $request->subject_code,
            $request->section,
            $request->academic_year,
            $request->semester,
            $request->exclude_id
        )) {
            $conflicts[] = [
                'type' => 'duplicate',
                'message' => 'Faculty already assigned to this subject and section.'
            ];
        }

        // Check schedule conflict
        if ($request->filled(['schedule_day', 'start_time', 'end_time'])) {
            $conflictingLoad = SubjectLoadTracker::hasScheduleConflict(
                $request->faculty_id,
                $request->schedule_day,
                $request->start_time,
                $request->end_time,
                $request->academic_year,
                $request->semester,
                $request->exclude_id
            );

            if ($conflictingLoad) {
                $conflicts[] = [
                    'type' => 'schedule',
                    'message' => 'Schedule conflict with ' . $conflictingLoad->subject_code . ' (' . $conflictingLoad->time_range . ')',
                    'conflicting_subject' => $conflictingLoad->subject_code,
                    'conflicting_schedule' => $conflictingLoad->schedule_display
                ];
            }
        }

        return response()->json([
            'has_conflicts' => count($conflicts) > 0,
            'conflicts' => $conflicts
        ]);
    }

    /**
     * Get faculty load summary via AJAX.
     */
    public function getFacultyLoad(Request $request)
    {
        $facultyId = $request->faculty_id;
        $academicYear = $request->academic_year;
        $semester = $request->semester;

        if (!$facultyId || !$academicYear || !$semester) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }

        $totalUnits = SubjectLoadTracker::getFacultyTotalUnits($facultyId, $academicYear, $semester);
        $totalHours = SubjectLoadTracker::getFacultyTotalHours($facultyId, $academicYear, $semester);
        $subjectCount = SubjectLoadTracker::where('faculty_id', $facultyId)
                                         ->where('academic_year', $academicYear)
                                         ->where('semester', $semester)
                                         ->where('status', SubjectLoadTracker::STATUS_ACTIVE)
                                         ->count();

        $loads = SubjectLoadTracker::where('faculty_id', $facultyId)
                                  ->where('academic_year', $academicYear)
                                  ->where('semester', $semester)
                                  ->where('status', SubjectLoadTracker::STATUS_ACTIVE)
                                  ->orderBy('schedule_day')
                                  ->orderBy('start_time')
                                  ->get();

        return response()->json([
            'total_units' => $totalUnits,
            'total_hours' => $totalHours,
            'subject_count' => $subjectCount,
            'loads' => $loads->map(function($load) {
                return [
                    'subject_code' => $load->subject_code,
                    'subject_name' => $load->subject_name,
                    'section' => $load->section,
                    'schedule' => $load->schedule_display,
                    'units' => $load->units,
                    'hours' => $load->hours_per_week
                ];
            })
        ]);
    }

    /**
     * Bulk update status.
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:subject_load_trackers,id',
            'status' => 'required|in:active,inactive,completed'
        ]);

        $updated = SubjectLoadTracker::whereIn('id', $request->ids)
                                   ->update(['status' => $request->status]);

        return redirect()->route('admin.subject-loads.index')
                        ->with('success', "Updated status for {$updated} subject loads.");
    }
}
