@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="text-purple mb-4">Create Teaching History</h1>

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('admin.teaching_history.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="faculty_id" class="form-label">Faculty Member *</label>
                            <select class="form-select" id="faculty_id" name="faculty_id" required>
                                <option value="">Select Faculty</option>
                                @foreach($faculties as $faculty)
                                    <option value="{{ $faculty->id }}" {{ old('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                        {{ $faculty->name }} ({{ $faculty->professor_id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="course_code" class="form-label">Course Code *</label>
                            <input type="text" class="form-control" id="course_code" name="course_code" value="{{ old('course_code') }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="course_title" class="form-label">Course Title *</label>
                            <input type="text" class="form-control" id="course_title" name="course_title" value="{{ old('course_title') }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="semester" class="form-label">Semester *</label>
                            <select class="form-select" id="semester" name="semester" required>
                                <option value="">Select Semester</option>
                                <option value="1st Semester" {{ old('semester') == '1st Semester' ? 'selected' : '' }}>1st Semester</option>
                                <option value="2nd Semester" {{ old('semester') == '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
                                <option value="Summer" {{ old('semester') == 'Summer' ? 'selected' : '' }}>Summer</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="academic_year" class="form-label">Academic Year *</label>
                            <input type="number" class="form-control" id="academic_year" name="academic_year" value="{{ old('academic_year', date('Y')) }}" min="2000" max="2100" required>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="units" class="form-label">Units *</label>
                            <input type="number" class="form-control" id="units" name="units" value="{{ old('units', 3) }}" min="1" max="10" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="schedule" class="form-label">Schedule</label>
                            <select class="form-select" id="schedule" name="schedule">
                                <option value="">Select Schedule</option>
                                <option value="MWF" {{ old('schedule') == 'MWF' ? 'selected' : '' }}>MWF</option>
                                <option value="TTH" {{ old('schedule') == 'TTH' ? 'selected' : '' }}>TTH</option>
                                <option value="MW" {{ old('schedule') == 'MW' ? 'selected' : '' }}>MW</option>
                                <option value="TTHS" {{ old('schedule') == 'TTHS' ? 'selected' : '' }}>TTHS</option>
                                <option value="F" {{ old('schedule') == 'F' ? 'selected' : '' }}>F</option>
                                <option value="S" {{ old('schedule') == 'S' ? 'selected' : '' }}>S</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="start_time" class="form-label">Start Time</label>
                            <input type="time" class="form-control" id="start_time" name="start_time" value="{{ old('start_time') }}">
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="end_time" class="form-label">End Time</label>
                            <input type="time" class="form-control" id="end_time" name="end_time" value="{{ old('end_time') }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="room" class="form-label">Room</label>
                            <input type="text" class="form-control" id="room" name="room" value="{{ old('room') }}">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="number_of_students" class="form-label">Number of Students *</label>
                            <input type="number" class="form-control" id="number_of_students" name="number_of_students" value="{{ old('number_of_students', 0) }}" min="0" max="500" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="rating" class="form-label">Rating (1-5)</label>
                            <input type="number" class="form-control" id="rating" name="rating" value="{{ old('rating') }}" min="1" max="5" step="0.01">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="is_active" class="form-label">Status</label>
                            <select class="form-select" id="is_active" name="is_active">
                                <option value="1" {{ old('is_active', 1) ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="remarks" class="form-label">Remarks</label>
                    <textarea class="form-control" id="remarks" name="remarks" rows="3">{{ old('remarks') }}</textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-purple">Create Teaching History</button>
                    <a href="{{ route('admin.teaching_history.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
