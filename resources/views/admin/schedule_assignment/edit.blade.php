@extends('layouts.admin')

@section('title', 'Edit Schedule Assignment')

@section('content')
<div class="container-fluid">
    <!-- Professional Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
                <i class="fas fa-edit text-warning mr-2"></i>
                Edit Schedule Assignment
            </h1>
            <p class="text-muted mb-0">Modify assignment details for {{ $scheduleAssignment->faculty->name ?? 'Faculty' }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.schedule-assignment.show', $scheduleAssignment) }}" class="btn btn-info btn-sm rounded-pill shadow-sm">
                <i class="fas fa-eye mr-1"></i> View Details
            </a>
            <a href="{{ route('admin.schedule-assignment.index') }}" class="btn btn-secondary btn-sm rounded-pill shadow-sm">
                <i class="fas fa-arrow-left mr-1"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-gradient-warning text-white py-3">
                    <h6 class="m-0 font-weight-bold text-center">
                        <i class="fas fa-edit mr-2"></i>Assignment Details
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.schedule-assignment.update', $scheduleAssignment) }}" id="assignment-form">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="professor_id" class="form-label font-weight-bold text-dark">
                                    <i class="fas fa-user-tie text-primary mr-1"></i>
                                    Faculty Member <span class="text-danger">*</span>
                                </label>
                                <select name="professor_id" id="professor_id" class="form-select form-select-lg @error('professor_id') is-invalid @enderror" required>
                                    <option value="">Select Faculty Member</option>
                                    @foreach($faculties as $faculty)
                                        <option value="{{ $faculty->id }}" {{ old('professor_id', $scheduleAssignment->professor_id) == $faculty->id ? 'selected' : '' }}>
                                            {{ $faculty->name }} ({{ $faculty->professor_id }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('professor_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="academic_year" class="form-label">Academic Year <span class="text-danger">*</span></label>
                                <select name="academic_year" id="academic_year" class="form-select @error('academic_year') is-invalid @enderror" required>
                                    @for($year = date('Y') - 2; $year <= date('Y') + 2; $year++)
                                        <option value="{{ $year }}" {{ old('academic_year', $scheduleAssignment->academic_year) == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endfor
                                </select>
                                @error('academic_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="semester" class="form-label">Semester <span class="text-danger">*</span></label>
                                <select name="semester" id="semester" class="form-select @error('semester') is-invalid @enderror" required>
                                    @foreach($semesters as $key => $semester)
                                        <option value="{{ $key }}" {{ old('semester', $scheduleAssignment->semester) == $key ? 'selected' : '' }}>
                                            {{ $semester }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('semester')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="subject_code" class="form-label">Subject Code <span class="text-danger">*</span></label>
                                <input type="text" name="subject_code" id="subject_code" 
                                       class="form-control @error('subject_code') is-invalid @enderror" 
                                       value="{{ old('subject_code', $scheduleAssignment->subject_code) }}" required maxlength="20">
                                @error('subject_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="section" class="form-label">Section <span class="text-danger">*</span></label>
                                <input type="text" name="section" id="section" 
                                       class="form-control @error('section') is-invalid @enderror" 
                                       value="{{ old('section', $scheduleAssignment->section) }}" required maxlength="10">
                                @error('section')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="year_level" class="form-label">Year Level <span class="text-danger">*</span></label>
                                <select name="year_level" id="year_level" class="form-select @error('year_level') is-invalid @enderror" required>
                                    <option value="">Select Year Level</option>
                                    @foreach($yearLevels as $key => $level)
                                        <option value="{{ $key }}" {{ old('year_level', $scheduleAssignment->year_level) == $key ? 'selected' : '' }}>
                                            {{ $level }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('year_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="subject_name" class="form-label">Subject Name <span class="text-danger">*</span></label>
                            <input type="text" name="subject_name" id="subject_name" 
                                   class="form-control @error('subject_name') is-invalid @enderror" 
                                   value="{{ old('subject_name', $scheduleAssignment->subject_name) }}" required maxlength="255">
                            @error('subject_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="units" class="form-label">Units <span class="text-danger">*</span></label>
                                <input type="number" name="units" id="units" 
                                       class="form-control @error('units') is-invalid @enderror" 
                                       value="{{ old('units', $scheduleAssignment->units) }}" required min="1" max="6">
                                @error('units')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="hours_per_week" class="form-label">Hours/Week <span class="text-danger">*</span></label>
                                <input type="number" name="hours_per_week" id="hours_per_week" 
                                       class="form-control @error('hours_per_week') is-invalid @enderror" 
                                       value="{{ old('hours_per_week', $scheduleAssignment->hours_per_week) }}" required min="1" max="40">
                                @error('hours_per_week')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                    @foreach($statusOptions as $key => $status)
                                        <option value="{{ $key }}" {{ old('status', $scheduleAssignment->status) == $key ? 'selected' : '' }}>
                                            {{ $status }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="source" class="form-label">Source <span class="text-danger">*</span></label>
                                <select name="source" id="source" class="form-select @error('source') is-invalid @enderror" required>
                                    @foreach($sourceOptions as $key => $source)
                                        <option value="{{ $key }}" {{ old('source', $scheduleAssignment->source) == $key ? 'selected' : '' }}>
                                            {{ $source }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('source')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="schedule_day" class="form-label">Day <span class="text-danger">*</span></label>
                                <select name="schedule_day" id="schedule_day" class="form-select @error('schedule_day') is-invalid @enderror" required>
                                    <option value="">Select Day</option>
                                    @foreach($days as $key => $day)
                                        <option value="{{ $key }}" {{ old('schedule_day', $scheduleAssignment->schedule_day) == $key ? 'selected' : '' }}>
                                            {{ $day }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('schedule_day')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                                <input type="time" name="start_time" id="start_time" 
                                       class="form-control @error('start_time') is-invalid @enderror" 
                                       value="{{ old('start_time', $scheduleAssignment->start_time ? (is_string($scheduleAssignment->start_time) ? $scheduleAssignment->start_time : $scheduleAssignment->start_time->format('H:i')) : '') }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                                <input type="time" name="end_time" id="end_time" 
                                       class="form-control @error('end_time') is-invalid @enderror" 
                                       value="{{ old('end_time', $scheduleAssignment->end_time ? (is_string($scheduleAssignment->end_time) ? $scheduleAssignment->end_time : $scheduleAssignment->end_time->format('H:i')) : '') }}" required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="room" class="form-label">Room</label>
                                <input type="text" name="room" id="room" 
                                       class="form-control @error('room') is-invalid @enderror" 
                                       value="{{ old('room', $scheduleAssignment->room) }}" maxlength="50">
                                @error('room')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" 
                                      rows="3" maxlength="1000">{{ old('notes', $scheduleAssignment->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Maximum 1000 characters</div>
                        </div>

                        <!-- Conflict and Duplicate Alerts -->
                        <div id="conflict-alert" class="alert alert-danger d-none">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span id="conflict-message"></span>
                        </div>

                        <div id="duplicate-alert" class="alert alert-warning d-none">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span id="duplicate-message"></span>
                        </div>

                        @if($errors->has('conflict'))
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ $errors->first('conflict') }}
                            </div>
                        @endif

                        @if($errors->has('duplicate'))
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ $errors->first('duplicate') }}
                            </div>
                        @endif

                        <div class="d-flex justify-content-end gap-3 mt-4">
                            <a href="{{ route('admin.schedule-assignment.index') }}" class="btn btn-secondary btn-lg rounded-pill px-4">
                                <i class="fas fa-times mr-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-warning btn-lg rounded-pill px-4 shadow" id="submit-btn">
                                <i class="fas fa-save mr-1"></i> Update Assignment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Faculty Load Summary Section -->
    <div class="row justify-content-center mt-4">
        <div class="col-lg-10 col-xl-8">
            <div class="card shadow">
                <div class="card-header py-3 bg-gradient-info text-white">
                    <h6 class="m-0 font-weight-bold text-center">
                        <i class="fas fa-chart-line mr-2"></i>Faculty Load Summary
                    </h6>
                </div>
                <div class="card-body" id="load-summary">
                    <!-- Initial load summary -->
                    <div class="mb-3">
                        <h6 class="font-weight-bold text-center">Current Load Summary</h6>
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="font-weight-bold text-primary display-6">{{ $loadSummary['total_units'] }}</div>
                                <small class="text-muted">Units</small>
                            </div>
                            <div class="col-4">
                                <div class="font-weight-bold text-info display-6">{{ $loadSummary['total_hours'] }}</div>
                                <small class="text-muted">Hours</small>
                            </div>
                            <div class="col-4">
                                <div class="d-flex justify-content-center align-items-center">
                                    <span class="badge bg-{{ $loadSummary['workload_status']['class'] }} badge-lg px-3 py-2">
                                        {{ $loadSummary['workload_status']['label'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($loadSummary['total_assignments'] > 0)
                        <div class="mb-3">
                            <h6 class="font-weight-bold text-center">Current Assignments ({{ $loadSummary['total_assignments'] }})</h6>
                            <div class="row">
                                @if($loadSummary['schedule_assignments']->count() > 0)
                                    <div class="col-md-6">
                                        <div class="mb-2"><strong>Schedule Assignments:</strong></div>
                                        @foreach($loadSummary['schedule_assignments'] as $assignment)
                                            <div class="mb-1 small">
                                                • {{ $assignment->subject_code }} ({{ $assignment->schedule_display }})
                                                @if($assignment->id == $scheduleAssignment->id)
                                                    <span class="badge bg-warning">Current</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                
                                @if($loadSummary['subject_load_assignments']->count() > 0)
                                    <div class="col-md-6">
                                        <div class="mb-2"><strong>Subject Load Tracker:</strong></div>
                                        @foreach($loadSummary['subject_load_assignments'] as $assignment)
                                            <div class="mb-1 small">• {{ $assignment->subject_code }} ({{ $assignment->schedule_display }})</div>
                                        @endforeach
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

@push('scripts')
<script>
$(document).ready(function() {
    let conflictCheckTimeout;
    let duplicateCheckTimeout;
    const currentAssignmentId = {{ $scheduleAssignment->id }};
    
    // Function to check for conflicts
    function checkConflicts() {
        const facultyId = $('#professor_id').val();
        const scheduleDay = $('#schedule_day').val();
        const startTime = $('#start_time').val();
        const endTime = $('#end_time').val();
        const academicYear = $('#academic_year').val();
        const semester = $('#semester').val();
        
        if (facultyId && scheduleDay && startTime && endTime && academicYear && semester) {
            $.ajax({
                url: '{{ route("admin.schedule-assignment.check-conflict") }}',
                method: 'GET',
                data: {
                    professor_id: facultyId,
                    schedule_day: scheduleDay,
                    start_time: startTime,
                    end_time: endTime,
                    academic_year: academicYear,
                    semester: semester,
                    exclude_id: currentAssignmentId
                },
                success: function(response) {
                    if (response.has_conflict) {
                        $('#conflict-alert').removeClass('d-none');
                        $('#conflict-message').text(response.message);
                        $('#submit-btn').prop('disabled', true);
                    } else {
                        $('#conflict-alert').addClass('d-none');
                        $('#submit-btn').prop('disabled', false);
                    }
                },
                error: function() {
                    $('#conflict-alert').addClass('d-none');
                    $('#submit-btn').prop('disabled', false);
                }
            });
        }
    }
    
    // Function to check for duplicates
    function checkDuplicates() {
        const facultyId = $('#professor_id').val();
        const subjectCode = $('#subject_code').val();
        const section = $('#section').val();
        const academicYear = $('#academic_year').val();
        const semester = $('#semester').val();
        
        if (facultyId && subjectCode && section && academicYear && semester) {
            $.ajax({
                url: '{{ route("admin.schedule-assignment.check-duplicate") }}',
                method: 'GET',
                data: {
                    professor_id: facultyId,
                    subject_code: subjectCode,
                    section: section,
                    academic_year: academicYear,
                    semester: semester,
                    exclude_id: currentAssignmentId
                },
                success: function(response) {
                    if (response.has_duplicate) {
                        $('#duplicate-alert').removeClass('d-none');
                        $('#duplicate-message').text(response.message);
                        $('#submit-btn').prop('disabled', true);
                    } else {
                        $('#duplicate-alert').addClass('d-none');
                        if (!$('#conflict-alert').hasClass('d-none')) {
                            $('#submit-btn').prop('disabled', true);
                        } else {
                            $('#submit-btn').prop('disabled', false);
                        }
                    }
                }
            });
        }
    }
    
    // Function to load faculty summary
    function loadFacultySummary() {
        const facultyId = $('#professor_id').val();
        const academicYear = $('#academic_year').val();
        const semester = $('#semester').val();
        
        if (facultyId && academicYear && semester) {
            $('#load-summary').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
            
            $.ajax({
                url: '{{ route("admin.schedule-assignment.faculty-load-summary") }}',
                method: 'GET',
                data: {
                    professor_id: facultyId,
                    academic_year: academicYear,
                    semester: semester
                },
                success: function(response) {
                    let html = '<div class="mb-3">';
                    html += '<h6 class="font-weight-bold">Load Summary</h6>';
                    html += '<div class="row text-center">';
                    html += '<div class="col-4"><div class="font-weight-bold text-primary">' + response.total_units + '</div><small>Units</small></div>';
                    html += '<div class="col-4"><div class="font-weight-bold text-info">' + response.total_hours + '</div><small>Hours</small></div>';
                    html += '<div class="col-4"><span class="badge bg-' + response.workload_status.class + '">' + response.workload_status.label + '</span></div>';
                    html += '</div></div>';
                    
                    if (response.total_assignments > 0) {
                        html += '<div class="mb-3">';
                        html += '<h6 class="font-weight-bold">Current Assignments (' + response.total_assignments + ')</h6>';
                        html += '<div class="small">';
                        
                        // Schedule Assignments
                        if (response.schedule_assignments.length > 0) {
                            html += '<div class="mb-2"><strong>Schedule Assignments:</strong></div>';
                            response.schedule_assignments.forEach(function(assignment) {
                                html += '<div class="mb-1">• ' + assignment.subject_code + ' (' + assignment.schedule_display + ')';
                                if (assignment.id == currentAssignmentId) {
                                    html += ' <span class="badge bg-warning">Current</span>';
                                }
                                html += '</div>';
                            });
                        }
                        
                        // Subject Load Assignments
                        if (response.subject_load_assignments.length > 0) {
                            html += '<div class="mb-2"><strong>Subject Load Tracker:</strong></div>';
                            response.subject_load_assignments.forEach(function(assignment) {
                                html += '<div class="mb-1">• ' + assignment.subject_code + ' (' + assignment.schedule_display + ')</div>';
                            });
                        }
                        
                        html += '</div></div>';
                    }
                    
                    $('#load-summary').html(html);
                },
                error: function() {
                    $('#load-summary').html('<div class="text-center text-danger">Error loading summary</div>');
                }
            });
        }
    }
    
    // Event handlers
    $('#professor_id, #academic_year, #semester').change(function() {
        loadFacultySummary();
        clearTimeout(conflictCheckTimeout);
        clearTimeout(duplicateCheckTimeout);
        conflictCheckTimeout = setTimeout(checkConflicts, 500);
        duplicateCheckTimeout = setTimeout(checkDuplicates, 500);
    });
    
    $('#schedule_day, #start_time, #end_time').change(function() {
        clearTimeout(conflictCheckTimeout);
        conflictCheckTimeout = setTimeout(checkConflicts, 500);
    });
    
    $('#subject_code, #section').change(function() {
        clearTimeout(duplicateCheckTimeout);
        duplicateCheckTimeout = setTimeout(checkDuplicates, 500);
    });
    
    // Enhanced form validation with professional feedback
    $('#assignment-form').submit(function(e) {
        e.preventDefault();
        
        // Show loading state
        const submitBtn = $('#submit-btn');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Updating...');
        
        // Check for conflicts and duplicates
        if ($('#conflict-alert').hasClass('d-none') === false) {
            submitBtn.prop('disabled', false).html(originalText);
            Swal.fire({
                icon: 'error',
                title: 'Schedule Conflict',
                text: 'Please resolve the schedule conflict before updating.',
                confirmButtonColor: '#ffc107'
            });
            return false;
        }
        
        if ($('#duplicate-alert').hasClass('d-none') === false) {
            submitBtn.prop('disabled', false).html(originalText);
            Swal.fire({
                icon: 'error',
                title: 'Duplicate Assignment',
                text: 'Please resolve the duplicate assignment before updating.',
                confirmButtonColor: '#ffc107'
            });
            return false;
        }
        
        // Validate required fields
        let isValid = true;
        const requiredFields = ['professor_id', 'subject_code', 'subject_name', 'section', 'academic_year', 'semester'];
        
        requiredFields.forEach(function(field) {
            const input = $(`#${field}`);
            if (!input.val()) {
                input.addClass('is-invalid');
                isValid = false;
            } else {
                input.removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            submitBtn.prop('disabled', false).html(originalText);
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please fill in all required fields.',
                confirmButtonColor: '#ffc107'
            });
            return false;
        }
        
        // Final conflict check before submission
        $.ajax({
            url: '{{ route("admin.schedule-assignment.check-conflict") }}',
            method: 'GET',
            data: {
                professor_id: $('#professor_id').val(),
                schedule_day: $('#schedule_day').val(),
                start_time: $('#start_time').val(),
                end_time: $('#end_time').val(),
                academic_year: $('#academic_year').val(),
                semester: $('#semester').val(),
                exclude_id: currentAssignmentId
            },
            success: function(response) {
                if (response.has_conflict) {
                    submitBtn.prop('disabled', false).html(originalText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Schedule Conflict',
                        text: response.message,
                        confirmButtonColor: '#ffc107'
                    });
                } else {
                    // No conflicts, submit the form
                    submitBtn.closest('form')[0].submit();
                }
            },
            error: function() {
                submitBtn.prop('disabled', false).html(originalText);
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Unable to validate schedule. Please try again.',
                    confirmButtonColor: '#ffc107'
                });
            }
        });
    });
    
    // Real-time form field styling
    $('.form-control, .form-select').on('focus', function() {
        $(this).parent().addClass('focused');
    }).on('blur', function() {
        $(this).parent().removeClass('focused');
    });
    
    // Load initial summary
    loadFacultySummary();
});
</script>

@push('styles')
<style>
.focused .form-control,
.focused .form-select {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(255, 193, 7, 0.2);
}

.alert-professional {
    border-radius: 0.75rem;
    border: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.faculty-load-card {
    transition: all 0.3s ease;
}

.faculty-load-card:hover {
    transform: translateY(-2px);
}
</style>
@endpush
@endsection
