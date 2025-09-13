@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Edit Subject Load</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.subject-loads.update', $subjectLoad) }}" method="POST" id="subjectLoadForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="professor_id" class="form-label">Faculty Member <span class="text-danger">*</span></label>
                                    <select name="professor_id" id="professor_id" class="form-select @error('professor_id') is-invalid @enderror" required>
                                        <option value="">Select Faculty</option>
                                        @foreach($faculties as $faculty)
                                            <option value="{{ $faculty->id }}" {{ old('professor_id', $subjectLoad->professor_id) == $faculty->id ? 'selected' : '' }}>
                                                {{ $faculty->name }} ({{ $faculty->professor_id }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('professor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="subject_code" class="form-label">Subject Code <span class="text-danger">*</span></label>
                                    <input type="text" name="subject_code" id="subject_code" 
                                           class="form-control @error('subject_code') is-invalid @enderror" 
                                           value="{{ old('subject_code', $subjectLoad->subject_code) }}" required maxlength="20">
                                    @error('subject_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="subject_name" class="form-label">Subject Name <span class="text-danger">*</span></label>
                                    <input type="text" name="subject_name" id="subject_name" 
                                           class="form-control @error('subject_name') is-invalid @enderror" 
                                           value="{{ old('subject_name', $subjectLoad->subject_name) }}" required>
                                    @error('subject_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="section" class="form-label">Section <span class="text-danger">*</span></label>
                                    <input type="text" name="section" id="section" 
                                           class="form-control @error('section') is-invalid @enderror" 
                                           value="{{ old('section', $subjectLoad->section) }}" required maxlength="10">
                                    @error('section')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="year_level" class="form-label">Year Level <span class="text-danger">*</span></label>
                                    <select name="year_level" id="year_level" class="form-select @error('year_level') is-invalid @enderror" required>
                                        <option value="">Select Year Level</option>
                                        @foreach($yearLevels as $key => $value)
                                            <option value="{{ $key }}" {{ old('year_level', $subjectLoad->year_level) == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('year_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="units" class="form-label">Units <span class="text-danger">*</span></label>
                                    <input type="number" name="units" id="units" 
                                           class="form-control @error('units') is-invalid @enderror" 
                                           value="{{ old('units', $subjectLoad->units) }}" required min="1" max="6">
                                    @error('units')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="hours_per_week" class="form-label">Hours/Week <span class="text-danger">*</span></label>
                                    <input type="number" name="hours_per_week" id="hours_per_week" 
                                           class="form-control @error('hours_per_week') is-invalid @enderror" 
                                           value="{{ old('hours_per_week', $subjectLoad->hours_per_week) }}" required min="1" max="40">
                                    @error('hours_per_week')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="academic_year" class="form-label">Academic Year <span class="text-danger">*</span></label>
                                    <input type="number" name="academic_year" id="academic_year" 
                                           class="form-control @error('academic_year') is-invalid @enderror" 
                                           value="{{ old('academic_year', $subjectLoad->academic_year) }}" required min="2000" max="2100">
                                    @error('academic_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="semester" class="form-label">Semester <span class="text-danger">*</span></label>
                                    <select name="semester" id="semester" class="form-select @error('semester') is-invalid @enderror" required>
                                        <option value="">Select Semester</option>
                                        @foreach($semesters as $key => $value)
                                            <option value="{{ $key }}" {{ old('semester', $subjectLoad->semester) == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('semester')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="schedule_day" class="form-label">Day <span class="text-danger">*</span></label>
                                    <select name="schedule_day" id="schedule_day" class="form-select @error('schedule_day') is-invalid @enderror" required>
                                        <option value="">Select Day</option>
                                        @foreach($days as $key => $value)
                                            <option value="{{ $key }}" {{ old('schedule_day', $subjectLoad->schedule_day) == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('schedule_day')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                                    <input type="time" name="start_time" id="start_time" 
                                           class="form-control @error('start_time') is-invalid @enderror" 
                                           value="{{ old('start_time', $subjectLoad->start_time) }}" required>
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                                    <input type="time" name="end_time" id="end_time" 
                                           class="form-control @error('end_time') is-invalid @enderror" 
                                           value="{{ old('end_time', $subjectLoad->end_time) }}" required>
                                    @error('end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="room" class="form-label">Room</label>
                                    <input type="text" name="room" id="room" 
                                           class="form-control @error('room') is-invalid @enderror" 
                                           value="{{ old('room', $subjectLoad->room) }}" maxlength="50">
                                    @error('room')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                        @foreach($statusOptions as $key => $value)
                                            <option value="{{ $key }}" {{ old('status', $subjectLoad->status) == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea name="notes" id="notes" rows="3" 
                                              class="form-control @error('notes') is-invalid @enderror" 
                                              maxlength="1000">{{ old('notes', $subjectLoad->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <span id="notesCount">{{ strlen(old('notes', $subjectLoad->notes)) }}</span>/1000 characters
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Conflict Detection Alert -->
                        <div id="conflictAlert" class="alert alert-warning" style="display: none;">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Conflicts Detected:</strong>
                            <ul id="conflictList"></ul>
                        </div>

                        <div class="form-group d-flex justify-content-between">
                            <a href="{{ route('admin.subject-loads.show', $subjectLoad) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Details
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save"></i> Update Subject Load
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Character counter for notes
document.getElementById('notes').addEventListener('input', function() {
    document.getElementById('notesCount').textContent = this.value.length;
});

// Real-time conflict detection
function checkConflicts() {
    const facultyId = document.getElementById('professor_id').value;
    const subjectCode = document.getElementById('subject_code').value;
    const section = document.getElementById('section').value;
    const academicYear = document.getElementById('academic_year').value;
    const semester = document.getElementById('semester').value;
    const scheduleDay = document.getElementById('schedule_day').value;
    const startTime = document.getElementById('start_time').value;
    const endTime = document.getElementById('end_time').value;

    if (!facultyId || !subjectCode || !section || !academicYear || !semester) {
        hideConflictAlert();
        return;
    }

    fetch('{{ route("admin.subject-loads.check-conflicts") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            professor_id: facultyId,
            subject_code: subjectCode,
            section: section,
            academic_year: academicYear,
            semester: semester,
            schedule_day: scheduleDay,
            start_time: startTime,
            end_time: endTime,
            exclude_id: {{ $subjectLoad->id }}
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.has_conflicts) {
            showConflictAlert(data.conflicts);
        } else {
            hideConflictAlert();
        }
    })
    .catch(error => console.error('Error:', error));
}

function showConflictAlert(conflicts) {
    const alertDiv = document.getElementById('conflictAlert');
    const conflictList = document.getElementById('conflictList');
    
    conflictList.innerHTML = '';
    conflicts.forEach(conflict => {
        const li = document.createElement('li');
        li.textContent = conflict.message;
        conflictList.appendChild(li);
    });
    
    alertDiv.style.display = 'block';
    document.getElementById('submitBtn').disabled = true;
}

function hideConflictAlert() {
    document.getElementById('conflictAlert').style.display = 'none';
    document.getElementById('submitBtn').disabled = false;
}

// Event listeners
['professor_id', 'subject_code', 'section', 'academic_year', 'semester', 'schedule_day', 'start_time', 'end_time'].forEach(id => {
    document.getElementById(id).addEventListener('change', checkConflicts);
});

// Initialize character counter
document.getElementById('notesCount').textContent = document.getElementById('notes').value.length;
</script>
@endsection
