@extends('layouts.professor_admin')

@section('title', 'Pay Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Pay Details - {{ $payslip->period_name }}</h3>
                    <div class="btn-group">
                        <a href="{{ route('professor.pay.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Pay Records
                        </a>
                        @if($payslip->status !== 'draft')
                        <a href="{{ route('professor.pay.download-pdf', $payslip->id) }}" class="btn btn-success">
                            <i class="fas fa-download"></i> Download PDF
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Pay Summary -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h4>{{ number_format($payslip->total_hours, 2) }}</h4>
                                    <small>Total Hours</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h4>₱{{ number_format($payslip->gross_salary, 2) }}</h4>
                                    <small>Gross Pay</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h4>₱{{ number_format($payslip->total_deductions, 2) }}</h4>
                                    <small>Deductions</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h4>₱{{ number_format($payslip->net_salary, 2) }}</h4>
                                    <small>Net Pay</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Breakdown -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Pay Calculation</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Employment Type:</strong></td>
                                            <td>
                                                <span class="badge {{ $payslip->employment_type === 'Full-Time' ? 'bg-success' : 'bg-info' }}">
                                                    {{ $payslip->employment_type }}
                                                </span>
                                            </td>
                                        </tr>
                                        @if($salaryGrade)
                                        <tr>
                                            <td><strong>Salary Grade:</strong></td>
                                            <td>Grade {{ $salaryGrade->grade }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Hourly Rate:</strong></td>
                                            <td>
                                                @if($payslip->employment_type === 'Full-Time')
                                                    ₱{{ number_format($salaryGrade->full_time_base_salary, 2) }}/hr
                                                @else
                                                    ₱{{ number_format($salaryGrade->part_time_base_salary, 2) }}/hr
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td><strong>Regular Hours:</strong></td>
                                            <td>{{ number_format($payslip->regular_hours, 2) }} hours</td>
                                        </tr>
                                        @if($payslip->overtime_hours > 0)
                                        <tr>
                                            <td><strong>Overtime Hours:</strong></td>
                                            <td>{{ number_format($payslip->overtime_hours, 2) }} hours</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td><strong>Base Pay:</strong></td>
                                            <td>₱{{ number_format($payslip->base_salary, 2) }}</td>
                                        </tr>
                                        @if($payslip->overtime_pay > 0)
                                        <tr>
                                            <td><strong>Overtime Pay:</strong></td>
                                            <td>₱{{ number_format($payslip->overtime_pay, 2) }}</td>
                                        </tr>
                                        @endif
                                        @if($payslip->allowance > 0)
                                        <tr>
                                            <td><strong>Allowance:</strong></td>
                                            <td>₱{{ number_format($payslip->allowance, 2) }}</td>
                                        </tr>
                                        @endif
                                        <tr class="table-success">
                                            <td><strong>Gross Pay:</strong></td>
                                            <td><strong>₱{{ number_format($payslip->gross_salary, 2) }}</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Attendance Summary</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Total Days:</strong></td>
                                            <td>{{ $attendances->count() }} days</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Present Days:</strong></td>
                                            <td>{{ $attendances->where('status', 'present')->count() }} days</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Late Days:</strong></td>
                                            <td>{{ $attendances->where('status', 'late')->count() }} days</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Absent Days:</strong></td>
                                            <td>{{ $attendances->where('status', 'absent')->count() }} days</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Hours:</strong></td>
                                            <td>{{ number_format($attendances->sum('total_hours'), 2) }} hours</td>
                                        </tr>
                                    </table>

                                    @if($payslip->total_deductions > 0)
                                    <hr>
                                    <h6>Deductions</h6>
                                    <table class="table table-borderless">
                                        @if($payslip->late_deductions > 0)
                                        <tr>
                                            <td>Late Deductions:</td>
                                            <td class="text-danger">-₱{{ number_format($payslip->late_deductions, 2) }}</td>
                                        </tr>
                                        @endif
                                        @if($payslip->absence_deductions > 0)
                                        <tr>
                                            <td>Absence Deductions:</td>
                                            <td class="text-danger">-₱{{ number_format($payslip->absence_deductions, 2) }}</td>
                                        </tr>
                                        @endif
                                        <tr class="table-warning">
                                            <td><strong>Total Deductions:</strong></td>
                                            <td><strong class="text-danger">-₱{{ number_format($payslip->total_deductions, 2) }}</strong></td>
                                        </tr>
                                    </table>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Final Net Pay -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h3 class="text-primary">Net Pay: ₱{{ number_format($payslip->net_salary, 2) }}</h3>
                                    <p class="text-muted">Status: {!! $payslip->status_badge !!}</p>
                                    @if($payslip->finalized_at)
                                    <small class="text-muted">Finalized on {{ $payslip->finalized_at->format('M d, Y H:i A') }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
