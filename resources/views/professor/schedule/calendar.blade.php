@extends('layouts.professor_admin')

@section('title', 'My Schedule - Calendar View')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><i class="fas fa-calendar-alt"></i> My Schedule - Calendar View</h3>
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
                        <a href="{{ route('professor.schedule.index') }}" class="btn btn-primary">
                            <i class="fas fa-list"></i> List View
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    <!-- Load Summary -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-tasks fa-2x mb-2"></i>
                                    <h4>{{ $loadSummary['total_assignments'] }}</h4>
                                    <small>Total Assignments</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-award fa-2x mb-2"></i>
                                    <h4>{{ $loadSummary['total_units'] }}</h4>
                                    <small>Total Units</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-clock fa-2x mb-2"></i>
                                    <h4>{{ $loadSummary['total_hours'] }}</h4>
                                    <small>Total Hours/Week</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-{{ $loadSummary['workload_status']['class'] }} text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-tachometer-alt fa-2x mb-2"></i>
                                    <h6>{{ $loadSummary['workload_status']['label'] }}</h6>
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
                                        <option value="{{ $year }}" {{ $academicYear == $year ? 'selected' : '' }}>
                                            {{ $year }}-{{ $year + 1 }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="semester" class="form-label">Semester</label>
                                <select name="semester" id="semester" class="form-select">
                                    @foreach($semesters as $key => $value)
                                        <option value="{{ $key }}" {{ $semester == $key ? 'selected' : '' }}>
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
                                    <a href="{{ route('professor.schedule.calendar') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-redo"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Calendar -->
                    <h5><i class="fas fa-calendar-week"></i> Weekly Calendar - {{ $semester }} {{ $academicYear }}-{{ $academicYear + 1 }}</h5>
                    @if(collect($calendarData)->flatten(1)->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered calendar-table">
                                <thead class="table-light">
                                    <tr>
                                        <th width="14%">Time</th>
                                        @foreach($days as $dayKey => $dayName)
                                            <th width="12%" class="text-center">{{ $dayName }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // Generate time slots from 7 AM to 9 PM
                                        $timeSlots = [];
                                        for ($hour = 7; $hour <= 21; $hour++) {
                                            $timeSlots[] = sprintf('%02d:00', $hour);
                                        }
                                    @endphp
                                    
                                    @foreach($timeSlots as $timeSlot)
                                        <tr>
                                            <td class="time-slot">
                                                <strong>{{ \Carbon\Carbon::createFromFormat('H:i', $timeSlot)->format('g:i A') }}</strong>
                                            </td>
                                            @foreach($days as $dayKey => $dayName)
                                                <td class="schedule-cell">
                                                    @if(isset($calendarData[$dayKey]))
                                                        @foreach($calendarData[$dayKey] as $assignment)
                                                            @php
                                                                $startTime = \Carbon\Carbon::createFromFormat('g:i A', explode(' - ', $assignment['time_range'])[0]);
                                                                $endTime = \Carbon\Carbon::createFromFormat('g:i A', explode(' - ', $assignment['time_range'])[1]);
                                                                $slotTime = \Carbon\Carbon::createFromFormat('H:i', $timeSlot);
                                                                
                                                                // Check if this assignment falls within this time slot
                                                                if ($startTime->hour == $slotTime->hour) {
                                                            @endphp
                                                                <div class="schedule-item border-{{ $assignment['color'] }} mb-1">
                                                                    <div class="schedule-content p-2">
                                                                        <div class="d-flex justify-content-between align-items-start">
                                                                            <strong class="subject-code">{{ $assignment['subject_code'] }}</strong>
                                                                            <span class="badge bg-{{ $assignment['color'] }} small">
                                                                                {{ $assignment['source'] === 'Subject Load Tracker' ? 'SLT' : 'SA' }}
                                                                            </span>
                                                                        </div>
                                                                        <div class="subject-name small">{{ $assignment['subject_name'] }}</div>
                                                                        <div class="section small">Section: {{ $assignment['section'] }}</div>
                                                                        <div class="time small">
                                                                            <i class="fas fa-clock"></i> {{ $assignment['time_range'] }}
                                                                        </div>
                                                                        @if($assignment['room'])
                                                                            <div class="room small">
                                                                                <i class="fas fa-map-marker-alt"></i> {{ $assignment['room'] }}
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @php
                                                                }
                                                            @endphp
                                                        @endforeach
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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

@push('styles')
<style>
    .calendar-table {
        font-size: 0.9rem;
    }
    
    .time-slot {
        background-color: #f8f9fa;
        vertical-align: middle;
        text-align: center;
    }
    
    .schedule-cell {
        vertical-align: top;
        height: 80px;
        position: relative;
    }
    
    .schedule-item {
        border-left: 4px solid;
        background-color: rgba(0,0,0,0.02);
        border-radius: 4px;
    }
    
    .schedule-content {
        font-size: 0.8rem;
    }
    
    .subject-code {
        font-size: 0.85rem;
        color: #333;
    }
    
    .subject-name {
        color: #666;
        margin-bottom: 2px;
    }
    
    .section, .time, .room {
        color: #888;
        margin-bottom: 1px;
    }
</style>
@endpush
