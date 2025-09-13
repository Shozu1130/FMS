@extends('layouts.admin')

@section('title', 'Faculty in Grade ' . $salaryGrade->grade)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Faculty in Grade {{ $salaryGrade->grade }}</h3>
                    <div class="btn-group">
                        <a href="{{ route('admin.salary-grades.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Grades
                        </a>
                        <a href="{{ route('admin.salary-grades.assign') }}" class="btn btn-success">
                            <i class="fas fa-user-plus"></i> Assign More Faculty
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h4>{{ $facultyCount }}</h4>
                                    <small>Total Faculty</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h4>{{ $fullTimeCount }}</h4>
                                    <small>Full-Time</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h4>{{ $partTimeCount }}</h4>
                                    <small>Part-Time</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h4>₱{{ number_format($averageMonthlyPay, 0) }}</h4>
                                    <small>Avg Monthly Pay</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Faculty Name</th>
                                    <th>Professor ID</th>
                                    <th>Employment Type</th>
                                    <th>Hourly Rate</th>
                                    <th>Est. Monthly Pay</th>
                                    <th>Assigned Date</th>
                                    <th>Actions</th>
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
                                    <td>
                                        @if($assignment->faculty->employment_type == 'Full-Time')
                                            ₱{{ number_format($salaryGrade->full_time_base_salary * 160, 2) }}
                                        @else
                                            ₱{{ number_format($salaryGrade->part_time_base_salary * 160, 2) }}
                                        @endif
                                    </td>
                                    <td>{{ $assignment->effective_date->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.payslips.calculations', ['professor_id' => $assignment->faculty->id]) }}" class="btn btn-outline-info" title="View Pay Details">
                                                <i class="fas fa-calculator"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.salary-grades.assign.remove', $assignment->id) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Remove {{ $assignment->faculty->name }} from this grade?')" title="Remove Assignment">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No faculty members assigned to this grade.</td>
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
