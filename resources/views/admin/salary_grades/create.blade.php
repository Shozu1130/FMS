@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="text-purple mb-4">New Salary Grade</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('admin.salary_grades.store') }}" method="POST" id="salary-grade-form">
                @csrf
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Grade <span class="text-danger">*</span></label>
                        <input type="number" name="grade" class="form-control @error('grade') is-invalid @enderror" value="{{ old('grade') }}" required>
                        @error('grade')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Step</label>
                        <input type="number" name="step" class="form-control @error('step') is-invalid @enderror" value="{{ old('step', 1) }}">
                        @error('step')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Base Salary <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="base_salary" class="form-control @error('base_salary') is-invalid @enderror" value="{{ old('base_salary') }}" required>
                        @error('base_salary')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Allowance</label>
                        <input type="number" step="0.01" name="allowance" class="form-control @error('allowance') is-invalid @enderror" value="{{ old('allowance', 0) }}">
                        @error('allowance')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.salary-grades.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-purple" id="submit-btn">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Create
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('salary-grade-form').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submit-btn');
    const spinner = submitBtn.querySelector('.spinner-border');

    // Show loading state
    submitBtn.disabled = true;
    spinner.classList.remove('d-none');

    // Log form data for debugging
    const formData = new FormData(this);
    console.log('Form Data:');
    for (let [key, value] of formData.entries()) {
        console.log(key + ': ' + value);
    }
});

// Re-enable button if form validation fails
document.getElementById('salary-grade-form').addEventListener('invalid', function(e) {
    const submitBtn = document.getElementById('submit-btn');
    const spinner = submitBtn.querySelector('.spinner-border');

    submitBtn.disabled = false;
    spinner.classList.add('d-none');
}, true);
</script>

<style>
.text-purple {
    color: #6f42c1;
}
.btn-purple {
    background-color: #6f42c1;
    border-color: #6f42c1;
    color: white;
}
.btn-purple:hover {
    background-color: #5a359a;
    border-color: #5a359a;
}
</style>
@endsection
