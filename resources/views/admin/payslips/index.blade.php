@extends('layouts.admin')

@section('title', 'Payroll Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Payroll Management - {{ \Carbon\Carbon::createFromDate($year, $month)->format('F Y') }}</h3>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateModal">
                            <i class="fas fa-calculator"></i> Generate Payslips
                        </button>
                        <a href="{{ route('admin.payslips.calculations', ['year' => $year, 'month' => $month]) }}" class="btn btn-info">
                            <i class="fas fa-chart-line"></i> View Calculations
                        </a>
                    </div>
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

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5>Total Payroll</h5>
                                    <h3>₱{{ number_format($totalPayroll, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5>Total Faculty</h5>
                                    <h3>{{ $totalFaculty }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5>Full-Time</h5>
                                    <h3>{{ $fullTimeCount }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5>Part-Time</h5>
                                    <h3>{{ $partTimeCount }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payslips Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Professor</th>
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
                                        <strong>{{ $payslip->faculty->name }}</strong><br>
                                        <small class="text-muted">{{ $payslip->faculty->professor_id }}</small>
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
                                            <a href="{{ route('admin.payslips.show', $payslip->id) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($payslip->status === 'draft')
                                            <form method="POST" action="{{ route('admin.payslips.finalize', $payslip->id) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-success" onclick="return confirm('Finalize this payslip?')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            @endif
                                            @if($payslip->status === 'finalized')
                                            <form method="POST" action="{{ route('admin.payslips.mark-paid', $payslip->id) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-warning" onclick="return confirm('Mark as paid?')">
                                                    <i class="fas fa-dollar-sign"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No payslips found for this period.</td>
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

<!-- Generate Payslips Modal -->
<div class="modal fade" id="generateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate Payslips</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.payslips.generate-all') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Year</label>
                            <select name="year" class="form-select" required>
                                @for($y = 2020; $y <= 2030; $y++)
                                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Month</label>
                            <select name="month" class="form-select" required>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::createFromDate(null, $m)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle"></i>
                        This will generate payslips for all faculty members based on their attendance records and salary grades.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate All Payslips</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
