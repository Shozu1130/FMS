<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payslip - {{ $payslip->period_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .document-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }
        .employee-info {
            width: 100%;
            margin-bottom: 20px;
        }
        .employee-info td {
            padding: 3px 0;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            width: 120px;
        }
        .period-info {
            background-color: #f8f9fa;
            padding: 10px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }
        .earnings-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .earnings-table th,
        .earnings-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .earnings-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .section-header {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .total-row {
            background-color: #d4edda;
            font-weight: bold;
        }
        .deduction-row {
            background-color: #f8d7da;
        }
        .net-pay-row {
            background-color: #cce5ff;
            font-weight: bold;
            font-size: 14px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary-box {
            border: 2px solid #007bff;
            padding: 15px;
            margin: 20px 0;
            background-color: #f8f9ff;
        }
        .attendance-summary {
            width: 100%;
            margin-top: 20px;
        }
        .attendance-summary td {
            padding: 5px;
            border: 1px solid #ddd;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-name">Faculty Management System</div>
        <div class="document-title">PAYSLIP</div>
        <div>{{ $payslip->period_name }}</div>
    </div>

    <!-- Employee Information -->
    <table class="employee-info">
        <tr>
            <td class="label">Employee Name:</td>
            <td><strong>{{ $faculty->name }}</strong></td>
            <td class="label" style="padding-left: 50px;">Professor ID:</td>
            <td><strong>{{ $faculty->professor_id }}</strong></td>
        </tr>
        <tr>
            <td class="label">Employment Type:</td>
            <td>{{ $payslip->employment_type }}</td>
            <td class="label" style="padding-left: 50px;">Salary Grade:</td>
            <td>{{ $salaryGrade ? "Grade {$salaryGrade->grade}-{$salaryGrade->step}" : 'Not assigned' }}</td>
        </tr>
        <tr>
            <td class="label">Pay Period:</td>
            <td>{{ $payslip->period_name }}</td>
            <td class="label" style="padding-left: 50px;">Hourly Rate:</td>
            <td>₱{{ number_format($payslip->hourly_rate, 2) }}</td>
        </tr>
    </table>

    <!-- Period Summary -->
    <div class="period-info">
        <strong>Pay Period Summary:</strong>
        Total Hours: {{ number_format($payslip->total_hours, 2) }} | 
        Present Days: {{ $payslip->present_days }} | 
        Late Days: {{ $payslip->late_days }} | 
        Absent Days: {{ $payslip->absent_days }}
    </div>

    <!-- Earnings and Deductions Table -->
    <table class="earnings-table">
        <thead>
            <tr>
                <th style="width: 40%;">Description</th>
                <th style="width: 20%;">Hours/Days</th>
                <th style="width: 20%;">Rate</th>
                <th style="width: 20%;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <!-- Earnings Section -->
            <tr class="section-header">
                <td colspan="4">EARNINGS</td>
            </tr>
            <tr>
                <td>Regular Hours</td>
                <td class="text-center">{{ number_format($payslip->regular_hours, 2) }} hrs</td>
                <td class="text-right">₱{{ number_format($payslip->hourly_rate, 2) }}</td>
                <td class="text-right">₱{{ number_format($payslip->base_salary, 2) }}</td>
            </tr>
            @if($payslip->overtime_hours > 0)
            <tr>
                <td>Overtime Hours</td>
                <td class="text-center">{{ number_format($payslip->overtime_hours, 2) }} hrs</td>
                <td class="text-right">₱{{ number_format($payslip->hourly_rate * ($salaryGrade->overtime_multiplier ?? 1.25), 2) }}</td>
                <td class="text-right">₱{{ number_format($payslip->overtime_pay, 2) }}</td>
            </tr>
            @endif
            @if($payslip->allowance > 0)
            <tr>
                <td>Allowance</td>
                <td class="text-center">-</td>
                <td class="text-right">-</td>
                <td class="text-right">₱{{ number_format($payslip->allowance, 2) }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td colspan="3"><strong>GROSS EARNINGS</strong></td>
                <td class="text-right"><strong>₱{{ number_format($payslip->gross_salary, 2) }}</strong></td>
            </tr>

            @if($payslip->total_deductions > 0)
            <!-- Deductions Section -->
            <tr class="section-header">
                <td colspan="4">DEDUCTIONS</td>
            </tr>
            @if($payslip->late_deductions > 0)
            <tr class="deduction-row">
                <td>Late Deductions</td>
                <td class="text-center">{{ $payslip->late_days }} days</td>
                <td class="text-right">₱{{ number_format($payslip->hourly_rate * 0.5, 2) }}</td>
                <td class="text-right">₱{{ number_format($payslip->late_deductions, 2) }}</td>
            </tr>
            @endif
            @if($payslip->absence_deductions > 0)
            <tr class="deduction-row">
                <td>Absence Deductions</td>
                <td class="text-center">{{ $payslip->absent_days }} days</td>
                <td class="text-right">₱{{ number_format($payslip->hourly_rate * 8, 2) }}</td>
                <td class="text-right">₱{{ number_format($payslip->absence_deductions, 2) }}</td>
            </tr>
            @endif
            <tr class="deduction-row">
                <td colspan="3"><strong>TOTAL DEDUCTIONS</strong></td>
                <td class="text-right"><strong>₱{{ number_format($payslip->total_deductions, 2) }}</strong></td>
            </tr>
            @endif

            <!-- Net Pay -->
            <tr class="net-pay-row">
                <td colspan="3"><strong>NET PAY</strong></td>
                <td class="text-right"><strong>₱{{ number_format($payslip->net_salary, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <!-- Net Pay Summary Box -->
    <div class="summary-box">
        <div style="text-align: center;">
            <div style="font-size: 16px; font-weight: bold; margin-bottom: 10px;">NET PAY AMOUNT</div>
            <div style="font-size: 24px; font-weight: bold; color: #007bff;">₱{{ number_format($payslip->net_salary, 2) }}</div>
            <div style="margin-top: 10px; font-size: 12px;">
                Pay Period: {{ $payslip->period_name }} | Status: {{ ucfirst($payslip->status) }}
            </div>
        </div>
    </div>

    <!-- Attendance Details -->
    @if($attendances->count() > 0)
    <div style="page-break-inside: avoid;">
        <h4>Daily Attendance Records</h4>
        <table class="earnings-table" style="font-size: 10px;">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Day</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Hours</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $attendance)
                <tr>
                    <td>{{ $attendance->date->format('M d, Y') }}</td>
                    <td>{{ $attendance->date->format('D') }}</td>
                    <td>{{ $attendance->formatted_time_in }}</td>
                    <td>{{ $attendance->formatted_time_out }}</td>
                    <td>{{ $attendance->formatted_total_hours }}</td>
                    <td>{{ ucfirst($attendance->status) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div>Generated on: {{ now()->format('F d, Y h:i A') }}</div>
        <div>This is a computer-generated payslip and does not require a signature.</div>
        <div style="margin-top: 10px;">
            <strong>Note:</strong> Late deductions are calculated at 0.5 hours per late day. 
            Absence deductions are calculated at 8 hours per absent day.
            @if($salaryGrade && $salaryGrade->overtime_multiplier)
            Overtime is calculated at {{ $salaryGrade->overtime_multiplier }}x the regular hourly rate.
            @endif
        </div>
    </div>
</body>
</html>
