@extends('layouts.admin')

@section('title', 'Salary Calculations')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Salary Calculations - {{ \Carbon\Carbon::createFromDate($year, $month)->format('F Y') }}</h3>
                    <a href="{{ route('admin.payslips.index', ['year' => $year, 'month' => $month]) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Payslips
                    </a>
                </div>
                <div class="card-body">
                    <!-- Period Filter -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="year" class="form-select">
                                    @for($y = 2020; $y <= 2030; $y++)
                                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="month" class="form-select">
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::createFromDate(null, $m)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-secondary">Filter</button>
                            </div>
                        </div>
                    </form>

                    <!-- Calculations Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Professor</th>
                                    <th>Employment</th>
                                    <th>Hourly Rate</th>
                                    <th>Hours</th>
                                    <th>Attendance</th>
                                    <th>Base Pay</th>
                                    <th>Overtime</th>
                                    <th>Deductions</th>
                                    <th>Net Salary</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($calculationData as $data)
                                <tr>
                                    <td>
                                        <strong>{{ $data['faculty']->name }}</strong><br>
                                        <small class="text-muted">{{ $data['faculty']->professor_id }}</small>
                                    </td>
                                    <td>
                                        <span class="badge {{ $data['faculty']->employment_type === 'Full-Time' ? 'bg-success' : 'bg-info' }}">
                                            {{ $data['faculty']->employment_type }}
                                        </span>
                                        @if($data['salary_grade'])
                                        <br><small class="text-muted">Grade {{ $data['salary_grade']->grade }}-{{ $data['salary_grade']->step }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($data['hourly_rate'])
                                            <strong>₱{{ number_format($data['hourly_rate'], 2) }}</strong>
                                        @else
                                            <span class="text-danger">Not Set</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="small">
                                            <strong>{{ number_format($data['total_hours'], 2) }}h</strong><br>
                                            Regular: {{ number_format($data['regular_hours'], 2) }}h<br>
                                            @if($data['overtime_hours'] > 0)
                                            <span class="text-warning">OT: {{ number_format($data['overtime_hours'], 2) }}h</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <span class="text-success">Present: {{ $data['present_days'] }}</span><br>
                                            @if($data['late_days'] > 0)
                                            <span class="text-warning">Late: {{ $data['late_days'] }}</span><br>
                                            @endif
                                            @if($data['absent_days'] > 0)
                                            <span class="text-danger">Absent: {{ $data['absent_days'] }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <strong>₱{{ number_format($data['base_salary'], 2) }}</strong>
                                        @if($data['salary_grade'] && $data['salary_grade']->allowance > 0)
                                        <br><small class="text-muted">+ ₱{{ number_format($data['salary_grade']->allowance, 2) }} allowance</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($data['overtime_pay'] > 0)
                                            <span class="text-warning">₱{{ number_format($data['overtime_pay'], 2) }}</span>
                                        @else
                                            <span class="text-muted">₱0.00</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($data['total_deductions'] > 0)
                                        <div class="small text-danger">
                                            <strong>₱{{ number_format($data['total_deductions'], 2) }}</strong><br>
                                            @if($data['late_deductions'] > 0)
                                            Late: ₱{{ number_format($data['late_deductions'], 2) }}<br>
                                            @endif
                                            @if($data['absence_deductions'] > 0)
                                            Absent: ₱{{ number_format($data['absence_deductions'], 2) }}
                                            @endif
                                        </div>
                                        @else
                                            <span class="text-muted">₱0.00</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong class="text-primary">₱{{ number_format($data['net_salary'], 2) }}</strong>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">No faculty data found for this period.</td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(!empty($calculationData))
                            <tfoot class="table-secondary">
                                <tr>
                                    <th colspan="8">Total Payroll</th>
                                    <th>₱{{ number_format(array_sum(array_column($calculationData, 'net_salary')), 2) }}</th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>

                    @if(!empty($calculationData))
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h5><i class="fas fa-info-circle"></i> Calculation Method</h5>
                                <ul class="mb-0">
                                    <li><strong>Base Salary:</strong> Regular Hours × Hourly Rate</li>
                                    <li><strong>Overtime Pay:</strong> Overtime Hours × Hourly Rate × {{ $calculationData[0]['salary_grade']->overtime_multiplier ?? 1.25 }} (multiplier)</li>
                                    <li><strong>Late Deduction:</strong> Late Days × (Hourly Rate × 0.5 hours)</li>
                                    <li><strong>Absence Deduction:</strong> Absent Days × (Hourly Rate × 8 hours)</li>
                                    <li><strong>Standard Hours:</strong> {{ $calculationData[0]['salary_grade']->standard_hours_per_month ?? 160 }} hours per month</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
