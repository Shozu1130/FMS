@extends('layouts.professor_admin')

@section('title', 'My Schedule')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><i class="fas fa-calendar-week"></i> My Schedule</h3>
                    <div class="btn-group">
                        <div class="dropdown">
                            <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-download"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('professor.schedule.export-pdf', request()->query()) }}">
                                    <i class="fas fa-file-pdf"></i> Export as PDF
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('professor.schedule.export-csv', request()->query()) }}">
                                    <i class="fas fa-file-csv"></i> Export as CSV
                                </a></li>
                            </ul>
                        </div>
                        <a href="{{ route('professor.schedule.calendar') }}" class="btn btn-primary">
                            <i class="fas fa-calendar-alt"></i> Calendar View
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-book fa-2x mb-2"></i>
                                    <h4>{{ $summary['total_subjects'] }}</h4>
                                    <small>Total Subjects</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-award fa-2x mb-2"></i>
                                    <h4>{{ $summary['total_units'] }}</h4>
                                    <small>Total Units</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-clock fa-2x mb-2"></i>
                                    <h4>{{ $summary['total_hours'] }}</h4>
                                    <small>Total Hours/Week</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-{{ $summary['workload_status']['class'] }} text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-tachometer-alt fa-2x mb-2"></i>
                                    <h6>{{ $summary['workload_status']['label'] }}</h6>
                                    <small>Workload Status</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="academic_year" class="form-label">Academic Year</label>
                                <select name="academic_year" id="academic_year" class="form-select">
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year }}" {{ request('academic_year', $summary['academic_year']) == $year ? 'selected' : '' }}>
                                            {{ $year }}-{{ $year + 1 }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="semester" class="form-label">Semester</label>
                                <select name="semester" id="semester" class="form-select">
                                    @foreach($semesters as $key => $value)
                                        <option value="{{ $key }}" {{ request('semester', $summary['semester']) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="year_level" class="form-label">Year Level</label>
                                <select name="year_level" id="year_level" class="form-select">
                                    <option value="">All Year Levels</option>
                                    @foreach($yearLevels as $key => $value)
                                        @if($availableYearLevels->contains($key))
                                            <option value="{{ $key }}" {{ request('year_level') == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-secondary">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                    <a href="{{ route('professor.schedule.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-redo"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Schedule Conflicts Alert -->
                    @if(count($summary['conflicts']) > 0)
                        <div class="alert alert-warning" role="alert">
                            <h6 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Schedule Conflicts Detected</h6>
                            <p>You have {{ count($summary['conflicts']) }} schedule conflict(s) that need attention:</p>
                            <ul class="mb-0">
                                @foreach($summary['conflicts'] as $conflict)
                                    <li>
                                        <strong>{{ $conflict['day'] }}:</strong> 
                                        {{ $conflict['schedule1']->subject_code }} ({{ $conflict['schedule1']->time_range }}) 
                                        overlaps with 
                                        {{ $conflict['schedule2']->subject_code }} ({{ $conflict['schedule2']->time_range }})
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Schedule Summary -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h5><i class="fas fa-info-circle"></i> Schedule Summary for {{ $summary['semester'] }} {{ $summary['academic_year'] }}-{{ $summary['academic_year'] + 1 }}</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Subject Load Tracker:</strong> {{ $summary['subject_load_count'] }} assignments</p>
                                    <p><strong>Schedule Assignment:</strong> {{ $summary['schedule_assignment_count'] }} assignments</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Total Teaching Load:</strong> {{ $summary['total_units'] }} units ({{ $summary['total_hours'] }} hours/week)</p>
                                    <p><strong>Status:</strong> 
                                        <span class="badge bg-{{ $summary['workload_status']['class'] }}">
                                            {{ $summary['workload_status']['label'] }}
                                        </span>
                                    </p>
                                    @if(count($summary['conflicts']) > 0)
                                        <p><strong>Conflicts:</strong> 
                                            <span class="badge bg-warning text-dark">
                                                {{ count($summary['conflicts']) }} conflict(s)
                                            </span>
                                        </p>
                                    @else
                                        <p><strong>Conflicts:</strong> 
                                            <span class="badge bg-success">
                                                No conflicts
                                            </span>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Weekly Schedule -->
                    <h5><i class="fas fa-calendar-week"></i> Weekly Schedule</h5>
                    @if($allSchedules->count() > 0)
                        <div class="row">
                            @foreach($days as $dayKey => $dayName)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0 text-center">{{ $dayName }}</h6>
                                        </div>
                                        <div class="card-body p-2">
                                            @if($schedule[$dayKey]->count() > 0)
                                                @foreach($schedule[$dayKey] as $item)
                                                    <div class="card mb-2 border-{{ $item->source_color }}">
                                                        <div class="card-body p-2">
                                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                                <h6 class="card-title mb-0 small">{{ $item->subject_code }}</h6>
                                                                <span class="badge bg-{{ $item->source_color }} small">
                                                                    {{ $item->source_name === 'Subject Load Tracker' ? 'SLT' : 'SA' }}
                                                                </span>
                                                            </div>
                                                            <p class="card-text small mb-1">{{ $item->subject_name }}</p>
                                                            <p class="card-text small mb-1">
                                                                <strong>Section:</strong> {{ $item->section }} | 
                                                                <strong>Year:</strong> {{ $item->year_level }} |
                                                                <strong>Units:</strong> {{ $item->units }}
                                                            </p>
                                                            <p class="card-text small mb-1">
                                                                <i class="fas fa-clock"></i> {{ $item->time_range }}
                                                            </p>
                                                            @if($item->room)
                                                                <p class="card-text small mb-1">
                                                                    <i class="fas fa-map-marker-alt"></i> {{ $item->room }}
                                                                </p>
                                                            @endif
                                                            <div class="text-end">
                                                                @if($item->source_name === 'Subject Load Tracker')
                                                                    <a href="{{ route('professor.schedule.show', ['type' => 'subject-load', 'id' => $item->id]) }}" 
                                                                           class="btn btn-outline-success btn-sm">
                                                                            <i class="fas fa-eye"></i> View
                                                                        </a>
                                                                @else
                                                                    <a href="{{ route('professor.schedule.show', ['type' => 'schedule-assignment', 'id' => $item->id]) }}" 
                                                                           class="btn btn-outline-primary btn-sm">
                                                                            <i class="fas fa-eye"></i> View
                                                                        </a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <p class="text-muted text-center small">No classes scheduled</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-5x text-muted mb-3"></i>
                            <h4 class="text-muted">No Schedule Found</h4>
                            <p class="text-muted">No schedule assignments found for the selected academic period.</p>
                        </div>
                    @endif

                    <!-- Legend -->
                    <div class="mt-4">
                        <h6>Legend</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <span class="badge bg-success me-2">SLT</span> Subject Load Tracker
                            </div>
                            <div class="col-md-6">
                                <span class="badge bg-primary me-2">SA</span> Schedule Assignment
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

