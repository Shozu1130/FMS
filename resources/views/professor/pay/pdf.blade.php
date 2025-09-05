<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pay Record - {{ $payslip->period_name }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table td { padding: 5px; border: 1px solid #ddd; }
        .summary-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .summary-table th, .summary-table td { padding: 8px; border: 1px solid #ddd; text-align: left; }
        .summary-table th { background-color: #f5f5f5; }
        .total-row { background-color: #e8f4fd; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Faculty Management System</h2>
        <h3>Pay Record</h3>
        <p>{{ $payslip->period_name }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="20%"><strong>Professor ID:</strong></td>
            <td width="30%">{{ $faculty->professor_id }}</td>
            <td width="20%"><strong>Employment Type:</strong></td>
            <td width="30%">{{ $payslip->employment_type }}</td>
        </tr>
        <tr>
            <td><strong>Name:</strong></td>
            <td>{{ $faculty->name }}</td>
            <td><strong>Period:</strong></td>
            <td>{{ $payslip->period_name }}</td>
        </tr>
        @if($salaryGrade)
        <tr>
            <td><strong>Salary Grade:</strong></td>
            <td>Grade {{ $salaryGrade->grade }}</td>
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
    </table>

    <h4>Pay Calculation</h4>
    <table class="summary-table">
        <tr>
            <th>Description</th>
            <th class="text-right">Hours/Days</th>
            <th class="text-right">Rate</th>
            <th class="text-right">Amount</th>
        </tr>
        <tr>
            <td>Regular Hours</td>
            <td class="text-right">{{ number_format($payslip->regular_hours, 2) }}</td>
            <td class="text-right">
                @if($salaryGrade)
                    @if($payslip->employment_type === 'Full-Time')
                        ₱{{ number_format($salaryGrade->full_time_base_salary, 2) }}
                    @else
                        ₱{{ number_format($salaryGrade->part_time_base_salary, 2) }}
                    @endif
                @endif
            </td>
            <td class="text-right">₱{{ number_format($payslip->base_salary, 2) }}</td>
        </tr>
        @if($payslip->overtime_hours > 0)
        <tr>
            <td>Overtime Hours</td>
            <td class="text-right">{{ number_format($payslip->overtime_hours, 2) }}</td>
            <td class="text-right">
                @if($salaryGrade)
                    @if($payslip->employment_type === 'Full-Time')
                        ₱{{ number_format($salaryGrade->full_time_base_salary * 1.25, 2) }}
                    @else
                        ₱{{ number_format($salaryGrade->part_time_base_salary * 1.25, 2) }}
                    @endif
                @endif
            </td>
            <td class="text-right">₱{{ number_format($payslip->overtime_pay, 2) }}</td>
        </tr>
        @endif
        @if($payslip->allowance > 0)
        <tr>
            <td>Allowance</td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
            <td class="text-right">₱{{ number_format($payslip->allowance, 2) }}</td>
        </tr>
        @endif
        <tr class="total-row">
            <td><strong>Gross Pay</strong></td>
            <td class="text-right">{{ number_format($payslip->total_hours, 2) }}</td>
            <td class="text-right">-</td>
            <td class="text-right"><strong>₱{{ number_format($payslip->gross_salary, 2) }}</strong></td>
        </tr>
    </table>

    @if($payslip->total_deductions > 0)
    <h4>Deductions</h4>
    <table class="summary-table">
        <tr>
            <th>Description</th>
            <th class="text-right">Days</th>
            <th class="text-right">Rate</th>
            <th class="text-right">Amount</th>
        </tr>
        @if($payslip->late_deductions > 0)
        <tr>
            <td>Late Deductions</td>
            <td class="text-right">{{ $attendances->where('status', 'late')->count() }}</td>
            <td class="text-right">0.5 hrs/day</td>
            <td class="text-right">-₱{{ number_format($payslip->late_deductions, 2) }}</td>
        </tr>
        @endif
        @if($payslip->absence_deductions > 0)
        <tr>
            <td>Absence Deductions</td>
            <td class="text-right">{{ $attendances->where('status', 'absent')->count() }}</td>
            <td class="text-right">8 hrs/day</td>
            <td class="text-right">-₱{{ number_format($payslip->absence_deductions, 2) }}</td>
        </tr>
        @endif
        <tr class="total-row">
            <td><strong>Total Deductions</strong></td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
            <td class="text-right"><strong>-₱{{ number_format($payslip->total_deductions, 2) }}</strong></td>
        </tr>
    </table>
    @endif

    <h4>Final Pay Summary</h4>
    <table class="summary-table">
        <tr>
            <td><strong>Gross Pay</strong></td>
            <td class="text-right"><strong>₱{{ number_format($payslip->gross_salary, 2) }}</strong></td>
        </tr>
        <tr>
            <td><strong>Total Deductions</strong></td>
            <td class="text-right"><strong>-₱{{ number_format($payslip->total_deductions, 2) }}</strong></td>
        </tr>
        <tr class="total-row">
            <td><strong>Net Pay</strong></td>
            <td class="text-right"><strong>₱{{ number_format($payslip->net_salary, 2) }}</strong></td>
        </tr>
    </table>

    <div style="margin-top: 40px; text-align: center; font-size: 10px; color: #666;">
        Generated on {{ now()->format('M d, Y H:i A') }} | Faculty Management System
    </div>
</body>
</html>
