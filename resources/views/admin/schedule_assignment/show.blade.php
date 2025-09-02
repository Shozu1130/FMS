@extends('layouts.admin')

@section('title', 'Schedule Assignment Details')

@section('content')
<div class="container-fluid">
    <!-- Professional Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
                <i class="fas fa-info-circle text-info mr-2"></i>
                Assignment Details
            </h1>
            <p class="text-muted mb-0">{{ $scheduleAssignment->subject_code }} - {{ $scheduleAssignment->subject_name }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.schedule-assignment.edit', $scheduleAssignment) }}" class="btn btn-warning btn-sm rounded-pill shadow-sm">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
            <a href="{{ route('admin.schedule-assignment.index') }}" class="btn btn-secondary btn-sm rounded-pill shadow-sm">
                <i class="fas fa-arrow-left mr-1"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-gradient-info text-white py-3">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-clipboard-list mr-2"></i>Assignment Information
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-gray-800">Faculty Details</h6>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="font-weight-bold">Name:</td>
                                    <td>{{ $scheduleAssignment->faculty->name }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Professor ID:</td>
                                    <td>{{ $scheduleAssignment->faculty->professor_id }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Employment Type:</td>
                                    <td>{{ $scheduleAssignment->faculty->employment_type }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Email:</td>
                                    <td>{{ $scheduleAssignment->faculty->email }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-gray-800">Subject Details</h6>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="font-weight-bold">Subject Code:</td>
                                    <td>{{ $scheduleAssignment->subject_code }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Subject Name:</td>
                                    <td>{{ $scheduleAssignment->subject_name }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Section:</td>
                                    <td>{{ $scheduleAssignment->section }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Year Level:</td>
                                    <td>{{ $scheduleAssignment->year_level }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-gray-800">Schedule Information</h6>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="font-weight-bold">Day:</td>
                                    <td>{{ ucfirst($scheduleAssignment->schedule_day) }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Time:</td>
                                    <td>{{ $scheduleAssignment->time_range }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Room:</td>
                                    <td>{{ $scheduleAssignment->room ?: 'Not assigned' }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Full Schedule:</td>
                                    <td><strong>{{ $scheduleAssignment->schedule_display }}</strong></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-gray-800">Load & Status</h6>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="font-weight-bold">Units:</td>
                                    <td>{{ $scheduleAssignment->units }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Hours per Week:</td>
                                    <td>{{ $scheduleAssignment->hours_per_week }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Status:</td>
                                    <td>
                                        <span class="badge bg-{{ $scheduleAssignment->status == 'active' ? 'success' : ($scheduleAssignment->status == 'inactive' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($scheduleAssignment->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Source:</td>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ $scheduleAssignment->source == 'direct' ? 'Direct Assignment' : 'Subject Load Tracker' }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-gray-800">Academic Period</h6>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="font-weight-bold">Academic Year:</td>
                                    <td>{{ $scheduleAssignment->academic_year }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Semester:</td>
                                    <td>{{ $scheduleAssignment->semester }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-gray-800">Timestamps</h6>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="font-weight-bold">Created:</td>
                                    <td>{{ $scheduleAssignment->created_at->format('M d, Y g:i A') }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Updated:</td>
                                    <td>{{ $scheduleAssignment->updated_at->format('M d, Y g:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($scheduleAssignment->notes)
                        <div class="mb-4">
                            <h6 class="font-weight-bold text-gray-800">Notes</h6>
                            <div class="p-3 bg-light rounded">
                                {{ $scheduleAssignment->notes }}
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.schedule-assignment.edit', $scheduleAssignment) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Assignment
                        </a>
                        <form method="POST" action="{{ route('admin.schedule-assignment.destroy', $scheduleAssignment) }}" 
                              class="d-inline" onsubmit="return confirm('Are you sure you want to delete this schedule assignment?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Delete Assignment
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Faculty Load Summary -->
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Faculty Load Summary</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="font-weight-bold">Total Load</h6>
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="font-weight-bold text-primary">{{ $loadSummary['total_units'] }}</div>
                                <small>Units</small>
                            </div>
                            <div class="col-4">
                                <div class="font-weight-bold text-info">{{ $loadSummary['total_hours'] }}</div>
                                <small>Hours</small>
                            </div>
                            <div class="col-4">
                                <span class="badge bg-{{ $loadSummary['workload_status']['class'] }}">
                                    {{ $loadSummary['workload_status']['label'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    @if($loadSummary['total_assignments'] > 1)
                        <div class="mb-3">
                            <h6 class="font-weight-bold">Other Assignments ({{ $loadSummary['total_assignments'] - 1 }})</h6>
                            <div class="small">
                                @foreach($loadSummary['schedule_assignments'] as $assignment)
                                    @if($assignment->id != $scheduleAssignment->id)
                                        <div class="mb-1">
                                            • {{ $assignment->subject_code }} ({{ $assignment->schedule_display }})
                                            <span class="badge bg-primary">Schedule</span>
                                        </div>
                                    @endif
                                @endforeach
                                
                                @foreach($loadSummary['subject_load_assignments'] as $assignment)
                                    <div class="mb-1">
                                        • {{ $assignment->subject_code }} ({{ $assignment->schedule_display }})
                                        <span class="badge bg-success">Subject Load</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($loadSummary['workload_status']['status'] == 'overloaded')
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Overloaded!</strong> This faculty member has more than 40 hours per week.
                        </div>
                    @elseif($loadSummary['workload_status']['status'] == 'full_load')
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <strong>Full Load</strong> This faculty member has a full teaching load.
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle"></i>
                            <strong>Partial Load</strong> This faculty member has room for more assignments.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
