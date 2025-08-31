@extends('layouts.professor_admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Current Month Attendance Summary -->
            @if($currentMonthAttendance && $currentSalaryGrade)
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-calendar-check"></i> Current Month Attendance Summary
                        ({{ now()->format('F Y') }})
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="text-primary">{{ number_format($currentMonthAttendance['total_hours'], 2) }}</h5>
                                    <p class="mb-0">Total Hours</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success">
                                <div class="card-body text-center text-white">
                                    <h5>{{ $currentMonthAttendance['present_days'] }}</h5>
                                    <p class="mb-0">Present Days</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning">
                                <div class="card-body text-center">
                                    <h5>{{ $currentMonthAttendance['late_days'] }}</h5>
                                    <p class="mb-0">Late Days</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger">
                                <div class="card-body text-center text-white">
                                    <h5>{{ $currentMonthAttendance['absent_days'] }}</h5>
                                    <p class="mb-0">Absent Days</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Salary Calculation with Attendance Adjustments -->
            @if($currentMonthSalaryCalculation && $currentSalaryGrade)
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-calculator"></i> Current Month Salary Calculation
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Base Information:</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>Base Salary:</td>
                                    <td class="text-end">{{ $currentMonthSalaryCalculation['formatted_base_salary'] ?? '₱' . number_format($currentSalaryGrade->base_salary, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Allowance:</td>
                                    <td class="text-end">{{ $currentMonthSalaryCalculation['formatted_allowance'] ?? '₱' . number_format($currentSalaryGrade->allowance, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Gross Salary:</strong></td>
                                    <td class="text-end"><strong>{{ $currentMonthSalaryCalculation['formatted_total_salary'] ?? $currentSalaryGrade->formatted_total_salary }}</strong></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Attendance Adjustments:</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>Total Hours:</td>
                                    <td class="text-end">{{ number_format($currentMonthSalaryCalculation['total_hours'], 2) }} hrs</td>
                                </tr>
                                <tr>
                                    <td>Late Days:</td>
                                    <td class="text-end">{{ $currentMonthSalaryCalculation['late_days'] }}</td>
                                </tr>
                                <tr>
                                    <td>Early Departures:</td>
                                    <td class="text-end">{{ $currentMonthSalaryCalculation['early_departure_days'] }}</td>
                                </tr>
                                <tr>
                                    <td>Half Days:</td>
                                    <td class="text-end">{{ $currentMonthSalaryCalculation['half_days'] }}</td>
                                </tr>
                                <tr>
                                    <td>Deductions:</td>
                                    <td class="text-end text-danger">{{ number_format($currentMonthSalaryCalculation['deductions'] * 100, 1) }}%</td>
                                </tr>
                                <tr>
                                    <td><strong>Net Salary:</strong></td>
                                    <td class="text-end"><strong class="text-success">{{ $currentMonthSalaryCalculation['formatted_final_salary'] }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Salary Grades History -->
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
                                        <th>Current Month Hours</th>
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
                                            <td>
                                                @if($salaryGrade->pivot->is_current && $totalHoursCurrentMonth > 0)
                                                    <span class="badge bg-info">{{ number_format($totalHoursCurrentMonth, 2) }} hrs</span>
                                                @else
                                                    <span class="text-muted">-</span>
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
