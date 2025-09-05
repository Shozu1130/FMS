@extends('layouts.admin')

@section('title', 'Salary Grade Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Grade {{ $salaryGrade->grade }} Details</h3>
                    <div class="btn-group">
                        <a href="{{ route('admin.salary-grades.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        <a href="{{ route('admin.salary-grades.edit', $salaryGrade->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Grade
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Salary Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Grade:</strong></td>
                                    <td>{{ $salaryGrade->grade }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Full-Time Hourly Rate:</strong></td>
                                    <td>₱{{ number_format($salaryGrade->full_time_base_salary, 2) }}/hr</td>
                                </tr>
                                <tr>
                                    <td><strong>Part-Time Hourly Rate:</strong></td>
                                    <td>₱{{ number_format($salaryGrade->part_time_base_salary, 2) }}/hr</td>
                                </tr>
                                <tr>
                                    <td><strong>Faculty Count:</strong></td>
                                    <td>{{ $facultyCount }} Faculty Members</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Monthly Estimates (160 hours)</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Full-Time Monthly:</strong></td>
                                    <td>₱{{ number_format($salaryGrade->full_time_base_salary * 160, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Part-Time Monthly:</strong></td>
                                    <td>₱{{ number_format($salaryGrade->part_time_base_salary * 160, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Faculty Members in this Grade -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Faculty Members in Grade {{ $salaryGrade->grade }}</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Faculty Name</th>
                                    <th>Professor ID</th>
                                    <th>Employment Type</th>
                                    <th>Hourly Rate</th>
                                    <th>Assigned Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($facultyMembers as $assignment)
                                <tr>
                                    <td>{{ $assignment->faculty->name }}</td>
                                    <td>{{ $assignment->faculty->professor_id }}</td>
                                    <td>
                                        <span class="badge {{ $assignment->faculty->employment_type == 'Full-Time' ? 'bg-success' : 'bg-info' }}">
                                            {{ $assignment->faculty->employment_type }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($assignment->faculty->employment_type == 'Full-Time')
                                            ₱{{ number_format($salaryGrade->full_time_base_salary, 2) }}/hr
                                        @else
                                            ₱{{ number_format($salaryGrade->part_time_base_salary, 2) }}/hr
                                        @endif
                                    </td>
                                    <td>{{ $assignment->effective_date->format('M d, Y') }}</td>
                                    <td>
                                        @if($assignment->end_date && $assignment->end_date <= now())
                                            <span class="badge bg-danger">Inactive</span>
                                        @else
                                            <span class="badge bg-success">Active</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No faculty members assigned to this grade.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
