@extends('layouts.professor')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Subject Load Details</h1>
                <a href="{{ route('professor.subject-loads.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>

            <div class="row">
                <!-- Subject Information -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Subject Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Subject Code:</strong></td>
                                            <td>{{ $subjectLoad->subject_code }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Subject Name:</strong></td>
                                            <td>{{ $subjectLoad->subject_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Section:</strong></td>
                                            <td>{{ $subjectLoad->section }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Units:</strong></td>
                                            <td>{{ $subjectLoad->units }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Hours per Week:</strong></td>
                                            <td>{{ $subjectLoad->hours_per_week }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Schedule:</strong></td>
                                            <td>{{ $subjectLoad->schedule_display }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Room:</strong></td>
                                            <td>{{ $subjectLoad->room ?: 'TBA' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Academic Year:</strong></td>
                                            <td>{{ $subjectLoad->academic_year }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Semester:</strong></td>
                                            <td>{{ $subjectLoad->semester }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                @if($subjectLoad->status == 'active')
                                                    <span class="badge bg-success">Active</span>
                                                @elseif($subjectLoad->status == 'inactive')
                                                    <span class="badge bg-warning">Inactive</span>
                                                @else
                                                    <span class="badge bg-secondary">Completed</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($subjectLoad->notes)
                                <div class="mt-3">
                                    <h6>Notes:</h6>
                                    <div class="bg-light p-3 rounded">
                                        {{ $subjectLoad->notes }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Period Summary -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Period Load Summary</h6>
                            <small class="text-muted">{{ $subjectLoad->academic_year }} - {{ $subjectLoad->semester }}</small>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <div class="row">
                                    <div class="col-4">
                                        <h5 class="text-primary">{{ $periodSummary['total_subjects'] }}</h5>
                                        <small>Subjects</small>
                                    </div>
                                    <div class="col-4">
                                        <h5 class="text-success">{{ $periodSummary['total_units'] }}</h5>
                                        <small>Units</small>
                                    </div>
                                    <div class="col-4">
                                        <h5 class="text-info">{{ $periodSummary['total_hours'] }}</h5>
                                        <small>Hours</small>
                                    </div>
                                </div>
                            </div>

                            @if($periodSummary['total_hours'] > 40)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Overloaded:</strong> You are assigned more than 40 hours per week.
                                </div>
                            @endif

                            <h6 class="mt-3">Load Status:</h6>
                            @if($periodSummary['total_hours'] > 40)
                                <span class="badge bg-warning">Overloaded ({{ $periodSummary['total_hours'] }} hrs)</span>
                            @elseif($periodSummary['total_hours'] >= 30)
                                <span class="badge bg-success">Full Load ({{ $periodSummary['total_hours'] }} hrs)</span>
                            @else
                                <span class="badge bg-info">Partial Load ({{ $periodSummary['total_hours'] }} hrs)</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Other Subjects in Same Period -->
            @if($otherLoads->count() > 0)
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Other Subjects in {{ $subjectLoad->academic_year }} - {{ $subjectLoad->semester }}</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Subject Code</th>
                                                <th>Subject Name</th>
                                                <th>Section</th>
                                                <th>Units</th>
                                                <th>Schedule</th>
                                                <th>Room</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($otherLoads as $load)
                                                <tr>
                                                    <td><strong>{{ $load->subject_code }}</strong></td>
                                                    <td>{{ $load->subject_name }}</td>
                                                    <td>{{ $load->section }}</td>
                                                    <td>{{ $load->units }}</td>
                                                    <td>{{ $load->schedule_display }}</td>
                                                    <td>{{ $load->room ?: 'TBA' }}</td>
                                                    <td>
                                                        @if($load->status == 'active')
                                                            <span class="badge bg-success">Active</span>
                                                        @elseif($load->status == 'inactive')
                                                            <span class="badge bg-warning">Inactive</span>
                                                        @else
                                                            <span class="badge bg-secondary">Completed</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('professor.subject-loads.show', $load) }}" 
                                                           class="btn btn-sm btn-outline-primary" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
