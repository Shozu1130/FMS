@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="bi bi-plus-circle"></i> Add Attendance Record
                </h1>
                <a href="{{ route('admin.attendance.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Attendance
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">New Attendance Record</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.attendance.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="faculty_id" class="form-label">Faculty <span class="text-danger">*</span></label>
                                    <select class="form-control @error('faculty_id') is-invalid @enderror" id="faculty_id" name="faculty_id" required>
                                        <option value="">Select Faculty</option>
                                        @foreach($faculties as $faculty)
                                            <option value="{{ $faculty->id }}" {{ old('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                                {{ $faculty->name }} ({{ $faculty->professor_id }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('faculty_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" required>
                                    @error('date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="">Select Status</option>
                                        <option value="present" {{ old('status') == 'present' ? 'selected' : '' }}>Present</option>
                                        <option value="absent" {{ old('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                                        <option value="late" {{ old('status') == 'late' ? 'selected' : '' }}>Late</option>
                                        <option value="early_departure" {{ old('status') == 'early_departure' ? 'selected' : '' }}>Early Departure</option>
                                        <option value="half_day" {{ old('status') == 'half_day' ? 'selected' : '' }}>Half Day</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="time_in" class="form-label">Time In</label>
                                    <input type="time" class="form-control @error('time_in') is-invalid @enderror" id="time_in" name="time_in" value="{{ old('time_in') }}">
                                    @error('time_in')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Leave empty if faculty was absent</div>
                                </div>

                                <div class="mb-3">
                                    <label for="time_out" class="form-label">Time Out</label>
                                    <input type="time" class="form-control @error('time_out') is-invalid @enderror" id="time_out" name="time_out" value="{{ old('time_out') }}">
                                    @error('time_out')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Leave empty if faculty was absent</div>
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Any additional notes...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Save Attendance Record
                                </button>
                                <a href="{{ route('admin.attendance.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-calculate total hours when both times are provided
document.addEventListener('DOMContentLoaded', function() {
    const timeIn = document.getElementById('time_in');
    const timeOut = document.getElementById('time_out');
    
    function calculateHours() {
        if (timeIn.value && timeOut.value) {
            const start = new Date(`2000-01-01T${timeIn.value}`);
            const end = new Date(`2000-01-01T${timeOut.value}`);
            
            if (end > start) {
                const diffMs = end - start;
                const diffHours = diffMs / (1000 * 60 * 60);
                console.log(`Total hours: ${diffHours.toFixed(2)}`);
            }
        }
    }
    
    timeIn.addEventListener('change', calculateHours);
    timeOut.addEventListener('change', calculateHours);
});
</script>
@endpush
