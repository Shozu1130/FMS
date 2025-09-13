<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\SubjectLoadTracker;
use App\Models\ScheduleAssignment;
use App\Models\Attendance;
use App\Models\ClearanceRequest;
use App\Services\ScheduleService;

class DashboardController extends Controller
{
    protected $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    public function index()
    {
        // Check if user is Master Admin - redirect to Master Admin dashboard
        if (auth()->user()->isMasterAdmin()) {
            return $this->masterAdminDashboard();
        }

        $currentYear = date('Y');
        $currentSemester = $this->getCurrentSemester();
        
        // Get current admin's department for filtering
        $adminDepartment = auth()->user()->department;
        $isMasterAdmin = false; // Regular admin dashboard

        // Core Statistics - filtered by department for regular admins
        $facultyQuery = Faculty::query();
        if ($adminDepartment) {
            $facultyQuery->where('department', $adminDepartment);
        }
        $facultyCount = $facultyQuery->count();
        // Filter subject loads by department if not master admin
        $subjectLoadQuery = SubjectLoadTracker::where('academic_year', $currentYear)
                                              ->where('semester', $currentSemester);
        if ($adminDepartment) {
            $subjectLoadQuery->whereHas('faculty', function($q) use ($adminDepartment) {
                $q->where('department', $adminDepartment);
            });
        }
        $totalSubjectLoads = $subjectLoadQuery->count();
        
        $activeSubjectLoadQuery = SubjectLoadTracker::where('academic_year', $currentYear)
                                                   ->where('semester', $currentSemester)
                                                   ->where('status', 'active');
        if ($adminDepartment) {
            $activeSubjectLoadQuery->whereHas('faculty', function($q) use ($adminDepartment) {
                $q->where('department', $adminDepartment);
            });
        }
        $activeSubjectLoads = $activeSubjectLoadQuery->count();

        // Subject Load Tracker Statistics
        $subjectLoadStats = [
            'total_assignments' => $totalSubjectLoads,
            'active_assignments' => $activeSubjectLoads,
            'inactive_assignments' => $totalSubjectLoads - $activeSubjectLoads,
            'total_units' => $subjectLoadQuery->sum('units') ?? 0,
            'total_hours' => $subjectLoadQuery->sum('hours_per_week') ?? 0,
        ];

        // Faculty Workload Analysis - filtered by department
        $workloadDistribution = $this->scheduleService->getFacultyWorkloadDistribution($currentYear, $currentSemester);
        
        // Filter workload distribution by department
        if ($adminDepartment) {
            $departmentFacultyIds = Faculty::where('department', $adminDepartment)->pluck('id')->toArray();
            $workloadDistribution = collect($workloadDistribution)->filter(function($item) use ($departmentFacultyIds) {
                return in_array($item['faculty']->id, $departmentFacultyIds);
            })->values()->toArray();
        }
        
        $overloadedFaculty = collect($workloadDistribution)->where('workload_status.status', 'overloaded')->count();
        $fullLoadFaculty = collect($workloadDistribution)->where('workload_status.status', 'full_load')->count();

        // Recent Activity - filtered by department
        $recentSubjectLoadsQuery = SubjectLoadTracker::with('faculty')
                                                     ->where('academic_year', $currentYear)
                                                     ->where('semester', $currentSemester);
        if ($adminDepartment) {
            $recentSubjectLoadsQuery->whereHas('faculty', function($q) use ($adminDepartment) {
                $q->where('department', $adminDepartment);
            });
        }
        $recentSubjectLoads = $recentSubjectLoadsQuery->orderBy('created_at', 'desc')
                                                     ->take(5)
                                                     ->get();

        // Schedule Conflicts
        $conflictCount = $this->getScheduleConflictCount($currentYear, $currentSemester);

        // Additional Stats - filtered by department
        $pendingClearanceQuery = ClearanceRequest::where('status', 'pending');
        if ($adminDepartment) {
            $pendingClearanceQuery->whereHas('faculty', function($q) use ($adminDepartment) {
                $q->where('department', $adminDepartment);
            });
        }
        $pendingClearanceRequests = $pendingClearanceQuery->count();
        $todayAttendance = Attendance::whereDate('date', today())->count();

        return view('admin.dashboard', compact(
            'facultyCount', 'subjectLoadStats', 'workloadDistribution', 
            'overloadedFaculty', 'fullLoadFaculty', 'recentSubjectLoads',
            'conflictCount', 'pendingClearanceRequests', 'todayAttendance',
            'currentYear', 'currentSemester', 'adminDepartment', 'isMasterAdmin'
        ));
    }

    public function masterAdminDashboard()
    {
        // Get admin management statistics
        $totalAdmins = \App\Models\User::whereIn('role', ['admin', 'master_admin'])->count();
        $masterAdmins = \App\Models\User::where('role', 'master_admin')->count();
        $regularAdmins = \App\Models\User::where('role', 'admin')->count();
        
        // Get department distribution
        $departmentStats = \App\Models\User::where('role', 'admin')
            ->selectRaw('department, COUNT(*) as count')
            ->groupBy('department')
            ->get();
        
        // Recent admin activities
        $recentAdmins = \App\Models\User::whereIn('role', ['admin', 'master_admin'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.master-admin-dashboard', compact(
            'totalAdmins', 'masterAdmins', 'regularAdmins', 
            'departmentStats', 'recentAdmins'
        ));
    }

    private function getCurrentSemester()
    {
        $month = date('n');
        if ($month >= 1 && $month <= 5) return '2nd Semester';
        if ($month >= 6 && $month <= 10) return '1st Semester';
        return 'Summer';
    }

    private function getScheduleConflictCount($year, $semester)
    {
        $conflicts = 0;
        $assignmentsQuery = SubjectLoadTracker::where('academic_year', $year)
                                        ->where('semester', $semester)
                                        ->where('status', 'active');
        
        // This method should not be called for Master Admins
        $adminDepartment = auth()->user()->department;
        if ($adminDepartment) {
            $assignmentsQuery->whereHas('faculty', function($q) use ($adminDepartment) {
                $q->where('department', $adminDepartment);
            });
        }
        
        $assignments = $assignmentsQuery->get();

        foreach ($assignments as $assignment) {
            $conflict = SubjectLoadTracker::where('professor_id', $assignment->professor_id)
                                         ->where('academic_year', $year)
                                         ->where('semester', $semester)
                                         ->where('schedule_day', $assignment->schedule_day)
                                         ->where('id', '!=', $assignment->id)
                                         ->where(function($query) use ($assignment) {
                                             $query->whereBetween('start_time', [$assignment->start_time, $assignment->end_time])
                                                   ->orWhereBetween('end_time', [$assignment->start_time, $assignment->end_time])
                                                   ->orWhere(function($q) use ($assignment) {
                                                       $q->where('start_time', '<=', $assignment->start_time)
                                                         ->where('end_time', '>=', $assignment->end_time);
                                                   });
                                         })
                                         ->exists();
            if ($conflict) $conflicts++;
        }

        return $conflicts;
    }
}