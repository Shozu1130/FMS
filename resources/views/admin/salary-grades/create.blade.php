@extends('layouts.admin')

@section('title', 'Add New Salary Grade')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Add New Salary Grade</h3>
                </div>
                <div class="card-body">
                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('admin.salary-grades.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Grade *</label>
                                    <input type="number" name="grade" class="form-control" value="{{ old('grade') }}" required min="1" max="99">
                                    <small class="form-text text-muted">Enter a unique grade number (1-99)</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Full-Time Base Salary *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" name="full_time_base_salary" class="form-control" value="{{ old('full_time_base_salary') }}" required step="0.01" min="0">
                                    </div>
                                    <small class="form-text text-muted">Monthly base salary for full-time professors</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Part-Time Base Salary *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" name="part_time_base_salary" class="form-control" value="{{ old('part_time_base_salary') }}" required step="0.01" min="0">
                                    </div>
                                    <small class="form-text text-muted">Monthly base salary for part-time professors</small>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note:</strong> The salary will be calculated based on the professor's employment type (Full-Time or Part-Time) from their profile and their total hours worked based on attendance records.
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.salary-grades.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Salary Grade
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
