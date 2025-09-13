@extends('layouts.professor_admin')

@section('title', 'Subject Load Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><i class="fas fa-bookmark"></i> Subject Load Details</h3>
                    <a href="{{ route('professor.schedule.index') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Back to Schedule
                    </a>
                </div>
                <div class="card-body">

                    <div class="row">
                        <!-- Subject Load Details -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <span class="badge bg-success me-2">Subject Load Tracker</span>
                                        {{ $schedule->subject_code }} - {{ $schedule->subject_name }}
                                    </h5>
                                </div>
                                <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Subject Code:</strong></td>
                                            <td>{{ $schedule->subject_code }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Subject Name:</strong></td>
                                            <td>{{ $schedule->subject_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Section:</strong></td>
                                            <td>{{ $schedule->section }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Year Level:</strong></td>
                                            <td>{{ $schedule->year_level }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Units:</strong></td>
                                            <td>{{ $schedule->units }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Hours per Week:</strong></td>
                                            <td>{{ $schedule->hours_per_week }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Schedule Day:</strong></td>
                                            <td>{{ ucfirst($schedule->schedule_day) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Time:</strong></td>
                                            <td>{{ $schedule->time_range }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Room:</strong></td>
                                            <td>{{ $schedule->room ?: 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Academic Year:</strong></td>
                                            <td>{{ $schedule->academic_year }}-{{ $schedule->academic_year + 1 }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Semester:</strong></td>
                                            <td>{{ $schedule->semester }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $schedule->status === 'active' ? 'success' : ($schedule->status === 'inactive' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($schedule->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            
                            @if($schedule->notes)
                                <div class="mt-3">
                                    <h6>Notes:</h6>
                                    <p class="text-muted">{{ $schedule->notes }}</p>
                                </div>
                            @endif
                                </div>
                            </div>
                        </div>

                        <!-- Period Summary -->
                        <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Period Summary</h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <h4>{{ $schedule->semester }} {{ $schedule->academic_year }}-{{ $schedule->academic_year + 1 }}</h4>
                            </div>
                            
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="border rounded p-2">
                                        <h5 class="text-primary mb-0">{{ $periodSummary['total_subjects'] }}</h5>
                                        <small class="text-muted">Subjects</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded p-2">
                                        <h5 class="text-success mb-0">{{ $periodSummary['total_units'] }}</h5>
                                        <small class="text-muted">Units</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded p-2">
                                        <h5 class="text-info mb-0">{{ $periodSummary['total_hours'] }}</h5>
                                        <small class="text-muted">Hours</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Other Assignments -->
                    @if($otherSchedules->count() > 0)
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="mb-0">Other Assignments This Period</h6>
                            </div>
                            <div class="card-body">
                                @foreach($otherSchedules->take(5) as $other)
                                    <div class="border-bottom pb-2 mb-2">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong class="small">{{ $other->subject_code }}</strong>
                                                <div class="text-muted small">{{ $other->section }}</div>
                                                <div class="text-muted small">
                                                    {{ ucfirst($other->schedule_day) }} {{ $other->time_range }}
                                                </div>
                                            </div>
                                            <span class="badge bg-{{ isset($other->source_name) ? ($other->source_name === 'Subject Load Tracker' ? 'success' : 'primary') : 'success' }} small">
                                                {{ isset($other->source_name) ? ($other->source_name === 'Subject Load Tracker' ? 'SLT' : 'SA') : 'SLT' }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                                
                                @if($otherSchedules->count() > 5)
                                    <div class="text-center">
                                        <small class="text-muted">... and {{ $otherSchedules->count() - 5 }} more</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

