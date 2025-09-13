<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display a listing of all attendance records.
     */
    public function index(Request $request)
    {
        $query = Attendance::with('faculty');

        // Filter by department if not master admin
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $query->whereHas('faculty', function($q) {
                $q->where('department', auth()->user()->department);
            });
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->where('date', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->where('date', '<=', $request->end_date);
        } else {
            // Default to current month
            $query->whereYear('date', now()->year)
                  ->whereMonth('date', now()->month);
        }

        // Filter by faculty
        if ($request->filled('professor_id')) {
            $query->where('professor_id', $request->professor_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->orderBy('date', 'desc')
                            ->orderBy('professor_id')
                            ->paginate(20);

        // Filter faculties by department
        $facultiesQuery = Faculty::orderBy('name');
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $facultiesQuery->where('department', auth()->user()->department);
        }
        $faculties = $facultiesQuery->get();
        
        // Get summary statistics
        $summary = $this->getAttendanceSummary($request);

        return view('admin.attendance.index', compact('attendances', 'faculties', 'summary'));
    }

    /**
     * Show the form for creating a new attendance record.
     */
    public function create()
    {
        $faculties = Faculty::orderBy('name')->get();
        return view('admin.attendance.create', compact('faculties'));
    }

    /**
     * Store a newly created attendance record.
     */
    public function store(Request $request)
    {
        $request->validate([
            'professor_id' => 'required|exists:faculties,id',
            'date' => 'required|date',
            'time_in' => 'nullable|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:present,absent,late,early_departure,half_day',
            'notes' => 'nullable|string|max:500'
        ]);

        // Check if attendance record already exists for this faculty and date
        $existingAttendance = Attendance::where('professor_id', $request->professor_id)
            ->where('date', $request->date)
            ->first();

        if ($existingAttendance) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Attendance record already exists for this faculty on the selected date.');
        }

        try {
            $attendance = new Attendance();
            $attendance->professor_id = $request->professor_id;
            $attendance->date = $request->date;
            $attendance->status = $request->status;
            $attendance->notes = $request->notes;

            // Set time in if provided
            if ($request->time_in) {
                $attendance->time_in = Carbon::parse($request->date . ' ' . $request->time_in);
            }

            // Set time out if provided
            if ($request->time_out) {
                $attendance->time_out = Carbon::parse($request->date . ' ' . $request->time_out);
            }

            // Calculate total hours if both times are provided
            if ($attendance->time_in && $attendance->time_out) {
                $attendance->calculateTotalHours();
            }

            $attendance->save();

            return redirect()->route('admin.attendance.index')
                ->with('success', 'Attendance record created successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating attendance record: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified attendance record.
     */
    public function show($id)
    {
        $attendance = Attendance::with('faculty')->findOrFail($id);
        return view('admin.attendance.show', compact('attendance'));
    }

    /**
     * Show the form for editing the specified attendance record.
     */
    public function edit($id)
    {
        $attendance = Attendance::findOrFail($id);
        $faculties = Faculty::orderBy('name')->get();
        return view('admin.attendance.edit', compact('attendance', 'faculties'));
    }

    /**
     * Update the specified attendance record.
     */
    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $request->validate([
            'professor_id' => 'required|exists:faculties,id',
            'date' => 'required|date',
            'time_in' => 'nullable|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:present,absent,late,early_departure,half_day',
            'notes' => 'nullable|string|max:500'
        ]);

        // Check if attendance record already exists for this faculty and date (excluding current record)
        $existingAttendance = Attendance::where('professor_id', $request->professor_id)
            ->where('date', $request->date)
            ->where('id', '!=', $id)
            ->first();

        if ($existingAttendance) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Attendance record already exists for this faculty on the selected date.');
        }

        try {
            $attendance->professor_id = $request->professor_id;
            $attendance->date = $request->date;
            $attendance->status = $request->status;
            $attendance->notes = $request->notes;

            // Set time in if provided
            if ($request->time_in) {
                $attendance->time_in = Carbon::parse($request->date . ' ' . $request->time_in);
            } else {
                $attendance->time_in = null;
            }

            // Set time out if provided
            if ($request->time_out) {
                $attendance->time_out = Carbon::parse($request->date . ' ' . $request->time_out);
            } else {
                $attendance->time_out = null;
            }

            // Calculate total hours if both times are provided
            if ($attendance->time_in && $attendance->time_out) {
                $attendance->calculateTotalHours();
            } else {
                $attendance->total_hours = 0;
            }

            $attendance->save();

            return redirect()->route('admin.attendance.index')
                ->with('success', 'Attendance record updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating attendance record: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified attendance record.
     */
    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);

        try {
            $attendance->delete();
            return redirect()->route('admin.attendance.index')
                ->with('success', 'Attendance record deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting attendance record: ' . $e->getMessage());
        }
    }

    /**
     * Get attendance summary statistics.
     */
    private function getAttendanceSummary(Request $request)
    {
        $query = Attendance::query();

        // Filter by department if not master admin
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $query->whereHas('faculty', function($q) {
                $q->where('department', auth()->user()->department);
            });
        }

        // Apply same filters as main query
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->where('date', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->where('date', '<=', $request->end_date);
        } else {
            $query->whereYear('date', now()->year)
                  ->whereMonth('date', now()->month);
        }

        if ($request->filled('professor_id')) {
            $query->where('professor_id', $request->professor_id);
        }

        $summary = $query->selectRaw('
            COUNT(*) as total_records,
            COUNT(CASE WHEN status = "present" THEN 1 END) as present_count,
            COUNT(CASE WHEN status = "absent" THEN 1 END) as absent_count,
            COUNT(CASE WHEN status = "late" THEN 1 END) as late_count,
            COUNT(CASE WHEN status = "early_departure" THEN 1 END) as early_departure_count,
            COUNT(CASE WHEN status = "half_day" THEN 1 END) as half_day_count,
            SUM(total_hours) as total_hours,
            AVG(total_hours) as average_hours
        ')->first();

        return $summary;
    }

    /**
     * Export attendance data to CSV.
     */
    public function export(Request $request)
    {
        $query = Attendance::with('faculty');

        // Apply filters
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->where('date', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->where('date', '<=', $request->end_date);
        } else {
            $query->whereYear('date', now()->year)
                  ->whereMonth('date', now()->month);
        }

        if ($request->filled('professor_id')) {
            $query->where('professor_id', $request->professor_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->orderBy('date', 'desc')
                            ->orderBy('professor_id')
                            ->get();

        $filename = 'attendance_report_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($attendances) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Date', 'Faculty ID', 'Faculty Name', 'Time In', 'Time Out', 
                'Total Hours', 'Status', 'Notes', 'Created At'
            ]);

            // CSV data
            foreach ($attendances as $attendance) {
                fputcsv($file, [
                    $attendance->date->format('Y-m-d'),
                    $attendance->faculty->professor_id,
                    $attendance->faculty->name,
                    $attendance->time_in ? $attendance->time_in->format('H:i:s') : '',
                    $attendance->time_out ? $attendance->time_out->format('H:i:s') : '',
                    $attendance->total_hours,
                    $attendance->status,
                    $attendance->notes,
                    $attendance->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show faculty attendance summary.
     */
    public function facultySummary()
    {
        // Filter faculties by department
        $facultiesQuery = Faculty::with(['attendances' => function($query) {
            $query->whereYear('date', now()->year)
                  ->whereMonth('date', now()->month);
        }]);
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $facultiesQuery->where('department', auth()->user()->department);
        }
        $faculties = $facultiesQuery->get();

        $summary = [];
        foreach ($faculties as $faculty) {
            $summary[] = [
                'faculty' => $faculty,
                'total_days' => $faculty->attendances->count(),
                'present_days' => $faculty->attendances->where('status', 'present')->count(),
                'late_days' => $faculty->attendances->where('status', 'late')->count(),
                'absent_days' => $faculty->attendances->where('status', 'absent')->count(),
                'total_hours' => $faculty->attendances->sum('total_hours'),
                'average_hours' => $faculty->attendances->avg('total_hours')
            ];
        }

        return view('admin.attendance.faculty-summary', compact('summary'));
    }
}
