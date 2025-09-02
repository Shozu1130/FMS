@extends('layouts.professor_admin')

@section('title', 'My Payslips')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">My Payslips - {{ $year }}</h3>
                    <div class="btn-group">
                        @if($currentMonthPayslip)
                        <a href="{{ route('professor.payslips.show', $currentMonthPayslip->id) }}" class="btn btn-primary">
                            <i class="fas fa-eye"></i> Current Month
                        </a>
                        @else
                        <form method="POST" action="{{ route('professor.payslips.generate-current') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-plus"></i> Generate Current Month
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Year Filter -->
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
                                <button type="submit" class="btn btn-secondary">Filter</button>
                            </div>
                        </div>
                    </form>

                    <!-- Current Month Summary -->
                    @if($currentMonthPayslip)
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5>Current Month Summary ({{ $currentMonthPayslip->period_name }})</h5>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h4 class="text-primary">{{ number_format($currentMonthPayslip->total_hours, 2) }}</h4>
                                                <small>Total Hours</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h4 class="text-success">₱{{ number_format($currentMonthPayslip->base_salary, 2) }}</h4>
                                                <small>Base Salary</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h4 class="text-warning">₱{{ number_format($currentMonthPayslip->total_deductions, 2) }}</h4>
                                                <small>Deductions</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h4 class="text-info">₱{{ number_format($currentMonthPayslip->net_salary, 2) }}</h4>
                                                <small>Net Salary</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Payslips Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Period</th>
                                    <th>Employment Type</th>
                                    <th>Total Hours</th>
                                    <th>Base Salary</th>
                                    <th>Deductions</th>
                                    <th>Net Salary</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payslips as $payslip)
                                <tr>
                                    <td>
                                        <strong>{{ $payslip->period_name }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge {{ $payslip->employment_type === 'Full-Time' ? 'bg-success' : 'bg-info' }}">
                                            {{ $payslip->employment_type }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($payslip->total_hours, 2) }}h</td>
                                    <td>₱{{ number_format($payslip->base_salary, 2) }}</td>
                                    <td>₱{{ number_format($payslip->total_deductions, 2) }}</td>
                                    <td><strong>₱{{ number_format($payslip->net_salary, 2) }}</strong></td>
                                    <td>{!! $payslip->status_badge !!}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('professor.payslips.show', $payslip->id) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            @if($payslip->status !== 'draft')
                                            <a href="{{ route('professor.payslips.download-pdf', $payslip->id) }}" class="btn btn-outline-success">
                                                <i class="fas fa-download"></i> PDF
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <div class="py-4">
                                            <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                                            <h5>No payslips found</h5>
                                            <p class="text-muted">Your payslips will appear here once they are generated.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $payslips->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
