@extends('layouts.professor_admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Submit Clearance Request</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('professor.clearance-requests.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group mb-3">
                            <label for="clearance_type" class="form-label">Clearance Type <span class="text-danger">*</span></label>
                            <select name="clearance_type" id="clearance_type" class="form-control @error('clearance_type') is-invalid @enderror" required>
                                <option value="">Select Clearance Type</option>
                                @foreach($clearanceTypes as $key => $name)
                                    <option value="{{ $key }}" {{ old('clearance_type') == $key ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('clearance_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="reason" class="form-label">Reason for Request <span class="text-danger">*</span></label>
                            <textarea name="reason" id="reason" rows="5" 
                                      class="form-control @error('reason') is-invalid @enderror" 
                                      placeholder="Please provide a detailed reason for your clearance request..."
                                      required>{{ old('reason') }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Maximum 1000 characters</small>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note:</strong> Once submitted, your request will be reviewed by the administration. 
                            You can edit or delete pending requests, but processed requests cannot be modified.
                        </div>

                        <div class="form-group d-flex justify-content-between">
                            <a href="{{ route('professor.clearance-requests.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Requests
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const reasonTextarea = document.getElementById('reason');
    const maxLength = 1000;
    
    // Create character counter
    const counter = document.createElement('small');
    counter.className = 'form-text text-muted text-right';
    counter.style.display = 'block';
    reasonTextarea.parentNode.appendChild(counter);
    
    function updateCounter() {
        const remaining = maxLength - reasonTextarea.value.length;
        counter.textContent = `${reasonTextarea.value.length}/${maxLength} characters`;
        
        if (remaining < 100) {
            counter.className = 'form-text text-warning text-right';
        } else if (remaining < 0) {
            counter.className = 'form-text text-danger text-right';
        } else {
            counter.className = 'form-text text-muted text-right';
        }
    }
    
    reasonTextarea.addEventListener('input', updateCounter);
    updateCounter(); // Initial call
});
</script>
@endsection
