@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
                <i class="fas fa-tachometer-alt text-primary mr-2"></i>
                Admin Dashboard
                @if($isMasterAdmin)
                    <span class="badge badge-danger ml-2" style="background-color: #dc3545 !important; color: white !important; font-weight: bold;">Master Admin</span>
                @endif
            </h1>
            <p class="text-muted mb-0">
                {{ $currentYear }} {{ $currentSemester }} - System Overview
                @if(!$isMasterAdmin && $adminDepartment)
                    <span class="badge badge-info ml-2" style="background-color: #17a2b8 !important; color: white !important; font-weight: bold;">{{ $adminDepartment }} Department</span>
                @endif
            </p>
        </div>
        <div class="text-muted small">
            <i class="fas fa-clock mr-1"></i>Last updated: {{ now()->format('M d, Y h:i A') }}
        </div>
    </div>

    <!-- Core Management Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Professors</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $facultyCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Subject Loads</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $subjectLoadStats['active_assignments'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Units</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $subjectLoadStats['total_units'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calculator fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Hours</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $subjectLoadStats['total_hours'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subject Load Tracker Overview -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar mr-2"></i>Subject Load Tracker Overview
                    </h6>
                    <a href="{{ route('admin.subject-loads.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye mr-1"></i>View All
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <div class="h4 font-weight-bold text-primary">{{ $subjectLoadStats['total_assignments'] }}</div>
                                <div class="text-xs text-uppercase text-gray-600">Total Assignments</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <div class="h4 font-weight-bold text-success">{{ $subjectLoadStats['active_assignments'] }}</div>
                                <div class="text-xs text-uppercase text-gray-600">Active</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <div class="h4 font-weight-bold text-secondary">{{ $subjectLoadStats['inactive_assignments'] }}</div>
                                <div class="text-xs text-uppercase text-gray-600">Inactive</div>
                            </div>
                        </div>
                    </div>
                    
                    @if($subjectLoadStats['total_assignments'] > 0)
                        <div class="progress mb-3">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ ($subjectLoadStats['active_assignments'] / $subjectLoadStats['total_assignments']) * 100 }}%">
                                {{ round(($subjectLoadStats['active_assignments'] / $subjectLoadStats['total_assignments']) * 100, 1) }}% Active
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-exclamation-triangle mr-2"></i>System Alerts
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-{{ $conflictCount > 0 ? 'danger' : 'success' }} mb-3">
                        <i class="fas fa-{{ $conflictCount > 0 ? 'exclamation-triangle' : 'check-circle' }} mr-2"></i>
                        <strong>{{ $conflictCount }}</strong> Schedule Conflicts
                    </div>
                    
                    <div class="alert alert-{{ $overloadedFaculty > 0 ? 'warning' : 'info' }} mb-3">
                        <i class="fas fa-{{ $overloadedFaculty > 0 ? 'user-clock' : 'user-check' }} mr-2"></i>
                        <strong>{{ $overloadedFaculty }}</strong> Overloaded Faculty
                    </div>

                    <div class="alert alert-info mb-0">
                        <i class="fas fa-clipboard-check mr-2"></i>
                        <strong>{{ $pendingClearanceRequests }}</strong> Pending Clearance Requests
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Faculty Workload Distribution -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-users-cog mr-2"></i>Faculty Workload Distribution
                    </h6>
                </div>
                <div class="card-body">
                    @if($workloadDistribution && count($workloadDistribution) > 0)
                        @php
                            $overloaded = collect($workloadDistribution)->where('workload_status.status', 'overloaded')->count();
                            $fullLoad = collect($workloadDistribution)->where('workload_status.status', 'full_load')->count();
                            $partialLoad = collect($workloadDistribution)->where('workload_status.status', 'partial_load')->count();
                            $total = count($workloadDistribution);
                        @endphp
                        
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="text-danger">
                                    <div class="h5 font-weight-bold">{{ $overloaded }}</div>
                                    <div class="text-xs">Overloaded</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-success">
                                    <div class="h5 font-weight-bold">{{ $fullLoad }}</div>
                                    <div class="text-xs">Full Load</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-info">
                                    <div class="h5 font-weight-bold">{{ $partialLoad }}</div>
                                    <div class="text-xs">Partial Load</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-muted text-center">No workload data available for current semester.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clock mr-2"></i>Recent Subject Load Assignments
                    </h6>
                    <a href="{{ route('admin.subject-loads.index') }}" class="btn btn-outline-primary btn-sm">View All</a>
                </div>
                <div class="card-body">
                    @if($recentSubjectLoads->count() > 0)
                        @foreach($recentSubjectLoads as $load)
                            <div class="d-flex align-items-center mb-3">
                                <div class="mr-3">
                                    <div class="icon-circle bg-primary">
                                        <i class="fas fa-chalkboard text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="small font-weight-bold">{{ $load->subject_code }} - {{ $load->subject_name }}</div>
                                    <div class="small text-muted">{{ $load->faculty ? $load->faculty->name : 'Unknown Faculty' }}</div>
                                </div>
                                <div class="text-muted small">
                                    {{ $load->created_at->diffForHumans() }}
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">No recent assignments found.</p>
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
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt mr-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.subject-loads.create') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-plus mr-2"></i>New Subject Load
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.schedule-assignment.dashboard') }}" class="btn btn-info btn-block">
                                <i class="fas fa-calendar-check mr-2"></i>Schedule Assignment
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.faculty.index') }}" class="btn btn-success btn-block">
                                <i class="fas fa-users mr-2"></i>Manage Professors
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.clearance-requests.index') }}" class="btn btn-warning btn-block">
                                <i class="fas fa-clipboard-check mr-2"></i>Clearance Requests
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.icon-circle {
    height: 2rem;
    width: 2rem;
    border-radius: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endsection
