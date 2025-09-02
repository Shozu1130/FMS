@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="text-purple mb-4">Edit Salary Grade</h1>

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('admin.salary_grades.update', $grade->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Grade</label>
                        <input type="number" name="grade" class="form-control" value="{{ old('grade', $grade->grade) }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Step</label>
                        <input type="number" name="step" class="form-control" value="{{ old('step', $grade->step) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Base Salary</label>
                        <input type="number" step="0.01" name="base_salary" class="form-control" value="{{ old('base_salary', $grade->base_salary) }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Allowance</label>
                        <input type="number" step="0.01" name="allowance" class="form-control" value="{{ old('allowance', $grade->allowance) }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $grade->notes) }}</textarea>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" {{ old('is_active', $grade->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.salary-grades.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection



