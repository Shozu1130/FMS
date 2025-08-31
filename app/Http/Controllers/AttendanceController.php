<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Show the attendance monitoring dashboard.
     */
    public function dashboard()
    {
        $faculty = Auth::guard('faculty')->user();
        
        // Mark this user as an attendance user when they access the dashboard
        session()->put('attendance_user', true);
        
        // Get today's attendance record
        $todayDate = now()->toDateString();
        $todayAttendance = Attendance::where('faculty_id', $faculty->id)
            ->whereDate('date', $todayDate)
            ->first();

        // Removed debug raw dump to avoid debug output in UI
        if ($todayAttendance) {
            $timeInFormatted = $todayAttendance->time_in ? $todayAttendance->time_in->format('H:i:s') : 'null';
            
        }
        
        // Test log entry to verify logging works
       
        
        // Get recent attendance history (last 30 days)
        $recentAttendance = Attendance::where('faculty_id', $faculty->id)
            ->where('date', '>=', now()->subDays(30))
            ->orderBy('date', 'desc')
            ->get();

      
        
        return view('attendance.dashboard', compact('todayAttendance', 'recentAttendance'));
    }

    /**
     * Handle time in with photo capture.
     */
    public function timeIn(Request $request)
    {
        $request->validate([
            'time_in_photo_data' => 'required|string',
            'time_in_location' => 'nullable|string',
            'notes' => 'nullable|string|max:500'
        ]);

        $faculty = Auth::guard('faculty')->user();
        $today = now()->toDateString();

        // Check if already logged in today
        $existingAttendance = Attendance::where('faculty_id', $faculty->id)
            ->whereDate('date', $today)
            ->first();

        if ($existingAttendance && $existingAttendance->time_in !== null) {
            return redirect()->route('attendance.dashboard')
                ->with('error', 'You have already logged in today.');
        }

        // Use current time with proper timezone
        $currentTime = Carbon::now();
        
        try {
            // Save photo
            $photoPath = $this->savePhoto($request->time_in_photo_data, 'time_in', $faculty->id, $today);
            
            // Create or update attendance record
            if (!$existingAttendance) {
                $attendance = new Attendance();
                $attendance->faculty_id = $faculty->id;
                $attendance->date = $today;
                $attendance->status = 'present';
            } else {
                $attendance = $existingAttendance;
            }

            $attendance->time_in = $currentTime;
            $attendance->time_in_photo = $photoPath;
            $attendance->time_in_location = $request->time_in_location;
            $attendance->notes = $request->notes;
            
            // Check if late (after 8:00 AM)
            $expectedTime = Carbon::parse($today)->setTime(8, 0, 0);
            if ($currentTime->gt($expectedTime)) {
                $attendance->status = 'late';
            }
            
            $saved = $attendance->save();

            if (!$saved) {
               
                return redirect()->route('attendance.dashboard')
                    ->with('error', 'Failed to save attendance record. Please try again.');
            }

            // Debug: Log the saved attendance record (removed raw time_in dump to avoid debug output in UI)
           

            return redirect()->route('attendance.dashboard')
                ->with('success', 'Time in recorded successfully at ' . $currentTime->format('h:i A'));

        } catch (\Exception $e) {
           
            return redirect()->route('attendance.dashboard')
                ->with('error', 'Error recording time in. Please try again.');
        }
    }

    /**
     * Handle time out with photo capture.
     */
    public function timeOut(Request $request)
    {
        $request->validate([
            'time_out_photo_data' => 'required|string',
            'time_out_location' => 'nullable|string',
            'notes' => 'nullable|string|max:500'
        ]);

        $faculty = Auth::guard('faculty')->user();
        $today = now()->toDateString();

        // Get today's attendance record
        $attendance = Attendance::where('faculty_id', $faculty->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance || !$attendance->time_in) {
            return redirect()->route('attendance.dashboard')
                ->with('error', 'Please log in first before logging out.');
        }

        if ($attendance->time_out) {
            return redirect()->route('attendance.dashboard')
                ->with('error', 'You have already logged out today.');
        }

        // Use current time with proper timezone
        $currentTime = Carbon::now();
        
        try {
            // Save photo
            $photoPath = $this->savePhoto($request->time_out_photo_data, 'time_out', $faculty->id, $today);
            
            $attendance->time_out = $currentTime;
            $attendance->time_out_photo = $photoPath;
            $attendance->time_out_location = $request->time_out_location;
            
            // Update notes if provided
            if ($request->notes) {
                $attendance->notes = $attendance->notes ? $attendance->notes . "\n" . $request->notes : $request->notes;
            }
            
            // Calculate total hours
            $attendance->calculateTotalHours();
            
            // Check if early departure (before 5:00 PM)
            $expectedTime = Carbon::parse($today)->setTime(17, 0, 0);
            if ($currentTime->lt($expectedTime)) {
                if ($attendance->status === 'late') {
                    $attendance->status = 'half_day';
                } else {
                    $attendance->status = 'early_departure';
                }
            }
            
            $attendance->save();

            

            return redirect()->route('attendance.dashboard')
                ->with('success', 'Time out recorded successfully at ' . $currentTime->format('h:i A') . '. Total hours: ' . $attendance->formatted_total_hours);

        } catch (\Exception $e) {
            
            return redirect()->route('attendance.dashboard')
                ->with('error', 'Error recording time out. Please try again.');
        }
    }

    /**
     * Show attendance details.
     */
    public function showDetails($id)
    {
        $faculty = Auth::guard('faculty')->user();
        
        $attendance = Attendance::where('id', $id)
            ->where('faculty_id', $faculty->id)
            ->firstOrFail();

        return view('attendance.details', compact('attendance'));
    }

    /**
     * Save photo from base64 data.
     */
    private function savePhoto($base64Data, $type, $facultyId, $date)
    {
        try {
            // Remove data:image/jpeg;base64, prefix if present
            $base64Data = preg_replace('/^data:image\/\w+;base64,/', '', $base64Data);
            
            // Decode base64
            $imageData = base64_decode($base64Data);
            
            if ($imageData === false) {
                throw new \Exception('Invalid base64 image data');
            }

            // Generate filename
            $filename = "attendance_{$type}_{$facultyId}_{$date}_" . time() . ".jpg";
            
            // Save to storage - ensure directory exists
            $path = "attendance_photos/{$facultyId}/{$date}";
            
            // Create directory if it doesn't exist
            if (!Storage::disk('public')->exists($path)) {
                Storage::disk('public')->makeDirectory($path);
            }
            
            Storage::disk('public')->put("{$path}/{$filename}", $imageData);
            
            return "{$path}/{$filename}";
            
        } catch (\Exception $e) {
           
            throw $e;
        }
    }

    /**
     * Get attendance statistics for the current month.
     */
    public function getMonthlyStats()
    {
        $faculty = Auth::guard('faculty')->user();
        $currentMonth = now()->startOfMonth();
        
        $monthlyAttendance = Attendance::where('faculty_id', $faculty->id)
            ->whereYear('date', $currentMonth->year)
            ->whereMonth('date', $currentMonth->month)
            ->get();
        
        $stats = [
            'total_days' => $monthlyAttendance->count(),
            'present_days' => $monthlyAttendance->where('status', 'present')->count(),
            'late_days' => $monthlyAttendance->where('status', 'late')->count(),
            'absent_days' => $monthlyAttendance->where('status', 'absent')->count(),
            'total_hours' => $monthlyAttendance->sum('total_hours'),
            'average_hours_per_day' => $monthlyAttendance->avg('total_hours')
        ];
        
        return response()->json($stats);
    }
}

