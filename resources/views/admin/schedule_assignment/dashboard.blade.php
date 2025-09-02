@extends('layouts.admin')

@section('title', 'Schedule Assignment Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
                <i class="fas fa-calendar-alt text-primary mr-2"></i>
                Schedule Assignment Dashboard
            </h1>
            <p class="text-muted mb-0">Manage faculty schedules and workload assignments</p>
        </div>
        <div class="d-sm-flex">
            <a href="{{ route('admin.schedule-assignment.create') }}" class="btn btn-primary btn-sm shadow-sm mr-2 rounded-pill">
                <i class="fas fa-plus fa-sm mr-1"></i> New Assignment
            </a>
            <a href="{{ route('admin.schedule-assignment.calendar') }}" class="btn btn-info btn-sm shadow-sm mr-2 rounded-pill">
                <i class="fas fa-calendar fa-sm mr-1"></i> Calendar View
            </a>
            <a href="{{ route('admin.schedule-assignment.export') }}" class="btn btn-success btn-sm shadow-sm rounded-pill">
                <i class="fas fa-download fa-sm mr-1"></i> Export Data
            </a>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-gradient-primary text-white py-3">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-filter mr-2"></i>Filters & Quick Actions
            </h6>
        </div>
        <div class="card-body bg-light">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="academic_year" class="form-label font-weight-bold text-dark">
                        <i class="fas fa-graduation-cap text-primary mr-1"></i>Academic Year
                    </label>
                    <select name="academic_year" id="academic_year" class="form-select form-select-sm border-primary">
                        @foreach($academicYears as $year)
                            <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="semester" class="form-label font-weight-bold text-dark">
                        <i class="fas fa-calendar-week text-info mr-1"></i>Semester
                    </label>
                    <select name="semester" id="semester" class="form-select form-select-sm border-info">
                        @foreach(\App\Models\ScheduleAssignment::getSemesters() as $key => $sem)
                            <option value="{{ $key }}" {{ $key == $currentSemester ? 'selected' : '' }}>
                                {{ $sem }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-sm px-4 rounded-pill shadow-sm">
                        <i class="fas fa-search mr-1"></i> Apply Filters
                    </button>
                    <a href="{{ route('admin.schedule-assignment.dashboard') }}" class="btn btn-outline-secondary btn-sm px-3 rounded-pill ml-2">
                        <i class="fas fa-redo mr-1"></i> Reset
                    </a>
                </div>
                <div class="col-md-3 text-right">
                    <div class="btn-group" role="group">
                        <a href="{{ route('admin.schedule-assignment.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-list mr-1"></i> View All
                        </a>
                        <a href="{{ route('admin.schedule-assignment.reports') }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-chart-bar mr-1"></i> Reports
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Enhanced Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-lg h-100 position-relative overflow-hidden">
                <div class="card-body bg-gradient-primary text-white">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white-50 text-uppercase mb-1">
                                Total Assignments
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-white">{{ number_format($stats['total_assignments']) }}</div>
                            <div class="text-xs text-white-50 mt-1">
                                <i class="fas fa-arrow-up mr-1"></i>Active Period
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle bg-white bg-opacity-20">
                                <i class="fas fa-calendar-check fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 py-2">
                    <a href="{{ route('admin.schedule-assignment.index') }}" class="text-primary text-decoration-none small font-weight-bold">
                        <i class="fas fa-eye mr-1"></i>View Details
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-lg h-100 position-relative overflow-hidden">
                <div class="card-body bg-gradient-success text-white">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white-50 text-uppercase mb-1">
                                Active Faculty
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-white">{{ number_format($stats['active_faculty']) }}</div>
                            <div class="text-xs text-white-50 mt-1">
                                <i class="fas fa-user-check mr-1"></i>With Assignments
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle bg-white bg-opacity-20">
                                <i class="fas fa-users fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 py-2">
                    <a href="{{ route('admin.schedule-assignment.reports') }}" class="text-success text-decoration-none small font-weight-bold">
                        <i class="fas fa-chart-line mr-1"></i>View Analytics
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-lg h-100 position-relative overflow-hidden">
                <div class="card-body bg-gradient-info text-white">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white-50 text-uppercase mb-1">
                                Total Units / Hours
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-white">
                                {{ number_format($stats['total_units']) }} / {{ number_format($stats['total_hours']) }}
                            </div>
                            <div class="text-xs text-white-50 mt-1">
                                <i class="fas fa-chart-pie mr-1"></i>Workload Distribution
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle bg-white bg-opacity-20">
                                <i class="fas fa-clock fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 py-2">
                    <a href="{{ route('admin.schedule-assignment.calendar') }}" class="text-info text-decoration-none small font-weight-bold">
                        <i class="fas fa-calendar mr-1"></i>View Schedule
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-lg h-100 position-relative overflow-hidden">
                <div class="card-body bg-gradient-warning text-white">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white-50 text-uppercase mb-1">
                                Conflicts / Overloaded
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-white">
                                {{ number_format($stats['conflicts']) }} / {{ number_format($stats['overloaded_faculty']) }}
                            </div>
                            <div class="text-xs text-white-50 mt-1">
                                <i class="fas fa-exclamation-circle mr-1"></i>Needs Attention
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle bg-white bg-opacity-20">
                                <i class="fas fa-exclamation-triangle fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 py-2">
                    <a href="{{ route('admin.schedule-assignment.index', ['status' => 'conflict']) }}" class="text-warning text-decoration-none small font-weight-bold">
                        <i class="fas fa-search mr-1"></i>View Issues
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Assignments -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-gradient-primary text-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-clock mr-2"></i>Recent Schedule Assignments
                    </h6>
                    <a href="{{ route('admin.schedule-assignment.index') }}" class="btn btn-sm btn-light rounded-pill px-3">
                        <i class="fas fa-list mr-1"></i>View All
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($recentAssignments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0 font-weight-bold text-dark">Faculty</th>
                                        <th class="border-0 font-weight-bold text-dark">Subject</th>
                                        <th class="border-0 font-weight-bold text-dark">Section</th>
                                        <th class="border-0 font-weight-bold text-dark">Schedule</th>
                                        <th class="border-0 font-weight-bold text-dark">Source</th>
                                        <th class="border-0 font-weight-bold text-dark">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentAssignments as $assignment)
                                        <tr class="border-0">
                                            <td class="border-0 py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-weight-bold text-dark">{{ $assignment->faculty->name }}</div>
                                                        <small class="text-muted">{{ $assignment->faculty->professor_id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="border-0 py-3">
                                                <div class="font-weight-bold text-dark">{{ $assignment->subject_code }}</div>
                                                <div class="text-muted small">{{ Str::limit($assignment->subject_name, 30) }}</div>
                                            </td>
                                            <td class="border-0 py-3">
                                                <span class="badge badge-light border font-weight-bold">{{ $assignment->section }}</span>
                                            </td>
                                            <td class="border-0 py-3">
                                                <div class="font-weight-bold text-dark">{{ $assignment->schedule_display }}</div>
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt mr-1"></i>{{ $assignment->room ?? 'No room assigned' }}
                                                </small>
                                            </td>
                                            <td class="border-0 py-3">
                                                <span class="badge rounded-pill bg-{{ isset($assignment->source_table) && $assignment->source_table == 'Subject Load Tracker' ? 'success' : 'primary' }}">
                                                    <i class="fas fa-{{ isset($assignment->source_table) && $assignment->source_table == 'Subject Load Tracker' ? 'sync' : 'plus' }} mr-1"></i>
                                                    {{ $assignment->source_table ?? 'Direct Assignment' }}
                                                </span>
                                            </td>
                                            <td class="border-0 py-3">
                                                <span class="badge rounded-pill bg-{{ $assignment->status == 'active' ? 'success' : ($assignment->status == 'inactive' ? 'warning' : 'secondary') }}">
                                                    <i class="fas fa-{{ $assignment->status == 'active' ? 'check' : ($assignment->status == 'inactive' ? 'pause' : 'stop') }} mr-1"></i>
                                                    {{ ucfirst($assignment->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">No schedule assignments found for this period.</p>
                            <a href="{{ route('admin.schedule-assignment.create') }}" class="btn btn-primary">
                                Create First Assignment
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Faculty Workload Distribution -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Faculty Workload Distribution</h6>
                    <a href="{{ route('admin.schedule-assignment.reports') }}" class="btn btn-sm btn-outline-primary">
                        View Reports
                    </a>
                </div>
                <div class="card-body">
                    @if(count($workloadDistribution) > 0)
                        @foreach(array_slice($workloadDistribution, 0, 10) as $workload)
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <div class="font-weight-bold">{{ $workload['faculty']->name }}</div>
                                    <small class="text-muted">{{ $workload['faculty']->professor_id }}</small>
                                </div>
                                <div class="text-right">
                                    <div class="font-weight-bold">{{ $workload['total_hours'] }}h</div>
                                    <span class="badge bg-{{ $workload['workload_status']['class'] }}">
                                        {{ $workload['workload_status']['label'] }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                        
                        @if(count($workloadDistribution) > 10)
                            <div class="text-center mt-3">
                                <a href="{{ route('admin.schedule-assignment.reports') }}" class="btn btn-sm btn-outline-primary">
                                    View All {{ count($workloadDistribution) }} Faculty
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-user-clock fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">No faculty workload data available.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.schedule-assignment.create') }}" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-plus-circle"></i><br>
                                <small>New Assignment</small>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.schedule-assignment.calendar') }}" class="btn btn-outline-info btn-block">
                                <i class="fas fa-calendar-alt"></i><br>
                                <small>Calendar View</small>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.schedule-assignment.reports') }}" class="btn btn-outline-success btn-block">
                                <i class="fas fa-chart-bar"></i><br>
                                <small>Reports</small>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.schedule-assignment.export', request()->query()) }}" class="btn btn-outline-secondary btn-block">
                                <i class="fas fa-download"></i><br>
                                <small>Export Data</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-submit form when filters change
    $('#academic_year, #semester').change(function() {
        $(this).closest('form').submit();
    });
});
</script>
@endpush
@endsection
