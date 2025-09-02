@extends('layouts.professor_admin')

@section('title', 'Payslip Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Payslip - {{ $payslip->period_name }}</h3>
                    <div class="btn-group">
                        <a href="{{ route('professor.payslips.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        @if($payslip->status !== 'draft')
                        <a href="{{ route('professor.payslips.download-pdf', $payslip->id) }}" class="btn btn-success">
                            <i class="fas fa-download"></i> Download PDF
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Payslip Header -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Employee Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $faculty->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Professor ID:</strong></td>
                                    <td>{{ $faculty->professor_id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Employment Type:</strong></td>
                                    <td>
                                        <span class="badge {{ $payslip->employment_type === 'Full-Time' ? 'bg-success' : 'bg-info' }}">
                                            {{ $payslip->employment_type }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Salary Grade:</strong></td>
                                    <td>{{ $salaryGrade ? "Grade {$salaryGrade->grade}-{$salaryGrade->step}" : 'Not assigned' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Pay Period</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Period:</strong></td>
                                    <td>{{ $payslip->period_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Hourly Rate:</strong></td>
                                    <td>₱{{ number_format($payslip->hourly_rate, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>{!! $payslip->status_badge !!}</td>
                                </tr>
                                <tr>
                                    <td><strong>Generated:</strong></td>
                                    <td>{{ $payslip->generated_at->format('M d, Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Earnings and Deductions -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Earnings & Deductions</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Description</th>
                                                <th>Hours/Days</th>
                                                <th>Rate</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Earnings -->
                                            <tr class="table-light">
                                                <td colspan="4"><strong>EARNINGS</strong></td>
                                            </tr>
                                            <tr>
                                                <td>Regular Hours</td>
                                                <td>{{ number_format($payslip->regular_hours, 2) }} hrs</td>
                                                <td>₱{{ number_format($payslip->hourly_rate, 2) }}</td>
                                                <td>₱{{ number_format($payslip->base_salary, 2) }}</td>
                                            </tr>
                                            @if($payslip->overtime_hours > 0)
                                            <tr>
                                                <td>Overtime Hours</td>
                                                <td>{{ number_format($payslip->overtime_hours, 2) }} hrs</td>
                                                <td>₱{{ number_format($payslip->hourly_rate * ($salaryGrade->overtime_multiplier ?? 1.25), 2) }}</td>
                                                <td>₱{{ number_format($payslip->overtime_pay, 2) }}</td>
                                            </tr>
                                            @endif
                                            @if($payslip->allowance > 0)
                                            <tr>
                                                <td>Allowance</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>₱{{ number_format($payslip->allowance, 2) }}</td>
                                            </tr>
                                            @endif
                                            <tr class="table-success">
                                                <td colspan="3"><strong>GROSS EARNINGS</strong></td>
                                                <td><strong>₱{{ number_format($payslip->gross_salary, 2) }}</strong></td>
                                            </tr>
                                            
                                            <!-- Deductions -->
                                            @if($payslip->total_deductions > 0)
                                            <tr class="table-light">
                                                <td colspan="4"><strong>DEDUCTIONS</strong></td>
                                            </tr>
                                            @if($payslip->late_deductions > 0)
                                            <tr>
                                                <td>Late Deductions</td>
                                                <td>{{ $payslip->late_days }} days</td>
                                                <td>₱{{ number_format($payslip->hourly_rate * 0.5, 2) }}</td>
                                                <td>₱{{ number_format($payslip->late_deductions, 2) }}</td>
                                            </tr>
                                            @endif
                                            @if($payslip->absence_deductions > 0)
                                            <tr>
                                                <td>Absence Deductions</td>
                                                <td>{{ $payslip->absent_days }} days</td>
                                                <td>₱{{ number_format($payslip->hourly_rate * 8, 2) }}</td>
                                                <td>₱{{ number_format($payslip->absence_deductions, 2) }}</td>
                                            </tr>
                                            @endif
                                            <tr class="table-warning">
                                                <td colspan="3"><strong>TOTAL DEDUCTIONS</strong></td>
                                                <td><strong>₱{{ number_format($payslip->total_deductions, 2) }}</strong></td>
                                            </tr>
                                            @endif
                                            
                                            <!-- Net Pay -->
                                            <tr class="table-primary">
                                                <td colspan="3"><strong>NET PAY</strong></td>
                                                <td><strong>₱{{ number_format($payslip->net_salary, 2) }}</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Summary Cards -->
                        <div class="col-md-4">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Attendance Summary</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <h4 class="text-success">{{ $payslip->present_days }}</h4>
                                            <small>Present</small>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="text-primary">{{ number_format($payslip->total_hours, 2) }}</h4>
                                            <small>Total Hours</small>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <h5 class="text-warning">{{ $payslip->late_days }}</h5>
                                            <small>Late</small>
                                        </div>
                                        <div class="col-6">
                                            <h5 class="text-danger">{{ $payslip->absent_days }}</h5>
                                            <small>Absent</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Pay Summary</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <span>Gross Pay:</span>
                                        <strong>₱{{ number_format($payslip->gross_salary, 2) }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Deductions:</span>
                                        <strong>₱{{ number_format($payslip->total_deductions, 2) }}</strong>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <strong>Net Pay:</strong>
                                        <strong class="text-primary">₱{{ number_format($payslip->net_salary, 2) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Daily Attendance Details -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Daily Attendance Records</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Day</th>
                                            <th>Time In</th>
                                            <th>Time Out</th>
                                            <th>Hours Worked</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($attendances as $attendance)
                                        <tr>
                                            <td>{{ $attendance->date->format('M d, Y') }}</td>
                                            <td>{{ $attendance->date->format('D') }}</td>
                                            <td>{{ $attendance->formatted_time_in }}</td>
                                            <td>{{ $attendance->formatted_time_out }}</td>
                                            <td>{{ $attendance->formatted_total_hours }}</td>
                                            <td>{!! $attendance->status_badge !!}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No attendance records found for this period.</td>
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
    </div>
</div>
@endsection
