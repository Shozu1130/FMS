<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubjectLoadTracker;
use App\Models\Faculty;
use Illuminate\Http\Request;

class ScheduleSearchController extends Controller
{
    public function index(Request $request)
    {
        // Filter faculties by department
        $facultiesQuery = Faculty::orderBy('name');
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $facultiesQuery->where('department', auth()->user()->department);
        }
        $faculties = $facultiesQuery->get();
        $academicYears = SubjectLoadTracker::distinct()->pluck('academic_year')->filter()->sort()->values();
        
        // Only show results if there's a search query or filter applied
        $hasSearchCriteria = $request->filled(['search', 'faculty_id', 'academic_year', 'semester', 'status', 'schedule_day', 'year_level']);
        
        $schedules = collect();
        $stats = [
            'total_assignments' => 0,
            'active_faculty' => 0,
            'total_units' => 0,
            'total_hours' => 0,
            'schedule_conflicts' => []
        ];
        
        if ($hasSearchCriteria) {
            $query = SubjectLoadTracker::with('faculty')
                ->where('status', 'active'); // Only show active assignments
            
            // Apply search filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('subject_code', 'like', "%{$search}%")
                      ->orWhere('subject_name', 'like', "%{$search}%")
                      ->orWhere('section', 'like', "%{$search}%")
                      ->orWhere('room', 'like', "%{$search}%")
                      ->orWhereHas('faculty', function($fq) use ($search) {
                          $fq->where('name', 'like', "%{$search}%")
                            ->orWhere('professor_id', 'like', "%{$search}%");
                      });
                });
            }
            
            if ($request->filled('faculty_id')) {
                $query->where('faculty_id', $request->faculty_id);
            }
            
            if ($request->filled('academic_year')) {
                $query->where('academic_year', $request->academic_year);
            }
            
            if ($request->filled('semester')) {
                $query->where('semester', $request->semester);
            }
            
            if ($request->filled('schedule_day')) {
                $query->where('schedule_day', $request->schedule_day);
            }
            
            if ($request->filled('year_level')) {
                $query->where('year_level', $request->year_level);
            }
            
            $schedules = $query->orderBy('schedule_day')
                             ->orderBy('start_time')
                             ->paginate($request->get('per_page', 15))
                             ->appends($request->query());
            
            // Calculate statistics
            $allSchedules = $query->get();
            $stats = [
                'total_assignments' => $allSchedules->count(),
                'active_faculty' => $allSchedules->pluck('faculty_id')->unique()->count(),
                'total_units' => $allSchedules->sum('units'),
                'total_hours' => $allSchedules->sum('hours_per_week'),
                'schedule_conflicts' => $this->detectConflicts($allSchedules)
            ];
        } else {
            // Create empty paginator for no search criteria
            $schedules = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(),
                0,
                15,
                1,
                ['path' => $request->url(), 'pageName' => 'page']
            );
        }
        
        return view('admin.schedule_search.index', compact(
            'schedules',
            'faculties', 
            'academicYears',
            'stats',
            'hasSearchCriteria'
        ));
    }
    
    private function detectConflicts($schedules)
    {
        $conflicts = [];
        $grouped = $schedules->groupBy(['faculty_id', 'schedule_day']);
        
        foreach ($grouped as $facultyId => $facultySchedules) {
            foreach ($facultySchedules as $day => $daySchedules) {
                $sorted = $daySchedules->sortBy('start_time');
                
                for ($i = 0; $i < $sorted->count() - 1; $i++) {
                    $current = $sorted->values()[$i];
                    $next = $sorted->values()[$i + 1];
                    
                    if ($current->end_time > $next->start_time) {
                        $conflicts[] = [
                            'faculty' => $current->faculty->name,
                            'day' => ucfirst($day),
                            'subject1' => $current->subject_code,
                            'time1' => $current->start_time . ' - ' . $current->end_time,
                            'subject2' => $next->subject_code,
                            'time2' => $next->start_time . ' - ' . $next->end_time,
                        ];
                    }
                }
            }
        }
        
        return $conflicts;
    }
    
    public function export(Request $request)
    {
        $query = SubjectLoadTracker::with('faculty')->where('status', 'active');
        
        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject_code', 'like', "%{$search}%")
                  ->orWhere('subject_name', 'like', "%{$search}%")
                  ->orWhere('section', 'like', "%{$search}%")
                  ->orWhere('room', 'like', "%{$search}%")
                  ->orWhereHas('faculty', function($fq) use ($search) {
                      $fq->where('name', 'like', "%{$search}%")
                        ->orWhere('professor_id', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($request->filled('faculty_id')) {
            $query->where('faculty_id', $request->faculty_id);
        }
        
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }
        
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }
        
        if ($request->filled('schedule_day')) {
            $query->where('schedule_day', $request->schedule_day);
        }
        
        if ($request->filled('year_level')) {
            $query->where('year_level', $request->year_level);
        }
        
        $schedules = $query->orderBy('schedule_day')->orderBy('start_time')->get();
        
        $filename = 'schedule_search_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($schedules) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Subject Code',
                'Subject Name',
                'Section',
                'Year Level',
                'Faculty Name',
                'Professor ID',
                'Schedule Day',
                'Start Time',
                'End Time',
                'Room',
                'Units',
                'Hours Per Week',
                'Academic Year',
                'Semester',
                'Status'
            ]);
            
            // CSV data
            foreach ($schedules as $schedule) {
                fputcsv($file, [
                    $schedule->subject_code,
                    $schedule->subject_name,
                    $schedule->section,
                    $schedule->year_level,
                    $schedule->faculty->name,
                    $schedule->faculty->professor_id,
                    ucfirst($schedule->schedule_day),
                    $schedule->start_time,
                    $schedule->end_time,
                    $schedule->room,
                    $schedule->units,
                    $schedule->hours_per_week,
                    $schedule->academic_year,
                    $schedule->semester,
                    ucfirst($schedule->status)
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
