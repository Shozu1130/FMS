@extends('layouts.professor_admin')

@section('content')
<h1 class="mb-4">Professor Dashboard</h1>

<div class="row">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h5>Welcome,</h5>
                <h3>{{ $professor->name }}</h3>
                <p class="mb-0">ID: {{ $professor->professor_id }}</p>
                <p class="mb-0">Email: {{ $professor->email }}</p>
            </div>
        </div>
        
        <!-- Salary Grade Information Card -->
        @if($currentSalaryGrade)
        <div class="card mt-3">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0"><i class="bi bi-currency-dollar"></i> Current Salary Grade</h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <h4 class="text-success">Grade {{ $currentSalaryGrade->grade }}-{{ $currentSalaryGrade->step }}</h4>
                    <p class="mb-1"><strong>Base Salary:</strong> {{ $currentSalaryGrade->formatted_base_salary }}</p>
                    <p class="mb-1"><strong>Allowance:</strong> {{ $currentSalaryGrade->formatted_allowance }}</p>
                    <hr>
                    <h5 class="text-primary">Total: {{ $currentSalaryGrade->formatted_total_salary }}</h5>
                </div>
            </div>
        </div>
        @else
        <div class="card mt-3">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0"><i class="bi bi-currency-dollar"></i> Salary Information</h6>
            </div>
            <div class="card-body text-center">
                <p class="text-muted mb-0">No salary grade assigned</p>
            </div>
        </div>
        @endif
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h5>Quick Actions</h5>
                <a href="{{ route('professor.profile.edit') }}" class="btn btn-primary me-2 mb-2">
                    <i class="bi bi-person-circle"></i> Edit My Profile
                </a>
                <a href="#" class="btn btn-outline-primary mb-2">
                    <i class="bi bi-key"></i> Change Password
                </a>
                
                <div class="mt-4">
                    <h6>Your Status: 
                        <span class="badge bg-{{ $professor->status == 'active' ? 'success' : 'secondary' }}">
                            {{ $professor->status }}
                        </span>
                    </h6>
                    
                    <!-- Additional Status Information -->
                    @if($currentSalaryGrade)
                    <h6 class="mt-3">Salary Status: 
                        <span class="badge bg-success">Active</span>
                    </h6>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Additional Information Section -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Additional Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Teaching Assignments</h6>
                        <p class="text-muted">Current semester: {{ $professor->currentTeachingAssignments()->count() }} courses</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Evaluations</h6>
                        <p class="text-muted">Overall rating: 
                            @if($professor->getOverallRatingAverage())
                                {{ number_format($professor->getOverallRatingAverage(), 1) }}/5.0
                            @else
                                No ratings yet
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
