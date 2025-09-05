@extends('layouts.admin')

@section('title', 'Assign Faculty to Salary Grades')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Assign Faculty to Salary Grades</h3>
                    <a href="{{ route('admin.salary-grades.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Grades
                    </a>
                </div>
                <div class="card-body">
                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('admin.salary-grades.assign.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Select Faculty *</label>
                                    <select name="faculty_id" class="form-control" required>
                                        <option value="">Choose Faculty Member</option>
                                        @foreach($faculties as $faculty)
                                            <option value="{{ $faculty->id }}" {{ old('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                                {{ $faculty->name }} ({{ $faculty->professor_id }}) - {{ $faculty->employment_type }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Salary Grade *</label>
                                    <select name="salary_grade_id" class="form-control" required>
                                        <option value="">Choose Salary Grade</option>
                                        @foreach($salaryGrades as $grade)
                                            <option value="{{ $grade->id }}" {{ old('salary_grade_id') == $grade->id ? 'selected' : '' }}>
                                                Grade {{ $grade->grade }} - FT: ₱{{ number_format($grade->full_time_base_salary, 2) }}/hr, PT: ₱{{ number_format($grade->part_time_base_salary, 2) }}/hr
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Effective Date *</label>
                                    <input type="date" name="effective_date" class="form-control" value="{{ old('effective_date', date('Y-m-d')) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Notes (Optional)</label>
                                    <textarea name="notes" class="form-control" rows="2" placeholder="Any additional notes...">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Assign Faculty to Grade
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Faculty Assignments -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Current Faculty Assignments</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Faculty</th>
                                    <th>Professor ID</th>
                                    <th>Employment Type</th>
                                    <th>Current Grade</th>
                                    <th>Hourly Rate</th>
                                    <th>Effective Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($currentAssignments as $assignment)
                                <tr>
                                    <td>{{ $assignment->faculty->name }}</td>
                                    <td>{{ $assignment->faculty->professor_id }}</td>
                                    <td>
                                        <span class="badge {{ $assignment->faculty->employment_type == 'Full-Time' ? 'bg-success' : 'bg-info' }}">
                                            {{ $assignment->faculty->employment_type }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>Grade {{ $assignment->salaryGrade->grade }}</strong>
                                    </td>
                                    <td>
                                        @if($assignment->faculty->employment_type == 'Full-Time')
                                            ₱{{ number_format($assignment->salaryGrade->full_time_base_salary, 2) }}/hr
                                        @else
                                            ₱{{ number_format($assignment->salaryGrade->part_time_base_salary, 2) }}/hr
                                        @endif
                                    </td>
                                    <td>{{ $assignment->effective_date->format('M d, Y') }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.salary-grades.assign.remove', $assignment->id) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Remove this assignment?')">
                                                <i class="fas fa-times"></i> Remove
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No faculty assignments found.</td>
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
