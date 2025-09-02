@extends('layouts.admin')

@section('title', 'Payslip Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Payslip Details - {{ $payslip->period_name }}</h3>
                    <div class="btn-group">
                        <a href="{{ route('admin.payslips.index', ['year' => $payslip->year, 'month' => $payslip->month]) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        @if($payslip->status === 'draft')
                        <form method="POST" action="{{ route('admin.payslips.finalize', $payslip->id) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success" onclick="return confirm('Finalize this payslip?')">
                                <i class="fas fa-check"></i> Finalize
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Faculty Information -->
                        <div class="col-md-6">
                            <h5>Faculty Information</h5>
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
                                <tr>
                                    <td><strong>Hourly Rate:</strong></td>
                                    <td>₱{{ number_format($payslip->hourly_rate, 2) }}</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Payslip Summary -->
                        <div class="col-md-6">
                            <h5>Payslip Summary</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Period:</strong></td>
                                    <td>{{ $payslip->period_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>{!! $payslip->status_badge !!}</td>
                                </tr>
                                <tr>
                                    <td><strong>Generated:</strong></td>
                                    <td>{{ $payslip->generated_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                @if($payslip->finalized_at)
                                <tr>
                                    <td><strong>Finalized:</strong></td>
                                    <td>{{ $payslip->finalized_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                @endif
                                @if($payslip->paid_at)
                                <tr>
                                    <td><strong>Paid:</strong></td>
                                    <td>{{ $payslip->paid_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <hr>

                    <!-- Salary Calculation Breakdown -->
                    <div class="row">
                        <div class="col-md-8">
                            <h5>Salary Calculation Breakdown</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Description</th>
                                            <th>Hours/Days</th>
                                            <th>Rate</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
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
                                            <td><strong>Gross Salary</strong></td>
                                            <td colspan="2"></td>
                                            <td><strong>₱{{ number_format($payslip->gross_salary, 2) }}</strong></td>
                                        </tr>
                                        @if($payslip->late_deductions > 0)
                                        <tr class="table-warning">
                                            <td>Late Deductions</td>
                                            <td>{{ $payslip->late_days }} days</td>
                                            <td>₱{{ number_format($payslip->hourly_rate * 0.5, 2) }}</td>
                                            <td>-₱{{ number_format($payslip->late_deductions, 2) }}</td>
                                        </tr>
                                        @endif
                                        @if($payslip->absence_deductions > 0)
                                        <tr class="table-danger">
                                            <td>Absence Deductions</td>
                                            <td>{{ $payslip->absent_days }} days</td>
                                            <td>₱{{ number_format($payslip->hourly_rate * 8, 2) }}</td>
                                            <td>-₱{{ number_format($payslip->absence_deductions, 2) }}</td>
                                        </tr>
                                        @endif
                                        <tr class="table-primary">
                                            <td><strong>Net Salary</strong></td>
                                            <td colspan="2"></td>
                                            <td><strong>₱{{ number_format($payslip->net_salary, 2) }}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Attendance Summary -->
                        <div class="col-md-4">
                            <h5>Attendance Summary</h5>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <h4 class="text-success">{{ $payslip->present_days }}</h4>
                                            <small>Present Days</small>
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
                                            <small>Late Days</small>
                                        </div>
                                        <div class="col-6">
                                            <h5 class="text-danger">{{ $payslip->absent_days }}</h5>
                                            <small>Absent Days</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Attendance Details -->
                    <h5>Daily Attendance Records</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time In</th>
                                    <th>Time Out</th>
                                    <th>Hours</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $attendance)
                                <tr>
                                    <td>{{ $attendance->date->format('M d, Y') }}</td>
                                    <td>{{ $attendance->formatted_time_in }}</td>
                                    <td>{{ $attendance->formatted_time_out }}</td>
                                    <td>{{ $attendance->formatted_total_hours }}</td>
                                    <td>{!! $attendance->status_badge !!}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No attendance records found.</td>
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
