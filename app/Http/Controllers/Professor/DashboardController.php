<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;

class DashboardController extends Controller
{
    public function index()
    {
        $professor = Auth::guard('faculty')->user();
        
        $currentSalaryGrade = $professor->getCurrentSalaryGrade();
        
        // Get recent attendance history (last 30 days)
        $recentAttendance = Attendance::where('faculty_id', $professor->id)
            ->where('date', '>=', now()->subDays(30))
            ->orderBy('date', 'desc')
            ->get();
        
        return view('professor.dashboard', compact('professor', 'currentSalaryGrade', 'recentAttendance'));
    }
    
    /**
     * Show detailed attendance history for the professor.
     */
    public function attendanceHistory()
    {
        $professor = Auth::guard('faculty')->user();
        
        // Get all attendance records for the professor
        $attendanceRecords = Attendance::where('faculty_id', $professor->id)
            ->orderBy('date', 'desc')
            ->paginate(20);
        
        // Get monthly statistics
        $currentMonth = now()->startOfMonth();
        $monthlyStats = Attendance::where('faculty_id', $professor->id)
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
}
