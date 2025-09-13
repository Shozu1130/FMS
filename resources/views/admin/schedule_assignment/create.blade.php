@extends('layouts.admin')

@section('title', 'Create Schedule Assignment')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
                <i class="fas fa-plus-circle text-primary mr-2"></i>
                Create Schedule Assignment
            </h1>
            <p class="text-muted mb-0">Add a new schedule assignment for faculty members</p>
        </div>
        <a href="{{ route('admin.schedule-assignment.index') }}" class="btn btn-secondary btn-sm shadow-sm rounded-pill">
            <i class="fas fa-arrow-left fa-sm mr-1"></i> Back to List
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg mb-4">
                <div class="card-header bg-gradient-primary text-white py-3">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-edit mr-2"></i>Assignment Details
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.schedule-assignment.store') }}" id="assignment-form">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="professor_id" class="form-label font-weight-bold text-dark">
                                    <i class="fas fa-user text-primary mr-1"></i>Faculty Member <span class="text-danger">*</span>
                                </label>
                                <select name="professor_id" id="professor_id" class="form-select form-select-lg @error('professor_id') is-invalid @enderror" required>
                                    <option value="">Choose faculty member...</option>
                                    @foreach($faculties as $faculty)
                                        <option value="{{ $faculty->id }}" {{ old('professor_id') == $faculty->id ? 'selected' : '' }}>
                                            {{ $faculty->name }} ({{ $faculty->professor_id }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('professor_id')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="academic_year" class="form-label font-weight-bold text-dark">
                                    <i class="fas fa-graduation-cap text-info mr-1"></i>Academic Year <span class="text-danger">*</span>
                                </label>
                                <select name="academic_year" id="academic_year" class="form-select form-select-lg @error('academic_year') is-invalid @enderror" required>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year }}" {{ old('academic_year', $currentYear) == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('academic_year')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label for="semester" class="form-label">Semester <span class="text-danger">*</span></label>
                                <select name="semester" id="semester" class="form-select @error('semester') is-invalid @enderror" required>
                                    @foreach($semesters as $key => $semester)
                                        <option value="{{ $key }}" {{ old('semester') == $key ? 'selected' : '' }}>
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
                                       value="{{ old('subject_code') }}" required maxlength="20">
                                @error('subject_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="section" class="form-label">Section <span class="text-danger">*</span></label>
                                <input type="text" name="section" id="section" 
                                       class="form-control @error('section') is-invalid @enderror" 
                                       value="{{ old('section') }}" required maxlength="10">
                                @error('section')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="year_level" class="form-label">Year Level <span class="text-danger">*</span></label>
                                <select name="year_level" id="year_level" class="form-select @error('year_level') is-invalid @enderror" required>
                                    <option value="">Select Year Level</option>
                                    @foreach($yearLevels as $key => $level)
                                        <option value="{{ $key }}" {{ old('year_level') == $key ? 'selected' : '' }}>
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
                                   value="{{ old('subject_name') }}" required maxlength="255">
                            @error('subject_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="units" class="form-label">Units <span class="text-danger">*</span></label>
                                <input type="number" name="units" id="units" 
                                       class="form-control @error('units') is-invalid @enderror" 
                                       value="{{ old('units') }}" required min="1" max="6">
                                @error('units')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="hours_per_week" class="form-label">Hours/Week <span class="text-danger">*</span></label>
                                <input type="number" name="hours_per_week" id="hours_per_week" 
                                       class="form-control @error('hours_per_week') is-invalid @enderror" 
                                       value="{{ old('hours_per_week') }}" required min="1" max="40">
                                @error('hours_per_week')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                    @foreach($statusOptions as $key => $status)
                                        <option value="{{ $key }}" {{ old('status', 'active') == $key ? 'selected' : '' }}>
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
                                        <option value="{{ $key }}" {{ old('source', 'direct') == $key ? 'selected' : '' }}>
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
                                        <option value="{{ $key }}" {{ old('schedule_day') == $key ? 'selected' : '' }}>
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
                                       value="{{ old('start_time') }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                                <input type="time" name="end_time" id="end_time" 
                                       class="form-control @error('end_time') is-invalid @enderror" 
                                       value="{{ old('end_time') }}" required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="room" class="form-label">Room</label>
                                <input type="text" name="room" id="room" 
                                       class="form-control @error('room') is-invalid @enderror" 
                                       value="{{ old('room') }}" maxlength="50">
                                @error('room')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="notes" class="form-label font-weight-bold text-dark">
                                    <i class="fas fa-sticky-note text-secondary mr-1"></i>Notes
                                </label>
                                <textarea name="notes" id="notes" class="form-control form-control-lg @error('notes') is-invalid @enderror" 
                                          rows="4" placeholder="Additional notes, comments, or special instructions...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Validation Alerts -->
                        <div id="conflict-alert" class="alert alert-danger d-none" role="alert">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Schedule Conflict Detected!</strong>
                            <div id="conflict-message"></div>
                        </div>
                        
                        <div id="duplicate-alert" class="alert alert-warning d-none" role="alert">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <strong>Duplicate Assignment Warning!</strong>
                            <div id="duplicate-message"></div>
                        </div>
                        @if($errors->has('duplicate'))
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ $errors->first('duplicate') }}
                            </div>
                        @endif

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.schedule-assignment.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <i class="fas fa-save"></i> Create Assignment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Faculty Load Summary -->
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Faculty Load Summary</h6>
                </div>
                <div class="card-body" id="load-summary">
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-user-clock fa-3x mb-3"></i>
                        <p>Select faculty, academic year, and semester to view load summary.</p>
                    </div>
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
    
    // Function to check conflicts
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
                    semester: semester
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
                    semester: semester
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
                                html += '<div class="mb-1">• ' + assignment.subject_code + ' (' + assignment.schedule_display + ')</div>';
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
        } else {
            $('#load-summary').html('<div class="text-center text-muted py-4"><i class="fas fa-user-clock fa-3x mb-3"></i><p>Select faculty, academic year, and semester to view load summary.</p></div>');
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
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Creating...');
        
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
                confirmButtonColor: '#007bff'
            });
            alert('Please resolve the duplicate assignment before submitting.');
            return false;
        }
    });
});
</script>
@endpush
@endsection
