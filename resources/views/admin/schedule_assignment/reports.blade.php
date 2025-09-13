@extends('layouts.admin')

@section('title', 'Faculty Workload Analytics')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
                <i class="fas fa-chart-bar text-primary mr-2"></i>
                Faculty Workload Analytics
            </h1>
            <p class="text-muted mb-0">Comprehensive analysis of faculty schedules and workload distribution</p>
        </div>
        <div class="d-sm-flex">
            <a href="{{ route('admin.schedule-assignment.export', request()->query()) }}" class="btn btn-success btn-sm shadow-sm mr-2 rounded-pill">
                <i class="fas fa-download fa-sm mr-1"></i> Export Data
            </a>
            <a href="{{ route('admin.schedule-assignment.index') }}" class="btn btn-secondary btn-sm shadow-sm rounded-pill">
                <i class="fas fa-arrow-left fa-sm mr-1"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Analytics Filters -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-gradient-success text-white py-3">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-filter mr-2"></i>Analytics Filters
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
                            <option value="{{ $year }}" {{ request('academic_year', $currentYear) == $year ? 'selected' : '' }}>
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
                        @foreach($semesters as $key => $semester)
                            <option value="{{ $key }}" {{ request('semester', $currentSemester) == $key ? 'selected' : '' }}>
                                {{ $semester }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="workload_filter" class="form-label font-weight-bold text-dark">
                        <i class="fas fa-chart-pie text-warning mr-1"></i>Workload Filter
                    </label>
                    <select name="workload_filter" id="workload_filter" class="form-select form-select-sm border-warning">
                        <option value="">All Faculty Members</option>
                        <option value="overloaded" {{ request('workload_filter') == 'overloaded' ? 'selected' : '' }}>
                            <i class="fas fa-exclamation-triangle"></i> Overloaded (>40hrs)
                        </option>
                        <option value="full" {{ request('workload_filter') == 'full' ? 'selected' : '' }}>
                            <i class="fas fa-check-circle"></i> Full Load (30-40hrs)
                        </option>
                        <option value="partial" {{ request('workload_filter') == 'partial' ? 'selected' : '' }}>
                            <i class="fas fa-clock"></i> Partial Load (<30hrs)
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success btn-sm px-4 rounded-pill shadow-sm">
                        <i class="fas fa-chart-line mr-1"></i> Analyze
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Faculty Workload Analysis -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Faculty Workload Analysis - {{ $currentYear }} {{ $currentSemester }}
            </h6>
        </div>
        <div class="card-body">
            @if($workloadDistribution && is_array($workloadDistribution) && count($workloadDistribution) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Rank</th>
                                <th>Faculty Details</th>
                                <th>Total Load</th>
                                <th>Assignments</th>
                                <th>Workload Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($workloadDistribution as $index => $workload)
                                <tr class="{{ $workload['workload_status']['status'] == 'overloaded' ? 'table-danger' : '' }}">
                                    <td class="text-center font-weight-bold">
                                        {{ $index + 1 }}
                                        @if($index == 0)
                                            <i class="fas fa-crown text-warning" title="Highest Load"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="font-weight-bold">{{ $workload['faculty']->name }}</div>
                                        <small class="text-muted">{{ $workload['faculty']->professor_id }}</small><br>
                                        <small class="text-muted">{{ $workload['faculty']->employment_type }}</small>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold text-primary">{{ $workload['total_units'] }} units</div>
                                        <div class="font-weight-bold text-info">{{ $workload['total_hours'] }} hours/week</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $workload['assignments_count'] }} assignments</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $workload['workload_status']['class'] }}">
                                            {{ $workload['workload_status']['label'] }}
                                        </span>
                                        @if($workload['workload_status']['status'] == 'overloaded')
                                            <div class="small text-danger mt-1">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                {{ $workload['total_hours'] - 40 }} hours over limit
                                            </div>
                                        @elseif($workload['workload_status']['status'] == 'partial_load')
                                            <div class="small text-success mt-1">
                                                <i class="fas fa-plus-circle"></i>
                                                {{ 30 - $workload['total_hours'] }} hours available
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.schedule-assignment.index', ['professor_id' => $workload['faculty']->id, 'academic_year' => $currentYear, 'semester' => $currentSemester]) }}" 
                                               class="btn btn-sm btn-outline-primary" title="View Assignments">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.schedule-assignment.create', ['professor_id' => $workload['faculty']->id]) }}" 
                                               class="btn btn-sm btn-outline-success" title="Add Assignment">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                            <a href="{{ route('admin.schedule-assignment.calendar', ['professor_id' => $workload['faculty']->id, 'academic_year' => $currentYear, 'semester' => $currentSemester]) }}" 
                                               class="btn btn-sm btn-outline-info" title="View Calendar">
                                                <i class="fas fa-calendar"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Workload Distribution Summary -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h6 class="font-weight-bold text-gray-800 mb-3">Workload Distribution Summary</h6>
                        <div class="row">
                            @php
                                $overloaded = $workloadDistribution ? collect($workloadDistribution)->where('workload_status.status', 'overloaded')->count() : 0;
                                $fullLoad = $workloadDistribution ? collect($workloadDistribution)->where('workload_status.status', 'full_load')->count() : 0;
                                $partialLoad = $workloadDistribution ? collect($workloadDistribution)->where('workload_status.status', 'partial_load')->count() : 0;
                                $totalFaculty = $workloadDistribution ? count($workloadDistribution) : 0;
                            @endphp
                            
                            <div class="col-md-3">
                                <div class="card border-left-danger shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Overloaded</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            {{ $overloaded }} ({{ $totalFaculty > 0 ? round(($overloaded / $totalFaculty) * 100, 1) : 0 }}%)
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="card border-left-success shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Full Load</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            {{ $fullLoad }} ({{ $totalFaculty > 0 ? round(($fullLoad / $totalFaculty) * 100, 1) : 0 }}%)
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="card border-left-warning shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Partial Load</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            {{ $partialLoad }} ({{ $totalFaculty > 0 ? round(($partialLoad / $totalFaculty) * 100, 1) : 0 }}%)
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="card border-left-info shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Faculty</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalFaculty }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-chart-bar fa-4x text-gray-300 mb-4"></i>
                    <h4 class="text-gray-500">No Workload Data Available</h4>
                    <p class="text-muted mb-4">No faculty workload data found for the selected academic period.</p>
                    <a href="{{ route('admin.schedule-assignment.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Assignment
                    </a>
                </div>
            @endif
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
