@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Subject Load Details</h4>
                    <div>
                        @if($subjectLoad->status == 'active')
                            <span class="badge bg-success badge-lg">Active</span>
                        @elseif($subjectLoad->status == 'inactive')
                            <span class="badge bg-warning badge-lg">Inactive</span>
                        @else
                            <span class="badge bg-secondary badge-lg">Completed</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Subject Information</h6>
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
                                    <td><strong>Year Level:</strong></td>
                                    <td>
                                        @if($subjectLoad->year_level)
                                            <span class="badge bg-info">{{ $subjectLoad->year_level }}</span>
                                        @else
                                            <span class="text-muted">Not Set</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Units:</strong></td>
                                    <td>{{ $subjectLoad->units }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Hours per Week:</strong></td>
                                    <td>{{ $subjectLoad->hours_per_week }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Room:</strong></td>
                                    <td>{{ $subjectLoad->room ?: 'TBA' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Faculty & Schedule</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Faculty:</strong></td>
                                    <td>{{ $subjectLoad->faculty->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Professor ID:</strong></td>
                                    <td>{{ $subjectLoad->faculty->professor_id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Schedule:</strong></td>
                                    <td>{{ $subjectLoad->schedule_display }}</td>
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
                                    <td>{{ ucfirst($subjectLoad->status) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($subjectLoad->notes)
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="text-muted">Notes</h6>
                                <div class="bg-light p-3 rounded">
                                    {{ $subjectLoad->notes }}
                                </div>
                            </div>
                        </div>
                    @endif

                    <hr>

                    <!-- Faculty Load Summary -->
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="text-muted">Faculty Load Summary ({{ $subjectLoad->academic_year }} - {{ $subjectLoad->semester }})</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h3>{{ $totalUnits }}</h3>
                                            <p class="mb-0">Total Units</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h3>{{ $totalHours }}</h3>
                                            <p class="mb-0">Total Hours/Week</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h3>{{ $otherLoads->count() + 1 }}</h3>
                                            <p class="mb-0">Total Subjects</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($otherLoads->count() > 0)
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="text-muted">Other Subject Loads (Same Period)</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th>Subject</th>
                                                <th>Section</th>
                                                <th>Schedule</th>
                                                <th>Units</th>
                                                <th>Hours</th>
                                                <th>Room</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($otherLoads as $load)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $load->subject_code }}</strong><br>
                                                        <small class="text-muted">{{ $load->subject_name }}</small>
                                                    </td>
                                                    <td>{{ $load->section }}</td>
                                                    <td>{{ $load->schedule_display }}</td>
                                                    <td>{{ $load->units }}</td>
                                                    <td>{{ $load->hours_per_week }}</td>
                                                    <td>{{ $load->room ?: 'TBA' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <hr>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.subject-loads.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                                <div>
                                    <a href="{{ route('admin.subject-loads.edit', $subjectLoad) }}" class="btn btn-primary me-2">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.subject-loads.destroy', $subjectLoad) }}" 
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this subject load?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
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
