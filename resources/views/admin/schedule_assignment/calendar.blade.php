@extends('layouts.admin')

@section('title', 'Schedule Calendar')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
                <i class="fas fa-calendar-week text-primary mr-2"></i>
                Schedule Calendar
            </h1>
            <p class="text-muted mb-0">Visual overview of faculty schedules and assignments</p>
        </div>
        <div class="d-sm-flex">
            <a href="{{ route('admin.schedule-assignment.create') }}" class="btn btn-primary btn-sm shadow-sm mr-2 rounded-pill">
                <i class="fas fa-plus fa-sm mr-1"></i> New Assignment
            </a>
            <a href="{{ route('admin.schedule-assignment.index') }}" class="btn btn-secondary btn-sm shadow-sm rounded-pill">
                <i class="fas fa-list fa-sm mr-1"></i> List View
            </a>
        </div>
    </div>

    <!-- Professional Calendar Filters -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-gradient-info text-white py-3">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-sliders-h mr-2"></i>Calendar Filters & View Options
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
                <div class="col-md-3">
                    <label for="professor_id" class="form-label font-weight-bold text-dark">
                        <i class="fas fa-user text-success mr-1"></i>Faculty Filter
                    </label>
                    <select name="professor_id" id="professor_id" class="form-select form-select-sm border-success">
                        <option value="">All Faculty Members</option>
                        @foreach($faculties as $faculty)
                            <option value="{{ $faculty->id }}" {{ request('professor_id') == $faculty->id ? 'selected' : '' }}>
                                {{ $faculty->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-info btn-sm px-4 rounded-pill shadow-sm">
                        <i class="fas fa-sync mr-1"></i> Update Calendar
                    </button>
                    <a href="{{ route('admin.schedule-assignment.calendar') }}" class="btn btn-outline-secondary btn-sm px-3 rounded-pill ml-2">
                        <i class="fas fa-redo mr-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Professional Legend -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body bg-light py-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div class="d-flex align-items-center gap-4">
                    <span class="font-weight-bold text-dark">
                        <i class="fas fa-info-circle text-primary mr-1"></i>Legend:
                    </span>
                    <div class="d-flex align-items-center">
                        <div class="rounded-pill bg-primary" style="width: 16px; height: 16px; margin-right: 8px;"></div>
                        <span class="small font-weight-medium">Direct Assignment</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="rounded-pill bg-success" style="width: 16px; height: 16px; margin-right: 8px;"></div>
                        <span class="small font-weight-medium">Subject Load Tracker</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="rounded-pill bg-danger" style="width: 16px; height: 16px; margin-right: 8px;"></div>
                        <span class="small font-weight-medium">Schedule Conflict</span>
                    </div>
                </div>
                <div class="text-muted small">
                    <i class="fas fa-mouse-pointer mr-1"></i>Click on schedule blocks for details
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar View -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Weekly Schedule - {{ $currentYear }} {{ $currentSemester }}
                @if($professorId)
                    ({{ $faculties->find($professorId)->name }})
                @endif
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered calendar-table">
                    <thead class="table-dark">
                        <tr>
                            <th width="12%" class="text-center">Time</th>
                            @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                <th width="12.5%" class="text-center">{{ ucfirst($day) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @for($hour = 7; $hour <= 20; $hour++)
                            <tr>
                                <td class="text-center font-weight-bold time-slot">
                                    {{ sprintf('%02d:00', $hour) }}
                                </td>
                                @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                    <td class="calendar-cell" data-day="{{ $day }}" data-hour="{{ $hour }}">
                                        @if(isset($calendarData[$day]))
                                            @foreach($calendarData[$day] as $assignment)
                                                @php
                                                    $timeParts = explode(' - ', $assignment['time_range']);
                                                    if (count($timeParts) >= 2) {
                                                        $startHour = (int) date('H', strtotime($timeParts[0]));
                                                        $endHour = (int) date('H', strtotime($timeParts[1]));
                                                        $startMinute = (int) date('i', strtotime($timeParts[0]));
                                                        $endMinute = (int) date('i', strtotime($timeParts[1]));
                                                    } else {
                                                        // Skip this assignment if time format is invalid
                                                        continue;
                                                    }
                                                @endphp
                                                
                                                @if($hour >= $startHour && $hour < $endHour || ($hour == $endHour && $endMinute > 0))
                                                    @if($hour == $startHour)
                                                        <div class="schedule-block bg-{{ $assignment['color'] }} text-white p-1 mb-1 rounded" 
                                                             style="font-size: 0.75rem; cursor: pointer;"
                                                             data-bs-toggle="tooltip" 
                                                             title="{{ $assignment['subject_name'] }} - {{ $assignment['faculty_name'] }} ({{ $assignment['source'] }})">
                                                            <div class="font-weight-bold">{{ $assignment['subject_code'] }}</div>
                                                            <div>{{ $assignment['section'] }}</div>
                                                            <div>{{ $assignment['time_range'] }}</div>
                                                            @if($assignment['room'])
                                                                <div><small>{{ $assignment['room'] }}</small></div>
                                                            @endif
                                                            @if(!$professorId)
                                                                <div><small>{{ $assignment['faculty_name'] }}</small></div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                @endif
                                            @endforeach
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if(count($calendarData) == 0)
        <div class="text-center py-5">
            <i class="fas fa-calendar-times fa-4x text-gray-300 mb-4"></i>
            <h4 class="text-gray-500">No Schedule Data Found</h4>
            <p class="text-muted mb-4">No schedule assignments found for the selected period and faculty.</p>
            <a href="{{ route('admin.schedule-assignment.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Assignment
            </a>
        </div>
    @endif
</div>

@push('styles')
<style>
.calendar-table {
    font-size: 0.85rem;
}

.calendar-cell {
    height: 60px;
    vertical-align: top;
    position: relative;
}

.time-slot {
    background-color: #f8f9fc;
    vertical-align: middle;
}

.schedule-block {
    border: 1px solid rgba(255,255,255,0.3);
    transition: all 0.2s ease;
}

.schedule-block:hover {
    transform: scale(1.02);
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.bg-primary {
    background-color: #4e73df !important;
}

.bg-success {
    background-color: #1cc88a !important;
}

.bg-danger {
    background-color: #e74a3b !important;
}

@media (max-width: 768px) {
    .calendar-table {
        font-size: 0.7rem;
    }
    
    .schedule-block {
        padding: 2px !important;
        font-size: 0.6rem !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Auto-submit form when filters change
    $('#academic_year, #semester, #professor_id').change(function() {
        $(this).closest('form').submit();
    });
    
    // Click handler for schedule blocks
    $('.schedule-block').click(function() {
        const scheduleData = $(this).data();
        // You can add modal or redirect logic here
        console.log('Schedule clicked:', scheduleData);
    });
});
</script>
@endpush
@endsection
