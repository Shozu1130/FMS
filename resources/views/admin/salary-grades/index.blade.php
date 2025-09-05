@extends('layouts.admin')

@section('title', 'Salary Grades Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Salary Grades & Pay</h3>
                    <div class="btn-group">
                        <a href="{{ route('admin.salary-grades.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Grade
                        </a>
                        <a href="{{ route('admin.salary-grades.assign') }}" class="btn btn-success">
                            <i class="fas fa-user-plus"></i> Assign Faculty
                        </a>
                        <a href="{{ route('admin.payslips.calculations') }}" class="btn btn-info">
                            <i class="fas fa-calculator"></i> Pay Calculations
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Grade</th>
                                    <th>Full-Time Hourly Rate</th>
                                    <th>Part-Time Hourly Rate</th>
                                    <th>Faculty Count</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($salaryGrades as $grade)
                                <tr>
                                    <td>
                                        <strong>Grade {{ $grade->grade }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-success fs-6">
                                            ₱{{ number_format($grade->full_time_base_salary, 2) }}/hr
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info fs-6">
                                            ₱{{ number_format($grade->part_time_base_salary, 2) }}/hr
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.salary-grades.faculty', $grade->id) }}" class="badge bg-secondary text-decoration-none">
                                            {{ $grade->faculties_count ?? 0 }} Faculty
                                        </a>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.salary-grades.show', $grade->id) }}" class="btn btn-outline-info" title="View Details">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="{{ route('admin.salary-grades.edit', $grade->id) }}" class="btn btn-outline-primary" title="Edit Grade">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            @if(($grade->faculties_count ?? 0) == 0)
                                            <form method="POST" action="{{ route('admin.salary-grades.destroy', $grade->id) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Delete this salary grade?')" title="Delete Grade">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No salary grades found.</td>
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
