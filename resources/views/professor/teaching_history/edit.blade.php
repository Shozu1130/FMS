@extends('layouts.professor_admin')

@section('content')
<h1 class="mb-4">Edit Teaching Assignment</h1>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('professor.teaching_history.update', $teachingHistory) }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="course_code" class="form-label">Course Code</label>
                <input type="text" name="course_code" id="course_code" class="form-control" value="{{ old('course_code', $teachingHistory->course_code) }}" required>
                @error('course_code')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="course_title" class="form-label">Course Title</label>
                <input type="text" name="course_title" id="course_title" class="form-control" value="{{ old('course_title', $teachingHistory->course_title) }}" required>
                @error('course_title')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="semester" class="form-label">Semester</label>
                <select name="semester" id="semester" class="form-select" required>
                    <option value="">Select Semester</option>
                    <option value="1st Semester" {{ old('semester', $teachingHistory->semester) == '1st Semester' ? 'selected' : '' }}>1st Semester</option>
                    <option value="2nd Semester" {{ old('semester', $teachingHistory->semester) == '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
                    <option value="Summer" {{ old('semester', $teachingHistory->semester) == 'Summer' ? 'selected' : '' }}>Summer</option>
                </select>
                @error('semester')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="academic_year" class="form-label">Academic Year</label>
                <input type="number" name="academic_year" id="academic_year" class="form-control" value="{{ old('academic_year', $teachingHistory->academic_year) }}" required>
                @error('academic_year')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="units" class="form-label">Units</label>
                <input type="number" name="units" id="units" class="form-control" value="{{ old('units', $teachingHistory->units) }}" min="1" max="10" required>
                @error('units')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="schedule" class="form-label">Schedule</label>
                <select name="schedule" id="schedule" class="form-select">
                    <option value="">Select Schedule</option>
                    <option value="MWF" {{ old('schedule', $teachingHistory->schedule) == 'MWF' ? 'selected' : '' }}>MWF</option>
                    <option value="TTH" {{ old('schedule', $teachingHistory->schedule) == 'TTH' ? 'selected' : '' }}>TTH</option>
                    <option value="MW" {{ old('schedule', $teachingHistory->schedule) == 'MW' ? 'selected' : '' }}>MW</option>
                    <option value="TTHS" {{ old('schedule', $teachingHistory->schedule) == 'TTHS' ? 'selected' : '' }}>TTHS</option>
                    <option value="F" {{ old('schedule', $teachingHistory->schedule) == 'F' ? 'selected' : '' }}>F</option>
                    <option value="S" {{ old('schedule', $teachingHistory->schedule) == 'S' ? 'selected' : '' }}>S</option>
                </select>
                @error('schedule')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label for="start_time" class="form-label">Start Time</label>
                    <input type="time" name="start_time" id="start_time" class="form-control" value="{{ old('start_time', $teachingHistory->start_time ? $teachingHistory->start_time->format('H:i') : '') }}">
                    @error('start_time')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col">
                    <label for="end_time" class="form-label">End Time</label>
                    <input type="time" name="end_time" id="end_time" class="form-control" value="{{ old('end_time', $teachingHistory->end_time ? $teachingHistory->end_time->format('H:i') : '') }}">
                    @error('end_time')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="mb-3">
                <label for="room" class="form-label">Room</label>
                <input type="text" name="room" id="room" class="form-control" value="{{ old('room', $teachingHistory->room) }}">
                @error('room')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="number_of_students" class="form-label">Number of Students</label>
                <input type="number" name="number_of_students" id="number_of_students" class="form-control" value="{{ old('number_of_students', $teachingHistory->number_of_students) }}" min="0" max="500" required>
                @error('number_of_students')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="remarks" class="form-label">Remarks</label>
                <textarea name="remarks" id="remarks" class="form-control" rows="3">{{ old('remarks', $teachingHistory->remarks) }}</textarea>
                @error('remarks')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('professor.teaching_history.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
