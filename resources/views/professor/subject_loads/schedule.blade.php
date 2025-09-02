@extends('layouts.professor')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">My Teaching Schedule</h1>
                <a href="{{ route('professor.subject-loads.index') }}" class="btn btn-secondary">
                    <i class="fas fa-list"></i> View All Loads
                </a>
            </div>

            <!-- Period Summary -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card bg-light">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h6 class="card-title mb-1">{{ $summary['academic_year'] }} - {{ $summary['semester'] }}</h6>
                                    <div class="row">
                                        <div class="col-4">
                                            <span class="badge bg-primary">{{ $summary['total_subjects'] }} Subjects</span>
                                        </div>
                                        <div class="col-4">
                                            <span class="badge bg-success">{{ $summary['total_units'] }} Units</span>
                                        </div>
                                        <div class="col-4">
                                            <span class="badge bg-info">{{ $summary['total_hours'] }} Hours/Week</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <form method="GET" class="row g-2">
                                        <div class="col-md-5">
                                            <select name="academic_year" class="form-select form-select-sm">
                                                @foreach($academicYears as $year)
                                                    <option value="{{ $year }}" {{ $summary['academic_year'] == $year ? 'selected' : '' }}>
                                                        {{ $year }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-5">
                                            <select name="semester" class="form-select form-select-sm">
                                                @foreach($semesters as $sem)
                                                    <option value="{{ $sem }}" {{ $summary['semester'] == $sem ? 'selected' : '' }}>
                                                        {{ $sem }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-primary btn-sm w-100">View</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Weekly Schedule -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Weekly Schedule</h5>
                </div>
                <div class="card-body">
                    @if($summary['total_subjects'] > 0)
                        <div class="row">
                            @foreach($days as $dayKey => $dayName)
                                <div class="col-md-12 mb-4">
                                    <h6 class="text-primary border-bottom pb-2">
                                        <i class="fas fa-calendar-day"></i> {{ $dayName }}
                                    </h6>
                                    
                                    @if($schedule[$dayKey]->count() > 0)
                                        <div class="row">
                                            @foreach($schedule[$dayKey]->sortBy('start_time') as $load)
                                                <div class="col-md-6 col-lg-4 mb-3">
                                                    <div class="card border-start border-primary border-3">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                                <h6 class="card-title mb-0">{{ $load->subject_code }}</h6>
                                                                @if($load->status == 'active')
                                                                    <span class="badge bg-success">Active</span>
                                                                @elseif($load->status == 'inactive')
                                                                    <span class="badge bg-warning">Inactive</span>
                                                                @else
                                                                    <span class="badge bg-secondary">Completed</span>
                                                                @endif
                                                            </div>
                                                            <p class="card-text mb-2">
                                                                <strong>{{ $load->subject_name }}</strong><br>
                                                                <small class="text-muted">Section {{ $load->section }}</small>
                                                            </p>
                                                            <div class="mb-2">
                                                                <i class="fas fa-clock text-muted"></i>
                                                                <small>{{ date('g:i A', strtotime($load->start_time)) }} - {{ date('g:i A', strtotime($load->end_time)) }}</small>
                                                            </div>
                                                            <div class="mb-2">
                                                                <i class="fas fa-map-marker-alt text-muted"></i>
                                                                <small>{{ $load->room ?: 'TBA' }}</small>
                                                            </div>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <small class="text-muted">{{ $load->units }} units | {{ $load->hours_per_week }} hrs/week</small>
                                                                <a href="{{ route('professor.subject-loads.show', $load) }}" 
                                                                   class="btn btn-sm btn-outline-primary">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-3 text-muted">
                                            <i class="fas fa-calendar-times"></i> No classes scheduled
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Schedule Available</h5>
                            <p class="text-muted">No active subject loads found for the selected period.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Load Analysis -->
            @if($summary['total_subjects'] > 0)
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Load Analysis</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <h5>{{ $summary['total_hours'] }}</h5>
                                            <small class="text-muted">Total Hours per Week</small>
                                            @if($summary['total_hours'] > 40)
                                                <div class="mt-2">
                                                    <span class="badge bg-warning">Overloaded</span>
                                                </div>
                                            @elseif($summary['total_hours'] >= 30)
                                                <div class="mt-2">
                                                    <span class="badge bg-success">Full Load</span>
                                                </div>
                                            @else
                                                <div class="mt-2">
                                                    <span class="badge bg-info">Partial Load</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <h5>{{ number_format($summary['total_hours'] / 5, 1) }}</h5>
                                            <small class="text-muted">Average Hours per Day</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <h5>{{ number_format($summary['total_units'] / $summary['total_subjects'], 1) }}</h5>
                                            <small class="text-muted">Average Units per Subject</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
