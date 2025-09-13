<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\SubjectLoadTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubjectLoadController extends Controller
{
    /**
     * Display a listing of the professor's subject loads.
     */
    public function index(Request $request)
    {
        $faculty = Auth::guard('faculty')->user();
        
        $query = SubjectLoadTracker::where('professor_id', $faculty->id);
        
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
        
        $subjectLoads = $query->orderBy('academic_year', 'desc')
                             ->orderBy('semester', 'desc')
                             ->orderBy('schedule_day')
                             ->orderBy('start_time')
                             ->paginate(15);
        
        // Get available academic years and semesters for filters
        $academicYears = SubjectLoadTracker::where('professor_id', $faculty->id)
                                          ->distinct()
                                          ->pluck('academic_year')
                                          ->sort()
                                          ->reverse();
        
        $semesters = SubjectLoadTracker::getSemesters();
        
        // Get current period summary
        $currentYear = now()->year;
        $currentSemester = now()->month <= 5 ? '2nd Semester' : '1st Semester';
        
        $currentLoads = SubjectLoadTracker::where('professor_id', $faculty->id)
                                         ->where('academic_year', $currentYear)
                                         ->where('semester', $currentSemester)
                                         ->where('status', 'active')
                                         ->get();
        
        $summary = [
            'total_subjects' => $currentLoads->count(),
            'total_units' => $currentLoads->sum('units'),
            'total_hours' => $currentLoads->sum('hours_per_week'),
            'academic_year' => $currentYear,
            'semester' => $currentSemester
        ];
        
        return view('professor.subject_loads.index', compact(
            'subjectLoads', 
            'academicYears', 
            'semesters', 
            'summary'
        ));
    }
    
    /**
     * Display the specified subject load.
     */
    public function show(SubjectLoadTracker $subjectLoad)
    {
        $faculty = Auth::guard('faculty')->user();
        
        // Ensure the subject load belongs to the authenticated professor
        if ($subjectLoad->professor_id !== $faculty->id) {
            abort(403, 'Unauthorized access to subject load.');
        }
        
        // Get other loads for the same academic period
        $otherLoads = SubjectLoadTracker::where('professor_id', $faculty->id)
                                       ->where('academic_year', $subjectLoad->academic_year)
                                       ->where('semester', $subjectLoad->semester)
                                       ->where('id', '!=', $subjectLoad->id)
                                       ->orderBy('schedule_day')
                                       ->orderBy('start_time')
                                       ->get();
        
        $periodSummary = [
            'total_subjects' => $otherLoads->count() + 1,
            'total_units' => $otherLoads->sum('units') + $subjectLoad->units,
            'total_hours' => $otherLoads->sum('hours_per_week') + $subjectLoad->hours_per_week
        ];
        
        return view('professor.subject_loads.show', compact(
            'subjectLoad', 
            'otherLoads', 
            'periodSummary'
        ));
    }
    
    /**
     * Get professor's schedule for a specific academic period.
     */
    public function schedule(Request $request)
    {
        $faculty = Auth::guard('faculty')->user();
        
        $academicYear = $request->get('academic_year', now()->year);
        $semester = $request->get('semester', now()->month <= 5 ? '2nd Semester' : '1st Semester');
        
        $loads = SubjectLoadTracker::where('professor_id', $faculty->id)
                                  ->where('academic_year', $academicYear)
                                  ->where('semester', $semester)
                                  ->where('status', 'active')
                                  ->orderBy('schedule_day')
                                  ->orderBy('start_time')
                                  ->get();
        
        // Group by day for schedule display
        $schedule = [];
        $days = SubjectLoadTracker::getDays();
        
        foreach ($days as $dayKey => $dayName) {
            $schedule[$dayKey] = $loads->where('schedule_day', $dayKey);
        }
        
        $summary = [
            'total_subjects' => $loads->count(),
            'total_units' => $loads->sum('units'),
            'total_hours' => $loads->sum('hours_per_week'),
            'academic_year' => $academicYear,
            'semester' => $semester
        ];
        
        // Get available academic years for filter
        $academicYears = SubjectLoadTracker::where('professor_id', $faculty->id)
                                          ->distinct()
                                          ->pluck('academic_year')
                                          ->sort()
                                          ->reverse();
        
        $semesters = SubjectLoadTracker::getSemesters();
        
        return view('professor.subject_loads.schedule', compact(
            'schedule', 
            'summary', 
            'academicYears', 
            'semesters',
            'days'
        ));
    }
}
