<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Schedule - {{ $faculty->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }
        .header h2 {
            margin: 5px 0;
            color: #666;
            font-size: 16px;
            font-weight: normal;
        }
        .summary {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .summary-grid {
            display: table;
            width: 100%;
        }
        .summary-row {
            display: table-row;
        }
        .summary-cell {
            display: table-cell;
            padding: 5px 10px;
            vertical-align: top;
        }
        .summary-label {
            font-weight: bold;
            color: #333;
        }
        .day-section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .day-header {
            background-color: #5044e4;
            color: white;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .schedule-item {
            border: 1px solid #ddd;
            margin-bottom: 8px;
            padding: 10px;
            background-color: #fff;
        }
        .schedule-item.slt {
            border-left: 4px solid #28a745;
        }
        .schedule-item.sa {
            border-left: 4px solid #007bff;
        }
        .subject-header {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .source-badge {
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 3px;
            color: white;
            font-weight: normal;
        }
        .source-badge.slt {
            background-color: #28a745;
        }
        .source-badge.sa {
            background-color: #007bff;
        }
        .schedule-details {
            display: table;
            width: 100%;
        }
        .detail-row {
            display: table-row;
        }
        .detail-label {
            display: table-cell;
            font-weight: bold;
            width: 25%;
            padding: 2px 5px 2px 0;
        }
        .detail-value {
            display: table-cell;
            padding: 2px 0;
        }
        .no-schedule {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .legend {
            margin-top: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .legend-title {
            font-weight: bold;
            margin-bottom: 8px;
        }
        .legend-item {
            margin-bottom: 3px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Teaching Schedule</h1>
        <h2>{{ $faculty->name }}</h2>
        <h2>{{ $summary['semester'] }} {{ $summary['academic_year'] }}-{{ $summary['academic_year'] + 1 }}</h2>
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-cell">
                    <span class="summary-label">Total Subjects:</span> {{ $summary['total_subjects'] }}
                </div>
                <div class="summary-cell">
                    <span class="summary-label">Total Units:</span> {{ $summary['total_units'] }}
                </div>
            </div>
            <div class="summary-row">
                <div class="summary-cell">
                    <span class="summary-label">Total Hours/Week:</span> {{ $summary['total_hours'] }}
                </div>
                <div class="summary-cell">
                    <span class="summary-label">Workload Status:</span> {{ $summary['workload_status']['label'] }}
                </div>
            </div>
            <div class="summary-row">
                <div class="summary-cell">
                    <span class="summary-label">Subject Load Tracker:</span> {{ $summary['subject_load_count'] }} assignments
                </div>
                <div class="summary-cell">
                    <span class="summary-label">Schedule Assignment:</span> {{ $summary['schedule_assignment_count'] }} assignments
                </div>
            </div>
        </div>
    </div>

    @foreach($days as $dayKey => $dayName)
        <div class="day-section">
            <div class="day-header">{{ $dayName }}</div>
            
            @if($schedule[$dayKey]->count() > 0)
                @foreach($schedule[$dayKey] as $item)
                    <div class="schedule-item {{ $item->source_name === 'Subject Load Tracker' ? 'slt' : 'sa' }}">
                        <div class="subject-header">
                            <span>{{ $item->subject_code }} - {{ $item->subject_name }}</span>
                            <span class="source-badge {{ $item->source_name === 'Subject Load Tracker' ? 'slt' : 'sa' }}">
                                {{ $item->source_name === 'Subject Load Tracker' ? 'SLT' : 'SA' }}
                            </span>
                        </div>
                        
                        <div class="schedule-details">
                            <div class="detail-row">
                                <div class="detail-label">Section:</div>
                                <div class="detail-value">{{ $item->section }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Year Level:</div>
                                <div class="detail-value">{{ $item->year_level }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Units:</div>
                                <div class="detail-value">{{ $item->units }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Hours/Week:</div>
                                <div class="detail-value">{{ $item->hours_per_week }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Time:</div>
                                <div class="detail-value">{{ $item->time_range }}</div>
                            </div>
                            @if($item->room)
                                <div class="detail-row">
                                    <div class="detail-label">Room:</div>
                                    <div class="detail-value">{{ $item->room }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="no-schedule">No classes scheduled for this day</div>
            @endif
        </div>
    @endforeach

    <div class="legend">
        <div class="legend-title">Legend:</div>
        <div class="legend-item"><strong>SLT</strong> - Subject Load Tracker</div>
        <div class="legend-item"><strong>SA</strong> - Schedule Assignment</div>
    </div>

    <div class="footer">
        Generated on {{ now()->format('F j, Y \a\t g:i A') }} | Faculty Management System
    </div>
</body>
</html>
