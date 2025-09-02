@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Subject Load Summary Report</h4>
                    <div>
                        <button onclick="window.print()" class="btn btn-secondary me-2">
                            <i class="fas fa-print"></i> Print
                        </button>
                        <a href="{{ route('admin.subject-loads.export', request()->query()) }}" class="btn btn-success">
                            <i class="fas fa-download"></i> Export CSV
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Report Header -->
                    <div class="row mb-4">
                        <div class="col-md-12 text-center">
                            <h3>Faculty Subject Load Report</h3>
                            <h5 class="text-muted">Academic Year {{ $summary['academic_year'] }} - {{ $summary['semester'] }}</h5>
                            <p class="text-muted">Generated on {{ date('F d, Y \a\t g:i A') }}</p>
                        </div>
                    </div>

                    <!-- Summary Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="text-muted">Summary Statistics</h6>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h4>{{ $summary['total_faculties'] }}</h4>
                                            <small>Total Faculty</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h4>{{ $summary['total_loads'] }}</h4>
                                            <small>Subject Loads</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h4>{{ $summary['total_units'] }}</h4>
                                            <small>Total Units</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h4>{{ $summary['total_hours'] }}</h4>
                                            <small>Total Hours/Week</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h4>{{ number_format($summary['average_units_per_faculty'], 1) }}</h4>
                                            <small>Avg Units/Faculty</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Faculty Load Details -->
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="text-muted">Faculty Load Details</h6>
                            @if($facultyLoads->count() > 0)
                                @foreach($facultyLoads as $faculty)
                                    <div class="card mb-3">
                                        <div class="card-header">
                                            <div class="row align-items-center">
                                                <div class="col-md-6">
                                                    <h6 class="mb-0">{{ $faculty->name }}</h6>
                                                    <small class="text-muted">{{ $faculty->professor_id }} | {{ $faculty->employment_type }}</small>
                                                </div>
                                                <div class="col-md-6 text-end">
                                                    @php
                                                        $totalUnits = $faculty->subjectLoads->sum('units');
                                                        $totalHours = $faculty->subjectLoads->sum('hours_per_week');
                                                        $subjectCount = $faculty->subjectLoads->count();
                                                    @endphp
                                                    <span class="badge bg-primary me-1">{{ $totalUnits }} units</span>
                                                    <span class="badge bg-info me-1">{{ $totalHours }} hrs/week</span>
                                                    <span class="badge bg-success">{{ $subjectCount }} subjects</span>
                                                </div>
                                            </div>
                                        </div>
                                        @if($faculty->subjectLoads->count() > 0)
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>Subject Code</th>
                                                                <th>Subject Name</th>
                                                                <th>Section</th>
                                                                <th>Units</th>
                                                                <th>Hours/Week</th>
                                                                <th>Schedule</th>
                                                                <th>Room</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($faculty->subjectLoads->sortBy(['schedule_day', 'start_time']) as $load)
                                                                <tr>
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
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @else
                                            <div class="card-body text-center text-muted">
                                                <i class="fas fa-calendar-times"></i> No subject loads assigned
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Faculty Found</h5>
                                    <p class="text-muted">No active faculty members found.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row mt-4">
                <div class="col-md-12 text-center">
                    <a href="{{ route('admin.subject-loads.dashboard') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                    <a href="{{ route('admin.subject-loads.index') }}" class="btn btn-primary me-2">
                        <i class="fas fa-list"></i> Manage Loads
                    </a>
                    <a href="{{ route('admin.subject-loads.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Assign New Subject
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .card-header .badge, .no-print {
        display: none !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
    }
    
    .card-body {
        padding: 1rem !important;
    }
    
    .table {
        font-size: 12px;
    }
    
    .badge {
        border: 1px solid #000;
        color: #000 !important;
        background-color: transparent !important;
    }
}
</style>
@endsection
