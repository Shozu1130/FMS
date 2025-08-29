@extends('layouts.professor_admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">My Salary Grades History</h3>
                </div>
                <div class="card-body">
                    @if($salaryGrades->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Grade-Step</th>
                                        <th>Base Salary</th>
                                        <th>Allowance</th>
                                        <th>Total Salary</th>
                                        <th>Effective Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($salaryGrades as $salaryGrade)
                                        <tr>
                                            <td>Grade {{ $salaryGrade->grade }}-{{ $salaryGrade->step }}</td>
                                            <td>{{ $salaryGrade->formatted_base_salary }}</td>
                                            <td>{{ $salaryGrade->formatted_allowance }}</td>
                                            <td>{{ $salaryGrade->formatted_total_salary }}</td>
                                            <td>{{ $salaryGrade->pivot->effective_date->format('M d, Y') }}</td>
                                            <td>
                                                @if($salaryGrade->pivot->end_date)
                                                    {{ $salaryGrade->pivot->end_date->format('M d, Y') }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($salaryGrade->pivot->is_current)
                                                    <span class="badge bg-success">Current</span>
                                                @else
                                                    <span class="badge bg-secondary">Historical</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <p>No salary grade history found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
