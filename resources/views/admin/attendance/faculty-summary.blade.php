@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="bi bi-bar-chart"></i> Faculty Attendance Summary
                </h1>
                <div>
                    <a href="{{ route('admin.attendance.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Attendance
                    </a>
                    <a href="{{ route('admin.attendance.export') }}" class="btn btn-success">
                        <i class="bi bi-download"></i> Export CSV
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Faculty
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ count($summary) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Average Present Days
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($summary->avg('present_days') ?? 0, 1) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Average Late Days
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($summary->avg('late_days') ?? 0, 1) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock-history fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Hours This Month
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($summary->sum('total_hours') ?? 0, 1) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-hourglass-split fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Faculty Summary Table -->
    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Current Month Attendance Summary</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Faculty</th>
                            <th>Total Days</th>
                            <th>Present Days</th>
                            <th>Late Days</th>
                            <th>Absent Days</th>
                            <th>Total Hours</th>
                            <th>Average Hours/Day</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($summary as $facultySummary)
                        <tr>
                            <td>
                                <strong>{{ $facultySummary['faculty']->name }}</strong><br>
                                <small class="text-muted">{{ $facultySummary['faculty']->professor_id }}</small>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $facultySummary['total_days'] }}</span>
                            </td>
                            <td>
                                <span class="badge bg-success">{{ $facultySummary['present_days'] }}</span>
                            </td>
                            <td>
                                @if($facultySummary['late_days'] > 0)
                                    <span class="badge bg-warning">{{ $facultySummary['late_days'] }}</span>
                                @else
                                    <span class="badge bg-secondary">0</span>
                                @endif
                            </td>
                            <td>
                                @if($facultySummary['absent_days'] > 0)
                                    <span class="badge bg-danger">{{ $facultySummary['absent_days'] }}</span>
                                @else
                                    <span class="badge bg-secondary">0</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ number_format($facultySummary['total_hours'], 1) }} hrs</strong>
                            </td>
                            <td>
                                @if($facultySummary['total_days'] > 0)
                                    <span class="text-info">{{ number_format($facultySummary['average_hours'], 1) }} hrs</span>
                                @else
                                    <span class="text-muted">0.0 hrs</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.attendance.index', ['faculty_id' => $facultySummary['faculty']->id]) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View Records
                                    </a>
                                    <a href="{{ route('admin.salary-grades.index') }}" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-calculator"></i> Salary
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                <i class="bi bi-inbox"></i> No faculty attendance data found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Performance Insights -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Top Performers</h6>
                </div>
                <div class="card-body">
                    @php
                        $topPerformers = collect($summary)->sortByDesc('present_days')->take(5);
                    @endphp
                    
                    @forelse($topPerformers as $performer)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong>{{ $performer['faculty']->name }}</strong>
                            <small class="text-muted d-block">{{ $performer['faculty']->professor_id }}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success">{{ $performer['present_days'] }} days</span>
                            <small class="text-muted d-block">{{ number_format($performer['total_hours'], 1) }} hrs</small>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted">No performance data available</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Attendance Issues</h6>
                </div>
                <div class="card-body">
                    @php
                        $issues = collect($summary)->filter(function($item) {
                            return $item['late_days'] > 0 || $item['absent_days'] > 0;
                        })->sortByDesc('late_days');
                    @endphp
                    
                    @forelse($issues->take(5) as $issue)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong>{{ $issue['faculty']->name }}</strong>
                            <small class="text-muted d-block">{{ $issue['faculty']->professor_id }}</small>
                        </div>
                        <div class="text-end">
                            @if($issue['late_days'] > 0)
                                <span class="badge bg-warning me-1">{{ $issue['late_days'] }} late</span>
                            @endif
                            @if($issue['absent_days'] > 0)
                                <span class="badge bg-danger">{{ $issue['absent_days'] }} absent</span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="text-success">No attendance issues found</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Add any additional JavaScript functionality here
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh every 5 minutes
    setInterval(function() {
        location.reload();
    }, 5 * 60 * 1000);
});
</script>
@endpush
