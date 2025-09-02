@extends('layouts.professor')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">My Subject Loads</h1>
                <a href="{{ route('professor.subject-loads.schedule') }}" class="btn btn-primary">
                    <i class="fas fa-calendar-alt"></i> View Schedule
                </a>
            </div>

            <!-- Current Period Summary -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Current Period Summary ({{ $summary['academic_year'] }} - {{ $summary['semester'] }})</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h4 class="text-primary">{{ $summary['total_subjects'] }}</h4>
                                        <small class="text-muted">Subjects</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h4 class="text-success">{{ $summary['total_units'] }}</h4>
                                        <small class="text-muted">Total Units</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h4 class="text-info">{{ $summary['total_hours'] }}</h4>
                                        <small class="text-muted">Hours/Week</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        @if($summary['total_hours'] > 40)
                                            <span class="badge bg-warning">Overloaded</span>
                                        @elseif($summary['total_hours'] >= 30)
                                            <span class="badge bg-success">Full Load</span>
                                        @else
                                            <span class="badge bg-info">Partial Load</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="academic_year" class="form-label">Academic Year</label>
                            <select name="academic_year" id="academic_year" class="form-select">
                                <option value="">All Years</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="semester" class="form-label">Semester</label>
                            <select name="semester" id="semester" class="form-select">
                                <option value="">All Semesters</option>
                                @foreach($semesters as $sem)
                                    <option value="{{ $sem }}" {{ request('semester') == $sem ? 'selected' : '' }}>
                                        {{ $sem }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="{{ route('professor.subject-loads.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Subject Loads Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Subject Load History</h5>
                </div>
                <div class="card-body">
                    @if($subjectLoads->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Academic Period</th>
                                        <th>Subject Code</th>
                                        <th>Subject Name</th>
                                        <th>Section</th>
                                        <th>Units</th>
                                        <th>Hours/Week</th>
                                        <th>Schedule</th>
                                        <th>Room</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subjectLoads as $load)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $load->academic_year }}</strong><br>
                                                    <small class="text-muted">{{ $load->semester }}</small>
                                                </div>
                                            </td>
                                            <td><strong>{{ $load->subject_code }}</strong></td>
                                            <td>{{ $load->subject_name }}</td>
                                            <td>{{ $load->section }}</td>
                                            <td>{{ $load->units }}</td>
                                            <td>{{ $load->hours_per_week }}</td>
                                            <td>{{ $load->schedule_display }}</td>
                                            <td>{{ $load->room ?: 'TBA' }}</td>
                                            <td>
                                                @if($load->status == 'active')
                                                    <span class="badge bg-success">Active</span>
                                                @elseif($load->status == 'inactive')
                                                    <span class="badge bg-warning">Inactive</span>
                                                @else
                                                    <span class="badge bg-secondary">Completed</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('professor.subject-loads.show', $load) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $subjectLoads->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-chalkboard-teacher fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Subject Loads Found</h5>
                            <p class="text-muted">
                                @if(request()->hasAny(['academic_year', 'semester', 'status']))
                                    No subject loads match your current filters.
                                @else
                                    You haven't been assigned any subjects yet.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
