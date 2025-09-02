@extends('layouts.professor_admin')

@section('content')
<div class="container">
    <h2>Payslip for {{ $professor->name }}</h2>
    <p>Salary Period: {{ now()->format('F Y') }}</p>

    <h4>Salary Grade</h4>
    <table class="table table-bordered">
        <tr>
            <th>Grade</th>
            <td>{{ $salaryGrade->grade }}</td>
        </tr>
        <tr>
            <th>Step</th>
            <td>{{ $salaryGrade->step }}</td>
        </tr>
        <tr>
            <th>Base Salary</th>
            <td>{{ number_format($salaryGrade->base_salary, 2) }}</td>
        </tr>
        <tr>
            <th>Allowance</th>
            <td>{{ number_format($salaryGrade->allowance, 2) }}</td>
        </tr>
        <tr>
            <th>Total Salary</th>
            <td>{{ number_format($salaryGrade->base_salary + $salaryGrade->allowance, 2) }}</td>
        </tr>
    </table>

    <h4>Attendance Summary</h4>
    <table class="table table-bordered">
        <tr>
            <th>Total Hours</th>
            <td>{{ number_format($attendanceSummary['total_hours'], 2) }}</td>
        </tr>
        <tr>
            <th>Present Days</th>
            <td>{{ $attendanceSummary['present_days'] }}</td>
        </tr>
        <tr>
            <th>Late Days</th>
            <td>{{ $attendanceSummary['late_days'] }}</td>
        </tr>
        <tr>
            <th>Absent Days</th>
            <td>{{ $attendanceSummary['absent_days'] }}</td>
        </tr>
    </table>

    <h4>Salary Calculation</h4>
    <table class="table table-bordered">
        <tr>
            <th>Gross Salary</th>
            <td>{{ $salaryCalculation['formatted_total_salary'] ?? '₱' . number_format($salaryGrade->base_salary + $salaryGrade->allowance, 2) }}</td>
        </tr>
        <tr>
            <th>Deductions</th>
            <td>{{ number_format($salaryCalculation['deductions'] * 100, 1) }}%</td>
        </tr>
        <tr>
            <th>Net Salary</th>
            <td>{{ $salaryCalculation['formatted_final_salary'] }}</td>
        </tr>
    </table>
</div>
@endsection
