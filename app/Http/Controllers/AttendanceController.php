<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function dashboard()
    {
        $faculty = Auth::guard('faculty')->user();
        session()->put('attendance_user', true);

        $todayDate = now()->toDateString();
        $todayAttendance = Attendance::where('faculty_id', $faculty->id)
            ->whereDate('date', $todayDate)
            ->first();

        $recentAttendance = Attendance::where('faculty_id', $faculty->id)
            ->where('date', '>=', now()->subDays(30))
            ->orderBy('date', 'desc')
            ->get();

        return view('attendance.dashboard', compact('todayAttendance', 'recentAttendance'));
    }

    public function timeIn(Request $request)
    {
        $request->validate([
            'time_in_photo_data' => 'required|string|min:100',
            'time_in_location' => 'nullable|string',
            'notes' => 'nullable|string|max:500'
        ]);

        $faculty = Auth::guard('faculty')->user();
        $today = now()->toDateString();

        $existingAttendance = Attendance::where('faculty_id', $faculty->id)
            ->whereDate('date', $today)
            ->first();

        if ($existingAttendance && $existingAttendance->time_in) {
            return redirect()->route('attendance.dashboard')
                ->with('error', 'You have already logged in today.');
        }

        if (!preg_match('/^data:image\/(jpeg|jpg|png);base64,/', $request->time_in_photo_data)) {
            return redirect()->route('attendance.dashboard')
                ->with('error', 'Invalid photo format. Please capture a new photo.');
        }

        $currentTime = Carbon::now();

        try {
            $photoPath = $this->savePhoto($request->time_in_photo_data, 'time_in', $faculty->id, $today);

            $attendance = $existingAttendance ?? new Attendance();
            $attendance->faculty_id = $faculty->id;
            $attendance->date = $today;
            $attendance->time_in = $currentTime;
            $attendance->time_in_photo = $photoPath;
            $attendance->time_in_location = $request->time_in_location;
            $attendance->notes = $request->notes;
            $attendance->status = $currentTime->gt(Carbon::parse($today)->setTime(8, 0, 0)) ? 'late' : 'present';

            $attendance->save();

            return redirect()->route('attendance.dashboard')
                ->with('success', 'Time in recorded successfully at ' . $currentTime->format('h:i A'));

        } catch (\Exception $e) {
            \Log::error('Time in error: ' . $e->getMessage(), [
                'faculty_id' => $faculty->id,
                'date' => $today,
            ]);

            return redirect()->route('attendance.dashboard')
                ->with('error', 'Error recording time in. Please try again.');
        }
    }

    public function timeOut(Request $request)
    {
        $request->validate([
            'time_out_photo_data' => 'required|string',
            'time_out_location' => 'nullable|string',
            'notes' => 'nullable|string|max:500'
        ]);

        $faculty = Auth::guard('faculty')->user();
        $today = now()->toDateString();

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

        $currentTime = Carbon::now();

        try {
            $photoPath = $this->savePhoto($request->time_out_photo_data, 'time_out', $faculty->id, $today);

            $attendance->time_out = $currentTime;
            $attendance->time_out_photo = $photoPath;
            $attendance->time_out_location = $request->time_out_location;
            if ($request->notes) {
                $attendance->notes = $attendance->notes ? $attendance->notes . "\n" . $request->notes : $request->notes;
            }

            $attendance->calculateTotalHours();

            $expectedTime = Carbon::parse($today)->setTime(17, 0, 0);
            if ($currentTime->lt($expectedTime)) {
                $attendance->status = $attendance->status === 'late' ? 'half_day' : 'early_departure';
            }

            $attendance->save();

            return redirect()->route('attendance.dashboard')
                ->with('success', 'Time out recorded successfully at ' . $currentTime->format('h:i A') . '. Total hours: ' . $attendance->formatted_total_hours);

        } catch (\Exception $e) {
            \Log::error('Time out error: ' . $e->getMessage(), [
                'faculty_id' => $faculty->id,
                'date' => $today,
            ]);

            return redirect()->route('attendance.dashboard')
                ->with('error', 'Error recording time out. Please try again.');
        }
    }

    public function showDetails($id)
    {
        $faculty = Auth::guard('faculty')->user();
        $attendance = Attendance::where('id', $id)
            ->where('faculty_id', $faculty->id)
            ->firstOrFail();

        return view('attendance.details', compact('attendance'));
    }

    private function savePhoto($base64Data, $type, $facultyId, $date)
    {
        $base64Data = preg_replace('/^data:image\/\w+;base64,/', '', $base64Data);
        $imageData = base64_decode($base64Data);

        if (!$imageData || strlen($imageData) < 1000) {
            throw new \Exception('Invalid or too small image data.');
        }

        $filename = "attendance_{$type}_{$facultyId}_{$date}_" . time() . ".jpg";
        $path = "attendance_photos/{$facultyId}/{$date}";

        if (!Storage::disk('public')->exists($path)) {
            Storage::disk('public')->makeDirectory($path, 0755, true);
        }

        $success = Storage::disk('public')->put("{$path}/{$filename}", $imageData);
        if (!$success) {
            throw new \Exception('Failed to save photo to storage.');
        }

        return "{$path}/{$filename}";
    }

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
            'average_hours_per_day' => $monthlyAttendance->avg('total_hours'),
        ];

        return response()->json($stats);
    }
}
