@extends('layouts.professor')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create New Salary Grade</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('professor.salary_grades.store') }}" method="POST">
                        @csrf
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="grade">Grade</label>
                            <input type="number" name="grade" class="form-control" value="{{ old('grade') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="step">Step</label>
                            <input type="number" name="step" class="form-control" value="{{ old('step') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="base_salary">Base Salary</label>
                            <input type="number" name="base_salary" class="form-control" step="0.01" value="{{ old('base_salary') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="allowance">Allowance</label>
                            <input type="number" name="allowance" class="form-control" step="0.01" value="{{ old('allowance') }}">
                        </div>
                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea name="notes" class="form-control">{{ old('notes') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="is_active">Active</label>
                            <input type="checkbox" name="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                        </div>
                        <button type="submit" class="btn btn-primary">Create</button>
                        <a href="{{ route('professor.salary_grades.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
