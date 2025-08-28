@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="text-purple mb-4">Evaluation Details</h1>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-purple text-white">
                    <h5 class="mb-0">Evaluation Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Faculty Member:</strong> {{ $evaluation->faculty->name }}</p>
                    <p><strong>Professor ID:</strong> {{ $evaluation->faculty->professor_id }}</p>
                    <p><strong>Evaluation Period:</strong> {{ $evaluation->evaluation_period_full }}</p>
                    <p><strong>Academic Year:</strong> {{ $evaluation->academic_year }}</p>
                    <p><strong>Semester:</strong> {{ $evaluation->semester }}</p>
                    <p><strong>Status:</strong> 
                        <span class="badge bg-{{ $evaluation->is_published ? 'success' : 'secondary' }}">
                            {{ $evaluation->is_published ? 'Published' : 'Unpublished' }}
                        </span>
                    </p>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Evaluation Scores</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Teaching Effectiveness:</strong> {{ number_format($evaluation->teaching_effectiveness, 2) }}</p>
                            <p><strong>Subject Matter Knowledge:</strong> {{ number_format($evaluation->subject_matter_knowledge, 2) }}</p>
                            <p><strong>Classroom Management:</strong> {{ number_format($evaluation->classroom_management, 2) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Communication Skills:</strong> {{ number_format($evaluation->communication_skills, 2) }}</p>
                            <p><strong>Student Engagement:</strong> {{ number_format($evaluation->student_engagement, 2) }}</p>
                            <p><strong>Overall Rating:</strong> <strong>{{ number_format($evaluation->overall_rating, 2) }}</strong></p>
                        </div>
                    </div>
                </div>
            </div>

            @if($evaluation->strengths)
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Strengths</h5>
                </div>
                <div class="card-body">
                    <p>{{ $evaluation->strengths }}</p>
                </div>
            </div>
            @endif

            @if($evaluation->areas_for_improvement)
            <div class="card shadow mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Areas for Improvement</h5>
                </div>
                <div class="card-body">
                    <p>{{ $evaluation->areas_for_improvement }}</极速加速器p>
                </div>
            </div>
            @endif

            @if($evaluation->recommendations)
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Recommendations</h5>
                </div>
                <div class="card-body">
                    <p>{{ $evaluation->recommendations }}</p>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.evaluation.edit', $evaluation->id) }}" class="btn btn-primary">
                            <i class="bi bi-pencil"></i> Edit Evaluation
                        </a>
                        
                        <form action="{{ route('admin.evaluation.destroy', $evaluation->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this evaluation?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Delete Evaluation
                            </button>
                        </form>
                        
                        @if(!$evaluation->is_published)
                        <form action="{{ route('admin.evaluation.publish', $evaluation->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Publish Evaluation
                            </button>
                        </form>
                        @else
                        <form action="{{ route('admin.evaluation.unpublish', $evaluation->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-x-circle"></i> Unpublish Evaluation
                            </button>
                        </form>
                        @endif
                        
                        <a href="{{ route('admin.evaluation.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
