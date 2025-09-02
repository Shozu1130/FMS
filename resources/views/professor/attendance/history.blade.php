@extends('layouts.professor_admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">
        <i class="bi bi-clock"></i> My Attendance History
    </h1>
</div>

<!-- Monthly Statistics -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card bg-primary text-white text-center">
            <div class="card-body">
                <h4 class="mb-1">{{ $stats['total_days'] }}</h4>
                <small>Total Days</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-success text-white text-center">
            <div class="card-body">
                <h4 class="mb-1">{{ $stats['present_days'] }}</h4>
                <small>Present</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-warning text-white text-center">
            <div class="card-body">
                <h4 class="mb-1">{{ $stats['late_days'] }}</h4>
                <small>Late</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-danger text-white text-center">
            <div class="card-body">
                <h4 class="mb-1">{{ $stats['absent_days'] }}</h4>
                <small>Absent</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-info text-white text-center">
            <div class="card-body">
                <h4 class="mb-1">{{ number_format($stats['total_hours'], 1) }}</h4>
                <small>Total Hours</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-secondary text-white text-center">
            <div class="card-body">
                <h4 class="mb-1">{{ number_format($stats['average_hours_per_day'], 1) }}</h4>
                <small>Avg/Day</small>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Records Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-calendar-week"></i> Attendance Records
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Total Hours</th>
                        <th>Status</th>
                        <th>Location</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendanceRecords as $attendance)
                    <tr>
                        <td>
                            <strong>{{ $attendance->date->format('M j, Y') }}</strong>
                            <br>
                            <small class="text-muted">{{ $attendance->date->format('l') }}</small>
                        </td>
                        <td>
                            @if($attendance->time_in)
                                <span class="text-success">{{ $attendance->formatted_time_in }}</span>
                                @if($attendance->is_late)
                                    <br><span class="badge bg-warning">Late</span>
                                @endif
                            @else
                                <span class="text-muted">Not logged in</span>
                            @endif
                        </td>
                        <td>
                            @if($attendance->time_out)
                                <span class="text-info">{{ $attendance->formatted_time_out }}</span>
                                @if($attendance->is_early_departure)
                                    <br><span class="badge bg-info">Early</span>
                                @endif
                            @else
                                <span class="text-muted">Not logged out</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $attendance->formatted_total_hours }}</strong>
                        </td>
                        <td>
                            {!! $attendance->status_badge !!}
                        </td>
                        <td>
                            @if($attendance->time_in_location)
                                <small class="text-muted">
                                    <i class="bi bi-geo-alt"></i> In: {{ Str::limit($attendance->time_in_location, 20) }}
                                </small>
                                @if($attendance->time_out_location)
                                    <br><small class="text-muted">
                                        <i class="bi bi-geo-alt"></i> Out: {{ Str::limit($attendance->time_out_location, 20) }}
                                    </small>
                                @endif
                            @else
                                <span class="text-muted">No location</span>
                            @endif
                        </td>
                        <td>
                            @if($attendance->notes)
                                <small class="text-muted">{{ Str::limit($attendance->notes, 30) }}</small>
                            @else
                                <span class="text-muted">No notes</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-inbox display-4"></i>
                            <p class="mt-2">No attendance records found</p>
                            <a href="{{ route('attendance.dashboard') }}" class="btn btn-success">
                                <i class="bi bi-clock"></i> Start Recording Attendance
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($attendanceRecords->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $attendanceRecords->links() }}
        </div>
        @endif
    </div>
</div>



@endsection

@push('scripts')
<script>
function exportCurrentMonth() {
    const currentDate = new Date();
    const startDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
    const endDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
    
    exportAttendanceData(startDate.toISOString().split('T')[0], endDate.toISOString().split('T')[0]);
}

function exportCustomRange() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    if (!startDate || !endDate) {
        alert('Please select both start and end dates');
        return;
    }
    
    if (startDate > endDate) {
        alert('Start date cannot be after end date');
        return;
    }
    
    exportAttendanceData(startDate, endDate);
}

function exportAttendanceData(startDate, endDate) {
    // Create a simple CSV export
    const table = document.querySelector('table');
    const rows = Array.from(table.querySelectorAll('tr'));
    
    let csv = 'Date,Time In,Time Out,Total Hours,Status,Location,Notes\n';
    
    rows.slice(1).forEach(row => {
        const cells = Array.from(row.querySelectorAll('td'));
        if (cells.length > 0) {
            const rowData = cells.map(cell => {
                let text = cell.textContent.trim();
                // Remove extra whitespace and newlines
                text = text.replace(/\s+/g, ' ').replace(/\n/g, ' ');
                // Escape quotes
                text = text.replace(/"/g, '""');
                return `"${text}"`;
            });
            csv += rowData.join(',') + '\n';
        }
    });
    
    // Download CSV file
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `attendance_${startDate}_to_${endDate}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
}

// Set default dates for custom range
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
    
    document.getElementById('startDate').value = firstDay.toISOString().split('T')[0];
    document.getElementById('endDate').value = lastDay.toISOString().split('T')[0];
});
</script>
@endpush
