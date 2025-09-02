@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h4 class="mb-4">Subject Load Dashboard</h4>
            
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h3>{{ $stats['total_loads'] }}</h3>
                            <p class="mb-0">Total Loads</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h3>{{ $stats['total_faculties'] }}</h3>
                            <p class="mb-0">Active Faculty</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h3>{{ $stats['current_period_loads'] }}</h3>
                            <p class="mb-0">Current Period</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h3>{{ $stats['total_units'] }}</h3>
                            <p class="mb-0">Total Units</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-secondary text-white">
                        <div class="card-body text-center">
                            <h3>{{ $stats['total_hours'] }}</h3>
                            <p class="mb-0">Total Hours/Week</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Recent Assignments -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Assignments</h5>
                            <span class="badge bg-primary">{{ $recentLoads->count() }}</span>
                        </div>
                        <div class="card-body">
                            @if($recentLoads->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($recentLoads as $load)
                                        <div class="list-group-item d-flex justify-content-between align-items-start">
                                            <div class="ms-2 me-auto">
                                                <div class="fw-bold">{{ $load->subject_code }} - {{ $load->section }}</div>
                                                <small class="text-muted">{{ $load->faculty->name }}</small><br>
                                                <small class="text-muted">{{ $load->schedule_display }}</small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-primary">{{ $load->units }} units</span><br>
                                                <small class="text-muted">{{ $load->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="text-center mt-3">
                                    <a href="{{ route('admin.subject-loads.index') }}" class="btn btn-primary">
                                        <i class="fas fa-list"></i> View All Loads
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-3">
                                    <i class="fas fa-calendar-plus fa-2x text-muted mb-2"></i>
                                    <p class="text-muted">No assignments yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Faculty Load Rankings -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Faculty Load Rankings</h5>
                            <span class="badge bg-info">Current Period</span>
                        </div>
                        <div class="card-body">
                            @if($facultyLoads->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($facultyLoads as $facultyLoad)
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="fw-bold">{{ $facultyLoad->faculty->name }}</div>
                                                <small class="text-muted">{{ $facultyLoad->faculty->professor_id }}</small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-primary">{{ $facultyLoad->total_units }} units</span><br>
                                                <small class="text-muted">{{ $facultyLoad->total_hours }} hrs/week</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="text-center mt-3">
                                    <a href="{{ route('admin.subject-loads.report') }}" class="btn btn-info">
                                        <i class="fas fa-chart-bar"></i> Full Report
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-3">
                                    <i class="fas fa-users fa-2x text-muted mb-2"></i>
                                    <p class="text-muted">No faculty loads for current period.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <a href="{{ route('admin.subject-loads.create') }}" class="btn btn-primary w-100 mb-2">
                                        <i class="fas fa-plus"></i><br>
                                        Assign New Subject
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="{{ route('admin.subject-loads.index') }}" class="btn btn-info w-100 mb-2">
                                        <i class="fas fa-list"></i><br>
                                        View All Loads
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="{{ route('admin.subject-loads.report') }}" class="btn btn-success w-100 mb-2">
                                        <i class="fas fa-file-alt"></i><br>
                                        Generate Report
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="{{ route('admin.subject-loads.export') }}" class="btn btn-warning w-100 mb-2">
                                        <i class="fas fa-download"></i><br>
                                        Export Data
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
