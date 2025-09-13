<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\SubjectLoadTracker;
use App\Models\ScheduleAssignment;

class DashboardController extends Controller
{
    public function index()
    {
        $professor = Auth::guard('faculty')->user();
        
        $currentSalaryGrade = $professor->getCurrentSalaryGrade();
        
        // Get recent attendance history (last 30 days)
        $recentAttendance = Attendance::where('professor_id', $professor->id)
            ->where('date', '>=', now()->subDays(30))
            ->orderBy('date', 'desc')
            ->get();
        
        // Get current schedule overview
        $currentYear = now()->year;
        $currentSemester = $this->getCurrentSemester();
        
        // Get today's schedule
        $today = strtolower(now()->format('l'));
        $todaySchedule = collect();
        
        // Get from Subject Load Tracker
        $todaySubjectLoads = SubjectLoadTracker::where('professor_id', $professor->id)
            ->where('academic_year', $currentYear)
            ->where('semester', $currentSemester)
            ->where('schedule_day', $today)
            ->where('status', 'active')
            ->orderBy('start_time')
            ->get();
        
        // Get from Schedule Assignment
        $todayScheduleAssignments = ScheduleAssignment::where('professor_id', $professor->id)
            ->where('academic_year', $currentYear)
            ->where('semester', $currentSemester)
            ->where('schedule_day', $today)
            ->where('status', 'active')
            ->orderBy('start_time')
            ->get();
        
        // Add source information
        $todaySubjectLoads->each(function ($item) {
            $item->source_name = 'Subject Load Tracker';
            $item->source_color = 'success';
        });
        
        $todayScheduleAssignments->each(function ($item) {
            $item->source_name = 'Schedule Assignment';
            $item->source_color = 'primary';
        });
        
        $todaySchedule = $todaySubjectLoads->concat($todayScheduleAssignments)
            ->sortBy('start_time');
        
        // Get current period summary
        $allCurrentSchedules = SubjectLoadTracker::where('professor_id', $professor->id)
            ->where('academic_year', $currentYear)
            ->where('semester', $currentSemester)
            ->where('status', 'active')
            ->get()
            ->concat(
                ScheduleAssignment::where('professor_id', $professor->id)
                    ->where('academic_year', $currentYear)
                    ->where('semester', $currentSemester)
                    ->where('status', 'active')
                    ->get()
            );
        
        $scheduleOverview = [
            'total_subjects' => $allCurrentSchedules->count(),
            'total_units' => $allCurrentSchedules->sum('units'),
            'total_hours' => $allCurrentSchedules->sum('hours_per_week'),
            'today_classes' => $todaySchedule->count(),
            'academic_year' => $currentYear,
            'semester' => $currentSemester
        ];
        
        $workloadStatus = ScheduleAssignment::getWorkloadStatus($scheduleOverview['total_hours']);
        $scheduleOverview['workload_status'] = $workloadStatus;
        
        return view('professor.dashboard', compact(
            'professor', 
            'currentSalaryGrade', 
            'recentAttendance',
            'todaySchedule',
            'scheduleOverview'
        ));
    }
    
    /**
     * Show detailed attendance history for the professor.
     */
    public function attendanceHistory()
    {
        $professor = Auth::guard('faculty')->user();
        
        // Get all attendance records for the professor
        $attendanceRecords = Attendance::where('professor_id', $professor->id)
            ->orderBy('date', 'desc')
            ->paginate(20);
        
        // Get monthly statistics
        $currentMonth = now()->startOfMonth();
        $monthlyStats = Attendance::where('professor_id', $professor->id)
            ->whereYear('date', $currentMonth->year)
            ->whereMonth('date', $currentMonth->month)
            ->get();
        
        $stats = [
            'total_days' => $monthlyStats->count(),
            'present_days' => $monthlyStats->where('status', 'present')->count(),
            'late_days' => $monthlyStats->where('status', 'late')->count(),
            'absent_days' => $monthlyStats->where('status', 'absent')->count(),
            'total_hours' => $monthlyStats->sum('total_hours'),
            'average_hours_per_day' => $monthlyStats->avg('total_hours')
        ];
        
        return view('professor.attendance.history', compact('professor', 'attendanceRecords', 'stats'));
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
