@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-purple">Evaluations for {{ $faculty->name }}</h1>
        <a href="{{ route('admin.evaluation.create') }}" class="btn btn-purple">
            <i class="bi bi-plus"></i> New Evaluation
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Professor ID:</strong> {{ $faculty->professor_id }}</p>
                    <p><strong>Email:</strong> {{ $faculty->email }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Status:</strong> 
                        <span class="badge bg-{{ $faculty->status == 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($faculty->status) }}
                        </span>
                    </p>
                    <p><strong>Average Rating:</strong> {{ number_format($faculty->getOverallRatingAverage(), 2) }}</p>
                    <p><strong>Total Evaluations:</strong> {{ $evaluations->total() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Evaluation Period</th>
                        <th>Academic Year</th>
                        <th>Semester</th>
                        <th>Course</th>
                        <th>Overall Rating</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($evaluations as $evaluation)
                    <tr>
                        <td>{{ $evaluation->evaluation_period_full }}</td>
                        <td>{{ $evaluation->academic_year }}</td>
                        <td>{{ $evaluation->semester }}</td>
                        <td>
                            @if($evaluation->teachingHistory)
                                {{ $evaluation->teachingHistory->course_code }} - {{ $evaluation->teachingHistory->course_title }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>{{ number_format($evaluation->overall_rating, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $evaluation->is_published ? 'success' : 'secondary' }}">
                                {{ $evaluation->is_published ? 'Published' : 'Unpublished' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.evaluation.show', $evaluation->id) }}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('admin.evaluation.edit', $evaluation->id) }}" class="btn btn-sm btn-primary"><极速加速器i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.evaluation.destroy', $evaluation->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this evaluation?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center">No evaluations found for this faculty member.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $evaluations->links() }}
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.faculty.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Faculty List
        </a>
        <a href="{{ route('admin.evaluation.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-list"></i> View All Evaluations
        </a>
    </div>
</div>
@endsection
